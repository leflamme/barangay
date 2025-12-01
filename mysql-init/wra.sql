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
-- Table structure for table `wra`
--

DROP TABLE IF EXISTS `wra`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wra` (
  `a_i` int NOT NULL AUTO_INCREMENT,
  `resident_id` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `nhts` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `pregnant` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `menopause` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `achieving` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `ofw` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `fp_method` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `desire_limit` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `desire_space` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `remarks` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`a_i`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wra`
--

LOCK TABLES `wra` WRITE;
/*!40000 ALTER TABLE `wra` DISABLE KEYS */;
INSERT INTO `wra` VALUES (62,'16455182440138','NTHS','YES','YES','YES','YES','FP Method','YES','YES',''),(63,'54278971251733','NTHS','YES','YES','YES','YES','FP Method','YES','YES','');
/*!40000 ALTER TABLE `wra` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-12-01 12:11:04
