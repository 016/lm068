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

}