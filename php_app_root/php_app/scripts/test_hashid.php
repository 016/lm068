<?php
/**
 * HashId 功能测试脚本
 *
 * 用途：测试HashId编码和解码功能
 * 运行：php scripts/test_hashid.php
 */

// 设置include路径
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Core/Config.php';
require_once __DIR__ . '/../Core/HashId.php';

use App\Core\HashId;

echo "========================================\n";
echo "HashId 功能测试\n";
echo "========================================\n\n";

// 测试用例
$testIds = [1, 5, 10, 100, 999, 1234, 9999, 12345];

echo "测试编码和解码（使用静态方法）:\n";
echo "----------------------------------------\n";

foreach ($testIds as $id) {
    $encoded = HashId::encode($id);
    $decoded = HashId::decode($encoded);

    $status = ($decoded === $id) ? '✓ 成功' : '✗ 失败';

    echo sprintf(
        "ID: %5d -> Hash: %10s -> Decoded: %5d [%s]\n",
        $id,
        $encoded,
        $decoded,
        $status
    );
}

echo "\n测试Hash长度:\n";
echo "----------------------------------------\n";
foreach ($testIds as $id) {
    $encoded = HashId::encode($id);
    echo sprintf("ID: %5d -> Hash: %10s (长度: %d)\n", $id, $encoded, strlen($encoded));
}

echo "\n测试Hash唯一性:\n";
echo "----------------------------------------\n";
$hashes = [];
$hasDuplicates = false;
foreach ($testIds as $id) {
    $encoded = HashId::encode($id);
    if (in_array($encoded, $hashes)) {
        echo "✗ 发现重复的Hash: $encoded (ID: $id)\n";
        $hasDuplicates = true;
    }
    $hashes[] = $encoded;
}
if (!$hasDuplicates) {
    echo "✓ 所有Hash都是唯一的\n";
}

echo "\n测试无效输入:\n";
echo "----------------------------------------\n";

// 测试无效的hash字符串
$invalidHashes = ['', 'invalid', '!@#$%', '123abc@@@'];
foreach ($invalidHashes as $hash) {
    $decoded = HashId::decode($hash);
    $status = ($decoded === null) ? '✓ 正确返回null' : '✗ 应该返回null';
    echo sprintf("Hash: '%s' -> Decoded: %s [%s]\n", $hash, var_export($decoded, true), $status);
}

echo "\n测试实际视频ID示例:\n";
echo "----------------------------------------\n";
// 假设我们有一些视频ID
$videoIds = [1, 2, 3, 15, 27, 42, 88, 156];
echo "生成的视频Hash URL:\n";
foreach ($videoIds as $videoId) {
    $hash = HashId::encode($videoId);
    echo "Video ID: $videoId -> URL: /videos/$hash\n";
}

echo "\n测试配置开关功能（简化版）:\n";
echo "----------------------------------------\n";

$testVideoId = 123;

echo "当前配置: HashID " . (HashId::isEnabled() ? "已启用" : "未启用") . "\n\n";

// 使用统一的静态方法 - 自动根据配置处理
$encoded = HashId::encode($testVideoId);
echo "编码: HashId::encode($testVideoId) = $encoded\n";

$decoded = HashId::decode($encoded);
echo "解码: HashId::decode('$encoded') = $decoded\n";

// 测试向后兼容（传入数字字符串）
$numericId = '456';
$decoded2 = HashId::decode($numericId);
echo "兼容: HashId::decode('$numericId') = $decoded2 （支持纯数字）\n";

echo "\n在Controller中的使用示例:\n";
echo "----------------------------------------\n";
echo "// 编码（生成URL）:\n";
echo "\$hash = HashId::encode(\$videoId);\n";
echo "\$url = \"/videos/{\$hash}\";\n\n";
echo "// 解码（接收URL参数）:\n";
echo "\$param = \$request->getParam(0);\n";
echo "\$id = HashId::decode(\$param);\n";

echo "\n配置说明:\n";
echo "----------------------------------------\n";
echo "在 config/main.php 中设置:\n";
echo "'hashid' => [\n";
echo "    'enabled' => true,  // true=启用hash, false=使用数字ID\n";
echo "    'salt' => 'lm068_video_site_2025',\n";
echo "    'min_length' => 6,\n";
echo "]\n\n";
echo "设计优势:\n";
echo "- 只有一组方法：encode() 和 decode()\n";
echo "- 配置逻辑内置，使用者无需关心\n";
echo "- 简单直观，符合KISS原则\n";
echo "- 避免了 encode vs encodeId 的选择困惑\n";

echo "\n========================================\n";
echo "测试完成！\n";
echo "========================================\n";
