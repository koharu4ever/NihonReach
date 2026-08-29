# NihonReach 协作规则

## 项目性质

- NihonReach 是求职用 Portfolio Demo，不是已商业运营的公司或客户项目。
- 所有企业、产品、客户、案例、询盘和经营数据必须是原创或明确标注的演示数据。
- 不复制前雇主的 Logo、受版权保护图片、内部资料、客户信息、图纸或未公开参数。
- 不伪造作者、日期、客户、流量、转化率或“独立完成”等履历证据。

## 开发方式

- Windows 只负责 Docker Desktop、VS Code 和凭据管理。
- PHP、Composer、Node、npm、数据库客户端和项目级 Codex CLI 命令在 `app` 容器内运行。
- 不向开发容器挂载 Docker socket、宿主机整个 `.ssh`、`.codex` 或生产 Secret。
- 不在仓库、镜像、示例配置、日志或文档中写入真实 Token、密码和个人信息。
- Dev Container 仅用于开发；VPS 生产环境使用单独的精简镜像与 Secret。

## 当前技术边界

- Laravel 单体应用；公开站使用 Blade，后台使用 Inertia 3 + Vue 3 + TypeScript。
- MySQL 和 Mailpit 是独立 Compose 服务。
- Phase 0 不加入 Redis、独立 API、微服务、对象存储或生产邮件。
- 未经新的明确决策，不扩大 MVP 范围。

## 教学与验收

每个关键节点都用中文说明：

1. 做什么。
2. 为什么这样做。
3. 核心运行逻辑。
4. 关键 Diff。
5. 如何验证。
6. 面试时怎样如实表达。

每次改动保持范围小、可审阅、可测试。只有真实实现并通过验证的能力，才可以写进简历。
