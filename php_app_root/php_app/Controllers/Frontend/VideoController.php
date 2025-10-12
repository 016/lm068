<?php

namespace App\Controllers\Frontend;

use App\Models\Content;
use App\Models\Tag;
use App\Models\Collection;
use App\Constants\ContentType;
use App\Constants\ContentStatus;

class VideoController extends FrontendController
{
    /**
     * 视频列表页面
     */
    public function index(): void
    {
        $contentModel = new Content();

        // 获取GET参数
        $page = max(1, (int)($this->request->getInput('page', 1)));
        $search = trim($this->request->getInput('search', ''));
        $tagIds = $this->parseIdsParam($this->request->getInput('tag_id', ''));
        $collectionIds = $this->parseIdsParam($this->request->getInput('collection_id', ''));
        $contentTypeIds = $this->parseIdsParam($this->request->getInput('content_type_id', ''));

        // 分页配置
        $perPage = 12; // 每页显示12个视频
        $offset = ($page - 1) * $perPage;

        // 构建查询条件
        $filters = [
            'status_id' => ContentStatus::PUBLISHED->value, // 只显示已发布的内容
        ];

        // 如果没有指定content_type_id，默认只显示视频类型
        if (empty($contentTypeIds)) {
            $filters['content_type_id'] = ContentType::VIDEO->value;
        }

        // 构建SQL查询
        $sql = "SELECT c.* FROM content c WHERE 1=1";
        $params = [];
        $whereClauses = [];

        // 状态过滤
        $whereClauses[] = "c.status_id = :status_id";
        $params['status_id'] = $filters['status_id'];

        // 内容类型过滤
        if (!empty($contentTypeIds)) {
            $placeholders = [];
            foreach ($contentTypeIds as $idx => $typeId) {
                $key = "content_type_id_{$idx}";
                $placeholders[] = ":{$key}";
                $params[$key] = (int)$typeId;
            }
            $whereClauses[] = "c.content_type_id IN (" . implode(',', $placeholders) . ")";
        } else {
            $whereClauses[] = "c.content_type_id = :default_content_type";
            $params['default_content_type'] = ContentType::VIDEO->value;
        }

        // 关键词搜索
        if (!empty($search)) {
            $whereClauses[] = "(c.title_cn LIKE :search_cn OR c.title_en LIKE :search_en OR c.short_desc_cn LIKE :search_desc_cn OR c.short_desc_en LIKE :search_desc_en)";
            $params['search_cn'] = "%{$search}%";
            $params['search_en'] = "%{$search}%";
            $params['search_desc_cn'] = "%{$search}%";
            $params['search_desc_en'] = "%{$search}%";
        }

        // 标签过滤
        if (!empty($tagIds)) {
            $tagPlaceholders = [];
            foreach ($tagIds as $idx => $tagId) {
                $key = "tag_id_{$idx}";
                $tagPlaceholders[] = ":{$key}";
                $params[$key] = (int)$tagId;
            }
            $whereClauses[] = "c.id IN (SELECT content_id FROM content_tag WHERE tag_id IN (" . implode(',', $tagPlaceholders) . "))";
        }

        // 合集过滤
        if (!empty($collectionIds)) {
            $collectionPlaceholders = [];
            foreach ($collectionIds as $idx => $collectionId) {
                $key = "collection_id_{$idx}";
                $collectionPlaceholders[] = ":{$key}";
                $params[$key] = (int)$collectionId;
            }
            $whereClauses[] = "c.id IN (SELECT content_id FROM content_collection WHERE collection_id IN (" . implode(',', $collectionPlaceholders) . "))";
        }

        // 组装完整SQL
        if (!empty($whereClauses)) {
            $sql .= " AND " . implode(" AND ", $whereClauses);
        }

        // 计算总数
        $countSql = "SELECT COUNT(*) as total FROM content c WHERE 1=1";
        if (!empty($whereClauses)) {
            $countSql .= " AND " . implode(" AND ", $whereClauses);
        }
        $totalResult = $contentModel->query($countSql, $params)->fetch(\PDO::FETCH_ASSOC);
        $totalVideos = (int)$totalResult['total'];
        $totalPages = ceil($totalVideos / $perPage);

        // 添加排序和分页
        $sql .= " ORDER BY c.created_at DESC LIMIT :limit OFFSET :offset";
        $params['limit'] = $perPage;
        $params['offset'] = $offset;

        // 执行查询
        $stmt = $contentModel->query($sql, $params);
        $videos = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // 为每个视频加载关联的标签和合集
        foreach ($videos as &$video) {
            $video['tags'] = $contentModel->getRelatedTags($video['id']);
            $video['collections'] = $contentModel->getRelatedCollections($video['id']);
        }
        unset($video); // 解除引用

        // 加载所有可用的标签和合集(用于筛选表单)
        $tagModel = new Tag();
        $collectionModel = new Collection();

        $allTags = $tagModel->query("SELECT * FROM tag WHERE status_id = 1 ORDER BY name_cn")->fetchAll(\PDO::FETCH_ASSOC);
        $allCollections = $collectionModel->query("SELECT * FROM collection WHERE status_id = 1 ORDER BY name_cn")->fetchAll(\PDO::FETCH_ASSOC);

        // 准备视图数据
        $data = [
            'videos' => $videos,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalVideos' => $totalVideos,
            'perPage' => $perPage,
            'search' => $search,
            'selectedTagIds' => $tagIds,
            'selectedCollectionIds' => $collectionIds,
            'selectedContentTypeIds' => $contentTypeIds,
            'allTags' => $allTags,
            'allCollections' => $allCollections,
            'resourceUrl' => '/assets', // 前端资源URL前缀
            'pageCss' => 'f-video-list.css',  // 页面专用CSS
            'pageJs' => 'f-video-list.js',    // 页面专用JS
        ];

        // 渲染视图
        $content = $this->view('videos.index', $data);
        echo $this->layout($content, '视频列表', $data);
    }

    /**
     * 解析ID参数(支持逗号分隔的多个ID)
     */
    private function parseIdsParam(string $param): array
    {
        if (empty($param)) {
            return [];
        }

        $ids = explode(',', $param);
        $ids = array_map('trim', $ids);
        $ids = array_filter($ids, 'is_numeric');
        $ids = array_map('intval', $ids);

        return array_unique($ids);
    }

    /**
     * 构建查询字符串(用于分页链接)
     */
    private function buildQueryString(array $params, array $override = []): string
    {
        $params = array_merge($params, $override);
        $params = array_filter($params, function($value) {
            return !empty($value) || $value === '0' || $value === 0;
        });

        return !empty($params) ? '?' . http_build_query($params) : '';
    }
}
