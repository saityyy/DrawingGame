<?php
header('Content-Type: application/json; charset=utf-8');
$data = json_decode($_POST["request"], true);
$array = [
    'mode' => 1,
    'id' => 2,
    'partnerID' => 3,
    'turn_flag' => 1,
    'drawLines' => [[100, 200, 300, 400]],
    'drawCircles' => [[400, 300, 200]],
    'ansLines' => [[100, 200, 300, 400]],
    'ansCircles' => [[200, 200, 300]],
    'nextURL' => 'result.php'
];
$json = json_encode($array);
echo $json;