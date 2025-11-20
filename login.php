<?php
session_start();

$email    = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($email === '' || $password === '') {
    die("Введите email и пароль.");
}

$users_file = "users.json";
if (!file_exists($users_file)) {
    die("Пользователь не найден.");
}

$users = json_decode(file_get_contents($users_file), true);
if (!is_array($users)) {
    die("Ошибка данных пользователей.");
}

$found = null;
foreach ($users as $u) {
    if (isset($u['email']) && strtolower($u['email']) === strtolower($email)) {
        $found = $u;
        break;
    }
}

if (!$found) {
    die("Пользователь не найден.");
}

if (!password_verify($password, $found["password"])) {
    die("Неверный пароль.");
}

$_SESSION['user_id'] = $found["id"];
$_SESSION['email']   = $found["email"];

header("Location: dashboard.php");
exit;
?>
