<?php

namespace App\Controllers\Backend;

use App\Core\Request;
use App\Models\Collection;
use App\Models\Content;

class CollectionController extends BackendController
{
    private Collection $collectionModel;
    private Content $contentModel;

    public function __construct()
    {
        parent::__construct();
        $this->collectionModel = new Collection();
        $this->contentModel = new Content();
    }

    public function index(Request $request): void
    {
        // 获取搜索过滤条件，支持所有搜索表单字段
        $filters = $this->getSearchFilters(['id','name', 'description','content_cnt','icon_class','status_id','order_by'], $request);


        // 获取所有符合条件的数据，不进行分页
        $collections = $this->collectionModel->findAllWithFilters($filters);
        $stats = $this->collectionModel->getStats();

        $this->render('collections/index', [
            'collections' => $collections,
            'filters' => $filters,
            'stats' => $stats,
            'title' => '合集管理 - 视频分享网站管理后台',
            'css_files' => ['collection_list_2.css'],
            'js_files' => ['collection_list_6.js']
        ]);
    }

    public function edit(Request $request): void
    {
        $id = (int)$request->getParam(0);
        $collection = $this->collectionModel->findById($id);

        if (!$collection) {
            $this->redirect('/collections');
            return;
        }

        $relatedContent = $this->collectionModel->getRelatedContent($id);
        
        $allContent = $this->contentModel->findAll([
            'status_id' => [21, 29, 31, 39, 91, 99]  // 只显示进行中或已完成的内容
        ]);

        $contentOptions = [];
        $selectedContentIds = array_column($relatedContent, 'id');
        
        foreach ($allContent as $content) {
            $contentOptions[] = [
                'id' => $content['id'],
                'title' => $content['title_cn'] ?: $content['title_en'],
                'selected' => in_array($content['id'], $selectedContentIds)
            ];
        }

        $this->render('collections/edit', [
            'collection' => $collection,
            'relatedContent' => $relatedContent,
            'contentOptions' => $contentOptions,
            'isCreateMode' => false,
            'title' => '编辑合集 - 视频分享网站管理后台',
            'css_files' => ['collection_edit_2.css', 'multi_select_dropdown_1.css'],
            'js_files' => ['multi_select_dropdown_2.js', 'form_utils_2.js', 'collection_edit_6.js']
        ]);
    }

