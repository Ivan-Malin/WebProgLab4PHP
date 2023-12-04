<?php
$host = "host.docker.internal"; // адрес сервера базы данных
$username = "root"; // имя пользователя
$password = "test"; // пароль (если есть)
$database = "apteka_db"; // название базы данных

// Подключение к базе данных
$conn = new PDO("mysql:host=$host;dbname=$database", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e){
    // echo $e->getName();
    die("Connection failed: " . $e->getMessage());
}
?>