<?php

namespace App\Controllers\Backend;

use App\Core\Request;
use App\Models\Content;
use App\Constants\ContentStatus;

class DashboardController extends BackendController
{
    public function index(Request $request): void
    {
        // 获取metrics-overview数据
        $metricsData = $this->getMetricsOverviewData();

        // 获取content-grid数据
        $contentGridData = $this->getContentGridData();

        $this->render('dashboard.index', [
            'title' => '管理后台 - 数据面板',
            'metrics' => $metricsData,
            'contentGrid' => $contentGridData,
            'css_files' => ['dashboard.css'],
            'js_files' => ['dashboard.js']
        ]);
    }

    /**
     * 获取metrics-overview区块数据
     */
    private function getMetricsOverviewData(): array
    {
        $contentModel = new Content();

        // 总视频数
        $totalVideos = Content::count();

        // 本月新增视频数
        $firstDayOfMonth = date('Y-m-01 00:00:00');
        $sql = "SELECT COUNT(*) as count FROM " . Content::getTableName() . " WHERE created_at >= :first_day_of_month";
        $result = $contentModel->query($sql, ['first_day_of_month' => $firstDayOfMonth])->fetch(\PDO::FETCH_ASSOC);
        $monthlyNewVideos = (int)$result['count'];

        // 计算本月增长百分比
        $monthlyGrowthRate = $totalVideos > 0
            ? round(($monthlyNewVideos / $totalVideos) * 100, 1)
            : 0;

        // 注册用户总数
        $sql = "SELECT COUNT(*) as count FROM user";
        $result = $contentModel->query($sql, [])->fetch(\PDO::FETCH_ASSOC);
        $totalUsers = (int)$result['count'];

        // 本月新增注册用户
        $sql = "SELECT COUNT(*) as count FROM user WHERE created_at >= :first_day_of_month";
        $result = $contentModel->query($sql, ['first_day_of_month' => $firstDayOfMonth])->fetch(\PDO::FETCH_ASSOC);
        $monthlyNewUsers = (int)$result['count'];

        // 计算用户本月增长百分比
        $userMonthlyGrowthRate = $totalUsers > 0
            ? round(($monthlyNewUsers / $totalUsers) * 100, 1)
            : 0;

        // 邮件订阅者总数 (status_id = 1 表示已订阅)
        $sql = "SELECT COUNT(*) as count FROM subscription WHERE status_id = 1";
        $result = $contentModel->query($sql, [])->fetch(\PDO::FETCH_ASSOC);
        $totalSubscribers = (int)$result['count'];

        // 本月新增邮件订阅者
        $sql = "SELECT COUNT(*) as count FROM subscription WHERE status_id = 1 AND created_at >= :first_day_of_month";
        $result = $contentModel->query($sql, ['first_day_of_month' => $firstDayOfMonth])->fetch(\PDO::FETCH_ASSOC);
        $monthlyNewSubscribers = (int)$result['count'];

        // 计算订阅者本月增长百分比
        $subscriberMonthlyGrowthRate = $totalSubscribers > 0
            ? round(($monthlyNewSubscribers / $totalSubscribers) * 100, 1)
            : 0;

        return [
            'total_videos' => $totalVideos,
            'monthly_new_videos' => $monthlyNewVideos,
            'monthly_growth_rate' => $monthlyGrowthRate,
            // 总观看次数无需读取用0表示
            'total_views' => 0,
            'total_users' => $totalUsers,
            'monthly_new_users' => $monthlyNewUsers,
            'user_monthly_growth_rate' => $userMonthlyGrowthRate,
            'total_subscribers' => $totalSubscribers,
            'monthly_new_subscribers' => $monthlyNewSubscribers,
            'subscriber_monthly_growth_rate' => $subscriberMonthlyGrowthRate
        ];
    }

    /**
     * 获取content-grid区块数据
     */
    private function getContentGridData(): array
    {
        $contentModel = new Content();

        // 视频状态统计
        $publishedCount = Content::count([
            'status_id' => ContentStatus::PUBLISHED->value
        ]);

        $pendingPublishCount = Content::count([
            'status_id' => ContentStatus::PENDING_PUBLISH->value
        ]);

        $shootingDoneCount = Content::count([
            'status_id' => ContentStatus::SHOOTING_DONE->value
        ]);

        $scriptDoneCount = Content::count([
            'status_id' => ContentStatus::SCRIPT_DONE->value
        ]);

        // 评论统计 (status_id: 0-已隐藏, 1-待审核, 99-审核通过)
        // 总数
        $sql = "SELECT COUNT(*) as count FROM comment";
        $result = $contentModel->query($sql, [])->fetch(\PDO::FETCH_ASSOC);
        $totalComments = (int)$result['count'];

        // 待审核
        $sql = "SELECT COUNT(*) as count FROM comment WHERE status_id = 1";
        $result = $contentModel->query($sql, [])->fetch(\PDO::FETCH_ASSOC);
        $pendingComments = (int)$result['count'];

        // 审核通过
        $sql = "SELECT COUNT(*) as count FROM comment WHERE status_id = 99";
        $result = $contentModel->query($sql, [])->fetch(\PDO::FETCH_ASSOC);
        $approvedComments = (int)$result['count'];

        // 已隐藏
        $sql = "SELECT COUNT(*) as count FROM comment WHERE status_id = 0";
        $result = $contentModel->query($sql, [])->fetch(\PDO::FETCH_ASSOC);
        $hiddenComments = (int)$result['count'];

        return [
            'video_stats' => [
                'published' => $publishedCount,
                'pending_publish' => $pendingPublishCount,
                'shooting_done' => $shootingDoneCount,
                'script_done' => $scriptDoneCount
            ],
            'comment_stats' => [
                'total' => $totalComments,
                'pending' => $pendingComments,
                'approved' => $approvedComments,
                'hidden' => $hiddenComments
            ],
            // TODO: 暂无数据，使用0占位，待后续实现
            'queue_stats' => [
                'new' => 0,
                'in_progress' => 0,
                'completed' => 0,
                'failed' => 0
            ]
        ];
    }

