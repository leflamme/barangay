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
-- Table structure for table `fire_hydrants`
--

DROP TABLE IF EXISTS `fire_hydrants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fire_hydrants` (
  `id` int NOT NULL AUTO_INCREMENT,
  `hydrant_number` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `location` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `last_inspection_date` date DEFAULT NULL,
  `status` enum('Operational','Under Maintenance','Out of Service') COLLATE utf8mb4_general_ci DEFAULT 'Operational',
  `notes` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fire_hydrants`
--

LOCK TABLES `fire_hydrants` WRITE;
/*!40000 ALTER TABLE `fire_hydrants` DISABLE KEYS */;
INSERT INTO `fire_hydrants` VALUES (1,'FH-001','Purok 1 Street','2025-01-15','Operational','Near barangay hall','2025-10-13 03:11:16','2025-10-13 03:11:16'),(2,'FH-002','Barangay Hall corner','2025-01-10','Operational','Main entrance','2025-10-13 03:11:16','2025-10-13 03:11:16'),(3,'FH-003','Market Area','2025-01-20','Operational','Public market','2025-10-13 03:11:16','2025-10-13 03:11:16'),(4,'FH-004','Riverside Road','2025-01-05','Under Maintenance','Needs repair','2025-10-13 03:11:16','2025-10-13 03:11:16');
/*!40000 ALTER TABLE `fire_hydrants` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-12-01 12:07:57
