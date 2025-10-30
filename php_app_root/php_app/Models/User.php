<?php

namespace App\Models;

use App\Core\Model;
use App\Constants\UserStatus;

class User extends Model
{
    protected static string $table = 'user';
    protected $fillable = [
        'username', 'email', 'password_hash', 'avatar', 
        'nickname', 'status_id'
    ];

    public function findByEmail(string $email): ?array
    {
        $table = static::getTableName();
        $sql = "SELECT * FROM {$table} WHERE email = :email LIMIT 1";
        return $this->db->fetch($sql, ['email' => $email]);
    }

    public function findByUsername(string $username): ?array
    {
        $table = static::getTableName();
        $sql = "SELECT * FROM {$table} WHERE username = :username LIMIT 1";
        return $this->db->fetch($sql, ['username' => $username]);
    }

    public function getActiveUsers(int $limit = 20, int $offset = 0): array
    {
        return $this->findAll(['status_id' => UserStatus::ACTIVE->value], 'created_at DESC', $limit, $offset);
    }

    public function createUser(array $data): int
    {
        if (!empty($data['password'])) {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
            unset($data['password']);
        }

        return $this->create($data);
    }

    public function verifyPassword(array $user, string $password): bool
    {
        return password_verify($password, $user['password_hash']);
    }

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

    /**
     * 获取指定日期范围内的新增用户数
     *
     * @param string $startDate 开始日期 (Y-m-d)
     * @param string $endDate 结束日期 (Y-m-d)
     * @return int
     */
    public static function getNewUserCountByDateRange(string $startDate, string $endDate): int
    {
        $db = \App\Core\Database::getInstance();
        $sql = "SELECT COUNT(*) as count FROM " . static::getTableName() .
               " WHERE created_at >= :start_date AND created_at < :end_date";

        $result = $db->fetch($sql, [
            'start_date' => $startDate . ' 00:00:00',
            'end_date' => $endDate . ' 23:59:59'
        ]);

        return (int)$result['count'];
    }
}