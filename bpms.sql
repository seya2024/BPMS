-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 02, 2026 at 09:56 AM
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
-- Database: `bpms`
--

-- --------------------------------------------------------

--
-- Table structure for table `annual_account_plans`
--

CREATE TABLE `annual_account_plans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `annual_plan_id` bigint(20) UNSIGNED NOT NULL,
  `annual_target_accounts` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `q1_target_accounts` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `q2_target_accounts` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `q3_target_accounts` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `q4_target_accounts` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `monthly_target_accounts` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `weekly_target_accounts` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `daily_target_accounts` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `annual_account_plans`
--

INSERT INTO `annual_account_plans` (`id`, `annual_plan_id`, `annual_target_accounts`, `q1_target_accounts`, `q2_target_accounts`, `q3_target_accounts`, `q4_target_accounts`, `monthly_target_accounts`, `weekly_target_accounts`, `daily_target_accounts`, `remarks`, `created_at`, `updated_at`) VALUES
(3, 4, 100000, 25000, 25000, 25000, 25000, 8333, 1923, 319, NULL, '2026-07-02 04:19:44', '2026-07-02 04:19:44');

-- --------------------------------------------------------

--
-- Table structure for table `annual_deposit_plans`
--

CREATE TABLE `annual_deposit_plans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `annual_plan_id` bigint(20) UNSIGNED NOT NULL,
  `annual_target_amount` decimal(20,2) DEFAULT NULL,
  `q1_target_amount` decimal(20,2) DEFAULT NULL,
  `q2_target_amount` decimal(20,2) DEFAULT NULL,
  `q3_target_amount` decimal(20,2) DEFAULT NULL,
  `q4_target_amount` decimal(20,2) DEFAULT NULL,
  `daily_target_amount` decimal(20,2) DEFAULT NULL,
  `monthly_target_amount` decimal(18,2) DEFAULT NULL,
  `weekly_target_amount` decimal(18,2) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `annual_deposit_plans`
--

INSERT INTO `annual_deposit_plans` (`id`, `annual_plan_id`, `annual_target_amount`, `q1_target_amount`, `q2_target_amount`, `q3_target_amount`, `q4_target_amount`, `daily_target_amount`, `monthly_target_amount`, `weekly_target_amount`, `remarks`, `created_at`, `updated_at`) VALUES
(6, 4, 200000000.00, 50000000.00, 50000000.00, 50000000.00, 50000000.00, 638977.64, 16666666.67, 3846153.85, NULL, '2026-07-02 04:17:44', '2026-07-02 04:17:44');

-- --------------------------------------------------------

--
-- Table structure for table `annual_plans`
--

CREATE TABLE `annual_plans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `financial_year_id` bigint(20) UNSIGNED NOT NULL,
  `district_id` bigint(20) UNSIGNED NOT NULL,
  `deposit` decimal(20,2) DEFAULT NULL,
  `account` int(10) UNSIGNED DEFAULT NULL,
  `supperappsubscription` int(10) UNSIGNED DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `annual_plans`
--

INSERT INTO `annual_plans` (`id`, `financial_year_id`, `district_id`, `deposit`, `account`, `supperappsubscription`, `remarks`, `created_by`, `created_at`, `updated_at`) VALUES
(4, 5, 1, 1000000000.00, 50000, 20000, NULL, NULL, '2026-07-02 04:15:13', '2026-07-02 04:15:13'),
(5, 5, 9, 500000000.00, 30000, 20000, NULL, NULL, '2026-07-02 04:17:04', '2026-07-02 04:17:04');

-- --------------------------------------------------------

--
-- Table structure for table `banking_types`
--

CREATE TABLE `banking_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `banking_types`
--

