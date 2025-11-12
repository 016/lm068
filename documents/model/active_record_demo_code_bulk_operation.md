```php
// 批量更新 ID 为 [1, 3, 5] 的用户的状态
$idsToUpdate = [1, 3, 5];
$result = User::bulkUpdateStatus($idsToUpdate, 2); // 设置 status 为 2
echo "总共: {$result['total']}, 成功: {$result['changed']}, 失败: {$result['fail']}";

// 批量删除 ID 为 [10, 11, 12] 的用户
$idsToDelete = [10, 11, 12];
$result = User::bulkDelete($idsToDelete);
echo "总共: {$result['total']}, 成功: {$result['changed']}, 失败: {$result['fail']}";
```