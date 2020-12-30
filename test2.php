<?php
header('Content-Type: application/json; charset=utf-8');
$data = json_decode($_POST["json"]);
$array = ['test' => 1, 'test2' => 2, 'test3' => $data];
$json = json_encode($array);
echo $json;