<?php

/**
 * @var $this \App\Controllers\Frontend\ContentController //$this->funcName() will auto work in IDE
 * @var $video \App\Models\Content //$content->id will auto work in IDE
 */

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
 * - $currentParams: 当前查询参数数组
 * - $videoListJsData: JavaScript数据
 * - $resourceUrl: 资源URL前缀
 */
?>

<!-- 视频筛选区域 -->
<form class="card mb-4" method="GET" action="/content">
    <!-- 保持语言参数 -->
    <?php if (!empty($currentLang)): ?>
        <input type="hidden" name="lang" value="<?= $currentLang ?>">
    <?php endif; ?>

    <div class="card-body">
        <div class="row g-3 mb-3">

            <!-- 标签筛选 -->
            <div class="col-md-6">
                <div id="tagMultiSelect" class="multi-select-container"></div>
            </div>

            <!-- 合集筛选 -->
            <div class="col-md-6">
                <div id="collectionMultiSelect" class="multi-select-container"></div>
            </div>
        </div>

        <div class="row g-3">
            <!-- 内容类型筛选 -->
            <div class="col-md-6">
                <div id="contentTypeMultiSelect" class="multi-select-container"></div>
            </div>
            <!-- 关键词搜索 -->
            <div class="col-md-4">
                <div class="input-group  mt-1">
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
            <div class="col-md-2">
                <div class="search-result-container">
                    <span class="text-muted">
                        <?php if ($currentLang === 'zh'): ?>
                            搜索结果: 共找到 <strong><?= $totalVideos ?></strong> 个
                        <?php else: ?>
                            Search Results: Found <strong><?= $totalVideos ?></strong>
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
                            <a class="video-thumbnail" href="<?= $this->buildVideoDetailUrl($video->id, $video->getTitle('en'), ['lang' => $currentLang]) ?>">
                                <?php if (!empty($video->thumbnail)): ?>
                                    <img src="<?= htmlspecialchars($video->getThumbnailUrl()) ?>"
                                         alt="<?= htmlspecialchars($video->getDisplayTitle()) ?>"
                                         class="card-img-top">
                                <?php endif; ?>
                            </a>
                        </div>
                        <div class="card-body p-3">
                            <h5 class="card-title video-title">
                                <a href="<?= $this->buildVideoDetailUrl($video->id, $video->getTitle('en'), ['lang' => $currentLang]) ?>" class="text-decoration-none">
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
                                        <a href="/content?tag_id=<?= $tag['id'] ?>&lang=<?= $currentLang ?>"
                                           class="btn btn-outline-primary btn-xs me-1">
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
                                        <a href="/content?collection_id=<?= $collection['id'] ?>&lang=<?= $currentLang ?>"
                                           class="btn btn-outline-success btn-xs">
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
        <nav aria-label="<?= $currentLang === 'zh' ? '列表分页' : 'Video List Pagination' ?>" class="mt-5">
            <div class="pagination-wrapper d-flex justify-content-center align-items-center">
                <!-- 上一页 -->
                <?php if ($currentPage > 1): ?>
                    <a href="<?= $this->buildPaginationUrl($currentPage - 1, $currentParams) ?>"
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
                            <a href="<?= $this->buildPaginationUrl($i, $currentParams) ?>"
                               class="pagination-btn">
                                <?= $i ?>
                            </a>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>

                <!-- 下一页 -->
                <?php if ($currentPage < $totalPages): ?>
                    <a href="<?= $this->buildPaginationUrl($currentPage + 1, $currentParams) ?>"
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
                        共 <?= $totalVideos ?> 个，当前第 <?= $currentPage ?> 页，共 <?= $totalPages ?> 页
                    <?php else: ?>
                        Total <?= $totalVideos ?> , Page <?= $currentPage ?> of <?= $totalPages ?>
                    <?php endif; ?>
                </small>
            </div>
        </nav>
    <?php endif; ?>

<?php else: ?>
    <!-- 空状态 -->
    <div class="empty-state">
        <i class="bi bi-film"></i>
        <h3 data-i18n="empty.title"><?= $currentLang === 'zh' ? '暂无内容' : 'No Contents' ?></h3>
        <p data-i18n="empty.desc"><?= $currentLang === 'zh' ? '没有找到符合条件的内容，请尝试调整筛选条件' : 'No Contents found matching your criteria. Try adjusting your filters.' ?></p>
    </div>
<?php endif; ?>

<script>
// 将PHP数据传递给JavaScript (由Controller准备)
window.videoListData = <?= json_encode($videoListJsData) ?>;
</script>
