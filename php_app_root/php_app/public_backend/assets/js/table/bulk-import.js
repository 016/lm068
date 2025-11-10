/**
 * Bulk Import - 批量导入功能
 *
 * 依赖：Bootstrap 5 (Modal)
 *
 * 提供功能：
 * - CSV文件批量导入
 * - 导入进度显示
 * - 导入结果反馈
 */

(function() {
    'use strict';

function setupBulkImport(config = {}) {
    const defaultConfig = {
        endpoint: '/tags/bulk-import',
        entityName: '标签'
    };
    
    const finalConfig = { ...defaultConfig, ...config };
    
    const bulkImportBtn = document.getElementById('bulkImportBtn');
    const csvFileInput = document.getElementById('csvFileInput');
    const importModal = document.getElementById('importModal');

    if (!bulkImportBtn || !csvFileInput || !importModal) {
        console.warn('Bulk import elements not found, skipping setup');
        return;
    }

    // 批量导入按钮点击事件
    bulkImportBtn.addEventListener('click', function() {
        csvFileInput.click();
    });

    // 文件选择事件
    csvFileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;

        // 验证文件类型
        if (!file.name.toLowerCase().endsWith('.csv')) {
            alert('请选择CSV文件');
            return;
        }

        // 验证文件大小 (限制为10MB)
        if (file.size > 10 * 1024 * 1024) {
            alert('文件大小不能超过10MB');
            return;
        }

        // 开始上传
        uploadCSVFile(file, finalConfig);

        // 清空文件输入框
        csvFileInput.value = '';
    });

    console.log(`Bulk import functionality setup completed for ${finalConfig.entityName}, endpoint: ${finalConfig.endpoint}`);
}

/**
 * 上传CSV文件并处理导入
 * @param {File} file - CSV文件对象
 * @param {Object} config - 配置对象
 */
function uploadCSVFile(file, config = {}) {
    const defaultConfig = {
        endpoint: '/tags/bulk-import',
        entityName: '标签'
    };
    
    const finalConfig = { ...defaultConfig, ...config };
    
    const importModal = new bootstrap.Modal(document.getElementById('importModal'));
    const importProgress = document.getElementById('importProgress');
    const importResult = document.getElementById('importResult');
    const importError = document.getElementById('importError');
    const importResultText = document.getElementById('importResultText');
    const importErrorText = document.getElementById('importErrorText');

    // 重置状态
    importProgress.style.display = 'block';
    importResult.style.display = 'none';
    importError.style.display = 'none';

    // 显示模态框
    importModal.show();

    // 创建FormData
    const formData = new FormData();
    formData.append('csv_file', file);
    formData.append('action', 'bulk_import');

    // 发送Ajax请求
    fetch(finalConfig.endpoint, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        importProgress.style.display = 'none';

        if (data.success) {
            // 显示成功结果
            importResult.style.display = 'block';
            importResult.querySelector('.alert').className = 'alert alert-success';
            importResultText.textContent = `成功${data.success_count || 0}条，失败${data.error_count || 0}条`;

            console.log('CSV import completed successfully:', data);
        } else {
            // 显示错误
            importError.style.display = 'block';
            importErrorText.textContent = data.message || '导入过程中发生未知错误';

            console.error('CSV import failed:', data);
        }
    })
    .catch(error => {
        importProgress.style.display = 'none';
        importError.style.display = 'block';
        importErrorText.textContent = '网络错误或服务器异常，请稍后重试';

        console.error('CSV import request failed:', error);
    });
}

    // ========== GLOBAL EXPORTS ==========
    if (!window.AdminCommon) {
        window.AdminCommon = {};
    }

    if (!window.AdminCommon.BulkImportUtils) {
        window.AdminCommon.BulkImportUtils = {};
    }

    window.AdminCommon.BulkImportUtils.setupBulkImport = setupBulkImport;
    window.AdminCommon.BulkImportUtils.uploadCSVFile = uploadCSVFile;

    console.log('Bulk Import 已加载');
})();
