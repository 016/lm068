# 后台登录"记住我"功能实现说明

## 功能概述
实现了后台管理员登录页面的"记住我"功能，用户选中该选项后，登录状态将持久化保存在浏览器中，即使关闭浏览器也能保持登录状态。

## 实现方案
采用 **Cookie + 数据库 Token** 的方式实现持久化登录：
1. 用户勾选"记住我"后，生成随机 token
2. Token 经过 SHA256 哈希后存储到数据库
3. 原始 token 存储到浏览器 Cookie（设置为 HttpOnly）
4. 用户下次访问时，如果 session 不存在，自动通过 Cookie token 验证并登录

## 技术实现细节

### 1. 配置文件修改
**文件**: `php_app_root/php_app/config/main.php`

添加了 `remember_me` 配置项：
- `enabled`: 是否启用记住我功能
- `cookie_name`: Cookie 名称
- `cookie_lifetime`: Cookie 有效期（默认 30 天）
- `cookie_httponly`: HttpOnly 标志（防止 JavaScript 访问）
- `cookie_samesite`: SameSite 属性（CSRF 保护）

### 2. 数据库表结构修改
**文件**:
- `database/schema.sql` - 更新了表结构定义
- `database/migration_add_remember_token.sql` - 数据库迁移文件

在 `admin_user` 表中添加了：
- `remember_token` 字段: VARCHAR(64)，存储 SHA256 哈希后的 token
- `idx_remember_token` 索引: 提高查询性能

**执行迁移**:
```bash
mysql -u your_username -p lm068 < database/migration_add_remember_token.sql
```

### 3. AdminUser 模型增强
**文件**: `php_app_root/php_app/Models/AdminUser.php`

新增方法：
- `generateRememberToken()`: 生成 64 字符的随机 token
- `saveRememberToken($adminId, $token)`: 保存哈希后的 token 到数据库
- `findByRememberToken($token)`: 通过 token 查找管理员
- `clearRememberToken($adminId)`: 清除数据库中的 token

### 4. AuthController 登录逻辑升级
**文件**: `php_app_root/php_app/Controllers/Backend/AuthController.php`

**login() 方法修改**:
- 检查用户是否勾选"记住我"
- 生成并保存 token 到数据库
- 设置 Cookie（包含安全配置）

**logout() 方法修改**:
- 清除数据库中的 remember token
- 删除浏览器 Cookie
- 销毁 session

### 5. BackendController 认证检查增强
**文件**: `php_app_root/php_app/Controllers/Backend/BackendController.php`

**requireAuth() 方法修改**:
1. 首先检查 session 是否存在
2. 如果 session 不存在，检查 Cookie 中的 token
3. 通过 token 查询数据库，验证有效性
4. 验证成功后自动登录（设置 session）
5. 更新 Cookie 过期时间（滑动过期）
6. 验证失败则删除无效 Cookie

## 安全特性

### 1. Token 安全
- **随机性**: 使用 `random_bytes(32)` 生成 64 字符的随机 token
- **哈希存储**: 数据库中存储 SHA256 哈希值，原始 token 不保存
- **唯一性**: 每次登录生成新的 token

### 2. Cookie 安全
- **HttpOnly**: 防止 JavaScript 访问，降低 XSS 风险
- **SameSite=Lax**: 防止 CSRF 攻击
- **有限期限**: 默认 30 天自动过期
- **可配置 Secure**: 生产环境建议启用（需要 HTTPS）

### 3. 其他安全措施
- 退出登录时清除数据库 token 和 Cookie
- Token 验证失败时自动删除 Cookie
- 支持滑动过期（每次访问延长有效期）

## 使用说明

### 用户端操作
1. 访问后台登录页面
2. 输入用户名和密码
3. 勾选"记住我"选项
4. 点击"登录系统"
5. 关闭浏览器后再次访问，自动保持登录状态

### 管理员配置
可以在 `config/main.php` 中调整配置：
```php
'remember_me' => [
    'enabled' => true,                    // 是否启用
    'cookie_lifetime' => 30 * 24 * 60 * 60, // 有效期（秒）
    'cookie_secure' => false,             // 生产环境改为 true
    // ... 其他配置
]
```

## 测试建议

### 功能测试
1. **基本登录**: 不勾选"记住我"，关闭浏览器后应该退出登录
2. **记住我功能**: 勾选"记住我"，关闭浏览器后应该保持登录
3. **主动退出**: 点击退出后，"记住我"功能应失效
4. **Token 过期**: 等待 30 天后，自动退出登录
5. **并发登录**: 同一账号多设备登录，后登录会覆盖 token

### 安全测试
1. 查看数据库，确认 token 是哈希存储
2. 检查 Cookie 属性（HttpOnly、SameSite）
3. 尝试篡改 Cookie 值，应该自动退出
4. 清空数据库 token，Cookie 应失效

## 注意事项

1. **数据库迁移**: 首次使用需要执行数据库迁移脚本
2. **HTTPS**: 生产环境建议启用 `cookie_secure`，需要 HTTPS 支持
3. **Token 唯一性**: 当前实现每次登录会覆盖旧 token，不支持多设备同时保持登录
4. **性能**: 已为 `remember_token` 字段添加索引，查询性能良好

## 文件清单

修改的文件：
- `php_app_root/php_app/config/main.php`
- `php_app_root/php_app/Models/AdminUser.php`
- `php_app_root/php_app/Controllers/Backend/AuthController.php`
- `php_app_root/php_app/Controllers/Backend/BackendController.php`
- `database/schema.sql`

新增的文件：
- `database/migration_add_remember_token.sql`

## 技术栈
- PHP 8.4.13RC1
- MySQL 5.7.40
- Cookie + Session 混合认证
- SHA256 哈希算法
