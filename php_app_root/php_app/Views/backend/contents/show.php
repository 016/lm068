<?php
use App\Constants\ContentStatus;
use App\Constants\ContentType;
?>
<!-- Content Show Content -->
<main class="dashboard-content">
    <!-- Breadcrumb and Page Title -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-eye page-title-icon"></i>
                <div>
                    <h1 class="page-title">内容详情</h1>
                    <p class="page-subtitle">Content Details</p>
                </div>
            </div>
            <a href="/content" class="back-link">
                <i class="bi bi-arrow-left"></i>
                返回内容列表
            </a>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="/dashboard" class="breadcrumb-link">首页</a></li>
                <li class="breadcrumb-item"><a href="/content" class="breadcrumb-link">内容管理</a></li>
                <li class="breadcrumb-item active breadcrumb-active" aria-current="page">内容详情</li>
            </ol>
        </nav>
    </div>

    <!-- Content Details -->
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="form-container">
                <div class="form-header">
                    <i class="bi bi-camera-video form-icon"></i>
                    <h3>内容详细信息 - ID: #<?= str_pad($content->id, 3, '0', STR_PAD_LEFT) ?></h3>
                </div>
                
                <div class="form-body">
                    <!-- 基本信息 -->
                    <div class="form-section">
                        <h4 class="form-section-title">
                            <i class="bi bi-info-circle form-section-icon"></i>
                            基本信息
                        </h4>

                        <div class="row">
                            <div class="col-md-6 pb-3">
                                <div class="form-group">
                                    <label class="form-label">内容ID</label>
                                    <div class="form-control-plaintext">#<?= str_pad($content->id, 3, '0', STR_PAD_LEFT) ?></div>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">内容类型</label>
                                    <div class="form-control-plaintext">
                                        <?php 
                                        $type = ContentType::tryFrom($content->content_type_id);
                                        echo htmlspecialchars($type ? $type->label() : '未知类型');
                                        ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">作者</label>
                                    <div class="form-control-plaintext"><?= htmlspecialchars($content->author ?? 'DP') ?></div>
                                </div>
                            </div>
                            <div class="col-md-6 pb-3">
                                <?php if ($content->thumbnail): ?>
                                <div class="form-group">
                                    <label class="form-label">缩略图</label>
                                    <div class="thumbnail-preview-container">
                                        <img src="<?= htmlspecialchars($content->thumbnail) ?>" alt="内容缩略图" class="thumbnail-preview" style="max-width: 300px; height: auto;">
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 pb-3">
                                <div class="form-group">
                                    <label class="form-label">中文标题</label>
                                    <div class="form-control-plaintext"><?= htmlspecialchars($content->title_cn ?? '未设置') ?></div>
                                </div>
                            </div>
                            <div class="col-md-6 pb-3">
                                <div class="form-group">
                                    <label class="form-label">英文标题</label>
                                    <div class="form-control-plaintext"><?= htmlspecialchars($content->title_en ?? '未设置') ?></div>
                                </div>
                            </div>
                        </div>

                        <?php if ($content->duration): ?>
                        <div class="row">
                            <div class="col-md-6 pb-3">
                                <div class="form-group">
                                    <label class="form-label">内容时长</label>
                                    <div class="form-control-plaintext"><?= htmlspecialchars($content->duration) ?></div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- 简介内容 -->
                    <div class="form-section">
                        <h4 class="form-section-title">
                            <i class="bi bi-body-text form-section-icon"></i>
                            简介内容
                        </h4>

                        <div class="row">
                            <div class="col-md-6 pb-3">
                                <div class="form-group">
                                    <label class="form-label">中文简介</label>
                                    <div class="form-control-plaintext"><?= htmlspecialchars($content->short_desc_cn ?: '未设置') ?></div>
                                </div>
                            </div>
                            <div class="col-md-6 pb-3">
                                <div class="form-group">
                                    <label class="form-label">英文简介</label>
                                    <div class="form-control-plaintext"><?= htmlspecialchars($content->short_desc_en ?: '未设置') ?></div>
                                </div>
                            </div>
                        </div>

                        <?php if ($content->desc_cn): ?>
                        <div class="form-group">
                            <label class="form-label">中文描述</label>
                            <div class="form-control-plaintext" style="border: 1px solid #dee2e6; padding: 10px; border-radius: 4px; background-color: #f8f9fa;">
                                <?= nl2br(htmlspecialchars($content->desc_cn)) ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($content->desc_en): ?>
                        <div class="form-group">
                            <label class="form-label">英文描述</label>
                            <div class="form-control-plaintext" style="border: 1px solid #dee2e6; padding: 10px; border-radius: 4px; background-color: #f8f9fa;">
                                <?= nl2br(htmlspecialchars($content->desc_en)) ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- 关联信息 -->
                    <div class="form-section">
                        <h4 class="form-section-title">
                            <i class="bi bi-collection form-section-icon"></i>
                            关联信息
                        </h4>

                        <div class="row">
                            <div class="col-md-6 pb-3">
                                <div class="form-group">
                                    <label class="form-label">关联标签</label>
                                    <div class="form-control-plaintext">
                                        <?php if (!empty($relatedTags)): ?>
                                            <?php foreach ($relatedTags as $tag): ?>
                                                <span class="badge bg-primary me-1 mb-1"><?= htmlspecialchars($tag['name_cn'] ?: $tag['name_en']) ?></span>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <span class="text-muted">无关联标签</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 pb-3">
                                <div class="form-group">
                                    <label class="form-label">关联合集</label>
                                    <div class="form-control-plaintext">
                                        <?php if (!empty($relatedCollections)): ?>
                                            <?php foreach ($relatedCollections as $collection): ?>
                                                <span class="badge bg-success me-1 mb-1"><?= htmlspecialchars($collection['name_cn'] ?: $collection['name_en']) ?></span>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <span class="text-muted">无关联合集</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 状态和统计 -->
                    <div class="form-section">
                        <h4 class="form-section-title">
                            <i class="bi bi-bar-chart form-section-icon"></i>
                            状态和统计
                        </h4>
                        
                        <div class="row">
                            <div class="col-md-6 pb-3">
                                <div class="form-group">
                                    <label class="form-label">发布状态</label>
                                    <div class="form-control-plaintext">
                                        <?php 
                                        $status = ContentStatus::tryFrom($content->status_id);
                                        $statusClass = $status ? $status->bootstrapBadgeClass() : 'text-bg-secondary';
                                        ?>
                                        <span class="badge <?= $statusClass ?>">
                                            <?= htmlspecialchars($status ? $status->label() : '未知') ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 pb-3">
                                <div class="form-group">
                                    <label class="form-label">观看次数</label>
                                    <div class="form-control-plaintext"><?= number_format($content->view_cnt ?? 0) ?></div>
                                </div>
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
                                    <label class="form-label">创建时间</label>
                                    <div class="form-control-plaintext"><?= htmlspecialchars($content->created_at ?? '未知') ?></div>
                                </div>
                            </div>
                            <div class="col-md-6 pb-3">
                                <div class="form-group">
                                    <label class="form-label">最后更新时间</label>
                                    <div class="form-control-plaintext"><?= htmlspecialchars($content->updated_at ?? '未知') ?></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 操作按钮 -->
                    <div class="form-actions">
                        <a href="/content" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i>
                            返回列表
                        </a>
                        <a href="/contents/edit/<?= $content->id ?>" class="btn btn-primary">
                            <i class="bi bi-pencil"></i>
                            编辑内容
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

