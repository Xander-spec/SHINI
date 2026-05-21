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

$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Валидация
if (strlen($username) < 3) {
    echo json_encode(['success' => false, 'error' => 'Имя пользователя должно содержать минимум 3 символа']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'error' => 'Введите корректный email']);
    exit;
}

if (strlen($password) < 6) {
    echo json_encode(['success' => false, 'error' => 'Пароль должен содержать минимум 6 символов']);
    exit;
}

if ($password !== $confirm_password) {
    echo json_encode(['success' => false, 'error' => 'Пароли не совпадают']);
    exit;
}

// Проверка на существование пользователя
$checkStmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
$checkStmt->bind_param("ss", $username, $email);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows > 0) {
    echo json_encode(['success' => false, 'error' => 'Пользователь с таким именем или email уже существует']);
    $checkStmt->close();
    $conn->close();
    exit;
}
$checkStmt->close();

// Хешируем пароль
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$avatar = strtoupper(substr($username, 0, 1));

// ВСТАВКА — ЗДЕСЬ ДОЛЖЕН БЫТЬ username, НЕ email!
$stmt = $conn->prepare("INSERT INTO users (username, email, password, avatar) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $username, $email, $hashed_password, $avatar);

if ($stmt->execute()) {
    $new_id = $stmt->insert_id;
    
    $_SESSION['user_id'] = $new_id;
    $_SESSION['username'] = $username;
    $_SESSION['email'] = $email;
    $_SESSION['avatar'] = $avatar;
    
    echo json_encode([
        'success' => true, 
        'user' => [
            'id' => $new_id,
            'username' => $username,
            'email' => $email,
            'avatar' => $avatar
        ]
    ]);
} else {
    echo json_encode(['success' => false, 'error' => 'Ошибка БД: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>