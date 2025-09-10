/* Multi-Select Dropdown Component - JavaScript */
/* 独立的多选组件，支持搜索、可配置列数，适用于PHP后端表单 */

class MultiSelectDropdown {
    constructor(container, options = {}) {
        this.container = typeof container === 'string' ? document.querySelector(container) : container;
        this.options = {
            placeholder: options.placeholder || '请选择...',
            maxDisplayItems: options.maxDisplayItems || 3,
            columns: options.columns || 1,
            searchPlaceholder: options.searchPlaceholder || '搜索选项...',
            hiddenInputName: options.hiddenInputName || 'selected_items',
            data: options.data || [],
            selected: options.selected || [],
            allowClear: options.allowClear !== false,
            ...options
        };

        this.selectedItems = [...this.options.selected];
        this.filteredData = [...this.options.data];
        this.isOpen = false;
        
        this.init();
    }

    init() {
        this.render();
        this.bindEvents();
        this.updateDisplay();
    }

    render() {
        this.container.innerHTML = `
            <div class="multi-select-wrapper">
                <!-- 隐藏的input用于表单提交 -->
                <input type="hidden" name="${this.options.hiddenInputName}" value="" class="multi-select-hidden-input">
                
                <!-- 显示区域 -->
                <div class="multi-select-display" tabindex="0" role="combobox" aria-expanded="false">
                    <div class="multi-select-content">
                        <div class="multi-select-tags"></div>
                        <div class="multi-select-placeholder">${this.options.placeholder}</div>
                        <div class="multi-select-count"></div>
                    </div>
                    <div class="multi-select-arrow">
                        <i class="bi bi-chevron-down"></i>
                    </div>
                </div>

                <!-- 下拉面板 -->
                <div class="multi-select-dropdown">
                    <div class="multi-select-search">
                        <div class="multi-select-search-wrapper">
                            <i class="bi bi-search multi-select-search-icon"></i>
                            <input type="text" class="multi-select-search-input" placeholder="${this.options.searchPlaceholder}">
                            ${this.options.allowClear ? '<button type="button" class="multi-select-clear-btn"><i class="bi bi-x"></i></button>' : ''}
                        </div>
                    </div>
                    <div class="multi-select-list" style="--columns: ${this.options.columns}">
                        ${this.renderItems()}
                    </div>
                </div>
            </div>
        `;

        // 获取DOM引用
        this.elements = {
            hiddenInput: this.container.querySelector('.multi-select-hidden-input'),
            display: this.container.querySelector('.multi-select-display'),
            dropdown: this.container.querySelector('.multi-select-dropdown'),
            searchInput: this.container.querySelector('.multi-select-search-input'),
            list: this.container.querySelector('.multi-select-list'),
            tags: this.container.querySelector('.multi-select-tags'),
            placeholder: this.container.querySelector('.multi-select-placeholder'),
            count: this.container.querySelector('.multi-select-count'),
            arrow: this.container.querySelector('.multi-select-arrow'),
            clearBtn: this.container.querySelector('.multi-select-clear-btn')
        };
    }

    renderItems() {
        return this.filteredData.map(item => {
            const isSelected = this.selectedItems.some(selected => selected.id === item.id);
            return `
                <div class="multi-select-item ${isSelected ? 'selected' : ''}" data-id="${item.id}">
                    <label class="multi-select-item-label">
                        <input type="checkbox" class="multi-select-checkbox" ${isSelected ? 'checked' : ''}>
                        <span class="multi-select-item-text">${item.text}</span>
                    </label>
                </div>
            `;
        }).join('');
    }

