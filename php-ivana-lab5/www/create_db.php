<?php
include("db.php");

try {
    $sql = "CREATE DATABASE IF NOT EXISTS apteka_db";
    $conn->query($sql);
    echo "База данных успешно создана.<br>";

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

    $conn->query($medicinesTable);
    $conn->query($clientsTable);
    $conn->query($salesTable);

    echo "Таблицы успешно созданы.";
} catch (PDOException $e) {
    echo "Ошибка: " . $e->getMessage();
} finally {
    $conn = null;
}
?>
