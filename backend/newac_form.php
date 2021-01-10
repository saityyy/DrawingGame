<?php
session_start();
$result;
if ($_GET["username"] && $_GET["password"]) {
  $username = $_GET["username"];
  $password = $_GET["password"];
  $pdo = new PDO("sqlite:data.sqlite");
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
  $st = $pdo->prepare("select * from users where name==?;");
  $st->execute(array($username));
  $data = $st->fetch();

  if (!$data) {
    $sql_query = "insert into users(name,password,mode,partnerID,turnFlag) values('" . $username . "','" . $password . "',0,-1,0);";
    $st = $pdo->query($sql_query);
    $st = $pdo->query("select max(id) from users;");
    $data = $st->fetch();
    $_SESSION["username"] = $username;
    $_SESSION["id"] = $data[0];
    echo "新規アカウント作成に成功しました";
  } else {
    echo "指定した名前はすでに存在しています";
  }
} else {
  echo "入力不備があります";
}