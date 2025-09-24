<?php

namespace App\Controllers\Backend;

use App\Core\Controller;
use App\Core\Model;
use App\Core\Request;
use App\Interfaces\HasStatuses;

class BackendController extends Controller
{
    /**
     * 当前控制器操作的模型实例
     */
    protected Model|HasStatuses $curModel;
    /**
     * 获取模型的所有状态
     * @return array
     */
    protected function getModelStatuses(): array
    {
        // 1. 获取当前模型的类名
        $modelClass = get_class($this->curModel);

        // 2. 检查模型是否实现了 HasStatuses 接口，确保方法存在
        if (!is_a($modelClass, HasStatuses::class, true)) {
            // 或者抛出异常，或者返回空数组
            return [];
        }

        // 3. 通过类名静态调用接口方法，获取枚举类名
        $statusEnumClass = $modelClass::getStatusEnum();

        // 4. 使用枚举的 cases() 方法动态生成状态数组，更灵活
        // array_column 可以方便地将枚举案例转换为 [NAME => value] 的格式
        return array_column($statusEnumClass::cases(), 'value', 'name');
    }


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

    protected function setFlashMessage(string $message, string $type = 'info'): void
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        $_SESSION['flash_message'] = [
            'message' => $message,
            'type' => $type
        ];
    }

    protected function getFlashMessage(): ?array
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        
        if (isset($_SESSION['flash_message'])) {
            $message = $_SESSION['flash_message'];
            unset($_SESSION['flash_message']);
            return $message;
        }
        
        return null;
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

        $statusList = $this->getModelStatuses();

        try {
            switch ($action) {
                case 'enable':
                    $result = $this->performBulkUpdateStatus($targetIds, $statusList['ENABLED']);
                    $successCount = $result['success'];
                    $errorCount = $result['error'];
                    break;
                case 'disable':
                    $result = $this->performBulkUpdateStatus($targetIds, $statusList['DISABLED']);
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

    /**
     * 执行批量状态更新 - 通用方法
     * 支持不同的模型类型进行批量状态更新
     */
    protected function performBulkUpdateStatus(array $targetIds, int $status_id): array
    {
        $successCount = 0;
        $errorCount = 0;

        // 检查当前模型是否支持批量更新
        if (isset($this->curModel) && method_exists($this->curModel, 'bulkUpdateStatus')) {
            try {
                $modelClass = get_class($this->curModel);
                $returnCnt = $modelClass::bulkUpdateStatus($targetIds, $status_id);
                $successCount = $returnCnt['changed'];
                $errorCount = $returnCnt['fail'];
            } catch (\Exception $e) {
                error_log("Bulk status update error: " . $e->getMessage());
            }
        } else {
            // 逐个更新的兜底方案
            foreach ($targetIds as $targetId) {
                try {
                    // 检查记录是否存在
                    $modelClass = get_class($this->curModel);
                    if (isset($this->curModel) && $modelClass::exists($targetId)) {
                        $this->curModel->update($targetId, ['status_id' => $status_id]);
                        $successCount++;
                    } else {
                        $errorCount++;
                    }
                } catch (\Exception $e) {
                    error_log("Failed to update item {$targetId}: " . $e->getMessage());
                    $errorCount++;
                }
            }
        }

        return ['success' => $successCount, 'error' => $errorCount];
    }

    /**
     * 执行批量删除 - 通用方法
     * 支持不同的模型类型进行批量删除
     */
    protected function performBulkDelete(array $targetIds): array
    {
        $successCount = 0;
        $errorCount = 0;

        // 检查当前模型是否支持批量删除
        if (isset($this->curModel) && method_exists($this->curModel, 'bulkDelete')) {
            try {
                $modelClass = get_class($this->curModel);
                $modelClass::bulkDelete($targetIds);
                $successCount = count($targetIds);
            } catch (\Exception $e) {
                error_log("Bulk delete error: " . $e->getMessage());
                $errorCount = count($targetIds);
            }
        } else {
            // 逐个删除的兜底方案
            foreach ($targetIds as $targetId) {
                try {
                    // 检查记录是否存在
                    $modelClass = get_class($this->curModel);
                    if (isset($this->curModel) && $modelClass::exists($targetId)) {
                        $this->curModel->delete($targetId);
                        $successCount++;
                    } else {
                        $errorCount++;
                    }
                } catch (\Exception $e) {
                    error_log("Failed to delete item {$targetId}: " . $e->getMessage());
                    $errorCount++;
                }
            }
        }

        return ['success' => $successCount, 'error' => $errorCount];
    }

    /**
     * 通用的CSV批量导入功能
     * 
     * @param Request $request 请求对象
     * @return void
     */
    public function bulkImport(Request $request): void
    {
        // 验证请求方法
        if (!$request->isPost()) {
            $this->jsonResponse(['success' => false, 'message' => '请求方法错误']);
            return;
        }

        // 检查是否有文件上传
        if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
            $this->jsonResponse(['success' => false, 'message' => '文件上传失败']);
            return;
        }

        $uploadedFile = $_FILES['csv_file'];
        
        // 验证文件类型
        $fileExtension = strtolower(pathinfo($uploadedFile['name'], PATHINFO_EXTENSION));
        if ($fileExtension !== 'csv') {
            $this->jsonResponse(['success' => false, 'message' => '请上传CSV文件']);
            return;
        }

        // 验证文件大小 (10MB)
        if ($uploadedFile['size'] > 10 * 1024 * 1024) {
            $this->jsonResponse(['success' => false, 'message' => '文件大小不能超过10MB']);
            return;
        }

        try {
            $result = $this->processCSVFile($uploadedFile['tmp_name']);
            $this->jsonResponse($result);
        } catch (\Exception $e) {
            error_log("CSV import error: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => '导入过程中发生错误: ' . $e->getMessage()]);
        }
    }

    /**
     * 处理CSV文件导入 - 通用方法
     * 子类需要重写 getRequiredCSVFields() 和 importSingleRecord() 方法
     */
    protected function processCSVFile(string $filePath): array
    {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            return ['success' => false, 'message' => '无法读取上传的文件'];
        }

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            return ['success' => false, 'message' => '无法打开CSV文件'];
        }

        $successCount = 0;
        $errorCount = 0;
        $lineNumber = 0;
        $headers = [];
        $errors = [];

        try {
            while (($data = fgetcsv($handle, 1000, ',', '"', '\\')) !== false) {
                $lineNumber++;
                
                // 跳过空行
                if (empty(array_filter($data))) {
                    continue;
                }

                // 第一行作为表头
                if ($lineNumber === 1) {
                    $headers = array_map('trim', $data);
                    
                    // 验证必需的字段
                    $requiredFields = $this->getRequiredCSVFields();
                    $missingFields = array_diff($requiredFields, $headers);
                    if (!empty($missingFields)) {
                        return [
                            'success' => false, 
                            'message' => 'CSV文件缺少必需的字段: ' . implode(', ', $missingFields)
                        ];
                    }
                    continue;
                }

                // 处理数据行
                try {
                    $rowData = $this->parseCSVRow($headers, $data);
                    if ($this->importSingleRecord($rowData)) {
                        $successCount++;
                    } else {
                        $errorCount++;
                        $errors[] = "第{$lineNumber}行：数据验证失败";
                    }
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "第{$lineNumber}行：" . $e->getMessage();
                }
            }

            return [
                'success' => true,
                'success_count' => $successCount,
                'error_count' => $errorCount,
                'message' => "导入完成：成功{$successCount}条，失败{$errorCount}条",
                'errors' => array_slice($errors, 0, 10) // 只返回前10个错误
            ];

        } finally {
            fclose($handle);
        }
    }

    /**
     * 解析CSV行数据 - 通用方法
     */
    protected function parseCSVRow(array $headers, array $data): array
    {
        $rowData = [];
        
        foreach ($headers as $index => $header) {
            $value = isset($data[$index]) ? trim($data[$index]) : '';
            $rowData[$header] = $value;
        }

        return $rowData;
    }

    /**
     * 获取CSV文件必需的字段 - 需要子类重写
     * 
     * @return array
     */
    protected function getRequiredCSVFields(): array
    {
        return ['name_cn', 'name_en']; // 默认字段，子类可以重写
    }

    /**
     * 导入单条记录 - 使用当前模型的导入方法
     * 
     * @param array $data
     * @return bool
     */
    protected function importSingleRecord(array $data): bool
    {
        if (!isset($this->curModel)) {
            throw new \Exception('当前模型未初始化');
        }

        return $this->curModel->importSingleRecord($data);
    }
}