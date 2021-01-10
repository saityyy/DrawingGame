<?php
session_start();
if ($_GET["username"] && $_GET["password"]) {
    $username = $_GET["username"];
    $password = $_GET["password"];

    $pdo = new PDO("sqlite:data.sqlite");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    $st = $pdo->prepare("select * from users where name==?;");
    $st->execute(array($username));
    $data = $st->fetch();

    if (!$data) {
        echo "<p>指定されたユーザーが存在しません。</p>";
    } else if ($data["password"] == $password) {
        echo "<p>" . $username . "さん。</p>";
        $_SESSION["username"] = $username;
        $_SESSION["id"] = $data["id"];
        echo "<p>ログインに成功しました。</p>";
    } else {
        echo "<p>パスワードが違います。</p>";
    }
} else echo "<p>入力不備があります。</p>";