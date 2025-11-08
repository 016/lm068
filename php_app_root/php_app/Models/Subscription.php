<?php

namespace App\Models;

use App\Constants\SubscriptionStatus;
use App\Core\Model;

/**
 * Subscription Model
 *
 * @property int $id 订阅ID
 * @property string $email 邮箱
 * @property int $status_id 订阅状态: 0=取消订阅, 1=已订阅
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class Subscription extends Model
{
    protected static string $table = 'subscription';
    protected $primaryKey = 'id';
    protected $fillable = [
        'default' => [
            'email', 'status_id'
        ]
    ];
    protected $timestamps = true;

    /**
     *
     * @return array ['total' => int, 'monthly_new' => int, 'monthly_growth_rate' => float]
     */
    public static function getActiveSubscriberStats(): array
    {
        $db = \App\Core\Database::getInstance();

        //
        $sql = "SELECT COUNT(*) as count FROM " . static::getTableName() . " WHERE status_id = :status_id";
        $result = $db->fetch($sql, ['status_id' => SubscriptionStatus::SUBSCRIBED->value]);
        $total = (int)$result['count'];

        //
        $firstDayOfMonth = date('Y-m-01 00:00:00');
        $sql = "SELECT COUNT(*) as count FROM " . static::getTableName() .
            " WHERE status_id = :status_id AND created_at >= :first_day";
        $result = $db->fetch($sql, [
            'status_id' => SubscriptionStatus::SUBSCRIBED->value,
            'first_day' => $firstDayOfMonth
        ]);
        $monthlyNew = (int)$result['count'];

        //
        $growthRate = $total > 0 ? round(($monthlyNew / $total) * 100, 1) : 0;

        return [
            'total' => $total,
            'monthly_new' => $monthlyNew,
            'monthly_growth_rate' => $growthRate
        ];
    }

}
