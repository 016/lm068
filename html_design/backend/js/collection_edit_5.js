/**
 * 合集编辑页面 JavaScript - 第5版
 * 基于 collection_edit_4.js 重构，参考 ContentEditManager 类结构设计
 * 使用类封装页面逻辑，依赖 FormUtils 通用表单工具
 */

class CollectionEditManager {
    constructor() {
        this.form = document.getElementById('collectionEditForm');
        this.formUtils = null;
        
        this.init();
    }

    /**
     * 初始化合集编辑页面
     */
    init() {
        // 初始化表单工具（依赖 form_utils_1.js）
        this.initializeFormUtils();
        
        // 初始化页面特定的多选组件
        this.initializePageMultiSelects();
        
        // 初始化页面特定功能
        this.initializePageFeatures();
        
        // 绑定页面特定事件
        this.bindPageEvents();
        
        console.log('CollectionEditManager initialized');
    }

    /**
     * 初始化表单工具
     * 使用通用的 FormUtils 类处理表单基础功能
     */
    initializeFormUtils() {
        if (!window.FormUtils) {
            console.error('FormUtils 未找到，请确保已引入 form_utils_1.js');
            return;
        }

        // 创建表单工具实例，启用所有功能
        this.formUtils = new FormUtils('#collectionEditForm', {
            enableAutoSave: true,
            enableCharacterCounter: true,
            enableFileUpload: true,
            enableNotification: true
        });

        // 监听表单提交事件，添加页面特定的处理逻辑
        this.form.addEventListener('formutils:submit', (e) => {
            this.handlePageSpecificSubmit(e.detail);
        });
    }

