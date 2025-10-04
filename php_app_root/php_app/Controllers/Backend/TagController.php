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
        $filters = $this->getSearchFilters(['id', 'name', 'content_cnt', 'icon_class', 'status_id', 'order_by'], $request);

        // 根据过滤条件获取所有符合条件的标签数据（不分页，由JS处理分页）
        $tags = Tag::findAllWithFilters($filters);
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
        
        // 1. 通过ID查找Tag实例
        $tag = Tag::find($id);
        if (!$tag) {
            $this->redirect('/tags');
            return;
        }

        // 处理 POST 请求（表单提交）
        if ($request->isPost()) {
            $postId = (int)($request->post('id') ?? 0);
            
            if (!$postId || $postId !== $id) {
                $this->jsonResponse(['success' => false, 'message' => 'Invalid tag ID']);
                return;
            }

            // 4. 对 POST 的数值进行提取并填充回 $tag
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
            $tag->fill($data);

            // 5. 使用 Tag 的 validate 对提取的 post 数值进行验证
            if (!$tag->validate()) {
                // 6. 如果验证失败，使用 $tag->errors 返回给 view
                $this->renderEditForm($tag);
                return;
            }

            try {
                // 7. 验证通过，写入数据库
                if ($tag->save()) {
                    // 处理关联内容
                    $contentIds = $request->post('content_ids');
                    if ($contentIds !== null) {
                        $contentIdsArray = $contentIds == '' ? [] : explode(',', $contentIds);
                        $this->curModel->syncContentAssociations($id, $contentIdsArray);
                    }

                    // 成功后跳转到列表页面
                    $this->setFlashMessage('标签编辑成功', 'success');
                    $this->redirect('/tags');
                } else {
                    // 保存失败，返回编辑页面并显示错误
                    $this->renderEditForm($tag);
                }
            } catch (\Exception $e) {
                error_log("Tag update error: " . $e->getMessage());
                $tag->errors['general'] = '更新失败: ' . $e->getMessage();
                $this->renderEditForm($tag);
            }
            return;
        }

        // 2. 把 $tag 传递到 view 实现渲染（GET请求 - 显示表单）
        $this->renderEditForm($tag);
    }

    private function renderEditForm(Tag $tag): void
    {
        $relatedContent = $this->curModel->getRelatedContent($tag->id);
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
            'tag' => $tag,  // 传递Tag实例而不是数组
            'relatedContent' => $relatedContent,
            'contentOptions' => $contentOptions,
            'pageTitle' => '编辑标签 - 视频分享网站管理后台',
            'css_files' => ['tag_edit_8.css', 'multi_select_dropdown_1.css'],
            'js_files' => ['multi_select_dropdown_2.js', 'form_utils_2.js', 'tag_edit_12.js']
        ]);
    }

    public function create(Request $request): void
    {
        // 1. 创建新的Tag实例
        $tag = new Tag();

        // 处理 POST 请求（表单提交）
        if ($request->isPost()) {
            // 4. 对 POST 的数值进行提取并填充回 $tag
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
            $tag->fill($data);

            // 5. 使用 Tag 的 validate 对提取的 post 数值进行验证
            if (!$tag->validate()) {
                // 6. 如果验证失败，使用 $tag->errors 返回给 view
                $this->renderCreateForm($tag);
                return;
            }

            try {
                // 7. 验证通过，写入数据库
                if ($tag->save()) {
                    $contentIds = $request->post('content_ids');
                    if ($contentIds && is_array($contentIds)) {
                        $contentIdsArray = array_map('intval', $contentIds);
                        $this->curModel->syncContentAssociations($tag->id, $contentIdsArray);
                    }

                    // 成功后跳转到列表页面
                    $this->setFlashMessage('标签创建成功', 'success');
                    $this->redirect('/tags');
                } else {
                    // 保存失败，返回创建页面并显示错误
                    $this->renderCreateForm($tag);
                }
            } catch (\Exception $e) {
                error_log("Tag creation error: " . $e->getMessage());
                $tag->errors['general'] = '创建失败: ' . $e->getMessage();
                $this->renderCreateForm($tag);
            }
            return;
        }

        // 2. 把 $tag 传递到 view 实现渲染（GET请求 - 显示表单）
        $this->renderCreateForm($tag);
    }

    private function renderCreateForm(Tag $tag): void
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
            'tag' => $tag,  // 传递Tag实例而不是数组
            'relatedContent' => [],
            'contentOptions' => $contentOptions,
            'videoData' => $videoData,
            'selectedVideoIds' => [],
            'pageTitle' => '创建标签 - 视频分享网站管理后台',
            'css_files' => ['tag_edit_8.css', 'multi_select_dropdown_1.css'],
            'js_files' => ['multi_select_dropdown_2.js', 'form_utils_2.js', 'tag_edit_12.js']
        ]);
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
        $tag = Tag::find($id);  // 返回Tag实例

        if (!$tag) {
            $this->redirect('/tags');
            return;
        }

        $relatedContent = $this->curModel->getRelatedContent($id);

        $this->render('tags/show', [
            'tag' => $tag,  // 传递Tag实例
            'relatedContent' => $relatedContent
        ]);
    }

}