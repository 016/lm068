<?php

namespace App\Models;

use App\Core\Model;
use App\Constants\AdminStatus;

class AdminUser extends Model
{
    protected $table = 'admin_user';
    protected $fillable = [
        'username', 'password_hash', 'email', 'real_name',
        'avatar', 'phone', 'status_id', 'role_id'
    ];

    public function findByUsername(string $username): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE username = :username AND status_id = :status_id LIMIT 1";
        return $this->db->fetch($sql, [
            'username' => $username,
            'status_id' => AdminStatus::ENABLED->value
        ]);
    }

    public function verifyPassword(array $admin, string $password): bool
    {
        return password_verify($password, $admin['password_hash']);
    }

    public function updateLoginInfo(int $id, string $ip): bool
    {
        return $this->update($id, [
            'last_login_time' => date('Y-m-d H:i:s'),
            'last_login_ip' => $ip
        ]);
    }
}