<?php
if (isset($_GET["request"])) {
    $request = $_GET["request"];
} else {
    header('Location: ../drawing.html');
    exit;
}
session_start();
$mode = $_SESSION["mode"];
$name = $_SESSION["username"];
$id = $_SESSION["id"];
$partnerID = $_SESSION["partnerID"];
$partnerName = $_SESSION["partnerName"];
$QNum = $_SESSION["QNum"];

if ($request === "iniSet") {
    /*[
       'mode' => $mode, 
       'id' => $id, 
       'name' => $name, 
       'partnerID' => $partnerID, 
       'partnerName' => $partnerName
    ];*/
} else if ($request === "iniSetXY") {
    /*[
        'drawLines'=>$drawLines,
        'drawCircles=>$drawCircles
    ];*/
} else if ($request === "changeTurn") {
    /*
    void 
    */
} else if ($request === "judge") {
    /*[
        'AnsLines'=>$AnsLines,
        'AnsCircles'=>$AnsCircles,
    ];*/
} else if ($request === "nextQuestion") {
    /*[
        'nextURL=>$URL
    ]*/
}