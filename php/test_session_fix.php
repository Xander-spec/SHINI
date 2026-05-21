<?php
session_start();
echo "ID сессии: " . session_id() . "<br>";
$_SESSION['test'] = 'Работает';
echo "Сессия создана! <a href='check_session_fix.php'>Проверить</a>";
?>