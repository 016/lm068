<?php

namespace App\Core;

/**
 * 验证规则解析器
 * 
 * 负责解析 Model 的验证规则字符串，提取规则信息
 * 
 * 使用示例:
 * ```php
 * $rules = "required|max:50|unique";
 * $parsed = RuleParser::parse($rules);
 * // ['required' => true, 'max' => '50', 'unique' => true]
 * ```
 */
class RuleParser
{
    /**
     * 解析规则字符串为结构化数组
     * 
     * @param string $rules 规则字符串，如 "required|max:50|unique"
     * @return array 解析后的规则数组
     */
    public static function parse(string $rules): array
    {
        $result = [];
        $ruleArray = explode('|', $rules);
        
        foreach ($ruleArray as $rule) {
            $rule = trim($rule);
            if (empty($rule)) {
                continue;
            }
            
            if (str_contains($rule, ':')) {
                [$name, $param] = explode(':', $rule, 2);
                $result[trim($name)] = trim($param);
            } else {
                $result[$rule] = true;
            }
        }
        
        return $result;
    }
    
    /**
     * 检查规则字符串是否包含指定规则
     * 
     * @param string $rules 规则字符串
     * @param string $ruleName 要检查的规则名称
     * @return bool
     */
    public static function hasRule(string $rules, string $ruleName): bool
    {
        return str_contains($rules, $ruleName);
    }
    
    /**
     * 从规则字符串中提取指定规则的参数
     * 
     * @param string $rules 规则字符串
     * @param string $ruleName 规则名称
     * @return string|null 规则参数，不存在返回 null
     */
    public static function getRuleParam(string $rules, string $ruleName): ?string
    {
        $parsed = self::parse($rules);
        $value = $parsed[$ruleName] ?? null;
        
        // 如果是 boolean true，返回 null（表示规则存在但无参数）
        return $value === true ? null : $value;
    }
}
