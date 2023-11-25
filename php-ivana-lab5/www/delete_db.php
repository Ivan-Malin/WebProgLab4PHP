<?php
include("db.php");

$sql = "DROP DATABASE IF EXISTS apteka_db";

if ($conn->query($sql) === TRUE) {
    echo "База данных успешно удалена.";
} else {
    echo "Ошибка при удалении базы данных: " . $conn->error;
}

$conn->close();
?>
