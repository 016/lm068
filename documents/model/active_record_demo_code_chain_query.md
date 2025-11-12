```php
// 查找状态为1，并按ID降序排列的前5个用户
$users = User::query()
    ->where(['status' => 1])
    ->orderBy('id DESC')
    ->limit(5)
    ->offset(0)
    ->all(); // 使用 all() 获取结果集

foreach ($users as $user) {
    echo $user->name . "\n";
}

// 查找第一个满足条件的记录
$firstUser = User::where(['status' => 1])->orderBy('id ASC')->first(); // 使用 first() 获取单个模型
if ($firstUser) {
    echo "第一个活动用户是: " . $firstUser->name;
}

// 查找标题为 'A' 或内容为 'B' 的文章
$posts = Post::query()
    ->whereRaw('(title = :title OR content = :content)', [
        'title' => 'A',
        'content' => 'B'
    ])
    ->all();
```