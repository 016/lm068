<?php

namespace App\Controllers\Frontend;

use App\Core\Controller;

class FrontendController extends Controller
{
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
}