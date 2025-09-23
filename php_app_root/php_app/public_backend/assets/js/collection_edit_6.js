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

    // 读取 PHP 填充数据。
    contentList = window.inputData.contentList;
    selectedContentIds = window.inputData.selectedContentIds;

    /**
     * 初始化页面特定的多选组件
     * 配置视频的多选下拉组件
     */
    initializePageMultiSelects() {
        if (!this.formUtils) {
            console.error('FormUtils 未初始化，无法创建多选组件');
            return;
        }


        const selectedContentIds = this.contentList.filter(video => this.selectedContentIds.includes(video.id));

        // 初始化视频多选组件
        const videosInstance = this.formUtils.initializeMultiSelect('videos', {
            container: '#videoMultiSelect',
            placeholder: '选择关联视频...',
            searchPlaceholder: '搜索视频标题...',
            hiddenInputName: 'related_videos',
            maxDisplayItems: 7,
            columns: 4,
            data: this.contentList,
            selected: selectedContentIds,
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
    updateVideoStats: (selectedContentIds) => {
        // 使用通用的统计更新方法
        window.collectionEditManager?.formUtils?.updateCommonContentStats(selectedContentIds);
    },
    getSelectedVideos: () => {
        return window.collectionEditManager?.getSelectedVideos() || [];
    },
    setSelectedVideos: (videoIds) => {
        window.collectionEditManager?.setSelectedVideos(videoIds);
    }
};
