# NihonReach Portfolio 实现与验收说明

> 本项目是求职用 Portfolio Demo，不是商业运营项目。所有企业、产品、规格、询盘与经营场景均为原创演示数据。

## 1. 做了什么

| 模块     | 公开端                 | 管理端                                 | 后端与数据                                     |
| -------- | ---------------------- | -------------------------------------- | ---------------------------------------------- |
| 产品分类 | 中日文分类筛选         | 双语新增、编辑、删除、排序、启停       | 主表、翻译表、Model、Factory、幂等 Seeder      |
| 产品     | 中日文目录、分页、详情 | 双语新增、编辑、删除、推荐、启停、规格 | 分类外键、翻译表、JSON 规格、唯一 slug/SKU     |
| 询盘     | 表单、产品预选、感谢页 | 列表、详情、三态流转                   | 可选产品外键、处理时间、限流                   |
| 权限     | 管理员登录入口         | 后台统一保护                           | `auth + verified + admin` 中间件               |
| 质量     | Blade 响应测试         | Inertia props/权限测试                 | Pint、PHPStan、PHPUnit、Vue 类型检查、生产构建 |

公开注册已关闭。本地 Seeder 创建一个明确标注的 Demo 管理员，并创建 4 个分类和 6 个原创 Demo 产品。

## 2. 为什么这样做

- 选择 Laravel 单体应用：公开内容与后台共享同一领域模型，当前规模不需要独立 API 或微服务。
- 公开站使用 Blade：产品目录主要是服务端渲染内容，路由与 SEO 语义简单。
- 后台使用 Inertia + Vue：CRUD 表单和状态交互更适合组件化，同时无需维护第二套 API 鉴权。
- 询盘只做三态：`new`、`in_progress`、`closed` 已足以展示业务闭环；指派、内部备注和真实邮件属于超出 MVP 的 CRM 能力。
- 产品图片只保存可选公开路径：本阶段没有对象存储或上传安全需求，避免引入未使用基础设施。
- 所有展示数据由 Seeder 明确生成：便于复现，同时避免使用前雇主或真实客户资产。

## 3. 核心运行逻辑

### 3.1 公开产品可见性

产品必须同时满足自身 `is_active = true`，并且所属分类也为启用状态。目录查询、详情查询和询盘产品验证都执行同一业务约束；不能只依赖前端隐藏选项。

### 3.2 管理员边界

`EnsureUserIsAdmin` 在认证和邮箱验证之后检查 `users.is_admin`。普通登录用户不能访问仪表盘或任何 `/admin/*` 资源；公开注册入口和 Fortify 注册功能都已关闭。

### 3.3 询盘提交

1. 访客可从产品详情进入表单，slug 只用于预选。
2. 服务端根据数据库重新生成可选产品集合。
3. `InquiryRequest` 验证联系人、邮箱、内容长度、隐私同意和产品可见性。
4. `privacy` 只用于请求验证，不写入数据库；状态由服务器强制设为 `new`。
5. POST 路由限制为每分钟 5 次，成功后跳转到 Demo 感谢页。

### 3.4 状态流转

管理员可将询盘更新为 `new`、`in_progress` 或 `closed`。从非关闭状态进入 `closed` 时写入 `handled_at`；重复保存关闭状态保留原完成时间，重新打开时清空。后台列表每页 20 条，按收到时间和 id 倒序排列。

### 3.5 可复现 Demo 数据

分类和产品 Seeder 使用稳定 slug/SKU 做 `updateOrCreate`，管理员使用稳定 Demo 邮箱做 `firstOrNew`。因此重复执行 Seed 会更新演示内容，而不会持续插入重复记录。

### 3.6 中日文运行逻辑

日文公开站使用原有 URL（例如 `/products`），中文站使用 `/zh` 前缀（例如 `/zh/products`）。`SetLocale` 中间件先确定当前语言，Controller 预加载翻译关系，Blade 再通过 `translated()` 读取对应语言；中文记录缺失时回退日文主数据。

分类和产品的 slug、SKU、图片、上下架及排序属于共享业务字段，只保存在主表。名称、摘要、说明和规格属于可翻译内容：日文保留在主表，中文存入独立翻译表。中文后台的同一个表单会同时提交两组语言内容，并在数据库事务中一起保存，避免只更新一半。

