/**
 * Table Manager - 表格管理器
 *
 * 依赖：
 * - table-operations.js (TableOperations)
 * - admin-notifications.js (setupDescriptionTooltips, reinitializeTooltips)
 *
 * 提供功能：
 * - 表格初始化和配置
 * - 列设置管理
 * - 搜索筛选功能
 * - 排序功能
 * - 分页功能
 * - 全选功能
 * - Tooltip配置应用
 */

(function() {
    'use strict';

class TableManager {
    constructor(config = {}) {
        this.config = {
            tableSelector: '#dataTable',
            tbodySelector: '#tagTableBody',
            paginationSelector: '#paginationNav',
            itemsPerPageSelector: '#itemsPerPage',
            selectedCountSelector: '#selectedCount',
            selectAllSelector: '#selectAll',
            rowCheckboxSelector: '.row-checkbox',
            columnSettingsPopupSelector: '#columnSettingsPopup',
            currentDisplaySelector: '#currentDisplay',
            defaultItemsPerPage: 2,
            enableSort: true,
            enablePagination: true,
            enableColumnSettings: true,
            enableSearch: true,
            // tooltip配置支持数组格式
            // 多个配置：tooltipConfig: [{ selector: '[data-column="description"]', maxLength: 20, placement: 'top' }, { selector: '[data-column="title"]', maxLength: 30, placement: 'bottom' }]
            tooltipConfig: null,
            // 多选框列配置数组
            // 格式: [{ columnName: 'status_id', instanceName: 'statusMultiSelectInstance', containerId: 'statusMultiSelect' }]
            multiSelectColumns: [],
            ...config
        };
        
        // 状态变量
        this.currentPage = 1;
        this.itemsPerPage = this.config.defaultItemsPerPage;
        this.originalData = [];
        this.filteredData = [];
        this.displayData = [];
        this.tableColumns = [];
        this.currentSort = { field: '', direction: '' };
        
        console.log('TableManager initialized with config:', this.config);
    }
    
    /**
     * 初始化表格管理器
     */
    init() {
        console.log('=== TableManager 初始化开始 ===');
        
        // 1. 从HTML表格中读取数据和列配置
        const tableData = window.AdminCommon.TableOperations.loadDataFromHTML(this.config.tableSelector, 'tbody');
        this.originalData = tableData.data;
        this.filteredData = [...this.originalData];
        this.tableColumns = tableData.columns;
        
        console.log(`加载了 ${this.originalData.length} 条数据，${this.tableColumns.length} 列`);
        
        // 2. 设置功能模块
        if (this.config.enableColumnSettings) {
            this.setupColumnSettings();
        }
        
        if (this.config.enableSearch) {
            this.setupSearchFunctionality();
        }
        
        if (this.config.enableSort) {
            this.setupSorting();
        }
        
        this.setupItemsPerPage();
        this.setupSelectAll();
        
        // 3. 使用事件委托设置事件监听
        this.setupEventDelegation();
        
        // 4. 初始渲染
        this.updateDisplay();
        
        console.log('=== TableManager 初始化完成 ===');
        return this;
    }
    
    /**
     * 使用事件委托减少事件绑定开销
     */
    setupEventDelegation() {
        const table = document.querySelector(this.config.tableSelector);
        if (!table) return;
        
        // 委托处理排序点击
        table.addEventListener('click', (e) => {
            const sortableHeader = e.target.closest('.sortable-header');
            if (sortableHeader) {
                e.preventDefault();
                e.stopPropagation();
                
                const columnId = sortableHeader.getAttribute('data-column');
                if (columnId) {
                    const nextDirection = this.getNextSortDirection(columnId);
                    this.performSort(columnId, nextDirection);
                }
            }
        });
        
        // 委托处理行选择框变化
        table.addEventListener('change', (e) => {
            if (e.target.classList.contains('row-checkbox')) {
                this.updateSelectedCount();
            }
        });
        
        console.log('事件委托设置完成');
    }
    
    /**
     * 设置列设置功能
     */
    setupColumnSettings() {
        this.generateColumnSettingsFromHeader();
        this.setupColumnVisibility();
        console.log('列设置功能已启用');
    }
    
    /**
     * 动态生成列设置选项
     */
    generateColumnSettingsFromHeader() {
        const headerCells = document.querySelectorAll(`${this.config.tableSelector} .table-header th[data-column]`);
        const columnSettingsPopup = document.querySelector(this.config.columnSettingsPopupSelector);
        
        if (!columnSettingsPopup) return;
        
        columnSettingsPopup.innerHTML = '';
        
        headerCells.forEach(headerCell => {
            const columnId = headerCell.getAttribute('data-column');

            if (columnId === 'checkbox') return;

            const columnName = window.AdminCommon.TableOperations.extractColumnName(headerCell, columnId);

            // 从 data-show 属性读取显示状态，默认为 1（显示）以保证兼容性
            const dataShow = headerCell.getAttribute('data-show');
            const isChecked = dataShow === '0' ? '' : 'checked';

            const checkboxDiv = document.createElement('div');
            checkboxDiv.className = 'popup-checkbox';
            checkboxDiv.innerHTML = `
                <input type="checkbox" id="col-${columnId}" ${isChecked}>
                <label for="col-${columnId}">${columnName}</label>
            `;

            columnSettingsPopup.appendChild(checkboxDiv);
        });
        
        console.log('Generated column settings from table header');
    }
    
    /**
     * 设置列显示/隐藏功能
     */
    setupColumnVisibility() {
        document.querySelectorAll(`${this.config.columnSettingsPopupSelector} input[type="checkbox"]`).forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                const columnId = checkbox.id.replace('col-', '');
                this.toggleColumnVisibility(columnId, checkbox.checked);
            });
        });
    }
    
    /**
     * 切换列的显示/隐藏状态
     */
    toggleColumnVisibility(columnId, isVisible) {
        const columns = document.querySelectorAll(`[data-column="${columnId}"]`);
        columns.forEach(col => {
            col.style.display = isVisible ? '' : 'none';
        });
        
        const specialColumns = {
            'actions': '.table-actions',
            'id': '.table-id',
            'name': '.table-name'
        };
        
        if (specialColumns[columnId]) {
            document.querySelectorAll(specialColumns[columnId]).forEach(cell => {
                cell.style.display = isVisible ? '' : 'none';
            });
        }
    }
    
    /**
     * 设置搜索功能
     */
    setupSearchFunctionality() {
        const filterInputs = document.querySelectorAll(`${this.config.tableSelector} .table-filter-cell input[type="text"]`);
        const filterSelects = document.querySelectorAll(`${this.config.tableSelector} .table-filter-cell select`);
        const filterDates = document.querySelectorAll(`${this.config.tableSelector} .table-filter-cell input[type="date"]`);

        filterInputs.forEach(input => {
            input.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    this.applyFilters();
                }
            });
        });

        filterSelects.forEach(select => {
            select.addEventListener('change', () => {
                this.applyFilters();
            });
        });

        // 为日期输入框添加change事件监听
        filterDates.forEach(dateInput => {
            dateInput.addEventListener('change', () => {
                this.applyFilters();
            });
        });
    }

    /**
     * 应用筛选器
     * 支持自定义保持参数配置,允许保持任意 URL 参数
     * 支持多选组件的筛选参数（通过 multiSelectColumns 配置数组自动处理）
     *
     * 多选框配置说明：
     * - columnName: 列名，对应 data-column 属性的值
     * - instanceName: window 上的多选组件实例名称
     * - containerId: 多选组件容器的 ID
     */
    applyFilters() {
        const filterParams = new URLSearchParams();
        const currentUrl = new URL(window.location);

        const filterInputs = document.querySelectorAll('.table-filter-cell input[type="text"], .table-filter-cell select, .table-filter-cell input[type="date"]');

        filterInputs.forEach(input => {
            const columnName = input.getAttribute('name') || input.closest('[data-column]').getAttribute('data-column');
            const value = input.value.trim();

            if (value && value !== '') {
                filterParams.set(columnName, value);
            }
        });

        // 处理配置的多选组件（自动遍历所有配置的多选列）
        this.config.multiSelectColumns.forEach(multiSelectConfig => {
            const { columnName, instanceName, containerId } = multiSelectConfig;
            const cell = document.querySelector(`.table-filter-cell[data-column="${columnName}"]`);
            if (cell) {
                const container = cell.querySelector(`#${containerId}`);
                if (container && window[instanceName]) {
                    // 获取多选组件的值(逗号分隔的ID字符串)
                    const value = window[instanceName].getValue();
                    if (value && value !== '') {
                        filterParams.set(columnName, value);
                    }
                }
            }
        });

        // 使用公共接口获取需要保持的参数
        const persistentParams = this.getPersistentUrlParams();
        persistentParams.forEach(param => {
            if (currentUrl.searchParams.has(param)) {
                filterParams.set(param, currentUrl.searchParams.get(param));
            }
        });

        const queryString = decodeURIComponent(filterParams.toString());

        const newUrl = queryString ? `${currentUrl.pathname}?${queryString}` : currentUrl.pathname;

        // 对比新旧 URL,如果相同则不执行刷新
        const currentPathAndQuery = `${currentUrl.pathname}${currentUrl.search}`;
        if (newUrl === currentPathAndQuery) {
            console.log('URL 未变化,跳过刷新');
            return;
        }

        console.log('Applying filters with URL:', newUrl);
        window.location.href = newUrl;
    }

    /**
     * 获取需要保持的 URL 参数列表（公共接口）
     * 子类或配置可以重写此方法来扩展保持参数
     * @returns {Array} 需要保持的参数名称数组
     */
    getPersistentUrlParams() {
        // 从配置中读取，如果没有配置则使用默认值
        return this.config.persistentUrlParams || ['page', 'limit', 'tag_id', 'collection_id'];
    }
    
    /**
     * 设置排序功能
     */
    setupSorting() {
        console.log('设置排序功能...');
        
        const sortableHeaders = document.querySelectorAll('.sortable-header');
        sortableHeaders.forEach(header => {
            header.style.cursor = 'pointer';
            const columnId = header.getAttribute('data-column');
            header.title = `点击排序 ${this.getColumnDisplayName(columnId)}`;
        });
        
        console.log(`设置了 ${sortableHeaders.length} 个可排序表头`);
    }
    
    /**
     * 获取列的显示名称
     */
    getColumnDisplayName(columnId) {
        const column = this.tableColumns.find(col => col.id === columnId);
        return column ? column.name : columnId;
    }
    
    /**
     * 获取下一个排序方向
     */
    getNextSortDirection(columnId) {
        // 如果当前排序的列不是这一列，重新开始排序
        if (this.currentSort.field !== columnId) {
            return 'asc';
        }
        
        // 如果是同一列，循环切换方向
        switch (this.currentSort.direction) {
            case '':
            case 'asc':
                return 'desc';
            case 'desc':
                return 'asc';
            default:
                return 'asc';
        }
    }
    
    /**
     * 执行排序
     */
    performSort(columnId, direction) {
        console.log(`执行排序: ${columnId} ${direction}`);
        
        // 更新排序状态
        this.currentSort = { field: columnId, direction };

        // 对过滤后的数据进行排序
        this.filteredData = window.AdminCommon.TableOperations.sortData(this.filteredData, columnId, direction, this.tableColumns);
        
        // 重置到第一页并更新显示
        this.currentPage = 1;
        this.updateDisplay();

        // 更新排序图标状态
        this.updateSortIcons(columnId, direction);
    }
    
    /**
     * 更新排序图标状态
     */
    updateSortIcons(activeField, direction) {
        // 清除所有排序图标的激活状态
        document.querySelectorAll('.sort-icon').forEach(icon => {
            icon.classList.remove('active');
        });
        
        // 激活当前排序的图标
        const activeIcon = document.querySelector(`.sort-icon[data-sort="${activeField}"][data-direction="${direction}"]`);
        if (activeIcon) {
            activeIcon.classList.add('active');
        }
        
        console.log(`排序图标状态更新: ${activeField} ${direction}`);
    }
    
    /**
     * 设置每页条数功能
     */
    setupItemsPerPage() {
        const itemsPerPageSelect = document.querySelector(this.config.itemsPerPageSelector);
        if (!itemsPerPageSelect) return;
        
        // 设置默认值
        itemsPerPageSelect.value = this.itemsPerPage;
        
        itemsPerPageSelect.addEventListener('change', (e) => {
            const newItemsPerPage = parseInt(e.target.value);
            console.log(`每页条数变更: ${this.itemsPerPage} -> ${newItemsPerPage}`);
            
            this.itemsPerPage = newItemsPerPage;
            this.currentPage = 1; // 重置到第一页
            this.updateDisplay();
        });
        
        console.log(`每页条数设置完成，默认值: ${this.itemsPerPage}`);
    }
    
    /**
     * 设置全选功能
     */
    setupSelectAll() {
        const selectAllCheckbox = document.querySelector(this.config.selectAllSelector);
        
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', (e) => {
                const isChecked = e.target.checked;
                const rowCheckboxes = document.querySelectorAll(this.config.rowCheckboxSelector);
                rowCheckboxes.forEach(checkbox => {
                    checkbox.checked = isChecked;
                });
                this.updateSelectedCount();
            });
        }
        
        console.log('全选功能已设置');
    }
    
    /**
     * 更新选中数量
     */
    updateSelectedCount() {
        const selectedRows = document.querySelectorAll(`${this.config.rowCheckboxSelector}:checked`);
        const selectedCountSpan = document.querySelector(this.config.selectedCountSelector);
        const selectAllCheckbox = document.querySelector(this.config.selectAllSelector);
        
        if (selectedCountSpan) {
            selectedCountSpan.textContent = selectedRows.length;
        }
        
        if (selectAllCheckbox) {
            const rowCheckboxes = document.querySelectorAll(this.config.rowCheckboxSelector);
            if (selectedRows.length === 0) {
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.checked = false;
            } else if (selectedRows.length === rowCheckboxes.length) {
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.checked = true;
            } else {
                selectAllCheckbox.indeterminate = true;
            }
        }
    }
    
    /**
     * 更新显示 - 处理分页、渲染表格、更新汇总信息
     * v12 新增：自动应用多列tooltip配置
     */
    updateDisplay() {
        console.log('=== TableManager 更新显示 ===');
        
        // 1. 获取当前页数据
        const paginationResult = window.AdminCommon.TableOperations.getPaginatedData(
            this.filteredData, 
            this.currentPage, 
            this.itemsPerPage
        );
        this.displayData = paginationResult.data;
        const pagination = paginationResult.pagination;
        
        console.log(`当前页: ${pagination.currentPage}/${pagination.totalPages}, 显示: ${pagination.startIndex}-${pagination.endIndex}/${pagination.totalItems}`);
        
        // 2. 渲染表格数据
        window.AdminCommon.TableOperations.renderTableData(this.displayData, this.tableColumns, this.config.tbodySelector);
        
        if (this.config.enablePagination) {
            this.setupPagination(pagination);
        }
        
        // 3. v12 新增：自动应用多列tooltip配置
        this.applyTooltipConfigs();
        
        // 4. 更新汇总信息
        this.updateSummaryInfo(pagination);

        // 5. 重新应用列显示设置
        this.reapplyColumnVisibility();
        
        console.log('=== TableManager 显示更新完成 ===');
    }
    
    /**
     * v12 新增：应用多列tooltip配置
     * 支持单个配置对象或配置数组
     * 在表格更新后自动应用tooltip设置，确保持久化
     */
    applyTooltipConfigs() {
        if (!this.config.tooltipConfig) {
            return;
        }
        
        // 判断是单个配置还是数组配置
        const tooltipConfigs = Array.isArray(this.config.tooltipConfig) 
            ? this.config.tooltipConfig 
            : [this.config.tooltipConfig];
        
        console.log(`应用TableManager tooltip配置，共 ${tooltipConfigs.length} 个配置:`, tooltipConfigs);
        
        // 遍历应用每个tooltip配置
        tooltipConfigs.forEach((config, index) => {
            if (config && config.selector) {
                console.log(`应用第 ${index + 1} 个tooltip配置:`, config);
                
                // 应用tooltip配置，设置reinitialize为true确保清理旧tooltip
                window.AdminCommon.setupDescriptionTooltips({
                    ...config,
                    reinitialize: true
                });
            } else {
                console.warn(`第 ${index + 1} 个tooltip配置无效:`, config);
            }
        });
        
        console.log('TableManager 多列tooltip配置应用完成');
    }
    
    /**
     * 设置分页功能
     */
    setupPagination(pagination) {
        const paginationNav = document.querySelector(this.config.paginationSelector);
        if (!paginationNav) return;
        
        this.renderPagination(paginationNav, pagination.currentPage, pagination.totalPages);
    }
    
    /**
     * 渲染分页组件
     */
    renderPagination(container, currentPage, totalPages) {
        container.innerHTML = '';
        
        if (totalPages <= 1) return;
        
        // Previous button
        const prevBtn = document.createElement('li');
        prevBtn.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
        prevBtn.innerHTML = `<a class="page-link" href="#" aria-label="Previous"><i class="bi bi-chevron-left"></i></a>`;
        prevBtn.addEventListener('click', (e) => {
            e.preventDefault();
            if (currentPage > 1) {
                this.currentPage = currentPage - 1;
                this.updateDisplay();
            }
        });
        container.appendChild(prevBtn);
        
        // Page numbers
        const startPage = Math.max(1, currentPage - 2);
        const endPage = Math.min(totalPages, currentPage + 2);
        
        for (let i = startPage; i <= endPage; i++) {
            const pageBtn = document.createElement('li');
            pageBtn.className = `page-item ${i === currentPage ? 'active' : ''}`;
            pageBtn.innerHTML = `<a class="page-link" href="#" data-page="${i}">${i}</a>`;
            pageBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.currentPage = i;
                this.updateDisplay();
            });
            container.appendChild(pageBtn);
        }
        
        // Next button
        const nextBtn = document.createElement('li');
        nextBtn.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
        nextBtn.innerHTML = `<a class="page-link" href="#" aria-label="Next"><i class="bi bi-chevron-right"></i></a>`;
        nextBtn.addEventListener('click', (e) => {
            e.preventDefault();
            if (currentPage < totalPages) {
                this.currentPage = currentPage + 1;
                this.updateDisplay();
            }
        });
        container.appendChild(nextBtn);
    }
    
    /**
     * 更新汇总信息
     */
    updateSummaryInfo(pagination) {
        // 更新当前显示范围
        const currentDisplay = document.querySelector(this.config.currentDisplaySelector);
        if (currentDisplay) {
            currentDisplay.textContent = `${pagination.startIndex}-${pagination.endIndex}/${pagination.totalItems}`;
        }
        
        console.log(`汇总信息更新: ${currentDisplay?.textContent}`);
    }
    
    /**
     * 重新应用列显示设置
     */
    reapplyColumnVisibility() {
        // 重新应用所有列的显示状态
        document.querySelectorAll(`${this.config.columnSettingsPopupSelector} input[type="checkbox"]`).forEach(checkbox => {
            const columnId = checkbox.id.replace('col-', '');
            this.toggleColumnVisibility(columnId, checkbox.checked);
        });
    }
    
    /**
     * 获取选中的行数据
     */
    getSelectedRows() {
        const selectedCheckboxes = document.querySelectorAll(`${this.config.rowCheckboxSelector}:checked`);
        const selectedIds = Array.from(selectedCheckboxes).map(cb => cb.value);
        return selectedIds;
    }
    
    /**
     * 刷新数据 - 重新加载页面数据
     */
    refresh() {
        console.log('刷新表格数据');
        window.location.href = window.location.origin + window.location.pathname;
    }
    
    /**
     * 从数据中移除指定ID的项目（用于删除操作）
     * @param {string|number} itemId - 要删除的项目ID
     */
    removeDataItem(itemId) {
        console.log(`从TableManager数据中移除项目: ${itemId}`);
        
        // 从原始数据中移除
        this.originalData = this.originalData.filter(item => 
            String(item._rowId || item.id) !== String(itemId)
        );
        
        // 从过滤数据中移除
        this.filteredData = this.filteredData.filter(item => 
            String(item._rowId || item.id) !== String(itemId)
        );
        
        // 检查当前页是否还有数据，如果没有则回到上一页
        const totalPages = Math.ceil(this.filteredData.length / this.itemsPerPage);
        if (this.currentPage > totalPages && totalPages > 0) {
            this.currentPage = totalPages;
        }
        
        console.log(`数据移除完成，剩余数据: ${this.filteredData.length} 条，当前页: ${this.currentPage}`);
        
        // 重新显示数据
        this.updateDisplay();
    }
    
    /**
     * 导出数据
     */
    exportData(format, filename = 'table_data') {
        console.log(`开始导出数据，格式: ${format}, 数据量: ${this.filteredData.length}`);
        window.AdminCommon.TableOperations.exportData(this.filteredData, this.tableColumns, format, filename);
    }
}

    // ========== GLOBAL EXPORTS ==========
    if (!window.AdminCommon) {
        window.AdminCommon = {};
    }

    window.AdminCommon.TableManager = TableManager;

    console.log('Table Manager 已加载');
})();
