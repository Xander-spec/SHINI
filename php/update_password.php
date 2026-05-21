<?php
require_once 'security_headers.php'; 
require_once 'csrf_protection.php';
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Не авторизован']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$current_password = $data['current_password'] ?? '';
$new_password = $data['new_password'] ?? '';
$confirm_password = $data['confirm_password'] ?? '';

if (strlen($new_password) < 6) {
    echo json_encode(['success' => false, 'error' => 'Новый пароль должен быть минимум 6 символов']);
    exit;
}

if ($new_password !== $confirm_password) {
    echo json_encode(['success' => false, 'error' => 'Пароли не совпадают']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Проверяем текущий пароль
$stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!password_verify($current_password, $user['password'])) {
    echo json_encode(['success' => false, 'error' => 'Текущий пароль неверен']);
    exit;
}

// Обновляем пароль
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
$stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
$stmt->bind_param("si", $hashed_password, $user_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Ошибка при обновлении пароля']);
}

$stmt->close();
$conn->close();
?>