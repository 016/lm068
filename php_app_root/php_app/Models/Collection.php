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

    public function findAllWithSearchConditions(array $conditions = [], array $searchConditions = []): array
    {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];
        $whereConditions = [];

        // 处理基本条件（如status_id）
        if (!empty($conditions)) {
            foreach ($conditions as $field => $value) {
                $whereConditions[] = "{$field} = :{$field}";
                $params[$field] = $value;
            }
        }

        // 处理搜索条件
        if (!empty($searchConditions)) {
            foreach ($searchConditions as $field => $value) {
                switch ($field) {
                    case 'id':
                        $whereConditions[] = "id = :search_id";
                        $params['search_id'] = (int)$value;
                        break;
                    case 'name':
                        $whereConditions[] = "(name_cn LIKE :search_name OR name_en LIKE :search_name)";
                        $params['search_name'] = "%{$value}%";
                        break;
                    case 'description':
                        $whereConditions[] = "(short_desc_cn LIKE :search_desc OR short_desc_en LIKE :search_desc OR desc_cn LIKE :search_desc OR desc_en LIKE :search_desc)";
                        $params['search_desc'] = "%{$value}%";
                        break;
                    case 'content_cnt':
                        // 处理数量范围搜索，支持格式如 "5-10" 或 ">5" 或 "10"
                        if (strpos($value, '-') !== false) {
                            $range = explode('-', $value);
                            if (count($range) === 2 && is_numeric($range[0]) && is_numeric($range[1])) {
                                $whereConditions[] = "content_cnt BETWEEN :cnt_min AND :cnt_max";
                                $params['cnt_min'] = (int)$range[0];
                                $params['cnt_max'] = (int)$range[1];
                            }
                        } elseif (preg_match('/^([><=]+)(\d+)$/', $value, $matches)) {
                            $operator = $matches[1];
                            $number = (int)$matches[2];
                            if (in_array($operator, ['>', '<', '>=', '<=', '='])) {
                                $whereConditions[] = "content_cnt {$operator} :cnt_value";
                                $params['cnt_value'] = $number;
                            }
                        } elseif (is_numeric($value)) {
                            $whereConditions[] = "content_cnt = :cnt_exact";
                            $params['cnt_exact'] = (int)$value;
                        }
                        break;
                    case 'icon_class':
                        $whereConditions[] = "icon_class LIKE :search_icon";
                        $params['search_icon'] = "%{$value}%";
                        break;
                }
            }
        }

        if (!empty($whereConditions)) {
            $sql .= " WHERE " . implode(" AND ", $whereConditions);
        }

        $sql .= " ORDER BY created_at DESC";

        return $this->db->fetchAll($sql, $params);
    }

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