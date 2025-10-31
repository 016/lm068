<?php
use App\Constants\ContentStatus;
use App\Constants\ContentType;
?>
<!-- Content Management Content -->
<main class="dashboard-content">
    <!-- Breadcrumb and Page Title -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-file-text page-title-icon"></i>
                <div>
                    <h1 class="page-title">内容管理中心</h1>
                    <p class="page-subtitle">Content Management Hub</p>
                </div>
            </div>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="/dashboard" class="breadcrumb-link">首页</a></li>
                <li class="breadcrumb-item"><a href="/content" class="breadcrumb-link">内容管理</a></li>
                <li class="breadcrumb-item active breadcrumb-active" aria-current="page">内容列表</li>
            </ol>
        </nav>
    </div>

    <!-- Quick Statistics Overview -->
    <div class="row mb-4">
        <div class="col-md-3 col-6 mb-3">
            <div class="metric-card-updated">
                <div class="metric-card-header">
                    <div class="metric-icon-small metric-icon-gradient-1">
                        <i class="bi bi-file-text metric-icon-white"></i>
                    </div>
                    <div class="metric-header-text">
                        <h4>总内容</h4>
                        <span class="metric-subtitle">Total Content</span>
                    </div>
                </div>
                <div class="metric-card-body">
                    <div class="metric-value-small"><?= number_format($stats['total_content'] ?? 0) ?></div>
                    <div class="monthly-stats">
                        <i class="bi bi-arrow-up monthly-stats-icon"></i>
                        <span>已发布: <?= number_format($stats['published_content'] ?? 0) ?></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="metric-card-updated">
                <div class="metric-card-header">
                    <div class="metric-icon-small metric-icon-gradient-2">
                        <i class="bi bi-camera-video metric-icon-white"></i>
                    </div>
                    <div class="metric-header-text">
                        <h4>视频内容</h4>
                        <span class="metric-subtitle">Videos</span>
                    </div>
                </div>
                <div class="metric-card-body">
                    <div class="metric-value-small"><?= number_format(array_filter($content, fn($c) => $c['content_type_id'] == ContentType::VIDEO->value) ? count(array_filter($content, fn($c) => $c['content_type_id'] == ContentType::VIDEO->value)) : 0) ?></div>
                    <div class="monthly-stats">
                        <i class="bi bi-camera-video monthly-stats-icon"></i>
                        <span>视频类型</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="metric-card-updated">
                <div class="metric-card-header">
                    <div class="metric-icon-small metric-icon-gradient-3">
                        <i class="bi bi-file-earmark-text metric-icon-white"></i>
                    </div>
                    <div class="metric-header-text">
                        <h4>文章内容</h4>
                        <span class="metric-subtitle">Articles</span>
                    </div>
                </div>
                <div class="metric-card-body">
                    <div class="metric-value-small"><?= number_format(array_filter($content, fn($c) => $c['content_type_id'] == ContentType::ARTICLE->value) ? count(array_filter($content, fn($c) => $c['content_type_id'] == ContentType::ARTICLE->value)) : 0) ?></div>
                    <div class="monthly-stats">
                        <i class="bi bi-file-earmark-text monthly-stats-icon"></i>
                        <span>文章类型</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="metric-card-updated">
                <div class="metric-card-header">
                    <div class="metric-icon-small metric-icon-gradient-4">
                        <i class="bi bi-megaphone metric-icon-white"></i>
                    </div>
                    <div class="metric-header-text">
                        <h4>公告</h4>
                        <span class="metric-subtitle">Announcements</span>
                    </div>
                </div>
                <div class="metric-card-body">
                    <div class="metric-value-small"><?= number_format(array_filter($content, fn($c) => $c['content_type_id'] == ContentType::ANNOUNCEMENT->value) ? count(array_filter($content, fn($c) => $c['content_type_id'] == ContentType::ANNOUNCEMENT->value)) : 0) ?></div>
                    <div class="monthly-stats">
                        <i class="bi bi-megaphone monthly-stats-icon"></i>
                        <span>公告类型</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content List Management Card -->
    <div class="chart-container">
        <div class="chart-header pb-2">
            <div class="chart-header-left">
                <i class="bi bi-list-ul chart-title-icon"></i>
                <h3>📄 内容列表管理</h3>
            </div>
            <?php if (!empty($filterDisplayInfo)): ?>
            <div class="filter-status-display" style="margin-left: 1rem; display: flex; align-items: center; gap: 0.5rem; font-size: 0.9rem; color: var(--text-muted);">
                <i class="bi bi-funnel-fill"></i>
                <span>当前筛选条件:</span>
                <?php if (isset($filterDisplayInfo['tag'])): ?>
                <span class="badge bg-primary">
                    <i class="bi bi-tag-fill"></i>
                    标签: <?= htmlspecialchars($filterDisplayInfo['tag']['name']) ?>
                </span>
                <?php endif; ?>
                <?php if (isset($filterDisplayInfo['collection'])): ?>
                <span class="badge bg-info">
                    <i class="bi bi-collection-fill"></i>
                    合集: <?= htmlspecialchars($filterDisplayInfo['collection']['name']) ?>
                </span>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Action Buttons Row -->
        <div style="padding: 1.5rem 1.5rem 0 1.5rem;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex gap-2 flex-wrap">
                    <a href="/contents/create" class="btn btn-primary d-flex align-items-center gap-2">
                        <i class="bi bi-plus-lg"></i>
                        创建新内容
                    </a>
                    <button class="btn btn-outline-primary d-flex align-items-center gap-2" id="bulkImportBtn">
                        <i class="bi bi-download"></i>
                        批量导入
                    </button>
                    <div class="dropdown-container" style="position: relative;">
                        <button class="btn btn-outline-primary d-flex align-items-center gap-2" id="exportBtn">
                            <i class="bi bi-upload"></i>
                            导出数据
                        </button>
                        <div class="export-popup" id="exportPopup">
                            <div class="popup-item" onclick="exportData('json')">
                                <i class="bi bi-filetype-json"></i>
                                <span>导出为 JSON</span>
                            </div>
                            <div class="popup-item" onclick="exportData('csv')">
                                <i class="bi bi-filetype-csv"></i>
                                <span>导出为 CSV</span>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-outline-primary d-flex align-items-center gap-2" id="refreshBtn">
                        <i class="bi bi-arrow-clockwise"></i>
                        刷新
                    </button>
                    <div class="dropdown-container" style="position: relative;">
                        <button class="btn btn-outline-primary d-flex align-items-center gap-2" id="columnSettingsBtn">
                            <i class="bi bi-gear"></i>
                            列设置
                        </button>
                        <div class="column-settings-popup" id="columnSettingsPopup">
                            <!-- Column settings will be dynamically generated from table header -->
                        </div>
                    </div>
                </div>
                <div class="dropdown-container">
                    <button class="btn btn-outline-secondary dropdown-toggle d-flex align-items-center gap-2" id="bulkActionsBtn">
                        已选择 <span id="selectedCount">0</span> 项
                        <i class="bi bi-chevron-down"></i>
                    </button>
                    <div class="dropdown-menu" id="bulkActionsDropdown">
                        <div class="dropdown-body">
                            <?php foreach (ContentStatus::cases() as $status): ?>
                            <div class="dropdown-item" data-action="<?= strtolower($status->name) ?>">
                                <?php $badgeClass = str_replace('text-bg-', 'text-', $status->bootstrapBadgeClass()); ?>
                                <i class="bi <?= $status->icon() ?> <?= $badgeClass ?>"></i>
                                批量设为: <?= $status->label() ?>
                            </div>
                            <?php endforeach; ?>
                            <div class="dropdown-divider"></div>
                            <div class="dropdown-item text-danger" data-action="delete">
                                <i class="bi bi-trash"></i>
                                批量删除
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="chart-body" style="height: auto; padding: 0 1.5rem 1.5rem 1.5rem;">
            <div class="table-responsive">
                <table class="table table-hover" id="dataTable">
                    <thead>
                        <tr class="table-header" id="tableHeader">
                            <th class="table-cell" style="width: 50px;" data-column="checkbox">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="selectAll">
                                </div>
                            </th>
                            <th class="table-cell sortable-header" data-column="id">
                                <div class="d-flex align-items-center">
                                    ID
                                    <div class="sort-icons-container">
                                        <i class="bi bi-caret-up-fill sort-icon" data-sort="id" data-direction="asc"></i>
                                        <i class="bi bi-caret-down-fill sort-icon sort-icon-up" data-sort="id" data-direction="desc"></i>
                                    </div>
                                </div>
                            </th>
                            <th class="table-cell sortable-header" data-column="content_type_id">
                                <div class="d-flex align-items-center">
                                    类型
                                    <div class="sort-icons-container">
                                        <i class="bi bi-caret-up-fill sort-icon" data-sort="content_type_id" data-direction="asc"></i>
                                        <i class="bi bi-caret-down-fill sort-icon sort-icon-up" data-sort="content_type_id" data-direction="desc"></i>
                                    </div>
                                </div>
                            </th>
                            <th class="table-cell sortable-header" data-column="title">
                                <div class="d-flex align-items-center">
                                    标题
                                    <div class="sort-icons-container">
                                        <i class="bi bi-caret-up-fill sort-icon" data-sort="title" data-direction="asc"></i>
                                        <i class="bi bi-caret-down-fill sort-icon sort-icon-up" data-sort="title" data-direction="desc"></i>
                                    </div>
                                </div>
                            </th>
                            <th class="table-cell sortable-header" data-column="code">
                                <div class="d-flex align-items-center">
                                    内部代码
                                    <div class="sort-icons-container">
                                        <i class="bi bi-caret-up-fill sort-icon" data-sort="code" data-direction="asc"></i>
                                        <i class="bi bi-caret-down-fill sort-icon sort-icon-up" data-sort="code" data-direction="desc"></i>
                                    </div>
                                </div>
                            </th>
                            <th class="table-cell sortable-header" data-column="author">
                                <div class="d-flex align-items-center">
                                    作者
                                    <div class="sort-icons-container">
                                        <i class="bi bi-caret-up-fill sort-icon" data-sort="author" data-direction="asc"></i>
                                        <i class="bi bi-caret-down-fill sort-icon sort-icon-up" data-sort="author" data-direction="desc"></i>
                                    </div>
                                </div>
                            </th>
                            <th class="table-cell sortable-header" data-column="pv_cnt" data-type="number">
                                <div class="d-flex align-items-center">
                                    观看数
                                    <div class="sort-icons-container">
                                        <i class="bi bi-caret-up-fill sort-icon" data-sort="pv_cnt" data-direction="asc"></i>
                                        <i class="bi bi-caret-down-fill sort-icon sort-icon-up" data-sort="pv_cnt" data-direction="desc"></i>
                                    </div>
                                </div>
                            </th>
                            <th class="table-cell sortable-header" data-column="pub_at">
                                <div class="d-flex align-items-center">
                                    发布日期
                                    <div class="sort-icons-container">
                                        <i class="bi bi-caret-up-fill sort-icon" data-sort="pub_at" data-direction="asc"></i>
                                        <i class="bi bi-caret-down-fill sort-icon sort-icon-up" data-sort="pub_at" data-direction="desc"></i>
                                    </div>
                                </div>
                            </th>
                            <th class="table-cell sortable-header" data-column="status_id">
                                <div class="d-flex align-items-center">
                                    状态
                                    <div class="sort-icons-container">
                                        <i class="bi bi-caret-up-fill sort-icon" data-sort="status_id" data-direction="asc"></i>
                                        <i class="bi bi-caret-down-fill sort-icon sort-icon-up" data-sort="status_id" data-direction="desc"></i>
                                    </div>
                                </div>
                            </th>
                            <th class="table-cell table-actions" style="width: 120px;" data-column="actions">操作</th>
                        </tr>
                        <tr class="table-header-bg">
                            <th class="table-filter-cell" data-column="checkbox"></th>
                            <th class="table-filter-cell" data-column="id">
                                <input type="text" class="form-control form-control-sm" placeholder="搜索ID" value="<?= htmlspecialchars($filters['id'] ?? '') ?>" >
                            </th>
                            <th class="table-filter-cell" data-column="content_type_id">
                                <select class="form-control form-select form-select-sm">
                                    <option value="">全部类型</option>
                                    <?php foreach (ContentType::getAllValues() as $value => $label): ?>
                                        <option value="<?= $value ?>" <?= ($filters['content_type_id'] ?? '') == $value ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </th>
                            <th class="table-filter-cell" data-column="title">
                                <input type="text" class="form-control form-control-sm" placeholder="搜索标题" value="<?= htmlspecialchars($filters['title'] ?? '') ?>">
                            </th>
                            <th class="table-filter-cell" data-column="code">
                                <input type="text" class="form-control form-control-sm" placeholder="搜索代码" value="<?= htmlspecialchars($filters['code'] ?? '') ?>">
                            </th>
                            <th class="table-filter-cell" data-column="author">
                                <input type="text" class="form-control form-control-sm" placeholder="搜索作者" value="<?= htmlspecialchars($filters['author'] ?? '') ?>">
                            </th>
                            <th class="table-filter-cell" data-column="pv_cnt">
                                <input type="text" class="form-control form-control-sm" placeholder="观看范围" value="<?= htmlspecialchars($filters['pv_cnt'] ?? '') ?>">
                            </th>
                            <th class="table-filter-cell" data-column="pub_at">
                                <input type="text" class="form-control form-control-sm" placeholder="发布日期范围" value="<?= htmlspecialchars($filters['pub_at'] ?? '') ?>">
                            </th>
                            <th class="table-filter-cell" data-column="status_id">
                                <select class="form-control form-select form-select-sm">
                                    <option value="">全部状态</option>
                                    <?php foreach (ContentStatus::getAllValues() as $value => $label): ?>
                                        <option value="<?= $value ?>" <?= ($filters['status_id'] ?? '') == $value ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </th>
                            <th class="table-filter-cell" data-column="actions"></th>
                        </tr>
                    </thead>
                    <tbody id="contentTableBody">
                        <?php if (!empty($content)): ?>
                            <?php foreach ($content as $item): ?>
                                <tr class="table-row" data-id="<?= $item['id'] ?>">
                                    <td class="table-cell">
                                        <div class="form-check">
                                            <input class="form-check-input row-checkbox" type="checkbox" value="<?= $item['id'] ?>">
                                        </div>
                                    </td>
                                    <td class="table-cell table-id" data-column="id"><?= $item['id'] ?></td>
                                    <td class="table-cell" data-column="content_type_id">
                                        <?php 
                                        $type = ContentType::tryFrom($item['content_type_id']);
                                        $typeClass = match($item['content_type_id']) {
                                            ContentType::VIDEO->value => 'content-type-video',
                                            ContentType::ARTICLE->value => 'content-type-article',
                                            ContentType::ANNOUNCEMENT->value => 'content-type-announcement',
                                            default => 'content-type-default'
                                        };
                                        $typeIcon = match($item['content_type_id']) {
                                            ContentType::VIDEO->value => 'bi-camera-video',
                                            ContentType::ARTICLE->value => 'bi-file-earmark-text',
                                            ContentType::ANNOUNCEMENT->value => 'bi-megaphone',
                                            default => 'bi-file'
                                        };
                                        ?>
                                        <span class="badge rounded-pill <?= $typeClass ?>">
                                            <i class="<?= $typeIcon ?> badge-icon"></i> 
                                            <?= htmlspecialchars($type ? $type->label() : '未知') ?>
                                        </span>
                                    </td>
                                    <td class="table-cell table-title" data-column="title">
                                        <div class="content-title-wrapper">
                                            <div class="content-title"><?= htmlspecialchars($item['title_cn'] ?: $item['title_en']) ?></div>
                                            <?php if ($item['title_cn'] && $item['title_en']): ?>
                                                <div class="content-subtitle"><?= htmlspecialchars($item['title_en']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="table-cell" data-column="code"><?= htmlspecialchars($item['code'] ?? '') ?></td>
                                    <td class="table-cell" data-column="author"><?= htmlspecialchars($item['author'] ?? 'DP') ?></td>
                                    <td class="table-cell" data-column="pv_cnt"><?= number_format($item['pv_cnt'] ?? 0) ?></td>
                                    <td class="table-cell" data-column="pub_at"><?= htmlspecialchars($item['pub_at'] ?? '') ?></td>
                                    <td class="table-cell" data-column="status_id">
                                        <?php 
                                        $status = ContentStatus::tryFrom($item['status_id']);
                                        $statusClass = $status ? $status->bootstrapBadgeClass() : 'text-bg-secondary';
                                        ?>
                                        <span class="badge rounded-pill <?= $statusClass ?>">
                                            <i class="bi bi-circle-fill badge-icon"></i> 
                                            <?= htmlspecialchars($status ? $status->label() : '未知') ?>
                                        </span>
                                    </td>
                                    <td class="table-cell table-actions" data-column="actions">
                                        <div class="d-flex gap-2 justify-content-center">
                                            <a href="/contents/<?= $item['id'] ?>/edit" class="btn btn-outline-primary btn-sm" title="编辑">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="/contents/<?= $item['id'] ?>/copy" class="btn btn-outline-warning btn-sm" title="复制">
                                                <i class="bi bi-copy"></i>
                                            </a>
                                            <a href="/contents/show/<?= $item['id'] ?>" class="btn btn-outline-info btn-sm d-none" title="查看">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <?php if ($item['content_type_id'] == \App\Constants\ContentType::VIDEO->value): ?>
                                            <a href="/video-links?content_id=<?= $item['id'] ?>" class="btn btn-outline-success btn-sm" title="视频链接">
                                                <i class="bi bi-link-45deg"></i>
                                            </a>
                                            <?php endif; ?>
                                            <button class="btn btn-outline-danger btn-sm delete-item" title="删除" data-id="<?= $item['id'] ?>">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                        暂无内容数据
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Table Summary and Pagination -->
            <div class="row align-items-center mt-4">
                <div class="col-md-6">
                    <div class="summary-box">
                        <div class="summary-text">
                            📊 <strong>汇总信息:</strong> 当前显示 <span class="summary-highlight" id="currentDisplay">1-<?= count($content) ?>/<?= count($content) ?></span> 条 |
                            总观看数: <span class="summary-highlight"><?= number_format($stats['total_views'] ?? 0) ?></span> 次 |
                            平均观看: <span class="summary-highlight"><?= number_format($stats['avg_views'] ?? 0, 1) ?></span> 次
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <nav aria-label="Page navigation">
                        <div class="d-flex justify-content-end align-items-center gap-3">
                            <div class="d-flex align-items-center gap-2">
                                <span class="pagination-text">每页</span>
                                <select class="form-select form-select-sm" id="itemsPerPage" style="width: auto;">
                                    <option value="5" selected>5</option>
                                    <option value="10" selected>10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                                <span class="pagination-text">条</span>
                            </div>
                            <ul class="pagination pagination-sm mb-0" id="paginationNav">
                                <!-- Pagination will be dynamically generated by JavaScript -->
                            </ul>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
// 包含内容批量导入组件
include __DIR__ . '/../common/_bulkImport.php';
?>

