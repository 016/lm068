<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? '视频创作展示网站') ?></title>

    <!-- Bootstrap CSS 5.3.7 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons 1.13.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- 自定义CSS -->
    <link href="<?= $resourceUrl ?? '/assets' ?>/css/main.css" rel="stylesheet">
    <?php if (isset($pageCss)): ?>
        <link href="<?= $resourceUrl ?? '/assets' ?>/css/<?= $pageCss ?>" rel="stylesheet">
    <?php endif; ?>

    <!-- i18n配置 - 必须在i18n-helper.js之前加载 -->
    <script>
        // PHP传递给JS的i18n配置
        window.PHP_I18N_CONFIG = {
            currentLang: '<?= $currentLang ?? 'zh' ?>',
            supportedLangs: <?= json_encode($supportedLangs ?? ['zh', 'en']) ?>
        };
    </script>
</head>
<body>
    <!-- 顶部导航栏 -->
    <nav class="navbar navbar-expand-lg bg-body-tertiary" data-bs-theme="light">
        <div class="container">
            <!-- Logo -->
            <a class="navbar-brand fw-bold" href="/">
                <i class="bi bi-play-circle-fill me-2"></i>
                视频创作
            </a>

            <!-- 移动端切换按钮 -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- 导航内容 -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- 主要导航 -->
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= ($_SERVER['REQUEST_URI'] === '/' ? 'active' : '') ?>" href="/" data-i18n="nav.home">首页</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], '/videos') === 0 ? 'active' : '') ?>" href="/videos" data-i18n="nav.videos">视频</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-i18n="nav.about">关于</a>
                    </li>
                </ul>

                <!-- 右侧功能区 -->
                <div class="d-flex align-items-center">
                    <!-- 语言切换 -->
                    <div class="dropdown me-2">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-globe"></i> <span id="current-lang-label"><?= ($currentLang ?? 'zh') === 'zh' ? 'CN' : 'EN' ?></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item lang-switch-item <?= ($currentLang ?? 'zh') === 'zh' ? 'active' : '' ?>"
                                   href="javascript:void(0)"
                                   data-lang="zh"
                                   onclick="window.i18n.switchLanguage('zh')">
                                    简体中文
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item lang-switch-item <?= ($currentLang ?? 'zh') === 'en' ? 'active' : '' ?>"
                                   href="javascript:void(0)"
                                   data-lang="en"
                                   onclick="window.i18n.switchLanguage('en')">
                                    English
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- 主题切换 -->
                    <div class="dropdown me-2">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" id="themeDropdown" type="button" data-bs-toggle="dropdown" title="主题切换">
                            <i class="bi bi-palette"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" data-theme="dark"><i class="bi bi-moon me-2"></i>深色</a></li>
                            <li><a class="dropdown-item" href="#" data-theme="light"><i class="bi bi-sun me-2"></i>浅色</a></li>
                            <li><a class="dropdown-item" href="#" data-theme="auto"><i class="bi bi-circle-half me-2"></i>自动</a></li>
                        </ul>
                    </div>

                    <button class="btn btn-sm btn-outline-primary me-2 login-type-btn" type="button" data-i18n="nav.login">登录</button>
                    <button class="btn btn-sm btn-primary login-type-btn" type="button" data-i18n="nav.register">注册</button>
                </div>
            </div>
        </div>
    </nav>

    <!-- 主要内容 -->
    <main class="container my-4">
        <?= $content ?? '' ?>
    </main>

    <!-- 页脚 -->
    <footer class="text-light py-5 mt-5">
        <div class="container">
            <div class="row">
                <!-- 快速链接 -->
                <div class="col-lg-9 mb-4">
                    <div class="row">
                        <div class="col-md-4">
                            <h5 class="mb-3">网站导航</h5>
                            <ul class="list-unstyled">
                                <li><a href="/" class="text-light">首页</a></li>
                                <li><a href="/videos" class="text-light">视频列表</a></li>
                                <li><a href="#" class="text-light">用户中心</a></li>
                                <li><a href="#" class="text-light">收藏夹</a></li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <h5 class="mb-3">关于我们</h5>
                            <ul class="list-unstyled">
                                <li><a href="#" class="text-light">公司介绍</a></li>
                                <li><a href="#" class="text-light">联系我们</a></li>
                                <li><a href="#" class="text-light">加入我们</a></li>
                                <li><a href="#" class="text-light">隐私政策</a></li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <h5 class="mb-3">学习资源</h5>
                            <ul class="list-unstyled">
                                <li><a href="#" class="text-light">编程教程</a></li>
                                <li><a href="#" class="text-light">实战案例</a></li>
                                <li><a href="#" class="text-light">技术博客</a></li>
                                <li><a href="#" class="text-light">开发工具</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- 订阅邮件+社交媒体 -->
                <div class="col-lg-3 mb-4">
                    <!-- 邮件订阅 -->
                    <div class="mb-4">
                        <h5 class="mb-1">邮件订阅</h5>
                        <p class="mb-2 ms-2">加入邮件列表，获取最新视频更新和资讯</p>
                        <div class="input-group mail-list-quick-apply-input">
                            <input type="email" class="form-control" placeholder="请输入您的邮箱地址">
                            <button class="btn btn-primary" type="button">订阅</button>
                        </div>
                    </div>

                    <!-- 社交媒体 -->
                    <div>
                        <h5 class="mb-0 mt-2">社交媒体</h5>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="#" class="text-light"><i class="bi bi-youtube me-1"></i>YT</a>
                            <a href="#" class="text-light"><i class="bi bi-tv me-1"></i>Bilibili</a>
                            <a href="#" class="text-light"><i class="bi bi-tiktok me-1"></i>抖音</a>
                            <a href="#" class="text-light"><i class="bi bi-wechat me-1"></i>微信</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 版权信息 -->
            <hr class="my-4">
            <div class="row">
                <div class="col-12 text-center">
                    <p class="mb-0">&copy; 2024 视频创作展示网站. 保留所有权利. |
                        <a href="#" class="text-light">使用条款</a> |
                        <a href="#" class="text-light">隐私政策</a> |
                        <a href="#" class="text-light">Cookie政策</a>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- 悬浮按钮 -->
    <div class="floating-buttons">
        <!-- 回到顶部 -->
        <button class="btn btn-primary float-btn" id="backToTop" title="回到顶部">
            <i class="bi bi-arrow-up"></i>
        </button>

        <!-- 联系我们 -->
        <button class="btn btn-success float-btn" id="contactUs" title="联系我们">
            <i class="bi bi-chat-dots"></i>
        </button>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

    <!-- i18n脚本 - 必须按顺序加载 -->
    <script src="<?= $resourceUrl ?? '/assets' ?>/js/i18n.js"></script>
    <script src="<?= $resourceUrl ?? '/assets' ?>/js/i18n-helper.js"></script>

    <!-- 自定义JavaScript -->
    <script src="<?= $resourceUrl ?? '/assets' ?>/js/main.js"></script>
    <?php if (isset($pageJs)): ?>
        <script src="<?= $resourceUrl ?? '/assets' ?>/js/<?= $pageJs ?>"></script>
    <?php endif; ?>
</body>
</html>
