<!-- Collection Show Content -->
<main class="dashboard-content">
    <!-- Breadcrumb and Page Title -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-eye page-title-icon"></i>
                <div>
                    <h1 class="page-title">查看合集详情</h1>
                    <p class="page-subtitle">Collection Details</p>
                </div>
            </div>
            <a href="/collections" class="back-link">
                <i class="bi bi-arrow-left"></i>
                返回合集列表
            </a>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="/dashboard" class="breadcrumb-link">首页</a></li>
                <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">内容管理</a></li>
                <li class="breadcrumb-item"><a href="/collections" class="breadcrumb-link">合集管理</a></li>
                <li class="breadcrumb-item active breadcrumb-active" aria-current="page">查看合集</li>
            </ol>
        </nav>
    </div>

    <!-- Collection Information -->
    <div class="row">
        <div class="col-12">
            <div class="form-container">
                <div class="form-header">
                    <i class="bi bi-collection form-icon"></i>
                    <h3><?= htmlspecialchars($collection['name_cn']) ?></h3>
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
                                    <label class="form-label">合集ID</label>
                                    <div class="form-control-plaintext">#<?= str_pad($collection['id'], 3, '0', STR_PAD_LEFT) ?></div>
                                </div>
                            </div>
                            <div class="col-md-6 pb-3">
                                <div class="form-group">
                                    <label class="form-label">状态</label>
                                    <div class="form-control-plaintext">
                                        <span class="badge rounded-pill <?= $collection['status_id'] ? 'badge-success' : 'badge-danger' ?>">
                                            <i class="bi bi-circle-fill badge-icon"></i>
                                            <?= $collection['status_id'] ? '显示' : '隐藏' ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 pb-3">
                                <div class="form-group">
                                    <label class="form-label">中文标题</label>
                                    <div class="form-control-plaintext"><?= htmlspecialchars($collection['name_cn']) ?></div>
                                </div>
                            </div>
                            <div class="col-md-6 pb-3">
                                <div class="form-group">
                                    <label class="form-label">英文标题</label>
                                    <div class="form-control-plaintext"><?= htmlspecialchars($collection['name_en']) ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 pb-3">
                                <div class="form-group">
                                    <label class="form-label">图标样式</label>
                                    <div class="form-control-plaintext">
                                        <span class="bi <?= htmlspecialchars($collection['icon_class'] ?: 'bi-collection') ?>"></span>
                                        <?= htmlspecialchars($collection['icon_class'] ?: 'bi-collection') ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 pb-3">
                                <div class="form-group">
                                    <label class="form-label">颜色样式</label>
                                    <div class="form-control-plaintext">
                                        <button class="btn <?= htmlspecialchars($collection['color_class'] ?: 'btn-outline-primary') ?> btn-sm" disabled>
                                            预览样式
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 描述信息 -->
                    <div class="form-section">
                        <h4 class="form-section-title">
                            <i class="bi bi-body-text form-section-icon"></i>
                            描述信息
                        </h4>

                        <div class="row">
                            <div class="col-md-6 pb-3">
                                <div class="form-group">
                                    <label class="form-label">中文简介</label>
                                    <div class="form-control-plaintext"><?= htmlspecialchars($collection['short_desc_cn'] ?: '暂无') ?></div>
                                </div>
                            </div>
                            <div class="col-md-6 pb-3">
                                <div class="form-group">
                                    <label class="form-label">英文简介</label>
                                    <div class="form-control-plaintext"><?= htmlspecialchars($collection['short_desc_en'] ?: '暂无') ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">中文描述</label>
                            <div class="form-control-plaintext" style="min-height: 80px; white-space: pre-wrap;"><?= htmlspecialchars($collection['desc_cn'] ?: '暂无详细描述') ?></div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">英文描述</label>
                            <div class="form-control-plaintext" style="min-height: 80px; white-space: pre-wrap;"><?= htmlspecialchars($collection['desc_en'] ?: '暂无详细描述') ?></div>
                        </div>
                    </div>

                    <!-- 关联内容 -->
                    <div class="form-section">
                        <h4 class="form-section-title">
                            <i class="bi bi-link-45deg form-section-icon"></i>
                            关联内容 (<?= count($relatedContent) ?> 个)
                        </h4>
                        
                        <?php if (!empty($relatedContent)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>标题</th>
                                        <th>类型</th>
                                        <th>状态</th>
                                        <th>观看次数</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($relatedContent as $content): ?>
                                    <tr>
                                        <td><?= $content['id'] ?></td>
                                        <td><?= htmlspecialchars($content['title_cn'] ?: $content['title_en']) ?></td>
                                        <td>
                                            <?php
                                            $typeMap = [
                                                1 => '网站公告',
                                                11 => '一般文章', 
                                                21 => '视频'
                                            ];
                                            echo $typeMap[$content['content_type_id']] ?? '未知';
                                            ?>
                                        </td>
                                        <td>
                                            <span class="badge rounded-pill <?= $content['status_id'] >= 99 ? 'badge-success' : 'badge-warning' ?>">
                                                <?= $content['status_id'] >= 99 ? '已发布' : '草稿' ?>
                                            </span>
                                        </td>
                                        <td><?= number_format($content['view_cnt']) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            暂无关联内容
                        </div>
                        <?php endif; ?>
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
                                    <div class="form-control-plaintext"><?= $collection['created_at'] ?></div>
                                </div>
                            </div>
                            <div class="col-md-6 pb-3">
                                <div class="form-group">
                                    <label class="form-label">最后更新时间</label>
                                    <div class="form-control-plaintext"><?= $collection['updated_at'] ?></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 操作按钮 -->
                    <div class="form-actions">
                        <a href="/collections" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i>
                            返回列表
                        </a>
                        <a href="/collections/<?= $collection['id'] ?>/edit" class="btn btn-primary">
                            <i class="bi bi-pencil"></i>
                            编辑合集
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>