## 4. 关键 Diff

- `app/Models`：新增 `ProductCategory`、`Product`、`Inquiry`、翻译模型及其关系、类型注释和 casts。
- `database/migrations`：新增分类、产品、中文翻译、管理员标记和询盘表结构。
- `app/Http/Requests`：把字段验证、唯一性、状态白名单和公开产品约束放在 Form Request。
- `app/Http/Controllers/Site`：公开查询仅返回可见数据；询盘保存不接受客户端状态。
- `app/Http/Controllers/Admin`：分类/产品 CRUD、询盘只读详情和有限状态更新。
- `resources/views/public`：中日文 Blade 首页、目录、详情、关于、询盘、语言切换和 Demo 数据声明。
- `resources/js/pages/admin`：中文 Vue 分类/产品双语 CRUD 与询盘管理页面。
- `tests/Feature`：覆盖公开可见性、权限、CRUD、验证、状态时间戳和 Inertia props。

## 5. 如何验证

在 `app` 容器内执行：

```bash
php artisan migrate --seed
npm run check
npm run types:check
npm run build
composer lint:check
composer types:check
php artisan test
```

开发数据库的重复 Seeder 检查结果应保持为：1 个 Demo 管理员、4 个分类、6 个产品；询盘默认不预置。

质量门禁包含前端格式/lint、Vue/TypeScript 类型检查、生产构建、Pint、PHPStan 和 PHPUnit；`composer ci:check` 会先生成 Wayfinder 类型，以支持全新检出。[CI 工作流](../.github/workflows/ci.yml) 在 PR 和 main 推送时执行同一入口，不使用生产 Secret，也不自动部署。配置存在不等于远端已通过，具体结果以对应提交的 Actions 记录为准。

回归测试覆盖语言切换时路径参数不能被查询参数覆盖、普通用户后台写请求被拒绝且数据不变、询盘限流与恢复，以及异常输入、重复关闭、分页、可信代理和管理员命令。部署验收范围见 [Coolify 部署说明](coolify-deployment.md)，具体测试数字以对应版本的命令输出为准。

## 6. 学到了什么

- 如何把“产品可见”这种业务规则同时落实在列表、详情和写入验证，而不是只做 UI 隐藏。
- 如何用外键删除策略表达领域语义：分类被产品引用时限制删除，产品删除后询盘保留但 `product_id` 置空。
- 如何在 Laravel 单体中划分 Blade 公开端与 Inertia/Vue 后台，并复用同一模型和认证会话。
- 如何使用 Form Request、路由模型绑定、中间件和服务器端状态白名单收紧信任边界。
- 如何用 Factory + RefreshDatabase 构造隔离测试，并验证数据库副作用、重定向和 Inertia props。
- 如何让 Seeder 幂等、让 Demo 数据可复现，并通过静态分析和前后端类型检查发现隐性问题。
- 如何用 locale 中间件、语言前缀路由和翻译关系实现中日文前台，并让后台在一个事务内维护两种语言。

## 7. 面试时怎样如实表达

可以说：

> 我在 Codex 辅助下完成了一个 Laravel 单体 Portfolio Demo。公开端用 Blade 提供中日文产品目录，后台用 Inertia 3、Vue 3 和 TypeScript 以中文管理双语分类、产品和询盘。我负责确认需求边界、审阅实现、运行迁移与自动化测试，并能解释语言路由与数据回退、公开可见性、管理员中间件、Form Request 验证、状态时间戳和测试设计。

根据已完成的人工部署验收，还可以补充：“将作品集通过 Coolify 部署到个人 VPS，配置域名访问并完成数据库迁移及 Demo 数据初始化。”CI 只有在远端实际运行通过后，才能说“该提交已通过 GitHub Actions 检查”；配置本身不是运行证据。

不要说：

- 这是已上线或有真实客户使用的商业项目。
- Demo 产品、客户、询盘或转化数据是真实经营数据。
- 已实现生产邮件、对象存储、独立 API、Redis、微服务，或未经演练就声称备份恢复已验证。
- 在无法解释代码和测试依据时声称完全独立完成。

简历中只应写入已经实现并通过最终验收的能力；本说明中的测试数字也应随实际最终输出更新。
