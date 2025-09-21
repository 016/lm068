<?php

namespace App\Models;

use App\Core\Model;
use App\Constants\CollectionStatus;
use App\Interfaces\HasStatuses;

class Collection extends Model implements HasStatuses
{
    protected $table = 'collection';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name_en', 'name_cn', 'short_desc_en', 'short_desc_cn', 
        'desc_en', 'desc_cn', 'color_class', 'icon_class', 
        'content_cnt', 'status_id'
    ];
    protected $timestamps = true;
    /**
     * 实现接口方法，返回对应的状态枚举类
     */
    public static function getStatusEnum(): string
    {
        return CollectionStatus::class;
    }

    public function getStats(): array
    {
        $sql = "SELECT 
                    COUNT(*) as total_collections,
                    SUM(CASE WHEN status_id = :active_status THEN 1 ELSE 0 END) as active_collections,
                    SUM(CASE WHEN status_id = :inactive_status THEN 1 ELSE 0 END) as inactive_collections,
                    SUM(content_cnt) as total_content_associations
                FROM {$this->table}";
        
        $result = $this->db->fetch($sql, [
            'active_status' => CollectionStatus::ENABLED->value,
            'inactive_status' => CollectionStatus::DISABLED->value
        ]);
        
        return [
            'total_collections' => (int)$result['total_collections'],
            'active_collections' => (int)$result['active_collections'],
            'inactive_collections' => (int)$result['inactive_collections'],
            'total_content_associations' => (int)$result['total_content_associations']
        ];
    }

    public function getRelatedContent(int $collectionId): array
    {
        $sql = "SELECT c.id, c.title_cn, c.title_en, c.content_type_id, c.status_id, c.view_cnt, c.thumbnail
                FROM content c
                INNER JOIN content_collection cc ON c.id = cc.content_id  
                WHERE cc.collection_id = :collection_id
                ORDER BY c.created_at DESC";
        
        return $this->db->fetchAll($sql, ['collection_id' => $collectionId]);
    }

    public function updateContentCount(int $collectionId): bool
    {
        $sql = "UPDATE {$this->table} 
                SET content_cnt = (
                    SELECT COUNT(*) 
                    FROM content_collection 
                    WHERE collection_id = :collection_id
                )
                WHERE id = :collection_id";
        
        $this->db->query($sql, ['collection_id' => $collectionId]);
        return true;
    }

    public function attachContent(int $collectionId, int $contentId): bool
    {
        $sql = "INSERT IGNORE INTO content_collection (collection_id, content_id) VALUES (:collection_id, :content_id)";
        $this->db->query($sql, ['collection_id' => $collectionId, 'content_id' => $contentId]);
        
        $this->updateContentCount($collectionId);
        return true;
    }

    public function detachContent(int $collectionId, int $contentId): bool
    {
        $sql = "DELETE FROM content_collection WHERE collection_id = :collection_id AND content_id = :content_id";
        $this->db->query($sql, ['collection_id' => $collectionId, 'content_id' => $contentId]);
        
        $this->updateContentCount($collectionId);
        return true;
    }

    public function syncContentAssociations(int $collectionId, array $contentIds): bool
    {
        $this->db->beginTransaction();
        
        try {
            $this->db->query("DELETE FROM content_collection WHERE collection_id = :collection_id", ['collection_id' => $collectionId]);
            
            foreach ($contentIds as $contentId) {
                $this->db->query(
                    "INSERT INTO content_collection (collection_id, content_id) VALUES (:collection_id, :content_id)",
                    ['collection_id' => $collectionId, 'content_id' => $contentId]
                );
            }
            
            $this->updateContentCount($collectionId);
            $this->db->commit();
            
            return true;
        } catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    /**
     * 重写父类方法，为Collection模型准备CSV导入数据
     * 
     * @param array $csvRowData CSV行数据
     * @return array 处理后的数据
     */
    public function prepareBulkImportData(array $csvRowData): array
    {
        return [
            'name_cn' => $csvRowData['name_cn'] ?? '',
            'name_en' => $csvRowData['name_en'] ?? '',
            'short_desc_cn' => $csvRowData['short_desc_cn'] ?? '',
            'short_desc_en' => $csvRowData['short_desc_en'] ?? '',
            'desc_cn' => $csvRowData['desc_cn'] ?? '',
            'desc_en' => $csvRowData['desc_en'] ?? '',
            'color_class' => $csvRowData['color_class'] ?? 'btn-outline-primary',
            'icon_class' => $csvRowData['icon_class'] ?? 'bi-collection',
            'status_id' => isset($csvRowData['status_id']) ? (int)$csvRowData['status_id'] : CollectionStatus::ENABLED->value,
            'content_cnt' => 0
        ];
    }

    /**
     * 重写父类方法 - 为Collection定义简单的验证逻辑
     * 
     * @param array $data 导入数据
     * @return bool 是否验证通过
     */
    public function validateBulkImportData(array $data): bool
    {
        // Collection没有定义rules方法，所以我们使用简单的验证
        if (empty($data['name_cn']) || empty($data['name_en'])) {
            return false;
        }
        
        return true;
    }

}