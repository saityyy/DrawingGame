<?php
session_start();
$maxQNum = $_SESSION['maxQNum']; //問題数
//この時点で今後使うセッション変数を定義
$_SESSION['QStack'] = [];
$_SESSION['score'] = 0;
$_SESSION['startT'] = 0;
//////////////////////////
if (isset($_POST['json'])) {
    //mode0ユーザーからのマッチング通知を受け取ったときに反応する
    //sessionに必要な情報を格納しexit
    $data = json_decode($_POST['json'], true);
    $_SESSION["partnerID"] = $data['id'];
    $_SESSION["partnerName"] = $data['name'];
    $_SESSION['QStack'] = $data['QStack'];
    exit();
}
///////////////////////////
header('Content-Type: application/json; charset=utf-8');
$mode = $_SESSION["mode"];
$name = $_SESSION["username"];
$id = $_SESSION["id"];
$_SESSION['turnFlag'] = $mode;
$pdo = new PDO("sqlite:data.sqlite");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
$st = $pdo->query("update users set mode=" . $mode . " where id=" . $id . ";"); //mode値設定
$st = $pdo->query("update users set turnFlag=" . $mode . " where id=" . $id . ";"); //turnFlag値設定（mode=1が先攻）
$st = $pdo->query("update users set partnerID=0 where id=" . $id . ";"); //マッチングしようとしていることを示す
$st = $pdo->query("select count(*) from questions;");
$st = $st->fetchAll();
$QuestionsCount = (int)$st[0][0];

$partnerID = -100;
$partnerName = -100;
//マッチング失敗したときはこのままの値でフロントに返す
if ($mode == 0) {
    if ($_SESSION["QStack"] == []) {
        $array_problem = range(1, $QuestionsCount);
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