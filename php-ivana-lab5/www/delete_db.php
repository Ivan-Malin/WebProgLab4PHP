<?php
include("db.php");

try {
    $sql = "DROP DATABASE IF EXISTS apteka_db";
    $conn->query($sql);
    // if ($conn->query($sql) === TRUE) {
    //     echo "База данных успешно удалена.";
    // } else {
    //     echo "Ошибка при удалении базы данных: ";
    //     //  . $conn->getMessage();
    // }
    echo "База данных успешно удалена.";
} catch (PDOException $e) {
    echo "Ошибка: " . $e->getMessage();
} finally {
    $conn = null;
}
?>
