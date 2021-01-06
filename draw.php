<?php
header('Content-Type: application/json; charset=utf-8');
$data = json_decode($_POST["reqText"]);
$array = [
    'mode' => 1,
    'id' => 2,
    'partnerID' => 3,
    'turn_flag' => 1,
    'drawLines' => [[100, 200, 300, 400]],
    'drawCircles' => [[400, 300, 200]]
];
$json = json_encode($array);
echo $json;