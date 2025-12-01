<?php
use App\Constants\VideoLinkStatus;
use App\Helpers\FormFieldBuilder;
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
                    <?php if (!$videoLink->isNew): ?>
                        <?= FormFieldBuilder::for($videoLink, 'id')
                            ->label('链接ID')
                            ->disabled()
                            ->formatter(fn($v) => '#' . str_pad($v, 3, '0', STR_PAD_LEFT))
                            ->helpText('系统自动生成,不可修改')
                            ->render() ?>
                    <?php endif;?>

                    <?php
                    $contentsOptions = [];
                    foreach ($contentsList as $content) {
                        $contentsOptions[] = [
                            'id' => $content['id'],
                            'text' => '#' . $content['id'] . ' - ' . $content['text']
                        ];
                    }
                    ?>
                    <?= FormFieldBuilder::for($videoLink, 'content_id')
                        ->type('custom-select')
                        ->label('关联内容')
                        ->options($contentsOptions)
                        ->placeholder('请选择关联内容')
                        ->render() ?>
                    
                    <?php
                    $platformsOptions = [];
                    foreach ($platformsList as $platform) {
                        $platformsOptions[] = [
                            'id' => $platform['id'],
                            'text' => $platform['text']
                        ];
                    }
                    ?>
                    <?= FormFieldBuilder::for($videoLink, 'platform_id')
                        ->type('custom-select')
                        ->label('视频平台')
                        ->options($platformsOptions)
                        ->placeholder('请选择视频平台')
                        ->render() ?>
                </div>

                <div class="row">
                    <?= FormFieldBuilder::for($videoLink, 'external_url')
                        ->label('第三方链接')
                        ->placeholder('https://example.com/video/123')
                        ->render() ?>
                    
                    <?= FormFieldBuilder::for($videoLink, 'external_video_id')
                        ->label('第三方视频ID')
                        -> placeholder('例如: BV1234567890')
                        ->render() ?>
                </div>
            </div>

            <!-- 统计数据 -->
            <div class="form-section">
                <h4 class="form-section-title">
                    <i class="bi bi-bar-chart form-section-icon"></i>
                    统计数据
                </h4>

                <div class="row">
                    <?= FormFieldBuilder::for($videoLink, 'play_cnt')->type('number')->label('播放数')->cssClass('col-md-4 pb-3')->render() ?>
                    <?= FormFieldBuilder::for($videoLink, 'like_cnt')->type('number')->label('点赞数')->cssClass('col-md-4 pb-3')->render() ?>
                    <?= FormFieldBuilder::for($videoLink, 'favorite_cnt')->type('number')->label('收藏数')->cssClass('col-md-4 pb-3')->render() ?>
                </div>

                <div class="row">
                    <?= FormFieldBuilder::for($videoLink, 'download_cnt')->type('number')->label('下载数')->cssClass('col-md-4 pb-3')->render() ?>
                    <?= FormFieldBuilder::for($videoLink, 'comment_cnt')->type('number')->label('评论数')->cssClass('col-md-4 pb-3')->render() ?>
                    <?= FormFieldBuilder::for($videoLink, 'share_cnt')->type('number')->label('分享数')->cssClass('col-md-4 pb-3')->render() ?>
                </div>
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
                    foreach (VideoLinkStatus::getAllValues() as $value => $label) {
                        $statusOptions[] = ['value' => $value, 'text' => $label];
                    }
                    ?>
                    <?= FormFieldBuilder::for($videoLink, 'status_id')
                        ->type('custom-select')
                        ->label('链接状态')
                        ->options($statusOptions)
                        ->render() ?>
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
                    <?= FormFieldBuilder::for($videoLink, 'created_at')->label('创建时间')->disabled()->render() ?>
                    <?= FormFieldBuilder::for($videoLink, 'updated_at')->label('最后更新时间')->disabled()->render() ?>
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
