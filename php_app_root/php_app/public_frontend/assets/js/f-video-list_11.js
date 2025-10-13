// f-video-list_11.js - 视频列表页面专用脚本
// 使用后台多选组件替换原有的多选下拉框

document.addEventListener('DOMContentLoaded', function() {
    // 初始化多选组件
    initMultiSelectComponents();
});

/**
 * 初始化多选组件
 * 使用后台的 MultiSelectDropdown 组件
 */
function initMultiSelectComponents() {
    // 检查数据是否存在
    if (!window.videoListData) {
        console.error('视频列表数据未找到');
        return;
    }

    const data = window.videoListData;

    // 初始化标签多选组件
    initTagMultiSelect(data);

    // 初始化合集多选组件
    initCollectionMultiSelect(data);
}

/**
 * 初始化标签多选组件
 */
function initTagMultiSelect(data) {
    // 找到已选中的标签对象
    const selectedTags = data.allTags.filter(tag =>
        data.selectedTagIds.includes(String(tag.id))
    );

    // 创建标签多选组件实例
    const tagSelector = new MultiSelectDropdown('#tagMultiSelect', {
        placeholder: data.placeholders.tag,
        searchPlaceholder: data.placeholders.tagSearch,
        hiddenInputName: 'tag_id',
        maxDisplayItems: 3,
        columns: 2,
        data: data.allTags,
        selected: selectedTags,
        allowClear: true
    });

    // 监听标签变更事件 - 自动提交表单
    document.getElementById('tagMultiSelect').addEventListener('multiselect:change', (e) => {
        // 提交表单以重新加载页面
        submitSearchForm();
    });
}

/**
 * 初始化合集多选组件
 */
function initCollectionMultiSelect(data) {
    // 找到已选中的合集对象
    const selectedCollections = data.allCollections.filter(collection =>
        data.selectedCollectionIds.includes(String(collection.id))
    );

    // 创建合集多选组件实例
    const collectionSelector = new MultiSelectDropdown('#collectionMultiSelect', {
        placeholder: data.placeholders.collection,
        searchPlaceholder: data.placeholders.collectionSearch,
        hiddenInputName: 'collection_id',
        maxDisplayItems: 3,
        columns: 2,
        data: data.allCollections,
        selected: selectedCollections,
        allowClear: true
    });

    // 监听合集变更事件 - 自动提交表单
    document.getElementById('collectionMultiSelect').addEventListener('multiselect:change', (e) => {
        // 提交表单以重新加载页面
        submitSearchForm();
    });
}

/**
 * 提交搜索表单
 * 当多选组件变更时自动提交表单
 */
function submitSearchForm() {
    // 找到表单并提交
    const form = document.querySelector('form[action="/videos"]');
    if (form) {
        // 使用GET方法提交表单
        form.submit();
    }
}
