<?php

namespace App\Models;

use App\Core\Config;
use App\Core\Model;
use PDO;

class SiteStatsDaily extends Model
{
    protected static string $table = 'site_stats_daily';
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

    /**
     * 插入/更新全站每日统计数据
     * @param string $statDate 统计日期 (Y-m-d)
     * @param array $stats 统计数据
     * @throws \Exception
     */
    public function upsertSiteDailyStats(string $statDate, array $stats): void
    {
        $tableName = self::getTableName();

        $sql = "
            INSERT INTO {$tableName} 
            (
                stat_date, 
                pv_count, 
                uv_count, 
                desktop_pv, 
                mobile_pv, 
                tablet_pv, 
                bot_pv,
                created_at, 
                updated_at
            ) 
            VALUES 
            (
                :stat_date, 
                :pv_count, 
                :uv_count, 
                :desktop_pv, 
                :mobile_pv, 
                :tablet_pv, 
                :bot_pv,
                NOW(), 
                NOW()
            )
            ON DUPLICATE KEY UPDATE
                pv_count = VALUES(pv_count),
                uv_count = VALUES(uv_count),
                desktop_pv = VALUES(desktop_pv),
                mobile_pv = VALUES(mobile_pv),
                tablet_pv = VALUES(tablet_pv),
                bot_pv = VALUES(bot_pv),
                updated_at = NOW()
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'stat_date' => $statDate,
            'pv_count' => $stats['pv_count'] ?? 0,
            'uv_count' => $stats['uv_count'] ?? 0,
            'desktop_pv' => $stats['desktop_pv'] ?? null,
            'mobile_pv' => $stats['mobile_pv'] ?? null,
            'tablet_pv' => $stats['tablet_pv'] ?? null,
            'bot_pv' => $stats['bot_pv'] ?? null
        ]);
    }

    /**
     * 获取指定日期的全站统计
     * @param string $statDate
     * @return array|null
     */
    public function getStatsByDate(string $statDate): ?array
    {
        $tableName = self::getTableName();

        $sql = "
            SELECT * 
            FROM {$tableName}
            WHERE stat_date = :stat_date
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['stat_date' => $statDate]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * 获取日期范围内的全站统计
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function getStatsByDateRange(string $startDate, string $endDate): array
    {
        $tableName = self::getTableName();

        $sql = "
            SELECT * 
            FROM {$tableName}
            WHERE stat_date >= :start_date 
              AND stat_date <= :end_date
            ORDER BY stat_date DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}