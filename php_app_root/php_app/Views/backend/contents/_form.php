<?php
use App\Constants\ContentStatus;
use App\Constants\ContentType;
use App\Helpers\FormFieldBuilder;
use App\Helpers\HtmlHelper;

/**
 * @var $content App\Models\Content
 * @var $isCopyMode bool
 */

// 检测是否为复制模式
$isCopyMode = isset($isCopyMode) && $isCopyMode === true;
$isNewContent = $content->isNew || $isCopyMode;  // 复制模式也视为新建

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
            <?php if (!$isNewContent): ?>
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" name="id" id="id" value="<?= htmlspecialchars($content->id) ?>">
            <?php endif; ?>

            <!-- 基本信息 -->
            <div class="form-section">
                <h4 class="form-section-title">
                    <i class="bi bi-info-circle form-section-icon"></i>
                    基本信息<?php if (!$isNewContent): ?> - ID: #<?= str_pad($content->id, 3, '0', STR_PAD_LEFT) ?><?php endif; ?>
                </h4>

                <div class="row">
                    <?php if (!$isNewContent): ?>
                        <?= FormFieldBuilder::for($content, 'id')
                            ->label('内容ID')
                            ->disabled()
                            ->formatter(fn($v) => '#' . str_pad($v, 3, '0', STR_PAD_LEFT))
                            ->helpText('系统自动生成,不可修改')
                            ->render() ?>
                    <?php endif; ?>

                    <?php
                    $contentTypeOptions = [];
                    foreach (ContentType::getAllValues() as $value => $label) {
                        $contentTypeOptions[] = ['value' => $value, 'text' => $label];
                    }
                    ?>
                    <?= FormFieldBuilder::for($content, 'content_type_id')
                        ->type('custom-select')
                        ->label('内容类型')
                        ->options($contentTypeOptions)
                        ->placeholder('请选择内容类型')
                        ->render() ?>
                    
                    <?= FormFieldBuilder::for($content, 'thumbnail')
                        ->type('image-uploader')
                        ->label('缩略图管理')
                        ->helpText('缩略图预览区域')
                        ->render() ?>
                </div>

                <div class="row">
                    <?= FormFieldBuilder::for($content, 'suggested_content_types_cn')->label('AI中文分类')->render() ?>
                    <?= FormFieldBuilder::for($content, 'suggested_content_types_en')->label('AI英文分类')->render() ?>
                </div>

                <div class="row">
                    <?= FormFieldBuilder::for($content, 'title_cn')->label('中文标题')->render() ?>
                    <?= FormFieldBuilder::for($content, 'title_en')->label('英文标题')->render() ?>
                </div>

                <div class="row">
                    <?= FormFieldBuilder::for($content, 'code')
                        ->label('内部管理代码')
                        ->placeholder('请输入内部管理代码')
                        ->render() ?>
                    
                    <?= FormFieldBuilder::for($content, 'author')
                        ->label('内容作者')
                        ->placeholder('请输入内容作者名称')
                        ->render() ?>
                </div>

                <div class="row">
                    <?= FormFieldBuilder::for($content, 'duration')
                        ->label('内容时长(s)')
                        ->placeholder('mm:ss')
                        ->helpText('格式：秒(如 123)')
                        ->render() ?>
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

                <div class="row">
                    <?= FormFieldBuilder::for($content, 'suggested_tags_cn')->label('AI中文标签')->render() ?>
                    <?= FormFieldBuilder::for($content, 'suggested_tags_en')->label('AI英文标签')->render() ?>
                </div>
            </div>

            <!-- 简介设置 -->
            <div class="form-section">
                <h4 class="form-section-title">
                    <i class="bi bi-body-text form-section-icon"></i>
                    简介设置
                </h4>

                <div class="row">
                    <?= FormFieldBuilder::for($content, 'short_desc_cn')
                        ->type('textarea')
                        ->label('中文简介')
                        ->useHtmlHelper(true)
                        ->cssClass('col-md-6 pb-3')
                        ->render() ?>
                    
                    <?= FormFieldBuilder::for($content, 'short_desc_en')
                        ->type('textarea')
                        ->label('英文简介')
                        ->useHtmlHelper(true)
                        ->cssClass('col-md-6 pb-3')
                        ->render() ?>
                </div>

                <?= FormFieldBuilder::for($content, 'desc_cn')
                    ->type('textarea')
                    ->label('中文描述')
                    ->placeholder('请输入内容的详细中文描述...')
                    ->rows(8)
                    ->helpText('内容的详细中文说明(支持Markdown格式)')
                    ->cssClass('')
                    ->render() ?>

                <?= FormFieldBuilder::for($content, 'desc_en')
                    ->type('textarea')
                    ->label('英文描述')
                    ->placeholder('Please enter the detailed English description of the content...')
                    ->rows(8)
                    ->helpText('内容的详细英文说明(支持Markdown格式)')
                    ->cssClass('')
                    ->render() ?>

                <?= FormFieldBuilder::for($content, 'sum_cn')
                    ->type('textarea')
                    ->label('中文总结')
                    ->placeholder('请输入内容的详细中文总结...')
                    ->rows(8)
                    ->helpText('内容的详细中文总结(支持Markdown格式)')
                    ->cssClass('')
                    ->render() ?>

                <?= FormFieldBuilder::for($content, 'sum_en')
                    ->type('textarea')
                    ->label('英文总结')
                    ->placeholder('Please enter the detailed English summary of the content...')
                    ->rows(8)
                    ->helpText('内容的详细英文总结(支持Markdown格式)')
                    ->cssClass('')
                    ->render() ?>
            </div>

            <!-- 状态设置 -->
            <div class="form-section">
                <h4 class="form-section-title">
                    <i class="bi bi-toggles form-section-icon"></i>
                    状态设置
                </h4>

                <div class="row">
                    <?php
                    $statusOptions = [];
                    foreach (ContentStatus::getAllValues() as $value => $label) {
                        $statusOptions[] = ['value' => $value, 'text' => $label];
                    }
                    ?>
                    <?= FormFieldBuilder::for($content, 'status_id')
                        ->type('custom-select')
                        ->label('发布状态')
                        ->options($statusOptions)
                        ->render() ?>
                    
                    <?= FormFieldBuilder::for($content, 'view_cnt')
                        ->type('number')
                        ->label('观看次数')
                        ->disabled()
                        ->render() ?>
                </div>
            </div>

            <?php if ($isCopyMode || !$content->isNew): ?>
            <!-- 统计信息 -->
            <div class="form-section">
                <h4 class="form-section-title">
                    <i class="bi bi-bar-chart form-section-icon"></i>
                    <?= $isCopyMode ? '源内容统计信息' : '统计信息' ?>
                </h4>

                <div class="info-box">
                    <i class="bi bi-info-circle info-icon"></i>
                    <div class="info-content">
                        <div class="info-title">数据统计</div>
                        <div class="info-text"><?= $isCopyMode ? '以下数据来自源内容的统计信息（仅供参考，新内容的统计将从零开始）' : '以下数据为系统自动统计,实时更新' ?></div>
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
                        <div class="stat-value"><?= count($selectedTagIds ?? []) ?></div>
                        <div class="stat-label">关联标签</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value"><?= count($selectedCollectionIds ?? []) ?></div>
                        <div class="stat-label">关联合集</div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- 时间信息 -->
            <div class="form-section">
                <h4 class="form-section-title">
                    <i class="bi bi-clock form-section-icon"></i>
                    时间信息
                </h4>

                <div class="row">
                    <?php if (!$content->isNew): ?>
                        <?= FormFieldBuilder::for($content, 'created_at')->label('创建时间')->disabled()->render() ?>
                        <?= FormFieldBuilder::for($content, 'updated_at')->label('最后更新时间')->disabled()->render() ?>
                    <?php endif; ?>
                    <?= FormFieldBuilder::for($content, 'pub_at')->label('发布时间')->render() ?>
                </div>
            </div>

            <!-- 表单操作按钮 -->
            <div class="form-actions">
                <a href="/content" class="btn btn-outline-secondary">
                    <i class="bi bi-x-lg"></i>
                    取消
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i>
                    <?php
                    if ($isCopyMode) {
                        echo '保存复制';
                    } elseif ($content->isNew) {
                        echo '创建内容';
                    } else {
                        echo '保存修改';
                    }
                    ?>
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