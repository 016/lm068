<?php

namespace App\Models;

use App\Core\Model;

class Content extends Model
{
    protected $table = 'content';
    protected $fillable = [
        'content_type_id', 'author', 'title_en', 'title_cn',
        'desc_en', 'desc_cn', 'short_desc_en', 'short_desc_cn',
        'thumbnail', 'duration', 'status_id'
    ];

    public function getPublishedVideos(int $limit = 20, int $offset = 0): array
    {
        return $this->findAll([
            'content_type_id' => 21,
            'status_id' => 99
        ], $limit, $offset, 'created_at DESC');
    }

    public function getPublishedArticles(int $limit = 20, int $offset = 0): array
    {
        return $this->findAll([
            'content_type_id' => 11,
            'status_id' => 99
        ], $limit, $offset, 'created_at DESC');
    }

    public function incrementViewCount(int $id): bool
    {
        $sql = "UPDATE {$this->table} SET view_cnt = view_cnt + 1, pv_cnt = pv_cnt + 1 WHERE id = :id";
        $this->db->query($sql, ['id' => $id]);
        return true;
    }

    public function getContentWithTags(int $id): ?array
    {
        $sql = "
            SELECT c.*, GROUP_CONCAT(t.name_cn) as tags_cn, GROUP_CONCAT(t.name_en) as tags_en
            FROM {$this->table} c
            LEFT JOIN content_tag ct ON c.id = ct.content_id
            LEFT JOIN tag t ON ct.tag_id = t.id
            WHERE c.id = :id
            GROUP BY c.id
        ";
        return $this->db->fetch($sql, ['id' => $id]);
    }
}