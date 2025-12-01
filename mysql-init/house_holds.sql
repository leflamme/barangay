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
-- Table structure for table `house_holds`
--

DROP TABLE IF EXISTS `house_holds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `house_holds` (
  `a_i` int NOT NULL AUTO_INCREMENT,
  `house_hold_id` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `hold_unique` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `purok_id` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `residence_id` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `middle_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `birth_date` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `gender` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `educ_attainment` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `occupation` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `nawasa` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `water_pump` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `water_sealed` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `flush` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `religion` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `ethnicity` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `sangkap_seal` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `is_approved` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `is_resident` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`a_i`)
) ENGINE=InnoDB AUTO_INCREMENT=125 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `house_holds`
--

LOCK TABLES `house_holds` WRITE;
/*!40000 ALTER TABLE `house_holds` DISABLE KEYS */;
INSERT INTO `house_holds` VALUES (119,'765720104206','90635228440419','916259339179300507242022155033612','16455182440138',' First name','Middle name','Last name','2022-10-08','Female','educ','Occupation','YES','YES','YES','YES','Religion','Ethnicity','YES','APPROVED','NO'),(120,'377220153950','90635228440419','916259339179300507242022155033612','16455182440138','qe','qwe','qweqwe','2022-10-12','Male','qweqw','wqe','YES','YES','YES','YES','qwe','eqwe','YES','APPROVED','NO'),(121,'377220153950','90635228440419','916259339179300507242022155033612','16455182440138','First Name','Middle Name','Last Name','2022-10-27','Male','qwewqe','Occupation','YES','YES','YES','NO','Religion','qwe','YES','APPROVED','YES'),(122,'530011579769','70838125253292','916259339179300507242022155033612','54278971251733',' First name','Middle name','Last name','2022-10-08','Male','Educational Attainment','Occupation','YES','YES','YES','YES','Religion','Ethnicity','YES','PENDING','NO'),(123,'85351248693','70838125253292','916259339179300507242022155033612','54278971251733','qwe','wqe','wqewqe','2022-10-15','Female','wqe','wqe','YES','YES','YES','YES','qwe','qwe','YES','PENDING','NO'),(124,'85351248693','70838125253292','916259339179300507242022155033612','54278971251733','Eugine','Palce','ROsillon','1997-09-06','Male','Educational Attainment','Wala','YES','YES','YES','YES','Catholic','Ethnicity','YES','PENDING','YES');
/*!40000 ALTER TABLE `house_holds` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-12-01 12:10:56
