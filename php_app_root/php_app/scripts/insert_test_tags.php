<?php

// 用于插入测试标签数据的脚本
require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\Tag;

try {
    $tagModel = new Tag();
    
    // 测试数据
    $testTags = [
        [
            'name_cn' => '搞笑视频',
            'name_en' => 'Funny Videos',
            'short_desc_cn' => '搞笑有趣的视频内容',
            'short_desc_en' => 'Funny and entertaining video content',
            'desc_cn' => '这是一个收集搞笑有趣视频内容的分类标签，包含各种幽默、娱乐类视频内容。',
            'desc_en' => 'This is a category tag for collecting funny and entertaining video content, including various humorous and entertainment videos.',
            'color_class' => 'btn-outline-primary',
            'icon_class' => 'bi-emoji-smile',
            'status_id' => 1,
            'content_cnt' => 15
        ],
        [
            'name_cn' => '科技前沿',
            'name_en' => 'Technology',
            'short_desc_cn' => '最新科技资讯和产品',
            'short_desc_en' => 'Latest technology news and products',
            'desc_cn' => '关注最新的科技发展趋势，包括AI、机器人、新产品发布等内容。',
            'desc_en' => 'Focus on the latest technology trends, including AI, robotics, new product launches, etc.',
            'color_class' => 'btn-outline-info',
            'icon_class' => 'bi-cpu',
            'status_id' => 1,
            'content_cnt' => 8
        ],
        [
            'name_cn' => '美食制作',
            'name_en' => 'Cooking',
            'short_desc_cn' => '各种美食制作教程',
            'short_desc_en' => 'Various cooking tutorials',
            'desc_cn' => '分享各种美食制作方法和烹饪技巧，让大家学会做美味的食物。',
            'desc_en' => 'Share various cooking methods and culinary skills to help everyone learn to make delicious food.',
            'color_class' => 'btn-outline-warning',
            'icon_class' => 'bi-cup-hot',
            'status_id' => 1,
            'content_cnt' => 12
        ],
        [
            'name_cn' => '旅行日志',
            'name_en' => 'Travel',
            'short_desc_cn' => '世界各地旅行体验',
            'short_desc_en' => 'Travel experiences around the world',
            'desc_cn' => '记录世界各地的旅行体验，分享美丽的风景和文化。',
            'desc_en' => 'Record travel experiences around the world, sharing beautiful scenery and culture.',
            'color_class' => 'btn-outline-success',
            'icon_class' => 'bi-airplane',
            'status_id' => 1,
            'content_cnt' => 6
        ],
        [
            'name_cn' => '游戏娱乐',
            'name_en' => 'Gaming',
            'short_desc_cn' => '游戏实况和攻略',
            'short_desc_en' => 'Game streams and guides',
            'desc_cn' => '分享各种游戏的实况直播和游戏攻略，为游戏爱好者提供参考。',
            'desc_en' => 'Share live streams and game guides for various games, providing reference for gaming enthusiasts.',
            'color_class' => 'btn-outline-danger',
            'icon_class' => 'bi-controller',
            'status_id' => 0,
            'content_cnt' => 20
        ]
    ];
    
    echo "插入测试标签数据...\n";
    
    foreach ($testTags as $tagData) {
        $id = $tagModel->create($tagData);
        echo "创建标签: {$tagData['name_cn']} (ID: {$id})\n";
    }
    
    echo "测试数据插入完成!\n";
    
} catch (Exception $e) {
    echo "错误: " . $e->getMessage() . "\n";
}