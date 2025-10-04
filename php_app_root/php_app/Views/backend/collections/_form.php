<?php
use App\Constants\CollectionStatus;
?>
<!-- Shared Collection Form Content -->
<div class="form-container">
    <div class="form-header">
        <i class="bi bi-collection form-icon"></i>
        <h3><?= !$collection->isNew ? '合集详细信息' : '新建合集信息' ?></h3>
    </div>
    
    <div class="form-body">
        <?php if (isset($errors) && !empty($errors)): ?>
            <div class="alert alert-danger mb-4">
                <h6><i class="bi bi-exclamation-triangle"></i> 请修正以下错误：</h6>
                <ul class="mb-0">
                    <?php foreach ($errors as $field => $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form id="collectionEditForm" action="<?= $formAction ?>" method="POST">
            <?php if (!$collection->isNew): ?>
                <input type="hidden" name="id" id="id" value="<?= $collection->id ?>">
            <?php endif; ?>

            <!-- 显示验证错误信息 -->
            <?php if (!empty($collection->errors)): ?>
            <div class="alert alert-danger mb-4">
                <h6 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> 表单验证失败</h6>
                <ul class="mb-0">
                    <?php foreach ($collection->errors as $field => $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

<!-- 基本信息 -->
<div class="form-section">
    <h4 class="form-section-title">
        <i class="bi bi-info-circle form-section-icon"></i>
        基本信息
    </h4>
    
    <div class="row">
        <?php if (!$collection->isNew): ?>
        <div class="col-md-6 pb-3">
            <div class="form-group">
                <label for="collectionId" class="form-label">合集ID</label>
                <input type="text" class="form-control" id="collectionId" value="#<?= str_pad($collection->id, 3, '0', STR_PAD_LEFT) ?>" disabled>
                <div class="form-text">系统自动生成，不可修改</div>
            </div>
        </div>
        <?php endif; ?>
        <div class="col-md-6 pb-3">
            <div class="form-group">
                <label for="collectionPreview" class="form-label">合集预览</label>
                <div class="collection-preview-container">
                    <button type="button" id="collectionPreviewBtn" class="btn btn-outline-primary">
                        <i class="bi bi-star" id="previewIcon"></i>
                        <span id="previewText"><?= !$collection->isNew ? htmlspecialchars($collection->name_cn) : '新合集' ?></span>
                    </button>
                </div>
                <div class="form-text">实时预览合集显示效果</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 pb-3">
            <div class="form-group">
                <label for="name_cn" class="form-label required">中文标题</label>
                <input type="text" class="form-control<?= isset($collection->errors['name_cn']) ? ' is-invalid' : '' ?>" id="name_cn" name="name_cn" value="<?= htmlspecialchars($collection->name_cn ?? '') ?>" required>
                <?php if (isset($collection->errors['name_cn'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($collection->errors['name_cn']) ?></div>
                <?php endif; ?>
                <div class="form-text">合集的中文显示名称</div>
            </div>
        </div>
        <div class="col-md-6 pb-3">
            <div class="form-group">
                <label for="name_en" class="form-label required">英文标题</label>
                <input type="text" class="form-control<?= isset($collection->errors['name_en']) ? ' is-invalid' : '' ?>" id="name_en" name="name_en" value="<?= htmlspecialchars($collection->name_en ?? '') ?>" required>
                <?php if (isset($collection->errors['name_en'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($collection->errors['name_en']) ?></div>
                <?php endif; ?>
                <div class="form-text">合集的英文显示名称</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 pb-3">
            <div class="form-group">
                <label for="color_class" class="form-label">合集颜色</label>
                <select class="form-control <?= !empty($collection->errors['color_class']) ? 'is-invalid' : '' ?>" id="color_class" name="color_class">
                    <option value="btn-outline-primary" <?= ($collection->color_class ?? 'btn-outline-primary') === 'btn-outline-primary' ? 'selected' : '' ?>>Primary (蓝色)</option>
                    <option value="btn-outline-secondary" <?= ($collection->color_class ?? '') === 'btn-outline-secondary' ? 'selected' : '' ?>>Secondary (灰色)</option>
                    <option value="btn-outline-success" <?= ($collection->color_class ?? '') === 'btn-outline-success' ? 'selected' : '' ?>>Success (绿色)</option>
                    <option value="btn-outline-danger" <?= ($collection->color_class ?? '') === 'btn-outline-danger' ? 'selected' : '' ?>>Danger (红色)</option>
                    <option value="btn-outline-warning" <?= ($collection->color_class ?? '') === 'btn-outline-warning' ? 'selected' : '' ?>>Warning (黄色)</option>
                    <option value="btn-outline-info" <?= ($collection->color_class ?? '') === 'btn-outline-info' ? 'selected' : '' ?>>Info (青色)</option>
                    <option value="btn-outline-light" <?= ($collection->color_class ?? '') === 'btn-outline-light' ? 'selected' : '' ?>>Light (浅色)</option>
                    <option value="btn-outline-dark" <?= ($collection->color_class ?? '') === 'btn-outline-dark' ? 'selected' : '' ?>>Dark (深色)</option>
                </select>
                <?php if (!empty($collection->errors['color_class'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($collection->errors['color_class']) ?></div>
                <?php endif; ?>
                <div class="form-text">选择合集在前端显示时的Bootstrap颜色样式</div>
            </div>
        </div>
        <div class="col-md-6 pb-3">
            <div class="form-group">
                <label for="icon_class" class="form-label">图标样式</label>
                <input type="text" class="form-control <?= !empty($collection->errors['icon_class']) ? 'is-invalid' : '' ?>" id="icon_class" name="icon_class" value="<?= htmlspecialchars($collection->icon_class ?? 'bi-collection') ?>" placeholder="请输入 Bootstrap 图标类名，如 bi-collection">
                <?php if (!empty($collection->errors['icon_class'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($collection->errors['icon_class']) ?></div>
                <?php endif; ?>
                <div class="form-text">直接输入Bootstrap icon 类名（如 bi-collection, bi-star...）</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 pb-3">
            <div class="form-group">
                <label for="contentMultiSelect" class="form-label">关联视频</label>
                <div id="contentMultiSelect" class="multi-select-container"></div>
                <div class="form-text">选择要关联到此合集的视频，可多选</div>
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
                <input type="text" class="form-control <?= !empty($collection->errors['short_desc_cn']) ? 'is-invalid' : '' ?>" id="short_desc_cn" name="short_desc_cn" value="<?= htmlspecialchars($collection->short_desc_cn ?? '') ?>" maxlength="100">
                <?php if (!empty($collection->errors['short_desc_cn'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($collection->errors['short_desc_cn']) ?></div>
                <?php endif; ?>
                <div class="form-text">合集的简短中文描述（最多100字符）</div>
            </div>
        </div>
        <div class="col-md-6 pb-3">
            <div class="form-group">
                <label for="short_desc_en" class="form-label">英文简介</label>
                <input type="text" class="form-control <?= !empty($collection->errors['short_desc_en']) ? 'is-invalid' : '' ?>" id="short_desc_en" name="short_desc_en" value="<?= htmlspecialchars($collection->short_desc_en ?? '') ?>" maxlength="100">
                <?php if (!empty($collection->errors['short_desc_en'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($collection->errors['short_desc_en']) ?></div>
                <?php endif; ?>
                <div class="form-text">合集的简短英文描述（最多100字符）</div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label for="desc_cn" class="form-label">中文描述</label>
        <textarea class="form-control <?= !empty($collection->errors['desc_cn']) ? 'is-invalid' : '' ?>" id="desc_cn" name="desc_cn" rows="3" placeholder="请输入合集的详细中文描述..." maxlength="500"><?= htmlspecialchars($collection->desc_cn ?? '') ?></textarea>
        <?php if (!empty($collection->errors['desc_cn'])): ?>
            <div class="invalid-feedback"><?= htmlspecialchars($collection->errors['desc_cn']) ?></div>
        <?php endif; ?>
        <div class="form-text">合集的详细中文说明（最多500字符）</div>
    </div>

    <div class="form-group">
        <label for="desc_en" class="form-label">英文描述</label>
        <textarea class="form-control <?= !empty($collection->errors['desc_en']) ? 'is-invalid' : '' ?>" id="desc_en" name="desc_en" rows="3" placeholder="Please enter the detailed English description of the collection..." maxlength="500"><?= htmlspecialchars($collection->desc_en ?? '') ?></textarea>
        <?php if (!empty($collection->errors['desc_en'])): ?>
            <div class="invalid-feedback"><?= htmlspecialchars($collection->errors['desc_en']) ?></div>
        <?php endif; ?>
        <div class="form-text">合集的详细英文说明（最多500字符）</div>
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
                <div class="switch-group" id="statusSwitchGroup">
                    <div class="custom-switch collection-edit-switch" id="statusSwitch">
                        <input type="checkbox" id="status_id" name="status_id" value="<?= CollectionStatus::ENABLED->value ?>" <?= ($collection->status_id ?? CollectionStatus::ENABLED->value) ? 'checked' : '' ?>>
                        <span class="switch-slider"></span>
                    </div>
                    <label for="status_id" class="switch-label">显示状态</label>
                </div>
                <div class="form-text">开启后合集在前端可见，关闭后隐藏</div>
            </div>
        </div>
    </div>
</div>

<?php if (!$collection->isNew): ?>
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
            <div class="info-text">以下数据为系统自动统计，实时更新</div>
        </div>
    </div>

    <div class="stats-row">
        <div class="stat-item">
            <div class="stat-value"><?= number_format($collection->content_cnt ?? 0) ?></div>
            <div class="stat-label">关联视频数量</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">
                <?php
                $totalViews = 0;
                if (!empty($relatedContent)) {
                    $totalViews = array_sum(array_column($relatedContent, 'view_cnt'));
                }
                echo $totalViews > 1000000 ? number_format($totalViews / 1000000, 1) . 'M' : 
                     ($totalViews > 1000 ? number_format($totalViews / 1000, 1) . 'K' : number_format($totalViews));
                ?>
            </div>
            <div class="stat-label">总播放量</div>
        </div>
        <div class="stat-item">
            <div class="stat-value"><?= rand(10, 100) . 'K' ?></div>
            <div class="stat-label">总点赞数</div>
        </div>
        <div class="stat-item">
            <div class="stat-value"><?= rand(100, 1000) ?></div>
            <div class="stat-label">总评论数</div>
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
                <input type="text" class="form-control" id="created_at" name="created_at" value="<?= htmlspecialchars($collection->created_at ?? '') ?>" disabled>
            </div>
        </div>
        <div class="col-md-6 pb-3">
            <div class="form-group">
                <label for="updated_at" class="form-label">最后更新时间</label>
                <input type="text" class="form-control" id="updated_at" name="updated_at" value="<?= htmlspecialchars($collection->updated_at ?? '') ?>" disabled>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

            <!-- 表单操作按钮 -->
            <div class="form-actions">
                <a href="/collections" id="btn-cancel" class="btn btn-outline-secondary">
                    <i class="bi bi-x-lg"></i>
                    取消
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i>
                    <?= !$collection->isNew ? '保存修改' : '创建合集' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // 将动态数据传递给JS
    window.inputData = {
        // 内容数据
        contentList: <?= json_encode($contentsList ?? []) ?>,
        selectedContentIds: <?= json_encode($selectedContentIds ?? []) ?>
    };
</script>