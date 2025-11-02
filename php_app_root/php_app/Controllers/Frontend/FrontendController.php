<?php

namespace App\Controllers\Frontend;

use App\Core\Controller;

class FrontendController extends Controller
{

    public $base_url = 'https://dpit.lib00.com';
    public $curAction = '';
    public $curAction_zh = '';
    public $curAction_en = '';
    public $curLang = '';
    public $seo_param = ['title' => '', 'desc' => '', 'canonical' => '', 'index'=>true];

    public function init(){
//        $this->curUri = $this->request->getUri();
    }

    protected function getTemplatePath(string $template): string
    {
        return __DIR__ . '/../../Views/frontend/' . str_replace('.', '/', $template) . '.php';
    }

    protected function layout(string $content, string $title = '', array $data = []): string
    {
        $data['content'] = $content;
        $data['title'] = $title;
        return $this->view('layouts.main', $data);
    }

    /**
     * 404 错误处理
     */
    public function notFound(): void
    {
        http_response_code(404);
        $currentLang = \App\Core\I18n::getCurrentLang();
        $message = $currentLang === 'zh' ? '页面未找到' : 'Page Not Found';
        echo "<h1>404 - {$message}</h1>";
        echo "<p><a href='/{$currentLang}/content'>返回列表页</a></p>";
    }

}