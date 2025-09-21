<?php
namespace App\Interfaces;

interface HasStatuses
{
    /**
     * 获取与此模型关联的状态枚举类名
     * @return string
     */
    public static function getStatusEnum(): string;
}