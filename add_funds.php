<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

$user_id = $_SESSION['user_id'];
$amount  = floatval($_POST["amount"] ?? 0);

if ($amount <= 0) {
    die("Сумма должна быть больше нуля.");
}

$balances_file     = "balances.json";
$transactions_file = "transactions.json";

$balances = [];
if (file_exists($balances_file)) {
    $decoded = json_decode(file_get_contents($balances_file), true);
    if (is_array($decoded)) $balances = $decoded;
}

$transactions = [];
if (file_exists($transactions_file)) {
    $decoded = json_decode(file_get_contents($transactions_file), true);
    if (is_array($decoded)) $transactions = $decoded;
}

if (!isset($balances[$user_id])) {
    $balances[$user_id] = 0;
}

$balances[$user_id] += $amount;

$transactions[] = [
    "user_id" => $user_id,
    "type"    => "deposit_balance",
    "amount"  => $amount,
    "wallet"  => "",
    "status"  => "completed",
    "date"    => date("Y-m-d H:i:s")
];

file_put_contents($balances_file, json_encode($balances, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
file_put_contents($transactions_file, json_encode($transactions, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

header("Location: dashboard.php");
exit;
?>
