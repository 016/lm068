---
argument-hint: [--action] [--page] [--spec] 
description: wireframe workflow
---

# workflow
1. 总是先读取前端工程师角色定义文件(roles/frontend-engineer.md), 使用该角色来负责当前任务
2. 先充分分析用户输入的[--spec]参数, 尝试读取所有关联的文件，从中获取必要的内容。注意[--spec]参数并不是完整需求，部分需求仍有可能存放于相关文档中，需要充分读取关联文档, 从中提取关联内容作为补充，以保证需求的完整性。
3. 用获得的参数以合适形式驱动指定角色完成线框图(wireframe)相关工作
4. 可以读取所需的文件，进行相关操作和使用必须的工具
5. 以文字的形式反馈处理结果

# 命令 explain
- 输入的命令格式可以是 /wireframe generate user-login "add login button"
- 会包含如下变量
- "--action" 为必选参数, 表示行为, 如generate, new, create, update, modify等
- "--page" 为可选参数, 有可能不存在, 表示操作的页面, 在对应的role.md文件中有详细定义
- "--spec" 为必选参数, 默认值为"", 表示用户的需求, 可以是多行markdown文本。





