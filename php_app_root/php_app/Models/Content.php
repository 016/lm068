<?php

namespace App\Models;

use App\Core\Model;
use App\Constants\ContentStatus;
use App\Constants\ContentType;
use App\Interfaces\HasStatuses;

class Content extends Model implements HasStatuses
{
    protected static string $table = 'content';
    protected $primaryKey = 'id';
    protected $fillable = [
        'content_type_id', 'author', 'title_en', 'title_cn',
        'desc_en', 'desc_cn', 'short_desc_en', 'short_desc_cn',
        'thumbnail', 'duration', 'pv_cnt', 'view_cnt', 'status_id'
    ];
    protected $timestamps = true;

    // 默认属性值
    protected array $defaults = [
        'content_type_id' => 21, // 默认为视频
        'author' => 'DP',
        'title_en' => '',
        'title_cn' => '',
        'desc_en' => '',
        'desc_cn' => '',
        'short_desc_en' => '',
        'short_desc_cn' => '',
        'thumbnail' => '',
        'duration' => '',
        'pv_cnt' => 0,
        'view_cnt' => 0,
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
        return ContentStatus::class;
    }

    /**
     * 定义验证规则
     * @param bool $isUpdate 是否为更新操作
     * @return array 验证规则
     */
    protected function rules(bool $isUpdate = false): array
    {
        return [
            'content_type_id' => 'required|numeric',
            'title_en' => 'required|max:255|unique',
            'title_cn' => 'required|max:255|unique',
            'desc_en' => 'max:65535', // TEXT类型
            'desc_cn' => 'max:65535', // TEXT类型
            'short_desc_en' => 'max:300',
            'short_desc_cn' => 'max:300',
            'author' => 'max:255',
            'thumbnail' => 'max:255',
            'duration' => 'max:10',
            'status_id' => 'numeric'
        ];
    }

    /**
     * 获取字段标签
     * @return array 字段标签映射
     */
    protected function getFieldLabels(): array
    {
        return [
            'content_type_id' => '内容类型',
            'author' => '作者',
            'title_en' => '英文标题',
            'title_cn' => '中文标题',
            'desc_en' => '英文描述',
            'desc_cn' => '中文描述',
            'short_desc_en' => '英文简介',
            'short_desc_cn' => '中文简介',
            'thumbnail' => '缩略图',
            'duration' => '时长',
            'status_id' => '状态'
        ];
    }

    /**
     * 获取显示标题（优先中文）
     */
    public function getDisplayTitle(): string
    {
        return $this->title_cn ?: $this->title_en;
    }

    /**
     * 获取显示描述（优先中文）
     */
    public function getDisplayDescription(): string
    {
        return $this->desc_cn ?: $this->desc_en;
    }

    /**
     * 获取显示简介（优先中文）
     */
    public function getDisplayShortDescription(): string
    {
        return $this->short_desc_cn ?: $this->short_desc_en;
    }

    /**
     * 获取状态标签
     */
    public function getStatusLabel(): string
    {
        if (isset($this->status_id)) {
            $status = ContentStatus::tryFrom($this->status_id);
            return $status ? $status->label() : '未知状态';
        }
        return '未设置';
    }

    /**
     * 获取内容类型标签
     */
    public function getContentTypeLabel(): string
    {
        if (isset($this->content_type_id)) {
            $type = ContentType::tryFrom($this->content_type_id);
            return $type ? $type->label() : '未知类型';
        }
        return '未设置';
    }

    /**
     * 检查是否已发布
     */
    public function isPublished(): bool
    {
        return $this->status_id === ContentStatus::PUBLISHED->value;
    }

    /**
     * 检查是否可见
     */
    public function isVisible(): bool
    {
        if (isset($this->status_id)) {
            $status = ContentStatus::tryFrom($this->status_id);
            return $status ? $status->isVisible() : false;
        }
        return false;
    }

    /**
     * 检查是否是视频类型
     */
    public function isVideoType(): bool
    {
        return $this->content_type_id === ContentType::VIDEO->value;
    }

