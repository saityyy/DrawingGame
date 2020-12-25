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
    $_SESSION["user"] = $username;
    $_SESSION["id"] = $user_on_db["id"];
    header("Location:top.php");
    exit;
  } else {
    $result = "パスワードが違います。";
  }
}
?>
<!DOCTYPE html>
<html lang="ja">

    <head>
        <meta charset="utf-8">
        <title>ログイン</title>
    </head>
    <body>
        <div class=article>
            <h2>ユーザ名とパスワードを入力してください</h2>
            <form action="login_form.php" method="get">
                <p>ユーザ名</p>
                <input type="text" name="username">
                <p>パスワード</p>
                <input type="password" name="passwd">
                <input type="submit" value="送信">
            </form>
            <p><?$result?></p>
        </div>
    </body>

</html>