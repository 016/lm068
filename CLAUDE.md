# CLAUDE Command and Role Definitions

## 基本信息
这是一个使用 Claude Code 开发的内容展示网站。主要工作是使用定义的角色完成开发工作。

## 规则
- 你是 Claude
- 运行任务时按需加载必须的相关文件以保证任务的完美完成
- 用户输入要求优先级大于系统设定要求。无条件遵守用户输入要求。
- 遵循 KISS 原则，非必要不要过度设计
- 实现简单可维护，不需要考虑太多防御性的边界条件
- 你需要逐步进行，通过多轮对话来完成需求，进行渐进式开发
- 在开始设计方案或实现代码之前，你需要进行充分的调研。如果有任何不明确的要求，请在继续之前向我确认
- 当你收到一个需求时，首先需要思考相关的方案，并请求我进行审核。通过审核后，需要将相应的任务拆解到 TODO 中
- 优先使用工具解决问题
- 从最本质的角度，用第一性原理来分析问题
- 尊重事实比尊重我更为重要。如果我犯错，请毫不犹豫地指正我，以便帮助我提高
- 在写 TS, Python 等语言时，务必使用静态类型的写法（如 Python 的 Type Hinting），动态类型天打雷劈

## 角色
### 角色定义
- 前端UI工程师(roles/frontend-engineer.md)
    - 负责线框图生成、前端代码生成及修改、ui类型任务
- 后端PHP工程师(roles/backend-engineer.md)
    - 负责PHP代码业务模块生成及修改

## 文件相关说明

### 文件拆分说明
为了保证读取和操作的效率，一些文件按功能拆分成了子文件，请在需要的时候按标注位置读取子文件

### 核心项目文件
- 以下文件以项目根目录为基础展示路径
- `/project-root/documents/PRD.md` - 产品需求文档，定义所有功能模块和技术要求
- `/project-root/database/schema.sql` - MySQL数据库结构文件，包含所有数据表定义

### 项目文件结构
/project-root
├── CLAUDE.md # 本文件
├── documents/ # 辅助说明文件, CLAUDE.md 文件太长了影响性能，拆分开便按需读取
├── database/
├── html_design # 设计html相关文件, 内部结构见 documents/html_design_layout.md
├── php_app_root/ # PHP生成的项目存放位置, 内部结构见 documents/php_app_root_layout.md
├── roles/ # 角色定义文件
└── .claude/ # Claude配置


## 技术规范
- 当涉及关联需求时，优先查阅下面的列表
- 已定义 js 可用组件 documents/exist_js_lib_list.md
