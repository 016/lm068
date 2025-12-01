<?php
use App\Constants\TagStatus;
use App\Helpers\FormFieldBuilder;
?>
<!-- Shared Tag Form Content -->
<div class="form-container">
    <div class="form-header">
        <i class="bi bi-tag form-icon"></i>
        <h3>标签详细信息</h3>
    </div>
    
    <div class="form-body">
        <?php if (!empty($tag->errors)): ?>
        <div class="alert alert-danger mb-4">
            <h6 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> 表单验证失败</h6>
            <ul class="mb-0">
                <?php foreach ($tag->errors as $field => $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <form id="tagEditForm" action="<?= $formAction ?>" method="POST">
            <?php if (!$tag->isNew): ?>
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" name="id" id="id" value="<?= htmlspecialchars($tag->id) ?>">
            <?php endif; ?>
            
            <!-- 基本信息 -->
            <div class="form-section">
                <h4 class="form-section-title">
                    <i class="bi bi-info-circle form-section-icon"></i>
                    基本信息
                </h4>
                
                <div class="row">
                    <?php if (!$tag->isNew): ?>
                        <?= FormFieldBuilder::for($tag, 'id')
                            ->label('标签ID')
                            ->disabled()
                            ->formatter(fn($v) => '#' . str_pad($v, 3, '0', STR_PAD_LEFT))
                            ->helpText('系统自动生成，不可修改')
                            ->render() ?>
                    <?php endif; ?>
                    
                    <?= FormFieldBuilder::for($tag, 'preview')
                        ->type('preview')
                        ->label('标签预览')
                        ->helpText('实时预览标签显示效果')
                        ->render() ?>
                </div>

                <div class="row">
                    <?= FormFieldBuilder::for($tag, 'name_cn')->label('中文标题')->render() ?>
                    <?= FormFieldBuilder::for($tag, 'name_en')->label('英文标题')->render() ?>
                </div>

                <div class="row">
                    <?= FormFieldBuilder::for($tag, 'color_class')
                        ->type('select')
                        ->label('标签颜色')
                        ->options([
                            'btn-outline-primary' => 'Primary (蓝色)',
                            'btn-outline-secondary' => 'Secondary (灰色)',
                            'btn-outline-success' => 'Success (绿色)',
                            'btn-outline-danger' => 'Danger (红色)',
                            'btn-outline-warning' => 'Warning (黄色)',
                            'btn-outline-info' => 'Info (青色)',
                            'btn-outline-light' => 'Light (浅色)',
                            'btn-outline-dark' => 'Dark (深色)',
                        ])
                        ->render() ?>
                    
                    <?= FormFieldBuilder::for($tag, 'icon_class')
                        ->label('图标样式')
                        ->placeholder('请输入 Bootstrap 图标类名，如 bi-star')
                        ->render() ?>
                </div>

                <div class="row">
                    <?= FormFieldBuilder::for($tag, 'content_ids')
                        ->type('multi-select')
                        ->label('关联视频')
                        ->containerId('contentMultiSelect')
                        ->cssClass('col-md-12 pb-3')
                        ->render() ?>
                </div>
            </div>

            <!-- 简介设置 -->
            <div class="form-section">
                <h4 class="form-section-title">
                    <i class="bi bi-body-text form-section-icon"></i>
                    简介设置
                </h4>

                <div class="row">
                    <?= FormFieldBuilder::for($tag, 'short_desc_cn')->label('中文简介')->render() ?>
                    <?= FormFieldBuilder::for($tag, 'short_desc_en')->label('英文简介')->render() ?>
                </div>

                <?= FormFieldBuilder::for($tag, 'desc_cn')
                    ->type('textarea')
                    ->label('中文描述')
                    ->placeholder('请输入标签的详细中文描述...')
                    ->rows(3)
                    ->cssClass('')
                    ->render() ?>

                <?= FormFieldBuilder::for($tag, 'desc_en')
                    ->type('textarea')
                    ->label('英文描述')
                    ->placeholder('Please enter the detailed English description of the tag...')
                    ->rows(3)
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
                    <?= FormFieldBuilder::for($tag, 'status_id')
                        ->type('switch')
                        ->label('显示状态')
                        ->value(TagStatus::ENABLED->value)
                        ->render() ?>
                </div>
            </div>

            <?php if (!$tag->isNew): ?>
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
                        <div class="stat-value"><?= number_format($tag->content_cnt ?? 0) ?></div>
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
                        <div class="stat-value">-</div>
                        <div class="stat-label">总点赞数</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">-</div>
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
                    <?= FormFieldBuilder::for($tag, 'created_at')->label('创建时间')->disabled()->render() ?>
                    <?= FormFieldBuilder::for($tag, 'updated_at')->label('最后更新时间')->disabled()->render() ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- 表单操作按钮 -->
            <div class="form-actions">
                <a href="/tags" class="btn btn-outline-secondary">
                    <i class="bi bi-x-lg"></i>
                    取消
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i>
                    <?= !$tag->isNew ? '保存修改' : '创建标签' ?>
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