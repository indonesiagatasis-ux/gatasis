-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Feb 02, 2026 at 02:19 AM
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
(1, 'Barcode Printers', 'barcode-printers', 'cat_6970a0067e4d2.png', NULL, 'active', '2026-01-15 02:48:24', '2026-01-22 08:49:12', 0),
(2, 'Mobile Terminals ', 'mobile-terminals-', 'cat_6970a01789a12.png', NULL, 'active', '2026-01-15 02:48:24', '2026-01-22 08:49:12', 2),
(3, 'Software Development', 'software-development', 'cat_6970a026e0ae2.jpg', NULL, 'active', '2026-01-15 02:48:24', '2026-01-22 08:54:27', 6),
(4, 'Mobile Printers', 'mobile-printers', NULL, 1, 'active', '2026-01-15 08:40:19', '2026-01-22 08:51:02', 3),
(5, 'Industrial Printers', 'industrial-printers', NULL, 1, 'active', '2026-01-15 08:56:32', '2026-01-22 08:49:44', 2),
(6, 'Desktop Printers', 'desktop-printers', NULL, 1, 'active', '2026-01-15 09:06:21', '2026-01-22 08:49:35', 1),
(10, 'Barcode Scanners', 'barcode-scanners', NULL, NULL, 'active', '2026-01-21 09:52:48', '2026-01-22 08:54:27', 3),
(11, 'Marking', 'marking', NULL, NULL, 'active', '2026-01-21 09:52:59', '2026-01-22 08:54:27', 4),
(12, 'Handheld Barcode Scanner', 'handheld-barcode-scanner', NULL, 10, 'active', '2026-01-22 01:35:12', '2026-01-22 01:35:12', 1),
(13, 'Rugged Smartphones', 'rugged-smartphones', NULL, 2, 'active', '2026-01-22 08:41:00', '2026-01-22 08:52:45', 1),
(14, 'Handheld Terminals', 'handheld-terminals', NULL, 2, 'active', '2026-01-22 08:41:30', '2026-01-22 08:52:57', 2),
(15, 'Enterprise Tablets', 'enterprise-tablets', NULL, 2, 'active', '2026-01-22 08:41:42', '2026-01-22 08:53:24', 3),
(18, 'Accessories', 'accessories', NULL, 1, 'active', '2026-01-22 08:54:14', '2026-01-22 08:54:14', 4),
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
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `product_type_id`, `name`, `slug`, `price`, `status`, `created_at`, `updated_at`, `category_id`, `brand_id`, `main_image`) VALUES
(1, 1, 'TSC TE200', 'tsc-te200', 0.00, 'active', '2026-01-15 02:57:38', '2026-01-22 03:18:14', 1, 1, 'uploads/products/product_697196f6e2e61.png'),
(22, 1, 'TSC TE210', 'tsc-te210', 0.00, 'active', '2026-01-15 07:13:38', '2026-01-22 03:17:59', 1, 1, 'uploads/products/product_697196e7a7462.png'),
(23, 1, 'TSC TDP-225W', 'tsc-tdp-225w', 0.00, 'active', '2026-01-22 03:28:41', '2026-01-22 08:35:25', 4, 1, 'uploads/products/product_6971996973064.png'),
(24, 5, 'Unitech MS926', 'unitech-ms926', 0.00, 'active', '2026-01-22 03:33:21', '2026-01-22 08:10:37', 10, 2, 'uploads/products/product_69719a81e99f8.jpg');

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
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(10, 24, 'uploads/products/product_69719a81ea531.png', 0, 4, 'active', '2026-01-22 10:33:21');

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
