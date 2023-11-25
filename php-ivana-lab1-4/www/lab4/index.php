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
            max-width: 600px;
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
        }

        input, textarea, select {
            margin-bottom: 16px;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        input[type="submit"] {
            background-color: #333;
            color: #fff;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #555;
        }

        .result {
            margin-top: 20px;
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 4px;
        }
    </style>
</head>
</head>
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

        <label for="books">Выберите учебники (разделяйте запятыми):</label>
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
                echo '<div>';
                echo '<label>';
                echo '<input type="text" name="quantity[' . $row["id"] . ']" placeholder="Количество">';
                echo $row["name"] . ' - ' . $row["author"] . ' - ' . $row["speciality"] . ' - ' . $row["price"] . ' руб.';
                echo '</label>';
                echo '</div>';
            }
        }
        ?>
        
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
        $userQuery = "INSERT IGNORE INTO Users (name, email) VALUES ('$fullName', '$email')";
        $conn->query($userQuery);

        // Получение ID пользователя
        $userIdQuery = "SELECT id FROM Users WHERE email = '$email'";
        $userResult = $conn->query($userIdQuery);
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
    }
    ?>
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

</body>
</html>