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

    protected function render(string $template, array $data = [], bool $useLayout = true): void
    {
        if ($useLayout) {
            echo $this->layout($this->view($template, $data), $data['title'] ?? '', $data);
        } else {
            echo $this->view($template, $data);
        }
    }

    protected function requireAuth(): bool
    {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect('/login');
            return false;
        }
        return true;
    }
}