<?php

namespace App\Controllers\Frontend;

use App\Core\Config;
use App\Core\Request;
use App\Core\HashId;
use App\Helpers\ArrayHelper;
use App\Helpers\UrlHelper;
use App\Models\Content;
use App\Models\Tag;
use App\Models\Collection;
use App\Constants\ContentType;
use App\Constants\ContentStatus;
use http\Url;

class ContentController extends FrontendController
{
    public $curAction_zh = '/content';
    public $curAction_en = '/content';

    /**
     * set SEO param in function
     * @return void
     */
    public function setSEOParam($action, $data=null, array $params = []): void
    {
        $params = $params ?: $_GET; // 便于测试

        $currentLang = \App\Core\I18n::getCurrentLang();



        if ($action == 'index') {
            $tmpFilter = array_diff_key($params, array_flip(['s', 'lang']) );

            //check tag_id for show linked title
            if (!empty($params['tag_id'])) {
                //load tag
                $linkedTags = Tag::findAll(['id' => explode(',', $params['tag_id'])]);
                $tmpName = ArrayHelper::getLocalizedNames($linkedTags, 'name_cn', 'name_en');
                $this->seo_param['title'] .= $currentLang == 'zh' ? '标签: '.$tmpName.' - ' : 'Tag: '.$tmpName.' - ' ;
            }
            //check collection_id for show linked title
            if (!empty($params['collection_id'])) {
                //load collection
                $linkedCollections = Collection::findAll(['id' => explode(',', $params['collection_id'])]);
                $tmpName = ArrayHelper::getLocalizedNames($linkedCollections, 'name_cn', 'name_en');
                $this->seo_param['title'] .= $currentLang == 'zh' ? '合集: '.$tmpName.' - ' : 'Collection: '.$tmpName.' - ' ;
            }
            //check search kw for show linked title
            if (!empty($params['search'])) {
                $this->seo_param['title'] .= $currentLang == 'zh' ? '搜索: '.$params['search'].' - ' : 'Search: '.$params['search'].' - ' ;
            }

            //SEO params
            $this->seo_param['title'] .= $currentLang == 'zh' ? '内容列表' : 'Content List' ;
            $this->seo_param['desc'] .= $currentLang == 'zh' ? '内容列表页' : 'Content list page, can use URI parameters to filter';
            if (count($tmpFilter) > 0) {
                $tmpQuery = http_build_query($tmpFilter, '', '&');
                $this->seo_param['desc'] .= $currentLang == 'zh' ? ', 当前筛选条件为: ' : ', current filter by: ';
                $this->seo_param['desc'] .= $tmpQuery;
            }else{
                $this->seo_param['desc'] .= $currentLang == 'zh' ? ', 当前无筛选条件' : ', currently no filters';
            }

            if (isset($params['page']) && $params['page'] > 1){
                $this->seo_param['index'] = false;
            }//if index page have $params['page'] >=2 set index to noindex
        }


        if ($action == 'view') {
            //SEO
            $this->seo_param['title'] = $data->getTitle() ;
            $this->seo_param['desc'] = $data->getShortDescription();
            $this->curAction_zh = substr($params['s'], 0, strrpos($params['s'], '/') + 1) . $data->title_cn;
            $this->curAction_en = substr($params['s'], 0, strrpos($params['s'], '/') + 1) . $data->title_en;
        }

    }


    /**
     * 视频列表页面
     */
    public function index(): void
    {
        $this->setSEOParam('index');

        // 获取当前语言
        $currentLang = \App\Core\I18n::getCurrentLang();


        // 获取GET参数
        $page = max(1, (int)($this->request->getInput('page', 1)));
        $search = trim($this->request->getInput('search', ''));
        $tagIds = $this->parseIdsParam($this->request->getInput('tag_id', ''));
        $collectionIds = $this->parseIdsParam($this->request->getInput('collection_id', ''));
        $contentTypeIds = $this->parseIdsParam($this->request->getInput('content_type_id', ''));

        // 分页配置
        $perPage = Config::get('pagination.per_page'); // 每页显示12个视频

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
        $result = Content::getContentList($filters, $page, $perPage);
        $videos = $result['items'];
        $totalVideos = $result['total'];
        $totalPages = $result['totalPages'];

        // 批量加载关联数据
        Content::loadRelations($videos);

        // 加载所有可用的标签、合集和内容类型(用于筛选表单)
        $allTags = Tag::getEnabledTags();
        $allCollections = Collection::getEnabledCollections();
        $allContentTypes = ContentType::getAllContentTypes();

        // 准备当前查询参数 (供View使用)
        $currentParams = $this->getCurrentParams($search, $tagIds, $collectionIds, $contentTypeIds);

        // 准备JavaScript数据 (供View使用)
        $videoListJsData = $this->prepareVideoListJsData($allTags, $tagIds, $allCollections, $collectionIds, $allContentTypes, $contentTypeIds, $currentLang);

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
            'allContentTypes' => $allContentTypes,
            'currentParams' => $currentParams,  // 新增：当前查询参数
            'videoListJsData' => $videoListJsData,  // 新增：JavaScript数据
            'resourceUrl' => '/assets', // 前端资源URL前缀
            'pageCss' => [
                'f-video-list.css',
                'multi_select_dropdown_1.css',        // 基础组件样式
                'multi_select_dropdown_frontend.css'  // 前端主题适配
            ],
            'pageJs' => ['multi_select_dropdown_3.js', 'f-video-list_11.js'],    // 页面专用JS
            // i18n相关数据
            'currentLang' => $currentLang,
            'supportedLangs' => \App\Core\I18n::getSupportedLangs(),
        ];

