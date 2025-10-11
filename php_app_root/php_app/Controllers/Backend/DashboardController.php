<?php

namespace App\Controllers\Backend;

use App\Core\Request;
use App\Models\DashboardStats;

class DashboardController extends BackendController
{
    public function index(Request $request): void
    {
        // 从Model层获取统计数据
        $metricsData = DashboardStats::getMetricsOverview();
        $contentGridData = DashboardStats::getContentGridStats();

        $this->render('dashboard.index', [
            'title' => '管理后台 - 数据面板',
            'metrics' => $metricsData,
            'contentGrid' => $contentGridData,
            'css_files' => ['dashboard.css'],
            'js_files' => ['dashboard.js']
        ]);
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
            // 从Model层获取图表数据
            $data = DashboardStats::getChartData($startDate, $endDate, $precision);

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
}