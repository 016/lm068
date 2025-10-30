<?php

namespace App\Models;

use App\Core\HashId;
use App\Core\UploadableModel;
use App\Constants\ContentStatus;
use App\Constants\ContentType;
use App\Helpers\RequestHelper;
use App\Helpers\UrlHelper;
use App\Interfaces\HasStatuses;

class Content extends UploadableModel implements HasStatuses
{
    protected static string $table = 'content';
    protected $primaryKey = 'id';
    protected $fillable = [
        'content_type_id', 'author', 'code', 'title_en', 'title_cn',
        'desc_en', 'desc_cn', 'sum_en', 'sum_cn', 'short_desc_en', 'short_desc_cn',
        'thumbnail', 'duration', 'pv_cnt', 'view_cnt', 'status_id', 'pub_at'
    ];
    protected $timestamps = true;

    /**
     * 可上传属性配置
     */
    protected array $uploadableAttributes = [
        'thumbnail' => [
            'type' => 'image',
            'path_key' => 'pics_path',
            'required' => false,
            'replace_old' => true,  // 启用旧文件替换，只保留最新的缩略图
        ]
    ];

    // 默认属性值
    protected array $defaults = [
        'content_type_id' => 21, // 默认为视频
        'author' => 'DP',
        'code' => '',
        'title_en' => '',
        'title_cn' => '',
        'desc_en' => '',
        'desc_cn' => '',
        'sum_en' => '',
        'sum_cn' => '',
        'short_desc_en' => '',
        'short_desc_cn' => '',
        'thumbnail' => '',
        'duration' => 0,
        'pv_cnt' => 0,
        'view_cnt' => 0,
        'status_id' => 1
    ];

    public function __construct()
    {
        parent::__construct();
        // 设置默认值
        $this->attributes = array_merge($this->defaults, $this->attributes);
    }

    /**
     * 实现接口方法，返回对应的状态枚举类
     */
    public static function getStatusEnum(): string
    {
        return ContentStatus::class;
    }

    /**
     * 定义验证规则
     * @param bool $isUpdate 是否为更新操作
     * @return array 验证规则
     */
    public function rules(bool $isUpdate = false): array
    {
        return [
            'content_type_id' => 'required|numeric',
            'code' => 'max:50',
            'title_en' => 'required|max:255|unique',
            'title_cn' => 'required|max:255|unique',
            'desc_en' => 'max:65535', // TEXT类型
            'desc_cn' => 'max:65535', // TEXT类型
            'sum_en' => 'max:65535', // TEXT类型
            'sum_cn' => 'max:65535', // TEXT类型
            'short_desc_en' => 'max:1000',
            'short_desc_cn' => 'max:1000',
            'author' => 'max:255',
            'thumbnail' => 'max:255',
            'duration' => 'numeric',
            'status_id' => 'numeric'
        ];
    }

    /**
     * 获取字段标签
     * @return array 字段标签映射
     */
    protected function getFieldLabels(): array
    {
        return [
            'content_type_id' => '内容类型',
            'author' => '作者',
            'code' => '内部代码',
            'title_en' => '英文标题',
            'title_cn' => '中文标题',
            'desc_en' => '英文描述',
            'desc_cn' => '中文描述',
            'sum_en' => '英文总结',
            'sum_cn' => '中文总结',
            'short_desc_en' => '英文简介',
            'short_desc_cn' => '中文简介',
            'thumbnail' => '缩略图',
            'duration' => '时长',
            'status_id' => '状态'
        ];
    }

    /**
     * 构建视频详情页面URL (供View调用)
     * 统一管理视频详情页面URL的生成，方便后续调整
     * @param string|null $targetLang 目标跳转语言
     * @param array $queryParams
     * @return string
     */
    public function buildContentDetailUrl(?string $targetLang = null, array $queryParams = []): string
    {
        // 构建基础URL 前缀
        $urlPrefix = "/content/".HashId::encode($this->id)."/".UrlHelper::formatString($this->getTitle('en'));

        return UrlHelper::generateUri($urlPrefix, $targetLang, $queryParams);
    }

