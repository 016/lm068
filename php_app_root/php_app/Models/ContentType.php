<?php

namespace App\Models;

use App\Core\Model;
use App\Constants\ContentTypeStatus;
use App\Constants\ContentStatus;
use App\Interfaces\HasStatuses;

/**
 * ContentType Model
 *
 * @property int $id 内容类型ID
 * @property string $name_en 英文名称
 * @property string $name_cn 中文名称
 * @property int $content_cnt 关联内容数量
 * @property int $published_content_cnt 关联已发布内容数量
 * @property int $status_id 状态: 1-启用, 0-禁用
 * @property int $sort_order 排序字段, 数字大在前面, 默认值1。一般置顶使用11。特殊置顶使用21+
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 *
 * @property-read Content[] $contents 关联的内容
 */
class ContentType extends Model implements HasStatuses
{
    protected static string $table = 'content_type';
    protected $primaryKey = 'id';
    protected $fillable = [
        'default' => [
            'name_en', 'name_cn', 'content_cnt', 'published_content_cnt', 'status_id', 'sort_order'
        ]
    ];
    protected $timestamps = true;

    // 默认属性值
    protected array $defaults = [
        'name_cn' => '',
        'name_en' => '',
        'content_cnt' => 0,
        'published_content_cnt' => 0,
        'status_id' => 1,
        'sort_order' => 1
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
        return ContentTypeStatus::class;
    }