    public function update(Request $request): void
    {
        $id = (int)($request->post('id') ?? 0);
        
        if (!$id) {
            $this->redirect('/collections');
            return;
        }

        $data = [
            'name_cn' => $request->post('name_cn'),
            'name_en' => $request->post('name_en'),
            'short_desc_cn' => $request->post('short_desc_cn'),
            'short_desc_en' => $request->post('short_desc_en'),
            'desc_cn' => $request->post('desc_cn'),
            'desc_en' => $request->post('desc_en'),
            'color_class' => $request->post('color_class'),
            'icon_class' => $request->post('icon_class'),
            'status_id' => (int)($request->post('status_id') ?? 0)
        ];

        // 验证必填字段
        $errors = [];
        if (empty($data['name_cn'])) {
            $errors['name_cn'] = '中文名称不能为空';
        }
        if (empty($data['name_en'])) {
            $errors['name_en'] = '英文名称不能为空';
        }

        if (!empty($errors)) {
            // 验证失败，返回编辑页面并显示错误
            $collection = $this->collectionModel->findById($id);
            if (!$collection) {
                $this->redirect('/collections');
                return;
            }
            
            // 合并用户输入的数据到collection数据中
            $collection = array_merge($collection, $data);
            
            $relatedContent = $this->collectionModel->getRelatedContent($id);
            $allContent = $this->contentModel->findAll([
                'status_id' => [21, 29, 31, 39, 91, 99]
            ]);
            
            $contentOptions = [];
            $selectedContentIds = array_column($relatedContent, 'id');
            
            foreach ($allContent as $content) {
                $contentOptions[] = [
                    'id' => $content['id'],
                    'title' => $content['title_cn'] ?: $content['title_en'],
                    'selected' => in_array($content['id'], $selectedContentIds)
                ];
            }
            
            $this->render('collections/edit', [
                'collection' => $collection,
                'relatedContent' => $relatedContent,
                'contentOptions' => $contentOptions,
                'errors' => $errors,
                'isCreateMode' => false,
                'title' => '编辑合集 - 视频分享网站管理后台',
                'css_files' => ['collection_edit_2.css', 'multi_select_dropdown_1.css'],
                'js_files' => ['multi_select_dropdown_2.js', 'form_utils_2.js', 'collection_edit_6.js']
            ]);
            return;
        }

        try {
            $this->collectionModel->update($id, $data);

            // 处理关联内容
            $relatedVideos = $request->post('related_videos');
            if ($relatedVideos !== null) {
                $contentIds = is_array($relatedVideos) ? array_map('intval', $relatedVideos) : [];
                $this->collectionModel->syncContentAssociations($id, $contentIds);
            }

            // 成功后跳转到列表页面
            $this->redirect('/collections');
        } catch (\Exception $e) {
            error_log("Collection update error: " . $e->getMessage());
            
            // 出错时返回编辑页面并显示错误
            $collection = $this->collectionModel->findById($id);
            if (!$collection) {
                $this->redirect('/collections');
                return;
            }
            
            // 合并用户输入的数据到collection数据中
            $collection = array_merge($collection, $data);
            
            $relatedContent = $this->collectionModel->getRelatedContent($id);
            $allContent = $this->contentModel->findAll([
                'status_id' => [21, 29, 31, 39, 91, 99]
            ]);
            
            $contentOptions = [];
            $selectedContentIds = array_column($relatedContent, 'id');
            
            foreach ($allContent as $content) {
                $contentOptions[] = [
                    'id' => $content['id'],
                    'title' => $content['title_cn'] ?: $content['title_en'],
                    'selected' => in_array($content['id'], $selectedContentIds)
                ];
            }
            
            $this->render('collections/edit', [
                'collection' => $collection,
                'relatedContent' => $relatedContent,
                'contentOptions' => $contentOptions,
                'errors' => ['general' => '更新失败: ' . $e->getMessage()],
                'isCreateMode' => false,
                'title' => '编辑合集 - 视频分享网站管理后台',
                'css_files' => ['collection_edit_2.css', 'multi_select_dropdown_1.css'],
                'js_files' => ['multi_select_dropdown_2.js', 'form_utils_2.js', 'collection_edit_6.js']
            ]);
        }
    }

    public function create(Request $request): void
    {
        $allContent = $this->contentModel->findAll([
            'status_id' => [21, 29, 31, 39, 91, 99]
        ]);

        $contentOptions = [];
        foreach ($allContent as $content) {
            $contentOptions[] = [
                'id' => $content['id'],
                'title' => $content['title_cn'] ?: $content['title_en'],
                'selected' => false
            ];
        }

        $this->render('collections/edit', [
            'collection' => null,
            'relatedContent' => [],
            'contentOptions' => $contentOptions,
            'isCreateMode' => true,
            'title' => '创建合集 - 视频分享网站管理后台',
            'css_files' => ['collection_edit_2.css', 'multi_select_dropdown_1.css'],
            'js_files' => ['multi_select_dropdown_2.js', 'form_utils_2.js', 'collection_edit_6.js']
        ]);
    }

