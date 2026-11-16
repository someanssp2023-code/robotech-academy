-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 16, 2026 at 12:24 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sarona_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `banners`
--

CREATE TABLE `banners` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `subtitle` text DEFAULT NULL,
  `image_url` varchar(255) NOT NULL,
  `link_url` text DEFAULT NULL,
  `button_text` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `banners`
--

INSERT INTO `banners` (`id`, `title`, `subtitle`, `image_url`, `link_url`, `button_text`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(2, 'ESP32', 'Arduino makes it very easy to get started with the development of connected objects (IoT – Internet of Things) thanks to its Arduino Cloud platform. In this tutorial you will be able to create your first project based on an Arduino Nano ESP32 board. With its Bluetooth and Wifi connectivity, this card is indeed very well suited to the development of IoT for educational purposes.', '/storage/banners/anGeNpty4wtgnOlV0MLWgvM7rZoZWlX3pr32lDk5.png', NULL, NULL, 0, 0, '2026-08-04 21:19:52', '2026-10-18 08:29:32'),
(3, 'New 3pin IR', 'New 3pin IR Infrared Obstacle Avoidance Sensor Module For Arduino Diy Kit.jpg', '/storage/banners/Ja3CYGFAjeyZofbQ5mlRhmGUuS9YtEs1gjh1GIRg.jpg', NULL, 'Shop Now', 0, 0, '2026-08-24 01:31:52', '2026-10-18 08:29:30');

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `logo_url` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`id`, `name`, `slug`, `logo_url`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'AWS', 'apple', '/storage/brands/g3HhEwngjRfEbY1Lk2s8Aq26f2j4jbGYOzgqihaf.webp', 'Amazon Web Services (AWS IoT): Provides powerful cloud infrastructure and software core services to manage connected devices.', 1, '2026-08-04 22:10:10', '2026-09-09 23:05:04'),
(2, 'Siemens', 'siemens', '/storage/brands/UmwFuwU1ykw1NNIu4bQcUglDQ0BSQ0e5NFvVfnYU.png', 'Siemens: A top industrial specialist for manufacturing automation, smart factories, and operational tech execution.', 1, '2026-09-09 23:06:33', '2026-09-09 23:06:33');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`id`, `user_id`, `product_id`, `quantity`, `created_at`, `updated_at`) VALUES
(84, 27, 7, 1, '2026-10-25 13:35:34', '2026-10-25 13:35:34');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(2, 'CIoT', 'Consumer IoT (CIoT): Personal and residential devices focused on lifestyle, convenience, and ease of use.', 1, 0, '2026-09-09 23:07:23', '2026-09-09 23:07:23'),
(3, 'Infrastructure', 'Infrastructure IoT: Large-scale systems that manage complex regional or city-wide operations and public services.', 1, 0, '2026-09-09 23:07:50', '2026-09-09 23:07:50'),
(4, 'OS', 'The best in the world', 1, 0, '2026-10-14 12:02:53', '2026-10-14 12:02:53'),
(5, 'Industrial', 'Improving customer experiences and optimizing business operations.', 1, 0, '2026-10-19 07:04:14', '2026-10-19 07:04:14');

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'percent',
  `value` decimal(10,2) NOT NULL,
  `min_order` decimal(10,2) NOT NULL DEFAULT 0.00,
  `max_uses` int(11) DEFAULT NULL,
  `used_count` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `expires_at` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `coupons`
--