    /**
     * 静态工厂方法 - 创建新Content实例
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
            throw new \Exception("Content with ID {$id} not found");
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
                    COUNT(*) as total_content,
                    SUM(CASE WHEN status_id = :published_status THEN 1 ELSE 0 END) as published_content,
                    SUM(CASE WHEN status_id = :draft_status THEN 1 ELSE 0 END) as draft_content,
                    SUM(view_cnt) as total_views,
                    AVG(view_cnt) as avg_views
                FROM {$table}";
        
        $result = $this->db->fetch($sql, [
            'published_status' => ContentStatus::PUBLISHED->value,
            'draft_status' => ContentStatus::DRAFT->value
        ]);
        
        return [
            'total_content' => (int)$result['total_content'],
            'published_content' => (int)$result['published_content'],
            'draft_content' => (int)$result['draft_content'],
            'total_views' => (int)$result['total_views'],
            'avg_views' => round((float)$result['avg_views'], 2)
        ];
    }

    /**
     * 获取关联标签
     */
    public function getRelatedTags(int $contentId): array
    {
        $sql = "SELECT t.id, t.name_cn, t.name_en, t.color_class, t.icon_class, t.status_id
                FROM tag t
                INNER JOIN content_tag ct ON t.id = ct.tag_id  
                WHERE ct.content_id = :content_id
                ORDER BY t.name_cn";
        
        return $this->db->fetchAll($sql, ['content_id' => $contentId]);
    }

    /**
     * 获取关联合集
     */
    public function getRelatedCollections(int $contentId): array
    {
        $sql = "SELECT c.id, c.name_cn, c.name_en, c.color_class, c.status_id
                FROM collection c
                INNER JOIN content_collection cc ON c.id = cc.collection_id  
                WHERE cc.content_id = :content_id
                ORDER BY c.name_cn";
        
        return $this->db->fetchAll($sql, ['content_id' => $contentId]);
    }

    /**
     * 关联标签
     */
    public function attachTag(int $contentId, int $tagId): bool
    {
        $sql = "INSERT IGNORE INTO content_tag (content_id, tag_id) VALUES (:content_id, :tag_id)";
        $this->db->query($sql, ['content_id' => $contentId, 'tag_id' => $tagId]);
        
        // 更新标签的内容计数
        $this->updateTagContentCount($tagId);
        return true;
    }

    /**
     * 移除标签关联
     */
    public function detachTag(int $contentId, int $tagId): bool
    {
        $sql = "DELETE FROM content_tag WHERE content_id = :content_id AND tag_id = :tag_id";
        $this->db->query($sql, ['content_id' => $contentId, 'tag_id' => $tagId]);
        
        // 更新标签的内容计数
        $this->updateTagContentCount($tagId);
        return true;
    }