    public function store(Request $request): void
    {
        $data = [
            'name_cn' => $request->post('name_cn'),
            'name_en' => $request->post('name_en'),
            'short_desc_cn' => $request->post('short_desc_cn') ?? '',
            'short_desc_en' => $request->post('short_desc_en') ?? '',
            'desc_cn' => $request->post('desc_cn') ?? '',
            'desc_en' => $request->post('desc_en') ?? '',
            'color_class' => $request->post('color_class') ?? 'btn-outline-primary',
            'icon_class' => $request->post('icon_class') ?? 'bi-collection',
            'status_id' => (int)($request->post('status_id') ?? 1),
            'content_cnt' => 0
        ];

        // 验证必填字段
        $errors = [];
        if (empty($data['name_cn'])) {
            $errors['name_cn'] = '中文名称不能为空';
        }
        if (empty($data['name_en'])) {
            $errors['name_en'] = '英文名称不能为空';
        }

        if (!empty($errors)) {
            // 验证失败，返回创建页面并显示错误
            $allContent = $this->contentModel->findAll([
                'status_id' => [21, 29, 31, 39, 91, 99]
            ]);
            
            $contentOptions = [];
            foreach ($allContent as $content) {
                $contentOptions[] = [
                    'id' => $content['id'],
                    'title' => $content['title_cn'] ?: $content['title_en'],
                    'selected' => false
                ];
            }
            
            $this->render('collections/edit', [
                'collection' => $data, // 传递用户输入的数据
                'relatedContent' => [],
                'contentOptions' => $contentOptions,
                'errors' => $errors,
                'isCreateMode' => true,
                'title' => '创建合集 - 视频分享网站管理后台',
                'css_files' => ['collection_edit_2.css', 'multi_select_dropdown_1.css'],
                'js_files' => ['multi_select_dropdown_2.js', 'form_utils_2.js', 'collection_edit_6.js']
            ]);
            return;
        }

        try {
            $collectionId = $this->collectionModel->create($data);

            $relatedVideos = $request->post('related_videos');
            if ($relatedVideos && is_array($relatedVideos)) {
                $contentIds = array_map('intval', $relatedVideos);
                $this->collectionModel->syncContentAssociations($collectionId, $contentIds);
            }

            // 成功后跳转到列表页面
            $this->redirect('/collections');
        } catch (\Exception $e) {
            error_log("Collection creation error: " . $e->getMessage());
            
            // 出错时返回创建页面并显示错误
            $allContent = $this->contentModel->findAll([
                'status_id' => [21, 29, 31, 39, 91, 99]
            ]);
            
            $contentOptions = [];
            foreach ($allContent as $content) {
                $contentOptions[] = [
                    'id' => $content['id'],
                    'title' => $content['title_cn'] ?: $content['title_en'],
                    'selected' => false
                ];
            }
            
            $this->render('collections/edit', [
                'collection' => $data, // 传递用户输入的数据
                'relatedContent' => [],
                'contentOptions' => $contentOptions,
                'errors' => ['general' => '创建失败: ' . $e->getMessage()],
                'isCreateMode' => true,
                'title' => '创建合集 - 视频分享网站管理后台',
                'css_files' => ['collection_edit_2.css', 'multi_select_dropdown_1.css'],
                'js_files' => ['multi_select_dropdown_2.js', 'form_utils_2.js', 'collection_edit_6.js']
            ]);
        }
    }

    public function destroy(Request $request): void
    {
        $id = (int)$request->getParam(0);
        
        if (!$id) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid collection ID']);
            return;
        }

        try {
            $this->collectionModel->delete($id);
            $this->jsonResponse(['success' => true, 'message' => '合集删除成功']);
        } catch (\Exception $e) {
            error_log("Collection deletion error: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => '删除失败: ' . $e->getMessage()]);
        }
    }

