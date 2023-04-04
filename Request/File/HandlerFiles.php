<?php
require_once __DIR__ . '/HanlderFilesMultiple.php';
define('br', "<br>");

class HandlerFiles extends Files
{

    use HanlderFilesMultiple;

    protected int $fileSize = 1048576;

    public function __construct($method, $rules)
    {
        parent::__construct($method, $rules);

        if (!$this->CheckRequestMethod()) {
            throw new Exception("Request Method not Match");
            return;
        }

        $this->init();
//        if (!$this->Required()):
        $this->min();
        $this->max();
        $this->isAllowExtension();
        $this->Required();
//        endif;
    }

    private function RuleFilterValue($rules, $search): string|bool
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

    protected function RequiredEmptyRequest($requestName): bool
    {
        return isset($this->required[$requestName]);
    }

    public function Required(): bool
    {

        $status = false;
        foreach ($this->required as $item => $rule) {
            $rq = $this->RuleFilterValue($rule, "required");


            if ($rq) {
                echo $item . br;
                $this->setError($item, "request {$item} is required");
                $status = true;
                continue;
            }
            $status = false;
        }

        return $status;
    }

    public function min(): void
    {
        foreach ($this->requests as $request => $value) {
            $size = intval($this->RuleFilterValue($value['rules'], "min:"));
            if (is_string($value['name'])) {
                echo 'asfdas' . br;
                if ($size !== false) {
                    $compSize = $size * $this->fileSize;
                    if ($value['size'] < $compSize):
                        echo $compSize . 'min' . br;
                        $this->setError($request, "file should you large then {$size}mb");
                    endif;
                }
            } else {
                $this->MultiMin($request, $value, $size);
            }
        }
    }

    public function max(): bool
    {
        foreach ($this->requests as $request => $value) {
            $size = intval($this->RuleFilterValue($value['rules'], "max:"));
            if (is_string($value['name'])) {
                if ($size !== false) {
                    $compSize = $size * $this->fileSize;
                    if ($value['size'] > $compSize):
                        echo $compSize . 'max' . br;
                        $this->setError($request, "file should you less then {$size}mb");
                        return false;
                    endif;
                }
            } else {
                $this->MultiMax($request, $value, $size);
            }
        }
        return true;
    }

    public function isAllowExtension(): bool
    {
        foreach ($this->requests as $request => $value) {


            $ex = $this->RuleFilterValue($value['rules'], "ex:");
            if ($ex !== false):
                $ex = explode(",", $ex);
                if (is_string($value['name'])) {
                    $fileEx = explode(".", $value['name']);
                    $fileEx = end($fileEx);
                    if (!in_array($fileEx, $ex)) {
                        $this->setError($request, "file not allow");
                        return false;
                    }
                } else {
                    $this->MultiIsAllowExtesion($request, $value, $ex);
                }
            endif;
        }
        return true;
    }
}