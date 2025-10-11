<?php

namespace App\Models;

use App\Core\Model;

class Subscription extends Model
{
    protected static string $table = 'subscription';
    protected $primaryKey = 'id';
    protected $fillable = ['email', 'status_id'];
    protected $timestamps = true;

    // ¢¶8Ï
    const STATUS_UNSUBSCRIBED = 0;  // Öˆ¢
    const STATUS_SUBSCRIBED = 1;    // ò¢

    /**
     * ·Ö;Ã¢;pŒ,°žß¡
     *
     * @return array ['total' => int, 'monthly_new' => int, 'monthly_growth_rate' => float]
     */
    public static function getActiveSubscriberStats(): array
    {
        $db = \App\Core\Database::getInstance();

        // ;Ã¢;p (status_id = 1)
        $sql = "SELECT COUNT(*) as count FROM " . static::getTableName() . " WHERE status_id = :status_id";
        $result = $db->fetch($sql, ['status_id' => self::STATUS_SUBSCRIBED]);
        $total = (int)$result['count'];

        // ,°ž¢
        $firstDayOfMonth = date('Y-m-01 00:00:00');
        $sql = "SELECT COUNT(*) as count FROM " . static::getTableName() .
               " WHERE status_id = :status_id AND created_at >= :first_day";
        $result = $db->fetch($sql, [
            'status_id' => self::STATUS_SUBSCRIBED,
            'first_day' => $firstDayOfMonth
        ]);
        $monthlyNew = (int)$result['count'];

        // ¡—ž‡
        $growthRate = $total > 0 ? round(($monthlyNew / $total) * 100, 1) : 0;

        return [
            'total' => $total,
            'monthly_new' => $monthlyNew,
            'monthly_growth_rate' => $growthRate
        ];
    }

    /**
     * ·Ö;Ã¢;p
     *
     * @return int
     */
    public static function getActiveCount(): int
    {
        return static::count(['status_id' => self::STATUS_SUBSCRIBED]);
    }

    /**
     * 9n®±å~¢°U
     *
     * @param string $email
     * @return array|null
     */
    public function findByEmail(string $email): ?array
    {
        $table = static::getTableName();
        $sql = "SELECT * FROM {$table} WHERE email = :email LIMIT 1";
        return $this->db->fetch($sql, ['email' => $email]);
    }

    /**
     * ¢®ö
     *
     * @param string $email
     * @return int ÔÞ¢ID
     */
    public function subscribe(string $email): int
    {
        $existing = $this->findByEmail($email);

        if ($existing) {
            // ‚œòX(,ô°¶:ò¢
            $this->update($existing['id'], ['status_id' => self::STATUS_SUBSCRIBED]);
            return $existing['id'];
        }

        // ú°¢
        return $this->create([
            'email' => $email,
            'status_id' => self::STATUS_SUBSCRIBED
        ]);
    }

    /**
     * Öˆ¢
     *
     * @param string $email
     * @return bool
     */
    public function unsubscribe(string $email): bool
    {
        $existing = $this->findByEmail($email);

        if ($existing) {
            return $this->update($existing['id'], ['status_id' => self::STATUS_UNSUBSCRIBED]);
        }

        return false;
    }
}
