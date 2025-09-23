<?php
use App\Constants\CollectionStatus;
?>
<!-- Collection Create Form Content -->
<main class="dashboard-content">
    <!-- Breadcrumb and Page Title -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-plus-square page-title-icon"></i>
                <div>
                    <h1 class="page-title">创建合集</h1>
                    <p class="page-subtitle">Create New Collection</p>
                </div>
            </div>
            <a href="/collections" class="back-link">
                <i class="bi bi-arrow-left"></i>
                返回合集列表
            </a>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="/dashboard" class="breadcrumb-link">首页</a></li>
                <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">内容管理</a></li>
                <li class="breadcrumb-item"><a href="/collections" class="breadcrumb-link">合集管理</a></li>
                <li class="breadcrumb-item active breadcrumb-active" aria-current="page">创建合集</li>
            </ol>
        </nav>
    </div>

    <!-- Collection Create Form -->
    <div class="row justify-content-center">
        <div class="col-12 col-lg-12">
            <div class="form-container">
                <div class="form-header">
                    <i class="bi bi-collection form-icon"></i>
                    <h3>新建合集信息</h3>
                </div>
                
                <div class="form-body">
                    <form id="collectionCreateForm" action="/collections/create" method="POST">
                        <?php if (isset($errors) && !empty($errors)): ?>
                            <div class="alert alert-danger mb-4">
                                <h6><i class="bi bi-exclamation-triangle"></i> 请修正以下错误：</h6>
                                <ul class="mb-0">
                                    <?php foreach ($errors as $field => $error): ?>
                                        <li><?= htmlspecialchars($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <?php include '_form.php'; ?>

                        <!-- 表单操作按钮 -->
                        <div class="form-actions">
                            <a href="/collections" id="btn-cancel" class="btn btn-outline-secondary">
                                <i class="bi bi-x-lg"></i>
                                取消
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg"></i>
                                创建合集
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
// 将动态数据传递给JS
window.inputData = {
    contentList: <?= json_encode($contentOptions) ?>,
    selectedContentIds: <?= json_encode($selectedContentIds ?? []) ?>
};
</script>