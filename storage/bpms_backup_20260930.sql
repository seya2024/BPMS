-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: bpms
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `annual_account_plans`
--

DROP TABLE IF EXISTS `annual_account_plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `annual_account_plans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `annual_plan_id` bigint(20) unsigned NOT NULL,
  `annual_target_accounts` int(10) unsigned NOT NULL DEFAULT 0,
  `q1_target_accounts` int(10) unsigned NOT NULL DEFAULT 0,
  `q2_target_accounts` int(10) unsigned NOT NULL DEFAULT 0,
  `q3_target_accounts` int(10) unsigned NOT NULL DEFAULT 0,
  `q4_target_accounts` int(10) unsigned NOT NULL DEFAULT 0,
  `monthly_target_accounts` int(10) unsigned NOT NULL DEFAULT 0,
  `weekly_target_accounts` int(10) unsigned NOT NULL DEFAULT 0,
  `daily_target_accounts` int(10) unsigned NOT NULL DEFAULT 0,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `annual_account_plans_annual_plan_id_foreign` (`annual_plan_id`),
  CONSTRAINT `annual_account_plans_annual_plan_id_foreign` FOREIGN KEY (`annual_plan_id`) REFERENCES `annual_plans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `annual_account_plans`
--

LOCK TABLES `annual_account_plans` WRITE;
/*!40000 ALTER TABLE `annual_account_plans` DISABLE KEYS */;
INSERT INTO `annual_account_plans` VALUES (3,4,100000,25000,25000,25000,25000,8333,1923,319,NULL,'2026-09-30 13:56:20','2026-09-30 13:56:20');
/*!40000 ALTER TABLE `annual_account_plans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `annual_deposit_plans`
--

DROP TABLE IF EXISTS `annual_deposit_plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `annual_deposit_plans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `annual_plan_id` bigint(20) unsigned NOT NULL,
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
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `annual_deposit_plans_annual_plan_id_foreign` (`annual_plan_id`),
  CONSTRAINT `annual_deposit_plans_annual_plan_id_foreign` FOREIGN KEY (`annual_plan_id`) REFERENCES `annual_plans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `annual_deposit_plans`
--

LOCK TABLES `annual_deposit_plans` WRITE;
/*!40000 ALTER TABLE `annual_deposit_plans` DISABLE KEYS */;
INSERT INTO `annual_deposit_plans` VALUES (6,4,200000000.00,50000000.00,50000000.00,50000000.00,50000000.00,638977.64,16666666.67,3846153.85,NULL,'2026-09-30 13:56:20','2026-09-30 13:56:20');
/*!40000 ALTER TABLE `annual_deposit_plans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `annual_plans`
--

DROP TABLE IF EXISTS `annual_plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `annual_plans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `financial_year_id` bigint(20) unsigned NOT NULL,
  `district_id` bigint(20) unsigned NOT NULL,
  `deposit` decimal(20,2) DEFAULT NULL,
  `account` int(10) unsigned DEFAULT NULL,
  `supperappsubscription` int(10) unsigned DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `approval_status` varchar(255) NOT NULL DEFAULT 'draft',
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approval_remarks` text DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `annual_plans_financial_year_id_district_id_unique` (`financial_year_id`,`district_id`),
  KEY `annual_plans_district_id_foreign` (`district_id`),
  KEY `annual_plans_created_by_foreign` (`created_by`),
  KEY `annual_plans_approved_by_foreign` (`approved_by`),
  CONSTRAINT `annual_plans_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `annual_plans_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `annual_plans_district_id_foreign` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `annual_plans_financial_year_id_foreign` FOREIGN KEY (`financial_year_id`) REFERENCES `financial_years` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `annual_plans`
--

LOCK TABLES `annual_plans` WRITE;
/*!40000 ALTER TABLE `annual_plans` DISABLE KEYS */;
INSERT INTO `annual_plans` VALUES (4,5,1,1000000000.00,50000,20000,NULL,'draft',NULL,NULL,NULL,1,'2026-09-30 13:56:20','2026-09-30 13:56:20'),(5,5,9,500000000.00,30000,20000,NULL,'draft',NULL,NULL,NULL,1,'2026-09-30 13:56:20','2026-09-30 13:56:20');
/*!40000 ALTER TABLE `annual_plans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `banking_types`
--

DROP TABLE IF EXISTS `banking_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `banking_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `banking_types`
--

