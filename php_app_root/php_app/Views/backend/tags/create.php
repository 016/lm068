            <!-- Tag Create Form Content -->
            <main class="dashboard-content">
                <!-- Breadcrumb and Page Title -->
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-pencil-square page-title-icon"></i>
                            <div>
                                <h1 class="page-title">编辑标签</h1>
                                <p class="page-subtitle">Edit Tag Information</p>
                            </div>
                        </div>
                        <a href="/tags" class="back-link">
                            <i class="bi bi-arrow-left"></i>
                            返回标签列表
                        </a>
                    </div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb breadcrumb-custom">
                            <li class="breadcrumb-item"><a href="/backend" class="breadcrumb-link">首页</a></li>
                            <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">内容管理</a></li>
                            <li class="breadcrumb-item"><a href="/tags" class="breadcrumb-link">标签管理</a></li>
                            <li class="breadcrumb-item active breadcrumb-active" aria-current="page">编辑标签</li>
                        </ol>
                    </nav>
                </div>

                <!-- Tag Create Form -->
                <div class="row justify-content-center">
                    <div class="col-12 col-lg-12">
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

                                <form id="tagEditForm" action="/tag/create" method="POST">
                                    <!-- CSRF Token for security -->
                                    <input type="hidden" name="_token" value="csrf_token_placeholder">
                                    
                                    <!-- 基本信息 -->
                                    <div class="form-section">
                                        <h4 class="form-section-title">
                                            <i class="bi bi-info-circle form-section-icon"></i>
                                            基本信息
                                        </h4>
                                        
                                        <div class="row">
                                            <div class="col-md-6 pb-3">
                                                <div class="form-group">
                                                    <label for="tagPreviewBtn" class="form-label">标签预览</label>
                                                    <div class="tag-preview-container">
                                                        <button type="button" id="tagPreviewBtn" class="btn btn-outline-primary">
                                                            <i class="bi bi-star" id="previewIcon"></i>
                                                            <span id="previewText">标签标题</span>
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
                                                    <label for="videoMultiSelect" class="form-label">关联视频</label>
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
                                                    <div class="switch-group">
                                                        <div class="custom-switch tag-edit-switch">
                                                            <!-- 隐藏字段确保总是提交status_id值，未选中时为0 -->
                                                            <input class="ee_switch-value" type="hidden" name="status_id" value="<?= ($tag['status_id'] ?? 1) ?>">
                                                            <!-- checkbox字段，选中时会覆盖隐藏字段的值为1 -->
                                                            <input type="checkbox" id="status_id" <?= ($tag['status_id'] ?? 1) ? 'checked' : '' ?>>
                                                            <span class="switch-slider"></span>
                                                        </div>
                                                        <label for="status_id" class="switch-label">显示状态</label>
                                                    </div>
                                                    <div class="form-text">开启后标签在前端可见，关闭后隐藏</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 表单操作按钮 -->
                                    <div class="form-actions">
                                        <button type="button" id="btn-cancel" class="btn btn-outline-secondary" onclick="window.location.href='/tags'">
                                            <i class="bi bi-x-lg"></i>
                                            取消
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-check-lg"></i>
                                            保存修改
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

<script>
    // 将动态数据传递给JS
    window.tagCreateData = {
        videoData: <?= json_encode($videoData ?? [], JSON_UNESCAPED_UNICODE) ?>,
        selectedVideoIds: <?= json_encode($selectedVideoIds ?? [], JSON_UNESCAPED_UNICODE) ?>
    };
</script>