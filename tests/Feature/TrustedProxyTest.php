<?php

namespace Tests\Feature;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class TrustedProxyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['trustedproxy.proxies' => ['10.20.30.2']]);
        Route::get('/test-request-context', fn (Request $request) => [
            'secure' => $request->secure(), 'ip' => $request->ip(), 'host' => $request->host(),
        ]);
    }

    public function test_configured_proxy_can_forward_https_and_client_ip_but_not_host(): void
    {
        $this->withServerVariables(['REMOTE_ADDR' => '10.20.30.2'])
            ->withHeaders([
                'X-Forwarded-Proto' => 'https',
                'X-Forwarded-For' => '192.0.2.10',
                'X-Forwarded-Host' => 'untrusted.example',
            ])->getJson('/test-request-context')
            ->assertOk()->assertJson([
                'secure' => true, 'ip' => '192.0.2.10', 'host' => 'localhost',
            ]);
    }

    public function test_untrusted_client_cannot_spoof_proxy_headers(): void
    {
        $this->withServerVariables(['REMOTE_ADDR' => '192.0.2.20'])
            ->withHeaders(['X-Forwarded-Proto' => 'https', 'X-Forwarded-For' => '192.0.2.10'])
            ->getJson('/test-request-context')
            ->assertOk()->assertJson(['secure' => false, 'ip' => '192.0.2.20']);
    }
}
