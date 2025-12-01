<?php

namespace App\Helpers;

use App\Core\Model;
use App\Helpers\FormComponents\SwitchComponent;
use App\Helpers\FormComponents\PreviewComponent;
use App\Helpers\FormComponents\MultiSelectComponent;
use App\Helpers\FormComponents\ImageUploaderComponent;
use App\Helpers\FormComponents\DynamicTextareaComponent;
use App\Helpers\FormComponents\CustomSelectComponent;

/**
 * 表单字段构建器
 * 
 * 提供流式 API 用于构建完整的表单字段 HTML
 * 支持标准HTML元素和自定义组件
 * 
 * 使用示例:
 * ```php
 * // 标准输入
 * echo FormFieldBuilder::for($model, 'name')->label('名称')->render();
 * 
 * // 自定义组件
 * echo FormFieldBuilder::for($model, 'status_id')->type('switch')->render();
 * 
 * // 格式化显示
 * echo FormFieldBuilder::for($model, 'id')->formatter(fn($v) => '#' . $v)->render();
 * ```
 */
class FormFieldBuilder
{
    private Model $model;
    private string $field;
    private array $config = [];
    
    /**
     * 构造函数
     */
    private function __construct(Model $model, string $field)
    {
        $this->model = $model;
        $this->field = $field;
        
        // 默认配置
        $this->config = [
            'type' => 'text',
            'label' => null,
            'helpText' => null,
            'rawHelpText' => false, // 是否将 helpText 作为原始 HTML 渲染
            'placeholder' => null,
            'cssClass' => 'col-md-6 pb-3',
            'inputClass' => 'form-control',
            'disabled' => false,
            'options' => [], // for select
            'rows' => 3, // for textarea
            'wrapperClass' => 'form-group',
            'formatter' => null, // 值格式化函数
            'value' => null, // 自定义 value (用于 switch 等)
            'selected' => [], // 用于 multi-select
            'iconField' => 'icon_class', // 用于 preview
            'textField' => 'name_cn', // 用于 preview
            'defaultText' => '新标签', // 用于 preview
            'containerId' => null, // 用于 multi-select
            'showTopLabel' => true, // 是否显示上部标签（对switch等组件默认为false）
            'showInlineLabel' => true, // 是否显示内联标签（如switch右侧的label）
            'accept' => 'image/*', // for image-uploader
            'previewMethod' => 'getThumbnailUrl', // for image-uploader
            'sizeButtons' => null, // for dynamic-textarea
            'useHtmlHelper' => false, // for dynamic-textarea
            'bottomHelpText' => null, // for dynamic-textarea
            'maxlength' => null, // for textarea
            'required' => false, // for all fields
        ];
    }
    
    /**
     * 静态工厂方法
     * 
     * @param Model $model 模型实例
     * @param string $field 字段名
     * @return self
     */
    public static function for(Model $model, string $field): self
    {
        return new self($model, $field);
    }
    
    /**
     * 设置字段类型
     * 
     * @param string $type 字段类型：text, textarea, select, checkbox 等
     * @return $this
     */
    public function type(string $type): self
    {
        $this->config['type'] = $type;
        
        // 某些类型默认不显示上部label（因为有自己的内联label）
        if (in_array($type, ['switch'])) {
            $this->config['showTopLabel'] = false;
        }
        
        return $this;
    }
    
    /**
     * 设置标签文本
     * 
     * @param string $label 标签文本
     * @return $this
     */
    public function label(string $label): self
    {
        $this->config['label'] = $label;
        return $this;
    }
    
    /**
     * 设置帮助文本
     * 
     * @param string $text 帮助文本
     * @param bool $raw 是否作为原始 HTML（不进行转义）
     * @return $this
     */
    public function helpText(string $text, bool $raw = false): self
    {
        $this->config['helpText'] = $text;
        $this->config['rawHelpText'] = $raw;
        return $this;
    }
    
