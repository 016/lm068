<?php

// 后端入口文件 - admin.yourdomain.com 指向这里
require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Core\Request;

// 设置错误报告
$config = require_once __DIR__ . '/../config/main.php';
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
$router->get('/login', 'Backend\\AuthController@showLogin');
$router->post('/login', 'Backend\\AuthController@login');
$router->get('/logout', 'Backend\\AuthController@logout');

// 内容管理
$router->get('/content', 'Backend\\ContentController@index');
$router->get('/content/create', 'Backend\\ContentController@create');
$router->post('/content', 'Backend\\ContentController@store');
$router->get('/content/{id}/edit', 'Backend\\ContentController@edit');
$router->post('/content/{id}', 'Backend\\ContentController@update');
$router->post('/content/{id}/delete', 'Backend\\ContentController@delete');

// 用户管理
$router->get('/users', 'Backend\\UserController@index');
$router->get('/users/{id}', 'Backend\\UserController@show');
$router->post('/users/{id}/ban', 'Backend\\UserController@ban');

// 标签管理 - 具体路径在参数路径之前
$router->get('/tags', 'Backend\\TagController@index');
$router->get('/tags/create', 'Backend\\TagController@create');
$router->post('/tag/create', 'Backend\\TagController@store');  // 特定的创建路由
//$router->post('/tags', 'Backend\\TagController@store');
$router->post('/tags/bulk-action', 'Backend\\TagController@bulkAction');
$router->post('/tags/bulk-import', 'Backend\\TagController@bulkImport');
$router->get('/tags/{id}/edit', 'Backend\\TagController@edit');
$router->post('/tag/{id}/edit', 'Backend\\TagController@update');  // 新的编辑路由
//$router->post('/tags/{id}', 'Backend\\TagController@update');
//$router->get('/tags/{id}', 'Backend\\TagController@show');
$router->delete('/tags/{id}', 'Backend\\TagController@destroy');

// 合集管理 - 具体路径在参数路径之前
$router->get('/collections', 'Backend\\CollectionController@index');
$router->get('/collections/create', 'Backend\\CollectionController@create');
$router->post('/collections', 'Backend\\CollectionController@store');
$router->post('/collections/bulk-action', 'Backend\\CollectionController@bulkAction');
$router->post('/collections/bulk-import', 'Backend\\CollectionController@bulkImport');
$router->get('/collections/export', 'Backend\\CollectionController@exportData');
$router->get('/collections/{id}/edit', 'Backend\\CollectionController@edit');
$router->post('/collections/{id}', 'Backend\\CollectionController@update');
$router->get('/collections/{id}', 'Backend\\CollectionController@show');
$router->delete('/collections/{id}', 'Backend\\CollectionController@destroy');

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