<?php

namespace App\Controllers\Backend;

use App\Controllers\Backend\BackendController;
use App\Core\Request;
use App\Models\AdminUser;
use App\Constants\AdminStatus;

class AuthController extends BackendController
{
    private AdminUser $adminUser;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->adminUser = new AdminUser();
    }

    protected function beforeActionFilters(): array
    {
        return [];
    }

    /**
     * 显示登录页面
     */
    public function showLogin(Request $request): void
    {
        // 检查是否已经登录
        if (isset($_SESSION['admin_id'])) {
            header('Location: /dashboard');
            exit;
        }

        $this->render('auth/login', [
            'title' => '管理员登录',
            'errors' => $_SESSION['login_errors'] ?? [],
            'username' => $_SESSION['login_username'] ?? ''
        ], false); // 不使用布局

        // 清除错误信息和表单数据
        unset($_SESSION['login_errors']);
        unset($_SESSION['login_username']);
    }

    /**
     * 处理登录请求
     */
    public function login(Request $request): void
    {
        $data = $request->getBody();
        
        $username = trim($data['username'] ?? '');
        $password = $data['password'] ?? '';
        $rememberMe = isset($data['rememberMe']);

        $errors = [];

        // 验证输入
        if (empty($username)) {
            $errors['username'] = '用户名或邮箱不能为空';
        } elseif (!filter_var($username, FILTER_VALIDATE_EMAIL) && strlen($username) < 3) {
            $errors['username'] = '请输入有效的邮箱地址或用户名';
        }

        if (empty($password)) {
            $errors['password'] = '密码不能为空';
        } elseif (strlen($password) < 5) {
            $errors['password'] = '密码长度不能少于6位';
        }

        // 如果有基础验证错误，返回表单
        if (!empty($errors)) {
            $_SESSION['login_errors'] = $errors;
            $_SESSION['login_username'] = $username;
            header('Location: /login');
            exit;
        }

        // 查找管理员
        $admin = $this->adminUser->findByUsername($username);
        
        if (!$admin) {
            $errors['username'] = '用户名或邮箱不存在';
            $_SESSION['login_errors'] = $errors;
            $_SESSION['login_username'] = $username;
            header('Location: /login');
            exit;
        }

        // 验证密码
        if (!$this->adminUser->verifyPassword($admin, $password)) {
            $errors['password'] = '密码错误，请重新输入';
            $_SESSION['login_errors'] = $errors;
            $_SESSION['login_username'] = $username;
            header('Location: /login');
            exit;
        }

        // 检查账户状态
        if ($admin['status_id'] != AdminStatus::ENABLED->value) {
            $errors['username'] = '账户已被禁用，请联系系统管理员';
            $_SESSION['login_errors'] = $errors;
            $_SESSION['login_username'] = $username;
            header('Location: /login');
            exit;
        }

        // 登录成功，设置会话
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        $_SESSION['admin_email'] = $admin['email'];
        $_SESSION['admin_real_name'] = $admin['real_name'];
        $_SESSION['admin_role_id'] = $admin['role_id'];

        // 设置记住我功能
        if ($rememberMe) {
            $config = require __DIR__ . '/../../config/main.php';
            $rememberConfig = $config['remember_me'];

            if ($rememberConfig['enabled']) {
                // 生成记住我的 token
                $token = $this->adminUser->generateRememberToken();

                // 保存 token 到数据库
                $this->adminUser->saveRememberToken($admin['id'], $token);

                // 设置 Cookie
                setcookie(
                    $rememberConfig['cookie_name'],
                    $token,
                    time() + $rememberConfig['cookie_lifetime'],
                    $rememberConfig['cookie_path'],
                    '', // domain (空字符串表示当前域)
                    $rememberConfig['cookie_secure'],
                    $rememberConfig['cookie_httponly']
                );
            }
        }

        // 更新最后登录信息
        $this->adminUser->updateLoginInfo($admin['id'], $request->getIp());

        // 重定向到后台首页
        header('Location: /dashboard');
        exit;
    }

    /**
     * 处理退出登录
     */
    public function logout(Request $request): void
    {
        // 清除数据库中的 remember token
        if (isset($_SESSION['admin_id'])) {
            $this->adminUser->clearRememberToken($_SESSION['admin_id']);
        }

        // 清除所有会话数据
        $_SESSION = [];

        // 删除会话cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // 删除记住我cookie
        $config = require __DIR__ . '/../../config/main.php';
        if (isset($_COOKIE[$config['remember_me']['cookie_name']])) {
            setcookie(
                $config['remember_me']['cookie_name'],
                '',
                time() - 3600,
                $config['remember_me']['cookie_path']
            );
        }

        // 销毁会话
        session_destroy();

        // 重定向到登录页面
        header('Location: /login');
        exit;
    }
}