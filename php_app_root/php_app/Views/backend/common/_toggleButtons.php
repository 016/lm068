<?php
/**
 * Toggle Buttons Component
 *
 * 切换按钮组件 - 用于控制页面区域的显示/隐藏
 *
 * @var array $targets 按钮配置数组，每个元素包含:
 *   - target: string 要控制的目标区域的 ID (必需)
 *   - icon: string Bootstrap Icon 类名 (可选，默认 bi-gear)
 *   - title: string 按钮提示文字 (可选)
 * @var bool $override 是否覆盖默认值 (可选，默认 false)
 *
 * 使用示例 1 - 使用默认值:
 * <?php include __DIR__ . '/../common/_toggleButtons.php'; ?>
 *
 * 使用示例 2 - 自定义配置:
 * <?php
 * $targets = [
 *     ['target' => 'quick-filter-section', 'icon' => 'bi-funnel', 'title' => '切换快速筛选区显示/隐藏'],
 *     ['target' => 'action-buttons-section', 'icon' => 'bi-gear', 'title' => '切换动作按钮区显示/隐藏']
 * ];
 * include __DIR__ . '/../common/_toggleButtons.php';
 * ?>
 *
 * 使用示例 3 - 覆盖默认值:
 * <?php
 * $targets = [
 *     ['target' => 'custom-section', 'icon' => 'bi-eye', 'title' => '切换自定义区']
 * ];
 * $override = true;
 * include __DIR__ . '/../common/_toggleButtons.php';
 * ?>
 */

// 默认配置
$defaultTargets = [
    ['target' => 'quick-filter-section', 'icon' => 'bi-funnel', 'title' => '切换快速筛选区显示/隐藏'],
    ['target' => 'action-buttons-section', 'icon' => 'bi-gear', 'title' => '切换动作按钮区显示/隐藏']
];

// 判断是否覆盖默认值
$override = $override ?? false;

if (!isset($targets)) {
    // 未设置 $targets，使用默认值
    $targets = $defaultTargets;
} elseif (!$override) {
    // 设置了 $targets 但不覆盖，合并默认值
    $targets = array_merge($defaultTargets, $targets);
}
// 如果 $override = true，则完全使用自定义的 $targets
?>
<?php if (!empty($targets)): ?>
<div class="header-toggle-buttons">
    <?php foreach ($targets as $config): ?>
        <?php
        $target = $config['target'] ?? '';
        $icon = $config['icon'] ?? 'bi-gear';
        $title = $config['title'] ?? '切换区域显示/隐藏';
        ?>
        <?php if (!empty($target)): ?>
        <button class="btn btn-sm btn-outline-secondary toggle-section-btn"
                data-target="<?= htmlspecialchars($target) ?>"
                title="<?= htmlspecialchars($title) ?>">
            <i class="<?= htmlspecialchars($icon) ?>"></i>
        </button>
        <?php endif; ?>
    <?php endforeach; ?>
</div>
<?php endif; ?>
