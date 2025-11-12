```php
$query = Post::query();

$posts = $query->where([
    // 等于: status = 1
    'status' => 1,

    // 不等于: user_id != 5
    'user_id' => ['!=', 5],

    // 大于: view_count > 100
    'view_count' => ['>', 100],

    // IN 查询: category_id IN (1, 2, 3)
    'category_id' => [1, 2, 3], // 简单形式
    'type' => ['IN', [1, 2, 3]], // 标准形式

    // NOT IN 查询: tag_id NOT IN (10, 11)
    'tag_id' => ['NOT IN', [10, 11]],

    // BETWEEN 查询: created_at BETWEEN '2023-01-01' AND '2023-12-31'
    'created_at' => ['BETWEEN', ['2023-01-01 00:00:00', '2023-12-31 23:59:59']],

    // LIKE 查询: title LIKE '%hello%'
    'title' => ['LIKE', '%hello%'],

    // IS NULL 查询: deleted_at IS NULL
    'deleted_at' => null,
    // 或者 'deleted_at' => ['IS NULL']

    // IS NOT NULL 查询
    'published_at' => ['IS NOT NULL'],
])
->orderBy('id DESC')
->all();
```