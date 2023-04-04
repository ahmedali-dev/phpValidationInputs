<?php
//
//require_once __DIR__.'/noob.php';
//
//
//class Input {
//    private $request,$data;
//    private $validation;
//    private $method,$rules;
//    public function __construct($requests, $method, $rules)
//    {
//        $this->data = $requests;
//        $this->method = strtoupper($method);
//        $this->rules = $rules;
////        $this->validation = $validation->getData();
//    }
//
//
//    public function validation()
//    {
//        $this->validation = new noob($this->data, $this->method, $this->rules);
//        $this->validation->run();
//        $this->request = $this->validation->getData();
//    }
//
//    public function Error($key)  {
//        return $this->validation->Error($key);
//    }
//
//    public function Value($key) {
//        return $this->validation->Value($key);
//    }
//
//
//    /**
//     * @return mixed
//     */
//    public function getValidation()
//    {
//        return $this->validation;
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getRequest()
//    {
//        return $this->request;
//    }
//}
//define('br', "<br>");

class Input
{
    protected array $requests, $rules, $data, $required;
    protected string $method;

    /**
     * @param array $rules
     * @param string $method
     */
    public function __construct(string $method, array $rules)
    {
        $this->rules = $rules;
        $this->method = $method;
        $this->data = [];
        $this->requests = [];
        $this->required = [];

        // run init function
        $this->init();
    }

    protected function CheckRequestMethod(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === strtoupper($this->method);
    }

    protected function getRequestType(): bool
    {
        if (!empty($_REQUEST)) {
            return true;
        }

        return false;
    }

    protected function checkRequestStatus($request): bool|string
    {

        $status = false;
        $input = null;
        if ($this->getRequestType()) {
            if (isset($_REQUEST[$request])) {
                $status = true;
                $input = $_REQUEST[$request];
            }
        } else {
            $input = json_decode(file_get_contents("php://input"), true);
            if (isset($input[$request])) {
                $status = true;
                $input = $input[$request];

            }
        }


        if ($status) {
            return $input;
        }
        return $status;
    }

    protected function init(): void
    {
        foreach ($this->rules as $requestName => $rule) {
            [$request, $type] = explode(".", $requestName);
            if ($this->checkRequestStatus($request) !== false) {
                $value = [
                    "value" => $this->checkRequestStatus($request),
                    "type" => $type,
                    "rules" => $rule,
                    "error" => ""
                ];
                $this->addRequest($request, $value);
                continue;
            }

            $this->setRequired($request, $rule);

        }
    }

    protected function addRequest($requestName, $value): void
    {
        $this->requests[$requestName] = $value;
    }

    protected function setRequired($requestname, $rules): void
    {
        $this->required[$requestname] = $rules;
    }

    public function getrequired(): void
    {
        $this->print($this->required);
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
    public function getRequests()
    {
        $this->print($this->requests);
    }

    public function print($p)
    {
        echo "<pre>";
        var_dump($p);
        echo "</pre>";
    }

}