    /**
     * 设置占位符
     * 
     * @param string $placeholder 占位符文本
     * @return $this
     */
    public function placeholder(string $placeholder): self
    {
        $this->config['placeholder'] = $placeholder;
        return $this;
    }
    
    /**
     * 设置外层容器 CSS 类
     * 
     * @param string $class CSS 类名
     * @return $this
     */
    public function cssClass(string $class): self
    {
        $this->config['cssClass'] = $class;
        return $this;
    }
    
    /**
     * 设置选项 (用于 select)
     * 
     * @param array $options 选项数组，格式: ['value' => 'label']
     * @return $this
     */
    public function options(array $options): self
    {
        $this->config['options'] = $options;
        return $this;
    }
    
    /**
     * 设置为禁用状态
     * 
     * @param bool $disabled 是否禁用
     * @return $this
     */
    public function disabled(bool $disabled = true): self
    {
        $this->config['disabled'] = $disabled;
        return $this;
    }
    
    /**
     * 设置文本域行数
     * 
     * @param int $rows 行数
     * @return $this
     */
    public function rows(int $rows): self
    {
        $this->config['rows'] = $rows;
        return $this;
    }
    
    /**
     * 设置值格式化函数
     * 
     * @param callable $formatter 格式化函数
     * @return $this
     */
    public function formatter(callable $formatter): self
    {
        $this->config['formatter'] = $formatter;
        return $this;
    }
    
    /**
     * 设置自定义值 (用于 switch 等)
     * 
     * @param mixed $value 值
     * @return $this
     */
    public function value($value): self
    {
        $this->config['value'] = $value;
        return $this;
    }
    
    /**
     * 设置选中的项 (用于 multi-select)
     * 
     * @param array $selected 选中的ID数组
     * @return $this
     */
    public function selected(array $selected): self
    {
        $this->config['selected'] = $selected;
        return $this;
    }
    
    /**
     * 设置容器ID (用于 multi-select)
     * 
     * @param string $id 容器ID
     * @return $this
     */
    public function containerId(string $id): self
    {
        $this->config['containerId'] = $id;
        return $this;
    }
    
    /**
     * 设置最大长度
     * 
     * @param int $length 最大长度
     * @return $this
     */
    public function maxlength(int $length): self
    {
        $this->config['maxlength'] = $length;
        return $this;
    }
    
    /**
     * 设置高度调整按钮（用于 dynamic-textarea）
     * 
     * @param array $buttons 按钮配置，如 ['小' => 7, '中' => 12, '大' => 20]
     * @return $this
     */
    public function sizeButtons(array $buttons): self
    {
        $this->config['sizeButtons'] = $buttons;
        return $this;
    }
    
    /**
     * 设置是否使用 HtmlHelper（用于 dynamic-textarea）
     * 
     * @param bool $use 是否使用
     * @return $this
     */
    public function useHtmlHelper(bool $use = true): self
    {
        $this->config['useHtmlHelper'] = $use;
        return $this;
    }
    
    /**
     * 设置底部帮助文本（用于 dynamic-textarea）
     * 
     * @param string $text 帮助文本
     * @return $this
     */
    public function bottomHelpText(string $text): self
    {
        $this->config['bottomHelpText'] = $text;
        return $this;
    }
    
    /**
     * 设置是否显示上部标签
     * 
     * @param bool $show 是否显示
     * @return $this
     */
    public function showTopLabel(bool $show = true): self
    {
        $this->config['showTopLabel'] = $show;
        return $this;
    }
    
    /**
     * 设置是否显示内联标签（如switch右侧的label）
     * 
     * @param bool $show 是否显示
     * @return $this
     */
    public function showInlineLabel(bool $show = true): self
    {
        $this->config['showInlineLabel'] = $show;
        return $this;
    }
    
