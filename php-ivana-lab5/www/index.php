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
            echo "<div id='conditionFields2'></div>";
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
                var conditionFields2 = document.getElementById('conditionFields2');

                
                // Clear previous condition fields
                conditionFields.innerHTML = "";
                conditionFields2.innerHTML = "";
                // conditionFields2.innerHTML = "";

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
                    conditionFields.appendChild(document.createElement('br'));
                }
                if (selectedAction === 'edit') {
                    conditionFields2.appendChild(document.createElement('br'));
                    var conditionLabel2 = document.createElement('label');
                    conditionLabel2.setAttribute('for', 'condition2');
                    conditionLabel2.textContent = 'Поле для получения данных искомой изменяемой строки:';
                    conditionFields2.appendChild(conditionLabel2);
                    var conditionInput2 = document.createElement('input');
                    // conditionInput2.disabled = "disabled";
                    conditionInput2.setAttribute('type', 'text');
                    // conditionInput2.setAttribute('name', 'condition2');
                    conditionInput2.setAttribute('id', 'condition2');
                    conditionFields2.appendChild(conditionInput2);
                    
                    // Create the button element
                    var button = document.createElement("div");
                    // button.setAttribute("content","Click Me");
                    button.innerHTML = "Получить данные";
                    // button.setAttribute("onclick", "fetch_row()");
                    button.style.cursor = "pointer";  // Change the cursor to indicate clickability
                    button.addEventListener("click", fetch_row);
                    conditionInput2.addEventListener("keydown", function(event) {
                        if (event.key === "Enter") {
                            fetch_row();
                        }
                    });

                    // Add an event listener to call fetch_row() function on click
                    // button.addEventListener("click", fetch_row);

                    // Append the button to the conditionFields2 element
                    conditionFields2.appendChild(button);
                    conditionFields2.appendChild(document.createElement('br'));
                    conditionFields2.appendChild(document.createElement('br'));

                }
            }

            function fetch_row() {
                var selectedTable = document.getElementById('tableSelect').value;
                var condition = document.getElementById('condition2').value;
                var xhr = new XMLHttpRequest();

                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        console.log(xhr.responseText);
                        console.log(condition);
                        var rowData = JSON.parse(xhr.responseText)[0];
                        // Do something with the rowData to edit columns
                        // For example, populate the input fields with the rowData
                        for (var key in rowData) {
                            console.log(key);
                            console.log(rowData);
                            if (rowData.hasOwnProperty(key)) {
                                document.getElementById(key).value = rowData[key];
                            }
                        }
                    }
                };

                xhr.open('GET', 'preread.php?table=' + selectedTable + '&condition=' + condition, true);
                xhr.send();
            }

        </script>

    </div>
</body>

</html>
