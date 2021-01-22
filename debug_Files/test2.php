<?php
header('Content-Type: application/json; charset=utf-8');
$data = json_decode($_POST["json"], true);
$data = $data['a'];
echo json_encode($data);
#$json = json_encode($array);
#echo $json;