    /**
     * 定义验证规则
     * @param bool $isUpdate 是否为更新操作
     * @param string|null $scenario 场景名称，为null时使用当前场景
     * @return array 验证规则
     */
    public function rules(bool $isUpdate = false, ?string $scenario = null): array
    {
        return [
            'default' => [
                'name_cn' => 'required|max:50|unique',
                'name_en' => 'required|max:50|unique',
                'status_id' => 'numeric',
                'sort_order' => 'numeric'
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
            'name_cn' => '中文名称',
            'name_en' => '英文名称',
            'status_id' => '状态',
            'sort_order' => '排序'
        ];
    }

    /**
     * 获取状态标签
     */
    public function getStatusLabel(): string
    {
        if (isset($this->status_id)) {
            $status = ContentTypeStatus::tryFrom($this->status_id);
            return $status ? $status->label() : '未知状态';
        }
        return '未设置';
    }

    /**
     * 检查是否启用
     */
    public function isEnabled(): bool
    {
        return $this->status_id === ContentTypeStatus::ENABLED->value;
    }

    /**
     * 静态工厂方法 - 创建新ContentType实例
     */
    public static function make(array $data = []): self
    {
        $instance = new static();
        $instance->fill($data);
        return $instance;
    }

    public function getStats(): array
    {
        $table = static::getTableName();
        $sql = "SELECT
                    COUNT(*) as total_content_types,
                    SUM(CASE WHEN status_id = :active_status THEN 1 ELSE 0 END) as active_content_types,
                    SUM(CASE WHEN status_id = :inactive_status THEN 1 ELSE 0 END) as inactive_content_types,
                    SUM(content_cnt) as total_content_count,
                    SUM(published_content_cnt) as total_published_content_count
                FROM {$table}";

        $result = $this->db->fetch($sql, [
            'active_status' => ContentTypeStatus::ENABLED->value,
            'inactive_status' => ContentTypeStatus::DISABLED->value
        ]);

        return [
            'total_content_types' => (int)$result['total_content_types'],
            'active_content_types' => (int)$result['active_content_types'],
            'inactive_content_types' => (int)$result['inactive_content_types'],
            'total_content_count' => (int)$result['total_content_count'],
            'total_published_content_count' => (int)$result['total_published_content_count']
        ];
    }

    /**
     * ============================================
     * 关系定义 - AR Pattern
     * ============================================
     */

    /**
     * 定义与 Content 的 HasMany 关系
     */
    public function contents(): \App\Core\Relations\HasMany
    {
        return $this->hasMany(\App\Models\Content::class, 'content_type_id', 'id');
    }

    /**
     * ============================================
     * 原有方法保持不变
     * ============================================
     */

    public function getRelatedContent(int $contentTypeId): array
    {
        $sql = "SELECT c.id, c.title_cn, c.title_en, c.content_type_id, c.status_id, c.pv_cnt, c.thumbnail
                FROM content c
                WHERE c.content_type_id = :content_type_id
                ORDER BY c.created_at DESC";

        return $this->db->fetchAll($sql, ['content_type_id' => $contentTypeId]);
    }

    /**
     * 根据英文名查找内容类型
     * @param string $nameEn 英文名称
     * @return array|null 内容类型数据或null
     */
    public static function findByNameEn(string $nameEn): ?array
    {
        $db = \App\Core\Database::getInstance();
        $sql = "SELECT * FROM " . static::getTableName() . " WHERE name_en = :name_en LIMIT 1";
        $result = $db->fetch($sql, ['name_en' => $nameEn]);

        return $result ?: null;
    }

    /**
     * 重写父类方法，为ContentType模型准备CSV导入数据
     *
     * @param array $csvRowData CSV行数据
     * @return array 处理后的数据
     */
    public function prepareBulkImportData(array $csvRowData): array
    {
        return [
            'name_cn' => $csvRowData['name_cn'] ?? '',
            'name_en' => $csvRowData['name_en'] ?? '',
            'status_id' => isset($csvRowData['status_id']) ? (int)$csvRowData['status_id'] : ContentTypeStatus::ENABLED->value,
            'sort_order' => isset($csvRowData['sort_order']) ? (int)$csvRowData['sort_order'] : 1,
            'content_cnt' => 0,
            'published_content_cnt' => 0
        ];
    }

    /**
     * 重写父类方法 - 为ContentType定义简单的验证逻辑
     *
     * @param array $data 导入数据
     * @return bool 是否验证通过
     */
    public function validateBulkImportData(array $data): bool
    {
        if (empty($data['name_cn']) || empty($data['name_en'])) {
            return false;
        }

        return true;
    }

    /**
     * 更新当前 ContentType 的内容统计数量
     * 统计 content_cnt 和 published_content_cnt
     *
     * @param int|null $contentTypeId ContentType ID, 如果为null则使用当前对象的ID
     * @return bool 是否更新成功
     */
    public function updateContentCnt(?int $contentTypeId = null): bool
    {
        // 确定要更新的 ContentType ID
        $targetId = $contentTypeId ?? $this->id;

        if (!$targetId) {
            return false;
        }

        try {
            // 统计总内容数量
            $totalCountSql = "SELECT COUNT(*) as cnt FROM content WHERE content_type_id = :content_type_id";
            $totalResult = $this->db->fetch($totalCountSql, ['content_type_id' => $targetId]);
            $totalCount = (int)($totalResult['cnt'] ?? 0);

            // 统计已发布内容数量
            $publishedCountSql = "SELECT COUNT(*) as cnt FROM content
                                  WHERE content_type_id = :content_type_id
                                  AND status_id = :published_status";
            $publishedResult = $this->db->fetch($publishedCountSql, [
                'content_type_id' => $targetId,
                'published_status' => ContentStatus::PUBLISHED->value
            ]);
            $publishedCount = (int)($publishedResult['cnt'] ?? 0);

            // 更新 ContentType 表
            $updateSql = "UPDATE " . static::getTableName() . "
                         SET content_cnt = :content_cnt,
                             published_content_cnt = :published_content_cnt,
                             updated_at = NOW()
                         WHERE id = :id";

            $this->db->query($updateSql, [
                'content_cnt' => $totalCount,
                'published_content_cnt' => $publishedCount,
                'id' => $targetId
            ]);

            // 如果是当前对象，更新属性
            if ($contentTypeId === null && isset($this->id) && $this->id == $targetId) {
                $this->attributes['content_cnt'] = $totalCount;
                $this->attributes['published_content_cnt'] = $publishedCount;
            }

            return true;

        } catch (\Exception $e) {
            error_log("UpdateContentCnt Error: " . $e->getMessage());
            return false;
        }
    }
}