    /**
     * 渲染完整字段 HTML
     * 使用策略模式分发到不同的渲染器
     * 
     * @return string HTML 字符串
     */
    public function render(): string
    {
        $type = $this->config['type'];
        
        // 自定义组件类型 - 使用专门的组件渲染器
        if ($this->isCustomComponent($type)) {
            return $this->renderCustomComponent();
        }
        
        // 标准HTML元素 - 使用标准渲染流程
        return $this->renderStandardField();
    }
    
    /**
     * 判断是否为自定义组件
     */
    private function isCustomComponent(string $type): bool
    {
        return in_array($type, ['switch', 'preview', 'multi-select', 'image-uploader', 'dynamic-textarea', 'custom-select']);
    }
    
    /**
     * 渲染自定义组件
     */
    private function renderCustomComponent(): string
    {
        $type = $this->config['type'];
        $html = '';
        
        // 外层容器
        if ($this->config['cssClass']) {
            $html .= '<div class="' . htmlspecialchars($this->config['cssClass']) . '">';
        }
        
        $html .= '<div class="' . htmlspecialchars($this->config['wrapperClass']) . '">';
        
        // 渲染 label - 根据配置决定是否显示
        if ($this->config['showTopLabel']) {
            $html .= $this->renderLabel();
        }
        
        // 根据类型分发到对应的组件
        switch ($type) {
            case 'switch':
                $component = new SwitchComponent($this->model, $this->field, $this->config);
                $html .= $component->render();
                break;
                
            case 'preview':
                $component = new PreviewComponent($this->model, $this->field, $this->config);
                $html .= $component->render();
                break;
                
            case 'multi-select':
                $component = new MultiSelectComponent($this->model, $this->field, $this->config);
                $html .= $component->render();
                break;
                
            case 'image-uploader':
                $component = new ImageUploaderComponent($this->model, $this->field, $this->config);
                $html .= $component->render();
                break;
                
            case 'dynamic-textarea':
                $component = new DynamicTextareaComponent($this->model, $this->field, $this->config);
                $html .= $component->render();
                break;
                
            case 'custom-select':
                $component = new CustomSelectComponent($this->model, $this->field, $this->config);
                $html .= $component->render();
                break;
        }
        
        $html .= '</div>'; // .form-group
        
        if ($this->config['cssClass']) {
            $html .= '</div>'; // .col-md-6
        }
        
        return $html;
    }
    
    /**
     * 渲染标准字段
     */
    private function renderStandardField(): string
    {
        $html = '';
        
        // 外层容器
        if ($this->config['cssClass']) {
            $html .= '<div class="' . htmlspecialchars($this->config['cssClass']) . '">';
        }
        
        $html .= '<div class="' . htmlspecialchars($this->config['wrapperClass']) . '">';
        
        // 渲染 label
        $html .= $this->renderLabel();
        
        // 渲染输入框
        $html .= $this->renderInput();
        
        // 渲染错误/成功反馈
        $html .= $this->renderFeedback();
        
        // 渲染帮助文本
        $html .= $this->renderHelpText();
        
        $html .= '</div>'; // .form-group
        
        if ($this->config['cssClass']) {
            $html .= '</div>'; // .col-md-6
        }
        
        return $html;
    }
    
    /**
     * 渲染标签
     */
    private function renderLabel(): string
    {
        $label = $this->config['label'] ?? $this->model->getFieldLabel($this->field);
        if (!$label) {
            return '';
        }
        
        $requiredClass = FormHelper::isRequired($this->model, $this->field) ? 'required' : '';
        
        return sprintf(
            '<label for="%s" class="form-label %s">%s</label>',
            htmlspecialchars($this->field),
            $requiredClass,
            htmlspecialchars($label)
        );
    }
    
    /**
     * 渲染输入框
     */
    private function renderInput(): string
    {
        $type = $this->config['type'];
        
        switch ($type) {
            case 'textarea':
                return $this->renderTextarea();
            case 'select':
                return $this->renderSelect();
            default:
                return $this->renderTextInput();
        }
    }
    
