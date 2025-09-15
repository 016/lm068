<?php

namespace App\Models;

use App\Core\Model;

class Tag extends Model
{
    protected $table = 'tag';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name_en', 'name_cn', 'short_desc_en', 'short_desc_cn', 
        'desc_en', 'desc_cn', 'color_class', 'icon_class', 
        'content_cnt', 'status_id'
    ];
    protected $timestamps = true;

    public function findAllWithPagination(int $page = 1, int $perPage = 10, array $conditions = [], ?string $search = null, ?string $orderBy = null): array
    {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT * FROM {$this->table}";
        $params = [];
        $whereConditions = [];

        if (!empty($conditions)) {
            foreach ($conditions as $field => $value) {
                $whereConditions[] = "{$field} = :{$field}";
                $params[$field] = $value;
            }
        }

        if ($search) {
            $whereConditions[] = "(name_cn LIKE :search OR name_en LIKE :search OR short_desc_cn LIKE :search OR short_desc_en LIKE :search)";
            $params['search'] = "%{$search}%";
        }

        if (!empty($whereConditions)) {
            $sql .= " WHERE " . implode(" AND ", $whereConditions);
        }

        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        } else {
            $sql .= " ORDER BY created_at DESC";
        }

        $sql .= " LIMIT {$perPage} OFFSET {$offset}";

        return $this->db->fetchAll($sql, $params);
    }

    public function countWithConditions(array $conditions = [], ?string $search = null): int
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        $params = [];
        $whereConditions = [];

        if (!empty($conditions)) {
            foreach ($conditions as $field => $value) {
                $whereConditions[] = "{$field} = :{$field}";
                $params[$field] = $value;
            }
        }

        if ($search) {
            $whereConditions[] = "(name_cn LIKE :search OR name_en LIKE :search OR short_desc_cn LIKE :search OR short_desc_en LIKE :search)";
            $params['search'] = "%{$search}%";
        }

        if (!empty($whereConditions)) {
            $sql .= " WHERE " . implode(" AND ", $whereConditions);
        }

        $result = $this->db->fetch($sql, $params);
        return (int)$result['count'];
    }

    public function getStats(): array
    {
        $sql = "SELECT 
                    COUNT(*) as total_tags,
                    SUM(CASE WHEN status_id = 1 THEN 1 ELSE 0 END) as active_tags,
                    SUM(CASE WHEN status_id = 0 THEN 1 ELSE 0 END) as inactive_tags,
                    SUM(content_cnt) as total_content_associations
                FROM {$this->table}";
        
        $result = $this->db->fetch($sql);
        
        return [
            'total_tags' => (int)$result['total_tags'],
            'active_tags' => (int)$result['active_tags'],
            'inactive_tags' => (int)$result['inactive_tags'],
            'total_content_associations' => (int)$result['total_content_associations']
        ];
    }

    public function getRelatedContent(int $tagId): array
    {
        $sql = "SELECT c.id, c.title_cn, c.title_en, c.content_type_id, c.status_id, c.view_cnt, c.thumbnail
                FROM content c
                INNER JOIN content_tag ct ON c.id = ct.content_id  
                WHERE ct.tag_id = :tag_id
                ORDER BY c.created_at DESC";
        
        return $this->db->fetchAll($sql, ['tag_id' => $tagId]);
    }

    public function updateContentCount(int $tagId): bool
    {
        $sql = "UPDATE {$this->table} 
                SET content_cnt = (
                    SELECT COUNT(*) 
                    FROM content_tag 
                    WHERE tag_id = :tag_id1
                )
                WHERE id = :tag_id2";

        $this->db->query($sql, ['tag_id1' => $tagId, 'tag_id2' => $tagId]);
        return true;
    }

    public function attachContent(int $tagId, int $contentId): bool
    {
        $sql = "INSERT IGNORE INTO content_tag (tag_id, content_id) VALUES (:tag_id, :content_id)";
        $this->db->query($sql, ['tag_id' => $tagId, 'content_id' => $contentId]);
        
        $this->updateContentCount($tagId);
        return true;
    }

    public function detachContent(int $tagId, int $contentId): bool
    {
        $sql = "DELETE FROM content_tag WHERE tag_id = :tag_id AND content_id = :content_id";
        $this->db->query($sql, ['tag_id' => $tagId, 'content_id' => $contentId]);
        
        $this->updateContentCount($tagId);
        return true;
    }

    public function syncContentAssociations(int $tagId, array $contentIds): bool
    {
        $this->db->beginTransaction();
        
        try {
            $this->db->query("DELETE FROM content_tag WHERE tag_id = :tag_id", ['tag_id' => $tagId]);
            
            foreach ($contentIds as $contentId) {
                $this->db->query(
                    "INSERT INTO content_tag (tag_id, content_id) VALUES (:tag_id, :content_id)",
                    ['tag_id' => $tagId, 'content_id' => $contentId]
                );
            }

            $this->updateContentCount($tagId);
            $this->db->commit();
            
            return true;
        } catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function bulkUpdateStatus(array $tagIds, int $status): bool
    {
        if (empty($tagIds)) {
            return false;
        }
        
        $placeholders = implode(',', array_fill(0, count($tagIds), '?'));
        $sql = "UPDATE {$this->table} SET status_id = ?, updated_at = NOW() WHERE id IN ({$placeholders})";
        
        $params = array_merge([$status], $tagIds);
        $this->db->query($sql, $params);
        
        return true;
    }

    public function bulkDelete(array $tagIds): bool
    {
        if (empty($tagIds)) {
            return false;
        }
        
        $this->db->beginTransaction();
        
        try {
            $placeholders = implode(',', array_fill(0, count($tagIds), '?'));
            
            $this->db->query("DELETE FROM content_tag WHERE tag_id IN ({$placeholders})", $tagIds);
            
            $this->db->query("DELETE FROM {$this->table} WHERE id IN ({$placeholders})", $tagIds);
            
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
}