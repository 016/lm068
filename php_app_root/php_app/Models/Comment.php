<?php

namespace App\Models;

use App\Core\Model;

class Comment extends Model
{
    protected static string $table = 'comment';
    protected $primaryKey = 'id';
    protected $fillable = [
        'root_id', 'parent_id', 'user_id', 'content_id', 'content', 'status_id'
    ];
    protected $timestamps = true;

    // Äº¶8Ï (9npn“ schema)
    const STATUS_HIDDEN = 0;      // òÏ
    const STATUS_PENDING = 1;     // …¡8
    const STATUS_APPROVED = 99;   // ¡8Ç

    /**
     * ·ÖÄº¶ß¡
     *
     * @return array ['total' => int, 'pending' => int, 'approved' => int, 'hidden' => int]
     */
    public static function getStatusStats(): array
    {
        $db = \App\Core\Database::getInstance();

        // ;p
        $sql = "SELECT COUNT(*) as count FROM " . static::getTableName();
        $result = $db->fetch($sql, []);
        $total = (int)$result['count'];

        // …¡8
        $sql = "SELECT COUNT(*) as count FROM " . static::getTableName() . " WHERE status_id = :status_id";
        $result = $db->fetch($sql, ['status_id' => self::STATUS_PENDING]);
        $pending = (int)$result['count'];

        // ¡8Ç
        $sql = "SELECT COUNT(*) as count FROM " . static::getTableName() . " WHERE status_id = :status_id";
        $result = $db->fetch($sql, ['status_id' => self::STATUS_APPROVED]);
        $approved = (int)$result['count'];

        // òÏ
        $sql = "SELECT COUNT(*) as count FROM " . static::getTableName() . " WHERE status_id = :status_id";
        $result = $db->fetch($sql, ['status_id' => self::STATUS_HIDDEN]);
        $hidden = (int)$result['count'];

        return [
            'total' => $total,
            'pending' => $pending,
            'approved' => $approved,
            'hidden' => $hidden
        ];
    }

    /**
     * 9n¶·ÖÄºpÏ
     *
     * @param int $statusId
     * @return int
     */
    public static function countByStatus(int $statusId): int
    {
        return static::count(['status_id' => $statusId]);
    }

    /**
     * ·Öš…¹„Äºh
     *
     * @param int $contentId
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getCommentsByContent(int $contentId, int $limit = 20, int $offset = 0): array
    {
        return $this->findAll(
            ['content_id' => $contentId, 'status_id' => self::STATUS_APPROVED],
            $limit,
            $offset,
            'created_at DESC'
        );
    }

    /**
     * ·Ö…¡8Äºh
     *
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getPendingComments(int $limit = 20, int $offset = 0): array
    {
        return $this->findAll(
            ['status_id' => self::STATUS_PENDING],
            $limit,
            $offset,
            'created_at DESC'
        );
    }

    /**
     * ¡8ÇÄº
     *
     * @param int $commentId
     * @return bool
     */
    public function approveComment(int $commentId): bool
    {
        return $this->update($commentId, ['status_id' => self::STATUS_APPROVED]);
    }

    /**
     * ÏÄº
     *
     * @param int $commentId
     * @return bool
     */
    public function hideComment(int $commentId): bool
    {
        return $this->update($commentId, ['status_id' => self::STATUS_HIDDEN]);
    }
}
