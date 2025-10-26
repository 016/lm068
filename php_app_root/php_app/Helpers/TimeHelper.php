<?php

namespace App\Helpers;

/**
 * Time 辅助类
 * 提供 Time 元素生成和处理的工具方法
 */
class TimeHelper
{

    /**
     * 将秒数转换为格式化的时间字符串（支持自定义格式）
     *
     * @param int $seconds 输入的秒数
     * @param string $format 格式类型: 'full' | 'short' | 'auto'
     * @return string 格式化后的时间字符串
     */
    public static function formatTime($seconds, $format = 'auto') {
        $seconds = abs(intval($seconds));

        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        switch ($format) {
            case 'short':
                // 仅显示非零部分
                if ($hours > 0) {
                    return sprintf("%d:%02d:%02d", $hours, $minutes, $secs);
                } elseif ($minutes > 0) {
                    return sprintf("%d:%02d", $minutes, $secs);
                } else {
                    return sprintf("0:%02d", $secs);
                }

            case 'auto':
                // 根据时长自动选择格式
                if ($hours > 0) {
                    return sprintf("%02d:%02d:%02d", $hours, $minutes, $secs);
                } else {
                    return sprintf("%02d:%02d", $minutes, $secs);
                }

            case 'full':
            default:
                // 完整格式 HH:MM:SS
                return sprintf("%02d:%02d:%02d", $hours, $minutes, $secs);
        }
    }

}