```php
// 查找 ID 为 1 的用户
$user = User::find(1);

if ($user) {
    // 修改属性
    $user->name = 'John Doe Updated';
    $user->status = 2;

    // 保存更改
    if ($user->save()) {
        echo "用户更新成功！";
    } else {
        print_r($user->errors);
    }
}
```