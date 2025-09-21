# 全局常量管理系统

这个目录包含了项目的全局常量定义，使用PHP 8.4的枚举特性来替代硬编码的数字常量。

## 文件结构

```
Constants/
├── Status.php              # 基础状态枚举 (0, 1)
├── ContentStatus.php       # 内容状态枚举 (21, 29, 31, 39, 91, 99)
├── HttpStatus.php          # HTTP状态码枚举
├── ConstantsDemo.php       # 基础使用示例
├── UsageExample.php        # 实际代码改造示例
└── README.md              # 本文件
```

## 常量类说明

### 1. Status.php - 基础状态枚举
处理简单的二元状态：
- `Status::INACTIVE` (0) - 非活跃/隐藏/禁用
- `Status::ACTIVE` (1) - 活跃/显示/启用

### 2. ContentStatus.php - 内容状态枚举
处理复杂的内容工作流状态：
- `ContentStatus::PROCESSING` (21) - 处理中
- `ContentStatus::READY` (29) - 准备就绪
- `ContentStatus::PUBLISHED` (31) - 已发布
- `ContentStatus::FEATURED` (39) - 精选内容
- `ContentStatus::ARCHIVED` (91) - 已归档
- `ContentStatus::COMPLETED` (99) - 已完成

### 3. HttpStatus.php - HTTP状态码枚举
标准HTTP状态码：
- 成功: `HttpStatus::OK` (200), `HttpStatus::CREATED` (201)
- 重定向: `HttpStatus::FOUND` (302), `HttpStatus::MOVED_PERMANENTLY` (301)
- 客户端错误: `HttpStatus::NOT_FOUND` (404), `HttpStatus::BAD_REQUEST` (400)
- 服务器错误: `HttpStatus::INTERNAL_SERVER_ERROR` (500)

## 使用方法

### 在Controller中使用

```php
// 旧写法
$this->json(['data' => $results], 200);
$this->redirect('/dashboard', 302);

// 新写法
use App\Constants\HttpStatus;
$this->json(['data' => $results], HttpStatus::OK->value);
$this->redirect('/dashboard', HttpStatus::FOUND->value);
```

### 在Model中使用

```php
// 旧写法
$this->findAll(['status_id' => 1], $limit, $offset);

// 新写法
use App\Constants\Status;
$this->findAll(['status_id' => Status::ACTIVE->value], $limit, $offset);

// 查询多个状态
use App\Constants\ContentStatus;
$this->findAll(['status_id' => ContentStatus::getVisibleStatuses()]);
```

### 在View中使用

```php
// 旧写法
<option value="1">显示</option>
<option value="0">隐藏</option>

// 新写法
<?php foreach (Status::getAllValues() as $value => $label): ?>
<option value="<?= $value ?>"><?= $label ?></option>
<?php endforeach; ?>
```

## 辅助方法

每个枚举都提供了丰富的辅助方法：

### Status 枚举方法
- `label()` - 中文标签
- `englishLabel()` - 英文标签
- `isActive()` - 是否为活跃状态
- `fromString()` - 从字符串创建状态
- `getAllValues()` - 获取所有状态值和标签

### ContentStatus 枚举方法
- `label()` - 状态标签
- `statusClass()` - CSS类名
- `isVisible()` - 是否可见
- `isPublished()` - 是否已发布
- `getVisibleStatuses()` - 获取所有可见状态
- `getPublishedStatuses()` - 获取所有已发布状态

### HttpStatus 枚举方法
- `message()` - 状态消息
- `isSuccess()` - 是否为成功状态
- `isError()` - 是否为错误状态
- `isRedirection()` - 是否为重定向状态

## 运行示例

```bash
# 查看基础使用示例
php Constants/ConstantsDemo.php

# 查看实际代码改造示例
php Constants/UsageExample.php
```

## 迁移建议

1. **Controller层改造**：
   - 将所有硬编码的HTTP状态码替换为 `HttpStatus` 枚举
   - 将状态检查改为使用 `Status` 或 `ContentStatus` 枚举

2. **Model层改造**：
   - 数据库查询条件中的状态值使用枚举
   - 数据验证时使用枚举提供的验证方法

3. **View层改造**：
   - 下拉菜单选项使用枚举的 `getAllValues()` 方法
   - 状态显示使用枚举的 `label()` 方法

4. **配置文件改造**：
   - 将配置中的硬编码状态值替换为枚举引用

## 优势

1. **类型安全**：IDE可以提供自动完成和类型检查
2. **可读性强**：代码含义更加清晰
3. **易于维护**：集中管理所有常量
4. **国际化支持**：提供中英文标签
5. **功能丰富**：提供各种便利的辅助方法
6. **向后兼容**：枚举值与原有数字常量保持一致

## 注意事项

- 在composer.json中确保autoload配置包含Constants目录
- 使用时需要先引入对应的枚举类
- 枚举值(.value)用于数据库操作，枚举对象用于逻辑判断