<?php

namespace App\Core;

use App\Core\Request;

abstract class Controller
{
    protected \App\Core\Request $request;

    public function __construct(Request $request)
    {

        $this->request = $request;

        $this->init();
    }

    /**
     * run init
     * @return void
     */
    public function init()
    {

    }

    protected function view(string $template, array $data = []): string
    {
        extract($data);
        
        ob_start();
        
        $templatePath = $this->getTemplatePath($template);
        
        if (!file_exists($templatePath)) {
            throw new \Exception("Template {$template} not found at {$templatePath}");
        }
        
        include $templatePath;
        
        return ob_get_clean();
    }

    protected function render(string $template, array $data = []): void
    {
        echo $this->view($template, $data);
    }

    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    protected function redirect(string $url, int $statusCode = 302): void
    {
        http_response_code($statusCode);
        header("Location: {$url}");
        exit;
    }

    protected function input(string $key, $default = null)
    {
        return $this->request->getInput($key, $default);
    }

    protected function validate(array $rules): array
    {
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            $value = $this->input($field);
            $ruleArray = is_string($rule) ? explode('|', $rule) : $rule;
            
            foreach ($ruleArray as $singleRule) {
                $error = $this->validateField($field, $value, $singleRule);
                if ($error) {
                    $errors[$field][] = $error;
                }
            }
        }
        
        return $errors;
    }

    private function validateField(string $field, $value, string $rule): ?string
    {
        switch ($rule) {
            case 'required':
                return empty($value) ? "{$field} is required" : null;
            case 'email':
                return $value && !filter_var($value, FILTER_VALIDATE_EMAIL) ? "{$field} must be a valid email" : null;
            default:
                if (strpos($rule, 'min:') === 0) {
                    $min = (int)substr($rule, 4);
                    return strlen($value) < $min ? "{$field} must be at least {$min} characters" : null;
                }
                if (strpos($rule, 'max:') === 0) {
                    $max = (int)substr($rule, 4);
                    return strlen($value) > $max ? "{$field} must not exceed {$max} characters" : null;
                }
                return null;
        }
    }

    abstract protected function getTemplatePath(string $template): string;
}