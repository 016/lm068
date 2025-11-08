<?php

namespace App\Models;

use App\Core\Model;

/**
 * ContentCollection Model
 *
 * @property int $id 主键ID
 * @property int $collection_id 合集ID
 * @property int $content_id 关联内容ID
 * @property string $created_at 创建时间
 *
 * @property-read Collection $collection 关联的合集
 * @property-read Content $content 关联的内容
 */
class ContentCollection extends Model
{
    protected static string $table = 'content_collection';
    protected $primaryKey = 'id';
    protected $fillable = [
        'default' => [
            'collection_id', 'content_id'
        ]
    ];
    protected $timestamps = false; // 该表只有created_at，没有updated_at

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
                'collection_id' => 'required|numeric',
                'content_id' => 'required|numeric'
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
            'collection_id' => '合集ID',
            'content_id' => '内容ID'
        ];
    }

    /**
     * ============================================
     * 关系定义 - AR Pattern
     * ============================================
     */

    /**
     * 定义与 Collection 的 BelongsTo 关系
     */
    public function collection(): \App\Core\Relations\BelongsTo
    {
        return $this->belongsTo(Collection::class, 'collection_id', 'id');
    }

    /**
     * 定义与 Content 的 BelongsTo 关系
     */
    public function content(): \App\Core\Relations\BelongsTo
    {
        return $this->belongsTo(Content::class, 'content_id', 'id');
    }
}