INSERT INTO `banking_types` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Conventional Banking', '2026-06-29 09:44:32', '2026-06-29 09:44:32'),
(2, 'Islamic Banking (IFB)', '2026-06-29 09:44:32', '2026-06-29 09:44:32');

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `grade` varchar(255) DEFAULT NULL,
  `district_id` bigint(20) UNSIGNED NOT NULL,
  `bankingType_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`id`, `code`, `name`, `grade`, `district_id`, `bankingType_id`, `created_at`, `updated_at`) VALUES
(1, '21', 'Jimma Branch', 'II', 1, 1, '2026-01-27 08:39:29', '2026-01-27 08:39:29'),
(2, '22', 'Agaro Branch', 'I', 1, 1, '2026-01-27 08:39:29', '2026-01-27 08:39:29'),
(3, '23', 'Limmugenet Branch', 'I', 1, 1, '2026-01-27 08:39:30', '2026-01-27 08:39:30'),
(4, 'YB', 'Yebu Branch', 'I', 1, 1, '2026-01-27 08:39:30', '2026-01-27 08:39:30'),
(5, 'BL', 'Bilida Outlet', 'I', 1, 1, '2026-01-27 08:39:30', '2026-03-11 10:16:26'),
(6, 'AL', 'Al-nur IFB', 'I', 1, 2, '2026-01-27 08:39:30', '2026-01-27 08:39:30'),
(7, 'CH', 'Gecha Branch', 'I', 1, 1, '2026-01-27 08:39:30', '2026-01-27 08:39:30'),
(8, 'BD', 'Bedele Branch', 'I', 1, 1, '2026-01-27 08:39:30', '2026-01-27 08:39:30'),
(9, 'FR', 'Furisa Abawoga', 'I', 1, 1, '2026-01-27 08:39:30', '2026-03-11 10:20:11'),
(10, 'CHR', 'Chora Outlet', 'I', 1, 1, '2026-01-27 08:39:31', '2026-03-11 10:18:53'),
(11, 'YY', 'Yayo Branch', 'I', 1, 1, '2026-01-27 08:39:31', '2026-01-27 08:39:31'),
(12, 'MT', 'Mettu Branch', 'I', 1, 1, '2026-01-27 08:39:31', '2026-01-27 08:39:31'),
(13, 'GH', 'Gechi Branch', 'I', 1, 1, '2026-01-27 08:39:31', '2026-01-27 08:39:31'),
(14, 'MSH', 'Masha Branch', 'I', 1, 1, '2026-01-27 08:39:31', '2026-01-27 08:39:31'),
(15, 'MTI', 'Meti Branch', 'I', 1, 1, '2026-01-27 08:39:31', '2026-01-27 08:39:31'),
(16, 'YR', 'Yeri Outlet', 'I', 1, 1, '2026-01-27 08:39:32', '2026-03-11 10:16:52'),
(17, 'SHE', 'Shebe Branch', 'I', 1, 1, '2026-01-27 08:39:32', '2026-01-27 08:39:32'),
(18, 'CHD', 'Chida Branch', 'I', 1, 1, '2026-01-27 08:39:32', '2026-01-27 08:39:32'),
(19, 'DNB', 'Deneba Branch', 'I', 1, 1, '2026-01-27 08:39:32', '2026-01-27 08:39:32'),
(20, 'SJ', 'Saja Branch', 'I', 1, 1, '2026-01-27 08:39:32', '2026-01-27 08:39:32'),
(21, 'SK', 'Sokoru Outlet', 'I', 1, 1, '2026-01-27 08:39:33', '2026-03-11 10:17:18'),
(22, 'TLY', 'Tollay Branch', 'I', 1, 1, '2026-01-27 08:39:33', '2026-01-27 08:39:33'),
(23, 'SLA', 'SilkAmba Branch', 'I', 1, 1, '2026-01-27 08:39:33', '2026-01-27 08:39:33'),
(24, 'ALF', 'Alif Branch', 'I', 1, 2, '2026-01-27 08:39:33', '2026-01-27 08:39:33'),
(26, 'HIR', 'Hirmata Branch', 'I', 1, 1, '2026-01-27 08:39:33', '2026-01-27 08:39:33'),
(27, 'MNR', 'Meneharia Branch', 'I', 1, 1, '2026-01-27 08:39:33', '2026-01-27 08:39:33'),
(28, 'IQR', 'IFB - Iqra Branch', 'I', 1, 2, '2026-01-27 08:39:34', '2026-01-27 08:39:34'),
(29, 'ABJ', 'Abajifar Branch', 'I', 1, 1, '2026-01-27 08:39:34', '2026-01-27 08:39:34'),
(30, 'ABJS', 'Abajifar Outlet', 'I', 1, 1, '2026-01-27 08:39:34', '2026-01-27 08:39:34'),
(31, 'FRJ', 'Ferenji Arada Branch', 'I', 1, 1, '2026-01-27 08:39:34', '2026-01-27 08:39:34');

-- --------------------------------------------------------

--
-- Table structure for table `business_segments`
--

CREATE TABLE `business_segments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `business_segments`
--

