<?php

namespace App\Models;

use App\Core\Model;
use App\Constants\AdminUserStatus;
use App\Constants\AdminUserRole;
use App\Interfaces\HasStatuses;

class AdminUser extends Model implements HasStatuses
{

    protected static string $table = 'admin_user';
    protected $primaryKey = 'id';
    protected $fillable = [
        'default' => [
            'username', 'password_hash', 'email', 'real_name',
            'avatar', 'phone', 'status_id', 'role_id',
            'last_login_time', 'last_login_ip', 'remember_token'
        ]
    ];
    protected $timestamps = true;

    // 默认属性值
    protected array $defaults = [
        'username' => '',
        'password_hash' => '',
        'email' => '',
        'real_name' => '',
        'avatar' => '',
        'phone' => '',
        'status_id' => 1,
        'role_id' => 1,
        'last_login_time' => null,
        'last_login_ip' => ''
    ];

    public function __construct()
    {
        parent::__construct();
        // 设置默认值
        $this->attributes = array_merge($this->defaults, $this->attributes);
    }

    /**
     * 实现接口方法, 返回对应的状态枚举类
     */
    public static function getStatusEnum(): string
    {
        return AdminUserStatus::class;
    }

    /**
     * 定义验证规则
     * @param bool $isUpdate 是否为更新操作
     * @param string|null $scenario 场景名称, 为null时使用当前场景
     * @return array 验证规则
     */
    public function rules(): array
    {
        $rules = [
            'username' => 'required|max:50|unique',
            'email' => 'max:100',
            'real_name' => 'max:50',
            'phone' => 'max:20',
            'status_id' => 'numeric',
            'role_id' => 'required|numeric'
        ];

        // 新建时必须有密码
        if (!$isUpdate) {
            $rules['password_hash'] = 'required';
        }

        return [
            'default' => $rules
        ];
    }

    /**
     * 获取字段标签
     * @return array 字段标签映射
     */
    protected function getFieldLabels(): array
    {
        return [
            'username' => '用户名',
            'password_hash' => '密码',
            'email' => '邮箱',
            'real_name' => '真实姓名',
            'phone' => '电话',
            'status_id' => '状态',
            'role_id' => '角色'
        ];
    }

    /**
     * 获取显示名称（优先真实姓名）
     */
    public function getDisplayName(): string
    {
        return $this->real_name ?: $this->username;
    }

    /**
     * 获取状态标签
     */
    public function getStatusLabel(): string
    {
        if (isset($this->status_id)) {
            $status = AdminUserStatus::tryFrom($this->status_id);
            return $status ? $status->label() : '未知状态';
        }
        return '未设置';
    }

    /**
     * 获取角色标签
     */
    public function getRoleLabel(): string
    {
        if (isset($this->role_id)) {
            $role = AdminUserRole::tryFrom($this->role_id);
            return $role ? $role->label() : '未知角色';
        }
        return '未设置';
    }

    /**
     * 检查是否启用
     */
    public function isEnabled(): bool
    {
        return $this->status_id === AdminUserStatus::ENABLED->value;
    }

    /**
     * 检查是否为超级管理员
     */
    public function isSuperAdmin(): bool
    {
        return $this->role_id >= AdminUserRole::SUPER_ADMIN->value;
    }

    /**
     * 检查是否可以管理其他管理员
     */
    public function canManageAdmins(): bool
    {
        return $this->role_id >= AdminUserRole::SUPER_ADMIN->value;
    }

    /**
     * 静态工厂方法 - 创建新AdminUser实例
     */
    public static function make(array $data = []): self
    {
        $instance = new static();
        $instance->fill($data);
        return $instance;
    }

    /**
     * 静态方法 - 通过ID查找
     */
    public static function findOrFail(int $id): self
    {
        $instance = new static();
        $found = $instance->find($id);
        if (!$found) {
            throw new \Exception("AdminUser with ID {$id} not found");
        }
        return $found;
    }

    /**
     * 获取统计信息
     */
    public function getStats(): array
    {
        $sql = "SELECT
                    COUNT(*) as total_admins,
                    SUM(CASE WHEN status_id = :active_status THEN 1 ELSE 0 END) as active_admins,
                    SUM(CASE WHEN status_id = :inactive_status THEN 1 ELSE 0 END) as inactive_admins,
                    SUM(CASE WHEN role_id = :super_admin_role THEN 1 ELSE 0 END) as super_admins,
                    SUM(CASE WHEN role_id = :normal_role THEN 1 ELSE 0 END) as normal_admins
                FROM ".static::getTableName();

        $result = $this->db->fetch($sql, [
            'active_status' => AdminUserStatus::ENABLED->value,
            'inactive_status' => AdminUserStatus::DISABLED->value,
            'super_admin_role' => AdminUserRole::SUPER_ADMIN->value,
            'normal_role' => AdminUserRole::NORMAL->value
        ]);

        return [
            'total_admins' => (int)$result['total_admins'],
            'active_admins' => (int)$result['active_admins'],
            'inactive_admins' => (int)$result['inactive_admins'],
            'super_admins' => (int)$result['super_admins'],
            'normal_admins' => (int)$result['normal_admins']
        ];
    }

