<?php

namespace App\Helpers\FormComponents;

use App\Core\Model;

/**
 * 预览组件
 * 
 * 用于渲染标签预览按钮等预览组件
 */
class PreviewComponent
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
     * 渲染预览组件
     * 
     * @return string HTML 字符串
     */
    public function render(): string
    {
        // 从配置或 model 获取预览所需的数据
        $iconField = $this->config['iconField'] ?? 'icon_class';
        $textField = $this->config['textField'] ?? 'name_cn';
        $defaultText = $this->config['defaultText'] ?? '新标签';
        
        $iconClass = htmlspecialchars($this->model->{$iconField} ?? 'bi-star');
        $text = htmlspecialchars($this->model->{$textField} ?? $defaultText);
        $helpText = $this->config['helpText'] ?? $this->model->getFieldHelpText($this->field);
        
        $html = '<div class="tag-preview-container">';
        $html .= '    <button type="button" id="tagPreviewBtn" class="btn btn-outline-primary">';
        $html .= '        <i class="bi ' . $iconClass . '" id="previewIcon"></i>';
        $html .= '        <span id="previewText">' . $text . '</span>';
        $html .= '    </button>';
        $html .= '</div>';
        
        if ($helpText) {
            $html .= '<div class="form-text">' . htmlspecialchars($helpText) . '</div>';
        }
        
        return $html;
    }
}
