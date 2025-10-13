<?php
/**
 * 前端视频详情视图
 *
 * @var $this \App\Controllers\Frontend\VideoController
 * @var $video \App\Models\Content
 * @var $videoTags array
 * @var $videoCollections array
 * @var $videoLinks array
 * @var $videoLinkStats array
 * @var $comments array
 * @var $commentPage int
 * @var $commentsTotalPages int
 * @var $commentsTotalCount int
 * @var $announcements array
 * @var $relatedVideos array
 * @var $recommendedVideos array
 * @var $resourceUrl string
 * @var $currentLang string
 * @var $supportedLangs array
 */
?>

<!-- 主要内容 -->
<div class="row g-4">
    <!-- 主内容区域 -->
    <div class="col-lg-8">
        <!-- 视频封面和信息区域 -->
        <div class="card mb-4">
            <div class="video-cover-container card-img-top">
                <?php if (!empty($video->thumbnail)): ?>
                    <img src="<?= htmlspecialchars($video->getThumbnailUrl()) ?>"
                         alt="<?= htmlspecialchars($video->getTitle($currentLang)) ?>"
                         class="video-cover">
                <?php else: ?>
                    <img src="https://via.placeholder.com/856x481?text=No+Image"
                         alt="<?= htmlspecialchars($video->getTitle($currentLang)) ?>"
                         class="video-cover">
                <?php endif; ?>
            </div>
            <div class="card-body">
                <!-- 视频标题行 -->
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h1 class="video-title mb-0 flex-grow-1 me-3">
                        <i class="bi bi-camera-video text-primary me-2"></i>
                        <?= htmlspecialchars($video->getTitle($currentLang)) ?>
                    </h1>
                    <!-- 交互按钮移到标题行右侧 -->
                    <div class="interaction-buttons-inline">
                        <button class="btn btn-outline-primary btn-sm me-1" title="<?= $currentLang === 'zh' ? '点赞' : 'Like' ?>">
                            <i class="bi bi-hand-thumbs-up me-1"></i>
                            <span class="badge bg-primary">0</span>
                        </button>
                        <button class="btn btn-outline-warning btn-sm me-1" title="<?= $currentLang === 'zh' ? '收藏' : 'Favorite' ?>">
                            <i class="bi bi-star me-1"></i>
                            <span class="badge bg-warning">0</span>
                        </button>
                        <button class="btn btn-outline-info btn-sm" title="<?= $currentLang === 'zh' ? '评论' : 'Comment' ?>">
                            <i class="bi bi-chat-dots me-1"></i>
                            <span class="badge bg-info"><?= $commentsTotalCount ?></span>
                        </button>
                    </div>
                </div>

                <!-- 多平台播放按钮 -->
                <?php if (!empty($videoLinks)): ?>
                    <div class="platform-buttons mb-3">
                        <div class="row g-2">
                            <?php foreach ($videoLinks as $link): ?>
                                <?php if ($link['status_id'] == 1): // 只显示有效链接 ?>
                                    <div class="col-4">
                                        <a href="<?= htmlspecialchars($link['external_url']) ?>"
                                           target="_blank"
                                           class="btn btn-outline-<?= strtolower($link['platform_code']) === 'ytb' ? 'danger' : (strtolower($link['platform_code']) === 'bi' ? 'info' : 'dark') ?> w-100">
                                            <i class="bi bi-<?= strtolower($link['platform_code']) === 'ytb' ? 'youtube' : (strtolower($link['platform_code']) === 'bi' ? 'tv' : 'play-circle') ?>"></i>
                                            <?= htmlspecialchars($link['platform_name']) ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="video-meta">
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="meta-item">
                                <i class="bi bi-calendar3 me-2"></i>
                                <?= $currentLang === 'zh' ? '发布时间' : 'Published' ?>: <?= date('Y-m-d', strtotime($video->created_at)) ?>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="meta-item">
                                <i class="bi bi-person me-2"></i>
                                <?= $currentLang === 'zh' ? '作者' : 'Author' ?>: <?= htmlspecialchars($video->author) ?>
                            </div>
                        </div>
                        <?php if (!empty($video->duration)): ?>
                        <div class="col-6">
                            <div class="meta-item">
                                <i class="bi bi-clock me-2"></i>
                                <?= $currentLang === 'zh' ? '时长' : 'Duration' ?>: <?= htmlspecialchars($video->duration) ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="col-6">
                            <div class="meta-item">
                                <i class="bi bi-eye me-2"></i>
                                <?= $currentLang === 'zh' ? '浏览数' : 'Views' ?>: <?= number_format($video->view_cnt) ?> <?= $currentLang === 'zh' ? '次' : '' ?>
                            </div>
                        </div>
                        <?php if (!empty($videoLinks)): ?>
                            <?php if ($videoLinkStats['totalDownloads'] > 0): ?>
                            <div class="col-6">
                                <div class="meta-item">
                                    <i class="bi bi-download me-2"></i>
                                    <?= $currentLang === 'zh' ? '下载次数' : 'Downloads' ?>: <?= number_format($videoLinkStats['totalDownloads']) ?> <?= $currentLang === 'zh' ? '次' : '' ?>
                                </div>
                            </div>
                            <?php endif; ?>
                            <?php if ($videoLinkStats['totalShares'] > 0): ?>
                            <div class="col-6">
                                <div class="meta-item">
                                    <i class="bi bi-share me-2"></i>
                                    <?= $currentLang === 'zh' ? '分享次数' : 'Shares' ?>: <?= number_format($videoLinkStats['totalShares']) ?> <?= $currentLang === 'zh' ? '次' : '' ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <!-- 标签和合集移到card footer -->
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="tags-section">
                        <?php foreach ($videoTags as $tag):
                            $tagName = $currentLang === 'zh' ? $tag['name_cn'] : $tag['name_en'];
                        ?>
                            <a href="/videos?tag_id=<?= $tag['id'] ?>&lang=<?= $currentLang ?>"
                               class="btn btn-outline-success btn-sm me-1">
                                <?= htmlspecialchars($tagName) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <div class="collection-section">
                        <?php foreach ($videoCollections as $collection):
                            $collectionName = $currentLang === 'zh' ? $collection['name_cn'] : $collection['name_en'];
                        ?>
                            <a href="/videos?collection_id=<?= $collection['id'] ?>&lang=<?= $currentLang ?>"
                               class="btn btn-outline-success btn-sm">
                                <?= htmlspecialchars($collectionName) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- 视频正文内容 -->
        <div class="card mb-4">
            <div class="card-header text-center">
                <h5 class="mb-0">
                    <i class="bi bi-file-text me-2"></i>
                    <?= $currentLang === 'zh' ? '视频详情' : 'Video Details' ?>
                </h5>
            </div>
            <div class="card-body">
                <div class="video-content" id="markdown-content">
                    <?= nl2br(htmlspecialchars($video->getDescription($currentLang))) ?>
                </div>
            </div>
        </div>

        <!-- 评论区域 -->
        <div class="card">
            <div class="card-header text-center">
                <h5 class="mb-0">
                    <i class="bi bi-chat-dots me-2"></i>
                    <?= $currentLang === 'zh' ? '评论' : 'Comments' ?>
                    <span class="badge bg-secondary"><?= $commentsTotalCount ?></span>
                </h5>
            </div>
            <div class="card-body">
                <!-- 发表评论 -->
                <div class="comment-form mb-4">
                    <div class="mb-2">
                        <textarea class="form-control"
                                  rows="3"
                                  placeholder="<?= $currentLang === 'zh' ? '写下你的评论...' : 'Write your comment...' ?>"></textarea>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button class="btn btn-primary">
                            <i class="bi bi-send"></i>
                            <span class="d-none d-sm-inline ms-1">
                                <?= $currentLang === 'zh' ? '发表评论' : 'Post Comment' ?>
                            </span>
                        </button>
                    </div>
                </div>

                <!-- 评论列表 -->
                <?php if (!empty($comments)): ?>
                    <div class="comments-list">
                        <?php foreach ($comments as $comment): ?>
                            <?php echo $this->view('videos._comment_item', [
                                'comment' => $comment,
                                'currentLang' => $currentLang,
                                'level' => 0
                            ]); ?>
                        <?php endforeach; ?>
                    </div>

                    <!-- 评论分页 -->
                    <?php if ($commentsTotalPages > 1): ?>
                        <nav class="mt-4">
                            <div class="custom-pagination d-flex justify-content-center align-items-center">
                                <a href="<?= $commentPage > 1 ? $this->buildCommentPaginationUrl($commentPage - 1, $video->id, $currentLang) : '#' ?>"
                                   class="btn btn-outline-secondary btn-sm pagination-btn"
                                   <?= $commentPage <= 1 ? 'disabled' : '' ?>>
                                    <i class="bi bi-chevron-left"></i>
                                </a>
                                <div class="pagination-pages mx-3">
                                    <?php
                                    $paginationRange = $this->calculateCommentPaginationRange($commentPage, $commentsTotalPages);
                                    for ($i = $paginationRange['start']; $i <= $paginationRange['end']; $i++):
                                    ?>
                                        <a href="<?= $this->buildCommentPaginationUrl($i, $video->id, $currentLang) ?>"
                                           class="btn <?= $i === $commentPage ? 'btn-primary' : 'btn-outline-secondary' ?> btn-sm pagination-page">
                                            <?= $i ?>
                                        </a>
                                    <?php endfor; ?>
                                </div>
                                <a href="<?= $commentPage < $commentsTotalPages ? $this->buildCommentPaginationUrl($commentPage + 1, $video->id, $currentLang) : '#' ?>"
                                   class="btn btn-outline-secondary btn-sm pagination-btn"
                                   <?= $commentPage >= $commentsTotalPages ? 'disabled' : '' ?>>
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            </div>
                        </nav>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-chat-dots fs-1"></i>
                        <p class="mt-2"><?= $currentLang === 'zh' ? '暂无评论，快来抢沙发吧！' : 'No comments yet. Be the first to comment!' ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- 侧边栏 -->
    <div class="col-lg-4">
        <!-- 公告模块 -->
        <?php if (!empty($announcements)): ?>
            <div class="card mb-4">
                <div class="card-header text-center">
                    <h6 class="mb-0">
                        <i class="bi bi-megaphone text-warning me-2"></i>
                        <?= $currentLang === 'zh' ? '最新公告' : 'Latest Announcements' ?>
                    </h6>
                </div>
                <div class="list-group list-group-flush">
                    <?php foreach ($announcements as $announcement):
                        $announcementTitle = $announcement->getTitle($currentLang);
                        $announcementDesc = $announcement->getShortDescription($currentLang);
                    ?>
                        <div class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-semibold">
                                        <i class="bi bi-exclamation-circle text-warning me-2"></i>
                                        <a href="/videos?content_type_id=1&lang=<?= $currentLang ?>" class="text-decoration-none">
                                            <?= htmlspecialchars($announcementTitle) ?>
                                        </a>
                                    </h6>
                                    <?php if (!empty($announcementDesc)): ?>
                                        <p class="mb-1 text-muted small">
                                            <?= htmlspecialchars(mb_substr($announcementDesc, 0, 50)) ?>...
                                        </p>
                                    <?php endif; ?>
                                    <small class="text-success">
                                        <i class="bi bi-clock me-1"></i><?= date('Y-m-d', strtotime($announcement->created_at)) ?>
                                    </small>
                                </div>
                                <span class="badge bg-warning text-dark"><?= $currentLang === 'zh' ? '重要' : 'Important' ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="card-footer text-center">
                    <a href="/videos?content_type_id=1&lang=<?= $currentLang ?>" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-arrow-right me-1"></i><?= $currentLang === 'zh' ? '查看更多公告' : 'View More' ?>
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <!-- 关联视频模块 -->
        <?php if (!empty($relatedVideos)): ?>
            <div class="card">
                <div class="card-header text-center">
                    <h6 class="mb-0">
                        <i class="bi bi-collection-play text-info me-2"></i>
                        <?= $currentLang === 'zh' ? '关联视频' : 'Related Videos' ?>
                    </h6>
                </div>
                <div class="card-body p-0">
                    <?php foreach ($relatedVideos as $relatedVideo):
                        $relatedTitle = $relatedVideo->getTitle($currentLang);
                    ?>
                        <div class="card m-2 border-0 shadow-sm">
                            <div class="row g-0">
                                <div class="col-5 d-flex align-items-center">
                                    <a href="/videos/<?= $relatedVideo->id ?>?lang=<?= $currentLang ?>" class="d-block w-100">
                                        <div class="video-thumbnail-container">
                                            <?php if (!empty($relatedVideo->thumbnail)): ?>
                                                <img src="<?= htmlspecialchars($relatedVideo->getThumbnailUrl()) ?>"
                                                     alt="<?= htmlspecialchars($relatedTitle) ?>"
                                                     class="img-fluid video-thumbnail">
                                            <?php else: ?>
                                                <img src="https://via.placeholder.com/320x180?text=No+Image"
                                                     alt="<?= htmlspecialchars($relatedTitle) ?>"
                                                     class="img-fluid video-thumbnail">
                                            <?php endif; ?>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-7">
                                    <div class="card-body p-2">
                                        <h6 class="card-title mb-1 small">
                                            <a href="/videos/<?= $relatedVideo->id ?>?lang=<?= $currentLang ?>" class="text-decoration-none">
                                                <?= htmlspecialchars(mb_substr($relatedTitle, 0, 30)) ?><?= mb_strlen($relatedTitle) > 30 ? '...' : '' ?>
                                            </a>
                                        </h6>
                                        <small class="text-muted d-block">
                                            <?= $currentLang === 'zh' ? '时长' : 'Duration' ?>: <?= htmlspecialchars($relatedVideo->duration ?: 'N/A') ?>
                                            | <i class="bi bi-person me-1"></i><?= htmlspecialchars($relatedVideo->author) ?>
                                        </small>
                                        <div class="mt-2">
                                            <?php
                                            $displayTags = array_slice($relatedVideo->tags, 0, 3);
                                            foreach ($displayTags as $tag):
                                                $tagName = $currentLang === 'zh' ? $tag['name_cn'] : $tag['name_en'];
                                            ?>
                                                <a href="/videos?tag_id=<?= $tag['id'] ?>&lang=<?= $currentLang ?>"
                                                   class="btn btn-outline-primary btn-sm me-1">
                                                    <?= htmlspecialchars($tagName) ?>
                                                </a>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- 推荐视频区域 -->
