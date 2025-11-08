<?php

namespace App\Models;

use App\Core\Model;
use App\Constants\UserStatus;

/**
 * User Model
 *
 * @property int $id 用户ID
 * @property string $username 用户名
 * @property string $email 邮箱
 * @property string $password_hash 密码哈希
 * @property string|null $avatar 用户头像URL
 * @property string|null $nickname 用户昵称
 * @property int $status_id 用户状态: 0=不可用/封停, 1=可用/正常
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 *
 * @property-read Comment[] $comments 用户的评论
 */
class User extends Model
{
    protected static string $table = 'user';
    protected $fillable = [
        'default' => [
            'username', 'email', 'password_hash', 'avatar',
            'nickname', 'status_id'
        ]
    ];

    /**
     * ============================================
     * 关系定义 - AR Pattern
     * ============================================
     */

    /**
     * 定义与 Comment 的 HasMany 关系
     */
    public function comments(): \App\Core\Relations\HasMany
    {
        return $this->hasMany(Comment::class, 'user_id', 'id');
    }

    /**
     * ============================================
     * 原有方法保持不变
     * ============================================
     */

    /**
     * 获取用户总数和本月新增统计
     *
     * @return array ['total' => int, 'monthly_new' => int, 'monthly_growth_rate' => float]
     */
    public static function getTotalAndMonthlyStats(): array
    {
        $db = \App\Core\Database::getInstance();

        // 总用户数
        $sql = "SELECT COUNT(*) as count FROM " . static::getTableName();
        $result = $db->fetch($sql, []);
        $total = (int)$result['count'];

        // 本月新增用户数
        $firstDayOfMonth = date('Y-m-01 00:00:00');
        $sql = "SELECT COUNT(*) as count FROM " . static::getTableName() . " WHERE created_at >= :first_day";
        $result = $db->fetch($sql, ['first_day' => $firstDayOfMonth]);
        $monthlyNew = (int)$result['count'];

        // 计算增长率
        $growthRate = $total > 0 ? round(($monthlyNew / $total) * 100, 1) : 0;

        return [
            'total' => $total,
            'monthly_new' => $monthlyNew,
            'monthly_growth_rate' => $growthRate
        ];
    }
}