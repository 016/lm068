<?php

use App\Core\Config;
use App\Helpers\UrlHelper;

/**
 * @var $this \App\Controllers\Frontend\FrontendController
 */

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($this->seo_param['title']) ?></title>
    <meta name="description" content="<?= htmlspecialchars($this->seo_param['desc']) ?>" />
    <link rel="canonical" href="<?= $this->base_url. htmlspecialchars( UrlHelper::generateUri($_GET['s'], $_GET))?>" />

    <link rel="alternate" hreflang="zh-CN" href="<?= $this->base_url. htmlspecialchars( UrlHelper::generateUri($this->curAction_en, array_merge($_GET, ['lang' => 'zh'])))?>" />
    <link rel="alternate" hreflang="en" href="<?= $this->base_url. htmlspecialchars( UrlHelper::generateUri($this->curAction_en, array_merge($_GET, ['lang' => 'en'])))?>" />
    <link rel="alternate" hreflang="x-default" href="<?= $this->base_url. htmlspecialchars( UrlHelper::generateUri($this->curAction_en, array_merge($_GET, ['lang' => 'en'])))?>" />



    <!-- Bootstrap CSS 5.3.8 -->
    <link href="/assets/lib/bootstrap-5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons 1.13.1 -->
    <link href="/assets/lib/bootstrap-icons-1.13.1/bootstrap-icons.css" rel="stylesheet">

    <!-- 自定义CSS -->
    <link href="<?= $resourceUrl ?? '/assets' ?>/css/main.css" rel="stylesheet">
    <?php if (isset($pageCss)): ?>
        <?php if (is_array($pageCss)): ?>
            <?php foreach ($pageCss as $css): ?>
                <link href="<?= $resourceUrl ?? '/assets' ?>/css/<?= $css ?>" rel="stylesheet">
            <?php endforeach; ?>
        <?php else: ?>
            <link href="<?= $resourceUrl ?? '/assets' ?>/css/<?= $pageCss ?>" rel="stylesheet">
        <?php endif; ?>
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
                <span data-i18n="nav.site_name"><?= ($currentLang ?? 'zh') === 'zh' ? 'DP_IT ' : 'DP_IT' ?></span>
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
                        <a class="nav-link <?= ($_SERVER['REQUEST_URI'] === '/' ? 'active' : '') ?>" href="/" data-i18n="nav.home"><?= ($currentLang ?? 'zh') === 'zh' ? '首页' : 'Home' ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], '/content') === 0 ? 'active' : '') ?>" href="/content" data-i18n="nav.content"><?= ($currentLang ?? 'zh') === 'zh' ? '内容' : 'Content' ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-i18n="nav.about"><?= ($currentLang ?? 'zh') === 'zh' ? '关于' : 'About' ?></a>
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
                                   onclick="if(window.i18n) window.i18n.switchLanguage('zh')">
                                    简体中文
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item lang-switch-item <?= ($currentLang ?? 'zh') === 'en' ? 'active' : '' ?>"
                                   href="javascript:void(0)"
                                   data-lang="en"
                                   onclick="if(window.i18n) window.i18n.switchLanguage('en')">
                                    English
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- 主题切换 -->
                    <div class="dropdown me-2">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" id="themeDropdown" type="button" data-bs-toggle="dropdown" data-i18n-title="theme.title">
                            <i class="bi bi-palette"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" data-theme="dark"><i class="bi bi-moon me-2"></i><span data-i18n="theme.dark"><?= ($currentLang ?? 'zh') === 'zh' ? '深色' : 'Dark' ?></span></a></li>
                            <li><a class="dropdown-item" href="#" data-theme="light"><i class="bi bi-sun me-2"></i><span data-i18n="theme.light"><?= ($currentLang ?? 'zh') === 'zh' ? '浅色' : 'Light' ?></span></a></li>
                            <li><a class="dropdown-item" href="#" data-theme="auto"><i class="bi bi-circle-half me-2"></i><span data-i18n="theme.auto"><?= ($currentLang ?? 'zh') === 'zh' ? '自动' : 'Auto' ?></span></a></li>
                        </ul>
                    </div>

                    <button class="btn btn-sm btn-outline-primary me-2 login-type-btn" type="button" data-i18n="nav.login"><?= ($currentLang ?? 'zh') === 'zh' ? '登录' : 'Login' ?></button>
                    <button class="btn btn-sm btn-primary login-type-btn" type="button" data-i18n="nav.register"><?= ($currentLang ?? 'zh') === 'zh' ? '注册' : 'Register' ?></button>
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
                            <h5 class="mb-3" data-i18n="footer.navigation"><?= ($currentLang ?? 'zh') === 'zh' ? '网站导航' : 'Navigation' ?></h5>
                            <ul class="list-unstyled">
                                <li><a href="/" class="text-light" data-i18n="footer.home"><?= ($currentLang ?? 'zh') === 'zh' ? '首页' : 'Home' ?></a></li>
                                <li><a href="/videos" class="text-light" data-i18n="footer.video_list"><?= ($currentLang ?? 'zh') === 'zh' ? '视频列表' : 'Video List' ?></a></li>
                                <li><a href="#" class="text-light" data-i18n="footer.user_center"><?= ($currentLang ?? 'zh') === 'zh' ? '用户中心' : 'User Center' ?></a></li>
                                <li><a href="#" class="text-light" data-i18n="footer.favorites"><?= ($currentLang ?? 'zh') === 'zh' ? '收藏夹' : 'Favorites' ?></a></li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <h5 class="mb-3" data-i18n="footer.about_us"><?= ($currentLang ?? 'zh') === 'zh' ? '关于我们' : 'About Us' ?></h5>
                            <ul class="list-unstyled">
                                <li><a href="#" class="text-light" data-i18n="footer.company_intro"><?= ($currentLang ?? 'zh') === 'zh' ? '公司介绍' : 'Company' ?></a></li>
                                <li><a href="#" class="text-light" data-i18n="footer.contact"><?= ($currentLang ?? 'zh') === 'zh' ? '联系我们' : 'Contact' ?></a></li>
                                <li><a href="#" class="text-light" data-i18n="footer.join_us"><?= ($currentLang ?? 'zh') === 'zh' ? '加入我们' : 'Join Us' ?></a></li>
                                <li><a href="#" class="text-light" data-i18n="footer.privacy"><?= ($currentLang ?? 'zh') === 'zh' ? '隐私政策' : 'Privacy Policy' ?></a></li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <h5 class="mb-3" data-i18n="footer.resources"><?= ($currentLang ?? 'zh') === 'zh' ? '学习资源' : 'Learning Resources' ?></h5>
                            <ul class="list-unstyled">
                                <li><a href="#" class="text-light" data-i18n="footer.tutorials"><?= ($currentLang ?? 'zh') === 'zh' ? '编程教程' : 'Tutorials' ?></a></li>
                                <li><a href="#" class="text-light" data-i18n="footer.cases"><?= ($currentLang ?? 'zh') === 'zh' ? '实战案例' : 'Cases' ?></a></li>
                                <li><a href="#" class="text-light" data-i18n="footer.blog"><?= ($currentLang ?? 'zh') === 'zh' ? '技术博客' : 'Tech Blog' ?></a></li>
                                <li><a href="#" class="text-light" data-i18n="footer.tools"><?= ($currentLang ?? 'zh') === 'zh' ? '开发工具' : 'Dev Tools' ?></a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- 订阅邮件+社交媒体 -->
                <div class="col-lg-3 mb-4">
                    <!-- 邮件订阅 -->
                    <div class="mb-4">
                        <h5 class="mb-1" data-i18n="footer.subscribe"><?= ($currentLang ?? 'zh') === 'zh' ? '邮件订阅' : 'Email Subscription' ?></h5>
                        <p class="mb-2 ms-2" data-i18n="footer.subscribe_desc"><?= ($currentLang ?? 'zh') === 'zh' ? '加入邮件列表，获取最新视频更新和资讯' : 'Join our mailing list for latest video updates' ?></p>
                        <div class="input-group mail-list-quick-apply-input">
                            <input type="email" class="form-control" placeholder="<?= ($currentLang ?? 'zh') === 'zh' ? '请输入您的邮箱地址' : 'Enter your email address' ?>" data-i18n-placeholder="footer.subscribe_placeholder">
                            <button class="btn btn-primary" type="button" data-i18n="footer.subscribe_btn"><?= ($currentLang ?? 'zh') === 'zh' ? '订阅' : 'Subscribe' ?></button>
                        </div>
                    </div>

                    <!-- 社交媒体 -->
                    <div>
                        <h5 class="mb-0 mt-2" data-i18n="footer.social"><?= ($currentLang ?? 'zh') === 'zh' ? '社交媒体' : 'Social Media' ?></h5>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="#" class="text-light"><i class="bi bi-youtube me-1"></i><span data-i18n="footer.youtube">YT</span></a>
                            <a href="#" class="text-light"><i class="bi bi-tv me-1"></i><span data-i18n="footer.bilibili">Bilibili</span></a>
                            <a href="#" class="text-light"><i class="bi bi-tiktok me-1"></i><span data-i18n="footer.douyin"><?= ($currentLang ?? 'zh') === 'zh' ? '抖音' : 'Douyin' ?></span></a>
                            <a href="#" class="text-light"><i class="bi bi-wechat me-1"></i><span data-i18n="footer.wechat"><?= ($currentLang ?? 'zh') === 'zh' ? '微信' : 'WeChat' ?></span></a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 版权信息 -->
            <hr class="my-4">
            <div class="row">
                <div class="col-12 text-center">
                    <p class="mb-0">&copy; 2024 <span data-i18n="footer.copyright"><?= ($currentLang ?? 'zh') === 'zh' ? '视频创作展示网站. 保留所有权利.' : 'Video Creation Platform. All rights reserved.' ?></span> |
                        <a href="#" class="text-light" data-i18n="footer.terms"><?= ($currentLang ?? 'zh') === 'zh' ? '使用条款' : 'Terms of Use' ?></a> |
                        <a href="#" class="text-light" data-i18n="footer.privacy"><?= ($currentLang ?? 'zh') === 'zh' ? '隐私政策' : 'Privacy Policy' ?></a> |
                        <a href="#" class="text-light" data-i18n="footer.cookies"><?= ($currentLang ?? 'zh') === 'zh' ? 'Cookie政策' : 'Cookie Policy' ?></a>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- 悬浮按钮 -->
    <div class="floating-buttons">
        <!-- 回到顶部 -->
        <button class="btn btn-primary float-btn" id="backToTop" data-i18n-title="float.back_to_top">
            <i class="bi bi-arrow-up"></i>
        </button>

        <!-- 联系我们 -->
        <button class="btn btn-success float-btn" id="contactUs" data-i18n-title="float.contact">
            <i class="bi bi-chat-dots"></i>
        </button>
    </div>

    <!-- Bootstrap JS -->
    <script src="/assets/lib/bootstrap-5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/lib/marked-15.0.12/js/marked.min.js"></script>

    <!-- i18n脚本 - 必须按顺序加载 -->
    <script src="<?= $resourceUrl ?? '/assets' ?>/js/i18n.js"></script>
    <script src="<?= $resourceUrl ?? '/assets' ?>/js/i18n-helper.js"></script>

    <!-- 自定义JavaScript -->
    <script src="<?= $resourceUrl ?? '/assets' ?>/js/main.js"></script>
    <?php if (isset($pageJs)): ?>
        <?php if (is_array($pageJs)): ?>
            <?php foreach ($pageJs as $js): ?>
                <script src="<?= $resourceUrl ?? '/assets' ?>/js/<?= $js ?>"></script>
            <?php endforeach; ?>
        <?php else: ?>
            <script src="<?= $resourceUrl ?? '/assets' ?>/js/<?= $pageJs ?>"></script>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-JYWS2Q7CHL"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-JYWS2Q7CHL');
    </script>
</body>
</html>
