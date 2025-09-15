<!-- Tag Edit Form Content -->
<main class="dashboard-content">
    <!-- Breadcrumb and Page Title -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-pencil-square page-title-icon"></i>
                <div>
                    <h1 class="page-title"><?= isset($tag) ? '编辑标签' : '创建标签' ?></h1>
                    <p class="page-subtitle"><?= isset($tag) ? 'Edit Tag Information' : 'Create New Tag' ?></p>
                </div>
            </div>
            <a href="/tags" class="back-link">
                <i class="bi bi-arrow-left"></i>
                返回标签列表
            </a>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="/dashboard" class="breadcrumb-link">首页</a></li>
                <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">内容管理</a></li>
                <li class="breadcrumb-item"><a href="/tags" class="breadcrumb-link">标签管理</a></li>
                <li class="breadcrumb-item active breadcrumb-active" aria-current="page"><?= isset($tag) ? '编辑标签' : '创建标签' ?></li>
            </ol>
        </nav>
    </div>

    <!-- Tag Edit Form -->
    <div class="row justify-content-center">
        <div class="col-12 col-lg-12">
            <div class="form-container">
                <div class="form-header">
                    <i class="bi bi-tag form-icon"></i>
                    <h3>标签详细信息</h3>
                </div>
                
                <div class="form-body">
                    <form id="tagEditForm" action="<?= isset($tag) ? '/tags/' . $tag['id'] . '/update' : '/tags/store' ?>" method="POST">
                        <!-- CSRF Token for security -->
                        <input type="hidden" name="_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                        <?php if (isset($tag)): ?>
                            <input type="hidden" name="id" id="id" value="<?= $tag['id'] ?>">
                        <?php endif; ?>
                        
                        <!-- 基本信息 -->
                        <div class="form-section">
                            <h4 class="form-section-title">
                                <i class="bi bi-info-circle form-section-icon"></i>
                                基本信息
                            </h4>
                            
                            <div class="row">
                                <?php if (isset($tag)): ?>
                                <div class="col-md-6 pb-3">
                                    <div class="form-group">
                                        <label for="tagId" class="form-label">标签ID</label>
                                        <input type="text" class="form-control" id="tagId" value="#<?= sprintf('%03d', $tag['id']) ?>" disabled>
                                        <div class="form-text">系统自动生成，不可修改</div>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <div class="col-md-6 pb-3">
                                    <div class="form-group">
                                        <label for="tagPreview" class="form-label">标签预览</label>
                                        <div class="tag-preview-container">
                                            <button type="button" id="tagPreviewBtn" class="btn btn-outline-primary">
                                                <i class="bi bi-star" id="previewIcon"></i>
                                                <span id="previewText"><?= isset($tag) ? htmlspecialchars($tag['name_cn']) : '标签预览' ?></span>
                                            </button>
                                        </div>
                                        <div class="form-text">实时预览标签显示效果</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 pb-3">
                                    <div class="form-group">
                                        <label for="name_cn" class="form-label required">中文标题</label>
                                        <input type="text" class="form-control" id="name_cn" name="name_cn" value="<?= isset($tag) ? htmlspecialchars($tag['name_cn']) : '' ?>" required>
                                        <div class="form-text">标签的中文显示名称</div>
                                    </div>
                                </div>
                                <div class="col-md-6 pb-3">
                                    <div class="form-group">
                                        <label for="name_en" class="form-label required">英文标题</label>
                                        <input type="text" class="form-control" id="name_en" name="name_en" value="<?= isset($tag) ? htmlspecialchars($tag['name_en']) : '' ?>" required>
                                        <div class="form-text">标签的英文显示名称</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 pb-3">
                                    <div class="form-group">
                                        <label for="color_class" class="form-label">标签颜色</label>
                                        <select class="form-control" id="color_class" name="color_class">
                                            <option value="btn-outline-primary" <?= (!isset($tag) || $tag['color_class'] == 'btn-outline-primary') ? 'selected' : '' ?>>Primary (蓝色)</option>
                                            <option value="btn-outline-secondary" <?= (isset($tag) && $tag['color_class'] == 'btn-outline-secondary') ? 'selected' : '' ?>>Secondary (灰色)</option>
                                            <option value="btn-outline-success" <?= (isset($tag) && $tag['color_class'] == 'btn-outline-success') ? 'selected' : '' ?>>Success (绿色)</option>
                                            <option value="btn-outline-danger" <?= (isset($tag) && $tag['color_class'] == 'btn-outline-danger') ? 'selected' : '' ?>>Danger (红色)</option>
                                            <option value="btn-outline-warning" <?= (isset($tag) && $tag['color_class'] == 'btn-outline-warning') ? 'selected' : '' ?>>Warning (黄色)</option>
                                            <option value="btn-outline-info" <?= (isset($tag) && $tag['color_class'] == 'btn-outline-info') ? 'selected' : '' ?>>Info (青色)</option>
                                            <option value="btn-outline-light" <?= (isset($tag) && $tag['color_class'] == 'btn-outline-light') ? 'selected' : '' ?>>Light (浅色)</option>
                                            <option value="btn-outline-dark" <?= (isset($tag) && $tag['color_class'] == 'btn-outline-dark') ? 'selected' : '' ?>>Dark (深色)</option>
                                        </select>
                                        <div class="form-text">选择标签在前端显示时的Bootstrap颜色样式</div>
                                    </div>
                                </div>
                                <div class="col-md-6 pb-3">
                                    <div class="form-group">
                                        <label for="icon_class" class="form-label">图标样式</label>
                                        <input type="text" class="form-control" id="icon_class" name="icon_class" value="<?= isset($tag) ? htmlspecialchars($tag['icon_class']) : 'bi-star' ?>" placeholder="请输入 Bootstrap 图标类名，如 bi-star">
                                        <div class="form-text">直接输入Bootstrap icon 类名（如 bi-star, bi-heart...）</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 pb-3">
                                    <div class="form-group">
                                        <label for="related_videos" class="form-label">关联视频</label>
                                        <div id="videoMultiSelect" class="multi-select-container"></div>
                                        <div class="form-text">选择要关联到此标签的视频，可多选</div>
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
                                        <input type="text" class="form-control" id="short_desc_cn" name="short_desc_cn" value="<?= isset($tag) ? htmlspecialchars($tag['short_desc_cn']) : '' ?>" maxlength="100">
                                        <div class="form-text">标签的简短中文描述（最多100字符）</div>
                                    </div>
                                </div>
                                <div class="col-md-6 pb-3">
                                    <div class="form-group">
                                        <label for="short_desc_en" class="form-label">英文简介</label>
                                        <input type="text" class="form-control" id="short_desc_en" name="short_desc_en" value="<?= isset($tag) ? htmlspecialchars($tag['short_desc_en']) : '' ?>" maxlength="100">
                                        <div class="form-text">标签的简短英文描述（最多100字符）</div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="desc_cn" class="form-label">中文描述</label>
                                <textarea class="form-control" id="desc_cn" name="desc_cn" rows="3" placeholder="请输入标签的详细中文描述..." maxlength="500"><?= isset($tag) ? htmlspecialchars($tag['desc_cn']) : '' ?></textarea>
                                <div class="form-text">标签的详细中文说明（最多500字符）</div>
                            </div>

                            <div class="form-group">
                                <label for="desc_en" class="form-label">英文描述</label>
                                <textarea class="form-control" id="desc_en" name="desc_en" rows="3" placeholder="Please enter the detailed English description of the tag..." maxlength="500"><?= isset($tag) ? htmlspecialchars($tag['desc_en']) : '' ?></textarea>
                                <div class="form-text">标签的详细英文说明（最多500字符）</div>
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
                                            <div class="custom-switch tag-edit-switch" id="statusSwitch">
                                                <input type="checkbox" id="status_id" name="status_id" value="1" <?= (!isset($tag) || $tag['status_id'] == 1) ? 'checked' : '' ?>>
                                                <span class="switch-slider"></span>
                                            </div>
                                            <label for="status_id" class="switch-label">显示状态</label>
                                        </div>
                                        <div class="form-text">开启后标签在前端可见，关闭后隐藏</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if (isset($tag)): ?>
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
                                    <div class="stat-value"><?= $tag['content_cnt'] ?></div>
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
                                        <input type="text" class="form-control" id="created_at" name="created_at" value="<?= $tag['created_at'] ?>" disabled>
                                    </div>
                                </div>
                                <div class="col-md-6 pb-3">
                                    <div class="form-group">
                                        <label for="updated_at" class="form-label">最后更新时间</label>
                                        <input type="text" class="form-control" id="updated_at" name="updated_at" value="<?= $tag['updated_at'] ?>" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- 表单操作按钮 -->
                        <div class="form-actions">
                            <button type="button" id="btn-cancel" class="btn btn-outline-secondary" onclick="window.location.href='/tags'">
                                <i class="bi bi-x-lg"></i>
                                取消
                            </button>
                            <button type="button" class="btn btn-outline-primary" onclick="previewTag()">
                                <i class="bi bi-eye"></i>
                                预览
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg"></i>
                                <?= isset($tag) ? '保存修改' : '创建标签' ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 实时预览功能
    updatePreview();
    
    // 监听表单字段变化
    document.getElementById('name_cn').addEventListener('input', updatePreview);
    document.getElementById('color_class').addEventListener('change', updatePreview);
    document.getElementById('icon_class').addEventListener('input', updatePreview);
    
    // 表单提交处理
    document.getElementById('tagEditForm').addEventListener('submit', handleFormSubmit);
    
    // 初始化多选下拉菜单
    initializeVideoMultiSelect();
});

