-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 17, 2022 at 12:03 PM
-- Server version: 10.4.25-MariaDB
-- PHP Version: 8.1.12

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

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

DROP TABLE IF EXISTS `carts`;
CREATE TABLE `carts` (
  `id` int(11) NOT NULL COMMENT 'Cart ID',
  `user` int(11) NOT NULL COMMENT 'User ID',
  `product` int(11) NOT NULL COMMENT 'Product ID',
  `quantity` int(11) NOT NULL COMMENT 'Quantity',
  `date` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Date added'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` int(11) NOT NULL COMMENT 'Product ID',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Product Name',
  `user` int(11) NOT NULL COMMENT 'Farmer Code',
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Product Category',
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Product Description',
  `price` double(12,2) NOT NULL COMMENT 'Product Price per Quantity',
  `created` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Product Added Date',
  `edited` timestamp NULL DEFAULT NULL COMMENT 'Product Last Edited Date'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stocks`
--

DROP TABLE IF EXISTS `stocks`;
CREATE TABLE `stocks` (
  `id` int(11) NOT NULL COMMENT 'Stock ID',
  `product` int(11) NOT NULL COMMENT 'Product ID',
  `quantity` int(11) NOT NULL COMMENT 'Quantity added or subtracted',
  `date` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Date added'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
CREATE TABLE `transactions` (
  `id` int(11) NOT NULL COMMENT 'Transaction ID',
  `transaction_code` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Unique Transaction Code',
  `user` int(11) NOT NULL COMMENT 'Customer ID',
  `product` int(11) NOT NULL COMMENT 'Product ID',
  `quantity` int(11) NOT NULL COMMENT 'Quantity',
  `amount` float(12,2) NOT NULL COMMENT 'Amount paid',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Transaction Status',
  `paymentoption` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Payment Option',
  `date` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Transaction Date'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL COMMENT 'User ID',
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Username',
  `password` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Password',
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Email',
  `mobile` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Mobile Number',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Full Name',
  `gender` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Gender',
  `birthday` date NOT NULL COMMENT 'Birthday',
  `addressstreet` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Address Street',
  `addressbrgy` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Address Barangay',
  `addresscity` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Address City',
  `created` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Creation Date',
  `lastlogin` timestamp NULL DEFAULT NULL COMMENT 'Last Login Date',
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'customer' COMMENT 'User Type',
  `active` int(11) NOT NULL DEFAULT 1 COMMENT 'Account Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stocks`
--
ALTER TABLE `stocks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Cart ID';

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Product ID';

--
-- AUTO_INCREMENT for table `stocks`
--
ALTER TABLE `stocks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Stock ID';

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Transaction ID';

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'User ID';
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
