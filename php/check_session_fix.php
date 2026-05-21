<?php
require_once 'security_headers.php'; 
session_start();
echo "ID сессии: " . session_id() . "<br>";
echo "Значение: " . ($_SESSION['test'] ?? 'Не найдено');
?>