<?php

namespace App\Controllers\Frontend;

use App\Core\Request;

class HomeController extends FrontendController
{
    public function index(Request $request): void
    {
        //force to videos
        $this->redirect('content');
        exit;

        $content = $this->view('contents.list', [
            'videos' => [],
            'message' => 'Welcome to Video Content Site'
        ]);
        
        echo $this->layout($content, '首页 - 视频内容网站');
    }

    public function test(Request $request): void
    {
        echo "<h1>Hello World</h1>";
        echo "<p>PHP Test Page - 基础功能测试成功！</p>";
        echo "<p>当前时间: " . date('Y-m-d H:i:s') . "</p>";
    }
}