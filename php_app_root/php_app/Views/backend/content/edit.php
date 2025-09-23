<?php
$this->extend('layouts/main');
$this->section('title', $pageTitle ?? '编辑内容 - 视频分享网站管理后台');

// 添加页面特定的CSS和JS文件
if (!empty($css_files)) {
    foreach ($css_files as $file) {
        $this->addCSS($file);
    }
}
if (!empty($js_files)) {
    foreach ($js_files as $file) {
        $this->addJS($file);
    }
}
?>

<?php $this->section('content'); ?>
<!-- Content Edit Form Content -->
<main class="dashboard-content">
    <!-- Breadcrumb and Page Title -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-pencil-square page-title-icon"></i>
                <div>
                    <h1 class="page-title">编辑内容</h1>
                    <p class="page-subtitle">Edit Content Information</p>
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
                <li class="breadcrumb-item active breadcrumb-active" aria-current="page">编辑内容</li>
            </ol>
        </nav>
    </div>

    <!-- Content Edit Form -->
    <div class="row justify-content-center">
        <div class="col-12 col-lg-12">
            <?php 
            $formAction = '/content/edit/' . $content->id;
            include __DIR__ . '/_form.php'; 
            ?>
        </div>
    </div>
</main>

<script>
// 将动态数据传递给JS
window.inputData = {
    // 内容标签数据
    tagsList: <?= json_encode(array_map(function($tag) {
        return ['id' => (string)$tag['id'], 'text' => $tag['text']];
    }, $tagOptions ?? [])) ?>,
    selectedTagIds: <?= json_encode(array_map('strval', array_column($relatedTags ?? [], 'id'))) ?>,

    // 内容合集数据
    collectionsList: <?= json_encode(array_map(function($collection) {
        return ['id' => (string)$collection['id'], 'text' => $collection['text']];
    }, $collectionOptions ?? [])) ?>,
    selectedCollectionIds: <?= json_encode(array_map('strval', array_column($relatedCollections ?? [], 'id'))) ?>,
};
</script>

<?php $this->endSection(); ?>