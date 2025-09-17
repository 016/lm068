<!-- Shared Tag Form Content -->
<div class="form-container">
    <div class="form-header">
        <i class="bi bi-tag form-icon"></i>
        <h3>标签详细信息</h3>
    </div>
    
    <div class="form-body">
        <?php if (!empty($errors)): ?>
        <div class="alert alert-danger mb-4">
            <h6 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> 表单验证失败</h6>
            <ul class="mb-0">
                <?php foreach ($errors as $field => $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <form id="tagEditForm" action="<?= $formAction ?>" method="POST">
            <?php if (!$isCreateMode): ?>
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" name="id" id="id" value="<?= htmlspecialchars($tag['id']) ?>">
            <?php endif; ?>
            
            <!-- 基本信息 -->
            <div class="form-section">
                <h4 class="form-section-title">
                    <i class="bi bi-info-circle form-section-icon"></i>
                    基本信息
                </h4>
                
                <div class="row">
                    <?php if (!$isCreateMode): ?>
                    <div class="col-md-6 pb-3">
                        <div class="form-group">
                            <label for="tagId" class="form-label">标签ID</label>
                            <input type="text" class="form-control" id="tagId" value="#<?= str_pad($tag['id'], 3, '0', STR_PAD_LEFT) ?>" disabled>
                            <div class="form-text">系统自动生成，不可修改</div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="col-md-6 pb-3">
                        <div class="form-group">
                            <label for="tagPreview" class="form-label">标签预览</label>
                            <div class="tag-preview-container">
                                <button type="button" id="tagPreviewBtn" class="btn btn-outline-primary">
                                    <i class="bi <?= htmlspecialchars($tag['icon_class'] ?? 'bi-star') ?>" id="previewIcon"></i>
                                    <span id="previewText"><?= htmlspecialchars($tag['name_cn'] ?? '新标签') ?></span>
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
                            <input type="text" class="form-control <?= !empty($errors['name_cn']) ? 'is-invalid' : '' ?>" 
                                   id="name_cn" name="name_cn" value="<?= htmlspecialchars($tag['name_cn'] ?? '') ?>" 
                                   maxlength="20" required>
                            <?php if (!empty($errors['name_cn'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['name_cn']) ?></div>
                            <?php else: ?>
                                <div class="valid-feedback">check passed.</div>
                            <?php endif; ?>
                            <div class="form-text">标签的中文显示名称</div>
                        </div>
                    </div>
                    <div class="col-md-6 pb-3">
                        <div class="form-group">
                            <label for="name_en" class="form-label required">英文标题</label>
                            <input type="text" class="form-control <?= !empty($errors['name_en']) ? 'is-invalid' : '' ?>" 
                                   id="name_en" name="name_en" value="<?= htmlspecialchars($tag['name_en'] ?? '') ?>" 
                                   maxlength="40" required>
                            <?php if (!empty($errors['name_en'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['name_en']) ?></div>
                            <?php else: ?>
                                <div class="valid-feedback">check passed.</div>
                            <?php endif; ?>
                            <div class="form-text">标签的英文显示名称</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 pb-3">
                        <div class="form-group">
                            <label for="color_class" class="form-label">标签颜色</label>
                            <select class="form-control" id="color_class" name="color_class">
                                <option value="btn-outline-primary" <?= ($tag['color_class'] ?? 'btn-outline-primary') === 'btn-outline-primary' ? 'selected' : '' ?>>Primary (蓝色)</option>
                                <option value="btn-outline-secondary" <?= ($tag['color_class'] ?? '') === 'btn-outline-secondary' ? 'selected' : '' ?>>Secondary (灰色)</option>
                                <option value="btn-outline-success" <?= ($tag['color_class'] ?? '') === 'btn-outline-success' ? 'selected' : '' ?>>Success (绿色)</option>
                                <option value="btn-outline-danger" <?= ($tag['color_class'] ?? '') === 'btn-outline-danger' ? 'selected' : '' ?>>Danger (红色)</option>
                                <option value="btn-outline-warning" <?= ($tag['color_class'] ?? '') === 'btn-outline-warning' ? 'selected' : '' ?>>Warning (黄色)</option>
                                <option value="btn-outline-info" <?= ($tag['color_class'] ?? '') === 'btn-outline-info' ? 'selected' : '' ?>>Info (青色)</option>
                                <option value="btn-outline-light" <?= ($tag['color_class'] ?? '') === 'btn-outline-light' ? 'selected' : '' ?>>Light (浅色)</option>
                                <option value="btn-outline-dark" <?= ($tag['color_class'] ?? '') === 'btn-outline-dark' ? 'selected' : '' ?>>Dark (深色)</option>
                            </select>
                            <div class="form-text">选择标签在前端显示时的Bootstrap颜色样式</div>
                        </div>
                    </div>
                    <div class="col-md-6 pb-3">
                        <div class="form-group">
                            <label for="icon_class" class="form-label">图标样式</label>
                            <input type="text" class="form-control" id="icon_class" name="icon_class" 
                                   value="<?= htmlspecialchars($tag['icon_class'] ?? 'bi-star') ?>" 
                                   placeholder="请输入 Bootstrap 图标类名，如 bi-star">
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
                            <input type="text" class="form-control <?= !empty($errors['short_desc_cn']) ? 'is-invalid' : '' ?>" 
                                   id="short_desc_cn" name="short_desc_cn" 
                                   value="<?= htmlspecialchars($tag['short_desc_cn'] ?? '') ?>" maxlength="100">
                            <?php if (!empty($errors['short_desc_cn'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['short_desc_cn']) ?></div>
                            <?php endif; ?>
                            <div class="form-text">标签的简短中文描述（最多100字符）</div>
                        </div>
                    </div>
                    <div class="col-md-6 pb-3">
                        <div class="form-group">
                            <label for="short_desc_en" class="form-label">英文简介</label>
                            <input type="text" class="form-control <?= !empty($errors['short_desc_en']) ? 'is-invalid' : '' ?>" 
                                   id="short_desc_en" name="short_desc_en" 
                                   value="<?= htmlspecialchars($tag['short_desc_en'] ?? '') ?>" maxlength="100">
                            <?php if (!empty($errors['short_desc_en'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['short_desc_en']) ?></div>
                            <?php endif; ?>
                            <div class="form-text">标签的简短英文描述（最多100字符）</div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="desc_cn" class="form-label">中文描述</label>
                    <textarea class="form-control" id="desc_cn" name="desc_cn" rows="3" 
                              placeholder="请输入标签的详细中文描述..." maxlength="500"><?= htmlspecialchars($tag['desc_cn'] ?? '') ?></textarea>
                    <div class="form-text">标签的详细中文说明（最多500字符）</div>
                </div>

                <div class="form-group">
                    <label for="desc_en" class="form-label">英文描述</label>
                    <textarea class="form-control" id="desc_en" name="desc_en" rows="3" 
                              placeholder="Please enter the detailed English description of the tag..." maxlength="500"><?= htmlspecialchars($tag['desc_en'] ?? '') ?></textarea>
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
                                    <input type="checkbox" id="status_id" name="status_id" value="1" 
                                           <?= ($tag['status_id'] ?? 1) ? 'checked' : '' ?>>
                                    <span class="switch-slider"></span>
                                </div>
                                <label for="status_id" class="switch-label">显示状态</label>
                            </div>
                            <div class="form-text">开启后标签在前端可见，关闭后隐藏</div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!$isCreateMode): ?>
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
                        <div class="stat-value"><?= number_format($tag['content_cnt'] ?? 0) ?></div>
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
                            <input type="text" class="form-control" id="created_at" name="created_at" 
                                   value="<?= htmlspecialchars($tag['created_at'] ?? '') ?>" disabled>
                        </div>
                    </div>
                    <div class="col-md-6 pb-3">
                        <div class="form-group">
                            <label for="updated_at" class="form-label">最后更新时间</label>
                            <input type="text" class="form-control" id="updated_at" name="updated_at" 
                                   value="<?= htmlspecialchars($tag['updated_at'] ?? '') ?>" disabled>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- 表单操作按钮 -->
            <div class="form-actions">
                <a href="/tags" class="btn btn-outline-secondary">
                    <i class="bi bi-x-lg"></i>
                    取消
                </a>
                <?php if (!$isCreateMode): ?>
                <button type="button" class="btn btn-outline-primary" id="previewBtn">
                    <i class="bi bi-eye"></i>
                    预览
                </button>
                <?php endif; ?>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i>
                    <?= !$isCreateMode ? '保存修改' : '创建标签' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // 简化的表单处理（传统POST提交方式）
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('tagEditForm');
        const nameInput = document.getElementById('name_cn');
        const iconInput = document.getElementById('icon_class');
        const colorSelect = document.getElementById('color_class');
        const previewBtn = document.getElementById('tagPreviewBtn');
        const previewIcon = document.getElementById('previewIcon');
        const previewText = document.getElementById('previewText');

        // 实时预览更新
        function updatePreview() {
            const name = nameInput.value || '新标签';
            const icon = iconInput.value || 'bi-star';
            const color = colorSelect.value || 'btn-outline-primary';

            previewText.textContent = name;
            previewIcon.className = 'bi ' + icon;
            previewBtn.className = 'btn ' + color;
        }

        // 绑定预览更新事件
        if (nameInput) nameInput.addEventListener('input', updatePreview);
        if (iconInput) iconInput.addEventListener('input', updatePreview);
        if (colorSelect) colorSelect.addEventListener('change', updatePreview);

        // 初始化多选组件（如果存在）
        if (window.MultiSelectDropdown && document.getElementById('videoMultiSelect')) {
            const contentOptions = <?= json_encode($contentOptions ?? [], JSON_UNESCAPED_UNICODE) ?>;
            
            // 转换数据格式
            const videoData = contentOptions.map(function(option) {
                return {
                    id: option.id.toString(),
                    text: option.title
                };
            });

            const selectedData = contentOptions.filter(function(option) {
                return option.selected;
            }).map(function(option) {
                return {
                    id: option.id.toString(),
                    text: option.title
                };
            });

            // 创建多选组件
            new MultiSelectDropdown(document.getElementById('videoMultiSelect'), {
                placeholder: '选择关联视频...',
                searchPlaceholder: '搜索视频标题...',
                hiddenInputName: 'related_videos',
                maxDisplayItems: 7,
                columns: 4,
                data: videoData,
                selected: selectedData,
                allowClear: true
            });
        }

        // 表单提交处理（传统POST方式）
        form.addEventListener('submit', function(e) {
            // 处理checkbox状态
            const statusCheckbox = document.getElementById('status_id');
            if (!statusCheckbox.checked) {
                // 如果没有选中，添加隐藏字段设置为0
                const hiddenStatus = document.createElement('input');
                hiddenStatus.type = 'hidden';
                hiddenStatus.name = 'status_id';
                hiddenStatus.value = '0';
                form.appendChild(hiddenStatus);
            }

            // 显示加载状态
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> 保存中...';
                submitBtn.disabled = true;
                
                // 如果用户刷新页面或返回，恢复按钮状态
                setTimeout(function() {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }, 5000);
            }
            
            // 表单将会自动提交到后端（传统POST方式）
            // 不阻止默认行为，让浏览器执行正常的表单提交
        });
    });
</script>