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

    /**
     * 定义验证规则
     * @param bool $isUpdate 是否为更新操作
     * @return array 验证规则
     */
    protected function rules(bool $isUpdate = false): array
    {
        return [
            'name_cn' => 'required|max:50|unique',
            'name_en' => 'required|max:50',
            'short_desc_cn' => 'max:100',
            'short_desc_en' => 'max:100',
            'desc_cn' => 'max:500',
            'desc_en' => 'max:500',
            'color_class' => 'max:50',
            'icon_class' => 'max:50',
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
            'name_cn' => '中文名称',
            'name_en' => '英文名称',
            'short_desc_cn' => '中文简介',
            'short_desc_en' => '英文简介',
            'desc_cn' => '中文描述',
            'desc_en' => '英文描述',
            'color_class' => '颜色样式',
            'icon_class' => '图标样式',
            'status_id' => '状态'
        ];
    }

    /**
     * 根据过滤条件获取所有标签数据（不分页，用于JS处理）
     * 支持多字段搜索过滤
     */
    public function findAllWithFilters(array $filters = []): array
    {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];
        $whereConditions = [];

        // ID 搜索
        if (!empty($filters['id'])) {
            $whereConditions[] = "id = :id";
            $params['id'] = (int)$filters['id'];
        }

        // 名称搜索（中文或英文）
        if (!empty($filters['name'])) {
            $whereConditions[] = "(name_cn LIKE :name OR name_en LIKE :name)";
            $params['name'] = "%" . $filters['name'] . "%";
        }

        // 关联内容数量范围搜索
        if (!empty($filters['content_cnt'])) {
            // 支持范围搜索，如 "10-50" 或单个数字
            $contentCnt = $filters['content_cnt'];
            if (strpos($contentCnt, '-') !== false) {
                $range = explode('-', $contentCnt);
                if (count($range) == 2) {
                    $whereConditions[] = "content_cnt BETWEEN :content_cnt_min AND :content_cnt_max";
                    $params['content_cnt_min'] = (int)trim($range[0]);
                    $params['content_cnt_max'] = (int)trim($range[1]);
                }
            } else {
                $whereConditions[] = "content_cnt = :content_cnt";
                $params['content_cnt'] = (int)$contentCnt;
            }
        }

        // 图标class搜索
        if (!empty($filters['icon_class'])) {
            $whereConditions[] = "icon_class LIKE :icon_class";
            $params['icon_class'] = "%" . $filters['icon_class'] . "%";
        }

        // 状态过滤
        if ($filters['status'] !== null && $filters['status'] !== '') {
            $whereConditions[] = "status_id = :status";
            $params['status'] = (int)$filters['status'];
        }

        if (!empty($whereConditions)) {
            $sql .= " WHERE " . implode(" AND ", $whereConditions);
        }

        // 排序
        $orderBy = $filters['order_by'] ?? 'created_at DESC';
        $sql .= " ORDER BY {$orderBy}";

        return $this->db->fetchAll($sql, $params);
    }

    // 保留原有方法以向后兼容
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