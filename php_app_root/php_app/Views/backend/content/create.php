<!-- Content Create Form Content -->
<main class="dashboard-content">
    <!-- Breadcrumb and Page Title -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-plus-square page-title-icon"></i>
                <div>
                    <h1 class="page-title">创建内容</h1>
                    <p class="page-subtitle">Create New Content</p>
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
                <li class="breadcrumb-item active breadcrumb-active" aria-current="page">创建内容</li>
            </ol>
        </nav>
    </div>

    <!-- Content Create Form -->
    <div class="row justify-content-center">
        <div class="col-12 col-lg-12">
            <?php 
            $formAction = '/content/create';
            include __DIR__ . '/_form.php'; 
            ?>
        </div>
    </div>
</main>

<script>
// 将动态数据传递给JS
window.inputData = {
    // 内容标签数据
    tagsList: <?= json_encode($tagData ?? []) ?>,
    selectedTagIds: <?= json_encode($selectedTagIds ?? []) ?>,

    // 内容合集数据
    collectionsList: <?= json_encode($collectionData ?? []) ?>,
    selectedCollectionIds: <?= json_encode($selectedCollectionIds ?? []) ?>,
};
</script>

