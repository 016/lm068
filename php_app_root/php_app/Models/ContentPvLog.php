<?php

namespace App\Models;

use App\Core\Config;
use App\Core\Model;

class ContentPvLog extends Model
{
    protected static string $table = 'content_pv_log';
    protected $primaryKey = 'id';
    protected $fillable = [
        'default' => [
//        'content_type_id', 'author', 'code', 'title_en', 'title_cn',
//        'desc_en', 'desc_cn', 'sum_en', 'sum_cn', 'short_desc_en', 'short_desc_cn',
//        'thumbnail', 'duration', 'pv_cnt', 'view_cnt', 'status_id', 'pub_at'
        ]
    ];
    protected $timestamps = true;

    // 默认属性值
    protected array $defaults = [
//        'content_type_id' => 21, // 默认为视频
    ];

    public function __construct()
    {
        parent::__construct();
        // 设置默认值
    }

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
//            'content_type_id' => '内容类型',
        ];
    }

}