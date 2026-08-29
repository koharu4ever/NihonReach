# NihonReach

NihonReach 是面向求职展示的精密刀具 B2B Portfolio Demo。目前处于 **Phase 0：开发底座**，尚未生成 Laravel 业务代码，也不能作为完成项目写入简历。

## 开发结构

```text
Windows
├─ Docker Desktop
├─ VS Code
└─ 本地工作区（含被 Git 忽略的 `.env`）
      │
      ▼
app（Dev Container）
├─ PHP 8.5 / Composer 2
├─ Node 24 / npm
├─ Git / Codex CLI
└─ Laravel 与前端工具（下一阶段生成）
      │
      ├─ mysql：MySQL 8.4 LTS
      ├─ mailpit：开发邮件捕获
      └─ Docker 命名卷：Codex 登录状态与开发缓存
```

MySQL 的 `3306` 和 Mailpit 的 SMTP `1025` 不发布到 Windows。Laravel、Vite 和 Mailpit Web UI 通过 VS Code Dev Containers 转发端口。

## 第一次启动

前置条件：Docker Desktop 已启动，VS Code 已安装 Dev Containers 扩展。

1. 用 VS Code 打开本仓库。
2. 将 `.env.example` 复制为 `.env`，为两个变量填写仅供本机使用、互不相同的随机值；`.env` 已被 Git 和 Docker 构建上下文忽略。
3. 首次使用且本地镜像不存在时，在仓库根目录执行 `docker compose build app`，显式生成本地 `nihonreach-app:dev` 镜像。
4. 执行 `Dev Containers: Reopen in Container`。
5. 等待 MySQL、Mailpit 通过健康检查。
6. `post-create.sh` 会继续验证 app 工具链以及数据库、邮件服务连通性。

VS Code 使用 `.devcontainer/compose.devcontainer.yaml` 复用这个本地镜像，不会在每次打开容器时重新查询所有基础镜像。该覆盖文件设置了 `pull_policy: never`，因此本地镜像不存在时会明确失败，不会从远端误拉同名镜像。

Dockerfile 或 Compose 的 build args 改动后，先执行 `docker compose build app`，再执行 `Dev Containers: Rebuild and Reopen in Container`；仅执行普通 Reopen 可能继续使用旧容器。首次下载基础镜像仍要求 Docker Desktop 能访问对应镜像源，这个覆盖只避免 Dev Containers 每次打开时自动重复构建和刷新标签。

进入容器后，也可以手动重跑：

```bash
bash .devcontainer/scripts/smoke-test.sh
```

不经过 VS Code 时，可以从仓库根目录验证 Compose：

```powershell
docker compose build app
docker compose up -d
docker compose exec app bash .devcontainer/scripts/smoke-test.sh
```

正常停止并保留 MySQL 数据：

```powershell
docker compose down
```

`docker compose down -v` 会删除本项目的 MySQL 数据、容器内 Codex 状态以及 Composer/npm 缓存，除非明确需要重置，否则不要执行。

## 本地配置边界

数据库口令只保存在本机、被 Git 忽略的 `.env` 中，不写入 `compose.yaml`、镜像或 Git 历史。MySQL 端口未发布到 Windows；这些开发值也不得复制到 VPS，生产环境以后使用独立 Secret。

Codex CLI 只把程序安装进镜像，不包含登录信息。首次在容器中运行 `codex` 时再交互登录；状态保存在 Docker 命名卷，不进入 Git。安装与登录方式参考 [OpenAI 官方 Codex CLI 文档](https://learn.chatgpt.com/docs/codex/cli)。

## Git 身份

仓库已经初始化为 `main`，但尚未设置作者，也没有创建提交。在确定本人控制的 Git 身份前，不应提交。

查看最终生效来源：

```bash
git config --show-origin --get user.name
git config --show-origin --get user.email
```

确认后只为本仓库设置：

```bash
git config --local user.name "你的 Git 显示名"
git config --local user.email "你本人控制并已验证的邮箱或 noreply 邮箱"
```

## 当前版本边界

- 已做：Git 初始化、Dev Container、PHP/Node/Composer/Codex 工具链、MySQL、Mailpit、基础规则和冒烟测试。
- 未做：Laravel 脚手架、数据库 Migration、公开站、后台、测试业务、CI、VPS 部署。
- Redis 暂不加入；出现真实队列或缓存需求时再决策。
