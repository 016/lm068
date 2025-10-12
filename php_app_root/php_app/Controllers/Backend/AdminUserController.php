<?php

namespace App\Controllers\Backend;

use App\Core\Request;
use App\Models\AdminUser;
use App\Constants\AdminUserStatus;
use App\Constants\AdminUserRole;

class AdminUserController extends BackendController
{
    public function __construct()
    {
        parent::__construct();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->curModel = new AdminUser();
    }

    /**
     * 权限检查 - 只有超级管理员可以访问管理员管理功能
     */
    protected function checkManagePermission(): bool
    {
        if (!isset($_SESSION['admin_role_id']) || $_SESSION['admin_role_id'] < AdminUserRole::SUPER_ADMIN->value) {
            $this->redirect('/backend');
            return false;
        }
        return true;
    }

    /**
     * 配置权限过滤器
     */
    protected function beforeActionFilters(): array
    {
        return [
            [
                'filter' => 'auth'
            ],
            [
                'filter' => 'method',
                'method' => 'checkManagePermission',
                'except' => ['selfUpdate', 'doSelfUpdate'] // self_update不需要超管权限
            ]
        ];
    }

    public function index(Request $request): void
    {
        // 获取搜索过滤条件
        $filters = $this->getSearchFilters(['id', 'username', 'email', 'real_name', 'phone', 'status_id', 'role_id', 'order_by'], $request);

        // 根据过滤条件获取所有符合条件的管理员数据（不分页，由JS处理分页）
        $adminUsers = AdminUser::findAllWithFilters($filters);
        $stats = $this->curModel->getStats();

        // 处理 Toast 消息
        $toastMessage = $_SESSION['toast_message'] ?? null;
        $toastType = $_SESSION['toast_type'] ?? null;
        if ($toastMessage) {
            unset($_SESSION['toast_message'], $_SESSION['toast_type']);
        }

        $this->render('admin_users/index', [
            'adminUsers' => $adminUsers,
            'filters' => $filters,
            'stats' => $stats,
            'toastMessage' => $toastMessage,
            'toastType' => $toastType,
            'pageTitle' => '管理员管理 - 视频分享网站管理后台',
            'css_files' => ['admin_user_list.css'],
            'js_files' => ['admin_user_list.js']
        ]);
    }

    public function edit(Request $request): void
    {
        $id = (int)$request->getParam(0);

        // 1. 通过ID查找AdminUser实例
        $adminUser = AdminUser::find($id);
        if (!$adminUser) {
            $this->redirect('/admin_users');
            return;
        }

        // 处理 POST 请求（表单提交）
        if ($request->isPost()) {
            $postId = (int)($request->post('id') ?? 0);

            if (!$postId || $postId !== $id) {
                $this->jsonResponse(['success' => false, 'message' => 'Invalid admin user ID']);
                return;
            }

            // 4. 对 POST 的数值进行提取并填充回 $adminUser
            $data = [
                'username' => $request->post('username'),
                'email' => $request->post('email'),
                'real_name' => $request->post('real_name'),
                'phone' => $request->post('phone'),
                'role_id' => (int)($request->post('role_id') ?? AdminUserRole::NORMAL->value),
                'status_id' => (int)($request->post('status_id') ?? AdminUserStatus::DISABLED->value)
            ];

            // 如果提交了新密码，则更新密码
            $newPassword = $request->post('new_password');
            if (!empty($newPassword)) {
                $data['password_hash'] = password_hash($newPassword, PASSWORD_DEFAULT);
            }

            $adminUser->fill($data);

            // 5. 使用 AdminUser 的 validate 对提取的 post 数值进行验证
            if (!$adminUser->validate()) {
                // 6. 如果验证失败，使用 $adminUser->errors 返回给 view
                $this->renderEditForm($adminUser);
                return;
            }

            try {
                // 7. 验证通过，写入数据库
                if ($adminUser->save()) {
                    // 成功后跳转到列表页面
                    $this->setFlashMessage('管理员编辑成功', 'success');
                    $this->redirect('/admin_users');
                } else {
                    // 保存失败，返回编辑页面并显示错误
                    $this->renderEditForm($adminUser);
                }
            } catch (\Exception $e) {
                error_log("AdminUser update error: " . $e->getMessage());
                $adminUser->errors['general'] = '更新失败: ' . $e->getMessage();
                $this->renderEditForm($adminUser);
            }
            return;
        }

        // 2. 把 $adminUser 传递到 view 实现渲染（GET请求 - 显示表单）
        $this->renderEditForm($adminUser);
    }

    private function renderEditForm(AdminUser $adminUser): void
    {
        $this->render('admin_users/edit', [
            'adminUser' => $adminUser,
            'pageTitle' => '编辑管理员 - 视频分享网站管理后台',
            'css_files' => ['admin_user_edit.css'],
            'js_files' => ['form_utils_2.js', 'admin_user_edit.js']
        ]);
    }

