<?php

namespace App\Models;

use App\Core\Model;
use App\Constants\VideoLinkStatus;
use App\Interfaces\HasStatuses;

/**
 * VideoLink Model
 *
 * @property int $id Link ID
 * @property int $content_id 关联内容ID (content_type_id 应为视频类型)
 * @property int $platform_id 关联平台表ID
 * @property string $external_url 第三方视频链接
 * @property string $external_video_id 第三方平台视频URI里的ID
 * @property int $play_cnt 播放数
 * @property int $like_cnt 点赞数
 * @property int $favorite_cnt 收藏数
 * @property int $download_cnt 下载数
 * @property int $comment_cnt 评论数
 * @property int $share_cnt 分享数
 * @property int $status_id 状态: 1-正常, 0-失效
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 *
 * @property-read Content $content
 * @property-read Platform $platform 每日PV统计
 */

class VideoLink extends Model implements HasStatuses
{
    protected static string $table = 'video_link';
    protected $primaryKey = 'id';
    protected $fillable = [
        'default' => [
            'content_id', 'platform_id', 'external_url', 'external_video_id',
            'play_cnt', 'like_cnt', 'favorite_cnt', 'download_cnt',
            'comment_cnt', 'share_cnt', 'status_id'
        ]
    ];
    protected $timestamps = true;

    // 默认属性值
    protected array $defaults = [
        'content_id' => 0,
        'platform_id' => 0,
        'external_url' => '',
        'external_video_id' => '',
        'play_cnt' => 0,
        'like_cnt' => 0,
        'favorite_cnt' => 0,
        'download_cnt' => 0,
        'comment_cnt' => 0,
        'share_cnt' => 0,
        'status_id' => 1
    ];

    public function __construct()
    {
        parent::__construct();
        // 设置默认值
        $this->attributes = array_merge($this->defaults, $this->attributes);
    }

    /**
     * 实现接口方法，返回对应的状态枚举类
     */
    public static function getStatusEnum(): string
    {
        return VideoLinkStatus::class;
    }

    /**
     * 定义验证规则
     * @param bool $isUpdate 是否为更新操作
     * @param string|null $scenario 场景名称，为null时使用当前场景
     * @return array 验证规则
     */
    public function rules(): array
    {
        return [
            'default' => [
                'content_id' => 'required|numeric',
                'platform_id' => 'required|numeric',
                'external_url' => 'required|max:500',
                'external_video_id' => 'required|max:200',
                'play_cnt' => 'numeric',
                'like_cnt' => 'numeric',
                'favorite_cnt' => 'numeric',
                'download_cnt' => 'numeric',
                'comment_cnt' => 'numeric',
                'share_cnt' => 'numeric',
                'status_id' => 'numeric'
            ]
        ];
    }

    /**
     * 获取字段标签
     * @return array 字段标签映射
     */
    protected function getFieldLabels(): array
    {
        return [
            'content_id' => '关联内容',
            'platform_id' => '视频平台',
            'external_url' => '第三方链接',
            'external_video_id' => '第三方视频ID',
            'play_cnt' => '播放数',
            'like_cnt' => '点赞数',
            'favorite_cnt' => '收藏数',
            'download_cnt' => '下载数',
            'comment_cnt' => '评论数',
            'share_cnt' => '分享数',
            'status_id' => '状态'
        ];
    }

