<?php
header('Content-Type: application/json; charset=utf-8');

session_start();
$mode = $_SESSION["mode"];
$name = $_SESSION["username"];
$id = $_SESSION["id"];
$partnerID = $_SESSION["partnerID"];
$partnerName = $_SESSION["partnerName"];
$maxQNum = 5; //問題数
if ($mode == 0) {
    $lineID = $partnerID;
    $circleID = $id;
} else {
    $lineID = $id;
    $circleID = $partnerID;
}

ini_set('display_errors', "On");
error_reporting(E_ALL);
$db = new PDO("sqlite:data.sqlite");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
$fet = $db->query("select * from users where id=" . $id . ";");
$fet = $fet->fetchAll();
$turnFlag = $fet[0]['turnFlag'];
if (!$_SESSION['addFigureStack']) {
    $_SESSION['addFigureStack'] = [];
}
$addFigureStack = $_SESSION['addFigureStack'];

if ($_SESSION["QStack"] == []) {
    $_SESSION["QStack"] = [0, 0, 0, 0, 0];
    //$array_problem = range(0, 10);
    //shuffle($array_problem);
    //$_SESSION["QStack"] = array_slice($array_problem, 0, $maxQNum);
}
if (count($_SESSION["QStack"]) - 1 == $maxQNum - $_SESSION['currentQNum']) {
    $_SESSION['QNum'] = array_pop($_SESSION["QStack"]);  //該当の問題番号を設定
}
$QNum = $_SESSION['QNum'];
$drawLines = [];
$drawCircles = [];
$ansLines = [];
$ansCircles = [];
$addLines = [];
$addCircles = [];
$fet = $db->query("select * from drawLines where QNumber=" . $QNum);
$fet = $fet->fetchAll();
foreach ($fet as $a) {
    array_push($drawLines, [$a['x1'], $a['y1'], $a['x2'], $a['y2']]);
}
$fet = $db->query("select * from drawCircles where QNumber=" . $QNum);
$fet = $fet->fetchAll();
foreach ($fet as $a) {
    array_push($drawCircles, [$a['x1'], $a['y1'], $a['r']]);
}
$fet = $db->query("select * from addLines where drawerID=" . $lineID . ";");
$fet = $fet->fetchAll();
foreach ($fet as $a) {
    array_push($addLines, [$a['x1'], $a['y1'], $a['x2'], $a['y2']]);
}
$fet = $db->query("select * from addCircles where drawerID=" . $circleID . ";");
$fet = $fet->fetchAll();
foreach ($fet as $a) {
    array_push($addCircles, [$a['x1'], $a['y1'], $a['r']]);
}
$fet = $db->query("select * from ansLines where QNumber=" . $QNum);
$fet = $fet->fetchAll();
foreach ($fet as $a) {
    array_push($ansLines, [$a['startX'], $a['startY'], $a['angle'], $a['length']]);
}
$fet = $db->query("select * from ansCircles where QNumber=" . $QNum);
$fet = $fet->fetchAll();
foreach ($fet as $a) {
    array_push($ansCircles, [$a['x1'], $a['y1'], $a['r']]);
}
$nextURL = "drawing.php?currentQNum=" . ($_SESSION['currentQNum'] + 1);
if (count($_SESSION["QStack"]) == 0) {
    $nextURL = "result.php";
}
$array = [
    'mode' => $mode,
    'id' => $id,
    'name' => $name,
    'partnerID' => $partnerID,
    'partnerName' => $partnerName,
    'turnFlag' => $turnFlag,
    'drawLines' => $drawLines,
    'drawCircles' => $drawCircles,
    'addLines' => $addLines,
    'addCircles' => $addCircles,
    'addFigureStack' => $addFigureStack,
    'ansLines' => $ansLines,
    'ansCircles' => $ansCircles,
    'nextURL' => $nextURL,
    'currentQNum' => $_SESSION['currentQNum']
];

$json = json_encode($array);
echo $json;