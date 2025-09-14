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

        $content = $this->view('dashboard.index', [
            'stats' => [
                'total_videos' => 0,
                'total_users' => 0,
                'total_comments' => 0
            ]
        ]);
        
        echo $this->layout($content, '管理后台 - 数据面板');
    }
}