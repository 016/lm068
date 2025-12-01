<?php

namespace App\Helpers\FormComponents;

use App\Core\Model;

/**
 * 多选组件
 * 
 * 用于渲染项目特有的多选组件
 */
class MultiSelectComponent
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
     * 渲染多选组件
     * 
     * @return string HTML 字符串
     */
    public function render(): string
    {
        $helpText = $this->config['helpText'] ?? $this->model->getFieldHelpText($this->field);
        $containerId = $this->config['containerId'] ?? $this->field . 'MultiSelect';
        
        // 多选组件主要依赖前端 JavaScript 初始化
        // 这里只渲染容器，数据通过 window.inputData 传递给 JS
        $html = '<div id="' . htmlspecialchars($containerId) . '" class="multi-select-container"></div>';
        
        if ($helpText) {
            $html .= '<div class="form-text">' . htmlspecialchars($helpText) . '</div>';
        }
        
        return $html;
    }
}
