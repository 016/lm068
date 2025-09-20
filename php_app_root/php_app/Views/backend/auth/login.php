<!DOCTYPE html>
<html lang="zh-CN" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>视频管理系统 - 管理员登录</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Main CSS - Common Styles -->
    <link rel="stylesheet" href="/assets/css/main_3.css">
    
    <!-- page lv Specific CSS -->
    <link rel="stylesheet" href="/assets/css/login_2.css">
</head>
<body>
    <!-- Background Decoration -->
    <div class="bg-decoration"></div>
    
    <div class="login-layout">
        <!-- Simplified Topbar -->
        <header class="login-topbar">
            <a href="#" class="login-logo-brand">
                <span class="login-logo-icon">📹</span>
                <span>视频管理系统</span>
            </a>
            
            <!-- Theme Toggle - Copied from dashboard -->
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
        </header>

        <!-- Main Login Content -->
        <main class="login-container">
            <div class="login-card">
                <div class="login-header">
                    <div class="login-logo">🔐</div>
                    <h1 class="login-title">管理员登录</h1>
                    <p class="login-subtitle">请输入您的管理员凭据以访问系统</p>
                </div>
                
                
                <form class="login-form needs-validation" action="/login" id="loginForm" method="post" novalidate>
                    <div class="form-group">
                        <label class="form-label" for="username">用户名或邮箱</label>
                        <input type="text" id="username" name="username" class="form-control<?php echo isset($errors['username']) ? ' is-invalid' : (isset($username) && $username ? ' is-valid' : ''); ?>" placeholder="请输入用户名或邮箱" value="<?php echo htmlspecialchars($username ?? ''); ?>" required>
                        <?php if (isset($errors['username'])): ?>
                            <div class="invalid-feedback">
                                <i class="bi bi-exclamation-circle me-1"></i><?php echo htmlspecialchars($errors['username']); ?>
                            </div>
                        <?php elseif (isset($username) && $username): ?>
                            <div class="valid-feedback">
                                <i class="bi bi-check-circle me-1"></i>用户名格式正确
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="password">密码</label>
                        <div class="input-group has-validation">
                            <input type="password" id="password" name="password" class="form-control<?php echo isset($errors['password']) ? ' is-invalid' : ''; ?>" placeholder="请输入密码" required>
                            <button type="button" class="btn btn-outline-secondary" id="passwordToggle">
                                <i class="bi bi-eye" id="passwordToggleIcon"></i>
                            </button>
                            <?php if (isset($errors['password'])): ?>
                                <div class="invalid-feedback">
                                    <i class="bi bi-exclamation-circle me-1"></i><?php echo htmlspecialchars($errors['password']); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="form-options">
                        <div class="remember-me">
                            <input type="checkbox" id="rememberMe" name="rememberMe">
                            <label for="rememberMe">记住我</label>
                        </div>
                        <a href="#" class="forgot-password d-none">忘记密码？</a>
                    </div>
                    
                    <button type="submit" class="login-btn">
                        登录系统
                    </button>
                </form>
                
                <div class="login-footer">
                    <p class="footer-text">
                        登录表示您同意我们的服务条款和隐私政策
                    </p>
                </div>
            </div>
        </main>

        <!-- Simplified Footer -->
        <footer class="login-page-footer">
            © 2024 视频分享平台管理系统 • 状态: <span style="color: var(--success);">🟢 系统正常运行</span>
        </footer>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Main JS - Common Functions -->
    <script src="/assets/js/main_8.js"></script>
    
    <!-- Dashboard Specific JS -->
    <script src="/assets/js/login.js"></script>
    
</body>
</html>