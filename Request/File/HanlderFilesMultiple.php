<?php


trait HanlderFilesMultiple
{

    protected function MultiMin($requestName, $value, $size): void
    {
        foreach ($value['size'] as $item => $requestValues) {
            var_dump($item);
            $compSize = $size * $this->fileSize;
            if ($requestValues < $compSize):
                echo $compSize . 'min' . br;
                $this->requests[$requestName]['error'][$item] = "file should you large then {$size}mb";
            endif;
        }
    }

    protected function MultiMax($requestName, $value, $size): void
    {
        foreach ($value['size'] as $item => $requestValues) {
            var_dump($item);
            $compSize = $size * $this->fileSize;
            if ($requestValues > $compSize):
                echo $compSize . 'max' . br;
                $this->requests[$requestName]['error'][$item] = "file should you less then {$size}mb";
            endif;
        }
    }

    protected function MultiIsAllowExtesion($requestName, $value, $ex): void
    {
        foreach ($value['name'] as $item => $requestValues) {
            $fileEx = explode(".", $requestValues);
            $fileEx = end($fileEx);
            if (!in_array($fileEx, $ex)) {
                $this->requests[$requestName]['error'][$item] = "file not allow";
            }
        }
    }

}