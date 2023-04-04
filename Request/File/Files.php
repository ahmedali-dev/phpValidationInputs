<?php

class Files
{
    protected array $requests;
    protected string $method;
    protected array $rules;
    protected array $required;

    public function __construct($method, $rules)
    {
        $this->requests = [];
        $this->required = [];
        $this->method = $method;
        $this->rules = $rules;


    }

    protected function CheckRequestMethod(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === strtoupper($this->method);
    }


    protected function init(): void
    {
        foreach ($this->rules as $requestName => $rule):
            // is set reqest name
            if (isset($_FILES[$requestName])) {
                $file = $_FILES[$requestName];
                $file['rules'] = $rule;
                $this->SaveRequest($file, $requestName);
                continue;
            }

            $this->setRequired($requestName, $rule);

        endforeach;
    }

    protected function SaveRequest($file, $requestName): void
    {
        $this->requests[$requestName] = $file;
    }

    protected function setRequired($file, $rule): void
    {
        $this->required[$file] = $rule;
    }

    public function getRequired()
    {
        return $this->required;
    }


    protected function setError($requestName, $error): void
    {
        if (!isset($this->requests[$requestName])):
            $this->requests[$requestName] = [
                'error' => ucwords($error)
            ];
            return;
        endif;

        $this->requests[$requestName]['error'] = ucwords($error);
    }

    /**
     * @return array
     */
    public function getRequests(): array
    {
        return $this->requests;
    }


}