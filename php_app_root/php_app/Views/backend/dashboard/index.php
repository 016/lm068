<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">数据面板</h1>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card text-white bg-primary mb-3">
            <div class="card-header">总视频数</div>
            <div class="card-body">
                <h4 class="card-title"><?= $stats['total_videos'] ?></h4>
                <p class="card-text">已发布的视频内容</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-success mb-3">
            <div class="card-header">总用户数</div>
            <div class="card-body">
                <h4 class="card-title"><?= $stats['total_users'] ?></h4>
                <p class="card-text">注册用户总数</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-info mb-3">
            <div class="card-header">总评论数</div>
            <div class="card-body">
                <h4 class="card-title"><?= $stats['total_comments'] ?></h4>
                <p class="card-text">用户评论总数</p>
            </div>
        </div>
    </div>
</div>

<div class="alert alert-success" role="alert">
    <h4 class="alert-heading">系统运行正常！</h4>
    <p>PHP应用已成功部署并运行。数据库连接正常，所有核心功能模块已就绪。</p>
    <hr>
    <p class="mb-0">当前时间：<?= date('Y-m-d H:i:s') ?></p>
</div>