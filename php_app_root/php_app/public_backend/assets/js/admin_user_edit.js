/**
 * 管理员编辑页面 JavaScript
 * 基于 tag_edit_12.js 修改，使用 form_utils_2.js 的通用表单功能
 * 移除标签特定功能，适配管理员编辑需求
 */

class AdminUserEditManager {
    constructor() {
        this.form = document.getElementById('adminUserEditForm');
        this.formUtils = null;

        this.init();
    }

    /**
     * 初始化管理员编辑页面
     */
    init() {
        // 初始化表单工具（依赖 form_utils_2.js）
        this.initializeFormUtils();

        // 初始化管理员特定功能
        this.initializeAdminUserFeatures();

        console.log('AdminUserEditManager initialized');
    }

    /**
     * 初始化表单工具
     * 使用通用的 FormUtils 类处理表单基础功能
     */
    initializeFormUtils() {
        if (!window.FormUtils) {
            console.error('FormUtils 未找到，请确保已引入 form_utils_2.js');
            return;
        }

        // 创建表单工具实例，启用基础功能（不包括预览，因为管理员编辑不需要）
        this.formUtils = new FormUtils('#adminUserEditForm', {
            enableAutoSave: true,
            enableCharacterCounter: true,
            enableFileUpload: false, // 管理员编辑暂不需要文件上传
            enableNotification: true,
            enablePreview: false // 管理员编辑不需要预览功能
        });
    }

    /**
     * 初始化管理员特定功能
     * 包括密码显示切换、密码强度检测等
     */
    initializeAdminUserFeatures() {
        // 初始化密码显示切换按钮
        this.initPasswordToggle();

        // 初始化密码强度检测
        this.initPasswordStrength();

        // 初始化角色选择变更监听
        this.initRoleChangeListener();

        console.log('Admin user specific features initialized');
    }

    /**
     * 初始化密码显示/隐藏切换功能
     */
    initPasswordToggle() {
        const passwordToggles = document.querySelectorAll('.password-toggle-btn');

        passwordToggles.forEach(toggle => {
            toggle.addEventListener('click', (e) => {
                e.preventDefault();
                const targetId = toggle.dataset.target;
                const passwordInput = document.getElementById(targetId);

                if (passwordInput) {
                    const isPassword = passwordInput.type === 'password';
                    passwordInput.type = isPassword ? 'text' : 'password';

                    const icon = toggle.querySelector('i');
                    if (icon) {
                        icon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
                    }
                }
            });
        });
    }

    /**
     * 初始化密码强度检测
     */
    initPasswordStrength() {
        const passwordInputs = document.querySelectorAll('input[type="password"][name="password"], input[type="password"][name="new_password"]');

        passwordInputs.forEach(input => {
            input.addEventListener('input', (e) => {
                const password = e.target.value;
                this.updatePasswordStrength(password);
            });
        });
    }

    /**
     * 更新密码强度指示器
     * @param {string} password - 密码值
     */
    updatePasswordStrength(password) {
        const strengthBars = document.querySelectorAll('.password-strength-bar');

        if (strengthBars.length === 0) {
            return;
        }

        // 计算密码强度
        let strength = 0;

        if (password.length >= 8) strength++;
        if (password.length >= 12) strength++;
        if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^a-zA-Z0-9]/.test(password)) strength++;

        // 更新强度条
        strengthBars.forEach((bar, index) => {
            bar.classList.remove('weak', 'medium', 'strong');

            if (index < strength) {
                if (strength <= 2) {
                    bar.classList.add('weak');
                } else if (strength <= 3) {
                    bar.classList.add('medium');
                } else {
                    bar.classList.add('strong');
                }
            }
        });
    }

    /**
     * 初始化角色选择变更监听
     * 当角色改变时可以显示相关提示或限制
     */
    initRoleChangeListener() {
        const roleInputs = document.querySelectorAll('input[name="role_id"], select[name="role_id"]');

        roleInputs.forEach(input => {
            input.addEventListener('change', (e) => {
                const roleId = parseInt(e.target.value);
                this.handleRoleChange(roleId);
            });
        });
    }

    /**
     * 处理角色变更
     * @param {number} roleId - 角色ID
     */
    handleRoleChange(roleId) {
        // 可以在这里添加角色变更的特定逻辑
        console.log('Role changed to:', roleId);

        // 例如：显示不同角色的权限说明
        // if (roleId === 99) {
        //     this.showNotification('超级管理员拥有所有权限', 'info');
        // } else if (roleId === 1) {
        //     this.showNotification('普通管理员无法管理其他管理员', 'info');
        // }
    }

    /**
     * 显示通知消息
     * 使用 FormUtils 的通知功能
     */
    showNotification(message, type = 'info') {
        if (this.formUtils) {
            this.formUtils.showNotification(message, type);
        } else {
            // 降级到全局 showToast 函数（如果可用）
            if (typeof showToast === 'function') {
                showToast(message, type);
            } else {
                alert(message);
            }
        }
    }

    /**
     * 验证表单
     * @returns {boolean} - 验证是否通过
     */
    validateForm() {
        if (!this.form) {
            return false;
        }

        // 基础验证
        const requiredFields = this.form.querySelectorAll('[required]');
        let isValid = true;

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('error');
                isValid = false;
            } else {
                field.classList.remove('error');
            }
        });

        // 密码确认验证
        const password = this.form.querySelector('input[name="password"], input[name="new_password"]');
        const confirmPassword = this.form.querySelector('input[name="confirm_password"]');

        if (password && confirmPassword && password.value && password.value !== confirmPassword.value) {
            confirmPassword.classList.add('error');
            this.showNotification('两次输入的密码不一致', 'error');
            isValid = false;
        } else if (confirmPassword) {
            confirmPassword.classList.remove('error');
        }

        return isValid;
    }

    /**
     * 销毁页面管理器
     * 清理页面特定的资源和监听器
     */
    destroy() {
        if (this.formUtils) {
            this.formUtils.destroy();
        }

        console.log('AdminUserEditManager destroyed');
    }
}

// 页面加载完成后初始化
document.addEventListener('DOMContentLoaded', () => {
    window.adminUserEditManager = new AdminUserEditManager();
});

// 兼容性：暴露给外部使用的工具函数（保持向后兼容）
window.AdminUserEditForm = {
    validateForm: () => {
        return window.adminUserEditManager?.validateForm() || false;
    },
    showNotification: (message, type) => {
        window.adminUserEditManager?.showNotification(message, type);
    }
};
