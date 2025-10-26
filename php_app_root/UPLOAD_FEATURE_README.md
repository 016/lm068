# 文件上传功能说明文档

## 功能概述

本次更新实现了基于 MVC 架构的文件上传功能，支持图片和通用文件上传。主要应用于内容(Content)模块的缩略图上传。

## 主要特性

### 1. 配置文件支持
- **主配置文件**: `php_app/config/main.php`
  - 定义上传路径配置
  - 定义资源 URL 前缀
  - 定义允许的文件类型

- **本地配置文件**: `php_app/config/main.local.php` (不提交到版本库)
  - 用于本地开发环境覆盖配置
  - 支持自定义本地上传路径和URL前缀

### 2. 文件上传基础类
- **Config 类**: `php_app/Core/Config.php`
  - 支持配置文件加载和合并
  - 支持点号分隔的多级配置获取

- **UploadableModel 类**: `php_app/Core/UploadableModel.php`
  - 继承自 Model 基类
  - 提供文件上传、验证、存储功能
  - 自动生成唯一文件名
  - 支持获取文件完整 URL

### 3. Content 模型集成
- Content 模型继承 UploadableModel
- 配置 `uploadableAttributes` 指定可上传属性
- 自动处理缩略图上传和 URL 生成

## 配置说明

### 上传路径配置 (config/main.php)

```php
'upload' => [
    'max_file_size' => 10 * 1024 * 1024, // 10MB
    'allowed_image_types' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
    'allowed_video_types' => ['mp4', 'webm', 'avi', 'mov'],
    'allowed_file_types' => ['pdf', 'doc', 'docx', 'zip', 'rar'],

    // 上传路径配置
    'base_path' => '../public_resources/uploads/',
    'pics_path' => '../public_resources/uploads/pics/',
    'videos_preview_path' => '../public_resources/uploads/videos_preview/',
    'avatars_path' => '../public_resources/uploads/avatars/',
    'files_path' => '../public_resources/uploads/files/',

    // 资源URL前缀配置
    'base_url' => 'https://dp-t-static.lib00.com/',
],
```

### 本地配置覆盖 (config/main.local.php)

```php
return [
    'upload' => [
        'base_url' => 'http://localhost:8080/uploads/',
        // 可根据需要覆盖其他配置
    ],
];
```

## 使用说明

### 1. 在 Model 中配置上传属性

```php
class Content extends UploadableModel
{
    protected array $uploadableAttributes = [
        'thumbnail' => [
            'type' => 'image',              // 文件类型: image, video, file
            'path_key' => 'pics_path', // 配置文件中的路径键
            'required' => false,             // 是否必需
        ]
    ];
}
```

### 2. 在 Controller 中处理上传

```php
// 处理文件上传
if (!empty($_FILES)) {
    $content->handleFileUploads($_FILES);
}

// 填充其他数据
$content->fill($data);

// 验证和保存
if ($content->validate() && $content->save()) {
    // 保存成功
}
```

### 3. 在 View 中显示图片

```php
<?php
$thumbnailUrl = $content->getThumbnailUrl();
if ($thumbnailUrl): ?>
    <img src="<?= htmlspecialchars($thumbnailUrl) ?>" alt="缩略图">
<?php else: ?>
    <img src="" alt="暂无缩略图" style="display:none;">
<?php endif; ?>
```

## 表单要求

表单必须设置 `enctype="multipart/form-data"`:

```html
<form action="..." method="POST" enctype="multipart/form-data">
    <input type="file" name="thumbnail" accept="image/*">
    <!-- 其他表单字段 -->
</form>
```

## 文件存储说明

### 存储位置
- 缩略图: `php_app_root/public_resources/uploads/thumbnails/`
- 视频预览: `php_app_root/public_resources/uploads/videos_preview/`
- 头像: `php_app_root/public_resources/uploads/avatars/`
- 其他文件: `php_app_root/public_resources/uploads/files/`

### 文件命名规则
- 格式: `upload_{unique_id}_{timestamp}.{extension}`
- 示例: `upload_65abc123def45678_1704067200.jpg`
- 保持原文件扩展名

