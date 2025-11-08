<?php

namespace App\Core\Relations;

use App\Core\Database;

/**
 * HasOne 关系 - 一对一
 */
class HasOne extends Relation
{
    public function get()
    {
        $db = Database::getInstance();
        $table = $this->related::getTableName();
        $localValue = $this->parent->{$this->localKey};

        $sql = "SELECT * FROM {$table} WHERE {$this->foreignKey} = :value LIMIT 1";
        $result = $db->fetch($sql, ['value' => $localValue]);

        if ($result) {
            $model = new $this->related();
            $model->setOriginal($result);
            $model->setNew(false);
            return $model;
        }

        return null;
    }

    public function eagerLoad(array $models): void
    {
        $db = Database::getInstance();
        $table = $this->related::getTableName();

        // 收集所有本地键值
        $localValues = array_map(function($model) {
            return $model->{$this->localKey};
        }, $models);
        $localValues = array_unique(array_filter($localValues));

        if (empty($localValues)) {
            return;
        }

        // 批量查询
        $placeholders = implode(',', array_fill(0, count($localValues), '?'));
        $sql = "SELECT * FROM {$table} WHERE {$this->foreignKey} IN ({$placeholders})";
        $results = $db->fetchAll($sql, array_values($localValues));

        // 建立映射
        $relatedMap = [];
        foreach ($results as $row) {
            $model = new $this->related();
            $model->setOriginal($row);
            $model->setNew(false);
            $relatedMap[$row[$this->foreignKey]] = $model;
        }

        // 关联到父模型
        $relationName = $this->getRelationName();
        foreach ($models as $model) {
            $localValue = $model->{$this->localKey};
            $model->$relationName = $relatedMap[$localValue] ?? null;
        }
    }

    public function save($model): bool
    {
        if (!$model instanceof $this->related) {
            return false;
        }

        // 设置外键值
        $model->{$this->foreignKey} = $this->parent->{$this->localKey};
        return $model->save();
    }

    protected function getRelationName(): string
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
        return $trace[2]['function'] ?? 'relation';
    }
}