INSERT INTO `business_segments` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'MSME', 'MSME (Micro, Small & Medium Enterprises)\n\n\n\n\n\n', '2026-07-01 05:25:49', '2026-07-01 05:28:00'),
(2, 'Retail', 'Retail (Personal Banking)\nMeaning: Individual customers, not businesses.', '2026-07-01 05:26:03', '2026-07-01 05:28:23'),
(3, 'Corporate', 'Large, structured companies with formal governance and high financial capacity.', '2026-07-01 05:26:16', '2026-07-01 05:28:44');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('branch-performance-management-systembpms-cache-livewire-rate-limiter:16d36dff9abd246c67dfac3e63b993a169af77e6', 'i:1;', 1782972251),
('branch-performance-management-systembpms-cache-livewire-rate-limiter:16d36dff9abd246c67dfac3e63b993a169af77e6:timer', 'i:1782972251;', 1782972251);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `districts`
--

CREATE TABLE `districts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `location_type` enum('City','Upcountry') DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `districts`
--

INSERT INTO `districts` (`id`, `name`, `location_type`, `created_at`, `updated_at`) VALUES
(1, 'Jimma', 'Upcountry', '2026-01-27 11:39:27', '2026-01-27 11:39:27'),
(2, 'South West', 'Upcountry', '2026-01-27 11:39:27', '2026-01-27 11:39:27'),
(3, 'Nekemte', 'Upcountry', '2026-01-27 11:39:27', '2026-01-27 11:39:27'),
(4, 'Hawasa', 'Upcountry', '2026-01-27 11:39:27', '2026-01-27 11:39:27'),
(5, 'Adama', 'Upcountry', '2026-01-27 11:39:27', '2026-01-27 11:39:27'),
(6, 'Dire Dawa', 'Upcountry', '2026-01-27 11:39:27', '2026-01-27 11:39:27'),
(7, 'Mekelle', 'Upcountry', '2026-01-27 11:39:27', '2026-04-02 11:48:30'),
(8, 'Dessie', 'Upcountry', '2026-01-27 11:39:27', '2026-01-27 11:39:27'),
(9, 'Bahir Dar', 'Upcountry', '2026-01-27 11:39:27', '2026-01-27 11:39:27'),
(10, 'North Addis', 'City', '2026-01-27 11:39:27', '2026-01-27 11:39:27'),
(11, 'East Addis', 'City', '2026-01-27 11:39:27', '2026-01-27 11:39:27'),
(12, 'West Addis', 'City', '2026-01-27 11:39:27', '2026-01-27 11:39:27'),
(13, 'South Addis', 'City', '2026-01-27 11:39:27', '2026-01-27 11:39:27'),
(14, 'Wolaita', 'Upcountry', '2026-01-27 11:39:27', '2026-01-27 11:39:27'),
(15, 'Head Office', 'City', '2026-01-27 11:39:27', '2026-01-27 11:39:27');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `financial_periods`
--

CREATE TABLE `financial_periods` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `financial_year_id` bigint(20) UNSIGNED NOT NULL,
  `quarter` tinyint(4) NOT NULL,
  `label` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('OPEN','CLOSED') NOT NULL DEFAULT 'OPEN',
  `closed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `financial_periods`
--

