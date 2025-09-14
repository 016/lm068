<div class="row">
    <div class="col-12">
        <div class="jumbotron bg-primary text-white p-5 rounded">
            <h1 class="display-4">欢迎来到视频内容网站</h1>
            <p class="lead">这里是展示视频作品的专业平台</p>
            <hr class="my-4">
            <p><?= htmlspecialchars($message ?? '') ?></p>
            <a class="btn btn-light btn-lg" href="/test" role="button">测试页面</a>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <h2>最新视频</h2>
        <?php if (empty($videos)): ?>
            <div class="alert alert-info">
                <h4>暂无视频内容</h4>
                <p>网站正在建设中，敬请期待精彩内容！</p>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($videos as $video): ?>
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($video['title_cn'] ?? $video['title_en']) ?></h5>
                                <p class="card-text"><?= htmlspecialchars($video['short_desc_cn'] ?? $video['short_desc_en']) ?></p>
                                <a href="/videos/<?= $video['id'] ?>" class="btn btn-primary">查看详情</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>