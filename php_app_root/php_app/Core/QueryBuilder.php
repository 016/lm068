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
     * 添加 WHERE 条件
     */
    public function where(array $conditions): self
    {
        foreach ($conditions as $field => $value) {
            if (is_array($value)) {
                $placeholders = [];
                foreach ($value as $idx => $val) {
                    $paramKey = $field . '_' . $idx;
                    $placeholders[] = ':' . $paramKey;
                    $this->params[$paramKey] = $val;
                }
                $this->wheres[] = "{$field} IN (" . implode(',', $placeholders) . ")";
            } else {
                $paramKey = $field . '_' . count($this->params);
                $this->wheres[] = "{$field} = :{$paramKey}";
                $this->params[$paramKey] = $value;
            }
        }
        return $this;
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
}