    /**
     * 通过用户名查找管理员 - 保持向后兼容
     */
    public function findByUsername(string $username): ?array
    {
        $table = static::getTableName();
        $sql = "SELECT * FROM {$table} WHERE username = :username AND status_id = :status_id LIMIT 1";
        return $this->db->fetch($sql, [
            'username' => $username,
            'status_id' => AdminUserStatus::ENABLED->value
        ]);
    }

    /**
     * 通过用户名查找管理员（返回Model实例）
     */
    public static function findByUsernameModel(string $username): ?self
    {
        $db = \App\Core\Database::getInstance();
        $sql = "SELECT * FROM " . static::getTableName() . " WHERE username = :username LIMIT 1";
        $result = $db->fetch($sql, ['username' => $username]);

        if ($result) {
            $instance = new static();
            $instance->setOriginal($result);
            $instance->setNew(false);
            return $instance;
        }

        return null;
    }

    /**
     * 验证密码 - 向后兼容旧方法
     */
    public function verifyPassword($admin, string $password): bool
    {
        if (is_array($admin)) {
            return password_verify($password, $admin['password_hash']);
        }
        return password_verify($password, $this->password_hash);
    }

    /**
     * 设置密码（自动加密）
     */
    public function setPassword(string $password): void
    {
        $this->password_hash = password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * 更新最后登录信息 - 向后兼容旧方法
     */
    public function updateLoginInfo(int $id, string $ip): bool
    {
        return $this->update($id, [
            'last_login_time' => date('Y-m-d H:i:s'),
            'last_login_ip' => $ip
        ]);
    }

    /**
     * 更新最后登录信息 - 新方法
     */
    public function updateLastLogin(string $ip): bool
    {
        $sql = "UPDATE " . static::getTableName() . "
                SET last_login_time = NOW(), last_login_ip = :ip
                WHERE id = :id";

        $this->db->query($sql, [
            'ip' => $ip,
            'id' => $this->id
        ]);

        return true;
    }

    /**
     * 重写父类方法, 为AdminUser模型准备CSV导入数据
     */
    public function prepareBulkImportData(array $csvRowData): array
    {
        return [
            'username' => $csvRowData['username'] ?? '',
            'email' => $csvRowData['email'] ?? null,
            'real_name' => $csvRowData['real_name'] ?? '',
            'phone' => $csvRowData['phone'] ?? '',
            'role_id' => isset($csvRowData['role_id']) ? (int)$csvRowData['role_id'] : AdminUserRole::NORMAL->value,
            'status_id' => isset($csvRowData['status_id']) ? (int)$csvRowData['status_id'] : AdminUserStatus::ENABLED->value,
            'password_hash' => isset($csvRowData['password']) ? password_hash($csvRowData['password'], PASSWORD_DEFAULT) : password_hash('123456', PASSWORD_DEFAULT)
        ];
    }

    /**
     * 重写字段搜索策略
     */
    protected static function getFieldSearchStrategies(): array
    {
        return [
            'id' => 'exact',
            'username' => 'like',
            'email' => 'like',
            'real_name' => 'like',
            'phone' => 'like',
            'status_id' => 'exact',
            'role_id' => 'exact'
        ];
    }

    /**
     * 生成记住我的 token
     */
    public function generateRememberToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * 保存记住我的 token 到数据库
     */
    public function saveRememberToken(int $adminId, string $token): bool
    {
        $hashedToken = hash('sha256', $token);
        return $this->update($adminId, [
            'remember_token' => $hashedToken
        ]);
    }

    /**
     * 通过记住我的 token 查找管理员
     */
    public function findByRememberToken(string $token): ?array
    {
        $hashedToken = hash('sha256', $token);
        $table = static::getTableName();
        $sql = "SELECT * FROM {$table}
                WHERE remember_token = :token
                AND status_id = :status_id
                LIMIT 1";
        return $this->db->fetch($sql, [
            'token' => $hashedToken,
            'status_id' => AdminUserStatus::ENABLED->value
        ]);
    }

    /**
     * 清除记住我的 token
     */
    public function clearRememberToken(int $adminId): bool
    {
        return $this->update($adminId, [
            'remember_token' => null
        ]);
    }
}