    /**
     * 根据指定语言获取标题
     *
     * @param string|null $lang 语言代码 (zh/en), 为null时使用当前语言
     * @return string 对应语言的标题
     */
    public function getTitle(?string $lang = null): string
    {
        $lang = $lang ?? \App\Core\I18n::getCurrentLang();
        $title = $lang === 'zh' ? $this->title_cn : $this->title_en;

        // 如果指定语言的标题为空,降级到另一个语言
        if (empty($title)) {
            $title = $lang === 'zh' ? $this->title_en : $this->title_cn;
        }

        return $title ?? '';
    }

    /**
     * 根据指定语言获取描述
     *
     * @param string|null $lang 语言代码 (zh/en), 为null时使用当前语言
     * @return string 对应语言的描述
     */
    public function getDescription(?string $lang = null): string
    {
        $lang = $lang ?? \App\Core\I18n::getCurrentLang();

        //load desc
        $desc = $lang === 'zh' ? $this->desc_cn : $this->desc_en;

        // 如果指定语言的描述为空,降级到另一个语言
        if (empty($desc)) {
            $desc = $lang === 'zh' ? $this->desc_en : $this->desc_cn;
        }

        return $desc ?? '';
    }
    /**
     * 根据指定语言获取总结
     *
     * @param string|null $lang 语言代码 (zh/en), 为null时使用当前语言
     * @return string 对应语言的描述
     */
    public function getSummary(?string $lang = null): string
    {
        $lang = $lang ?? \App\Core\I18n::getCurrentLang();

        //load sum
        $sum = $lang === 'zh' ? $this->sum_cn : $this->sum_en;
        if (empty($sum)) {
            return '';
        }

        //split title
        $contentTitle = $lang === 'zh' ? $this->title_cn : $this->title_en;

        //return string
        $returnString = "# " . $contentTitle . " \n\n ". $sum;

        return $returnString;
    }

    /**
     * 根据指定语言获取简介
     *
     * @param string|null $lang 语言代码 (zh/en), 为null时使用当前语言
     * @return string 对应语言的简介
     */
    public function getShortDescription(?string $lang = null): string
    {
        $lang = $lang ?? \App\Core\I18n::getCurrentLang();
        $shortDesc = $lang === 'zh' ? $this->short_desc_cn : $this->short_desc_en;

        // 如果指定语言的简介为空,降级到另一个语言
        if (empty($shortDesc)) {
            $shortDesc = $lang === 'zh' ? $this->short_desc_en : $this->short_desc_cn;
        }

        return $shortDesc ?? '';
    }

    /**
     * 获取缩略图URL
     * @param bool $withFallback 是否返回空字符串（无缩略图时）
     * @return string 缩略图URL
     */
    public function getThumbnailUrl(bool $withFallback = false): string
    {
        $url = $this->getFileUrl('thumbnail');

        if (!$url && !$withFallback) {
            return ''; // 返回空字符串，由前端控制显示
        }

        return $url ?? '';
    }

    /**
     * 获取状态标签
     */
    public function getStatusLabel(): string
    {
        if (isset($this->status_id)) {
            $status = ContentStatus::tryFrom($this->status_id);
            return $status ? $status->label() : '未知状态';
        }
        return '未设置';
    }

    /**
     * 获取内容类型标签
     */
    public function getContentTypeLabel(): string
    {
        if (isset($this->content_type_id)) {
            $type = ContentType::tryFrom($this->content_type_id);
            return $type ? $type->label() : '未知类型';
        }
        return '未设置';
    }

    /**
     * 检查是否已发布
     */
    public function isPublished(): bool
    {
        return $this->status_id === ContentStatus::PUBLISHED->value;
    }

    /**
     * 检查是否可见
     */
    public function isVisible(): bool
    {
        if (isset($this->status_id)) {
            $status = ContentStatus::tryFrom($this->status_id);
            return $status ? $status->isVisible() : false;
        }
        return false;
    }

    /**
     * 检查是否是视频类型
     */
    public function isVideoType(): bool
    {
        return $this->content_type_id === ContentType::VIDEO->value;
    }

