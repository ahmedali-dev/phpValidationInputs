<?php
require_once __DIR__ . '/Input.php';
require_once __DIR__ . '/MoreOptions.php';

class HanlderInputValidator extends Input
{
    use MoreOptions;

    public function __construct(string $method, array $rules)
    {
        parent::__construct($method, $rules);


        if (!$this->required()):
            $this->Filter();
            $this->min();
            $this->max();
            $this->isAllowExtensionEmail();
        endif;
    }


    protected function RuleFilterValue($rules, $search): string|bool
    {
        $filter = array_filter(explode("|", $rules), function ($rule) use ($search) {
            return strpos($rule, $search) !== false;
        });

        if (!empty($filter)) {
            if (str_contains(implode(":", $filter), ":")) {
                [$rule, $value] = explode(":", implode(":", $filter), 2);
                return $value;
            }
            return implode("", $filter);


        }

        return false;

    }

    protected function Required(): bool
    {

        foreach ($this->required as $item => $rule) {
            $rq = $this->RuleFilterValue($rule, "required");

            echo $rq . br;
            if ($rq !== false) {
                $this->setError($item, "request {$item} is required");
                return true;
            }
            echo 'error' . br;
            return false;
        }

        return false;
    }

    protected function Filter(): void
    {
        foreach ($this->requests as $request => $v) {
            $type = $v['type'];
            $value = htmlspecialchars($v['value'], ENT_QUOTES, 'UTF-8');
            $item = 'value';

            switch ($type) {
                case "string":
                    $value = filter_var($value, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
                    $this->overWriteRequests($request, $item, $value);
                    break;
                case "email":
                    echo 'email' . br;
                    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $this->setError($request, "email is not valid");
                    } else {
                        $value = filter_var($value, FILTER_SANITIZE_EMAIL);
                        $this->overWriteRequests($request, $item, $value);
                    }
                    break;
                case "number":
                case "int":
                    if (!filter_var($value, FILTER_VALIDATE_INT)) {
                        $this->setError($request, "type not valid");
                    } else {
                        $value = filter_var($value, FILTER_VALIDATE_INT);
                        $this->overWriteRequests($request, $item, $value);
                    }
                    break;
                case "float":
                    if (!filter_var($value, FILTER_VALIDATE_FLOAT)) {
                        $this->setError($request, "type not valid");
                    } else {
                        $value = filter_var($value, FILTER_VALIDATE_FLOAT);
                        $this->overWriteRequests($request, $item, $value);
                    }


                    break;
                case "url":
                    if (!filter_var($value, FILTER_VALIDATE_URL)) {
                        $this->setError($request, "type not valid");
                    } else {
                        $value = filter_var($value, FILTER_VALIDATE_URL);
                        $this->overWriteRequests($request, $item, $value);
                    }
                    break;
                default:
                    throw new Exception("Request Type not found");

            }
        }
    }

    protected function overWriteRequests($request, $item, $value)
    {
        $this->requests[$request][$item] = $value;
    }

}

