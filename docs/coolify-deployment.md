# NihonReach：Coolify 部署与人工验收

当前 Demo 已由维护者完成首次 VPS 部署、数据库迁移与演示数据初始化，并确认基本业务可访问；公开入口见 [README](../README.md)。项目仍是求职 Portfolio Demo，不是真实企业运营系统。生产密码、内部地址和 APP_KEY 只在部署环境管理，不写入本文件。

本文保留可复用的首次部署步骤和后续验收清单，不表示每一项运维检查都已完成；尤其没有备份恢复与高可用验收结论。本次文档同步依据维护者提供的部署记录，不包含新的生产环境操作。

## 1. 做什么、为什么这样做

- 本地继续使用 `.devcontainer/` 和 `compose.yaml`；不要把开发 Compose 导入 Coolify。
- 生产使用根目录 `Dockerfile`：一个官方 PHP 8.5 + Apache 容器，一个 Coolify MySQL 8.4 资源。
- Node/Composer 只在镜像构建阶段使用，最终镜像没有 Codex、Node、npm 或 PHPUnit。没有 SSR、队列 Worker、调度任务、Mailpit 或真实邮件服务。
- Web 根目录仅为 `/var/www/html/public`，进程使用 `www-data`、监听容器内部 `8080`。
- 代码和产品图在镜像中；Session、Cache 和业务数据在 MySQL。当前无上传功能，不需要给整个应用目录挂持久卷。

### 核心运行逻辑

```text
Git 源码 → 安装锁定依赖 → 构建 Vue/CSS → PHP 生产镜像
Coolify 注入运行变量 → 生成 Laravel 配置/路由/视图缓存 → Apache 启动
访客 → HTTPS 反向代理 → Apache:8080 → Laravel → MySQL
```

启动脚本不会生成 APP_KEY、执行迁移或运行 Seeder。密钥保持稳定；数据库操作由人确认，不能因为容器重启而重复初始化数据。

## 2. 本地验证（不会连接开发数据库）

在 Windows 仓库终端执行，PHP/Node 的实际运行都在 Docker 内：

```powershell
docker build --tag nihonreach:production-local --file Dockerfile .
./scripts/Test-Production.ps1
```

验证脚本要求 PowerShell 7、Docker Desktop，以及本地已有 `mysql:8.4.11` 镜像。它创建随机名称的独立 Docker 网络和临时容器，数据库使用内存文件系统，只向 `127.0.0.1` 发布随机应用端口。结束时只清理本次创建的资源；不会运行 `docker compose down`，不会使用开发 MySQL。

覆盖范围：冷启动、真实 MySQL 迁移、生产 Seeder 不创建默认管理员、日文/中文页面、构建后 CSS、真实 CSRF 校验、后台登录、询盘保存与关闭、应用重启后的数据和 Session、非 root 运行以及镜像不包含开发工具/本地配置。

这不是浏览器端 E2E，也不验证公网 DNS/TLS。测试使用 HTTP 回环地址，所以仅在这个临时环境关闭 Secure Cookie；线上保持 `SESSION_SECURE_COOKIE=true`。

### 首次部署前的本地验证记录（历史记录）

以下数字来自部署准备节点，不代表后续提交或当前 CI 的最新结果：

- PHPUnit：102 个测试、515 个断言通过。
- PHPStan、修改文件的 Pint、Vue TypeScript、前端格式和 lint 通过。
- 多阶段生产镜像构建通过，前端构建处理 3323 个模块。
- `Test-Production.ps1` 全部检查通过，结束后已清理本轮临时容器、数据库合成数据和专用网络。
- 该次本地验收当时没有执行 Git 提交/推送或 VPS 操作。后续维护者已完成首次部署；备份恢复等尚未提供验收证据的项目不计入已完成能力。

## 3. Coolify 准备：先确认，再操作

以下是首次部署的操作模板。已有运行实例不要重复创建资源或初始化数据；后续变更先审阅 Diff 和测试结果，再由维护者决定提交、推送及部署。

