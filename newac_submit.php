<?php
session_start();

if (isset($_GET["username"]) && isset($_GET["passwd"])) {
  $name = $_GET["username"];
  $password = $_GET["passwd"];

  $pdo = new PDO("sqlite:myblog.sqlite");
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
  $st = $pdo->prepare("select * from user where name==?;");
  $st->execute(array($name));
  $user_on_db = $st->fetch();

  if (!$user_on_db) {
    $result = "指定されたユーザーが存在しません。";
  } else if ($user_on_db["password"] == $password) {
    $result = "ようこそ" . $name . "さん。ログインに成功しました。";
    $_SESSION["user"] = $username;
  } else {
    $result = "パスワードが違います。";
  }
}
?>
<!DOCTYPE html>
<html>

    <head>
        <meta charset="utf-8">
        <title>Login success</title>
        <link rel="stylesheet" href="myblog_style.css">
    </head>

    <body>
        <div class="article">
            <h2><?php print $result; ?></h2>
            <p><a href="top.php">ブログのページに戻る</a></p>
        </div>
    </body>

</html>