INSERT INTO `financial_periods` (`id`, `financial_year_id`, `quarter`, `label`, `start_date`, `end_date`, `status`, `closed_at`, `created_at`, `updated_at`) VALUES
(28, 5, 1, 'Q1-FY-2026/27', '2026-07-01', '2026-09-30', 'OPEN', NULL, '2026-07-02 04:13:31', '2026-07-02 04:13:31'),
(29, 5, 2, 'Q2-FY-2026/27', '2026-10-01', '2026-12-31', 'OPEN', NULL, '2026-07-02 04:13:31', '2026-07-02 04:13:31'),
(30, 5, 3, 'Q3-FY-2026/27', '2027-01-01', '2027-03-31', 'OPEN', NULL, '2026-07-02 04:13:31', '2026-07-02 04:13:31'),
(31, 5, 4, 'Q4-FY-2026/27', '2027-04-01', '2027-06-30', 'OPEN', NULL, '2026-07-02 04:13:31', '2026-07-02 04:13:31');

-- --------------------------------------------------------

--
-- Table structure for table `financial_years`
--

CREATE TABLE `financial_years` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('OPEN','CLOSING','CLOSED') NOT NULL DEFAULT 'OPEN',
  `opened_at` timestamp NULL DEFAULT NULL,
  `closed_at` timestamp NULL DEFAULT NULL,
  `closed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `financial_years`
--

INSERT INTO `financial_years` (`id`, `name`, `start_date`, `end_date`, `status`, `opened_at`, `closed_at`, `closed_by`, `created_at`, `updated_at`) VALUES
(5, 'FY2026/27', '2026-07-01', '2027-06-30', 'OPEN', NULL, NULL, NULL, '2026-07-02 04:13:15', '2026-07-02 04:13:15');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `k_p_i_categories`
--

CREATE TABLE `k_p_i_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `k_p_i_categories`
--

INSERT INTO `k_p_i_categories` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Customer Acquisition', NULL, '2026-06-30 06:50:02', '2026-06-30 06:50:02'),
(2, 'Deposit Mobilization', NULL, '2026-06-30 06:50:15', '2026-06-30 06:50:15'),
(3, 'Digital Banking', NULL, '2026-06-30 06:50:28', '2026-06-30 06:50:28'),
(4, 'Service Quality', NULL, '2026-06-30 06:50:54', '2026-06-30 06:50:54');

-- --------------------------------------------------------

--
-- Table structure for table `k_p_i_s`
--

CREATE TABLE `k_p_i_s` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `unit` varchar(255) NOT NULL,
  `calculation_method` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `k_p_i_s`
--

