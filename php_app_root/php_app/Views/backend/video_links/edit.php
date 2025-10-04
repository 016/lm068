<!-- Video Link Edit Form Content -->
<main class="dashboard-content">
    <!-- Breadcrumb and Page Title -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-pencil-square page-title-icon"></i>
                <div>
                    <h1 class="page-title">编辑视频链接</h1>
                    <p class="page-subtitle">Edit Video Link Information</p>
                </div>
            </div>
            <a href="/video-links" class="back-link">
                <i class="bi bi-arrow-left"></i>
                返回链接列表
            </a>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom">
                <li class="breadcrumb-item"><a href="/dashboard" class="breadcrumb-link">首页</a></li>
                <li class="breadcrumb-item"><a href="/video-links" class="breadcrumb-link">视频链接</a></li>
                <li class="breadcrumb-item active breadcrumb-active" aria-current="page">编辑链接</li>
            </ol>
        </nav>
    </div>

    <!-- Video Link Edit Form -->
    <div class="row justify-content-center">
        <div class="col-12 col-lg-12">
            <?php
            $formAction = "/video-links/{$videoLink->id}/edit";
            include __DIR__ . '/_form.php';
            ?>
        </div>
    </div>
</main>