    /**
     * 初始化页面特定的多选组件
     * 配置视频的多选下拉组件
     */
    initializePageMultiSelects() {
        if (!this.formUtils) {
            console.error('FormUtils 未初始化，无法创建多选组件');
            return;
        }

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

        // 初始化视频多选组件
        const videosInstance = this.formUtils.initializeMultiSelect('videos', {
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

        // 绑定视频变更的特定处理
        if (videosInstance) {
            document.getElementById('videoMultiSelect').addEventListener('multiselect:change', (e) => {
                this.handleVideosChange(e.detail);
            });
        }
    }

    /**
     * 初始化页面特定功能
     * 设置合集预览功能
     */
    initializePageFeatures() {
        this.initializeCollectionPreview();
    }

    /**
     * 初始化合集预览功能
     * 实时预览合集标题、图标和颜色效果
     */
    initializeCollectionPreview() {
        const nameInput = document.getElementById('name_cn');
        const iconInput = document.getElementById('icon_class');
        const colorSelect = document.getElementById('color_class');
        const previewText = document.getElementById('previewText');
        const previewIcon = document.getElementById('previewIcon');
        const previewBtn = document.getElementById('collectionPreviewBtn');

        // 实时更新预览文本
        if (nameInput && previewText) {
            nameInput.addEventListener('input', () => {
                previewText.textContent = nameInput.value || '合集标题';
            });
        }

        // 实时更新预览图标
        if (iconInput && previewIcon) {
            iconInput.addEventListener('input', () => {
                previewIcon.className = `bi ${iconInput.value || 'bi-star'}`;
            });
        }

        // 实时更新预览颜色
        if (colorSelect && previewBtn) {
            colorSelect.addEventListener('change', () => {
                previewBtn.className = `btn ${colorSelect.value}`;
            });
        }
    }

    /**
     * 绑定页面特定事件
     */
    bindPageEvents() {
        // 暂无其他页面特定事件
    }

    /**
     * 处理视频选择变更
     * 在视频选择变化时显示相应的通知信息和统计更新
     */
    handleVideosChange(detail) {
        const { action, item, selected } = detail;
        
        switch (action) {
            case 'add':
                this.showNotification(`已添加视频: ${item.text}`, 'success');
                break;
            case 'remove':
                this.showNotification(`已移除视频: ${item.text}`, 'info');
                break;
            case 'clear':
                this.showNotification('已清空所有视频', 'warning');
                break;
        }

        // 更新视频统计信息
        this.updateVideoStats(selected);

        // 视频数量限制提示
        if (selected.length > 50) {
            this.showNotification('建议单个合集包含的视频数量不超过50个', 'warning');
        }
    }

    /**
     * 更新视频统计信息
     * 更新页面上的视频数量显示
     */
    updateVideoStats(selectedVideos) {
        const statValueElement = document.querySelector('.stats-row .stat-item .stat-value');
        if (statValueElement && selectedVideos) {
            statValueElement.textContent = selectedVideos.length;
        }
    }

    /**
     * 处理页面特定的表单提交逻辑
     * 在通用表单提交基础上添加合集编辑页面的特殊处理
     */
    handlePageSpecificSubmit(detail) {
        const { formData } = detail;
        
        // 获取选中的视频数据
        const videoSelector = this.formUtils.getMultiSelectInstance('videos');
        const selectedVideos = videoSelector ? videoSelector.getSelected() : [];
        
        console.log('合集表单提交数据:', formData);
        console.log('关联视频:', selectedVideos);
        
        // 验证合集特定的业务规则
        if (this.validateCollectionRules(formData, selectedVideos)) {
            console.log('合集编辑页面提交验证通过:', formData);
            
            // 执行提交处理
            this.executeSubmit(formData, selectedVideos);
        } else {
            // 阻止默认提交流程
            detail.preventDefault?.();
        }
    }

    /**
     * 验证合集特定的业务规则
     * 检查合集编辑页面的特殊验证逻辑
     */
    validateCollectionRules(formData, selectedVideos) {
        let isValid = true;

        // 检查合集名称
        if (!formData.name_cn || formData.name_cn.trim().length < 2) {
            this.showNotification('合集名称至少需要2个字符', 'error');
            isValid = false;
        }

        // 检查合集是否包含视频
        if (!selectedVideos || selectedVideos.length === 0) {
            this.showNotification('合集至少需要包含一个视频', 'error');
            isValid = false;
        }

        // 检查图标类名格式
        if (formData.icon_class && !formData.icon_class.startsWith('bi-')) {
            this.showNotification('图标类名必须以 "bi-" 开头', 'error');
            isValid = false;
        }

        return isValid;
    }

    /**
     * 执行提交处理
     * 处理合集表单的实际提交逻辑
     */
    executeSubmit(formData, selectedVideos) {
        // 确认提交
        if (!confirm('确定要保存合集修改吗？')) {
            return;
        }

        // 模拟提交过程
        const submitBtn = document.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.classList.add('loading');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>保存中...';
        
        // 模拟异步提交
        setTimeout(() => {
            // 恢复按钮状态
            submitBtn.classList.remove('loading');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
            
            this.showNotification('合集信息已成功保存！', 'success');
            this.formUtils.markAsClean();
            
            console.log('合集数据提交完成:', {
                formData,
                selectedVideos: selectedVideos.length
            });
        }, 2000);
    }

    /**
     * 显示通知消息
     * 使用 FormUtils 的通知功能
     */
    showNotification(message, type = 'info') {
        if (this.formUtils) {
            this.formUtils.showNotification(message, type);
        }
    }

    /**
     * 获取页面数据摘要
     * 返回当前页面的关键信息，用于调试和监控
     */
    getPageSummary() {
        if (!this.formUtils) return null;

        const formData = this.formUtils.collectFormData();
        const videosInstance = this.formUtils.getMultiSelectInstance('videos');

        return {
            collectionId: formData.id,
            nameCn: formData.name_cn,
            nameEn: formData.name_en,
            iconClass: formData.icon_class,
            colorClass: formData.color_class,
            videosCount: videosInstance ? videosInstance.getSelected().length : 0,
            isModified: this.formUtils.isModified
        };
    }

    /**
     * 获取选中的视频
     * 外部接口方法
     */
    getSelectedVideos() {
        const videoSelector = this.formUtils?.getMultiSelectInstance('videos');
        return videoSelector ? videoSelector.getSelected() : [];
    }

    /**
     * 设置选中的视频
     * 外部接口方法
     */
    setSelectedVideos(videoIds) {
        const videoSelector = this.formUtils?.getMultiSelectInstance('videos');
        if (videoSelector) {
            if (typeof videoIds === 'string') {
                videoSelector.setValue(videoIds);
            } else if (Array.isArray(videoIds)) {
                videoSelector.setValue(videoIds.join(','));
            }
        }
    }

    /**
     * 销毁页面管理器
     * 清理页面特定的资源和监听器
     */
    destroy() {
        if (this.formUtils) {
            this.formUtils.destroy();
        }
        
        console.log('CollectionEditManager destroyed');
    }
}

// 页面加载完成后初始化
document.addEventListener('DOMContentLoaded', () => {
    window.collectionEditManager = new CollectionEditManager();
});

// 页面卸载前清理
window.addEventListener('beforeunload', (e) => {
    if (window.collectionEditManager?.formUtils?.isModified) {
        e.preventDefault();
        e.returnValue = '您有未保存的更改，确定要离开吗？';
    }
});

// 兼容性：暴露给外部使用的工具函数（保持向后兼容）
window.CollectionEditForm = {
    updateVideoStats: (selectedVideos) => {
        window.collectionEditManager?.updateVideoStats(selectedVideos);
    },
    getSelectedVideos: () => {
        return window.collectionEditManager?.getSelectedVideos() || [];
    },
    setSelectedVideos: (videoIds) => {
        window.collectionEditManager?.setSelectedVideos(videoIds);
    }
};

// 开发调试功能（仅在开发环境使用）
if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
    // 在控制台添加调试命令
    window.debugCollectionEdit = {
        getSummary: () => window.collectionEditManager?.getPageSummary(),
        getFormData: () => window.collectionEditManager?.formUtils?.collectFormData(),
        resetForm: () => window.collectionEditManager?.formUtils?.resetForm(),
        showTest: (msg, type) => window.collectionEditManager?.showNotification(msg || '测试通知', type || 'info')
    };
    
    console.log('合集编辑页面调试工具已加载，使用 debugCollectionEdit 对象进行调试');
}