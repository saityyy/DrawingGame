<?php
session_start();
if (isset($_GET["mode"])) {
    $_SESSION["mode"] = $_GET["mode"];
    header('Location: ../matching.html');
    exit;
}
if (isset($_GET["partnerID"]) && isset($_GET["partnerName"])) {
    $_SESSION["partnerID"] =  $_GET["partnerID"];
    $_SESSION["partnerName"] = $_GET["partnerName"];
    exit;
}
header('Content-Type: application/json; charset=utf-8');
$mode = $_SESSION["mode"];
$name = $_SESSION["username"];
$id = $_SESSION["id"];
$pdo = new PDO("sqlite:data.sqlite");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
$st = $pdo->query("update users set mode=" . $mode . " where id=" . $id . ";");
$st = $pdo->query("update users set turnFlag=" . $mode . " where id=" . $id . ";");
$st = $pdo->query("update users set partnerID=0 where id=" . $id . ";");

$partnerID = -100;
$partnerName = -100;
if ($mode == 0) {
    $st = $pdo->query("select * from users where mode=1 and partnerID=0;");
    $data = $st->fetchAll();
    if (count($data) > 0) {
        $rd = rand(0, count($data) - 1);
        $partnerID = $data[$rd]["id"]; //マッチング相手のID
        $partnerName = $data[$rd]["name"];
        $st = $pdo->query("update users set partnerID=" . $partnerID .
            " where id=" . $id .
            ";");
        $st = $pdo->query("update users set partnerID=" . $id .
            " where id=" . $partnerID .
            ";");
        $_SESSION["partnerID"] = $partnerID;
        $_SESSION["partnerName"] = $partnerName;
    }
}
$array = ['mode' => $mode, 'id' => $id, 'name' => $name, 'partnerID' => $partnerID, 'partnerName' => $partnerName, 'turnFlag' => $mode];
$json = json_encode($array);
echo $json;