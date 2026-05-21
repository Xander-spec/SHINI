<?php
// ЗДЕСЬ ВАШИ ДАННЫЕ: настройте подключение к вашей базе данных MySQL
$servername = "localhost";     // обычно localhost
$username = "root";            // ваше имя пользователя БД
$password = "";                // ваш пароль БД
$dbname = "shinigames_db";     // имя вашей базы данных

// Создаём подключение
$conn = new mysqli($servername, $username, $password, $dbname);

// Проверяем подключение
if ($conn->connect_error) {
    die("Ошибка подключения к базе данных: " . $conn->connect_error);
}

// Устанавливаем кодировку UTF-8
$conn->set_charset("utf8");
?>