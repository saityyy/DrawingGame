<?php
session_start();
if (isset($_GET["username"]) && isset($_GET["password"])) {
  $username = $_GET["username"];
  $password = $_GET["password"];

  $pdo = new PDO("sqlite:data.sqlite");
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
  $st = $pdo->prepare("select * from user where name==?;");
  $st->execute(array($username));
  $data = $st->fetch();

  if (!$data) {
    $result = "指定されたユーザーが存在しません。";
  } else if ($data["password"] == $password) {
    echo $username;
    $_SESSION["username"] = $username;
    $_SESSION["id"] = $data["id"];
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
        <div class="login">
            <h2>ユーザ名とパスワードを入力してください</h2>
            <form action="login_form.php" method="get">
                <p>ユーザ名</p>
                <input type="text" name="username">
                <p>パスワード</p>
                <input type="password" name="password">
                <input type="submit" value="送信">
            </form>
            <? echo '<p>'.$result.'</p>';?>
            <p><a href="top.php">トップページに戻る</a></p>
        </div>
    </body>

</html>