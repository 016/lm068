<?php

/**
 * 本地开发环境配置文件
 * 此文件不应提交到版本库
 * 用于覆盖 main.php 中的默认配置
 */

return [
    // 文件上传本地配置 (可根据本地环境调整)
    'upload' => [
        // 资源URL前缀配置 - 本地开发环境
        'base_url' => 'http://localhost/lm068/php_app_root/public_resources/uploads/1',

        // 如需修改上传路径，可在此覆盖
        // 'pics_path' => '/custom/path/pics/',
    ],
];
