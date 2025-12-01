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
-- Table structure for table `official_information`
--

DROP TABLE IF EXISTS `official_information`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `official_information` (
  `a_i` int NOT NULL AUTO_INCREMENT,
  `official_id` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `first_name` varchar(69) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `middle_name` varchar(69) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `last_name` varchar(69) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `suffix` varchar(69) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `birth_date` varchar(69) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `birth_place` varchar(69) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `gender` varchar(69) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `age` varchar(69) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `civil_status` varchar(69) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `religion` varchar(69) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `nationality` varchar(69) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `municipality` varchar(69) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `zip` varchar(69) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `barangay` varchar(69) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `house_number` varchar(69) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `street` varchar(69) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `address` varchar(69) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `email_address` varchar(69) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `contact_number` varchar(69) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `fathers_name` varchar(69) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `mothers_name` varchar(69) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `guardian` varchar(69) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `guardian_contact` varchar(69) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `image` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `image_path` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  UNIQUE KEY `a_i` (`a_i`)
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `official_information`
--

LOCK TABLES `official_information` WRITE;
/*!40000 ALTER TABLE `official_information` DISABLE KEYS */;
INSERT INTO `official_information` VALUES (70,'0625202514064336021','Mannie','Barrio','Shevy','','2003-12-12','Manila','Male','21','Single','Catholic','Filipino','','1102','Kalusugan','awaw','qqRQWR','sagvQARv','mani@gmail.com','25352353252','','','','','',''),(71,'1112202514454304825','Allen','','Carismo','','2000-08-24','DeLos Santos Medical Center, Quezon City','Male','25','Single','','Filipino','Quezon City','1112','Kalusugan','20','15th Street','15th Street, Brgy. Kalusugan','allencarismo@gmail.com','09994756834','','','','','',''),(72,'1113202514341918422','Emmanuel','','Cruz','','2003-07-24','EAMC, QC','Male','22','Single','','Filipino','Quezon City','1112','Kalusugan','3 A','10th Street','10th Street, Brgy. Kalusugan','emmancruz@gmail.com','09934356734','','','','','',''),(73,'1113202514533515623','Reighn Erika','','Valmona','','2002-10-03','Cardinal Santos Medical Center, San Juan','Female','23','Single','','Filipino','Quezon City','1112','Kalusugan','21 C','20th Street','20th Street, Brgy. Kalusugan','revalmona@gmail.com','09935828375','','','','','',''),(74,'1113202514564154238','Elmer','','Pinto','','1987-02-19','DeLos Santos Med Center, QC','Male','38','Married','','Filipino','Quezon City','1112','Kalusugan','17 F','15th Street','15th Street, Brgy. Kalusugan','elmerpinto@gmail.com','09848786758','','','','','',''),(75,'1113202520381766844','Jun','','Cristo','','1981-03-25','EAMC, QC','Male','44','Married','','Filipino','Quezon City','1112','Kalusugan','10A','Del Pilar','10 A Del Pilar, Brgy. Kalusugan','juncristo@gmail.com','09847775849','','','','','',''),(76,'1116202510452345226','Alvin','','Conate','','1999-10-07','SLMC, QC','Male','26','Single','','Filipino','Quezon City','1102','Kalusugan','3B','15th Street','3B 15th Street, Brgy. Kalusugan 1102','alvinconate@gmail.com','09348338234','','','none','none','','');
/*!40000 ALTER TABLE `official_information` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-12-01 12:08:58
