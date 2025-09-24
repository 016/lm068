/**
 * 内容编辑页面 JavaScript - 第11版
 * 基于 content_edit_10.js 重构，移除form通用功能到独立文件
 * 仅保留页面特定的业务逻辑和多选组件配置
 */

class ContentEditManager {
    constructor() {
        this.form = document.getElementById('contentEditForm');
        this.formUtils = null;
        
        this.init();
    }

    /**
     * 初始化内容编辑页面
     */
    init() {
        // 初始化表单工具（依赖 form_utils_1.js）
        this.initializeFormUtils();
        
        // 初始化页面特定的多选组件
        this.initializePageMultiSelects();
        
        console.log('ContentEditManager initialized');
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
        this.formUtils = new FormUtils('#contentEditForm', {
            enableAutoSave: false, // 暂时禁用自动保存
            enableCharacterCounter: true,
            enableFileUpload: true,
            enableNotification: true
        });
    }

    // 读取 PHP 填充数据。
    tagsList = window.inputData.tagsList;
    selectedTagIds = window.inputData.selectedTagIds;
    collectionsList = window.inputData.collectionsList;
    selectedCollectionIds = window.inputData.selectedCollectionIds;

    /**
     * 初始化页面特定的多选组件
     * 配置标签和合集的多选下拉组件
     */
    initializePageMultiSelects() {
        if (!this.formUtils) {
            console.error('FormUtils 未初始化，无法创建多选组件');
            return;
        }


        const selectedTags = this.tagsList.filter(tag => this.selectedTagIds.includes(tag.id));
        // 初始化标签多选组件
        const tagsInstance = this.formUtils.initializeMultiSelect('tags', {
            container: '#contentTagsMultiSelect',
            placeholder: '选择内容标签...',
            searchPlaceholder: '搜索标签...',
            hiddenInputName: 'tag_ids',
            maxDisplayItems: 5,
            columns: 2,
            data: this.tagsList,
            selected: selectedTags,
            allowClear: true
        });

        const selectedCollections = this.collectionsList.filter(collection => this.selectedCollectionIds.includes(collection.id));
        // 初始化合集多选组件
        const collectionsInstance = this.formUtils.initializeMultiSelect('collections', {
            container: '#contentCollectionsMultiSelect',
            placeholder: '选择内容合集...',
            searchPlaceholder: '搜索合集...',
            hiddenInputName: 'collection_ids',
            maxDisplayItems: 5,
            columns: 2,
            data: this.collectionsList,
            selected: selectedCollections,
            allowClear: true
        });

        // 绑定标签变更的特定处理
        if (tagsInstance) {
            document.getElementById('contentTagsMultiSelect').addEventListener('multiselect:change', (e) => {
                this.handleTagsChange(e.detail);
            });
        }

        // 绑定合集变更的特定处理
        if (collectionsInstance) {
            document.getElementById('contentCollectionsMultiSelect').addEventListener('multiselect:change', (e) => {
                this.handleCollectionsChange(e.detail);
            });
        }
    }

    /**
     * 处理标签选择变更
     * 在标签选择变化时显示相应的通知信息
     */
    handleTagsChange(detail) {
        const { action, item, selected } = detail;
        
        switch (action) {
            case 'add':
                this.showNotification(`已添加标签: ${item.text}`, 'success');
                break;
            case 'remove':
                this.showNotification(`已移除标签: ${item.text}`, 'info');
                break;
            case 'clear':
                this.showNotification('已清空所有标签', 'warning');
                break;
        }

        // 标签数量限制提示
        if (selected.length > 10) {
            this.showNotification('建议选择的标签数量不超过10个，以便更好的分类效果', 'warning');
        }
    }

    /**
     * 处理合集选择变更
     * 在合集选择变化时显示相应的通知信息
     */
    handleCollectionsChange(detail) {
        const { action, item, selected } = detail;
        
        switch (action) {
            case 'add':
                this.showNotification(`已添加到合集: ${item.text}`, 'success');
                break;
            case 'remove':
                this.showNotification(`已从合集移除: ${item.text}`, 'info');
                break;
            case 'clear':
                this.showNotification('已清空所有合集', 'warning');
                break;
        }

        // 合集数量限制提示
        if (selected.length > 5) {
            this.showNotification('建议一个内容不要加入超过5个合集', 'warning');
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
     * 销毁页面管理器
     * 清理页面特定的资源和监听器
     */
    destroy() {
        if (this.formUtils) {
            this.formUtils.destroy();
        }
        
        console.log('ContentEditManager destroyed');
    }
}

// 页面加载完成后初始化
document.addEventListener('DOMContentLoaded', () => {
    window.contentEditManager = new ContentEditManager();
});
