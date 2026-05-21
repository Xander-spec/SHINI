<?php
require_once 'security_headers.php'; 
session_start();
header('Content-Type: application/json');

if (isset($_SESSION['user_id'])) {
    echo json_encode([
        'isAuthenticated' => true,
        'user' => [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'email' => $_SESSION['email'],
            'avatar' => $_SESSION['avatar'],
            'playtime' => $_SESSION['playtime'] ?? 0,
            'level' => $_SESSION['level'] ?? 1
        ]
    ]);
} else {
    echo json_encode(['isAuthenticated' => false]);
}
?>