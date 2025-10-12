<?php
/**
 * 前端视频列表视图
 *
 * 可用变量:
 * - $videos: 视频数组
 * - $currentPage: 当前页码
 * - $totalPages: 总页数
 * - $totalVideos: 视频总数
 * - $perPage: 每页数量
 * - $search: 搜索关键词
 * - $selectedTagIds: 选中的标签ID数组
 * - $selectedCollectionIds: 选中的合集ID数组
 * - $selectedContentTypeIds: 选中的内容类型ID数组
 * - $allTags: 所有可用标签
 * - $allCollections: 所有可用合集
 * - $resourceUrl: 资源URL前缀
 */

// 构建查询字符串辅助函数
function buildQueryParams(array $params): string {
    $filteredParams = array_filter($params, function($value) {
        return !empty($value) || $value === '0' || $value === 0;
    });
    return !empty($filteredParams) ? '?' . http_build_query($filteredParams) : '';
}

// 构建分页链接
function buildPaginationUrl(int $page, array $currentParams): string {
    $params = $currentParams;
    $params['page'] = $page;
    return '/videos' . buildQueryParams($params);
}

// 当前查询参数
$currentParams = [];
if (!empty($search)) $currentParams['search'] = $search;
if (!empty($selectedTagIds)) $currentParams['tag_id'] = implode(',', $selectedTagIds);
if (!empty($selectedCollectionIds)) $currentParams['collection_id'] = implode(',', $selectedCollectionIds);
if (!empty($selectedContentTypeIds)) $currentParams['content_type_id'] = implode(',', $selectedContentTypeIds);
?>

