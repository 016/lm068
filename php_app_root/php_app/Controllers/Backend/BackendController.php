<?php

namespace App\Controllers\Backend;

use App\Core\Controller;

class BackendController extends Controller
{
    protected function getTemplatePath(string $template): string
    {
        return __DIR__ . '/../../Views/backend/' . str_replace('.', '/', $template) . '.php';
    }

    protected function layout(string $content, string $title = '', array $data = []): string
    {
        $data['content'] = $content;
        $data['title'] = $title;
        return $this->view('layouts.main', $data);
    }

    protected function requireAuth(): bool
    {
        session_start();
        if (!isset($_SESSION['admin_user_id'])) {
            $this->redirect('/admin/login');
            return false;
        }
        return true;
    }
}