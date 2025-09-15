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
        $page = (int)($request->get('page') ?? 1);
        $perPage = (int)($request->get('per_page') ?? 10);
        $search = $request->get('search');
        $statusFilter = $request->get('status');
        $orderBy = $request->get('order_by') ?? 'created_at DESC';

        $conditions = [];
        if ($statusFilter !== null && $statusFilter !== '') {
            $conditions['status_id'] = (int)$statusFilter;
        }

        $collections = $this->collectionModel->findAllWithPagination($page, $perPage, $conditions, $search, $orderBy);
        $totalCount = $this->collectionModel->countWithConditions($conditions, $search);
        $stats = $this->collectionModel->getStats();

        $totalPages = ceil($totalCount / $perPage);

        $this->render('collections/index', [
            'collections' => $collections,
            'page' => $page,
            'perPage' => $perPage,
            'totalCount' => $totalCount,
            'totalPages' => $totalPages,
            'search' => $search,
            'statusFilter' => $statusFilter,
            'orderBy' => $orderBy,
            'stats' => $stats,
            'title' => '合集管理 - 视频分享网站管理后台',
            'css_files' => ['collection_list_2.css'],
            'js_files' => ['main_7.js', 'collection_list_2.js']
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
            'title' => '编辑合集 - 视频分享网站管理后台',
            'css_files' => ['collection_edit_2.css', 'multi_select_dropdown_1.css'],
            'js_files' => ['multi_select_dropdown_2.js', 'form_utils_2.js', 'collection_edit_6.js']
        ]);
    }

    public function update(Request $request): void
    {
        $id = (int)($request->post('id') ?? 0);
        
        if (!$id) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid collection ID']);
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
            $this->jsonResponse(['success' => false, 'message' => '合集名称不能为空']);
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

            $this->jsonResponse(['success' => true, 'message' => '合集更新成功']);
        } catch (\Exception $e) {
            error_log("Collection update error: " . $e->getMessage());
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

        $this->render('collections/edit', [
            'collection' => null,
            'relatedContent' => [],
            'contentOptions' => $contentOptions,
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

        if (empty($data['name_cn']) || empty($data['name_en'])) {
            $this->jsonResponse(['success' => false, 'message' => '合集名称不能为空']);
            return;
        }

        try {
            $collectionId = $this->collectionModel->create($data);

            $relatedVideos = $request->post('related_videos');
            if ($relatedVideos && is_array($relatedVideos)) {
                $contentIds = array_map('intval', $relatedVideos);
                $this->collectionModel->syncContentAssociations($collectionId, $contentIds);
            }

            $this->jsonResponse(['success' => true, 'message' => '合集创建成功', 'collection_id' => $collectionId]);
        } catch (\Exception $e) {
            error_log("Collection creation error: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => '创建失败: ' . $e->getMessage()]);
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
        $collectionIds = $request->post('collection_ids');

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
}