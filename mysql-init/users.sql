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
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `a_i` int NOT NULL AUTO_INCREMENT,
  `id` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `middle_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `last_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `username` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `user_type` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `contact_number` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `image` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `image_path` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  UNIQUE KEY `a_i` (`a_i`)
) ENGINE=InnoDB AUTO_INCREMENT=266 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (52,'1506135735699','Admin','Admin','Admin','admin123','admin123','admin','11111111111','182708071361a0f053c94fb.png','../assets/dist/img/182708071361a0f053c94fb.png'),(195,'174668789044820710152022021619941','Secretary','Secretary','Secretary','secretary123','secretary123','secretary','99999999999','',''),(205,'43880855422082','Jabidi','Khalid','Santos','jabidi123','2022303672','resident','01931243252','',''),(206,'44920364405135','Ali','Ai','Ga','aliaiga1121','aliaiga1121','resident','09183748713','',''),(207,'31548169432031','Mons','','Garcia','monsgarcia','monsgarcia','resident','09376487316','170349703968ec932ad4fc9.png','../assets/dist/img/170349703968ec932ad4fc9.png'),(208,'65640009717929','Cass','O','Pansacola','kirstencassey','kirstencassey','resident','09982746776','',''),(209,'12202311826724','Jaun','','Cruz','jauncruz','jauncruz','resident','09938493849','',''),(210,'24389959441883','Augusto','','Hernandez','augustohernandez','augustoqc','resident','09946587246','',''),(211,'56562352027011','Aria','','Antonina','ariantonina','ariantonina','resident','09972465782','',''),(212,'71850183277563','Chris','','Sandoval','chrissandoval','chrissandoval','resident','09994757774','',''),(213,'87582342756244','David','','Santos','davidsantos','davidsantos','resident','09984766478','',''),(229,'6646131111233319','Luna','','Guillermo','lunaguillermo','lunaguillermo','resident','09996576856','',''),(230,'4228561112101827','Gregory','','Castillo','gregcastillo','gregcastillo','resident','09274176508','',''),(231,'9163801112102238','Anthony','','Belmonte','antonbelmonte','4nt0nBelmont3','resident','09485237856','',''),(232,'1113202514341918422','Emmanuel','','Cruz','emmancruz','emmancruz','secretary','09934356734','',''),(233,'1113202514533515623','Reighn Erika','','Valmona','revalmona','revalmona','secretary','09935828375','',''),(234,'1113202514564154238','Elmer','','Pinto','elmerpinto','elmerpinto','secretary','09848786758','',''),(235,'6754201113200048','Miles','','Gregorio','milesmorales','miles.morales24','resident','09975748885','',''),(236,'1113202520381766844','Jun','','Cristo','juncristo','juncristo','secretary','09847775849','',''),(249,'4473891115111400','Elmeranza','','Pamintuan','elmerpam','elmerpam','resident','09443754754','',''),(250,'7724761115112334','Lei','','Hinanay','leihinanay','leihinanay','resident','09274176508','',''),(251,'3311811115223335','jade','Castillo','Imperial','imperialjade2','2022303672','resident','09398383921','',''),(252,'4678561115232522','Francis','','Chua','francischua','1234francischua','resident','09171234567','',''),(254,'8308211116031735','Alessandra','','Lopez','alessandralopez','alessandralopez','resident','09919097737','',''),(255,'5745831116034318','Marie','','Rosa','marierosa','marierosa','resident','09274176508','',''),(256,'3937361116101648','Stella','','Carisma','stellacarisma','stellacarisma','resident','09483747432','',''),(257,'1116202510452345226','Alvin','','Conate','alvinconate','alvinconate','secretary','09348338234','',''),(258,'6818481128233623','KC','','Pansacola','kcpansacola','kcpansacola','resident','12345678910','',''),(259,'4581111128234032','KC','','Pansacola','pansacolakc','pansacolakc','resident','12345678910','',''),(260,'5394431128234648','Roman','','Ramos','romanramos','romanramos','resident','10123456789','',''),(261,'6829341129000112','Jona','','Ramos','jonaramos','jonaramos','resident','32145678910','',''),(262,'5545311129150407','Enzo','','Juancho','enzojuancho123','enzojuancho123','resident','35416451321','',''),(263,'2636031129152409','Jane','','Llamas','janellamas123','janellamas123','resident','24165132123','',''),(264,'4924671129152513','Juan','','Llamas','juanllamas123','juanllamas123','resident','21651213215','',''),(265,'1864081129163236','Mak','','Mamore','makmamore','makmamore','resident','09138847394','','');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-12-01 12:08:28
