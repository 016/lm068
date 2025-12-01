<?php

namespace App\Models;

use App\Core\Config;
use App\Core\Model;

/**
 * ContentPvLog Model
 *
 * @property int $id 日志ID
 * @property int $content_id 关联内容ID
 * @property int|null $user_id 用户ID(可选,用于UV统计), 当前未启用用户系统
 * @property string|null $ip IPv4/IPv6 地址(可选,用于UV统计)
 * @property string $accessed_at 访问时间
 * @property int|null $device_type 设备类型: 1-Desktop, 2-Mobile, 3-Tablet, 4-Bot
 * @property int $os_family 操作系统类型枚举
 * @property int $browser_family 浏览器类型枚举
 * @property bool $is_bot 是否爬虫
 *
 * @property-read Content $content 关联内容
 */
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
     * ============================================
     * 关系定义 - AR Pattern
     * ============================================
     */

    /**
     * 定义与 Content 的 BelongsTo 关系
     */
    public function content(): \App\Core\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Content::class, 'content_id', 'id');
    }

    /**
     * ============================================
     * 原有方法保持不变
     * ============================================
     */

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