        // 渲染视图
        $content = $this->view('contents.index', $data);
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
    public function show( Request $request): void
    {
        //@ee1 check title in url, if it's wrong use 403 or 30x to jump to correct url

        // 获取URL参数并解码为ID（HashId::decode会根据配置自动处理）
        $param = $request->getParam(0);
        $id = HashId::decode($param);

        // 如果解码失败，返回404
        if ($id === null) {
            http_response_code(404);
            echo json_encode(['error' => 'Video not found']);
            return;
        }

        // 获取当前语言
        $currentLang = \App\Core\I18n::getCurrentLang();

        // 查找视频
        $video = Content::findOrFail($id);

        // 检查是否是视频类型且已发布
        if (!$video->isPublished()) {
            http_response_code(404);
            echo json_encode(['error' => 'Video not found']);
            return;
        }

        // set SEO params
        $this->setSEOParam('view', $video);

        // 增加浏览次数
        $video->logPVAccess($id);

        // 获取视频的标签和合集
        $videoTags = $video->getRelatedTags($id);
        $videoCollections = $video->getRelatedCollections($id);

        // 获取视频的第三方平台链接
        $videoLinkModel = new \App\Models\VideoLink();
        $videoLinks = $videoLinkModel->getByContentId($id);

        // 获取评论 - 只显示已审核通过的评论（嵌套结构）
        $commentModel = new \App\Models\Comment();
        $commentsPerPage = 10;
        $commentPage = max(1, (int)($this->request->getInput('comment_page', 1)));

        // 使用嵌套评论查询方法
        $commentsResult = $commentModel->getNestedComments(
            $id,
            \App\Models\Comment::STATUS_APPROVED,
            $commentPage,
            $commentsPerPage
        );
        $comments = $commentsResult['items'];
        $commentsTotalPages = $commentsResult['totalPages'];
        $commentsTotalCount = $commentsResult['total'];

        // 获取最新公告（content_type_id = 1, 取3条）
        $announcementsResult = Content::getContentList(
            ['content_type_ids' => [1]],
            1,
            3
        );
        $announcements = $announcementsResult['items'];

        // 获取关联视频（相同标签或合集的其他视频，取6条）
        $relatedVideos = $this->getRelatedVideos($id, $videoTags, $videoCollections, 6);

        // 获取推荐视频（随机推荐，取4条）
        $recommendedVideos = $this->getRecommendedVideos($id, 4);

        // 计算视频链接统计数据
        $videoLinkStats = $this->calculateVideoLinkStats($videoLinks);

        // 准备视图数据
        $data = [
            'video' => $video,
            'videoTags' => $videoTags,
            'videoCollections' => $videoCollections,
            'videoLinks' => $videoLinks,
            'videoLinkStats' => $videoLinkStats,
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
        $content = $this->view('contents.view', $data);
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

    /**
     * 构建查询参数字符串 (供View调用)
     */
    public function buildQueryParams(array $params): string
    {
        $filteredParams = array_filter($params, function($value) {
            return !empty($value) || $value === '0' || $value === 0;
        });
        return !empty($filteredParams) ? '?' . http_build_query($filteredParams) : '';
    }

    /**
     * 构建分页链接URL (供View调用)
     */
    public function buildPaginationUrl(int $page, array $currentParams): string
    {
        $params = $currentParams;
        $params['page'] = $page;
        return '/content' . $this->buildQueryParams($params);
    }

    /**
     * 获取当前查询参数 (供View调用)
     */
    public function getCurrentParams(string $search, array $selectedTagIds, array $selectedCollectionIds, array $selectedContentTypeIds): array
    {
        $currentParams = [];
        if (!empty($search)) $currentParams['search'] = $search;
        if (!empty($selectedTagIds)) $currentParams['tag_id'] = implode(',', $selectedTagIds);
        if (!empty($selectedCollectionIds)) $currentParams['collection_id'] = implode(',', $selectedCollectionIds);
        if (!empty($selectedContentTypeIds)) $currentParams['content_type_id'] = implode(',', $selectedContentTypeIds);
        return $currentParams;
    }

    /**
     * 准备用于JavaScript的视频列表数据 (供View调用)
     */
    public function prepareVideoListJsData(array $allTags, array $selectedTagIds, array $allCollections, array $selectedCollectionIds, array $allContentTypes, array $selectedContentTypeIds, string $currentLang): array
    {
        return [
            'allTags' => array_map(function($tag) use ($currentLang) {
                return [
                    'id' => $tag['id'],
                    'text' => $currentLang === 'zh' ? $tag['name_cn'] : $tag['name_en']
                ];
            }, $allTags),
            'selectedTagIds' => array_map('strval', $selectedTagIds),
            'allCollections' => array_map(function($collection) use ($currentLang) {
                return [
                    'id' => $collection['id'],
                    'text' => $currentLang === 'zh' ? $collection['name_cn'] : $collection['name_en']
                ];
            }, $allCollections),
            'selectedCollectionIds' => array_map('strval', $selectedCollectionIds),
            'allContentTypes' => array_map(function($contentType) use ($currentLang) {
                return [
                    'id' => $contentType['id'],
                    'text' => $currentLang === 'zh' ? $contentType['name_cn'] : $contentType['name_en']
                ];
            }, $allContentTypes),
            'selectedContentTypeIds' => array_map('strval', $selectedContentTypeIds),
            'currentLang' => $currentLang,
            'placeholders' => [
                'tag' => $currentLang === 'zh' ? '请选择标签' : 'Select Tags',
                'tagSearch' => $currentLang === 'zh' ? '搜索标签...' : 'Search tags...',
                'collection' => $currentLang === 'zh' ? '请选择合集' : 'Select Collections',
                'collectionSearch' => $currentLang === 'zh' ? '搜索合集...' : 'Search collections...',
                'contentType' => $currentLang === 'zh' ? '请选择内容类型' : 'Select Content Types',
                'contentTypeSearch' => $currentLang === 'zh' ? '搜索内容类型...' : 'Search content types...'
            ]
        ];
    }

    /**
     * 构建评论分页链接 (供View调用)
     */
    public function buildCommentPaginationUrl(int $page, int $videoId, string $lang): string
    {
        $hashId = $this->getVideoHashId($videoId);
        return "/videos/{$hashId}?comment_page={$page}&lang={$lang}";
    }

    /**
     * 获取视频的Hash ID (供View调用)
     * HashId::encode会根据配置自动决定返回hash或原始数字ID
     *
     * @param int $videoId 视频数字ID
     * @return string Hash ID或数字ID（根据配置）
     */
    public function getVideoHashId(int $videoId): string
    {
        return HashId::encode($videoId);
    }

    /**
     * 构建视频详情页面URL (供View调用)
     * 统一管理视频详情页面URL的生成，方便后续调整
     *
     * @param int $videoId 视频数字ID
     * @param string $title 视频标题（用于SEO友好URL）
     * @param array $queryParams 可选的查询参数（如 lang 等）
     * @return string 完整的视频详情页面URL
     */
    public function buildVideoDetailUrl(int $videoId, string $title = '', array $queryParams = []): string
    {
        $hashId = $this->getVideoHashId($videoId);

        // 如果提供了标题，进行URL友好化处理
        $urlTitle = '';
        if (!empty($title)) {
            $urlTitle = UrlHelper::formatString($title);
        }

        // 构建基础URL
        $url = "/content/{$hashId}/{$urlTitle}";

        // 添加查询参数
        if (!empty($queryParams)) {
            $url .= '?' . http_build_query($queryParams);
        }

        return $url;
    }

    /**
     * 计算视频链接统计数据 (供View调用)
     */
    public function calculateVideoLinkStats(array $videoLinks): array
    {
        return [
            'totalPlays' => array_sum(array_column($videoLinks, 'play_cnt')),
            'totalLikes' => array_sum(array_column($videoLinks, 'like_cnt')),
            'totalFavorites' => array_sum(array_column($videoLinks, 'favorite_cnt')),
            'totalDownloads' => array_sum(array_column($videoLinks, 'download_cnt')),
            'totalComments' => array_sum(array_column($videoLinks, 'comment_cnt')),
            'totalShares' => array_sum(array_column($videoLinks, 'share_cnt')),
        ];
    }

    /**
     * 计算评论分页范围 (供View调用)
     */
    public function calculateCommentPaginationRange(int $currentPage, int $totalPages, int $range = 2): array
    {
        return [
            'start' => max(1, $currentPage - $range),
            'end' => min($totalPages, $currentPage + $range)
        ];
    }
}
