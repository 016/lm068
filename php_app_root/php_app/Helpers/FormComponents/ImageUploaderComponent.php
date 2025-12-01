<?php

namespace App\Helpers\FormComponents;

use App\Core\Model;

/**
 * 图片上传组件
 * 支持文件上传和预览功能
 */
class ImageUploaderComponent
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
     * 渲染图片上传组件
     */
    public function render(): string
    {
        $helpText = $this->config['helpText'] ?? '';
        $accept = $this->config['accept'] ?? 'image/*';
        $previewMethod = $this->config['previewMethod'] ?? 'getThumbnailUrl';
        
        $html = '<div class="thumbnail-section">';
        $html .= '    <div class="thumbnail-upload-area">';
        $html .= '        <input type="file" class="form-control" id="' . $this->field . 'Upload" name="' . $this->field . '" accept="' . htmlspecialchars($accept) . '">';
        $html .= '        <div class="form-text">上传缩略图文件 (支持 JPG、PNG、GIF、WEBP 格式)</div>';
        $html .= '    </div>';
        $html .= '    <div class="thumbnail-preview-container">';
        
        // 获取预览URL
        $previewUrl = '';
        if (method_exists($this->model, $previewMethod)) {
            $previewUrl = $this->model->$previewMethod();
        }
        
        if ($previewUrl) {
            $html .= '        <img src="' . htmlspecialchars($previewUrl) . '" alt="内容缩略图" class="thumbnail-preview" id="' . $this->field . 'Preview">';
        } else {
            $html .= '        <img src="" alt="暂无缩略图" class="thumbnail-preview" id="' . $this->field . 'Preview" style="display:none;">';
        }
        
        $html .= '    </div>';
        $html .= '</div>';
        
        if ($helpText) {
            $html .= '<div class="form-text">' . htmlspecialchars($helpText) . '</div>';
        }
        
        return $html;
    }
}
