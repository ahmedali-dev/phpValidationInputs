<?php
spl_autoload_register(function ($classname) {
    $dir = __DIR__ . "/";
    $paths = ["./Request/File/", "./Request/Init/", "./Request/Input/"];

    foreach ($paths as $path) {
        $url = $dir . $path . $classname . ".php";
        if (file_exists($url)) {
            require_once $url;
        }
    }
});

class Request
{
    /**
     * @param string $method
     * @param array $rules
     * @return HandlerFiles
     */
    public static function CheckFiles(string $method, array $rules)
    {
        return new HandlerFiles($method, $rules);
    }

    public static function CheckInput(string $method, array $rules)
    {
        return new HanlderInputValidator($method, $rules);
    }

    public static function print($p): void
    {
        echo "<pre>";
        var_dump($p);
        echo "</pre>";
    }
}

$req = new Request();
// $valid = $req->InitFile("post", [
//   "name.string" => "required|min:20|max:40",
//   "age.int" => "required|min:10|max:100",
// ]);
// $valid->validation();
// $req->print($valid->getRequest());
//
// echo $valid->Error("age") . br;
// echo $valid->Value("name") . br;

$file = Request::CheckFiles("post", [
    "file" => "required|min:1|max:5|ex:png,jpg,ejpg",
    "single" => "required|min:3|max:8|ex:png,jpg,ejpg,exe",
]);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
//    $file->noob();
//    Request::print($file->getRequired());
//    Request::print($file->getRequests());
//    $file->min($file->getRequests(), $file);
//    $file->InitFiles();
    //  $file->getReqest();

    $valid = Request::CheckInput("post", [
        "name.string" => "required|min:20|max:40",
        "age.int" => "required|min:10|max:50",
        "email.email" => "required|min:10|max:50|ex:gmail.com,hotmail.com,yahoo.com",
        "url.url" => "required"
    ]);
//    $valid->init();
    $valid->getrequired();
    echo 'requests------------------' . br;
    $valid->getRequests();
    return;
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>

<form action="" enctype="multipart/form-data">
    <input type="text" class="name">
    <input type="text" class="age">
    <!--    <input type="file" class="signle"/>-->
    <!--    <input type="file" class="file" multiple>-->
    <input type="button" class="button" value="button">
</form>

<script>
    const button = document.querySelector('.button');
    // const file = document.querySelector('.file');
    // const singleFile = document.querySelector(".signle");
    const name = document.querySelector('.name');
    const age = document.querySelector(".age");
    button.onclick = () => {
        // const fs = file.files;
        // const si = singleFile.files[0];
        // const fdata = new FormData();
        // fdata.append('single', si);
        // // f.foreach(a => fdata.append('file[]', a));
        // for (f of fs) {
        //     fdata.append('file[]', f)
        // }
        fetch('/request.php', {
            method: "POST",
            body: JSON.stringify({name: name.value, age: age.value})
        }).then(res => {
            return res.json()
        }).then(data => console.log(data))
    }
</script>

</body>
</html>
