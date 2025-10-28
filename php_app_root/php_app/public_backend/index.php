<?php

// 检测 CLI 模式
if (php_sapi_name() === 'cli') {
    // 模拟 HTTP 环境
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['REQUEST_URI'] = $argv[1] ?? '/sitemap/generate';
    $_SERVER['HTTP_HOST'] = 'localhost';
}

// 后端入口文件 - admin.yourdomain.com 指向这里
require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Core\Request;
use App\Core\Config;

// 加载配置（支持本地配置覆盖）
$config = Config::load('main');

// 设置错误报告
if ($config['debug']) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// 设置时区
date_default_timezone_set($config['timezone']);

// 启动会话
session_start();

// 自动加载
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

// 创建路由实例
$router = new Router();

// 定义后台路由
$router->get('/', 'Backend\\DashboardController@index');
$router->get('/dashboard', 'Backend\\DashboardController@index');
$router->get('/dashboard/chart-data', 'Backend\\DashboardController@getChartData');
$router->get('/login', 'Backend\\AuthController@showLogin');
$router->post('/login', 'Backend\\AuthController@login');
$router->get('/logout', 'Backend\\AuthController@logout');

// 内容管理
$router->get('/contents', 'Backend\\ContentController@index');
$router->get('/contents/create', 'Backend\\ContentController@create');
$router->post('/contents/create', 'Backend\\ContentController@create');  // 创建路由，GET显示表单，POST处理数据
$router->post('/contents/bulk-action', 'Backend\\ContentController@bulkAction');
$router->post('/contents/bulk-import', 'Backend\\ContentController@bulkImport');
$router->get('/contents/{id}/edit', 'Backend\\ContentController@edit');
$router->post('/contents/{id}/edit', 'Backend\\ContentController@edit');  // 编辑路由，GET显示表单，POST处理数据
$router->delete('/contents/{id}', 'Backend\\ContentController@destroy');

// 用户管理
$router->get('/users', 'Backend\\UserController@index');
$router->get('/users/{id}', 'Backend\\UserController@show');
$router->post('/users/{id}/ban', 'Backend\\UserController@ban');

// 标签管理 - 具体路径在参数路径之前
$router->get('/tags', 'Backend\\TagController@index');
$router->get('/tags/create', 'Backend\\TagController@create');
$router->post('/tags/create', 'Backend\\TagController@create');  // 创建路由，GET显示表单，POST处理数据
$router->post('/tags/bulk-action', 'Backend\\TagController@bulkAction');
$router->post('/tags/bulk-import', 'Backend\\TagController@bulkImport');
$router->get('/tags/{id}/edit', 'Backend\\TagController@edit');
$router->post('/tags/{id}/edit', 'Backend\\TagController@edit');  // 编辑路由，GET显示表单，POST处理数据
$router->delete('/tags/{id}', 'Backend\\TagController@destroy');

// 合集管理 - 具体路径在参数路径之前
$router->get('/collections', 'Backend\\CollectionController@index');
$router->get('/collections/create', 'Backend\\CollectionController@create');
$router->post('/collections/create', 'Backend\\CollectionController@create');
$router->post('/collections/bulk-action', 'Backend\\CollectionController@bulkAction');
$router->post('/collections/bulk-import', 'Backend\\CollectionController@bulkImport');
$router->get('/collections/export', 'Backend\\CollectionController@exportData');
$router->get('/collections/{id}/edit', 'Backend\\CollectionController@edit');
$router->post('/collections/{id}/edit', 'Backend\\CollectionController@edit');
$router->get('/collections/{id}', 'Backend\\CollectionController@show');
$router->delete('/collections/{id}', 'Backend\\CollectionController@destroy');

// 视频链接管理
$router->get('/video-links', 'Backend\\VideoLinkController@index');
$router->get('/video-links/create', 'Backend\\VideoLinkController@create');
$router->post('/video-links/create', 'Backend\\VideoLinkController@create');  // 创建路由，GET显示表单，POST处理数据
$router->post('/video-links/bulk-action', 'Backend\\VideoLinkController@bulkAction');
$router->post('/video-links/bulk-import', 'Backend\\VideoLinkController@bulkImport');
$router->get('/video-links/{id}/edit', 'Backend\\VideoLinkController@edit');
$router->post('/video-links/{id}/edit', 'Backend\\VideoLinkController@edit');  // 编辑路由，GET显示表单，POST处理数据
$router->delete('/video-links/{id}', 'Backend\\VideoLinkController@destroy');

// 管理员管理 (只有超级管理员可访问管理列表和编辑，所有管理员可访问个人信息管理)
$router->get('/admin_users', 'Backend\\AdminUserController@index');
$router->get('/admin_users/create', 'Backend\\AdminUserController@create');
$router->post('/admin_users/create', 'Backend\\AdminUserController@create');
$router->get('/admin_users/self_update', 'Backend\\AdminUserController@selfUpdate');  // 个人信息管理(所有管理员可用)
$router->post('/admin_users/self_update', 'Backend\\AdminUserController@selfUpdate');
$router->post('/admin_users/bulk-action', 'Backend\\AdminUserController@bulkAction');
$router->post('/admin_users/bulk-import', 'Backend\\AdminUserController@bulkImport');
$router->get('/admin_users/{id}/edit', 'Backend\\AdminUserController@edit');
$router->post('/admin_users/{id}/edit', 'Backend\\AdminUserController@edit');
$router->delete('/admin_users/{id}', 'Backend\\AdminUserController@destroy');

// sitemap
$router->get('/sitemap/generate', 'Backend\\SitemapController@generate');

// 404页面
$router->notFound(function() {
    http_response_code(404);
    echo "<h1>404 - 页面未找到</h1>";
    echo "<p><a href='/dashboard'>返回后台首页</a></p>";
});

try {
    $request = new Request();
    $router->resolve($request);
} catch (Exception $e) {
    if ($config['debug']) {
        echo "<h1>Error</h1>";
        echo "<p>" . $e->getMessage() . "</p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    } else {
        http_response_code(500);
        echo "<h1>服务器错误</h1>";
    }
}