```php
// 查找10篇文章 (1次查询)
$posts = Post::query()->limit(10)->all();

// 循环中每次访问 $post->user 都会执行一次新的查询 (10次查询)
// 总共执行了 1 + 10 = 11 次查询
foreach ($posts as $post) {
    echo "作者: " . $post->user->name . "\n";
}
```