<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Интернет-магазин учебной литературы</title>
	<link rel="stylesheet" href="styles.css">
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

		    <h3>Выберите учебники:</h3>
		    <div class="cards-container">
				<?php
				$servername = "host.docker.internal";
				$username = "root";
				$password = "test";
				$dbname = "myDB";

				try {
				    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
				    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

				    $sql = "SELECT id, name, author, speciality, price, quantity FROM Books";
				    $stmt = $conn->query($sql);

				    if ($stmt->rowCount() > 0) {
				        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				            echo "<div class='card'>";
				            echo "<h3>" . $row["name"] . "</h3>";
				            echo "<p>Автор: " . $row["author"] . "</p>";
				            echo "<p>Специальность: " . $row["speciality"] . "</p>";
				            echo "<p>Цена: " . $row["price"] . "</p>";
				            echo "<input type='number' name='quantity[" . $row["id"] . "]' value='0' min='0'>";
				            echo "</div>";
				        }
				    } else {
				        echo "0 results";
				    }
				} catch(PDOException $e) {
				    echo "Error: " . $e->getMessage();
				}
				$conn = null;
				?>
		    </div>

		    <label for="delivery">Доставка на дом:</label>
		    <input type="checkbox" id="delivery" name="delivery">

		    <label for="to_file">Сохранить детали заказа в файл</label>
		    <input type="checkbox" id="to_file" name="to_file">

		    <label for="address">Адрес доставки</label>
		    <input type="text" id="address" name="address">

		    <input type="submit" name="submitOrder" value="Оформить заказ">
		</form>
		</div>

		<div class="total-cost" id="totalCost">
			Итоговая стоимость: <span id="totalAmount">0</span> руб.
		</div>
	</main>
			<?php
			
			if (isset($_POST['submitOrder'])) {
			    $servername = "host.docker.internal";
			    $username = "root";
			    $password = "test";
			    $dbname = "myDB";

			    try {
			        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
			        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			        $user_name = $_POST['fullName'];
			        $email = $_POST['email'];
			        $datetime = date('Y-m-d H:i:s');
			        $on_home = isset($_POST['delivery']) ? 1 : 0;
			        $address = isset($_POST['delivery']) ? $_POST['address'] : '';

			        // Проверка существования пользователя
			        $stmt = $conn->prepare("SELECT id FROM Person WHERE name=:user_name");
			        $stmt->bindParam(':user_name', $user_name);
			        $stmt->execute();

			        if ($stmt->rowCount() > 0) {
			            $row = $stmt->fetch(PDO::FETCH_ASSOC);
			            $user_id = $row["id"];
			            echo "User ID: " . $user_id;
			        } else {
			            $stmt = $conn->query("SELECT MAX(id) AS max_id FROM Person");
			            $row_max_id = $stmt->fetch(PDO::FETCH_ASSOC);
			            $new_id = $row_max_id["max_id"] + 1;
			            $sql_insert_user = "INSERT INTO Person (id, name, email) VALUES (:new_id, :user_name, :email)";
			            $stmt = $conn->prepare($sql_insert_user);
			            $stmt->bindParam(':new_id', $new_id);
			            $stmt->bindParam(':user_name', $user_name);
			            $stmt->bindParam(':email', $email);
			            if ($stmt->execute()) {
			                //echo "New record created successfully. User ID: " . $new_id;
			                $user_id = $new_id;
			            } else {
			                echo "Error creating user: " . $stmt->errorInfo();
			            }
			        }

			        foreach ($_POST['quantity'] as $book_id => $quantity) {
			            if ($quantity != 0) {
			                $stmt = $conn->prepare("SELECT id, price FROM Books WHERE id = :book_id");
			                $stmt->bindParam(':book_id', $book_id);
			                $stmt->execute();
			                $row = $stmt->fetch(PDO::FETCH_ASSOC);

			                if ($stmt->rowCount() > 0) {
			                    $price = $row["price"];
			                    if ($on_home) {
			                        $price *= 0.9;
			                    }
			                    $stmt = $conn->prepare("INSERT INTO Orders (user_id, datetime, book_id, count, price, on_home, address) 
			                        VALUES (:user_id, :datetime, :book_id, :quantity, :price, :on_home, :address)");
			                    $stmt->bindParam(':user_id', $user_id);
			                    $stmt->bindParam(':datetime', $datetime);
			                    $stmt->bindParam(':book_id', $book_id);
			                    $stmt->bindParam(':quantity', $quantity);
			                    $stmt->bindParam(':price', $price);
			                    $stmt->bindParam(':on_home', $on_home);
			                    $stmt->bindParam(':address', $address);
			                    if (!$stmt->execute()) {
			                        echo "Error: " . $stmt->errorInfo();
			                    }
			                }
			            }
			        }
			    } catch(PDOException $e) {
			        echo "Connection failed: " . $e->getMessage();
			    }
			}
		// Вывод на экран данных о заказе
		try {
		    $sql_order_details = "
		        SELECT Books.author, Books.name as 'title', Books.speciality, Orders.count, Orders.price, Person.name AS 'person_name', Orders.address AS 'address_delivery'
		        FROM Orders
		        INNER JOIN Books ON Orders.book_id = Books.id
		        INNER JOIN Person ON Orders.user_id = Person.id
		        WHERE Orders.datetime = :datetime AND Orders.user_id = :user_id
		    ";

		    $stmt = $conn->prepare($sql_order_details);
		    $stmt->bindParam(':datetime', $datetime);
		    $stmt->bindParam(':user_id', $user_id);
		    $stmt->execute();

		    if ($stmt) {
		        $order_details = "<h2 style='color: blue; font-size: 28px;'>Order Details</h2>";
		        $total_amount = 0;

		        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		            $subtotal = $row['count'] * $row['price'];
		            $order_details .= "<p><strong style='color: green; font-size: 18px;'>Название:</strong> {$row['title']}</p>";
		            $order_details .= "<p><strong>Автор:</strong> {$row['author']}</p>";
		            $order_details .= "<p><strong>Специальность:</strong> {$row['speciality']}</p>";
		            $order_details .= "<p><strong>Количество:</strong> {$row['count']}</p>";
		            $order_details .= "<p><strong>Цена за один экземпляр:</strong> {$row['price']}</p>";
		            $order_details .= "<p><strong>Цена за все экземпляры:</strong> $subtotal</p>";
		            $total_amount += $subtotal;
		            $person_name = $row['person_name'];
		            $address_delivery = $row['address_delivery'];
		        }

		        $order_details .= "<p><strong style='color: blue; font-size: 28px;'>Общая стоимость заказа:</strong> $total_amount</p>";
		        $order_details .= "<p><strong style='color: blue; font-size: 28px;'>Имя заказчика:</strong> {$person_name}</p>";
		        $order_details .= "<p><strong style='color: blue; font-size: 28px;'>Адрес доставки:</strong> {$address_delivery}</p>";

		        echo $order_details; 

		        if (isset($_POST['to_file'])) {
		            // Выгрузка результат в файл
		            $file_path = 'order_details_' . date('Y-m-d_H-i-s') . '.txt';

		            $stmt->execute(); 

		            $order_details = "Order Details\n\n";
		            $total_amount = 0;

		            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		                $subtotal = $row['count'] * $row['price'];
		                $order_details .= "Название: {$row['title']}\n";
		                $order_details .= "Автор: {$row['author']}\n";
		                $order_details .= "Специальность: {$row['speciality']}\n";
		                $order_details .= "Количество: {$row['count']}\n";
		                $order_details .= "Цена за один экземпляр: {$row['price']}\n";
		                $order_details .= "Цена за все экземпляры: $subtotal\n";
		                $total_amount += $subtotal;
		                $person_name = $row['person_name'];
		                $address_delivery = $row['address_delivery'];
		            }

		            $order_details .= "Общая стоимость заказа: $total_amount\n";
		            $order_details .= "Имя заказчика: $person_name\n";
		            $order_details .= "Адрес доставки: $address_delivery\n";

		            // Сохранение в файл
		            file_put_contents($file_path, $order_details);

		            $conn = null; // Закрытие соединения с базой
		        }
		    }
		} catch (PDOException $e) {
		    echo "Error: " . $e->getMessage();
		}
		?>

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