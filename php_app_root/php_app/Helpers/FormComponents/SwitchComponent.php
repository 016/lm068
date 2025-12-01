<?php

namespace App\Helpers\FormComponents;

use App\Core\Model;

/**
 * 开关组件
 * 
 * 用于渲染项目特有的自定义开关样式
 */
class SwitchComponent
{
    private Model $model;
    private string $field;
    private array $config;
    
    public function __construct(Model $model, string $field, array $config = [])
    {
        $this->model = $model;
        $this->field = $field;
        $this->config = $config;
    }
    
    /**
     * 渲染开关组件
     * 
     * @return string HTML 字符串
     */
    public function render(): string
    {
        $value = $this->config['value'] ?? 1;
        $checked = ($this->model->{$this->field} ?? $value) ? 'checked' : '';
        $label = $this->config['label'] ?? '';
        $helpText = $this->config['helpText'] ?? $this->model->getFieldHelpText($this->field);
        $showInlineLabel = $this->config['showInlineLabel'] ?? true;
        
        $html = '<div class="switch-group" id="' . $this->field . 'SwitchGroup">';
        $html .= '    <div class="custom-switch tag-edit-switch" id="' . $this->field . 'Switch">';
        $html .= '        <input type="checkbox" id="' . $this->field . '" name="' . $this->field . '" ';
        $html .= '               value="' . htmlspecialchars($value) . '" ' . $checked . '>';
        $html .= '        <span class="switch-slider"></span>';
        $html .= '    </div>';
        
        // 右侧内联label - 根据配置决定是否显示
        if ($showInlineLabel && $label) {
            $html .= '    <label for="' . $this->field . '" class="switch-label">' . htmlspecialchars($label) . '</label>';
        }
        
        $html .= '</div>';
        
        if ($helpText) {
            $html .= '<div class="form-text">' . htmlspecialchars($helpText) . '</div>';
        }
        
        return $html;
    }
}
