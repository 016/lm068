<?php

?>

<!-- 隐藏的文件上传输入框 -->
<input type="file" id="csvFileInput" accept=".csv" style="display: none;">

<!-- 批量导入进度模态框 -->
<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">批量导入</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="importProgress" class="text-center" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">正在处理...</span>
                    </div>
                    <p class="mt-2">正在导入CSV文件，请稍候...</p>
                </div>
                <div id="importResult" style="display: none;">
                    <div class="alert" role="alert">
                        <h6 class="alert-heading">导入结果</h6>
                        <p id="importResultText"></p>
                        <hr>
                        <p class="mb-0">可使用刷新按钮查看新数据</p>
                    </div>
                </div>
                <div id="importError" style="display: none;">
                    <div class="alert alert-danger" role="alert">
                        <h6 class="alert-heading">导入失败</h6>
                        <p id="importErrorText"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">关闭</button>
            </div>
        </div>
    </div>
</div>
