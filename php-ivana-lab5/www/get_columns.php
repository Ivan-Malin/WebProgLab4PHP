<?php
include("db.php");

if (isset($_GET['table'])) {
    $selectedTable = $_GET['table'];
    $columns = $conn->query("SHOW COLUMNS FROM apteka_db.$selectedTable");

    if ($columns->num_rows > 0) {
        //echo "<h3>Информация для каждой колонки:</h3>";
        while ($column = $columns->fetch_assoc()) {
            echo "<label for='{$column['Field']}'>{$column['Field']}:</label>";
            echo "<input type='text' name='{$column['Field']}' id='{$column['Field']}'>";
            echo "<br>";
        }
    } else {
        echo "Выбранная таблица не содержит колонок.";
    }
}

$conn->close();
?>
