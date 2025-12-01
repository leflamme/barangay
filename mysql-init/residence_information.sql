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
-- Table structure for table `residence_information`
--

DROP TABLE IF EXISTS `residence_information`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `residence_information` (
  `a_i` int NOT NULL AUTO_INCREMENT,
  `residence_id` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `middle_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `age` varchar(11) COLLATE utf8mb4_general_ci NOT NULL,
  `suffix` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `alias` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `gender` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `civil_status` varchar(36) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `religion` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nationality` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `contact_number` varchar(69) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `email_address` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `address` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `birth_date` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `birth_place` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `municipality` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `zip` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `barangay` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `house_number` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `street` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fathers_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `mothers_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `guardian` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `guardian_contact` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `occupation` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `employer_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `family_relation` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `national_number` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sss_number` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tin_number` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `gsis_number` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pagibig_number` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `philhealth_number` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `bloodtype` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  UNIQUE KEY `a_` (`a_i`)
) ENGINE=InnoDB AUTO_INCREMENT=226 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `residence_information`
--

LOCK TABLES `residence_information` WRITE;
/*!40000 ALTER TABLE `residence_information` DISABLE KEYS */;
INSERT INTO `residence_information` VALUES (182,'43880855422082','Jabidi','Khalid','Santos','21','','','Male','Single','Catholic','Filipino','01931243252','serg@gmail.com','92','2003-09-13','  Manila','sanuusad','1102','dinapala','12','dinamalayan','Dagul Naegar','Pokie Naegar','Pikachu','3154525234','','','','','','','','','','','',''),(183,'44920364405135','Ali','Ai','Ga','20','','','Male','Single','Roman Catholic','Filipino','09183748713','aliaiga@gmail.com','10 Ginhawa Street, Barangay Kalusugan, QC','2004-10-29','Quezon City','','','','','','','','','','','','','','','','','','','','',''),(184,'31548169432031','Mons','','Garcia','25','','','','','Catholic','Filipino','09376487316','monsgarcia@gmail.com','pasay qc','2000-09-21','Pasay','','','','','','','','','','','','','','','','','','','','170349703968ec932ad4fc9.png','../assets/dist/img/170349703968ec932ad4fc9.png'),(185,'65640009717929','Cass','O','Pansacola','22','','','Female','Single','Catholic','Filipino','09982746776','cass@gmail.com','Kalusugan','2003-08-01','     Quezon Province','','','','','','','','','','','','','','','','','','','','',''),(186,'12202311826724','Jaun','','Cruz','14','','','Male','Single','','','09938493849','','Brgy Kalusugan','2010-10-10','','','','','','','','','','','','','','','','','','','','','',''),(187,'24389959441883','Augusto','','Hernandez','10','','','Male','Single','Roman Catholic','Filipino','09946587246','augustohernandez@gmail.com','12, 16th Street, Brgy Kalusugan, QC','2015-08-18',' SLMC, QC','Quezon City','1112','Kalusugan','12','16th','','','','','','','','','','','','','','','',''),(188,'56562352027011','Aria','','Antonina','24','','','Female','Single','','Filipino','09972465782','','37 Sta. Ignacia Street, Brgy Kalusugan, QC','2001-05-23','EAMC, QC','','','','','','','','','','','','','','','','','','','','',''),(189,'71850183277563','Chris','','Sandoval','33','','','Male','Single','','','09994757774','','12 19th Street, Brgy kalusugan, QC','1991-11-06','De Los Santos Medical Center, QC','','','','','','','','','','','','','','','','','','','','',''),(190,'87582342756244','David','','Santos','23','','','Male','Single','Roman Catholic','Filipino','09984766478','davidsantos@gmail.com','5 16th Street, Barangay Kalusugan, Quezon City','2002-04-04','SLMC, QC','Quezon City','1102','Kalusugan','5','16th','','','','','','','','','','','','','','','',''),(193,'9441431111231856','Alison','','Colmenares','18','','Alison Colmenares','Male','Single','','','09485734245','alisoncolmenares@gmail.com','11th Street, Brgy. Kalusugan','2007-05-24','','','','','','',NULL,'','','','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',''),(194,'6646131111233319','Luna','','Guillermo','22','','Luna Guillermo','Female','Single','','','09996576856','lunaguillermo@gmail.com','Brgy Kalusugan','2003-06-20','','','','','','',NULL,'','','','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',''),(195,'4228561112101827','Gregory','','Castillo','21','','Gregory Castillo','Male','Single','','','09274176508','gregcastillo@gmail.com','10th Street, Brgy. Kalusugan','2003-11-17','','','','','','',NULL,'','','','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',''),(196,'9163801112102238','Anthony','','Belmonte','21','','Anthony Belmonte','Male','Single','','','09485237856','antonbelmonte@gmail.com','10th Street, Brgy. Kalusugan','2003-11-15','','','','','','',NULL,'','','','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',''),(197,'6754201113200048','Miles','','Gregorio','10','','Miles Morales','Male','Single','','','09975748885','milesmorales@gmail.com','15th Street, Brgy. Kalusugan','2015-11-11','  ','','','','','','','','','','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',''),(210,'4473891115111400','Elmeranza','','Pamintuan','37','','Elmeranza Pamintuan','Male','Single','','','09443754754','pincaelmer453@gmail.com','Barangay Kalusugan, QC','1988-08-23','','','','','','','','','','','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',''),(211,'7724761115112334','Lei','','Hinanay','21','','Lei Hinanay','Male','Single','','','09274176508','ljbalong29@gmail.com','San Juan','2004-10-29',' ','','','','','','','','','','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',''),(212,'3311811115223335','jade','Castillo','Imperial','22','','jade Imperial','Male','Single','Catholic','Filipino','09398383921','jadecimperial@tua.edu.ph','cftyuigjgcfxchufxryu','2003-03-13','','','','','','','','','','','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',''),(213,'4678561115232522','Francis','','Chua','28','','Francis Chua','Male','Single','Born Again','Filipino','09171234567','francischua@gmail.com','10B Blue St, Kalusugan, Quezon City','1997-02-14','Quezon City','','1611','Kalusugan','10B','','','Kim Chua','','','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',''),(216,'5745831116034318','Marie','','Rosa','2019','','Marie Rosa','Female','Single','','','09274176508','marierosa@gmail.com','Kalusugan','0006-09-11','','','','','','','','','','','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',''),(217,'3937361116101648','Stella','','Carisma','24','','Stella Carisma','Female','Single','','','09483747432','pincaelmer453@gmail.com','1A 10th Street, Barangay Kalusugan, Quezon City 1102','2001-11-02','EAMC, QC','Quezon City','1102','Kalusugan','1A','10th Street','','','','','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',''),(218,'6818481128233623','KC','','Pansacola','25','','KC Pansacola','Female','Single','','','12345678910','kc@gmail.com','Silangan','2000-11-28','','','','','','','','','','','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',''),(219,'4581111128234032','KC','','Pansacola','25','','KC Pansacola','Female','Single','','','12345678910','kc@gmail.com','Silangan','2000-11-28','','','','','','','','','','','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',''),(220,'5394431128234648','Roman','','Ramos','26','','Roman Ramos','Female','Single','','','10123456789','roman@gmail.com','123 Silangan','1999-05-23','','','','','','','','','','','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',''),(221,'6829341129000112','Jona','','Ramos','36','','Jona Ramos','Female','Single','','','32145678910','jona@gmail.com','123 Silangan','1989-06-04','','','','','','','','','','','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',''),(222,'5545311129150407','Enzo','','Juancho','24','','Enzo Juancho','Male','Single','','','35416451321','enzol@gmail.com','123 silangan quezon city','2001-10-25','','','','','','','','','','','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',''),(223,'2636031129152409','Jane','','Llamas','22','','Jane Llamas','Male','Single','','','24165132123','jane@gmail.com','123 Main Street','2003-10-10','','','','','','','','','','','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',''),(224,'4924671129152513','Juan','','Llamas','24','','Juan Llamas','Male','Single','','','21651213215','juan@gmail.com','123 Main Street','2000-12-25','','','','','','','','','','','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',''),(225,'1864081129163236','Mak','','Mamore','25','','Mak Mamore','Male','Single','','','09138847394','mak@gmail.com','Silangan','2000-02-03','','','','','','','','','','','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'','');
/*!40000 ALTER TABLE `residence_information` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-12-01 12:07:49
