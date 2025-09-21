<?php

// 替代设计方案：按业务领域分离

namespace App\Constants;

// 用户相关状态
enum UserStatus: int
{
    case BANNED = 0;      // 封停
    case ACTIVE = 1;      // 正常
    
    public function label(): string {
        return match($this) {
            self::BANNED => '封停',
            self::ACTIVE => '正常',
        };
    }
}

// 管理功能状态（tag, collection, admin_user）
enum ManagementStatus: int  
{
    case DISABLED = 0;    // 禁用
    case ENABLED = 1;     // 启用
    
    public function label(): string {
        return match($this) {
            self::DISABLED => '禁用',
            self::ENABLED => '启用',
        };
    }
}

// 订阅状态
enum SubscriptionStatus: int
{
    case UNSUBSCRIBED = 0;  // 取消订阅
    case SUBSCRIBED = 1;    // 已订阅
    
    public function label(): string {
        return match($this) {
            self::UNSUBSCRIBED => '取消订阅',
            self::SUBSCRIBED => '已订阅',
        };
    }
}

// 链接有效性状态
enum LinkStatus: int
{
    case INVALID = 0;     // 失效
    case VALID = 1;       // 正常
    
    public function label(): string {
        return match($this) {
            self::INVALID => '失效',
            self::VALID => '正常',
        };
    }
}