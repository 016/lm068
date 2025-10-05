<?php
use App\Constants\ContentStatus;
use App\Constants\ContentType;
?>
<!-- Shared Content Form Content -->
<div class="form-container">
    <div class="form-header">
        <i class="bi bi-camera-video form-icon"></i>
        <h3>内容详细信息</h3>
    </div>
    
    <div class="form-body">
        <?php if (!empty($content->errors)): ?>
        <div class="alert alert-danger mb-4">
            <h6 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> 表单验证失败</h6>
            <ul class="mb-0">
                <?php foreach ($content->errors as $field => $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <form id="contentEditForm" action="<?= $formAction ?>" method="POST" enctype="multipart/form-data">
            <?php if (!$content->isNew): ?>
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" name="id" id="id" value="<?= htmlspecialchars($content->id) ?>">
            <?php endif; ?>
            
            <!-- 基本信息 -->
            <div class="form-section">
                <h4 class="form-section-title">
                    <i class="bi bi-info-circle form-section-icon"></i>
                    基本信息<?php if (!$content->isNew): ?> - ID: #<?= str_pad($content->id, 3, '0', STR_PAD_LEFT) ?><?php endif; ?>
                </h4>

                <div class="row">
                    <div class="col-md-6 pb-3">
                        <?php if (!$content->isNew): ?>
                        <div class="form-group">
                            <label for="contentId" class="form-label">内容ID</label>
                            <input type="text" class="form-control" id="contentId" value="#<?= str_pad($content->id, 3, '0', STR_PAD_LEFT) ?>" disabled>
                            <div class="form-text">系统自动生成,不可修改</div>
                        </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="content_type_id" class="form-label required">内容类型</label>
                            <select class="form-control form-select <?= isset($content->errors['content_type_id']) ? 'is-invalid' : '' ?>" id="content_type_id" name="content_type_id" required>
                                <option value="">请选择内容类型</option>
                                <?php foreach (ContentType::getAllValues() as $value => $label): ?>
                                    <option value="<?= $value ?>" <?= ($content->content_type_id == $value) ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($content->errors['content_type_id'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($content->errors['content_type_id']) ?></div>
                            <?php endif; ?>
                            <div class="form-text">选择内容的类型分类</div>
                        </div>
                    </div>
                    <div class="col-md-6 pb-3">
                        <div class="form-group">
                            <label for="thumbnailUpload" class="form-label">缩略图管理</label>
                            <div class="thumbnail-section">
                                <div class="thumbnail-upload-area">
                                    <input type="file" class="form-control" id="thumbnailUpload" name="thumbnail" accept="image/*">
                                    <div class="form-text">上传缩略图文件 (支持 JPG、PNG、GIF、WEBP 格式)</div>
                                </div>
                                <div class="thumbnail-preview-container">
                                    <?php
                                    $thumbnailUrl = $content->getThumbnailUrl();
                                    if ($thumbnailUrl): ?>
                                        <img src="<?= htmlspecialchars($thumbnailUrl) ?>" alt="内容缩略图" class="thumbnail-preview" id="thumbnailPreview">
                                    <?php else: ?>
                                        <img src="" alt="暂无缩略图" class="thumbnail-preview" id="thumbnailPreview" style="display:none;">
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="form-text">缩略图预览区域</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 pb-3">
                        <div class="form-group">
                            <label for="name_cn" class="form-label required">中文标题</label>
                            <input type="text" class="form-control <?= isset($content->errors['title_cn']) ? 'is-invalid' : '' ?>" id="name_cn" name="name_cn" value="<?= htmlspecialchars($content->title_cn ?? '') ?>" maxlength="255" required>
                            <?php if (isset($content->errors['title_cn'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($content->errors['title_cn']) ?></div>
                            <?php endif; ?>
                            <div class="form-text">内容的中文标题</div>
                        </div>
                    </div>
                    <div class="col-md-6 pb-3">
                        <div class="form-group">
                            <label for="name_en" class="form-label required">英文标题</label>
                            <input type="text" class="form-control <?= isset($content->errors['title_en']) ? 'is-invalid' : '' ?>" id="name_en" name="name_en" value="<?= htmlspecialchars($content->title_en ?? '') ?>" maxlength="255" required>
                            <?php if (isset($content->errors['title_en'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($content->errors['title_en']) ?></div>
                            <?php endif; ?>
                            <div class="form-text">内容的英文标题</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 pb-3">
                        <div class="form-group">
                            <label for="code" class="form-label">内部管理代码</label>
                            <input type="text" class="form-control <?= isset($content->errors['code']) ? 'is-invalid' : '' ?>" id="code" name="code" value="<?= htmlspecialchars($content->code ?? '') ?>" maxlength="50" placeholder="请输入内部管理代码">
                            <?php if (isset($content->errors['code'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($content->errors['code']) ?></div>
                            <?php endif; ?>
                            <div class="form-text">内容的内部管理代码，用于内部标识</div>
                        </div>
                    </div>
                    <div class="col-md-6 pb-3">
                        <div class="form-group">
                            <label for="author" class="form-label">内容作者</label>
                            <input type="text" class="form-control <?= isset($content->errors['author']) ? 'is-invalid' : '' ?>" id="author" name="author" value="<?= htmlspecialchars($content->author ?? 'DP') ?>" maxlength="255" placeholder="请输入内容作者名称">
                            <?php if (isset($content->errors['author'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($content->errors['author']) ?></div>
                            <?php endif; ?>
                            <div class="form-text">内容的创作者或制作者</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 pb-3">
                        <div class="form-group">
                            <label for="duration" class="form-label">内容时长</label>
                            <input type="text" class="form-control <?= isset($content->errors['duration']) ? 'is-invalid' : '' ?>" id="duration" name="duration" value="<?= htmlspecialchars($content->duration ?? '') ?>" placeholder="mm:ss">
                            <?php if (isset($content->errors['duration'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($content->errors['duration']) ?></div>
                            <?php endif; ?>
                            <div class="form-text">格式：分钟:秒(如 12:35)</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 分类配置 -->
            <div class="form-section">
                <h4 class="form-section-title">
                    <i class="bi bi-collection form-section-icon"></i>
                    分类配置
                </h4>

                <div class="row">
                    <div class="col-md-6 pb-3">
                        <div class="form-group">
                            <label for="contentTagsMultiSelect" class="form-label">内容标签</label>
                            <div id="contentTagsMultiSelect"></div>
                            <div class="form-text">为内容选择相关标签，便于用户查找和分类</div>
                        </div>
                    </div>
                    <div class="col-md-6 pb-3">
                        <div class="form-group">
                            <label for="contentCollectionsMultiSelect" class="form-label">内容合集</label>
                            <div id="contentCollectionsMultiSelect"></div>
                            <div class="form-text">将内容加入到相关合集中，便于系列化管理</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 简介设置 -->
            <div class="form-section">
                <h4 class="form-section-title">
                    <i class="bi bi-body-text form-section-icon"></i>
                    简介设置
                </h4>

                <div class="row">
                    <div class="col-md-6 pb-3">
                        <div class="form-group">
                            <label for="short_desc_cn" class="form-label">中文简介</label>
                            <input type="text" class="form-control <?= isset($content->errors['short_desc_cn']) ? 'is-invalid' : '' ?>" id="short_desc_cn" name="short_desc_cn" value="<?= htmlspecialchars($content->short_desc_cn ?? '') ?>" maxlength="300">
                            <?php if (isset($content->errors['short_desc_cn'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($content->errors['short_desc_cn']) ?></div>
                            <?php endif; ?>
                            <div class="form-text">内容的简短中文描述(最多300字符)</div>
                        </div>
                    </div>
                    <div class="col-md-6 pb-3">
                        <div class="form-group">
                            <label for="short_desc_en" class="form-label">英文简介</label>
                            <input type="text" class="form-control <?= isset($content->errors['short_desc_en']) ? 'is-invalid' : '' ?>" id="short_desc_en" name="short_desc_en" value="<?= htmlspecialchars($content->short_desc_en ?? '') ?>" maxlength="300">
                            <?php if (isset($content->errors['short_desc_en'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($content->errors['short_desc_en']) ?></div>
                            <?php endif; ?>
                            <div class="form-text">内容的简短英文描述(最多300字符)</div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="desc_cn" class="form-label">中文描述</label>
                    <textarea class="form-control <?= isset($content->errors['desc_cn']) ? 'is-invalid' : '' ?>" id="desc_cn" name="desc_cn" rows="4" placeholder="请输入内容的详细中文描述..." maxlength="65535"><?= htmlspecialchars($content->desc_cn ?? '') ?></textarea>
                    <?php if (isset($content->errors['desc_cn'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($content->errors['desc_cn']) ?></div>
                    <?php endif; ?>
                    <div class="form-text">内容的详细中文说明(支持Markdown格式)</div>
                </div>

                <div class="form-group">
                    <label for="desc_en" class="form-label">英文描述</label>
                    <textarea class="form-control <?= isset($content->errors['desc_en']) ? 'is-invalid' : '' ?>" id="desc_en" name="desc_en" rows="4" placeholder="Please enter the detailed English description of the content..." maxlength="65535"><?= htmlspecialchars($content->desc_en ?? '') ?></textarea>
                    <?php if (isset($content->errors['desc_en'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($content->errors['desc_en']) ?></div>
                    <?php endif; ?>
                    <div class="form-text">内容的详细英文说明(支持Markdown格式)</div>
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
                            <label for="status_id" class="form-label">发布状态</label>
                            <select class="form-control form-select <?= isset($content->errors['status_id']) ? 'is-invalid' : '' ?>" id="status_id" name="status_id">
                                <?php foreach (ContentStatus::getAllValues() as $value => $label): ?>
                                    <option value="<?= $value ?>" <?= ($content->status_id == $value) ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($content->errors['status_id'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($content->errors['status_id']) ?></div>
                            <?php endif; ?>
                            <div class="form-text">选择内容当前的制作和发布状态</div>
                        </div>
                    </div>
                    <div class="col-md-6 pb-3">
                        <div class="form-group">
                            <label for="view_cnt" class="form-label">观看次数</label>
                            <input type="number" class="form-control" id="view_cnt" name="view_cnt" value="<?= $content->view_cnt ?? 0 ?>" min="0" disabled>
                            <div class="form-text">内容的观看次数(自动统计)</div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!$content->isNew): ?>
            <!-- 统计信息 -->
            <div class="form-section">
                <h4 class="form-section-title">
                    <i class="bi bi-bar-chart form-section-icon"></i>
                    统计信息
                </h4>
                
                <div class="info-box">
                    <i class="bi bi-info-circle info-icon"></i>
                    <div class="info-content">
                        <div class="info-title">数据统计</div>
                        <div class="info-text">以下数据为系统自动统计,实时更新</div>
                    </div>
                </div>

                <div class="stats-row">
                    <div class="stat-item">
                        <div class="stat-value"><?= number_format($content->view_cnt ?? 0) ?></div>
                        <div class="stat-label">总观看次数</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value"><?= number_format($content->pv_cnt ?? 0) ?></div>
                        <div class="stat-label">PV计数</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value"><?= count($relatedTags ?? []) ?></div>
                        <div class="stat-label">关联标签</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value"><?= count($relatedCollections ?? []) ?></div>
                        <div class="stat-label">关联合集</div>
                    </div>
                </div>
            </div>

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
                            <input type="text" class="form-control" id="created_at" name="created_at" value="<?= htmlspecialchars($content->created_at ?? '') ?>" disabled>
                        </div>
                    </div>
                    <div class="col-md-6 pb-3">
                        <div class="form-group">
                            <label for="updated_at" class="form-label">最后更新时间</label>
                            <input type="text" class="form-control" id="updated_at" name="updated_at" value="<?= htmlspecialchars($content->updated_at ?? '') ?>" disabled>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- 表单操作按钮 -->
            <div class="form-actions">
                <a href="/content" class="btn btn-outline-secondary">
                    <i class="bi bi-x-lg"></i>
                    取消
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i>
                    <?= $content->isNew ? '创建内容' : '保存修改' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // 将动态数据传递给JS
    window.inputData = {
        // 内容标签数据
        tagsList: <?= json_encode($tagsList ?? []) ?>,
        selectedTagIds: <?= json_encode($selectedTagIds ?? []) ?>,

        // 内容合集数据
        collectionsList: <?= json_encode($collectionsList ?? []) ?>,
        selectedCollectionIds: <?= json_encode($selectedCollectionIds ?? []) ?>,
    };
</script>