<?php if (!empty($recommendedVideos)): ?>
    <div class="row mt-5">
        <div class="col-12">
            <div class="card">
                <div class="card-header text-center">
                    <h5 class="mb-0">
                        <i class="bi bi-megaphone text-success me-2"></i>
                        <?= $currentLang === 'zh' ? '相关推荐' : 'Recommended' ?>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <?php foreach ($recommendedVideos as $recommendedVideo):
                            $recommendedTitle = $recommendedVideo->getTitle($currentLang);
                            $recommendedDesc = $recommendedVideo->getShortDescription($currentLang);
                        ?>
                            <div class="col-lg-3 col-md-3 col-sm-4 col-6">
                                <div class="card h-100 shadow-sm border-1">
                                    <a href="/videos/<?= $recommendedVideo->id ?>?lang=<?= $currentLang ?>">
                                        <?php if (!empty($recommendedVideo->thumbnail)): ?>
                                            <img src="<?= htmlspecialchars($recommendedVideo->getThumbnailUrl()) ?>"
                                                 alt="<?= htmlspecialchars($recommendedTitle) ?>"
                                                 class="card-img-top">
                                        <?php else: ?>
                                            <img src="https://via.placeholder.com/200x113?text=No+Image"
                                                 alt="<?= htmlspecialchars($recommendedTitle) ?>"
                                                 class="card-img-top">
                                        <?php endif; ?>
                                    </a>
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <a href="/videos/<?= $recommendedVideo->id ?>?lang=<?= $currentLang ?>" class="text-decoration-none">
                                                <?= htmlspecialchars(mb_substr($recommendedTitle, 0, 30)) ?><?= mb_strlen($recommendedTitle) > 30 ? '...' : '' ?>
                                            </a>
                                        </h6>
                                        <small class="text-muted">
                                            <?= htmlspecialchars($recommendedVideo->duration ?: 'N/A') ?>
                                            | <?= number_format($recommendedVideo->view_cnt) ?><?= $currentLang === 'zh' ? '次' : '' ?>
                                        </small>
                                        <p class="card-text">
                                            <?= htmlspecialchars(mb_substr($recommendedDesc, 0, 50)) ?><?= mb_strlen($recommendedDesc) > 50 ? '...' : '' ?>
                                        </p>
                                    </div>
                                    <div class="card-footer">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="recommended-tags">
                                                <?php
                                                $displayTags = array_slice($recommendedVideo->tags, 0, 2);
                                                foreach ($displayTags as $tag):
                                                    $tagName = $currentLang === 'zh' ? $tag['name_cn'] : $tag['name_en'];
                                                ?>
                                                    <a href="/videos?tag_id=<?= $tag['id'] ?>&lang=<?= $currentLang ?>"
                                                       class="btn btn-outline-success btn-sm me-1">
                                                        <?= htmlspecialchars($tagName) ?>
                                                    </a>
                                                <?php endforeach; ?>
                                            </div>
                                            <?php if (!empty($recommendedVideo->collections)):
                                                $collection = $recommendedVideo->collections[0];
                                                $collectionName = $currentLang === 'zh' ? $collection['name_cn'] : $collection['name_en'];
                                            ?>
                                                <a href="/videos?collection_id=<?= $collection['id'] ?>&lang=<?= $currentLang ?>"
                                                   class="btn btn-outline-success btn-md">
                                                    <?= htmlspecialchars($collectionName) ?>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
