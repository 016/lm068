```php
// 实例化一个新的 User 模型
$user = new User();

// 填充数据
$user->fill([
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
    'status' => 1,
]);

// 也可以直接给属性赋值
// $user->name = 'John Doe';

// 保存到数据库
if ($user->save()) {
    echo "新用户创建成功，ID: " . $user->id;
} else {
    // 打印错误信息
    print_r($user->errors);
}
```