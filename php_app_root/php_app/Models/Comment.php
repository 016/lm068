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

    const STATUS_HIDDEN = 0;
    const STATUS_PENDING = 1;
    const STATUS_APPROVED = 99;

    /**
     * ��ĺ�ߡ
     *
     * @return array ['total' => int, 'pending' => int, 'approved' => int, 'hidden' => int]
     */
    public static function getStatusStats(): array
    {
        $db = \App\Core\Database::getInstance();

        $sql = "SELECT COUNT(*) as count FROM " . static::getTableName();
        $result = $db->fetch($sql, []);
        $total = (int)$result['count'];

        $sql = "SELECT COUNT(*) as count FROM " . static::getTableName() . " WHERE status_id = :status_id";
        $result = $db->fetch($sql, ['status_id' => self::STATUS_PENDING]);
        $pending = (int)$result['count'];

        $sql = "SELECT COUNT(*) as count FROM " . static::getTableName() . " WHERE status_id = :status_id";
        $result = $db->fetch($sql, ['status_id' => self::STATUS_APPROVED]);
        $approved = (int)$result['count'];

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
     * 9n���ĺp�
     *
     * @param int $statusId
     * @return int
     */
    public static function countByStatus(int $statusId): int
    {
        return static::count(['status_id' => $statusId]);
    }

    /**
     * ������ĺh
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
     * �օ�8ĺh
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
     *
     * @param int $commentId
     * @return bool
     */
    public function approveComment(int $commentId): bool
    {
        return $this->update($commentId, ['status_id' => self::STATUS_APPROVED]);
    }

    /**
     *
     * @param int $commentId
     * @return bool
     */
    public function hideComment(int $commentId): bool
    {
        return $this->update($commentId, ['status_id' => self::STATUS_HIDDEN]);
    }
}
