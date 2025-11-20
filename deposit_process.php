<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

$user_id = $_SESSION['user_id'];
$type    = $_POST['type']   ?? '';
$amount  = floatval($_POST['amount'] ?? 0);

if ($amount <= 0) {
    die("Неверная сумма.");
}

$file = 'deposits.json';
if (!file_exists($file)) {
    file_put_contents($file, json_encode([]));
}

$data = json_decode(file_get_contents($file), true);
if (!is_array($data)) $data = [];

if ($type === "monthly") {
    $percent = 8;
    $days    = 30;
} elseif ($type === "yearly") {
    $percent = 12;
    $days    = 365;
} else {
    die("Ошибка типа депозита.");
}

$created  = date("Y-m-d");
$end_date = date("Y-m-d", strtotime("+$days days"));

$data[] = [
    "user_id"    => $user_id,
    "type"       => $type,
    "amount"     => $amount,
    "percent"    => $percent,
    "days"       => $days,
    "created_at" => $created,
    "end_at"     => $end_date
];

file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

header("Location: dashboard.php");
exit;
?>
