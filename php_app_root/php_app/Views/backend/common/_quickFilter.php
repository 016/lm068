<?php
/**
 * Quick Filter Component
 *
 * 快速筛选组件 - 用于快速筛选内容状态
 *
 * @var array $config 筛选配置数组:
 *   - show: bool 是否默认显示 (可选，默认 true)
 *   - baseUrl: string 基础URL路径 (可选，默认 '/contents')
 *   - filterParam: string 筛选参数名 (可选，默认 'status_id')
 *   - currentValue: mixed 当前选中的值 (可选)
 *   - items: array 筛选项数组 (可选，有默认值)，每个元素包含:
 *     - value: mixed 筛选值 (空值表示显示全部)
 *     - label: string 显示标签
 *     - icon: string Bootstrap Icon 类名 (可选)
 *
 * 使用示例 1 - 使用默认值:
 * <?php
 * $config = [
 *     'baseUrl' => '/contents',
 *     'currentValue' => $_GET['status_id'] ?? null
 * ];
 * include __DIR__ . '/../common/_quickFilter.php';
 * ?>
 *
 * 使用示例 2 - 自定义配置(合并默认值):
 * <?php
 * use App\Constants\ContentStatus;
 *
 * $config = [
 *     'show' => true,
 *     'baseUrl' => '/contents',
 *     'filterParam' => 'status_id',
 *     'currentValue' => $_GET['status_id'] ?? null,
 *     'items' => ['s_', 's_11', 's_96'] //only show index in this list
 * ];
 * include __DIR__ . '/../common/_quickFilter.php';
 * ?>
 *
 */

use App\Constants\ContentStatus;

// 默认筛选项
$defaultItems = [
    's_' => ['queryParam' => 'status_id', 'value' => '', 'label' => '全部', 'icon' => 'bi-list-ul'],
    's_'.ContentStatus::DRAFT->value => ['queryParam' => 'status_id', 'value' => ContentStatus::DRAFT->value, 'label' => ContentStatus::DRAFT->label(), 'icon' => ContentStatus::DRAFT->icon()],
    's_'.ContentStatus::CREATIVE_0->value.'-'.ContentStatus::CREATIVE_START->value.'-'.ContentStatus::CREATIVE_DONE->value => ['queryParam' => 'status_id', 'value' => ContentStatus::CREATIVE_0->value.','.ContentStatus::CREATIVE_START->value.','.ContentStatus::CREATIVE_DONE->value, 'label' => '创意*', 'icon' => ContentStatus::CREATIVE_0->icon()],
    's_'.ContentStatus::SHOOTING_DONE->value => ['queryParam' => 'status_id', 'value' => ContentStatus::SHOOTING_DONE->value, 'label' => ContentStatus::SHOOTING_DONE->label(), 'icon' => ContentStatus::SHOOTING_DONE->icon()],
    's_'.ContentStatus::EDITING_DONE->value => ['queryParam' => 'status_id', 'value' => ContentStatus::EDITING_DONE->value, 'label' => ContentStatus::EDITING_DONE->label(), 'icon' => ContentStatus::EDITING_DONE->icon()],
    's_'.ContentStatus::PENDING_PUBLISH->value => ['queryParam' => 'status_id', 'value' => ContentStatus::PENDING_PUBLISH->value, 'label' => ContentStatus::PENDING_PUBLISH->label(), 'icon' => ContentStatus::PENDING_PUBLISH->icon()],
    's_'.ContentStatus::PUBLISHED->value => ['queryParam' => 'status_id', 'value' => ContentStatus::PUBLISHED->value, 'label' => ContentStatus::PUBLISHED->label(), 'icon' => ContentStatus::PUBLISHED->icon()],
    's_'.ContentStatus::PENDING_PUBLISH->value.'-'.ContentStatus::PUBLISHED->value => ['queryParam' => 'status_id', 'value' => ContentStatus::PENDING_PUBLISH->value.",".ContentStatus::PUBLISHED->value, 'label' => ContentStatus::PENDING_PUBLISH->label().'+'.ContentStatus::PUBLISHED->label(), 'icon' => ContentStatus::PUBLISHED->icon()]
];

// 默认配置
if (!isset($config)) {
    $config = [];
}

$show = $config['show'] ?? true;
$default_baseUrl = $config['baseUrl'] ?? '/contents';
$default_filterParam = $config['filterParam'] ?? 'status_id';
$currentValue = $config['currentValue'] ?? null;

// 处理 items
if (!isset($config['items'])) {
    // 未设置 items，使用默认值
    $items = $defaultItems;
} else {
    // 覆盖模式，完全使用自定义的 items
    $items = array_intersect_key($defaultItems, array_flip($config['items']));
}
?>
<?php if (!empty($items)): ?>
    <!-- Quick Filter Section -->
    <div class="quick-filter-section" id="quick-filter-section" data-show="<?= $show ? 'true' : 'false' ?>">
        <div class="quick-filter-container">
            <div class="quick-filter-label">
                <i class="bi bi-funnel"></i>
                <span>快速筛选:</span>
            </div>
            <div class="quick-filter-items">
                <?php foreach ($items as $item): ?>
                    <?php
                    $value = $item['value'] ?? '';
                    $label = $item['label'] ?? '';
                    $icon = $item['icon'] ?? 'bi-circle';

                    //build item lv url
                    $item_baseUrl = $default_baseUrl;
                    if (isset($item['baseUrl'])) {
                        $item_baseUrl = $item['baseUrl'];
                    }

                    $item_filterParam = $default_filterParam;
                    if (isset($item['filterParam'])) {
                        $item_filterParam = $item['filterParam'];
                    }

                    // 构建URL
                    if (empty($value)) {
                        // 全部：不带参数
                        $url = $item_baseUrl;
                        $isActive = empty($currentValue);
                    } else {
                        // 带参数
                        $url = $item_baseUrl . '?' . $item_filterParam . '=' . urlencode($value);
                        $isActive = ($currentValue == $value);
                    }
                    ?>
                    <a href="<?= htmlspecialchars($url) ?>" class="quick-filter-item <?= $isActive ? 'active' : '' ?>">
                        <i class="<?= htmlspecialchars($icon) ?>"></i>
                        <?= htmlspecialchars($label) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif; ?>
