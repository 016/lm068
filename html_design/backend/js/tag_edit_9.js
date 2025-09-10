/* Tag Edit Form with Multi-Select Video Component - JavaScript */

document.addEventListener('DOMContentLoaded', function() {
    // 初始化多选视频组件
    initVideoMultiSelect();
    
    // 初始化其他表单功能
    initFormPreview();
    initFormValidation();
});

// 初始化视频多选组件
function initVideoMultiSelect() {
    const videoContainer = document.getElementById('videoMultiSelect');
    if (!videoContainer) return;

    // 模拟视频数据 - 在实际项目中这些数据应该从后端API获取
    const videoData = [
        { id: 'v101', text: '【搞笑】动物搞笑合集第一期【搞笑】动物搞笑合集第一期【搞笑】动物搞笑合集第一期' },
        { id: 'v102', text: '【搞笑】网络热门段子精选' },
        { id: 'v103', text: '【搞笑】街头恶搞大合集' },
        { id: 'v104', text: '【搞笑】宠物萌宠搞怪瞬间' },
        { id: 'v105', text: '【搞笑】校园趣事分享' },
        { id: 'v106', text: '【搞笑】办公室日常爆笑办公室日常爆笑办公室日常爆笑办公室日常爆笑办公室日常爆笑办公室日常爆笑' },
        { id: 'v107', text: '【搞笑】生活中的尴尬时刻' },
        { id: 'v108', text: '【搞笑】网红模仿秀合集' },
        { id: 'v109', text: '【搞笑】儿童童言无忌' },
        { id: 'v110', text: '【搞笑】运动失误搞笑瞬间' },
        { id: 'v111', text: '【搞笑】厨房烹饪意外合集' },
        { id: 'v112', text: '【搞笑】旅游途中趣事分享' },
        { id: 'v113', text: '【搞笑】家庭聚会爆笑时刻' },
        { id: 'v114', text: '【搞笑】公共场所尴尬瞬间' },
        { id: 'v115', text: '【搞笑】网络直播搞笑片段' },
        { id: 'v116', text: '【搞笑】老人爆笑语录合集老人爆笑语录合集老人爆笑语录合集老人爆笑语录合集老人爆笑语录合集老人爆笑语录合集' },
        { id: 'v117', text: '【搞笑】交通工具趣事' },
        { id: 'v118', text: '【搞笑】购物时的奇遇记' },
        { id: 'v119', text: '【搞笑】节日庆典搞怪时刻节日庆典搞怪时刻节日庆典搞怪时刻节日庆典搞怪时刻节日庆典搞怪时刻' },
        { id: 'v120', text: '【搞笑】科技产品使用囧事' }
    ];

    // 模拟已选中的视频ID - 在实际项目中从PHP渲染的数据中获取
    const selectedVideoIds = ['v101', 'v104', 'v107', 'v109', 'v112', 'v115'];
    const selectedVideos = videoData.filter(video => selectedVideoIds.includes(video.id));

    // 初始化多选组件
    window.tagVideoSelector = new MultiSelectDropdown(videoContainer, {
        placeholder: '选择关联视频...',
        searchPlaceholder: '搜索视频标题...',
        hiddenInputName: 'related_videos', // PHP表单字段名
        maxDisplayItems: 7,
        columns: 4,
        data: videoData,
        selected: selectedVideos,
        allowClear: true
    });

    // 监听选择变化事件
    videoContainer.addEventListener('multiselect:change', function(event) {
        const { action, selected } = event.detail;
        console.log(`视频选择${action}:`, event.detail.item);
        console.log('当前已选择视频数量:', selected.length);
        
        // 可以在这里添加其他业务逻辑
        // 例如更新统计信息、保存草稿等
        updateVideoStats(selected);
    });

    console.log('视频多选组件初始化完成');
}

// 更新视频统计信息
function updateVideoStats(selectedVideos) {
    // 更新页面上的关联视频数量显示
    const statValueElement = document.querySelector('.stats-row .stat-item .stat-value');
    if (statValueElement && selectedVideos) {
        // 将数量格式化为千位分隔符形式
        const count = selectedVideos.length;
        const formattedCount = count > 999 ? `${(count / 1000).toFixed(1)}K` : count.toString();
        statValueElement.textContent = formattedCount;
    }
}

