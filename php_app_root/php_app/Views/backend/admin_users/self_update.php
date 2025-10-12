<?php
use App\Constants\AdminUserRole;
?>
<!-- Self Update Form Content -->
<div class="form-container">
    <div class="form-header">
        <i class="bi bi-person-circle form-icon"></i>
        <h3>个人信息管理</h3>
    </div>

    <div class="form-body">
        <?php if (!empty($adminUser->errors)): ?>
        <div class="alert alert-danger mb-4">
            <h6 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> 表单验证失败</h6>
            <ul class="mb-0">
                <?php foreach ($adminUser->errors as $field => $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <form id="selfUpdateForm" action="/admin_users/self_update" method="POST">
            <!-- 基本信息 -->
            <div class="form-section">
                <h4 class="form-section-title">
                    <i class="bi bi-info-circle form-section-icon"></i>
                    基本信息
                </h4>

                <div class="row">
                    <div class="col-md-6 pb-3">
                        <div class="form-group">
                            <label for="username" class="form-label">用户名</label>
                            <input type="text" class="form-control" id="username" value="<?= htmlspecialchars($adminUser->username ?? '') ?>" disabled>
                            <div class="form-text">用户名不可修改</div>
                        </div>
                    </div>
                    <div class="col-md-6 pb-3">
                        <div class="form-group">
                            <label for="role" class="form-label">角色</label>
                            <input type="text" class="form-control" id="role"
                                   value="<?= $adminUser->role_id >= AdminUserRole::SUPER_ADMIN->value ? '超级管理员' : '普通管理员' ?>" disabled>
                            <div class="form-text">角色权限不可修改</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 pb-3">
                        <div class="form-group">
                            <label for="real_name" class="form-label">真实姓名</label>
                            <input type="text" class="form-control <?= !empty($adminUser->errors['real_name']) ? 'is-invalid' : '' ?>"
                                   id="real_name" name="real_name" value="<?= htmlspecialchars($adminUser->real_name ?? '') ?>"
                                   maxlength="50">
                            <?php if (!empty($adminUser->errors['real_name'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($adminUser->errors['real_name']) ?></div>
                            <?php endif; ?>
                            <div class="form-text">您的真实姓名</div>
                        </div>
                    </div>
                    <div class="col-md-6 pb-3">
                        <div class="form-group">
                            <label for="email" class="form-label required">邮箱</label>
                            <input type="email" class="form-control <?= !empty($adminUser->errors['email']) ? 'is-invalid' : '' ?>"
                                   id="email" name="email" value="<?= htmlspecialchars($adminUser->email ?? '') ?>"
                                   maxlength="100" required>
                            <?php if (!empty($adminUser->errors['email'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($adminUser->errors['email']) ?></div>
                            <?php endif; ?>
                            <div class="form-text">您的邮箱地址</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 pb-3">
                        <div class="form-group">
                            <label for="phone" class="form-label">电话</label>
                            <input type="text" class="form-control <?= !empty($adminUser->errors['phone']) ? 'is-invalid' : '' ?>"
                                   id="phone" name="phone" value="<?= htmlspecialchars($adminUser->phone ?? '') ?>"
                                   maxlength="20">
                            <?php if (!empty($adminUser->errors['phone'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($adminUser->errors['phone']) ?></div>
                            <?php endif; ?>
                            <div class="form-text">您的联系电话</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 密码修改 -->
            <div class="form-section">
                <h4 class="form-section-title">
                    <i class="bi bi-key form-section-icon"></i>
                    密码修改
                </h4>

                <div class="alert alert-info mb-3">
                    <i class="bi bi-info-circle"></i>
                    如需修改密码，请填写以下三个字段。如果不修改密码，请留空。
                </div>

                <div class="row">
                    <div class="col-md-12 pb-3">
                        <div class="form-group">
                            <label for="old_password" class="form-label">原密码</label>
                            <input type="password" class="form-control <?= !empty($adminUser->errors['old_password']) ? 'is-invalid' : '' ?>"
                                   id="old_password" name="old_password" placeholder="请输入当前密码">
                            <?php if (!empty($adminUser->errors['old_password'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($adminUser->errors['old_password']) ?></div>
                            <?php endif; ?>
                            <div class="form-text">修改密码前需要验证原密码</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 pb-3">
                        <div class="form-group">
                            <label for="new_password" class="form-label">新密码</label>
                            <input type="password" class="form-control <?= !empty($adminUser->errors['new_password']) ? 'is-invalid' : '' ?>"
                                   id="new_password" name="new_password" placeholder="请输入新密码">
                            <?php if (!empty($adminUser->errors['new_password'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($adminUser->errors['new_password']) ?></div>
                            <?php endif; ?>
                            <div class="form-text">请输入新密码</div>
                        </div>
                    </div>
                    <div class="col-md-6 pb-3">
                        <div class="form-group">
                            <label for="confirm_password" class="form-label">确认新密码</label>
                            <input type="password" class="form-control <?= !empty($adminUser->errors['confirm_password']) ? 'is-invalid' : '' ?>"
                                   id="confirm_password" name="confirm_password" placeholder="请再次输入新密码">
                            <?php if (!empty($adminUser->errors['confirm_password'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($adminUser->errors['confirm_password']) ?></div>
                            <?php endif; ?>
                            <div class="form-text">请再次输入新密码以确认</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 表单操作按钮 -->
            <div class="form-actions">
                <a href="/backend" class="btn btn-outline-secondary">
                    <i class="bi bi-x-lg"></i>
                    取消
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i>
                    保存修改
                </button>
            </div>
        </form>
    </div>
</div>
