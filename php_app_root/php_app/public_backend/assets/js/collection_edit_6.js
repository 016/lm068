/**
 * 合集编辑页面 JavaScript - 第6版
 * 基于 collection_edit_5.js 重构，使用 form_utils_2.js 的通用表单功能
 * 移除重复代码，使用共享的表单处理逻辑
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
        // 初始化表单工具（依赖 form_utils_2.js）
        this.initializeFormUtils();
        
        // 初始化页面特定的多选组件
        this.initializePageMultiSelects();
        
        // 绑定页面特定事件
        this.bindPageEvents();
        
        console.log('CollectionEditManager initialized');
    }

    /**
     * 初始化表单工具
     * 使用通用的 FormUtils 类处理表单基础功能，包括预览功能
     */
    initializeFormUtils() {
        if (!window.FormUtils) {
            console.error('FormUtils 未找到，请确保已引入 form_utils_2.js');
            return;
        }

        // 创建表单工具实例，启用所有功能包括预览
        this.formUtils = new FormUtils('#collectionEditForm', {
            enableAutoSave: true,
            enableCharacterCounter: true,
            enableFileUpload: true,
            enableNotification: true,
            enablePreview: true,
            previewConfig: {
                nameInput: 'name_cn',
                previewText: 'previewText',
                iconInput: 'icon_class',
                previewIcon: 'previewIcon',
                colorSelect: 'color_class',
                previewBtn: 'collectionPreviewBtn',
                defaultText: '合集标题',
                defaultIcon: 'bi-star'
            }
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
                // 使用通用的视频变更处理方法
                this.formUtils.handleCommonVideosChange(e.detail, 50, '建议单个合集包含的视频数量不超过{limit}个');
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
            
            // 使用通用的提交处理流程
            this.formUtils.executeCommonSubmitFlow(
                formData, 
                selectedVideos,
                '确定要保存合集修改吗？',
                '保存中...',
                '合集信息已成功保存！',
                2000
            );
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

        // 使用通用的中文名称验证
        const nameValidation = this.formUtils.validateChineseName(formData.name_cn, 2, 50);
        if (!nameValidation.isValid) {
            this.showNotification(nameValidation.message, 'error');
            isValid = false;
        }

        // 检查合集是否包含视频
        if (!selectedVideos || selectedVideos.length === 0) {
            this.showNotification('合集至少需要包含一个视频', 'error');
            isValid = false;
        }

        // 使用通用的图标类名验证
        const iconValidation = this.formUtils.validateIconClass(formData.icon_class);
        if (!iconValidation.isValid) {
            this.showNotification(iconValidation.message, 'error');
            isValid = false;
        }

        return isValid;
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
        // 使用通用的统计更新方法
        window.collectionEditManager?.formUtils?.updateCommonVideoStats(selectedVideos);
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