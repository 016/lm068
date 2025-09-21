<?php

namespace App\Models;

use App\Core\Model;

class Collection extends Model
{
    protected $table = 'collection';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name_en', 'name_cn', 'short_desc_en', 'short_desc_cn', 
        'desc_en', 'desc_cn', 'color_class', 'icon_class', 
        'content_cnt', 'status_id'
    ];
    protected $timestamps = true;

    public function getStats(): array
    {
        $sql = "SELECT 
                    COUNT(*) as total_collections,
                    SUM(CASE WHEN status_id = 1 THEN 1 ELSE 0 END) as active_collections,
                    SUM(CASE WHEN status_id = 0 THEN 1 ELSE 0 END) as inactive_collections,
                    SUM(content_cnt) as total_content_associations
                FROM {$this->table}";
        
        $result = $this->db->fetch($sql);
        
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

    public function bulkUpdateStatus(array $collectionIds, int $status): bool
    {
        if (empty($collectionIds)) {
            return false;
        }
        
        $placeholders = implode(',', array_fill(0, count($collectionIds), '?'));
        $sql = "UPDATE {$this->table} SET status_id = ?, updated_at = NOW() WHERE id IN ({$placeholders})";
        
        $params = array_merge([$status], $collectionIds);
        $this->db->query($sql, $params);
        
        return true;
    }

    public function bulkDelete(array $collectionIds): bool
    {
        if (empty($collectionIds)) {
            return false;
        }
        
        $this->db->beginTransaction();
        
        try {
            $placeholders = implode(',', array_fill(0, count($collectionIds), '?'));
            
            $this->db->query("DELETE FROM content_collection WHERE collection_id IN ({$placeholders})", $collectionIds);
            
            $this->db->query("DELETE FROM {$this->table} WHERE id IN ({$placeholders})", $collectionIds);
            
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
}