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
-- Table structure for table `vaccine`
--

DROP TABLE IF EXISTS `vaccine`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vaccine` (
  `a_i` int NOT NULL AUTO_INCREMENT,
  `vaccine_id` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `residence_id` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `vaccine` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `second_vaccine` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `first_dose_date` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `second_dose_date` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `booster` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `booster_date` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `second_booster` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `second_booster_date` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`a_i`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vaccine`
--

LOCK TABLES `vaccine` WRITE;
/*!40000 ALTER TABLE `vaccine` DISABLE KEYS */;
INSERT INTO `vaccine` VALUES (35,'3267818051106726','16455182440138','first','second','2022-10-01','2022-10-02','first b','2022-10-03','second b','2022-10-04'),(36,'9517807083807772','54278971251733','first','second','2022-10-01','2022-10-02','first b','2022-10-03','second b','2022-10-04');
/*!40000 ALTER TABLE `vaccine` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-12-01 12:11:27
