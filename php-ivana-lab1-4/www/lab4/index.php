<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Интернет-магазин учебной литературы</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 1em;
        }

        main {
            max-width: 800px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 8px;
            display: flex;
            justify-content: space-between;
        }

        input,
        textarea,
        select {
            margin-bottom: 16px;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            /* добавлено для учета padding и border в общей ширине элемента */
        }

        input[type="submit"] {
            background-color: #333;
            color: #fff;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #555;
        }

        .cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }

        .card {
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 10px;
        }

        .quantity-input {
            width: 80px;
            transition: width 0.3s;
            /* Добавлено для плавного перехода при изменении ширины */
        }

        .quantity-input:valid {
            width: 80px;
        }

        .quantity-input:invalid {
            width: 0;
        }

        .result {
            margin-top: 20px;
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 4px;
        }

        .total-cost {
            position: fixed;
            bottom: 0;
            right: 0;
            background-color: #fff;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>

    <header>
        <h1>Книжный магазин учебной литературы</h1>
    </header>

    <main>
        <h2>Заказ учебной литературы</h2>

        <!-- Форма заказа -->
        <form action="" method="post" onsubmit="return validateForm()">
            <label for="fullName">ФИО:</label>
            <input type="text" id="fullName" name="fullName" required pattern="[A-Za-zА-Яа-яЁё\s]+" title="Только буквы и пробелы">

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="userId">UserId:</label>
            <input type="text" name="userId">

            <h3>Выберите учебники:</h3>
            <div class="cards-container">
                <?php
                $host = "host.docker.internal"; // адрес сервера базы данных
                $username = "root"; // имя пользователя
                $password = "test"; // пароль (если есть)
                $database = "myDb"; // название базы данных

                // Подключение к базе данных
                $conn = new mysqli($host, $username, $password, $database);

                // Проверка соединения
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Получение информации о книгах из таблицы Books
                $sql = "SELECT id, name, author, speciality, price FROM Books";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="card">';
                        echo '<h4>' . $row["name"] . '</h4>';
                        echo '<p><strong>Автор:</strong> ' . $row["author"] . '</p>';
                        echo '<p><strong>Специальность:</strong> ' . $row["speciality"] . '</p>';
                        echo '<p><strong>Цена:</strong> ' . $row["price"] . ' руб.</p>';
                        echo '<label>';
                        echo 'Количество: <input class="quantity-input" type="number" name="quantity[' . $row["id"] . ']" placeholder="Количество" min="0">';
                        echo '</label>';
                        echo '</div>';
                    }
                }
                ?>
            </div>

            <label for="delivery">Доставка на дом:</label>
            <input type="checkbox" id="delivery" name="delivery">

            <input type="submit" name="submitOrder" value="Оформить заказ">
        </form>

        <!-- Результат обработки заказа -->
        <div class="result">
            <?php
            if (isset($_POST['submitOrder'])) {
                $fullName = $_POST["fullName"];
                $email = $_POST["email"];
                $userId = $_POST["userId"];
                $delivery = isset($_POST["delivery"]) ? 1 : 0; // Проверка наличия доставки

                // Общая стоимость заказа
                $totalPrice = 0;

                // Блок для хранения информации о заказанных книгах
                $orderedBooksInfo = "";

                foreach ($_POST["quantity"] as $bookId => $count) {
                    if ($count > 0) {
                        $sql = "SELECT id, name, author, speciality, price FROM Books WHERE id = '$bookId'";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            $row = $result->fetch_assoc();
                            $bookPrice = $row["price"];

                            // Уменьшение цены на 10% при выборе доставки на дом
                            if ($delivery) {
                                $bookPrice *= 0.9;
                            }

                            $totalPrice += $bookPrice * $count; // Обновление общей стоимости заказа

                            // Формирование информации о книгах в заказе
                            $orderedBooksInfo .= $row["name"] . ' - ' . $row["author"] . ' - ' . $row["speciality"] . ' - ' . $bookPrice . ' руб. (количество: ' . $count . ')<br>';
                        }
                    }
                }

                // Вставка данных в таблицу Users, если пользователя нет
                $userQuery = "INSERT IGNORE INTO Users (id, name, email) VALUES ($userId, '$fullName', '$email')";
                $conn->query($userQuery);

                // Получение ID пользователя
                $userIdQuery = "SELECT id FROM Users WHERE email = '$email'";
                $userResult = $conn->query($userIdQuery);

                if ($userResult->num_rows > 0) {
                    $userRow = $userResult->fetch_assoc();
                    $userId = $userRow["id"];

                    // Вставка данных в таблицу Orders
                    $insertOrderQuery = "INSERT INTO Orders (id_user, date, status, delivery) VALUES ('$userId', NOW(), 'В обработке', '$delivery')";
                    $conn->query($insertOrderQuery);
                    $orderId = $conn->insert_id; // Получение ID только что созданного заказа

                    // Добавление информации о заказанных книгах в таблицу OrderDetails
                    foreach ($_POST["quantity"] as $bookId => $count) {
                        if ($count > 0) {
                            $sql = "INSERT INTO OrderDetails (id_order, id_book, count) VALUES ('$orderId', '$bookId', '$count')";
                            $conn->query($sql);
                        }
                    }

                    // Вывод информации о заказе
                    echo "<h3>Информация о заказе:</h3>";
                    echo "<p><strong>ФИО:</strong> $fullName</p>";
                    echo "<p><strong>Email:</strong> $email</p>";
                    echo "<p><strong>Дата заказа:</strong> " . date("Y-m-d H:i:s") . "</p>";
                    echo "<p><strong>Заказанные книги:</strong><br> $orderedBooksInfo</p>";

                    // Итоговая стоимость с учетом доставки на дом
                    $deliveryCost = $delivery ? $totalPrice * 0.1 : 0;
                    $totalWithDelivery = $totalPrice + $deliveryCost;
                    echo "<p><strong>Итоговая стоимость с учетом доставки на дом:</strong> $totalWithDelivery руб.</p>";

                    echo "Заказ успешно оформлен!";
                } else {
                    echo "Ошибка при создании пользователя.";
                }
            }
            ?>
        </div>

        <div class="total-cost" id="totalCost">
            Итоговая стоимость: <span id="totalAmount">0</span> руб.
        </div>
    </main>

    <script>
        function validateForm() {
            var fullName = document.getElementById("fullName").value;
            var email = document.getElementById("email").value;

            // Простая проверка на наличие букв в имени
            if (!/^[A-Za-zА-Яа-яЁё\s]+$/.test(fullName)) {
                alert("Пожалуйста, введите корректное ФИО (только буквы и пробелы).");
                return false;
            }

            // HTML5 встроенная проверка email
            if (!document.getElementById("email").checkValidity()) {
                alert("Пожалуйста, введите корректный email.");
                return false;
            }

            return true;
        }
    </script>
    <script>
        // Обновление блока "Итоговая стоимость" при изменении количества товаров
        function updateTotalCost() {
            var totalAmount = 0;
            var quantityInputs = document.querySelectorAll('.quantity-input');

            quantityInputs.forEach(function(input) {
                var card = input.closest('.card');
                var priceElement = card.querySelector('p strong:last-child');
                // input.value
                document.getElementById('totalAmount').textContent = card.textContent.match('[a-z][a-z0-9]*')+'..';
                // Используем регулярное выражение для извлечения числа из текста
                var priceMatch = priceElement.textContent.match('/(\d+(\.\d+)?) руб\./');
                var price = priceMatch ? parseFloat(priceMatch[1]) : 0;
                var count = parseInt(input.value) || 0;
                totalAmount += price * count;
            });

            var deliveryCheckbox = document.getElementById('delivery');
            var deliveryCost = deliveryCheckbox.checked ? totalAmount * 0.1 : 0;
            var totalWithDelivery = totalAmount + deliveryCost;

            document.getElementById('totalAmount').textContent += totalWithDelivery.toFixed(2);
        }


        // Добавление обработчика событий к каждому input
        var quantityInputs = document.querySelectorAll('.quantity-input');
        quantityInputs.forEach(function(input) {
            input.addEventListener('input', updateTotalCost);
        });
    </script>

</body>

</html>