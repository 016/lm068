<?php
/**
 * 评论组件 - 支持递归渲染
 *
 * @var $this \App\Controllers\Frontend\VideoController
 * @var $comment object 评论对象
 * @var $currentLang string 当前语言
 * @var $level int 嵌套层级
 */

$isReply = $level > 0;
$marginClass = $level > 0 ? 'ms-4 mt-3' : '';
$itemClass = $isReply ? 'reply-item' : '';
?>
<div class="comment-item <?= $itemClass ?>">
    <div class="comment-avatar">
        <img src="https://via.placeholder.com/40?text=U" alt="<?= $currentLang === 'zh' ? '用户头像' : 'User Avatar' ?>">
    </div>
    <div class="comment-content">
        <div class="comment-header">
            <strong><?= $currentLang === 'zh' ? '用户' : 'User' ?> #<?= $comment->user_id ?></strong>
            <small class="text-muted ms-2"><?= date('Y-m-d H:i', strtotime($comment->created_at)) ?></small>
        </div>
        <div class="comment-text">
            <?= nl2br(htmlspecialchars($comment->content)) ?>
        </div>
        <div class="comment-actions">
            <button class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-reply me-1"></i><?= $currentLang === 'zh' ? '回复' : 'Reply' ?>
            </button>
            <button class="btn btn-outline-primary btn-sm">
                <i class="bi bi-hand-thumbs-up me-1"></i>0
            </button>
        </div>

        <?php if (!empty($comment->replies)): ?>
            <!-- 渲染子回复 -->
            <div class="comment-replies <?= $marginClass ?>">
                <?php foreach ($comment->replies as $reply): ?>
                    <?php echo $this->view('videos._comment_item', [
                        'comment' => $reply,
                        'currentLang' => $currentLang,
                        'level' => $level + 1
                    ]); ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
