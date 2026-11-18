-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 17, 2026 at 12:18 PM
-- Server version: 8.0.31
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `event_portal`
--

-- --------------------------------------------------------

--
-- Table structure for table `approvals`
--

DROP TABLE IF EXISTS `approvals`;
CREATE TABLE IF NOT EXISTS `approvals` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `request_id` bigint UNSIGNED NOT NULL,
  `checkpoint` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `approvals_user_id_foreign` (`user_id`),
  KEY `approvals_request_id_checkpoint_index` (`request_id`,`checkpoint`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `approvals`
--

INSERT INTO `approvals` (`id`, `request_id`, `checkpoint`, `action`, `remarks`, `user_id`, `created_at`, `updated_at`) VALUES
(3, 1, 'initial', 'Approved', NULL, 1, '2026-11-16 01:19:43', '2026-11-16 01:19:43'),
(4, 2, 'initial', 'Approved', NULL, 1, '2026-11-16 01:26:05', '2026-11-16 01:26:05'),
(19, 39, 'initial', 'Approved', NULL, 1, '2026-11-17 02:59:45', '2026-11-17 02:59:45'),
(23, 64, 'initial', 'Approved', 'tes', 1, '2026-11-17 05:07:38', '2026-11-17 05:07:38');

-- --------------------------------------------------------

--
-- Table structure for table `branding_materials`
--

DROP TABLE IF EXISTS `branding_materials`;
CREATE TABLE IF NOT EXISTS `branding_materials` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('ACTIVE','INACTIVE') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVE',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `branding_materials_name_unique` (`name`),
  KEY `branding_materials_created_by_foreign` (`created_by`),
  KEY `branding_materials_updated_by_foreign` (`updated_by`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `branding_materials`
--

INSERT INTO `branding_materials` (`id`, `name`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'Backdrop', 'ACTIVE', 1, 1, '2026-11-13 04:58:22', '2026-11-13 04:58:22'),
(2, 'Banners', 'ACTIVE', 1, 1, '2026-11-13 04:58:22', '2026-11-13 04:58:22'),
(3, 'Stage Branding', 'ACTIVE', 1, 1, '2026-11-13 04:58:22', '2026-11-13 04:58:22'),
(4, 'Standees', 'ACTIVE', 1, 1, '2026-11-13 04:58:23', '2026-11-13 04:58:23'),
(5, 'Digital Creatives', 'ACTIVE', 1, 1, '2026-11-13 04:58:23', '2026-11-13 04:58:23'),
(6, 'Welcome Arch', 'ACTIVE', 1, 1, '2026-11-13 04:58:23', '2026-11-13 04:58:23');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-spatie.permission.cache', 'a:3:{s:5:\"alias\";a:8:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:18:\"permissiongroup_id\";s:1:\"c\";s:4:\"name\";s:1:\"d\";s:10:\"guard_name\";s:1:\"e\";s:7:\"methods\";s:1:\"r\";s:5:\"roles\";s:1:\"j\";s:10:\"created_by\";s:1:\"k\";s:10:\"updated_by\";}s:11:\"permissions\";a:81:{i:0;a:6:{s:1:\"a\";i:1;s:1:\"b\";i:1;s:1:\"c\";s:14:\"dashboard-view\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:9:\"[\"index\"]\";s:1:\"r\";a:11:{i:0;i:1;i:1;i:3;i:2;i:4;i:3;i:5;i:4;i:6;i:5;i:8;i:6;i:9;i:7;i:10;i:8;i:11;i:9;i:12;i:10;i:13;}}i:1;a:6:{s:1:\"a\";i:2;s:1:\"b\";i:2;s:1:\"c\";s:9:\"role-view\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:30:\"[\"index\",\"data\",\"list\",\"show\"]\";s:1:\"r\";a:1:{i:0;i:1;}}i:2;a:6:{s:1:\"a\";i:3;s:1:\"b\";i:2;s:1:\"c\";s:10:\"role-store\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:18:\"[\"create\",\"store\"]\";s:1:\"r\";a:1:{i:0;i:1;}}i:3;a:6:{s:1:\"a\";i:4;s:1:\"b\";i:2;s:1:\"c\";s:11:\"role-update\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:32:\"[\"edit\",\"update\",\"changeStatus\"]\";s:1:\"r\";a:1:{i:0;i:1;}}i:4;a:6:{s:1:\"a\";i:5;s:1:\"b\";i:2;s:1:\"c\";s:15:\"role-permission\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:39:\"[\"permissionsShow\",\"permissionsUpdate\"]\";s:1:\"r\";a:1:{i:0;i:1;}}i:5;a:6:{s:1:\"a\";i:6;s:1:\"b\";i:3;s:1:\"c\";s:15:\"department-view\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:30:\"[\"index\",\"data\",\"list\",\"show\"]\";s:1:\"r\";a:1:{i:0;i:1;}}i:6;a:6:{s:1:\"a\";i:7;s:1:\"b\";i:3;s:1:\"c\";s:16:\"department-store\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:18:\"[\"create\",\"store\"]\";s:1:\"r\";a:1:{i:0;i:1;}}i:7;a:6:{s:1:\"a\";i:8;s:1:\"b\";i:3;s:1:\"c\";s:17:\"department-update\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:32:\"[\"edit\",\"update\",\"changeStatus\"]\";s:1:\"r\";a:1:{i:0;i:1;}}i:8;a:6:{s:1:\"a\";i:9;s:1:\"b\";i:3;s:1:\"c\";s:17:\"department-delete\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:11:\"[\"destroy\"]\";s:1:\"r\";a:1:{i:0;i:1;}}i:9;a:6:{s:1:\"a\";i:10;s:1:\"b\";i:4;s:1:\"c\";s:9:\"zone-view\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:30:\"[\"index\",\"data\",\"list\",\"show\"]\";s:1:\"r\";a:1:{i:0;i:1;}}i:10;a:6:{s:1:\"a\";i:11;s:1:\"b\";i:4;s:1:\"c\";s:10:\"zone-store\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:18:\"[\"create\",\"store\"]\";s:1:\"r\";a:1:{i:0;i:1;}}i:11;a:6:{s:1:\"a\";i:12;s:1:\"b\";i:4;s:1:\"c\";s:11:\"zone-update\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:32:\"[\"edit\",\"update\",\"changeStatus\"]\";s:1:\"r\";a:1:{i:0;i:1;}}i:12;a:6:{s:1:\"a\";i:13;s:1:\"b\";i:4;s:1:\"c\";s:11:\"zone-delete\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:11:\"[\"destroy\"]\";s:1:\"r\";a:1:{i:0;i:1;}}i:13;a:6:{s:1:\"a\";i:14;s:1:\"b\";i:5;s:1:\"c\";s:10:\"event-view\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:30:\"[\"index\",\"data\",\"list\",\"show\"]\";s:1:\"r\";a:1:{i:0;i:1;}}i:14;a:6:{s:1:\"a\";i:15;s:1:\"b\";i:5;s:1:\"c\";s:11:\"event-store\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:18:\"[\"create\",\"store\"]\";s:1:\"r\";a:1:{i:0;i:1;}}i:15;a:6:{s:1:\"a\";i:16;s:1:\"b\";i:5;s:1:\"c\";s:12:\"event-update\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:32:\"[\"edit\",\"update\",\"changeStatus\"]\";s:1:\"r\";a:1:{i:0;i:1;}}i:16;a:6:{s:1:\"a\";i:17;s:1:\"b\";i:5;s:1:\"c\";s:12:\"event-delete\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:11:\"[\"destroy\"]\";s:1:\"r\";a:1:{i:0;i:1;}}i:17;a:6:{s:1:\"a\";i:18;s:1:\"b\";i:6;s:1:\"c\";s:14:\"eventtype-view\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:30:\"[\"index\",\"data\",\"list\",\"show\"]\";s:1:\"r\";a:1:{i:0;i:1;}}i:18;a:6:{s:1:\"a\";i:19;s:1:\"b\";i:6;s:1:\"c\";s:15:\"eventtype-store\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:18:\"[\"create\",\"store\"]\";s:1:\"r\";a:1:{i:0;i:1;}}i:19;a:6:{s:1:\"a\";i:20;s:1:\"b\";i:6;s:1:\"c\";s:16:\"eventtype-update\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:32:\"[\"edit\",\"update\",\"changeStatus\"]\";s:1:\"r\";a:1:{i:0;i:1;}}i:20;a:6:{s:1:\"a\";i:21;s:1:\"b\";i:6;s:1:\"c\";s:16:\"eventtype-delete\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:11:\"[\"destroy\"]\";s:1:\"r\";a:1:{i:0;i:1;}}i:21;a:6:{s:1:\"a\";i:22;s:1:\"b\";i:7;s:1:\"c\";s:13:\"giftitem-view\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:30:\"[\"index\",\"data\",\"list\",\"show\"]\";s:1:\"r\";a:1:{i:0;i:1;}}i:22;a:6:{s:1:\"a\";i:23;s:1:\"b\";i:7;s:1:\"c\";s:14:\"giftitem-store\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:18:\"[\"create\",\"store\"]\";s:1:\"r\";a:1:{i:0;i:1;}}i:23;a:6:{s:1:\"a\";i:24;s:1:\"b\";i:7;s:1:\"c\";s:15:\"giftitem-update\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:32:\"[\"edit\",\"update\",\"changeStatus\"]\";s:1:\"r\";a:1:{i:0;i:1;}}i:24;a:6:{s:1:\"a\";i:25;s:1:\"b\";i:7;s:1:\"c\";s:15:\"giftitem-delete\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:11:\"[\"destroy\"]\";s:1:\"r\";a:1:{i:0;i:1;}}i:25;a:6:{s:1:\"a\";i:26;s:1:\"b\";i:8;s:1:\"c\";s:12:\"popitem-view\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:30:\"[\"index\",\"data\",\"list\",\"show\"]\";s:1:\"r\";a:1:{i:0;i:1;}}i:26;a:6:{s:1:\"a\";i:27;s:1:\"b\";i:8;s:1:\"c\";s:13:\"popitem-store\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:18:\"[\"create\",\"store\"]\";s:1:\"r\";a:1:{i:0;i:1;}}i:27;a:6:{s:1:\"a\";i:28;s:1:\"b\";i:8;s:1:\"c\";s:14:\"popitem-update\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:32:\"[\"edit\",\"update\",\"changeStatus\"]\";s:1:\"r\";a:1:{i:0;i:1;}}i:28;a:6:{s:1:\"a\";i:29;s:1:\"b\";i:8;s:1:\"c\";s:14:\"popitem-delete\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:11:\"[\"destroy\"]\";s:1:\"r\";a:1:{i:0;i:1;}}i:29;a:6:{s:1:\"a\";i:30;s:1:\"b\";i:9;s:1:\"c\";s:18:\"productsample-view\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:30:\"[\"index\",\"data\",\"list\",\"show\"]\";s:1:\"r\";a:1:{i:0;i:1;}}i:30;a:6:{s:1:\"a\";i:31;s:1:\"b\";i:9;s:1:\"c\";s:19:\"productsample-store\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:18:\"[\"create\",\"store\"]\";s:1:\"r\";a:1:{i:0;i:1;}}i:31;a:6:{s:1:\"a\";i:32;s:1:\"b\";i:9;s:1:\"c\";s:20:\"productsample-update\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:32:\"[\"edit\",\"update\",\"changeStatus\"]\";s:1:\"r\";a:1:{i:0;i:1;}}i:32;a:6:{s:1:\"a\";i:33;s:1:\"b\";i:9;s:1:\"c\";s:20:\"productsample-delete\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:11:\"[\"destroy\"]\";s:1:\"r\";a:1:{i:0;i:1;}}i:33;a:6:{s:1:\"a\";i:34;s:1:\"b\";i:10;s:1:\"c\";s:18:\"catalogueitem-view\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:30:\"[\"index\",\"data\",\"list\",\"show\"]\";s:1:\"r\";a:1:{i:0;i:1;}}i:34;a:6:{s:1:\"a\";i:35;s:1:\"b\";i:10;s:1:\"c\";s:19:\"catalogueitem-store\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:18:\"[\"create\",\"store\"]\";s:1:\"r\";a:1:{i:0;i:1;}}i:35;a:6:{s:1:\"a\";i:36;s:1:\"b\";i:10;s:1:\"c\";s:20:\"catalogueitem-update\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:32:\"[\"edit\",\"update\",\"changeStatus\"]\";s:1:\"r\";a:1:{i:0;i:1;}}i:36;a:6:{s:1:\"a\";i:37;s:1:\"b\";i:10;s:1:\"c\";s:20:\"catalogueitem-delete\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:11:\"[\"destroy\"]\";s:1:\"r\";a:1:{i:0;i:1;}}i:37;a:6:{s:1:\"a\";i:38;s:1:\"b\";i:11;s:1:\"c\";s:27:\"sponsorshipdeliverable-view\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:30:\"[\"index\",\"data\",\"list\",\"show\"]\";s:1:\"r\";a:1:{i:0;i:1;}}i:38;a:6:{s:1:\"a\";i:39;s:1:\"b\";i:11;s:1:\"c\";s:28:\"sponsorshipdeliverable-store\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:18:\"[\"create\",\"store\"]\";s:1:\"r\";a:1:{i:0;i:1;}}i:39;a:6:{s:1:\"a\";i:40;s:1:\"b\";i:11;s:1:\"c\";s:29:\"sponsorshipdeliverable-update\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:32:\"[\"edit\",\"update\",\"changeStatus\"]\";s:1:\"r\";a:1:{i:0;i:1;}}i:40;a:6:{s:1:\"a\";i:41;s:1:\"b\";i:11;s:1:\"c\";s:29:\"sponsorshipdeliverable-delete\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:11:\"[\"destroy\"]\";s:1:\"r\";a:1:{i:0;i:1;}}i:41;a:6:{s:1:\"a\";i:42;s:1:\"b\";i:12;s:1:\"c\";s:21:\"brandingmaterial-view\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:30:\"[\"index\",\"data\",\"list\",\"show\"]\";s:1:\"r\";a:1:{i:0;i:1;}}i:42;a:6:{s:1:\"a\";i:43;s:1:\"b\";i:12;s:1:\"c\";s:22:\"brandingmaterial-store\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:18:\"[\"create\",\"store\"]\";s:1:\"r\";a:1:{i:0;i:1;}}i:43;a:6:{s:1:\"a\";i:44;s:1:\"b\";i:12;s:1:\"c\";s:23:\"brandingmaterial-update\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:32:\"[\"edit\",\"update\",\"changeStatus\"]\";s:1:\"r\";a:1:{i:0;i:1;}}i:44;a:6:{s:1:\"a\";i:45;s:1:\"b\";i:12;s:1:\"c\";s:23:\"brandingmaterial-delete\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:11:\"[\"destroy\"]\";s:1:\"r\";a:1:{i:0;i:1;}}i:45;a:6:{s:1:\"a\";i:46;s:1:\"b\";i:13;s:1:\"c\";s:18:\"guestcategory-view\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:30:\"[\"index\",\"data\",\"list\",\"show\"]\";s:1:\"r\";a:1:{i:0;i:1;}}i:46;a:6:{s:1:\"a\";i:47;s:1:\"b\";i:13;s:1:\"c\";s:19:\"guestcategory-store\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:18:\"[\"create\",\"store\"]\";s:1:\"r\";a:1:{i:0;i:1;}}i:47;a:6:{s:1:\"a\";i:48;s:1:\"b\";i:13;s:1:\"c\";s:20:\"guestcategory-update\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:32:\"[\"edit\",\"update\",\"changeStatus\"]\";s:1:\"r\";a:1:{i:0;i:1;}}i:48;a:6:{s:1:\"a\";i:49;s:1:\"b\";i:13;s:1:\"c\";s:20:\"guestcategory-delete\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:11:\"[\"destroy\"]\";s:1:\"r\";a:1:{i:0;i:1;}}i:49;a:6:{s:1:\"a\";i:50;s:1:\"b\";i:14;s:1:\"c\";s:10:\"hotel-view\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:30:\"[\"index\",\"data\",\"list\",\"show\"]\";s:1:\"r\";a:1:{i:0;i:1;}}i:50;a:6:{s:1:\"a\";i:51;s:1:\"b\";i:14;s:1:\"c\";s:11:\"hotel-store\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:18:\"[\"create\",\"store\"]\";s:1:\"r\";a:1:{i:0;i:1;}}i:51;a:6:{s:1:\"a\";i:52;s:1:\"b\";i:14;s:1:\"c\";s:12:\"hotel-update\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:32:\"[\"edit\",\"update\",\"changeStatus\"]\";s:1:\"r\";a:1:{i:0;i:1;}}i:52;a:6:{s:1:\"a\";i:53;s:1:\"b\";i:14;s:1:\"c\";s:12:\"hotel-delete\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:11:\"[\"destroy\"]\";s:1:\"r\";a:1:{i:0;i:1;}}i:53;a:6:{s:1:\"a\";i:54;s:1:\"b\";i:15;s:1:\"c\";s:21:\"meeteventrequest-view\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:34:\"[\"index\",\"data\",\"show\",\"document\"]\";s:1:\"r\";a:10:{i:0;i:1;i:1;i:3;i:2;i:4;i:3;i:5;i:4;i:6;i:5;i:8;i:6;i:9;i:7;i:11;i:8;i:12;i:9;i:13;}}i:54;a:6:{s:1:\"a\";i:55;s:1:\"b\";i:15;s:1:\"c\";s:22:\"meeteventrequest-store\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:18:\"[\"create\",\"store\"]\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:3;i:2;i:4;i:3;i:5;}}i:55;a:6:{s:1:\"a\";i:56;s:1:\"b\";i:15;s:1:\"c\";s:23:\"meeteventrequest-update\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:17:\"[\"edit\",\"update\"]\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:3;i:2;i:4;i:3;i:5;}}i:56;a:6:{s:1:\"a\";i:58;s:1:\"b\";i:16;s:1:\"c\";s:13:\"location-view\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:30:\"[\"index\",\"data\",\"list\",\"show\"]\";s:1:\"r\";a:1:{i:0;i:1;}}i:57;a:6:{s:1:\"a\";i:59;s:1:\"b\";i:16;s:1:\"c\";s:14:\"location-store\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:18:\"[\"create\",\"store\"]\";s:1:\"r\";a:1:{i:0;i:1;}}i:58;a:6:{s:1:\"a\";i:60;s:1:\"b\";i:16;s:1:\"c\";s:15:\"location-update\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:32:\"[\"edit\",\"update\",\"changeStatus\"]\";s:1:\"r\";a:1:{i:0;i:1;}}i:59;a:6:{s:1:\"a\";i:61;s:1:\"b\";i:16;s:1:\"c\";s:15:\"location-delete\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:11:\"[\"destroy\"]\";s:1:\"r\";a:1:{i:0;i:1;}}i:60;a:6:{s:1:\"a\";i:62;s:1:\"b\";i:17;s:1:\"c\";s:22:\"exhibitionrequest-view\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:34:\"[\"index\",\"data\",\"show\",\"document\"]\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:3;i:2;i:4;i:3;i:5;}}i:61;a:6:{s:1:\"a\";i:63;s:1:\"b\";i:17;s:1:\"c\";s:23:\"exhibitionrequest-store\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:18:\"[\"create\",\"store\"]\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:3;i:2;i:4;i:3;i:5;}}i:62;a:6:{s:1:\"a\";i:64;s:1:\"b\";i:17;s:1:\"c\";s:24:\"exhibitionrequest-update\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:17:\"[\"edit\",\"update\"]\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:3;i:2;i:4;i:3;i:5;}}i:63;a:6:{s:1:\"a\";i:66;s:1:\"b\";i:18;s:1:\"c\";s:24:\"presentationrequest-view\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:34:\"[\"index\",\"data\",\"show\",\"document\"]\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:3;i:2;i:4;i:3;i:5;}}i:64;a:6:{s:1:\"a\";i:67;s:1:\"b\";i:18;s:1:\"c\";s:25:\"presentationrequest-store\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:18:\"[\"create\",\"store\"]\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:3;i:2;i:4;i:3;i:5;}}i:65;a:6:{s:1:\"a\";i:68;s:1:\"b\";i:18;s:1:\"c\";s:26:\"presentationrequest-update\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:17:\"[\"edit\",\"update\"]\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:3;i:2;i:4;i:3;i:5;}}i:66;a:6:{s:1:\"a\";i:70;s:1:\"b\";i:19;s:1:\"c\";s:19:\"giftpoprequest-view\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:23:\"[\"index\",\"data\",\"show\"]\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:3;i:2;i:4;i:3;i:5;}}i:67;a:6:{s:1:\"a\";i:71;s:1:\"b\";i:19;s:1:\"c\";s:20:\"giftpoprequest-store\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:18:\"[\"create\",\"store\"]\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:3;i:2;i:4;i:3;i:5;}}i:68;a:6:{s:1:\"a\";i:72;s:1:\"b\";i:19;s:1:\"c\";s:21:\"giftpoprequest-update\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:17:\"[\"edit\",\"update\"]\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:3;i:2;i:4;i:3;i:5;}}i:69;a:6:{s:1:\"a\";i:74;s:1:\"b\";i:20;s:1:\"c\";s:20:\"brandingrequest-view\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:34:\"[\"index\",\"data\",\"show\",\"document\"]\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:5;}}i:70;a:6:{s:1:\"a\";i:75;s:1:\"b\";i:20;s:1:\"c\";s:21:\"brandingrequest-store\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:18:\"[\"create\",\"store\"]\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:5;}}i:71;a:6:{s:1:\"a\";i:76;s:1:\"b\";i:20;s:1:\"c\";s:22:\"brandingrequest-update\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:17:\"[\"edit\",\"update\"]\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:5;}}i:72;a:6:{s:1:\"a\";i:78;s:1:\"b\";i:21;s:1:\"c\";s:28:\"experiencecentrerequest-view\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:34:\"[\"index\",\"data\",\"show\",\"document\"]\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:5;}}i:73;a:6:{s:1:\"a\";i:79;s:1:\"b\";i:21;s:1:\"c\";s:29:\"experiencecentrerequest-store\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:18:\"[\"create\",\"store\"]\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:5;}}i:74;a:6:{s:1:\"a\";i:80;s:1:\"b\";i:21;s:1:\"c\";s:30:\"experiencecentrerequest-update\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:17:\"[\"edit\",\"update\"]\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:5;}}i:75;a:6:{s:1:\"a\";i:83;s:1:\"b\";i:22;s:1:\"c\";s:15:\"approval-action\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:22:\"[\"approve\",\"sendBack\"]\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:6;i:2;i:8;}}i:76;a:6:{s:1:\"a\";i:85;s:1:\"b\";i:23;s:1:\"c\";s:19:\"meetplanning-update\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:18:\"[\"store\",\"update\"]\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:9;}}i:77;a:6:{s:1:\"a\";i:86;s:1:\"b\";i:23;s:1:\"c\";s:18:\"meetplanning-close\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:33:\"[\"eventReady\",\"complete\",\"close\"]\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:9;}}i:78;a:6:{s:1:\"a\";i:88;s:1:\"b\";i:24;s:1:\"c\";s:19:\"purchasetask-update\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:20:\"[\"store\",\"complete\"]\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:11;}}i:79;a:6:{s:1:\"a\";i:90;s:1:\"b\";i:25;s:1:\"c\";s:19:\"accountstask-update\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:20:\"[\"store\",\"complete\"]\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:12;}}i:80;a:6:{s:1:\"a\";i:92;s:1:\"b\";i:26;s:1:\"c\";s:20:\"logisticstask-update\";s:1:\"d\";s:3:\"web\";s:1:\"e\";s:19:\"[\"items\",\"sendOut\"]\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:13;}}}s:5:\"roles\";a:11:{i:0;a:5:{s:1:\"a\";i:1;s:1:\"c\";s:10:\"SuperAdmin\";s:1:\"d\";s:3:\"web\";s:1:\"j\";N;s:1:\"k\";N;}i:1;a:5:{s:1:\"a\";i:3;s:1:\"c\";s:13:\"Zonal Manager\";s:1:\"d\";s:3:\"web\";s:1:\"j\";N;s:1:\"k\";N;}i:2;a:5:{s:1:\"a\";i:4;s:1:\"c\";s:5:\"Sales\";s:1:\"d\";s:3:\"web\";s:1:\"j\";N;s:1:\"k\";N;}i:3;a:5:{s:1:\"a\";i:5;s:1:\"c\";s:3:\"HOD\";s:1:\"d\";s:3:\"web\";s:1:\"j\";N;s:1:\"k\";N;}i:4;a:5:{s:1:\"a\";i:6;s:1:\"c\";s:8:\"Director\";s:1:\"d\";s:3:\"web\";s:1:\"j\";N;s:1:\"k\";N;}i:5;a:5:{s:1:\"a\";i:8;s:1:\"c\";s:10:\"Management\";s:1:\"d\";s:3:\"web\";s:1:\"j\";N;s:1:\"k\";N;}i:6;a:5:{s:1:\"a\";i:9;s:1:\"c\";s:8:\"Branding\";s:1:\"d\";s:3:\"web\";s:1:\"j\";N;s:1:\"k\";N;}i:7;a:5:{s:1:\"a\";i:10;s:1:\"c\";s:14:\"Design and Dev\";s:1:\"d\";s:3:\"web\";s:1:\"j\";N;s:1:\"k\";N;}i:8;a:5:{s:1:\"a\";i:11;s:1:\"c\";s:8:\"Purchase\";s:1:\"d\";s:3:\"web\";s:1:\"j\";N;s:1:\"k\";N;}i:9;a:5:{s:1:\"a\";i:12;s:1:\"c\";s:8:\"Accounts\";s:1:\"d\";s:3:\"web\";s:1:\"j\";N;s:1:\"k\";N;}i:10;a:5:{s:1:\"a\";i:13;s:1:\"c\";s:9:\"Logistics\";s:1:\"d\";s:3:\"web\";s:1:\"j\";N;s:1:\"k\";N;}}}', 1794991776);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `catalogue_items`
--

DROP TABLE IF EXISTS `catalogue_items`;
CREATE TABLE IF NOT EXISTS `catalogue_items` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `description` text COLLATE utf8mb4_unicode_ci,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('ACTIVE','INACTIVE') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVE',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `catalogue_items_name_unique` (`name`),
  KEY `catalogue_items_created_by_foreign` (`created_by`),
  KEY `catalogue_items_updated_by_foreign` (`updated_by`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `catalogue_items`
--

INSERT INTO `catalogue_items` (`id`, `description`, `name`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'Full range of sliding and casement windows with specifications.', 'Window Systems Catalogue', 'ACTIVE', 1, 1, '2026-11-13 04:58:23', '2026-11-13 04:58:23'),
(2, 'Doors, profiles and finishes for residential and commercial projects.', 'Door Systems Catalogue', 'ACTIVE', 1, 1, '2026-11-13 04:58:23', '2026-11-13 04:58:23'),
(3, 'Technical drawings, U-values and project references for architects.', 'Architect Reference Book', 'ACTIVE', 1, 1, '2026-11-13 04:58:23', '2026-11-13 04:58:23'),
(4, 'Current dealer price list and schemes.', 'Dealer Price List', 'ACTIVE', 1, 1, '2026-11-13 04:58:23', '2026-11-13 04:58:23');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
CREATE TABLE IF NOT EXISTS `departments` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `departments_user_id_foreign` (`user_id`),
  KEY `departments_created_by_foreign` (`created_by`),
  KEY `departments_updated_by_foreign` (`updated_by`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `user_id`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 1, '2026-11-13 04:58:25', '2026-11-13 04:58:25'),
(2, 3, 1, 1, '2026-11-13 06:44:42', '2026-11-13 06:44:42'),
(3, 4, 1, 1, '2026-11-13 06:44:43', '2026-11-13 06:44:43'),
(4, 5, 1, 1, '2026-11-13 06:44:43', '2026-11-13 06:44:43'),
(5, 6, 1, 1, '2026-11-13 06:44:44', '2026-11-13 06:44:44'),
(6, 7, 1, 1, '2026-11-13 06:44:44', '2026-11-13 06:44:44'),
(7, 8, 1, 1, '2026-11-13 06:44:44', '2026-11-13 06:44:44'),
(8, 9, 1, 1, '2026-11-13 06:44:45', '2026-11-13 06:44:45'),
(10, 12, NULL, NULL, '2026-11-16 02:41:41', '2026-11-16 02:41:41'),
(11, 13, NULL, NULL, '2026-11-16 02:41:42', '2026-11-16 02:41:42'),
(12, 14, NULL, NULL, '2026-11-16 02:41:42', '2026-11-16 02:41:42'),
(13, 15, NULL, NULL, '2026-11-16 02:41:43', '2026-11-16 02:41:43'),
(14, 16, NULL, NULL, '2026-11-16 02:41:43', '2026-11-16 02:41:43'),
(15, 17, NULL, NULL, '2026-11-16 07:31:43', '2026-11-16 07:31:43'),
(16, 18, NULL, NULL, '2026-11-16 07:31:44', '2026-11-16 07:31:44'),
(17, 19, NULL, NULL, '2026-11-16 07:31:44', '2026-11-16 07:31:44'),
(18, 20, NULL, NULL, '2026-11-16 07:31:44', '2026-11-16 07:31:44'),
(19, 21, NULL, NULL, '2026-11-16 07:31:45', '2026-11-16 07:31:45'),
(20, 22, NULL, NULL, '2026-11-16 07:31:45', '2026-11-16 07:31:45'),
(21, 23, NULL, NULL, '2026-11-16 07:31:45', '2026-11-16 07:31:45'),
(23, 25, NULL, NULL, '2026-11-16 07:39:21', '2026-11-16 07:39:21'),
(24, 26, NULL, NULL, '2026-11-16 07:39:22', '2026-11-16 07:39:22'),
(25, 27, NULL, NULL, '2026-11-16 07:39:22', '2026-11-16 07:39:22');

-- --------------------------------------------------------

--
-- Table structure for table `dispatches`
--

DROP TABLE IF EXISTS `dispatches`;
CREATE TABLE IF NOT EXISTS `dispatches` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `team_task_id` bigint UNSIGNED NOT NULL,
  `courier` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tracking_no` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dispatched_on` date NOT NULL,
  `expected_on` date DEFAULT NULL,
  `remarks` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dispatches_team_task_id_foreign` (`team_task_id`),
  KEY `dispatches_created_by_foreign` (`created_by`),
  KEY `dispatches_updated_by_foreign` (`updated_by`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
CREATE TABLE IF NOT EXISTS `events` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` enum('ACTIVE','INACTIVE') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVE',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `events_name_unique` (`name`),
  UNIQUE KEY `events_code_unique` (`code`),
  KEY `events_created_by_foreign` (`created_by`),
  KEY `events_updated_by_foreign` (`updated_by`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `code`, `name`, `description`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'meet_event', 'Meet Events', 'Detailed planning and execution of structured meets involving fabricators, architects and related stakeholders.', 'ACTIVE', NULL, NULL, '2026-11-13 04:47:50', '2026-11-13 05:08:46'),
(2, 'exhibition', 'Events / Exhibitions / Sponsorship', 'To manage participation in major exhibitions, small exhibition spaces and sponsorship opportunities.', 'ACTIVE', NULL, NULL, '2026-11-13 04:47:50', '2026-11-13 05:08:46'),
(3, 'presentation', 'Presentations & Small Events', 'To provide Branding and Logistics support for presentations or meetings at Government, Architect or Client offices.', 'ACTIVE', NULL, NULL, '2026-11-13 04:47:50', '2026-11-13 05:08:46'),
(4, 'gift_pop', 'Gift & POP Requirement', 'A dedicated module for gifting and POP/material requests without creating a full event request.', 'ACTIVE', NULL, NULL, '2026-11-13 04:47:51', '2026-11-13 05:08:46'),
(5, 'branding', 'Branding Support & Trade Marketing', 'To manage ongoing retail, display, product sample, outdoor and advertising support requirements.', 'ACTIVE', NULL, NULL, '2026-11-13 04:47:51', '2026-11-13 05:08:46'),
(6, 'experience_centre', 'New Experience Centre', 'To manage the end-to-end request, planning, design, development, setup and launch of a new Viva Experience Centre.', 'ACTIVE', NULL, NULL, '2026-11-13 04:47:52', '2026-11-13 05:08:46');

-- --------------------------------------------------------

--
-- Table structure for table `event_types`
--

DROP TABLE IF EXISTS `event_types`;
CREATE TABLE IF NOT EXISTS `event_types` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `event_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('ACTIVE','INACTIVE') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVE',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_types_event_id_name_unique` (`event_id`,`name`),
  KEY `event_types_created_by_foreign` (`created_by`),
  KEY `event_types_updated_by_foreign` (`updated_by`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `event_types`
--

INSERT INTO `event_types` (`id`, `event_id`, `name`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 1, 'Fabricator Meet', 'ACTIVE', NULL, NULL, '2026-11-13 04:47:50', '2026-11-13 04:47:50'),
(2, 1, 'Architect Meet', 'ACTIVE', NULL, NULL, '2026-11-13 04:47:50', '2026-11-13 04:47:50'),
(3, 1, 'In-Shop Meet', 'ACTIVE', NULL, NULL, '2026-11-13 04:47:50', '2026-11-13 04:47:50'),
(4, 2, 'Major Exhibition', 'ACTIVE', NULL, NULL, '2026-11-13 04:47:50', '2026-11-13 04:47:50'),
(5, 2, 'Small Exhibition', 'ACTIVE', NULL, NULL, '2026-11-13 04:47:50', '2026-11-13 04:47:50'),
(6, 2, 'Event Sponsorship', 'ACTIVE', NULL, NULL, '2026-11-13 04:47:50', '2026-11-13 04:47:50'),
(7, 3, 'Government Office Presentation', 'ACTIVE', NULL, NULL, '2026-11-13 04:47:50', '2026-11-13 04:47:50'),
(8, 3, 'Architect Office Presentation', 'ACTIVE', NULL, NULL, '2026-11-13 04:47:50', '2026-11-13 04:47:50'),
(9, 3, 'Client Office Presentation', 'ACTIVE', NULL, NULL, '2026-11-13 04:47:50', '2026-11-13 04:47:50'),
(10, 3, 'Product Presentation', 'ACTIVE', NULL, NULL, '2026-11-13 04:47:51', '2026-11-13 04:47:51'),
(11, 3, 'Small Site / On-site Meeting', 'ACTIVE', NULL, NULL, '2026-11-13 04:47:51', '2026-11-13 04:47:51'),
(12, 4, 'Architect Gifts', 'ACTIVE', NULL, NULL, '2026-11-13 04:47:51', '2026-11-13 04:47:51'),
(13, 4, 'Fabricator Gifts', 'ACTIVE', NULL, NULL, '2026-11-13 04:47:51', '2026-11-13 04:47:51'),
(14, 4, 'Government Department Gifts', 'ACTIVE', NULL, NULL, '2026-11-13 04:47:51', '2026-11-13 04:47:51'),
(15, 4, 'Client Gifts', 'ACTIVE', NULL, NULL, '2026-11-13 04:47:51', '2026-11-13 04:47:51'),
(16, 4, 'Dealer / Channel Partner Gifts', 'ACTIVE', NULL, NULL, '2026-11-13 04:47:51', '2026-11-13 04:47:51'),
(17, 4, 'Seasonal Gifts', 'ACTIVE', NULL, NULL, '2026-11-13 04:47:51', '2026-11-13 04:47:51'),
(18, 4, 'Event / Exhibition Gifts', 'ACTIVE', NULL, NULL, '2026-11-13 04:47:51', '2026-11-13 04:47:51'),
(19, 4, 'POP Material', 'ACTIVE', NULL, NULL, '2026-11-13 04:47:51', '2026-11-13 04:47:51'),
(20, 4, 'Samples & Catalogues', 'ACTIVE', NULL, NULL, '2026-11-13 04:47:51', '2026-11-13 04:47:51'),
(21, 4, 'Other Promotional Material', 'ACTIVE', NULL, NULL, '2026-11-13 04:47:51', '2026-11-13 04:47:51'),
(22, 5, 'POP Requirement', 'ACTIVE', NULL, NULL, '2026-11-13 04:47:51', '2026-11-13 04:47:51'),
(23, 5, 'Retail Branding Support', 'ACTIVE', NULL, NULL, '2026-11-13 04:47:51', '2026-11-13 04:47:51'),
(24, 5, 'Small Displays', 'ACTIVE', NULL, NULL, '2026-11-13 04:47:51', '2026-11-13 04:47:51'),
(25, 5, 'Big Displays', 'ACTIVE', NULL, NULL, '2026-11-13 04:47:51', '2026-11-13 04:47:51'),
(26, 5, 'Product Samples', 'ACTIVE', NULL, NULL, '2026-11-13 04:47:51', '2026-11-13 04:47:51'),
(27, 5, 'Outdoor Branding (OOH)', 'ACTIVE', NULL, NULL, '2026-11-13 04:47:51', '2026-11-13 04:47:51'),
(28, 5, 'Newspaper Advertisement', 'ACTIVE', NULL, NULL, '2026-11-13 04:47:52', '2026-11-13 04:47:52'),
(29, 5, 'Magazine Advertisement', 'ACTIVE', NULL, NULL, '2026-11-13 04:47:52', '2026-11-13 04:47:52'),
(30, 5, 'Other Trade Marketing', 'ACTIVE', NULL, NULL, '2026-11-13 04:47:52', '2026-11-13 04:47:52'),
(31, 6, 'New City / Location', 'ACTIVE', NULL, NULL, '2026-11-13 04:47:52', '2026-11-13 04:47:52'),
(32, 6, 'Dealer / Partner Location', 'ACTIVE', NULL, NULL, '2026-11-13 04:47:52', '2026-11-13 04:47:52'),
(33, 6, 'Relocation / Major Revamp', 'ACTIVE', NULL, 1, '2026-11-13 04:47:52', '2026-11-13 05:26:30'),
(34, 1, 'Dealer Meet', 'ACTIVE', NULL, NULL, '2026-11-13 05:25:56', '2026-11-13 05:25:56'),
(35, 1, 'Others', 'ACTIVE', NULL, NULL, '2026-11-13 05:25:56', '2026-11-13 05:25:56'),
(36, 3, 'Architect Visit', 'ACTIVE', NULL, NULL, '2026-11-13 05:25:56', '2026-11-13 05:25:56');

-- --------------------------------------------------------

--
-- Table structure for table `event_type_gift_item`
--

DROP TABLE IF EXISTS `event_type_gift_item`;
CREATE TABLE IF NOT EXISTS `event_type_gift_item` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `event_type_id` bigint UNSIGNED NOT NULL,
  `gift_item_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_type_gift_item_event_type_id_gift_item_id_unique` (`event_type_id`,`gift_item_id`),
  KEY `event_type_gift_item_gift_item_id_foreign` (`gift_item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `event_type_gift_item`
--

INSERT INTO `event_type_gift_item` (`id`, `event_type_id`, `gift_item_id`, `created_at`, `updated_at`) VALUES
(4, 34, 3, '2026-11-13 07:53:27', '2026-11-13 07:53:27'),
(5, 34, 1, '2026-11-13 07:53:27', '2026-11-13 07:53:27'),
(6, 34, 4, '2026-11-13 07:53:27', '2026-11-13 07:53:27'),
(7, 2, 1, '2026-11-13 07:54:50', '2026-11-13 07:54:50'),
(8, 2, 5, '2026-11-13 07:54:50', '2026-11-13 07:54:50'),
(9, 1, 5, '2026-11-13 07:54:50', '2026-11-13 07:54:50'),
(10, 3, 3, '2026-11-13 07:54:50', '2026-11-13 07:54:50');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gift_items`
--

DROP TABLE IF EXISTS `gift_items`;
CREATE TABLE IF NOT EXISTS `gift_items` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('ACTIVE','INACTIVE') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVE',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `gift_items_name_unique` (`name`),
  KEY `gift_items_created_by_foreign` (`created_by`),
  KEY `gift_items_updated_by_foreign` (`updated_by`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gift_items`
--

INSERT INTO `gift_items` (`id`, `name`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'Desk Calendar', 'ACTIVE', 1, 1, '2026-11-13 04:58:20', '2026-11-13 04:58:20'),
(2, 'Diary & Pen Set', 'ACTIVE', 1, 1, '2026-11-13 04:58:21', '2026-11-13 04:58:21'),
(3, 'Coffee Mug', 'ACTIVE', 1, 1, '2026-11-13 04:58:21', '2026-11-13 04:58:21'),
(4, 'Diwali Dry Fruit Box', 'ACTIVE', 1, 1, '2026-11-13 04:58:21', '2026-11-13 04:58:21'),
(5, 'Laptop Bag', 'ACTIVE', 1, 1, '2026-11-13 04:58:21', '2026-11-13 04:58:21'),
(6, 'Wall Clock', 'ACTIVE', 1, 1, '2026-11-13 04:58:21', '2026-11-13 04:58:21');

-- --------------------------------------------------------

--
-- Table structure for table `guest_categories`
--

DROP TABLE IF EXISTS `guest_categories`;
CREATE TABLE IF NOT EXISTS `guest_categories` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('ACTIVE','INACTIVE') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVE',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `guest_categories_name_unique` (`name`),
  KEY `guest_categories_created_by_foreign` (`created_by`),
  KEY `guest_categories_updated_by_foreign` (`updated_by`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `guest_categories`
--

INSERT INTO `guest_categories` (`id`, `name`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'Distributor', 'ACTIVE', 1, 1, '2026-11-13 04:58:20', '2026-11-13 04:58:20'),
(2, 'Dealer', 'ACTIVE', 1, 1, '2026-11-13 04:58:20', '2026-11-13 04:58:20'),
(3, 'Architect', 'ACTIVE', 1, 1, '2026-11-13 04:58:20', '2026-11-13 04:58:20'),
(4, 'Fabricator', 'ACTIVE', 1, 1, '2026-11-13 04:58:20', '2026-11-13 04:58:20'),
(5, 'Others', 'ACTIVE', 1, 1, '2026-11-13 04:58:20', '2026-11-13 04:58:20');

-- --------------------------------------------------------

--
-- Table structure for table `hotels`
--

DROP TABLE IF EXISTS `hotels`;
CREATE TABLE IF NOT EXISTS `hotels` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('ACTIVE','INACTIVE') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVE',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `hotels_name_unique` (`name`),
  KEY `hotels_created_by_foreign` (`created_by`),
  KEY `hotels_updated_by_foreign` (`updated_by`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hotels`
--

INSERT INTO `hotels` (`id`, `name`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'Hyatt Regency, Ahmedabad', 'ACTIVE', 1, 1, '2026-11-13 04:58:23', '2026-11-13 04:58:23'),
(2, 'Taj Skyline, Ahmedabad', 'ACTIVE', 1, 1, '2026-11-13 04:58:23', '2026-11-13 04:58:23'),
(3, 'Novotel, Pune', 'ACTIVE', 1, 1, '2026-11-13 04:58:23', '2026-11-13 05:34:58'),
(4, 'Radisson Blu, Nagpur', 'ACTIVE', 1, 1, '2026-11-13 04:58:23', '2026-11-13 04:58:23'),
(5, 'The Grand Bhagwati, Surat', 'ACTIVE', 1, 1, '2026-11-13 04:58:23', '2026-11-13 04:58:23');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

DROP TABLE IF EXISTS `locations`;
CREATE TABLE IF NOT EXISTS `locations` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('ACTIVE','INACTIVE') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVE',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `locations_name_unique` (`name`),
  KEY `locations_created_by_foreign` (`created_by`),
  KEY `locations_updated_by_foreign` (`updated_by`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`id`, `name`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'Ahmedabad', 'ACTIVE', 1, 1, '2026-11-13 06:48:13', '2026-11-13 06:48:13'),
(2, 'Surat', 'ACTIVE', 1, 1, '2026-11-13 06:48:13', '2026-11-13 08:12:10'),
(3, 'Vadodara', 'ACTIVE', 1, 1, '2026-11-13 06:48:14', '2026-11-13 06:48:14'),
(4, 'Pune', 'ACTIVE', 1, 1, '2026-11-13 06:48:14', '2026-11-13 06:48:14'),
(5, 'Mumbai', 'ACTIVE', 1, 1, '2026-11-13 06:48:14', '2026-11-13 06:48:14'),
(6, 'Nagpur', 'ACTIVE', 1, 1, '2026-11-13 06:48:14', '2026-11-13 06:48:14'),
(7, 'Gandhinagar', 'ACTIVE', 1, 1, '2026-11-13 06:48:14', '2026-11-13 06:48:14');

-- --------------------------------------------------------

--
-- Table structure for table `meet_proposals`
--

DROP TABLE IF EXISTS `meet_proposals`;
CREATE TABLE IF NOT EXISTS `meet_proposals` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `request_id` bigint UNSIGNED NOT NULL,
  `decoration_plan` text COLLATE utf8mb4_unicode_ci,
  `branding_plan` text COLLATE utf8mb4_unicode_ci,
  `setup_plan` text COLLATE utf8mb4_unicode_ci,
  `recommended_plan` text COLLATE utf8mb4_unicode_ci,
  `estimated_budget` decimal(14,2) DEFAULT NULL,
  `submitted_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `meet_proposals_request_id_unique` (`request_id`),
  KEY `meet_proposals_created_by_foreign` (`created_by`),
  KEY `meet_proposals_updated_by_foreign` (`updated_by`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `meet_proposals`
--

INSERT INTO `meet_proposals` (`id`, `request_id`, `decoration_plan`, `branding_plan`, `setup_plan`, `recommended_plan`, `estimated_budget`, `submitted_at`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(2, 1, NULL, NULL, NULL, 'CVBN', '4555.00', '2026-11-17 02:39:53', 1, 1, '2026-11-17 02:39:53', '2026-11-17 02:39:53'),
(8, 2, NULL, NULL, NULL, 'TTT', '455.00', NULL, 1, 1, '2026-11-17 02:58:22', '2026-11-17 02:58:22');

-- --------------------------------------------------------

--
-- Table structure for table `meet_proposal_options`
--

DROP TABLE IF EXISTS `meet_proposal_options`;
CREATE TABLE IF NOT EXISTS `meet_proposal_options` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `meet_proposal_id` bigint UNSIGNED NOT NULL,
  `type` enum('venue','vendor') COLLATE utf8mb4_unicode_ci NOT NULL,
  `hotel_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `details` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `capacity` int UNSIGNED DEFAULT NULL,
  `amount` decimal(14,2) DEFAULT NULL,
  `is_recommended` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `meet_proposal_options_meet_proposal_id_foreign` (`meet_proposal_id`),
  KEY `meet_proposal_options_hotel_id_foreign` (`hotel_id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `meet_proposal_options`
--

INSERT INTO `meet_proposal_options` (`id`, `meet_proposal_id`, `type`, `hotel_id`, `name`, `details`, `capacity`, `amount`, `is_recommended`, `created_at`, `updated_at`) VALUES
(10, 2, 'venue', 2, 'Taj Skyline, Ahmedabad', 'PUNE', 3, '34400.00', 1, '2026-11-17 02:39:53', '2026-11-17 02:39:53'),
(11, 2, 'vendor', NULL, 'REENA', 'R', NULL, '2334.00', 0, '2026-11-17 02:39:53', '2026-11-17 02:39:53'),
(36, 8, 'venue', 4, 'Radisson Blu, Nagpur', 'TEE', 4, '44.00', 1, '2026-11-17 02:58:22', '2026-11-17 02:58:22'),
(37, 8, 'vendor', NULL, 'TT', 'TRRR', NULL, '45.00', 0, '2026-11-17 02:58:22', '2026-11-17 02:58:22');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_10_24_155636_create_permissiongroups_table', 1),
(5, '2026_10_24_165611_create_permission_tables', 1),
(8, '2026_10_29_095927_create_departments_table', 1),
(9, '2026_10_29_110000_create_zones_table', 1),
(10, '2026_10_29_110001_create_events_table', 1),
(11, '2026_10_29_110002_create_event_types_table', 1),
(12, '2026_10_29_110003_create_gift_items_table', 1),
(13, '2026_10_29_110004_create_pop_items_table', 1),
(14, '2026_10_29_110005_create_product_samples_table', 1),
(15, '2026_10_29_110006_create_catalogue_items_table', 1),
(16, '2026_10_29_110007_create_sponsorship_deliverables_table', 1),
(17, '2026_10_29_110008_create_branding_materials_table', 1),
(18, '2026_10_29_110009_create_guest_categories_table', 1),
(19, '2026_10_29_110010_create_hotels_table', 1),
(20, '2026_11_02_100000_create_requests_table', 1),
(21, '2026_11_02_100001_create_request_guest_categories_table', 1),
(22, '2026_11_02_100002_create_request_items_table', 1),
(23, '2026_11_02_100003_create_request_documents_table', 1),
(24, '2026_11_02_100004_create_request_histories_table', 1),
(27, '2026_10_29_110011_create_locations_table', 3),
(29, '2026_11_13_131658_create_event_type_gift_item_table', 5),
(35, '2026_11_16_100000_create_approvals_table', 11),
(37, '2026_11_17_100000_create_meet_proposals_table', 12),
(38, '2026_11_17_100001_create_meet_proposal_options_table', 12),
(39, '2026_11_17_120000_create_team_tasks_table', 13),
(40, '2026_11_17_120001_create_purchase_orders_table', 13),
(41, '2026_11_17_120002_create_payments_table', 13),
(42, '2026_11_17_120003_create_dispatches_table', 13);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
CREATE TABLE IF NOT EXISTS `model_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_permissions`
--

INSERT INTO `model_has_permissions` (`permission_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(2, 'App\\Models\\User', 1),
(3, 'App\\Models\\User', 1),
(4, 'App\\Models\\User', 1),
(5, 'App\\Models\\User', 1),
(6, 'App\\Models\\User', 1),
(7, 'App\\Models\\User', 1),
(8, 'App\\Models\\User', 1),
(9, 'App\\Models\\User', 1),
(10, 'App\\Models\\User', 1),
(11, 'App\\Models\\User', 1),
(12, 'App\\Models\\User', 1),
(13, 'App\\Models\\User', 1),
(14, 'App\\Models\\User', 1),
(15, 'App\\Models\\User', 1),
(16, 'App\\Models\\User', 1),
(17, 'App\\Models\\User', 1),
(18, 'App\\Models\\User', 1),
(19, 'App\\Models\\User', 1),
(20, 'App\\Models\\User', 1),
(21, 'App\\Models\\User', 1),
(22, 'App\\Models\\User', 1),
(23, 'App\\Models\\User', 1),
(24, 'App\\Models\\User', 1),
(25, 'App\\Models\\User', 1),
(26, 'App\\Models\\User', 1),
(27, 'App\\Models\\User', 1),
(28, 'App\\Models\\User', 1),
(29, 'App\\Models\\User', 1),
(30, 'App\\Models\\User', 1),
(31, 'App\\Models\\User', 1),
(32, 'App\\Models\\User', 1),
(33, 'App\\Models\\User', 1),
(34, 'App\\Models\\User', 1),
(35, 'App\\Models\\User', 1),
(36, 'App\\Models\\User', 1),
(37, 'App\\Models\\User', 1),
(38, 'App\\Models\\User', 1),
(39, 'App\\Models\\User', 1),
(40, 'App\\Models\\User', 1),
(41, 'App\\Models\\User', 1),
(42, 'App\\Models\\User', 1),
(43, 'App\\Models\\User', 1),
(44, 'App\\Models\\User', 1),
(45, 'App\\Models\\User', 1),
(46, 'App\\Models\\User', 1),
(47, 'App\\Models\\User', 1),
(48, 'App\\Models\\User', 1),
(49, 'App\\Models\\User', 1),
(50, 'App\\Models\\User', 1),
(51, 'App\\Models\\User', 1),
(52, 'App\\Models\\User', 1),
(53, 'App\\Models\\User', 1),
(54, 'App\\Models\\User', 1),
(55, 'App\\Models\\User', 1),
(56, 'App\\Models\\User', 1),
(58, 'App\\Models\\User', 1),
(59, 'App\\Models\\User', 1),
(60, 'App\\Models\\User', 1),
(61, 'App\\Models\\User', 1),
(62, 'App\\Models\\User', 1),
(63, 'App\\Models\\User', 1),
(64, 'App\\Models\\User', 1),
(66, 'App\\Models\\User', 1),
(67, 'App\\Models\\User', 1),
(68, 'App\\Models\\User', 1),
(70, 'App\\Models\\User', 1),
(71, 'App\\Models\\User', 1),
(72, 'App\\Models\\User', 1),
(74, 'App\\Models\\User', 1),
(75, 'App\\Models\\User', 1),
(76, 'App\\Models\\User', 1),
(78, 'App\\Models\\User', 1),
(79, 'App\\Models\\User', 1),
(80, 'App\\Models\\User', 1),
(83, 'App\\Models\\User', 1),
(85, 'App\\Models\\User', 1),
(86, 'App\\Models\\User', 1),
(88, 'App\\Models\\User', 1),
(90, 'App\\Models\\User', 1),
(92, 'App\\Models\\User', 1),
(1, 'App\\Models\\User', 2),
(54, 'App\\Models\\User', 2),
(55, 'App\\Models\\User', 2),
(56, 'App\\Models\\User', 2),
(62, 'App\\Models\\User', 2),
(63, 'App\\Models\\User', 2),
(64, 'App\\Models\\User', 2),
(66, 'App\\Models\\User', 2),
(67, 'App\\Models\\User', 2),
(68, 'App\\Models\\User', 2),
(70, 'App\\Models\\User', 2),
(71, 'App\\Models\\User', 2),
(72, 'App\\Models\\User', 2),
(74, 'App\\Models\\User', 2),
(75, 'App\\Models\\User', 2),
(76, 'App\\Models\\User', 2),
(78, 'App\\Models\\User', 2),
(79, 'App\\Models\\User', 2),
(80, 'App\\Models\\User', 2),
(1, 'App\\Models\\User', 3),
(54, 'App\\Models\\User', 3),
(55, 'App\\Models\\User', 3),
(56, 'App\\Models\\User', 3),
(62, 'App\\Models\\User', 3),
(63, 'App\\Models\\User', 3),
(64, 'App\\Models\\User', 3),
(66, 'App\\Models\\User', 3),
(67, 'App\\Models\\User', 3),
(68, 'App\\Models\\User', 3),
(70, 'App\\Models\\User', 3),
(71, 'App\\Models\\User', 3),
(72, 'App\\Models\\User', 3),
(74, 'App\\Models\\User', 3),
(75, 'App\\Models\\User', 3),
(76, 'App\\Models\\User', 3),
(78, 'App\\Models\\User', 3),
(79, 'App\\Models\\User', 3),
(80, 'App\\Models\\User', 3),
(1, 'App\\Models\\User', 4),
(54, 'App\\Models\\User', 4),
(55, 'App\\Models\\User', 4),
(56, 'App\\Models\\User', 4),
(62, 'App\\Models\\User', 4),
(63, 'App\\Models\\User', 4),
(64, 'App\\Models\\User', 4),
(66, 'App\\Models\\User', 4),
(67, 'App\\Models\\User', 4),
(68, 'App\\Models\\User', 4),
(70, 'App\\Models\\User', 4),
(71, 'App\\Models\\User', 4),
(72, 'App\\Models\\User', 4),
(74, 'App\\Models\\User', 4),
(75, 'App\\Models\\User', 4),
(76, 'App\\Models\\User', 4),
(78, 'App\\Models\\User', 4),
(79, 'App\\Models\\User', 4),
(80, 'App\\Models\\User', 4),
(1, 'App\\Models\\User', 5),
(54, 'App\\Models\\User', 5),
(55, 'App\\Models\\User', 5),
(56, 'App\\Models\\User', 5),
(62, 'App\\Models\\User', 5),
(63, 'App\\Models\\User', 5),
(64, 'App\\Models\\User', 5),
(66, 'App\\Models\\User', 5),
(67, 'App\\Models\\User', 5),
(68, 'App\\Models\\User', 5),
(70, 'App\\Models\\User', 5),
(71, 'App\\Models\\User', 5),
(72, 'App\\Models\\User', 5),
(1, 'App\\Models\\User', 6),
(54, 'App\\Models\\User', 6),
(55, 'App\\Models\\User', 6),
(56, 'App\\Models\\User', 6),
(62, 'App\\Models\\User', 6),
(63, 'App\\Models\\User', 6),
(64, 'App\\Models\\User', 6),
(66, 'App\\Models\\User', 6),
(67, 'App\\Models\\User', 6),
(68, 'App\\Models\\User', 6),
(70, 'App\\Models\\User', 6),
(71, 'App\\Models\\User', 6),
(72, 'App\\Models\\User', 6),
(1, 'App\\Models\\User', 7),
(54, 'App\\Models\\User', 7),
(55, 'App\\Models\\User', 7),
(56, 'App\\Models\\User', 7),
(62, 'App\\Models\\User', 7),
(63, 'App\\Models\\User', 7),
(64, 'App\\Models\\User', 7),
(66, 'App\\Models\\User', 7),
(67, 'App\\Models\\User', 7),
(68, 'App\\Models\\User', 7),
(70, 'App\\Models\\User', 7),
(71, 'App\\Models\\User', 7),
(72, 'App\\Models\\User', 7),
(1, 'App\\Models\\User', 8),
(54, 'App\\Models\\User', 8),
(55, 'App\\Models\\User', 8),
(56, 'App\\Models\\User', 8),
(62, 'App\\Models\\User', 8),
(63, 'App\\Models\\User', 8),
(64, 'App\\Models\\User', 8),
(66, 'App\\Models\\User', 8),
(67, 'App\\Models\\User', 8),
(68, 'App\\Models\\User', 8),
(70, 'App\\Models\\User', 8),
(71, 'App\\Models\\User', 8),
(72, 'App\\Models\\User', 8),
(74, 'App\\Models\\User', 8),
(75, 'App\\Models\\User', 8),
(76, 'App\\Models\\User', 8),
(78, 'App\\Models\\User', 8),
(79, 'App\\Models\\User', 8),
(80, 'App\\Models\\User', 8),
(1, 'App\\Models\\User', 9),
(54, 'App\\Models\\User', 9),
(55, 'App\\Models\\User', 9),
(56, 'App\\Models\\User', 9),
(62, 'App\\Models\\User', 9),
(63, 'App\\Models\\User', 9),
(64, 'App\\Models\\User', 9),
(66, 'App\\Models\\User', 9),
(67, 'App\\Models\\User', 9),
(68, 'App\\Models\\User', 9),
(70, 'App\\Models\\User', 9),
(71, 'App\\Models\\User', 9),
(72, 'App\\Models\\User', 9),
(74, 'App\\Models\\User', 9),
(75, 'App\\Models\\User', 9),
(76, 'App\\Models\\User', 9),
(78, 'App\\Models\\User', 9),
(79, 'App\\Models\\User', 9),
(80, 'App\\Models\\User', 9),
(1, 'App\\Models\\User', 12),
(54, 'App\\Models\\User', 12),
(83, 'App\\Models\\User', 12),
(1, 'App\\Models\\User', 13),
(54, 'App\\Models\\User', 13),
(83, 'App\\Models\\User', 13),
(1, 'App\\Models\\User', 14),
(54, 'App\\Models\\User', 14),
(83, 'App\\Models\\User', 14),
(1, 'App\\Models\\User', 15),
(54, 'App\\Models\\User', 15),
(83, 'App\\Models\\User', 15),
(1, 'App\\Models\\User', 16),
(54, 'App\\Models\\User', 16),
(83, 'App\\Models\\User', 16),
(1, 'App\\Models\\User', 17),
(54, 'App\\Models\\User', 17),
(85, 'App\\Models\\User', 17),
(86, 'App\\Models\\User', 17),
(1, 'App\\Models\\User', 18),
(54, 'App\\Models\\User', 18),
(85, 'App\\Models\\User', 18),
(86, 'App\\Models\\User', 18),
(1, 'App\\Models\\User', 19),
(1, 'App\\Models\\User', 20),
(54, 'App\\Models\\User', 20),
(88, 'App\\Models\\User', 20),
(1, 'App\\Models\\User', 21),
(54, 'App\\Models\\User', 21),
(90, 'App\\Models\\User', 21),
(1, 'App\\Models\\User', 22),
(54, 'App\\Models\\User', 22),
(92, 'App\\Models\\User', 22),
(1, 'App\\Models\\User', 23),
(54, 'App\\Models\\User', 23),
(92, 'App\\Models\\User', 23),
(1, 'App\\Models\\User', 25),
(1, 'App\\Models\\User', 26),
(54, 'App\\Models\\User', 26),
(88, 'App\\Models\\User', 26),
(1, 'App\\Models\\User', 27),
(54, 'App\\Models\\User', 27),
(90, 'App\\Models\\User', 27);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
CREATE TABLE IF NOT EXISTS `model_has_roles` (
  `role_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(3, 'App\\Models\\User', 2),
(3, 'App\\Models\\User', 3),
(3, 'App\\Models\\User', 4),
(4, 'App\\Models\\User', 5),
(4, 'App\\Models\\User', 6),
(4, 'App\\Models\\User', 7),
(5, 'App\\Models\\User', 8),
(5, 'App\\Models\\User', 9),
(6, 'App\\Models\\User', 12),
(6, 'App\\Models\\User', 13),
(8, 'App\\Models\\User', 14),
(8, 'App\\Models\\User', 15),
(8, 'App\\Models\\User', 16),
(9, 'App\\Models\\User', 17),
(9, 'App\\Models\\User', 18),
(10, 'App\\Models\\User', 19),
(11, 'App\\Models\\User', 20),
(12, 'App\\Models\\User', 21),
(13, 'App\\Models\\User', 22),
(13, 'App\\Models\\User', 23),
(10, 'App\\Models\\User', 25),
(11, 'App\\Models\\User', 26),
(12, 'App\\Models\\User', 27);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
CREATE TABLE IF NOT EXISTS `payments` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `team_task_id` bigint UNSIGNED NOT NULL,
  `type` enum('advance','final') COLLATE utf8mb4_unicode_ci NOT NULL,
  `paid_to` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(14,2) NOT NULL,
  `paid_on` date NOT NULL,
  `mode` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payments_team_task_id_foreign` (`team_task_id`),
  KEY `payments_created_by_foreign` (`created_by`),
  KEY `payments_updated_by_foreign` (`updated_by`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissiongroups`
--

DROP TABLE IF EXISTS `permissiongroups`;
CREATE TABLE IF NOT EXISTS `permissiongroups` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `controller` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissiongroups`
--

INSERT INTO `permissiongroups` (`id`, `name`, `controller`, `created_at`, `updated_at`) VALUES
(1, 'Dashboard', 'Admin\\DashboardController', '2026-11-13 04:47:43', '2026-11-13 04:47:43'),
(2, 'Role', 'Admin\\RoleController', '2026-11-13 04:47:43', '2026-11-13 04:47:43'),
(3, 'Department', 'Admin\\DepartmentController', '2026-11-13 04:47:44', '2026-11-13 04:47:44'),
(4, 'Zone', 'Admin\\ZoneController', '2026-11-13 04:47:44', '2026-11-13 04:47:44'),
(5, 'Event', 'Admin\\EventController', '2026-11-13 04:47:45', '2026-11-13 04:47:45'),
(6, 'Event Type', 'Admin\\EventTypeController', '2026-11-13 04:47:45', '2026-11-13 04:47:45'),
(7, 'Gift Item', 'Admin\\GiftItemController', '2026-11-13 04:47:45', '2026-11-13 04:47:45'),
(8, 'POP Item', 'Admin\\PopItemController', '2026-11-13 04:47:45', '2026-11-13 04:47:45'),
(9, 'Product Sample', 'Admin\\ProductSampleController', '2026-11-13 04:47:46', '2026-11-13 04:47:46'),
(10, 'Catalogue Item', 'Admin\\CatalogueItemController', '2026-11-13 04:47:46', '2026-11-13 04:47:46'),
(11, 'Sponsorship Deliverable', 'Admin\\SponsorshipDeliverableController', '2026-11-13 04:47:46', '2026-11-13 04:47:46'),
(12, 'Branding Material', 'Admin\\BrandingMaterialController', '2026-11-13 04:47:47', '2026-11-13 04:47:47'),
(13, 'Guest Category', 'Admin\\GuestCategoryController', '2026-11-13 04:47:47', '2026-11-13 04:47:47'),
(14, 'Hotel', 'Admin\\HotelController', '2026-11-13 04:47:47', '2026-11-13 04:47:47'),
(15, 'Meet Event Request', 'Admin\\MeetEventRequestController', '2026-11-13 04:47:48', '2026-11-13 04:47:48'),
(16, 'Location', 'Admin\\LocationController', '2026-11-13 06:48:08', '2026-11-13 06:48:08'),
(17, 'Exhibition Request', 'Admin\\ExhibitionRequestController', '2026-11-14 03:26:19', '2026-11-14 03:26:19'),
(18, 'Presentation Request', 'Admin\\PresentationRequestController', '2026-11-14 04:19:44', '2026-11-14 04:19:44'),
(19, 'Gift & POP Request', 'Admin\\GiftPopRequestController', '2026-11-14 06:19:47', '2026-11-14 06:19:47'),
(20, 'Branding Support Request', 'Admin\\BrandingRequestController', '2026-11-14 06:27:45', '2026-11-14 06:27:45'),
(21, 'Experience Centre Request', 'Admin\\ExperienceCentreRequestController', '2026-11-14 06:34:25', '2026-11-14 06:34:25'),
(22, 'Approval', 'Admin\\ApprovalController', '2026-11-16 01:15:07', '2026-11-16 01:15:07'),
(23, 'Meet Planning', 'Admin\\MeetPlanningController', '2026-11-17 02:20:15', '2026-11-17 02:20:15'),
(24, 'Purchase Task', 'Admin\\PurchaseTaskController', '2026-11-17 02:43:41', '2026-11-17 02:43:41'),
(25, 'Accounts Task', 'Admin\\AccountsTaskController', '2026-11-17 02:43:41', '2026-11-17 02:43:41'),
(26, 'Logistics Task', 'Admin\\LogisticsTaskController', '2026-11-17 02:43:41', '2026-11-17 02:43:41');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `permissiongroup_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `methods` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`),
  KEY `permissions_permissiongroup_id_foreign` (`permissiongroup_id`)
) ENGINE=InnoDB AUTO_INCREMENT=93 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `permissiongroup_id`, `name`, `guard_name`, `methods`, `created_at`, `updated_at`) VALUES
(1, 1, 'dashboard-view', 'web', '[\"index\"]', '2026-11-13 04:47:43', '2026-11-13 04:47:43'),
(2, 2, 'role-view', 'web', '[\"index\",\"data\",\"list\",\"show\"]', '2026-11-13 04:47:43', '2026-11-13 04:47:43'),
(3, 2, 'role-store', 'web', '[\"create\",\"store\"]', '2026-11-13 04:47:44', '2026-11-13 04:47:44'),
(4, 2, 'role-update', 'web', '[\"edit\",\"update\",\"changeStatus\"]', '2026-11-13 04:47:44', '2026-11-13 04:47:44'),
(5, 2, 'role-permission', 'web', '[\"permissionsShow\",\"permissionsUpdate\"]', '2026-11-13 04:47:44', '2026-11-13 04:47:44'),
(6, 3, 'department-view', 'web', '[\"index\",\"data\",\"list\",\"show\"]', '2026-11-13 04:47:44', '2026-11-13 04:47:44'),
(7, 3, 'department-store', 'web', '[\"create\",\"store\"]', '2026-11-13 04:47:44', '2026-11-13 04:47:44'),
(8, 3, 'department-update', 'web', '[\"edit\",\"update\",\"changeStatus\"]', '2026-11-13 04:47:44', '2026-11-13 04:47:44'),
(9, 3, 'department-delete', 'web', '[\"destroy\"]', '2026-11-13 04:47:44', '2026-11-13 04:47:44'),
(10, 4, 'zone-view', 'web', '[\"index\",\"data\",\"list\",\"show\"]', '2026-11-13 04:47:44', '2026-11-13 04:47:44'),
(11, 4, 'zone-store', 'web', '[\"create\",\"store\"]', '2026-11-13 04:47:44', '2026-11-13 04:47:44'),
(12, 4, 'zone-update', 'web', '[\"edit\",\"update\",\"changeStatus\"]', '2026-11-13 04:47:45', '2026-11-13 04:47:45'),
(13, 4, 'zone-delete', 'web', '[\"destroy\"]', '2026-11-13 04:47:45', '2026-11-13 04:47:45'),
(14, 5, 'event-view', 'web', '[\"index\",\"data\",\"list\",\"show\"]', '2026-11-13 04:47:45', '2026-11-13 04:47:45'),
(15, 5, 'event-store', 'web', '[\"create\",\"store\"]', '2026-11-13 04:47:45', '2026-11-13 04:47:45'),
(16, 5, 'event-update', 'web', '[\"edit\",\"update\",\"changeStatus\"]', '2026-11-13 04:47:45', '2026-11-13 04:47:45'),
(17, 5, 'event-delete', 'web', '[\"destroy\"]', '2026-11-13 04:47:45', '2026-11-13 04:47:45'),
(18, 6, 'eventtype-view', 'web', '[\"index\",\"data\",\"list\",\"show\"]', '2026-11-13 04:47:45', '2026-11-13 04:47:45'),
(19, 6, 'eventtype-store', 'web', '[\"create\",\"store\"]', '2026-11-13 04:47:45', '2026-11-13 04:47:45'),
(20, 6, 'eventtype-update', 'web', '[\"edit\",\"update\",\"changeStatus\"]', '2026-11-13 04:47:45', '2026-11-13 04:47:45'),
(21, 6, 'eventtype-delete', 'web', '[\"destroy\"]', '2026-11-13 04:47:45', '2026-11-13 04:47:45'),
(22, 7, 'giftitem-view', 'web', '[\"index\",\"data\",\"list\",\"show\"]', '2026-11-13 04:47:45', '2026-11-13 04:47:45'),
(23, 7, 'giftitem-store', 'web', '[\"create\",\"store\"]', '2026-11-13 04:47:45', '2026-11-13 04:47:45'),
(24, 7, 'giftitem-update', 'web', '[\"edit\",\"update\",\"changeStatus\"]', '2026-11-13 04:47:45', '2026-11-13 04:47:45'),
(25, 7, 'giftitem-delete', 'web', '[\"destroy\"]', '2026-11-13 04:47:45', '2026-11-13 04:47:45'),
(26, 8, 'popitem-view', 'web', '[\"index\",\"data\",\"list\",\"show\"]', '2026-11-13 04:47:46', '2026-11-13 04:47:46'),
(27, 8, 'popitem-store', 'web', '[\"create\",\"store\"]', '2026-11-13 04:47:46', '2026-11-13 04:47:46'),
(28, 8, 'popitem-update', 'web', '[\"edit\",\"update\",\"changeStatus\"]', '2026-11-13 04:47:46', '2026-11-13 04:47:46'),
(29, 8, 'popitem-delete', 'web', '[\"destroy\"]', '2026-11-13 04:47:46', '2026-11-13 04:47:46'),
(30, 9, 'productsample-view', 'web', '[\"index\",\"data\",\"list\",\"show\"]', '2026-11-13 04:47:46', '2026-11-13 04:47:46'),
(31, 9, 'productsample-store', 'web', '[\"create\",\"store\"]', '2026-11-13 04:47:46', '2026-11-13 04:47:46'),
(32, 9, 'productsample-update', 'web', '[\"edit\",\"update\",\"changeStatus\"]', '2026-11-13 04:47:46', '2026-11-13 04:47:46'),
(33, 9, 'productsample-delete', 'web', '[\"destroy\"]', '2026-11-13 04:47:46', '2026-11-13 04:47:46'),
(34, 10, 'catalogueitem-view', 'web', '[\"index\",\"data\",\"list\",\"show\"]', '2026-11-13 04:47:46', '2026-11-13 04:47:46'),
(35, 10, 'catalogueitem-store', 'web', '[\"create\",\"store\"]', '2026-11-13 04:47:46', '2026-11-13 04:47:46'),
(36, 10, 'catalogueitem-update', 'web', '[\"edit\",\"update\",\"changeStatus\"]', '2026-11-13 04:47:46', '2026-11-13 04:47:46'),
(37, 10, 'catalogueitem-delete', 'web', '[\"destroy\"]', '2026-11-13 04:47:46', '2026-11-13 04:47:46'),
(38, 11, 'sponsorshipdeliverable-view', 'web', '[\"index\",\"data\",\"list\",\"show\"]', '2026-11-13 04:47:46', '2026-11-13 04:47:46'),
(39, 11, 'sponsorshipdeliverable-store', 'web', '[\"create\",\"store\"]', '2026-11-13 04:47:46', '2026-11-13 04:47:46'),
(40, 11, 'sponsorshipdeliverable-update', 'web', '[\"edit\",\"update\",\"changeStatus\"]', '2026-11-13 04:47:47', '2026-11-13 04:47:47'),
(41, 11, 'sponsorshipdeliverable-delete', 'web', '[\"destroy\"]', '2026-11-13 04:47:47', '2026-11-13 04:47:47'),
(42, 12, 'brandingmaterial-view', 'web', '[\"index\",\"data\",\"list\",\"show\"]', '2026-11-13 04:47:47', '2026-11-13 04:47:47'),
(43, 12, 'brandingmaterial-store', 'web', '[\"create\",\"store\"]', '2026-11-13 04:47:47', '2026-11-13 04:47:47'),
(44, 12, 'brandingmaterial-update', 'web', '[\"edit\",\"update\",\"changeStatus\"]', '2026-11-13 04:47:47', '2026-11-13 04:47:47'),
(45, 12, 'brandingmaterial-delete', 'web', '[\"destroy\"]', '2026-11-13 04:47:47', '2026-11-13 04:47:47'),
(46, 13, 'guestcategory-view', 'web', '[\"index\",\"data\",\"list\",\"show\"]', '2026-11-13 04:47:47', '2026-11-13 04:47:47'),
(47, 13, 'guestcategory-store', 'web', '[\"create\",\"store\"]', '2026-11-13 04:47:47', '2026-11-13 04:47:47'),
(48, 13, 'guestcategory-update', 'web', '[\"edit\",\"update\",\"changeStatus\"]', '2026-11-13 04:47:47', '2026-11-13 04:47:47'),
(49, 13, 'guestcategory-delete', 'web', '[\"destroy\"]', '2026-11-13 04:47:47', '2026-11-13 04:47:47'),
(50, 14, 'hotel-view', 'web', '[\"index\",\"data\",\"list\",\"show\"]', '2026-11-13 04:47:47', '2026-11-13 04:47:47'),
(51, 14, 'hotel-store', 'web', '[\"create\",\"store\"]', '2026-11-13 04:47:48', '2026-11-13 04:47:48'),
(52, 14, 'hotel-update', 'web', '[\"edit\",\"update\",\"changeStatus\"]', '2026-11-13 04:47:48', '2026-11-13 04:47:48'),
(53, 14, 'hotel-delete', 'web', '[\"destroy\"]', '2026-11-13 04:47:48', '2026-11-13 04:47:48'),
(54, 15, 'meeteventrequest-view', 'web', '[\"index\",\"data\",\"show\",\"document\"]', '2026-11-13 04:47:48', '2026-11-13 04:47:48'),
(55, 15, 'meeteventrequest-store', 'web', '[\"create\",\"store\"]', '2026-11-13 04:47:48', '2026-11-13 04:47:48'),
(56, 15, 'meeteventrequest-update', 'web', '[\"edit\",\"update\"]', '2026-11-13 04:47:48', '2026-11-13 04:47:48'),
(58, 16, 'location-view', 'web', '[\"index\",\"data\",\"list\",\"show\"]', '2026-11-13 06:48:08', '2026-11-13 06:48:08'),
(59, 16, 'location-store', 'web', '[\"create\",\"store\"]', '2026-11-13 06:48:08', '2026-11-13 06:48:08'),
(60, 16, 'location-update', 'web', '[\"edit\",\"update\",\"changeStatus\"]', '2026-11-13 06:48:09', '2026-11-13 06:48:09'),
(61, 16, 'location-delete', 'web', '[\"destroy\"]', '2026-11-13 06:48:09', '2026-11-13 06:48:09'),
(62, 17, 'exhibitionrequest-view', 'web', '[\"index\",\"data\",\"show\",\"document\"]', '2026-11-14 03:26:19', '2026-11-14 03:26:19'),
(63, 17, 'exhibitionrequest-store', 'web', '[\"create\",\"store\"]', '2026-11-14 03:26:19', '2026-11-14 03:26:19'),
(64, 17, 'exhibitionrequest-update', 'web', '[\"edit\",\"update\"]', '2026-11-14 03:26:19', '2026-11-14 03:26:19'),
(66, 18, 'presentationrequest-view', 'web', '[\"index\",\"data\",\"show\",\"document\"]', '2026-11-14 04:19:44', '2026-11-14 04:19:44'),
(67, 18, 'presentationrequest-store', 'web', '[\"create\",\"store\"]', '2026-11-14 04:19:44', '2026-11-14 04:19:44'),
(68, 18, 'presentationrequest-update', 'web', '[\"edit\",\"update\"]', '2026-11-14 04:19:44', '2026-11-14 04:19:44'),
(70, 19, 'giftpoprequest-view', 'web', '[\"index\",\"data\",\"show\"]', '2026-11-14 06:19:47', '2026-11-14 06:19:47'),
(71, 19, 'giftpoprequest-store', 'web', '[\"create\",\"store\"]', '2026-11-14 06:19:48', '2026-11-14 06:19:48'),
(72, 19, 'giftpoprequest-update', 'web', '[\"edit\",\"update\"]', '2026-11-14 06:19:48', '2026-11-14 06:19:48'),
(74, 20, 'brandingrequest-view', 'web', '[\"index\",\"data\",\"show\",\"document\"]', '2026-11-14 06:27:45', '2026-11-14 06:27:45'),
(75, 20, 'brandingrequest-store', 'web', '[\"create\",\"store\"]', '2026-11-14 06:27:45', '2026-11-14 06:27:45'),
(76, 20, 'brandingrequest-update', 'web', '[\"edit\",\"update\"]', '2026-11-14 06:27:45', '2026-11-14 06:27:45'),
(78, 21, 'experiencecentrerequest-view', 'web', '[\"index\",\"data\",\"show\",\"document\"]', '2026-11-14 06:34:26', '2026-11-14 06:34:26'),
(79, 21, 'experiencecentrerequest-store', 'web', '[\"create\",\"store\"]', '2026-11-14 06:34:26', '2026-11-14 06:34:26'),
(80, 21, 'experiencecentrerequest-update', 'web', '[\"edit\",\"update\"]', '2026-11-14 06:34:26', '2026-11-14 06:34:26'),
(83, 22, 'approval-action', 'web', '[\"approve\",\"sendBack\"]', '2026-11-16 01:15:07', '2026-11-16 01:15:07'),
(85, 23, 'meetplanning-update', 'web', '[\"store\",\"update\"]', '2026-11-17 02:20:15', '2026-11-17 03:18:24'),
(86, 23, 'meetplanning-close', 'web', '[\"eventReady\",\"complete\",\"close\"]', '2026-11-17 02:43:41', '2026-11-17 02:43:41'),
(88, 24, 'purchasetask-update', 'web', '[\"store\",\"complete\"]', '2026-11-17 02:43:41', '2026-11-17 02:43:41'),
(90, 25, 'accountstask-update', 'web', '[\"store\",\"complete\"]', '2026-11-17 02:43:41', '2026-11-17 02:43:41'),
(92, 26, 'logisticstask-update', 'web', '[\"items\",\"sendOut\"]', '2026-11-17 02:43:42', '2026-11-17 02:43:42');

-- --------------------------------------------------------

--
-- Table structure for table `pop_items`
--

DROP TABLE IF EXISTS `pop_items`;
CREATE TABLE IF NOT EXISTS `pop_items` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('ACTIVE','INACTIVE') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVE',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pop_items_name_unique` (`name`),
  KEY `pop_items_created_by_foreign` (`created_by`),
  KEY `pop_items_updated_by_foreign` (`updated_by`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pop_items`
--

INSERT INTO `pop_items` (`id`, `name`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'Standee', 'ACTIVE', 1, 1, '2026-11-13 04:58:21', '2026-11-13 04:58:21'),
(2, 'Danglers', 'ACTIVE', 1, 1, '2026-11-13 04:58:21', '2026-11-13 04:58:21'),
(3, 'Shelf Strip', 'ACTIVE', 1, 1, '2026-11-13 04:58:22', '2026-11-13 04:58:22'),
(4, 'Product Poster', 'ACTIVE', 1, 1, '2026-11-13 04:58:22', '2026-11-13 04:58:22'),
(5, 'Counter Display', 'ACTIVE', 1, 1, '2026-11-13 04:58:22', '2026-11-13 04:58:22'),
(6, 'Window Sticker', 'ACTIVE', 1, 1, '2026-11-13 04:58:22', '2026-11-13 04:58:22');

-- --------------------------------------------------------

--
-- Table structure for table `product_samples`
--

DROP TABLE IF EXISTS `product_samples`;
CREATE TABLE IF NOT EXISTS `product_samples` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('ACTIVE','INACTIVE') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVE',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_samples_name_unique` (`name`),
  KEY `product_samples_created_by_foreign` (`created_by`),
  KEY `product_samples_updated_by_foreign` (`updated_by`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_samples`
--

INSERT INTO `product_samples` (`id`, `name`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'Sliding Window Sample', 'ACTIVE', 1, 1, '2026-11-13 04:58:22', '2026-11-13 04:58:22'),
(2, 'Casement Window Sample', 'ACTIVE', 1, 1, '2026-11-13 04:58:22', '2026-11-13 04:58:22'),
(3, 'Door Profile Sample', 'ACTIVE', 1, 1, '2026-11-13 04:58:22', '2026-11-13 04:58:22'),
(4, 'Glass Sample Kit', 'ACTIVE', 1, 1, '2026-11-13 04:58:22', '2026-11-13 04:58:22'),
(5, 'Hardware Sample Board', 'ACTIVE', 1, 1, '2026-11-13 04:58:22', '2026-11-13 04:58:22');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_orders`
--

DROP TABLE IF EXISTS `purchase_orders`;
CREATE TABLE IF NOT EXISTS `purchase_orders` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `team_task_id` bigint UNSIGNED NOT NULL,
  `vendor_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `po_number` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `po_date` date NOT NULL,
  `amount` decimal(14,2) NOT NULL,
  `remarks` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_orders_team_task_id_foreign` (`team_task_id`),
  KEY `purchase_orders_created_by_foreign` (`created_by`),
  KEY `purchase_orders_updated_by_foreign` (`updated_by`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `requests`
--

DROP TABLE IF EXISTS `requests`;
CREATE TABLE IF NOT EXISTS `requests` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `request_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `module` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `event_id` bigint UNSIGNED DEFAULT NULL,
  `event_type_id` bigint UNSIGNED DEFAULT NULL,
  `zone_id` bigint UNSIGNED DEFAULT NULL,
  `location_id` bigint UNSIGNED DEFAULT NULL,
  `event_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `organiser` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `organisation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_person` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `recipient` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dealer_project` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `objective` text COLLATE utf8mb4_unicode_ci,
  `preferred_date_1` date DEFAULT NULL,
  `preferred_date_2` date DEFAULT NULL,
  `meeting_time` time DEFAULT NULL,
  `required_by` date DEFAULT NULL,
  `team_strength` int UNSIGNED DEFAULT NULL,
  `total_guests` int UNSIGNED NOT NULL DEFAULT '0',
  `attendee_count` int UNSIGNED DEFAULT NULL,
  `expected_footfall` int UNSIGNED DEFAULT NULL,
  `target_audience` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `space_details` text COLLATE utf8mb4_unicode_ci,
  `dimensions` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` int UNSIGNED DEFAULT NULL,
  `creative_requirement` text COLLATE utf8mb4_unicode_ci,
  `interior_requirement` text COLLATE utf8mb4_unicode_ci,
  `av_requirement` text COLLATE utf8mb4_unicode_ci,
  `furniture_requirement` text COLLATE utf8mb4_unicode_ci,
  `launch_needed` tinyint(1) NOT NULL DEFAULT '0',
  `launch_requirement` text COLLATE utf8mb4_unicode_ci,
  `presentation_material` text COLLATE utf8mb4_unicode_ci,
  `needs_design` tinyint(1) NOT NULL DEFAULT '0',
  `proposed_budget` decimal(14,2) DEFAULT NULL,
  `estimated_budget` decimal(14,2) DEFAULT NULL,
  `branding_needs` json DEFAULT NULL,
  `deliverables` json DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `stage` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Request Raised',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Draft',
  `submitted_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `requests_request_no_unique` (`request_no`),
  KEY `requests_user_id_foreign` (`user_id`),
  KEY `requests_event_id_foreign` (`event_id`),
  KEY `requests_event_type_id_foreign` (`event_type_id`),
  KEY `requests_zone_id_foreign` (`zone_id`),
  KEY `requests_created_by_foreign` (`created_by`),
  KEY `requests_updated_by_foreign` (`updated_by`),
  KEY `requests_module_status_index` (`module`,`status`),
  KEY `requests_location_id_foreign` (`location_id`)
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `requests`
--

INSERT INTO `requests` (`id`, `request_no`, `module`, `user_id`, `event_id`, `event_type_id`, `zone_id`, `location_id`, `event_name`, `organiser`, `organisation`, `contact_person`, `recipient`, `dealer_project`, `city`, `objective`, `preferred_date_1`, `preferred_date_2`, `meeting_time`, `required_by`, `team_strength`, `total_guests`, `attendee_count`, `expected_footfall`, `target_audience`, `space_details`, `dimensions`, `quantity`, `creative_requirement`, `interior_requirement`, `av_requirement`, `furniture_requirement`, `launch_needed`, `launch_requirement`, `presentation_material`, `needs_design`, `proposed_budget`, `estimated_budget`, `branding_needs`, `deliverables`, `remarks`, `stage`, `status`, `submitted_at`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'MEET-2026-0001', 'meet_event', 2, 1, 1, 4, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'Ahmedabad', 'Launch the new sliding window range to fabricators in Ahmedabad.', '2026-12-08', '2026-12-15', NULL, NULL, 8, 75, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, NULL, '[1, 2, 4]', NULL, 'Prefer a hotel near SG Highway.', 'Planning / Design', 'Proposal Submitted', '2026-11-13 04:58:25', 2, 1, '2026-11-13 04:58:25', '2026-11-17 02:39:53'),
(2, 'MEET-2026-0002', 'meet_event', 2, 1, 2, 4, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'Surat', 'Present door systems and technical data to leading architects of Surat.', '2026-12-23', NULL, NULL, NULL, 5, 40, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, NULL, '[1, 5]', NULL, NULL, 'Planning / Design', 'Branding Planning', '2026-11-13 04:58:26', 2, 1, '2026-11-13 04:58:26', '2026-11-17 02:58:22'),
(36, 'EVNT-2026-0001', 'exhibition', 1, 2, 4, NULL, 7, 'BUILD EXPO 2026', 'ABC', NULL, NULL, NULL, NULL, NULL, NULL, '2026-11-11', NULL, NULL, NULL, NULL, 0, NULL, 4, '5', 'HAA', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, '33400.00', NULL, '[2]', '[]', NULL, 'Request Raised', 'Request Raised', '2026-11-16 02:39:26', 1, 1, '2026-11-16 02:39:26', '2026-11-16 02:39:26'),
(37, 'MEET-2026-0003', 'meet_event', 8, 1, 34, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Annual dealer meet for Gujarat to launch the new sliding window range and share the 2027 scheme.', '2026-12-12', NULL, NULL, NULL, 6, 120, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, NULL, '[1, 2, 3, 6]', NULL, 'Need a banquet hall for 120 guests with stage and projector.', 'Request Raised', 'Request Raised', '2026-11-13 10:37:00', 8, 8, '2026-11-16 07:31:46', '2026-11-16 07:31:46'),
(38, 'MEET-2026-0004', 'meet_event', 3, 1, 2, NULL, 5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Architect meet in Mumbai to present door systems and get specifications in upcoming projects.', '2027-01-09', NULL, NULL, NULL, 4, 60, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, NULL, '[1, 4, 5]', NULL, 'Evening session with dinner.', 'Request Raised', 'Request Raised', '2026-11-14 08:47:00', 3, 3, '2026-11-16 07:31:46', '2026-11-16 07:31:46'),
(39, 'MEET-2026-0005', 'meet_event', 2, 1, 1, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Fabricator meet in Surat for training on new profiles and hardware fitting.', '2027-01-23', NULL, NULL, NULL, 3, 45, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, NULL, '[2, 4]', NULL, 'Live demo of fitting needed.', 'Approval', 'Initial Approval', '2026-11-15 06:16:00', 2, 1, '2026-11-16 07:31:46', '2026-11-17 02:59:45'),
(40, 'EVNT-2026-0002', 'exhibition', 9, 2, 4, NULL, 5, 'Windoor Expo 2027', 'Windoor Trade Fairs Pvt Ltd', NULL, NULL, NULL, NULL, NULL, NULL, '2027-02-18', NULL, NULL, NULL, NULL, 0, NULL, 15000, 'Architects, builders, fabricators and dealers', '6m x 9m corner stall in Hall 2', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, '800000.00', NULL, '[1, 2, 4, 5]', NULL, 'Stall design and 3D layout needed from Design & Dev.', 'Request Raised', 'Request Raised', '2026-11-12 08:56:00', 9, 9, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(41, 'EVNT-2026-0003', 'exhibition', 4, 2, 5, NULL, 2, 'Surat Builders Expo', 'Surat Builders Association', NULL, NULL, NULL, NULL, NULL, NULL, '2027-01-15', NULL, NULL, NULL, NULL, 0, NULL, 3000, 'Builders and contractors', '3m x 3m shell scheme stall', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, '150000.00', NULL, '[2, 4]', NULL, NULL, 'Request Raised', 'Request Raised', '2026-11-14 08:58:00', 4, 4, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(42, 'EVNT-2026-0004', 'exhibition', 2, 2, 6, NULL, 1, 'Ahmedabad Architects Awards Night', 'Gujarat Institute of Architects', NULL, NULL, NULL, NULL, NULL, NULL, '2026-12-27', NULL, NULL, NULL, NULL, 0, NULL, 400, 'Architects and interior designers', 'Co-sponsor with a table space at the venue', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, '250000.00', NULL, '[3, 5]', '[1, 3, 6, 7]', 'Speaking slot of 10 minutes for our product head.', 'Request Raised', 'Request Raised', '2026-11-15 07:11:00', 2, 2, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(43, 'PRES-2026-0001', 'presentation', 5, 3, 7, NULL, 7, NULL, NULL, 'Roads & Buildings Department', 'Executive Engineer', NULL, NULL, NULL, 'Present window systems for upcoming government building projects.', '2026-12-08', NULL, '11:00:00', NULL, NULL, 0, 8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Company profile, product catalogues and sample board', 0, NULL, NULL, '[4]', NULL, NULL, 'Request Raised', 'Request Raised', '2026-11-13 06:18:00', 5, 5, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(44, 'PRES-2026-0002', 'presentation', 6, 3, 8, NULL, 4, NULL, NULL, 'Studio Arc Architects', 'Ar. Meera Kulkarni', NULL, NULL, NULL, 'Product presentation to the design team for a residential tower project.', '2026-12-15', NULL, '15:30:00', NULL, NULL, 0, 12, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Custom presentation with project references', 1, NULL, NULL, '[5]', NULL, 'Need a custom deck from Design & Dev.', 'Request Raised', 'Request Raised', '2026-11-14 12:09:00', 6, 6, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(45, 'PRES-2026-0003', 'presentation', 3, 3, 9, NULL, 5, NULL, NULL, 'Skyline Developers', 'Mr. Rakesh Mehta', NULL, NULL, NULL, 'Client office presentation for supply of doors and windows for a new project.', '2027-01-06', NULL, '12:00:00', NULL, NULL, 0, 5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Standard catalogues and price list', 0, NULL, NULL, '[]', NULL, NULL, 'Request Raised', 'Request Raised', '2026-11-15 10:09:00', 3, 3, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(46, 'GIFT-2026-0001', 'gift_pop', 7, 4, 12, NULL, 2, NULL, NULL, NULL, NULL, 'Surat architects (25)', NULL, NULL, 'New year gifts for key architects in Surat.', NULL, NULL, NULL, '2026-12-28', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Deliver to the Surat office.', 'Request Raised', 'Request Raised', '2026-11-13 05:27:00', 7, 7, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(47, 'GIFT-2026-0002', 'gift_pop', 8, 4, 17, NULL, 1, NULL, NULL, NULL, NULL, 'Gujarat dealers (40)', NULL, NULL, 'Seasonal gifts for top dealers of Gujarat.', NULL, NULL, NULL, '2026-12-20', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Request Raised', 'Request Raised', '2026-11-14 08:18:00', 8, 8, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(48, 'GIFT-2026-0003', 'gift_pop', 2, 4, 19, NULL, 3, NULL, NULL, NULL, NULL, 'Vadodara dealers', NULL, NULL, 'POP material for 5 new dealer counters in Vadodara.', NULL, NULL, NULL, '2027-01-05', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Send with the next dispatch.', 'Request Raised', 'Request Raised', '2026-11-15 07:30:00', 2, 2, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(49, 'BRND-2026-0001', 'branding', 9, 5, 23, NULL, 4, NULL, NULL, NULL, NULL, NULL, 'Shree Ganesh Windows, Pune', NULL, 'Shop front branding and in-shop display for a top dealer.', NULL, NULL, NULL, '2027-01-10', NULL, 0, NULL, NULL, NULL, NULL, 'Front 20ft x 4ft, inside wall 10ft x 8ft', 2, 'New brand look with product images', NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, '85000.00', NULL, NULL, NULL, 'Request Raised', 'Request Raised', '2026-11-13 11:07:00', 9, 9, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(50, 'BRND-2026-0002', 'branding', 2, 5, 22, NULL, 1, NULL, NULL, NULL, NULL, NULL, 'Ahmedabad dealer network', NULL, 'Counter displays and shelf strips for 10 dealers.', NULL, NULL, NULL, '2026-12-22', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, '30000.00', NULL, NULL, NULL, 'Request Raised', 'Request Raised', '2026-11-14 11:35:00', 2, 2, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(51, 'BRND-2026-0003', 'branding', 4, 5, 27, NULL, 2, NULL, NULL, NULL, NULL, NULL, 'Ring Road hoarding, Surat', NULL, 'Outdoor hoarding for brand visibility for 3 months.', NULL, NULL, NULL, '2027-01-20', NULL, 0, NULL, NULL, NULL, NULL, '40ft x 20ft', 1, 'Hoarding creative with the new campaign line', NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, '240000.00', NULL, NULL, 'Media rate to be confirmed by vendor.', 'Request Raised', 'Request Raised', '2026-11-15 10:34:00', 4, 4, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(52, 'EXPC-2026-0001', 'experience_centre', 8, 6, 31, NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'New experience centre in Vadodara to serve architects and builders of central Gujarat.', NULL, NULL, NULL, '2027-03-15', NULL, 0, NULL, NULL, NULL, 'Ground floor showroom on Alkapuri main road', '1800 sq ft', NULL, NULL, 'Full interior with window and door display walls', 'LED screen and sound system', 'Meeting table, sofa and reception desk', 1, 'Launch event for 80 architects and dealers', NULL, 0, NULL, '2500000.00', '[1, 4, 5]', NULL, NULL, 'Request Raised', 'Request Raised', '2026-11-12 06:38:00', 8, 8, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(53, 'EXPC-2026-0002', 'experience_centre', 9, 6, 32, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Display corner at a partner dealer location in Nagpur.', NULL, NULL, NULL, '2027-02-28', NULL, 0, NULL, NULL, NULL, 'Inside dealer showroom, first floor', '600 sq ft', NULL, NULL, 'Display panels and lighting', NULL, 'Small meeting table', 0, NULL, NULL, 0, NULL, '600000.00', '[4]', NULL, 'Dealer will share half the cost.', 'Request Raised', 'Request Raised', '2026-11-14 06:15:00', 9, 9, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(54, 'EXPC-2026-0003', 'experience_centre', 3, 6, 31, NULL, 5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Flagship experience centre in Mumbai for premium projects.', NULL, NULL, NULL, '2027-04-30', NULL, 0, NULL, NULL, NULL, 'Commercial space in Andheri East', '3000 sq ft', NULL, NULL, 'Premium interior with live working models', 'Video wall and conference setup', 'Conference room and lounge', 1, 'Launch with press and 150 guests', NULL, 0, NULL, '4500000.00', '[1, 5, 6]', NULL, NULL, 'Request Raised', 'Request Raised', '2026-11-15 05:52:00', 3, 3, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(64, 'MEET-2026-0006', 'meet_event', 1, 1, 2, NULL, 7, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'yryg', '2026-11-04', NULL, NULL, NULL, 87, 7, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, NULL, '[2, 5]', NULL, 'tee', 'Approval', 'Initial Approval', '2026-11-17 05:05:49', 1, 1, '2026-11-17 05:05:48', '2026-11-17 05:07:38');

-- --------------------------------------------------------

--
-- Table structure for table `request_documents`
--

DROP TABLE IF EXISTS `request_documents`;
CREATE TABLE IF NOT EXISTS `request_documents` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `request_id` bigint UNSIGNED NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `original_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `version` int UNSIGNED NOT NULL DEFAULT '1',
  `uploaded_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `request_documents_request_id_foreign` (`request_id`),
  KEY `request_documents_uploaded_by_foreign` (`uploaded_by`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `request_documents`
--

INSERT INTO `request_documents` (`id`, `request_id`, `type`, `file_path`, `original_name`, `version`, `uploaded_by`, `created_at`, `updated_at`) VALUES
(1, 1, 'guest_list', 'requests/1/guest_list_v1.csv', 'guest_list_ahmedabad.csv', 1, 2, '2026-11-13 04:58:25', '2026-11-13 04:58:25'),
(2, 2, 'guest_list', 'requests/2/guest_list_v1.csv', 'guest_list_surat.csv', 1, 2, '2026-11-13 04:58:26', '2026-11-13 04:58:26'),
(20, 36, 'proposal', 'requests/36/proposal_1.docx', 'DEVELOPEMENT_PHASE (1).docx', 1, 1, '2026-11-16 02:39:26', '2026-11-16 02:39:26'),
(21, 37, 'guest_list', 'requests/37/guest_list_v1.csv', 'Dealer_Meet_Guest_List.csv', 1, 8, '2026-11-16 07:31:46', '2026-11-16 07:31:46'),
(22, 38, 'guest_list', 'requests/38/guest_list_v1.csv', 'Architect_Meet_Mumbai.csv', 1, 3, '2026-11-16 07:31:46', '2026-11-16 07:31:46'),
(23, 39, 'guest_list', 'requests/39/guest_list_v1.csv', 'Fabricator_Meet_Surat.csv', 1, 2, '2026-11-16 07:31:46', '2026-11-16 07:31:46'),
(24, 40, 'proposal', 'requests/40/proposal_v1.pdf', 'Windoor_Expo_Rate_Card.pdf', 1, 9, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(25, 41, 'proposal', 'requests/41/proposal_v1.pdf', 'Surat_Expo_Proposal.pdf', 1, 4, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(26, 42, 'proposal', 'requests/42/proposal_v1.pdf', 'Awards_Night_Sponsorship_Deck.pdf', 1, 2, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(27, 49, 'site_photo', 'requests/49/site_photo_v1.png', 'Shop_Front_Pune.png', 1, 9, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(28, 51, 'site_photo', 'requests/51/site_photo_v1.png', 'Hoarding_Site_Surat.png', 1, 4, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(29, 52, 'site_photo', 'requests/52/site_photo_v1.png', 'Vadodara_Site.png', 1, 8, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(33, 1, 'quotation', 'requests/1/quotation_v1.jpeg', 'shera_flooring.jpeg', 1, 1, '2026-11-17 02:39:53', '2026-11-17 02:39:53'),
(55, 2, 'quotation', 'requests/2/quotation_v1.jpg', 'ACP banner New.jpg', 1, 1, '2026-11-17 02:58:22', '2026-11-17 02:58:22'),
(60, 64, 'guest_list', 'requests/64/guest_list_v1.xlsx', 'August SEO Performance Report (1).xlsx', 1, 1, '2026-11-17 05:05:49', '2026-11-17 05:05:49');

-- --------------------------------------------------------

--
-- Table structure for table `request_guest_categories`
--

DROP TABLE IF EXISTS `request_guest_categories`;
CREATE TABLE IF NOT EXISTS `request_guest_categories` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `request_id` bigint UNSIGNED NOT NULL,
  `guest_category_id` bigint UNSIGNED NOT NULL,
  `guest_count` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `request_guest_categories_request_id_guest_category_id_unique` (`request_id`,`guest_category_id`),
  KEY `request_guest_categories_guest_category_id_foreign` (`guest_category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `request_guest_categories`
--

INSERT INTO `request_guest_categories` (`id`, `request_id`, `guest_category_id`, `guest_count`, `created_at`, `updated_at`) VALUES
(1, 1, 4, 60, '2026-11-13 04:58:25', '2026-11-13 04:58:25'),
(2, 1, 2, 15, '2026-11-13 04:58:25', '2026-11-13 04:58:25'),
(3, 2, 3, 40, '2026-11-13 04:58:26', '2026-11-13 04:58:26'),
(10, 37, 1, 20, '2026-11-16 07:31:46', '2026-11-16 07:31:46'),
(11, 37, 2, 100, '2026-11-16 07:31:46', '2026-11-16 07:31:46'),
(12, 38, 3, 60, '2026-11-16 07:31:46', '2026-11-16 07:31:46'),
(13, 39, 4, 40, '2026-11-16 07:31:46', '2026-11-16 07:31:46'),
(14, 39, 5, 5, '2026-11-16 07:31:46', '2026-11-16 07:31:46'),
(16, 64, 2, 5, '2026-11-17 05:05:49', '2026-11-17 05:05:49'),
(17, 64, 1, 2, '2026-11-17 05:05:49', '2026-11-17 05:05:49');

-- --------------------------------------------------------

--
-- Table structure for table `request_histories`
--

DROP TABLE IF EXISTS `request_histories`;
CREATE TABLE IF NOT EXISTS `request_histories` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `request_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `from_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `to_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `request_histories_request_id_foreign` (`request_id`),
  KEY `request_histories_user_id_foreign` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=155 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `request_histories`
--

INSERT INTO `request_histories` (`id`, `request_id`, `user_id`, `action`, `from_status`, `to_status`, `remarks`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 'Draft created', NULL, 'Draft', NULL, '2026-11-13 04:58:25', '2026-11-13 04:58:25'),
(2, 1, 2, 'Submitted', 'Draft', 'Request Raised', NULL, '2026-11-13 04:58:25', '2026-11-13 04:58:25'),
(3, 2, 2, 'Draft created', NULL, 'Draft', NULL, '2026-11-13 04:58:26', '2026-11-13 04:58:26'),
(4, 2, 2, 'Submitted', 'Draft', 'Request Raised', NULL, '2026-11-13 04:58:26', '2026-11-13 04:58:26'),
(27, 1, 1, 'Initial approval given', 'Request Raised', 'Initial Approval', NULL, '2026-11-16 01:19:43', '2026-11-16 01:19:43'),
(28, 2, 1, 'Initial approval given', 'Request Raised', 'Initial Approval', NULL, '2026-11-16 01:26:05', '2026-11-16 01:26:05'),
(46, 36, 1, 'Submitted', NULL, 'Request Raised', NULL, '2026-11-16 02:39:26', '2026-11-16 02:39:26'),
(47, 37, 8, 'Submitted', NULL, 'Request Raised', NULL, '2026-11-16 07:31:46', '2026-11-16 07:31:46'),
(48, 38, 3, 'Submitted', NULL, 'Request Raised', NULL, '2026-11-16 07:31:46', '2026-11-16 07:31:46'),
(49, 39, 2, 'Submitted', NULL, 'Request Raised', NULL, '2026-11-16 07:31:46', '2026-11-16 07:31:46'),
(50, 40, 9, 'Submitted', NULL, 'Request Raised', NULL, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(51, 41, 4, 'Submitted', NULL, 'Request Raised', NULL, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(52, 42, 2, 'Submitted', NULL, 'Request Raised', NULL, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(53, 43, 5, 'Submitted', NULL, 'Request Raised', NULL, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(54, 44, 6, 'Submitted', NULL, 'Request Raised', NULL, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(55, 45, 3, 'Submitted', NULL, 'Request Raised', NULL, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(56, 46, 7, 'Submitted', NULL, 'Request Raised', NULL, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(57, 47, 8, 'Submitted', NULL, 'Request Raised', NULL, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(58, 48, 2, 'Submitted', NULL, 'Request Raised', NULL, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(59, 49, 9, 'Submitted', NULL, 'Request Raised', NULL, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(60, 50, 2, 'Submitted', NULL, 'Request Raised', NULL, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(61, 51, 4, 'Submitted', NULL, 'Request Raised', NULL, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(62, 52, 8, 'Submitted', NULL, 'Request Raised', NULL, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(63, 53, 9, 'Submitted', NULL, 'Request Raised', NULL, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(64, 54, 3, 'Submitted', NULL, 'Request Raised', NULL, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(72, 1, 1, 'Proposal submitted to the Director', 'Initial Approval', 'Proposal Submitted', NULL, '2026-11-17 02:39:53', '2026-11-17 02:39:53'),
(131, 2, 1, 'Branding planning started', 'Initial Approval', 'Branding Planning', NULL, '2026-11-17 02:58:22', '2026-11-17 02:58:22'),
(132, 39, 1, 'Initial approval given', 'Request Raised', 'Initial Approval', NULL, '2026-11-17 02:59:45', '2026-11-17 02:59:45'),
(153, 64, 1, 'Submitted', NULL, 'Request Raised', NULL, '2026-11-17 05:05:49', '2026-11-17 05:05:49'),
(154, 64, 1, 'Initial approval given', 'Request Raised', 'Initial Approval', 'tes', '2026-11-17 05:07:38', '2026-11-17 05:07:38');

-- --------------------------------------------------------

--
-- Table structure for table `request_items`
--

DROP TABLE IF EXISTS `request_items`;
CREATE TABLE IF NOT EXISTS `request_items` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `request_id` bigint UNSIGNED NOT NULL,
  `item_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_id` bigint UNSIGNED NOT NULL,
  `quantity` int UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `request_items_request_id_foreign` (`request_id`),
  KEY `request_items_item_type_item_id_index` (`item_type`,`item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=80 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `request_items`
--

INSERT INTO `request_items` (`id`, `request_id`, `item_type`, `item_id`, `quantity`, `created_at`, `updated_at`) VALUES
(1, 1, 'gift', 1, 75, '2026-11-13 04:58:25', '2026-11-13 04:58:25'),
(2, 1, 'pop', 1, 4, '2026-11-13 04:58:25', '2026-11-13 04:58:25'),
(3, 1, 'catalogue', 1, 75, '2026-11-13 04:58:25', '2026-11-13 04:58:25'),
(4, 1, 'sample', 1, 2, '2026-11-13 04:58:25', '2026-11-13 04:58:25'),
(5, 2, 'gift', 2, 40, '2026-11-13 04:58:26', '2026-11-13 04:58:26'),
(6, 2, 'catalogue', 3, 40, '2026-11-13 04:58:26', '2026-11-13 04:58:26'),
(28, 36, 'gift', 3, 3, '2026-11-16 02:39:26', '2026-11-16 02:39:26'),
(29, 37, 'catalogue', 1, 120, '2026-11-16 07:31:46', '2026-11-16 07:31:46'),
(30, 37, 'gift', 1, 120, '2026-11-16 07:31:46', '2026-11-16 07:31:46'),
(31, 37, 'gift', 3, 120, '2026-11-16 07:31:46', '2026-11-16 07:31:46'),
(32, 37, 'gift', 4, 120, '2026-11-16 07:31:46', '2026-11-16 07:31:46'),
(33, 38, 'catalogue', 3, 60, '2026-11-16 07:31:46', '2026-11-16 07:31:46'),
(34, 38, 'sample', 3, 2, '2026-11-16 07:31:46', '2026-11-16 07:31:46'),
(35, 38, 'gift', 1, 60, '2026-11-16 07:31:46', '2026-11-16 07:31:46'),
(36, 38, 'gift', 5, 60, '2026-11-16 07:31:46', '2026-11-16 07:31:46'),
(37, 39, 'sample', 5, 2, '2026-11-16 07:31:46', '2026-11-16 07:31:46'),
(38, 39, 'gift', 5, 45, '2026-11-16 07:31:46', '2026-11-16 07:31:46'),
(39, 40, 'catalogue', 1, 500, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(40, 40, 'catalogue', 2, 500, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(41, 40, 'gift', 3, 200, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(42, 41, 'catalogue', 1, 200, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(43, 41, 'pop', 1, 2, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(44, 43, 'sample', 1, 1, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(45, 43, 'sample', 4, 1, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(46, 43, 'catalogue', 1, 8, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(47, 44, 'catalogue', 3, 12, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(48, 44, 'sample', 2, 1, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(49, 45, 'catalogue', 2, 5, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(50, 45, 'catalogue', 4, 2, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(51, 46, 'gift', 1, 25, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(52, 46, 'gift', 2, 25, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(53, 47, 'gift', 4, 40, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(54, 48, 'pop', 1, 10, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(55, 48, 'pop', 2, 50, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(56, 48, 'pop', 3, 20, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(57, 50, 'pop', 5, 10, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(58, 50, 'pop', 3, 40, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(59, 52, 'sample', 1, 2, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(60, 52, 'sample', 2, 2, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(61, 52, 'catalogue', 1, 100, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(62, 53, 'sample', 3, 1, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(63, 53, 'pop', 5, 1, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(64, 54, 'sample', 1, 2, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(65, 54, 'sample', 4, 2, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(66, 54, 'catalogue', 3, 200, '2026-11-16 07:31:47', '2026-11-16 07:31:47'),
(78, 64, 'gift', 1, 7, '2026-11-17 05:05:49', '2026-11-17 05:05:49'),
(79, 64, 'gift', 5, 7, '2026-11-17 05:05:49', '2026-11-17 05:05:49');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`),
  KEY `roles_created_by_foreign` (`created_by`),
  KEY `roles_updated_by_foreign` (`updated_by`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'SuperAdmin', 'web', NULL, NULL, '2026-11-13 04:47:48', '2026-11-13 04:47:48'),
(2, 'User', 'web', NULL, NULL, '2026-11-13 04:47:49', '2026-11-13 04:47:49'),
(3, 'Zonal Manager', 'web', NULL, NULL, '2026-11-13 04:58:24', '2026-11-13 04:58:24'),
(4, 'Sales', 'web', NULL, NULL, '2026-11-13 06:43:20', '2026-11-13 06:43:20'),
(5, 'HOD', 'web', NULL, NULL, '2026-11-13 06:43:20', '2026-11-13 06:43:20'),
(6, 'Director', 'web', NULL, NULL, '2026-11-16 01:15:09', '2026-11-16 01:15:09'),
(8, 'Management', 'web', NULL, NULL, '2026-11-16 02:41:07', '2026-11-16 02:41:07'),
(9, 'Branding', 'web', NULL, NULL, '2026-11-16 07:31:40', '2026-11-16 07:31:40'),
(10, 'Design and Dev', 'web', NULL, NULL, '2026-11-16 07:31:40', '2026-11-16 07:31:40'),
(11, 'Purchase', 'web', NULL, NULL, '2026-11-16 07:31:41', '2026-11-16 07:31:41'),
(12, 'Accounts', 'web', NULL, NULL, '2026-11-16 07:31:41', '2026-11-16 07:31:41'),
(13, 'Logistics', 'web', NULL, NULL, '2026-11-16 07:31:42', '2026-11-16 07:31:42');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
CREATE TABLE IF NOT EXISTS `role_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(15, 1),
(16, 1),
(17, 1),
(18, 1),
(19, 1),
(20, 1),
(21, 1),
(22, 1),
(23, 1),
(24, 1),
(25, 1),
(26, 1),
(27, 1),
(28, 1),
(29, 1),
(30, 1),
(31, 1),
(32, 1),
(33, 1),
(34, 1),
(35, 1),
(36, 1),
(37, 1),
(38, 1),
(39, 1),
(40, 1),
(41, 1),
(42, 1),
(43, 1),
(44, 1),
(45, 1),
(46, 1),
(47, 1),
(48, 1),
(49, 1),
(50, 1),
(51, 1),
(52, 1),
(53, 1),
(54, 1),
(55, 1),
(56, 1),
(58, 1),
(59, 1),
(60, 1),
(61, 1),
(62, 1),
(63, 1),
(64, 1),
(66, 1),
(67, 1),
(68, 1),
(70, 1),
(71, 1),
(72, 1),
(74, 1),
(75, 1),
(76, 1),
(78, 1),
(79, 1),
(80, 1),
(83, 1),
(85, 1),
(86, 1),
(88, 1),
(90, 1),
(92, 1),
(1, 3),
(54, 3),
(55, 3),
(56, 3),
(62, 3),
(63, 3),
(64, 3),
(66, 3),
(67, 3),
(68, 3),
(70, 3),
(71, 3),
(72, 3),
(74, 3),
(75, 3),
(76, 3),
(78, 3),
(79, 3),
(80, 3),
(1, 4),
(54, 4),
(55, 4),
(56, 4),
(62, 4),
(63, 4),
(64, 4),
(66, 4),
(67, 4),
(68, 4),
(70, 4),
(71, 4),
(72, 4),
(1, 5),
(54, 5),
(55, 5),
(56, 5),
(62, 5),
(63, 5),
(64, 5),
(66, 5),
(67, 5),
(68, 5),
(70, 5),
(71, 5),
(72, 5),
(74, 5),
(75, 5),
(76, 5),
(78, 5),
(79, 5),
(80, 5),
(1, 6),
(54, 6),
(83, 6),
(1, 8),
(54, 8),
(83, 8),
(1, 9),
(54, 9),
(85, 9),
(86, 9),
(1, 10),
(1, 11),
(54, 11),
(88, 11),
(1, 12),
(54, 12),
(90, 12),
(1, 13),
(54, 13),
(92, 13);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('0TdeRMNcJMkk8DC80Q1sEqWbO1kfKRFXUz58OySD', 5, '127.0.0.1', '', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiNHVCT0tzMkFHRGNReE1MamVHajdBdkMyeG51RkdQSFBVMzVhdFBNVCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTQ6Imh0dHA6Ly8xMjcuMC4wLjE6ODEzMy9hZG1pbi9leHBlcmllbmNlLWNlbnRyZS1yZXF1ZXN0cyI7czo1OiJyb3V0ZSI7czozODoiYWRtaW4uZXhwZXJpZW5jZS1jZW50cmUtcmVxdWVzdHMuaW5kZXgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo1O3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjY0OiJlYWI0NzNlMTI0NTU3Mzk0OGVlNjQwNGRhZWY0MjhjMWM5MzRlMzY0MWMzODQ1MzU5MmQyZjhlMzQ1YzJmOTU5Ijt9', 1794910714),
('0WJoxVmxaMiUK3LqNWvZwEUKtx8aTQ0xyRIPADiZ', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRnRyUVB5WmoyazFmSG5iWG54NThwdGthc0pTTWZ3WW1rN2h4VWFWQyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1794916476),
('0xcGB3XWQybC6O5Ekb58dHhdz2EkDH0DQnZoEz82', 17, '127.0.0.1', '', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiS3lJTlVBeEdBdHNlRTBiTmIwTTMzTXQ1MVl6UFJrUkxZYjA1QTM4SyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTc7czoxNzoicGFzc3dvcmRfaGFzaF93ZWIiO3M6NjQ6ImVhYjQ3M2UxMjQ1NTczOTQ4ZWU2NDA0ZGFlZjQyOGMxYzkzNGUzNjQxYzM4NDUzNTkyZDJmOGUzNDVjMmY5NTkiO30=', 1794910502),
('0XLAoyM8LZMyhvgibZlEn01RqNUDBnpQgVfIevSR', NULL, '127.0.0.1', 'curl/8.21.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidXJUaVhrUUV4aU53T245VWFibmtvZUZOb3ZaVE9uU0RnWlpEMkR2VSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODEzNC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1794911191),
('2fxGZKqiNa0dzWygGmzO5nBwRPvbkEYZoi4Hz6Gy', 5, '127.0.0.1', '', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiUGlITTd6eDZzWUVqQ1NENEdqTEhHQWduTjJCU1RqQzFsVU9BUFhBayI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6NTtzOjE3OiJwYXNzd29yZF9oYXNoX3dlYiI7czo2NDoiZWFiNDczZTEyNDU1NzM5NDhlZTY0MDRkYWVmNDI4YzFjOTM0ZTM2NDFjMzg0NTM1OTJkMmY4ZTM0NWMyZjk1OSI7fQ==', 1794910504),
('2pZ20izqoFJrp4gPAaUHoxwztuxnexpL8rFLXsBA', 22, '127.0.0.1', '', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoid1JVU2xqcGdzbU9vSzlxd0RPdGdkekgycDY4cFpOQjZ6SVJhcmpwcCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MjI7czoxNzoicGFzc3dvcmRfaGFzaF93ZWIiO3M6NjQ6ImVhYjQ3M2UxMjQ1NTczOTQ4ZWU2NDA0ZGFlZjQyOGMxYzkzNGUzNjQxYzM4NDUzNTkyZDJmOGUzNDVjMmY5NTkiO30=', 1794910496),
('Avq7Q7AHpuCnGcHFpPYpOpXv4nHO4XNI0MJAPOAZ', 1, '127.0.0.1', '', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiUWpoWlFrczl6SEN3U3hIM0t0eTVDTE9VcHFCeDllTkZmd09lMTBXbyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTQ6Imh0dHA6Ly8xMjcuMC4wLjE6ODEzMy9hZG1pbi9leHBlcmllbmNlLWNlbnRyZS1yZXF1ZXN0cyI7czo1OiJyb3V0ZSI7czozODoiYWRtaW4uZXhwZXJpZW5jZS1jZW50cmUtcmVxdWVzdHMuaW5kZXgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjY0OiJiMGZjNThkNDAxN2Q3MjFlOWFmNTMzNDcxMDI5MDI2ZTA1NDMwZDhkNWVjMWY2YzEyMTVhZDAwMTE5NmE1ZTI5Ijt9', 1794910709),
('C15rhLgRh9a2NeiV3T8FGbQwNDAV3nXInqaPCJIg', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiS1NsQzN0QXlKN1F3dG1JUW05VVdnaHQ0Z3dyd3lGRWxNRFpQYjZ0ZiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9tZWV0LWV2ZW50LXJlcXVlc3RzIjtzOjU6InJvdXRlIjtzOjMxOiJhZG1pbi5tZWV0LWV2ZW50LXJlcXVlc3RzLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjE3OiJwYXNzd29yZF9oYXNoX3dlYiI7czo2NDoiMWFhZGVhMjY0YWMxMmZkZGMyZDA3MzZkZjA2NDA5ZDA2ODE5ZTY3NDBlZjE4Y2NiNWNlZmQ1NTljNjBlOTBlNiI7fQ==', 1794913937),
('cJyWZpJ06q2FXgR4tuz2qPYQ2LBeAgOKga7HnhHw', 12, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/5.11492.5 Chrome/156.0.8078.75 Safari/537.36 MSIX', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiRWZxMlpUbjJQV1VJMlhLSFVMQjd1SDd1YjdsYmNjT2ZJWGtrMHZveSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODEzNy9hZG1pbi9tZWV0LWV2ZW50LXJlcXVlc3RzL05FamtxeDgxbTVWMm4iO3M6NToicm91dGUiO3M6MzA6ImFkbWluLm1lZXQtZXZlbnQtcmVxdWVzdHMuc2hvdyI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjEyO3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjY0OiJlYWI0NzNlMTI0NTU3Mzk0OGVlNjQwNGRhZWY0MjhjMWM5MzRlMzY0MWMzODQ1MzU5MmQyZjhlMzQ1YzJmOTU5Ijt9', 1794912356),
('gWEFpVjmlQfSSB2cZha1e0Lb2X6TZdVie0BTNywv', 12, '127.0.0.1', '', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiVHpwb1ZRb212WTlLWGFuSmpLODNHYkR2ZklZOTZMQTczU2xKNXpQWCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTQ6Imh0dHA6Ly8xMjcuMC4wLjE6ODEzNC9hZG1pbi9leHBlcmllbmNlLWNlbnRyZS1yZXF1ZXN0cyI7czo1OiJyb3V0ZSI7czozODoiYWRtaW4uZXhwZXJpZW5jZS1jZW50cmUtcmVxdWVzdHMuaW5kZXgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxMjtzOjE3OiJwYXNzd29yZF9oYXNoX3dlYiI7czo2NDoiZWFiNDczZTEyNDU1NzM5NDhlZTY0MDRkYWVmNDI4YzFjOTM0ZTM2NDFjMzg0NTM1OTJkMmY4ZTM0NWMyZjk1OSI7fQ==', 1794911221),
('HT9a4ShvHanOXYedWLux2VKHIDLmv2JDP5117zDl', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiU3NPNk5NN2djNUJEYkRSRWxSZTJiSUNwd1J0UVJuZ2Nvb0RkYVFlVCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1794916484),
('I3rgjKpV1kqDPNJyVlrUnHDQ4n16kExbjlyitIMd', 8, '127.0.0.1', '', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoibmxRcGNRSm5DR0h0NmhkWWtmeE9zZ3QxazRqaFI0UWd5amUxZXNhYiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6ODtzOjE3OiJwYXNzd29yZF9oYXNoX3dlYiI7czo2NDoiZWFiNDczZTEyNDU1NzM5NDhlZTY0MDRkYWVmNDI4YzFjOTM0ZTM2NDFjMzg0NTM1OTJkMmY4ZTM0NWMyZjk1OSI7fQ==', 1794911430),
('IJGTtryufu1rKzigLAZhYf4XAMqw7CI8qDAgkHOy', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWFM5Y0xNVllJNkx0RHVCeWYyeGE2ZURUS2pWM0pFbVU2SVdjYUVGYiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1794916491),
('iqwrcl0d0gUqfqJBQzRjhjoWlSMJpjf6YXVTf9PR', 12, '127.0.0.1', '', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiRGc3bThCSmV0NHF6ZGY4NmY2dVZJNmtyODI0MGVtUlJ5SlFOWWgwViI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTQ6Imh0dHA6Ly8xMjcuMC4wLjE6ODEzMy9hZG1pbi9leHBlcmllbmNlLWNlbnRyZS1yZXF1ZXN0cyI7czo1OiJyb3V0ZSI7czozODoiYWRtaW4uZXhwZXJpZW5jZS1jZW50cmUtcmVxdWVzdHMuaW5kZXgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxMjtzOjE3OiJwYXNzd29yZF9oYXNoX3dlYiI7czo2NDoiZWFiNDczZTEyNDU1NzM5NDhlZTY0MDRkYWVmNDI4YzFjOTM0ZTM2NDFjMzg0NTM1OTJkMmY4ZTM0NWMyZjk1OSI7fQ==', 1794910724),
('JABv3HC3p9I4ZUW5DidyX7wzDroiQy5eWV0yUEWR', 8, '127.0.0.1', '', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiMFFSQ2R3WVpWcWZId0YwbGl0RmduaGlHWVZWVUpaa0sxSWQ0cDM5ViI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTQ6Imh0dHA6Ly8xMjcuMC4wLjE6ODEzNC9hZG1pbi9leHBlcmllbmNlLWNlbnRyZS1yZXF1ZXN0cyI7czo1OiJyb3V0ZSI7czozODoiYWRtaW4uZXhwZXJpZW5jZS1jZW50cmUtcmVxdWVzdHMuaW5kZXgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo4O3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjY0OiJlYWI0NzNlMTI0NTU3Mzk0OGVlNjQwNGRhZWY0MjhjMWM5MzRlMzY0MWMzODQ1MzU5MmQyZjhlMzQ1YzJmOTU5Ijt9', 1794911217),
('KthKTo8B8Y0QhXqldlq0eyVEUxqV975MVelUZ5lW', 20, '127.0.0.1', '', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoibFVkd01oRWtXaFJXQWI5VVFjWjZ2ekNrSUJYcVo3V0pRQ0dFVjlrbiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MjA7czoxNzoicGFzc3dvcmRfaGFzaF93ZWIiO3M6NjQ6ImVhYjQ3M2UxMjQ1NTczOTQ4ZWU2NDA0ZGFlZjQyOGMxYzkzNGUzNjQxYzM4NDUzNTkyZDJmOGUzNDVjMmY5NTkiO30=', 1794910506),
('MqjNE1bZnpyZ4sICe0sG4Ci7P4yRe1sG4mRTkihY', 21, '127.0.0.1', '', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiWE43UmozRUhZWFdSbXF2bnZjNFppOEM4Z0hxRTEwVW1BWjhRbTZlUyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MjE7czoxNzoicGFzc3dvcmRfaGFzaF93ZWIiO3M6NjQ6ImVhYjQ3M2UxMjQ1NTczOTQ4ZWU2NDA0ZGFlZjQyOGMxYzkzNGUzNjQxYzM4NDUzNTkyZDJmOGUzNDVjMmY5NTkiO30=', 1794910498),
('oFXPq2MfHy35CoUrmkGZUatZhda3epp6qDVuyxJL', 1, '127.0.0.1', '', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiUHZ2aEV1eXBuUkdtSXlUZXVTZXBWSG84SGZxUE9ydG1CYjNXVG12OSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODEzMi9hZG1pbi9tZWV0LWV2ZW50LXJlcXVlc3RzLzlucjJrbUdNeUxHalEiO3M6NToicm91dGUiO3M6MzA6ImFkbWluLm1lZXQtZXZlbnQtcmVxdWVzdHMuc2hvdyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7czoxNzoicGFzc3dvcmRfaGFzaF93ZWIiO3M6NjQ6ImIwZmM1OGQ0MDE3ZDcyMWU5YWY1MzM0NzEwMjkwMjZlMDU0MzBkOGQ1ZWMxZjZjMTIxNWFkMDAxMTk2YTVlMjkiO30=', 1794910524),
('pbrSZBq6OtxnqTa8mvlvd0ZjkZMY8Kgn1qf6xnAj', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiM3ZaQThMSERZaVRHV0FLS1pmRTV4Mlk0QkY4NE5tSjhhU0dEOEcyNSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzk6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9kZXBhcnRtZW50cyI7czo1OiJyb3V0ZSI7czoyMzoiYWRtaW4uZGVwYXJ0bWVudHMuaW5kZXgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjY0OiJiMGZjNThkNDAxN2Q3MjFlOWFmNTMzNDcxMDI5MDI2ZTA1NDMwZDhkNWVjMWY2YzEyMTVhZDAwMTE5NmE1ZTI5Ijt9', 1794916793),
('PNuNBSUjLb3b5wxtMSw0zRvrU5RAKMyCBgaz0OGZ', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUFlUM2hIYzVCQXozVkplZjhpNmdIRFlxQXJqUEgyWGxSYk5Vb1F0NCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1794916483),
('QboyvOy0oCVlToTfWX6cyvTSaE15kIvZtUCvIz7F', 12, '127.0.0.1', '', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiWFpqRFNxSWpLNUpEWG5NdUhMa1o0cmk3cnhFWGpqSnB6aFdKS3pKTCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTI7czoxNzoicGFzc3dvcmRfaGFzaF93ZWIiO3M6NjQ6ImVhYjQ3M2UxMjQ1NTczOTQ4ZWU2NDA0ZGFlZjQyOGMxYzkzNGUzNjQxYzM4NDUzNTkyZDJmOGUzNDVjMmY5NTkiO30=', 1794910490),
('qlaxlWhfA7IIa7ETtkhNpSSfkPoN9cPsMb0wdCwr', 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiTGxZMlhJMjY0NWtMaG95eEI1Y0FUOWhDdHA5bVlLV0FKTnFGdFQxaSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTQ6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9tZWV0LWV2ZW50LXJlcXVlc3RzL2NyZWF0ZSI7czo1OiJyb3V0ZSI7czozMjoiYWRtaW4ubWVldC1ldmVudC1yZXF1ZXN0cy5jcmVhdGUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo0O3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjY0OiJlYWI0NzNlMTI0NTU3Mzk0OGVlNjQwNGRhZWY0MjhjMWM5MzRlMzY0MWMzODQ1MzU5MmQyZjhlMzQ1YzJmOTU5Ijt9', 1794916722),
('TYecgNBsZAX9MtPFIekVisllCHJ4wpWLVrbIoAkQ', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiN0tYbjRkUWV4UWpSbWsyOGRaWWl3OGtVNUJmZm5tdzZLaUdiRFFxUSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1794916484),
('UqOQAyHkbR3DGZBo2cA2NNHcUwLm1MfnbIbKlqLY', NULL, '127.0.0.1', 'curl/8.21.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidXhsaGpCQkI4V0xhczFGbmQxTFlORW1FZVBmRlF4c2k5cXhnR2FyYiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODEzMi9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1794910467),
('VwHVEKGn48ljTUmk9nLQw8lCYTNlMR161n1z69tz', NULL, '127.0.0.1', 'curl/8.21.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSnY4aHo5YjFlV2x6eW9zSTVmZTBWMjVrNk9IZHpaakxEdm4zYXJEbiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODEzMy9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1794910699),
('wH59RZunbGn4zL7AlFLcQdR7Hv6S84D6ME6vNWwn', NULL, '127.0.0.1', 'curl/8.21.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicjlHSmZkUzJRV2d5SmlySzVLU3VMODJ1M1NIcmVYNDlkeDViUEduSyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODEzNy9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1794912337),
('WmuqNDrwqJ5GyEfuMk3YWAeyFWN6QgiwX6cFyPxF', 8, '127.0.0.1', '', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiczVDUVUyUXZhNWtMRjJZdlJEQVRUN1ZhTzQxTklLb1lKVDQ2MDhpTiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTQ6Imh0dHA6Ly8xMjcuMC4wLjE6ODEzMy9hZG1pbi9leHBlcmllbmNlLWNlbnRyZS1yZXF1ZXN0cyI7czo1OiJyb3V0ZSI7czozODoiYWRtaW4uZXhwZXJpZW5jZS1jZW50cmUtcmVxdWVzdHMuaW5kZXgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo4O3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjY0OiJlYWI0NzNlMTI0NTU3Mzk0OGVlNjQwNGRhZWY0MjhjMWM5MzRlMzY0MWMzODQ1MzU5MmQyZjhlMzQ1YzJmOTU5Ijt9', 1794910719),
('XgqxDQC6hrMNoXz75Nu3lScWCHn9J2afU0g8WuLG', 1, '127.0.0.1', '', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoidkxNRmVqWG53eG8wTjRZdFlFdm56WGx0eGNIbmJ6dkV3cW9BZ3pMSSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTQ6Imh0dHA6Ly8xMjcuMC4wLjE6ODEzNC9hZG1pbi9leHBlcmllbmNlLWNlbnRyZS1yZXF1ZXN0cyI7czo1OiJyb3V0ZSI7czozODoiYWRtaW4uZXhwZXJpZW5jZS1jZW50cmUtcmVxdWVzdHMuaW5kZXgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjY0OiJiMGZjNThkNDAxN2Q3MjFlOWFmNTMzNDcxMDI5MDI2ZTA1NDMwZDhkNWVjMWY2YzEyMTVhZDAwMTE5NmE1ZTI5Ijt9', 1794911205),
('XHiWtqwOpotxNcwsYJFLoaU7snImEpkuJaVvu5Do', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVDZqOWlNUVRYdzZBRlIwbEZhaVNhUXRkZFVDVlduVEN0MTlheG1VTyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1794916485),
('yHutGulOCK9ZYI4p2xuJH0gyjMGuHtxFY9sGLD4K', NULL, '127.0.0.1', 'curl/8.21.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNGJ0eWtGdEI5UExadHdaSmZDWkh6QW15YmVwUVdoVnpxZFZTMkZNcSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODEzNS9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1794911426),
('zPaoFnDkdfcDWsNbahrzuOjH5eV95lOkQHpRsShH', 5, '127.0.0.1', '', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiRnZvYVdIZ29IWXNxM3VGRkVMb3JmZTd1dXg1TjJWNUc3QnllZDFBNSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTQ6Imh0dHA6Ly8xMjcuMC4wLjE6ODEzNC9hZG1pbi9leHBlcmllbmNlLWNlbnRyZS1yZXF1ZXN0cyI7czo1OiJyb3V0ZSI7czozODoiYWRtaW4uZXhwZXJpZW5jZS1jZW50cmUtcmVxdWVzdHMuaW5kZXgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo1O3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjY0OiJlYWI0NzNlMTI0NTU3Mzk0OGVlNjQwNGRhZWY0MjhjMWM5MzRlMzY0MWMzODQ1MzU5MmQyZjhlMzQ1YzJmOTU5Ijt9', 1794911210);

-- --------------------------------------------------------

--
-- Table structure for table `sponsorship_deliverables`
--

DROP TABLE IF EXISTS `sponsorship_deliverables`;
CREATE TABLE IF NOT EXISTS `sponsorship_deliverables` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `description` text COLLATE utf8mb4_unicode_ci,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('ACTIVE','INACTIVE') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVE',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sponsorship_deliverables_name_unique` (`name`),
  KEY `sponsorship_deliverables_created_by_foreign` (`created_by`),
  KEY `sponsorship_deliverables_updated_by_foreign` (`updated_by`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sponsorship_deliverables`
--

INSERT INTO `sponsorship_deliverables` (`id`, `description`, `name`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'Viva logo on event backdrop, invites and print material.', 'Logo Placement', 'ACTIVE', 1, 1, '2026-11-13 04:58:23', '2026-11-13 04:58:23'),
(2, 'Viva banners at entry and main hall.', 'Banners', 'ACTIVE', 1, 1, '2026-11-13 04:58:23', '2026-11-13 04:58:23'),
(3, 'Branding on the stage backdrop and podium.', 'Stage Branding', 'ACTIVE', 1, 1, '2026-11-13 04:58:24', '2026-11-13 04:58:24'),
(4, 'Logo and posts on the organiser website and social media.', 'Digital Visibility', 'ACTIVE', 1, 1, '2026-11-13 04:58:24', '2026-11-13 04:58:24'),
(5, 'Table or stall space to display products.', 'Table / Stall Space', 'ACTIVE', 1, 1, '2026-11-13 04:58:24', '2026-11-13 04:58:24'),
(6, 'Delegate passes for the Viva team and guests.', 'Passes', 'ACTIVE', 1, 1, '2026-11-13 04:58:24', '2026-11-13 04:58:24'),
(7, 'A short speaking or product presentation slot.', 'Speaking Slot', 'ACTIVE', 1, 1, '2026-11-13 04:58:24', '2026-11-13 04:58:24');

-- --------------------------------------------------------

--
-- Table structure for table `team_tasks`
--

DROP TABLE IF EXISTS `team_tasks`;
CREATE TABLE IF NOT EXISTS `team_tasks` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `request_id` bigint UNSIGNED NOT NULL,
  `team` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('Pending','In Progress','Done') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `prepared_items` json DEFAULT NULL,
  `material_ready_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `completed_by` bigint UNSIGNED DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `team_tasks_request_id_team_unique` (`request_id`,`team`),
  KEY `team_tasks_completed_by_foreign` (`completed_by`),
  KEY `team_tasks_created_by_foreign` (`created_by`),
  KEY `team_tasks_updated_by_foreign` (`updated_by`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('ACTIVE','INACTIVE') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVE',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_mobile_unique` (`mobile`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `mobile`, `email_verified_at`, `password`, `status`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'super@abc.com', NULL, NULL, '$2y$12$fDf.nKBCFPhl5uts2PDeb.Hg9bqkAVZZxOFlOYhxQonGjHiGnONX2', 'ACTIVE', NULL, '2026-11-13 04:47:49', '2026-11-17 03:18:26'),
(2, 'Rajesh Patel', 'rajesh.patel@vivabranding.in', '9800000001', NULL, '$2y$12$yLqVNE/XxK2GdNQyiOf0P.TQM9lcw5FKMEqsEuNcDn9aoMZUU5MP6', 'ACTIVE', NULL, '2026-11-13 04:58:25', '2026-11-17 00:45:24'),
(3, 'Vikram Singh', 'vikram.singh@vivabranding.in', '9800000002', NULL, '$2y$12$yLqVNE/XxK2GdNQyiOf0P.TQM9lcw5FKMEqsEuNcDn9aoMZUU5MP6', 'ACTIVE', NULL, '2026-11-13 06:44:42', '2026-11-17 00:45:24'),
(4, 'Suresh Iyer', 'suresh.iyer@vivabranding.in', '9800000003', NULL, '$2y$12$yLqVNE/XxK2GdNQyiOf0P.TQM9lcw5FKMEqsEuNcDn9aoMZUU5MP6', 'ACTIVE', NULL, '2026-11-13 06:44:43', '2026-11-17 00:45:24'),
(5, 'Amit Shah', 'amit.shah@vivabranding.in', '9800000011', NULL, '$2y$12$yLqVNE/XxK2GdNQyiOf0P.TQM9lcw5FKMEqsEuNcDn9aoMZUU5MP6', 'ACTIVE', NULL, '2026-11-13 06:44:43', '2026-11-17 00:45:24'),
(6, 'Rohan Deshmukh', 'rohan.deshmukh@vivabranding.in', '9800000012', NULL, '$2y$12$yLqVNE/XxK2GdNQyiOf0P.TQM9lcw5FKMEqsEuNcDn9aoMZUU5MP6', 'ACTIVE', NULL, '2026-11-13 06:44:44', '2026-11-17 00:45:24'),
(7, 'Hardik Desai', 'hardik.desai@vivabranding.in', '9800000013', NULL, '$2y$12$yLqVNE/XxK2GdNQyiOf0P.TQM9lcw5FKMEqsEuNcDn9aoMZUU5MP6', 'INACTIVE', NULL, '2026-11-13 06:44:44', '2026-11-17 00:45:24'),
(8, 'Nilesh Mehta', 'nilesh.mehta@vivabranding.in', '9800000021', NULL, '$2y$12$yLqVNE/XxK2GdNQyiOf0P.TQM9lcw5FKMEqsEuNcDn9aoMZUU5MP6', 'ACTIVE', NULL, '2026-11-13 06:44:44', '2026-11-17 00:45:24'),
(9, 'Sachin Joshi', 'sachin.joshi@vivabranding.in', '9800000022', NULL, '$2y$12$yLqVNE/XxK2GdNQyiOf0P.TQM9lcw5FKMEqsEuNcDn9aoMZUU5MP6', 'ACTIVE', NULL, '2026-11-13 06:44:45', '2026-11-17 00:45:24'),
(12, 'Ramesh Agarwal', 'ramesh.agarwal@vivabranding.in', '9800000031', NULL, '$2y$12$yLqVNE/XxK2GdNQyiOf0P.TQM9lcw5FKMEqsEuNcDn9aoMZUU5MP6', 'ACTIVE', NULL, '2026-11-16 02:41:41', '2026-11-17 00:45:25'),
(13, 'Vipin Sir', 'vipin@vivabranding.in', '9800000032', NULL, '$2y$12$yLqVNE/XxK2GdNQyiOf0P.TQM9lcw5FKMEqsEuNcDn9aoMZUU5MP6', 'ACTIVE', NULL, '2026-11-16 02:41:41', '2026-11-17 00:45:25'),
(14, 'Kavita Nair', 'kavita.nair@vivabranding.in', '9800000041', NULL, '$2y$12$yLqVNE/XxK2GdNQyiOf0P.TQM9lcw5FKMEqsEuNcDn9aoMZUU5MP6', 'ACTIVE', NULL, '2026-11-16 02:41:42', '2026-11-17 00:45:25'),
(15, 'Manoj Gupta', 'manoj.gupta@vivabranding.in', '9800000042', NULL, '$2y$12$yLqVNE/XxK2GdNQyiOf0P.TQM9lcw5FKMEqsEuNcDn9aoMZUU5MP6', 'ACTIVE', NULL, '2026-11-16 02:41:43', '2026-11-17 00:45:25'),
(16, 'Deepak Verma', 'deepak.verma@vivabranding.in', '9800000043', NULL, '$2y$12$yLqVNE/XxK2GdNQyiOf0P.TQM9lcw5FKMEqsEuNcDn9aoMZUU5MP6', 'ACTIVE', NULL, '2026-11-16 02:41:43', '2026-11-17 00:45:25'),
(17, 'Prashant Nilakhe', 'prashant.nilakhe@vivabranding.in', '9800000051', NULL, '$2y$12$yLqVNE/XxK2GdNQyiOf0P.TQM9lcw5FKMEqsEuNcDn9aoMZUU5MP6', 'ACTIVE', NULL, '2026-11-16 07:31:43', '2026-11-17 00:45:25'),
(18, 'Sneha Kapoor', 'sneha.kapoor@vivabranding.in', '9800000052', NULL, '$2y$12$yLqVNE/XxK2GdNQyiOf0P.TQM9lcw5FKMEqsEuNcDn9aoMZUU5MP6', 'ACTIVE', NULL, '2026-11-16 07:31:44', '2026-11-17 00:45:25'),
(19, 'Ankit Jain', 'ankit.jain@vivabranding.in', '9800000061', NULL, '$2y$12$yLqVNE/XxK2GdNQyiOf0P.TQM9lcw5FKMEqsEuNcDn9aoMZUU5MP6', 'ACTIVE', NULL, '2026-11-16 07:31:44', '2026-11-17 00:45:25'),
(20, 'Mahesh Trivedi', 'mahesh.trivedi@vivabranding.in', '9800000071', NULL, '$2y$12$yLqVNE/XxK2GdNQyiOf0P.TQM9lcw5FKMEqsEuNcDn9aoMZUU5MP6', 'ACTIVE', NULL, '2026-11-16 07:31:44', '2026-11-17 00:45:25'),
(21, 'Neha Agarwal', 'neha.agarwal@vivabranding.in', '9800000081', NULL, '$2y$12$yLqVNE/XxK2GdNQyiOf0P.TQM9lcw5FKMEqsEuNcDn9aoMZUU5MP6', 'ACTIVE', NULL, '2026-11-16 07:31:45', '2026-11-17 00:45:25'),
(22, 'Ravi Yadav', 'ravi.yadav@vivabranding.in', '9800000091', NULL, '$2y$12$yLqVNE/XxK2GdNQyiOf0P.TQM9lcw5FKMEqsEuNcDn9aoMZUU5MP6', 'ACTIVE', NULL, '2026-11-16 07:31:45', '2026-11-17 00:45:25'),
(23, 'Imran Shaikh', 'imran.shaikh@vivabranding.in', '9800000092', NULL, '$2y$12$yLqVNE/XxK2GdNQyiOf0P.TQM9lcw5FKMEqsEuNcDn9aoMZUU5MP6', 'ACTIVE', NULL, '2026-11-16 07:31:45', '2026-11-17 00:45:26'),
(25, 'Pooja Rao', 'pooja.rao@vivabranding.in', '9800000062', NULL, '$2y$12$yLqVNE/XxK2GdNQyiOf0P.TQM9lcw5FKMEqsEuNcDn9aoMZUU5MP6', 'ACTIVE', NULL, '2026-11-16 07:39:21', '2026-11-17 00:45:26'),
(26, 'Kiran Bhatt', 'kiran.bhatt@vivabranding.in', '9800000072', NULL, '$2y$12$yLqVNE/XxK2GdNQyiOf0P.TQM9lcw5FKMEqsEuNcDn9aoMZUU5MP6', 'ACTIVE', NULL, '2026-11-16 07:39:22', '2026-11-17 00:45:26'),
(27, 'Jignesh Shah', 'jignesh.shah@vivabranding.in', '9800000082', NULL, '$2y$12$yLqVNE/XxK2GdNQyiOf0P.TQM9lcw5FKMEqsEuNcDn9aoMZUU5MP6', 'ACTIVE', NULL, '2026-11-16 07:39:22', '2026-11-17 00:45:26');

-- --------------------------------------------------------

--
-- Table structure for table `zones`
--

DROP TABLE IF EXISTS `zones`;
CREATE TABLE IF NOT EXISTS `zones` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('ACTIVE','INACTIVE') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVE',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `zones_name_unique` (`name`),
  KEY `zones_created_by_foreign` (`created_by`),
  KEY `zones_updated_by_foreign` (`updated_by`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `zones`
--

INSERT INTO `zones` (`id`, `name`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'North', 'ACTIVE', 1, 1, '2026-11-13 04:58:20', '2026-11-13 04:58:20'),
(2, 'South', 'ACTIVE', 1, 1, '2026-11-13 04:58:20', '2026-11-13 04:58:20'),
(3, 'East', 'ACTIVE', 1, 1, '2026-11-13 04:58:20', '2026-11-13 04:58:20'),
(4, 'West', 'ACTIVE', 1, 1, '2026-11-13 04:58:20', '2026-11-13 04:58:20'),
(5, 'Central', 'ACTIVE', 1, 1, '2026-11-13 04:58:20', '2026-11-13 04:58:20');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `approvals`
--
ALTER TABLE `approvals`
  ADD CONSTRAINT `approvals_request_id_foreign` FOREIGN KEY (`request_id`) REFERENCES `requests` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `approvals_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `branding_materials`
--
ALTER TABLE `branding_materials`
  ADD CONSTRAINT `branding_materials_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `branding_materials_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `catalogue_items`
--
ALTER TABLE `catalogue_items`
  ADD CONSTRAINT `catalogue_items_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `catalogue_items_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `departments`
--
ALTER TABLE `departments`
  ADD CONSTRAINT `departments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `departments_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `departments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `dispatches`
--
ALTER TABLE `dispatches`
  ADD CONSTRAINT `dispatches_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `dispatches_team_task_id_foreign` FOREIGN KEY (`team_task_id`) REFERENCES `team_tasks` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dispatches_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `events_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `event_types`
--
ALTER TABLE `event_types`
  ADD CONSTRAINT `event_types_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `event_types_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `event_types_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `event_type_gift_item`
--
ALTER TABLE `event_type_gift_item`
  ADD CONSTRAINT `event_type_gift_item_event_type_id_foreign` FOREIGN KEY (`event_type_id`) REFERENCES `event_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `event_type_gift_item_gift_item_id_foreign` FOREIGN KEY (`gift_item_id`) REFERENCES `gift_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `gift_items`
--
ALTER TABLE `gift_items`
  ADD CONSTRAINT `gift_items_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `gift_items_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `guest_categories`
--
ALTER TABLE `guest_categories`
  ADD CONSTRAINT `guest_categories_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `guest_categories_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `hotels`
--
ALTER TABLE `hotels`
  ADD CONSTRAINT `hotels_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hotels_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `locations`
--
ALTER TABLE `locations`
  ADD CONSTRAINT `locations_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `locations_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `meet_proposals`
--
ALTER TABLE `meet_proposals`
  ADD CONSTRAINT `meet_proposals_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `meet_proposals_request_id_foreign` FOREIGN KEY (`request_id`) REFERENCES `requests` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `meet_proposals_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `meet_proposal_options`
--
ALTER TABLE `meet_proposal_options`
  ADD CONSTRAINT `meet_proposal_options_hotel_id_foreign` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `meet_proposal_options_meet_proposal_id_foreign` FOREIGN KEY (`meet_proposal_id`) REFERENCES `meet_proposals` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payments_team_task_id_foreign` FOREIGN KEY (`team_task_id`) REFERENCES `team_tasks` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `permissions`
--
ALTER TABLE `permissions`
  ADD CONSTRAINT `permissions_permissiongroup_id_foreign` FOREIGN KEY (`permissiongroup_id`) REFERENCES `permissiongroups` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pop_items`
--
ALTER TABLE `pop_items`
  ADD CONSTRAINT `pop_items_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pop_items_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `product_samples`
--
ALTER TABLE `product_samples`
  ADD CONSTRAINT `product_samples_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `product_samples_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD CONSTRAINT `purchase_orders_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `purchase_orders_team_task_id_foreign` FOREIGN KEY (`team_task_id`) REFERENCES `team_tasks` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_orders_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `requests`
--
ALTER TABLE `requests`
  ADD CONSTRAINT `requests_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `requests_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `requests_event_type_id_foreign` FOREIGN KEY (`event_type_id`) REFERENCES `event_types` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `requests_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `requests_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `requests_zone_id_foreign` FOREIGN KEY (`zone_id`) REFERENCES `zones` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `request_documents`
--
ALTER TABLE `request_documents`
  ADD CONSTRAINT `request_documents_request_id_foreign` FOREIGN KEY (`request_id`) REFERENCES `requests` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `request_documents_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `request_guest_categories`
--
ALTER TABLE `request_guest_categories`
  ADD CONSTRAINT `request_guest_categories_guest_category_id_foreign` FOREIGN KEY (`guest_category_id`) REFERENCES `guest_categories` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `request_guest_categories_request_id_foreign` FOREIGN KEY (`request_id`) REFERENCES `requests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `request_histories`
--
ALTER TABLE `request_histories`
  ADD CONSTRAINT `request_histories_request_id_foreign` FOREIGN KEY (`request_id`) REFERENCES `requests` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `request_histories_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `request_items`
--
ALTER TABLE `request_items`
  ADD CONSTRAINT `request_items_request_id_foreign` FOREIGN KEY (`request_id`) REFERENCES `requests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `roles`
--
ALTER TABLE `roles`
  ADD CONSTRAINT `roles_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `roles_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sponsorship_deliverables`
--
ALTER TABLE `sponsorship_deliverables`
  ADD CONSTRAINT `sponsorship_deliverables_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sponsorship_deliverables_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `team_tasks`
--
ALTER TABLE `team_tasks`
  ADD CONSTRAINT `team_tasks_completed_by_foreign` FOREIGN KEY (`completed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `team_tasks_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `team_tasks_request_id_foreign` FOREIGN KEY (`request_id`) REFERENCES `requests` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `team_tasks_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `zones`
--
ALTER TABLE `zones`
  ADD CONSTRAINT `zones_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `zones_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
