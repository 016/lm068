<?php

namespace App\Controllers\Backend;

use App\Core\Request;
use App\Models\Tag;
use App\Models\Content;
use App\Constants\TagStatus;
use App\Constants\ContentStatus;

class TagController extends BackendController
{
    private Content $contentModel;

    public function __construct()
    {
        parent::__construct();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->curModel = new Tag();
        $this->contentModel = new Content();
    }

    public function index(Request $request): void
    {
        // 获取搜索过滤条件，支持所有搜索表单字段
        $filters = $this->getSearchFilters(['id','name','content_cnt','icon_class','status','order_by'], $request);
        
        // 根据过滤条件获取所有符合条件的标签数据（不分页，由JS处理分页）
        $tags = $this->curModel->findAllWithFilters($filters);
        $stats = $this->curModel->getStats();

        // 处理 Toast 消息
        $toastMessage = $_SESSION['toast_message'] ?? null;
        $toastType = $_SESSION['toast_type'] ?? null;
        if ($toastMessage) {
            unset($_SESSION['toast_message'], $_SESSION['toast_type']);
        }

        $this->render('tags/index', [
            'tags' => $tags,
            'filters' => $filters,
            'stats' => $stats,
            'toastMessage' => $toastMessage,
            'toastType' => $toastType,
            'pageTitle' => '标签管理 - 视频分享网站管理后台',
            'css_files' => ['tag_list_8.css'],
            'js_files' => ['tag_list_11.js']
        ]);
    }

