<!DOCTYPE html>
<html lang="zh-CN" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? '内容分享网站 - 管理后台') ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="/assets/lib/bootstrap-5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="/assets/lib/bootstrap-icons-1.13.1/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Main CSS - Common Styles -->
    <link rel="stylesheet" href="/assets/css/main_3.css">
    
    <?php if (isset($css_files)): ?>
        <?php foreach ($css_files as $css_file): ?>
            <link rel="stylesheet" href="/assets/css/<?= $css_file ?>">
        <?php endforeach; ?>
    <?php endif; ?>

</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <nav class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="#" class="logo">
                    <span class="logo-icon">📹</span>
                    <span class="logo-text">内容管理</span>
                </a>
            </div>
            
            <div class="nav-menu">
                <div class="nav-section">
                    <a href="/dashboard" class="nav-item <?= strpos($_SERVER['REQUEST_URI'], '/dashboard') !== false || $_SERVER['REQUEST_URI'] === '/' ? 'active' : '' ?>">
                        <i class="bi bi-grid nav-icon"></i>
                        <span class="nav-text">仪表板</span>
                        <span class="tooltip">仪表板</span>
                    </a>
                    <a href="/content" class="nav-item <?= strpos($_SERVER['REQUEST_URI'], '/content') !== false ? 'active' : '' ?>">
                        <i class="bi bi-camera-video nav-icon"></i>
                        <span class="nav-text">内容管理</span>
                        <span class="tooltip">内容管理</span>
                    </a>
                    <a href="/tags" class="nav-item <?= strpos($_SERVER['REQUEST_URI'], '/tags') !== false ? 'active' : '' ?>">
                        <i class="bi bi-bookmark nav-icon"></i>
                        <span class="nav-text">标签管理</span>
                        <span class="tooltip">标签管理</span>
                    </a>
                    <a href="/collections" class="nav-item <?= strpos($_SERVER['REQUEST_URI'], '/collections') !== false ? 'active' : '' ?>">
                        <i class="bi bi-card-list nav-icon"></i>
                        <span class="nav-text">合集管理</span>
                        <span class="tooltip">合集管理</span>
                    </a>
                    <a href="/users" class="nav-item <?= strpos($_SERVER['REQUEST_URI'], '/users') !== false ? 'active' : '' ?>">
                        <i class="bi bi-people nav-icon"></i>
                        <span class="nav-text">用户管理</span>
                        <span class="tooltip">用户管理</span>
                    </a>
                    <a href="#" class="nav-item">
                        <i class="bi bi-chat-left nav-icon"></i>
                        <span class="nav-text">评论管理</span>
                        <span class="tooltip">评论管理</span>
                    </a>
                    <a href="#" class="nav-item">
                        <i class="bi bi-bar-chart nav-icon"></i>
                        <span class="nav-text">数据分析</span>
                        <span class="tooltip">数据分析</span>
                    </a>
                    <a href="#" class="nav-item">
                        <i class="bi bi-envelope nav-icon"></i>
                        <span class="nav-text">订阅管理</span>
                        <span class="tooltip">订阅管理</span>
                    </a>
                    <a href="#" class="nav-item">
                        <i class="bi bi-shield-check nav-icon"></i>
                        <span class="nav-text">内容审核</span>
                        <span class="tooltip">内容审核</span>
                    </a>
                    <a href="#" class="nav-item">
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
                        <i class="bi bi-list" style="font-size: 20px;"></i>
                    </button>
                    
                    <!-- Navigation Tabs -->
                    <nav class="topbar-nav">
                        <a href="/content" class="nav-tab <?= strpos($_SERVER['REQUEST_URI'], '/content') !== false ? 'active' : '' ?>">内容</a>
                        <a href="/tags" class="nav-tab <?= strpos($_SERVER['REQUEST_URI'], '/tags') !== false ? 'active' : '' ?>">标签</a>
                        <a href="/collections" class="nav-tab <?= strpos($_SERVER['REQUEST_URI'], '/collections') !== false ? 'active' : '' ?>">合集</a>
                        <a href="/users" class="nav-tab <?= strpos($_SERVER['REQUEST_URI'], '/users') !== false ? 'active' : '' ?>">用户</a>
                    </nav>
                </div>
                
                <div class="header-actions">
                    <!-- Search Box - Moved here as first element -->
                    <div class="header-search">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" class="search-input" placeholder="搜索用户、内容、评论...">
                    </div>
                    
                    <div class="dropdown-container">
                        <button class="header-btn" id="notificationBtn">
                            <i class="bi bi-bell" style="font-size: 20px;"></i>
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
                                        <i class="bi bi-person-plus" style="font-size: 16px;"></i>
                                    </div>
                                    <div class="notification-content">
                                        <div class="notification-title">新用户注册</div>
                                        <div class="notification-text">张小明刚刚注册了账户</div>
                                        <div class="notification-time">2分钟前</div>
                                    </div>
                                </div>
                                <div class="notification-item">
                                    <div class="notification-icon system">
                                        <i class="bi bi-gear" style="font-size: 16px;"></i>
                                    </div>
                                    <div class="notification-content">
                                        <div class="notification-title">系统更新</div>
                                        <div class="notification-text">内容转码模块已更新至v2.1</div>
                                        <div class="notification-time">1小时前</div>
                                    </div>
                                </div>
                                <div class="notification-item">
                                    <div class="notification-icon warning">
                                        <i class="bi bi-exclamation-triangle" style="font-size: 16px;"></i>
                                    </div>
                                    <div class="notification-content">
                                        <div class="notification-title">存储警告</div>
                                        <div class="notification-text">存储空间使用率达到78%</div>
                                        <div class="notification-time">3小时前</div>
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
                            <i class="bi bi-person" style="font-size: 20px;"></i>
                        </button>
                        <div class="dropdown-menu" id="userDropdown">
                            <div class="dropdown-body">
                                <div class="user-info">
                                    <div class="user-avatar">
                                        <i class="bi bi-person" style="font-size: 24px;"></i>
                                    </div>
                                    <div class="user-details">
                                        <div class="user-name"><?= htmlspecialchars($_SESSION['admin_real_name'] ?? $_SESSION['admin_username'] ?? '管理员') ?></div>
                                        <div class="user-email"><?= htmlspecialchars($_SESSION['admin_email'] ?? 'admin@example.com') ?></div>
                                    </div>
                                </div>
                                <div class="dropdown-item">
                                    <i class="bi bi-person" style="font-size: 16px;"></i>
                                    个人资料
                                </div>
                                <div class="dropdown-item">
                                    <i class="bi bi-gear" style="font-size: 16px;"></i>
                                    账户设置
                                </div>
                                <div class="dropdown-divider"></div>
                                <div class="dropdown-item text-danger">
                                    <a href="/logout" class="text-decoration-none text-danger">
                                        <i class="bi bi-box-arrow-right" style="font-size: 16px;"></i>
                                        退出登录
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- FIXED: Theme Toggle Button - Icon Only -->
                    <div class="dropdown-container">
                        <button class="theme-toggle-btn" id="themeToggleBtn" title="切换主题">
                            <i class="bi bi-sun theme-icon" id="themeIcon" style="font-size: 18px;"></i>
                        </button>
                        <div class="dropdown-menu" id="themeDropdown">
                            <div class="dropdown-body">
                                <div class="theme-option active" data-theme="light">
                                    <i class="bi bi-sun" style="font-size: 16px;"></i>
                                    <span>浅色模式</span>
                                    <i class="bi bi-check check-icon" style="font-size: 16px;"></i>
                                </div>
                                <div class="theme-option" data-theme="dark">
                                    <i class="bi bi-moon" style="font-size: 16px;"></i>
                                    <span>深色模式</span>
                                    <i class="bi bi-check check-icon" style="font-size: 16px;"></i>
                                </div>
                                <div class="theme-option" data-theme="auto">
                                    <i class="bi bi-display" style="font-size: 16px;"></i>
                                    <span>跟随系统</span>
                                    <i class="bi bi-check check-icon" style="font-size: 16px;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <?php
            // 显示Flash消息
            if (!isset($_SESSION)) {
                session_start();
            }
            
            if (isset($_SESSION['flash_message'])):
                $flash = $_SESSION['flash_message'];
                unset($_SESSION['flash_message']);
                $alertClass = match($flash['type']) {
                    'success' => 'alert-success',
                    'error' => 'alert-danger',
                    'warning' => 'alert-warning',
                    default => 'alert-info'
                };
            ?>
                <div class="container-fluid mt-3">
                    <div class="alert <?= $alertClass ?> alert-dismissible fade show" role="alert">
                        <i class="bi bi-<?= $flash['type'] === 'success' ? 'check-circle' : ($flash['type'] === 'error' ? 'exclamation-triangle' : 'info-circle') ?>"></i>
                        <?= htmlspecialchars($flash['message']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            <?php endif; ?>

            <?= $content ?? '' ?>

            <!-- Footer -->
            <footer class="footer">
                <div class="d-flex justify-content-between align-items-center">
                    <div>状态: <span style="color: var(--success);">🟢 所有系统正常运行</span></div>
                    <div>© 2024 DP内容分享平台 v1.0</div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Mobile Menu Overlay -->
    <div class="mobile-overlay" id="mobileOverlay"></div>

    <!-- Bootstrap 5 JS -->
    <script src="/assets/lib/bootstrap-5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Chart.js - FIXED: Use CDN without module import issue -->
    <script src="/assets/lib/chart.js-4.4.0/dist/chart.umd.js"></script>
    
    <!-- Main JS - Common Functions -->
    <script src="/assets/js/main_12.js"></script>
    
    <?php if (isset($js_files)): ?>
        <?php foreach ($js_files as $js_file): ?>
            <script src="/assets/js/<?= $js_file ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>

</body>
</html>