<!-- Tag Management Content -->
<main class="dashboard-content">
    <!-- Breadcrumb and Page Title -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-tags page-title-icon"></i>
                <div>
                    <h1 class="page-title">标签管理中心</h1>
                    <p class="page-subtitle">Tag Management Hub</p>
                </div>
            </div>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="/dashboard" class="breadcrumb-link">首页</a></li>
                <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">内容管理</a></li>
                <li class="breadcrumb-item active breadcrumb-active" aria-current="page">标签管理</li>
            </ol>
        </nav>
    </div>

    <!-- Quick Statistics Overview -->
    <div class="row mb-4">
        <div class="col-md-3 col-6 mb-3">
            <div class="metric-card-updated">
                <div class="metric-card-header">
                    <div class="metric-icon-small metric-icon-gradient-1">
                        <i class="bi bi-tags metric-icon-white"></i>
                    </div>
                    <div class="metric-header-text">
                        <h4>总标签</h4>
                        <span class="metric-subtitle">Total Tags</span>
                    </div>
                </div>
                <div class="metric-card-body">
                    <div class="metric-value-small"><?= $stats['total_tags'] ?></div>
                    <div class="monthly-stats">
                        <i class="bi bi-arrow-up monthly-stats-icon"></i>
                        <span>本月新增: 15</span>
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
                        <span class="metric-subtitle">Active Tags</span>
                    </div>
                </div>
                <div class="metric-card-body">
                    <div class="metric-value-small"><?= $stats['active_tags'] ?></div>
                    <div class="monthly-stats">
                        <i class="bi bi-check-circle monthly-stats-icon"></i>
                        <span>启用比例: <?= $stats['total_tags'] > 0 ? round(($stats['active_tags'] / $stats['total_tags']) * 100) : 0 ?>%</span>
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
                        <span class="metric-subtitle">Disabled Tags</span>
                    </div>
                </div>
                <div class="metric-card-body">
                    <div class="metric-value-small"><?= $stats['inactive_tags'] ?></div>
                    <div class="monthly-stats">
                        <i class="bi bi-x-circle monthly-stats-icon"></i>
                        <span>禁用比例: <?= $stats['total_tags'] > 0 ? round(($stats['inactive_tags'] / $stats['total_tags']) * 100) : 0 ?>%</span>
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
                        <span class="metric-subtitle">Associated Videos</span>
                    </div>
                </div>
                <div class="metric-card-body">
                    <div class="metric-value-small"><?= number_format($stats['total_content_associations']) ?></div>
                    <div class="monthly-stats">
                        <i class="bi bi-graph-up monthly-stats-icon"></i>
                        <span>平均关联: <?= $stats['total_tags'] > 0 ? round($stats['total_content_associations'] / $stats['total_tags'], 1) : 0 ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tag List Management Card -->
    <div class="chart-container">
        <div class="chart-header pb-2">
            <div class="chart-header-left">
                <i class="bi bi-list-ul chart-title-icon"></i>
                <h3>🏷️ 标签列表管理</h3>
            </div>
        </div>
        
        <!-- Action Buttons Row -->
        <div style="padding: 1.5rem 1.5rem 0 1.5rem;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex gap-2 flex-wrap">
                    <button class="btn btn-primary d-flex align-items-center gap-2" onclick="createTag()">
                        <i class="bi bi-plus-lg"></i>
                        创建新标签
                    </button>
                    <button class="btn btn-outline-primary d-flex align-items-center gap-2">
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
                    <button class="btn btn-outline-primary d-flex align-items-center gap-2" id="refreshBtn" onclick="refreshTable()">
                        <i class="bi bi-arrow-clockwise"></i>
                        刷新
                    </button>
                    <div class="dropdown-container" style="position: relative;">
                        <button class="btn btn-outline-primary d-flex align-items-center gap-2" id="columnSettingsBtn">
                            <i class="bi bi-gear"></i>
                            列设置
                        </button>
                        <div class="column-settings-popup" id="columnSettingsPopup">
                            <div class="popup-checkbox">
                                <input type="checkbox" id="col-id" checked>
                                <label for="col-id">ID</label>
                            </div>
                            <div class="popup-checkbox">
                                <input type="checkbox" id="col-name" checked>
                                <label for="col-name">标签名称</label>
                            </div>
                            <div class="popup-checkbox">
                                <input type="checkbox" id="col-videos" checked>
                                <label for="col-videos">关联视频</label>
                            </div>
                            <div class="popup-checkbox">
                                <input type="checkbox" id="col-status" checked>
                                <label for="col-status">状态</label>
                            </div>
                            <div class="popup-checkbox">
                                <input type="checkbox" id="col-actions" checked>
                                <label for="col-actions">操作</label>
                            </div>
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
                <table class="table table-hover">
                    <thead>
                        <tr class="table-header">
                            <th class="table-cell" style="width: 50px;" data-column="checkbox">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="selectAll">
                                </div>
                            </th>
                            <th class="table-cell" data-column="id">
                                <div class="d-flex align-items-center gap-2">
                                    ID
                                    <div class="d-flex flex-column">
                                        <i class="bi bi-caret-up-fill sort-btn sort-icon" data-sort="id" data-direction="asc"></i>
                                        <i class="bi bi-caret-down-fill sort-btn sort-icon sort-icon-up" data-sort="id" data-direction="desc"></i>
                                    </div>
                                </div>
                            </th>
                            <th class="table-cell" data-column="name">
                                <div class="d-flex align-items-center gap-2">
                                    标签名称
                                    <div class="d-flex flex-column">
                                        <i class="bi bi-caret-up-fill sort-btn sort-icon" data-sort="name_cn" data-direction="asc"></i>
                                        <i class="bi bi-caret-down-fill sort-btn sort-icon sort-icon-up" data-sort="name_cn" data-direction="desc"></i>
                                    </div>
                                </div>
                            </th>
                            <th class="table-cell" data-column="videos">
                                <div class="d-flex align-items-center gap-2">
                                    关联视频
                                    <div class="d-flex flex-column">
                                        <i class="bi bi-caret-up-fill sort-btn sort-icon" data-sort="content_cnt" data-direction="asc"></i>
                                        <i class="bi bi-caret-down-fill sort-btn sort-icon sort-icon-up" data-sort="content_cnt" data-direction="desc"></i>
                                    </div>
                                </div>
                            </th>
                            <th class="table-cell" data-column="status">
                                <div class="d-flex align-items-center gap-2">
                                    状态
                                    <div class="d-flex flex-column">
                                        <i class="bi bi-caret-up-fill sort-btn sort-icon" data-sort="status_id" data-direction="asc"></i>
                                        <i class="bi bi-caret-down-fill sort-btn sort-icon sort-icon-up" data-sort="status_id" data-direction="desc"></i>
                                    </div>
                                </div>
                            </th>
                            <th class="table-cell table-actions" style="width: 120px;" data-column="actions">操作</th>
                        </tr>
                        <tr class="table-header-bg">
                            <th class="table-filter-cell" data-column="checkbox"></th>
                            <th class="table-filter-cell" data-column="id">
                                <input type="text" class="form-control form-control-sm" placeholder="搜索ID" id="filter-id">
                            </th>
                            <th class="table-filter-cell" data-column="name">
                                <input type="text" class="form-control form-control-sm" placeholder="搜索标签名" id="filter-name">
                            </th>
                            <th class="table-filter-cell" data-column="videos">
                                <input type="text" class="form-control form-control-sm" placeholder="数量范围" id="filter-videos">
                            </th>
                            <th class="table-filter-cell" data-column="status">
                                <select class="form-control form-select form-select-sm" id="filter-status">
                                    <option value="">全部状态</option>
                                    <option value="1" <?= $status === '1' ? 'selected' : '' ?>>启用</option>
                                    <option value="0" <?= $status === '0' ? 'selected' : '' ?>>禁用</option>
                                </select>
                            </th>
                            <th class="table-filter-cell" data-column="actions"></th>
                        </tr>
                    </thead>
                    <tbody id="tagTableBody">
                        <?php foreach ($tags as $tag): ?>
                        <tr data-id="<?= $tag['id'] ?>">
                            <td class="table-cell">
                                <div class="form-check">
                                    <input class="form-check-input row-checkbox" type="checkbox" value="<?= $tag['id'] ?>">
                                </div>
                            </td>
                            <td class="table-cell">#<?= sprintf('%03d', $tag['id']) ?></td>
                            <td class="table-cell">
                                <div class="tag-info">
                                    <div class="tag-preview">
                                        <button type="button" class="btn <?= $tag['color_class'] ?: 'btn-outline-primary' ?> btn-sm">
                                            <?php if ($tag['icon_class']): ?>
                                                <i class="bi <?= $tag['icon_class'] ?>"></i>
                                            <?php endif; ?>
                                            <span><?= htmlspecialchars($tag['name_cn']) ?></span>
                                        </button>
                                    </div>
                                    <div class="tag-names">
                                        <div class="tag-name-cn"><?= htmlspecialchars($tag['name_cn']) ?></div>
                                        <div class="tag-name-en"><?= htmlspecialchars($tag['name_en']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="table-cell">
                                <div class="content-count">
                                    <span class="count-number"><?= $tag['content_cnt'] ?></span>
                                    <small class="text-muted">个视频</small>
                                </div>
                            </td>
                            <td class="table-cell">
                                <?php if ($tag['status_id'] == 1): ?>
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle"></i> 启用
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-warning">
                                        <i class="bi bi-x-circle"></i> 禁用
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="table-cell">
                                <div class="action-buttons">
                                    <button class="btn btn-outline-primary btn-sm" onclick="editTag(<?= $tag['id'] ?>)" title="编辑">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-outline-info btn-sm" onclick="viewTag(<?= $tag['id'] ?>)" title="查看">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm" onclick="deleteTag(<?= $tag['id'] ?>)" title="删除">
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
                            📊 <strong>汇总信息:</strong> 当前显示 
                            <span class="summary-highlight" id="currentDisplay">
                                <?= (($pagination['current_page'] - 1) * $pagination['per_page'] + 1) ?>-<?= min($pagination['current_page'] * $pagination['per_page'], $pagination['total']) ?>/<?= $pagination['total'] ?>
                            </span> 条 | 
                            关联视频总计: <span class="summary-highlight"><?= number_format($stats['total_content_associations']) ?></span> 个 | 
                            平均每标签: <span class="summary-highlight"><?= $stats['total_tags'] > 0 ? round($stats['total_content_associations'] / $stats['total_tags'], 1) : 0 ?></span> 个
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <nav aria-label="Page navigation">
                        <div class="d-flex justify-content-end align-items-center gap-3">
                            <div class="d-flex align-items-center gap-2">
                                <span class="pagination-text">每页</span>
                                <select class="form-select form-select-sm" id="itemsPerPage" style="width: auto;">
                                    <option value="5" <?= $pagination['per_page'] == 5 ? 'selected' : '' ?>>5</option>
                                    <option value="10" <?= $pagination['per_page'] == 10 ? 'selected' : '' ?>>10</option>
                                    <option value="20" <?= $pagination['per_page'] == 20 ? 'selected' : '' ?>>20</option>
                                    <option value="50" <?= $pagination['per_page'] == 50 ? 'selected' : '' ?>>50</option>
                                    <option value="100" <?= $pagination['per_page'] == 100 ? 'selected' : '' ?>>100</option>
                                </select>
                                <span class="pagination-text">条</span>
                            </div>
                            <ul class="pagination pagination-sm mb-0" id="paginationNav">
                                <?php if ($pagination['has_prev']): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?= $pagination['current_page'] - 1 ?>&per_page=<?= $pagination['per_page'] ?>" data-page="<?= $pagination['current_page'] - 1 ?>">上一页</a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php 
                                $start = max(1, $pagination['current_page'] - 2);
                                $end = min($pagination['total_pages'], $pagination['current_page'] + 2);
                                ?>
                                
                                <?php for ($i = $start; $i <= $end; $i++): ?>
                                    <li class="page-item <?= $i == $pagination['current_page'] ? 'active' : '' ?>">
                                        <a class="page-link" href="?page=<?= $i ?>&per_page=<?= $pagination['per_page'] ?>" data-page="<?= $i ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php if ($pagination['has_next']): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?= $pagination['current_page'] + 1 ?>&per_page=<?= $pagination['per_page'] ?>" data-page="<?= $pagination['current_page'] + 1 ?>">下一页</a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
// Page specific JavaScript functions
function createTag() {
    window.location.href = '/tags/create';
}

function editTag(id) {
    window.location.href = '/tags/' + id + '/edit';
}

function viewTag(id) {
    // Implementation for viewing tag details
    console.log('Viewing tag:', id);
}

function deleteTag(id) {
    if (confirm('确定要删除此标签吗？这将同时删除所有关联关系。')) {
        fetch('/tags/' + id + '/delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('删除成功');
                location.reload();
            } else {
                alert('删除失败: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('删除失败');
        });
    }
}

function exportData(format) {
    window.location.href = '/tags/export?format=' + format;
}

function refreshTable() {
    location.reload();
}
</script>