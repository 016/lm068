<?php
$pageTitle = '标签管理 - 视频分享网站管理后台';
$cssFiles = [
    '/assets/css/main_3.css',
    '/assets/css/tag_list_8.css'
];
$jsFiles = [
    '/assets/js/main_7.js', 
    '/assets/js/tag_list_11.js'
];
?>

<!DOCTYPE html>
<html lang="zh-CN" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <?php foreach ($cssFiles as $cssFile): ?>
        <link rel="stylesheet" href="<?= htmlspecialchars($cssFile) ?>">
    <?php endforeach; ?>
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <nav class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="/backend" class="logo">
                    <span class="logo-icon">📺</span>
                    <span class="logo-text">VideoHub Admin</span>
                </a>
            </div>

            <div class="nav-menu">
                <div class="nav-section">
                    <a href="/backend" class="nav-item">
                        <i class="bi bi-grid nav-icon"></i>
                        <span class="nav-text">仪表板</span>
                        <span class="tooltip">仪表板</span>
                    </a>
                    <a href="/backend/content" class="nav-item">
                        <i class="bi bi-camera-video nav-icon"></i>
                        <span class="nav-text">视频管理</span>
                        <span class="tooltip">视频管理</span>
                    </a>
                    <a href="/backend/tags" class="nav-item active">
                        <i class="bi bi-tags nav-icon"></i>
                        <span class="nav-text">标签管理</span>
                        <span class="tooltip">标签管理</span>
                    </a>
                    <a href="/backend/users" class="nav-item">
                        <i class="bi bi-people nav-icon"></i>
                        <span class="nav-text">用户管理</span>
                        <span class="tooltip">用户管理</span>
                    </a>
                    <a href="/backend/comments" class="nav-item">
                        <i class="bi bi-chat-left nav-icon"></i>
                        <span class="nav-text">评论管理</span>
                        <span class="tooltip">评论管理</span>
                    </a>
                    <a href="/backend/analytics" class="nav-item">
                        <i class="bi bi-bar-chart nav-icon"></i>
                        <span class="nav-text">数据分析</span>
                        <span class="tooltip">数据分析</span>
                    </a>
                    <a href="/backend/subscriptions" class="nav-item">
                        <i class="bi bi-envelope nav-icon"></i>
                        <span class="nav-text">订阅管理</span>
                        <span class="tooltip">订阅管理</span>
                    </a>
                    <a href="/backend/moderation" class="nav-item">
                        <i class="bi bi-shield-check nav-icon"></i>
                        <span class="nav-text">内容审核</span>
                        <span class="tooltip">内容审核</span>
                    </a>
                    <a href="/backend/settings" class="nav-item">
                        <i class="bi bi-gear nav-icon"></i>
                        <span class="nav-text">系统设置</span>
                        <span class="tooltip">系统设置</span>
                    </a>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <header class="header">
                <div class="header-left">
                    <button class="toggle-sidebar" id="toggleSidebar">
                        <i class="bi bi-list sidebar-toggle-icon"></i>
                    </button>

                    <!-- Navigation Tabs -->
                    <nav class="topbar-nav">
                        <a href="/backend/content" class="nav-tab">视频</a>
                        <a href="/backend/tags" class="nav-tab active">标签</a>
                        <a href="/backend/collections" class="nav-tab">合集</a>
                        <a href="/backend/users" class="nav-tab">用户</a>
                    </nav>
                </div>

                <div class="header-actions">
                    <!-- Search Box -->
                    <div class="header-search">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" class="search-input" placeholder="搜索标签、关联视频..." 
                               value="<?= htmlspecialchars($search ?? '') ?>">
                    </div>

                    <div class="dropdown-container">
                        <button class="header-btn" id="notificationBtn">
                            <i class="bi bi-bell header-icon"></i>
                            <span class="notification-badge"></span>
                        </button>
                        <div class="dropdown-menu" id="notificationDropdown">
                            <div class="dropdown-header">
                                <h6>通知中心</h6>
                                <button class="mark-all-read">全部标记已读</button>
                            </div>
                            <div class="dropdown-body">
                                <div class="notification-item">
                                    <div class="notification-icon new-user">
                                        <i class="bi bi-tag notification-icon-style"></i>
                                    </div>
                                    <div class="notification-content">
                                        <div class="notification-title">新标签创建</div>
                                        <div class="notification-text">用户创建了"科技前沿"标签</div>
                                        <div class="notification-time">5分钟前</div>
                                    </div>
                                </div>
                            </div>
                            <div class="dropdown-footer">
                                <button class="view-all-btn">查看全部通知</button>
                            </div>
                        </div>
                    </div>

                    <div class="dropdown-container">
                        <button class="header-btn" id="userBtn">
                            <i class="bi bi-person header-icon"></i>
                        </button>
                        <div class="dropdown-menu" id="userDropdown">
                            <div class="dropdown-body">
                                <div class="user-info">
                                    <div class="user-avatar">
                                        <i class="bi bi-person" style="font-size: 24px;"></i>
                                    </div>
                                    <div class="user-details">
                                        <div class="user-name">管理员</div>
                                        <div class="user-email">admin@videohub.com</div>
                                    </div>
                                </div>
                                <div class="dropdown-item">
                                    <i class="bi bi-person notification-icon-style"></i>
                                    个人资料
                                </div>
                                <div class="dropdown-item">
                                    <i class="bi bi-gear notification-icon-style"></i>
                                    账户设置
                                </div>
                                <div class="dropdown-divider"></div>
                                <div class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right notification-icon-style"></i>
                                    退出登录
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Theme Toggle Button -->
                    <div class="dropdown-container">
                        <button class="theme-toggle-btn" id="themeToggleBtn" title="切换主题">
                            <i class="bi bi-sun theme-icon theme-toggle-icon" id="themeIcon"></i>
                        </button>
                        <div class="dropdown-menu" id="themeDropdown">
                            <div class="dropdown-body">
                                <div class="theme-option active" data-theme="light">
                                    <i class="bi bi-sun notification-icon-style"></i>
                                    <span>浅色模式</span>
                                    <i class="bi bi-check check-icon notification-icon-style"></i>
                                </div>
                                <div class="theme-option" data-theme="dark">
                                    <i class="bi bi-moon notification-icon-style"></i>
                                    <span>深色模式</span>
                                    <i class="bi bi-check check-icon notification-icon-style"></i>
                                </div>
                                <div class="theme-option" data-theme="auto">
                                    <i class="bi bi-display notification-icon-style"></i>
                                    <span>跟随系统</span>
                                    <i class="bi bi-check check-icon notification-icon-style"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

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
                            <li class="breadcrumb-item"><a href="/backend" class="breadcrumb-link">首页</a></li>
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
                                <div class="metric-value-small"><?= number_format($stats['total_tags'] ?? 0) ?></div>
                                <div class="monthly-stats">
                                    <i class="bi bi-arrow-up monthly-stats-icon"></i>
                                    <span>本月新增: <?= number_format(($stats['total_tags'] ?? 0) > 10 ? 15 : 0) ?></span>
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
                                <div class="metric-value-small"><?= number_format($stats['active_tags'] ?? 0) ?></div>
                                <div class="monthly-stats">
                                    <i class="bi bi-check-circle monthly-stats-icon"></i>
                                    <span>本月启用: <?= number_format(($stats['active_tags'] ?? 0) > 8 ? 12 : 0) ?></span>
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
                                <div class="metric-value-small"><?= number_format($stats['inactive_tags'] ?? 0) ?></div>
                                <div class="monthly-stats">
                                    <i class="bi bi-x-circle monthly-stats-icon"></i>
                                    <span>本月禁用: <?= number_format(($stats['inactive_tags'] ?? 0) > 1 ? 3 : 0) ?></span>
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
                                <div class="metric-value-small"><?= $stats['total_content_associations'] > 1000 ? number_format($stats['total_content_associations'] / 1000, 1) . 'K' : number_format($stats['total_content_associations'] ?? 0) ?></div>
                                <div class="monthly-stats">
                                    <i class="bi bi-graph-up monthly-stats-icon"></i>
                                    <span>本月增长: <?= ($stats['total_content_associations'] ?? 0) > 1000 ? '50K' : '0' ?></span>
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
                                <a href="/backend/tags/create" class="btn btn-primary d-flex align-items-center gap-2">
                                    <i class="bi bi-plus-lg"></i>
                                    创建新标签
                                </a>
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
                                                标签名称
                                                <div class="sort-icons-container">
                                                    <i class="bi bi-caret-up-fill sort-icon" data-sort="name" data-direction="asc"></i>
                                                    <i class="bi bi-caret-down-fill sort-icon sort-icon-up" data-sort="name" data-direction="desc"></i>
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
                                        <th class="table-cell sortable-header" data-column="status">
                                            <div class="d-flex align-items-center">
                                                状态
                                                <div class="sort-icons-container">
                                                    <i class="bi bi-caret-up-fill sort-icon" data-sort="status" data-direction="asc"></i>
                                                    <i class="bi bi-caret-down-fill sort-icon sort-icon-up" data-sort="status" data-direction="desc"></i>
                                                </div>
                                            </div>
                                        </th>
                                        <th class="table-cell table-actions" style="width: 120px;" data-column="actions">操作</th>
                                    </tr>
                                    <tr class="table-header-bg">
                                        <th class="table-filter-cell" data-column="checkbox"></th>
                                        <th class="table-filter-cell" data-column="id">
                                            <input type="text" class="form-control form-control-sm" placeholder="搜索ID">
                                        </th>
                                        <th class="table-filter-cell" data-column="name">
                                            <input type="text" class="form-control form-control-sm" placeholder="搜索标签名">
                                        </th>
                                        <th class="table-filter-cell" data-column="content_cnt">
                                            <input type="text" class="form-control form-control-sm" placeholder="数量范围">
                                        </th>
                                        <th class="table-filter-cell" data-column="icon_class">
                                            <input type="text" class="form-control form-control-sm" placeholder="搜索icon">
                                        </th>
                                        <th class="table-filter-cell" data-column="status">
                                            <select class="form-control form-select form-select-sm">
                                                <option value="">全部状态</option>
                                                <option value="1" <?= ($statusFilter === '1') ? 'selected' : '' ?>>显示</option>
                                                <option value="0" <?= ($statusFilter === '0') ? 'selected' : '' ?>>隐藏</option>
                                            </select>
                                        </th>
                                        <th class="table-filter-cell" data-column="actions"></th>
                                    </tr>
                                </thead>
                                <tbody id="tagTableBody">
                                    <?php if (!empty($tags)): ?>
                                        <?php foreach ($tags as $tag): ?>
                                            <tr class="table-row" data-id="<?= htmlspecialchars($tag['id']) ?>">
                                                <td class="table-cell">
                                                    <div class="form-check">
                                                        <input class="form-check-input row-checkbox" type="checkbox" value="<?= htmlspecialchars($tag['id']) ?>">
                                                    </div>
                                                </td>
                                                <td class="table-cell table-id" data-column="id"><?= htmlspecialchars($tag['id']) ?></td>
                                                <td class="table-cell table-name" data-column="name"><?= htmlspecialchars($tag['name_cn'] ?: $tag['name_en']) ?></td>
                                                <td class="table-cell" data-column="content_cnt">
                                                    <a href="/backend/content?tag_id=<?= htmlspecialchars($tag['id']) ?>" class="content-link">
                                                        <?= number_format($tag['content_cnt'] ?? 0) ?>
                                                    </a>
                                                </td>
                                                <td class="table-cell" data-column="icon_class">
                                                    <div class="icon-class-display">
                                                        <span class="bi <?= htmlspecialchars($tag['icon_class'] ?: 'bi-tag') ?>"></span> 
                                                        <?= htmlspecialchars($tag['icon_class'] ?: 'bi-tag') ?>
                                                    </div>
                                                </td>
                                                <td class="table-cell" data-column="status">
                                                    <span class="badge rounded-pill <?= $tag['status_id'] ? 'badge-success' : 'badge-danger' ?>">
                                                        <i class="bi bi-circle-fill badge-icon"></i> 
                                                        <?= $tag['status_id'] ? '显示' : '隐藏' ?>
                                                    </span>
                                                </td>
                                                <td class="table-cell table-actions" data-column="actions">
                                                    <div class="d-flex gap-2 justify-content-center">
                                                        <a href="/backend/tags/<?= htmlspecialchars($tag['id']) ?>/edit" class="btn btn-outline-primary btn-sm" title="编辑">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <a href="/backend/tags/<?= htmlspecialchars($tag['id']) ?>" class="btn btn-outline-info btn-sm" title="查看">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <button class="btn btn-outline-danger btn-sm delete-tag" title="删除" data-id="<?= htmlspecialchars($tag['id']) ?>">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <div class="empty-state">
                                                    <i class="bi bi-inbox" style="font-size: 3rem; color: #6c757d;"></i>
                                                    <p class="mt-2 text-muted">暂无标签数据</p>
                                                    <a href="/backend/tags/create" class="btn btn-primary">创建第一个标签</a>
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
                                        📊 <strong>汇总信息:</strong> 当前显示 
                                        <span class="summary-highlight" id="currentDisplay">
                                            <?= ($page - 1) * $perPage + 1 ?>-<?= min($page * $perPage, $totalCount) ?>/<?= number_format($totalCount) ?>
                                        </span> 条 |
                                        关联视频总计: <span class="summary-highlight"><?= number_format($stats['total_content_associations'] ?? 0) ?></span> 个 |
                                        平均每标签: <span class="summary-highlight"><?= $stats['total_tags'] > 0 ? number_format(($stats['total_content_associations'] ?? 0) / $stats['total_tags'], 1) : '0' ?></span> 个
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <nav aria-label="Page navigation">
                                    <div class="d-flex justify-content-end align-items-center gap-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="pagination-text">每页</span>
                                            <select class="form-select form-select-sm" id="itemsPerPage" style="width: auto;">
                                                <option value="5" <?= $perPage == 5 ? 'selected' : '' ?>>5</option>
                                                <option value="10" <?= $perPage == 10 ? 'selected' : '' ?>>10</option>
                                                <option value="20" <?= $perPage == 20 ? 'selected' : '' ?>>20</option>
                                                <option value="50" <?= $perPage == 50 ? 'selected' : '' ?>>50</option>
                                                <option value="100" <?= $perPage == 100 ? 'selected' : '' ?>>100</option>
                                            </select>
                                            <span class="pagination-text">条</span>
                                        </div>
                                        <?php if ($totalPages > 1): ?>
                                        <ul class="pagination pagination-sm mb-0" id="paginationNav">
                                            <?php if ($page > 1): ?>
                                                <li class="page-item">
                                                    <a class="page-link" href="?page=<?= $page - 1 ?>&per_page=<?= $perPage ?><?= $search ? '&search=' . urlencode($search) : '' ?><?= $statusFilter !== null ? '&status=' . urlencode($statusFilter) : '' ?>">
                                                        <i class="bi bi-chevron-left"></i>
                                                    </a>
                                                </li>
                                            <?php endif; ?>
                                            
                                            <?php
                                            $startPage = max(1, $page - 2);
                                            $endPage = min($totalPages, $page + 2);
                                            ?>
                                            
                                            <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                                    <a class="page-link" href="?page=<?= $i ?>&per_page=<?= $perPage ?><?= $search ? '&search=' . urlencode($search) : '' ?><?= $statusFilter !== null ? '&status=' . urlencode($statusFilter) : '' ?>">
                                                        <?= $i ?>
                                                    </a>
                                                </li>
                                            <?php endfor; ?>
                                            
                                            <?php if ($page < $totalPages): ?>
                                                <li class="page-item">
                                                    <a class="page-link" href="?page=<?= $page + 1 ?>&per_page=<?= $perPage ?><?= $search ? '&search=' . urlencode($search) : '' ?><?= $statusFilter !== null ? '&status=' . urlencode($statusFilter) : '' ?>">
                                                        <i class="bi bi-chevron-right"></i>
                                                    </a>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                        <?php endif; ?>
                                    </div>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <!-- Footer -->
            <footer class="footer">
                <div class="d-flex justify-content-between align-items-center">
                    <div>© 2024 VideoHub | 最后更新: 2分钟前</div>
                    <div>在线管理员: <span class="summary-highlight">3人</span></div>
                    <div>系统状态: <span style="color: var(--success);">🟢正常</span></div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Mobile Menu Overlay -->
    <div class="mobile-overlay" id="mobileOverlay"></div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

    <?php foreach ($jsFiles as $jsFile): ?>
        <script src="<?= htmlspecialchars($jsFile) ?>"></script>
    <?php endforeach; ?>

    <script>
        // 页面特定的JavaScript配置
        window.TagListConfig = {
            currentPage: <?= $page ?>,
            perPage: <?= $perPage ?>,
            totalCount: <?= $totalCount ?>,
            totalPages: <?= $totalPages ?>,
            baseUrl: '/backend/tags',
            apiUrls: {
                bulkAction: '/backend/tags/bulk-action',
                export: '/backend/tags/export',
                delete: '/backend/tags/{id}'
            }
        };

        // 删除标签功能
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.delete-tag').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const tagId = this.dataset.id;
                    if (confirm('确定要删除这个标签吗？此操作不可恢复。')) {
                        fetch(`/backend/tags/${tagId}`, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            } else {
                                alert(data.message || '删除失败');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('操作失败，请重试');
                        });
                    }
                });
            });
        });

        // 导出数据功能
        function exportData(format) {
            window.location.href = `/backend/tags/export?format=${format}`;
        }
    </script>
</body>
</html>