### 数据库存储
- 只存储文件名，不存储路径
- URL 由 Model 的 `getFileUrl()` 方法动态生成
- 支持完整 URL 和相对路径自动识别

## 测试步骤

### 1. 环境准备

```bash
# 确保上传目录存在并有写权限
mkdir -p php_app_root/public_resources/uploads/thumbnails
chmod 755 php_app_root/public_resources/uploads/thumbnails
```

### 2. 本地配置 (可选)

创建 `php_app/config/main.local.php`:

```php
<?php
return [
    'upload' => [
        'base_url' => 'http://localhost:8080/uploads/',
    ],
];
```

### 3. 测试上传功能

1. 访问内容创建页面: `http://admin.yourdomain.com/contents/create`
2. 填写表单信息
3. 选择缩略图文件上传
4. 提交表单
5. 验证:
   - 文件是否成功上传到指定目录
   - 数据库中是否正确存储文件名
   - 编辑页面是否正确显示缩略图

### 4. 测试编辑功能

1. 访问内容编辑页面: `http://admin.yourdomain.com/contents/{id}/edit`
2. 验证已有缩略图是否正确显示
3. 上传新的缩略图替换
4. 保存后验证新文件是否正确存储和显示

## 扩展说明

### 添加新的上传属性

1. 在 Model 中添加配置:

```php
protected array $uploadableAttributes = [
    'thumbnail' => [
        'type' => 'image',
        'path_key' => 'pics_path',
    ],
    'cover_image' => [
        'type' => 'image',
        'path_key' => 'avatars_path',
    ],
    'attachment' => [
        'type' => 'file',
        'path_key' => 'files_path',
    ],
];
```

2. 确保数据库中有对应字段
3. 在表单中添加对应的 file input

### 其他 Model 使用上传功能

```php
use App\Core\UploadableModel;

class User extends UploadableModel
{
    protected array $uploadableAttributes = [
        'avatar' => [
            'type' => 'image',
            'path_key' => 'avatars_path',
            'required' => false,
        ]
    ];

    // ... 其他代码
}
```

## 注意事项

1. **文件大小限制**: 默认 10MB，可在配置文件中调整
2. **文件类型验证**: 基于文件扩展名，建议生产环境添加 MIME 类型验证
3. **安全考虑**:
   - 文件名自动重命名，避免覆盖
   - 文件类型白名单验证
   - 上传目录权限控制
4. **旧文件清理**: 当前未实现自动删除旧文件，需手动管理
5. **图片处理**: 当前不包含图片压缩/缩放，可后续扩展

## 故障排查

### 上传失败
1. 检查目录权限: `ls -la php_app_root/public_resources/uploads/`
2. 检查 PHP 上传配置: `upload_max_filesize`, `post_max_size`
3. 检查错误日志: `php_app/runtime/logs/`

### 图片不显示
1. 检查数据库中文件名是否正确
2. 检查文件是否真实存在
3. 检查 URL 配置是否正确
4. 检查浏览器控制台错误信息

### 配置不生效
1. 确认 Config 类正确加载
2. 检查本地配置文件语法
3. 清除 PHP OPcache (如有)

## 文件清单

### 新增文件
- `php_app/Core/Config.php` - 配置管理类
- `php_app/Core/UploadableModel.php` - 上传功能基类
- `php_app/config/main.local.php` - 本地配置模板

### 修改文件
- `php_app/config/main.php` - 添加上传配置
- `php_app/Models/Content.php` - 继承上传功能
- `php_app/Controllers/Backend/ContentController.php` - 处理文件上传
- `php_app/Views/backend/contents/_form.php` - 表单支持文件上传
- `php_app/public_backend/index.php` - 使用 Config 类
- `php_app/public_frontend/index.php` - 使用 Config 类
- `php_app/.gitignore` - 忽略本地配置文件

## 后续优化建议

1. 添加图片压缩和缩放功能
2. 实现旧文件自动清理机制
3. 添加更严格的 MIME 类型验证
4. 支持批量上传
5. 添加上传进度显示
6. 实现 CDN 集成
7. 添加图片水印功能
