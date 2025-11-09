<?php

namespace App\Helpers;

use App\Core\Config;

/**
 * Log 辅助类
 * 提供 Log 生成和处理的工具方法
 */
class LogHelper
{
    // 静态属性在类加载时初始化，存储在内存中
    // 在整个 PHP 脚本执行期间保持状态
    private static $config = [
        'base_path' => '',  // 日志目录地址, 默认使用空字符串, 会在每次写入前检查实现从 config 里读取默认值。
        'channels' => [
            'app'      => 'app.log',
            'error'    => 'error.log',
            'api'      => 'api.log',
            'user'     => 'user.log',
            'payment'  => 'payment.log',
            'debug'    => 'debug.log',
        ]
    ];

    /**
     * 初始化配置(可选,在项目入口调用一次即可)
     */
    public static function init($basePath = null, $channels = []) {
        if ($basePath !== null) {
            self::$config['base_path'] = $basePath;
        }
        if (!empty($channels)) {
            self::$config['channels'] = array_merge(self::$config['channels'], $channels);
        }
    }

    /**
     * 检查config是否正确设置
     * @return array
     */
    private static function checkConfig()
    {
        if (empty(self::$config['base_path'])) {
            self::$config['base_path'] = Config::get('log.path', './../runtime/logs/');
        }
    }

    /**
     * 写入日志 - 使用通道名称
     */
    /**
     * @param $data
     * @param $channel
     * @param $level
     * @return bool
     */
    public static function write($data, $channel = 'app', $level = 'INFO') {
        // 总是在写入前进行一次 config 检查，保证 config 有来自配置文件的默认值
        self::checkConfig();

        //check log enable
        if (!Config::get('log.enabled')) {
            $data = ['log 写入失败，当前 log 写入功能在 config 文件中已关闭。'];
        }

        // 如果 channel 是完整路径(包含 / 或 \),直接使用
        if (strpos($channel, '/') !== false || strpos($channel, '\\') !== false) {
            $logPath = $channel;
        } else {
            // 否则从配置的通道获取
            $filename = self::$config['channels'][$channel] ?? "{$channel}.log";
            $logPath = self::$config['base_path'] . '/' . $filename;
        }

        return self::writeToFile($data, $logPath, $level);
    }

    /**
     * 快捷方法 - 不同级别
     */
    public static function info($data, $channel = 'app') {
        return self::write($data, $channel, 'INFO');
    }

    public static function error($data, $channel = 'error') {
        return self::write($data, $channel, 'ERROR');
    }

    public static function warning($data, $channel = 'app') {
        return self::write($data, $channel, 'WARNING');
    }

    public static function debug($data, $channel = 'debug') {
        return self::write($data, $channel, 'DEBUG');
    }

    /**
     * 实际写入文件的核心方法
     */
    private static function writeToFile($data, $logPath, $level) {

        $timestamp = date('Y-m-d H:i:s');

        $logContent = str_repeat('=', 80) . PHP_EOL;
        $logContent .= "[{$timestamp}] [{$level}]" . PHP_EOL;
        $logContent .= str_repeat('-', 80) . PHP_EOL;

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (is_array($value) || is_object($value)) {
                    $value = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                }
                $logContent .= "[{$key}]: {$value}" . PHP_EOL;
            }
        } else {
            $logContent .= $data . PHP_EOL;
        }

        $logContent .= str_repeat('=', 80) . PHP_EOL . PHP_EOL;

        $dir = dirname($logPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $result = file_put_contents($logPath, $logContent, FILE_APPEND | LOCK_EX);
        return $result !== false;
    }
}