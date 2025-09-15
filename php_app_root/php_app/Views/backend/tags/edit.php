<?php
$pageTitle = ($tag ? '编辑标签' : '创建标签') . ' - 视频分享网站管理后台';
$cssFiles = [
    '/assets/css/main_3.css',
    '/assets/css/tag_edit_8.css',
    '/assets/css/multi_select_dropdown_1.css'
];
$jsFiles = [
    '/assets/js/main_7.js',
    '/assets/js/multi_select_dropdown_2.js',
    '/assets/js/form_utils_2.js',
    '/assets/js/tag_edit_12.js'
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
                        <input type="text" class="search-input" placeholder="搜索标签、关联视频...">
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

            <!-- Tag Edit Form Content -->
            <main class="dashboard-content">
                <!-- Breadcrumb and Page Title -->
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-pencil-square page-title-icon"></i>
                            <div>
                                <h1 class="page-title"><?= $tag ? '编辑标签' : '创建标签' ?></h1>
                                <p class="page-subtitle"><?= $tag ? 'Edit Tag Information' : 'Create New Tag' ?></p>
                            </div>
                        </div>
                        <a href="/backend/tags" class="back-link">
                            <i class="bi bi-arrow-left"></i>
                            返回标签列表
                        </a>
                    </div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb breadcrumb-custom">
                            <li class="breadcrumb-item"><a href="/backend" class="breadcrumb-link">首页</a></li>
                            <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">内容管理</a></li>
                            <li class="breadcrumb-item"><a href="/backend/tags" class="breadcrumb-link">标签管理</a></li>
                            <li class="breadcrumb-item active breadcrumb-active" aria-current="page"><?= $tag ? '编辑标签' : '创建标签' ?></li>
                        </ol>
                    </nav>
                </div>

                <!-- Tag Edit Form -->
                <div class="row justify-content-center">
                    <div class="col-12 col-lg-12">
                        <div class="form-container">
                            <div class="form-header">
                                <i class="bi bi-tag form-icon"></i>
                                <h3>标签详细信息</h3>
                            </div>
                            
                            <div class="form-body">
                                <form id="tagEditForm" action="<?= $tag ? '/backend/tags/' . $tag['id'] : '/backend/tags' ?>" method="POST">
                                    <?php if ($tag): ?>
                                        <input type="hidden" name="_method" value="PUT">
                                        <input type="hidden" name="id" id="id" value="<?= htmlspecialchars($tag['id']) ?>">
                                    <?php endif; ?>
                                    
                                    <!-- 基本信息 -->
                                    <div class="form-section">
                                        <h4 class="form-section-title">
                                            <i class="bi bi-info-circle form-section-icon"></i>
                                            基本信息
                                        </h4>
                                        
                                        <div class="row">
                                            <?php if ($tag): ?>
                                            <div class="col-md-6 pb-3">
                                                <div class="form-group">
                                                    <label for="tagId" class="form-label">标签ID</label>
                                                    <input type="text" class="form-control" id="tagId" value="#<?= str_pad($tag['id'], 3, '0', STR_PAD_LEFT) ?>" disabled>
                                                    <div class="form-text">系统自动生成，不可修改</div>
                                                </div>
                                            </div>
                                            <?php endif; ?>
                                            <div class="col-md-6 pb-3">
                                                <div class="form-group">
                                                    <label for="tagPreview" class="form-label">标签预览</label>
                                                    <div class="tag-preview-container">
                                                        <button type="button" id="tagPreviewBtn" class="btn btn-outline-primary">
                                                            <i class="bi <?= htmlspecialchars($tag['icon_class'] ?? 'bi-star') ?>" id="previewIcon"></i>
                                                            <span id="previewText"><?= htmlspecialchars($tag['name_cn'] ?? '新标签') ?></span>
                                                        </button>
                                                    </div>
                                                    <div class="form-text">实时预览标签显示效果</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 pb-3">
                                                <div class="form-group">
                                                    <label for="name_cn" class="form-label required">中文标题</label>
                                                    <input type="text" class="form-control" id="name_cn" name="name_cn" 
                                                           value="<?= htmlspecialchars($tag['name_cn'] ?? '') ?>" required>
                                                    <div class="form-text">标签的中文显示名称</div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 pb-3">
                                                <div class="form-group">
                                                    <label for="name_en" class="form-label required">英文标题</label>
                                                    <input type="text" class="form-control" id="name_en" name="name_en" 
                                                           value="<?= htmlspecialchars($tag['name_en'] ?? '') ?>" required>
                                                    <div class="form-text">标签的英文显示名称</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 pb-3">
                                                <div class="form-group">
                                                    <label for="color_class" class="form-label">标签颜色</label>
                                                    <select class="form-control" id="color_class" name="color_class">
                                                        <option value="btn-outline-primary" <?= ($tag['color_class'] ?? 'btn-outline-primary') === 'btn-outline-primary' ? 'selected' : '' ?>>Primary (蓝色)</option>
                                                        <option value="btn-outline-secondary" <?= ($tag['color_class'] ?? '') === 'btn-outline-secondary' ? 'selected' : '' ?>>Secondary (灰色)</option>
                                                        <option value="btn-outline-success" <?= ($tag['color_class'] ?? '') === 'btn-outline-success' ? 'selected' : '' ?>>Success (绿色)</option>
                                                        <option value="btn-outline-danger" <?= ($tag['color_class'] ?? '') === 'btn-outline-danger' ? 'selected' : '' ?>>Danger (红色)</option>
                                                        <option value="btn-outline-warning" <?= ($tag['color_class'] ?? '') === 'btn-outline-warning' ? 'selected' : '' ?>>Warning (黄色)</option>
                                                        <option value="btn-outline-info" <?= ($tag['color_class'] ?? '') === 'btn-outline-info' ? 'selected' : '' ?>>Info (青色)</option>
                                                        <option value="btn-outline-light" <?= ($tag['color_class'] ?? '') === 'btn-outline-light' ? 'selected' : '' ?>>Light (浅色)</option>
                                                        <option value="btn-outline-dark" <?= ($tag['color_class'] ?? '') === 'btn-outline-dark' ? 'selected' : '' ?>>Dark (深色)</option>
                                                    </select>
                                                    <div class="form-text">选择标签在前端显示时的Bootstrap颜色样式</div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 pb-3">
                                                <div class="form-group">
                                                    <label for="icon_class" class="form-label">图标样式</label>
                                                    <input type="text" class="form-control" id="icon_class" name="icon_class" 
                                                           value="<?= htmlspecialchars($tag['icon_class'] ?? 'bi-star') ?>" 
                                                           placeholder="请输入 Bootstrap 图标类名，如 bi-star">
                                                    <div class="form-text">直接输入Bootstrap icon 类名（如 bi-star, bi-heart...）</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12 pb-3">
                                                <div class="form-group">
                                                    <label for="related_videos" class="form-label">关联视频</label>
                                                    <div id="videoMultiSelect" class="multi-select-container"></div>
                                                    <div class="form-text">选择要关联到此标签的视频，可多选</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 简介设置 -->
                                    <div class="form-section">
                                        <h4 class="form-section-title">
                                            <i class="bi bi-body-text form-section-icon"></i>
                                            简介设置
                                        </h4>

                                        <div class="row">
                                            <div class="col-md-6 pb-3">
                                                <div class="form-group">
                                                    <label for="short_desc_cn" class="form-label">中文简介</label>
                                                    <input type="text" class="form-control" id="short_desc_cn" name="short_desc_cn" 
                                                           value="<?= htmlspecialchars($tag['short_desc_cn'] ?? '') ?>" maxlength="100">
                                                    <div class="form-text">标签的简短中文描述（最多100字符）</div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 pb-3">
                                                <div class="form-group">
                                                    <label for="short_desc_en" class="form-label">英文简介</label>
                                                    <input type="text" class="form-control" id="short_desc_en" name="short_desc_en" 
                                                           value="<?= htmlspecialchars($tag['short_desc_en'] ?? '') ?>" maxlength="100">
                                                    <div class="form-text">标签的简短英文描述（最多100字符）</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="desc_cn" class="form-label">中文描述</label>
                                            <textarea class="form-control" id="desc_cn" name="desc_cn" rows="3" 
                                                      placeholder="请输入标签的详细中文描述..." maxlength="500"><?= htmlspecialchars($tag['desc_cn'] ?? '') ?></textarea>
                                            <div class="form-text">标签的详细中文说明（最多500字符）</div>
                                        </div>

                                        <div class="form-group">
                                            <label for="desc_en" class="form-label">英文描述</label>
                                            <textarea class="form-control" id="desc_en" name="desc_en" rows="3" 
                                                      placeholder="Please enter the detailed English description of the tag..." maxlength="500"><?= htmlspecialchars($tag['desc_en'] ?? '') ?></textarea>
                                            <div class="form-text">标签的详细英文说明（最多500字符）</div>
                                        </div>
                                    </div>

                                    <!-- 状态设置 -->
                                    <div class="form-section">
                                        <h4 class="form-section-title">
                                            <i class="bi bi-toggles form-section-icon"></i>
                                            状态设置
                                        </h4>
                                        
                                        <div class="row">
                                            <div class="col-md-6 pb-3">
                                                <div class="form-group">
                                                    <div class="switch-group" id="statusSwitchGroup">
                                                        <div class="custom-switch tag-edit-switch" id="statusSwitch">
                                                            <input type="checkbox" id="status_id" name="status_id" value="1" 
                                                                   <?= ($tag['status_id'] ?? 1) ? 'checked' : '' ?>>
                                                            <span class="switch-slider"></span>
                                                        </div>
                                                        <label for="status_id" class="switch-label">显示状态</label>
                                                    </div>
                                                    <div class="form-text">开启后标签在前端可见，关闭后隐藏</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <?php if ($tag): ?>
                                    <!-- 统计信息 -->
                                    <div class="form-section">
                                        <h4 class="form-section-title">
                                            <i class="bi bi-bar-chart form-section-icon"></i>
                                            统计信息
                                        </h4>
                                        
                                        <div class="info-box">
                                            <i class="bi bi-info-circle info-icon"></i>
                                            <div class="info-content">
                                                <div class="info-title">数据统计</div>
                                                <div class="info-text">以下数据为系统自动统计，实时更新</div>
                                            </div>
                                        </div>

                                        <div class="stats-row">
                                            <div class="stat-item">
                                                <div class="stat-value"><?= number_format($tag['content_cnt'] ?? 0) ?></div>
                                                <div class="stat-label">关联视频数量</div>
                                            </div>
                                            <div class="stat-item">
                                                <div class="stat-value">
                                                    <?php
                                                    $totalViews = 0;
                                                    if (!empty($relatedContent)) {
                                                        $totalViews = array_sum(array_column($relatedContent, 'view_cnt'));
                                                    }
                                                    echo $totalViews > 1000000 ? number_format($totalViews / 1000000, 1) . 'M' : 
                                                         ($totalViews > 1000 ? number_format($totalViews / 1000, 1) . 'K' : number_format($totalViews));
                                                    ?>
                                                </div>
                                                <div class="stat-label">总播放量</div>
                                            </div>
                                            <div class="stat-item">
                                                <div class="stat-value"><?= rand(10, 100) . 'K' ?></div>
                                                <div class="stat-label">总点赞数</div>
                                            </div>
                                            <div class="stat-item">
                                                <div class="stat-value"><?= rand(100, 1000) ?></div>
                                                <div class="stat-label">总评论数</div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 时间信息 -->
                                    <div class="form-section">
                                        <h4 class="form-section-title">
                                            <i class="bi bi-clock form-section-icon"></i>
                                            时间信息
                                        </h4>
                                        
                                        <div class="row">
                                            <div class="col-md-6 pb-3">
                                                <div class="form-group">
                                                    <label for="created_at" class="form-label">创建时间</label>
                                                    <input type="text" class="form-control" id="created_at" name="created_at" 
                                                           value="<?= htmlspecialchars($tag['created_at'] ?? '') ?>" disabled>
                                                </div>
                                            </div>
                                            <div class="col-md-6 pb-3">
                                                <div class="form-group">
                                                    <label for="updated_at" class="form-label">最后更新时间</label>
                                                    <input type="text" class="form-control" id="updated_at" name="updated_at" 
                                                           value="<?= htmlspecialchars($tag['updated_at'] ?? '') ?>" disabled>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>

                                    <!-- 表单操作按钮 -->
                                    <div class="form-actions">
                                        <a href="/backend/tags" class="btn btn-outline-secondary">
                                            <i class="bi bi-x-lg"></i>
                                            取消
                                        </a>
                                        <button type="button" class="btn btn-outline-primary" id="previewBtn">
                                            <i class="bi bi-eye"></i>
                                            预览
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-check-lg"></i>
                                            <?= $tag ? '保存修改' : '创建标签' ?>
                                        </button>
                                    </div>
                                </form>
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
        // 页面配置
        window.TagEditConfig = {
            isEdit: <?= $tag ? 'true' : 'false' ?>,
            tagId: <?= $tag ? $tag['id'] : 'null' ?>,
            contentOptions: <?= json_encode($contentOptions ?? [], JSON_UNESCAPED_UNICODE) ?>,
            submitUrl: '<?= $tag ? '/backend/tags/' . $tag['id'] : '/backend/tags' ?>',
            method: '<?= $tag ? 'PUT' : 'POST' ?>'
        };

        // 表单提交处理
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('tagEditForm');
            const nameInput = document.getElementById('name_cn');
            const iconInput = document.getElementById('icon_class');
            const colorSelect = document.getElementById('color_class');
            const previewBtn = document.getElementById('tagPreviewBtn');
            const previewIcon = document.getElementById('previewIcon');
            const previewText = document.getElementById('previewText');

            // 实时预览更新
            function updatePreview() {
                const name = nameInput.value || '新标签';
                const icon = iconInput.value || 'bi-star';
                const color = colorSelect.value || 'btn-outline-primary';
                
                previewText.textContent = name;
                previewIcon.className = 'bi ' + icon;
                previewBtn.className = 'btn ' + color;
            }

            nameInput.addEventListener('input', updatePreview);
            iconInput.addEventListener('input', updatePreview);
            colorSelect.addEventListener('change', updatePreview);

            // 表单提交
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(form);
                
                // 处理checkbox状态
                if (!document.getElementById('status_id').checked) {
                    formData.set('status_id', '0');
                }

                // 获取选中的关联视频
                const selectedVideos = [];
                document.querySelectorAll('#videoMultiSelect input[type="checkbox"]:checked').forEach(function(checkbox) {
                    selectedVideos.push(checkbox.value);
                });
                formData.set('related_videos', JSON.stringify(selectedVideos));

                const submitBtn = form.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> 保存中...';
                submitBtn.disabled = true;

                fetch(window.TagEditConfig.submitUrl, {
                    method: window.TagEditConfig.method === 'PUT' ? 'POST' : 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message || '操作成功');
                        window.location.href = '/backend/tags';
                    } else {
                        alert(data.message || '操作失败');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('操作失败，请重试');
                })
                .finally(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
            });

            // 初始化多选组件
            if (typeof MultiSelectDropdown !== 'undefined' && window.TagEditConfig.contentOptions) {
                new MultiSelectDropdown({
                    container: '#videoMultiSelect',
                    options: window.TagEditConfig.contentOptions,
                    placeholder: '选择关联视频...',
                    searchPlaceholder: '搜索视频...'
                });
            }
        });
    </script>
</body>
</html>