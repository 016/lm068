<?php

return [
    // 应用基础配置
    'app_name' => 'Video Content Site',
    'app_version' => '1.0.0',
    'debug' => true,
    'timezone' => 'Asia/Shanghai',
    'charset' => 'UTF-8',
    
    // 安全配置
    'secret_key' => 'your-secret-key-change-in-production',
    'session_lifetime' => 3600, // 1小时
    
    // 语言配置
    'default_language' => 'cn',
    'supported_languages' => ['cn', 'en'],
    
    // 文件上传配置
    'upload' => [
        'max_file_size' => 10 * 1024 * 1024, // 10MB
        'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'webm'],
        'upload_path' => '../public_resources/uploads/',
    ],
    
    // 缓存配置
    'cache' => [
        'enabled' => true,
        'default_ttl' => 3600, // 1小时
    ],
    
    // 分页配置
    'pagination' => [
        'per_page' => 20,
        'max_per_page' => 100,
    ],
    
    // 日志配置
    'log' => [
        'enabled' => true,
        'level' => 'error', // debug, info, warning, error
        'path' => __DIR__ . '/../runtime/logs/',
    ]
];