-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jan 08, 2023 at 08:21 AM
-- Server version: 8.0.27
-- PHP Version: 7.4.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bugana`
--

DELIMITER $$
--
-- Procedures
--
DROP PROCEDURE IF EXISTS `Perish`$$
CREATE PROCEDURE `Perish` (IN `perish_list` TEXT, IN `length` INT)  BEGIN
  SET @i = 0;
  WHILE @i < length DO
    SET @id = SUBSTRING_INDEX(perish_list, ',', @i + 1);

    SELECT product, quantity, stocks
    INTO @productid, @quantity, @available
    FROM stocks WHERE id=@id;

    SELECT price INTO @price
    FROM products WHERE id=@productid;

    SET @amount = @price * @available;
    INSERT INTO stocks (product, quantity, stocks, amount, status)
    VALUES (@productid, @available * -1, @id, @amount, 'perished');

    UPDATE stocks SET stocks=0 WHERE id=@id;

    SET @i = @i + 1;
  END WHILE;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

DROP TABLE IF EXISTS `carts`;
CREATE TABLE IF NOT EXISTS `carts` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'Cart ID',
  `user` int NOT NULL COMMENT 'User ID',
  `product` int NOT NULL COMMENT 'Product ID',
  `quantity` int NOT NULL COMMENT 'Quantity',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Date added',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'Product ID',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Product Name',
  `user` int NOT NULL COMMENT 'Farmer Code',
  `category` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Product Category',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Product Description',
  `price` double(12,2) NOT NULL COMMENT 'Product Price per Quantity',
  `perish` int NOT NULL COMMENT 'Days to perish',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Product Added Date',
  `edited` timestamp NULL DEFAULT NULL COMMENT 'Product Last Edited Date',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stocks`
--

DROP TABLE IF EXISTS `stocks`;
CREATE TABLE IF NOT EXISTS `stocks` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'Stock ID',
  `product` int NOT NULL COMMENT 'Product ID',
  `quantity` int NOT NULL COMMENT 'Quantity added or subtracted',
  `stocks` int NOT NULL COMMENT 'Stock In - Available stocks\r\nStock Out - Stock id',
  `amount` float(12,2) NOT NULL DEFAULT '0.00' COMMENT 'Amount (in pesos) received when stocking out',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Stock out status (sold or perished)',
  `transaction_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Transaction code when sold',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Date added',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
CREATE TABLE IF NOT EXISTS `transactions` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'Transaction ID',
  `transaction_code` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Unique Transaction Code',
  `user` int NOT NULL COMMENT 'Customer ID',
  `product` int NOT NULL COMMENT 'Product ID',
  `quantity` int NOT NULL COMMENT 'Quantity',
  `shipping` float(6,2) NOT NULL COMMENT 'Shipping Fee',
  `amount` float(12,2) NOT NULL COMMENT 'Amount paid',
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending' COMMENT 'Transaction Status',
  `paymentoption` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Payment Option',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Transaction Date',
  `updated` timestamp ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Latest Update Timestamp',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shipping`
--

DROP TABLE IF EXISTS `shipping`;
CREATE TABLE IF NOT EXISTS `shipping` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'Shipping fee ID',
  `name` varchar(255) NOT NULL COMMENT 'Barangay name',
  `fee` float(12,2) NOT NULL COMMENT 'Shipping fee',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shipping`
--

INSERT INTO `shipping` (`id`, `name`, `fee`) VALUES
(1, 'Abuanan', 45.00),
(2, 'Alianza', 40.00),
(3, 'Atipuluan', 40.00),
(4, 'Bacong-Montilla', 30.00),
(5, 'Bagroy', 50.00),
(6, 'Balingasag', 50.00),
(7, 'Binubuhan', 30.00),
(8, 'Busay', 50.00),
(9, 'Calumangan', 55.00),
(10, 'Caridad', 45.00),
(11, 'Don Jorge L. Araneta', 30.00),
(12, 'Dulao', 50.00),
(13, 'Ilijan', 30.00),
(14, 'Lag-Asan', 50.00),
(15, 'Ma-ao', 20.00),
(16, 'Mailum', 30.00),
(17, 'Malingin', 50.00),
(18, 'Napoles', 50.00),
(19, 'Pacol', 45.00),
(20, 'Poblacion', 50.00),
(21, 'Saga\r\nsa', 45.00),
(22, 'Tabunan', 55.00),
(23, 'Taloc', 55.00),
(24, 'Sampinit', 55.00);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'User ID',
  `code` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'User unique code',
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Username',
  `password` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Password',
  `temp_password` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Temporary password (used when password is forgotten)',
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Email',
  `mobile` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Mobile Number',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Full Name',
  `gender` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Gender',
  `birthday` date NOT NULL COMMENT 'Birthday',
  `addressstreet` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Address Street',
  `addresspurok` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Address Purok',
  `addressbrgy` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Address Barangay',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Creation Date',
  `lastlogin` timestamp NULL DEFAULT NULL COMMENT 'Last Login Date',
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'customer' COMMENT 'User Type',
  `active` int NOT NULL DEFAULT '1' COMMENT 'Account Active',
  `verified` int NOT NULL DEFAULT '0' COMMENT 'Account verification status',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `violations`
--

DROP TABLE IF EXISTS `violations`;
CREATE TABLE IF NOT EXISTS `violations` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'Violation ID',
  `user` int NOT NULL COMMENT 'User ID',
  `transaction_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Transaction Code',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Date added',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DELIMITER $$
--
-- Events
--
DROP EVENT IF EXISTS `check_perish`$$
CREATE EVENT `check_perish` ON SCHEDULE EVERY 1 DAY STARTS '2022-12-05 00:00:00' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
  SELECT
    GROUP_CONCAT(stocks.id),
    COUNT(stocks.id)
  INTO @perished, @length
  FROM stocks
  JOIN products ON
    products.id = stocks.product AND
    DATEDIFF(NOW(), stocks.date) >= products.perish
  WHERE stocks.stocks>0;
  CALL Perish(@perished, @length);
END$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
