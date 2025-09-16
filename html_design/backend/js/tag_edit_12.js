/**
 * 标签编辑页面 JavaScript - 第12版
 * 基于 tag_edit_11.js 重构，使用 form_utils_2.js 的通用表单功能
 * 移除重复代码，使用共享的表单处理逻辑
 */

class TagEditManager {
    constructor() {
        this.form = document.getElementById('tagEditForm');
        this.formUtils = null;
        
        this.init();
    }

    /**
     * 初始化标签编辑页面
     */
    init() {
        // 初始化表单工具（依赖 form_utils_2.js）
        this.initializeFormUtils();
        
        // 初始化页面特定的多选组件
        this.initializePageMultiSelects();
        
        console.log('TagEditManager initialized');
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
        this.formUtils = new FormUtils('#tagEditForm', {
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
                previewBtn: 'tagPreviewBtn',
                defaultText: '标签标题',
                defaultIcon: 'bi-star'
            }
        });
    }


    // 模拟视频数据
    videoData = [
        { id: '1', text: '【搞笑1】动物搞笑合集第一期【搞笑】动物搞笑合集第一期【搞笑】动物搞笑合集第一期' },
        { id: '2', text: '【搞笑】网络热门段子精选' },
        { id: '3', text: '【搞笑】街头恶搞大合集' },
        { id: '4', text: '【搞笑】宠物萌宠搞怪瞬间' },
        { id: '5', text: '【搞笑】校园趣事分享' },
        { id: '6', text: '【搞笑】办公室日常爆笑办公室日常爆笑办公室日常爆笑办公室日常爆笑办公室日常爆笑办公室日常爆笑' },
        { id: '7', text: '【搞笑】生活中的尴尬时刻' },
        { id: '8', text: '【搞笑】网红模仿秀合集' },
        { id: '9', text: '【搞笑】儿童童言无忌' },
        { id: '10', text: '【搞笑】运动失误搞笑瞬间' },
        { id: '11', text: '【搞笑】厨房烹饪意外合集' },
        { id: '12', text: '【搞笑】旅游途中趣事分享' },
        { id: '13', text: '【搞笑】家庭聚会爆笑时刻' },
        { id: '14', text: '【搞笑】公共场所尴尬瞬间' },
        { id: '15', text: '【搞笑】网络直播搞笑片段' },
        { id: '16', text: '【搞笑】老人爆笑语录合集老人爆笑语录合集老人爆笑语录合集老人爆笑语录合集老人爆笑语录合集老人爆笑语录合集' },
        { id: '17', text: '【搞笑】交通工具趣事' },
        { id: '18', text: '【搞笑】购物时的奇遇记' },
        { id: '19', text: '【搞笑】节日庆典搞怪时刻节日庆典搞怪时刻节日庆典搞怪时刻节日庆典搞怪时刻节日庆典搞怪时刻' },
        { id: '20', text: '【搞笑】科技产品使用囧事' }
    ];

    /**
     * 初始化页面特定的多选组件
     * 配置视频的多选下拉组件
     */
    initializePageMultiSelects() {
        if (!this.formUtils) {
            console.error('FormUtils 未初始化，无法创建多选组件');
            return;
        }

        // 模拟已选中的视频
        const selectedVideoIds = ['1', '4', '7', '9', '12', '15'];
        const selectedVideos = this.videoData.filter(video => selectedVideoIds.includes(video.id));

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
                // 使用通用的视频变更处理方法，标签页面有更高的视频数量限制
                this.formUtils.handleCommonVideosChange(e.detail, 100, '建议单个标签关联的视频数量不超过{limit}个');
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
        
        console.log('TagEditManager destroyed');
    }
}

// 页面加载完成后初始化
document.addEventListener('DOMContentLoaded', () => {
    window.tagEditManager = new TagEditManager();
});

// 兼容性：暴露给外部使用的工具函数（保持向后兼容）
window.TagEditForm = {
    updateVideoStats: (selectedVideos) => {
        // 使用通用的统计更新方法，支持大数字格式化
        window.tagEditManager?.formUtils?.updateCommonVideoStats(selectedVideos, '.stats-row .stat-item .stat-value', true);
    },
    getSelectedVideos: () => {
        return window.tagEditManager?.getSelectedVideos() || [];
    },
    setSelectedVideos: (videoIds) => {
        window.tagEditManager?.setSelectedVideos(videoIds);
    }
};