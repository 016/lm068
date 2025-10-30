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
     * 每日 PV 统计（增量更新）
     * Crontab: 10 0 * * *
     * @throws \Exception
     */
    public function dailyPVCal(): void
    {

        set_time_limit(600);
        ini_set('memory_limit', '512M');

        $statDate = $_GET['date'] ?? date('Y-m-d', strtotime('-1 day'));

//        var_dump($statDate);
//        exit;

        $contentModel = new ContentPvDaily();
        $result = $contentModel->calculateDailyStatistics($statDate);
        $result['run-date'] = date('Y-m-d h:i:s');

        header('Content-Type: application/json');
        echo json_encode($result, JSON_PRETTY_PRINT);
    }

    /**
     * 全量修复 content.pv_cnt
     * Crontab: 0 3 * * 0  (每周日凌晨3点)
     */
    public function repairFullPVCal(): void
    {
        set_time_limit(600);

        $contentModel = new ContentPvDaily();
        $result = $contentModel->fullRepairContentPV();
        $result['run-date'] = date('Y-m-d h:i:s');

        header('Content-Type: application/json');
        echo json_encode($result, JSON_PRETTY_PRINT);
    }



}