<?php $content = ob_start(); ?>

<!-- Collection Edit Form Content -->
<main class="dashboard-content">
    <!-- Breadcrumb and Page Title -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-pencil-square page-title-icon"></i>
                <div>
                    <h1 class="page-title"><?= $collection ? '编辑合集' : '创建合集' ?></h1>
                    <p class="page-subtitle"><?= $collection ? 'Edit Collection Information' : 'Create New Collection' ?></p>
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
                <li class="breadcrumb-item active breadcrumb-active" aria-current="page"><?= $collection ? '编辑合集' : '创建合集' ?></li>
            </ol>
        </nav>
    </div>

    <!-- Collection Edit Form -->
    <div class="row justify-content-center">
        <div class="col-12 col-lg-12">
            <div class="form-container">
                <div class="form-header">
                    <i class="bi bi-collection form-icon"></i>
                    <h3>合集详细信息</h3>
                </div>
                
                <div class="form-body">
                    <form id="collectionEditForm" action="<?= $collection ? "/collections/{$collection['id']}" : '/collections' ?>" method="POST">
                        <?php if ($collection): ?>
                            <input type="hidden" name="_method" value="PUT">
                            <input type="hidden" name="id" id="id" value="<?= $collection['id'] ?>">
                        <?php endif; ?>
                        
                        <!-- 基本信息 -->
                        <div class="form-section">
                            <h4 class="form-section-title">
                                <i class="bi bi-info-circle form-section-icon"></i>
                                基本信息
                            </h4>
                            
                            <div class="row">
                                <?php if ($collection): ?>
                                <div class="col-md-6 pb-3">
                                    <div class="form-group">
                                        <label for="collectionId" class="form-label">合集ID</label>
                                        <input type="text" class="form-control" id="collectionId" value="#<?= str_pad($collection['id'], 3, '0', STR_PAD_LEFT) ?>" disabled>
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
                                                <span id="previewText"><?= $collection ? htmlspecialchars($collection['name_cn']) : '新合集' ?></span>
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
                                        <input type="text" class="form-control" id="name_cn" name="name_cn" value="<?= $collection ? htmlspecialchars($collection['name_cn']) : '' ?>" required>
                                        <div class="form-text">合集的中文显示名称</div>
                                    </div>
                                </div>
                                <div class="col-md-6 pb-3">
                                    <div class="form-group">
                                        <label for="name_en" class="form-label required">英文标题</label>
                                        <input type="text" class="form-control" id="name_en" name="name_en" value="<?= $collection ? htmlspecialchars($collection['name_en']) : '' ?>" required>
                                        <div class="form-text">合集的英文显示名称</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 pb-3">
                                    <div class="form-group">
                                        <label for="color_class" class="form-label">合集颜色</label>
                                        <select class="form-control" id="color_class" name="color_class">
                                            <option value="btn-outline-primary" <?= ($collection && $collection['color_class'] === 'btn-outline-primary') || !$collection ? 'selected' : '' ?>>Primary (蓝色)</option>
                                            <option value="btn-outline-secondary" <?= $collection && $collection['color_class'] === 'btn-outline-secondary' ? 'selected' : '' ?>>Secondary (灰色)</option>
                                            <option value="btn-outline-success" <?= $collection && $collection['color_class'] === 'btn-outline-success' ? 'selected' : '' ?>>Success (绿色)</option>
                                            <option value="btn-outline-danger" <?= $collection && $collection['color_class'] === 'btn-outline-danger' ? 'selected' : '' ?>>Danger (红色)</option>
                                            <option value="btn-outline-warning" <?= $collection && $collection['color_class'] === 'btn-outline-warning' ? 'selected' : '' ?>>Warning (黄色)</option>
                                            <option value="btn-outline-info" <?= $collection && $collection['color_class'] === 'btn-outline-info' ? 'selected' : '' ?>>Info (青色)</option>
                                            <option value="btn-outline-light" <?= $collection && $collection['color_class'] === 'btn-outline-light' ? 'selected' : '' ?>>Light (浅色)</option>
                                            <option value="btn-outline-dark" <?= $collection && $collection['color_class'] === 'btn-outline-dark' ? 'selected' : '' ?>>Dark (深色)</option>
                                        </select>
                                        <div class="form-text">选择合集在前端显示时的Bootstrap颜色样式</div>
                                    </div>
                                </div>
                                <div class="col-md-6 pb-3">
                                    <div class="form-group">
                                        <label for="icon_class" class="form-label">图标样式</label>
                                        <input type="text" class="form-control" id="icon_class" name="icon_class" value="<?= $collection ? htmlspecialchars($collection['icon_class']) : 'bi-collection' ?>" placeholder="请输入 Bootstrap 图标类名，如 bi-collection">
                                        <div class="form-text">直接输入Bootstrap icon 类名（如 bi-collection, bi-star...）</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 pb-3">
                                    <div class="form-group">
                                        <label for="related_videos" class="form-label">关联视频</label>
                                        <div id="videoMultiSelect" class="multi-select-container"></div>
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
                                        <input type="text" class="form-control" id="short_desc_cn" name="short_desc_cn" value="<?= $collection ? htmlspecialchars($collection['short_desc_cn']) : '' ?>" maxlength="100">
                                        <div class="form-text">合集的简短中文描述（最多100字符）</div>
                                    </div>
                                </div>
                                <div class="col-md-6 pb-3">
                                    <div class="form-group">
                                        <label for="short_desc_en" class="form-label">英文简介</label>
                                        <input type="text" class="form-control" id="short_desc_en" name="short_desc_en" value="<?= $collection ? htmlspecialchars($collection['short_desc_en']) : '' ?>" maxlength="100">
                                        <div class="form-text">合集的简短英文描述（最多100字符）</div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="desc_cn" class="form-label">中文描述</label>
                                <textarea class="form-control" id="desc_cn" name="desc_cn" rows="3" placeholder="请输入合集的详细中文描述..." maxlength="500"><?= $collection ? htmlspecialchars($collection['desc_cn']) : '' ?></textarea>
                                <div class="form-text">合集的详细中文说明（最多500字符）</div>
                            </div>

                            <div class="form-group">
                                <label for="desc_en" class="form-label">英文描述</label>
                                <textarea class="form-control" id="desc_en" name="desc_en" rows="3" placeholder="Please enter the detailed English description of the collection..." maxlength="500"><?= $collection ? htmlspecialchars($collection['desc_en']) : '' ?></textarea>
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
                                                <input type="checkbox" id="status_id" name="status_id" value="1" <?= !$collection || $collection['status_id'] ? 'checked' : '' ?>>
                                                <span class="switch-slider"></span>
                                            </div>
                                            <label for="status_id" class="switch-label">显示状态</label>
                                        </div>
                                        <div class="form-text">开启后合集在前端可见，关闭后隐藏</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if ($collection): ?>
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
                                    <div class="stat-value"><?= $collection['content_cnt'] ?></div>
                                    <div class="stat-label">关联视频数量</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value">-</div>
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
                                <div class="col-md-6 pb-3">
                                    <div class="form-group">
                                        <label for="created_at" class="form-label">创建时间</label>
                                        <input type="text" class="form-control" id="created_at" name="created_at" value="<?= $collection['created_at'] ?>" disabled>
                                    </div>
                                </div>
                                <div class="col-md-6 pb-3">
                                    <div class="form-group">
                                        <label for="updated_at" class="form-label">最后更新时间</label>
                                        <input type="text" class="form-control" id="updated_at" name="updated_at" value="<?= $collection['updated_at'] ?>" disabled>
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
                                <?= $collection ? '保存修改' : '创建合集' ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
