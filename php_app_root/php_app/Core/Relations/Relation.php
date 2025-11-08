<?php

namespace App\Core\Relations;

use App\Core\Database;

/**
 * 关系基类
 */
abstract class Relation
{
    protected $parent;          // 父模型实例
    protected $related;         // 关联模型类名
    protected $foreignKey;      // 外键
    protected $localKey;        // 本地键

    public function __construct($parent, string $related, string $foreignKey, string $localKey)
    {
        $this->parent = $parent;
        $this->related = $related;
        $this->foreignKey = $foreignKey;
        $this->localKey = $localKey;
    }

    /**
     * 获取关联数据
     */
    abstract public function get();

    /**
     * 预加载关联数据
     */
    abstract public function eagerLoad(array $models): void;

    /**
     * 保存关联模型
     */
    abstract public function save($model): bool;
}


