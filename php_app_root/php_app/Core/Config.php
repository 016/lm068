<?php

namespace App\Core;

/**
 * 配置管理类
 * 支持配置文件加载和本地配置合并
 */
class Config
{
    private static ?array $config = null;

    /**
     * 加载配置文件
     * 支持本地配置文件覆盖
     *
     * @param string $configFile 配置文件名（不含.php后缀）
     * @return array 合并后的配置数组
     */
    public static function load(string $configFile = 'main'): array
    {
        if (self::$config !== null) {
            return self::$config;
        }

        $configPath = __DIR__ . '/../config/';
        $mainConfig = [];
        $localConfig = [];

        // 加载主配置文件
        $mainFile = $configPath . $configFile . '.php';
        if (file_exists($mainFile)) {
            $mainConfig = require $mainFile;
        }

        // 加载本地配置文件（如果存在）
        $localFile = $configPath . $configFile . '.local.php';
        if (file_exists($localFile)) {
            $localConfig = require $localFile;
        }

        // 深度合并配置
        self::$config = self::mergeConfig($mainConfig, $localConfig);

        return self::$config;
    }

    /**
     * 获取配置值
     *
     * @param string $key 配置键，支持点号分隔的多级键（如 'upload.base_url'）
     * @param mixed $default 默认值
     * @return mixed 配置值
     */
    public static function get(string $key, $default = null)
    {
        if (self::$config === null) {
            self::load();
        }

        $keys = explode('.', $key);
        $value = self::$config;

        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }

        return $value;
    }

    /**
     * 深度合并配置数组
     * 本地配置会覆盖主配置
     *
     * @param array $main 主配置
     * @param array $local 本地配置
     * @return array 合并后的配置
     */
    private static function mergeConfig(array $main, array $local): array
    {
        foreach ($local as $key => $value) {
            if (is_array($value) && isset($main[$key]) && is_array($main[$key])) {
                $main[$key] = self::mergeConfig($main[$key], $value);
            } else {
                $main[$key] = $value;
            }
        }

        return $main;
    }

    /**
     * 重置配置缓存（用于测试）
     */
    public static function reset(): void
    {
        self::$config = null;
    }
}
