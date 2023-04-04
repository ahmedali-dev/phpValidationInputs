<?php


class noob
{

    public function run()
    {
        $this->initializeInput();
        $this->moreFilter();

    }

    private function initializeInput(): void
    {
        foreach ($this->rules as $key => $rule) {
            [$requestKey, $requestType] = explode('.', $key, 2);

            if ($_SERVER['REQUEST_METHOD'] !== $this->method) {
                continue;
            }

            if (!isset($_REQUEST[$requestKey])) {
                continue;
            }

            $this->filterRequest($requestType, $requestKey, $rule);
        }
    }


    private function filterRequest(string $type, string $requestKey, string $rules): void
    {
        $value = $_REQUEST[$requestKey];

        switch ($type) {
            case 'string':
                $value = $this->sanitize($value);
                $this->addRequest($requestKey, $type, $value, $rules);
                break;
            case 'email':
                $value = $this->sanitize($value, FILTER_SANITIZE_EMAIL);

                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $message = 'Email is not valid. Please check your email.';
                    $this->addRequest($requestKey, $type, $value, $rules, ucwords($message));
                    return;
                }

                if (!$this->isEmexAllowed($rules, $value, '@')) {
                    $message = 'Email is not allowed.';
                    $this->addRequest($requestKey, $type, $value, $rules, ucwords($message));
                    return;
                }

                $this->addRequest($requestKey, $type, $value, $rules);
                break;
            case 'int':
            case 'number':
                $value = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
                $this->addRequest($requestKey, $type, $value, $rules);
                break;
        }
    }

    private function sanitize(string $value, int $filter = FILTER_SANITIZE_STRING): string
    {
        return filter_var($value, $filter);
    }

    private function isEmexAllowed($rules, $value, $separator = '.', $rulsSearch = 'emex'): bool
    {
        $rules = explode("|", $rules);

        foreach ($rules as $rule) {
            if (substr($rule, 0, strpos($rule, ":")) === $rulsSearch) {
                $emex = explode(",", substr($rule, strpos($rule, ":") + 1));
                $array = explode($separator, $value);
                $value_emex = end($array);
                if (in_array($value_emex, $emex)) {
                    return true;
                } else {
                    return false;
                }

            }
        }

        return true;
    }

    private function moreFilter(): void
    {
        foreach ($this->request as $key => $req) {
            $rules = $req['rules'];
            $search = function ($rules, $search) {
                return strpos($rules, $search) !== false;
            };
            if ($search($rules, 'min:')) {
                $this->Min($key, $req);
            }

            if ($search($rules, 'max:')) {
                $this->Max($key, $req);
            }

            if ($search($rules, 'required')) {
                $this->Required($key, $req);
            }


        }
    }

    function Required($key, $req): bool
    {
        // TODO: Implement Required() method.
        if (empty($req['value'])) {
            $this->request[$key]['error'] = ucwords('this failed is required');
            return false;
        }
        return true;
    }

    public function lengthString($req, $rule, $num): bool
    {
        $length = strlen($req['value']);
        $min = $length < $num;
        $max = $length > $num;

        if ($min && $rule == 'min:') {

            return false;
        }
        if ($max && $rule == 'max:') {

            return false;
        }


        return true;
    }

    public function lengthNumber($req, $rule, $num): bool
    {
        $length = intval($req['value']);
        $min = $length < $num;
        $max = $length > $num;

        if ($min && $rule == 'min:') {
            return false;
        }
        if ($max && $rule == 'max:') {
            return false;
        }


        return true;
    }

    private function checkLength($key, $req, $msg = '', $type = 'min:'): bool
    {
        $rules = explode("|", $req['rules']);
        foreach ($rules as $rule) {
            if (strpos($rule, $rule) !== false) {
                $array = explode(":", $rule);
                $number = end($array);

                switch ($req['type']) {
                    case 'int':
                    case 'number':

                        break;
                    case 'string':
                    case 'email':

                        echo 'string <br>';
                        $check = $this->lengthString($req, $type, $number);
                        $check ? $this->request[$key]['error'] = ucwords($msg) : null;
                        break;
                }
//                if ($intcheck == 'int' || $intcheck == 'number') {
//                    if ($type === "min:") {
//                        if (intval($req['value']) < intval($number)) {
//                            $this->request[$key]['error'] = ucwords($msg);
//                            return false;
//                        }
//                    } else {
//                        if (intval($req['value']) > intval($number)) {
//                            $this->request[$key]['error'] = ucwords($msg);
//                            return false;
//                        }
//                    }
//                }
//                if ($type === "min:") {
//                    if (strlen($req['value']) < $number) {
//                        $this->request[$key]['error'] = ucwords($msg);
//                        return false;
//                    }
//                } else {
//                    if (strlen($req['value']) > $number) {
//                        $this->request[$key]['error'] = ucwords($msg);
//                        return false;
//                    }
//                }

            }
        }
        return true;
    }

    private function length($req, $msg, $rule = 'min:', $key = null)
    {
        $defaultRule = $rule;
        $filter = array_filter(
            explode("|", $req['rules']),
            function ($rule) use ($defaultRule) {
                return substr($rule, 0, 4) == $defaultRule;
            }
        );

        $filter = intval(substr(implode('', $filter), 4));
        echo "<pre>";
        var_dump($filter);
        echo "</pre>";
//        $type = $req['type'];

        switch ($req['type']) {
            case 'int':
            case 'number':
                echo 'int' . br;
                $check = $this->lengthNumber($req, $defaultRule, $filter);
                !$check ? $this->request[$key]['error'] = ucwords($msg) : null;
                break;
            case 'string':
            case 'email':
            case 'text':
                echo 'string <br>';
                $check = $this->lengthString($req, $defaultRule, $filter);
                !$check ? $this->request[$key]['error'] = ucwords($msg) : null;
                break;
        }


        return true;
    }

    function Min($key, $req): bool
    {
        // TODO: Implement Min() method.
        return $this->length($req, "the $key is too short", key: $key);
    }

    function Max($key, $req): bool
    {
        // TODO: Implement Max() method.
        return $this->length($req, "the $key is too long", 'max:', $key);
//        return false;
    }


}