    /**
     * 静态工厂方法 - 创建新Content实例
     */
    public static function make(array $data = []): self
    {
        $instance = new static();
        $instance->fill($data);
        return $instance;
    }

    /**
     * 静态方法 - 通过ID查找
     */
    public static function findOrFail(int $id): self
    {
        $instance = new static();
        $found = $instance->find($id);
        if (!$found) {
            throw new \Exception("Content with ID {$id} not found");
        }
        return $found;
    }

    /**
     * 获取统计信息
     */
    public function getStats(): array
    {
        $table = static::getTableName();
        $sql = "SELECT 
                    COUNT(*) as total_content,
                    SUM(CASE WHEN status_id = :published_status THEN 1 ELSE 0 END) as published_content,
                    SUM(CASE WHEN status_id = :draft_status THEN 1 ELSE 0 END) as draft_content,
                    SUM(view_cnt) as total_views,
                    AVG(view_cnt) as avg_views
                FROM {$table}";
        
        $result = $this->db->fetch($sql, [
            'published_status' => ContentStatus::PUBLISHED->value,
            'draft_status' => ContentStatus::DRAFT->value
        ]);
        
        return [
            'total_content' => (int)$result['total_content'],
            'published_content' => (int)$result['published_content'],
            'draft_content' => (int)$result['draft_content'],
            'total_views' => (int)$result['total_views'],
            'avg_views' => round((float)$result['avg_views'], 2)
        ];
    }

    /**
     * 获取关联标签
     */
    public function getRelatedTags(int $contentId): array
    {
        $sql = "SELECT t.id, t.name_cn, t.name_en, t.color_class, t.icon_class, t.status_id
                FROM tag t
                INNER JOIN content_tag ct ON t.id = ct.tag_id  
                WHERE ct.content_id = :content_id
                ORDER BY t.name_cn";
        
        return $this->db->fetchAll($sql, ['content_id' => $contentId]);
    }

    /**
     * 获取关联合集
     */
    public function getRelatedCollections(int $contentId): array
    {
        $sql = "SELECT c.id, c.name_cn, c.name_en, c.color_class, c.status_id
                FROM collection c
                INNER JOIN content_collection cc ON c.id = cc.collection_id  
                WHERE cc.content_id = :content_id
                ORDER BY c.name_cn";
        
        return $this->db->fetchAll($sql, ['content_id' => $contentId]);
    }

