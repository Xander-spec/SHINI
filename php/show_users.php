<?php
require_once 'config.php';
require_once 'security_headers.php'; 
echo "<h2>Проверка таблицы users</h2>";
echo "База данных: " . $dbname . "<br><br>";

// Проверяем, есть ли таблица
$check = $conn->query("SHOW TABLES LIKE 'users'");
if ($check->num_rows > 0) {
    echo "✅ Таблица 'users' существует<br><br>";
    
    // Показываем ВСЕХ пользователей
    $result = $conn->query("SELECT * FROM users ORDER BY id DESC");
    
    if ($result->num_rows > 0) {
        echo "<strong>Найдено пользователей: " . $result->num_rows . "</strong><br><br>";
        echo "<table border='1' cellpadding='8'>";
        echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Avatar</th><th>Дата</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . htmlspecialchars($row['username']) . "</td>";
            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
            echo "<td>" . $row['avatar'] . "</td>";
            echo "<td>" . $row['created_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "❌ В таблице 'users' НЕТ записей!<br>";
        echo "Вы регистрировались, но записи не появились.";
    }
} else {
    echo "❌ Таблица 'users' НЕ найдена в базе данных '$dbname'<br>";
    echo "Проверьте, правильно ли вы создали таблицу.";
}

$conn->close();
?>