<?php

namespace App\Controllers\Backend;

use App\Core\Request;
use App\Models\Collection;
use App\Models\Content;
use App\Constants\CollectionStatus;
use App\Constants\ContentStatus;

class CollectionController extends BackendController
{
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
        $filters = $this->getSearchFilters(['id', 'name', 'description', 'content_cnt', 'icon_class', 'status_id', 'order_by'], $request);


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
        
        if ($request->isPost()) {
            // 处理POST请求（更新合集）
            $this->handleEditPost($request, $id);
        } else {
            // 处理GET请求（显示编辑表单）
            $this->handleEditGet($request, $id);
        }
    }

    private function handleEditGet(Request $request, int $id): void
    {
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
                'text' => $content['title_cn'] ?: $content['title_en']
            ];
        }

        $this->render('collections/edit', [
            'collection' => $collection,
            'relatedContent' => $relatedContent,
            'contentOptions' => $contentOptions,
            'selectedContentIds' => $selectedContentIds,
            'isCreateMode' => false,
            'title' => '编辑合集 - 视频分享网站管理后台',
            'css_files' => ['collection_edit_2.css', 'multi_select_dropdown_1.css'],
            'js_files' => ['multi_select_dropdown_2.js', 'form_utils_2.js', 'collection_edit_6.js']
        ]);
    }

    private function handleEditPost(Request $request, int $id): void
    {
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
            'status_id' => (int)($request->post('status_id') ?? CollectionStatus::DISABLED->value)
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
                    'text' => $content['title_cn'] ?: $content['title_en']
                ];
            }

            $this->render('collections/edit', [
                'collection' => $collection,
                'relatedContent' => $relatedContent,
                'contentOptions' => $contentOptions,
                'selectedContentIds' => $selectedContentIds,
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
                    'text' => $content['title_cn'] ?: $content['title_en']
                ];
            }

            $this->render('collections/edit', [
                'collection' => $collection,
                'relatedContent' => $relatedContent,
                'contentOptions' => $contentOptions,
                'selectedContentIds' => $selectedContentIds,
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
        if ($request->isPost()) {
            // 处理POST请求（创建合集）
            $this->handleCreatePost($request);
        } else {
            // 处理GET请求（显示创建表单）
            $this->handleCreateGet($request);
        }
    }

    private function handleCreateGet(Request $request): void
    {
        $allContent = $this->contentModel->findAll([
            'status_id' => ContentStatus::getVisibleStatuses()
        ]);

        $contentOptions = [];
        foreach ($allContent as $content) {
            $contentOptions[] = [
                'id' => $content['id'],
                'text' => $content['title_cn'] ?: $content['title_en']
            ];
        }

        $this->render('collections/create', [
            'collection' => null,
            'relatedContent' => [],
            'contentOptions' => $contentOptions,
            'selectedContentIds' => [],
            'isCreateMode' => true,
            'title' => '创建合集 - 视频分享网站管理后台',
            'css_files' => ['collection_edit_2.css', 'multi_select_dropdown_1.css'],
            'js_files' => ['multi_select_dropdown_2.js', 'form_utils_2.js', 'collection_edit_6.js']
        ]);
    }

    private function handleCreatePost(Request $request): void
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
            'status_id' => (int)($request->post('status_id') ?? CollectionStatus::DISABLED->value),
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

        // 检查名称是否已存在
        if (!empty($data['name_cn'])) {
            $existing = $this->curModel->findByField('name_cn', $data['name_cn']);
            if ($existing) {
                $errors['name_cn'] = '中文名称已存在';
            }
        }
        if (!empty($data['name_en'])) {
            $existing = $this->curModel->findByField('name_en', $data['name_en']);
            if ($existing) {
                $errors['name_en'] = '英文名称已存在';
            }
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
                    'text' => $content['title_cn'] ?: $content['title_en']
                ];
            }

            // 获取用户选择的关联内容
            $relatedVideos = $request->post('related_videos');
            $selectedContentIds = [];
            if (is_string($relatedVideos) && !empty($relatedVideos)) {
                $selectedContentIds = explode(',', $relatedVideos);
            } elseif (is_array($relatedVideos)) {
                $selectedContentIds = $relatedVideos;
            }

            $this->render('collections/create', [
                'collection' => $data, // 传递用户输入的数据
                'relatedContent' => [],
                'contentOptions' => $contentOptions,
                'selectedContentIds' => $selectedContentIds,
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

            // 处理关联内容
            $relatedVideos = $request->post('related_videos');
            if ($relatedVideos) {
                $contentIds = [];
                if (is_string($relatedVideos)) {
                    $contentIds = array_map('intval', explode(',', $relatedVideos));
                } elseif (is_array($relatedVideos)) {
                    $contentIds = array_map('intval', $relatedVideos);
                }
                $contentIds = array_filter($contentIds);
                if (!empty($contentIds)) {
                    $this->curModel->syncContentAssociations($collectionId, $contentIds);
                }
            }

            // 成功后跳转到列表页面并显示成功消息
            $this->setFlashMessage('合集创建成功！', 'success');
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
                    'text' => $content['title_cn'] ?: $content['title_en']
                ];
            }

            // 获取用户选择的关联内容
            $relatedVideos = $request->post('related_videos');
            $selectedContentIds = [];
            if (is_string($relatedVideos) && !empty($relatedVideos)) {
                $selectedContentIds = explode(',', $relatedVideos);
            } elseif (is_array($relatedVideos)) {
                $selectedContentIds = $relatedVideos;
            }

            $this->render('collections/create', [
                'collection' => $data, // 传递用户输入的数据
                'relatedContent' => [],
                'contentOptions' => $contentOptions,
                'selectedContentIds' => $selectedContentIds,
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

}