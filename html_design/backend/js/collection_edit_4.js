/* Collection Edit Form with FormUtils Integration - JavaScript */

document.addEventListener('DOMContentLoaded', function() {
    // 使用 FormUtils 初始化表单
    const formUtils = new FormUtils('#collectionEditForm', {
        enableAutoSave: true,
        enableCharacterCounter: true,
        enableFileUpload: true,
        enableNotification: true
    });

    // 初始化合集特定功能
    initCollectionSpecificFeatures(formUtils);
    
    // 初始化多选视频组件
    initVideoMultiSelect(formUtils);
});

// 初始化合集特定功能
function initCollectionSpecificFeatures(formUtils) {
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

    // 监听表单提交事件
    document.getElementById('collectionEditForm').addEventListener('formutils:submit', function(event) {
        const { formData } = event.detail;
        
        // 获取选中的视频数据
        const videoSelector = formUtils.getMultiSelectInstance('videos');
        const selectedVideos = videoSelector ? videoSelector.getSelected() : [];
        
        console.log('合集表单提交数据:', formData);
        console.log('关联视频:', selectedVideos);
        
        // 自定义提交逻辑
        handleCollectionFormSubmit(formData, selectedVideos, formUtils);
        
        // 阻止默认提交行为，使用自定义处理
        event.preventDefault();
    });
}

// 初始化视频多选组件
function initVideoMultiSelect(formUtils) {
    // 模拟视频数据
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

    // 模拟已选中的视频
    const selectedVideoIds = ['v001', 'v003', 'v007', 'v010'];
    const selectedVideos = videoData.filter(video => selectedVideoIds.includes(video.id));

    // 使用 FormUtils 初始化多选组件
    const videoSelector = formUtils.initializeMultiSelect('videos', {
        container: '#videoMultiSelect',
        placeholder: '选择关联视频...',
        searchPlaceholder: '搜索视频标题...',
        hiddenInputName: 'related_videos',
        maxDisplayItems: 7,
        columns: 4,
        data: videoData,
        selected: selectedVideos,
        allowClear: true
    });

    // 监听选择变化，更新统计信息
    document.getElementById('videoMultiSelect').addEventListener('multiselect:change', function(event) {
        const { action, selected } = event.detail;
        console.log(`视频选择${action}:`, event.detail.item);
        console.log('当前已选择视频数量:', selected.length);
        
        updateVideoStats(selected);
    });

    // 监听FormUtils的multiselect变化事件
    document.getElementById('collectionEditForm').addEventListener('formutils:multiselect:change', function(event) {
        const { key, selected } = event.detail;
        if (key === 'videos') {
            updateVideoStats(selected);
        }
    });

    console.log('合集视频多选组件初始化完成');
}

// 更新视频统计信息
function updateVideoStats(selectedVideos) {
    const statValueElement = document.querySelector('.stats-row .stat-item .stat-value');
    if (statValueElement && selectedVideos) {
        statValueElement.textContent = selectedVideos.length;
    }
}

// 处理合集表单提交
function handleCollectionFormSubmit(formData, selectedVideos, formUtils) {
    // 在实际项目中这里会提交到后端
    if (confirm('确定要保存合集修改吗？')) {
        // 模拟提交过程
        const submitBtn = document.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.classList.add('loading');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>保存中...';
        
        setTimeout(() => {
            // 恢复按钮状态
            submitBtn.classList.remove('loading');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
            
            formUtils.showNotification('合集信息已成功保存！', 'success');
            formUtils.markAsClean();
            
            console.log('合集数据提交完成:', {
                formData,
                selectedVideos: selectedVideos.length
            });
        }, 2000);
    }
}

// 暴露给外部使用的工具函数
window.CollectionEditForm = {
    updateVideoStats,
    getSelectedVideos: () => {
        const formUtils = window.formUtils;
        const videoSelector = formUtils ? formUtils.getMultiSelectInstance('videos') : null;
        return videoSelector ? videoSelector.getSelected() : [];
    },
    setSelectedVideos: (videoIds) => {
        const formUtils = window.formUtils;
        const videoSelector = formUtils ? formUtils.getMultiSelectInstance('videos') : null;
        if (videoSelector) {
            if (typeof videoIds === 'string') {
                videoSelector.setValue(videoIds);
            } else if (Array.isArray(videoIds)) {
                videoSelector.setValue(videoIds.join(','));
            }
        }
    }
};