// Multi-select initialization
document.addEventListener('DOMContentLoaded', function() {
    const contentOptions = <?= json_encode($contentOptions) ?>;
    
    if (typeof initMultiSelect === 'function') {
        initMultiSelect('videoMultiSelect', {
            options: contentOptions,
            name: 'related_videos[]',
            placeholder: '选择关联视频...',
            searchPlaceholder: '搜索视频标题...'
        });
    }
    
    // Preview update
    const nameInput = document.getElementById('name_cn');
    const iconInput = document.getElementById('icon_class');
    const colorSelect = document.getElementById('color_class');
    const previewBtn = document.getElementById('collectionPreviewBtn');
    const previewIcon = document.getElementById('previewIcon');
    const previewText = document.getElementById('previewText');
    
    function updatePreview() {
        const name = nameInput.value || '新合集';
        const iconClass = iconInput.value || 'bi-collection';
        const colorClass = colorSelect.value || 'btn-outline-primary';
        
        previewText.textContent = name;
        previewIcon.className = `bi ${iconClass}`;
        previewBtn.className = `btn ${colorClass}`;
    }
    
    if (nameInput) nameInput.addEventListener('input', updatePreview);
    if (iconInput) iconInput.addEventListener('input', updatePreview);
    if (colorSelect) colorSelect.addEventListener('change', updatePreview);
    
    updatePreview();
});

// Form submission
document.getElementById('collectionEditForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const url = this.getAttribute('action');
    const method = <?= $collection ? '"PUT"' : '"POST"' ?>;
    
    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.location.href = '/collections';
        } else {
            alert('操作失败: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('操作失败');
    });
});
</script>

<?php $content = ob_get_clean(); ?>

<?php 
// 渲染布局
include __DIR__ . '/../layouts/main.php'; 
?>