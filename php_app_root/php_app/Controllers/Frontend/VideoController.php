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
