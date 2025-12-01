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
-- Table structure for table `first_aid_officers`
--

DROP TABLE IF EXISTS `first_aid_officers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `first_aid_officers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `position` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `contact_number` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `years_of_service` int DEFAULT NULL,
  `specialization` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('Active','Inactive') COLLATE utf8mb4_general_ci DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `first_aid_officers`
--

LOCK TABLES `first_aid_officers` WRITE;
/*!40000 ALTER TABLE `first_aid_officers` DISABLE KEYS */;
INSERT INTO `first_aid_officers` VALUES (1,'Juan','Dela Cruz','Senior Medic','0917-111-1111',1,'Emergency Medicine','Active','2025-10-13 03:12:29','2025-10-13 03:12:29'),(2,'Maria','Santos','Volunteer Nurse','0917-222-2222',2,'First Aid','Active','2025-10-13 03:12:29','2025-10-13 03:12:29'),(3,'Pedro','Cruz','EMT','0917-333-3333',3,'Emergency Response','Active','2025-10-13 03:12:29','2025-10-13 03:12:29');
/*!40000 ALTER TABLE `first_aid_officers` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-12-01 12:11:35
