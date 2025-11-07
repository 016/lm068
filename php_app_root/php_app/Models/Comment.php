<?php

namespace App\Models;

use App\Core\Model;

/**
 * Comment Model
 *
 * @property int $id 评论ID
 * @property int|null $root_id 根评论ID, 用于快速查询整个评论树
 * @property int|null $parent_id 父评论ID, 支持回复功能
 * @property int $user_id 用户ID
 * @property int $content_id 关联内容ID
 * @property string $content 评论内容
 * @property int $status_id 状态: 0-已隐藏, 1-待审核, 99-审核通过
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 *
 * @property-read User $user 评论用户
 * @property-read Content $linkContent 关联内容
 * @property-read Comment $parent 父评论
 * @property-read Comment $root 根评论
 * @property-read Comment[] $replies 子评论
 */
class Comment extends Model
{
    protected static string $table = 'comment';
    protected $primaryKey = 'id';
    protected $fillable = [
        'default' => [
            'root_id', 'parent_id', 'user_id', 'content_id', 'content', 'status_id'
        ]
    ];
    protected $timestamps = true;

    const STATUS_HIDDEN = 0;
    const STATUS_PENDING = 1;
    const STATUS_APPROVED = 99;

    /**
     * ============================================
     * 关系定义 - AR Pattern
     * ============================================
     */

    /**
     * 定义与 User 的 BelongsTo 关系
     */
    public function user(): \App\Core\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * 定义与 Content 的 BelongsTo 关系
     */
    public function linkContent(): \App\Core\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Content::class, 'content_id', 'id');
    }

    /**
     * 定义与父评论的 BelongsTo 关系（自关联）
     */
    public function parent(): \App\Core\Relations\BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id', 'id');
    }

    /**
     * 定义与根评论的 BelongsTo 关系（自关联）
     */
    public function root(): \App\Core\Relations\BelongsTo
    {
        return $this->belongsTo(Comment::class, 'root_id', 'id');
    }

    /**
     * 定义子评论的 HasMany 关系（自关联）
     */
    public function replies(): \App\Core\Relations\HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id', 'id');
    }

    /**
     * for dashboard show.
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

        // 1. 先查询所有评论的总数（包括顶级评论和嵌套评论）
        $countSql = "SELECT COUNT(*) as count FROM " . static::getTableName() . "
                     WHERE content_id = :content_id
                     AND status_id = :status_id";
        $countResult = $db->fetch($countSql, [
            'content_id' => $contentId,
            'status_id' => $statusId
        ]);
        $total = (int)$countResult['count'];

        // 2. 统计顶级评论数量（用于分页）
        $rootCountSql = "SELECT COUNT(*) as count FROM " . static::getTableName() . "
                         WHERE content_id = :content_id
                         AND status_id = :status_id
                         AND parent_id IS NULL";
        $rootCountResult = $db->fetch($rootCountSql, [
            'content_id' => $contentId,
            'status_id' => $statusId
        ]);
        $rootTotal = (int)$rootCountResult['count'];
        $totalPages = (int)ceil($rootTotal / $perPage);

        // 3. 查询当前页的顶级评论（关联用户信息）
        $offset = ($page - 1) * $perPage;
        $rootSql = "SELECT c.*,
                           u.avatar as user_avatar,
                           u.nickname as user_nickname,
                           u.username as user_username
                    FROM " . static::getTableName() . " c
                    LEFT JOIN user u ON c.user_id = u.id
                    WHERE c.content_id = :content_id
                    AND c.status_id = :status_id
                    AND c.parent_id IS NULL
                    ORDER BY c.created_at DESC
                    LIMIT {$perPage} OFFSET {$offset}";
        $rootRows = $db->fetchAll($rootSql, [
            'content_id' => $contentId,
            'status_id' => $statusId
        ]);

        // 4. 如果没有顶级评论，直接返回
        if (empty($rootRows)) {
            return [
                'items' => [],
                'total' => $total,
                'totalPages' => $totalPages
            ];
        }

        // 5. 获取所有子评论（基于root_id，关联用户信息）
        $rootIds = array_column($rootRows, 'id');
        $rootIdsPlaceholders = [];
        $params = [];
        foreach ($rootIds as $idx => $rootId) {
            $key = "root_id_{$idx}";
            $rootIdsPlaceholders[] = ":{$key}";
            $params[$key] = $rootId;
        }

        $childSql = "SELECT c.*,
                            u.avatar as user_avatar,
                            u.nickname as user_nickname,
                            u.username as user_username
                     FROM " . static::getTableName() . " c
                     LEFT JOIN user u ON c.user_id = u.id
                     WHERE c.root_id IN (" . implode(',', $rootIdsPlaceholders) . ")
                     AND c.status_id = :status_id
                     ORDER BY c.created_at ASC";
        $params['status_id'] = $statusId;
        $childRows = $db->fetchAll($childSql, $params);

        // 6. 构建评论树结构
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
