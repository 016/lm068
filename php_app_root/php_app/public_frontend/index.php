<?php

// 前端入口文件 - www.yourdomain.com 指向这里
require_once __DIR__ . '/../vendor/autoload.php';

// lang init, lang is in url
\App\Core\I18n::initLang();

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

// 内容列表页（支持多参数筛选）
$router->get('/content', 'Frontend\\ContentController@index');

// 新增：单 ID 语义化 URL 路由
$router->get('/tag/{id}', 'Frontend\\ContentController@tagList');
$router->get('/tag/{id}/{slug}', 'Frontend\\ContentController@tagList');
$router->get('/collection/{id}', 'Frontend\\ContentController@collectionList');
$router->get('/collection/{id}/{slug}', 'Frontend\\ContentController@collectionList');
$router->get('/content-type/{id}', 'Frontend\\ContentController@contentTypeList');
$router->get('/content-type/{id}/{slug}', 'Frontend\\ContentController@contentTypeList');

// 内容详情页
$router->get('/content/{id}', 'Frontend\\ContentController@show');
$router->get('/content/{id}/{title}', 'Frontend\\ContentController@show');

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