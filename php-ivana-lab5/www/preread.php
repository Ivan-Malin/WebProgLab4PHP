<?php
include("db.php");

try {
    $conn->query("USE apteka_db");
    if (isset($_GET['table']) && isset($_GET['condition'])) {
        $selectedTable = $_GET['table'];
        $condition = $_GET['condition'];
        
        // Modify the query as per your specific requirements
        // $condition = $_POST["condition"];
        $sql = "SELECT * FROM $selectedTable WHERE $condition";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Output the row data in JSON format
        echo json_encode($result);
    }
} catch (PDOException $e) {
    echo "Ошибка: " . $e->getMessage();
    echo $selectedTable . " " . $condition;
} finally {
    $conn = null;
}
?>