    /**
     * 同步标签关联
     */
    public function syncTagAssociations(int $contentId, array $tagIds): bool
    {
        $this->db->beginTransaction();
        
        try {
            // 一次性读取所有现有的关联记录（包含主键ID和tag_id）
            $existingAssociations = $this->db->fetchAll(
                "SELECT id, tag_id FROM content_tag WHERE content_id = :content_id",
                ['content_id' => $contentId]
            );
            
            $oldTagIds = array_column($existingAssociations, 'tag_id');
            
            // 筛选需要删除和添加的标签
            $tagsToRemove = array_diff($oldTagIds, $tagIds);  // 需要删除的tag_id
            $tagsToAdd = array_diff($tagIds, $oldTagIds);     // 需要添加的tag_id
            
            // 找到需要删除的记录的主键ID
            $recordsToDelete = [];
            foreach ($existingAssociations as $association) {
                if (in_array($association['tag_id'], $tagsToRemove)) {
                    $recordsToDelete[] = $association['id'];
                }
            }
            
            // 用主键ID删除记录
            foreach ($recordsToDelete as $recordId) {
                $this->db->query("DELETE FROM content_tag WHERE id = :id", ['id' => $recordId]);
            }
            
            // 添加新关联
            foreach ($tagsToAdd as $tagId) {
                $this->db->query(
                    "INSERT INTO content_tag (content_id, tag_id) VALUES (:content_id, :tag_id)",
                    ['content_id' => $contentId, 'tag_id' => $tagId]
                );
            }

            // 更新所有相关标签的内容计数（只更新有变化的标签）
            $affectedTagIds = array_unique(array_merge($tagsToRemove, $tagsToAdd));
            foreach ($affectedTagIds as $tagId) {
                $this->updateTagContentCount($tagId);
            }
            
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    /**
     * 关联合集
     */
    public function attachCollection(int $contentId, int $collectionId): bool
    {
        $sql = "INSERT IGNORE INTO content_collection (content_id, collection_id) VALUES (:content_id, :collection_id)";
        $this->db->query($sql, ['content_id' => $contentId, 'collection_id' => $collectionId]);
        return true;
    }

    /**
     * 移除合集关联
     */
    public function detachCollection(int $contentId, int $collectionId): bool
    {
        $sql = "DELETE FROM content_collection WHERE content_id = :content_id AND collection_id = :collection_id";
        $this->db->query($sql, ['content_id' => $contentId, 'collection_id' => $collectionId]);
        return true;
    }

    /**
     * 同步合集关联
     */
    public function syncCollectionAssociations(int $contentId, array $collectionIds): bool
    {
        $this->db->beginTransaction();
        
        try {
            // 一次性读取所有现有的关联记录（包含主键ID和collection_id）
            $existingAssociations = $this->db->fetchAll(
                "SELECT id, collection_id FROM content_collection WHERE content_id = :content_id",
                ['content_id' => $contentId]
            );
            
            $oldCollectionIds = array_column($existingAssociations, 'collection_id');
            
            // 筛选需要删除和添加的合集
            $collectionsToRemove = array_diff($oldCollectionIds, $collectionIds);  // 需要删除的collection_id
            $collectionsToAdd = array_diff($collectionIds, $oldCollectionIds);     // 需要添加的collection_id
            
            // 找到需要删除的记录的主键ID
            $recordsToDelete = [];
            foreach ($existingAssociations as $association) {
                if (in_array($association['collection_id'], $collectionsToRemove)) {
                    $recordsToDelete[] = $association['id'];
                }
            }
            
            // 用主键ID删除记录
            foreach ($recordsToDelete as $recordId) {
                $this->db->query("DELETE FROM content_collection WHERE id = :id", ['id' => $recordId]);
            }
            
            // 添加新关联
            foreach ($collectionsToAdd as $collectionId) {
                $this->db->query(
                    "INSERT INTO content_collection (content_id, collection_id) VALUES (:content_id, :collection_id)",
                    ['content_id' => $contentId, 'collection_id' => $collectionId]
                );
            }

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    /**
     * 更新标签的内容计数
     */
    private function updateTagContentCount(int $tagId): bool
    {
        $sql = "UPDATE tag 
                SET content_cnt = (
                    SELECT COUNT(*) 
                    FROM content_tag 
                    WHERE tag_id = :tag_id1
                )
                WHERE id = :tag_id2";

        $this->db->query($sql, ['tag_id1' => $tagId, 'tag_id2' => $tagId]);
        return true;
    }

    /**
     * 增加观看次数
     */
    public function incrementViewCount(int $contentId): bool
    {
        $table = static::getTableName();
        $sql = "UPDATE {$table} SET view_cnt = view_cnt + 1 WHERE id = :id";
        $this->db->query($sql, ['id' => $contentId]);
        return true;
    }

    /**
     * 重写父类方法，为Content模型准备CSV导入数据
     * 
     * @param array $csvRowData CSV行数据
     * @return array 处理后的数据
     */
    public function prepareBulkImportData(array $csvRowData): array
    {
        return [
            'content_type_id' => isset($csvRowData['content_type_id']) ? (int)$csvRowData['content_type_id'] : ContentType::VIDEO->value,
            'author' => $csvRowData['author'] ?? 'DP',
            'title_en' => $csvRowData['title_en'] ?? '',
            'title_cn' => $csvRowData['title_cn'] ?? '',
            'desc_en' => $csvRowData['desc_en'] ?? '',
            'desc_cn' => $csvRowData['desc_cn'] ?? '',
            'short_desc_en' => $csvRowData['short_desc_en'] ?? '',
            'short_desc_cn' => $csvRowData['short_desc_cn'] ?? '',
            'thumbnail' => $csvRowData['thumbnail'] ?? '',
            'duration' => $csvRowData['duration'] ?? '',
            'status_id' => isset($csvRowData['status_id']) ? (int)$csvRowData['status_id'] : ContentStatus::DRAFT->value,
            'pv_cnt' => 0,
            'view_cnt' => 0
        ];
    }
}