    bindEvents() {
        // 点击显示区域切换下拉状态
        this.elements.display.addEventListener('click', (e) => {
            e.stopPropagation();
            this.toggle();
        });

        // 键盘导航支持
        this.elements.display.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.toggle();
            } else if (e.key === 'Escape') {
                this.close();
            }
        });

        // 搜索功能
        this.elements.searchInput.addEventListener('input', (e) => {
            this.handleSearch(e.target.value);
        });

        // 清空按钮
        if (this.elements.clearBtn) {
            this.elements.clearBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                this.clearAll();
            });
        }

        // 列表项点击事件
        this.elements.list.addEventListener('click', (e) => {
            const item = e.target.closest('.multi-select-item');
            const checkbox = e.target.closest('.multi-select-checkbox');
            
            if (item) {
                e.preventDefault();
                e.stopPropagation();
                
                const id = item.dataset.id;
                const isCurrentlySelected = this.selectedItems.some(selected => selected.id === id);
                
                // 如果点击的是checkbox，阻止默认行为让我们来处理
                if (checkbox) {
                    checkbox.checked = !isCurrentlySelected;
                }
                
                // 根据当前选中状态来切换
                if (isCurrentlySelected) {
                    this.removeItem(id);
                } else {
                    this.addItem(id);
                }
            }
        });

        // 点击外部关闭下拉
        document.addEventListener('click', (e) => {
            if (!this.container.contains(e.target)) {
                this.close();
            }
        });
    }

    toggle() {
        this.isOpen ? this.close() : this.open();
    }

    open() {
        if (this.isOpen) return;
        
        this.isOpen = true;
        this.elements.dropdown.classList.add('show');
        this.elements.display.setAttribute('aria-expanded', 'true');
        this.elements.arrow.style.transform = 'rotate(180deg)';
        
        // 聚焦搜索框，但不滚动页面
        setTimeout(() => {
            this.elements.searchInput.focus({ preventScroll: true });
        }, 100);

        // 触发自定义事件
        this.container.dispatchEvent(new CustomEvent('multiselect:open', { detail: this }));
    }

    close() {
        if (!this.isOpen) return;
        
        this.isOpen = false;
        this.elements.dropdown.classList.remove('show');
        this.elements.display.setAttribute('aria-expanded', 'false');
        this.elements.arrow.style.transform = 'rotate(0deg)';
        
        // 清空搜索
        this.elements.searchInput.value = '';
        this.handleSearch('');

        // 触发自定义事件
        this.container.dispatchEvent(new CustomEvent('multiselect:close', { detail: this }));
    }

    addItem(id) {
        const dataItem = this.options.data.find(item => item.id === id);
        if (dataItem && !this.selectedItems.some(item => item.id === id)) {
            this.selectedItems.push(dataItem);
            this.updateDisplay();
            this.updateListItem(id, true);
            
            // 触发变更事件
            this.container.dispatchEvent(new CustomEvent('multiselect:change', { 
                detail: { 
                    action: 'add',
                    item: dataItem,
                    selected: [...this.selectedItems],
                    instance: this
                }
            }));
        }
    }

    removeItem(id) {
        this.selectedItems = this.selectedItems.filter(item => item.id !== id);
        this.updateDisplay();
        this.updateListItem(id, false);
        
        // 触发变更事件
        const removedItem = this.options.data.find(item => item.id === id);
        this.container.dispatchEvent(new CustomEvent('multiselect:change', { 
            detail: { 
                action: 'remove',
                item: removedItem,
                selected: [...this.selectedItems],
                instance: this
            }
        }));
    }

    clearAll() {
        this.selectedItems = [];
        this.updateDisplay();
        this.updateAllListItems();
        
        // 触发变更事件
        this.container.dispatchEvent(new CustomEvent('multiselect:change', { 
            detail: { 
                action: 'clear',
                selected: [],
                instance: this
            }
        }));
    }

    updateListItem(id, selected) {
        const item = this.elements.list.querySelector(`[data-id="${id}"]`);
        if (item) {
            const checkbox = item.querySelector('.multi-select-checkbox');
            checkbox.checked = selected;
            item.classList.toggle('selected', selected);
        }
    }

    updateAllListItems() {
        const items = this.elements.list.querySelectorAll('.multi-select-item');
        items.forEach(item => {
            const id = item.dataset.id;
            const selected = this.selectedItems.some(selectedItem => selectedItem.id === id);
            const checkbox = item.querySelector('.multi-select-checkbox');
            checkbox.checked = selected;
            item.classList.toggle('selected', selected);
        });
    }

    updateDisplay() {
        // 更新隐藏input的值
        const selectedIds = this.selectedItems.map(item => item.id);
        this.elements.hiddenInput.value = selectedIds.join(',');

        // 更新显示区域
        if (this.selectedItems.length === 0) {
            this.elements.placeholder.style.display = 'block';
            this.elements.tags.style.display = 'none';
            this.elements.count.style.display = 'none';
        } else {
            this.elements.placeholder.style.display = 'none';
            this.elements.tags.style.display = 'flex';
            
            // 显示选中的标签
            const displayItems = this.selectedItems.slice(0, this.options.maxDisplayItems);
            const remainingCount = this.selectedItems.length - displayItems.length;
            
            this.elements.tags.innerHTML = displayItems.map(item => 
                `<span class="multi-select-tag">
                    <span class="multi-select-tag-text">${item.text}</span>
                    <button type="button" class="multi-select-tag-remove" data-id="${item.id}">
                        <i class="bi bi-x"></i>
                    </button>
                </span>`
            ).join('');
            
            // 显示剩余数量
            if (remainingCount > 0) {
                this.elements.count.style.display = 'block';
                this.elements.count.textContent = `+${remainingCount}`;
            } else {
                this.elements.count.style.display = 'none';
            }
        }

        // 绑定标签删除事件
        this.elements.tags.querySelectorAll('.multi-select-tag-remove').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                this.removeItem(btn.dataset.id);
            });
        });
    }

    handleSearch(query) {
        const lowerQuery = query.toLowerCase();
        
        if (!query.trim()) {
            this.filteredData = [...this.options.data];
        } else {
            this.filteredData = this.options.data.filter(item => 
                item.text.toLowerCase().includes(lowerQuery)
            );
        }
        
        this.elements.list.innerHTML = this.renderItems();
        this.updateAllListItems();

        // 触发搜索事件
        this.container.dispatchEvent(new CustomEvent('multiselect:search', { 
            detail: { query, results: this.filteredData, instance: this }
        }));
    }

    // 公共API方法
    setData(data) {
        this.options.data = data;
        this.filteredData = [...data];
        this.elements.list.innerHTML = this.renderItems();
        this.updateAllListItems();
    }

    setSelected(selected) {
        this.selectedItems = [...selected];
        this.updateDisplay();
        this.updateAllListItems();
    }

    getSelected() {
        return [...this.selectedItems];
    }

    setValue(value) {
        if (typeof value === 'string') {
            const ids = value.split(',').filter(id => id.trim());
            const items = this.options.data.filter(item => ids.includes(item.id));
            this.setSelected(items);
        }
    }

    getValue() {
        return this.selectedItems.map(item => item.id).join(',');
    }

    setColumns(columns) {
        this.options.columns = Math.max(1, Math.min(4, columns));
        this.elements.list.style.setProperty('--columns', this.options.columns);
    }

    enable() {
        this.elements.display.style.pointerEvents = '';
        this.elements.display.style.opacity = '';
        this.container.classList.remove('disabled');
    }

    disable() {
        this.close();
        this.elements.display.style.pointerEvents = 'none';
        this.elements.display.style.opacity = '0.6';
        this.container.classList.add('disabled');
    }

    destroy() {
        // 移除事件监听器
        this.container.innerHTML = '';
        this.container.dispatchEvent(new CustomEvent('multiselect:destroy', { detail: this }));
    }
}

// 全局注册组件
window.MultiSelectDropdown = MultiSelectDropdown;

// 自动初始化带有data-multiselect属性的元素
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-multiselect]').forEach(element => {
        try {
            const options = JSON.parse(element.dataset.multiselect || '{}');
            new MultiSelectDropdown(element, options);
        } catch (error) {
            console.error('MultiSelectDropdown initialization error:', error);
        }
    });
});