// 初始化表单预览功能
function initFormPreview() {
    // 实时预览标签标题和图标
    const nameInput = document.getElementById('name_cn');
    const iconInput = document.getElementById('icon_class');
    const colorSelect = document.getElementById('color_class');
    const previewText = document.getElementById('previewText');
    const previewIcon = document.getElementById('previewIcon');
    const previewBtn = document.getElementById('tagPreviewBtn');

    if (nameInput && previewText) {
        nameInput.addEventListener('input', function() {
            previewText.textContent = this.value || '标签标题';
        });
    }

    if (iconInput && previewIcon) {
        iconInput.addEventListener('input', function() {
            previewIcon.className = `bi ${this.value || 'bi-star'}`;
        });
    }

    if (colorSelect && previewBtn) {
        colorSelect.addEventListener('change', function() {
            previewBtn.className = `btn ${this.value}`;
        });
    }
}

// 初始化表单验证
function initFormValidation() {
    const form = document.getElementById('tagEditForm');
    if (!form) return;

    form.addEventListener('submit', function(event) {
        event.preventDefault();
        
        // 基本字段验证
        const requiredFields = ['name_cn', 'name_en'];
        let isValid = true;
        
        requiredFields.forEach(fieldName => {
            const field = document.getElementById(fieldName);
            if (field && !field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else if (field) {
                field.classList.remove('is-invalid');
            }
        });

        if (!isValid) {
            alert('请填写所有必填字段');
            return;
        }

        // 获取选中的视频数据
        const selectedVideos = window.tagVideoSelector ? window.tagVideoSelector.getSelected() : [];
        console.log('准备提交的关联视频:', selectedVideos);

        // 在实际项目中这里会提交到后端
        if (confirm('确定要保存修改吗？')) {
            // 模拟提交
            console.log('表单数据提交中...');
            alert('标签信息已成功保存！（演示模式）');
        }
    });

    // 预览按钮功能
    const previewBtn = document.querySelector('.btn-outline-primary');
    if (previewBtn && previewBtn.textContent.includes('预览')) {
        previewBtn.addEventListener('click', function() {
            showTagPreview();
        });
    }
}

// 显示标签预览
function showTagPreview() {
    const nameCn = document.getElementById('name_cn').value || '标签名称';
    const nameEn = document.getElementById('name_en').value || 'Tag Name';
    const shortDescCn = document.getElementById('short_desc_cn').value || '标签简介';
    const iconClass = document.getElementById('icon_class').value || 'bi-star';
    const colorClass = document.getElementById('color_class').value || 'btn-outline-primary';
    const selectedVideos = window.tagVideoSelector ? window.tagVideoSelector.getSelected() : [];

    const previewContent = `
        <div class="tag-preview-modal">
            <h5>标签预览效果</h5>
            <div class="preview-tag-display">
                <button type="button" class="btn ${colorClass}">
                    <i class="bi ${iconClass}"></i>
                    ${nameCn}
                </button>
            </div>
            <div class="preview-details mt-3">
                <p><strong>中文标题:</strong> ${nameCn}</p>
                <p><strong>英文标题:</strong> ${nameEn}</p>
                <p><strong>简介:</strong> ${shortDescCn}</p>
                <p><strong>关联视频数:</strong> ${selectedVideos.length} 个</p>
                ${selectedVideos.length > 0 ? 
                    `<div class="preview-videos">
                        <strong>关联视频:</strong>
                        <ul class="list-unstyled mt-2">
                            ${selectedVideos.slice(0, 5).map(video => `<li>• ${video.text}</li>`).join('')}
                            ${selectedVideos.length > 5 ? `<li>... 还有 ${selectedVideos.length - 5} 个视频</li>` : ''}
                        </ul>
                    </div>` : ''
                }
            </div>
        </div>
    `;

    // 创建模态框显示预览
    const modalDiv = document.createElement('div');
    modalDiv.innerHTML = `
        <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">标签预览</h5>
                        <button type="button" class="btn-close" onclick="this.closest('.modal').remove()"></button>
                    </div>
                    <div class="modal-body">
                        ${previewContent}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="this.closest('.modal').remove()">关闭</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(modalDiv);
}

// 工具函数：获取当前选中的视频
function getSelectedVideos() {
    return window.tagVideoSelector ? window.tagVideoSelector.getSelected() : [];
}

// 工具函数：设置选中的视频
function setSelectedVideos(videoIds) {
    if (!window.tagVideoSelector) return;
    
    if (typeof videoIds === 'string') {
        window.tagVideoSelector.setValue(videoIds);
    } else if (Array.isArray(videoIds)) {
        window.tagVideoSelector.setValue(videoIds.join(','));
    }
}

// 暴露全局函数供其他脚本使用
window.TagEditForm = {
    getSelectedVideos,
    setSelectedVideos,
    updateVideoStats,
    showTagPreview
};