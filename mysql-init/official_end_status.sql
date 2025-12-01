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
-- Table structure for table `official_end_status`
--

DROP TABLE IF EXISTS `official_end_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `official_end_status` (
  `official_id` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `position` varchar(69) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `purok_id` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `senior` varchar(69) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `term_from` varchar(69) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `term_to` varchar(69) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `pwd` varchar(69) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `pwd_info` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `single_parent` varchar(69) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `status` varchar(69) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `voters` varchar(69) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `date_deleted` varchar(69) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  PRIMARY KEY (`official_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `official_end_status`
--

LOCK TABLES `official_end_status` WRITE;
/*!40000 ALTER TABLE `official_end_status` DISABLE KEYS */;
/*!40000 ALTER TABLE `official_end_status` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-12-01 12:08:43
