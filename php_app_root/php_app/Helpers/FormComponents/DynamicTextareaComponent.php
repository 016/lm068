<?php

namespace App\Helpers\FormComponents;

use App\Core\Model;
use App\Helpers\HtmlHelper;

/**
 * 动态 Textarea 组件
 * 支持高度调整按钮
 */
class DynamicTextareaComponent
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
     * 渲染动态 Textarea
     */
    public function render(): string
    {
        $helpText = $this->config['helpText'] ?? $this->model->getFieldHelpText($this->field);
        $placeholder = $this->config['placeholder'] ?? '';
        $rows = $this->config['rows'] ?? 7;
        $maxlength = $this->config['maxlength'] ?? null;
        $sizeButtons = $this->config['sizeButtons'] ?? ['小' => 7, '中' => 12, '大' => 20];
        $useHtmlHelper = $this->config['useHtmlHelper'] ?? false;
        
        $hasError = !empty($this->model->errors[$this->field]);
        $inputClass = 'form-control' . ($hasError ? ' is-invalid' : '');
        
        $html = '';
        
        // 高度调整按钮和 helpText
        if ($sizeButtons || $helpText) {
            $html .= '<div class="d-flex justify-content-between align-items-center mb-2">';
            $html .= '    <div></div>'; // 占位符
            if ($helpText) {
                $html .= '    <div class="form-text">' . htmlspecialchars($helpText) . '</div>';
            }
            if ($sizeButtons) {
                $html .= '    <div class="btn-group btn-group-sm" role="group" aria-label="调整描述框高度">';
                foreach ($sizeButtons as $label => $rowCount) {
                    $html .= '        <button type="button" class="btn btn-outline-secondary textarea-height-btn" data-target="' . $this->field . '" data-rows="' . $rowCount . '">' . htmlspecialchars($label) . '</button>';
                }
                $html .= '    </div>';
            }
            $html .= '</div>';
        }
        
        // Textarea
        $value = $this->model->{$this->field} ?? '';
        if ($useHtmlHelper) {
            $value = HtmlHelper::escape($value);
        }
        
        $html .= '<textarea class="' . $inputClass . '" id="' . $this->field . '" name="' . $this->field . '" rows="' . $rows . '"';
        if ($placeholder) {
            $html .= ' placeholder="' . htmlspecialchars($placeholder) . '"';
        }
        if ($maxlength) {
            $html .= ' maxlength="' . $maxlength . '"';
        }
        if ($this->config['required'] ?? false) {
            $html .= ' required';
        }
        $html .= '>' . $value . '</textarea>';
        
        // Error feedback
        if ($hasError) {
            $html .= '<div class="invalid-feedback">' . htmlspecialchars($this->model->errors[$this->field]) . '</div>';
        }
        
        // Help text (底部)
        if (isset($this->config['bottomHelpText'])) {
            $html .= '<div class="form-text">' . htmlspecialchars($this->config['bottomHelpText']) . '</div>';
        }
        
        return $html;
    }
}