    public function bulkAction(Request $request): void
    {
        $action = $request->post('action');
        $collectionIds = $request->post('ids');
        var_dump($action);
        var_dump($collectionIds);

        if (!$action || !$collectionIds || !is_array($collectionIds)) {
            $this->jsonResponse(['success' => false, 'message' => '参数错误']);
            return;
        }

        $collectionIds = array_map('intval', $collectionIds);

        try {
            switch ($action) {
                case 'enable':
                    $this->collectionModel->bulkUpdateStatus($collectionIds, 1);
                    $message = '批量启用成功';
                    break;
                case 'disable':
                    $this->collectionModel->bulkUpdateStatus($collectionIds, 0);
                    $message = '批量禁用成功';
                    break;
                case 'delete':
                    $this->collectionModel->bulkDelete($collectionIds);
                    $message = '批量删除成功';
                    break;
                default:
                    $this->jsonResponse(['success' => false, 'message' => '不支持的操作']);
                    return;
            }

            $this->jsonResponse(['success' => true, 'message' => $message]);
        } catch (\Exception $e) {
            error_log("Bulk action error: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => '操作失败: ' . $e->getMessage()]);
        }
    }

    public function exportData(Request $request): void
    {
        $format = $request->get('format') ?? 'json';
        
        try {
            $collections = $this->collectionModel->findAll();
            
            if ($format === 'csv') {
                $this->exportCsv($collections);
            } else {
                $this->exportJson($collections);
            }
        } catch (\Exception $e) {
            error_log("Export error: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => '导出失败: ' . $e->getMessage()]);
        }
    }

    private function exportCsv(array $collections): void
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="collections_' . date('Y-m-d_H-i-s') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // 添加BOM以支持中文
        fwrite($output, "\xEF\xBB\xBF");
        
        // CSV头
        fputcsv($output, ['ID', '中文名称', '英文名称', '中文简介', '英文简介', '状态', '关联内容数', '创建时间']);
        
        foreach ($collections as $collection) {
            fputcsv($output, [
                $collection['id'],
                $collection['name_cn'],
                $collection['name_en'], 
                $collection['short_desc_cn'],
                $collection['short_desc_en'],
                $collection['status_id'] ? '启用' : '禁用',
                $collection['content_cnt'],
                $collection['created_at']
            ]);
        }
        
        fclose($output);
    }

    private function exportJson(array $collections): void
    {
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="collections_' . date('Y-m-d_H-i-s') . '.json"');
        
        echo json_encode([
            'export_time' => date('Y-m-d H:i:s'),
            'total_count' => count($collections),
            'collections' => $collections
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function show(Request $request): void
    {
        $id = (int)$request->getParam(0);
        $collection = $this->collectionModel->findById($id);

        if (!$collection) {
            $this->redirect('/collections');
            return;
        }

        $relatedContent = $this->collectionModel->getRelatedContent($id);

        $this->render('collections/show', [
            'collection' => $collection,
            'relatedContent' => $relatedContent,
            'title' => '查看合集 - 视频分享网站管理后台'
        ]);
    }

    public function getContentForCollection(Request $request): void
    {
        $collectionId = (int)$request->get('collection_id');
        
        if (!$collectionId) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid collection ID']);
            return;
        }

        try {
            $content = $this->collectionModel->getRelatedContent($collectionId);
            $this->jsonResponse(['success' => true, 'content' => $content]);
        } catch (\Exception $e) {
            error_log("Get content error: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => '获取内容失败']);
        }
    }

    /**
     * 批量导入合集
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
     * 处理CSV文件导入
     */
    private function processCSVFile(string $filePath): array
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
                    $requiredFields = ['name_cn', 'name_en'];
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
                    if ($this->importSingleCollection($rowData)) {
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
     * 解析CSV行数据
     */
    private function parseCSVRow(array $headers, array $data): array
    {
        $rowData = [];
        foreach ($headers as $index => $header) {
            $rowData[$header] = isset($data[$index]) ? trim($data[$index]) : '';
        }
        return $rowData;
    }

    /**
     * 导入单个合集记录
     */
    private function importSingleCollection(array $data): bool
    {
        // 验证必填字段
        if (empty($data['name_cn']) || empty($data['name_en'])) {
            return false;
        }

        // 检查是否已存在同名标签
        if ($this->collectionModel->findByName($data['name_cn'], $data['name_en'])) {
            return false; // 跳过重复标签
        }

        // 准备数据
        $collectionData = [
            'name_cn' => $data['name_cn'],
            'name_en' => $data['name_en'],
            'short_desc_cn' => $data['short_desc_cn'] ?? '',
            'short_desc_en' => $data['short_desc_en'] ?? '',
            'desc_cn' => $data['desc_cn'] ?? '',
            'desc_en' => $data['desc_en'] ?? '',
            'color_class' => $data['color_class'] ?? 'btn-outline-primary',
            'icon_class' => $data['icon_class'] ?? 'bi-collection',
            'status_id' => isset($data['status_id']) ? (int)$data['status_id'] : 1,
            'content_cnt' => 0
        ];

        // 验证状态ID
        if (!in_array($collectionData['status_id'], [0, 1])) {
            $collectionData['status_id'] = 1; // 默认启用
        }

        try {
            $this->collectionModel->create($collectionData);
            return true;
        } catch (\Exception $e) {
            error_log("Single collection import error: " . $e->getMessage());
            throw new \Exception("创建合集失败: " . $e->getMessage());
        }
    }
}