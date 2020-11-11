--
-- Скрипт сгенерирован Devart dbForge Studio for MySQL, Версия 7.2.58.0
-- Домашняя страница продукта: http://www.devart.com/ru/dbforge/mysql/studio
-- Дата скрипта: 11.11.2020 10:46:16
-- Версия сервера: 5.5.5-10.3.22-MariaDB
-- Версия клиента: 4.1
--


--
-- Описание для базы данных dserov__gallery
--
DROP DATABASE IF EXISTS dserov__gallery;
CREATE DATABASE IF NOT EXISTS dserov__gallery
	CHARACTER SET utf8mb4
	COLLATE utf8mb4_general_ci;

-- 
-- Отключение внешних ключей
-- 
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;

-- 
-- Установить режим SQL (SQL mode)
-- 
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- 
-- Установка кодировки, с использованием которой клиент будет посылать запросы на сервер
--
SET NAMES 'utf8';

-- 
-- Установка базы данных по умолчанию
--
USE dserov__gallery;

--
-- Описание для таблицы pictures
--
CREATE TABLE IF NOT EXISTS pictures (
  id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  path VARCHAR(50) NOT NULL,
  name VARCHAR(50) NOT NULL,
  size INT(11) UNSIGNED NOT NULL,
  click INT(11) UNSIGNED NOT NULL DEFAULT 0,
  alt VARCHAR(255) DEFAULT '''NULL''',
  PRIMARY KEY (id)
)
ENGINE = INNODB
AUTO_INCREMENT = 5
AVG_ROW_LENGTH = 5461
CHARACTER SET utf8mb4
COLLATE utf8mb4_general_ci
ROW_FORMAT = DYNAMIC;

-- 
-- Вывод данных для таблицы pictures
--

/*!40000 ALTER TABLE pictures DISABLE KEYS */;
INSERT INTO pictures VALUES
(1, 'foto\\', '5fab93f2.jpg', 78607, 2, NULL),
(2, 'foto\\', '5fab93f9.jpg', 422017, 10, NULL),
(3, 'foto\\', '5fab9403.jpg', 19376, 3, NULL),
(4, 'foto\\', '5fab969e.jpg', 73387, 1, 'NULL');

/*!40000 ALTER TABLE pictures ENABLE KEYS */;

-- 
-- Восстановить предыдущий режим SQL (SQL mode)
-- 
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;

-- 
-- Включение внешних ключей
-- 
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;