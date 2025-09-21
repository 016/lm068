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

    /**
     * 根据中文名或英文名查找标签
     * @param string $nameCn 中文名称
     * @param string $nameEn 英文名称
     * @return array|null 标签数据或null
     */
    public function findByName(string $nameCn, string $nameEn): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE name_cn = :name_cn OR name_en = :name_en LIMIT 1";
        $params = [
            'name_cn' => $nameCn,
            'name_en' => $nameEn
        ];

        $result = $this->db->fetch($sql, $params);
        return $result ?: null;
    }

    /**
     * 根据过滤条件获取所有标签数据（不分页，用于JS处理）
     * 支持多字段搜索过滤
     */
    public function findAllWithFilters(array $filters = []): array
    {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];
        $whereConditions = [];

        // 处理搜索条件
        if (!empty($filters)) {
            foreach ($filters as $field => $value) {
                if (empty($value) && $value !== '0') {
                    continue; //skip for empty null or empty string
                }
                switch ($field) {
                    case 'id':
                        $whereConditions[] = "id = :search_id";
                        $params['search_id'] = (int)$value;
                        break;
                    case 'name':
                        $whereConditions[] = "(name_cn LIKE :search_name_cn OR name_en LIKE :search_name_en)";
                        $params['search_name_cn'] = "%{$value}%";
                        $params['search_name_en'] = "%{$value}%";
                        break;
                    case 'description':
                        $whereConditions[] = "(desc_cn LIKE :search_desc_cn OR desc_en LIKE :search_desc_en)";
                        $params['search_desc_cn'] = "%{$value}%";
                        $params['search_desc_en'] = "%{$value}%";
                        break;
                    case 'content_cnt':
                        // 处理数量范围搜索，支持格式如 "5-10" 或 ">5" 或 "10"
                        if (strpos($value, '-') !== false) {
                            $range = explode('-', $value);
                            if (count($range) === 2 && is_numeric($range[0]) && is_numeric($range[1])) {
                                $whereConditions[] = "content_cnt BETWEEN :cnt_min AND :cnt_max";
                                $params['cnt_min'] = (int)$range[0];
                                $params['cnt_max'] = (int)$range[1];
                            }
                        } elseif (preg_match('/^([><=]+)(\d+)$/', $value, $matches)) {
                            $operator = $matches[1];
                            $number = (int)$matches[2];
                            if (in_array($operator, ['>', '<', '>=', '<=', '='])) {
                                $whereConditions[] = "content_cnt {$operator} :cnt_value";
                                $params['cnt_value'] = $number;
                            }
                        } elseif (is_numeric($value)) {
                            $whereConditions[] = "content_cnt = :cnt_exact";
                            $params['cnt_exact'] = (int)$value;
                        }
                        break;
                    case 'icon_class':
                        $whereConditions[] = "icon_class LIKE :search_icon";
                        $params['search_icon'] = "%{$value}%";
                        break;

                    case 'status_id':
                        $whereConditions[] = "status_id = :status_id";
                        $params['status_id'] = (int)$value;
                        break;
                }
            }
        }

        if (!empty($whereConditions)) {
            $sql .= " WHERE " . implode(" AND ", $whereConditions);
        }

        // 排序
        $orderBy = $filters['order_by'] ?? 'created_at DESC';
        $sql .= " ORDER BY {$orderBy}";

        return $this->db->fetchAll($sql, $params);
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



    //// bulk actions
    //bulk update
    public function bulkUpdateStatus(array $targetIds, int $status): array
    {
        $returnCnt = ['total'=>count($targetIds), 'changed'=>0, 'fail'=>0];
        if (empty($targetIds)) {
            return $returnCnt;
        }

        // 分批执行，保证性能. 定义每个批次的大小，例如 1000
        $chunkSize = 1000;
        $tagIdChunks = array_chunk($targetIds, $chunkSize);

        foreach ($tagIdChunks as $chunk) {
            $placeholders = implode(',', array_fill(0, count($chunk), '?'));
            $sql = "UPDATE {$this->table} SET status_id = ?, updated_at = NOW() WHERE id IN ({$placeholders})";

            $params = array_merge([$status], $chunk);

            // 累加每次成功更新的数量
            $returnCnt['changed'] += $this->db->execute($sql, $params);
        }


        $returnCnt['fail'] = $returnCnt['total'] - $returnCnt['changed'];

        return $returnCnt;
    }

    //bulk delete
    public function bulkDelete(array $targetIds): array
    {
        $returnCnt = ['total'=>count($targetIds), 'changed'=>0, 'fail'=>0];
        if (empty($targetIds)) {
            return $returnCnt;
        }


        $this->db->beginTransaction();

        try {
            // 分批执行，保证性能. 定义每个批次的大小，例如 1000
            $chunkSize = 1000;
            $tagIdChunks = array_chunk($targetIds, $chunkSize);

            foreach ($tagIdChunks as $chunk) {
                $placeholders = implode(',', array_fill(0, count($chunk), '?'));

                $returnCnt['changed'] += $this->db->execute("DELETE FROM {$this->table} WHERE id IN ({$placeholders})", $chunk);
            }

            $this->db->commit();

            $returnCnt['fail'] = $returnCnt['total'] - $returnCnt['changed'];
            return $returnCnt;

        } catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    /**
     * 准备CSV导入数据 - 子类可重写来自定义数据准备逻辑
     * 
     * @param array $csvRowData CSV行数据
     * @return array 处理后的数据
     */
    public function prepareBulkImportData(array $csvRowData): array
    {
        // 默认实现，子类可以重写
        return $csvRowData;
    }

    /**
     * 验证CSV导入数据 - 基于现有的validate方法
     * 
     * @param array $data 要验证的数据
     * @return bool 是否验证通过
     */
    public function validateBulkImportData(array $data): bool
    {
        $errors = $this->validate($data, false);
        return empty($errors);
    }

    /**
     * 检查导入数据是否重复 - 子类可重写
     * 
     * @param array $data 导入数据
     * @return bool 是否重复
     */
    public function isDuplicateImportData(array $data): bool
    {
        // 默认实现，基于name_cn和name_en检查
        if (isset($data['name_cn']) && isset($data['name_en'])) {
            return (bool)$this->findByName($data['name_cn'], $data['name_en']);
        }
        return false;
    }

    /**
     * 批量导入单条记录 - 通用方法
     * 
     * @param array $csvRowData CSV行数据
     * @return bool 是否成功
     */
    public function importSingleRecord(array $csvRowData): bool
    {
        try {
            // 1. 准备数据
            $data = $this->prepareBulkImportData($csvRowData);
            
            // 2. 验证数据
            if (!$this->validateBulkImportData($data)) {
                return false;
            }
            
            // 3. 检查重复
            if ($this->isDuplicateImportData($data)) {
                return false; // 跳过重复数据
            }
            
            // 4. 创建记录
            $this->create($data);
            return true;
            
        } catch (\Exception $e) {
            error_log("Import single record error: " . $e->getMessage());
            return false;
        }
    }
}