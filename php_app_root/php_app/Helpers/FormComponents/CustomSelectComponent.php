<?php

namespace App\Helpers\FormComponents;

use App\Core\Model;

/**
 * 自定义 Select 组件
 * 支持动态选项加载
 */
class CustomSelectComponent
{
    private Model $model;
    private string $field;
    private array $config;

    public function __construct(Model $model, string $field, array $config)
    {
        $this->model = $model;
        $this->field = $field;
        $this->config = $config;
    }

    /**
     * 渲染自定义 Select
     */
    public function render(): string
    {
        $helpText = $this->config['helpText'] ?? $this->model->getFieldHelpText($this->field);
        $options = $this->config['options'] ?? [];
        $required = $this->config['required'] ?? false;
        $placeholder = $this->config['placeholder'] ?? '请选择';
        
        $hasError = !empty($this->model->errors[$this->field]);
        $selectClass = 'form-control form-select' . ($hasError ? ' is-invalid' : '');
        
        $html = '<select class="' . $selectClass . '" id="' . $this->field . '" name="' . $this->field . '"';
        if ($required) {
            $html .= ' required';
        }
        $html .= '>';
        
        // Placeholder option
        if ($placeholder) {
            $html .= '    <option value="">' . htmlspecialchars($placeholder) . '</option>';
        }
        
        // Options
        $currentValue = $this->model->{$this->field} ?? '';
        foreach ($options as $option) {
            $value = $option['id'] ?? $option['value'] ?? '';
            $text = $option['text'] ?? $option['label'] ?? '';
            $selected = ($currentValue == $value) ? 'selected' : '';
            $html .= '    <option value="' . htmlspecialchars($value) . '" ' . $selected . '>' . htmlspecialchars($text) . '</option>';
        }
        
        $html .= '</select>';
        
        // Error feedback
        if ($hasError) {
            $html .= '<div class="invalid-feedback">' . htmlspecialchars($this->model->errors[$this->field]) . '</div>';
        }
        
        // Help text
        if ($helpText) {
            $html .= '<div class="form-text">' . htmlspecialchars($helpText) . '</div>';
        }
        
        return $html;
    }
}
