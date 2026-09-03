# GitHub 仓库展示与分支保护

这些设置保存在 GitHub，不会随 PR 合并自动生效。本文件记录建议配置，不代表已经启用；由仓库维护者在 GitHub 确认并保存。

## About

在仓库首页右侧 About 的设置中填写：

- Description：`Bilingual B2B product catalog and inquiry management portfolio built with Laravel 13, Blade, Inertia, Vue 3, TypeScript and MySQL.`
- Website：`https://nihonreach.kral-koharu.com/zh`
- Topics：`laravel`、`php`、`vue`、`inertiajs`、`typescript`、`mysql`、`docker`、`bilingual`、`portfolio`、`b2b`

许可证通过仓库根目录的 [LICENSE](../LICENSE) 提供，不需要在 About 中手写许可证标签。

## main Ruleset

在 Settings → Rules → Rulesets 创建一个分支规则集：

| 配置                                  | 建议值                                            |
| ------------------------------------- | ------------------------------------------------- |
| 名称                                  | `main-quality-gate`                               |
| Enforcement status                    | `Active`                                          |
| Target branches                       | 仅 `main`                                         |
| Require a pull request before merging | 启用                                              |
| Required approvals                    | `0`（当前为个人作品集，避免无法给自己的 PR 审批） |
| Require status checks to pass         | 启用，选择 `Quality checks`，来源 GitHub Actions  |
| Block force pushes                    | 启用                                              |
| Restrict deletions                    | 启用                                              |
| Bypass list                           | 默认留空；不要为了方便日常提交而绕过检查          |

`Quality checks` 对应 [.github/workflows/ci.yml](../.github/workflows/ci.yml) 中的 job 名称。若选项尚未出现，先让该 workflow 实际成功运行一次，再添加检查；不要手填不存在的检查名称。要求 PR 不等于要求他人审批，未来有协作者时再调整审批人数。

保存后，重新打开规则集确认其为 Active、范围仅为 main，并检查 PR 页面是否要求 `Quality checks` 通过。不要通过强推或删除 main 来测试保护。

GitHub 官方说明：[Ruleset 可用规则](https://docs.github.com/en/repositories/configuring-branches-and-merges-in-your-repository/managing-rulesets/available-rules-for-rulesets)。CI workflow、远端 Ruleset 和部署是三件不同的事；本文件不配置自动上线。