INSERT INTO `coupons` (`id`, `code`, `type`, `value`, `min_order`, `max_uses`, `used_count`, `is_active`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'WEEKLY001', 'percent', 25.00, 150.00, 10, 0, 1, '2026-09-30', '2026-08-24 01:33:19', '2026-09-12 19:34:21'),
(2, 'WEEKLY002', 'percent', 50.00, 100.00, 10, 0, 1, '2026-10-16', '2026-09-12 19:35:33', '2026-10-14 11:49:42');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2026_10_22_053343_create_notifications_table', 1),
(2, '2026_10_22_070000_create_settings_table', 2),
(3, '2026_10_22_070001_add_tax_to_orders_table', 3),
(4, '2026_03_09_173346_create_users_table', 1),
(5, '2026_03_09_173346_create_users_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` varchar(255) NOT NULL,
  `icon` varchar(255) NOT NULL DEFAULT '?',
  `url` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `for_admin` tinyint(1) NOT NULL DEFAULT 1,
  `for_user` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `type`, `title`, `message`, `icon`, `url`, `user_id`, `is_read`, `for_admin`, `for_user`, `created_at`, `updated_at`) VALUES
(1, 'order', 'New Order #63', 'Sokly placed an order — $20.00', '🛒', 'http://127.0.0.1:8000/admin/orders/63', NULL, 1, 1, 0, '2026-10-21 22:37:24', '2026-10-21 22:37:50'),
(2, 'order', 'Order Confirmed! 🎉', 'Your order #63 has been placed successfully.', '🛒', 'http://127.0.0.1:8000/my-orders/63', 27, 1, 0, 1, '2026-10-21 22:37:24', '2026-10-21 22:40:18'),
(3, 'order', 'Order #63 ⚙️', 'Your order status has been updated to: Processing', '🛒', 'http://127.0.0.1:8000/my-orders/63', 27, 1, 0, 1, '2026-10-21 22:37:58', '2026-10-21 22:38:15'),
(4, 'order', 'Order #62 ✅', 'Your order status has been updated to: Delivered', '🛒', 'http://127.0.0.1:8000/my-orders/62', 27, 0, 0, 1, '2026-10-21 22:41:14', '2026-10-21 22:41:14'),
(5, 'stock', 'Low Stock Warning ⚠️', '\'Mini fast charging Model\' stock is low: only 3 units remaining.', '📦', 'http://127.0.0.1:8000/admin/stock', NULL, 1, 1, 0, '2026-10-21 22:50:15', '2026-10-22 21:25:34'),
(6, 'stock', 'Low Stock Warning ⚠️', '\'Internal 12V 3 Pin CPU\' stock is low: only 4 units remaining.', '📦', 'http://127.0.0.1:8000/admin/stock', NULL, 1, 1, 0, '2026-10-21 22:50:57', '2026-10-22 21:25:34'),
(7, 'order', 'New Order #64', 'Sokly placed an order — $11.00', '🛒', 'http://127.0.0.1:8000/admin/orders/64', NULL, 1, 1, 0, '2026-10-22 08:15:32', '2026-10-22 21:25:34'),
(8, 'order', 'Order Confirmed! 🎉', 'Your order #64 has been placed successfully.', '🛒', 'http://127.0.0.1:8000/my-orders/64', 27, 0, 0, 1, '2026-10-22 08:15:32', '2026-10-22 08:15:32'),
(9, 'order', 'New Order #65', 'Sokly placed an order — $16.50', '🛒', 'http://127.0.0.1:8000/admin/orders/65', NULL, 1, 1, 0, '2026-10-22 08:18:57', '2026-10-22 21:25:34'),
(10, 'order', 'Order Confirmed! 🎉', 'Your order #65 has been placed successfully.', '🛒', 'http://127.0.0.1:8000/my-orders/65', 27, 0, 0, 1, '2026-10-22 08:18:57', '2026-10-22 08:18:57'),
(11, 'order', 'Order #65 🚚', 'Your order status has been updated to: Shipped', '🛒', 'http://127.0.0.1:8000/my-orders/65', 27, 0, 0, 1, '2026-10-22 08:47:21', '2026-10-22 08:47:21'),
(12, 'order', 'New Order #66', 'Sokly placed an order — $6.60', '🛒', 'http://127.0.0.1:8000/admin/orders/66', NULL, 1, 1, 0, '2026-10-22 09:15:50', '2026-10-22 21:22:50'),
(13, 'order', 'Order Confirmed! 🎉', 'Your order #66 has been placed successfully.', '🛒', 'http://127.0.0.1:8000/my-orders/66', 27, 0, 0, 1, '2026-10-22 09:15:50', '2026-10-22 09:15:50'),
(14, 'order', 'New Order #67', 'Sokly placed an order — $26.40', '🛒', 'http://127.0.0.1:8000/admin/orders/67', NULL, 0, 1, 0, '2026-10-22 22:17:06', '2026-10-22 22:17:06'),
(15, 'order', 'Order Confirmed! 🎉', 'Your order #67 has been placed successfully.', '🛒', 'http://127.0.0.1:8000/my-orders/67', 27, 0, 0, 1, '2026-10-22 22:17:06', '2026-10-22 22:17:06'),
(16, 'order', 'Order #67 ✅', 'Your order status has been updated to: Delivered', '🛒', 'http://127.0.0.1:8000/my-orders/67', 27, 0, 0, 1, '2026-10-22 22:17:45', '2026-10-22 22:17:45'),
(17, 'order', 'New Order #68', 'Sokly placed an order — $27.50', '🛒', 'http://127.0.0.1:8000/admin/orders/68', NULL, 0, 1, 0, '2026-10-22 23:28:21', '2026-10-22 23:28:21'),
(18, 'order', 'Order Confirmed! 🎉', 'Your order #68 has been placed successfully.', '🛒', 'http://127.0.0.1:8000/my-orders/68', 27, 0, 0, 1, '2026-10-22 23:28:21', '2026-10-22 23:28:21'),
(19, 'stock', 'Stock Restocked 📦', '\'Mini fast charging Model\' restocked: 2 → 52 units (+50)', '📦', 'http://127.0.0.1:8000/admin/stock', NULL, 0, 1, 0, '2026-10-23 12:17:27', '2026-10-23 12:17:27'),
(20, 'stock', 'Stock Restocked 📦', '\'Internal 12V 3 Pin CPU\' restocked: 3 → 53 units (+50)', '📦', 'http://127.0.0.1:8000/admin/stock', NULL, 0, 1, 0, '2026-10-23 12:17:40', '2026-10-23 12:17:40'),
(21, 'order', 'New Order #69', 'Sokly placed an order — $13.20', '🛒', 'http://127.0.0.1:8000/admin/orders/69', NULL, 0, 1, 0, '2026-10-23 13:13:35', '2026-10-23 13:13:35'),
(22, 'order', 'Order Confirmed! 🎉', 'Your order #69 has been placed successfully.', '🛒', 'http://127.0.0.1:8000/my-orders/69', 27, 0, 0, 1, '2026-10-23 13:13:35', '2026-10-23 13:13:35'),
(23, 'order', 'Order #69 ✅', 'Your order status has been updated to: Delivered', '🛒', 'http://127.0.0.1:8000/my-orders/69', 27, 0, 0, 1, '2026-10-23 13:32:32', '2026-10-23 13:32:32'),
(24, 'order', 'New Order #70', 'Sokly placed an order — $82.50', '🛒', 'http://127.0.0.1:8000/admin/orders/70', NULL, 1, 1, 0, '2026-10-23 13:55:34', '2026-10-23 17:04:06'),
(25, 'order', 'Order Confirmed! 🎉', 'Your order #70 has been placed successfully.', '🛒', 'http://127.0.0.1:8000/my-orders/70', 27, 0, 0, 1, '2026-10-23 13:55:34', '2026-10-23 13:55:34'),
(26, 'order', 'Order #70 🚚', 'Your order status has been updated to: Shipped', '🛒', 'http://127.0.0.1:8000/my-orders/70', 27, 0, 0, 1, '2026-10-23 13:56:22', '2026-10-23 13:56:22'),
(27, 'order', 'New Order #71', 'Sokly placed an order — $13.20', '🛒', 'http://127.0.0.1:8000/admin/orders/71', NULL, 0, 1, 0, '2026-10-23 17:08:10', '2026-10-23 17:08:10'),
(28, 'order', 'Order Confirmed! 🎉', 'Your order #71 has been placed successfully.', '🛒', 'http://127.0.0.1:8000/my-orders/71', 27, 0, 0, 1, '2026-10-23 17:08:10', '2026-10-23 17:08:10'),
(29, 'order', 'Order #71 ✅', 'Your order status has been updated to: Delivered', '🛒', 'http://127.0.0.1:8000/my-orders/71', 27, 0, 0, 1, '2026-10-23 17:31:47', '2026-10-23 17:31:47');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `coupon_id` bigint(20) UNSIGNED DEFAULT NULL,
  `order_number` varchar(255) DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `discount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `shipping_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tax_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL,
  `notes` text DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Pending',
  `shipping_address` text NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `payment_method` varchar(255) NOT NULL DEFAULT 'COD',
  `payment_status` varchar(255) NOT NULL DEFAULT 'Unpaid',
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `coupon_id`, `order_number`, `subtotal`, `discount`, `shipping_fee`, `tax_rate`, `tax_amount`, `total_amount`, `notes`, `status`, `shipping_address`, `phone`, `payment_method`, `payment_status`, `note`, `created_at`, `updated_at`) VALUES
(69, 27, NULL, 'ORD-20261023-0001', 12.00, 0.00, 0.00, 10.00, 1.20, 13.20, NULL, 'Delivered', 'ZSDCAXZ', '0977096515', 'COD', 'Paid', NULL, '2026-10-23 13:13:23', '2026-10-23 13:32:32'),
(70, 27, NULL, 'ORD-20261023-0070', 75.00, 0.00, 0.00, 10.00, 7.50, 82.50, NULL, 'Shipped', 'SR', '0977096515', 'COD', 'Paid', NULL, '2026-10-23 13:55:26', '2026-10-23 13:56:22'),
(71, 27, NULL, 'ORD-20261024-0071', 12.00, 0.00, 0.00, 10.00, 1.20, 13.20, NULL, 'Delivered', 'SR', '0977096515', 'COD', 'Unpaid', NULL, '2026-10-23 17:08:02', '2026-10-23 17:31:47');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_image` varchar(255) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `product_image`, `quantity`, `unit_price`, `subtotal`, `created_at`, `updated_at`) VALUES
(69, 69, 6, 'Ultrasonic Sensor', NULL, 1, 12.00, 12.00, '2026-10-23 13:13:23', '2026-10-23 13:13:23'),
(70, 70, 7, 'Hard Disc', NULL, 1, 20.00, 20.00, '2026-10-23 13:55:27', '2026-10-23 13:55:27'),
(71, 70, 5, '4 X SG90 Mini', NULL, 1, 9.00, 9.00, '2026-10-23 13:55:27', '2026-10-23 13:55:27'),
(72, 70, 9, 'Way To Control Servo Motor', NULL, 1, 15.00, 15.00, '2026-10-23 13:55:27', '2026-10-23 13:55:27'),
(73, 70, 12, 'Internal 12V 3 Pin CPU', NULL, 1, 25.00, 25.00, '2026-10-23 13:55:27', '2026-10-23 13:55:27'),
(74, 70, 8, 'PC2-5300', NULL, 1, 6.00, 6.00, '2026-10-23 13:55:27', '2026-10-23 13:55:27'),
(75, 71, 6, 'Ultrasonic Sensor', NULL, 1, 12.00, 12.00, '2026-10-23 17:08:02', '2026-10-23 17:08:02');

-- --------------------------------------------------------

--
-- Table structure for table `otp_codes`
--

CREATE TABLE `otp_codes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `code` varchar(6) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_used` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `otp_codes`
--

INSERT INTO `otp_codes` (`id`, `email`, `code`, `expires_at`, `is_used`, `created_at`, `updated_at`) VALUES
(10, 'mizternet@gmail.com', '151646', '2026-09-14 11:47:07', 0, '2026-09-14 11:37:07', '2026-09-14 11:37:07'),
(11, 'mizternex@gmail.com', '719719', '2026-09-14 11:38:31', 1, '2026-09-14 11:38:00', '2026-09-14 11:38:31'),
(12, 'seha.pepy@gmail.com', '977574', '2026-10-02 04:31:01', 1, '2026-10-02 04:30:29', '2026-10-02 04:31:01'),
(14, 'meanluyot5@gmail.com', '894277', '2026-10-14 13:03:56', 1, '2026-10-14 13:03:24', '2026-10-14 13:03:56'),
(15, 'hakheath9@gmail.com', '496435', '2026-10-19 06:50:00', 1, '2026-10-19 06:49:19', '2026-10-19 06:50:00'),
(16, 'hakheathcisco@gmail.com', '946723', '2026-10-19 06:52:51', 1, '2026-10-19 06:52:05', '2026-10-19 06:52:51'),
(17, 'somean.ssp2023@gmail.com', '781956', '2026-10-19 09:40:52', 1, '2026-10-19 09:40:29', '2026-10-19 09:40:52');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `method` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `order_id`, `user_id`, `transaction_id`, `amount`, `method`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(68, 69, 27, NULL, 13.20, 'COD', 'Completed', NULL, '2026-10-23 13:13:23', '2026-10-23 13:32:32'),
(69, 70, 27, NULL, 82.50, 'COD', 'Completed', NULL, '2026-10-23 13:55:27', '2026-10-23 13:56:22'),
(70, 71, 27, NULL, 13.20, 'COD', 'Pending', NULL, '2026-10-23 17:08:02', '2026-10-23 17:08:02');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `brand_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `short_description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `sale_price` decimal(10,2) DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `image_url` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `views` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `brand_id`, `name`, `slug`, `description`, `short_description`, `price`, `sale_price`, `stock`, `image_url`, `is_active`, `is_featured`, `views`, `created_at`, `updated_at`) VALUES
(5, 2, NULL, '4 X SG90 Mini', '4-x-sg90-mini-5', '4 X SG90 Mini Gear Micro 9g Analog Servo Check', '4 X SG90 Mini Gear Micro 9g Analog Servo Check', 6.00, 9.00, 99, '/storage/products/Ybq1NJbq1uk256X5Fk4SekAbfnKjDBitUbqm3WJn.jpg', 1, 1, 25, '2026-09-09 23:09:08', '2026-10-23 13:55:27'),
(6, 3, NULL, 'Ultrasonic Sensor', 'ultrasonic-sensor-6', 'Distance Measurement, Arduino Ultrasonic Sensor Code, Simulation With LCD', 'Distance Measurement, Arduino Ultrasonic Sensor Code, Simulation With LCD', 8.00, 12.00, 112, '/storage/products/yb7Xmidrp2s1GPyi4ZlIazruze2IbLmyhw6lio0p.jpg', 1, 1, 25, '2026-09-09 23:10:05', '2026-10-23 17:08:02'),
(7, 2, NULL, 'Hard Disc', 'hard-disc-7', 'Hard Disc', 'Hard Disc', 15.00, 20.00, 58, '/storage/products/CEGY6jwmzyERNdaY8e8mUnQBXi77735hLyUCL3ci.jpg', 1, 1, 60, '2026-09-09 23:10:42', '2026-10-25 13:35:34'),
(8, 3, NULL, 'PC2-5300', 'pc2-5300-8', 'PC2-5300 DDR2 677MHZ Laptop memory', 'PC2-5300 DDR2 677MHZ Laptop memory', 4.00, 6.00, 105, '/storage/products/cdJnWYhutwjoyKXifRBJGTARWR2fnP2Jp917FgkA.jpg', 1, 1, 33, '2026-09-09 23:11:23', '2026-10-23 13:55:27'),
(9, 3, NULL, 'Way To Control Servo Motor', 'way-to-control-servo-motor-9', 'Super Easy Way To Control Servo Motor With Arduino', 'Super Easy Way To Control Servo Motor', 12.00, 15.00, 1008, '/storage/products/YTA3GovslD4ToP9mbhqK9gdiY0HARVKxIrPGsRcg.jpg', 1, 1, 47, '2026-09-09 23:12:45', '2026-10-23 13:55:27'),
(11, 4, NULL, 'Sensor LD', 'sensor-ld-11', 'A sensor is an electronic or physical device that detects changes in its environment (such as light, heat, motion, or pressure) and converts that stimulus into a measurable electrical or optical', 'A sensor is an electronic or physical', 20.00, 25.00, 101, '/storage/products/8LFClaQincPaEJe2LvjbDCypgNHh128XqQpUe0FX.jpg', 1, 1, 0, '2026-10-14 12:07:26', '2026-10-21 22:40:40'),
(12, 5, NULL, 'Internal 12V 3 Pin CPU', 'internal-12v-3-pin-cpu-12', 'Internal 12V 3 Pin CPU Fan Cooler Fan for PC 70x70x15mm - AliExpress 7', 'Internal 12V 3 Pin CPU', 12.00, 25.00, 52, '/storage/products/rX2dOFisoJtp84GA0AVBFO4Z5BwSDnRDKy17LBpR.jpg', 1, 1, 5, '2026-10-19 07:05:37', '2026-10-23 13:55:27'),
(13, 5, NULL, 'Mini fast charging Model', 'mini-fast-charging-model-13', 'Mini fast charging Model 22.5W Charging Treasure Two-way Fast-harging Mobile Power Module Circuit Board DIY Motherboard Sets - AliExpress 142.jpg', 'Mini fast charging Model', 5.00, 10.00, 52, '/storage/products/VX3xPWJLpvq6gR94zdKlh4MlOpJmHCdOLbIkWvKU.jpg', 1, 1, 6, '2026-10-19 07:06:54', '2026-10-23 17:07:06');

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `rating` tinyint(4) NOT NULL DEFAULT 5,
  `comment` text DEFAULT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES
(1, 'tax_rate', '10', '2026-10-21 23:41:40', '2026-10-21 23:41:40'),
(2, 'tax_on_shipping', '1', '2026-10-22 00:06:39', '2026-10-22 22:15:17'),
(3, 'prices_include_tax', '1', '2026-10-22 00:06:39', '2026-10-22 22:15:17'),
(8, 'currency', 'USD', '2026-10-22 00:07:35', '2026-10-22 00:07:35'),
(9, 'shipping_type', 'fixed', '2026-10-22 00:09:52', '2026-10-22 00:09:52'),
(10, 'shipping_fee', '1.5', '2026-10-22 00:09:52', '2026-10-22 00:09:52'),
(11, 'free_shipping_min', NULL, '2026-10-22 00:09:52', '2026-10-22 00:09:52');

-- --------------------------------------------------------

--
-- Table structure for table `stock_histories`
--

CREATE TABLE `stock_histories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity_added` int(11) NOT NULL,
  `adj_type` varchar(20) NOT NULL DEFAULT 'add' COMMENT 'add or subtract',
  `reason` varchar(50) DEFAULT NULL COMMENT 'restock, damaged, returned, correction, expired, other',
  `stock_before` int(11) NOT NULL,
  `stock_after` int(11) NOT NULL,
  `note` varchar(255) DEFAULT NULL,
  `added_by_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stock_histories`
--

INSERT INTO `stock_histories` (`id`, `product_id`, `quantity_added`, `adj_type`, `reason`, `stock_before`, `stock_after`, `note`, `added_by_user_id`, `created_at`, `updated_at`) VALUES
(2, 5, 29, 'add', NULL, 97, 126, NULL, 8, '2026-09-11 21:08:25', '2026-09-11 21:08:25');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'Customer',
  `phone` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_main_admin` tinyint(1) NOT NULL DEFAULT 0,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `phone`, `address`, `is_active`, `is_main_admin`, `email_verified_at`, `created_at`, `updated_at`) VALUES
