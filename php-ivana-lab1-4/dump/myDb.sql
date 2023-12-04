CREATE DATABASE IF NOT EXISTS myDB;
USE myDB;
/*
SET
  SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET
  time_zone = "+01:00";
*/
  /*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
  /*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
  /*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
CREATE TABLE `Person` (
    `id` int(11) NOT NULL,
    `name` varchar(20) NOT NULL,
    `email` varchar(100) NOT NULL
  ) ENGINE = InnoDB DEFAULT CHARSET = latin1;
INSERT INTO
  `Person` (`id`, `name`,`email`)
VALUES
  (1, 'Conf', '1@test.com'),
  (2, 'Nipsu','2@test.com'),
  (3, 'Chelovek','3@test.com'),
  (4, 'Beyar','4@test.com');

CREATE TABLE `Books` (
  `id` int(11) NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `author` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `speciality` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` float NOT NULL,
  `quantity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



INSERT INTO `Books` (`id`, `name`, `author`, `speciality`, `price`,`quantity`) VALUES
(1, 'Математика. Курс лекций', 'Иванов И.И.', 'Математика', 1200,5),
(2, 'История России', 'Петров П.П.', 'История', 800.5,6),
(3, 'Основы программирования', 'Сидоров С.С.', 'Программирование', 1500.75,7),
(4, 'Физика. Практикум', 'Андреев А.А.', 'Физика', 1100.2,8),
(5, 'Английский язык. Учебник', 'Смирнова О.И.', 'Иностранные языки', 950.9,9);

CREATE TABLE `Orders` (
    `user_id` INT,
    `datetime` DATETIME,
    `book_id` INT NOT NULL,
    `count` INT NOT NULL,
    `price` FLOAT NOT NULL,
    `on_home` BOOLEAN,
    `address` VARCHAR(100),
    PRIMARY KEY (user_id, datetime,book_id)
);
  /*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
  /*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
  /*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;