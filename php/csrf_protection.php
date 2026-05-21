<?php

function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        return false;
    }
    return true;
}

// Для AJAX запросов - проверяем заголовок
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $headers = getallheaders();
    $csrf_token = $_POST['csrf_token'] ?? $headers['X-CSRF-Token'] ?? '';
    
    if (!verify_csrf_token($csrf_token)) {
        http_response_code(403);
        echo json_encode(['error' => 'CSRF token validation failed']);
        exit;
    }
}
?>