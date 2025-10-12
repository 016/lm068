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

    /**
     * 获取嵌套评论树（用于前端展示）
     *
     * @param int $contentId 内容ID
     * @param int $statusId 评论状态
     * @param int $page 当前页码
     * @param int $perPage 每页数量
     * @return array ['items' => Comment[], 'total' => int, 'totalPages' => int]
     */
    public function getNestedComments(int $contentId, int $statusId = self::STATUS_APPROVED, int $page = 1, int $perPage = 10): array
    {
        $db = \App\Core\Database::getInstance();

        // 1. 先查询所有顶级评论（root_id为NULL或parent_id为NULL的评论）的总数
        $countSql = "SELECT COUNT(*) as count FROM " . static::getTableName() . "
                     WHERE content_id = :content_id
                     AND status_id = :status_id
                     AND parent_id IS NULL";
        $countResult = $db->fetch($countSql, [
            'content_id' => $contentId,
            'status_id' => $statusId
        ]);
        $total = (int)$countResult['count'];
        $totalPages = (int)ceil($total / $perPage);

        // 2. 查询当前页的顶级评论
        $offset = ($page - 1) * $perPage;
        $rootSql = "SELECT * FROM " . static::getTableName() . "
                    WHERE content_id = :content_id
                    AND status_id = :status_id
                    AND parent_id IS NULL
                    ORDER BY created_at DESC
                    LIMIT {$perPage} OFFSET {$offset}";
        $rootRows = $db->fetchAll($rootSql, [
            'content_id' => $contentId,
            'status_id' => $statusId
        ]);

        // 3. 如果没有顶级评论，直接返回
        if (empty($rootRows)) {
            return [
                'items' => [],
                'total' => $total,
                'totalPages' => $totalPages
            ];
        }

        // 4. 获取所有子评论（基于root_id）
        $rootIds = array_column($rootRows, 'id');
        $rootIdsPlaceholders = [];
        $params = [];
        foreach ($rootIds as $idx => $rootId) {
            $key = "root_id_{$idx}";
            $rootIdsPlaceholders[] = ":{$key}";
            $params[$key] = $rootId;
        }

        $childSql = "SELECT * FROM " . static::getTableName() . "
                     WHERE root_id IN (" . implode(',', $rootIdsPlaceholders) . ")
                     AND status_id = :status_id
                     ORDER BY created_at ASC";
        $params['status_id'] = $statusId;
        $childRows = $db->fetchAll($childSql, $params);

        // 5. 构建评论树结构
        $comments = [];

        // 先处理顶级评论
        foreach ($rootRows as $row) {
            $comment = new \stdClass();
            foreach ($row as $key => $value) {
                $comment->$key = $value;
            }
            $comment->replies = []; // 初始化回复数组
            $comments[$row['id']] = $comment;
        }

        // 再处理子评论，并使用stdClass避免魔术方法问题
        $childComments = [];
        foreach ($childRows as $row) {
            $comment = new \stdClass();
            foreach ($row as $key => $value) {
                $comment->$key = $value;
            }
            $comment->replies = []; // 初始化回复数组
            $childComments[$row['id']] = $comment;
        }

        // 构建树形结构
        foreach ($childComments as $childComment) {
            $parentId = $childComment->parent_id;

            // 如果parent_id是顶级评论，添加到顶级评论的replies中
            if (isset($comments[$parentId])) {
                $comments[$parentId]->replies[] = $childComment;
            }
            // 如果parent_id是其他子评论，添加到对应子评论的replies中
            elseif (isset($childComments[$parentId])) {
                $childComments[$parentId]->replies[] = $childComment;
            }
        }

        return [
            'items' => array_values($comments),
            'total' => $total,
            'totalPages' => $totalPages
        ];
    }
}