    /**
     * AJAX API - 获取图表数据
     * 根据开始日期、结束日期和精度返回统计数据
     */
    public function getChartData(Request $request): void
    {
        // 设置响应头为JSON
        header('Content-Type: application/json');

        // 获取请求参数
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $precision = $request->get('precision', 'day'); // 默认为天，支持 day, hour

        // 验证参数
        if (empty($startDate) || empty($endDate)) {
            echo json_encode([
                'success' => false,
                'message' => '缺少必要参数: start_date, end_date'
            ]);
            return;
        }

        // 验证日期格式
        $startDateTime = \DateTime::createFromFormat('Y-m-d', $startDate);
        $endDateTime = \DateTime::createFromFormat('Y-m-d', $endDate);

        if (!$startDateTime || !$endDateTime) {
            echo json_encode([
                'success' => false,
                'message' => '日期格式错误，应为 Y-m-d'
            ]);
            return;
        }

        // 确保开始日期不晚于结束日期
        if ($startDateTime > $endDateTime) {
            echo json_encode([
                'success' => false,
                'message' => '开始日期不能晚于结束日期'
            ]);
            return;
        }

        try {
            $contentModel = new Content();
            $data = [];

            // 根据精度生成日期范围
            if ($precision === 'hour') {
                // 小时精度（暂不实现，返回空数据）
                // TODO: 待后续实现小时精度统计
                $data = $this->generateEmptyChartData($startDate, $endDate, 'hour');
            } else {
                // 天精度
                $data = $this->generateDayChartData($contentModel, $startDate, $endDate);
            }

            echo json_encode([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => '查询数据时发生错误: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * 生成按天统计的图表数据
     */
    private function generateDayChartData(Content $contentModel, string $startDate, string $endDate): array
    {
        $data = [];
        $currentDate = new \DateTime($startDate);
        $endDateTime = new \DateTime($endDate);

        // 遍历每一天
        while ($currentDate <= $endDateTime) {
            $dateStr = $currentDate->format('Y-m-d');
            $nextDayStr = $currentDate->modify('+1 day')->format('Y-m-d');
            $currentDate->modify('-1 day'); // 恢复当前日期

            // 当日总视频数量
            $sql = "SELECT COUNT(*) as count FROM " . Content::getTableName() . " WHERE created_at < :next_day";
            $result = $contentModel->query($sql, ['next_day' => $nextDayStr])->fetch(\PDO::FETCH_ASSOC);
            $totalVideos = (int)$result['count'];

            // 当日新增视频数量
            $sql = "SELECT COUNT(*) as count FROM " . Content::getTableName() .
                   " WHERE created_at >= :current_day AND created_at < :next_day";
            $result = $contentModel->query($sql, [
                'current_day' => $dateStr . ' 00:00:00',
                'next_day' => $nextDayStr . ' 00:00:00'
            ])->fetch(\PDO::FETCH_ASSOC);
            $newVideos = (int)$result['count'];

            // 当日发布视频数量
            $sql = "SELECT COUNT(*) as count FROM " . Content::getTableName() .
                   " WHERE status_id = :status_id AND updated_at >= :current_day AND updated_at < :next_day";
            $result = $contentModel->query($sql, [
                'status_id' => ContentStatus::PUBLISHED->value,
                'current_day' => $dateStr . ' 00:00:00',
                'next_day' => $nextDayStr . ' 00:00:00'
            ])->fetch(\PDO::FETCH_ASSOC);
            $publishedVideos = (int)$result['count'];

            $data[] = [
                'date' => $dateStr,
                'total_videos' => $totalVideos,
                'new_videos' => $newVideos,
                'published_videos' => $publishedVideos,
                // TODO: 暂无数据，使用0占位，待后续实现
                'video_plays' => 0,
                'new_users' => 0
            ];

            $currentDate->modify('+1 day');
        }

        return $data;
    }

    /**
     * 生成空的图表数据（用于未实现的功能）
     */
    private function generateEmptyChartData(string $startDate, string $endDate, string $precision): array
    {
        // TODO: 待后续实现
        return [];
    }
}