<?php

namespace App\Helpers;

/**
 * HTML 辅助类
 * 提供 HTML 元素生成和处理的工具方法
 */
class TextHelper
{
    /**
     * 显示隐藏字符
     *
     * @param string $inputString input orginal string for show
     * @param string $characters 格式化类型 "\x00..\x1f"  "\r\n\t\0"
     * @return string
     */
    public static function showRealString(string $inputString, string $characters = "\x00..\x1f"): string
    {
        return addcslashes($inputString, $characters);
    }



}