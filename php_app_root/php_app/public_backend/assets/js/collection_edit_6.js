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

    }

    // 模拟视频数据
    videoData = [
        { id: '1', text: '【科技】AI人工智能未来发展趋势解析' },
        { id: '2', text: '【科技】量子计算机的工作原理详解' },
        { id: '3', text: '【科技】新能源汽车技术革命' },
        { id: '4', text: '【科技】5G网络如何改变我们的生活' },
        { id: '5', text: '【科技】虚拟现实VR技术应用前景' },
        { id: '6', text: '【科技】区块链技术深度解析' },
        { id: '7', text: '【科技】机器学习算法入门教程' },
        { id: '8', text: '【科技】物联网IoT设备连接原理' },
        { id: '9', text: '【科技】云计算服务架构详解' },
        { id: '10', text: '【科技】大数据分析技术应用' },
        { id: '11', text: '【科技】人脸识别技术发展历程' },
        { id: '12', text: '【科技】自动驾驶汽车技术原理' },
        { id: '13', text: '【科技】智能家居系统搭建指南' },
        { id: '14', text: '【科技】生物识别技术应用场景' },
        { id: '15', text: '【科技】3D打印技术发展现状' }
    ];

    // 模拟已选中的视频
    selectedVideoIds = ['1', '3', '7', '10'];

    /**
     * 初始化页面特定的多选组件
     * 配置视频的多选下拉组件
     */
    initializePageMultiSelects() {
        if (!this.formUtils) {
            console.error('FormUtils 未初始化，无法创建多选组件');
            return;
        }


        const selectedVideos = this.videoData.filter(video => this.selectedVideoIds.includes(video.id));

        // 初始化视频多选组件
        const videosInstance = this.formUtils.initializeMultiSelect('videos', {
            container: '#videoMultiSelect',
            placeholder: '选择关联视频...',
            searchPlaceholder: '搜索视频标题...',
            hiddenInputName: 'related_videos',
            maxDisplayItems: 7,
            columns: 4,
            data: this.videoData,
            selected: selectedVideos,
            allowClear: true
        });

        // 绑定视频变更的特定处理
        if (videosInstance) {
            document.getElementById('videoMultiSelect').addEventListener('multiselect:change', (e) => {
                // 使用通用的视频变更处理方法
                this.formUtils.handleCommonContentsChange(e.detail);
            });
        }
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

// 兼容性：暴露给外部使用的工具函数（保持向后兼容）
window.CollectionEditForm = {
    updateVideoStats: (selectedVideos) => {
        // 使用通用的统计更新方法
        window.collectionEditManager?.formUtils?.updateCommonContentStats(selectedVideos);
    },
    getSelectedVideos: () => {
        return window.collectionEditManager?.getSelectedVideos() || [];
    },
    setSelectedVideos: (videoIds) => {
        window.collectionEditManager?.setSelectedVideos(videoIds);
    }
};