### A. 数据库资源

在 NihonReach / production 中创建专用 MySQL 8.4 资源：

- 数据库名建议 `nihonreach`，应用使用专用非 root 用户，仅有该数据库权限。
- 确认数据库目录有持久存储，数据库不要设置公网访问端口。
- 应用和数据库选择可互通的 Coolify Destination/网络。
- 记录 Coolify 提供的内部主机名及账户，在应用中填写；不要把开发环境的 `DB_HOST=mysql` 当作线上固定主机名。
- 不复用其他项目的数据库、用户或密码，不使用 Coolify 面板自身的数据库。

### B. 应用资源

从 GitHub 创建应用，选择已由你提交、推送并验收的分支。私有仓库通过 Coolify 的 GitHub App 或专用只读 Deploy Key 授权，不把 Token 写进仓库 URL。[Coolify Dockerfile 文档](https://coolify.io/docs/applications/build-packs/dockerfile)

| 设置                         | 本项目值                                                       |
| ---------------------------- | -------------------------------------------------------------- |
| Build Pack                   | Dockerfile                                                     |
| Base Directory               | `/`                                                            |
| Dockerfile Location          | `/Dockerfile`                                                  |
| Docker build target（若有）  | `production`，或留空使用最后阶段                               |
| Ports Exposes                | `8080`                                                         |
| Ports Mappings               | 留空，不把 8080 发布到 VPS 公网                                |
| Domains                      | 你的完整 HTTPS 项目地址，例如 `https://nihonreach.example.com` |
| Start Command                | 留空，使用镜像 CMD                                             |
| Pre/Post Deployment Commands | 初次均留空，数据库初始化手动确认                               |
| Healthcheck                  | 启用，HTTP /up，内部端口 8080                                  |
| Auto Deploy                  | 初次先关闭，由你手动部署                                       |

这里填的是 NihonReach 应用域名，不是 Coolify 控制面板域名。Coolify 的 HTTPS 域名配置负责申请证书；DNS 已添加也不代表证书已经签发。[域名说明](https://coolify.io/docs/knowledge-base/domains)

### C. 运行变量

按 `docker/production/coolify.env.example` 填入 Coolify，不能直接上传开发 `.env`。

- `APP_URL` 为最终 HTTPS 项目地址。
- `APP_ENV=production`、`APP_DEBUG=false`、`SESSION_SECURE_COOKIE=true`。
- `APP_KEY` 只生成一次，保存在密码管理器及 Coolify；重新部署不能更换。
- APP_KEY 可以在本地 app 容器中人工执行 `php artisan key:generate --show` 获取：只显示，不覆盖开发 `.env`。把输出视作 Secret，不截图、不提交。
- 数据库密码和 APP_KEY 设为 **Runtime only**，取消 **Build Variable**；本 Dockerfile 构建不需要生产密钥。其他运行配置也无需传入构建阶段。若界面提供 `Inject Build Args to Dockerfile`，可关闭。[运行变量说明](https://coolify.io/docs/knowledge-base/environment-variables)
- `TRUSTED_PROXIES` 填实际反向代理 IP 或所在 Docker 网络 CIDR，多个值逗号分隔。可由运维在 VPS 查看实际 Destination 网络；不要猜地址、照抄示例或直接填 `*`。该设置用于识别 HTTPS 和访客 IP，关系到正确链接、登录 Cookie 与限流。
- `MAIL_MAILER=array` 丢弃邮件，不输出邮件链接到日志、不连接生产 SMTP。
- 保留数据库 Session/Cache；当前没有异步业务，`QUEUE_CONNECTION=sync` 即可。

## 4. 首次启动：数据库和管理员是人工确认点

首次启动后 `/up` 可以正常，而首页因空库尚未迁移而失败。这是初始化窗口：健康检查只证明应用能启动，不证明业务数据准备完成。初始化完毕前不要向面试官发送链接。

在 **NihonReach 应用容器终端**，确认环境为 production、数据库是刚创建的专用库后，人工执行：

```bash
php artisan migrate --force
php artisan db:seed --force
php artisan nihonreach:admin
```

- migrate 创建表结构；不要运行 `migrate:fresh`、`db:wipe`。
- Seeder 生成原创分类/产品及中日文内容，production 不会创建 README 中的固定 Demo 管理员。
- 后续不要在每次部署自动运行 Seeder：它会覆盖相同 slug/SKU 的演示内容，可能覆盖你在后台做的修改。
- 管理员命令交互询问邮箱、显示名和密码，密码不回显、不作为命令参数。至少 12 位且含大小写字母、数字和符号，两次一致才写入。
- 命令由具有容器终端权限的运维执行，会明确标记该管理员邮箱已验证；不是让公开访客绕过验证。
- 使用专用演示管理员标识，不向面试官共享 Coolify 登录、数据库口令或可修改/删除数据的管理员密码。后台能力可通过屏幕演示展示。

### 人工恢复

```bash
php artisan nihonreach:admin --reset-password
```

仅允许重置已有管理员，不会把普通用户提升为管理员。它重新确认邮箱验证状态、轮换记住登录 Token，并撤销该用户的数据库 Session。已有双因素认证和 Passkey 不变；丢失双因素设备需要预先保存的恢复码，不能把重置密码当成关闭 2FA。

当前账户设置仍包含官方脚手架的更换邮箱和自助删除能力；本轮没有重做账户系统。生产没有邮件，故不要用邮件找回；误改邮箱导致待验证时，运维可按新的邮箱执行恢复命令。若删除唯一管理员，可重新执行创建命令。对外展示前明确这一运维约定即可，不需要引入完整用户管理平台。

## 5. 公网验收与备份

人工检查以下项目后再写“已上线”：

1. 域名证书有效，无 HTTP 资源混入；中文 `/zh`、日文 `/`、语言切换与手机布局正常。
2. `/login` 可登录；未登录不能查看后台；分类/产品双语编辑能反映到公开页面。
3. 只填写合成数据提交询盘，后台能查看、关闭；重复保存 closed 不改完成时间；超过 20 条可翻页。
4. 重新部署相同代码后数据不丢失；APP_KEY 不变；Session 正常。
5. MySQL 做定期备份，备份至少保留一份在 VPS 之外；单独安全保存 APP_KEY。先恢复到一个临时数据库检查分类、产品、询盘数量和读取功能，不直接覆盖线上库。具体存储位置/保留期限由你选择，不在本轮连接外部存储。
6. 保留上一个已验收镜像/提交；上线失败可回到旧代码。数据库迁移不会随镜像回滚，涉及字段删除等变化必须先备份并制定单独恢复步骤。

当前没有真实邮件和文件上传、没有付费订单，不需要为了首次求职演示新增 Redis、对象存储或消息队列。Cloudflare 橙云不是部署必需；若开启，确认源站证书正常后使用 Full (strict)，不对带 Session 的页面设置 Cache Everything。

## 6. 关键 Diff 与面试表达

- 询盘更新区分状态转换和重复保存；列表使用每页 20 条分页并按时间/id 稳定排序。
- 公开参数拒绝数组形状，空筛选仍然可用；验证失败的旧输入只按标量文本回显。
- Dockerfile 区分构建与运行，启动时才读取生产变量并缓存，明确禁用未使用的 Inertia SSR。
- 可信代理按部署环境配置；管理员采用交互创建和运维恢复。

只有本地验证通过时，可说：“为 Laravel Portfolio Demo 准备多阶段生产镜像，并使用独立 MySQL 完成本地容器验收。”

根据已完成的首次部署，可以增加：“通过 Coolify 部署到个人 VPS，配置域名访问并完成数据库迁移及 Demo 数据初始化。”备份恢复只有实际完成恢复演练后才能单独写入；不能把这份清单或 `/up` 通过当作全部生产验收的证据。
