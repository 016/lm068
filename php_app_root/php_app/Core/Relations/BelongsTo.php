<?php

namespace App\Core\Relations;

use App\Core\Database;


/**
 * BelongsTo 关系 - 属于(反向一对多)
 */
class BelongsTo extends Relation
{
    public function get()
    {
        $db = Database::getInstance();
        $table = $this->related::getTableName();
        $foreignValue = $this->parent->{$this->foreignKey};

        if (!$foreignValue) {
            return null;
        }

        $sql = "SELECT * FROM {$table} WHERE {$this->localKey} = :value LIMIT 1";
        $result = $db->fetch($sql, ['value' => $foreignValue]);

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

        // 收集所有外键值
        $foreignValues = array_map(function($model) {
            return $model->{$this->foreignKey};
        }, $models);
        $foreignValues = array_unique(array_filter($foreignValues));

        if (empty($foreignValues)) {
            return;
        }

        // 批量查询
        $placeholders = implode(',', array_fill(0, count($foreignValues), '?'));
        $sql = "SELECT * FROM {$table} WHERE {$this->localKey} IN ({$placeholders})";
        $results = $db->fetchAll($sql, array_values($foreignValues));

        // 建立映射
        $relatedMap = [];
        foreach ($results as $row) {
            $model = new $this->related();
            $model->setOriginal($row);
            $model->setNew(false);
            $relatedMap[$row[$this->localKey]] = $model;
        }

        // 关联到父模型
        $relationName = $this->getRelationName();
        foreach ($models as $model) {
            $foreignValue = $model->{$this->foreignKey};
            $model->$relationName = $relatedMap[$foreignValue] ?? null;
        }
    }

    public function save($model): bool
    {
        if (!$model instanceof $this->related) {
            return false;
        }

        // 先保存关联模型
        if (!$model->save()) {
            return false;
        }

        // 更新父模型的外键
        $this->parent->{$this->foreignKey} = $model->{$this->localKey};
        return true;
    }

    protected function getRelationName(): string
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
        return $trace[2]['function'] ?? 'relation';
    }
}