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
-- Table structure for table `position`
--

DROP TABLE IF EXISTS `position`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `position` (
  `a_i` int NOT NULL AUTO_INCREMENT,
  `position_id` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `position` varchar(69) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `position_limit` varchar(69) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `position_description` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `color` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  UNIQUE KEY `a_i` (`a_i`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `position`
--

LOCK TABLES `position` WRITE;
/*!40000 ALTER TABLE `position` DISABLE KEYS */;
INSERT INTO `position` VALUES (20,'268778674891281501142022025704271','kagawad','7','','#50d425'),(21,'811981911875128801142022163118246','sk kagawad','7','','#3bc173'),(22,'619131249471207208162022141229307','chairman','1','','#4fb42e'),(23,'448686288587602111132025162448066','safety officer','21','Barangay Tanod','#64c884'),(24,'460511747843798311132025204407881','SK CHairman','1','Head of SK','#aa8b8d');
/*!40000 ALTER TABLE `position` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-12-01 12:08:36
