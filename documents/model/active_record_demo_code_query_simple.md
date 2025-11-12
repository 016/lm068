```php
// 1. 根据主键 ID 查找单个用户
$user = User::find(1);
if ($user) {
    echo "找到用户: " . $user->name;
}

// 2. 根据条件查找第一条记录
$activeUser = User::find(['status' => 1]);
if ($activeUser) {
    echo "找到第一个活动用户: " . $activeUser->name;
}

// 3. 查找所有记录
$allUsers = User::findAll();
foreach ($allUsers as $user) {
    echo $user->name . "\n";
}

// 4. 带条件的查找所有记录
$activeUsers = User::findAll(['status' => 1], 'id DESC', 10); // 条件, 排序, 数量

// 5. 统计所有活动用户的数量
$activeUserCount = User::where(['status' => 1])->count();
echo "活动用户总数: " . $activeUserCount;

// 6. 查找一条数据
$activeUser = User::where(['status' => 1])->one();
$activeUser = User::where(['status' => 1])->first();
```