<?php

namespace App\Helpers;

use App\Core\Model;
use App\Core\RuleParser;

/**
 * 表单辅助类
 * 
 * 负责将 Model 的验证规则转换为 HTML 表单属性
 * 
 * 使用示例:
 * ```php
 * $attrs = FormHelper::getFieldAttributes($model, 'name');
 * // ['required' => true, 'maxlength' => '50']
 * 
 * echo FormHelper::renderAttributes($attrs);
 * // required maxlength="50"
 * ```
 */
class FormHelper
{
    /**
     * 从 Model 规则生成字段的 HTML 属性
     * 
     * @param Model $model 模型实例
     * @param string $field 字段名
     * @return array HTML 属性数组
     */
    public static function getFieldAttributes(Model $model, string $field): array
    {
        $rules = self::getFieldRules($model, $field);
        if (!$rules) {
            return [];
        }
        
        $parsed = RuleParser::parse($rules);
        $attrs = [];
        
        // required 规则 -> required 属性
        if (isset($parsed['required'])) {
            $attrs['required'] = true;
        }
        
        // max 规则 -> maxlength 属性
        if (isset($parsed['max'])) {
            $attrs['maxlength'] = $parsed['max'];
        }
        
        // min 规则 -> minlength 属性
        if (isset($parsed['min'])) {
            $attrs['minlength'] = $parsed['min'];
        }
        
        // numeric 规则 -> type="number" + pattern
        if (isset($parsed['numeric'])) {
            $attrs['type'] = 'number';
            $attrs['pattern'] = '[0-9]+';
        }
        
        // email 规则 -> type="email"
        if (isset($parsed['email'])) {
            $attrs['type'] = 'email';
        }
        
        // unique 规则 -> data-rule 属性 (用于前端异步验证)
        if (isset($parsed['unique'])) {
            $attrs['data-rule'] = 'unique';
        }
        
        return $attrs;
    }
    
    /**
     * 获取字段的验证规则字符串
     * 
     * @param Model $model 模型实例
     * @param string $field 字段名
     * @return string|null 规则字符串，如 "required|max:50"
     */
    public static function getFieldRules(Model $model, string $field): ?string
    {
        $scenario = $model->getScenario();
        $allRules = $model->rules();
        
        // 获取当前场景的规则
        $rules = $allRules[$scenario] ?? $allRules['default'] ?? [];
        
        return $rules[$field] ?? null;
    }
    
    /**
     * 渲染属性数组为 HTML 字符串
     * 
     * @param array $attributes 属性数组
     * @return string HTML 属性字符串
     */
    public static function renderAttributes(array $attributes): string
    {
        $html = [];
        
        foreach ($attributes as $key => $value) {
            if ($value === true) {
                // Boolean 属性 (required, disabled, checked, readonly)
                $html[] = htmlspecialchars($key);
            } elseif ($value !== false && $value !== null) {
                // 键值对属性
                $html[] = htmlspecialchars($key) . '="' . htmlspecialchars($value) . '"';
            }
        }
        
        return implode(' ', $html);
    }
    
    /**
     * 检查字段是否必填
     * 
     * @param Model $model 模型实例
     * @param string $field 字段名
     * @return bool
     */
    public static function isRequired(Model $model, string $field): bool
    {
        $rules = self::getFieldRules($model, $field);
        return $rules ? RuleParser::hasRule($rules, 'required') : false;
    }
    
    /**
     * 获取字段的最大长度
     * 
     * @param Model $model 模型实例
     * @param string $field 字段名
     * @return int|null
     */
    public static function getMaxLength(Model $model, string $field): ?int
    {
        $rules = self::getFieldRules($model, $field);
        if (!$rules) {
            return null;
        }
        
        $max = RuleParser::getRuleParam($rules, 'max');
        return $max ? (int)$max : null;
    }
    
    /**
     * 获取字段的最小长度
     * 
     * @param Model $model 模型实例
     * @param string $field 字段名
     * @return int|null
     */
    public static function getMinLength(Model $model, string $field): ?int
    {
        $rules = self::getFieldRules($model, $field);
        if (!$rules) {
            return null;
        }
        
        $min = RuleParser::getRuleParam($rules, 'min');
        return $min ? (int)$min : null;
    }
}
