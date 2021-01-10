<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
$pdo = new PDO("sqlite:data.sqlite");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
$st = $pdo->query("select * from users");
$data = $st->fetchAll();
$login_flag = 0;
$registNum = 0;
$playNum = 0;
$waitMatchingNum = 0;
foreach ($data as $user) {
    $registNum += 1;
    if ($user["partnerID"] == 0) $waitMatchingNum += 1;
    else if ($user["partnerID"] > 0) $playNum += 1;
}
if (isset($_SESSION["username"])) $login_flag = $_SESSION["username"];
$array = ['name' => $login_flag, 'registNum' => $registNum, 'playNum' => $playNum, 'waitMatchingNum' => $waitMatchingNum];
$json = json_encode($array);
echo $json;