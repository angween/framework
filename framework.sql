-- MariaDB dump 10.19  Distrib 10.4.24-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: framework
-- ------------------------------------------------------
-- Server version	10.4.24-MariaDB

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
-- Table structure for table `_configuration`
--

DROP TABLE IF EXISTS `_configuration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_configuration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_configuration`
--

LOCK TABLES `_configuration` WRITE;
/*!40000 ALTER TABLE `_configuration` DISABLE KEYS */;
INSERT INTO `_configuration` VALUES (1,'website_name','RLA_tech'),(2,'website_url','localhost'),(3,'sessionName','seSiKu'),(4,'language','ID'),(5,'creator','RLAtech'),(6,'website_description','Web PHP MVC'),(7,'alamat','Padang'),(8,'telepon',''),(9,'email',''),(10,'perawatan','0'),(12,'hari_lalu','1'),(13,'bulan_lalu','6'),(14,'tahun_lalu','2022'),(15,'hari_lalu','1');
/*!40000 ALTER TABLE `_configuration` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_kode_riwayat`
--

DROP TABLE IF EXISTS `_kode_riwayat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_kode_riwayat` (
  `kode` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `task` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `addon` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`kode`),
  UNIQUE KEY `task` (`task`,`addon`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_kode_riwayat`
--

LOCK TABLES `_kode_riwayat` WRITE;
/*!40000 ALTER TABLE `_kode_riwayat` DISABLE KEYS */;
INSERT INTO `_kode_riwayat` VALUES ('LIN',NULL,'Log-In dari %ip%',NULL),('LOU',NULL,'Log-Out dari sistem',NULL),('LUP',NULL,'%uid% update Kelompok %txt%',NULL),('ULV',NULL,'Hak akses user %uid% diperbarui %lvl%',NULL),('UNE',NULL,'User didaftarkan %uid%',NULL),('UU2',NULL,'Memperbarui biodata %uid%: %txt%',NULL),('UUP',NULL,'Biodata diperbarui oleh %uid%: %txt%',NULL);
/*!40000 ALTER TABLE `_kode_riwayat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_navigation_groups`
--

DROP TABLE IF EXISTS `_navigation_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_navigation_groups` (
  `group_id` tinyint(3) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `addon_id` int(3) DEFAULT NULL,
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_navigation_groups`
--

LOCK TABLES `_navigation_groups` WRITE;
/*!40000 ALTER TABLE `_navigation_groups` DISABLE KEYS */;
INSERT INTO `_navigation_groups` VALUES (1,'Stock','bi bi-menu-button-wide',NULL),(2,'Pengaturan','bi bi-gear',NULL),(3,'Drop Order','bi bi-menu-button-wide',NULL),(4,'Distribusi','bi bi-menu-button-wide',NULL),(5,'Strategi','bi bi-menu-button-wide',NULL),(6,'Inventory','bi bi-menu-button-wide',NULL);
/*!40000 ALTER TABLE `_navigation_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_navigation_sections`
--

DROP TABLE IF EXISTS `_navigation_sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_navigation_sections` (
  `section_id` tinyint(3) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `addon_id` int(3) DEFAULT NULL,
  PRIMARY KEY (`section_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_navigation_sections`
--

LOCK TABLES `_navigation_sections` WRITE;
/*!40000 ALTER TABLE `_navigation_sections` DISABLE KEYS */;
INSERT INTO `_navigation_sections` VALUES (1,'Settings',NULL),(2,'Warehouse',NULL),(3,'Utama',NULL),(4,'Report',NULL);
/*!40000 ALTER TABLE `_navigation_sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_navigations`
--

DROP TABLE IF EXISTS `_navigations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_navigations` (
  `page_id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `page` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `link` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `private` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `section_id` int(5) DEFAULT NULL,
  `group_id` int(5) DEFAULT NULL,
  `urutan` tinyint(3) unsigned DEFAULT NULL,
  `addon_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`page_id`),
  UNIQUE KEY `page` (`page`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_navigations`
--

LOCK TABLES `_navigations` WRITE;
/*!40000 ALTER TABLE `_navigations` DISABLE KEYS */;
INSERT INTO `_navigations` VALUES (1,'Dashboard','dashboard','bi bi-speedometer',0,NULL,NULL,1,NULL,1),(2,'Pengguna','user','bi bi-people',1,1,2,1,NULL,1),(3,'Navigasi','navigation','bi bi-menu-button-wide',1,1,2,2,NULL,1);
/*!40000 ALTER TABLE `_navigations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_permission_navigations`
--

DROP TABLE IF EXISTS `_permission_navigations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_permission_navigations` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `permission_id` int(3) NOT NULL,
  `page_id` int(4) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permission_id` (`permission_id`,`page_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_permission_navigations`
--

LOCK TABLES `_permission_navigations` WRITE;
/*!40000 ALTER TABLE `_permission_navigations` DISABLE KEYS */;
INSERT INTO `_permission_navigations` VALUES (1,2,2),(2,2,3),(3,3,5),(4,3,4),(5,2,4),(6,2,5),(7,2,6),(8,2,7);
/*!40000 ALTER TABLE `_permission_navigations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_permissions`
--

DROP TABLE IF EXISTS `_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_permissions` (
  `permission_id` int(3) unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `keterangan` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kunci_nama` tinyint(1) DEFAULT NULL,
  `builtin` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`permission_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_permissions`
--

LOCK TABLES `_permissions` WRITE;
/*!40000 ALTER TABLE `_permissions` DISABLE KEYS */;
INSERT INTO `_permissions` VALUES (1,'Administrator','Web Master',1,1),(2,'Superuser','Asisten Web',1,1),(3,'Supervisor','Supervisor Sales',0,0),(4,'Salesman','Salesman',0,0);
/*!40000 ALTER TABLE `_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_search_kode`
--

DROP TABLE IF EXISTS `_search_kode`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_search_kode` (
  `key_code` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `controller` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `addons` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`key_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_search_kode`
--

LOCK TABLES `_search_kode` WRITE;
/*!40000 ALTER TABLE `_search_kode` DISABLE KEYS */;
INSERT INTO `_search_kode` VALUES ('USER','User','User account',1,NULL);
/*!40000 ALTER TABLE `_search_kode` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_user_permission`
--

DROP TABLE IF EXISTS `_user_permission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_user_permission` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` smallint(4) NOT NULL,
  `permission_id` int(3) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id_2` (`user_id`,`permission_id`),
  KEY `user_id` (`user_id`),
  KEY `permission_id` (`permission_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_user_permission`
--

LOCK TABLES `_user_permission` WRITE;
/*!40000 ALTER TABLE `_user_permission` DISABLE KEYS */;
INSERT INTO `_user_permission` VALUES (1,1,1),(2,1,2),(3,2,2),(4,4,3),(5,5,4);
/*!40000 ALTER TABLE `_user_permission` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_user_riwayat`
--

DROP TABLE IF EXISTS `_user_riwayat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_user_riwayat` (
  `id` int(4) NOT NULL,
  `riwayat` longtext COLLATE utf8mb4_unicode_ci DEFAULT '[]',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_user_riwayat`
--

LOCK TABLES `_user_riwayat` WRITE;
/*!40000 ALTER TABLE `_user_riwayat` DISABLE KEYS */;
INSERT INTO `_user_riwayat` VALUES (1,'[{\"tm\": \"2024-01-31 18:30:36\", \"rw\": \"LIN%IP:Angween (==1)\"}, {\"tm\": \"2024-01-31 18:30:25\", \"rw\": \"LOU\"}, {\"tm\": \"2024-01-31 17:55:54\", \"rw\": \"LIN%IP:Angween (==1)\"}, {\"tm\": \"2023-05-30 08:54:41\", \"rw\": \"LIN%IP:Angween (==1)\"}, {\"tm\": \"2023-05-29 12:04:20\", \"rw\": \"LIN%IP:Angween (==1)\"}, {\"tm\": \"2023-05-29 10:22:45\", \"rw\": \"LIN%IP:Angween (==1)\"}, {\"tm\": \"2023-05-26 11:36:10\", \"rw\": \"LIN%IP:Angween (==1)\"}, {\"tm\": \"2023-03-02 17:02:39\", \"rw\": \"LIN%IP:Angween (==1)\"}, {\"tm\": \"2023-03-02 17:02:28\", \"rw\": \"LOU\"}, {\"tm\": \"2023-03-02 17:02:13\", \"rw\": \"LIN%IP:Angween (==1)\"}, {\"tm\": \"2023-03-02 17:01:36\", \"rw\": \"LOU\"}, {\"tm\": \"2023-03-02 16:59:37\", \"rw\": \"LIN%IP:Angween (==1)\"}, {\"tm\": \"2023-02-28 15:44:44\", \"rw\": \"LIN%IP:Angween (==1)\"}, {\"tm\": \"2023-02-28 12:06:04\", \"rw\": \"LIN%IP:Angween (==1)\"}, {\"tm\": \"2023-02-28 12:05:43\", \"rw\": \"LOU\"}, {\"tm\": \"2023-02-28 12:05:37\", \"rw\": \"LIN%IP:Angween (==1)\"}, {\"tm\": \"2023-02-28 11:09:45\", \"rw\": \"LOU\"}, {\"tm\": \"2023-02-28 10:37:03\", \"rw\": \"LIN%IP:Angween (==1)\"}, {\"tm\": \"2023-01-28 11:11:02\", \"rw\": \"LIN%IP:Angween (==1)\"}, {\"tm\": \"2023-01-24 22:10:11\", \"rw\": \"LIN%IP:Angween (==1)\"}, {\"tm\": \"2023-01-24 22:05:41\", \"rw\": \"LOU\"}, {\"tm\": \"2023-01-24 22:04:17\", \"rw\": \"LIN%IP:Angween (==1)\"}, {\"tm\": \"2023-01-24 20:56:15\", \"rw\": \"LOU\"}, {\"tm\": \"2023-01-24 20:51:30\", \"rw\": \"LIN%IP:Angween (==1)\"}, {\"tm\": \"2023-01-21 09:34:42\", \"rw\": \"LIN%IP:Angween (==1)\"}, {\"tm\": \"2023-01-17 10:34:16\", \"rw\": \"LIN%IP:Angween (==1)\"}, {\"tm\": \"2023-01-17 10:27:48\", \"rw\": \"LOU\"}, {\"tm\": \"2023-01-17 10:01:53\", \"rw\": \"LIN%IP:Angween (==1)\"}, {\"tm\": \"2023-01-12 14:51:21\", \"rw\": \"LIN%IP:Angween (127.0.0.1)\"}, {\"tm\": \"2023-01-09 15:13:21\", \"rw\": \"LOU\"}, {\"tm\": \"2023-01-09 15:10:57\", \"rw\": \"LIN%IP:Angween (==1)\"}]'),(2,'[{\"tm\": \"2023-01-12 14:53:28\", \"rw\": \"LIN%IP:Angween (127.0.0.1)\"}, {\"tm\": \"2023-01-12 14:53:17\", \"rw\": \"LOU\"}, {\"tm\": \"2023-01-12 14:53:09\", \"rw\": \"LIN%IP:Angween (127.0.0.1)\"}, {\"tm\": \"2023-01-12 14:53:06\", \"rw\": \"LOU\"}, {\"tm\": \"2023-01-12 14:51:26\", \"rw\": \"LIN%IP:Angween (127.0.0.1)\"}, {\"tm\": \"2023-01-12 14:20:19\", \"rw\": \"LIN%IP:Angween (127.0.0.1)\"}, {\"tm\": \"2023-01-12 14:19:20\", \"rw\": \"LIN%IP:Angween (127.0.0.1)\"}, {\"tm\": \"2023-01-12 14:07:46\", \"rw\": \"LIN%IP:Angween (127.0.0.1)\"}]');
/*!40000 ALTER TABLE `_user_riwayat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `_users`
--

DROP TABLE IF EXISTS `_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_users` (
  `user_id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `user_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gender` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'M',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `handphone` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activation_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_activation_request` datetime DEFAULT NULL,
  `lost_password_request` tinyint(1) unsigned DEFAULT NULL,
  `active` tinyint(1) unsigned NOT NULL,
  `tgl_mendaftar` datetime NOT NULL,
  `last_sign_in` datetime DEFAULT NULL,
  `help` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `nav_expanded` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_name` (`user_name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_users`
--

LOCK TABLES `_users` WRITE;
/*!40000 ALTER TABLE `_users` DISABLE KEYS */;
INSERT INTO `_users` VALUES (1,'superadmin','Web Administrator','M','$2y$08$I9LKDo9wXseKTSQ25A1NDOB1nwjnxFBTtT.69xyNjQ6mLFKqtWlpO','lazwardi@gmail.com','08222333888',NULL,NULL,NULL,1,'2022-01-16 16:38:34',NULL,1,1),(2,'admin','Sales Admin','M','$2y$08$bHzrVLbHZwVClder/.SzOuSfPsT75LJGDcZVS5Wgivlxtj6VS3wl.',NULL,'082',NULL,NULL,NULL,1,'2022-01-16 16:38:34',NULL,1,1);
/*!40000 ALTER TABLE `_users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-01-31 19:29:17
