<?php
require_once 'security_headers.php'; 
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Не авторизован']);
    exit;
}

if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'error' => 'Ошибка загрузки файла']);
    exit;
}

$file = $_FILES['avatar'];
$allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
$max_size = 2 * 1024 * 1024; // 2MB

if (!in_array($file['type'], $allowed_types)) {
    echo json_encode(['success' => false, 'error' => 'Разрешены только JPG, PNG, GIF']);
    exit;
}

if ($file['size'] > $max_size) {
    echo json_encode(['success' => false, 'error' => 'Файл слишком большой (макс. 2MB)']);
    exit;
}

// Создаём папку для аватаров если её нет
$upload_dir = '../uploads/avatars/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$user_id = $_SESSION['user_id'];
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = $user_id . '_' . time() . '.' . $extension;
$filepath = $upload_dir . $filename;

if (move_uploaded_file($file['tmp_name'], $filepath)) {
    // Сохраняем путь в БД
    $avatar_path = 'uploads/avatars/' . $filename;
    $stmt = $conn->prepare("UPDATE users SET avatar = ? WHERE id = ?");
    $stmt->bind_param("si", $avatar_path, $user_id);
    $stmt->execute();
    $stmt->close();
    
    $_SESSION['avatar'] = $avatar_path;
    echo json_encode(['success' => true, 'avatar_path' => $avatar_path]);
} else {
    echo json_encode(['success' => false, 'error' => 'Ошибка сохранения файла']);
}

$conn->close();
?>