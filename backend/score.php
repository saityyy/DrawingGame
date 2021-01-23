<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
$data = json_decode($_POST['score'], true);
if (!isset($data['judge_result']) && $_SESSION['startT'] == 0) {
    $_SESSION['startT'] = $data['startT'];
} else if (isset($data['judge_result'])) {
    $Time = $data['Time'];
    $diff = $data['diff'];
    if ($data['judge_result'] == 1) {
        $_SESSION['score'] += 1000 * $diff * (1 + 100 / (pow(1.1, $Time + 15)));
        $_SESSION['startT'] = 0;
    } else {
        $_SESSION['score'] -= max($_SESSION['score'] - 500 * $diff, 0);
    }
}