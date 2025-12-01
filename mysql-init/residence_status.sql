-- MySQL dump 10.13  Distrib 8.0.44, for Win64 (x86_64)
--
-- Host: hopper.proxy.rlwy.net    Database: railway
-- ------------------------------------------------------
-- Server version	9.4.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `residence_status`
--

DROP TABLE IF EXISTS `residence_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `residence_status` (
  `a_i` int NOT NULL AUTO_INCREMENT,
  `residence_id` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `status` varchar(69) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `is_approved` varchar(255) COLLATE utf8mb4_general_ci DEFAULT 'PENDING',
  `voters` varchar(69) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `pwd` varchar(69) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `pwd_info` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `senior` varchar(69) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `single_parent` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `wra` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `4ps` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `purok_id` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `precint_id` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `archive` varchar(69) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `date_added` varchar(69) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `date_archive` varchar(69) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `date_unarchive` varchar(69) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  UNIQUE KEY `a_i` (`a_i`)
) ENGINE=InnoDB AUTO_INCREMENT=223 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `residence_status`
--

LOCK TABLES `residence_status` WRITE;
/*!40000 ALTER TABLE `residence_status` DISABLE KEYS */;
INSERT INTO `residence_status` VALUES (182,'43880855422082','ACTIVE','','YES','NO','','NO','NO','','','','','NO','06/25/2025 02:00 PM','none','none'),(183,'44920364405135','INACTIVE','','YES','NO','','NO','NO','','','','','YES','07/18/2025 10:41 AM','07/18/2025 09:18 AM','none'),(184,'31548169432031','ACTIVE','','','','','NO','','','','','','NO','07/18/2025 01:52 PM','none','none'),(185,'65640009717929','ACTIVE','','YES','YES','deaf','NO','NO','','','','','NO','07/18/2025 03:07 PM','none','none'),(186,'12202311826724','ACTIVE','','NO','NO','','NO','NO','','','','','NO','09/13/2025 04:54 PM','none','none'),(187,'24389959441883','ACTIVE','','NO','NO','','NO','NO','','','','','NO','10/13/2025 10:50 PM','none','none'),(188,'56562352027011','ACTIVE','','YES','NO','','NO','','','','','','NO','10/14/2025 11:00 AM','none','none'),(189,'71850183277563','ACTIVE','','YES','NO','','NO','','','','','','NO','10/14/2025 11:10 AM','none','none'),(190,'87582342756244','ACTIVE','','NO','NO','','NO','NO','','','','','NO','10/14/2025 06:09 PM','none','none'),(193,'9163801112102238','ACTIVE','PENDING','YES','NO','','NO','',NULL,NULL,NULL,NULL,'NO','11/12/2025 10:22 AM','none','none'),(194,'6754201113200048','INACTIVE','PENDING','NO','NO','','NO','NO',NULL,NULL,NULL,NULL,'YES','11/13/2025 08:00 PM','11/15/2025 05:25 AM','none'),(207,'4473891115111400','ACTIVE','PENDING','YES','NO','','NO','',NULL,NULL,NULL,NULL,'NO','11/15/2025 11:14 AM','none','none'),(208,'7724761115112334','ACTIVE','PENDING','YES','NO','','NO','NO',NULL,NULL,NULL,NULL,'NO','11/15/2025 11:23 AM','none','none'),(209,'3311811115223335','ACTIVE','PENDING','YES','NO','','NO','NO',NULL,NULL,NULL,NULL,'NO','11/15/2025 10:33 PM','none','none'),(210,'4678561115232522','ACTIVE','PENDING','NO','NO','','NO','NO',NULL,NULL,NULL,NULL,'NO','11/15/2025 11:25 PM','none','none'),(213,'5745831116034318','ACTIVE','PENDING','YES','NO','','YES','',NULL,NULL,NULL,NULL,'NO','11/16/2025 03:43 AM','none','none'),(214,'3937361116101648','ACTIVE','PENDING','YES','NO','','NO','NO',NULL,NULL,NULL,NULL,'NO','11/16/2025 10:16 AM','none','none'),(215,'6818481128233623','INACTIVE','PENDING','YES','NO','','NO','',NULL,NULL,NULL,NULL,'YES','11/28/2025 11:36 PM','11/28/2025 03:37 PM','none'),(216,'4581111128234032','INACTIVE','PENDING','YES','NO','','NO','',NULL,NULL,NULL,NULL,'YES','11/28/2025 11:40 PM','11/29/2025 07:29 AM','none'),(217,'5394431128234648','ACTIVE','PENDING','YES','YES','','NO','',NULL,NULL,NULL,NULL,'NO','11/28/2025 11:46 PM','none','none'),(218,'6829341129000112','ACTIVE','PENDING','YES','NO','','NO','',NULL,NULL,NULL,NULL,'NO','11/29/2025 12:01 AM','none','none'),(219,'5545311129150407','INACTIVE','PENDING','NO','NO','','NO','NO',NULL,NULL,NULL,NULL,'YES','11/29/2025 03:04 PM','11/29/2025 07:29 AM','none'),(220,'2636031129152409','INACTIVE','PENDING','YES','NO','','NO','',NULL,NULL,NULL,NULL,'YES','11/29/2025 03:24 PM','11/29/2025 07:29 AM','none'),(221,'4924671129152513','INACTIVE','PENDING','YES','NO','','NO','',NULL,NULL,NULL,NULL,'YES','11/29/2025 03:25 PM','11/29/2025 07:29 AM','none'),(222,'1864081129163236','ACTIVE','PENDING','YES','NO','','NO','',NULL,NULL,NULL,NULL,'NO','11/29/2025 04:32 PM','none','none');
/*!40000 ALTER TABLE `residence_status` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-12-01 12:10:25
