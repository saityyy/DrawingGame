<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
$data = json_decode($_POST['score'], true);
if (!isset($data['judge_result']) && $_SESSION['startT'] == 0) {
    $_SESSION['startT'] = $data['startT'];
} else if (isset($data['judge_result'])) {
    $startT = $data['startT'];
    $endT = $data['endT'];
    $diff = $data['diff'];
    if ($data['judge_result'] == 1) {
        $_SESSION['score'] += 1000 * $diff * pow(($endT - $startT), -0.1);
        $_SESSION['startT'] = 0;
    } else {
        $_SESSION['score'] -= 100 * $diff * pow($end - $start, -0.1);
    }
}