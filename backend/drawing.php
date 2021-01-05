<?php
if (isset($_GET["request"])) {
    $request = $_GET["request"];
    $reqText=$request['request'];
    $reqData=$request['data'];
} else 
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

if ($reqText === "iniSet") {
    /*[
       'mode' => $mode, 
       'id' => $id, 
       'name' => $name, 
       'partnerID' => $partnerID, 
       'partnerName' => $partnerName
    ];*/
} else if ($reqText === "iniSetFigures") {
    /*[
        'drawLines'=>$drawLines,
        'drawCircles=>$drawCircles
    ];*/
} else if ($reqText === "changeTurn") {
    /*
    void 
    */
} else if ($reqText === "judge") {
    /*[
        'AnsLines'=>$AnsLines,
        'AnsCircles'=>$AnsCircles,
    ];*/
} else if ($reqText === "nextQuestion") {
    /*[
        'nextURL=>$URL
    ]*/
}