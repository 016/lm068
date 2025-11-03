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

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->curModel = new Collection();
        $this->contentModel = new Content();
    }

    public function index(Request $request): void
    {
        // 获取搜索过滤条件，支持所有搜索表单字段
        $filters = $this->getSearchFilters(['id', 'name', 'description', 'content_cnt', 'icon_class', 'status_id', 'order_by'], $request);


        // 获取所有符合条件的数据，不进行分页
        $collections = Collection::findAllWithFilters($filters);
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
        // 1. 通过ID查找Collection实例
        $collection = Collection::find($id);

        if (!$collection) {
            $this->redirect('/collections');
            return;
        }

        // 2. 把 $collection 传递到 view 实现渲染
        $this->renderEditForm($collection);
    }

    private function handleEditPost(Request $request, int $id): void
    {
        // 1. 通过ID查找Collection实例
        $collection = Collection::find($id);
        if (!$collection) {
            $this->redirect('/collections');
            return;
        }

        // 4. 对 POST 的数值进行提取并填充回 $collection
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
        $collection->fill($data);
        $contentIds = $request->post('content_ids');
        $contentIds = $contentIds == '' ? [] : array_map('intval', explode(',', $contentIds));

        // 5. 使用 Collection 的 validate 对提取的 post 数值进行验证
        if (!$collection->validate()) {
            // 6. 如果验证失败，使用 $collection->errors 返回给 view
            $this->renderEditForm($collection, $contentIds);
            return;
        }

        try {
            // 7. 验证通过，写入数据库
            if ($collection->save()) {
                // 处理关联内容

                if (!empty($contentIds)) {
                    $this->curModel->syncContentAssociations($id, $contentIds);
                }

                $this->setFlashMessage('合集更新成功', 'success');
                // 成功后跳转到列表页面
                $this->redirect('/collections');
            } else {
                // 保存失败，返回编辑页面并显示错误
                $this->renderEditForm($collection, $contentIds);
            }
        } catch (\Exception $e) {
            $fullMsg = $e->getFile() .' - L:'. $e->getLine(). ' - '. $e->getMessage() . '- <br/>'. $e->getTraceAsString();
            error_log("Collection update error: " . $fullMsg);
            $collection->errors['general'] = '更新失败: ' . $fullMsg;
            $this->renderEditForm($collection, $contentIds);
        }
    }

    private function renderEditForm(Collection $collection, ?array $postedContentIds = null): void
    {
        $relatedContents = $this->curModel->getRelatedContent($collection->id);

        // 如果是表单错误重新渲染，使用提交的数据；否则使用数据库中的关联数据
        if ($postedContentIds !== null) {
            $selectedContentIds =  $postedContentIds;
        } else {
            $selectedContentIds = array_column($relatedContents, 'id');
        }

        $contentsList = Content::loadList([
            'status_id' => ContentStatus::getVisibleStatuses()
        ], ['id'=>'id', 'text'=>'title_cn']);

        $this->render('collections/edit', [
            'collection' => $collection,  // 传递Collection实例
            'relatedContent' => $relatedContents,
            'contentsList' => $contentsList,
            'selectedContentIds' => $selectedContentIds,
            'title' => '编辑合集 - 视频分享网站管理后台',
            'css_files' => ['collection_edit_2.css', 'multi_select_dropdown_1.css'],
            'js_files' => ['multi_select_dropdown_3.js', 'form_utils_2.js', 'collection_edit_6.js']
        ]);
    }

    public function create(Request $request): void
    {
        // 1. 创建新的Collection实例
        $collection = new Collection();

        if ($request->isPost()) {
            // 4. 对 POST 的数值进行提取并填充回 $collection
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
            $collection->fill($data);

            $contentIds = $request->post('content_ids');
            $contentIds = $contentIds == '' ? [] : array_map('intval', explode(',', $contentIds));

            // 5. 使用 Collection 的 validate 对提取的 post 数值进行验证
            if (!$collection->validate()) {
                // 6. 如果验证失败，使用 $collection->errors 返回给 view
                $this->renderCreateForm($collection, $contentIds);
                return;
            }

            try {
                // 7. 验证通过，写入数据库
                if ($collection->save()) {
                    // 处理关联内容

                    if (!empty($contentIds)) {
                        $this->curModel->syncContentAssociations($collection->id, $contentIds);
                    }

                    // 成功后跳转到列表页面
                    $this->setFlashMessage('合集创建成功！', 'success');
                    $this->redirect('/collections');
                } else {
                    // 保存失败，返回创建页面并显示错误
                    $this->renderCreateForm($collection, $contentIds);
                }
            } catch (\Exception $e) {
                error_log("Collection creation error: " . $e->getMessage());
                $collection->errors['general'] = '创建失败: ' . $e->getMessage();
                $this->renderCreateForm($collection, $contentIds);
            }
            return;
        }

        // 2. 把 $collection 传递到 view 实现渲染（GET请求 - 显示表单）
        $this->renderCreateForm($collection);
    }

    private function renderCreateForm(Collection $collection, ?array $postedContentIds = null): void
    {
        $contentsList = Content::loadList([
            'status_id' => ContentStatus::getVisibleStatuses()
        ], ['id'=>'id', 'text'=>'title_cn']);

        // 如果是表单错误重新渲染，使用提交的数据；否则为空数组
        $selectedContentIds = $postedContentIds !== null ? $postedContentIds : [];


        $this->render('collections/create', [
            'collection' => $collection,  // 传递Collection实例而不是null
            'relatedContent' => [],
            'contentsList' => $contentsList,
            'selectedContentIds' => $selectedContentIds,
            'title' => '创建合集 - 视频分享网站管理后台',
            'css_files' => ['collection_edit_2.css', 'multi_select_dropdown_1.css'],
            'js_files' => ['multi_select_dropdown_3.js', 'form_utils_2.js', 'collection_edit_6.js']
        ]);
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
        $collection = Collection::find($id);  // 返回Collection实例

        if (!$collection) {
            $this->redirect('/collections');
            return;
        }

        $relatedContent = $this->curModel->getRelatedContent($id);

        $this->render('collections/show', [
            'collection' => $collection,  // 传递Collection实例
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