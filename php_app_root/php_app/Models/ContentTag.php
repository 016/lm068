<?php

namespace App\Models;

use App\Core\Model;

/**
 * ContentTag Model
 *
 * @property int $id 主键ID
 * @property int $content_id 关联内容ID
 * @property int $tag_id 标签ID
 * @property string $created_at 创建时间
 *
 * @property-read Content $content 关联的内容
 * @property-read Tag $tag 关联的标签
 */
class ContentTag extends Model
{
    protected static string $table = 'content_tag';
    protected $primaryKey = 'id';
    protected $fillable = [
        'default' => [
            'content_id', 'tag_id'
        ]
    ];
    protected $timestamps = false; // 该表只有created_at，没有updated_at

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
                'tag_id' => 'required|numeric'
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
            'content_id' => '内容ID',
            'tag_id' => '标签ID'
        ];
    }

    /**
     * ============================================
     * 关系定义 - AR Pattern
     * ============================================
     */

    /**
     * 定义与 Content 的 BelongsTo 关系
     */
    public function content(): \App\Core\Relations\BelongsTo
    {
        return $this->belongsTo(Content::class, 'content_id', 'id');
    }

    /**
     * 定义与 Tag 的 BelongsTo 关系
     */
    public function tag(): \App\Core\Relations\BelongsTo
    {
        return $this->belongsTo(Tag::class, 'tag_id', 'id');
    }
}
