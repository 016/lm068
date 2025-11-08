<?php
use App\Constants\AdminUserStatus;
use App\Constants\AdminUserRole;
?>
<!-- AdminUser Management Content -->
<main class="dashboard-content">
    <!-- Breadcrumb and Page Title -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-person-gear page-title-icon"></i>
                <div>
                    <h1 class="page-title">管理员管理中心</h1>
                    <p class="page-subtitle">Admin User Management Hub</p>
                </div>
            </div>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="/backend" class="breadcrumb-link">首页</a></li>
                <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">系统管理</a></li>
                <li class="breadcrumb-item active breadcrumb-active" aria-current="page">管理员管理</li>
            </ol>
        </nav>
    </div>

    <!-- Quick Statistics Overview -->
    <div class="row mb-4">
        <div class="col-md-3 col-6 mb-3">
            <div class="metric-card-updated">
                <div class="metric-card-header">
                    <div class="metric-icon-small metric-icon-gradient-1">
                        <i class="bi bi-people metric-icon-white"></i>
                    </div>
                    <div class="metric-header-text">
                        <h4>总管理员</h4>
                        <span class="metric-subtitle">Total Admins</span>
                    </div>
                </div>
                <div class="metric-card-body">
                    <div class="metric-value-small"><?= number_format($stats['total_admins'] ?? 0) ?></div>
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
                        <span class="metric-subtitle">Active Admins</span>
                    </div>
                </div>
                <div class="metric-card-body">
                    <div class="metric-value-small"><?= number_format($stats['active_admins'] ?? 0) ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="metric-card-updated">
                <div class="metric-card-header">
                    <div class="metric-icon-small metric-icon-gradient-3">
                        <i class="bi bi-star metric-icon-white"></i>
                    </div>
                    <div class="metric-header-text">
                        <h4>超级管理员</h4>
                        <span class="metric-subtitle">Super Admins</span>
                    </div>
                </div>
                <div class="metric-card-body">
                    <div class="metric-value-small"><?= number_format($stats['super_admins'] ?? 0) ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="metric-card-updated">
                <div class="metric-card-header">
                    <div class="metric-icon-small metric-icon-gradient-4">
                        <i class="bi bi-person metric-icon-white"></i>
                    </div>
                    <div class="metric-header-text">
                        <h4>普通管理员</h4>
                        <span class="metric-subtitle">Normal Admins</span>
                    </div>
                </div>
                <div class="metric-card-body">
                    <div class="metric-value-small"><?= number_format($stats['normal_admins'] ?? 0) ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- AdminUser List Management Card -->
    <div class="chart-container">
        <div class="chart-header pb-2">
            <div class="chart-header-left">
                <i class="bi bi-list-ul chart-title-icon"></i>
                <h3>👥 管理员列表管理</h3>
            </div>
        </div>

        <!-- Action Buttons Row -->
        <div style="padding: 1.5rem 1.5rem 0 1.5rem;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex gap-2 flex-wrap">
                    <a href="/admin_users/create" class="btn btn-primary d-flex align-items-center gap-2">
                        <i class="bi bi-plus-lg"></i>
                        创建新管理员
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
                            <th class="table-cell sortable-header" data-column="username">
                                <div class="d-flex align-items-center">
                                    用户名
                                    <div class="sort-icons-container">
                                        <i class="bi bi-caret-up-fill sort-icon" data-sort="username" data-direction="asc"></i>
                                        <i class="bi bi-caret-down-fill sort-icon sort-icon-up" data-sort="username" data-direction="desc"></i>
                                    </div>
                                </div>
                            </th>
                            <th class="table-cell sortable-header" data-column="real_name">
                                <div class="d-flex align-items-center">
                                    真实姓名
                                    <div class="sort-icons-container">
                                        <i class="bi bi-caret-up-fill sort-icon" data-sort="real_name" data-direction="asc"></i>
                                        <i class="bi bi-caret-down-fill sort-icon sort-icon-up" data-sort="real_name" data-direction="desc"></i>
                                    </div>
                                </div>
                            </th>
                            <th class="table-cell sortable-header" data-column="email">
                                <div class="d-flex align-items-center">
                                    邮箱
                                    <div class="sort-icons-container">
                                        <i class="bi bi-caret-up-fill sort-icon" data-sort="email" data-direction="asc"></i>
                                        <i class="bi bi-caret-down-fill sort-icon sort-icon-up" data-sort="email" data-direction="desc"></i>
                                    </div>
                                </div>
                            </th>
                            <th class="table-cell sortable-header" data-column="role_id">
                                <div class="d-flex align-items-center">
                                    角色
                                    <div class="sort-icons-container">
                                        <i class="bi bi-caret-up-fill sort-icon" data-sort="role_id" data-direction="asc"></i>
                                        <i class="bi bi-caret-down-fill sort-icon sort-icon-up" data-sort="role_id" data-direction="desc"></i>
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
                            <th class="table-filter-cell" data-column="username">
                                <input type="text" name="username" class="form-control form-control-sm" placeholder="搜索用户名" value="<?= htmlspecialchars($filters['username'] ?? '') ?>">
                            </th>
                            <th class="table-filter-cell" data-column="real_name">
                                <input type="text" name="real_name" class="form-control form-control-sm" placeholder="搜索姓名" value="<?= htmlspecialchars($filters['real_name'] ?? '') ?>">
                            </th>
                            <th class="table-filter-cell" data-column="email">
                                <input type="text" name="email" class="form-control form-control-sm" placeholder="搜索邮箱" value="<?= htmlspecialchars($filters['email'] ?? '') ?>">
                            </th>
                            <th class="table-filter-cell" data-column="role_id">
                                <select name="role_id" class="form-control form-select form-select-sm">
                                    <option value="">全部角色</option>
                                    <option value="<?= AdminUserRole::SUPER_ADMIN->value ?>" <?= ($filters['role_id'] === (string)AdminUserRole::SUPER_ADMIN->value) ? 'selected' : '' ?>>超级管理员</option>
                                    <option value="<?= AdminUserRole::NORMAL->value ?>" <?= ($filters['role_id'] === (string)AdminUserRole::NORMAL->value) ? 'selected' : '' ?>>普通管理员</option>
                                </select>
                            </th>
                            <th class="table-filter-cell" data-column="status_id">
                                <select name="status_id" class="form-control form-select form-select-sm">
                                    <option value="">全部状态</option>
                                    <option value="<?= AdminUserStatus::ENABLED->value ?>" <?= ($filters['status_id'] === (string)AdminUserStatus::ENABLED->value) ? 'selected' : '' ?>>启用</option>
                                    <option value="<?= AdminUserStatus::DISABLED->value ?>" <?= ($filters['status_id'] === (string)AdminUserStatus::DISABLED->value) ? 'selected' : '' ?>>禁用</option>
                                </select>
                            </th>
                            <th class="table-filter-cell" data-column="actions"></th>
                        </tr>
                    </thead>
                    <tbody id="adminUserTableBody">
                        <?php if (!empty($adminUsers)): ?>
                            <?php foreach ($adminUsers as $adminUser): ?>
                                <tr class="table-row" data-id="<?= htmlspecialchars($adminUser->id) ?>">
                                    <td class="table-cell">
                                        <div class="form-check">
                                            <input class="form-check-input row-checkbox" type="checkbox" value="<?= htmlspecialchars($adminUser->id) ?>">
                                        </div>
                                    </td>
                                    <td class="table-cell table-id" data-column="id"><?= htmlspecialchars($adminUser->id) ?></td>
                                    <td class="table-cell table-name" data-column="username"><?= htmlspecialchars($adminUser->username) ?></td>
                                    <td class="table-cell" data-column="real_name"><?= htmlspecialchars($adminUser->real_name ?: '-') ?></td>
                                    <td class="table-cell" data-column="email"><?= htmlspecialchars($adminUser->email ?: '-') ?></td>
                                    <td class="table-cell" data-column="role_id">
                                        <span class="badge rounded-pill <?= $adminUser->role_id >= AdminUserRole::SUPER_ADMIN->value ? 'text-bg-danger' : 'text-bg-info' ?>">
                                            <i class="bi bi-circle-fill badge-icon"></i>
                                            <?= $adminUser->role_id >= AdminUserRole::SUPER_ADMIN->value ? '超级管理员' : '普通管理员' ?>
                                        </span>
                                    </td>
                                    <td class="table-cell" data-column="status_id">
                                        <span class="badge rounded-pill <?= $adminUser->status_id ? 'text-bg-success' : 'text-bg-secondary' ?>">
                                            <i class="bi bi-circle-fill badge-icon"></i>
                                            <?= $adminUser->status_id ? '启用' : '禁用' ?>
                                        </span>
                                    </td>
                                    <td class="table-cell table-actions" data-column="actions">
                                        <div class="d-flex gap-2 justify-content-center">
                                            <a href="/admin_users/<?= htmlspecialchars($adminUser->id) ?>/edit" class="btn btn-outline-primary btn-sm" title="编辑">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <?php if ($adminUser->id != $_SESSION['admin_id']): ?>
                                            <button class="btn btn-outline-danger btn-sm delete-item" title="删除" data-id="<?= htmlspecialchars($adminUser->id) ?>">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="empty-state">
                                        <i class="bi bi-inbox" style="font-size: 3rem; color: #6c757d;"></i>
                                        <p class="mt-2 text-muted">暂无管理员数据</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Table Summary - 由JS动态更新 -->
            <div class="row align-items-center mt-4">
                <div class="col-md-6">
                    <div class="summary-box">
                        <div class="summary-text">
                            📊 <strong>汇总信息:</strong> 当前显示
                            <span class="summary-highlight" id="currentDisplay">
                                <!-- 由JS动态更新 -->
                            </span> 条
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
                                    <option value="10">10</option>
                                    <option value="20" selected>20</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                                <span class="pagination-text">条</span>
                            </div>
                            <!-- 分页导航由JS动态生成 -->
                            <ul class="pagination pagination-sm mb-0" id="paginationNav">
                                <!-- 由JS动态生成 -->
                            </ul>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
// Include bulk import modal
include __DIR__ . '/../common/_bulkImport.php';
?>

<script>
    // 显示Toast消息
    document.addEventListener('DOMContentLoaded', function() {
        <?php if ($toastMessage): ?>
            if (window.AdminCommon && window.AdminCommon.showToast) {
                window.AdminCommon.showToast('<?= addslashes($toastMessage) ?>', '<?= addslashes($toastType ?? 'info') ?>');
            }
        <?php endif; ?>
    });
</script>
