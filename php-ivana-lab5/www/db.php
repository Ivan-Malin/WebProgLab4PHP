<?php
$host = "host.docker.internal"; // адрес сервера базы данных
$username = "root"; // имя пользователя
$password = "test"; // пароль (если есть)
$database = "myDb"; // название базы данных

// Подключение к базе данных
$conn = new mysqli($host, $username, $password);

// Проверка соединения
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