(8, 'Main Admin', 'mainadmin@gmail.com', '$2y$12$tSGgpMvrj.71PQ3qhLuxN.tG9WY51DjH8otCUFW91.AnxJq7fWk6a', 'Admin', '0974906226', NULL, 1, 1, NULL, '2026-09-02 05:44:35', '2026-10-22 09:13:36'),
(26, 'Hak Heath', 'hakheath9@gmail.com', '$2y$12$enovLm7y3sc6ywHKg6XP.uSoQSYARX/QubqgzVuDNaNew1MiLsgfa', 'Staff', '0974906226', NULL, 1, 0, NULL, '2026-10-19 06:50:01', '2026-10-19 08:13:10'),
(27, 'Sokly', 'hakheathcisco@gmail.com', '$2y$12$1X.irPc7scRMlwBDk/0sdugbETjxgj6bv1COHZocx//Eb6ZsLueja', 'Customer', '0977096515', NULL, 1, 0, NULL, '2026-10-19 06:52:52', '2026-10-19 07:12:44'),
(28, 'Somean', 'somean.ssp2023@gmail.com', '$2y$12$RShM3E49QjCO/po8zKL5jee/yEaMWOkTM1RQZofl8z8aZNPumnqGK', 'Customer', '0977096515', NULL, 1, 0, NULL, '2026-10-19 09:40:53', '2026-10-19 09:40:53');

