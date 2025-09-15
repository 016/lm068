<?php

namespace App\Controllers\Backend;

use App\Controllers\Backend\BackendController;
use App\Models\Tag;
use App\Models\Content;
use App\Core\Request;

class TagController extends BackendController
{
    private $tagModel;
    private $contentModel;

    public function __construct()
    {
        parent::__construct();
        $this->tagModel = new Tag();
        $this->contentModel = new Content();
    }

    public function index(Request $request): void
    {
        if (!$this->requireAuth()) {
            return;
        }

        $page = max(1, (int)$request->getInput('page', 1));
        $perPage = max(1, min(100, (int)$request->getInput('per_page', 10)));
        $search = $request->getInput('search');
        $status = $request->getInput('status');
        $sortBy = $request->getInput('sort_by', 'created_at');
        $sortDirection = $request->getInput('sort_direction', 'DESC');

        $conditions = [];
        if ($status !== null && $status !== '') {
            $conditions['status_id'] = $status;
        }

        $orderBy = "{$sortBy} {$sortDirection}";

        $tags = $this->tagModel->findAllWithPagination($page, $perPage, $conditions, $search, $orderBy);
        $totalTags = $this->tagModel->countWithConditions($conditions, $search);
        $stats = $this->tagModel->getStats();

        $totalPages = ceil($totalTags / $perPage);

        if ($request->isAjax()) {
            $this->json([
                'success' => true,
                'data' => [
                    'tags' => $tags,
                    'pagination' => [
                        'current_page' => $page,
                        'per_page' => $perPage,
                        'total' => $totalTags,
                        'total_pages' => $totalPages,
                        'has_prev' => $page > 1,
                        'has_next' => $page < $totalPages
                    ],
                    'stats' => $stats
                ]
            ]);
            return;
        }

        $this->render('tags.index', [
            'title' => '标签管理',
            'tags' => $tags,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $totalTags,
                'total_pages' => $totalPages,
                'has_prev' => $page > 1,
                'has_next' => $page < $totalPages
            ],
            'stats' => $stats,
            'search' => $search,
            'status' => $status,
            'sort_by' => $sortBy,
            'sort_direction' => $sortDirection,
            'css_files' => ['tag_list_5.css'],
            'js_files' => ['tag_list_5.js']
        ]);
    }

    public function create(Request $request): void
    {
        if (!$this->requireAuth()) {
            return;
        }

        $this->render('tags.create', [
            'title' => '创建标签',
            'css_files' => ['tag_edit_8.css', 'multi_select_dropdown_1.css'],
            'js_files' => ['multi_select_dropdown_2.js', 'form_utils_2.js', 'tag_edit_12.js']
        ]);
    }

    public function store(Request $request): void
    {
        if (!$this->requireAuth()) {
            return;
        }

        $validation = $this->validate([
            'name_cn' => 'required|max:50',
            'name_en' => 'required|max:50',
            'short_desc_cn' => 'max:100',
            'short_desc_en' => 'max:100',
            'desc_cn' => 'max:500',
            'desc_en' => 'max:500'
        ]);

        if (!empty($validation)) {
            $this->json([
                'success' => false,
                'message' => '验证失败',
                'errors' => $validation
            ], 422);
            return;
        }

        try {
            $tagData = [
                'name_cn' => $this->input('name_cn'),
                'name_en' => $this->input('name_en'),
                'short_desc_cn' => $this->input('short_desc_cn', ''),
                'short_desc_en' => $this->input('short_desc_en', ''),
                'desc_cn' => $this->input('desc_cn', ''),
                'desc_en' => $this->input('desc_en', ''),
                'color_class' => $this->input('color_class', 'btn-outline-primary'),
                'icon_class' => $this->input('icon_class', ''),
                'status_id' => (int)$this->input('status_id', 1),
                'content_cnt' => 0
            ];

            $tagId = $this->tagModel->create($tagData);

            $relatedVideos = $this->input('related_videos', []);
            if (!empty($relatedVideos) && is_array($relatedVideos)) {
                $this->tagModel->syncContentAssociations($tagId, $relatedVideos);
            }

            $this->json([
                'success' => true,
                'message' => '标签创建成功',
                'data' => ['id' => $tagId]
            ]);

        } catch (\Exception $e) {
            $this->json([
                'success' => false,
                'message' => '创建失败: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit(Request $request): void
    {
        if (!$this->requireAuth()) {
            return;
        }

        $id = (int)($request->getParam(0) ?? 0);
        if (!$id) {
            $this->redirect('/tags');
            return;
        }

        $tag = $this->tagModel->find($id);
        if (!$tag) {
            if ($request->isAjax()) {
                $this->json(['success' => false, 'message' => '标签不存在'], 404);
                return;
            }
            $this->redirect('/tags');
            return;
        }

        $relatedContent = $this->tagModel->getRelatedContent($id);
        $allContent = $this->contentModel->findAll(['content_type_id' => 21]); // 只获取视频内容

        if ($request->isAjax()) {
            $this->json([
                'success' => true,
                'data' => [
                    'tag' => $tag,
                    'related_content' => $relatedContent,
                    'all_content' => $allContent
                ]
            ]);
            return;
        }

        $this->render('tags.edit', [
            'title' => '编辑标签',
            'tag' => $tag,
            'related_content' => $relatedContent,
            'all_content' => $allContent,
            'css_files' => ['tag_edit_8.css', 'multi_select_dropdown_1.css'],
            'js_files' => ['multi_select_dropdown_2.js', 'form_utils_2.js', 'tag_edit_12.js']
        ]);
    }

    public function update(Request $request): void
    {
        if (!$this->requireAuth()) {
            return;
        }

        $id = (int)($request->getParam(0) ?? 0);
        if (!$id) {
            $this->json(['success' => false, 'message' => '无效的标签ID'], 400);
            return;
        }

        $tag = $this->tagModel->find($id);
        if (!$tag) {
            $this->json(['success' => false, 'message' => '标签不存在'], 404);
            return;
        }

        $validation = $this->validate([
            'name_cn' => 'required|max:50',
            'name_en' => 'required|max:50',
            'short_desc_cn' => 'max:100',
            'short_desc_en' => 'max:100',
            'desc_cn' => 'max:500',
            'desc_en' => 'max:500'
        ]);

        if (!empty($validation)) {
            $this->json([
                'success' => false,
                'message' => '验证失败',
                'errors' => $validation
            ], 422);
            return;
        }

        try {
            $tagData = [
                'name_cn' => $this->input('name_cn'),
                'name_en' => $this->input('name_en'),
                'short_desc_cn' => $this->input('short_desc_cn', ''),
                'short_desc_en' => $this->input('short_desc_en', ''),
                'desc_cn' => $this->input('desc_cn', ''),
                'desc_en' => $this->input('desc_en', ''),
                'color_class' => $this->input('color_class', 'btn-outline-primary'),
                'icon_class' => $this->input('icon_class', ''),
                'status_id' => (int)$this->input('status_id', 1)
            ];

            $this->tagModel->update($id, $tagData);

            $relatedVideos = $this->input('related_videos', []);
            if (is_array($relatedVideos)) {
                $this->tagModel->syncContentAssociations($id, $relatedVideos);
            }

            $this->json([
                'success' => true,
                'message' => '标签更新成功'
            ]);

        } catch (\Exception $e) {
            $this->json([
                'success' => false,
                'message' => '更新失败: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Request $request): void
    {
        if (!$this->requireAuth()) {
            return;
        }

        $id = (int)($request->getParam(0) ?? 0);
        if (!$id) {
            $this->json(['success' => false, 'message' => '无效的标签ID'], 400);
            return;
        }

        $tag = $this->tagModel->find($id);
        if (!$tag) {
            $this->json(['success' => false, 'message' => '标签不存在'], 404);
            return;
        }

        $relatedContent = $this->tagModel->getRelatedContent($id);

        $this->json([
            'success' => true,
            'data' => [
                'tag' => $tag,
                'related_content' => $relatedContent
            ]
        ]);
    }

    public function delete(Request $request): void
    {
        if (!$this->requireAuth()) {
            return;
        }

        $id = (int)($request->getParam(0) ?? 0);
        if (!$id) {
            $this->json(['success' => false, 'message' => '无效的标签ID'], 400);
            return;
        }

        $tag = $this->tagModel->find($id);
        if (!$tag) {
            $this->json(['success' => false, 'message' => '标签不存在'], 404);
            return;
        }

        try {
            $this->tagModel->delete($id);
            $this->json([
                'success' => true,
                'message' => '标签删除成功'
            ]);
        } catch (\Exception $e) {
            $this->json([
                'success' => false,
                'message' => '删除失败: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkAction(Request $request): void
    {
        if (!$this->requireAuth()) {
            return;
        }

        $action = $this->input('action');
        $tagIds = $this->input('tag_ids', []);

        if (empty($tagIds) || !is_array($tagIds)) {
            $this->json(['success' => false, 'message' => '请选择要操作的标签'], 400);
            return;
        }

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
                    $this->json(['success' => false, 'message' => '无效的操作'], 400);
                    return;
            }

            $this->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            $this->json([
                'success' => false,
                'message' => '操作失败: ' . $e->getMessage()
            ], 500);
        }
    }

    public function export(Request $request): void
    {
        if (!$this->requireAuth()) {
            return;
        }

        $format = $this->input('format', 'json');
        $tags = $this->tagModel->findAll();

        if ($format === 'csv') {
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="tags_' . date('Y-m-d_H-i-s') . '.csv"');
            
            $output = fopen('php://output', 'w');
            fputcsv($output, ['ID', '中文名称', '英文名称', '中文简介', '英文简介', '状态', '关联内容数', '创建时间']);
            
            foreach ($tags as $tag) {
                fputcsv($output, [
                    $tag['id'],
                    $tag['name_cn'],
                    $tag['name_en'],
                    $tag['short_desc_cn'],
                    $tag['short_desc_en'],
                    $tag['status_id'] ? '启用' : '禁用',
                    $tag['content_cnt'],
                    $tag['created_at']
                ]);
            }
            
            fclose($output);
        } else {
            header('Content-Type: application/json; charset=utf-8');
            header('Content-Disposition: attachment; filename="tags_' . date('Y-m-d_H-i-s') . '.json"');
            echo json_encode($tags, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }
    }
}