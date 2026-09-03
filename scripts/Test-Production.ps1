#requires -Version 7.0
param([string]$Image = 'nihonreach:production-local')

$ErrorActionPreference = 'Stop'
$runName = 'nihonreach-check-' + [guid]::NewGuid().ToString('N').Substring(0, 10)
$appName = "$runName-app"
$dbName = "$runName-db"
$createdContainers = [System.Collections.Generic.List[string]]::new()
$networkCreated = $false
$dbPassword = [guid]::NewGuid().ToString('N')
$rootPassword = [guid]::NewGuid().ToString('N')
$adminPassword = 'Check-' + [guid]::NewGuid().ToString('N') + '!9aA'
$appKey = 'base64:' + [Convert]::ToBase64String([System.Security.Cryptography.RandomNumberGenerator]::GetBytes(32))

function Invoke-Docker {
    & docker @args
    if ($LASTEXITCODE -ne 0) { throw 'Docker command failed; no production resources were used.' }
}

function Assert-Status($Response, [int]$Expected, [string]$Label) {
    if ([int]$Response.StatusCode -ne $Expected) {
        throw "$Label returned $($Response.StatusCode), expected $Expected."
    }
}

function Get-Csrf($Session, [string]$BaseUrl) {
    $cookie = $Session.Cookies.GetCookies([uri]$BaseUrl)['XSRF-TOKEN']
    if ($null -eq $cookie) { throw 'Missing CSRF cookie.' }
    return [uri]::UnescapeDataString($cookie.Value)
}

function Wait-ForApplication([string]$BaseUrl) {
    for ($attempt = 0; $attempt -lt 30; $attempt++) {
        try {
            $health = Invoke-WebRequest "$BaseUrl/up" -TimeoutSec 3 -SkipHttpErrorCheck
            if ($health.StatusCode -eq 200) { return }
        } catch { }
        Start-Sleep -Seconds 1
    }
    throw 'Application did not become healthy.'
}

