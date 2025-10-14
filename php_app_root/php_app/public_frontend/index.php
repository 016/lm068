<?php

// 前端入口文件 - www.yourdomain.com 指向这里
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

// 创建路由实例
$router = new Router();

// 定义前端路由
$router->get('/', 'Frontend\\HomeController@index');
$router->get('/test', 'Frontend\\HomeController@test');
$router->get('/content', 'Frontend\\ContentController@index');
$router->get('/content/{id}', 'Frontend\\ContentController@show');
$router->get('/content/{id}/{title}', 'Frontend\\ContentController@show');
$router->get('/login', 'Frontend\\AuthController@showLogin');
$router->post('/login', 'Frontend\\AuthController@login');
$router->get('/register', 'Frontend\\AuthController@showRegister');
$router->post('/register', 'Frontend\\AuthController@register');

// 404页面
$router->notFound(function() {
    http_response_code(404);
    echo "<h1>404 - 页面未找到</h1>";
    echo "<p><a href='/'>返回首页</a></p>";
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