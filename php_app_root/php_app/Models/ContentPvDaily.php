<?php

namespace App\Models;

use App\Core\Config;
use App\Core\Model;
use PDO;

class ContentPvDaily extends Model
{
    protected static string $table = 'content_pv_daily';
    protected $primaryKey = 'id';
    protected $fillable = [
        'content_type_id', 'author', 'code', 'title_en', 'title_cn',
        'desc_en', 'desc_cn', 'sum_en', 'sum_cn', 'short_desc_en', 'short_desc_cn',
        'thumbnail', 'duration', 'pv_cnt', 'view_cnt', 'status_id', 'pub_at'
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
     * @return array 验证规则
     */
    public function rules(bool $isUpdate = false): array
    {
        return [
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
     * 完整的每日统计流程（增量更新版本）
     * @throws \Exception
     */
    public function calculateDailyPVStatistics(string $statDate): array
    {
        $startTime = microtime(true);

        $tableName_contentPvLog = ContentPvLog::getTableName();
        // 1. 从日志表统计当日数据
        $sql = "
            SELECT content_id, ip
            FROM {$tableName_contentPvLog}
            WHERE accessed_at >= :start_date 
              AND accessed_at < :end_date
        ";

        $params = [
            'start_date' => $statDate . ' 00:00:00',
            'end_date' => date('Y-m-d', strtotime($statDate . ' +1 day')) . ' 00:00:00'
        ];

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        $stats = [];
        $totalPV = 0;

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $contentId = $row['content_id'];

            if (!isset($stats[$contentId])) {
                $stats[$contentId] = [
                    'pv_count' => 0,
                    'uv_ips' => []
                ];
            }

            $stats[$contentId]['pv_count']++;
            $totalPV++;

            if ($row['ip']) {
                $ipKey = bin2hex($row['ip']);
                $stats[$contentId]['uv_ips'][$ipKey] = 1;
            }
        }

        // 4. 批量插入结果
        $insertData = [];
        foreach ($stats as $contentId => $data) {
            $insertData[] = [
                'content_id' => $contentId,
                'stat_date' => $statDate,
                'pv_count' => $data['pv_count'],
                'uv_count' => count($data['uv_ips'])
            ];
        }

        // 2. 更新 content_pv_daily
        $this->batchUpsertDailyStats($insertData);

        // 3. 增量更新 content.pv_cnt（只更新今天有变化的）
        $todayContentIds = array_keys($stats);
        $this->updateContentTotalPVIncremental($todayContentIds);

        $execTime = microtime(true) - $startTime;

        return [
            'success' => true,
            'stat_date' => $statDate,
            'processed_contents' => count($stats),
            'total_pv' => $totalPV,
            'exec_time_seconds' => round($execTime, 2),
            'memory_peak_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2)
        ];
    }


    /**
     * 批量插入/更新统计数据
     * @throws \Exception
     */
    private function batchUpsertDailyStats(array $data): void
    {
        if (empty($data)) {
            return;
        }

        $this->db->beginTransaction();
        $tableName_contentPvDaily = ContentPvDaily::getTableName();

        try {
            // 使用 ON DUPLICATE KEY UPDATE 实现 upsert
            $sql = "
                INSERT INTO {$tableName_contentPvDaily} 
                (content_id, stat_date, pv_count, uv_count, created_at, updated_at) 
                VALUES 
                (:content_id, :stat_date, :pv_count, :uv_count, NOW(), NOW())
                ON DUPLICATE KEY UPDATE
                    pv_count = VALUES(pv_count),
                    uv_count = VALUES(uv_count),
                    updated_at = NOW()
            ";

            $stmt = $this->db->prepare($sql);

            foreach ($data as $row) {
                $stmt->execute([
                    'content_id' => $row['content_id'],
                    'stat_date' => $row['stat_date'],
                    'pv_count' => $row['pv_count'],
                    'uv_count' => $row['uv_count']
                ]);
            }

            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * 增量更新 content.pv_cnt
     * 只更新今天有变化的 content
     *
     * @param array $todayContentIds 今天有PV的 content_id 数组
     */
    private function updateContentTotalPVIncremental(array $todayContentIds): void
    {
        if (empty($todayContentIds)) {
            return;
        }

        $tableName_content = Content::getTableName();
        $placeholders = implode(',', array_fill(0, count($todayContentIds), '?'));

        $sql = "
            UPDATE {$tableName_content} c
            INNER JOIN (
                SELECT 
                    content_id,
                    SUM(pv_count) as total_pv
                FROM content_pv_daily
                WHERE content_id IN ({$placeholders})
                GROUP BY content_id
            ) cpd ON c.id = cpd.content_id
            SET 
                c.pv_cnt = cpd.total_pv,
                c.updated_at = NOW()
        ";

        $this->db->query($sql, $todayContentIds);
    }

    /**
     * 全量修复（每周执行一次，确保数据准确性）
     */
    public function fullRepairContentPV(): array
    {
        $startTime = microtime(true);

        $tableName_content = Content::getTableName();

        // 使用纯 SQL 全量更新
        $sql = "
            UPDATE {$tableName_content} c
            INNER JOIN (
                SELECT 
                    content_id,
                    SUM(pv_count) as total_pv
                FROM content_pv_daily
                GROUP BY content_id
            ) cpd ON c.id = cpd.content_id
            SET 
                c.pv_cnt = cpd.total_pv,
                c.updated_at = NOW()
        ";

        $this->db->query($sql);

        // 统计修复结果
        $result = $this->db->queryOne("
            SELECT 
                COUNT(*) as repaired_count,
                SUM(pv_cnt) as total_pv
            FROM {$tableName_content}
            WHERE pv_cnt > 0
        ");

        $execTime = microtime(true) - $startTime;

        return [
            'success' => true,
            'repaired_contents' => $result['repaired_count'] ?? 0,
            'total_pv' => $result['total_pv'] ?? 0,
            'exec_time_seconds' => round($execTime, 2)
        ];
    }

}