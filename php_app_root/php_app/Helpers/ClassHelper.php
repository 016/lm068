<?php

namespace App\Helpers;

use App\Core\I18n;

/**
 * Array 辅助类
 * 提供 Array 元素生成和处理的工具方法
 */
class ClassHelper
{
    /**
     * Get the class "basename" of the given object / class.
     *
     * @param  string|object  $class
     * @return string
     */
    public static function class_basename($class): string
    {
        // 如果传入的是对象，先获取其类名
        $class = is_object($class) ? get_class($class) : $class;

        // 查找最后一个命名空间分隔符 `\`
        $lastSlashPosition = strrpos($class, '\\');

        // 如果找到了，则截取它后面的部分；否则返回原字符串
        return false === $lastSlashPosition
            ? $class
            : substr($class, $lastSlashPosition + 1);
    }


}