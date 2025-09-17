<?php

namespace App\Controllers\Backend;

use App\Core\Request;
use App\Models\Tag;
use App\Models\Content;

class TagController extends BackendController
{
    private Tag $tagModel;
    private Content $contentModel;

    public function __construct()
    {
        parent::__construct();
        $this->tagModel = new Tag();
        $this->contentModel = new Content();
    }

    public function index(Request $request): void
    {
        // 获取搜索过滤条件，支持所有搜索表单字段
        $filters = $this->getSearchFilters($request);
        
        // 根据过滤条件获取所有符合条件的标签数据（不分页，由JS处理分页）
        $tags = $this->tagModel->findAllWithFilters($filters);
        $stats = $this->tagModel->getStats();

        $this->render('tags/index', [
            'tags' => $tags,
            'filters' => $filters,
            'stats' => $stats,
            'pageTitle' => '标签管理 - 视频分享网站管理后台',
            'css_files' => ['tag_list_8.css'],
            'js_files' => ['tag_list_11.js']
        ]);
    }

    /**
     * 从请求中提取搜索过滤条件
     */
    private function getSearchFilters(Request $request): array
    {
        return [
            'id' => $request->get('id'),
            'name' => $request->get('name'),
            'content_cnt' => $request->get('content_cnt'),
            'icon_class' => $request->get('icon_class'),
            'status' => $request->get('status'),
            'order_by' => $request->get('order_by') ?? 'created_at DESC'
        ];
    }

    public function edit(Request $request): void
    {
        $id = (int)$request->getParam(0);
        $tag = $this->tagModel->findById($id);

        if (!$tag) {
            $this->redirect('/backend/tags');
            return;
        }

        $relatedContent = $this->tagModel->getRelatedContent($id);
        
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

        $this->render('tags/edit', [
            'tag' => $tag,
            'relatedContent' => $relatedContent,
            'contentOptions' => $contentOptions,
            'isCreateMode' => false,
            'pageTitle' => '编辑标签 - 视频分享网站管理后台',
            'css_files' => ['tag_edit_8.css', 'multi_select_dropdown_1.css'],
            'js_files' => ['multi_select_dropdown_2.js']
        ]);
    }

    public function update(Request $request): void
    {
        $id = (int)($request->post('id') ?? 0);
        
        if (!$id) {
            $this->redirect('/tags');
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

        // 使用模型验证，传入当前ID以排除自身
        $errors = $this->tagModel->validate($data, true, $id);
        if (!empty($errors)) {
            // 验证失败，返回编辑页面并显示错误
            $tag = $this->tagModel->findById($id);
            if (!$tag) {
                $this->redirect('/tags');
                return;
            }
            
            // 合并用户输入的数据到tag数据中
            $tag = array_merge($tag, $data);
            
            $relatedContent = $this->tagModel->getRelatedContent($id);
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
            $this->tagModel->update($id, $data);

            // 处理关联内容
            $relatedVideos = $request->post('related_videos');
            if ($relatedVideos !== null) {
                $contentIds = is_array($relatedVideos) ? array_map('intval', $relatedVideos) : [];
                $this->tagModel->syncContentAssociations($id, $contentIds);
            }

            // 成功后跳转到列表页面
            $this->redirect('/tags');
        } catch (\Exception $e) {
            error_log("Tag update error: " . $e->getMessage());
            
            // 出错时返回编辑页面并显示错误
            $tag = $this->tagModel->findById($id);
            if (!$tag) {
                $this->redirect('/tags');
                return;
            }
            
            // 合并用户输入的数据到tag数据中
            $tag = array_merge($tag, $data);
            
            $relatedContent = $this->tagModel->getRelatedContent($id);
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

        $this->render('tags/edit', [
            'tag' => null,
            'relatedContent' => [],
            'contentOptions' => $contentOptions,
            'isCreateMode' => true,
            'pageTitle' => '创建标签 - 视频分享网站管理后台',
            'css_files' => ['tag_edit_8.css', 'multi_select_dropdown_1.css'],
            'js_files' => ['multi_select_dropdown_2.js']
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
            'status_id' => (int)($request->post('status_id') ?? 1),
            'content_cnt' => 0
        ];

        // 使用模型验证
        $errors = $this->tagModel->validate($data, false);
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
            
            $this->render('tags/edit', [
                'tag' => $data, // 传递用户输入的数据
                'relatedContent' => [],
                'contentOptions' => $contentOptions,
                'errors' => $errors,
                'isCreateMode' => true,
                'pageTitle' => '创建标签 - 视频分享网站管理后台',
                'css_files' => ['tag_edit_8.css', 'multi_select_dropdown_1.css'],
                'js_files' => ['multi_select_dropdown_2.js']
            ]);
            return;
        }

        try {
            $tagId = $this->tagModel->create($data);

            $relatedVideos = $request->post('related_videos');
            if ($relatedVideos && is_array($relatedVideos)) {
                $contentIds = array_map('intval', $relatedVideos);
                $this->tagModel->syncContentAssociations($tagId, $contentIds);
            }

            // 成功后跳转到列表页面
            $this->redirect('/tags');
        } catch (\Exception $e) {
            error_log("Tag creation error: " . $e->getMessage());
            
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
            
            $this->render('tags/edit', [
                'tag' => $data, // 传递用户输入的数据
                'relatedContent' => [],
                'contentOptions' => $contentOptions,
                'errors' => ['general' => '创建失败: ' . $e->getMessage()],
                'isCreateMode' => true,
                'pageTitle' => '创建标签 - 视频分享网站管理后台',
                'css_files' => ['tag_edit_8.css', 'multi_select_dropdown_1.css'],
                'js_files' => ['multi_select_dropdown_2.js']
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
            $this->tagModel->delete($id);
            $this->jsonResponse(['success' => true, 'message' => '标签删除成功']);
        } catch (\Exception $e) {
            error_log("Tag deletion error: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => '删除失败: ' . $e->getMessage()]);
        }
    }

    public function bulkAction(Request $request): void
    {
        $action = $request->post('action');
        $tagIds = $request->post('tag_ids');

        if (!$action || !$tagIds || !is_array($tagIds)) {
            $this->jsonResponse(['success' => false, 'message' => '参数错误']);
            return;
        }

        $tagIds = array_map('intval', $tagIds);

        try {
            switch ($action) {
                case 'enable':
                    $this->tagModel->bulkUpdateStatus($tagIds, 1);
                    $message = '批量启用成功';
                    break;
                case 'disable':
                    $this->tagModel->bulkUpdateStatus($tagIds, 0);
                    $message = '批量禁用成功';
                    break;
                case 'delete':
                    $this->tagModel->bulkDelete($tagIds);
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

    // 导出功能已移至JS处理，删除相关PHP代码

    public function show(Request $request): void
    {
        $id = (int)$request->getParam(0);
        $tag = $this->tagModel->findById($id);

        if (!$tag) {
            $this->redirect('/backend/tags');
            return;
        }

        $relatedContent = $this->tagModel->getRelatedContent($id);

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
            $content = $this->tagModel->getRelatedContent($tagId);
            $this->jsonResponse(['success' => true, 'content' => $content]);
        } catch (\Exception $e) {
            error_log("Get content error: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => '获取内容失败']);
        }
    }
}