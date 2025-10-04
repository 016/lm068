<?php
use App\Constants\LinkStatus;
?>
<!-- Shared Video Link Form Content -->
<div class="form-container">
    <div class="form-header">
        <i class="bi bi-link-45deg form-icon"></i>
        <h3>视频链接详细信息</h3>
    </div>

    <div class="form-body">
        <?php if (!empty($videoLink->errors)): ?>
        <div class="alert alert-danger mb-4">
            <h6 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> 表单验证失败</h6>
            <ul class="mb-0">
                <?php foreach ($videoLink->errors as $field => $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <form id="videoLinkEditForm" action="<?= $formAction ?>" method="POST">
            <?php if (!$videoLink->isNew): ?>
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" name="id" id="id" value="<?= htmlspecialchars($videoLink->id) ?>">
            <?php endif; ?>

            <!-- 基本信息 -->
            <div class="form-section">
                <h4 class="form-section-title">
                    <i class="bi bi-info-circle form-section-icon"></i>
                    基本信息<?php if (!$videoLink->isNew): ?> - ID: #<?= str_pad($videoLink->id, 3, '0', STR_PAD_LEFT) ?><?php endif; ?>
                </h4>

                <div class="row">
                    <div class="col-md-6 pb-3">
                        <?php if (!$videoLink->isNew): ?>
                        <div class="form-group">
                            <label for="videoLinkId" class="form-label">链接ID</label>
                            <input type="text" class="form-control" id="videoLinkId" value="#<?= str_pad($videoLink->id, 3, '0', STR_PAD_LEFT) ?>" disabled>
                            <div class="form-text">系统自动生成,不可修改</div>
                        </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="content_id" class="form-label required">关联内容</label>
                            <select class="form-control form-select <?= isset($videoLink->errors['content_id']) ? 'is-invalid' : '' ?>" id="content_id" name="content_id" required>
                                <option value="">请选择关联内容</option>
                                <?php foreach ($contentsList as $content): ?>
                                    <option value="<?= $content['id'] ?>" <?= ($videoLink->content_id == $content['id']) ? 'selected' : '' ?>>
                                        #<?= $content['id'] ?> - <?= htmlspecialchars($content['text']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($videoLink->errors['content_id'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($videoLink->errors['content_id']) ?></div>
                            <?php endif; ?>
                            <div class="form-text">选择要关联的视频内容</div>
                        </div>
                    </div>

                    <div class="col-md-6 pb-3">
                        <div class="form-group">
                            <label for="platform_id" class="form-label required">视频平台</label>
                            <select class="form-control form-select <?= isset($videoLink->errors['platform_id']) ? 'is-invalid' : '' ?>" id="platform_id" name="platform_id" required>
                                <option value="">请选择视频平台</option>
                                <?php foreach ($platformsList as $platform): ?>
                                    <option value="<?= $platform['id'] ?>" <?= ($videoLink->platform_id == $platform['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($platform['text']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($videoLink->errors['platform_id'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($videoLink->errors['platform_id']) ?></div>
                            <?php endif; ?>
                            <div class="form-text">选择第三方视频平台</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 pb-3">
                        <div class="form-group">
                            <label for="external_url" class="form-label required">第三方链接</label>
                            <input type="url" class="form-control <?= isset($videoLink->errors['external_url']) ? 'is-invalid' : '' ?>" id="external_url" name="external_url" value="<?= htmlspecialchars($videoLink->external_url ?? '') ?>" maxlength="500" required placeholder="https://example.com/video/123">
                            <?php if (isset($videoLink->errors['external_url'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($videoLink->errors['external_url']) ?></div>
                            <?php endif; ?>
                            <div class="form-text">完整的第三方视频链接URL</div>
                        </div>
                    </div>

                    <div class="col-md-6 pb-3">
                        <div class="form-group">
                            <label for="external_video_id" class="form-label required">第三方视频ID</label>
                            <input type="text" class="form-control <?= isset($videoLink->errors['external_video_id']) ? 'is-invalid' : '' ?>" id="external_video_id" name="external_video_id" value="<?= htmlspecialchars($videoLink->external_video_id ?? '') ?>" maxlength="200" required placeholder="例如: BV1234567890">
                            <?php if (isset($videoLink->errors['external_video_id'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($videoLink->errors['external_video_id']) ?></div>
                            <?php endif; ?>
                            <div class="form-text">第三方平台的视频唯一标识ID</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 统计数据 -->
            <div class="form-section">
                <h4 class="form-section-title">
                    <i class="bi bi-bar-chart form-section-icon"></i>
                    统计数据
                </h4>

                <div class="row">
                    <div class="col-md-4 pb-3">
                        <div class="form-group">
                            <label for="play_cnt" class="form-label">播放数</label>
                            <input type="number" class="form-control <?= isset($videoLink->errors['play_cnt']) ? 'is-invalid' : '' ?>" id="play_cnt" name="play_cnt" value="<?= htmlspecialchars($videoLink->play_cnt ?? 0) ?>" min="0">
                            <?php if (isset($videoLink->errors['play_cnt'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($videoLink->errors['play_cnt']) ?></div>
                            <?php endif; ?>
                            <div class="form-text">视频在第三方平台的播放次数</div>
                        </div>
                    </div>

                    <div class="col-md-4 pb-3">
                        <div class="form-group">
                            <label for="like_cnt" class="form-label">点赞数</label>
                            <input type="number" class="form-control <?= isset($videoLink->errors['like_cnt']) ? 'is-invalid' : '' ?>" id="like_cnt" name="like_cnt" value="<?= htmlspecialchars($videoLink->like_cnt ?? 0) ?>" min="0">
                            <?php if (isset($videoLink->errors['like_cnt'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($videoLink->errors['like_cnt']) ?></div>
                            <?php endif; ?>
                            <div class="form-text">视频获得的点赞数量</div>
                        </div>
                    </div>

                    <div class="col-md-4 pb-3">
                        <div class="form-group">
                            <label for="favorite_cnt" class="form-label">收藏数</label>
                            <input type="number" class="form-control <?= isset($videoLink->errors['favorite_cnt']) ? 'is-invalid' : '' ?>" id="favorite_cnt" name="favorite_cnt" value="<?= htmlspecialchars($videoLink->favorite_cnt ?? 0) ?>" min="0">
                            <?php if (isset($videoLink->errors['favorite_cnt'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($videoLink->errors['favorite_cnt']) ?></div>
                            <?php endif; ?>
                            <div class="form-text">视频被收藏的次数</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 pb-3">
                        <div class="form-group">
                            <label for="download_cnt" class="form-label">下载数</label>
                            <input type="number" class="form-control <?= isset($videoLink->errors['download_cnt']) ? 'is-invalid' : '' ?>" id="download_cnt" name="download_cnt" value="<?= htmlspecialchars($videoLink->download_cnt ?? 0) ?>" min="0">
                            <?php if (isset($videoLink->errors['download_cnt'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($videoLink->errors['download_cnt']) ?></div>
                            <?php endif; ?>
                            <div class="form-text">视频被下载的次数</div>
                        </div>
                    </div>

                    <div class="col-md-4 pb-3">
                        <div class="form-group">
                            <label for="comment_cnt" class="form-label">评论数</label>
                            <input type="number" class="form-control <?= isset($videoLink->errors['comment_cnt']) ? 'is-invalid' : '' ?>" id="comment_cnt" name="comment_cnt" value="<?= htmlspecialchars($videoLink->comment_cnt ?? 0) ?>" min="0">
                            <?php if (isset($videoLink->errors['comment_cnt'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($videoLink->errors['comment_cnt']) ?></div>
                            <?php endif; ?>
                            <div class="form-text">视频收到的评论数量</div>
                        </div>
                    </div>

                    <div class="col-md-4 pb-3">
                        <div class="form-group">
                            <label for="share_cnt" class="form-label">分享数</label>
                            <input type="number" class="form-control <?= isset($videoLink->errors['share_cnt']) ? 'is-invalid' : '' ?>" id="share_cnt" name="share_cnt" value="<?= htmlspecialchars($videoLink->share_cnt ?? 0) ?>" min="0">
                            <?php if (isset($videoLink->errors['share_cnt'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($videoLink->errors['share_cnt']) ?></div>
                            <?php endif; ?>
                            <div class="form-text">视频被分享的次数</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 状态设置 -->
            <div class="form-section">
                <h4 class="form-section-title">
                    <i class="bi bi-toggles form-section-icon"></i>
                    状态设置
                </h4>

                <div class="row">
                    <div class="col-md-6 pb-3">
                        <div class="form-group">
                            <label for="status_id" class="form-label">链接状态</label>
                            <select class="form-control form-select <?= isset($videoLink->errors['status_id']) ? 'is-invalid' : '' ?>" id="status_id" name="status_id">
                                <?php foreach (LinkStatus::getAllValues() as $value => $label): ?>
                                    <option value="<?= $value ?>" <?= ($videoLink->status_id == $value) ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($videoLink->errors['status_id'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($videoLink->errors['status_id']) ?></div>
                            <?php endif; ?>
                            <div class="form-text">链接的有效状态</div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!$videoLink->isNew): ?>
            <!-- 时间信息 -->
            <div class="form-section">
                <h4 class="form-section-title">
                    <i class="bi bi-clock form-section-icon"></i>
                    时间信息
                </h4>

                <div class="row">
                    <div class="col-md-6 pb-3">
                        <div class="form-group">
                            <label for="created_at" class="form-label">创建时间</label>
                            <input type="text" class="form-control" id="created_at" name="created_at" value="<?= htmlspecialchars($videoLink->created_at ?? '') ?>" disabled>
                        </div>
                    </div>
                    <div class="col-md-6 pb-3">
                        <div class="form-group">
                            <label for="updated_at" class="form-label">最后更新时间</label>
                            <input type="text" class="form-control" id="updated_at" name="updated_at" value="<?= htmlspecialchars($videoLink->updated_at ?? '') ?>" disabled>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- 表单操作按钮 -->
            <div class="form-actions">
                <a href="/video-links" class="btn btn-outline-secondary">
                    <i class="bi bi-x-lg"></i>
                    取消
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i>
                    <?= $videoLink->isNew ? '创建链接' : '保存修改' ?>
                </button>
            </div>
        </form>
    </div>
</div>