INSERT INTO `k_p_i_s` (`id`, `name`, `category_id`, `unit`, `calculation_method`, `created_at`, `updated_at`) VALUES
(1, 'Account Opening', 1, 'count', 'Existing plus new Active account', '2026-07-01 01:55:23', '2026-07-01 01:55:46'),
(2, 'Deposit', 2, 'amount', 'Last Jun plus new deposit', '2026-07-01 01:56:41', '2026-07-01 01:56:41');

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
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_06_29_084523_create_districts_table', 2),
(5, '2026_06_29_085037_create_branches_table', 2),
(6, '2026_06_29_123227_create_banking_types_table', 3),
(7, '2026_06_29_125136_add_banking_type_to_branches_table', 4),
(8, '2026_06_30_090356_create_k_p_i_categories_table', 5),
(9, '2026_06_30_080415_create_k_p_i_s_table', 6),
(10, '2026_06_30_084702_create_financial_years_table', 7),
(11, '2026_06_30_085241_create_financial_periods_table', 8),
(12, '2026_06_30_080417_create_business_segments_table', 9),
(13, '2026_06_30_090360_create_annual_plans_table', 10),
(15, '2026_07_01_130640_create_annual_account_plans_table', 11);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
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

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('QpaubFxKrECjjKstTBMdjE3vdMMF3UzuyfBxoVZI', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:152.0) Gecko/20100101 Firefox/152.0', 'YTo3OntzOjY6Il90b2tlbiI7czo0MDoiR05XYmZuU29PeDFQNDc3aEdNR2JDREdrdWY1UFlnbnRJOXBsRXRKbyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9hbm51YWwtYWNjb3VudC1wbGFucyI7czo1OiJyb3V0ZSI7czo1MToiZmlsYW1lbnQuYWRtaW4ucmVzb3VyY2VzLmFubnVhbC1hY2NvdW50LXBsYW5zLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjE3OiJwYXNzd29yZF9oYXNoX3dlYiI7czo2NDoiN2IzYjBkZDg0NThlNWQzZmY1NTQ5MmQ3ZjFkZTQ4ZDFkYmRmYjNiMTQ0OWZmOGU2MzdjMjEzMTJjZGE3NGNjYSI7czo2OiJ0YWJsZXMiO2E6MTA6e3M6NDA6IjEyOTljZjk5NTIxMmQ0MzMzMmUwNGFhZjk2YTFlNzI5X2NvbHVtbnMiO2E6Nzp7aTowO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjE4OiJmaW5hbmNpYWxZZWFyLm5hbWUiO3M6NToibGFiZWwiO3M6MTQ6IkZpbmFuY2lhbCBZZWFyIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMzoiZGlzdHJpY3QubmFtZSI7czo1OiJsYWJlbCI7czo4OiJEaXN0cmljdCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjI7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NzoiZGVwb3NpdCI7czo1OiJsYWJlbCI7czo3OiJEZXBvc2l0IjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo3OiJhY2NvdW50IjtzOjU6ImxhYmVsIjtzOjg6IkFjY291bnRzIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoyMToic3VwcGVyYXBwc3Vic2NyaXB0aW9uIjtzOjU6ImxhYmVsIjtzOjIzOiJTdXBlciBBcHAgU3Vic2NyaXB0aW9ucyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjU7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTI6ImNyZWF0b3IubmFtZSI7czo1OiJsYWJlbCI7czoxMDoiQ3JlYXRlZCBCeSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjY7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTA6ImNyZWF0ZWRfYXQiO3M6NToibGFiZWwiO3M6MTI6IkNyZWF0ZWQgRGF0ZSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO319czo0MDoiYWM5ZmRiOTViYjI1NGUzY2I0MTcwMDlhMTUzYzg2ZGJfY29sdW1ucyI7YToxMDp7aTowO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjI5OiJhbm51YWxQbGFuLmZpbmFuY2lhbFllYXIubmFtZSI7czo1OiJsYWJlbCI7czoxNDoiRmluYW5jaWFsIFllYXIiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToxO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjI0OiJhbm51YWxQbGFuLmRpc3RyaWN0Lm5hbWUiO3M6NToibGFiZWwiO3M6ODoiRGlzdHJpY3QiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToyO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjIwOiJhbm51YWxfdGFyZ2V0X2Ftb3VudCI7czo1OiJsYWJlbCI7czoxMzoiQW5udWFsIFRhcmdldCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjM7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTY6InExX3RhcmdldF9hbW91bnQiO3M6NToibGFiZWwiO3M6MjoiUTEiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo0O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjE2OiJxMl90YXJnZXRfYW1vdW50IjtzOjU6ImxhYmVsIjtzOjI6IlEyIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxNjoicTNfdGFyZ2V0X2Ftb3VudCI7czo1OiJsYWJlbCI7czoyOiJRMyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjY7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTY6InE0X3RhcmdldF9hbW91bnQiO3M6NToibGFiZWwiO3M6MjoiUTQiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo3O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjIxOiJtb250aGx5X3RhcmdldF9hbW91bnQiO3M6NToibGFiZWwiO3M6NzoiTW9udGhseSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjg7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MjA6IndlZWtseV90YXJnZXRfYW1vdW50IjtzOjU6ImxhYmVsIjtzOjY6IldlZWtseSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjk7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTk6ImRhaWx5X3RhcmdldF9hbW91bnQiO3M6NToibGFiZWwiO3M6NToiRGFpbHkiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9fXM6NDA6IjJjYWM1ZGM4MDc0MWJlMWYzYWFiOWYzMzhmZTQ2YmRiX2NvbHVtbnMiO2E6MTE6e2k6MDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoyOToiYW5udWFsUGxhbi5maW5hbmNpYWxZZWFyLm5hbWUiO3M6NToibGFiZWwiO3M6MTQ6IkZpbmFuY2lhbCBZZWFyIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoyNDoiYW5udWFsUGxhbi5kaXN0cmljdC5uYW1lIjtzOjU6ImxhYmVsIjtzOjg6IkRpc3RyaWN0IjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoyMjoiYW5udWFsX3RhcmdldF9hY2NvdW50cyI7czo1OiJsYWJlbCI7czoxMzoiQW5udWFsIFRhcmdldCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjM7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTg6InExX3RhcmdldF9hY2NvdW50cyI7czo1OiJsYWJlbCI7czoyOiJRMSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjQ7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTg6InEyX3RhcmdldF9hY2NvdW50cyI7czo1OiJsYWJlbCI7czoyOiJRMiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjU7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTg6InEzX3RhcmdldF9hY2NvdW50cyI7czo1OiJsYWJlbCI7czoyOiJRMyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjY7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTg6InE0X3RhcmdldF9hY2NvdW50cyI7czo1OiJsYWJlbCI7czoyOiJRNCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjc7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MjM6Im1vbnRobHlfdGFyZ2V0X2FjY291bnRzIjtzOjU6ImxhYmVsIjtzOjc6Ik1vbnRobHkiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo4O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjIyOiJ3ZWVrbHlfdGFyZ2V0X2FjY291bnRzIjtzOjU6ImxhYmVsIjtzOjY6IldlZWtseSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjk7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MjE6ImRhaWx5X3RhcmdldF9hY2NvdW50cyI7czo1OiJsYWJlbCI7czo1OiJEYWlseSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjEwO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjIzOiJhbm51YWxQbGFuLmNyZWF0b3IubmFtZSI7czo1OiJsYWJlbCI7czoxMDoiQ3JlYXRlZCBCeSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO319czo0MDoiZmE0YjdhM2QxMWI2NzAwMGJiNDc1OTgyY2Y1M2E1YTVfY29sdW1ucyI7YTo5OntpOjA7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NDoibmFtZSI7czo1OiJsYWJlbCI7czoxNDoiRmluYW5jaWFsIFllYXIiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToxO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEwOiJzdGFydF9kYXRlIjtzOjU6ImxhYmVsIjtzOjEwOiJTdGFydCBkYXRlIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo4OiJlbmRfZGF0ZSI7czo1OiJsYWJlbCI7czo4OiJFbmQgZGF0ZSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjM7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6Njoic3RhdHVzIjtzOjU6ImxhYmVsIjtzOjY6IlN0YXR1cyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjQ7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6OToib3BlbmVkX2F0IjtzOjU6ImxhYmVsIjtzOjk6Ik9wZW5lZCBhdCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fWk6NTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo5OiJjbG9zZWRfYXQiO3M6NToibGFiZWwiO3M6OToiQ2xvc2VkIGF0IjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MDtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MTt9aTo2O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEzOiJjbG9zZWRCeS5uYW1lIjtzOjU6ImxhYmVsIjtzOjk6IkNsb3NlZCBieSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjc7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTA6ImNyZWF0ZWRfYXQiO3M6NToibGFiZWwiO3M6MTA6IkNyZWF0ZWQgYXQiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO31pOjg7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTA6InVwZGF0ZWRfYXQiO3M6NToibGFiZWwiO3M6MTA6IlVwZGF0ZWQgYXQiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO319czo0MDoiOGVkMTBiMmYzNTAyMzg1ZDlmYTcwNTgzYWUwM2ZlZjJfY29sdW1ucyI7YTo5OntpOjA7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTg6ImZpbmFuY2lhbFllYXIubmFtZSI7czo1OiJsYWJlbCI7czoxNDoiRmluYW5jaWFsIFllYXIiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToxO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjc6InF1YXJ0ZXIiO3M6NToibGFiZWwiO3M6NzoiUXVhcnRlciI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjI7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NToibGFiZWwiO3M6NToibGFiZWwiO3M6NjoiUGVyaW9kIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMDoic3RhcnRfZGF0ZSI7czo1OiJsYWJlbCI7czoxMDoiU3RhcnQgRGF0ZSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjQ7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6ODoiZW5kX2RhdGUiO3M6NToibGFiZWwiO3M6ODoiRW5kIERhdGUiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo1O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjY6InN0YXR1cyI7czo1OiJsYWJlbCI7czo2OiJTdGF0dXMiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo2O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjk6ImNsb3NlZF9hdCI7czo1OiJsYWJlbCI7czo5OiJDbG9zZWQgQXQiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo3O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEwOiJjcmVhdGVkX2F0IjtzOjU6ImxhYmVsIjtzOjc6IkNyZWF0ZWQiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO31pOjg7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTA6InVwZGF0ZWRfYXQiO3M6NToibGFiZWwiO3M6NzoiVXBkYXRlZCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fX1zOjQwOiIzYmJmNTZkMzNmNjQ3NGRlMDU0NTk5NGQ1M2ZlOGNmNV9jb2x1bW5zIjthOjM6e2k6MDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo2OiJzZXJpYWwiO3M6NToibGFiZWwiO3M6MToiIyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjE7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NDoibmFtZSI7czo1OiJsYWJlbCI7czo0OiJOYW1lIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMToiZGVzY3JpcHRpb24iO3M6NToibGFiZWwiO3M6MTE6IkRlc2NyaXB0aW9uIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fX1zOjQwOiI4YjNlNjM0Yzk5OGNlNTFkZGJjYmVmZWY1MjkxNzRiYV9jb2x1bW5zIjthOjU6e2k6MDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo0OiJuYW1lIjtzOjU6ImxhYmVsIjtzOjg6IktQSSBOYW1lIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMzoiY2F0ZWdvcnkubmFtZSI7czo1OiJsYWJlbCI7czo4OiJDYXRlZ29yeSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjI7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NDoidW5pdCI7czo1OiJsYWJlbCI7czo0OiJVbml0IjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxODoiY2FsY3VsYXRpb25fbWV0aG9kIjtzOjU6ImxhYmVsIjtzOjE4OiJDYWxjdWxhdGlvbiBNZXRob2QiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo0O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEwOiJjcmVhdGVkX2F0IjtzOjU6ImxhYmVsIjtzOjEwOiJDcmVhdGVkIGF0IjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MDtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MTt9fXM6NDA6ImZkMzVkMDYxZTNiMTY2M2VlNzM0NDgyZjc4ZTRkNTc0X2NvbHVtbnMiO2E6NDp7aTowO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjQ6Im5hbWUiO3M6NToibGFiZWwiO3M6NzoiU2VnbWVudCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjE7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTE6ImRlc2NyaXB0aW9uIjtzOjU6ImxhYmVsIjtzOjExOiJEZXNjcmlwdGlvbiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjI7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTA6ImNyZWF0ZWRfYXQiO3M6NToibGFiZWwiO3M6MTA6IkNyZWF0ZWQgYXQiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO31pOjM7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTA6InVwZGF0ZWRfYXQiO3M6NToibGFiZWwiO3M6MTA6IlVwZGF0ZWQgYXQiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO319czo0MDoiZjc2YjgxMmY2ZGI3MGRjOTRkMTNlNGNhNTgxOTM2MTlfY29sdW1ucyI7YToyOntpOjA7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6Njoic2VyaWFsIjtzOjU6ImxhYmVsIjtzOjE6IiMiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToxO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjQ6Im5hbWUiO3M6NToibGFiZWwiO3M6MTM6IiBCYW5raW5nIFR5cGUiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9fXM6NDE6ImFjOWZkYjk1YmIyNTRlM2NiNDE3MDA5YTE1M2M4NmRiX3Blcl9wYWdlIjtzOjE6IjUiO31zOjg6ImZpbGFtZW50IjthOjA6e319', 1782976793);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Seid Mohammed', 'seidm2031@gmail.com', '2026-06-29 08:41:26', '$2y$12$UIDV4KLSwaiBzFKBrZyCu.plf1iC/K1F83iCCj2jxSLcH0RSZgLbC', NULL, '2026-06-29 04:37:59', '2026-06-29 04:37:59');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `annual_account_plans`
--
ALTER TABLE `annual_account_plans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `annual_account_plans_annual_plan_id_foreign` (`annual_plan_id`);

--
-- Indexes for table `annual_deposit_plans`
--
ALTER TABLE `annual_deposit_plans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `annual_plans`
--
ALTER TABLE `annual_plans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `annual_plans_financial_year_id_district_id_unique` (`financial_year_id`,`district_id`),
  ADD KEY `annual_plans_district_id_foreign` (`district_id`),
  ADD KEY `annual_plans_created_by_foreign` (`created_by`);

--
-- Indexes for table `banking_types`
--
ALTER TABLE `banking_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `branches_district_id_foreign` (`district_id`),
  ADD KEY `branches_bankingtype_id_foreign` (`bankingType_id`);

