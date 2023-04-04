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


if ($_SERVER["REQUEST_METHOD"] == "POST") {
//    $file->noob();
//    Request::print($file->getRequired());
//    Request::print($file->getRequests());
//    $file->min($file->getRequests(), $file);
//    $file->InitFiles();
    //  $file->getReqest();
    $file = Request::CheckFiles("post", [
        "file" => "required|min:1|max:5|ex:png,jpg,ejpg",
        "single" => "required|min:3|max:8|ex:png,jpg,ejpg,exe",
    ]);

    Request::print($file->getRequired());
    Request::print($file->getRequests());

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
//    return;
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

    <style>
        form {
            width: 100%;
            display: flex;
            align-content: center;
            justify-content: center;
            flex-flow: column;
            gap: 1rem;
        }

        form div {
            display: flex;
            align-content: flex-start;
            justify-content: center;
            flex-flow: column;

        }

        form div input {
            /*width: 100%;*/
            height: 2.4rem;
            margin-top: .4rem;
            border-radius: .4rem;
            border: 2px solid #757575;
            cursor: pointer;
        }
    </style>

</head>
<body>

<form action="" method="post" enctype="multipart/form-data">
    <div>
        <label for="">name</label>
        <input type="text" value="<?= isset($valid) ? $valid->value('name') : '' ?>" class="name" name="name">
        <!--        <p>--><?php //= $valid->error('name') ?? '' ?><!--</p>-->
        <?php
        if (isset($valid)) {
            echo "<p>" . $valid->error('name') . "</p>";
        }
        ?>
    </div>
    <div>
        <label for="">age</label>
        <input type="text" class="age" value="<?= isset($valid) ? $valid->value('age') : '' ?>" name="age">
        <?php
        if (isset($valid)) {
            echo "<p>" . $valid->error('age') . "</p>";
        }
        ?>
    </div>
    <div>
        <label for="">email</label>
        <input type="email" value="<?= isset($valid) ? $valid->value('email') : '' ?>" class="email" name="email">
        <?php
        if (isset($valid)) {
            echo "<p>" . $valid->error('email') . "</p>";
        }
        ?>
    </div>

    <div>
        <label for="">url</label>
        <input type="text" value="<?= isset($valid) ? $valid->value('url') : '' ?>" class="email" name="url">
        <?php
        if (isset($valid)) {
            echo "<p>" . $valid->error('url') . "</p>";
        }
        ?>
    </div>

    <div>
        <label for="">single</label>
        <input type="file" class="signle"/>

    </div>
    <div>
        <label for="">multiple</label>
        <input type="file" class="file" multiple>
    </div>
    <div>
        <input type="submit" name="btnsubmit" value="click">
    </div>
    <div>
        <input type="button" class="button" value="button">
    </div>
</form>

<script>
    const button = document.querySelector('.button');
    const file = document.querySelector('.file');
    const singleFile = document.querySelector(".signle");
    const name = document.querySelector('.name');
    const age = document.querySelector(".age");
    const email = document.querySelector('.email');
    button.onclick = () => {
        const fs = file.files;
        const si = singleFile.files[0];
        const fdata = new FormData();
        fdata.append('single', si);
        fdata.append('name', name.value);
        fdata.append('age', age.value);
        fdata.append('url', 'https://youtube.coj');
        fdata.append('email', email.value)
        // f.foreach(a => fdata.append('file[]', a));
        for (f of fs) {
            fdata.append('file[]', f)
        }

        fetch('/request.php', {
            method: "POST",
            // body: JSON.stringify({name: name.value, age: age.value})
            body: fdata
        }).then(res => {
            return res.json()
        }).then(data => console.log(data))
    }
</script>

</body>
</html>
