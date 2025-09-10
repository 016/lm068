/* Collection Edit Form with Multi-Select Video Component - JavaScript */

document.addEventListener('DOMContentLoaded', function() {
    // 初始化多选视频组件
    initVideoMultiSelect();
    
    // 初始化其他表单功能
    initFormPreview();
    initFormValidation();

    window.AdminCommon.ValidationUtils.initializeCharacterCounters(document.getElementById('collectionEditForm'));
});

// 初始化视频多选组件
function initVideoMultiSelect() {
    const videoContainer = document.getElementById('videoMultiSelect');
    if (!videoContainer) return;

    // 模拟视频数据 - 在实际项目中这些数据应该从后端API获取
    const videoData = [
        { id: 'v001', text: '【科技】AI人工智能未来发展趋势解析' },
        { id: 'v002', text: '【科技】量子计算机的工作原理详解' },
        { id: 'v003', text: '【科技】新能源汽车技术革命' },
        { id: 'v004', text: '【科技】5G网络如何改变我们的生活' },
        { id: 'v005', text: '【科技】虚拟现实VR技术应用前景' },
        { id: 'v006', text: '【科技】区块链技术深度解析' },
        { id: 'v007', text: '【科技】机器学习算法入门教程' },
        { id: 'v008', text: '【科技】物联网IoT设备连接原理' },
        { id: 'v009', text: '【科技】云计算服务架构详解' },
        { id: 'v010', text: '【科技】大数据分析技术应用' },
        { id: 'v011', text: '【科技】人脸识别技术发展历程' },
        { id: 'v012', text: '【科技】自动驾驶汽车技术原理' },
        { id: 'v013', text: '【科技】智能家居系统搭建指南' },
        { id: 'v014', text: '【科技】生物识别技术应用场景' },
        { id: 'v015', text: '【科技】3D打印技术发展现状' }
    ];

    // 模拟已选中的视频ID - 在实际项目中从PHP渲染的数据中获取
    const selectedVideoIds = ['v001', 'v003', 'v007', 'v010'];
    const selectedVideos = videoData.filter(video => selectedVideoIds.includes(video.id));

    // 初始化多选组件
    window.collectionVideoSelector = new MultiSelectDropdown(videoContainer, {
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
        statValueElement.textContent = selectedVideos.length;
    }
}

// 初始化表单预览功能
function initFormPreview() {
    // 实时预览合集标题和图标
    const nameInput = document.getElementById('name_cn');
    const iconInput = document.getElementById('icon_class');
    const colorSelect = document.getElementById('color_class');
    const previewText = document.getElementById('previewText');
    const previewIcon = document.getElementById('previewIcon');
    const previewBtn = document.getElementById('collectionPreviewBtn');

    if (nameInput && previewText) {
        nameInput.addEventListener('input', function() {
            previewText.textContent = this.value || '合集标题';
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
    const form = document.getElementById('collectionEditForm');
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
        const selectedVideos = window.collectionVideoSelector ? window.collectionVideoSelector.getSelected() : [];
        console.log('准备提交的关联视频:', selectedVideos);

        // 在实际项目中这里会提交到后端
        if (confirm('确定要保存修改吗？')) {
            // 模拟提交
            console.log('表单数据提交中...');
            alert('合集信息已成功保存！（演示模式）');
        }
    });
}

// 工具函数：获取当前选中的视频
function getSelectedVideos() {
    return window.collectionVideoSelector ? window.collectionVideoSelector.getSelected() : [];
}

// 工具函数：设置选中的视频
function setSelectedVideos(videoIds) {
    if (!window.collectionVideoSelector) return;
    
    if (typeof videoIds === 'string') {
        window.collectionVideoSelector.setValue(videoIds);
    } else if (Array.isArray(videoIds)) {
        window.collectionVideoSelector.setValue(videoIds.join(','));
    }
}

// 暴露全局函数供其他脚本使用
window.CollectionEditForm = {
    getSelectedVideos,
    setSelectedVideos,
    updateVideoStats
};