-- --------------------------------------------------------

--
-- Table structure for table `wishlists`
--

CREATE TABLE `wishlists` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wishlists`
--

INSERT INTO `wishlists` (`id`, `user_id`, `product_id`, `created_at`, `updated_at`) VALUES
(7, 27, 13, '2026-10-23 17:07:05', '2026-10-23 17:07:05');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `banners`
--
ALTER TABLE `banners`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `brands_slug_unique` (`slug`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cart_items_user_id_product_id_unique` (`user_id`,`product_id`),
  ADD KEY `cart_items_product_id_foreign` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `coupons_code_unique` (`code`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `orders_order_number_unique` (`order_number`),
  ADD KEY `orders_user_id_foreign` (`user_id`),
  ADD KEY `orders_coupon_id_foreign` (`coupon_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_items_order_id_foreign` (`order_id`),
  ADD KEY `order_items_product_id_foreign` (`product_id`);

--
-- Indexes for table `otp_codes`
--
ALTER TABLE `otp_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `otp_codes_email_index` (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payments_transaction_id_unique` (`transaction_id`),
  ADD KEY `payments_order_id_foreign` (`order_id`),
  ADD KEY `payments_user_id_foreign` (`user_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `products_category_id_foreign` (`category_id`),
  ADD KEY `products_brand_id_foreign` (`brand_id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_images_product_id_foreign` (`product_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reviews_user_id_foreign` (`user_id`),
  ADD KEY `reviews_product_id_foreign` (`product_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_key_unique` (`key`);

--
-- Indexes for table `stock_histories`
--
ALTER TABLE `stock_histories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_histories_product_id_foreign` (`product_id`),
  ADD KEY `stock_histories_added_by_user_id_foreign` (`added_by_user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `wishlists_user_id_product_id_unique` (`user_id`,`product_id`),
  ADD KEY `wishlists_product_id_foreign` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `banners`
--
ALTER TABLE `banners`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `cache`
--
ALTER TABLE `cache`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `otp_codes`
--
ALTER TABLE `otp_codes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `stock_histories`
--
ALTER TABLE `stock_histories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `wishlists`
--
ALTER TABLE `wishlists`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_coupon_id_foreign` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stock_histories`
--
ALTER TABLE `stock_histories`
  ADD CONSTRAINT `stock_histories_added_by_user_id_foreign` FOREIGN KEY (`added_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `stock_histories_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD CONSTRAINT `wishlists_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlists_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