<!-- 视频筛选区域 -->
<form class="card mb-4" method="GET" action="/videos">
    <!-- 保持语言参数 -->
    <?php if (!empty($currentLang)): ?>
        <input type="hidden" name="lang" value="<?= $currentLang ?>">
    <?php endif; ?>

    <div class="card-body">
        <div class="row g-3 mb-3">
            <!-- 标签筛选 -->
            <div class="col-md-6">
                <div class="custom-multiselect" data-name="tag_id">
                    <div class="multiselect-display" data-placeholder="<?= $currentLang === 'zh' ? '请选择标签' : 'Select Tags' ?>">
                        <?php if (!empty($selectedTagIds)): ?>
                            <div class="selected-items">
                                <?php
                                $displayCount = min(count($selectedTagIds), 5);
                                foreach (array_slice($selectedTagIds, 0, $displayCount) as $tagId):
                                    $tag = array_filter($allTags, fn($t) => $t['id'] == $tagId);
                                    $tag = reset($tag);
                                    if ($tag):
                                        $tagName = $currentLang === 'zh' ? $tag['name_cn'] : $tag['name_en'];
                                ?>
                                    <span class="selected-item">
                                        <?= htmlspecialchars($tagName) ?>
                                        <button type="button" class="remove-btn" data-value="<?= $tagId ?>">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    </span>
                                <?php
                                    endif;
                                endforeach;
                                ?>
                                <span class="selected-count" data-i18n="filter.selected_count" data-i18n-vars='{"count":<?= count($selectedTagIds) ?>}'>共<?= count($selectedTagIds) ?>个</span>
                            </div>
                        <?php else: ?>
                            <span class="placeholder-text" data-i18n="filter.tag_placeholder"><?= $currentLang === 'zh' ? '请选择标签' : 'Select Tags' ?></span>
                        <?php endif; ?>
                        <i class="bi bi-chevron-down arrow-icon"></i>
                    </div>
                    <div class="multiselect-dropdown">
                        <?php foreach ($allTags as $tag):
                            $tagName = $currentLang === 'zh' ? $tag['name_cn'] : $tag['name_en'];
                        ?>
                            <div class="dropdown-option" data-value="<?= $tag['id'] ?>">
                                <input type="checkbox"
                                       id="tag-<?= $tag['id'] ?>"
                                       name="tag_id[]"
                                       value="<?= $tag['id'] ?>"
                                       <?= in_array($tag['id'], $selectedTagIds) ? 'checked' : '' ?>>
                                <label for="tag-<?= $tag['id'] ?>"><?= htmlspecialchars($tagName) ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- 合集筛选 -->
            <div class="col-md-6">
                <div class="custom-multiselect" data-name="collection_id">
                    <div class="multiselect-display" data-placeholder="<?= $currentLang === 'zh' ? '请选择合集' : 'Select Collections' ?>">
                        <?php if (!empty($selectedCollectionIds)): ?>
                            <div class="selected-items">
                                <?php
                                $displayCount = min(count($selectedCollectionIds), 5);
                                foreach (array_slice($selectedCollectionIds, 0, $displayCount) as $collectionId):
                                    $collection = array_filter($allCollections, fn($c) => $c['id'] == $collectionId);
                                    $collection = reset($collection);
                                    if ($collection):
                                        $collectionName = $currentLang === 'zh' ? $collection['name_cn'] : $collection['name_en'];
                                ?>
                                    <span class="selected-item">
                                        <?= htmlspecialchars($collectionName) ?>
                                        <button type="button" class="remove-btn" data-value="<?= $collectionId ?>">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    </span>
                                <?php
                                    endif;
                                endforeach;
                                ?>
                                <span class="selected-count" data-i18n="filter.selected_count" data-i18n-vars='{"count":<?= count($selectedCollectionIds) ?>}'>共<?= count($selectedCollectionIds) ?>个</span>
                            </div>
                        <?php else: ?>
                            <span class="placeholder-text" data-i18n="filter.collection_placeholder"><?= $currentLang === 'zh' ? '请选择合集' : 'Select Collections' ?></span>
                        <?php endif; ?>
                        <i class="bi bi-chevron-down arrow-icon"></i>
                    </div>
                    <div class="multiselect-dropdown">
                        <?php foreach ($allCollections as $collection):
                            $collectionName = $currentLang === 'zh' ? $collection['name_cn'] : $collection['name_en'];
                        ?>
                            <div class="dropdown-option" data-value="<?= $collection['id'] ?>">
                                <input type="checkbox"
                                       id="collection-<?= $collection['id'] ?>"
                                       name="collection_id[]"
                                       value="<?= $collection['id'] ?>"
                                       <?= in_array($collection['id'], $selectedCollectionIds) ? 'checked' : '' ?>>
                                <label for="collection-<?= $collection['id'] ?>"><?= htmlspecialchars($collectionName) ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <!-- 关键词搜索 -->
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text"
                           class="form-control"
                           placeholder="<?= $currentLang === 'zh' ? '输入关键词搜索...' : 'Search keywords...' ?>"
                           name="search"
                           id="searchInput"
                           value="<?= htmlspecialchars($search) ?>"
                           data-i18n-placeholder="filter.search_placeholder">
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>

            <!-- 搜索结果显示 -->
            <div class="col-md-6 d-flex align-items-end">
                <div class="w-100">
                    <span class="text-muted">
                        <?php if ($currentLang === 'zh'): ?>
                            搜索结果: 共找到 <strong><?= $totalVideos ?></strong> 个视频
                        <?php else: ?>
                            Search Results: Found <strong><?= $totalVideos ?></strong> videos
                        <?php endif; ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- 视频展示列表 -->
