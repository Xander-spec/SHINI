<?php

header('X-Frame-Options: DENY');
header("Content-Security-Policy: frame-ancestors 'none'");

// Дополнительные полезные заголовки безопасности
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

// Защита сессий
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_samesite', 'Strict');
?>