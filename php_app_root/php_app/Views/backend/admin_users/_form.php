<?php
use App\Constants\AdminUserStatus;
use App\Constants\AdminUserRole;
?>
<!-- Shared AdminUser Form Content -->
<div class="form-container">
    <div class="form-header">
        <i class="bi bi-person-gear form-icon"></i>
        <h3>管理员详细信息</h3>
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

        <form id="adminUserEditForm" action="<?= $formAction ?>" method="POST">
            <?php if (!$adminUser->isNew): ?>
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" name="id" id="id" value="<?= htmlspecialchars($adminUser->id) ?>">
            <?php endif; ?>

            <!-- 基本信息 -->
            <div class="form-section">
                <h4 class="form-section-title">
                    <i class="bi bi-info-circle form-section-icon"></i>
                    基本信息
                </h4>

                <div class="row">
                    <?php if (!$adminUser->isNew): ?>
                    <div class="col-md-6 pb-3">
                        <div class="form-group">
                            <label for="adminUserId" class="form-label">管理员ID</label>
                            <input type="text" class="form-control" id="adminUserId" value="#<?= str_pad($adminUser->id, 3, '0', STR_PAD_LEFT) ?>" disabled>
                            <div class="form-text">系统自动生成，不可修改</div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="row">
                    <div class="col-md-6 pb-3">
                        <div class="form-group">
                            <label for="username" class="form-label required">用户名</label>
                            <input type="text" class="form-control <?= !empty($adminUser->errors['username']) ? 'is-invalid' : '' ?>"
                                   id="username" name="username" value="<?= htmlspecialchars($adminUser->username ?? '') ?>"
                                   maxlength="50" required>
                            <?php if (!empty($adminUser->errors['username'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($adminUser->errors['username']) ?></div>
                            <?php endif; ?>
                            <div class="form-text">管理员登录用户名</div>
                        </div>
                    </div>
                    <div class="col-md-6 pb-3">
                        <div class="form-group">
                            <label for="real_name" class="form-label">真实姓名</label>
                            <input type="text" class="form-control <?= !empty($adminUser->errors['real_name']) ? 'is-invalid' : '' ?>"
                                   id="real_name" name="real_name" value="<?= htmlspecialchars($adminUser->real_name ?? '') ?>"
                                   maxlength="50">
                            <?php if (!empty($adminUser->errors['real_name'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($adminUser->errors['real_name']) ?></div>
                            <?php endif; ?>
                            <div class="form-text">管理员真实姓名</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 pb-3">
                        <div class="form-group">
                            <label for="email" class="form-label">邮箱</label>
                            <input type="email" class="form-control <?= !empty($adminUser->errors['email']) ? 'is-invalid' : '' ?>"
                                   id="email" name="email" value="<?= htmlspecialchars($adminUser->email ?? '') ?>"
                                   maxlength="100">
                            <?php if (!empty($adminUser->errors['email'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($adminUser->errors['email']) ?></div>
                            <?php endif; ?>
                            <div class="form-text">管理员邮箱地址</div>
                        </div>
                    </div>
                    <div class="col-md-6 pb-3">
                        <div class="form-group">
                            <label for="phone" class="form-label">电话</label>
                            <input type="text" class="form-control <?= !empty($adminUser->errors['phone']) ? 'is-invalid' : '' ?>"
                                   id="phone" name="phone" value="<?= htmlspecialchars($adminUser->phone ?? '') ?>"
                                   maxlength="20">
                            <?php if (!empty($adminUser->errors['phone'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($adminUser->errors['phone']) ?></div>
                            <?php endif; ?>
                            <div class="form-text">管理员联系电话</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <?php if ($adminUser->isNew): ?>
                    <div class="col-md-6 pb-3">
                        <div class="form-group">
                            <label for="password" class="form-label required">密码</label>
                            <input type="password" class="form-control <?= !empty($adminUser->errors['password']) ? 'is-invalid' : '' ?>"
                                   id="password" name="password" required
                                   placeholder="请输入登录密码">
                            <?php if (!empty($adminUser->errors['password'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($adminUser->errors['password']) ?></div>
                            <?php endif; ?>
                            <div class="form-text">管理员登录密码，至少6个字符</div>
                        </div>
                    </div>
                    <div class="col-md-6 pb-3">
                        <div class="form-group">
                            <label for="confirm_password" class="form-label required">确认密码</label>
                            <input type="password" class="form-control <?= !empty($adminUser->errors['confirm_password']) ? 'is-invalid' : '' ?>"
                                   id="confirm_password" name="confirm_password" required
                                   placeholder="请再次输入密码">
                            <?php if (!empty($adminUser->errors['confirm_password'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($adminUser->errors['confirm_password']) ?></div>
                            <?php endif; ?>
                            <div class="form-text">请再次输入密码以确认</div>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="col-md-6 pb-3">
                        <div class="form-group">
                            <label for="new_password" class="form-label">新密码</label>
                            <input type="password" class="form-control" id="new_password" name="new_password"
                                   placeholder="如需修改密码请输入新密码">
                            <div class="form-text">留空则不修改密码</div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- 权限设置 -->
            <div class="form-section">
                <h4 class="form-section-title">
                    <i class="bi bi-shield-check form-section-icon"></i>
                    权限设置
                </h4>

                <div class="row">
                    <div class="col-md-6 pb-3">
                        <div class="form-group">
                            <label for="role_id" class="form-label required">角色</label>
                            <select class="form-control form-select" id="role_id" name="role_id" required>
                                <option value="<?= AdminUserRole::NORMAL->value ?>" <?= ($adminUser->role_id ?? AdminUserRole::NORMAL->value) === AdminUserRole::NORMAL->value ? 'selected' : '' ?>>普通管理员</option>
                                <option value="<?= AdminUserRole::SUPER_ADMIN->value ?>" <?= ($adminUser->role_id ?? 0) === AdminUserRole::SUPER_ADMIN->value ? 'selected' : '' ?>>超级管理员</option>
                            </select>
                            <div class="form-text">选择管理员角色权限</div>
                        </div>
                    </div>
                    <div class="col-md-6 pb-3">
                        <div class="form-group">
                            <div class="switch-group" id="statusSwitchGroup">
                                <div class="custom-switch tag-edit-switch" id="statusSwitch">
                                    <input type="checkbox" id="status_id" name="status_id" value="<?= AdminUserStatus::ENABLED->value ?>"
                                           <?= ($adminUser->status_id ?? AdminUserStatus::ENABLED->value) ? 'checked' : '' ?>>
                                    <span class="switch-slider"></span>
                                </div>
                                <label for="status_id" class="switch-label">启用状态</label>
                            </div>
                            <div class="form-text">开启后允许登录，关闭后禁止登录</div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!$adminUser->isNew): ?>
            <!-- 时间信息 -->
            <div class="form-section">
                <h4 class="form-section-title">
                    <i class="bi bi-clock form-section-icon"></i>
                    时间信息
                </h4>

                <div class="row">
                    <div class="col-md-6 pb-3">
                        <div class="form-group">
                            <label for="created_at" class="form-label">创建时间</label>
                            <input type="text" class="form-control" id="created_at" name="created_at"
                                   value="<?= htmlspecialchars($adminUser->created_at ?? '') ?>" disabled>
                        </div>
                    </div>
                    <div class="col-md-6 pb-3">
                        <div class="form-group">
                            <label for="last_login_time" class="form-label">最后登录时间</label>
                            <input type="text" class="form-control" id="last_login_time" name="last_login_time"
                                   value="<?= htmlspecialchars($adminUser->last_login_time ?? '-') ?>" disabled>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- 表单操作按钮 -->
            <div class="form-actions">
                <a href="/admin_users" class="btn btn-outline-secondary">
                    <i class="bi bi-x-lg"></i>
                    取消
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i>
                    <?= !$adminUser->isNew ? '保存修改' : '创建管理员' ?>
                </button>
            </div>
        </form>
    </div>
</div>
