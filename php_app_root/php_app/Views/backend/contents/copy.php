<!-- Content Copy Form Content -->
<main class="dashboard-content">
    <!-- Breadcrumb and Page Title -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-copy page-title-icon"></i>
                <div>
                    <h1 class="page-title">复制内容</h1>
                    <p class="page-subtitle">Copy Content Information</p>
                </div>
            </div>
            <a href="/content" class="back-link">
                <i class="bi bi-arrow-left"></i>
                返回内容列表
            </a>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="/dashboard" class="breadcrumb-link">首页</a></li>
                <li class="breadcrumb-item"><a href="/content" class="breadcrumb-link">内容管理</a></li>
                <li class="breadcrumb-item active breadcrumb-active" aria-current="page">复制内容</li>
            </ol>
        </nav>
    </div>

    <!-- Source Content Info Alert -->
    <div class="alert alert-info mb-4">
        <div class="d-flex align-items-start gap-3">
            <i class="bi bi-info-circle-fill fs-4"></i>
            <div>
                <h6 class="alert-heading mb-2">正在复制内容</h6>
                <p class="mb-1">
                    <strong>源内容ID:</strong> #<?= str_pad($sourceContent->id, 3, '0', STR_PAD_LEFT) ?>
                </p>
                <p class="mb-1">
                    <strong>源标题:</strong> <?= htmlspecialchars($sourceContent->title_cn ?: $sourceContent->title_en) ?>
                </p>
                <p class="mb-0">
                    <strong>提示:</strong> 下方表单已自动填充源内容的数据（缩略图除外），您可以进行修改后保存为新内容。
                </p>
            </div>
        </div>
    </div>

    <!-- Content Copy Form -->
    <div class="row justify-content-center">
        <div class="col-12 col-lg-12">
            <?php
            $formAction = "/contents/{$sourceContent->id}/copy";
            $isCopyMode = true;  // 标记为复制模式
            include __DIR__ . '/_form.php';
            ?>
        </div>
    </div>
</main>