    /**
     * 定义与 Content 的 BelongsTo 关系
     */
    public function content(): \App\Core\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Content::class, 'content_id', 'id');
    }
    /**
     * 定义与 Platform 的 BelongsTo 关系
     */
    public function platform(): \App\Core\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Platform::class, 'platform_id', 'id');
    }

    /**
     * 获取状态标签
     */
    public function getStatusLabel(): string
    {
        if (isset($this->status_id)) {
            $status = VideoLinkStatus::tryFrom($this->status_id);
            return $status ? $status->label() : '未知状态';
        }
        return '未设置';
    }

    /**
     * 检查是否有效
     */
    public function isValid(): bool
    {
        return $this->status_id === VideoLinkStatus::VALID->value;
    }

    /**
     * 静态工厂方法 - 创建新VideoLink实例
     */
    public static function make(array $data = []): self
    {
        $instance = new static();
        $instance->fill($data);
        return $instance;
    }

    /**
     * 静态方法 - 通过ID查找
     */
    public static function findOrFail(int $id): self
    {
        $instance = new static();
        $found = $instance->find($id);
        if (!$found) {
            throw new \Exception("VideoLink with ID {$id} not found");
        }
        return $found;
    }

    /**
     * 获取统计信息
     */
    public function getStats(): array
    {
        $table = static::getTableName();
        $sql = "SELECT
                    COUNT(*) as total_links,
                    SUM(CASE WHEN status_id = :valid_status THEN 1 ELSE 0 END) as valid_links,
                    SUM(CASE WHEN status_id = :invalid_status THEN 1 ELSE 0 END) as invalid_links,
                    SUM(play_cnt) as total_plays,
                    SUM(like_cnt) as total_likes,
                    SUM(favorite_cnt) as total_favorites
                FROM {$table}";

        $result = $this->db->fetch($sql, [
            'valid_status' => VideoLinkStatus::VALID->value,
            'invalid_status' => VideoLinkStatus::INVALID->value
        ]);

        return [
            'total_links' => (int)$result['total_links'],
            'valid_links' => (int)$result['valid_links'],
            'invalid_links' => (int)$result['invalid_links'],
            'total_plays' => (int)$result['total_plays'],
            'total_likes' => (int)$result['total_likes'],
            'total_favorites' => (int)$result['total_favorites']
        ];
    }

    /**
     * 通过content_id获取所有视频链接
     */
    public function getByContentId(int $contentId): array
    {
        $table = static::getTableName();
        $sql = "SELECT vl.*, p.name as platform_name, p.code as platform_code
                FROM {$table} vl
                LEFT JOIN platform p ON vl.platform_id = p.id
                WHERE vl.content_id = :content_id
                ORDER BY vl.created_at DESC";

        return $this->db->fetchAll($sql, ['content_id' => $contentId]);
    }

    /**
     * 获取关联的内容信息
     */
    public function getRelatedContent(int $videoLinkId): ?array
    {
        $sql = "SELECT c.id, c.title_cn, c.title_en, c.content_type_id, c.status_id
                FROM content c
                INNER JOIN video_link vl ON c.id = vl.content_id
                WHERE vl.id = :video_link_id";

        return $this->db->fetch($sql, ['video_link_id' => $videoLinkId]);
    }

    /**
     * 获取关联的平台信息
     */
    public function getRelatedPlatform(int $videoLinkId): ?array
    {
        $sql = "SELECT p.id, p.name, p.code, p.base_url
                FROM platform p
                INNER JOIN video_link vl ON p.id = vl.platform_id
                WHERE vl.id = :video_link_id";

        return $this->db->fetch($sql, ['video_link_id' => $videoLinkId]);
    }

    /**
     * 重写父类方法，为VideoLink模型准备CSV导入数据
     *
     * @param array $csvRowData CSV行数据
     * @return array 处理后的数据
     */
    public function prepareBulkImportData(array $csvRowData): array
    {
        return [
            'content_id' => isset($csvRowData['content_id']) ? (int)$csvRowData['content_id'] : 0,
            'platform_id' => isset($csvRowData['platform_id']) ? (int)$csvRowData['platform_id'] : 0,
            'external_url' => $csvRowData['external_url'] ?? '',
            'external_video_id' => $csvRowData['external_video_id'] ?? '',
            'play_cnt' => isset($csvRowData['play_cnt']) ? (int)$csvRowData['play_cnt'] : 0,
            'like_cnt' => isset($csvRowData['like_cnt']) ? (int)$csvRowData['like_cnt'] : 0,
            'favorite_cnt' => isset($csvRowData['favorite_cnt']) ? (int)$csvRowData['favorite_cnt'] : 0,
            'download_cnt' => isset($csvRowData['download_cnt']) ? (int)$csvRowData['download_cnt'] : 0,
            'comment_cnt' => isset($csvRowData['comment_cnt']) ? (int)$csvRowData['comment_cnt'] : 0,
            'share_cnt' => isset($csvRowData['share_cnt']) ? (int)$csvRowData['share_cnt'] : 0,
            'status_id' => isset($csvRowData['status_id']) ? (int)$csvRowData['status_id'] : VideoLinkStatus::VALID->value
        ];
    }
}
