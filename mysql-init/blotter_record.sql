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
-- Table structure for table `blotter_record`
--

DROP TABLE IF EXISTS `blotter_record`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `blotter_record` (
  `blotter_id` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `complainant_not_residence` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `statement` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `respodent` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `involved_not_resident` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `statement_person` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `date_incident` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `date_reported` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `type_of_incident` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `location_incident` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `status` varchar(69) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `remarks` varchar(69) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `date_added` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  PRIMARY KEY (`blotter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blotter_record`
--

LOCK TABLES `blotter_record` WRITE;
/*!40000 ALTER TABLE `blotter_record` DISABLE KEYS */;
INSERT INTO `blotter_record` VALUES ('','','Loitering','Juan Cruz','','Kung saan saan nagkakalat','2025-10-14 11:50:24','2025-10-14 11:50:24','Complaint','12th Street Brgy Kalusugan QC','NEW','OPEN','none'),('2025-11-07-0001','','Nagkakalat','Jade Imperial','','Miles Morales','2025-11-07T15:45','2025-11-13 12:09:22','Loitering','20th Street, Barangay Kalusugan','NEW','OPEN','2025-11-13 12:09:22'),('2025-11-11-0001','','Broke items and caused ruckus','Greg Castillo','','Anthony Belmonte','2025-11-11T13:00','2025-11-12 03:33:34','Property Damage and Disturbance','15th Street, Brgy. Kalusugan','NEW','OPEN','2025-11-12 03:33:34'),('2025-11-11-0002','','Broke items and caused ruckus','Greg Castillo','','Anthony Belmonte','2025-11-11T13:00','2025-11-12 03:41:47','Property Damage and Disturbance','15th Street, Brgy. Kalusugan','NEW','OPEN','2025-11-12 03:41:47'),('2025-11-11-0003','','Mindlessly thrown a butt of cigarette on the floor','Chris Sandoval','','Anthony Belmonte','2025-11-11T14:35','2025-11-12 04:27:21','Loitering','In front of Barangay Hall','NEW','OPEN','2025-11-12 04:27:21'),('2025-11-14-0001','','nagtapon ng yosi sa ilog','Jan Cristobal','','Lei Hinanay','2025-11-14T11:15','2025-11-15 15:38:55','nagkakalat','Bridge','NEW','OPEN','2025-11-15 15:38:55'),('2025-11-14-0002','','masyado maingay','Allen Banig','','Lei Hinanay','2025-11-14T23:00','2025-11-15 23:48:56','noise disturbance','Near City Hall','NEW','OPEN','2025-11-15 23:48:56'),('2025-11-15-0001','','Singing karaoke late at night','Ramon Mendoza','','Francis Chua','2025-11-15T23:45','2025-11-15 15:46:15','Noise Disturbance','Blue Street Brgy Kalusugan','NEW','OPEN','2025-11-15 15:46:15'),('2025-11-15-0002','','Causing noise and ruckus','Lei Gailo','','Stella Carisma','2025-11-15T13:25','2025-11-16 02:20:25','Disturbance','In Front of Barangay Hall','NEW','OPEN','2025-11-16 02:20:25'),('2025-11-16-0001','','Singing karaoke late at night','Ramon Mendoza','','Francis Chua','2025-11-16T00:23','2025-11-15 16:24:31','Noise Disturbance','Blue Street, Brgy Kalusugan','NEW','OPEN','2025-11-15 16:24:31');
/*!40000 ALTER TABLE `blotter_record` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-12-01 12:08:12
