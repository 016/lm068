##### php_app 目录结构
/php_app
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
├── Helpers/                  # 无状态的工具类存放位置, 提供可复用的静态方法
├── Interfaces/               # Interface 存放位置
├── Constants/                # Constant 存放位置
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