    /**
     * 同步标签关联
     */
    public function syncTagAssociations(int $contentId, array $tagIds): bool
    {
        $this->db->beginTransaction();
        
        try {
            // 一次性读取所有现有的关联记录（包含主键ID和tag_id）
            $existingAssociations = $this->db->fetchAll(
                "SELECT id, tag_id FROM content_tag WHERE content_id = :content_id",
                ['content_id' => $contentId]
            );
            
            $oldTagIds = array_column($existingAssociations, 'tag_id');
            
            // 筛选需要删除和添加的标签
            $tagsToRemove = array_diff($oldTagIds, $tagIds);  // 需要删除的tag_id
            $tagsToAdd = array_diff($tagIds, $oldTagIds);     // 需要添加的tag_id
            
            // 找到需要删除的记录的主键ID
            $recordsToDelete = [];
            foreach ($existingAssociations as $association) {
                if (in_array($association['tag_id'], $tagsToRemove)) {
                    $recordsToDelete[] = $association['id'];
                }
            }
            
            // 用主键ID删除记录
            foreach ($recordsToDelete as $recordId) {
                $this->db->query("DELETE FROM content_tag WHERE id = :id", ['id' => $recordId]);
            }
            
            // 添加新关联
            foreach ($tagsToAdd as $tagId) {
                $this->db->query(
                    "INSERT INTO content_tag (content_id, tag_id) VALUES (:content_id, :tag_id)",
                    ['content_id' => $contentId, 'tag_id' => $tagId]
                );
            }

            // 更新所有相关标签的内容计数（只更新有变化的标签）
            $affectedTagIds = array_unique(array_merge($tagsToRemove, $tagsToAdd));
            foreach ($affectedTagIds as $tagId) {
                $tmpTag = new Tag();
                $tmpTag->updateContentCount($tagId);
            }
            
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    /**
     * 同步合集关联
     */
    public function syncCollectionAssociations(int $contentId, array $collectionIds): bool
    {
        $this->db->beginTransaction();
        
        try {
            // 一次性读取所有现有的关联记录（包含主键ID和collection_id）
            $existingAssociations = $this->db->fetchAll(
                "SELECT id, collection_id FROM content_collection WHERE content_id = :content_id",
                ['content_id' => $contentId]
            );
            
            $oldCollectionIds = array_column($existingAssociations, 'collection_id');
            
            // 筛选需要删除和添加的合集
            $collectionsToRemove = array_diff($oldCollectionIds, $collectionIds);  // 需要删除的collection_id
            $collectionsToAdd = array_diff($collectionIds, $oldCollectionIds);     // 需要添加的collection_id
            
            // 找到需要删除的记录的主键ID
            $recordsToDelete = [];
            foreach ($existingAssociations as $association) {
                if (in_array($association['collection_id'], $collectionsToRemove)) {
                    $recordsToDelete[] = $association['id'];
                }
            }
            
            // 用主键ID删除记录
            foreach ($recordsToDelete as $recordId) {
                $this->db->query("DELETE FROM content_collection WHERE id = :id", ['id' => $recordId]);
            }

            // 添加新关联
            foreach ($collectionsToAdd as $collectionId) {
                $this->db->query(
                    "INSERT INTO content_collection (content_id, collection_id) VALUES (:content_id, :collection_id)",
                    ['content_id' => $contentId, 'collection_id' => $collectionId]
                );

                $tmpCollection = new Collection();
                $tmpCollection->updateContentCount($collectionId);
            }

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    /**
     * 记录 PV 访问日志
     * @param int $contentId 内容ID
     * @param int|null $userId 用户ID（可选）
     * @return bool
     */
    public function logPVAccess(int $contentId, ?int $userId = null): bool
    {
        $table = 'content_pv_log';

        // 自动获取客户端信息
        $ip =  RequestHelper::getClientIp();

        // 解析 User Agent
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $deviceInfo = RequestHelper::parseUserAgent($userAgent);

        // 准备插入数据
        $data = [
            'content_id' => $contentId,
            'user_id' => $userId,
            'ip' => "INET6_ATON('{$ip}')",
            'accessed_at' => date('Y-m-d H:i:s'),
            'device_type' => $deviceInfo['device_type'],
            'os_family' => $deviceInfo['os_family'],
            'browser_family' => $deviceInfo['browser_family'],
            'is_bot' => $deviceInfo['is_bot']
        ];

        // 构建 SQL
        $fields = [];
        $values = [];
        $params = [];

        foreach ($data as $key => $value) {
            if ($value !== null) {
                $fields[] = $key;
                if ($key === 'ip' && str_contains($value, 'INET6_ATON')) {
                    $values[] = $value; // 直接使用函数
                } else {
                    $values[] = ":{$key}";
                    $params[$key] = $value;
                }
            }
        }

        $sql = "INSERT INTO {$table} (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $values) . ")";

        $this->db->query($sql, $params);
        return true;
    }

    public function beforeSave(): bool
    {
        if ($this->status_id == ContentStatus::PUBLISHED->value) {
            if ($this->isNew || $this->original['status_id'] != $this->status_id){
                $this->attributes['pub_at'] = date('Y-m-d H:i:s');
            }

            return true;
        }

        return parent::beforeSave();
    }

    /**
     * 重写父类方法，为Content模型准备CSV导入数据
     *
     * @param array $csvRowData CSV行数据
     * @return array 处理后的数据
     */
    public function prepareBulkImportData(array $csvRowData): array
    {
        return [
            'content_type_id' => isset($csvRowData['content_type_id']) ? (int)$csvRowData['content_type_id'] : ContentType::VIDEO->value,
            'author' => $csvRowData['author'] ?? 'DP',
            'code' => $csvRowData['code'] ?? '',
            'title_en' => $csvRowData['title_en'] ?? '',
            'title_cn' => $csvRowData['title_cn'] ?? '',
            'desc_en' => $csvRowData['desc_en'] ?? '',
            'desc_cn' => $csvRowData['desc_cn'] ?? '',
            'sum_en' => $csvRowData['sum_en'] ?? '',
            'sum_cn' => $csvRowData['sum_cn'] ?? '',
            'short_desc_en' => $csvRowData['short_desc_en'] ?? '',
            'short_desc_cn' => $csvRowData['short_desc_cn'] ?? '',
            'thumbnail' => $csvRowData['thumbnail'] ?? '',
            'duration' => $csvRowData['duration'] ?? '',
            'status_id' => isset($csvRowData['status_id']) ? (int)$csvRowData['status_id'] : ContentStatus::DRAFT->value,
            'pv_cnt' => 0,
            'view_cnt' => 0
        ];
    }

    /**
     * 获取内容总数和本月新增统计
     *
     * @return array ['total' => int, 'monthly_new' => int, 'monthly_growth_rate' => float]
     */
    public static function getTotalAndMonthlyStats(): array
    {
        $db = \App\Core\Database::getInstance();

        // 总内容数
        $total = static::count();

        // 本月新增内容数
        $firstDayOfMonth = date('Y-m-01 00:00:00');
        $sql = "SELECT COUNT(*) as count FROM " . static::getTableName() . " WHERE created_at >= :first_day";
        $result = $db->fetch($sql, ['first_day' => $firstDayOfMonth]);
        $monthlyNew = (int)$result['count'];

        // 计算增长率
        $growthRate = $total > 0 ? round(($monthlyNew / $total) * 100, 1) : 0;

        return [
            'total' => $total,
            'monthly_new' => $monthlyNew,
            'monthly_growth_rate' => $growthRate
        ];
    }

    /**
     * 获取按状态统计的内容数量
     *
     * @return array ['published' => int, 'pending_publish' => int, 'shooting_done' => int, 'script_done' => int]
     */
    public static function getStatusStats(): array
    {
        return [
            'published' => static::count(['status_id' => ContentStatus::PUBLISHED->value]),
            'pending_publish' => static::count(['status_id' => ContentStatus::PENDING_PUBLISH->value]),
            'shooting_done' => static::count(['status_id' => ContentStatus::SHOOTING_DONE->value]),
            'script_done' => static::count(['status_id' => ContentStatus::SCRIPT_DONE->value])
        ];
    }

    /**
     * 获取指定日期范围内的每日统计数据
     *
     * @param string $startDate 开始日期 (Y-m-d)
     * @param string $endDate 结束日期 (Y-m-d)
     * @return array
     */
    public static function getDailyStats(string $startDate, string $endDate): array
    {
        $db = \App\Core\Database::getInstance();
        $data = [];
        $currentDate = new \DateTime($startDate);
        $endDateTime = new \DateTime($endDate);

        while ($currentDate <= $endDateTime) {
            $dateStr = $currentDate->format('Y-m-d');
            $nextDayStr = $currentDate->modify('+1 day')->format('Y-m-d');
            $currentDate->modify('-1 day'); // 恢复当前日期

            // 当日总视频数量
            $sql = "SELECT COUNT(*) as count FROM " . static::getTableName() . " WHERE created_at < :next_day";
            $result = $db->fetch($sql, ['next_day' => $nextDayStr]);
            $totalVideos = (int)$result['count'];

            // 当日新增视频数量
            $sql = "SELECT COUNT(*) as count FROM " . static::getTableName() .
                   " WHERE created_at >= :current_day AND created_at < :next_day";
            $result = $db->fetch($sql, [
                'current_day' => $dateStr . ' 00:00:00',
                'next_day' => $nextDayStr . ' 00:00:00'
            ]);
            $newVideos = (int)$result['count'];

            // 当日发布视频数量
            $sql = "SELECT COUNT(*) as count FROM " . static::getTableName() .
                   " WHERE status_id = :status_id AND pub_at >= :current_day AND pub_at < :next_day";
            $result = $db->fetch($sql, [
                'status_id' => ContentStatus::PUBLISHED->value,
                'current_day' => $dateStr . ' 00:00:00',
                'next_day' => $nextDayStr . ' 00:00:00'
            ]);
            $publishedVideos = (int)$result['count'];

            //当日站内pv, uv数
            $siteStatsDaily = new SiteStatsDaily();
            $dailySiteStats = $siteStatsDaily->getStatsByDate($dateStr);
            if (!$dailySiteStats) {
                $dailySiteStats = ['pv_count'=>0, 'uv_count'=>0];
            }


            $data[] = [
                'date' => $dateStr,
                'total_videos' => $totalVideos,
                'new_videos' => $newVideos,
                'published_videos' => $publishedVideos,
                'site_pv' => (int)$dailySiteStats['pv_count'],
                'site_uv' => (int)$dailySiteStats['uv_count']
            ];

            $currentDate->modify('+1 day');
        }

        return $data;
    }

    /**
     * 获取视频列表（带筛选、搜索、分页功能）
     * 返回Content对象数组
     *
     * @param array $filters 筛选条件
     * @param int $page 页码
     * @param int $perPage 每页数量
     * @return array ['items' => Content[], 'total' => int, 'totalPages' => int]
     */
    public static function getContentList(array $filters = [], int $page = 1, int $perPage = 12): array
    {
        $db = \App\Core\Database::getInstance();
        $offset = ($page - 1) * $perPage;

        // 构建SQL查询
        $sql = "SELECT c.* FROM content c WHERE 1=1";
        $params = [];
        $whereClauses = [];

        // 状态过滤 - 只显示已发布的内容
        $whereClauses[] = "c.status_id = :status_id";
        $params['status_id'] = ContentStatus::PUBLISHED->value;

        // 内容类型过滤
        if (!empty($filters['content_type_ids'])) {
            $placeholders = [];
            foreach ($filters['content_type_ids'] as $idx => $typeId) {
                $key = "content_type_id_{$idx}";
                $placeholders[] = ":{$key}";
                $params[$key] = (int)$typeId;
            }
            $whereClauses[] = "c.content_type_id IN (" . implode(',', $placeholders) . ")";
        }
        // 如果没有指定content_type_ids，则显示所有内容类型（不添加额外的WHERE条件）

        // 关键词搜索
        if (!empty($filters['search'])) {
            $whereClauses[] = "(c.title_cn LIKE :search_cn OR c.title_en LIKE :search_en OR c.short_desc_cn LIKE :search_desc_cn OR c.short_desc_en LIKE :search_desc_en)";
            $search = $filters['search'];
            $params['search_cn'] = "%{$search}%";
            $params['search_en'] = "%{$search}%";
            $params['search_desc_cn'] = "%{$search}%";
            $params['search_desc_en'] = "%{$search}%";
        }

        // 标签过滤
        if (!empty($filters['tag_ids'])) {
            $tagPlaceholders = [];
            foreach ($filters['tag_ids'] as $idx => $tagId) {
                $key = "tag_id_{$idx}";
                $tagPlaceholders[] = ":{$key}";
                $params[$key] = (int)$tagId;
            }
            $whereClauses[] = "c.id IN (SELECT content_id FROM content_tag WHERE tag_id IN (" . implode(',', $tagPlaceholders) . "))";
        }

        // 合集过滤
        if (!empty($filters['collection_ids'])) {
            $collectionPlaceholders = [];
            foreach ($filters['collection_ids'] as $idx => $collectionId) {
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
        $totalResult = $db->fetch($countSql, $params);
        $total = (int)$totalResult['total'];
        $totalPages = ceil($total / $perPage);

        // 添加排序和分页
        $sql .= " ORDER BY c.pub_at DESC LIMIT :limit OFFSET :offset";
        $params['limit'] = $perPage;
        $params['offset'] = $offset;

        // 执行查询
        $stmt = $db->query($sql, $params);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // 将数组转换为Content对象
        $items = [];
        foreach ($rows as $row) {
            $content = new static();
            $content->setOriginal($row);
            $content->setNew(false);
            $items[] = $content;
        }

        return [
            'items' => $items,
            'total' => $total,
            'totalPages' => $totalPages
        ];
    }

    /**
     * 为Content对象批量加载关联的标签和合集
     *
     * @param array $contents Content对象数组
     * @return void
     */
    public static function loadRelations(array $contents): void
    {
        if (empty($contents)) {
            return;
        }

        $db = \App\Core\Database::getInstance();
        $contentIds = array_map(fn($c) => $c->id, $contents);

        // 批量加载所有标签关联
        $tagPlaceholders = implode(',', array_fill(0, count($contentIds), '?'));
        $tagSql = "SELECT ct.content_id, t.id, t.name_cn, t.name_en, t.color_class, t.icon_class, t.status_id
                   FROM tag t
                   INNER JOIN content_tag ct ON t.id = ct.tag_id
                   WHERE ct.content_id IN ({$tagPlaceholders})
                   ORDER BY t.name_cn";
        $tagsResult = $db->fetchAll($tagSql, $contentIds);

        // 组织标签数据
        $tagsByContentId = [];
        foreach ($tagsResult as $tag) {
            $contentId = $tag['content_id'];
            unset($tag['content_id']);
            $tagsByContentId[$contentId][] = $tag;
        }

        // 批量加载所有合集关联
        $collectionPlaceholders = implode(',', array_fill(0, count($contentIds), '?'));
        $collectionSql = "SELECT cc.content_id, c.id, c.name_cn, c.name_en, c.color_class, c.status_id
                          FROM collection c
                          INNER JOIN content_collection cc ON c.id = cc.collection_id
                          WHERE cc.content_id IN ({$collectionPlaceholders})
                          ORDER BY c.name_cn";
        $collectionsResult = $db->fetchAll($collectionSql, $contentIds);

        // 组织合集数据
        $collectionsByContentId = [];
        foreach ($collectionsResult as $collection) {
            $contentId = $collection['content_id'];
            unset($collection['content_id']);
            $collectionsByContentId[$contentId][] = $collection;
        }

        // 将关联数据附加到Content对象
        foreach ($contents as $content) {
            $content->tags = $tagsByContentId[$content->id] ?? [];
            $content->collections = $collectionsByContentId[$content->id] ?? [];
        }
    }

    /**
     * 重写父类方法，添加 tag_id 和 collection_id 的字段搜索策略
     *
     * @return array 字段搜索策略配置
     */
    protected static function getFieldSearchStrategies(): array
    {
        return array_merge(parent::getFieldSearchStrategies(), [
            'tag_id' => 'custom',
            'collection_id' => 'custom',
            'content_type_id' => 'exact'
        ]);
    }

    /**
     * 重写父类方法，处理 tag_id 和 collection_id 的自定义过滤逻辑
     *
     * @param string $field 字段名
     * @param mixed $value 搜索值
     * @param array &$whereConditions WHERE条件数组
     * @param array &$params 参数数组
     */
    protected static function handleCustomFieldFilter(string $field, $value, array &$whereConditions, array &$params): void
    {
        $table = static::getTableName();

        switch ($field) {
            case 'tag_id':
                // 通过 content_tag 关联表筛选
                $whereConditions[] = "{$table}.id IN (SELECT content_id FROM content_tag WHERE tag_id = :filter_tag_id)";
                $params['filter_tag_id'] = (int)$value;
                break;

            case 'collection_id':
                // 通过 content_collection 关联表筛选
                $whereConditions[] = "{$table}.id IN (SELECT content_id FROM content_collection WHERE collection_id = :filter_collection_id)";
                $params['filter_collection_id'] = (int)$value;
                break;

            default:
                // 调用父类方法处理其他自定义字段
                parent::handleCustomFieldFilter($field, $value, $whereConditions, $params);
                break;
        }
    }


    public static function countByContentTypeId($contentTypeId): int
    {
        // 真实SQL: SELECT COUNT(id) FROM videos WHERE content_type_id = ?
        return 25;
    }

}