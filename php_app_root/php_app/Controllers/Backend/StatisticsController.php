<?php

namespace App\Controllers\Backend;

use App\Constants\CollectionStatus;
use App\Constants\ContentStatus;
use App\Constants\ContentType;
use App\Constants\TagStatus;
use App\Core\Config;
use App\Helpers\UrlHelper;
use App\Models\Collection;
use App\Models\Content;
use App\Models\ContentPvDaily;
use App\Models\SiteStatsDaily;
use App\Models\Tag;
use PDO;

class StatisticsController extends BackendController
{
    /**
     * 定义 before action 过滤器配置
     * 子类重写此方法来配置过滤器
     *
     * @return array 过滤器配置数组
     */
    protected function beforeActionFilters(): array
    {
        //return [] for skip auth check
        return [];

    }

    /**
     * 每日 PV 统计（增量更新 + 自动补全）
     * Crontab: 10 0 * * *
     * @throws \Exception
     */
    public function dailyPVCal(): void
    {
        set_time_limit(600);
        ini_set('memory_limit', '512M');

        $results = [];

        // 如果指定日期，只统计指定日期
        if (isset($_GET['date'])) {
            $statDate = $_GET['date'];
            $contentPvDailyModel = new ContentPvDaily();
            $result = $contentPvDailyModel->calculateDailyStatistics($statDate);
            $results[] = $result;
        } else {
            // 默认模式：检查并补全前 3 天的缺失数据
            $checkDays = 3;
            $contentPvDailyModel = new ContentPvDaily();
            $siteStatsDailyModel = new SiteStatsDaily();

            for ($i = 1; $i <= $checkDays; $i++) {
                $checkDate = date('Y-m-d', strtotime("-{$i} day"));

                // 检查该日期是否已有统计数据
                $existingStats = $siteStatsDailyModel->getStatsByDate($checkDate);

                if (!$existingStats) {
                    // 缺失数据，自动补全
                    try {
                        $result = $contentPvDailyModel->calculateDailyStatistics($checkDate);
                        $result['auto_filled'] = true;
                        $result['reason'] = 'Missing data detected';
                        $results[] = $result;
                    } catch (\Exception $e) {
                        $results[] = [
                            'success' => false,
                            'stat_date' => $checkDate,
                            'error' => $e->getMessage(),
                            'auto_filled' => true
                        ];
                    }
                } else {
                    // 数据已存在，跳过（但如果是昨天，仍然更新）
                    if ($i === 1) {
                        $result = $contentPvDailyModel->calculateDailyStatistics($checkDate);
                        $result['auto_filled'] = false;
                        $result['reason'] = 'Daily routine update';
                        $results[] = $result;
                    }
                }
            }
        }

        $output = [
            'run_time' => date('Y-m-d H:i:s'),
            'total_processed' => count($results),
            'details' => $results
        ];

        header('Content-Type: application/json');
        echo json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * 全量修复 content.pv_cnt
     * Crontab: 0 3 * * 0  (每周日凌晨3点)
     */
    public function repairFullPVCal(): void
    {
        set_time_limit(600);

        $contentPvDailyModel = new ContentPvDaily();
        $result = $contentPvDailyModel->fullRepairContentPV();
        $result['run-date'] = date('Y-m-d h:i:s');

        header('Content-Type: application/json');
        echo json_encode($result, JSON_PRETTY_PRINT);
    }



}