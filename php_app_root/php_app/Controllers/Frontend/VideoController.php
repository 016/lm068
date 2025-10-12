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
        // 获取当前语言
        $currentLang = \App\Core\I18n::getCurrentLang();

        // 获取GET参数
        $page = max(1, (int)($this->request->getInput('page', 1)));
        $search = trim($this->request->getInput('search', ''));
        $tagIds = $this->parseIdsParam($this->request->getInput('tag_id', ''));
        $collectionIds = $this->parseIdsParam($this->request->getInput('collection_id', ''));
        $contentTypeIds = $this->parseIdsParam($this->request->getInput('content_type_id', ''));

        // 分页配置
        $perPage = 12; // 每页显示12个视频

        // 构建筛选条件
        $filters = [];
        if (!empty($search)) {
            $filters['search'] = $search;
        }
        if (!empty($tagIds)) {
            $filters['tag_ids'] = $tagIds;
        }
        if (!empty($collectionIds)) {
            $filters['collection_ids'] = $collectionIds;
        }
        if (!empty($contentTypeIds)) {
            $filters['content_type_ids'] = $contentTypeIds;
        }

        // 使用Model层方法获取视频列表
        $result = Content::getVideoList($filters, $page, $perPage);
        $videos = $result['items'];
        $totalVideos = $result['total'];
        $totalPages = $result['totalPages'];

        // 批量加载关联数据
        Content::loadRelations($videos);

        // 加载所有可用的标签和合集(用于筛选表单)
        $allTags = Tag::getEnabledTags();
        $allCollections = Collection::getEnabledCollections();

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
            // i18n相关数据
            'currentLang' => $currentLang,
            'supportedLangs' => \App\Core\I18n::getSupportedLangs(),
        ];

        // 渲染视图
        $content = $this->view('videos.index', $data);
        $pageTitle = $currentLang === 'zh' ? '视频列表' : 'Video List';
        echo $this->layout($content, $pageTitle, $data);
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
     * 视频详情页面
     */
    public function view(int $id): void
    {
        // 获取当前语言
        $currentLang = \App\Core\I18n::getCurrentLang();

        // 查找视频
        $video = Content::findOrFail($id);

        // 检查是否是视频类型且已发布
        if (!$video->isVideoType() || !$video->isPublished()) {
            http_response_code(404);
            echo json_encode(['error' => 'Video not found']);
            return;
        }

        // 增加浏览次数
        $video->incrementViewCount($id);

        // 获取视频的标签和合集
        $videoTags = $video->getRelatedTags($id);
        $videoCollections = $video->getRelatedCollections($id);

        // 获取视频的第三方平台链接
        $videoLinkModel = new \App\Models\VideoLink();
        $videoLinks = $videoLinkModel->getByContentId($id);

        // 获取评论 - 只显示已审核通过的评论
        $commentModel = new \App\Models\Comment();
        $commentsPerPage = 10;
        $commentPage = max(1, (int)($this->request->getInput('comment_page', 1)));

        // 使用分页查询评论
        $commentsResult = $commentModel->paginate(
            ['content_id' => $id, 'status_id' => 99],
            $commentsPerPage,
            $commentPage,
            'created_at DESC'
        );
        $comments = $commentsResult['items'];
        $commentsTotalPages = $commentsResult['totalPages'];
        $commentsTotalCount = $commentsResult['total'];

        // 获取最新公告（content_type_id = 1, 取3条）
        $announcementsResult = Content::getVideoList(
            ['content_type_ids' => [1]],
            1,
            3
        );
        $announcements = $announcementsResult['items'];

        // 获取关联视频（相同标签或合集的其他视频，取6条）
        $relatedVideos = $this->getRelatedVideos($id, $videoTags, $videoCollections, 6);

        // 获取推荐视频（随机推荐，取4条）
        $recommendedVideos = $this->getRecommendedVideos($id, 4);

        // 准备视图数据
        $data = [
            'video' => $video,
            'videoTags' => $videoTags,
            'videoCollections' => $videoCollections,
            'videoLinks' => $videoLinks,
            'comments' => $comments,
            'commentPage' => $commentPage,
            'commentsTotalPages' => $commentsTotalPages,
            'commentsTotalCount' => $commentsTotalCount,
            'announcements' => $announcements,
            'relatedVideos' => $relatedVideos,
            'recommendedVideos' => $recommendedVideos,
            'resourceUrl' => '/assets',
            'pageCss' => 'f-video-detail.css',
            'pageJs' => 'f-video-detail.js',
            // i18n相关数据
            'currentLang' => $currentLang,
            'supportedLangs' => \App\Core\I18n::getSupportedLangs(),
        ];

        // 渲染视图
        $content = $this->view('videos.view', $data);
        $pageTitle = $video->getTitle($currentLang);
        echo $this->layout($content, $pageTitle, $data);
    }

    /**
     * 获取关联视频（相同标签或合集）
     */
    private function getRelatedVideos(int $currentVideoId, array $tags, array $collections, int $limit = 6): array
    {
        $db = \App\Core\Database::getInstance();

        // 提取标签和合集ID
        $tagIds = array_column($tags, 'id');
        $collectionIds = array_column($collections, 'id');

        if (empty($tagIds) && empty($collectionIds)) {
            return [];
        }

        // 构建查询
        $params = ['current_video_id' => $currentVideoId];
        $whereClauses = [];

        if (!empty($tagIds)) {
            $tagPlaceholders = [];
            foreach ($tagIds as $idx => $tagId) {
                $key = "tag_id_{$idx}";
                $tagPlaceholders[] = ":{$key}";
                $params[$key] = (int)$tagId;
            }
            $whereClauses[] = "c.id IN (SELECT content_id FROM content_tag WHERE tag_id IN (" . implode(',', $tagPlaceholders) . "))";
        }

        if (!empty($collectionIds)) {
            $collectionPlaceholders = [];
            foreach ($collectionIds as $idx => $collectionId) {
                $key = "collection_id_{$idx}";
                $collectionPlaceholders[] = ":{$key}";
                $params[$key] = (int)$collectionId;
            }
            $whereClauses[] = "c.id IN (SELECT content_id FROM content_collection WHERE collection_id IN (" . implode(',', $collectionPlaceholders) . "))";
        }

        $whereClause = !empty($whereClauses) ? '(' . implode(' OR ', $whereClauses) . ')' : '1=1';

        $sql = "SELECT c.* FROM content c
                WHERE {$whereClause}
                AND c.id != :current_video_id
                AND c.status_id = 99
                AND c.content_type_id = 21
                ORDER BY c.created_at DESC
                LIMIT {$limit}";

        $rows = $db->fetchAll($sql, $params);

        // 转换为Content对象
        $videos = [];
        foreach ($rows as $row) {
            $content = new Content();
            $content->setOriginal($row);
            $content->setNew(false);
            $videos[] = $content;
        }

        // 加载关联数据
        Content::loadRelations($videos);

        return $videos;
    }

    /**
     * 获取推荐视频（随机）
     */
    private function getRecommendedVideos(int $currentVideoId, int $limit = 4): array
    {
        $db = \App\Core\Database::getInstance();

        $sql = "SELECT c.* FROM content c
                WHERE c.id != :current_video_id
                AND c.status_id = 99
                AND c.content_type_id = 21
                ORDER BY RAND()
                LIMIT {$limit}";

        $rows = $db->fetchAll($sql, ['current_video_id' => $currentVideoId]);

        // 转换为Content对象
        $videos = [];
        foreach ($rows as $row) {
            $content = new Content();
            $content->setOriginal($row);
            $content->setNew(false);
            $videos[] = $content;
        }

        // 加载关联数据
        Content::loadRelations($videos);

        return $videos;
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
