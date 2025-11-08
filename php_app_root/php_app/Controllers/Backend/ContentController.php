<?php

namespace App\Controllers\Backend;

use App\Core\Request;
use App\Models\Content;
use App\Models\Tag;
use App\Models\Collection;
use App\Constants\ContentStatus;
use App\Constants\ContentType;
use App\Constants\TagStatus;
use App\Constants\CollectionStatus;

class ContentController extends BackendController
{
    private Tag $tagModel;
    private Collection $collectionModel;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->curModel = new Content();
        $this->tagModel = new Tag();
        $this->collectionModel = new Collection();
    }

    public function index(Request $request): void
    {
        // 获取搜索过滤条件，支持所有搜索表单字段，包括tag_id和collection_id
        $filters = $this->getSearchFilters(['id', 'title', 'code', 'author', 'content_type_id', 'pv_cnt', 'status_id', 'tag_id', 'collection_id', 'order_by'], $request);

        // 根据过滤条件获取所有符合条件的内容数据（不分页，由JS处理分页）
        $content = Content::findAllWithFilters($filters);
        $stats = $this->curModel->getStats();

        // 如果存在tag_id或collection_id筛选，获取对应的名称用于显示
        $filterDisplayInfo = [];
        if (!empty($filters['tag_id'])) {
            $tag = $this->tagModel->find((int)$filters['tag_id']);
            if ($tag) {
                $filterDisplayInfo['tag'] = [
                    'id' => $tag->id,
                    'name' => $tag->name_cn ?: $tag->name_en
                ];
            }
        }
        if (!empty($filters['collection_id'])) {
            $collection = $this->collectionModel->find((int)$filters['collection_id']);
            if ($collection) {
                $filterDisplayInfo['collection'] = [
                    'id' => $collection->id,
                    'name' => $collection->name_cn ?: $collection->name_en
                ];
            }
        }

        $this->render('contents/index', [
            'content' => $content,
            'filters' => $filters,
            'filterDisplayInfo' => $filterDisplayInfo,
            'stats' => $stats,
            'pageTitle' => '内容管理 - 视频分享网站管理后台',
            'css_files' => ['content_list_2.css', 'multi_select_dropdown_1.css'],
            'js_files' => ['multi_select_dropdown_3.js', 'content_list_2.js']
        ]);
    }

    public function edit(Request $request): void
    {
        $id = (int)$request->getParam(0);
        
        // 1. 通过ID查找Content实例
        $content = Content::find($id);
        if (!$content) {
            $this->redirect('/contents');
            return;
        }

        // 处理 POST 请求（表单提交）
        if ($request->isPost()) {
            $postId = (int)($request->post('id') ?? 0);

            if (!$postId || $postId !== $id) {
                $this->jsonResponse(['success' => false, 'message' => 'Invalid content ID']);
                return;
            }

            // 4. 对 POST 的数值进行提取并填充回 $content
            $data = [
                'content_type_id' => (int)($request->post('content_type_id')),
                'author' => $request->post('author'),
                'code' => $request->post('code'),
                'title_cn' => $request->post('name_cn'),
                'title_en' => $request->post('name_en'),
                'short_desc_cn' => $request->post('short_desc_cn'),
                'short_desc_en' => $request->post('short_desc_en'),
                'desc_cn' => $request->post('desc_cn'),
                'desc_en' => $request->post('desc_en'),
                'sum_cn' => $request->post('sum_cn'),
                'sum_en' => $request->post('sum_en'),
                'duration' => $request->post('duration'),
                'pub_at' => !empty($request->post('pub_at')) ? $request->post('pub_at') : null,
                'status_id' => (int)($request->post('status_id'))
            ];

            // 处理文件上传
            if (!empty($_FILES)) {
                $content->handleFileUploads($_FILES);
            }

            $content->fill($data);

            $postedTagIds = $request->post('tag_ids');
            $postedTagIds = $postedTagIds == '' ? [] : array_map('intval', explode(',', $postedTagIds));
            $postedCollectionIds = $request->post('collection_ids');
            $postedCollectionIds = $postedCollectionIds == '' ? [] : array_map('intval', explode(',', $postedCollectionIds));

            // 5. 使用 Content 的 validate 对提取的 post 数值进行验证
            if (!$content->validate()) {
                // 6. 如果验证失败，使用 $content->errors 返回给 view

                $this->renderEditForm($content, $postedTagIds, $postedCollectionIds);
                return;
            }

            try {
                // 7. 验证通过，写入数据库
                if ($content->save()) {
                    // 处理关联标签
                    if (!empty($postedTagIds)) {
                        $this->curModel->syncTagAssociations($id, $postedTagIds);
                    }

                    // 处理关联合集
                    if (!empty($postedCollectionIds)) {
                        $this->curModel->syncCollectionAssociations($id, $postedCollectionIds);
                    }

                    // 成功后跳转到列表页面
                    $this->setFlashMessage('内容编辑成功', 'success');
                    $this->redirect('/contents');
                } else {
                    // 保存失败，返回编辑页面并显示错误
                    $postedTagIds = $request->post('tag_ids');
                    $postedCollectionIds = $request->post('collection_ids');
                    $this->renderEditForm($content, $postedTagIds, $postedCollectionIds);
                }
            } catch (\Exception $e) {
//                var_dump($e->getTraceAsString());
                error_log("Content update error: " . $e->getMessage());
                $content->errors['general'] = '更新失败: ' . $e->getMessage();
                $postedTagIds = $request->post('tag_ids');
                $postedCollectionIds = $request->post('collection_ids');
                $this->renderEditForm($content, $postedTagIds, $postedCollectionIds);
            }
            return;
        }

        // 2. 把 $content 传递到 view 实现渲染（GET请求 - 显示表单）
        $this->renderEditForm($content);
    }

    private function renderEditForm(Content $content, null|array|string $postedTagIds = null, null|array|string $postedCollectionIds = null): void
    {
        // 如果是表单错误重新渲染，使用提交的数据；否则使用数据库中的关联数据
        if ($postedTagIds !== null) {
            $selectedTagIds = $postedTagIds;
        } else {
            $relatedTags = $this->curModel->getRelatedTags($content->id);
            $selectedTagIds = array_column($relatedTags, 'id');
        }
        
        if ($postedCollectionIds !== null) {
            $selectedCollectionIds = $postedCollectionIds;
        } else {
            $relatedCollections = $this->curModel->getRelatedCollections($content->id);
            $selectedCollectionIds = array_column($relatedCollections, 'id');
        }
        
        $tagsList = Tag::loadList([
            'status_id' => TagStatus::getVisibleStatuses()
        ]);
        
        $collectionsList = Collection::loadList([
            'status_id' => CollectionStatus::getVisibleStatuses()
        ]);

        $this->render('contents/edit', [
            'content' => $content,  // 传递Content实例而不是数组
            'tagsList' => $tagsList,
            'collectionsList' => $collectionsList,
            'selectedTagIds' => $selectedTagIds,
            'selectedCollectionIds' => $selectedCollectionIds,
            'pageTitle' => '编辑内容 - 视频分享网站管理后台',
            'css_files' => ['content_edit_10.css', 'multi_select_dropdown_1.css'],
            'js_files' => ['multi_select_dropdown_3.js', 'form_utils_2.js', 'content_edit_11.js']
        ]);
    }

    public function create(Request $request): void
    {
        // 1. 创建新的Content实例
        $content = new Content();

        // 处理 POST 请求（表单提交）
        if ($request->isPost()) {
            // 4. 对 POST 的数值进行提取并填充回 $content
            $data = [
                'content_type_id' => (int)($request->post('content_type_id')),
                'author' => $request->post('author') ?? 'DP',
                'code' => $request->post('code') ?? '',
                'title_cn' => $request->post('name_cn'),
                'title_en' => $request->post('name_en'),
                'short_desc_cn' => $request->post('short_desc_cn') ?? '',
                'short_desc_en' => $request->post('short_desc_en') ?? '',
                'desc_cn' => $request->post('desc_cn') ?? '',
                'desc_en' => $request->post('desc_en') ?? '',
                'sum_cn' => $request->post('sum_cn') ?? '',
                'sum_en' => $request->post('sum_en') ?? '',
                'duration' => $request->post('duration') ?? '',
                'pub_at' => !empty($request->post('pub_at')) ? $request->post('pub_at') : null,
                'status_id' => (int)($request->post('status_id') ?? ContentStatus::DRAFT->value),
                'pv_cnt' => 0,
                'view_cnt' => 0
            ];

            // 处理文件上传
            if (!empty($_FILES)) {
                $content->handleFileUploads($_FILES);
            }

            $content->fill($data);

            $postedTagIds = $request->post('tag_ids');
            $postedTagIds = $postedTagIds == '' ? [] : array_map('intval', explode(',', $postedTagIds));
            $postedCollectionIds = $request->post('collection_ids');
            $postedCollectionIds = $postedCollectionIds == '' ? [] : array_map('intval', explode(',', $postedCollectionIds));

            // 5. 使用 Content 的 validate 对提取的 post 数值进行验证
            if (!$content->validate()) {
                // 6. 如果验证失败，使用 $content->errors 返回给 view

                $this->renderCreateForm($content, $postedTagIds, $postedCollectionIds);
                return;
            }


            try {
                // 7. 验证通过，写入数据库
                if ($content->save()) {
                    if (!empty($postedTagIds)) {
                        $this->curModel->syncTagAssociations($content->id, $postedTagIds);
                    }

                    if (!empty($postedCollectionIds)) {
                        $this->curModel->syncCollectionAssociations($content->id, $postedCollectionIds);
                    }

                    // 成功后跳转到列表页面
                    $this->setFlashMessage('内容创建成功', 'success');
                    $this->redirect('/contents');
                } else {
//                    var_dump('ee12  ');
                    // 保存失败，返回创建页面并显示错误
                    $postedTagIds = $request->post('tag_ids');
                    $postedCollectionIds = $request->post('collection_ids');
                    $this->renderCreateForm($content, $postedTagIds, $postedCollectionIds);
                }
            } catch (\Exception $e) {
                error_log("Content creation error: " . $e->getMessage());
//                var_dump('ee11');
                $content->errors['general'] = '创建失败: ' . $e->getMessage();
                $postedTagIds = $request->post('tag_ids');
                $postedCollectionIds = $request->post('collection_ids');
                $this->renderCreateForm($content, $postedTagIds, $postedCollectionIds);
            }
            return;
        }

        // 2. 把 $content 传递到 view 实现渲染（GET请求 - 显示表单）
        $this->renderCreateForm($content);
    }

    private function renderCreateForm(Content $content, array|string|null $postedTagIds = null, array|string|null $postedCollectionIds = null): void
    {
        $tagsList = Tag::loadList([
            'status_id' => TagStatus::getVisibleStatuses()
        ]);

        $collectionsList = Collection::loadList([
            'status_id' => CollectionStatus::getVisibleStatuses()
        ]);

        // 如果是表单错误重新渲染，使用提交的数据；否则为空数组
        $selectedTagIds = $postedTagIds !== null ? $postedTagIds : [];
        $selectedCollectionIds = $postedCollectionIds !== null ? $postedCollectionIds : [];

        $this->render('contents/create', [
            'content' => $content,  // 传递Content实例而不是数组
            'relatedTags' => [],
            'relatedCollections' => [],
            'tagsList' => $tagsList,
            'collectionsList' => $collectionsList,
            'selectedTagIds' => $selectedTagIds,
            'selectedCollectionIds' => $selectedCollectionIds,
            'pageTitle' => '创建内容 - 视频分享网站管理后台',
            'css_files' => ['content_edit_10.css', 'multi_select_dropdown_1.css'],
            'js_files' => ['multi_select_dropdown_3.js', 'form_utils_2.js', 'content_edit_11.js']
        ]);
    }

    public function destroy(Request $request): void
    {
        $id = (int)$request->getParam(0);

        if (!$id) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid content ID']);
            return;
        }

        try {
            $this->curModel->delete($id);
            $this->jsonResponse(['success' => true, 'message' => '内容删除成功']);
        } catch (\Exception $e) {
            error_log("Content deletion error: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => '删除失败: ' . $e->getMessage()]);
        }
    }

    /**
     * 获取CSV文件必需的字段 - 重写父类方法适配Content模型
     * 
     * @return array
     */
    protected function getRequiredCSVFields(): array
    {
        return ['title_en']; // 内容必须有英文标题，中文标题可选
    }

    /**
     * for content detail view page.
     * @param Request $request
     * @return void
     */
    public function show(Request $request): void
    {
        $id = (int)$request->getParam(0);
        $content = Content::find($id);  // 返回Content实例

        if (!$content) {
            $this->redirect('/contents');
            return;
        }

        $relatedTags = $this->curModel->getRelatedTags($id);
        $relatedCollections = $this->curModel->getRelatedCollections($id);

        $this->render('contents/show', [
            'content' => $content,  // 传递Content实例
            'relatedTags' => $relatedTags,
            'relatedCollections' => $relatedCollections
        ]);
    }

    public function copy(Request $request): void
    {
        $id = (int)$request->getParam(0);

        // 1. 通过ID查找要复制的Content实例
        $sourceContent = Content::find($id);
        if (!$sourceContent) {
            $this->redirect('/contents');
            return;
        }

        // 处理 POST 请求（复制后的表单提交 - 执行创建操作）
        if ($request->isPost()) {
            // 创建新的Content实例用于保存
            $newContent = new Content();

            // 4. 对 POST 的数值进行提取并填充到新实例
            $data = [
                'content_type_id' => (int)($request->post('content_type_id') ?? ContentType::VIDEO->value),
                'author' => $request->post('author') ?? 'DP',
                'code' => $request->post('code') ?? '',
                'title_cn' => $request->post('name_cn'),
                'title_en' => $request->post('name_en'),
                'short_desc_cn' => $request->post('short_desc_cn') ?? '',
                'short_desc_en' => $request->post('short_desc_en') ?? '',
                'desc_cn' => $request->post('desc_cn') ?? '',
                'desc_en' => $request->post('desc_en') ?? '',
                'sum_cn' => $request->post('sum_cn') ?? '',
                'sum_en' => $request->post('sum_en') ?? '',
                'duration' => $request->post('duration') ?? '',
                'status_id' => (int)($request->post('status_id') ?? ContentStatus::DRAFT->value),
                'pub_at' => $request->post('pub_at') ?? null,
                'pv_cnt' => 0,
                'view_cnt' => 0
            ];

            // 处理文件上传（缩略图）
            if (!empty($_FILES)) {
                $newContent->handleFileUploads($_FILES);
            }

            $newContent->fill($data);

            $postedTagIds = $request->post('tag_ids');
            $postedTagIds = $postedTagIds == '' ? [] : array_map('intval', explode(',', $postedTagIds));
            $postedCollectionIds = $request->post('collection_ids');
            $postedCollectionIds = $postedCollectionIds == '' ? [] : array_map('intval', explode(',', $postedCollectionIds));

            // 5. 使用 Content 的 validate 对提取的 post 数值进行验证
            if (!$newContent->validate()) {
                // 6. 如果验证失败，使用 $newContent->errors 返回给 view
                $this->renderCopyForm($sourceContent, $newContent, $postedTagIds, $postedCollectionIds);
                return;
            }

            try {
                // 7. 验证通过，写入数据库（执行创建操作）
                if ($newContent->save()) {
                    if (!empty($postedTagIds)) {
                        $this->curModel->syncTagAssociations($newContent->id, $postedTagIds);
                    }

                    if (!empty($postedCollectionIds)) {
                        $this->curModel->syncCollectionAssociations($newContent->id, $postedCollectionIds);
                    }

                    // 成功后跳转到列表页面
                    $this->setFlashMessage('内容复制成功', 'success');
                    $this->redirect('/contents');
                } else {
                    // 保存失败，返回复制页面并显示错误
                    $this->renderCopyForm($sourceContent, $newContent, $postedTagIds, $postedCollectionIds);
                }
            } catch (\Exception $e) {
                error_log("Content copy error: " . $e->getMessage());
                $newContent->errors['general'] = '复制失败: ' . $e->getMessage();
                $this->renderCopyForm($sourceContent, $newContent, $postedTagIds, $postedCollectionIds);
            }
            return;
        }

        // 2. GET请求 - 显示复制表单，使用源内容数据填充
        $this->renderCopyForm($sourceContent);
    }

    private function renderCopyForm(Content $sourceContent, ?Content $newContent = null, null|array|string $postedTagIds = null, null|array|string $postedCollectionIds = null): void
    {
        // 如果没有提供新内容实例，创建一个并从源内容复制数据
        if ($newContent === null) {
            $newContent = new Content();
            // 复制源内容的数据（除了缩略图）
            $newContent->content_type_id = $sourceContent->content_type_id;
            $newContent->title_cn = $sourceContent->title_cn;
            $newContent->title_en = $sourceContent->title_en;
            $newContent->code = $sourceContent->code;
            $newContent->author = $sourceContent->author;
            $newContent->duration = $sourceContent->duration;
            $newContent->short_desc_cn = $sourceContent->short_desc_cn;
            $newContent->short_desc_en = $sourceContent->short_desc_en;
            $newContent->desc_cn = $sourceContent->desc_cn;
            $newContent->desc_en = $sourceContent->desc_en;
            $newContent->sum_cn = $sourceContent->sum_cn;
            $newContent->sum_en = $sourceContent->sum_en;
            $newContent->status_id = $sourceContent->status_id;
            $newContent->pub_at = $sourceContent->pub_at;
            // 不复制缩略图和统计数据
        }

        // 如果是表单错误重新渲染，使用提交的数据；否则使用源内容的关联数据
        if ($postedTagIds !== null) {
            $selectedTagIds = $postedTagIds;
        } else {
            $relatedTags = $this->curModel->getRelatedTags($sourceContent->id);
            $selectedTagIds = array_column($relatedTags, 'id');
        }

        if ($postedCollectionIds !== null) {
            $selectedCollectionIds = $postedCollectionIds;
        } else {
            $relatedCollections = $this->curModel->getRelatedCollections($sourceContent->id);
            $selectedCollectionIds = array_column($relatedCollections, 'id');
        }

        $tagsList = Tag::loadList([
            'status_id' => TagStatus::getVisibleStatuses()
        ]);

        $collectionsList = Collection::loadList([
            'status_id' => CollectionStatus::getVisibleStatuses()
        ]);

        $this->render('contents/copy', [
            'content' => $newContent,  // 传递新的Content实例
            'sourceContent' => $sourceContent,  // 传递源Content实例用于参考
            'tagsList' => $tagsList,
            'collectionsList' => $collectionsList,
            'selectedTagIds' => $selectedTagIds,
            'selectedCollectionIds' => $selectedCollectionIds,
            'pageTitle' => '复制内容 - 视频分享网站管理后台',
            'css_files' => ['content_edit_10.css', 'multi_select_dropdown_1.css'],
            'js_files' => ['multi_select_dropdown_3.js', 'form_utils_2.js', 'content_edit_11.js']
        ]);
    }
}