try {
    Invoke-Docker image inspect $Image --format '{{.Id}}' | Out-Null
    Invoke-Docker network create $runName | Out-Null
    $networkCreated = $true
    $dbId = Invoke-Docker run -d --pull=never --name $dbName --network $runName --network-alias mysql `
        --tmpfs '/var/lib/mysql:rw,mode=1777' `
        -e "MYSQL_ROOT_PASSWORD=$rootPassword" -e "MYSQL_PASSWORD=$dbPassword" `
        -e MYSQL_USER=nihonreach -e MYSQL_DATABASE=nihonreach_check mysql:8.4.11
    $createdContainers.Add(($dbId | Select-Object -Last 1))

    $databaseReady = $false
    for ($attempt = 0; $attempt -lt 90; $attempt++) {
        & docker exec -e "MYSQL_PWD=$dbPassword" $dbName mysql --protocol=TCP -h 127.0.0.1 -u nihonreach nihonreach_check -Nse 'SELECT 1' 2>$null | Out-Null
        if ($LASTEXITCODE -eq 0) { $databaseReady = $true; break }
        Start-Sleep -Seconds 1
    }
    if (-not $databaseReady) { throw 'Isolated MySQL did not become ready.' }
    Write-Host 'PASS: isolated MySQL ready (no published database port).'

    $appId = Invoke-Docker run -d --pull=never --name $appName --network $runName `
        -p '127.0.0.1::8080' -e "APP_KEY=$appKey" -e APP_URL=http://127.0.0.1 `
        -e DB_CONNECTION=mysql -e DB_HOST=mysql -e DB_DATABASE=nihonreach_check `
        -e DB_USERNAME=nihonreach -e "DB_PASSWORD=$dbPassword" `
        -e SESSION_DRIVER=database -e SESSION_SECURE_COOKIE=false -e CACHE_STORE=database `
        -e QUEUE_CONNECTION=sync -e MAIL_MAILER=array -e "SMOKE_ADMIN_PASSWORD=$adminPassword" $Image
    $createdContainers.Add(($appId | Select-Object -Last 1))
    $address = (Invoke-Docker port $appName 8080/tcp | Select-Object -First 1).Trim()
    $baseUrl = "http://$address"
    Wait-ForApplication $baseUrl

    # These commands target only this script's fresh, disposable MySQL instance.
    Invoke-Docker exec $appName php artisan migrate --force | Out-Null
    Invoke-Docker exec $appName php artisan db:seed --force | Out-Null
    $fixture = @'
<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
if (App\Models\User::count() !== 0 || App\Models\Product::count() !== 6) {
    throw new RuntimeException('Production seeding did not respect the demo boundary.');
}
(new App\Models\User)->forceFill([
    'name' => 'Synthetic Operator', 'email' => 'operator@example.test',
    'password' => getenv('SMOKE_ADMIN_PASSWORD'), 'is_admin' => true,
    'email_verified_at' => now(),
])->save();
echo 'PASS: production seed creates products, not a default administrator.'.PHP_EOL;
'@
    $fixture | & docker exec -i $appName php
    if ($LASTEXITCODE -ne 0) { throw 'Fixture validation failed.' }

    $visitor = [Microsoft.PowerShell.Commands.WebRequestSession]::new()
    foreach ($path in @('/', '/zh', '/products', '/zh/products', '/login')) {
        Assert-Status (Invoke-WebRequest "$baseUrl$path" -WebSession $visitor -SkipHttpErrorCheck) 200 $path
    }
    $form = Invoke-WebRequest "$baseUrl/zh/inquiries/create" -WebSession $visitor
    $csrf = [regex]::Match($form.Content, 'name="_token" value="([^"]+)"').Groups[1].Value
    if (-not $csrf) { throw 'Missing form CSRF token.' }
    $asset = [regex]::Match($form.Content, 'href="([^"]+/build/assets/[^"]+\.css)"').Groups[1].Value
    if (-not $asset) { throw 'Missing compiled stylesheet.' }
    Assert-Status (Invoke-WebRequest $asset -SkipHttpErrorCheck) 200 'compiled CSS'

    $payload = @{
        _token = $csrf; name = 'Synthetic Visitor'; email = 'visitor@example.test'
        subject = 'Synthetic production check'; message = 'This is isolated synthetic inquiry data for the smoke test.'; privacy = '1'
    }
    $thanks = Invoke-WebRequest "$baseUrl/zh/inquiries" -Method Post -Body $payload -WebSession $visitor -SkipHttpErrorCheck
    Assert-Status $thanks 200 'inquiry submission'
    if ($thanks.Content -notmatch '提交完成') { throw 'Inquiry did not reach the Chinese thank-you page.' }
    $noCsrfSession = [Microsoft.PowerShell.Commands.WebRequestSession]::new()
    Assert-Status (Invoke-WebRequest "$baseUrl/zh/inquiries" -Method Post -Body @{name='Synthetic'} -WebSession $noCsrfSession -SkipHttpErrorCheck) 419 'CSRF rejection'
    Assert-Status (Invoke-WebRequest "$baseUrl/products?category%5B%5D=x" -SkipHttpErrorCheck) 400 'invalid query'

    $admin = [Microsoft.PowerShell.Commands.WebRequestSession]::new()
    Invoke-WebRequest "$baseUrl/login" -WebSession $admin | Out-Null
    $headers = @{ 'X-XSRF-TOKEN' = (Get-Csrf $admin $baseUrl) }
    $login = Invoke-WebRequest "$baseUrl/login" -Method Post -WebSession $admin -Headers $headers `
        -Body @{email='operator@example.test'; password=$adminPassword} -SkipHttpErrorCheck
    Assert-Status $login 200 'admin login'
    $pageJson = [regex]::Match($login.Content, '<script data-page="app" type="application/json">(.*?)</script>', 'Singleline').Groups[1].Value
    if (-not $pageJson) { throw 'Missing initial Inertia page.' }
    $page = $pageJson | ConvertFrom-Json
    $headers = @{ 'X-Inertia'='true'; 'X-Inertia-Version'=$page.version; Accept='application/json' }
    $list = Invoke-RestMethod "$baseUrl/admin/inquiries" -WebSession $admin -Headers $headers
    if ($list.component -ne 'admin/inquiries/Index' -or $list.props.inquiries.total -ne 1) { throw 'Admin inquiry list is incorrect.' }
    $inquiryId = $list.props.inquiries.data[0].id
    $headers['X-XSRF-TOKEN'] = Get-Csrf $admin $baseUrl
    Invoke-WebRequest "$baseUrl/admin/inquiries/$inquiryId" -Method Patch -WebSession $admin -Headers $headers `
        -ContentType 'application/json' -Body '{"status":"closed"}' | Out-Null
    Write-Host 'PASS: Japanese/Chinese pages, compiled CSS, real CSRF, admin login and inquiry update.'

    Invoke-Docker restart $appName | Out-Null
    # Docker may assign a different ephemeral host port when restarting.
    $address = (Invoke-Docker port $appName 8080/tcp | Select-Object -First 1).Trim()
    $baseUrl = "http://$address"
    Wait-ForApplication $baseUrl
    $headers.Remove('X-XSRF-TOKEN')
    $list = Invoke-RestMethod "$baseUrl/admin/inquiries" -WebSession $admin -Headers $headers
    if ($list.props.inquiries.total -ne 1 -or $list.props.inquiries.data[0].status -ne 'closed') {
        throw 'Inquiry/session did not survive application restart.'
    }
    $uid = (Invoke-Docker exec $appName id -u).Trim()
    if ($uid -eq '0') { throw 'Production process must not run as root.' }
    Invoke-Docker exec $appName sh -c 'test ! -e .env && test ! -d docs/private && test ! -d node_modules && test ! -e public/hot && ! command -v codex && ! command -v node' | Out-Null
    Write-Host 'PASS: data/session survive app restart; non-root runtime; no local secrets or development tools.'
} finally {
    # Remove only container IDs returned by this run, never existing development services.
    foreach ($containerId in $createdContainers) {
        if ($containerId -match '^[a-f0-9]{64}$') { & docker rm -f $containerId | Out-Null }
    }
    if ($networkCreated) { & docker network rm $runName | Out-Null }
    Write-Host 'Cleaned up this run: disposable containers, in-memory test data and isolated network.'
}
