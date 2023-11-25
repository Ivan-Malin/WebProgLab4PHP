<!-- Тип документа -->
<!DOCTYPE html>
<!-- Начало html кода -->
<html>
<!-- Начало тега head -->
<head>
    <!-- Название вкладки -->
    <title>Обработка строк</title>
</head>
<!-- Начало основного тела документа -->
<body>
    <!-- Форма для ввода строки -->
    <form method="post">
        <!-- Подсказка для пользователя -->
        <label for="userInput">Введите строку:</label><br>
        <!-- Текстовое поле для ввода текста -->
        <input type="text" id="userInput" name="userInput">
        <!-- Кнопка отправки запроса -->
        <input type="submit" value="Обработать">
    </form>

    <?php
    // Проверка, была ли отправлена строка пользователем
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Получение введенной строки
        $userString = $_POST['userInput'];

        // Вывод информации о исходной строке
        echo "<p>Исходная строка: $userString</p>";
        // Конкатенация строк
        echo "<p>Длина строки: " . strlen($userString) . "</p>";
        // Найти сколько и на какой позиции определенных букв
        $searchLetters = ['а', 'б', 'в', 'г','э','ю', 'я'];
        foreach ($searchLetters as $letter) {
            // Подсчитать количество каждой из букв в строке 
            $count = substr_count(mb_strtolower($userString, 'utf-8'), $letter);
            echo "<p>Буква '$letter' встречается $count раз(а)</p>";
            // Найти позиции буквы в строке
            $positions = [];
            $pos = mb_strpos(mb_strtolower($userString, 'utf-8'), $letter);
            while ($pos !== false) {
                $positions[] = $pos + 1; 
                $pos = mb_strpos(mb_strtolower($userString, 'utf-8'), $letter, $pos + 1);
            }
            echo "<p>Позиции буквы '$letter': " . implode(', ', $positions) . "</p>";
        }

        // Заменить подстроки в строке
        $userString = str_replace('и', 'e', $userString);
        echo "<p>Строка после замены 'и' на 'e': $userString</p>";

        // Вывести инвертированную строку
        $length = mb_strlen($userString);
        $invertedString = '';
        // Из-за особенностей языка при работе с кириллицей, 
        // реализуем инверсию как взятие на каждой итерации цикла подстроки длиной 1
        while ($length > 0) {
            $length--;
            $invertedString .= mb_substr($userString, $length, 1, 'UTF-8');
        }

    echo "<p>Инвертированная строка: $invertedString</p>";

        // Разбить строку на подстроки (аналог split в JavaScript)
        $substrings = explode(' ', $userString);
        echo "<p>Подстроки после разбиения по пробелу: " . implode(', ', $substrings) . "</p>";
    }
    ?>
<!-- Конец тега body -->
</body>
<!-- Конец тега html -->
</html>