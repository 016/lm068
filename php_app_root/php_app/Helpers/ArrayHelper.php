<?php

namespace App\Helpers;

use App\Core\I18n;

/**
 * Array 辅助类
 * 提供 Array 元素生成和处理的工具方法
 */
class ArrayHelper
{
    /**
     * after load items from db, use this function to return title list as string with lang support
     * @param array $items
     * @param string $cnField
     * @param string $enField
     * @return string
     */
    public static function getLocalizedNames(array $items, string $cnField, string $enField, ?string $currentLang = null): string
    {
        if ($currentLang == null) {
            $currentLang = I18n::getCurrentLang();
        }

        $names = array_column($items, $currentLang === 'zh' ? $cnField : $enField);
        return implode(', ', $names);
    }


}