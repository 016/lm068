<?php

namespace App\Controllers\Backend;

use App\Core\Request;

class DashboardController extends BackendController
{
    public function index(Request $request): void
    {
        if (!$this->requireAuth()) {
            return;
        }

        $this->render('dashboard.index', [
            'title' => '管理后台 - 数据面板',
            'stats' => [
                'total_videos' => 12547,
                'total_views' => '2.34M',
                'total_users' => 45678,
                'total_subscribers' => 12890
            ],
            'css_files' => ['dashboard.css'],
            'js_files' => ['dashboard.js']
        ]);
    }
}