    /**
     * 渲染文本输入框
     */
    private function renderTextInput(): string
    {
        $attrs = FormHelper::getFieldAttributes($this->model, $this->field);
        
        // 基础属性
        $attrs['type'] = $attrs['type'] ?? $this->config['type'];
        $attrs['id'] = $this->field;
        $attrs['name'] = $this->field;
        
        // 值处理 - 支持 formatter
        $value = $this->model->{$this->field} ?? '';
        if ($this->config['formatter'] && is_callable($this->config['formatter'])) {
            $value = call_user_func($this->config['formatter'], $value);
        }
        $attrs['value'] = htmlspecialchars($value);
        
        // CSS 类
        $inputClass = $this->config['inputClass'];
        if (!empty($this->model->errors[$this->field])) {
            $inputClass .= ' is-invalid';
        }
        $attrs['class'] = $inputClass;
        
        // 占位符
        if ($this->config['placeholder']) {
            $attrs['placeholder'] = $this->config['placeholder'];
        }
        
        // 禁用
        if ($this->config['disabled']) {
            $attrs['disabled'] = true;
        }
        
        return '<input ' . FormHelper::renderAttributes($attrs) . '>';
    }
    
    /**
     * 渲染文本域
     */
    private function renderTextarea(): string
    {
        $attrs = FormHelper::getFieldAttributes($this->model, $this->field);
        
        $attrs['id'] = $this->field;
        $attrs['name'] = $this->field;
        $attrs['rows'] = $this->config['rows'];
        
        $inputClass = $this->config['inputClass'];
        if (!empty($this->model->errors[$this->field])) {
            $inputClass .= ' is-invalid';
        }
        $attrs['class'] = $inputClass;
        
        if ($this->config['placeholder']) {
            $attrs['placeholder'] = $this->config['placeholder'];
        }
        
        $value = htmlspecialchars($this->model->{$this->field} ?? '');
        
        return sprintf(
            '<textarea %s>%s</textarea>',
            FormHelper::renderAttributes($attrs),
            $value
        );
    }
    
    /**
     * 渲染下拉框
     */
    private function renderSelect(): string
    {
        $attrs = [
            'id' => $this->field,
            'name' => $this->field,
            'class' => $this->config['inputClass'],
        ];
        
        if ($this->config['disabled']) {
            $attrs['disabled'] = true;
        }
        
        $html = '<select ' . FormHelper::renderAttributes($attrs) . '>';
        
        $currentValue = $this->model->{$this->field} ?? null;
        
        foreach ($this->config['options'] as $value => $label) {
            $selected = ($currentValue == $value) ? 'selected' : '';
            $html .= sprintf(
                '<option value="%s" %s>%s</option>',
                htmlspecialchars($value),
                $selected,
                htmlspecialchars($label)
            );
        }
        
        $html .= '</select>';
        
        return $html;
    }
    
    /**
     * 渲染错误/成功反馈
     */
    private function renderFeedback(): string
    {
        $html = '';
        
        if (!empty($this->model->errors[$this->field])) {
            // 错误反馈
            $html .= sprintf(
                '<div class="invalid-feedback">%s</div>',
                htmlspecialchars($this->model->errors[$this->field])
            );
        } else {
            // 成功反馈
            $html .= '<div class="valid-feedback">验证通过</div>';
        }
        
        return $html;
    }
    
    /**
     * 渲染帮助文本
     */
    private function renderHelpText(): string
    {
        $helpText = $this->config['helpText'] ?? $this->model->getFieldHelpText($this->field);
        
        if (!$helpText) {
            return '';
        }
        
        // 根据配置决定是否转义 HTML
        $content = $this->config['rawHelpText'] ? $helpText : htmlspecialchars($helpText);
        
        return sprintf(
            '<div class="form-text">%s</div>',
            $content
        );
    }
}
