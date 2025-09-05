// f-video-list_10.js - 视频列表页面专用脚本

document.addEventListener('DOMContentLoaded', function() {
    // 初始化原生多选下拉框
    initCustomMultiselect();
});

// 初始化自定义多选下拉框
function initCustomMultiselect() {
    const multiselects = document.querySelectorAll('.custom-multiselect');
    
    multiselects.forEach(multiselect => {
        const display = multiselect.querySelector('.multiselect-display');
        const dropdown = multiselect.querySelector('.multiselect-dropdown');
        const options = multiselect.querySelectorAll('.dropdown-option');
        const placeholder = multiselect.querySelector('.placeholder-text');
        const arrowIcon = multiselect.querySelector('.arrow-icon');
        
        // 点击显示区域切换下拉菜单
        display.addEventListener('click', function() {
            const isOpen = dropdown.classList.contains('show');
            
            // 关闭所有其他下拉菜单
            document.querySelectorAll('.multiselect-dropdown.show').forEach(d => {
                d.classList.remove('show');
                d.parentElement.querySelector('.multiselect-display').classList.remove('active');
            });
            
            if (!isOpen) {
                dropdown.classList.add('show');
                display.classList.add('active');
            } else {
                dropdown.classList.remove('show');
                display.classList.remove('active');
            }
        });
        
        // 处理选项点击 - 简化逻辑，避免事件冲突
        options.forEach(option => {
            const checkbox = option.querySelector('input[type="checkbox"]');
            
            // 点击整个选项区域都可以切换复选框状态
            option.addEventListener('click', function(e) {
                // 阻止事件冒泡
                e.stopPropagation();
                // 切换选中状态
                checkbox.checked = !checkbox.checked;
                // 更新显示
                updateDisplay(multiselect);
            });
        });
    });
    
    // 点击外部关闭下拉菜单
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.custom-multiselect')) {
            document.querySelectorAll('.multiselect-dropdown.show').forEach(dropdown => {
                dropdown.classList.remove('show');
                dropdown.parentElement.querySelector('.multiselect-display').classList.remove('active');
            });
        }
    });
}

// 更新显示区域
function updateDisplay(multiselect) {
    const display = multiselect.querySelector('.multiselect-display');
    const placeholder = multiselect.querySelector('.placeholder-text');
    const checkboxes = multiselect.querySelectorAll('input[type="checkbox"]:checked');
    const arrowIcon = multiselect.querySelector('.arrow-icon');
    
    // 清空已选中的项目显示 - 修复bug：正确清理selected-items容器
    const existingContainer = display.querySelector('.selected-items');
    if (existingContainer) {
        existingContainer.remove();
    }
    
    if (checkboxes.length > 0) {
        // 隐藏占位符文字
        placeholder.style.display = 'none';
        
        // 创建选中项目容器
        const selectedContainer = document.createElement('div');
        selectedContainer.className = 'selected-items';
        
        const selectedArray = Array.from(checkboxes);
        const maxVisible = 5;
        
        // 显示前5个选中项目
        selectedArray.slice(0, maxVisible).forEach(checkbox => {
            const label = checkbox.parentElement.querySelector('label');
            const selectedItem = document.createElement('span');
            selectedItem.className = 'selected-item';
            selectedItem.innerHTML = `
                ${label.textContent}
                <button type="button" class="remove-btn" data-value="${checkbox.value}">
                    <i class="bi bi-x"></i>
                </button>
            `;
            
            // 添加删除按钮事件
            const removeBtn = selectedItem.querySelector('.remove-btn');
            removeBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                checkbox.checked = false;
                updateDisplay(multiselect);
            });
            
            selectedContainer.appendChild(selectedItem);
        });
        
        // 如果选中数量大于0，显示总数
        if (selectedArray.length > 0) {
            const countSpan = document.createElement('span');
            countSpan.className = 'selected-count';
            countSpan.textContent = `共${selectedArray.length}个`;
            selectedContainer.appendChild(countSpan);
        }
        
        display.insertBefore(selectedContainer, arrowIcon);
    } else {
        // 显示占位符文字
        placeholder.style.display = 'inline';
    }
}