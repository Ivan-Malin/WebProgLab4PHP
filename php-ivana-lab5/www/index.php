<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Аптека</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="container">
        <h1>Аптека: Учет лекарств</h1>

        <!-- Ссылки на создание и удаление базы данных -->
        <p><a href="create_db.php">Создать базу данных</a></p>
        <p><a href="delete_db.php">Удалить базу данных</a></p>

        <?php
        include("db.php");

        try {
            // Fetch table names from the database
            $showTablesSQL = "SHOW TABLES FROM apteka_db";
            $result = $conn->query($showTablesSQL);

            // if ($result->rowCount() > 0) {
            echo "<form action='process.php' method='post'>";
            echo "<h3>Выберите таблицу:</h3>";
            echo "<select name='selected_table' id='tableSelect' onchange='fetchColumns()'>";
            while ($row = $result->fetch()) {
                echo "<option value='{$row[0]}'>{$row[0]}</option>";
            }
            echo "</select>";
            echo "<br>";
            // echo "<label for='action'>Выберите действие:</label>";
            echo "<h3>Выберите действие:</h3>";
            echo "<select name='action' id='actionSelect' onchange='fetchColumns(); handleAction()'>";
            echo "<option value='add'>Добавить</option>";
            echo "<option value='edit'>Редактировать</option>";
            echo "<option value='sort'>Сортировать</option>";
            echo "<option value='read'>Читать</option>";
            echo "<option value='delete'>Удалить</option>";
            echo "</select>";

            // Dynamically generate input fields for each column
            echo "<div id='input-fields'>";
            echo "<h3>Параметры:</h3>";
            echo "<div id='columnFields'></div>"; // Container for column input fields

            // Additional input fields for conditions
            echo "<div id='conditionFields'></div>";

            echo "</div>";

            echo "<button type='submit' name='submit_action'>Выполнить</button>";
            echo "</form>";
        } catch (PDOException $e) {
            echo "Ошибка: " . $e->getMessage();
        } finally {
            $conn = null;
        }
        ?>

        <script>
            function fetchColumns() {
                var selectedTable = document.getElementById('tableSelect').value;
                var selectedAction = document.getElementById('actionSelect').value;
                var xhr = new XMLHttpRequest();

                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        document.getElementById('columnFields').innerHTML = xhr.responseText;
                        if (selectedAction === 'sort' || selectedAction === 'read' || selectedAction === 'delete') {
                            document.getElementById('columnFields').innerHTML = "";
                        }
                    }
                };

                xhr.open('GET', 'get_columns.php?table=' + selectedTable, true);
                xhr.send();
            }

            function handleAction() {
                var selectedAction = document.getElementById('actionSelect').value;
                var conditionFields = document.getElementById('conditionFields');

                // Clear previous condition fields
                conditionFields.innerHTML = "";

                if (selectedAction === 'edit' || selectedAction === 'sort' || selectedAction === 'read' || selectedAction === 'delete') {
                    // Additional input fields for conditions
                    var conditionLabel = document.createElement('label');
                    conditionLabel.setAttribute('for', 'condition');
                    conditionLabel.textContent = 'Условие:';
                    conditionFields.appendChild(conditionLabel);

                    var conditionInput = document.createElement('input');
                    conditionInput.setAttribute('type', 'text');
                    conditionInput.setAttribute('name', 'condition');
                    conditionInput.setAttribute('id', 'condition');
                    conditionFields.appendChild(conditionInput);

                    var breakElement = document.createElement('br');
                    conditionFields.appendChild(breakElement);
                }
            }
        </script>

    </div>
</body>

</html>
