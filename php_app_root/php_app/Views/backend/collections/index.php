<?php
use App\Constants\CollectionStatus;
?>
<!-- Collection Management Content -->
<main class="dashboard-content">
    <!-- Breadcrumb and Page Title -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-collection page-title-icon"></i>
                <div>
                    <h1 class="page-title">合集管理中心</h1>
                    <p class="page-subtitle">Collection Management Hub</p>
                </div>
            </div>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="/dashboard" class="breadcrumb-link">首页</a></li>
                <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">内容管理</a></li>
                <li class="breadcrumb-item active breadcrumb-active" aria-current="page">合集管理</li>
            </ol>
        </nav>
    </div>

    <!-- Quick Statistics Overview -->
    <div class="row mb-4">
        <div class="col-md-3 col-6 mb-3">
            <div class="metric-card-updated">
                <div class="metric-card-header">
                    <div class="metric-icon-small metric-icon-gradient-1">
                        <i class="bi bi-collection metric-icon-white"></i>
                    </div>
                    <div class="metric-header-text">
                        <h4>总合集</h4>
                        <span class="metric-subtitle">Total Collections</span>
                    </div>
                </div>
                <div class="metric-card-body">
                    <div class="metric-value-small"><?= $stats['total_collections'] ?></div>
                    <div class="monthly-stats">
                        <i class="bi bi-arrow-up monthly-stats-icon"></i>
                        <span>本月新增: <?= $stats['total_collections'] >= 8 ? '8' : $stats['total_collections'] ?></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="metric-card-updated">
                <div class="metric-card-header">
                    <div class="metric-icon-small metric-icon-gradient-2">
                        <i class="bi bi-check-circle metric-icon-white"></i>
                    </div>
                    <div class="metric-header-text">
                        <h4>已启用</h4>
                        <span class="metric-subtitle">Active Collections</span>
                    </div>
                </div>
                <div class="metric-card-body">
                    <div class="metric-value-small"><?= $stats['active_collections'] ?></div>
                    <div class="monthly-stats">
                        <i class="bi bi-check-circle monthly-stats-icon"></i>
                        <span>本月启用: <?= $stats['active_collections'] >= 6 ? '6' : $stats['active_collections'] ?></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="metric-card-updated">
                <div class="metric-card-header">
                    <div class="metric-icon-small metric-icon-gradient-3">
                        <i class="bi bi-x-circle metric-icon-white"></i>
                    </div>
                    <div class="metric-header-text">
                        <h4>已禁用</h4>
                        <span class="metric-subtitle">Disabled Collections</span>
                    </div>
                </div>
                <div class="metric-card-body">
                    <div class="metric-value-small"><?= $stats['inactive_collections'] ?></div>
                    <div class="monthly-stats">
                        <i class="bi bi-x-circle monthly-stats-icon"></i>
                        <span>本月禁用: <?= $stats['inactive_collections'] >= 2 ? '2' : $stats['inactive_collections'] ?></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="metric-card-updated">
                <div class="metric-card-header">
                    <div class="metric-icon-small metric-icon-gradient-4">
                        <i class="bi bi-link-45deg metric-icon-white"></i>
                    </div>
                    <div class="metric-header-text">
                        <h4>关联量</h4>
                        <span class="metric-subtitle">Associated Content</span>
                    </div>
                </div>
                <div class="metric-card-body">
                    <div class="metric-value-small"><?= number_format($stats['total_content_associations'], 0) ?></div>
                    <div class="monthly-stats">
                        <i class="bi bi-graph-up monthly-stats-icon"></i>
                        <span>本月增长: 120</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Collection List Management Card -->
    <div class="chart-container">
        <div class="chart-header pb-2">
            <div class="chart-header-left">
                <i class="bi bi-list-ul chart-title-icon"></i>
                <h3>📁 合集列表管理</h3>
            </div>
        </div>

        <!-- Action Buttons Row -->
        <div style="padding: 1.5rem 1.5rem 0 1.5rem;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex gap-2 flex-wrap">
                    <a href="/collections/create" class="btn btn-primary d-flex align-items-center gap-2">
                        <i class="bi bi-plus-lg"></i>
                        创建新合集
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
                            <div class="dropdown-item" data-action="enable">
                                <i class="bi bi-check-circle text-success"></i>
                                批量启用
                            </div>
                            <div class="dropdown-item" data-action="disable">
                                <i class="bi bi-x-circle text-warning"></i>
                                批量禁用
                            </div>
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
                            <th class="table-cell sortable-header" data-column="name">
                                <div class="d-flex align-items-center">
                                    合集名称
                                    <div class="sort-icons-container">
                                        <i class="bi bi-caret-up-fill sort-icon" data-sort="name" data-direction="asc"></i>
                                        <i class="bi bi-caret-down-fill sort-icon sort-icon-up" data-sort="name" data-direction="desc"></i>
                                    </div>
                                </div>
                            </th>
                            <th class="table-cell sortable-header" data-column="description">
                                <div class="d-flex align-items-center">
                                    描述
                                    <div class="sort-icons-container">
                                        <i class="bi bi-caret-up-fill sort-icon" data-sort="description" data-direction="asc"></i>
                                        <i class="bi bi-caret-down-fill sort-icon sort-icon-up" data-sort="description" data-direction="desc"></i>
                                    </div>
                                </div>
                            </th>
                            <th class="table-cell sortable-header" data-column="content_cnt">
                                <div class="d-flex align-items-center">
                                    关联内容
                                    <div class="sort-icons-container">
                                        <i class="bi bi-caret-up-fill sort-icon" data-sort="content_cnt" data-direction="asc"></i>
                                        <i class="bi bi-caret-down-fill sort-icon sort-icon-up" data-sort="content_cnt" data-direction="desc"></i>
                                    </div>
                                </div>
                            </th>
                            <th class="table-cell sortable-header" data-column="icon_class">
                                <div class="d-flex align-items-center">
                                    icon class
                                    <div class="sort-icons-container">
                                        <i class="bi bi-caret-up-fill sort-icon" data-sort="icon_class" data-direction="asc"></i>
                                        <i class="bi bi-caret-down-fill sort-icon sort-icon-up" data-sort="icon_class" data-direction="desc"></i>
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
                                <input type="text" name="id" class="form-control form-control-sm" placeholder="搜索ID" value="<?= htmlspecialchars($filters['id'] ?? '') ?>">
                            </th>
                            <th class="table-filter-cell" data-column="name">
                                <input type="text" name="name" class="form-control form-control-sm" placeholder="搜索合集名" value="<?= htmlspecialchars($filters['name'] ?? '') ?>">
                            </th>
                            <th class="table-filter-cell" data-column="description">
                                <input type="text" name="description" class="form-control form-control-sm" placeholder="搜索描述" value="<?= htmlspecialchars($filters['description'] ?? '') ?>">
                            </th>
                            <th class="table-filter-cell" data-column="content_cnt">
                                <input type="text" name="content_cnt" class="form-control form-control-sm" placeholder="数量范围" value="<?= htmlspecialchars($filters['content_cnt'] ?? '') ?>">
                            </th>
                            <th class="table-filter-cell" data-column="icon_class">
                                <input type="text" name="icon_class" class="form-control form-control-sm" placeholder="搜索icon" value="<?= htmlspecialchars($filters['icon_class'] ?? '') ?>">
                            </th>
                            <th class="table-filter-cell" data-column="status_id">
                                <select name="status_id" class="form-control form-select form-select-sm">
                                    <option value="">全部状态</option>
                                    <option value="<?= CollectionStatus::ENABLED->value ?>" <?= ($filters['status_id'] === (string)CollectionStatus::ENABLED->value) ? 'selected' : '' ?>>显示</option>
                                    <option value="<?= CollectionStatus::DISABLED->value ?>" <?= ($filters['status_id'] === (string)CollectionStatus::DISABLED->value) ? 'selected' : '' ?>>隐藏</option>
                                </select>
                            </th>
                            <th class="table-filter-cell" data-column="actions"></th>
                        </tr>
                    </thead>
                    <tbody id="collectionTableBody">
                        <?php foreach ($collections as $collection): ?>
                        <tr class="table-row" data-id="<?= $collection['id'] ?>">
                            <td class="table-cell">
                                <div class="form-check">
                                    <input class="form-check-input row-checkbox" type="checkbox" value="<?= $collection['id'] ?>">
                                </div>
                            </td>
                            <td class="table-cell table-id" data-column="id"><?= $collection['id'] ?></td>
                            <td class="table-cell table-name" data-column="name"><?= htmlspecialchars($collection['name_cn']) ?></td>
                            <td class="table-cell" data-column="description"><?= htmlspecialchars($collection['short_desc_cn'] ?: $collection['short_desc_en'] ?: '') ?></td>
                            <td class="table-cell" data-column="content_cnt">
                                <a href="/content?collection_id=<?= $collection['id'] ?>" class="content-link"><?= $collection['content_cnt'] ?></a>
                            </td>
                            <td class="table-cell" data-column="icon_class">
                                <div class="icon-class-display">
                                    <span class="bi <?= htmlspecialchars($collection['icon_class'] ?: 'bi-collection') ?>"></span>
                                    <?= htmlspecialchars($collection['icon_class'] ?: 'bi-collection') ?>
                                </div>
                            </td>
                            <td class="table-cell" data-column="status_id">
                                <span class="badge rounded-pill <?= $collection['status_id'] ? 'text-bg-success' : 'text-bg-danger' ?>">
                                    <i class="bi bi-circle-fill badge-icon"></i>
                                    <?= $collection['status_id'] ? '显示' : '隐藏' ?>
                                </span>
                            </td>
                            <td class="table-cell table-actions" data-column="actions">
                                <div class="d-flex gap-2 justify-content-center">
                                    <a href="/collections/<?= $collection['id'] ?>/edit" class="btn btn-outline-primary btn-sm" title="编辑">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="/collections/<?= $collection['id'] ?>" class="btn btn-outline-info btn-sm" title="查看">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <button class="btn btn-outline-danger btn-sm delete-item" title="删除" data-id="<?= htmlspecialchars($collection['id']) ?>">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Table Summary and Pagination -->
            <div class="row align-items-center mt-4">
                <div class="col-md-6">
                    <div class="summary-box">
                        <div class="summary-text">
                            📊 <strong>汇总信息:</strong> 当前显示 <span class="summary-highlight" id="currentDisplay">1-<?= count($collections) ?>/<?= count($collections) ?></span> 条 |
                            关联内容总计: <span class="summary-highlight"><?= $stats['total_content_associations'] ?></span> 个 |
                            平均每合集: <span class="summary-highlight"><?= $stats['total_collections'] > 0 ? number_format($stats['total_content_associations'] / $stats['total_collections'], 1) : '0' ?></span> 个
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <nav aria-label="Page navigation">
                        <div class="d-flex justify-content-end align-items-center gap-3">
                            <div class="d-flex align-items-center gap-2">
                                <span class="pagination-text">每页</span>
                                <select class="form-select form-select-sm" id="itemsPerPage" style="width: auto;">
                                    <option value="5">5</option>
                                    <option value="10" selected>10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                                <span class="pagination-text">条</span>
                            </div>
                            <ul class="pagination pagination-sm mb-0" id="paginationNav">
                                <!-- 分页导航将由JS动态生成 -->
                            </ul>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
// Set form action for create
include __DIR__ . '/../common/_bulkImport.php';
?>