LOCK TABLES `banking_types` WRITE;
/*!40000 ALTER TABLE `banking_types` DISABLE KEYS */;
INSERT INTO `banking_types` VALUES (1,'Conventional','2026-09-30 13:56:20','2026-09-30 13:56:20'),(2,'IFB','2026-09-30 13:56:20','2026-09-30 13:56:20');
/*!40000 ALTER TABLE `banking_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `branch_account_plans`
--

DROP TABLE IF EXISTS `branch_account_plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `branch_account_plans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `annual_account_plan_id` bigint(20) unsigned NOT NULL,
  `branch_id` bigint(20) unsigned NOT NULL,
  `annual_target_accounts` int(10) unsigned DEFAULT NULL,
  `q1_target_accounts` int(10) unsigned DEFAULT NULL,
  `q2_target_accounts` int(10) unsigned DEFAULT NULL,
  `q3_target_accounts` int(10) unsigned DEFAULT NULL,
  `q4_target_accounts` int(10) unsigned DEFAULT NULL,
  `monthly_target_accounts` int(10) unsigned DEFAULT NULL,
  `weekly_target_accounts` int(10) unsigned DEFAULT NULL,
  `daily_target_accounts` int(10) unsigned DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `approval_status` varchar(255) NOT NULL DEFAULT 'draft',
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approval_remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `branch_account_plans_annual_account_plan_id_branch_id_unique` (`annual_account_plan_id`,`branch_id`),
  KEY `branch_account_plans_branch_id_foreign` (`branch_id`),
  KEY `branch_account_plans_approved_by_foreign` (`approved_by`),
  CONSTRAINT `branch_account_plans_annual_account_plan_id_foreign` FOREIGN KEY (`annual_account_plan_id`) REFERENCES `annual_account_plans` (`id`) ON DELETE CASCADE,
  CONSTRAINT `branch_account_plans_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `branch_account_plans_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `branch_account_plans`
--

LOCK TABLES `branch_account_plans` WRITE;
/*!40000 ALTER TABLE `branch_account_plans` DISABLE KEYS */;
/*!40000 ALTER TABLE `branch_account_plans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `branch_deposit_plans`
--

DROP TABLE IF EXISTS `branch_deposit_plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `branch_deposit_plans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `annual_deposit_plan_id` bigint(20) unsigned NOT NULL,
  `branch_id` bigint(20) unsigned NOT NULL,
  `annual_target_amount` decimal(20,2) DEFAULT NULL,
  `q1_target_amount` decimal(20,2) DEFAULT NULL,
  `q2_target_amount` decimal(20,2) DEFAULT NULL,
  `q3_target_amount` decimal(20,2) DEFAULT NULL,
  `q4_target_amount` decimal(20,2) DEFAULT NULL,
  `monthly_target_amount` decimal(20,2) DEFAULT NULL,
  `weekly_target_amount` decimal(20,2) DEFAULT NULL,
  `daily_target_amount` decimal(20,2) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `approval_status` varchar(255) NOT NULL DEFAULT 'draft',
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approval_remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `branch_deposit_plans_annual_deposit_plan_id_branch_id_unique` (`annual_deposit_plan_id`,`branch_id`),
  KEY `branch_deposit_plans_branch_id_foreign` (`branch_id`),
  KEY `branch_deposit_plans_approved_by_foreign` (`approved_by`),
  CONSTRAINT `branch_deposit_plans_annual_deposit_plan_id_foreign` FOREIGN KEY (`annual_deposit_plan_id`) REFERENCES `annual_deposit_plans` (`id`) ON DELETE CASCADE,
  CONSTRAINT `branch_deposit_plans_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `branch_deposit_plans_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `branch_deposit_plans`
--

LOCK TABLES `branch_deposit_plans` WRITE;
/*!40000 ALTER TABLE `branch_deposit_plans` DISABLE KEYS */;
/*!40000 ALTER TABLE `branch_deposit_plans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `branches`
--

DROP TABLE IF EXISTS `branches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `branches` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `grade` varchar(255) DEFAULT NULL,
  `district_id` bigint(20) unsigned NOT NULL,
  `bankingType_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `branches_district_id_foreign` (`district_id`),
  KEY `branches_bankingtype_id_foreign` (`bankingType_id`),
  CONSTRAINT `branches_bankingtype_id_foreign` FOREIGN KEY (`bankingType_id`) REFERENCES `banking_types` (`id`) ON DELETE CASCADE,
  CONSTRAINT `branches_district_id_foreign` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `branches`
--

LOCK TABLES `branches` WRITE;
/*!40000 ALTER TABLE `branches` DISABLE KEYS */;
INSERT INTO `branches` VALUES (1,'21','Jimma Branch','II',1,1,'2026-09-30 13:56:20','2026-09-30 13:56:20'),(2,'22','Agaro Branch','I',1,1,'2026-09-30 13:56:20','2026-09-30 13:56:20'),(3,'23','Limmugenet Branch','I',1,1,'2026-09-30 13:56:20','2026-09-30 13:56:20'),(4,'YB','Yebu Branch','I',1,1,'2026-09-30 13:56:20','2026-09-30 13:56:20'),(5,'BL','Bilida Outlet','I',1,1,'2026-09-30 13:56:20','2026-09-30 13:56:20'),(6,'AL','Al-nur IFB','I',1,2,'2026-09-30 13:56:20','2026-09-30 13:56:20'),(7,'CH','Gecha Branch','I',1,1,'2026-09-30 13:56:20','2026-09-30 13:56:20'),(8,'BD','Bedele Branch','I',1,1,'2026-09-30 13:56:20','2026-09-30 13:56:20'),(9,'FR','Furisa Abawoga','I',1,1,'2026-09-30 13:56:20','2026-09-30 13:56:20'),(10,'CHR','Chora Outlet','I',1,1,'2026-09-30 13:56:20','2026-09-30 13:56:20'),(11,'YY','Yayo Branch','I',1,1,'2026-09-30 13:56:20','2026-09-30 13:56:20'),(12,'MT','Mettu Branch','I',1,1,'2026-09-30 13:56:20','2026-09-30 13:56:20'),(13,'GH','Gechi Branch','I',1,1,'2026-09-30 13:56:20','2026-09-30 13:56:20'),(14,'MSH','Masha Branch','I',1,1,'2026-09-30 13:56:20','2026-09-30 13:56:20'),(15,'MTI','Meti Branch','I',1,1,'2026-09-30 13:56:20','2026-09-30 13:56:20'),(16,'YR','Yeri Outlet','I',1,1,'2026-09-30 13:56:20','2026-09-30 13:56:20'),(17,'SHE','Shebe Branch','I',1,1,'2026-09-30 13:56:20','2026-09-30 13:56:20'),(18,'CHD','Chida Branch','I',1,1,'2026-09-30 13:56:20','2026-09-30 13:56:20'),(19,'DNB','Deneba Branch','I',1,1,'2026-09-30 13:56:20','2026-09-30 13:56:20'),(20,'SJ','Saja Branch','I',1,1,'2026-09-30 13:56:20','2026-09-30 13:56:20'),(21,'SK','Sokoru Outlet','I',1,1,'2026-09-30 13:56:20','2026-09-30 13:56:20'),(22,'TLY','Tollay Branch','I',1,1,'2026-09-30 13:56:20','2026-09-30 13:56:20'),(23,'SLA','SilkAmba Branch','I',1,1,'2026-09-30 13:56:20','2026-09-30 13:56:20'),(24,'ALF','Alif Branch','I',1,2,'2026-09-30 13:56:20','2026-09-30 13:56:20'),(26,'HIR','Hirmata Branch','I',1,1,'2026-09-30 13:56:20','2026-09-30 13:56:20'),(27,'MNR','Meneharia Branch','I',1,1,'2026-09-30 13:56:20','2026-09-30 13:56:20'),(28,'IQR','IFB - Iqra Branch','I',1,2,'2026-09-30 13:56:20','2026-09-30 13:56:20'),(29,'ABJ','Abajifar Branch','I',1,1,'2026-09-30 13:56:20','2026-09-30 13:56:20'),(30,'ABJS','Abajifar Outlet','I',1,1,'2026-09-30 13:56:20','2026-09-30 13:56:20'),(31,'FRJ','Ferenji Arada Branch','I',1,1,'2026-09-30 13:56:20','2026-09-30 13:56:20');
/*!40000 ALTER TABLE `branches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `business_segments`
--

DROP TABLE IF EXISTS `business_segments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `business_segments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `business_segments_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `business_segments`
--

LOCK TABLES `business_segments` WRITE;
/*!40000 ALTER TABLE `business_segments` DISABLE KEYS */;
INSERT INTO `business_segments` VALUES (1,'MSME','MSME (Micro, Small & Medium Enterprises)','2026-09-30 13:56:20','2026-09-30 13:56:20'),(2,'Retail','Retail (Personal Banking)\\nMeaning: Individual customers, not businesses.','2026-09-30 13:56:20','2026-09-30 13:56:20'),(3,'Corporate','Large, structured companies with formal governance and high financial capacity.','2026-09-30 13:56:20','2026-09-30 13:56:20');
/*!40000 ALTER TABLE `business_segments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('branch-performance-management-systembpms-cache-spatie.permission.cache','a:3:{s:5:\"alias\";a:4:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:4:\"name\";s:1:\"c\";s:10:\"guard_name\";s:1:\"r\";s:5:\"roles\";}s:11:\"permissions\";a:48:{i:0;a:4:{s:1:\"a\";i:1;s:1:\"b\";s:14:\"view dashboard\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;}}i:1;a:4:{s:1:\"a\";i:2;s:1:\"b\";s:12:\"view reports\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;}}i:2;a:4:{s:1:\"a\";i:3;s:1:\"b\";s:13:\"view branches\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;}}i:3;a:4:{s:1:\"a\";i:4;s:1:\"b\";s:15:\"create branches\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:4;a:4:{s:1:\"a\";i:5;s:1:\"b\";s:13:\"edit branches\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:5;a:4:{s:1:\"a\";i:6;s:1:\"b\";s:15:\"delete branches\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:6;a:4:{s:1:\"a\";i:7;s:1:\"b\";s:14:\"view districts\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:4;i:3;i:5;}}i:7;a:4:{s:1:\"a\";i:8;s:1:\"b\";s:16:\"create districts\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:8;a:4:{s:1:\"a\";i:9;s:1:\"b\";s:14:\"edit districts\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:9;a:4:{s:1:\"a\";i:10;s:1:\"b\";s:16:\"delete districts\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:10;a:4:{s:1:\"a\";i:11;s:1:\"b\";s:20:\"view financial years\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:4;i:3;i:5;}}i:11;a:4:{s:1:\"a\";i:12;s:1:\"b\";s:22:\"create financial years\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:12;a:4:{s:1:\"a\";i:13;s:1:\"b\";s:20:\"edit financial years\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:13;a:4:{s:1:\"a\";i:14;s:1:\"b\";s:22:\"delete financial years\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:14;a:4:{s:1:\"a\";i:15;s:1:\"b\";s:21:\"close financial years\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:15;a:4:{s:1:\"a\";i:16;s:1:\"b\";s:22:\"view financial periods\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:4;i:3;i:5;}}i:16;a:4:{s:1:\"a\";i:17;s:1:\"b\";s:24:\"create financial periods\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:17;a:4:{s:1:\"a\";i:18;s:1:\"b\";s:22:\"edit financial periods\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:18;a:4:{s:1:\"a\";i:19;s:1:\"b\";s:24:\"delete financial periods\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:19;a:4:{s:1:\"a\";i:20;s:1:\"b\";s:17:\"view annual plans\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:4;i:3;i:5;}}i:20;a:4:{s:1:\"a\";i:21;s:1:\"b\";s:19:\"create annual plans\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:5;}}i:21;a:4:{s:1:\"a\";i:22;s:1:\"b\";s:17:\"edit annual plans\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:5;}}i:22;a:4:{s:1:\"a\";i:23;s:1:\"b\";s:19:\"delete annual plans\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:23;a:4:{s:1:\"a\";i:24;s:1:\"b\";s:19:\"submit annual plans\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:5;}}i:24;a:4:{s:1:\"a\";i:25;s:1:\"b\";s:20:\"approve annual plans\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:25;a:4:{s:1:\"a\";i:26;s:1:\"b\";s:19:\"reject annual plans\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:26;a:4:{s:1:\"a\";i:27;s:1:\"b\";s:17:\"view branch plans\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:4;i:3;i:5;}}i:27;a:4:{s:1:\"a\";i:28;s:1:\"b\";s:19:\"create branch plans\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:5;}}i:28;a:4:{s:1:\"a\";i:29;s:1:\"b\";s:17:\"edit branch plans\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:5;}}i:29;a:4:{s:1:\"a\";i:30;s:1:\"b\";s:19:\"delete branch plans\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:30;a:4:{s:1:\"a\";i:31;s:1:\"b\";s:19:\"submit branch plans\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:5;}}i:31;a:4:{s:1:\"a\";i:32;s:1:\"b\";s:20:\"approve branch plans\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:32;a:4:{s:1:\"a\";i:33;s:1:\"b\";s:19:\"reject branch plans\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:33;a:4:{s:1:\"a\";i:34;s:1:\"b\";s:17:\"view performances\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;}}i:34;a:4:{s:1:\"a\";i:35;s:1:\"b\";s:19:\"create performances\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:5;}}i:35;a:4:{s:1:\"a\";i:36;s:1:\"b\";s:17:\"edit performances\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:5;}}i:36;a:4:{s:1:\"a\";i:37;s:1:\"b\";s:19:\"delete performances\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:37;a:4:{s:1:\"a\";i:38;s:1:\"b\";s:9:\"view kpis\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:4;i:3;i:5;}}i:38;a:4:{s:1:\"a\";i:39;s:1:\"b\";s:11:\"create kpis\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:39;a:4:{s:1:\"a\";i:40;s:1:\"b\";s:9:\"edit kpis\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:40;a:4:{s:1:\"a\";i:41;s:1:\"b\";s:11:\"delete kpis\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:41;a:4:{s:1:\"a\";i:42;s:1:\"b\";s:13:\"view settings\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:42;a:4:{s:1:\"a\";i:43;s:1:\"b\";s:13:\"edit settings\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:43;a:4:{s:1:\"a\";i:44;s:1:\"b\";s:10:\"view users\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:5;}}i:44;a:4:{s:1:\"a\";i:45;s:1:\"b\";s:12:\"create users\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:45;a:4:{s:1:\"a\";i:46;s:1:\"b\";s:10:\"edit users\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:46;a:4:{s:1:\"a\";i:47;s:1:\"b\";s:12:\"delete users\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:47;a:4:{s:1:\"a\";i:48;s:1:\"b\";s:11:\"export data\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:5;}}}s:5:\"roles\";a:5:{i:0;a:3:{s:1:\"a\";i:1;s:1:\"b\";s:5:\"admin\";s:1:\"c\";s:3:\"web\";}i:1;a:3:{s:1:\"a\";i:2;s:1:\"b\";s:7:\"manager\";s:1:\"c\";s:3:\"web\";}i:2;a:3:{s:1:\"a\";i:3;s:1:\"b\";s:14:\"branch_manager\";s:1:\"c\";s:3:\"web\";}i:3;a:3:{s:1:\"a\";i:4;s:1:\"b\";s:6:\"viewer\";s:1:\"c\";s:3:\"web\";}i:4;a:3:{s:1:\"a\";i:5;s:1:\"b\";s:11:\"super_admin\";s:1:\"c\";s:3:\"web\";}}}',1790885738);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `daily_account_openings`
--

DROP TABLE IF EXISTS `daily_account_openings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `daily_account_openings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint(20) unsigned NOT NULL,
  `business_day` date NOT NULL,
  `conventional_accounts` int(11) NOT NULL DEFAULT 0,
  `ifb_accounts` int(11) NOT NULL DEFAULT 0,
  `target_accounts` int(11) NOT NULL DEFAULT 0,
  `remarks` text DEFAULT NULL,
  `recorded_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `daily_account_openings_branch_id_business_day_unique` (`branch_id`,`business_day`),
  KEY `daily_account_openings_recorded_by_foreign` (`recorded_by`),
  CONSTRAINT `daily_account_openings_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `daily_account_openings_recorded_by_foreign` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `daily_account_openings`
--

LOCK TABLES `daily_account_openings` WRITE;
/*!40000 ALTER TABLE `daily_account_openings` DISABLE KEYS */;
/*!40000 ALTER TABLE `daily_account_openings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `daily_account_performances`
--

DROP TABLE IF EXISTS `daily_account_performances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `daily_account_performances` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint(20) unsigned NOT NULL,
  `total_accounts` int(10) unsigned NOT NULL DEFAULT 0,
  `active_accounts` int(10) unsigned NOT NULL DEFAULT 0,
  `dormant_accounts` int(10) unsigned NOT NULL DEFAULT 0,
  `reactivated_accounts` int(10) unsigned NOT NULL DEFAULT 0,
  `new_accounts` int(10) unsigned NOT NULL DEFAULT 0,
  `business_day` date DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `daily_account_performances_branch_id_business_day_unique` (`branch_id`,`business_day`),
  CONSTRAINT `daily_account_performances_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `daily_account_performances`
--

LOCK TABLES `daily_account_performances` WRITE;
/*!40000 ALTER TABLE `daily_account_performances` DISABLE KEYS */;
/*!40000 ALTER TABLE `daily_account_performances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `daily_deposit_performance_details`
--

DROP TABLE IF EXISTS `daily_deposit_performance_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `daily_deposit_performance_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint(20) unsigned NOT NULL,
  `business_day` date NOT NULL,
  `banking_type_id` bigint(20) unsigned NOT NULL,
  `business_segment_id` bigint(20) unsigned NOT NULL,
  `amount` decimal(20,2) NOT NULL DEFAULT 0.00,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `daily_dep_perf_det_unique` (`branch_id`,`business_day`,`banking_type_id`,`business_segment_id`),
  KEY `daily_deposit_performance_details_banking_type_id_foreign` (`banking_type_id`),
  KEY `daily_deposit_performance_details_business_segment_id_foreign` (`business_segment_id`),
  KEY `daily_deposit_performance_details_branch_id_business_day_index` (`branch_id`,`business_day`),
  CONSTRAINT `daily_deposit_performance_details_banking_type_id_foreign` FOREIGN KEY (`banking_type_id`) REFERENCES `banking_types` (`id`) ON DELETE CASCADE,
  CONSTRAINT `daily_deposit_performance_details_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `daily_deposit_performance_details_business_segment_id_foreign` FOREIGN KEY (`business_segment_id`) REFERENCES `business_segments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=570 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `daily_deposit_performance_details`
--

LOCK TABLES `daily_deposit_performance_details` WRITE;
/*!40000 ALTER TABLE `daily_deposit_performance_details` DISABLE KEYS */;
INSERT INTO `daily_deposit_performance_details` VALUES (474,29,'2026-09-29',1,3,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(475,29,'2026-09-29',1,2,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(476,29,'2026-09-29',1,1,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(477,30,'2026-09-29',1,3,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(478,30,'2026-09-29',1,2,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(479,30,'2026-09-29',1,1,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(480,2,'2026-09-29',1,3,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(481,2,'2026-09-29',1,2,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(482,2,'2026-09-29',1,1,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(483,8,'2026-09-29',1,3,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(484,8,'2026-09-29',1,2,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(485,8,'2026-09-29',1,1,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(486,5,'2026-09-29',1,3,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(487,5,'2026-09-29',1,2,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(488,5,'2026-09-29',1,1,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(489,18,'2026-09-29',1,3,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(490,18,'2026-09-29',1,2,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(491,18,'2026-09-29',1,1,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(492,10,'2026-09-29',1,3,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(493,10,'2026-09-29',1,2,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(494,10,'2026-09-29',1,1,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(495,19,'2026-09-29',1,3,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(496,19,'2026-09-29',1,2,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(497,19,'2026-09-29',1,1,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(498,31,'2026-09-29',1,3,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(499,31,'2026-09-29',1,2,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(500,31,'2026-09-29',1,1,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(501,9,'2026-09-29',1,3,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(502,9,'2026-09-29',1,2,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(503,9,'2026-09-29',1,1,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(504,7,'2026-09-29',1,3,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(505,7,'2026-09-29',1,2,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(506,7,'2026-09-29',1,1,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(507,13,'2026-09-29',1,3,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(508,13,'2026-09-29',1,2,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(509,13,'2026-09-29',1,1,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(510,26,'2026-09-29',1,3,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(511,26,'2026-09-29',1,2,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(512,26,'2026-09-29',1,1,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(513,1,'2026-09-29',1,3,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(514,1,'2026-09-29',1,2,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(515,1,'2026-09-29',1,1,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(516,3,'2026-09-29',1,3,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(517,3,'2026-09-29',1,2,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(518,3,'2026-09-29',1,1,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(519,14,'2026-09-29',1,3,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(520,14,'2026-09-29',1,2,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(521,14,'2026-09-29',1,1,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(522,27,'2026-09-29',1,3,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(523,27,'2026-09-29',1,2,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(524,27,'2026-09-29',1,1,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(525,15,'2026-09-29',1,3,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(526,15,'2026-09-29',1,2,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(527,15,'2026-09-29',1,1,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(528,12,'2026-09-29',1,3,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(529,12,'2026-09-29',1,2,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(530,12,'2026-09-29',1,1,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(531,20,'2026-09-29',1,3,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(532,20,'2026-09-29',1,2,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(533,20,'2026-09-29',1,1,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(534,17,'2026-09-29',1,3,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(535,17,'2026-09-29',1,2,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(536,17,'2026-09-29',1,1,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(537,23,'2026-09-29',1,3,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(538,23,'2026-09-29',1,2,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(539,23,'2026-09-29',1,1,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(540,21,'2026-09-29',1,3,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(541,21,'2026-09-29',1,2,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(542,21,'2026-09-29',1,1,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(543,22,'2026-09-29',1,3,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(544,22,'2026-09-29',1,2,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(545,22,'2026-09-29',1,1,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(546,11,'2026-09-29',1,3,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(547,11,'2026-09-29',1,2,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(548,11,'2026-09-29',1,1,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(549,4,'2026-09-29',1,3,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(550,4,'2026-09-29',1,2,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(551,4,'2026-09-29',1,1,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(552,16,'2026-09-29',1,3,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(553,16,'2026-09-29',1,2,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(554,16,'2026-09-29',1,1,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(555,6,'2026-09-29',2,3,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(556,6,'2026-09-29',2,2,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(557,6,'2026-09-29',2,1,0.00,NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(564,28,'2026-09-29',2,3,1000.00,NULL,'2026-09-30 17:12:46','2026-09-30 17:12:46'),(565,28,'2026-09-29',2,2,1000.00,NULL,'2026-09-30 17:12:46','2026-09-30 17:12:46'),(566,28,'2026-09-29',2,1,1000.00,NULL,'2026-09-30 17:12:46','2026-09-30 17:12:46'),(567,24,'2026-09-29',2,3,5000.00,NULL,'2026-09-30 17:21:04','2026-09-30 17:21:04'),(568,24,'2026-09-29',2,2,5000.00,NULL,'2026-09-30 17:21:04','2026-09-30 17:21:04'),(569,24,'2026-09-29',2,1,0.00,NULL,'2026-09-30 17:21:04','2026-09-30 17:21:04');
/*!40000 ALTER TABLE `daily_deposit_performance_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `daily_deposit_performances`
--

DROP TABLE IF EXISTS `daily_deposit_performances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `daily_deposit_performances` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` bigint(20) unsigned NOT NULL,
  `total_deposit_amount` decimal(20,2) NOT NULL DEFAULT 0.00,
  `new_deposit_amount` decimal(20,2) NOT NULL DEFAULT 0.00,
  `deposit_inflow_amount` decimal(20,2) NOT NULL DEFAULT 0.00,
  `deposit_outflow_amount` decimal(20,2) NOT NULL DEFAULT 0.00,
  `net_deposit_change` decimal(20,2) NOT NULL DEFAULT 0.00,
  `business_day` date DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `daily_deposit_performances_branch_id_business_day_unique` (`branch_id`,`business_day`),
  CONSTRAINT `daily_deposit_performances_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=246 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `daily_deposit_performances`
--

LOCK TABLES `daily_deposit_performances` WRITE;
/*!40000 ALTER TABLE `daily_deposit_performances` DISABLE KEYS */;
INSERT INTO `daily_deposit_performances` VALUES (216,29,0.00,0.00,0.00,0.00,0.00,'2026-09-29',NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(217,30,0.00,0.00,0.00,0.00,0.00,'2026-09-29',NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(218,2,0.00,0.00,0.00,0.00,0.00,'2026-09-29',NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(219,8,0.00,0.00,0.00,0.00,0.00,'2026-09-29',NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(220,5,0.00,0.00,0.00,0.00,0.00,'2026-09-29',NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(221,18,0.00,0.00,0.00,0.00,0.00,'2026-09-29',NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(222,10,0.00,0.00,0.00,0.00,0.00,'2026-09-29',NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(223,19,0.00,0.00,0.00,0.00,0.00,'2026-09-29',NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(224,31,0.00,0.00,0.00,0.00,0.00,'2026-09-29',NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(225,9,0.00,0.00,0.00,0.00,0.00,'2026-09-29',NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(226,7,0.00,0.00,0.00,0.00,0.00,'2026-09-29',NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(227,13,0.00,0.00,0.00,0.00,0.00,'2026-09-29',NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(228,26,0.00,0.00,0.00,0.00,0.00,'2026-09-29',NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(229,1,0.00,0.00,0.00,0.00,0.00,'2026-09-29',NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(230,3,0.00,0.00,0.00,0.00,0.00,'2026-09-29',NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(231,14,0.00,0.00,0.00,0.00,0.00,'2026-09-29',NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(232,27,0.00,0.00,0.00,0.00,0.00,'2026-09-29',NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(233,15,0.00,0.00,0.00,0.00,0.00,'2026-09-29',NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(234,12,0.00,0.00,0.00,0.00,0.00,'2026-09-29',NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(235,20,0.00,0.00,0.00,0.00,0.00,'2026-09-29',NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(236,17,0.00,0.00,0.00,0.00,0.00,'2026-09-29',NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(237,23,0.00,0.00,0.00,0.00,0.00,'2026-09-29',NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(238,21,0.00,0.00,0.00,0.00,0.00,'2026-09-29',NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(239,22,0.00,0.00,0.00,0.00,0.00,'2026-09-29',NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(240,11,0.00,0.00,0.00,0.00,0.00,'2026-09-29',NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(241,4,0.00,0.00,0.00,0.00,0.00,'2026-09-29',NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(242,16,0.00,0.00,0.00,0.00,0.00,'2026-09-29',NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(243,6,0.00,0.00,0.00,0.00,0.00,'2026-09-29',NULL,'2026-09-30 16:57:37','2026-09-30 16:57:37'),(244,24,10000.00,0.00,0.00,0.00,0.00,'2026-09-29',NULL,'2026-09-30 16:57:37','2026-09-30 17:21:04'),(245,28,3000.00,0.00,0.00,0.00,0.00,'2026-09-29',NULL,'2026-09-30 16:57:37','2026-09-30 17:12:46');
/*!40000 ALTER TABLE `daily_deposit_performances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `districts`
--

DROP TABLE IF EXISTS `districts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `districts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `location_type` enum('City','Upcountry') DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `districts`
--

LOCK TABLES `districts` WRITE;
/*!40000 ALTER TABLE `districts` DISABLE KEYS */;
INSERT INTO `districts` VALUES (1,'Jimma','Upcountry','2026-09-30 13:56:20','2026-09-30 13:56:20'),(2,'South West','Upcountry','2026-09-30 13:56:20','2026-09-30 13:56:20'),(3,'Nekemte','Upcountry','2026-09-30 13:56:20','2026-09-30 13:56:20'),(4,'Hawasa','Upcountry','2026-09-30 13:56:20','2026-09-30 13:56:20'),(5,'Adama','Upcountry','2026-09-30 13:56:20','2026-09-30 13:56:20'),(6,'Dire Dawa','Upcountry','2026-09-30 13:56:20','2026-09-30 13:56:20'),(7,'Mekelle','Upcountry','2026-09-30 13:56:20','2026-09-30 13:56:20'),(8,'Dessie','Upcountry','2026-09-30 13:56:20','2026-09-30 13:56:20'),(9,'Bahir Dar','Upcountry','2026-09-30 13:56:20','2026-09-30 13:56:20'),(10,'North Addis','City','2026-09-30 13:56:20','2026-09-30 13:56:20'),(11,'East Addis','City','2026-09-30 13:56:20','2026-09-30 13:56:20'),(12,'West Addis','City','2026-09-30 13:56:20','2026-09-30 13:56:20'),(13,'South Addis','City','2026-09-30 13:56:20','2026-09-30 13:56:20'),(14,'Wolaita','Upcountry','2026-09-30 13:56:20','2026-09-30 13:56:20'),(15,'Head Office','City','2026-09-30 13:56:20','2026-09-30 13:56:20');
/*!40000 ALTER TABLE `districts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `financial_periods`
--

DROP TABLE IF EXISTS `financial_periods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `financial_periods` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `financial_year_id` bigint(20) unsigned NOT NULL,
  `quarter` tinyint(4) NOT NULL,
  `label` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('OPEN','CLOSED') NOT NULL DEFAULT 'OPEN',
  `closed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `financial_periods_financial_year_id_foreign` (`financial_year_id`),
  CONSTRAINT `financial_periods_financial_year_id_foreign` FOREIGN KEY (`financial_year_id`) REFERENCES `financial_years` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `financial_periods`
--

LOCK TABLES `financial_periods` WRITE;
/*!40000 ALTER TABLE `financial_periods` DISABLE KEYS */;
INSERT INTO `financial_periods` VALUES (28,5,1,'Q1-FY-2026/27','2026-07-01','2026-09-30','OPEN',NULL,'2026-09-30 13:56:20','2026-09-30 13:56:20'),(29,5,2,'Q2-FY-2026/27','2026-10-01','2026-12-31','OPEN',NULL,'2026-09-30 13:56:20','2026-09-30 13:56:20'),(30,5,3,'Q3-FY-2026/27','2027-01-01','2027-03-31','OPEN',NULL,'2026-09-30 13:56:20','2026-09-30 13:56:20'),(31,5,4,'Q4-FY-2026/27','2027-04-01','2027-06-30','OPEN',NULL,'2026-09-30 13:56:20','2026-09-30 13:56:20');
/*!40000 ALTER TABLE `financial_periods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `financial_years`
--

DROP TABLE IF EXISTS `financial_years`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `financial_years` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('OPEN','CLOSING','CLOSED') NOT NULL DEFAULT 'OPEN',
  `opened_at` timestamp NULL DEFAULT NULL,
  `closed_at` timestamp NULL DEFAULT NULL,
  `closed_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `financial_years_name_unique` (`name`),
  KEY `financial_years_closed_by_foreign` (`closed_by`),
  CONSTRAINT `financial_years_closed_by_foreign` FOREIGN KEY (`closed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `financial_years`
--

LOCK TABLES `financial_years` WRITE;
/*!40000 ALTER TABLE `financial_years` DISABLE KEYS */;
INSERT INTO `financial_years` VALUES (5,'FY2026/27','2026-07-01','2027-06-30','OPEN',NULL,NULL,NULL,'2026-09-30 13:56:20','2026-09-30 13:56:20');
/*!40000 ALTER TABLE `financial_years` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `group_permission`
--

DROP TABLE IF EXISTS `group_permission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `group_permission` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` bigint(20) unsigned NOT NULL,
  `permission_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `group_permission_group_id_permission_id_unique` (`group_id`,`permission_id`),
  KEY `group_permission_permission_id_foreign` (`permission_id`),
  CONSTRAINT `group_permission_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `user_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `group_permission_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `group_permission`
--

LOCK TABLES `group_permission` WRITE;
/*!40000 ALTER TABLE `group_permission` DISABLE KEYS */;
INSERT INTO `group_permission` VALUES (1,1,25,NULL,NULL),(2,1,32,NULL,NULL),(3,1,15,NULL,NULL),(4,1,21,NULL,NULL),(5,1,28,NULL,NULL),(6,1,4,NULL,NULL),(7,1,8,NULL,NULL),(8,1,17,NULL,NULL),(9,1,12,NULL,NULL),(10,1,39,NULL,NULL),(11,1,35,NULL,NULL),(12,1,45,NULL,NULL),(13,1,23,NULL,NULL),(14,1,30,NULL,NULL),(15,1,6,NULL,NULL),(16,1,10,NULL,NULL),(17,1,19,NULL,NULL),(18,1,14,NULL,NULL),(19,1,41,NULL,NULL),(20,1,37,NULL,NULL),(21,1,47,NULL,NULL),(22,1,22,NULL,NULL),(23,1,29,NULL,NULL),(24,1,5,NULL,NULL),(25,1,9,NULL,NULL),(26,1,18,NULL,NULL),(27,1,13,NULL,NULL),(28,1,40,NULL,NULL),(29,1,36,NULL,NULL),(30,1,43,NULL,NULL),(31,1,46,NULL,NULL),(32,1,48,NULL,NULL),(33,1,26,NULL,NULL),(34,1,33,NULL,NULL),(35,1,24,NULL,NULL),(36,1,31,NULL,NULL),(37,1,20,NULL,NULL),(38,1,27,NULL,NULL),(39,1,3,NULL,NULL),(40,1,1,NULL,NULL),(41,1,7,NULL,NULL),(42,1,16,NULL,NULL),(43,1,11,NULL,NULL),(44,1,38,NULL,NULL),(45,1,34,NULL,NULL),(46,1,2,NULL,NULL),(47,1,42,NULL,NULL),(48,1,44,NULL,NULL),(49,2,21,NULL,NULL),(50,2,28,NULL,NULL),(51,2,35,NULL,NULL),(52,2,22,NULL,NULL),(53,2,29,NULL,NULL),(54,2,36,NULL,NULL),(55,2,48,NULL,NULL),(56,2,24,NULL,NULL),(57,2,31,NULL,NULL),(58,2,20,NULL,NULL),(59,2,27,NULL,NULL),(60,2,3,NULL,NULL),(61,2,1,NULL,NULL),(62,2,7,NULL,NULL),(63,2,16,NULL,NULL),(64,2,11,NULL,NULL),(65,2,38,NULL,NULL),(66,2,34,NULL,NULL),(67,2,2,NULL,NULL),(68,2,44,NULL,NULL),(69,3,35,NULL,NULL),(70,3,36,NULL,NULL),(71,3,3,NULL,NULL),(72,3,1,NULL,NULL),(73,3,34,NULL,NULL),(74,3,2,NULL,NULL),(75,4,20,NULL,NULL),(76,4,27,NULL,NULL),(77,4,3,NULL,NULL),(78,4,1,NULL,NULL),(79,4,7,NULL,NULL),(80,4,16,NULL,NULL),(81,4,11,NULL,NULL),(82,4,38,NULL,NULL),(83,4,34,NULL,NULL),(84,4,2,NULL,NULL);
/*!40000 ALTER TABLE `group_permission` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `k_p_i_categories`
--

DROP TABLE IF EXISTS `k_p_i_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `k_p_i_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `k_p_i_categories`
--

LOCK TABLES `k_p_i_categories` WRITE;
/*!40000 ALTER TABLE `k_p_i_categories` DISABLE KEYS */;
INSERT INTO `k_p_i_categories` VALUES (1,'Customer Acquisition',NULL,'2026-09-30 13:56:20','2026-09-30 13:56:20'),(2,'Deposit Mobilization',NULL,'2026-09-30 13:56:20','2026-09-30 13:56:20'),(3,'Digital Banking',NULL,'2026-09-30 13:56:20','2026-09-30 13:56:20'),(4,'Service Quality',NULL,'2026-09-30 13:56:20','2026-09-30 13:56:20');
/*!40000 ALTER TABLE `k_p_i_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `k_p_i_s`
--

DROP TABLE IF EXISTS `k_p_i_s`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `k_p_i_s` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `category_id` bigint(20) unsigned NOT NULL,
  `unit` varchar(255) NOT NULL,
  `calculation_method` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `k_p_i_s_category_id_foreign` (`category_id`),
  CONSTRAINT `k_p_i_s_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `k_p_i_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `k_p_i_s`
--

LOCK TABLES `k_p_i_s` WRITE;
/*!40000 ALTER TABLE `k_p_i_s` DISABLE KEYS */;
INSERT INTO `k_p_i_s` VALUES (1,'Account Opening',1,'count','Existing plus new Active account','2026-09-30 13:56:20','2026-09-30 13:56:20'),(2,'Deposit',2,'amount','Last Jun plus new deposit','2026-09-30 13:56:20','2026-09-30 13:56:20');
/*!40000 ALTER TABLE `k_p_i_s` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `login_histories`
--

DROP TABLE IF EXISTS `login_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `login_histories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `logged_in_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `login_histories_user_id_foreign` (`user_id`),
  CONSTRAINT `login_histories_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login_histories`
--

LOCK TABLES `login_histories` WRITE;
/*!40000 ALTER TABLE `login_histories` DISABLE KEYS */;
/*!40000 ALTER TABLE `login_histories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_06_29_084523_create_districts_table',1),(5,'2026_06_29_085037_create_branches_table',1),(6,'2026_06_29_123227_create_banking_types_table',1),(7,'2026_06_29_125136_add_banking_type_to_branches_table',1),(8,'2026_06_30_080415_create_k_p_i_s_table',1),(9,'2026_06_30_080417_create_business_segments_table',1),(10,'2026_06_30_084702_create_financial_years_table',1),(11,'2026_06_30_085241_create_financial_periods_table',1),(12,'2026_06_30_090356_create_k_p_i_categories_table',1),(13,'2026_06_30_090360_create_annual_plans_table',1),(14,'2026_07_01_130640_create_annual_account_plans_table',1),(15,'2026_07_01_130725_create_annual_deposit_plans_table',1),(16,'2026_07_02_073620_create_branch_account_plans_table',1),(17,'2026_07_02_073622_create_branch_deposit_plans_table',1),(18,'2026_07_02_123855_create_daily_account_performances_table',1),(19,'2026_07_02_123857_create_daily_deposit_performances_table',1),(20,'2026_09_30_071952_create_permission_tables',1),(21,'2026_09_30_071953_create_personal_access_tokens_table',1),(22,'2026_09_30_100000_add_approval_workflow_to_plans',1),(23,'2026_09_30_120000_create_user_groups_table',1),(24,'2026_09_30_130000_create_daily_account_openings_table',1),(25,'2026_09_30_140000_enhance_users_table',1),(26,'2026_09_30_150000_create_daily_deposit_performance_details_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_permissions`
--

LOCK TABLES `model_has_permissions` WRITE;
/*!40000 ALTER TABLE `model_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_roles`
--

LOCK TABLES `model_has_roles` WRITE;
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
INSERT INTO `model_has_roles` VALUES (1,'App\\Models\\User',2),(2,'App\\Models\\User',3),(3,'App\\Models\\User',4),(4,'App\\Models\\User',5),(5,'App\\Models\\User',1);
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'view dashboard','web','2026-09-30 13:56:18','2026-09-30 13:56:18'),(2,'view reports','web','2026-09-30 13:56:18','2026-09-30 13:56:18'),(3,'view branches','web','2026-09-30 13:56:18','2026-09-30 13:56:18'),(4,'create branches','web','2026-09-30 13:56:18','2026-09-30 13:56:18'),(5,'edit branches','web','2026-09-30 13:56:18','2026-09-30 13:56:18'),(6,'delete branches','web','2026-09-30 13:56:18','2026-09-30 13:56:18'),(7,'view districts','web','2026-09-30 13:56:18','2026-09-30 13:56:18'),(8,'create districts','web','2026-09-30 13:56:18','2026-09-30 13:56:18'),(9,'edit districts','web','2026-09-30 13:56:18','2026-09-30 13:56:18'),(10,'delete districts','web','2026-09-30 13:56:18','2026-09-30 13:56:18'),(11,'view financial years','web','2026-09-30 13:56:19','2026-09-30 13:56:19'),(12,'create financial years','web','2026-09-30 13:56:19','2026-09-30 13:56:19'),(13,'edit financial years','web','2026-09-30 13:56:19','2026-09-30 13:56:19'),(14,'delete financial years','web','2026-09-30 13:56:19','2026-09-30 13:56:19'),(15,'close financial years','web','2026-09-30 13:56:19','2026-09-30 13:56:19'),(16,'view financial periods','web','2026-09-30 13:56:19','2026-09-30 13:56:19'),(17,'create financial periods','web','2026-09-30 13:56:19','2026-09-30 13:56:19'),(18,'edit financial periods','web','2026-09-30 13:56:19','2026-09-30 13:56:19'),(19,'delete financial periods','web','2026-09-30 13:56:19','2026-09-30 13:56:19'),(20,'view annual plans','web','2026-09-30 13:56:19','2026-09-30 13:56:19'),(21,'create annual plans','web','2026-09-30 13:56:19','2026-09-30 13:56:19'),(22,'edit annual plans','web','2026-09-30 13:56:19','2026-09-30 13:56:19'),(23,'delete annual plans','web','2026-09-30 13:56:19','2026-09-30 13:56:19'),(24,'submit annual plans','web','2026-09-30 13:56:19','2026-09-30 13:56:19'),(25,'approve annual plans','web','2026-09-30 13:56:19','2026-09-30 13:56:19'),(26,'reject annual plans','web','2026-09-30 13:56:19','2026-09-30 13:56:19'),(27,'view branch plans','web','2026-09-30 13:56:19','2026-09-30 13:56:19'),(28,'create branch plans','web','2026-09-30 13:56:19','2026-09-30 13:56:19'),(29,'edit branch plans','web','2026-09-30 13:56:19','2026-09-30 13:56:19'),(30,'delete branch plans','web','2026-09-30 13:56:19','2026-09-30 13:56:19'),(31,'submit branch plans','web','2026-09-30 13:56:19','2026-09-30 13:56:19'),(32,'approve branch plans','web','2026-09-30 13:56:19','2026-09-30 13:56:19'),(33,'reject branch plans','web','2026-09-30 13:56:19','2026-09-30 13:56:19'),(34,'view performances','web','2026-09-30 13:56:19','2026-09-30 13:56:19'),(35,'create performances','web','2026-09-30 13:56:19','2026-09-30 13:56:19'),(36,'edit performances','web','2026-09-30 13:56:19','2026-09-30 13:56:19'),(37,'delete performances','web','2026-09-30 13:56:19','2026-09-30 13:56:19'),(38,'view kpis','web','2026-09-30 13:56:19','2026-09-30 13:56:19'),(39,'create kpis','web','2026-09-30 13:56:19','2026-09-30 13:56:19'),(40,'edit kpis','web','2026-09-30 13:56:19','2026-09-30 13:56:19'),(41,'delete kpis','web','2026-09-30 13:56:19','2026-09-30 13:56:19'),(42,'view settings','web','2026-09-30 13:56:19','2026-09-30 13:56:19'),(43,'edit settings','web','2026-09-30 13:56:19','2026-09-30 13:56:19'),(44,'view users','web','2026-09-30 13:56:19','2026-09-30 13:56:19'),(45,'create users','web','2026-09-30 13:56:19','2026-09-30 13:56:19'),(46,'edit users','web','2026-09-30 13:56:19','2026-09-30 13:56:19'),(47,'delete users','web','2026-09-30 13:56:19','2026-09-30 13:56:19'),(48,'export data','web','2026-09-30 13:56:19','2026-09-30 13:56:19');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_has_permissions`
--

LOCK TABLES `role_has_permissions` WRITE;
/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
INSERT INTO `role_has_permissions` VALUES (1,1),(1,2),(1,3),(1,4),(1,5),(2,1),(2,2),(2,3),(2,4),(2,5),(3,1),(3,2),(3,3),(3,4),(3,5),(4,1),(4,5),(5,1),(5,5),(6,1),(6,5),(7,1),(7,2),(7,4),(7,5),(8,1),(8,5),(9,1),(9,5),(10,1),(10,5),(11,1),(11,2),(11,4),(11,5),(12,1),(12,5),(13,1),(13,5),(14,1),(14,5),(15,1),(15,5),(16,1),(16,2),(16,4),(16,5),(17,1),(17,5),(18,1),(18,5),(19,1),(19,5),(20,1),(20,2),(20,4),(20,5),(21,1),(21,2),(21,5),(22,1),(22,2),(22,5),(23,1),(23,5),(24,1),(24,2),(24,5),(25,1),(25,5),(26,1),(26,5),(27,1),(27,2),(27,4),(27,5),(28,1),(28,2),(28,5),(29,1),(29,2),(29,5),(30,1),(30,5),(31,1),(31,2),(31,5),(32,1),(32,5),(33,1),(33,5),(34,1),(34,2),(34,3),(34,4),(34,5),(35,1),(35,2),(35,3),(35,5),(36,1),(36,2),(36,3),(36,5),(37,1),(37,5),(38,1),(38,2),(38,4),(38,5),(39,1),(39,5),(40,1),(40,5),(41,1),(41,5),(42,1),(42,5),(43,1),(43,5),(44,1),(44,2),(44,5),(45,1),(45,5),(46,1),(46,5),(47,1),(47,5),(48,1),(48,2),(48,5);
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'admin','web','2026-09-30 13:56:19','2026-09-30 13:56:19'),(2,'manager','web','2026-09-30 13:56:19','2026-09-30 13:56:19'),(3,'branch_manager','web','2026-09-30 13:56:19','2026-09-30 13:56:19'),(4,'viewer','web','2026-09-30 13:56:19','2026-09-30 13:56:19'),(5,'super_admin','web','2026-09-30 13:56:19','2026-09-30 13:56:19');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('7jBUwse7nbndpk5ybhphDrhRX8lC6STFebJ0JYAG',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) OpenCode/2.0.20 Chrome/152.0.7977.130 Electron/44.4.5 Safari/537.36','YTo3OntzOjY6Il90b2tlbiI7czo0MDoiOVBISGdTUlUzZVlwVXZEb0h0eUR3WmM2QkxLV0htV25XcWNkWnRSMCI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjU0OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYWRtaW4vZGFpbHktZGVwb3NpdC1wZXJmb3JtYW5jZXMiO3M6NToicm91dGUiO3M6NTc6ImZpbGFtZW50LmFkbWluLnJlc291cmNlcy5kYWlseS1kZXBvc2l0LXBlcmZvcm1hbmNlcy5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7czoxNzoicGFzc3dvcmRfaGFzaF93ZWIiO3M6NjQ6IjM5OTEyOGVmMjExYmE1MWNiYmRjOTdiMTU3NDQ1MDUyOTMzMTFkNWQ0ZDVkNjU2YTk4YWI0ZjcyZWYwOTUxYzUiO3M6NjoidGFibGVzIjthOjE6e3M6NDA6ImQ2NzYwNmIzZjNkYjJjOTg4ZWY4YmQ4M2MyZGFmNzQ5X2NvbHVtbnMiO2E6MTY6e2k6MDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMjoiYnVzaW5lc3NfZGF5IjtzOjU6ImxhYmVsIjtzOjEyOiJCdXNpbmVzcyBEYXkiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToxO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjExOiJicmFuY2gubmFtZSI7czo1OiJsYWJlbCI7czo2OiJCcmFuY2giO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToyO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjIzOiJicmFuY2guYmFua2luZ1R5cGUubmFtZSI7czo1OiJsYWJlbCI7czo0OiJUeXBlIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoyMjoiY29udmVudGlvbmFsX2NvcnBvcmF0ZSI7czo1OiJsYWJlbCI7czoxMDoiQ29udi4gQ29ycCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjA7fWk6NDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxOToiY29udmVudGlvbmFsX3JldGFpbCI7czo1OiJsYWJlbCI7czoxMjoiQ29udi4gUmV0YWlsIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MDt9aTo1O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjE3OiJjb252ZW50aW9uYWxfbXNtZSI7czo1OiJsYWJlbCI7czoxMDoiQ29udi4gTVNNRSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjA7fWk6NjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxODoiY29udmVudGlvbmFsX3RvdGFsIjtzOjU6ImxhYmVsIjtzOjExOiJDb252LiBUb3RhbCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjc7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTM6ImlmYl9jb3Jwb3JhdGUiO3M6NToibGFiZWwiO3M6ODoiSUZCIENvcnAiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjowO31pOjg7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTA6ImlmYl9yZXRhaWwiO3M6NToibGFiZWwiO3M6MTA6IklGQiBSZXRhaWwiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjowO31pOjk7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6ODoiaWZiX21zbWUiO3M6NToibGFiZWwiO3M6ODoiSUZCIE1TTUUiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjowO31pOjEwO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjk6ImlmYl90b3RhbCI7czo1OiJsYWJlbCI7czo5OiJJRkIgVG90YWwiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToxMTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoyMDoidG90YWxfZGVwb3NpdF9hbW91bnQiO3M6NToibGFiZWwiO3M6MTE6IkdyYW5kIFRvdGFsIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MTI7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTg6Im5ldF9kZXBvc2l0X2NoYW5nZSI7czo1OiJsYWJlbCI7czoxMDoiTmV0IENoYW5nZSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjEzO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjE4OiJuZXRfY2hhbmdlX3BlcmNlbnQiO3M6NToibGFiZWwiO3M6OToiQWN0dWFsKCUpIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MDt9aToxNDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo3OiJyZW1hcmtzIjtzOjU6ImxhYmVsIjtzOjc6IlJlbWFya3MiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO31pOjE1O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEwOiJjcmVhdGVkX2F0IjtzOjU6ImxhYmVsIjtzOjc6IkNyZWF0ZWQiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO319fX0=',1790799086),('Irgx8U8NUTsC5yc2bmk6fMKYZFKdyIV79SGfR5OW',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:156.0) Gecko/20100101 Firefox/156.0','YTo4OntzOjY6Il90b2tlbiI7czo0MDoiZ3FtYTBXbDU4VTBza0xUYTBpZmV0VHF0YWxwRVZoSzVLQ1E5bnVRUCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjI3OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYWRtaW4iO3M6NToicm91dGUiO3M6MzA6ImZpbGFtZW50LmFkbWluLnBhZ2VzLmRhc2hib2FyZCI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7czoxNzoicGFzc3dvcmRfaGFzaF93ZWIiO3M6NjQ6IjM5OTEyOGVmMjExYmE1MWNiYmRjOTdiMTU3NDQ1MDUyOTMzMTFkNWQ0ZDVkNjU2YTk4YWI0ZjcyZWYwOTUxYzUiO3M6NjoidGFibGVzIjthOjQ6e3M6NDA6IjNiNzlhMzFjN2Y4MTFkNzZhMmYxOGUxMTlkZDRhMjE4X2NvbHVtbnMiO2E6ODp7aTowO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjQ6Im5hbWUiO3M6NToibGFiZWwiO3M6MTE6IkJyYW5jaCBOYW1lIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMzoiZGlzdHJpY3QubmFtZSI7czo1OiJsYWJlbCI7czo4OiJEaXN0cmljdCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjI7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTQ6ImRlcG9zaXRfdGFyZ2V0IjtzOjU6ImxhYmVsIjtzOjE0OiJEZXBvc2l0IFRhcmdldCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjM7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTQ6ImRlcG9zaXRfYWN0dWFsIjtzOjU6ImxhYmVsIjtzOjE0OiJEZXBvc2l0IEFjdHVhbCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjQ7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTk6ImRlcG9zaXRfYWNoaWV2ZW1lbnQiO3M6NToibGFiZWwiO3M6MTM6IkFjaGlldmVtZW50ICUiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo1O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjE0OiJhY2NvdW50X3RhcmdldCI7czo1OiJsYWJlbCI7czoxNDoiQWNjb3VudCBUYXJnZXQiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo2O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjE0OiJhY2NvdW50X2FjdHVhbCI7czo1OiJsYWJlbCI7czoxNDoiQWNjb3VudCBBY3R1YWwiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo3O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjE5OiJhY2NvdW50X2FjaGlldmVtZW50IjtzOjU6ImxhYmVsIjtzOjEzOiJBY2hpZXZlbWVudCAlIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fX1zOjQwOiJkNjc2MDZiM2YzZGIyYzk4OGVmOGJkODNjMmRhZjc0OV9jb2x1bW5zIjthOjE2OntpOjA7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTI6ImJ1c2luZXNzX2RheSI7czo1OiJsYWJlbCI7czoxMjoiQnVzaW5lc3MgRGF5IjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMToiYnJhbmNoLm5hbWUiO3M6NToibGFiZWwiO3M6NjoiQnJhbmNoIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoyMzoiYnJhbmNoLmJhbmtpbmdUeXBlLm5hbWUiO3M6NToibGFiZWwiO3M6NDoiVHlwZSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjM7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MjI6ImNvbnZlbnRpb25hbF9jb3Jwb3JhdGUiO3M6NToibGFiZWwiO3M6MTA6IkNvbnYuIENvcnAiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjowO31pOjQ7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTk6ImNvbnZlbnRpb25hbF9yZXRhaWwiO3M6NToibGFiZWwiO3M6MTI6IkNvbnYuIFJldGFpbCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjA7fWk6NTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxNzoiY29udmVudGlvbmFsX21zbWUiO3M6NToibGFiZWwiO3M6MTA6IkNvbnYuIE1TTUUiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjowO31pOjY7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTg6ImNvbnZlbnRpb25hbF90b3RhbCI7czo1OiJsYWJlbCI7czoxMToiQ29udi4gVG90YWwiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo3O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEzOiJpZmJfY29ycG9yYXRlIjtzOjU6ImxhYmVsIjtzOjg6IklGQiBDb3JwIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MDt9aTo4O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEwOiJpZmJfcmV0YWlsIjtzOjU6ImxhYmVsIjtzOjEwOiJJRkIgUmV0YWlsIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MDt9aTo5O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjg6ImlmYl9tc21lIjtzOjU6ImxhYmVsIjtzOjg6IklGQiBNU01FIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MDt9aToxMDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo5OiJpZmJfdG90YWwiO3M6NToibGFiZWwiO3M6OToiSUZCIFRvdGFsIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MTE7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MjA6InRvdGFsX2RlcG9zaXRfYW1vdW50IjtzOjU6ImxhYmVsIjtzOjExOiJHcmFuZCBUb3RhbCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjEyO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjE4OiJuZXRfZGVwb3NpdF9jaGFuZ2UiO3M6NToibGFiZWwiO3M6MTA6Ik5ldCBDaGFuZ2UiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToxMzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxODoibmV0X2NoYW5nZV9wZXJjZW50IjtzOjU6ImxhYmVsIjtzOjk6IkFjdHVhbCglKSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjA7fWk6MTQ7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NzoicmVtYXJrcyI7czo1OiJsYWJlbCI7czo3OiJSZW1hcmtzIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MDtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MTt9aToxNTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMDoiY3JlYXRlZF9hdCI7czo1OiJsYWJlbCI7czo3OiJDcmVhdGVkIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MDtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MTt9fXM6NDA6IjM1NWQ4MDg0MTFhY2FiOTlhNGUxMjRhNmJmMzNmZmMzX2NvbHVtbnMiO2E6MTA6e2k6MDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMToiYnJhbmNoLm5hbWUiO3M6NToibGFiZWwiO3M6NjoiQnJhbmNoIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoyMDoiYnJhbmNoLmRpc3RyaWN0Lm5hbWUiO3M6NToibGFiZWwiO3M6ODoiRGlzdHJpY3QiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjowO31pOjI7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MjM6ImJyYW5jaC5iYW5raW5nVHlwZS5uYW1lIjtzOjU6ImxhYmVsIjtzOjQ6IlR5cGUiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjowO31pOjM7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTI6ImJ1c2luZXNzX2RheSI7czo1OiJsYWJlbCI7czo0OiJEYXRlIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoyMToiY29udmVudGlvbmFsX2FjY291bnRzIjtzOjU6ImxhYmVsIjtzOjEyOiJDb252ZW50aW9uYWwiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo1O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEyOiJpZmJfYWNjb3VudHMiO3M6NToibGFiZWwiO3M6MzoiSUZCIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo1OiJ0b3RhbCI7czo1OiJsYWJlbCI7czo1OiJUb3RhbCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjc7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTU6InRhcmdldF9hY2NvdW50cyI7czo1OiJsYWJlbCI7czo2OiJUYXJnZXQiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo4O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjE5OiJhY2hpZXZlbWVudF9wZXJjZW50IjtzOjU6ImxhYmVsIjtzOjg6IkFjdHVhbCAlIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6OTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo3OiJyZW1hcmtzIjtzOjU6ImxhYmVsIjtzOjc6IlJlbWFya3MiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9fXM6NDE6ImQ2NzYwNmIzZjNkYjJjOTg4ZWY4YmQ4M2MyZGFmNzQ5X3Blcl9wYWdlIjtzOjI6IjUwIjt9czo4OiJmaWxhbWVudCI7YTowOnt9fQ==',1790800418),('UfCYyLhGakv19ga6FrGVkOm5f92FdurdIGln4Z6D',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Microsoft Windows 10.0.26200; en-US) PowerShell/7.6.6','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiVkpOSzFtdHdoMTk2OGw2UWRDeTRadUZIVXdsd2JlTldTNWFhT3JMRyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo1NDoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2FkbWluL2RhaWx5LWRlcG9zaXQtcGVyZm9ybWFuY2VzIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzM6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9sb2dpbiI7czo1OiJyb3V0ZSI7czoyNToiZmlsYW1lbnQuYWRtaW4uYXV0aC5sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1790798717);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_audits`
--

DROP TABLE IF EXISTS `user_audits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_audits` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `performed_by` bigint(20) unsigned DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_audits_user_id_foreign` (`user_id`),
  KEY `user_audits_performed_by_foreign` (`performed_by`),
  CONSTRAINT `user_audits_performed_by_foreign` FOREIGN KEY (`performed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `user_audits_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_audits`
--

LOCK TABLES `user_audits` WRITE;
/*!40000 ALTER TABLE `user_audits` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_audits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_branch`
--

DROP TABLE IF EXISTS `user_branch`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_branch` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `branch_id` bigint(20) unsigned NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_branch_user_id_branch_id_unique` (`user_id`,`branch_id`),
  KEY `user_branch_branch_id_foreign` (`branch_id`),
  CONSTRAINT `user_branch_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_branch_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_branch`
--

LOCK TABLES `user_branch` WRITE;
/*!40000 ALTER TABLE `user_branch` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_branch` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_group_user`
--

DROP TABLE IF EXISTS `user_group_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_group_user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `group_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_group_user_user_id_group_id_unique` (`user_id`,`group_id`),
  KEY `user_group_user_group_id_foreign` (`group_id`),
  CONSTRAINT `user_group_user_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `user_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_group_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_group_user`
--

LOCK TABLES `user_group_user` WRITE;
/*!40000 ALTER TABLE `user_group_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_group_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_groups`
--

DROP TABLE IF EXISTS `user_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_groups` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `color` varchar(7) NOT NULL DEFAULT '#3B82F6',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_groups_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_groups`
--

LOCK TABLES `user_groups` WRITE;
/*!40000 ALTER TABLE `user_groups` DISABLE KEYS */;
INSERT INTO `user_groups` VALUES (1,'Administrators','System administrators with full access','#EF4444',1,'2026-09-30 13:56:21','2026-09-30 13:56:21',NULL),(2,'Managers','District and branch managers','#3B82F6',1,'2026-09-30 13:56:22','2026-09-30 13:56:22',NULL),(3,'Branch Managers','Branch-level managers','#10B981',1,'2026-09-30 13:56:22','2026-09-30 13:56:22',NULL),(4,'Viewers','Read-only users','#6B7280',1,'2026-09-30 13:56:22','2026-09-30 13:56:22',NULL);
/*!40000 ALTER TABLE `user_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` bigint(20) unsigned DEFAULT NULL,
  `branch_id` bigint(20) unsigned DEFAULT NULL,
  `district_id` bigint(20) unsigned DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `job_title` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `mfa_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `mfa_secret` varchar(255) DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_locked` tinyint(1) NOT NULL DEFAULT 0,
  `must_change_password` tinyint(1) NOT NULL DEFAULT 0,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_login_ip` varchar(45) DEFAULT NULL,
  `failed_login_attempts` int(11) NOT NULL DEFAULT 0,
  `locked_at` timestamp NULL DEFAULT NULL,
  `password_changed_at` timestamp NULL DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_group_id_foreign` (`group_id`),
  KEY `users_branch_id_foreign` (`branch_id`),
  KEY `users_district_id_foreign` (`district_id`),
  KEY `users_approved_by_foreign` (`approved_by`),
  CONSTRAINT `users_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_district_id_foreign` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `user_groups` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,'active',1,0,0,NULL,NULL,0,NULL,NULL,'Seid Mohammed','seidm2031@gmail.com','2026-09-30 13:56:20','$2y$12$NRleFAfmZEQyDcafd2ZWJefG4RPETF7nbqFZ99Tkj/hzBqb.2catW',NULL,'2026-09-30 13:56:20','2026-09-30 13:56:20',NULL),(2,1,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,'active',1,0,0,NULL,NULL,0,NULL,NULL,'System Admin','admin@bpms.com',NULL,'$2y$12$TztxosQCTbIUC60FWgFYAuP4/xHYjMksi3d3qXOtKxnABa0EvmsKa',NULL,'2026-09-30 13:56:23','2026-09-30 13:56:23',NULL),(3,2,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,'active',1,0,0,NULL,NULL,0,NULL,NULL,'District Manager','manager@bpms.com',NULL,'$2y$12$D8pqSV9VOGmcNpzD.lLjFOIzGzFshYrLZxS6qOiihtQ75YgJWFIIq',NULL,'2026-09-30 13:56:23','2026-09-30 13:56:23',NULL),(4,3,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,'active',1,0,0,NULL,NULL,0,NULL,NULL,'Branch Manager','branch@bpms.com',NULL,'$2y$12$9UJk1jwgLD2D.m3OanxY3uHVXWbEPaefH9zYNZbuMGmk9U5MJhtZm',NULL,'2026-09-30 13:56:24','2026-09-30 13:56:24',NULL),(5,4,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,'active',1,0,0,NULL,NULL,0,NULL,NULL,'Viewer','viewer@bpms.com',NULL,'$2y$12$TaCYVd8J4W3E33N0lx./aeTs1re/yz2LyNk3ss/pd9J.LhmiGTil6',NULL,'2026-09-30 13:56:25','2026-09-30 13:56:25',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'bpms'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-30 23:33:41
