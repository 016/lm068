# CLAUDE Command and Role Definitions

## 基本信息
这是一个使用 Claude Code 开发的视频展示网站。主要工作是使用定义的角色完成开发工作。

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

### 核心项目文件
- 以下文件以项目根目录为基础展示路径
- `/project-root/PRD.md` - 产品需求文档，定义所有功能模块和技术要求
- `/project-root/database/schema.sql` - MySQL数据库结构文件，包含所有数据表定义
- `/project-root/roles/frontend-engineer.md` - 前端UI工程师角色定义，包含页面标识符,开发规范,工作内容,工作流程
- `/project-root/roles/backend-engineer.md` - 后端PHP工程师角色定义，包含模块标识符,开发规范,工作内容,工作流程

### 项目文件结构
/project-root
├── CLAUDE.md # 本文件
├── PRD.md # 产品需求文档
├── database/
│ └── schema.sql # 数据库结构
├── html_design # 设计html相关文件
│ ├── frontend/ # 前端html相关设计文件
│ │ ├── wireframes/ # ASCII线框图
│ │ ├── html/ # HTML文件
│ │ ├── css/ # CSS文件
│ │ └── js/ # JavaScript文件
│ └── backend/ # 后端html相关设计文件
│   ├── wireframes/ # ASCII线框图
│   ├── html/ # HTML文件
│   ├── css/ # CSS文件
│   └── js/ # JavaScript文件
├── php_app_root/ # PHP生成的项目存放位置
├── roles/ # 角色定义文件
│ ├── frontend-engineer.md
│ └── backend-engineer.md
└── .claude/ # Claude配置
  ├── commands/ # 自定义斜杠命令
  └── settings.json # 项目设置

#### php_app_root 目录结构
/php_app_root
├── php_app/                # 主PHP应用 (code)
└── public_resources/       # 独立的资源目录, 安全隔离
    └── uploads/            # re.domain.com 指向这里
        ├── avatars/
        ├── thumbnails/
        └── videos_preview/

##### php_app 目录结构
php_app/
├── Models/                   # 模型层 (M) - 数据库交互, 业务逻辑 (前后端共用)
├── Views/                    # 视图层 (V)
│   ├── backend/              # 后台视图 (以子文件夹结构来和controller呼应)
│   │   ├──layouts/           # 布局文件 (如头部, 尾部, 侧边栏)
│   │   └──videos/            # 以video举例
│   └── frontend/             # 前台视图 (您的前台HTML页面放这里)
│       ├── layouts/          # 布局文件
│       └── videos/           # 以video举例
├── Controllers/              # 控制器层 (C)
│   ├── Backend/              # 后台控制器
│   └── Frontend/             # 前台控制器
├── Core/                     # 核心框架类
│   ├── Router.php            # 路由器
│   ├── Request.php           # 请求处理
│   ├── Database.php          # 数据库连接
│   ├── Controller.php        # 基础控制器
│   └── Model.php             # 基础模型
├── Constants/                # 常量存放位置
├── config/                   # 配置文件
│   ├── main.php              # 应用主配置 (时区, 密钥等)
│   ├── database.php          # 默认数据库配置
│   └── database.local.php    # 本地数据库配置 (此文件不提交到版本库)
├── scripts/                  # 测试脚本存放位置
│
├── public_backend/           # 【后台入口】(admin.yourdomain.com 指向这里)
│   ├── assets/               # 后台静态资源 (CSS, JS, Images)
│   └── index.php             # 后台唯一入口文件
│
├── public_frontend/          # 【前端入口】(www.yourdomain.com 指向这里)
│   ├── assets/               # 前台静态资源
│   └── index.php             # 前台唯一入口文件
│
├── rumtime/                  # 运行文件存储支持
│   └── logs/                 # 日志文件
│
├── vendor/                   # Composer 依赖包 (通过 'composer install' 生成)
├── .gitignore                # Git 忽略配置
├── composer.json             # Composer 依赖管理文件
└── README.md                 # 项目说明文件


## 技术规范

### 已定义 js 可用组件
- 当需求符合时，优先使用本列表中定义的内容
- backend
  - 向用户展示提示信息。 main.js showToast() 
- frontend
