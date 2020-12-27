<?php
session_start();
if ($_GET["username"] && $_GET["password"]) {
  $username = $_GET["username"];
  $password = $_GET["password"];
  $pdo = new PDO("sqlite:data.sqlite");
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
  $st = $pdo->prepare("select * from user where name==?;");
  $st->execute(array($username));
  $data = $st->fetch();

  if (!$data) {
    $sql_query = "insert into user(name,password,mode,partnerID) values('" . $username . "','" . $password . "',0,-1);";
    $st = $pdo->query($sql_query);
    $st = $pdo->query("select max(id) from user;");
    $data = $st->fetch();
    $_SESSION["username"] = $username;
    $_SESSION["id"] = $data[0];
    header("Location:top.php");
    exit;
  } else {
    $result = "同じ名前の人がいます";
  }
} else {
  $result = "ユーザー名とパスワードを入力してください";
}
?>
<!DOCTYPE html>
<html lang="ja">

    <head>
        <meta charset="utf-8">
        <title>新規登録</title>
    </head>

    <body>
        <div class=article>
            <h2>ユーザ名とパスワードを入力してください</h2>
            <form action="newac_form.php" method="get">
                <p>ユーザ名</p>
                <input type="text" name="username">
                <p>パスワード</p>
                <input type="password" name="password">
                <input type="submit" value="送信">
            </form>
            <p>
                <?php echo $result; ?>
            </p>
        </div>
    </body>

</html>