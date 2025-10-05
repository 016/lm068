<?php
use App\Constants\LinkStatus;
?>
<!-- Video Link Management Content -->
<main class="dashboard-content">
    <!-- Breadcrumb and Page Title -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-link-45deg page-title-icon"></i>
                <div>
                    <h1 class="page-title">视频链接管理中心</h1>
                    <p class="page-subtitle">Video Link Management Hub</p>
                </div>
            </div>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="/dashboard" class="breadcrumb-link">首页</a></li>
                <li class="breadcrumb-item"><a href="/video-links" class="breadcrumb-link">视频链接</a></li>
                <li class="breadcrumb-item active breadcrumb-active" aria-current="page">链接列表</li>
            </ol>
        </nav>
    </div>

    <!-- Quick Statistics Overview -->
    <div class="row mb-4">
        <div class="col-md-3 col-6 mb-3">
            <div class="metric-card-updated">
                <div class="metric-card-header">
                    <div class="metric-icon-small metric-icon-gradient-1">
                        <i class="bi bi-link-45deg metric-icon-white"></i>
                    </div>
                    <div class="metric-header-text">
                        <h4>总链接</h4>
                        <span class="metric-subtitle">Total Links</span>
                    </div>
                </div>
                <div class="metric-card-body">
                    <div class="metric-value-small"><?= number_format($stats['total_links'] ?? 0) ?></div>
                    <div class="monthly-stats">
                        <i class="bi bi-arrow-up monthly-stats-icon"></i>
                        <span>有效: <?= number_format($stats['valid_links'] ?? 0) ?></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="metric-card-updated">
                <div class="metric-card-header">
                    <div class="metric-icon-small metric-icon-gradient-2">
                        <i class="bi bi-play-circle metric-icon-white"></i>
                    </div>
                    <div class="metric-header-text">
                        <h4>总播放</h4>
                        <span class="metric-subtitle">Total Plays</span>
                    </div>
                </div>
                <div class="metric-card-body">
                    <div class="metric-value-small"><?= number_format($stats['total_plays'] ?? 0) ?></div>
                    <div class="monthly-stats">
                        <i class="bi bi-play-circle monthly-stats-icon"></i>
                        <span>播放次数</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="metric-card-updated">
                <div class="metric-card-header">
                    <div class="metric-icon-small metric-icon-gradient-3">
                        <i class="bi bi-heart metric-icon-white"></i>
                    </div>
                    <div class="metric-header-text">
                        <h4>总点赞</h4>
                        <span class="metric-subtitle">Total Likes</span>
                    </div>
                </div>
                <div class="metric-card-body">
                    <div class="metric-value-small"><?= number_format($stats['total_likes'] ?? 0) ?></div>
                    <div class="monthly-stats">
                        <i class="bi bi-heart monthly-stats-icon"></i>
                        <span>点赞数</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="metric-card-updated">
                <div class="metric-card-header">
                    <div class="metric-icon-small metric-icon-gradient-4">
                        <i class="bi bi-star metric-icon-white"></i>
                    </div>
                    <div class="metric-header-text">
                        <h4>总收藏</h4>
                        <span class="metric-subtitle">Total Favorites</span>
                    </div>
                </div>
                <div class="metric-card-body">
                    <div class="metric-value-small"><?= number_format($stats['total_favorites'] ?? 0) ?></div>
                    <div class="monthly-stats">
                        <i class="bi bi-star monthly-stats-icon"></i>
                        <span>收藏数</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Video Link List Management Card -->
    <div class="chart-container">
        <div class="chart-header pb-2">
            <div class="chart-header-left">
                <i class="bi bi-list-ul chart-title-icon"></i>
                <h3>🔗 视频链接列表管理</h3>
            </div>
        </div>

        <!-- Action Buttons Row -->
        <div style="padding: 1.5rem 1.5rem 0 1.5rem;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex gap-2 flex-wrap">
                    <a href="/video-links/create<?= isset($filters['content_id']) && $filters['content_id'] ? '?content_id=' . $filters['content_id'] : '' ?>" class="btn btn-primary d-flex align-items-center gap-2">
                        <i class="bi bi-plus-lg"></i>
                        创建新链接
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
                            <th class="table-cell sortable-header" data-column="content_id">
                                <div class="d-flex align-items-center">
                                    关联内容
                                    <div class="sort-icons-container">
                                        <i class="bi bi-caret-up-fill sort-icon" data-sort="content_id" data-direction="asc"></i>
                                        <i class="bi bi-caret-down-fill sort-icon sort-icon-up" data-sort="content_id" data-direction="desc"></i>
                                    </div>
                                </div>
                            </th>
                            <th class="table-cell sortable-header" data-column="platform_id">
                                <div class="d-flex align-items-center">
                                    平台
                                    <div class="sort-icons-container">
                                        <i class="bi bi-caret-up-fill sort-icon" data-sort="platform_id" data-direction="asc"></i>
                                        <i class="bi bi-caret-down-fill sort-icon sort-icon-up" data-sort="platform_id" data-direction="desc"></i>
                                    </div>
                                </div>
                            </th>
                            <th class="table-cell sortable-header" data-column="play_cnt">
                                <div class="d-flex align-items-center">
                                    播放数
                                    <div class="sort-icons-container">
                                        <i class="bi bi-caret-up-fill sort-icon" data-sort="play_cnt" data-direction="asc"></i>
                                        <i class="bi bi-caret-down-fill sort-icon sort-icon-up" data-sort="play_cnt" data-direction="desc"></i>
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
                                <input type="text" class="form-control form-control-sm" placeholder="搜索ID" value="<?= htmlspecialchars($filters['id'] ?? '') ?>">
                            </th>
                            <th class="table-filter-cell" data-column="content_id">
                                <select class="form-control form-select form-select-sm">
                                    <option value="">全部内容</option>
                                    <?php foreach ($contentsList as $content): ?>
                                        <option value="<?= $content['id'] ?>" <?= ($filters['content_id'] ?? '') == $content['id'] ? 'selected' : '' ?>><?= htmlspecialchars($content['text']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </th>
                            <th class="table-filter-cell" data-column="platform_id">
                                <select class="form-control form-select form-select-sm">
                                    <option value="">全部平台</option>
                                    <?php foreach ($platformsList as $platform): ?>
                                        <option value="<?= $platform['id'] ?>" <?= ($filters['platform_id'] ?? '') == $platform['id'] ? 'selected' : '' ?>><?= htmlspecialchars($platform['text']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </th>
                            <th class="table-filter-cell" data-column="play_cnt">
                                <input type="text" class="form-control form-control-sm" placeholder="播放范围" value="<?= htmlspecialchars($filters['play_cnt'] ?? '') ?>">
                            </th>
                            <th class="table-filter-cell" data-column="status_id">
                                <select class="form-control form-select form-select-sm">
                                    <option value="">全部状态</option>
                                    <?php foreach (LinkStatus::getAllValues() as $value => $label): ?>
                                        <option value="<?= $value ?>" <?= ($filters['status_id'] ?? '') == $value ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </th>
                            <th class="table-filter-cell" data-column="actions"></th>
                        </tr>
                    </thead>
                    <tbody id="videoLinkTableBody">
                        <?php if (!empty($videoLinks)): ?>
                            <?php foreach ($videoLinks as $item): ?>
                                <tr class="table-row" data-id="<?= $item['id'] ?>">
                                    <td class="table-cell">
                                        <div class="form-check">
                                            <input class="form-check-input row-checkbox" type="checkbox" value="<?= $item['id'] ?>">
                                        </div>
                                    </td>
                                    <td class="table-cell table-id" data-column="id"><?= $item['id'] ?></td>
                                    <td class="table-cell" data-column="content_id">
                                        <a href="/contents/<?= $item['content_id'] ?>/edit" class="text-decoration-none">
                                            <?= htmlspecialchars($item['content_title'] ?? "ID: {$item['content_id']}") ?>
                                        </a>
                                    </td>
                                    <td class="table-cell" data-column="platform_id">
                                        <span class="badge rounded-pill text-bg-info">
                                            <i class="bi bi-play-btn badge-icon"></i>
                                            <?= htmlspecialchars($item['platform_name'] ?? '') ?>
                                        </span>
                                    </td>
                                    <td class="table-cell" data-column="play_cnt"><?= number_format($item['play_cnt'] ?? 0) ?></td>
                                    <td class="table-cell" data-column="status_id">
                                        <?php
                                        $status = LinkStatus::tryFrom($item['status_id']);
                                        $statusClass = match($item['status_id']) {
                                            LinkStatus::VALID->value => 'text-bg-success',
                                            LinkStatus::INVALID->value => 'text-bg-danger',
                                            default => 'text-bg-secondary'
                                        };
                                        ?>
                                        <span class="badge rounded-pill <?= $statusClass ?>">
                                            <i class="bi bi-circle-fill badge-icon"></i>
                                            <?= htmlspecialchars($status ? $status->label() : '未知') ?>
                                        </span>
                                    </td>
                                    <td class="table-cell table-actions" data-column="actions">
                                        <div class="d-flex gap-2 justify-content-center">
                                            <a href="/video-links/<?= $item['id'] ?>/edit" class="btn btn-outline-primary btn-sm" title="编辑">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="<?= htmlspecialchars($item['external_url']) ?>" target="_blank" class="btn btn-outline-info btn-sm" title="查看链接">
                                                <i class="bi bi-box-arrow-up-right"></i>
                                            </a>
                                            <button class="btn btn-outline-danger btn-sm delete-item" title="删除" data-id="<?= $item['id'] ?>">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                        暂无视频链接数据
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
                            📊 <strong>汇总信息:</strong> 当前显示 <span class="summary-highlight" id="currentDisplay">1-<?= count($videoLinks) ?>/<?= count($videoLinks) ?></span> 条 |
                            总播放数: <span class="summary-highlight"><?= number_format($stats['total_plays'] ?? 0) ?></span> 次 |
                            总点赞: <span class="summary-highlight"><?= number_format($stats['total_likes'] ?? 0) ?></span> 次
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
// 包含视频链接批量导入组件
include __DIR__ . '/../common/_bulkImport.php';
?>
