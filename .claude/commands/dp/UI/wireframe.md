---
argument-hint: [--action] [--page] [--spec] 
description: wireframe workflow
---

# workflow
1. 使用 前端工程师 roles/frontend-engineer.md 作为role来负责本次任务
2. 用获得的参数以合适形式驱动指定角色完成线框图(wireframe)相关工作
3. 可以读取所需的文件，进行相关操作和使用必须的工具
4. 以文字的形式反馈处理结果

# 命令 explain
- 输入的命令格式可以是 /wireframe generate user-login "add login button"
- 会包含如下变量
- "--action" 为必选参数, 表示行为, 如generate, new, create, update, modify等
- "--page" 为可选参数, 有可能不存在, 表示操作的页面, 在对应的role.md文件中有详细定义
- "--spec" 为必选参数, 默认值为"", 表示用户的需求, 可以是多行markdown文本。 请特别注意这里并不是完整需求，部分需求仍有可能存放于相关文档中，所以需要充分读取关联文档, 从中提取关联内容作为补充，以保证需求的完整性。





