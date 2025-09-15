<?php

namespace App\Core;

use App\Core\Database;

abstract class Model
{
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $timestamps = true;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function find(int $id): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1";
        return $this->db->fetch($sql, ['id' => $id]);
    }

    public function findById(int $id): ?array
    {
        return $this->find($id);
    }

    public function findAll(array $conditions = [], ?int $limit = null, int $offset = 0, ?string $orderBy = null): array
    {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];

        if (!empty($conditions)) {
            $whereClause = [];
            foreach ($conditions as $field => $value) {
                if (is_array($value)) {
                    $placeholders = implode(',', array_fill(0, count($value), '?'));
                    $whereClause[] = "{$field} IN ({$placeholders})";
                    $params = array_merge($params, $value);
                } else {
                    $whereClause[] = "{$field} = :{$field}";
                    $params[$field] = $value;
                }
            }
            $sql .= " WHERE " . implode(" AND ", $whereClause);
        }

        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }

        if ($limit) {
            $sql .= " LIMIT {$limit}";
            if ($offset > 0) {
                $sql .= " OFFSET {$offset}";
            }
        }

        return $this->db->fetchAll($sql, $params);
    }

    public function create(array $data): int
    {
        $data = $this->filterFillable($data);

        if ($this->timestamps) {
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
        }

        return $this->db->insert($this->table, $data);
    }

    public function update(int $id, array $data): bool
    {
        $data = $this->filterFillable($data);

        if ($this->timestamps) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }

        $updated = $this->db->update(
            $this->table,
            $data,
            "{$this->primaryKey} = :id",
            ['id' => $id]
        );

        return $updated > 0;
    }

    public function delete(int $id): bool
    {
        $deleted = $this->db->delete(
            $this->table,
            "{$this->primaryKey} = :id",
            ['id' => $id]
        );

        return $deleted > 0;
    }

    public function count(array $conditions = []): int
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        $params = [];

        if (!empty($conditions)) {
            $whereClause = [];
            foreach ($conditions as $field => $value) {
                $whereClause[] = "{$field} = :{$field}";
                $params[$field] = $value;
            }
            $sql .= " WHERE " . implode(" AND ", $whereClause);
        }

        $result = $this->db->fetch($sql, $params);
        return (int)$result['count'];
    }

    public function exists(int $id): bool
    {
        $sql = "SELECT 1 FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1";
        return (bool)$this->db->fetch($sql, ['id' => $id]);
    }

    public function query(string $sql, array $params = []): \PDOStatement
    {
        return $this->db->query($sql, $params);
    }

    protected function filterFillable(array $data): array
    {
        if (empty($this->fillable)) {
            return $data;
        }

        return array_intersect_key($data, array_flip($this->fillable));
    }

    /**
     * 验证数据是否符合规则
     * @param array $data 要验证的数据
     * @param bool $isUpdate 是否为更新操作
     * @param ?int $excludeId 更新时排除的ID
     * @return array 错误信息数组，为空表示验证通过
     */
    public function validate(array $data, bool $isUpdate = false, ?int $excludeId = null): array
    {
        $errors = [];

        // 子类可以重写此方法实现具体验证逻辑
        if (method_exists($this, 'rules')) {
            $rules = $this->rules($isUpdate);
            $errors = $this->validateRules($data, $rules, $excludeId);
        }

        return $errors;
    }

    /**
     * 执行验证规则
     * @param array $data 数据
     * @param array $rules 规则
     * @param ?int $excludeId 排除的ID
     * @return array 错误信息
     */
    protected function validateRules(array $data, array $rules, ?int $excludeId = null): array
    {
        $errors = [];

        foreach ($rules as $field => $ruleSet) {
            $value = $data[$field] ?? null;
            $fieldRules = explode('|', $ruleSet);

            foreach ($fieldRules as $rule) {
                $ruleParts = explode(':', $rule);
                $ruleName = $ruleParts[0];
                $ruleParam = $ruleParts[1] ?? null;

                switch ($ruleName) {
                    case 'required':
                        if (empty($value) && $value !== '0' && $value !== 0) {
                            $errors[$field] = $this->getErrorMessage($field, 'required');
                        }
                        break;

                    case 'max':
                        if (!empty($value) && mb_strlen($value) > (int)$ruleParam) {
                            $errors[$field] = $this->getErrorMessage($field, 'max', $ruleParam);
                        }
                        break;

                    case 'min':
                        if (!empty($value) && mb_strlen($value) < (int)$ruleParam) {
                            $errors[$field] = $this->getErrorMessage($field, 'min', $ruleParam);
                        }
                        break;

                    case 'unique':
                        if (!empty($value)) {
                            $table = $ruleParam ?: $this->table;
                            $exists = $this->checkUnique($field, $value, $table, $excludeId);
                            if ($exists) {
                                $errors[$field] = $this->getErrorMessage($field, 'unique');
                            }
                        }
                        break;

                    case 'numeric':
                        if (!empty($value) && !is_numeric($value)) {
                            $errors[$field] = $this->getErrorMessage($field, 'numeric');
                        }
                        break;
                }
            }
        }

        return $errors;
    }

    /**
     * 检查字段值是否唯一
     * @param string $field 字段名
     * @param mixed $value 值
     * @param string $table 表名
     * @param ?int $excludeId 排除的ID
     * @return bool 是否存在重复
     */
    protected function checkUnique(string $field, $value, string $table, ?int $excludeId = null): bool
    {
        $sql = "SELECT 1 FROM {$table} WHERE {$field} = :value";
        $params = ['value' => $value];

        if ($excludeId !== null) {
            $sql .= " AND {$this->primaryKey} != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }

        $sql .= " LIMIT 1";

        return (bool)$this->db->fetch($sql, $params);
    }

    /**
     * 获取错误信息
     * @param string $field 字段名
     * @param string $rule 规则名
     * @param ?string $param 参数
     * @return string 错误信息
     */
    protected function getErrorMessage(string $field, string $rule, ?string $param = null): string
    {
        $fieldLabels = $this->getFieldLabels();
        $fieldLabel = $fieldLabels[$field] ?? $field;

        $messages = [
            'required' => "{$fieldLabel}不能为空",
            'max' => "{$fieldLabel}不能超过{$param}个字符",
            'min' => "{$fieldLabel}不能少于{$param}个字符",
            'unique' => "{$fieldLabel}已存在",
            'numeric' => "{$fieldLabel}必须是数字"
        ];

        return $messages[$rule] ?? "{$fieldLabel}验证失败";
    }

    /**
     * 获取字段标签，子类可重写
     * @return array 字段标签映射
     */
    protected function getFieldLabels(): array
    {
        return [];
    }
}