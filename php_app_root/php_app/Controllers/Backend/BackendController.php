<?php

namespace App\Controllers\Backend;

use App\Core\Controller;
use App\Core\Request;

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

    protected function jsonResponse(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * 从请求中提取搜索过滤条件
     */
    protected function getSearchFilters(array $indexList, Request $request): array
    {

        $filters = [];
        foreach ($indexList as $index) {
            $filters[$index] = $request->get($index);
        }

        return $filters;
    }


    /**
     * global based bulk action
     * @param Request $request
     * @return void
     */
    public function bulkAction(Request $request): void
    {
        $action = $request->post('action');
        $inputIds = $request->post('ids');

        if (!$action || !$inputIds) {
            $this->jsonResponse(['success' => false, 'message' => '参数错误']);
            return;
        }

        // 处理可能的JSON字符串格式
        if (is_string($inputIds)) {
            $targetIds = json_decode($inputIds, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->jsonResponse(['success' => false, 'message' => 'tag_ids 格式错误']);
                return;
            }
        } else if (is_array($inputIds)) {
            $targetIds = $inputIds;
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'tag_ids 参数类型错误']);
            return;
        }

        if (empty($targetIds) || !is_array($targetIds)) {
            $this->jsonResponse(['success' => false, 'message' => '请选择要操作的标签']);
            return;
        }

        $targetIds = array_map('intval', $targetIds);
        $successCount = 0;
        $errorCount = 0;
        $totalCount = count($targetIds);

        try {
            switch ($action) {
                case 'enable':
                    $result = $this->performBulkUpdateStatus($targetIds, 1);
                    $successCount = $result['success'];
                    $errorCount = $result['error'];
                    break;
                case 'disable':
                    $result = $this->performBulkUpdateStatus($targetIds, 0);
                    $successCount = $result['success'];
                    $errorCount = $result['error'];
                    break;
                case 'delete':
                    $result = $this->performBulkDelete($targetIds);
                    $successCount = $result['success'];
                    $errorCount = $result['error'];
                    break;
                default:
                    $this->jsonResponse(['success' => false, 'message' => '不支持的操作']);
                    return;
            }

            $this->jsonResponse([
                'success' => true,
                'success_count' => $successCount,
                'error_count' => $errorCount,
                'total_count' => $totalCount,
                'message' => "操作完成：成功{$successCount}条，失败{$errorCount}条"
            ]);
        } catch (\Exception $e) {
            error_log("Bulk action error: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => '操作失败: ' . $e->getMessage()]);
        }
    }
}