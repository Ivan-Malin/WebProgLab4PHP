<?php
include("db.php");

$conn->query("USE apteka_db");
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selectedTable = $_POST["selected_table"];
    $selectedAction = $_POST["action"];

    switch ($selectedAction) {
        case 'add':
            // Handle adding data to the selected table
            unset($_POST["selected_table"]);
            unset($_POST["action"]);
            unset($_POST["condition"]);
            unset($_POST["submit_action"]);
            $columns = implode(", ", array_keys($_POST));
            $values = "'" . implode("', '", array_values($_POST)) . "'";
            $sql = "INSERT INTO $selectedTable ($columns) VALUES ($values)";
            break;

        case 'edit':
            // Handle editing data in the selected table
            $condition = $_POST["condition"];
            unset($_POST["selected_table"]);
            unset($_POST["action"]);
            unset($_POST["condition"]);
            unset($_POST["submit_action"]);
            $setClause = implode(", ", array_map(function ($key, $value) {
                return "$key = '$value'";
            }, array_keys($_POST), array_values($_POST)));
            $sql = "UPDATE $selectedTable SET $setClause WHERE $condition";
            break;

        case 'sort':
            // Handle sorting data in the selected table
            $condition = $_POST["condition"];
            $sql = "SELECT * FROM $selectedTable ORDER BY $condition";
            $result = $conn->query($sql);
            // Process and display the sorted data (for demonstration purposes)
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Process each row as needed
                    echo "<pre>";
                    print_r($row);
                    echo "</pre>";
                }
            } else {
                echo "No data found.";
            }
            break;

        case 'read':
            // Handle reading data from the selected table
            $condition = $_POST["condition"];
            $sql = "SELECT * FROM $selectedTable WHERE $condition";
            echo "$sql";
            $result = $conn->query($sql);
            // Process and display the data (for demonstration purposes)
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Process each row as needed
                    echo "<pre>";
                    print_r($row);
                    echo "</pre>";
                }
            } else {
                echo "No data found.";
            }
            break;

        case 'delete':
            // Handle deleting data from the selected table
            $condition = $_POST["condition"];
            $sql = "DELETE FROM $selectedTable WHERE $condition";
            break;

        default:
            echo "Invalid action.";
            break;
    }

    if ($selectedAction !== 'sort' && $selectedAction !== 'read') {
        // Execute the SQL query for add, edit, or delete actions
        if ($conn->query($sql) === TRUE) {
            echo "Operation delivered succesfully.";
        } else {
            echo "Error: " . $conn->error;
        }
    }
}

$conn->close();
?>
