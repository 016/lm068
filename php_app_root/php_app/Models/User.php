<?php

namespace App\Models;

use App\Core\Model;
use App\Constants\Status;

class User extends Model
{
    protected $table = 'user';
    protected $fillable = [
        'username', 'email', 'password_hash', 'avatar', 
        'nickname', 'status_id'
    ];

    public function findByEmail(string $email): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email LIMIT 1";
        return $this->db->fetch($sql, ['email' => $email]);
    }

    public function findByUsername(string $username): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE username = :username LIMIT 1";
        return $this->db->fetch($sql, ['username' => $username]);
    }

    public function getActiveUsers(int $limit = 20, int $offset = 0): array
    {
        return $this->findAll(['status_id' => Status::ACTIVE->value], $limit, $offset, 'created_at DESC');
    }

    public function createUser(array $data): int
    {
        if (!empty($data['password'])) {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
            unset($data['password']);
        }

        return $this->create($data);
    }

    public function verifyPassword(array $user, string $password): bool
    {
        return password_verify($password, $user['password_hash']);
    }
}