    public function create(Request $request): void
    {
        // 1. 创建新的AdminUser实例
        $adminUser = new AdminUser();

        // 处理 POST 请求（表单提交）
        if ($request->isPost()) {
            // 4. 对 POST 的数值进行提取并填充回 $adminUser
            $data = [
                'username' => $request->post('username'),
                'email' => $request->post('email'),
                'real_name' => $request->post('real_name'),
                'phone' => $request->post('phone'),
                'role_id' => (int)($request->post('role_id') ?? AdminUserRole::NORMAL->value),
                'status_id' => (int)($request->post('status_id') ?? AdminUserStatus::ENABLED->value)
            ];

            // 密码是必需的
            $password = $request->post('password');
            if (empty($password)) {
                $adminUser->fill($data);
                $adminUser->errors['password'] = '密码不能为空';
                $this->renderCreateForm($adminUser);
                return;
            }

            $confirmPassword = $request->post('confirm_password');
            if ($password !== $confirmPassword) {
                $adminUser->fill($data);
                $adminUser->errors['confirm_password'] = '两次输入的密码不一致';
                $this->renderCreateForm($adminUser);
                return;
            }

            $data['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
            $adminUser->fill($data);

            // 5. 使用 AdminUser 的 validate 对提取的 post 数值进行验证
            if (!$adminUser->validate()) {
                // 6. 如果验证失败，使用 $adminUser->errors 返回给 view
                $this->renderCreateForm($adminUser);
                return;
            }

            try {
                // 7. 验证通过，写入数据库
                if ($adminUser->save()) {
                    // 成功后跳转到列表页面
                    $this->setFlashMessage('管理员创建成功', 'success');
                    $this->redirect('/admin_users');
                } else {
                    // 保存失败，返回创建页面并显示错误
                    $this->renderCreateForm($adminUser);
                }
            } catch (\Exception $e) {
                error_log("AdminUser creation error: " . $e->getMessage());
                $adminUser->errors['general'] = '创建失败: ' . $e->getMessage();
                $this->renderCreateForm($adminUser);
            }
            return;
        }

        // 2. 把 $adminUser 传递到 view 实现渲染（GET请求 - 显示表单）
        $this->renderCreateForm($adminUser);
    }

    private function renderCreateForm(AdminUser $adminUser): void
    {
        $this->render('admin_users/create', [
            'adminUser' => $adminUser,
            'pageTitle' => '创建管理员 - 视频分享网站管理后台',
            'css_files' => ['admin_user_edit.css'],
            'js_files' => ['form_utils_2.js', 'admin_user_edit.js']
        ]);
    }

    public function destroy(Request $request): void
    {
        $id = (int)$request->getParam(0);

        if (!$id) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid admin user ID']);
            return;
        }

        // 不允许删除当前登录的管理员
        if ($id === $_SESSION['admin_id']) {
            $this->jsonResponse(['success' => false, 'message' => '不能删除当前登录的管理员']);
            return;
        }

        try {
            $this->curModel->delete($id);
            $this->jsonResponse(['success' => true, 'message' => '管理员删除成功']);
        } catch (\Exception $e) {
            error_log("AdminUser deletion error: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => '删除失败: ' . $e->getMessage()]);
        }
    }

    /**
     * 当前登录管理员的自我更新页面（所有管理员都可访问）
     */
    public function selfUpdate(Request $request): void
    {
        $id = (int)$_SESSION['admin_id'];

        // 查找当前登录的管理员
        $adminUser = AdminUser::find($id);
        if (!$adminUser) {
            $this->redirect('/backend');
            return;
        }

        // 处理 POST 请求（表单提交）
        if ($request->isPost()) {
            // 验证原密码（如果要修改密码）
            $oldPassword = $request->post('old_password');
            $newPassword = $request->post('new_password');
            $confirmPassword = $request->post('confirm_password');

            $data = [
                'email' => $request->post('email'),
                'real_name' => $request->post('real_name'),
                'phone' => $request->post('phone')
            ];

            // 如果提供了原密码，则验证并更新密码
            if (!empty($oldPassword)) {
                if (!$adminUser->verifyPassword($adminUser, $oldPassword)) {
                    $adminUser->errors['old_password'] = '原密码错误';
                    $this->renderSelfUpdateForm($adminUser);
                    return;
                }

                if (empty($newPassword)) {
                    $adminUser->errors['new_password'] = '请输入新密码';
                    $this->renderSelfUpdateForm($adminUser);
                    return;
                }

                if ($newPassword !== $confirmPassword) {
                    $adminUser->errors['confirm_password'] = '两次输入的密码不一致';
                    $this->renderSelfUpdateForm($adminUser);
                    return;
                }

                $data['password_hash'] = password_hash($newPassword, PASSWORD_DEFAULT);
            }

            $adminUser->fill($data);

            // 简化验证：只验证非密码字段
            $errors = [];
            if (empty($data['email'])) {
                $errors['email'] = '邮箱不能为空';
            }
            if (!empty($data['email']) && mb_strlen($data['email']) > 100) {
                $errors['email'] = '邮箱不能超过100个字符';
            }

            if (!empty($errors)) {
                $adminUser->errors = $errors;
                $this->renderSelfUpdateForm($adminUser);
                return;
            }

            try {
                if ($adminUser->save()) {
                    $this->setFlashMessage('个人信息更新成功', 'success');
                    $this->redirect('/admin_users/self_update');
                } else {
                    $this->renderSelfUpdateForm($adminUser);
                }
            } catch (\Exception $e) {
                error_log("AdminUser self update error: " . $e->getMessage());
                $adminUser->errors['general'] = '更新失败: ' . $e->getMessage();
                $this->renderSelfUpdateForm($adminUser);
            }
            return;
        }

        // GET请求 - 显示表单
        $this->renderSelfUpdateForm($adminUser);
    }

    private function renderSelfUpdateForm(AdminUser $adminUser): void
    {
        $this->render('admin_users/self_update', [
            'adminUser' => $adminUser,
            'pageTitle' => '个人信息管理 - 视频分享网站管理后台',
            'css_files' => ['admin_user_edit.css'],
            'js_files' => ['form_utils_2.js', 'admin_user_edit.js']
        ]);
    }
}