<?php if (!empty($videos)): ?>
    <div class="video-grid">
        <div class="row g-4">
            <?php foreach ($videos as $video): ?>
                <div class="col-lg-3 col-md-4 col-sm-6 video-card-container">
                    <div class="card h-100 video-card">
                        <div class="position-relative">
                            <a class="video-thumbnail" href="/videos/<?= $video->id ?>">
                                <?php if (!empty($video->thumbnail)): ?>
                                    <img src="<?= htmlspecialchars($video->getThumbnailUrl()) ?>"
                                         alt="<?= htmlspecialchars($video->getDisplayTitle()) ?>"
                                         class="card-img-top">
                                <?php endif; ?>
                            </a>
                        </div>
                        <div class="card-body p-3">
                            <h5 class="card-title video-title">
                                <a href="/videos/<?= $video->id ?>?lang=<?= $currentLang ?>" class="text-decoration-none">
                                    <?= htmlspecialchars($video->getTitle($currentLang)) ?>
                                </a>
                            </h5>
                            <div class="video-meta mb-2">
                                <small class="text-muted">
                                    <i class="bi bi-calendar3 me-1"></i><?= date('Y-m-d', strtotime($video->created_at)) ?>
                                    <?php if (!empty($video->author)): ?>
                                        <i class="bi bi-person ms-2 me-1"></i><?= htmlspecialchars($video->author) ?>
                                    <?php endif; ?>
                                </small>
                            </div>
                            <p class="card-text video-description text-muted small">
                                <?= htmlspecialchars($video->getShortDescription($currentLang)) ?>
                            </p>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="video-tags">
                                    <?php
                                    $displayTags = array_slice($video->tags, 0, 2);
                                    foreach ($displayTags as $tag):
                                        $tagName = $currentLang === 'zh' ? $tag['name_cn'] : $tag['name_en'];
                                    ?>
                                        <a href="/videos?tag_id=<?= $tag['id'] ?>&lang=<?= $currentLang ?>"
                                           class="btn btn-outline-primary btn-sm me-1">
                                            <?= htmlspecialchars($tagName) ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                                <div class="video-collection">
                                    <?php
                                    if (!empty($video->collections)):
                                        $collection = $video->collections[0];
                                        $collectionName = $currentLang === 'zh' ? $collection['name_cn'] : $collection['name_en'];
                                    ?>
                                        <a href="/videos?collection_id=<?= $collection['id'] ?>&lang=<?= $currentLang ?>"
                                           class="btn btn-outline-success btn-sm">
                                            <?= htmlspecialchars($collectionName) ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- 分页导航 -->
    <?php if ($totalPages > 1):
        // 添加语言参数到分页链接
        if (!empty($currentLang)) {
            $currentParams['lang'] = $currentLang;
        }
    ?>
        <nav aria-label="<?= $currentLang === 'zh' ? '视频列表分页' : 'Video List Pagination' ?>" class="mt-5">
            <div class="pagination-wrapper d-flex justify-content-center align-items-center">
                <!-- 上一页 -->
                <?php if ($currentPage > 1): ?>
                    <a href="<?= buildPaginationUrl($currentPage - 1, $currentParams) ?>"
                       class="pagination-btn pagination-btn-prev"
                       title="<?= $currentLang === 'zh' ? '上一页' : 'Previous' ?>"
                       data-i18n-title="pagination.prev">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                <?php else: ?>
                    <span class="pagination-btn pagination-btn-prev" style="opacity: 0.5; cursor: not-allowed;">
                        <i class="bi bi-chevron-left"></i>
                    </span>
                <?php endif; ?>

                <!-- 页码 -->
                <div class="pagination-numbers">
                    <?php
                    $startPage = max(1, $currentPage - 2);
                    $endPage = min($totalPages, $currentPage + 2);

                    for ($i = $startPage; $i <= $endPage; $i++):
                    ?>
                        <?php if ($i === $currentPage): ?>
                            <span class="pagination-btn active"><?= $i ?></span>
                        <?php else: ?>
                            <a href="<?= buildPaginationUrl($i, $currentParams) ?>"
                               class="pagination-btn">
                                <?= $i ?>
                            </a>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>

                <!-- 下一页 -->
                <?php if ($currentPage < $totalPages): ?>
                    <a href="<?= buildPaginationUrl($currentPage + 1, $currentParams) ?>"
                       class="pagination-btn pagination-btn-next"
                       title="<?= $currentLang === 'zh' ? '下一页' : 'Next' ?>"
                       data-i18n-title="pagination.next">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                <?php else: ?>
                    <span class="pagination-btn pagination-btn-next" style="opacity: 0.5; cursor: not-allowed;">
                        <i class="bi bi-chevron-right"></i>
                    </span>
                <?php endif; ?>
            </div>
            <div class="text-center mt-3">
                <small class="text-muted">
                    <?php if ($currentLang === 'zh'): ?>
                        共 <?= $totalVideos ?> 个视频，当前第 <?= $currentPage ?> 页，共 <?= $totalPages ?> 页
                    <?php else: ?>
                        Total <?= $totalVideos ?> videos, Page <?= $currentPage ?> of <?= $totalPages ?>
                    <?php endif; ?>
                </small>
            </div>
        </nav>
    <?php endif; ?>

<?php else: ?>
    <!-- 空状态 -->
    <div class="empty-state">
        <i class="bi bi-film"></i>
        <h3 data-i18n="empty.title"><?= $currentLang === 'zh' ? '暂无视频' : 'No Videos' ?></h3>
        <p data-i18n="empty.desc"><?= $currentLang === 'zh' ? '没有找到符合条件的视频，请尝试调整筛选条件' : 'No videos found matching your criteria. Try adjusting your filters.' ?></p>
    </div>
<?php endif; ?>
