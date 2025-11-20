<?php
session_start();

$email     = trim($_POST['email'] ?? '');
$password  = trim($_POST['password'] ?? '');
$password2 = trim($_POST['password2'] ?? '');

if ($email === '' || $password === '' || $password2 === '') {
    die("Заполните все поля.");
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Некорректный email.");
}
if (strlen($password) < 6) {
    die("Пароль должен быть не менее 6 символов.");
}
if ($password !== $password2) {
    die("Пароли не совпадают.");
}

$users_file    = "users.json";
$balances_file = "balances.json";

$users = [];
if (file_exists($users_file)) {
    $decoded = json_decode(file_get_contents($users_file), true);
    if (is_array($decoded)) $users = $decoded;
}

// Проверяем email
foreach ($users as $u) {
    if (isset($u['email']) && strtolower($u['email']) === strtolower($email)) {
        die("Этот email уже зарегистрирован.");
    }
}

// Новый ID
$new_id = 1;
if (!empty($users)) {
    $ids = array_column($users, 'id');
    $new_id = max($ids) + 1;
}

$hash = password_hash($password, PASSWORD_DEFAULT);

$users[] = [
    "id"         => $new_id,
    "email"      => $email,
    "password"   => $hash,
    "created_at" => date("Y-m-d H:i:s")
];

file_put_contents($users_file, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// Баланс по умолчанию 0
$balances = [];
if (file_exists($balances_file)) {
    $decoded = json_decode(file_get_contents($balances_file), true);
    if (is_array($decoded)) $balances = $decoded;
}
if (!isset($balances[$new_id])) {
    $balances[$new_id] = 0;
}
file_put_contents($balances_file, json_encode($balances, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

header("Location: login.html");
exit;
?>
