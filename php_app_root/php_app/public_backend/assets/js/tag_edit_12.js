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
        
        // 绑定页面特定事件
        this.bindPageEvents();
        
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

        // 模拟已选中的视频
        const selectedVideoIds = ['v101', 'v104', 'v107', 'v109', 'v112', 'v115'];
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
                // 使用通用的视频变更处理方法，标签页面有更高的视频数量限制
                this.formUtils.handleCommonVideosChange(e.detail, 100, '建议单个标签关联的视频数量不超过{limit}个');
            });
        }
    }

    /**
     * 绑定页面特定事件
     */
    bindPageEvents() {
        // 预览按钮功能
        const previewBtnSelector = document.querySelector('.btn-outline-primary');
        if (previewBtnSelector && previewBtnSelector.textContent.includes('预览')) {
            previewBtnSelector.addEventListener('click', () => {
                this.showTagPreview();
            });
        }
    }

    /**
     * 显示标签预览
     * 使用通用的预览模态框功能
     */
    showTagPreview() {
        const nameCn = document.getElementById('name_cn')?.value || '标签名称';
        const nameEn = document.getElementById('name_en')?.value || 'Tag Name';
        const shortDescCn = document.getElementById('short_desc_cn')?.value || '标签简介';
        const iconClass = document.getElementById('icon_class')?.value || 'bi-star';
        const colorClass = document.getElementById('color_class')?.value || 'btn-outline-primary';
        
        const videoSelector = this.formUtils?.getMultiSelectInstance('videos');
        const selectedVideos = videoSelector ? videoSelector.getSelected() : [];

        // 使用通用的预览模态框
        this.formUtils.showCommonPreviewModal({
            nameCn,
            nameEn,
            shortDescCn,
            iconClass,
            colorClass,
            selectedVideos
        }, '标签预览');
    }

    /**
     * 处理页面特定的表单提交逻辑
     * 在通用表单提交基础上添加标签编辑页面的特殊处理
     */
    handlePageSpecificSubmit(detail) {
        const { formData } = detail;
        
        // 获取选中的视频数据
        const videoSelector = this.formUtils.getMultiSelectInstance('videos');
        const selectedVideos = videoSelector ? videoSelector.getSelected() : [];
        
        console.log('标签表单提交数据:', formData);
        console.log('关联视频:', selectedVideos);
        
        // 验证标签特定的业务规则
        if (this.validateTagRules(formData, selectedVideos)) {
            console.log('标签编辑页面提交验证通过:', formData);
            
            // 使用通用的提交处理流程
            this.formUtils.executeCommonSubmitFlow(
                formData, 
                selectedVideos,
                '确定要保存标签修改吗？',
                '保存中...',
                '标签信息已成功保存！',
                2000
            );
        } else {
            // 阻止默认提交流程
            detail.preventDefault?.();
        }
    }

    /**
     * 验证标签特定的业务规则
     * 检查标签编辑页面的特殊验证逻辑
     */
    validateTagRules(formData, selectedVideos) {
        let isValid = true;

        // 使用通用的中文名称验证
        const nameValidation = this.formUtils.validateChineseName(formData.name_cn, 1, 20);
        if (!nameValidation.isValid) {
            this.showNotification(nameValidation.message, 'error');
            isValid = false;
        }

        // 使用通用的图标类名验证
        const iconValidation = this.formUtils.validateIconClass(formData.icon_class);
        if (!iconValidation.isValid) {
            this.showNotification(iconValidation.message, 'error');
            isValid = false;
        }

        // 使用通用的英文名称验证
        const englishNameValidation = this.formUtils.validateEnglishName(formData.name_en);
        if (!englishNameValidation.isValid) {
            this.showNotification(englishNameValidation.message, 'error');
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
            tagId: formData.id,
            nameCn: formData.name_cn,
            nameEn: formData.name_en,
            shortDescCn: formData.short_desc_cn,
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
        
        console.log('TagEditManager destroyed');
    }
}

// 页面加载完成后初始化
document.addEventListener('DOMContentLoaded', () => {
    window.tagEditManager = new TagEditManager();
});

// 页面卸载前清理
window.addEventListener('beforeunload', (e) => {
    if (window.tagEditManager?.formUtils?.isModified) {
        e.preventDefault();
        e.returnValue = '您有未保存的更改，确定要离开吗？';
    }
});

// 兼容性：暴露给外部使用的工具函数（保持向后兼容）
window.TagEditForm = {
    updateVideoStats: (selectedVideos) => {
        // 使用通用的统计更新方法，支持大数字格式化
        window.tagEditManager?.formUtils?.updateCommonVideoStats(selectedVideos, '.stats-row .stat-item .stat-value', true);
    },
    showTagPreview: () => {
        window.tagEditManager?.showTagPreview();
    },
    getSelectedVideos: () => {
        return window.tagEditManager?.getSelectedVideos() || [];
    },
    setSelectedVideos: (videoIds) => {
        window.tagEditManager?.setSelectedVideos(videoIds);
    }
};

// 开发调试功能（仅在开发环境使用）
if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
    // 在控制台添加调试命令
    window.debugTagEdit = {
        getSummary: () => window.tagEditManager?.getPageSummary(),
        getFormData: () => window.tagEditManager?.formUtils?.collectFormData(),
        resetForm: () => window.tagEditManager?.formUtils?.resetForm(),
        showTest: (msg, type) => window.tagEditManager?.showNotification(msg || '测试通知', type || 'info'),
        showPreview: () => window.tagEditManager?.showTagPreview()
    };
    
    console.log('标签编辑页面调试工具已加载，使用 debugTagEdit 对象进行调试');
}