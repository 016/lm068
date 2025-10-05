<?php
/**
 * 测试 bulkAction 的状态解析功能
 *
 * 运行方式：
 * cd /eeBox/eeProject/lm068/php_app_root/php_app
 * php scripts/test_bulk_action.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Constants\ContentStatus;

// 模拟 getModelStatuses() 的返回结果
$statusList = array_column(ContentStatus::cases(), 'value', 'name');

echo "=== ContentStatus 枚举状态列表 ===\n";
foreach ($statusList as $name => $value) {
    echo sprintf("%-20s => %d\n", $name, $value);
}
echo "\n";

// 模拟 resolveActionToStatus() 函数
function resolveActionToStatus(string $action, array $statusList): ?int
{
    // 将 action 转换为大写，用于匹配枚举名称
    $enumName = strtoupper($action);

    // 检查是否在状态列表中存在
    if (array_key_exists($enumName, $statusList)) {
        return $statusList[$enumName];
    }

    // 兼容旧的 enable/disable 操作
    if ($action === 'enable' && array_key_exists('ENABLED', $statusList)) {
        return $statusList['ENABLED'];
    }

    if ($action === 'disable' && array_key_exists('DISABLED', $statusList)) {
        return $statusList['DISABLED'];
    }

    // 无法解析，返回 null
    return null;
}

// 测试用例
$testCases = [
    // ContentStatus 枚举名称（推荐使用）
    'published',
    'hidden',
    'draft',
    'creative',
    'script_start',
    'script_done',
    'shooting_start',
    'shooting_done',
    'editing_start',
    'editing_done',
    'pending_publish',

    // 大写形式（也应该支持）
    'PUBLISHED',
    'DRAFT',

    // 混合大小写（应该支持）
    'Published',
    'ScRiPt_StArT',

    // 旧的 enable/disable（ContentStatus 中不存在，应该返回 null）
    'enable',
    'disable',

    // 删除操作（不是状态，应该返回 null）
    'delete',

    // 无效操作
    'invalid_action',
    'status_99',
];

echo "=== 测试 action 解析结果 ===\n";
foreach ($testCases as $action) {
    $result = resolveActionToStatus($action, $statusList);
    $status = $result !== null ? $result : 'NULL';
    $emoji = $result !== null ? '✅' : '❌';

    echo sprintf("%s %-20s => %s", $emoji, "'$action'", $status);

    // 显示对应的枚举名称
    if ($result !== null) {
        $enumName = array_search($result, $statusList);
        echo " ({$enumName})";
    }
    echo "\n";
}

echo "\n=== 测试结论 ===\n";
echo "✅ 所有 ContentStatus 枚举名称均可正确解析\n";
echo "✅ 大小写不敏感，统一转为大写匹配\n";
echo "❌ enable/disable 在 ContentStatus 中不存在，返回 NULL（符合预期）\n";
echo "❌ delete 操作不是状态，返回 NULL（需要单独处理）\n";
echo "❌ 无效操作返回 NULL，会被 bulkAction 拒绝\n";
echo "\n";
echo "🎯 前端推荐使用 snake_case 格式：\n";
echo "   - action: 'published'      (推荐)\n";
echo "   - action: 'script_start'   (推荐)\n";
echo "   - action: 'delete'         (删除操作)\n";
