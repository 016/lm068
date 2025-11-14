<?php

namespace App\Core;

use App\Core\Database;

/**
 * 查询构建器 - 支持链式查询
 */
class QueryBuilder
{
    protected $modelClass;
    protected $table;
    protected $wheres = [];
    protected $params = [];
    protected $orderBy = null;
    protected $limit = null;
    protected $offset = 0;
    protected $with = []; // 关系预加载
    protected $select = '*';

    public function __construct(string $modelClass)
    {
        $this->modelClass = $modelClass;
        $this->table = $modelClass::getTableName();
    }

    /**
     * 重置构建器状态，方便在循环中重复使用
     */
    public function reset(): self
    {
        $this->wheres = [];
        $this->params = [];
        $this->orderBy = null;
        $this->limit = null;
        $this->offset = 0;
        $this->with = [];
        $this->select = '*';

        return $this;
    }

    /**
     * 添加 WHERE 条件 (增强版)
     *
     * 支持多种条件格式:
     * - `['id' => 1]` -> `id = :id_...`
     * - `['id' => [1, 2, 3]]` -> `id IN (:id_0, :id_1, ...)`
     * - `['id' => ['!=', 1]]` -> `id != :id_...`
     * - `['price' => ['BETWEEN', [100, 200]]]` -> `price BETWEEN :price_0 AND :price_1`
     * - `['deleted_at' => null]` -> `deleted_at IS NULL`
     *
     * @param array $conditions 条件数组
     * @return self
     */
    public function where(array $conditions): self
    {
        foreach ($conditions as $field => $value) {
            // 1. 处理 IS NULL / IS NOT NULL
            if ($value === null) {
                $this->wheres[] = "{$field} IS NULL";
                continue;
            }
            if (is_array($value) && isset($value[0]) && strtoupper($value[0]) === 'IS NOT NULL') {
                $this->wheres[] = "{$field} IS NOT NULL";
                continue;
            }
            if (is_array($value) && isset($value[0]) && strtoupper($value[0]) === 'IS NULL') {
                $this->wheres[] = "{$field} IS NULL";
                continue;
            }

            // 2. 处理数组类型的值
            if (is_array($value)) {
                // 2.1 检查是否为 [操作符, 值] 的高级格式
                $isOperatorFormat = false;
                if (isset($value[0]) && is_string($value[0])) {
                    $operator = strtoupper($value[0]);

                    // 定义有效操作符白名单
                    $validOperators = [
                        'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN',
                        'IS NULL', 'IS NOT NULL',
                        '=', '!=', '<>', '>', '<', '>=', '<=',
                        'LIKE', 'NOT LIKE'
                    ];

                    if (in_array($operator, $validOperators)) {
                        $isOperatorFormat = true;
                        $val = $value[1] ?? null;

                        switch ($operator) {
                            case 'IN':
                            case 'NOT IN':
                                if (!is_array($val) || empty($val)) {
                                    // 避免 IN () 导致的SQL语法错误
                                    $this->wheres[] = ($operator === 'NOT IN') ? '1=1' : '1=0';
                                    break;
                                }
                                $placeholders = [];
                                foreach ($val as $idx => $v) {
                                    $paramKey = $this->generateParamKey($field, $idx);
                                    $placeholders[] = ':' . $paramKey;
                                    $this->params[$paramKey] = $v;
                                }
                                $this->wheres[] = "{$field} {$operator} (" . implode(',', $placeholders) . ")";
                                break;

                            case 'BETWEEN':
                            case 'NOT BETWEEN':
                                if (!is_array($val) || count($val) !== 2) {
                                    // 值必须是包含两个元素的数组
                                    continue 2; // continue the outer foreach loop
                                }
                                $paramKey1 = $this->generateParamKey($field, 'start');
                                $paramKey2 = $this->generateParamKey($field, 'end');
                                $this->wheres[] = "{$field} {$operator} :{$paramKey1} AND :{$paramKey2}";
                                $this->params[$paramKey1] = $val[0];
                                $this->params[$paramKey2] = $val[1];
                                break;

                            default:
                                // 处理 =, !=, >, <, LIKE 等常规操作符
                                $paramKey = $this->generateParamKey($field);
                                $this->wheres[] = "{$field} {$operator} :{$paramKey}";
                                $this->params[$paramKey] = $val;
                                break;
                        }
                    }
                }

                // 2.2 不是操作符格式,按普通数组处理(IN 查询)
                if (!$isOperatorFormat) {
                    if (empty($value)) {
                        $this->wheres[] = '1=0';
                        continue;
                    }
                    $placeholders = [];
                    foreach ($value as $idx => $v) {
                        $paramKey = $this->generateParamKey($field, $idx);
                        $placeholders[] = ':' . $paramKey;
                        $this->params[$paramKey] = $v;
                    }
                    $this->wheres[] = "{$field} IN (" . implode(',', $placeholders) . ")";
                }
            } else {
                // 3. 处理简单值: ['id' => 1] -> =
                $paramKey = $this->generateParamKey($field);
                $this->wheres[] = "{$field} = :{$paramKey}";
                $this->params[$paramKey] = $value;
            }
        }
        return $this;
    }

