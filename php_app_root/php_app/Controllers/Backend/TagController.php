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
        $page = (int)($request->get('page') ?? 1);
        $perPage = (int)($request->get('per_page') ?? 10);
        $search = $request->get('search');
        $statusFilter = $request->get('status');
        $orderBy = $request->get('order_by') ?? 'created_at DESC';

        $conditions = [];
        if ($statusFilter !== null && $statusFilter !== '') {
            $conditions['status_id'] = (int)$statusFilter;
        }

        $tags = $this->tagModel->findAllWithPagination($page, $perPage, $conditions, $search, $orderBy);
        $totalCount = $this->tagModel->countWithConditions($conditions, $search);
        $stats = $this->tagModel->getStats();

        $totalPages = ceil($totalCount / $perPage);

        $this->render('tags/index', [
            'tags' => $tags,
            'page' => $page,
            'perPage' => $perPage,
            'totalCount' => $totalCount,
            'totalPages' => $totalPages,
            'search' => $search,
            'statusFilter' => $statusFilter,
            'orderBy' => $orderBy,
            'stats' => $stats,
            'pageTitle' => '标签管理 - 视频分享网站管理后台',
            'css_files' => ['tag_list_8.css'],
            'js_files' => ['tag_list_11.js']
        ]);
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
            'contentOptions' => $contentOptions
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
            'status_id' => (int)($request->post('status_id') ?? 0)
        ];

        // 验证必填字段
        if (empty($data['name_cn']) || empty($data['name_en'])) {
            $this->jsonResponse(['success' => false, 'message' => '标签名称不能为空']);
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

            $this->jsonResponse(['success' => true, 'message' => '标签更新成功']);
        } catch (\Exception $e) {
            error_log("Tag update error: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => '更新失败: ' . $e->getMessage()]);
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
            'contentOptions' => $contentOptions
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

        if (empty($data['name_cn']) || empty($data['name_en'])) {
            $this->jsonResponse(['success' => false, 'message' => '标签名称不能为空']);
            return;
        }

        try {
            $tagId = $this->tagModel->create($data);

            $relatedVideos = $request->post('related_videos');
            if ($relatedVideos && is_array($relatedVideos)) {
                $contentIds = array_map('intval', $relatedVideos);
                $this->tagModel->syncContentAssociations($tagId, $contentIds);
            }

            $this->jsonResponse(['success' => true, 'message' => '标签创建成功', 'tag_id' => $tagId]);
        } catch (\Exception $e) {
            error_log("Tag creation error: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => '创建失败: ' . $e->getMessage()]);
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

    public function exportData(Request $request): void
    {
        $format = $request->get('format') ?? 'json';
        
        try {
            $tags = $this->tagModel->findAll();
            
            if ($format === 'csv') {
                $this->exportCsv($tags);
            } else {
                $this->exportJson($tags);
            }
        } catch (\Exception $e) {
            error_log("Export error: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => '导出失败: ' . $e->getMessage()]);
        }
    }

    private function exportCsv(array $tags): void
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="tags_' . date('Y-m-d_H-i-s') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // 添加BOM以支持中文
        fwrite($output, "\xEF\xBB\xBF");
        
        // CSV头
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
    }

    private function exportJson(array $tags): void
    {
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="tags_' . date('Y-m-d_H-i-s') . '.json"');
        
        echo json_encode([
            'export_time' => date('Y-m-d H:i:s'),
            'total_count' => count($tags),
            'tags' => $tags
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

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