---
trigger: always_on
---

# Project basic rule

## 基本信息
This is a 游戏下载站的后台和API程序 on PHP。主要工作是开发。

## Rules
- 前台和后台都有自己的样式，请在工作中尽量保持样式的统一


## 工作流程自动匹配
- 本共涉及两个类型的工作, 根据收到的任务类型自动匹配下面的工作流程
    - html页面相关修改/设计工作匹配 html-worker(.agent/workflows/html-worker.md)
    - php开发相关工作使用 php-worker(.agent/workflows/php-worker.md)


## 文件相关说明

### 文件拆分说明
为了保证读取和操作的效率，一些文件按功能拆分成了子文件，请在需要的时候按标注位置读取子文件

### 核心项目文件
- 以下文件以项目根目录为基础展示路径
- `documents/PRD.md` - 产品需求文档，定义所有功能模块和技术要求

### 项目文件结构
/project-root
├── documents/ # 辅助说明文件
├── database/
├── html_design # 设计html相关文件, 内部结构见 (documents/html_design_layout.md)
└── php_app_root/ # PHP生成的项目存放位置, 内部结构见 (documents/php_app_root_layout.md)