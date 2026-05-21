<?php
require_once 'security_headers.php'; 
require_once 'csrf_protection.php';
session_start();
require_once 'config.php';


header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Метод не поддерживается']);
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'error' => 'Введите корректный email']);
    exit;
}

if (empty($password)) {
    echo json_encode(['success' => false, 'error' => 'Введите пароль']);
    exit;
}

// Ищем пользователя по email
$stmt = $conn->prepare("SELECT id, username, email, password, avatar, playtime, level FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'error' => 'Неверный email или пароль']);
    $stmt->close();
    $conn->close();
    exit;
}

$user = $result->fetch_assoc();

// Проверяем пароль
if (!password_verify($password, $user['password'])) {
    echo json_encode(['success' => false, 'error' => 'Неверный email или пароль']);
    $stmt->close();
    $conn->close();
    exit;
}

// Запускаем сессию
session_start();
$_SESSION['user_id'] = $user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['email'] = $user['email'];
$_SESSION['avatar'] = $user['avatar'];
$_SESSION['playtime'] = $user['playtime'] ?? 0;
$_SESSION['level'] = $user['level'] ?? 1;

echo json_encode([
    'success' => true,
    'user' => [
        'id' => $user['id'],
        'username' => $user['username'],
        'email' => $user['email'],
        'avatar' => $user['avatar'],
        'playtime' => $user['playtime'] ?? 0,
        'level' => $user['level'] ?? 1
    ]
]);

$stmt->close();
$conn->close();
?>