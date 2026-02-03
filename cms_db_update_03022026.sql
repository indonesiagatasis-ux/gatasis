-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Feb 03, 2026 at 08:27 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cms_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `attributes`
--

DROP TABLE IF EXISTS `attributes`;
CREATE TABLE IF NOT EXISTS `attributes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `slug` varchar(191) DEFAULT NULL,
  `input_type` enum('text','number','select') DEFAULT 'text',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `attributes`
--

INSERT INTO `attributes` (`id`, `name`, `slug`, `input_type`, `created_at`) VALUES
(1, 'Size', 'size', 'text', '2026-01-15 03:14:58'),
(2, 'Material', 'material', 'text', '2026-01-15 03:14:58'),
(3, 'Thickness', 'thickness', 'number', '2026-01-15 03:14:58'),
(4, 'Voltage', 'voltage', 'number', '2026-01-15 03:14:58');

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

DROP TABLE IF EXISTS `brands`;
CREATE TABLE IF NOT EXISTS `brands` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `position` int DEFAULT '0',
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`id`, `name`, `slug`, `status`, `created_at`, `updated_at`, `position`, `logo`) VALUES
(1, 'TSC', 'tsc', 'active', '2026-01-15 02:48:24', '2026-01-21 09:02:55', 1, 'brand_6970949f9508e.jpg'),
(2, 'Unitech', 'unitech', 'active', '2026-01-15 02:48:24', '2026-01-21 09:02:59', 2, 'brand_697094aca95c4.jpg'),
(4, 'IDKODE', 'idkode', 'active', '2026-01-21 09:14:47', '2026-01-21 09:14:47', 3, 'brand_69709907e7ff3.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` int DEFAULT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `position` int DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `icon`, `parent_id`, `status`, `created_at`, `updated_at`, `position`) VALUES
(1, 'Barcode Printers', 'barcode-printers', 'cat_6981b086d40a8.png', NULL, 'active', '2026-01-15 02:48:24', '2026-02-03 08:23:34', 1),
(2, 'Mobile Terminals ', 'mobile-terminals-', 'cat_698012ae3c157.png', NULL, 'active', '2026-01-15 02:48:24', '2026-02-02 02:57:50', 2),
(3, 'Software Development', 'software-development', 'cat_6970a026e0ae2.jpg', NULL, 'active', '2026-01-15 02:48:24', '2026-01-22 08:54:27', 6),
(4, 'Mobile Printers', 'mobile-printers', 'cat_6980122d6ff57.jpg', 1, 'active', '2026-01-15 08:40:19', '2026-02-02 02:55:41', 3),
(5, 'Industrial Printers', 'industrial-printers', 'cat_6980121b83d63.png', 1, 'active', '2026-01-15 08:56:32', '2026-02-02 02:55:23', 2),
(6, 'Desktop Printers', 'desktop-printers', 'cat_698012075efab.png', 1, 'active', '2026-01-15 09:06:21', '2026-02-02 02:55:03', 1),
(10, 'Barcode Scanners', 'barcode-scanners', 'cat_69801326cd758.jpg', NULL, 'active', '2026-01-21 09:52:48', '2026-02-02 02:59:50', 3),
(11, 'Marking', 'marking', 'cat_6981b0da62922.png', NULL, 'active', '2026-01-21 09:52:59', '2026-02-03 08:24:58', 4),
(12, 'Handheld Barcode Scanner', 'handheld-barcode-scanner', NULL, 10, 'active', '2026-01-22 01:35:12', '2026-01-22 01:35:12', 1),
(13, 'Rugged Smartphones', 'rugged-smartphones', 'cat_698012a5112e1.png', 2, 'active', '2026-01-22 08:41:00', '2026-02-02 02:57:41', 1),
(14, 'Handheld Terminals', 'handheld-terminals', 'cat_6981b0c1cd931.png', 2, 'active', '2026-01-22 08:41:30', '2026-02-03 08:24:33', 2),
(15, 'Enterprise Tablets', 'enterprise-tablets', 'cat_698012c60043b.png', 2, 'active', '2026-01-22 08:41:42', '2026-02-02 02:58:14', 3),
(18, 'Accessories', 'accessories', 'cat_6981b0b854ea2.png', 1, 'active', '2026-01-22 08:54:14', '2026-02-03 08:24:24', 4),
(19, 'Wireless Handheld Scanners', 'wireless-handheld-scanners', NULL, 10, 'active', '2026-01-22 08:54:53', '2026-01-22 08:54:53', 2),
(20, 'Pocket Barcode Scanners', 'pocket-barcode-scanners', NULL, 10, 'active', '2026-01-22 08:55:08', '2026-01-22 08:55:08', 3),
(21, 'Industrial DPM Scanners', 'industrial-dpm-scanners', NULL, 10, 'active', '2026-01-22 08:55:22', '2026-01-22 08:55:22', 4),
(22, 'Fix Mount Barcode Scanners', 'fix-mount-barcode-scanners', NULL, 10, 'active', '2026-01-22 08:55:34', '2026-01-22 08:55:34', 5),
(23, 'High Resolution Inkjet Printers', 'high-resolution-inkjet-printers', NULL, 11, 'active', '2026-01-22 08:56:00', '2026-01-22 08:56:00', 1),
(24, 'Portable Inkjet Printers', 'portable-inkjet-printers', NULL, 11, 'active', '2026-01-22 08:56:16', '2026-01-22 08:56:16', 2);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_type_id` int DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(12,2) DEFAULT '0.00',
  `status` enum('active','draft') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `category_id` int DEFAULT NULL,
  `brand_id` int DEFAULT NULL,
  `main_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `fk_product_category` (`category_id`),
  KEY `fk_product_brand` (`brand_id`),
  KEY `fk_product_type` (`product_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `product_type_id`, `name`, `slug`, `price`, `status`, `created_at`, `updated_at`, `category_id`, `brand_id`, `main_image`) VALUES
(1, 1, 'TSC TE200', 'tsc-te200', 0.00, 'active', '2026-01-15 02:57:38', '2026-01-22 03:18:14', 1, 1, 'uploads/products/product_697196f6e2e61.png'),
(22, 1, 'TSC TE210', 'tsc-te210', 0.00, 'active', '2026-01-15 07:13:38', '2026-01-22 03:17:59', 1, 1, 'uploads/products/product_697196e7a7462.png'),
(23, 1, 'TSC TDP-225W', 'tsc-tdp-225w', 0.00, 'active', '2026-01-22 03:28:41', '2026-01-22 08:35:25', 4, 1, 'uploads/products/product_6971996973064.png'),
(24, 5, 'Unitech MS926', 'unitech-ms926', 0.00, 'active', '2026-01-22 03:33:21', '2026-01-22 08:10:37', 10, 2, 'uploads/products/product_69719a81e99f8.jpg'),
(25, 5, '1517-14130 Roller Unit', '1517-14130-roller-unit', 0.00, 'active', '2026-02-02 03:28:54', '2026-02-02 03:28:54', 4, 2, NULL),
(26, 1, 'Seal Length', 'seal-length', 0.00, 'active', '2026-02-02 03:30:54', '2026-02-02 03:30:54', 20, 4, NULL),
(27, 2, 'test', 'test', 0.00, 'active', '2026-02-02 03:37:03', '2026-02-02 03:37:03', 2, 4, NULL),
(28, 1, 'test2', 'test2', 0.00, 'active', '2026-02-02 03:38:58', '2026-02-02 03:38:58', 3, 2, NULL),
(30, 4, 'test3', 'test3', 0.00, 'active', '2026-02-02 03:43:00', '2026-02-02 03:43:00', 11, 2, NULL),
(31, 3, 'dgsdgsd', 'dgsdgsd', 0.00, 'active', '2026-02-02 03:46:32', '2026-02-02 03:46:32', 12, 4, NULL),
(32, 2, 'eyerytreye', 'eyerytreye', 0.00, 'active', '2026-02-02 03:49:48', '2026-02-02 03:49:48', 23, 4, NULL),
(33, 1, 'werwerwerwer', 'werwerwerwer', 0.00, 'active', '2026-02-02 03:52:34', '2026-02-02 03:52:34', 14, 2, NULL),
(34, 2, 'asdasfasfasdf', 'asdasfasfasdf', 0.00, 'active', '2026-02-02 03:59:22', '2026-02-02 06:49:43', 10, 4, 'uploads/products/product_69804337c23d4.jpg'),
(35, 5, 'TSC TTP-345', 'tsc-ttp-345', 0.00, 'active', '2026-02-02 04:08:57', '2026-02-02 07:24:35', 14, 1, 'uploads/products/product_698041a1379e6.jpg'),
(36, 2, '21werwerwe', '21werwerwe', 0.00, 'active', '2026-02-02 04:11:31', '2026-02-02 06:14:52', 15, 4, 'uploads/products/product_698040d5dafad.jpg'),
(37, 2, '3423434343', '3423434343', 0.00, 'active', '2026-02-02 04:19:24', '2026-02-02 06:11:04', 14, 4, 'uploads/products/product_69803ff8785a9.jpg'),
(38, 2, 'eeeeeeeeeee', 'eeeeeeeeeee', 0.00, 'active', '2026-02-02 04:25:03', '2026-02-02 06:03:14', 20, 4, 'uploads/products/product_69803e22baa087.65585654.png'),
(39, 2, 'fafafafaf', 'fafafafaf', 0.00, 'active', '2026-02-02 06:53:47', '2026-02-02 06:53:47', 6, 4, NULL),
(41, 1, 'TSC TTP-247', 'tsc-ttp-247', 0.00, 'active', '2026-02-02 06:54:40', '2026-02-02 07:24:41', 2, 1, NULL),
(42, 3, 'aaaaaa', 'aaaaaa', 0.00, 'draft', '2026-02-02 06:56:24', '2026-02-02 06:56:24', 10, 2, NULL),
(43, 1, 'TSC TDP-225', 'tsc-tdp-225', 0.00, 'draft', '2026-02-02 06:59:24', '2026-02-02 07:24:50', 10, 1, NULL),
(44, 5, 'TSC TDP-225 (FULL PORT)', 'tsc-tdp-225-full-port', 0.00, 'draft', '2026-02-02 07:03:22', '2026-02-02 07:24:59', 11, 1, 'uploads/products/product_69804c5984179.png'),
(45, 2, '4ewer', '4ewer', 0.00, 'active', '2026-02-02 07:05:04', '2026-02-02 07:05:26', 18, 2, 'uploads/products/product_69804cb61d8ef.png'),
(46, 2, 'TSC TE310', 'tsc-te310', 0.00, 'draft', '2026-02-02 07:08:25', '2026-02-02 07:25:04', 10, 1, 'uploads/products/product_69804dbda6ab2.jpg'),
(47, 1, 'TSC TTP-243 PLUS', 'tsc-ttp-243-plus', 0.00, 'active', '2026-02-02 07:11:20', '2026-02-02 07:25:10', 11, 1, NULL),
(48, 1, 'TSC TC300', 'tsc-tc300', 0.00, 'active', '2026-02-02 07:11:54', '2026-02-02 07:25:15', 11, 1, NULL),
(49, 1, 'yrtyurtyrtyrtyryt', 'yrtyurtyrtyrtyryt', 0.00, 'active', '2026-02-02 07:13:28', '2026-02-02 07:13:28', 5, 4, NULL),
(50, 1, 'TSC TTP', 'tsc-ttp', 0.00, 'active', '2026-02-02 07:17:21', '2026-02-03 03:09:09', 5, 1, 'uploads/products/product_69804f8d600c8.png'),
(51, 5, 'Unitech HT730', 'unitech-ht730', 0.00, 'active', '2026-02-03 02:36:51', '2026-02-03 02:37:25', 14, 2, 'uploads/products/product_69815f6404014.jpg'),
(52, 2, 'test red', 'test-red', 0.00, 'active', '2026-02-03 03:46:26', '2026-02-03 03:46:26', 18, 2, NULL),
(53, 4, 'test desc', 'test-desc', 0.00, 'active', '2026-02-03 06:30:46', '2026-02-03 06:30:46', 24, 1, NULL),
(54, 4, 'Test Deskripsi', 'test-deskripsi', 0.00, 'active', '2026-02-03 06:33:34', '2026-02-03 06:33:34', 5, 1, NULL),
(57, 4, 'deskripsi spek download', 'deskripsi-spek-download', 0.00, 'active', '2026-02-03 06:54:13', '2026-02-03 06:55:36', 1, 4, 'uploads/products/product_69819be590a3e.jpg'),
(58, 4, 'downloads ', 'downloads-', 0.00, 'active', '2026-02-03 07:56:19', '2026-02-03 07:56:39', 1, 4, 'uploads/products/product_6981aa35c1a2b.jpg'),
(59, 4, 'downloads first', 'downloads-first', 0.00, 'active', '2026-02-03 08:11:05', '2026-02-03 08:21:42', 13, 1, 'uploads/products/product_6981adac2e435.png');

-- --------------------------------------------------------

--
-- Table structure for table `product_attribute_values`
--

DROP TABLE IF EXISTS `product_attribute_values`;
CREATE TABLE IF NOT EXISTS `product_attribute_values` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `attribute_id` int NOT NULL,
  `value` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_product_attr` (`product_id`,`attribute_id`),
  KEY `attribute_id` (`attribute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_descriptions`
--

DROP TABLE IF EXISTS `product_descriptions`;
CREATE TABLE IF NOT EXISTS `product_descriptions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `content` longtext COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `description` longtext COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_id` (`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_descriptions`
--

INSERT INTO `product_descriptions` (`id`, `product_id`, `content`, `created_at`, `updated_at`, `description`) VALUES
(1, 56, 'deskripsi\r\n                                                                    ', '2026-02-03 13:43:17', '2026-02-03 13:43:17', ''),
(2, 57, 'deskripsi spek download\r\n                                                                    ', '2026-02-03 13:54:13', '2026-02-03 13:54:13', ''),
(3, 58, '<span style=\"color: rgb(107, 114, 128); background-color: rgb(245, 246, 250);\">downloads&nbsp;</span>\r\n                                                                    ', '2026-02-03 14:56:19', '2026-02-03 14:56:19', ''),
(4, 59, '\r\n                                    \r\n        \r\n                                    <span style=\"color: rgb(107, 114, 128); background-color: rgb(245, 246, 250);\">downloads first</span>\r\n                                                                                                        <div><span style=\"color: rgb(107, 114, 128); background-color: rgb(245, 246, 250);\"><br></span></div><div><span style=\"color: rgb(107, 114, 128); background-color: rgb(245, 246, 250);\">coba coba saja lah</span></div><div><span style=\"color: rgb(107, 114, 128); background-color: rgb(245, 246, 250);\"><br></span></div><div><ul><li style=\"text-align: left;\"><span style=\"color: rgb(107, 114, 128); background-color: rgb(245, 246, 250);\"><b>hahahaha</b></span></li></ul></div>                                ', '2026-02-03 15:11:05', '2026-02-03 15:21:57', '');

-- --------------------------------------------------------

--
-- Table structure for table `product_downloads`
--

DROP TABLE IF EXISTS `product_downloads`;
CREATE TABLE IF NOT EXISTS `product_downloads` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `title` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `file_size` int DEFAULT NULL,
  `sort_order` int DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_downloads`
--

INSERT INTO `product_downloads` (`id`, `product_id`, `title`, `file_path`, `file_size`, `sort_order`, `created_at`) VALUES
(1, 57, 'deskripsi spek download', 'uploads/product_downloads/dl_69819b95419ec.png', 758238, 0, '2026-02-03 13:54:13'),
(2, 58, 'downloads ', 'uploads/product_downloads/dl_6981aa237b30d.png', 500407, 0, '2026-02-03 14:56:19'),
(3, 59, 'downloads first', 'uploads/product_downloads/dl_6981ad99d247a.png', 493690, 0, '2026-02-03 15:11:05');

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

DROP TABLE IF EXISTS `product_images`;
CREATE TABLE IF NOT EXISTS `product_images` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `is_primary` tinyint(1) DEFAULT '0',
  `position` int DEFAULT '0',
  `status` enum('active','inactive') COLLATE utf8mb4_general_ci DEFAULT 'active',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `image_path`, `is_primary`, `position`, `status`, `created_at`) VALUES
(1, 22, 'uploads/products/product_697196e7a7462.png', 1, 0, 'active', '2026-01-22 10:15:40'),
(2, 1, 'uploads/products/product_697196f6e2e61.png', 1, 0, 'active', '2026-01-22 10:18:14'),
(3, 23, 'uploads/products/product_6971996972843.png', 0, 0, 'active', '2026-01-22 10:28:41'),
(4, 23, 'uploads/products/product_6971996972d1e.png', 0, 1, 'active', '2026-01-22 10:28:41'),
(5, 23, 'uploads/products/product_6971996973064.png', 1, 2, 'active', '2026-01-22 10:28:41'),
(6, 24, 'uploads/products/product_69719a81e99f8.jpg', 1, 0, 'active', '2026-01-22 10:33:21'),
(7, 24, 'uploads/products/product_69719a81e9cba.jpg', 0, 1, 'active', '2026-01-22 10:33:21'),
(8, 24, 'uploads/products/product_69719a81e9ef7.jpg', 0, 2, 'active', '2026-01-22 10:33:21'),
(9, 24, 'uploads/products/product_69719a81ea1e7.jpg', 0, 3, 'active', '2026-01-22 10:33:21'),
(10, 24, 'uploads/products/product_69719a81ea531.png', 0, 4, 'active', '2026-01-22 10:33:21'),
(11, 38, 'uploads/products/product_69802cb55bd63.png', 0, 0, 'active', '2026-02-02 11:48:53'),
(12, 38, 'uploads/products/product_69802cbbd0afb.png', 0, 0, 'active', '2026-02-02 11:48:59'),
(13, 38, 'uploads/products/product_69803e22baa087.65585654.png', 1, 0, 'active', '2026-02-02 13:03:14'),
(14, 38, 'uploads/products/product_69803fd99c532.png', 0, 0, 'active', '2026-02-02 13:10:33'),
(15, 38, 'uploads/products/product_69803fdc714ba.png', 0, 0, 'active', '2026-02-02 13:10:36'),
(16, 37, 'uploads/products/product_69803ff8785a9.jpg', 1, 0, 'active', '2026-02-02 13:11:04'),
(17, 37, 'uploads/products/product_69804005b5592.jpg', 0, 0, 'active', '2026-02-02 13:11:17'),
(18, 37, 'uploads/products/product_69804005b59a1.jpg', 0, 0, 'active', '2026-02-02 13:11:17'),
(19, 37, 'uploads/products/product_69804005b5c67.jpg', 0, 0, 'active', '2026-02-02 13:11:17'),
(20, 37, 'uploads/products/product_69804005b5f16.jpg', 0, 0, 'active', '2026-02-02 13:11:17'),
(21, 36, 'uploads/products/product_698040d5dac20.jpg', 0, 0, 'active', '2026-02-02 13:14:45'),
(22, 36, 'uploads/products/product_698040d5dafad.jpg', 1, 0, 'active', '2026-02-02 13:14:45'),
(23, 36, 'uploads/products/product_698040dcbe080.jpg', 0, 0, 'active', '2026-02-02 13:14:52'),
(24, 36, 'uploads/products/product_698040dcbee23.jpg', 0, 0, 'active', '2026-02-02 13:14:52'),
(25, 35, 'uploads/products/product_698041a136bf5.jpg', 0, 0, 'active', '2026-02-02 13:18:09'),
(26, 35, 'uploads/products/product_698041a136f78.jpg', 0, 0, 'active', '2026-02-02 13:18:09'),
(27, 35, 'uploads/products/product_698041a13721d.jpg', 0, 0, 'active', '2026-02-02 13:18:09'),
(28, 35, 'uploads/products/product_698041a1374b4.jpg', 0, 0, 'active', '2026-02-02 13:18:09'),
(29, 35, 'uploads/products/product_698041a13774b.jpg', 0, 0, 'active', '2026-02-02 13:18:09'),
(30, 35, 'uploads/products/product_698041a1379e6.jpg', 1, 0, 'active', '2026-02-02 13:18:09'),
(31, 35, 'uploads/products/product_698041a407a5c.jpg', 0, 0, 'active', '2026-02-02 13:18:12'),
(32, 35, 'uploads/products/product_698041a407e1e.jpg', 0, 0, 'active', '2026-02-02 13:18:12'),
(33, 35, 'uploads/products/product_698041a408f5a.jpg', 0, 0, 'active', '2026-02-02 13:18:12'),
(34, 35, 'uploads/products/product_698041a409242.jpg', 0, 0, 'active', '2026-02-02 13:18:12'),
(35, 35, 'uploads/products/product_698041a4094db.jpg', 0, 0, 'active', '2026-02-02 13:18:12'),
(36, 35, 'uploads/products/product_698041a409818.jpg', 0, 0, 'active', '2026-02-02 13:18:12'),
(37, 34, 'uploads/products/product_69804337c1cce.jpg', 0, 2, 'active', '2026-02-02 13:24:55'),
(39, 34, 'uploads/products/product_69804337c23d4.jpg', 1, 0, 'active', '2026-02-02 13:24:55'),
(41, 43, 'uploads/products/product_69804b5875d4f.png', 0, 0, 'active', '2026-02-02 13:59:36'),
(42, 43, 'uploads/products/product_69804b5876082.png', 0, 0, 'active', '2026-02-02 13:59:36'),
(43, 43, 'uploads/products/product_69804b587634d.png', 0, 0, 'active', '2026-02-02 13:59:36'),
(44, 44, 'uploads/products/product_69804c5984179.png', 1, 0, 'active', '2026-02-02 14:03:53'),
(45, 45, 'uploads/products/product_69804cb61d8ef.png', 1, 0, 'active', '2026-02-02 14:05:26'),
(46, 45, 'uploads/products/product_69804cb61db34.png', 0, 1, 'active', '2026-02-02 14:05:26'),
(47, 45, 'uploads/products/product_69804cb61dc3b.png', 0, 2, 'active', '2026-02-02 14:05:26'),
(48, 45, 'uploads/products/product_69804cb61dd30.png', 0, 3, 'active', '2026-02-02 14:05:26'),
(49, 46, 'uploads/products/product_69804dbda5a6b.jpg', 0, 0, 'active', '2026-02-02 14:09:49'),
(50, 46, 'uploads/products/product_69804dbda6ab2.jpg', 1, 0, 'active', '2026-02-02 14:09:49'),
(51, 49, 'uploads/products/product_69804ea85f295.png', 0, 0, 'active', '2026-02-02 14:13:44'),
(52, 49, 'uploads/products/product_69804ea85f57d.png', 0, 0, 'active', '2026-02-02 14:13:44'),
(53, 49, 'uploads/products/product_69804ea85f855.png', 0, 0, 'active', '2026-02-02 14:13:44'),
(54, 49, 'uploads/products/product_69804ea85faf1.png', 0, 0, 'active', '2026-02-02 14:13:44'),
(55, 49, 'uploads/products/product_69804ea85fd71.png', 0, 0, 'active', '2026-02-02 14:13:44'),
(56, 50, 'uploads/products/product_69804f8d5fd6b.png', 0, 0, 'active', '2026-02-02 14:17:33'),
(57, 50, 'uploads/products/product_69804f8d600c8.png', 1, 1, 'active', '2026-02-02 14:17:33'),
(58, 51, 'uploads/products/product_69815f6404014.jpg', 1, 0, 'active', '2026-02-03 09:37:24'),
(59, 50, 'uploads/products/product_6981676d9788b.png', 0, 0, 'active', '2026-02-03 10:11:41'),
(60, 57, 'uploads/products/product_69819be590a3e.jpg', 1, 0, 'active', '2026-02-03 13:55:33'),
(61, 58, 'uploads/products/product_6981aa35c1a2b.jpg', 1, 0, 'active', '2026-02-03 14:56:37'),
(62, 59, 'uploads/products/product_6981adac2e435.png', 1, 0, 'active', '2026-02-03 15:11:24'),
(63, 59, 'uploads/products/product_6981b0218326f.jpg', 0, 0, 'active', '2026-02-03 15:21:53');

-- --------------------------------------------------------

--
-- Table structure for table `product_specifications`
--

DROP TABLE IF EXISTS `product_specifications`;
CREATE TABLE IF NOT EXISTS `product_specifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `spec_key` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `spec_value` text COLLATE utf8mb4_general_ci NOT NULL,
  `sort_order` int DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_specifications`
--

INSERT INTO `product_specifications` (`id`, `product_id`, `spec_key`, `spec_value`, `sort_order`) VALUES
(1, 57, 'deskripsi spek download', 'deskripsi spek download', 0),
(2, 58, 'downloads', 'downloads', 0),
(6, 59, 'downloads first', 'downloads first', 0);

-- --------------------------------------------------------

--
-- Table structure for table `product_types`
--

DROP TABLE IF EXISTS `product_types`;
CREATE TABLE IF NOT EXISTS `product_types` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `description` text,
  `slug` varchar(191) DEFAULT NULL,
  `position` int NOT NULL DEFAULT '1',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `product_types`
--

INSERT INTO `product_types` (`id`, `name`, `description`, `slug`, `position`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Printers', 'Industrial machinery and equipment', 'printers', 1, 'active', '2026-01-15 03:19:57', '2026-01-15 06:04:34'),
(2, 'Consumable', 'Consumable products and materials', 'consumable', 4, 'active', '2026-01-15 03:19:57', '2026-01-22 09:22:41'),
(3, 'Spare Parts', 'Replacement parts and components', 'spare-parts', 5, 'active', '2026-01-15 03:19:57', '2026-01-22 09:22:45'),
(4, 'Accessory', 'Accessories and add-ons', 'accessory', 3, 'active', '2026-01-15 03:19:57', '2026-01-22 09:22:37'),
(5, 'Scanners & Terminals', '', 'scanners-terminals', 2, 'active', '2026-01-22 08:08:54', '2026-01-22 09:22:17');

-- --------------------------------------------------------

--
-- Table structure for table `product_type_attributes`
--

DROP TABLE IF EXISTS `product_type_attributes`;
CREATE TABLE IF NOT EXISTS `product_type_attributes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_type_id` int NOT NULL,
  `attribute_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_type_attr` (`product_type_id`,`attribute_id`),
  KEY `attribute_id` (`attribute_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `product_type_attributes`
--

INSERT INTO `product_type_attributes` (`id`, `product_type_id`, `attribute_id`) VALUES
(7, 1, 1),
(8, 1, 2),
(9, 1, 3);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_product_brand` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_product_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_product_type` FOREIGN KEY (`product_type_id`) REFERENCES `product_types` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `product_attribute_values`
--
ALTER TABLE `product_attribute_values`
  ADD CONSTRAINT `product_attribute_values_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_attribute_values_ibfk_2` FOREIGN KEY (`attribute_id`) REFERENCES `attributes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_type_attributes`
--
ALTER TABLE `product_type_attributes`
  ADD CONSTRAINT `product_type_attributes_ibfk_1` FOREIGN KEY (`product_type_id`) REFERENCES `product_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_type_attributes_ibfk_2` FOREIGN KEY (`attribute_id`) REFERENCES `attributes` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
