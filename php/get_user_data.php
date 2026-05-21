<?php
require_once 'security_headers.php'; 
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Не авторизован']);
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT id, username, email, avatar, playtime, level, DATE_FORMAT(created_at, '%Y') as created_year FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['error' => 'Пользователь не найден']);
    exit;
}

$user = $result->fetch_assoc();

echo json_encode([
    'success' => true,
    'user' => [
        'id' => $user['id'],
        'username' => $user['username'],
        'email' => $user['email'],
        'avatar' => $user['avatar'],
        'playtime' => $user['playtime'] ?? 0,
        'level' => $user['level'] ?? 1,
        'created_at' => $user['created_year'] ?? '2024'
    ]
]);

$stmt->close();
$conn->close();
?>