    public function edit(Request $request): void
    {
        $id = (int)$request->getParam(0);
        $tag = $this->curModel->findById($id);

        if (!$tag) {
            $this->redirect('/backend/tags');
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

        $this->render('tags/edit', [
            'tag' => $tag,
            'relatedContent' => $relatedContent,
            'contentOptions' => $contentOptions,
            'isCreateMode' => false,
            'pageTitle' => '编辑标签 - 视频分享网站管理后台',
            'css_files' => ['tag_edit_8.css', 'multi_select_dropdown_1.css'],
            'js_files' => ['multi_select_dropdown_2.js', 'form_utils_2.js', 'tag_edit_12.js']
        ]);
    }

    public function update(Request $request): void
    {
        $id = (int)($request->post('id') ?? 0);
        
        if (!$id) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid tag ID']);
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
            'status_id' => (int)($request->post('status_id') ?? TagStatus::DISABLED->value)
        ];

        // 使用模型验证，传入当前ID以排除自身
        $errors = $this->curModel->validate($data, true, $id);
        if (!empty($errors)) {
            // 验证失败，返回编辑页面并显示错误
            $tag = $this->curModel->findById($id);
            if (!$tag) {
                $this->redirect('/tags');
                return;
            }
            
            // 合并用户输入的数据到tag数据中
            $tag = array_merge($tag, $data);
            
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
            
            $this->render('tags/edit', [
                'tag' => $tag,
                'relatedContent' => $relatedContent,
                'contentOptions' => $contentOptions,
                'errors' => $errors,
                'isCreateMode' => false,
                'pageTitle' => '编辑标签 - 视频分享网站管理后台',
                'css_files' => ['tag_edit_8.css', 'multi_select_dropdown_1.css'],
                'js_files' => ['multi_select_dropdown_2.js']
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

            // 成功后跳转到列表页面，添加Toast消息到session
            $_SESSION['toast_message'] = '标签编辑成功';
            $_SESSION['toast_type'] = 'success';
            $this->redirect('/tags');
        } catch (\Exception $e) {
            error_log("Tag update error: " . $e->getMessage());
            
            // 出错时返回编辑页面并显示错误
            $tag = $this->curModel->findById($id);
            if (!$tag) {
                $this->redirect('/tags');
                return;
            }
            
            // 合并用户输入的数据到tag数据中
            $tag = array_merge($tag, $data);
            
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
            
            $this->render('tags/edit', [
                'tag' => $tag,
                'relatedContent' => $relatedContent,
                'contentOptions' => $contentOptions,
                'errors' => ['general' => '更新失败: ' . $e->getMessage()],
                'isCreateMode' => false,
                'pageTitle' => '编辑标签 - 视频分享网站管理后台',
                'css_files' => ['tag_edit_8.css', 'multi_select_dropdown_1.css'],
                'js_files' => ['multi_select_dropdown_2.js']
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

        // 准备视频数据用于JS
        $videoData = [];
        foreach ($allContent as $content) {
            $videoData[] = [
                'id' => (string)$content['id'],
                'text' => $content['title_cn'] ?: $content['title_en']
            ];
        }

        $this->render('tags/create', [
            'tag' => null,
            'relatedContent' => [],
            'contentOptions' => $contentOptions,
            'videoData' => $videoData,
            'selectedVideoIds' => [],
            'isCreateMode' => true,
            'pageTitle' => '创建标签 - 视频分享网站管理后台',
            'css_files' => ['tag_edit_8.css', 'multi_select_dropdown_1.css'],
            'js_files' => ['multi_select_dropdown_2.js', 'form_utils_2.js', 'tag_edit_12.js']
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
            'icon_class' => $request->post('icon_class') ?? 'bi-tag',
            'status_id' => (int)($request->post('status_id') ?? TagStatus::ENABLED->value),
            'content_cnt' => 0
        ];

        // 使用模型验证
        $errors = $this->curModel->validate($data, false);
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
            
            // 准备视频数据用于JS
            $videoData = [];
            foreach ($allContent as $content) {
                $videoData[] = [
                    'id' => (string)$content['id'],
                    'text' => $content['title_cn'] ?: $content['title_en']
                ];
            }

            $this->render('tags/create', [
                'tag' => $data, // 传递用户输入的数据
                'relatedContent' => [],
                'contentOptions' => $contentOptions,
                'videoData' => $videoData,
                'selectedVideoIds' => [],
                'errors' => $errors,
                'isCreateMode' => true,
                'pageTitle' => '创建标签 - 视频分享网站管理后台',
                'css_files' => ['tag_edit_8.css', 'multi_select_dropdown_1.css'],
                'js_files' => ['multi_select_dropdown_2.js', 'form_utils_2.js', 'tag_edit_12.js']
            ]);
            return;
        }

        try {
            $tagId = $this->curModel->create($data);

            $relatedVideos = $request->post('related_videos');
            if ($relatedVideos && is_array($relatedVideos)) {
                $contentIds = array_map('intval', $relatedVideos);
                $this->curModel->syncContentAssociations($tagId, $contentIds);
            }

            // 成功后跳转到列表页面，添加Toast消息到session
            $_SESSION['toast_message'] = '标签创建成功';
            $_SESSION['toast_type'] = 'success';
            $this->redirect('/tags');
        } catch (\Exception $e) {
            error_log("Tag creation error: " . $e->getMessage());
            
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
            
            // 准备视频数据用于JS
            $videoData = [];
            foreach ($allContent as $content) {
                $videoData[] = [
                    'id' => (string)$content['id'],
                    'text' => $content['title_cn'] ?: $content['title_en']
                ];
            }

            $this->render('tags/create', [
                'tag' => $data, // 传递用户输入的数据
                'relatedContent' => [],
                'contentOptions' => $contentOptions,
                'videoData' => $videoData,
                'selectedVideoIds' => [],
                'errors' => ['general' => '创建失败: ' . $e->getMessage()],
                'isCreateMode' => true,
                'pageTitle' => '创建标签 - 视频分享网站管理后台',
                'css_files' => ['tag_edit_8.css', 'multi_select_dropdown_1.css'],
                'js_files' => ['multi_select_dropdown_2.js', 'form_utils_2.js', 'tag_edit_12.js']
            ]);
        }
    }

    public function destroy(Request $request): void
    {
        $id = (int)$request->getParam(0);
        
        if (!$id) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid tag ID']);
            return;
        }

        try {
            $this->curModel->delete($id);
            $this->jsonResponse(['success' => true, 'message' => '标签删除成功']);
        } catch (\Exception $e) {
            error_log("Tag deletion error: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => '删除失败: ' . $e->getMessage()]);
        }
    }


    /**
     * for tag detail view page.
     * @param Request $request
     * @return void
     */
    public function show(Request $request): void
    {
        $id = (int)$request->getParam(0);
        $tag = $this->curModel->findById($id);

        if (!$tag) {
            $this->redirect('/backend/tags');
            return;
        }

        $relatedContent = $this->curModel->getRelatedContent($id);

        $this->render('tags/show', [
            'tag' => $tag,
            'relatedContent' => $relatedContent
        ]);
    }

    public function getContentForTag(Request $request): void
    {
        $tagId = (int)$request->get('tag_id');
        
        if (!$tagId) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid tag ID']);
            return;
        }

        try {
            $content = $this->curModel->getRelatedContent($tagId);
            $this->jsonResponse(['success' => true, 'content' => $content]);
        } catch (\Exception $e) {
            error_log("Get content error: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => '获取内容失败']);
        }
    }

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
                    if ($this->importSingleTag($rowData)) {
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
                'errors' => $errors
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
            $value = isset($data[$index]) ? trim($data[$index]) : '';
            $rowData[$header] = $value;
        }

        return $rowData;
    }

    /**
     * 导入单个标签
     */
    private function importSingleTag(array $data): bool
    {
        // 构建标签数据
        $tagData = [
            'name_cn' => $data['name_cn'] ?? '',
            'name_en' => $data['name_en'] ?? '',
            'short_desc_cn' => $data['short_desc_cn'] ?? '',
            'short_desc_en' => $data['short_desc_en'] ?? '',
            'desc_cn' => $data['desc_cn'] ?? '',
            'desc_en' => $data['desc_en'] ?? '',
            'color_class' => $data['color_class'] ?? 'btn-outline-primary',
            'icon_class' => $data['icon_class'] ?? 'bi-tag',
            'status_id' => isset($data['status_id']) ? (int)$data['status_id'] : TagStatus::ENABLED->value,
            'content_cnt' => 0
        ];

        // 验证数据
        $errors = $this->curModel->validate($tagData, false);
        if (!empty($errors)) {
            return false;
        }

        // 检查是否已存在同名标签
        if ($this->curModel->findByName($tagData['name_cn'], $tagData['name_en'])) {
            return false; // 跳过重复标签
        }

        // 创建标签
        try {
            $this->curModel->create($tagData);
            return true;
        } catch (\Exception $e) {
            error_log("Failed to create tag: " . $e->getMessage());
            return false;
        }
    }
}