    /**
     * 生成一个唯一的参数键名以避免冲突
     */
    private function generateParamKey(string $field, $suffix = null): string
    {
        // 清理字段名，只保留字母数字和下划线
        $baseKey = preg_replace('/[^a-zA-Z0-9_]/', '', $field);
        $key = $baseKey . ($suffix !== null ? '_' . $suffix : '');

        $uniqueKey = $key;
        $count = 0;
        // 如果键名已存在，则添加数字后缀确保唯一性
        while (array_key_exists($uniqueKey, $this->params)) {
            $count++;
            $uniqueKey = $key . '_' . $count;
        }
        return $uniqueKey;
    }

    /**
     * 原始 WHERE 条件
     */
    public function whereRaw(string $condition, array $params = []): self
    {
        $this->wheres[] = $condition;
        $this->params = array_merge($this->params, $params);
        return $this;
    }

    /**
     * 排序
     */
    public function orderBy(string $orderBy): self
    {
        $this->orderBy = $orderBy;
        return $this;
    }

    /**
     * 限制数量
     */
    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * 偏移量
     */
    public function offset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * 预加载关系
     */
    public function with($relations): self
    {
        if (is_string($relations)) {
            $relations = [$relations];
        }
        $this->with = array_merge($this->with, $relations);
        return $this;
    }

    /**
     * 选择字段
     */
    public function select($fields): self
    {
        if (is_array($fields)) {
            $this->select = implode(', ', $fields);
        } else {
            $this->select = $fields;
        }
        return $this;
    }

    /**
     * 构建 SQL
     */
    protected function buildSql(): string
    {
        $sql = "SELECT {$this->select} FROM {$this->table}";

        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(" AND ", $this->wheres);
        }

        if ($this->orderBy) {
            $sql .= " ORDER BY {$this->orderBy}";
        }

        if ($this->limit !== null) {
            $sql .= " LIMIT {$this->limit}";
            if ($this->offset > 0) {
                $sql .= " OFFSET {$this->offset}";
            }
        }

        return $sql;
    }

    /**
     * 查询所有记录
     */
    public function all(): array
    {
        $db = Database::getInstance();
        $sql = $this->buildSql();
        $results = $db->fetchAll($sql, $this->params);

        $models = [];
        foreach ($results as $row) {
            $model = new $this->modelClass();
            $model->setOriginal($row);
            $model->setNew(false);
            $models[] = $model;
        }

        // 处理关系预加载
        if (!empty($this->with) && !empty($models)) {
            $this->loadRelations($models);
        }

        return $models;
    }

    /**
     * 查询第一条记录
     */
    public function first(): ?object
    {
        $this->limit(1);
        $models = $this->all();
        return $models[0] ?? null;
    }

    /**
     * 查询单条记录
     */
    public function one(): ?object
    {
        return $this->first();
    }

    /**
     * 统计记录数
     */
    public function count(): int
    {
        $db = Database::getInstance();
        $this->select = 'COUNT(*) as count';
        $sql = $this->buildSql();
        $result = $db->fetch($sql, $this->params);
        return (int)($result['count'] ?? 0);
    }

    /**
     * 加载关系数据
     */
    protected function loadRelations(array $models): void
    {
        foreach ($this->with as $relationName) {
            // 获取关系定义
            $firstModel = $models[0];
            if (!method_exists($firstModel, $relationName)) {
                continue;
            }

            $relation = $firstModel->$relationName();
            $relation->eagerLoad($models);
        }
    }


    //test support
    /**
     * 获取生成的SQL和参数，用于测试
     */
    public function getResult(): array
    {
        if (empty($this->wheres)) {
            return [
                'sql_string' => ' (No WHERE clause generated)',
                'params'     => [],
            ];
        }

        return [
            'sql_string' => 'WHERE ' . implode(' AND ', $this->wheres),
            'params'     => $this->params,
        ];
    }
}