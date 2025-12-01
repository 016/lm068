<?php
use App\Constants\AdminUserStatus;
use App\Constants\AdminUserRole;
use App\Helpers\FormFieldBuilder;
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
                        <?= FormFieldBuilder::for($adminUser, 'id')
                            ->label('管理员ID')
                            ->disabled()
                            ->formatter(fn($v) => '#' . str_pad($v, 3, '0', STR_PAD_LEFT))
                            ->helpText('系统自动生成，不可修改')
                            ->render() ?>
                    <?php endif; ?>
                </div>

                <div class="row">
                    <?= FormFieldBuilder::for($adminUser, 'username')->label('用户名')->render() ?>
                    <?= FormFieldBuilder::for($adminUser, 'real_name')->label('真实姓名')->render() ?>
                </div>

                <div class="row">
                    <?= FormFieldBuilder::for($adminUser, 'email')->type('email')->label('邮箱')->render() ?>
                    <?= FormFieldBuilder::for($adminUser, 'phone')->label('电话')->render() ?>
                </div>

                <div class="row">
                    <?php if ($adminUser->isNew): ?>
                        <?= FormFieldBuilder::for($adminUser, 'password')
                            ->type('password')
                            ->label('密码')
                            ->placeholder('请输入登录密码')
                            ->render() ?>
                        
                        <?= FormFieldBuilder::for($adminUser, 'confirm_password')
                            ->type('password')
                            ->label('确认密码')
                            ->placeholder('请再次输入密码')
                            ->helpText('请再次输入密码以确认')
                            ->render() ?>
                    <?php else: ?>
                        <?= FormFieldBuilder::for($adminUser, 'new_password')
                            ->type('password')
                            ->label('新密码')
                            ->placeholder('如需修改密码请输入新密码')
                            ->helpText('留空则不修改密码')
                            ->render() ?>
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
                    <?= FormFieldBuilder::for($adminUser, 'role_id')
                        ->type('select')
                        ->label('角色')
                        ->options([
                            AdminUserRole::NORMAL->value => '普通管理员',
                            AdminUserRole::SUPER_ADMIN->value => '超级管理员'
                        ])
                        ->render() ?>
                    
                    <?= FormFieldBuilder::for($adminUser, 'status_id')
                        ->type('switch')
                        ->label('启用状态')
                        ->value(AdminUserStatus::ENABLED->value)
                        ->render() ?>
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
                    <?= FormFieldBuilder::for($adminUser, 'created_at')->label('创建时间')->disabled()->render() ?>
                    <?= FormFieldBuilder::for($adminUser, 'last_login_time')->label('最后登录时间')->disabled()->render() ?>
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
