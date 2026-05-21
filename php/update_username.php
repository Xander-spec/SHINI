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
$new_username = trim($data['username'] ?? '');

if (strlen($new_username) < 3) {
    echo json_encode(['success' => false, 'error' => 'Имя должно содержать минимум 3 символа']);
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("UPDATE users SET username = ? WHERE id = ?");
$stmt->bind_param("si", $new_username, $user_id);

if ($stmt->execute()) {
    $_SESSION['username'] = $new_username;
    echo json_encode(['success' => true, 'username' => $new_username]);
} else {
    echo json_encode(['success' => false, 'error' => 'Ошибка при обновлении']);
}

$stmt->close();
$conn->close();
?>