function updatePreview() {
    const nameInput = document.getElementById('name_cn');
    const colorSelect = document.getElementById('color_class');
    const iconInput = document.getElementById('icon_class');
    const previewBtn = document.getElementById('tagPreviewBtn');
    const previewText = document.getElementById('previewText');
    const previewIcon = document.getElementById('previewIcon');
    
    // 更新文本
    previewText.textContent = nameInput.value || '标签预览';
    
    // 更新颜色
    previewBtn.className = 'btn ' + (colorSelect.value || 'btn-outline-primary');
    
    // 更新图标
    const iconClass = iconInput.value || 'bi-star';
    previewIcon.className = 'bi ' + iconClass;
}

function handleFormSubmit(e) {
    e.preventDefault();
    
    const form = e.target;
    const formData = new FormData(form);
    
    // 处理开关状态
    const statusCheckbox = document.getElementById('status_id');
    if (!statusCheckbox.checked) {
        formData.set('status_id', '0');
    }
    
    // 获取选中的视频
    const selectedVideos = getSelectedVideos();
    formData.set('related_videos', JSON.stringify(selectedVideos));
    
    fetch(form.action, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message || '操作成功');
            window.location.href = '/tags';
        } else {
            alert(data.message || '操作失败');
            if (data.errors) {
                console.error('Validation errors:', data.errors);
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('提交失败，请重试');
    });
}

function initializeVideoMultiSelect() {
    // 这里应该初始化多选下拉菜单
    // 由于需要后端数据，这里使用一个简单的实现
    const container = document.getElementById('videoMultiSelect');
    if (container) {
        container.innerHTML = '<div class="form-text">视频选择器正在加载...</div>';
        
        // 加载可用视频列表
        loadAvailableVideos();
    }
}

function loadAvailableVideos() {
    // 这里应该从后端加载视频列表
    // 暂时使用模拟数据
    const container = document.getElementById('videoMultiSelect');
    container.innerHTML = `
        <select class="form-control" multiple size="5" name="related_videos[]">
            <option value="">暂无可用视频</option>
        </select>
        <div class="form-text">多选视频，按住Ctrl键可选择多个</div>
    `;
}

function getSelectedVideos() {
    const select = document.querySelector('select[name="related_videos[]"]');
    if (!select) return [];
    
    return Array.from(select.selectedOptions).map(option => option.value).filter(val => val);
}

function previewTag() {
    // 预览标签效果
    const formData = new FormData(document.getElementById('tagEditForm'));
    
    // 显示预览窗口或跳转到预览页面
    alert('预览功能开发中...');
}
</script>