<?php

namespace App\Controllers\Backend;

use App\Core\Request;
use App\Models\Collection;
use App\Models\Content;
use App\Constants\Status;
use App\Constants\ContentStatus;

class CollectionController extends BackendController
{
    private Collection $curModel;
    private Content $contentModel;

    public function __construct()
    {
        parent::__construct();
        $this->curModel = new Collection();
        $this->contentModel = new Content();
    }

    public function index(Request $request): void
    {
        // 获取搜索过滤条件，支持所有搜索表单字段
        $filters = $this->getSearchFilters(['id','name', 'description','content_cnt','icon_class','status_id','order_by'], $request);


        // 获取所有符合条件的数据，不进行分页
        $collections = $this->curModel->findAllWithFilters($filters);
        $stats = $this->curModel->getStats();

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
        $collection = $this->curModel->findById($id);

        if (!$collection) {
            $this->redirect('/collections');
            return;
        }

        $relatedContent = $this->curModel->getRelatedContent($id);
        
        $allContent = $this->contentModel->findAll([
            'status_id' => ContentStatus::getVisibleStatuses()
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
            'status_id' => (int)($request->post('status_id') ?? Status::INACTIVE->value)
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
            $collection = $this->curModel->findById($id);
            if (!$collection) {
                $this->redirect('/collections');
                return;
            }
            
            // 合并用户输入的数据到collection数据中
            $collection = array_merge($collection, $data);
            
            $relatedContent = $this->curModel->getRelatedContent($id);
            $allContent = $this->contentModel->findAll([
                'status_id' => ContentStatus::getVisibleStatuses()
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
            $this->curModel->update($id, $data);

            // 处理关联内容
            $relatedVideos = $request->post('related_videos');
            if ($relatedVideos !== null) {
                $contentIds = is_array($relatedVideos) ? array_map('intval', $relatedVideos) : [];
                $this->curModel->syncContentAssociations($id, $contentIds);
            }

            // 成功后跳转到列表页面
            $this->redirect('/collections');
        } catch (\Exception $e) {
            error_log("Collection update error: " . $e->getMessage());
            
            // 出错时返回编辑页面并显示错误
            $collection = $this->curModel->findById($id);
            if (!$collection) {
                $this->redirect('/collections');
                return;
            }
            
            // 合并用户输入的数据到collection数据中
            $collection = array_merge($collection, $data);
            
            $relatedContent = $this->curModel->getRelatedContent($id);
            $allContent = $this->contentModel->findAll([
                'status_id' => ContentStatus::getVisibleStatuses()
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
            'status_id' => ContentStatus::getVisibleStatuses()
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
            'status_id' => (int)($request->post('status_id') ?? Status::ACTIVE->value),
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
                'status_id' => ContentStatus::getVisibleStatuses()
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
            $collectionId = $this->curModel->create($data);

            $relatedVideos = $request->post('related_videos');
            if ($relatedVideos && is_array($relatedVideos)) {
                $contentIds = array_map('intval', $relatedVideos);
                $this->curModel->syncContentAssociations($collectionId, $contentIds);
            }

            // 成功后跳转到列表页面
            $this->redirect('/collections');
        } catch (\Exception $e) {
            error_log("Collection creation error: " . $e->getMessage());
            
            // 出错时返回创建页面并显示错误
            $allContent = $this->contentModel->findAll([
                'status_id' => ContentStatus::getVisibleStatuses()
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
            $this->curModel->delete($id);
            $this->jsonResponse(['success' => true, 'message' => '合集删除成功']);
        } catch (\Exception $e) {
            error_log("Collection deletion error: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => '删除失败: ' . $e->getMessage()]);
        }
    }

    public function show(Request $request): void
    {
        $id = (int)$request->getParam(0);
        $collection = $this->curModel->findById($id);

        if (!$collection) {
            $this->redirect('/collections');
            return;
        }

        $relatedContent = $this->curModel->getRelatedContent($id);

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
            $content = $this->curModel->getRelatedContent($collectionId);
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
        if ($this->curModel->findByName($data['name_cn'], $data['name_en'])) {
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
            $this->curModel->create($collectionData);
            return true;
        } catch (\Exception $e) {
            error_log("Single collection import error: " . $e->getMessage());
            throw new \Exception("创建合集失败: " . $e->getMessage());
        }
    }
}