# Status常量设计最佳实践建议

## 推荐方案：混合设计

### 1. 保留通用 Status.php 
**适用场景**：真正通用的启用/禁用状态
```php
// Status.php - 仅用于纯粹的功能开关
enum Status: int {
    case INACTIVE = 0;
    case ACTIVE = 1;
}
```

**使用对象**：
- `tag.status_id` - 标签启用/禁用
- `collection.status_id` - 合集启用/禁用  
- `admin_user.status_id` - 管理员启用/禁用

### 2. 创建领域特定状态枚举

#### UserStatus.php
```php
enum UserStatus: int {
    case BANNED = 0;      // 封停/不可用
    case ACTIVE = 1;      // 正常/可用
}
```

#### SubscriptionStatus.php  
```php
enum SubscriptionStatus: int {
    case UNSUBSCRIBED = 0;  // 取消订阅
    case SUBSCRIBED = 1;    // 已订阅
}
```

#### LinkStatus.php
```php
enum LinkStatus: int {
    case INVALID = 0;     // 失效
    case VALID = 1;       // 正常
}
```

## 判断标准

### 使用通用 Status 的条件：
1. ✅ 纯粹的启用/禁用概念
2. ✅ 不太可能扩展状态值
3. ✅ 可以共享相同的业务逻辑

### 使用专用状态枚举的条件：
1. ✅ 有特定的业务含义
2. ✅ 可能需要扩展状态
3. ✅ 有独特的业务规则

## 实际应用示例

### 当前合理的使用：
```php
// 功能管理 - 使用通用Status  
$tag['status_id'] = Status::ACTIVE->value;
$collection['status_id'] = Status::INACTIVE->value;

// 业务特定 - 使用专用枚举
$user['status_id'] = UserStatus::BANNED->value;
$subscription['status_id'] = SubscriptionStatus::UNSUBSCRIBED->value;
```

### 避免的反模式：
```php
// ❌ 不要跨领域使用
$user['status_id'] = Status::INACTIVE->value;  // 语义不明确
```

## 迁移建议

如果要优化当前设计：

1. **保持向后兼容**：现有代码继续使用 Status
2. **渐进式重构**：新功能使用专用枚举
3. **文档清晰**：明确每个枚举的适用范围
4. **类型约束**：在方法签名中明确状态类型

```php
// 类型明确的方法签名
function updateUserStatus(int $userId, UserStatus $status): bool;
function updateTagStatus(int $tagId, Status $status): bool;
```