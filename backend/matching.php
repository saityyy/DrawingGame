<?php
session_start();
$maxQNum = 3; //問題数
$_SESSION['QStack'] = [];
if (isset($_GET["mode"])) {
    $_SESSION["mode"] = $_GET["mode"];
    header('Location: ../matching.html');
    exit;
}
if (isset($_POST['json'])) {
    $data = json_decode($_POST['json'], true);
    $_SESSION["partnerID"] = $data['id'];
    $_SESSION["partnerName"] = $data['name'];
    $_SESSION['QStack'] = $data['QStack'];
    echo $data;
    exit();
}
header('Content-Type: application/json; charset=utf-8');
$mode = $_SESSION["mode"];
$name = $_SESSION["username"];
$id = $_SESSION["id"];
$_SESSION['turnFlag'] = $mode;
$pdo = new PDO("sqlite:data.sqlite");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
$st = $pdo->query("update users set mode=" . $mode . " where id=" . $id . ";");
$st = $pdo->query("update users set turnFlag=" . $mode . " where id=" . $id . ";");
$st = $pdo->query("update users set partnerID=0 where id=" . $id . ";");

$partnerID = -100;
$partnerName = -100;
if ($mode == 0) {
    if ($_SESSION["QStack"] == []) {
        $array_problem = [1, 2, 3];
        shuffle($array_problem);
        $_SESSION["QStack"] = array_slice($array_problem, 0, $maxQNum);
    }
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
$array = [
    'mode' => $mode,
    'id' => $id,
    'name' => $name,
    'partnerID' => $partnerID,
    'partnerName' => $partnerName,
    'turnFlag' => $mode,
    'QStack' => $_SESSION['QStack']
];
$json = json_encode($array);
echo $json;