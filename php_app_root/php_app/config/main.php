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
        'allowed_image_types' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        'allowed_video_types' => ['mp4', 'webm', 'avi', 'mov'],
        'allowed_file_types' => ['pdf', 'doc', 'docx', 'zip', 'rar'],

        // 上传路径配置 - 基于项目根目录的相对路径
        'base_path' =>  __DIR__.'/../../public_resources/uploads/',
        'thumbnails_path' => __DIR__.'/../../public_resources/uploads/thumbnails/',
        'videos_preview_path' =>  __DIR__.'/../../public_resources/uploads/videos_preview/',
        'avatars_path' =>  __DIR__.'/../../public_resources/uploads/avatars/',
        'files_path' =>  __DIR__.'/../../public_resources/uploads/files/',

        // 资源URL前缀配置
        'base_url' => 'https://dp-t-static.lib00.com/',
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
    ],

    // HashID配置 - 用于URL中ID的混淆
    'hashid' => [
        'enabled' => false, // 是否启用HashID功能
        'salt' => 'lm068_video_site_2025', // 盐值，用于生成唯一的hash
        'min_length' => 6, // 最小长度
    ]
];