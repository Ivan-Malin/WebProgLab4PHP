<?php
include("db.php");

$sql = "CREATE DATABASE IF NOT EXISTS apteka_db";

if ($conn->query($sql) === TRUE) {
    echo "База данных успешно создана.<br>";

    // Используем созданную базу данных
    $conn->select_db($database);

    // Выбираем созданную базу данных
    $conn->query("USE apteka_db");
    echo "База данных успешно выбрана.<br>";

    // Создание таблиц
    $medicinesTable = "CREATE TABLE IF NOT EXISTS medicines (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        manufacturer VARCHAR(255) NOT NULL,
        quantity INT NOT NULL,
        price DECIMAL(10, 2) NOT NULL
    )";

    $clientsTable = "CREATE TABLE IF NOT EXISTS clients (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        phone VARCHAR(20) NOT NULL,
        address VARCHAR(255) NOT NULL
    )";

    $salesTable = "CREATE TABLE IF NOT EXISTS sales (
        id INT AUTO_INCREMENT PRIMARY KEY,
        medicine_id INT,
        client_id INT,
        quantity INT NOT NULL,
        date DATE,
        FOREIGN KEY (medicine_id) REFERENCES medicines(id),
        FOREIGN KEY (client_id) REFERENCES clients(id)
    )";

    if ($conn->query($medicinesTable) === TRUE &&
        $conn->query($clientsTable) === TRUE &&
        $conn->query($salesTable) === TRUE) {
        echo "Таблицы успешно созданы.";
    } else {
        echo "Ошибка при создании таблиц: " . $conn->error;
    }
} else {
    echo "Ошибка при создании базы данных: " . $conn->error;
}

$conn->close();
?>
