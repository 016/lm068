<?php
/**
 * 评论组件 - 支持递归渲染
 *
 * @var $this \App\Controllers\Frontend\ContentController
 * @var $comment object 评论对象
 * @var $currentLang string 当前语言
 * @var $level int 嵌套层级
 */

$isReply = $level > 0;
$marginClass = $level > 0 ? 'ms-4 mt-3' : '';
$itemClass = $isReply ? 'reply-item' : '';

// 用户头像处理：优先使用用户头像，如果为空则使用默认占位符
$userAvatar = !empty($comment->user_avatar)
    ? htmlspecialchars($comment->user_avatar)
    : 'https://via.placeholder.com/40?text=U';

// 用户名称处理：优先使用昵称，如果为空则使用用户名
$userName = !empty($comment->user_nickname)
    ? htmlspecialchars($comment->user_nickname)
    : (!empty($comment->user_username)
        ? htmlspecialchars($comment->user_username)
        : ($currentLang === 'zh' ? '匿名用户' : 'Anonymous'));
?>
<div class="comment-item <?= $itemClass ?>">
    <div class="comment-avatar">
        <img src="<?= $userAvatar ?>" alt="<?= $currentLang === 'zh' ? '用户头像' : 'User Avatar' ?>">
    </div>
    <div class="comment-content">
        <div class="comment-header">
            <strong><?= $userName ?></strong>
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
                    <?php echo $this->view('contents._comment_item', [
                        'comment' => $reply,
                        'currentLang' => $currentLang,
                        'level' => $level + 1
                    ]); ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
