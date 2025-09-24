<?php
/**
 * 测试静态方法改造后的功能
 */

// 设置根目录和自动加载
$rootPath = __DIR__ . '/php_app_root/php_app';
require_once $rootPath . '/vendor/autoload.php';

use App\Models\Tag;
use App\Models\Content;
use App\Models\Collection;

echo "=== 静态方法测试 ===\n\n";

try {
    // 测试1: 静态findAll方法
    echo "1. 测试静态findAll方法:\n";
    echo "Tag::findAll() - ";
    $tags = Tag::findAll();
    echo "成功 (返回 " . count($tags) . " 条记录)\n";
    
    // 测试2: 带条件的静态findAll
    echo "Tag::findAll(['status_id' => 1]) - ";
    $activeTags = Tag::findAll(['status_id' => 1]);
    echo "成功 (返回 " . count($activeTags) . " 条记录)\n";
    
    // 测试3: 带格式化的静态findAll
    echo "Tag::findAll([], formatter) - ";
    $formattedTags = Tag::findAll([], function($row) {
        return [
            'id' => $row['id'],
            'name' => $row['name_cn'] ?: $row['name_en']
        ];
    });
    echo "成功 (格式化 " . count($formattedTags) . " 条记录)\n";
    
    // 测试4: 静态find方法
    echo "\n2. 测试静态find方法:\n";
    echo "Tag::find(1) - ";
    $tag = Tag::find(1);
    echo $tag ? "成功 (找到记录)" : "成功 (记录不存在)";
    echo "\n";
    
    // 测试5: 静态findById方法
    echo "Tag::findById(1) - ";
    $tagById = Tag::findById(1);
    echo $tagById ? "成功 (找到记录)" : "成功 (记录不存在)";
    echo "\n";
    
    // 测试6: 静态count方法
    echo "\n3. 测试静态count方法:\n";
    echo "Tag::count() - ";
    $totalCount = Tag::count();
    echo "成功 (总数: $totalCount)\n";
    
    echo "Tag::count(['status_id' => 1]) - ";
    $activeCount = Tag::count(['status_id' => 1]);
    echo "成功 (激活数: $activeCount)\n";
    
    // 测试7: 静态exists方法
    echo "\n4. 测试静态exists方法:\n";
    echo "Tag::exists(1) - ";
    $exists = Tag::exists(1);
    echo $exists ? "成功 (记录存在)" : "成功 (记录不存在)";
    echo "\n";
    
    // 测试8: 静态findAllWithFilters方法
    echo "\n5. 测试静态findAllWithFilters方法:\n";
    echo "Tag::findAllWithFilters(['name' => 'test']) - ";
    $filteredTags = Tag::findAllWithFilters(['name' => 'test']);
    echo "成功 (过滤结果: " . count($filteredTags) . " 条记录)\n";
    
    // 测试其他Model类
    echo "\n6. 测试其他Model类:\n";
    echo "Content::count() - ";
    $contentCount = Content::count();
    echo "成功 (内容总数: $contentCount)\n";
    
    echo "Collection::count() - ";
    $collectionCount = Collection::count();
    echo "成功 (集合总数: $collectionCount)\n";
    
    echo "\n=== 所有测试通过! ===\n";
    
} catch (Exception $e) {
    echo "\n❌ 测试失败: " . $e->getMessage() . "\n";
    echo "文件: " . $e->getFile() . ":" . $e->getLine() . "\n";
}