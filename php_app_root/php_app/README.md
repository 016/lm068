# PHP视频内容展示网站

## 项目简介
这是一个基于原生PHP 8.4开发的视频内容展示网站，采用MVC架构模式，支持用户前端和管理后端。

## 技术栈
- **后端**: 原生PHP 8.4.13RC1
- **数据库**: MySQL 5.7.40
- **前端**: HTML5, Bootstrap 5.3, JavaScript
- **部署**: Docker Nginx 1.23.3 + PHP-FPM 8.4.13RC1

## 项目结构
```
php_app/
├── app/                      # 主要应用代码
│   ├── Controllers/          # 控制器层
│   │   ├── Backend/          # 后台控制器
│   │   └── Frontend/         # 前台控制器
│   ├── Models/               # 模型层
│   ├── Views/                # 视图层
│   │   ├── backend/          # 后台视图
│   │   └── frontend/         # 前台视图
│   └── Core/                 # 核心框架类
├── config/                   # 配置文件
├── public_backend/           # 后台入口 (admin.domain.com)
├── public_frontend/          # 前台入口 (www.domain.com)
├── runtime/logs/             # 日志文件
└── vendor/                   # Composer依赖
```

## 功能模块

### 用户前端功能
- 视频列表查看
- 视频详情查看  
- 评论发布与管理
- 视频收藏
- 邮件订阅
- 多语言支持 (中文/英文)

### 管理后台功能
- 用户管理 (状态控制、封停功能)
- 视频管理 (上传链接、编辑、删除)
- 标签管理
- 视频合集管理
- 邮件订阅列表管理
- 视频数据分析

## 安装说明

### 1. 环境要求
- PHP >= 7.4.0
- MySQL >= 5.7
- Nginx (推荐)

### 2. init
1. cd pathTo/php_app/
2. composer install
3. change db in

### 3. 配置数据库+配置
1. 导入 `database/schema.sql` 到MySQL数据库 // 也可以手动导入初始 MYSQL数据
2. 修改 `config/database.local.php` 中的数据库连接信息
3. 修改 `config/main.local.php` 中的静态资源域名
4. 手动导入配套资源. 如: 对应content的封面图片等
5. 修改默认管理员账号，系统默认初始化账号为 admin/admin

### 4. 配置Web服务器
- 前台入口: 将 `public_frontend/` 目录配置为网站根目录
- 后台入口: 将 `public_backend/` 目录配置为管理后台根目录
- 资源目录: 将 `../public_resources/uploads/` 配置为资源访问目录

### 5. 测试访问
- 访问前台: `http://your-domain.com/test` 查看 Hello World 测试页面
- 访问后台: `http://admin.your-domain.com/dashboard`

### 6. 其他支持
- Sitemap 生成命令
    - php php_app_root/php_app/public_backend/index.php /sitemap/generate > php_app_root/php_app/public_frontend/sitemap.xml
    - crontab -e
    - 0 4 * * * docker exec ee-php-fpm-8.4.13 php /pathToProject/php_app_root/php_app/public_backend/index.php /sitemap/generate > /pathToProject/php_app_root/php_app/public_frontend/sitemap.xml 2>&1
    - crontab -l
- content.pv 定时更新任务
    - php php_app_root/php_app/public_backend/index.php /statistics/daily-pv-cal?date=2025-10-30 //指定时间
    - php php_app_root/php_app/public_backend/index.php /statistics/daily-pv-cal //默认时间前一天
    - php php_app_root/php_app/public_backend/index.php /statistics/repair-full-pv-cal
    - crontab -e
    - 0 2 * * * docker exec ee-php-fpm-8.4.13 php /pathToProject/php_app_root/php_app/public_backend/index.php /statistics/daily-pv-cal >> /pathToLog/daily_pv_cal.log 2>&1
    - 0 3 * * 0 docker exec ee-php-fpm-8.4.13 php /pathToProject/php_app_root/php_app/public_backend/index.php /statistics/repair-full-pv-cal >> /pathToLog/weekly_full_pv_cal.log 2>&1
    - crontab -l
## 开发规范

### 命名规范
- 类名: PascalCase (例: UserController)
- 方法名: camelCase (例: getUserList)
- 变量名: camelCase (例: $userName)
- 数据库表名: snake_case (例: admin_user)

### 目录规范
- 控制器文件放在对应的 `Controllers/Backend/` 或 `Controllers/Frontend/` 目录
- 模型文件放在 `Models/` 目录
- 视图文件放在对应的 `Views/backend/` 或 `Views/frontend/` 目录

### 安全规范
- 所有数据库操作使用预处理语句
- 用户输入必须进行验证和过滤
- 密码使用 password_hash() 进行加密
- 不在版本库中提交敏感配置信息

## API路由

### 前端路由
- `GET /` - 首页
- `GET /test` - 测试页面
- `GET /videos` - 视频列表
- `GET /videos/{id}` - 视频详情

### 后台路由
- `GET /dashboard` - 数据面板
- `GET /content` - 内容管理
- `GET /users` - 用户管理
- `GET /tags` - 标签管理

## 许可证
MIT License