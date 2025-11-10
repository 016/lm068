/**
 * Table Operations - 表格操作工具类
 *
 * 依赖：admin-notifications.js (reinitializeTooltips)
 *
 * 提供功能：
 * - HTML表格数据加载
 * - 单元格属性保存/恢复
 * - 数据排序
 * - 数据分页
 * - 表格渲染
 * - 数据导出（JSON/CSV）
 * - 批量操作处理
 * - 单项删除处理
 */

(function() {
    'use strict';

const TableOperations = {
    
    // 存储所有 cell 的完整属性信息
    cellAttributeStore: new Map(),
    
    /**
     * 从HTML表格中读取数据并解析列配置
     * 支持任意列结构的表格，自动识别列类型和数据
     * 新增：完整保存每个 cell 的所有 HTML 属性
     * @param {string} tableSelector - 表格选择器，默认为 '#dataTable'
     * @param {string} bodySelector - 表格body选择器，默认为 'tbody'
     * @returns {Object} 包含 columns 和 data 的对象
     */
    loadDataFromHTML: function(tableSelector = '#dataTable', bodySelector = 'tbody') {
        const table = document.querySelector(tableSelector);
        if (!table) {
            console.error('Table not found:', tableSelector);
            return { columns: [], data: [] };
        }

        // 清空之前的属性存储
        this.cellAttributeStore.clear();

        // 获取表头信息
        const headerCells = table.querySelectorAll('thead .table-header th[data-column]');
        const columns = [];
        
        headerCells.forEach(headerCell => {
            const columnId = headerCell.getAttribute('data-column');
            if (columnId === 'checkbox') return; // 跳过checkbox列
            
            const columnName = this.extractColumnName(headerCell, columnId);
            const isSortable = headerCell.querySelector('.sort-icon') !== null;
            
            columns.push({
                id: columnId,
                name: columnName,
                sortable: isSortable,
                type: this.detectColumnType(columnId, headerCell)
            });
        });

        // 获取数据行
        const dataRows = table.querySelectorAll(`${bodySelector} tr.table-row`);
        const data = [];
        
        dataRows.forEach((row, index) => {
            const rowData = { _rowIndex: index, _rowId: row.getAttribute('data-id') };
            
            columns.forEach(column => {
                const cell = row.querySelector(`[data-column="${column.id}"]`);
                if (cell) {
                    // 提取 cell 数据
                    rowData[column.id] = this.extractCellData(cell, column);
                    
                    // 保存 cell 的完整属性信息
                    const cellKey = `${rowData._rowId || index}_${column.id}`;
                    this.saveCellAttributes(cellKey, cell);
                }
            });
            
            data.push(rowData);
        });

        console.log(`Loaded ${data.length} rows with ${columns.length} columns from HTML table`);
        console.log(`Saved attributes for ${this.cellAttributeStore.size} cells`);
        return { columns, data };
    },
    
    /**
     * 保存单个 cell 的所有 HTML 属性和完整内容
     * @param {string} cellKey - cell 的唯一标识
     * @param {Element} cell - cell 元素
     */
    saveCellAttributes: function(cellKey, cell) {
        const cellInfo = {
            // 保存所有 HTML 属性
            attributes: {},
            // 保存完整的 innerHTML
            innerHTML: cell.innerHTML,
            // 保存文本内容
            textContent: cell.textContent,
            // 保存类名
            className: cell.className,
            // 保存内联样式
            style: cell.getAttribute('style') || ''
        };
        
        // 遍历并保存所有属性
        for (let i = 0; i < cell.attributes.length; i++) {
            const attr = cell.attributes[i];
            cellInfo.attributes[attr.name] = attr.value;
        }
        
        // 保存子元素的属性（如果有的话）
        const childElements = cell.querySelectorAll('*');
        if (childElements.length > 0) {
            cellInfo.childElements = [];
            childElements.forEach((child, index) => {
                const childInfo = {
                    tagName: child.tagName,
                    className: child.className,
                    innerHTML: child.innerHTML,
                    textContent: child.textContent,
                    attributes: {}
                };
                
                // 保存子元素的所有属性
                for (let i = 0; i < child.attributes.length; i++) {
                    const attr = child.attributes[i];
                    childInfo.attributes[attr.name] = attr.value;
                }
                
                cellInfo.childElements.push(childInfo);
            });
        }
        
        this.cellAttributeStore.set(cellKey, cellInfo);
        // console.log(`Saved attributes for cell: ${cellKey}`);
    },
    
    /**
     * 获取保存的 cell 属性信息
     * @param {string} cellKey - cell 的唯一标识
     * @returns {Object|null} cell 属性信息
     */
    getCellAttributes: function(cellKey) {
        return this.cellAttributeStore.get(cellKey) || null;
    },
    
    /**
     * 从header cell中提取列名称
     * @param {Element} headerCell - header cell元素
     * @param {string} columnId - 列ID
     * @returns {string} 列名称
     */
    extractColumnName: function(headerCell, columnId) {
        if (columnId === 'actions') {
            return '操作';
        }
        
        // 优先从flexDiv中提取第一个文本内容
        const flexDiv = headerCell.querySelector('.d-flex');
        if (flexDiv) {
            const textContent = flexDiv.textContent.trim();
            return textContent.split('\n')[0].trim();
        }
        
        // 直接获取文本内容，过滤掉图标等元素
        const textNodes = Array.from(headerCell.childNodes).filter(node =>
            node.nodeType === Node.TEXT_NODE && node.textContent.trim()
        );
        
        if (textNodes.length > 0) {
            return textNodes[0].textContent.trim();
        }
        
        return columnId; // 备用方案
    },
    
    /**
     * 检测列的数据类型
     * @param {string} columnId - 列ID
     * @param {Element} headerCell - header元素
     * @returns {string} 数据类型：'number', 'text', 'status_id', 'actions'
     */
    detectColumnType: function(columnId, headerCell) {

        //support define col-type via data-type in html
        let col_type = headerCell.getAttribute('data-type');
        if (col_type !== undefined) {
            return col_type;
        }

        //normal define way
        const typeMapping = {
            'id': 'number',
            'content_cnt': 'number',
            'views': 'number',
            'count': 'number',
            'icon_class': 'icon_class',
            'status_id': 'status',
            'actions': 'actions'
        };
        
        return typeMapping[columnId] || 'text';
    },
    
    /**
     * 从表格单元格中提取数据
     * 增强：保持原有的数据提取逻辑，cell 属性已在 saveCellAttributes 中处理
     * @param {Element} cell - 单元格元素
     * @param {Object} column - 列配置信息
     * @returns {any} 提取的数据
     */
    extractCellData: function(cell, column) {
        const cellType = column.type;
        const cellContent = cell.textContent.trim();
        
        switch (cellType) {
            case 'number':
                // 处理带逗号的数字，如 "1,234"
                const numberMatch = cellContent.match(/[\d,]+/);
                if (numberMatch) {
                    return parseInt(numberMatch[0].replace(/,/g, ''));
                }
                return 0;
                
            case 'status':
                // 提取状态信息
                const badge = cell.querySelector('.badge');
                if (badge) {
                    const statusText = badge.textContent.trim().split(' ').pop();
                    const isActive = badge.classList.contains('badge-success');
                    return {
                        text: statusText,
                        value: isActive ? 'active' : 'inactive',
                        isActive: isActive
                    };
                }
                return { text: cellContent, value: 'unknown', isActive: false };
                
            case 'actions':
                // 对于操作列，返回原始HTML
                return cell.innerHTML;
                
            default:
                return cellContent;
        }
    },
    
    /**
     * 排序功能
     * @param {Array} data - 要排序的数据数组
     * @param {string} field - 排序字段
     * @param {string} direction - 排序方向：'asc' 或 'desc'
     * @param {Object} columns - 列配置信息
     * @returns {Array} 排序后的数据
     */
    sortData: function(data, field, direction, columns) {
        const column = columns.find(col => col.id === field);
        if (!column) return data;
        
        const sortedData = [...data].sort((a, b) => {
            let aVal = a[field];
            let bVal = b[field];
            
            // 根据列类型进行不同的比较
            switch (column.type) {
                case 'number':
                    aVal = typeof aVal === 'number' ? aVal : 0;
                    bVal = typeof bVal === 'number' ? bVal : 0;
                    return direction === 'asc' ? aVal - bVal : bVal - aVal;
                    
                case 'status':
                    aVal = aVal?.text || '';
                    bVal = bVal?.text || '';
                    break;
                    
                default:
                    aVal = String(aVal || '').toLowerCase();
                    bVal = String(bVal || '').toLowerCase();
            }
            
            // 文本比较
            if (aVal < bVal) return direction === 'asc' ? -1 : 1;
            if (aVal > bVal) return direction === 'asc' ? 1 : -1;
            return 0;
        });
        
        console.log(`Sorted ${sortedData.length} rows by ${field} ${direction}`);
        return sortedData;
    },
    
    /**
     * 分页功能
     */
    getPaginatedData: function(data, currentPage = 1, itemsPerPage = 10) {
        const totalItems = data.length;
        const totalPages = Math.ceil(totalItems / itemsPerPage);
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const currentPageData = data.slice(startIndex, endIndex);
        
        return {
            data: currentPageData,
            pagination: {
                currentPage,
                totalPages,
                itemsPerPage,
                totalItems,
                startIndex: startIndex + 1,
                endIndex: Math.min(endIndex, totalItems)
            }
        };
    },
    
    /**
     * 渲染表格数据
     * 新增：完美复原每个 cell 的所有属性和数据
     * @param {Array} data - 要渲染的数据
     * @param {Array} columns - 列配置
     * @param {string} tbodySelector - tbody选择器
     */
    renderTableData: function(data, columns, tbodySelector = '#tagTableBody') {
        const tbody = document.querySelector(tbodySelector);
        if (!tbody) return;
        
        tbody.innerHTML = '';
        
        data.forEach((row, index) => {
            const tr = document.createElement('tr');
            tr.className = 'table-row';
            tr.setAttribute('data-id', row._rowId || row.id || index);
            
            // 添加checkbox列
            const checkboxTd = document.createElement('td');
            checkboxTd.className = 'table-cell';
            checkboxTd.innerHTML = `
                <div class="form-check">
                    <input class="form-check-input row-checkbox" type="checkbox" value="${row._rowId || row.id || index}">
                </div>
            `;
            tr.appendChild(checkboxTd);
            
            // 添加数据列
            columns.forEach(column => {
                const td = document.createElement('td');
                const cellKey = `${row._rowId || row.id || index}_${column.id}`;
                const savedCellInfo = this.getCellAttributes(cellKey);
                
                if (savedCellInfo) {
                    // 完美复原：使用保存的属性信息
                    // console.log(`Restoring cell attributes for: ${cellKey}`);
                    
                    // 恢复所有 HTML 属性
                    Object.entries(savedCellInfo.attributes).forEach(([attrName, attrValue]) => {
                        td.setAttribute(attrName, attrValue);
                    });
                    
                    // 恢复完整的 innerHTML
                    td.innerHTML = savedCellInfo.innerHTML;
                    
                    // 恢复类名（如果不是通过 class 属性设置的话）
                    if (savedCellInfo.className && !savedCellInfo.attributes.class) {
                        td.className = savedCellInfo.className;
                    }
                    
                } else {
                    // 降级方案：使用原有的渲染逻辑
                    console.log(`No saved attributes for ${cellKey}, using fallback rendering`);
                    
                    td.className = 'table-cell';
                    td.setAttribute('data-column', column.id);

                    // 根据列类型渲染不同的内容
                    switch (column.type) {
                        case 'status':
                            const statusData = row[column.id];
                            const badgeClass = statusData?.isActive ? 'badge-success' : 'badge-danger';
                            td.innerHTML = `
                                <span class="badge rounded-pill ${badgeClass}">
                                    <i class="bi bi-circle-fill badge-icon"></i> ${statusData?.text || '未知'}
                                </span>
                            `;
                            break;

                        case 'icon_class':
                            const iconClassString = row[column.id];
                            td.innerHTML = `
                                <div class="icon-class-display"><span class="bi ${iconClassString}"></span> ${iconClassString}</div>
                            `;
                            break;
                            
                        case 'number':
                            if (column.id === 'content_cnt') {
                                td.innerHTML = `
                                    <a href="/videos/index?tag_id=${row["id"]}" class="content-link" target="_blank">${row[column.id]?.toLocaleString() || '0'}</a>
                                `;
                            } else {
                                td.textContent = row[column.id]?.toLocaleString() || '0';
                            }
                            break;
                            
                        case 'actions':
                            td.innerHTML = row[column.id] || '';
                            td.classList.add('table-actions');
                            break;
                            
                        default:
                            td.textContent = row[column.id] || '';
                            if (column.id === 'id') td.classList.add('table-id');
                            if (column.id === 'name') td.classList.add('table-name');
                    }
                }
                
                tr.appendChild(td);
            });
            
            tbody.appendChild(tr);
        });
        
        // 表格重新渲染后，重新初始化所有tooltips - 解决分页排序后tooltip失效问题
        window.AdminCommon.reinitializeTooltips();
        
        console.log(`Rendered ${data.length} rows with restored cell attributes`);
    },
    
    /**
     * 导出数据
     * @param {Array} data - 要导出的数据
     * @param {Array} columns - 列配置
     * @param {string} format - 导出格式：'json' 或 'csv'
     * @param {string} filename - 文件名前缀
     */
    exportData: function(data, columns, format = 'json', filename = 'table_data') {
        const timestamp = new Date().toISOString().split('T')[0];
        const fullFilename = `${filename}_${timestamp}.${format}`;
        
        if (format === 'json') {
            const exportData = data.map(row => {
                const exportRow = {};
                columns.forEach(column => {
                    let value = row[column.id];
                    
                    // 根据列类型处理导出值
                    if (column.type === 'status' && value?.text) {
                        value = value.text;
                    } else if (column.type === 'actions') {
                        value = '操作'; // 操作列不导出具体内容
                    }
                    
                    exportRow[column.name] = value;
                });
                return exportRow;
            });
            
            const jsonData = JSON.stringify(exportData, null, 2);
            const blob = new Blob([jsonData], { type: 'application/json' });
            this.downloadFile(blob, fullFilename);
            
        } else if (format === 'csv') {
            // CSV header
            const csvHeader = columns.map(col => `"${col.name}"`).join(',') + '\n';
            
            // CSV data
            const csvData = data.map(row => {
                return columns.map(column => {
                    let value = row[column.id];
                    
                    // 根据列类型处理CSV值
                    if (column.type === 'status' && value?.text) {
                        value = value.text;
                    } else if (column.type === 'actions') {
                        value = '操作';
                    } else if (typeof value === 'number') {
                        return value; // 数字不加引号
                    }
                    
                    return `"${String(value || '').replace(/"/g, '""')}"`;
                }).join(',');
            }).join('\n');
            
            const csvContent = csvHeader + csvData;
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            this.downloadFile(blob, fullFilename);
        }
    },
    
    /**
     * 下载文件
     * @param {Blob} blob - 文件blob
     * @param {string} filename - 文件名
     */
    downloadFile: function(blob, filename) {
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        a.click();
        URL.revokeObjectURL(url);
        
        console.log(`Downloaded file: ${filename}`);
    },
    
    /**
     * 批量操作处理方法
     * 提供通用的批量操作AJAX请求功能，可配置接口地址和操作类型
     * @param {Object} config - 配置对象
     * @param {string} config.action - 操作类型：'enable', 'disable', 'delete'
     * @param {Array} config.selectedIds - 选中的项目ID数组
     * @param {string} config.endpoint - API端点地址，如 '/tags/bulk-action'
     * @param {string} config.entityName - 实体名称，用于确认提示，如 '标签'
     * @param {Function} config.onSuccess - 成功回调函数
     * @param {Function} config.onError - 错误回调函数
     */
    handleBulkAction: function(config) {
        const {
            action,
            selectedIds,
            endpoint,
            entityName = '项目',
            onSuccess,
            onError
        } = config;
        
        console.log(`批量操作: ${action}，选中${entityName}:`, selectedIds);
        
        if (!selectedIds || selectedIds.length === 0) {
            alert(`请先选择要操作的${entityName}`);
            return;
        }
        
        let actionText = '';
        switch(action) {
            case 'enable':
                actionText = '启用';
                break;
            case 'disable':
                actionText = '禁用';
                break;
            case 'delete':
                actionText = '删除';
                break;
            default:
                actionText = '';
                // alert('不支持的操作');
                // return;
        }
        
        // 删除操作需要确认
        if (action === 'delete' && !confirm(`确定要删除 ${selectedIds.length} 个${entityName}吗？此操作不可撤销。`)) {
            return;
        }
        
        // 发送AJAX请求
        const xhr = new XMLHttpRequest();
        xhr.open('POST', endpoint, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    
                    if (response.success) {
                        // 成功处理
                        const message = response.success_count !== undefined && response.error_count !== undefined 
                            ? `成功${response.success_count}条，失败${response.error_count}条，点击确认后将自动刷新数据。`
                            : `${actionText}操作完成，点击确认后将自动刷新数据。`;
                        
                        alert(message);
                        
                        // 调用成功回调或默认刷新页面
                        if (onSuccess && typeof onSuccess === 'function') {
                            onSuccess(response);
                        } else {
                            window.location.reload();
                        }
                    } else {
                        const errorMessage = `操作失败: ${response.message || '未知错误'}`;
                        
                        // 调用错误回调或默认显示错误
                        if (onError && typeof onError === 'function') {
                            onError(errorMessage, response);
                        } else {
                            alert(errorMessage);
                        }
                    }
                } catch (e) {
                    console.error('响应解析错误:', e);
                    const errorMessage = '操作失败，服务器响应格式错误';
                    
                    if (onError && typeof onError === 'function') {
                        onError(errorMessage, e);
                    } else {
                        alert(errorMessage);
                    }
                }
            }
        };
        
        xhr.onerror = function() {
            const errorMessage = '网络错误，请稍后重试';
            
            if (onError && typeof onError === 'function') {
                onError(errorMessage);
            } else {
                alert(errorMessage);
            }
        };
        
        // 发送请求数据
        const formData = `action=${encodeURIComponent(action)}&ids=${encodeURIComponent(JSON.stringify(selectedIds))}`;
        xhr.send(formData);
    },
    
    /**
     * 单项删除处理方法
     * 提供通用的单项删除AJAX请求功能，可配置接口地址和实体类型
     * @param {Object} config - 配置对象
     * @param {string|number} config.itemId - 要删除的项目ID
     * @param {string} config.endpoint - API端点地址，如 '/tags/{id}'
     * @param {string} config.entityName - 实体名称，用于确认提示，如 '标签'
     * @param {Object} config.tableManager - 表格管理器实例（可选）
     * @param {Function} config.onSuccess - 成功回调函数
     * @param {Function} config.onError - 错误回调函数
     */
    handleSingleDelete: function(config) {
        const {
            itemId,
            endpoint,
            entityName = '项目',
            tableManager,
            onSuccess,
            onError
        } = config;
        
        console.log(`准备删除${entityName}: ${itemId}`);
        
        // 确认删除操作
        if (!confirm(`确定要删除这个${entityName}吗？此操作不可撤销。`)) {
            return;
        }
        
        // 构建删除端点URL
        const deleteUrl = endpoint.replace('{id}', itemId) || `${endpoint}/${itemId}`;
        
        // 发送AJAX删除请求
        const xhr = new XMLHttpRequest();
        xhr.open('DELETE', deleteUrl, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    
                    if (xhr.status === 200 && response.success) {
                        // 删除成功
                        console.log(`${entityName} ${itemId} 删除成功`);
                        
                        // 使用notification系统显示成功消息
                        if (window.AdminCommon && window.AdminCommon.showToast) {
                            window.AdminCommon.showToast(response.message || `${entityName}删除成功`, 'success');
                        }
                        
                        // 调用成功回调或使用默认处理
                        if (onSuccess && typeof onSuccess === 'function') {
                            onSuccess(response, itemId);
                        } else {
                            // 默认处理：从TableManager数据中移除项目并重新渲染
                            if (tableManager && typeof tableManager.removeDataItem === 'function') {
                                tableManager.removeDataItem(itemId);
                            } else {
                                // 备用方案：手动删除表格行并刷新页面
                                const tableRow = document.querySelector(`tr[data-id="${itemId}"]`);
                                if (tableRow) {
                                    tableRow.remove();
                                }
                                setTimeout(() => {
                                    window.location.reload();
                                }, 1000);
                            }
                        }
                        
                    } else {
                        // 删除失败
                        console.error(`${entityName} ${itemId} 删除失败:`, response);
                        const errorMessage = response.message || '删除失败，请稍后重试';
                        
                        // 调用错误回调或默认处理
                        if (onError && typeof onError === 'function') {
                            onError(errorMessage, response);
                        } else {
                            // 使用notification系统显示错误消息
                            if (window.AdminCommon && window.AdminCommon.showToast) {
                                window.AdminCommon.showToast(errorMessage, 'danger');
                            } else {
                                alert(errorMessage);
                            }
                        }
                    }
                    
                } catch (e) {
                    console.error('解析服务器响应时出错:', e);
                    console.error('原始响应:', xhr.responseText);
                    const errorMessage = '删除失败，服务器响应格式错误';
                    
                    // 调用错误回调或默认处理
                    if (onError && typeof onError === 'function') {
                        onError(errorMessage, e);
                    } else {
                        // 使用notification系统显示错误消息
                        if (window.AdminCommon && window.AdminCommon.showToast) {
                            window.AdminCommon.showToast(errorMessage, 'danger');
                        } else {
                            alert(errorMessage);
                        }
                    }
                }
            }
        };
        
        xhr.onerror = function() {
            console.error('删除请求网络错误');
            const errorMessage = '网络错误，请检查网络连接后重试';
            
            // 调用错误回调或默认处理
            if (onError && typeof onError === 'function') {
                onError(errorMessage);
            } else {
                // 使用notification系统显示网络错误消息
                if (window.AdminCommon && window.AdminCommon.showToast) {
                    window.AdminCommon.showToast(errorMessage, 'danger');
                } else {
                    alert(errorMessage);
                }
            }
        };
        
        // 发送请求
        xhr.send();
        
        console.log(`删除请求已发送: DELETE ${deleteUrl}`);
    },
    
    /**
     * 设置删除按钮事件监听器
     * 使用事件委托来处理动态生成的删除按钮
     * @param {Object} config - 配置对象
     * @param {string} config.tableSelector - 表格选择器，如 '#dataTable'
     * @param {string} config.tbodySelector - 表格body选择器，如 '#tagTableBody'
     * @param {string} config.deleteButtonSelector - 删除按钮选择器，如 '.delete-item'
     * @param {string} config.endpoint - 删除API端点，如 '/tags/{id}'
     * @param {string} config.entityName - 实体名称，如 '标签'
     * @param {Object} config.tableManager - 表格管理器实例（可选）
     * @param {Function} config.onSuccess - 成功回调函数（可选）
     * @param {Function} config.onError - 错误回调函数（可选）
     */
    setupDeleteButtonEventListeners: function(config) {
        const {
            tableSelector = '#dataTable',
            tbodySelector,
            deleteButtonSelector,
            endpoint,
            entityName = '项目',
            tableManager,
            onSuccess,
            onError
        } = config;
        
        console.log('设置删除按钮事件监听器...');

        // 确定事件监听的目标元素
        let targetElement;
        if (tbodySelector) {
            targetElement = document.querySelector(tbodySelector);
        } else {
            targetElement = document.querySelector(tableSelector);
        }

        if (!targetElement) {
            console.error('未找到目标元素:', tbodySelector || tableSelector);
            return;
        }

        // 使用事件委托绑定删除按钮点击事件
        targetElement.addEventListener('click', (e) => {
            // 检查点击的元素或其父元素是否有指定的删除按钮类
            const deleteButton = e.target.closest(deleteButtonSelector);
            if (deleteButton) {
                e.preventDefault();
                e.stopPropagation();

                const itemId = deleteButton.getAttribute('data-id');
                if (itemId) {
                    // 调用单项删除方法
                    this.handleSingleDelete({
                        itemId,
                        endpoint,
                        entityName,
                        tableManager,
                        onSuccess,
                        onError
                    });
                }
            }
        });
        
        console.log(`删除按钮事件监听器已设置，目标元素: ${tbodySelector || tableSelector}，按钮选择器: ${deleteButtonSelector}`);
    }
};

    // ========== GLOBAL EXPORTS ==========
    if (!window.AdminCommon) {
        window.AdminCommon = {};
    }

    window.AdminCommon.TableOperations = TableOperations;

    console.log('Table Operations 已加载');
})();
