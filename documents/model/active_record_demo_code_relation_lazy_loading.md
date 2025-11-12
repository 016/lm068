```php
// 查找一篇文章
$post = Post::find(1);

if ($post) {
    // 访问关联的 user 模型
    // 这里会触发一条新的 SQL 查询: SELECT * FROM users WHERE id = ? LIMIT 1
    $author = $post->user;

    if ($author) {
        echo "文章 '{$post->title}' 的作者是: {$author->name}";
    }
}

// 反向查找
$user = User::find(1);
if ($user) {
    // 访问该用户的所有文章
    // 这里会触发一条新的 SQL 查询: SELECT * FROM posts WHERE user_id = ?
    $posts = $user->posts;
    echo "用户 {$user->name} 有 " . count($posts) . " 篇文章。";
}
```