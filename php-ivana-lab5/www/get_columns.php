<?php
include("db.php");

try {
    if (isset($_GET['table'])) {
        $selectedTable = $_GET['table'];
        $stmt = $conn->prepare("SHOW COLUMNS FROM apteka_db.$selectedTable");
        $stmt->execute();
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // if ($stmt->rowCount() > 0) {
            //echo "<h3>Информация для каждой колонки:</h3>";
            foreach ($columns as $column) {
                echo "<label for='{$column['Field']}'>{$column['Field']}:</label>";
                echo "<input type='text' name='{$column['Field']}' id='{$column['Field']}'>";
                echo "<br>";
            }
        // } else {
        //     echo "Выбранная таблица не содержит колонок.";
        // }
    }
} catch (PDOException $e) {
    echo "Ошибка: " . $e->getMessage();
} finally {
    $conn = null;
}
?>