--
-- Indexes for table `business_segments`
--
ALTER TABLE `business_segments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `business_segments_name_unique` (`name`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `districts`
--
ALTER TABLE `districts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `financial_periods`
--
ALTER TABLE `financial_periods`
  ADD PRIMARY KEY (`id`),
  ADD KEY `financial_periods_financial_year_id_foreign` (`financial_year_id`);

--
-- Indexes for table `financial_years`
--
ALTER TABLE `financial_years`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `financial_years_name_unique` (`name`),
  ADD KEY `financial_years_closed_by_foreign` (`closed_by`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `k_p_i_categories`
--
ALTER TABLE `k_p_i_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `k_p_i_s`
--
ALTER TABLE `k_p_i_s`
  ADD PRIMARY KEY (`id`),
  ADD KEY `k_p_i_s_category_id_foreign` (`category_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `annual_account_plans`
--
ALTER TABLE `annual_account_plans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `annual_deposit_plans`
--
ALTER TABLE `annual_deposit_plans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `annual_plans`
--
ALTER TABLE `annual_plans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `banking_types`
--
ALTER TABLE `banking_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `business_segments`
--
ALTER TABLE `business_segments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `districts`
--
ALTER TABLE `districts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `financial_periods`
--
ALTER TABLE `financial_periods`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `financial_years`
--
ALTER TABLE `financial_years`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `k_p_i_categories`
--
ALTER TABLE `k_p_i_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `k_p_i_s`
--
ALTER TABLE `k_p_i_s`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `annual_account_plans`
--
ALTER TABLE `annual_account_plans`
  ADD CONSTRAINT `annual_account_plans_annual_plan_id_foreign` FOREIGN KEY (`annual_plan_id`) REFERENCES `annual_plans` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `annual_plans`
--
ALTER TABLE `annual_plans`
  ADD CONSTRAINT `annual_plans_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `annual_plans_district_id_foreign` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `annual_plans_financial_year_id_foreign` FOREIGN KEY (`financial_year_id`) REFERENCES `financial_years` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `branches`
--
ALTER TABLE `branches`
  ADD CONSTRAINT `branches_bankingtype_id_foreign` FOREIGN KEY (`bankingType_id`) REFERENCES `banking_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `branches_district_id_foreign` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `financial_periods`
--
ALTER TABLE `financial_periods`
  ADD CONSTRAINT `financial_periods_financial_year_id_foreign` FOREIGN KEY (`financial_year_id`) REFERENCES `financial_years` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `financial_years`
--
ALTER TABLE `financial_years`
  ADD CONSTRAINT `financial_years_closed_by_foreign` FOREIGN KEY (`closed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `k_p_i_s`
--
ALTER TABLE `k_p_i_s`
  ADD CONSTRAINT `k_p_i_s_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `k_p_i_categories` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
