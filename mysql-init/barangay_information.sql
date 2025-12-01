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
-- Table structure for table `barangay_information`
--

DROP TABLE IF EXISTS `barangay_information`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `barangay_information` (
  `id` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `barangay` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `zone` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `district` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `address` varchar(69) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `postal_address` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `image` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `image_path` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `flood_history` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'rare',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `barangay_information`
--

LOCK TABLES `barangay_information` WRITE;
/*!40000 ALTER TABLE `barangay_information` DISABLE KEYS */;
INSERT INTO `barangay_information` VALUES ('32432432432432432','Barangay Kalusugan','Area 213','District IV','Quezon CIty ','1102','165897181867eb5acf2e8c4.jpg','../assets/dist/img/165897181867eb5acf2e8c4.jpg','rare');
/*!40000 ALTER TABLE `barangay_information` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-12-01 12:11:12
