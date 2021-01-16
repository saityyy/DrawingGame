<?php
session_start();
if (isset($_POST['afs'])) {
    $data = json_decode($_POST['afs'], true);
    $_SESSION['addFigureStack'] = $data['addFigureStack'];
    echo json_encode($_SESSION['addFigureStack']);
    exit();
}
$mode = $_SESSION['mode'];
$id = $_SESSION['id'];
$partnerID = $_SESSION['partnerID'];
if ($mode == 0) {
    $lineID = $partnerID;
    $circleID = $id;
} else {
    $lineID = $id;
    $circleID = $partnerID;
}
$data = json_decode($_POST['json'], true);
$addLines = $data['addLines'];
$addCircles = $data['addCircles'];
$_SESSION['addFigureStack'] = $data['addFigureStack'];
$db = new PDO("sqlite:data.sqlite");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
$db->query("update users set turnFlag=0 where id=" . $id . ";");
$db->query("update users set turnFlag=1 where id=" . $partnerID . ";");
$db->query("delete from addLines;");
$db->query("delete from addCircles;");
foreach ($addLines as $a) {
    $db->query("insert into addLines(drawerID,x1,y1,x2,y2) values(" . $lineID . "," . $a[0] . "," . $a[1] . "," . $a[2] . "," . $a[3] . ");");
}
foreach ($addCircles as $a) {
    $db->query("insert into addCircles(drawerID,x1,y1,r) values(" . $circleID . "," . $a[0] . "," . $a[1] . "," . $a[2] . ");");
}