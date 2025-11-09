-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 09, 2025 at 10:08 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `barangay`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL,
  `message` varchar(255) NOT NULL DEFAULT 'none',
  `date` varchar(255) NOT NULL DEFAULT 'none',
  `status` varchar(255) NOT NULL DEFAULT 'none'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `message`, `date`, `status`) VALUES
(1250, 'ADMIN: UPDATED OFFICIAL POSITION -  0401202511295839347 |  FROM CHAIRMANS TO CHAIRMAN', '1-4-2025 1:05 PM', 'update'),
(1251, 'ADMIN: DELETED POSITION -  77311317124789201092022180612765 | chairmans', '1-4-2025 7:05 AM', 'delete'),
(1252, 'ADMIN: ADDED RESIDENT - 23388956417195 |  Alexandra Kane Non commodi saepe se', '1-4-2025 1:06 PM', 'create'),
(1253, 'ADMIN: ADDED RESIDENT - 3188696235402 |  Alexandra Mooney In rem voluptatem E', '1-4-2025 1:06 PM', 'create'),
(1254, 'ADMIN: Admin Admin | LOGOUT', '1-4-2025 7:07 AM', 'logout'),
(1255, 'ADMIN: Admin Admin | LOGIN', '1-4-2025 1:09 PM', 'login'),
(1256, 'ADMIN: ADDED BLOTTER RECORD  -  3647560271426891 | Complainant - Alexandra Kane | Incident - Incident | Date Incident 2025-04-19T13:10 | Location Incident Location of Incident | Complainant Statement - Complainant Statement | Respondent - Respondent', '1-4-2025 1:10 PM', 'delete'),
(1257, 'ADMIN: ADDED BLOTTER RECORD  -  3647560271426891 | Person Involved - Alexandra Kane | Incident - Incident | Date Incident 2025-04-19T13:10 | Location Incident Location of Incident | Complainant Statement - Complainant Statement | Respondent - Respondent', '1-4-2025 1:10 PM', 'delete'),
(1258, 'ADMIN: ADDED BLOTTER RECORD  -  3647560271426891 | Person Not Resident - wq | Incident - Incident | Date Incident 2025-04-19T13:10 | Location Incident Location of Incident | Complainant Statement - ewqewq | Respondent - Respondent', '1-4-2025 1:10 PM', 'delete'),
(1259, 'ADMIN: ADDED BLOTTER RECORD  -  3647560271426891 | Complainant Not Resident - Complainant Not Resident | Incident - Incident | Date Incident 2025-04-19T13:10 | Location Incident Location of Incident | Complainant Statement - Complainant Statement | Respon', '1-4-2025 1:10 PM', 'delete'),
(1260, 'ADMIN: ADDED RESIDENT - 37404238492438 |  Miriam Frost Harum sit ut provide', '1-4-2025 1:10 PM', 'create'),
(1261, 'ADMIN: ADDED RESIDENT - 1086692484891 |  Jelani Ellison Dolorum qui qui id v', '1-4-2025 1:11 PM', 'create'),
(1262, 'ADMIN: ADDED RESIDENT - 12435095932673 |  Darrel Kline Quas perferendis aut', '1-4-2025 1:11 PM', 'create'),
(1263, 'ADMIN: ADDED RESIDENT - 34151365970057 |  Moana Burt Dolorum fugiat nisi', '1-4-2025 1:11 PM', 'create'),
(1264, 'ADMIN: ADDED OFFICIAL - 0401202513120186625 | KAGAWAD Branden Whitney Minim dolores velit | START 2021-12-17 END 1971-04-06', '1-4-2025 1:12 PM', 'create'),
(1265, 'ADMIN: Admin Admin | LOGOUT', '1-4-2025 7:12 AM', 'logout'),
(1266, 'ADMIN: Admin Admin | LOGIN', '1-4-2025 1:15 PM', 'login'),
(1267, 'ADMIN: Admin Admin | LOGIN', '25-6-2025 9:19 AM', 'login'),
(1268, 'ADMIN: Admin Admin | LOGOUT', '25-6-2025 3:20 AM', 'logout'),
(1269, 'ADMIN: Admin Admin | LOGIN', '25-6-2025 9:35 AM', 'login'),
(1270, 'ADMIN: Admin Admin | LOGOUT', '25-6-2025 3:35 AM', 'logout'),
(1271, 'ADMIN: Admin Admin | LOGIN', '25-6-2025 1:56 PM', 'login'),
(1272, 'ADMIN: Admin Admin | LOGOUT', '25-6-2025 7:57 AM', 'logout'),
(1273, 'RESIDENT: REGISTER RESIDENT - 43880855422082 |  Abdul Naegar ', '25-6-2025 2:00 PM', 'create'),
(1274, 'RESIDENT: Abdul Naegar | LOGIN', '25-6-2025 2:00 PM', 'login'),
(1275, 'RESIDENT: Abdul Naegar | LOGOUT', '25-6-2025 8:00 AM', 'logout'),
(1276, 'ADMIN: Admin Admin | LOGIN', '25-6-2025 2:00 PM', 'login'),
(1277, 'ADMIN: Admin Admin | LOGOUT', '25-6-2025 8:01 AM', 'logout'),
(1278, 'RESIDENT: Abdul Naegar | LOGIN', '25-6-2025 2:02 PM', 'login'),
(1279, 'RESIDENT - 43880855422082: Abdul Naegar | REQUEST CERTIFICATE - CERTIFICATE OF INDIGENCY', '25-6-2025 2:02 PM', 'create'),
(1280, 'RESIDENT: Abdul Naegar | LOGOUT', '25-6-2025 8:02 AM', 'logout'),
(1281, 'ADMIN: Admin Admin | LOGIN', '25-6-2025 2:02 PM', 'login'),
(1282, 'ADMIN: RESIDENT REQUEST CERTIFICATE ACCEPTED - 43880855422082 | PURPOSE CERTIFICATE OF INDIGENCY | MESSAGE none | DATE ISSUED 2025-12-12 | DATE EXPIRED 2026-12-12', '25-6-2025 8:03 AM', 'updated'),
(1283, 'ADMIN: ADDED OFFICIAL - 0625202514064336021 | CHAIRMAN mani pogie Shival  | START 2025-12-12 END 2026-12-12', '25-6-2025 2:06 PM', 'create'),
(1284, 'ADMIN: Admin Admin | LOGOUT', '25-6-2025 8:10 AM', 'logout'),
(1285, 'ADMIN: Admin Admin | LOGIN', '25-6-2025 2:44 PM', 'login'),
(1286, 'ADMIN: Admin Admin | LOGOUT', '25-6-2025 8:44 AM', 'logout'),
(1287, 'ADMIN: Admin Admin | LOGIN', '25-6-2025 2:50 PM', 'login'),
(1288, 'ADMIN: Admin Admin | LOGOUT', '25-6-2025 8:50 AM', 'logout'),
(1289, 'ADMIN: Admin Admin | LOGIN', '25-6-2025 3:14 PM', 'login'),
(1290, 'ADMIN: UPDATED RESIDENT USER`S FIRST NAME -  43880855422082 |  FROM Abdul TO jabidi', '25-6-2025 9:16 AM', 'update'),
(1291, 'ADMIN: UPDATED RESIDENT USER`S MIDDLE NAME -  43880855422082 |  FROM Shakur TO Khalid', '25-6-2025 9:16 AM', 'update'),
(1292, 'ADMIN: UPDATED RESIDENT USER`S LAST NAME -  43880855422082 |  FROM Naegar TO Santos', '25-6-2025 9:16 AM', 'update'),
(1293, 'ADMIN: Admin Admin | LOGOUT', '25-6-2025 9:18 AM', 'logout'),
(1294, 'ADMIN: Admin Admin | LOGIN', '26-6-2025 8:33 AM', 'login'),
(1295, 'ADMIN: Admin Admin | LOGOUT', '26-6-2025 2:37 AM', 'logout'),
(1296, 'OFFICIAL: Secretary Secretary | LOGIN', '26-6-2025 8:37 AM', 'login'),
(1297, 'OFFICAL: Secretary Secretary - 174668789044820710152022021619941 | UPDATED RESIDENT  STATUS - 43880855422082 |  FROM ACTIVE TO INACTIVE', '26-6-2025 2:38 AM', 'update'),
(1298, 'OFFICAL: Secretary Secretary - 174668789044820710152022021619941 | UPDATED RESIDENT  STATUS - 43880855422082 |  FROM INACTIVE TO ACTIVE', '26-6-2025 2:38 AM', 'update'),
(1299, 'OFFICAL: Secretary Secretary - 174668789044820710152022021619941 | UPDATED RESIDENT SINGLE PARENT - 43880855422082 |  FROM NO TO YES', '26-6-2025 2:40 AM', 'update'),
(1300, 'OFFICAL: Secretary Secretary - 174668789044820710152022021619941 | UPDATED RESIDENT EMAIL ADDRESS - 43880855422082 |  FROM SERG@ TO serg@gmail.com', '26-6-2025 2:40 AM', 'update'),
(1301, 'OFFICIAL: Secretary Secretary | LOGOUT', '26-6-2025 2:51 AM', 'logout'),
(1302, 'ADMIN: Admin Admin | LOGIN', '26-6-2025 9:40 AM', 'login'),
(1303, 'ADMIN: Admin Admin | LOGOUT', '26-6-2025 3:40 AM', 'logout'),
(1304, 'RESIDENT: jabidi Santos | LOGIN', '26-6-2025 9:40 AM', 'login'),
(1305, 'ADMIN: Admin Admin | LOGIN', '1-7-2025 8:32 PM', 'login'),
(1306, 'ADMIN: Admin Admin | LOGOUT', '1-7-2025 2:32 PM', 'logout'),
(1307, 'ADMIN: Admin Admin | LOGIN', '10-7-2025 11:07 PM', 'login'),
(1308, 'ADMIN:   | LOGOUT', '10-7-2025 5:15 PM', 'logout'),
(1309, 'RESIDENT: jabidi Santos | LOGIN', '10-7-2025 11:16 PM', 'login'),
(1310, 'RESIDENT: jabidi Santos - 43880855422082 | UPDATED RESIDENT FIRST NAME - 43880855422082 |  FROM jabidi TO Jabidi', '10-7-2025 5:18 PM', 'update'),
(1311, 'RESIDENT: jabidi Santos - 43880855422082 | UPDATED RESIDENT SINGLE PARENT - 43880855422082 |  FROM YES TO NO', '10-7-2025 5:18 PM', 'update'),
(1312, 'RESIDENT: jabidi Santos - 43880855422082 | UPDATED RESIDENT MUNICIPALITY - 43880855422082 |  FROM evqqrw TO sanuusad', '10-7-2025 5:18 PM', 'update'),
(1313, 'RESIDENT: jabidi Santos - 43880855422082 | UPDATED RESIDENT BARANGAY - 43880855422082 |  FROM qvwrwqrv TO dinapala', '10-7-2025 5:18 PM', 'update'),
(1314, 'RESIDENT: jabidi Santos - 43880855422082 | UPDATED RESIDENT HOUSE NUMBER - 43880855422082 |  FROM 999 TO 12', '10-7-2025 5:18 PM', 'update'),
(1315, 'RESIDENT: jabidi Santos - 43880855422082 | UPDATED RESIDENT STREET - 43880855422082 |  FROM qrvwrqwvqvr TO dinamalayan', '10-7-2025 5:18 PM', 'update'),
(1316, 'RESIDENT: Jabidi Santos | LOGOUT', '10-7-2025 5:18 PM', 'logout'),
(1317, 'ADMIN: Admin Admin | LOGIN', '10-7-2025 11:18 PM', 'login'),
(1318, 'ADMIN: Admin Admin | LOGIN', '12-7-2025 1:33 PM', 'login'),
(1319, 'ADMIN: Admin Admin | LOGOUT', '12-7-2025 7:39 AM', 'logout'),
(1320, 'ADMIN: Admin Admin | LOGIN', '12-7-2025 3:03 PM', 'login'),
(1321, 'ADMIN: Admin Admin | LOGIN', '12-7-2025 3:06 PM', 'login'),
(1322, 'ADMIN: Admin Admin | LOGIN', '12-7-2025 3:12 PM', 'login'),
(1323, 'ADMIN: Admin Admin | LOGIN', '12-7-2025 4:30 PM', 'login'),
(1324, 'RESIDENT:   | LOGOUT', '13-7-2025 8:25 AM', 'logout'),
(1325, 'ADMIN: Admin Admin | LOGIN', '13-7-2025 2:28 PM', 'login'),
(1326, 'ADMIN: Admin Admin | LOGOUT', '13-7-2025 9:36 AM', 'logout'),
(1327, 'ADMIN: Admin Admin | LOGIN', '13-7-2025 3:47 PM', 'login'),
(1328, 'RESIDENT: Jabidi Santos | LOGIN', '17-7-2025 8:34 AM', 'login'),
(1329, 'RESIDENT: Jabidi Santos | LOGIN', '17-7-2025 8:42 AM', 'login'),
(1330, 'ADMIN: Admin Admin | LOGIN', '18-7-2025 9:48 AM', 'login'),
(1331, 'ADMIN: Admin Admin | LOGIN', '18-7-2025 9:51 AM', 'login'),
(1332, 'ADMIN: Admin Admin | LOGOUT', '18-7-2025 3:54 AM', 'logout'),
(1333, 'OFFICIAL: Secretary Secretary | LOGIN', '18-7-2025 9:55 AM', 'login'),
(1334, 'OFFICIAL: Secretary Secretary | LOGIN', '18-7-2025 10:02 AM', 'login'),
(1335, 'OFFICIAL: Secretary Secretary | LOGOUT', '18-7-2025 4:25 AM', 'logout'),
(1336, 'ADMIN: Admin Admin | LOGIN', '18-7-2025 10:25 AM', 'login'),
(1337, 'ADMIN: Admin Admin | LOGOUT', '18-7-2025 4:31 AM', 'logout'),
(1338, 'ADMIN: Admin Admin | LOGIN', '18-7-2025 10:38 AM', 'login'),
(1339, 'ADMIN: Admin Admin | LOGOUT', '18-7-2025 4:38 AM', 'logout'),
(1340, 'ADMIN: Admin Admin | LOGIN', '18-7-2025 10:38 AM', 'login'),
(1341, 'ADMIN: Admin Admin | LOGOUT', '18-7-2025 4:39 AM', 'logout'),
(1342, 'RESIDENT: REGISTER RESIDENT - 44920364405135 |  Ali Ga ', '18-7-2025 10:41 AM', 'create'),
(1343, 'RESIDENT: Ali Ga | LOGIN', '18-7-2025 10:41 AM', 'login'),
(1344, 'RESIDENT: Ali Ga | LOGOUT', '18-7-2025 5:01 AM', 'logout'),
(1345, 'RESIDENT: Ali Ga | LOGIN', '18-7-2025 11:01 AM', 'login'),
(1346, 'RESIDENT: Ali Ga | LOGOUT', '18-7-2025 5:03 AM', 'logout'),
(1347, 'RESIDENT:   | LOGOUT', '18-7-2025 7:49 AM', 'logout'),
(1348, 'RESIDENT: REGISTER RESIDENT - 31548169432031 |  Mons Garcia ', '18-7-2025 1:52 PM', 'create'),
(1349, 'RESIDENT: Mons Garcia | LOGIN', '18-7-2025 1:53 PM', 'login'),
(1350, 'RESIDENT - :   | REQUEST CERTIFICATE - ', '18-7-2025 1:54 PM', 'create'),
(1351, 'RESIDENT - 31548169432031: Mons Garcia | REQUEST CERTIFICATE - PROOF OF RESIDENCY', '18-7-2025 1:55 PM', 'create'),
(1352, 'RESIDENT: Mons Garcia | LOGIN', '18-7-2025 2:25 PM', 'login'),
(1353, 'RESIDENT: Mons Garcia | LOGOUT', '18-7-2025 8:54 AM', 'logout'),
(1354, 'RESIDENT: REGISTER RESIDENT - 65640009717929 |  Cass Pansacola ', '18-7-2025 3:07 PM', 'create'),
(1355, 'RESIDENT: Cass Pansacola | LOGIN', '18-7-2025 3:10 PM', 'login'),
(1356, 'RESIDENT - 65640009717929: Cass Pansacola | REQUEST CERTIFICATE - PROOF OF RESIDENCY', '18-7-2025 3:13 PM', 'create'),
(1357, 'RESIDENT: Cass Pansacola | LOGOUT', '18-7-2025 9:13 AM', 'logout'),
(1358, 'OFFICIAL: Secretary Secretary | LOGIN', '18-7-2025 3:13 PM', 'login'),
(1359, 'OFFICIAL: Secretary Secretary 174668789044820710152022021619941 | RESIDENT Cass Pansacola REQUEST CERTIFICATE ACCEPTED - 65640009717929 | PURPOSE PROOF OF RESIDENCY | MESSAGE none | DATE ISSUED 2222-12-22 | DATE EXPIRED 2223-12-21', '18-7-2025 9:14 AM', 'updated'),
(1360, 'OFFICAL: Secretary Secretary - 174668789044820710152022021619941 | DELETED RESIDENT -  44920364405135 |  - Ali Ga', '18-7-2025 9:18 AM', 'update'),
(1361, 'OFFICIAL: Secretary Secretary | LOGOUT', '18-7-2025 9:20 AM', 'logout'),
(1362, 'OFFICIAL: Secretary Secretary | LOGIN', '18-7-2025 3:21 PM', 'login'),
(1363, 'OFFICAL: Secretary Secretary - 174668789044820710152022021619941 | UPDATED RESIDENT PWD TYPE - 65640009717929 |  FROM  TO bulag', '18-7-2025 9:21 AM', 'update'),
(1364, 'OFFICAL: Secretary Secretary - 174668789044820710152022021619941 | UPDATED RESIDENT PWD - 65640009717929 |  FROM NO TO YES', '18-7-2025 9:21 AM', 'update'),
(1365, 'OFFICIAL: Secretary Secretary | LOGOUT', '18-7-2025 9:22 AM', 'logout'),
(1366, 'ADMIN: Admin Admin | LOGIN', '18-7-2025 3:22 PM', 'login'),
(1367, 'ADMIN: DELETED OFFICIAL -  0625202514064336021 | CHAIRMAN - mani pogie Shival', '18-7-2025 9:23 AM', 'delete'),
(1368, 'RESIDENT: Mons Garcia | LOGIN', '1-9-2025 9:58 PM', 'login'),
(1369, 'RESIDENT: Mons Garcia | LOGOUT', '1-9-2025 4:23 PM', 'logout'),
(1370, 'ADMIN: Admin Admin | LOGIN', '1-9-2025 10:23 PM', 'login'),
(1371, 'ADMIN: Admin Admin | LOGOUT', '1-9-2025 4:24 PM', 'logout'),
(1372, 'OFFICIAL: Secretary Secretary | LOGIN', '1-9-2025 10:24 PM', 'login'),
(1373, 'OFFICIAL: Secretary Secretary | LOGOUT', '1-9-2025 4:25 PM', 'logout'),
(1374, 'ADMIN: Admin Admin | LOGIN', '1-9-2025 10:25 PM', 'login'),
(1375, 'ADMIN: Admin Admin | LOGOUT', '1-9-2025 5:15 PM', 'logout'),
(1376, 'OFFICIAL: Secretary Secretary | LOGIN', '1-9-2025 11:15 PM', 'login'),
(1377, 'OFFICIAL: Secretary Secretary | LOGOUT', '1-9-2025 5:24 PM', 'logout'),
(1378, 'ADMIN: Admin Admin | LOGIN', '1-9-2025 11:24 PM', 'login'),
(1379, 'ADMIN: Admin Admin | LOGOUT', '1-9-2025 5:25 PM', 'logout'),
(1380, 'OFFICIAL: Secretary Secretary | LOGIN', '1-9-2025 11:25 PM', 'login'),
(1381, 'OFFICIAL: Secretary Secretary | LOGOUT', '1-9-2025 5:28 PM', 'logout'),
(1382, 'RESIDENT: Mons Garcia | LOGIN', '1-9-2025 11:28 PM', 'login'),
(1383, 'RESIDENT: Mons Garcia | LOGOUT', '2-9-2025 3:10 PM', 'logout'),
(1384, 'ADMIN: Admin Admin | LOGIN', '2-9-2025 9:10 PM', 'login'),
(1385, 'ADMIN: Admin Admin | LOGOUT', '2-9-2025 5:15 PM', 'logout'),
(1386, 'OFFICIAL: Secretary Secretary | LOGIN', '2-9-2025 11:17 PM', 'login'),
(1387, 'OFFICIAL: Secretary Secretary | LOGOUT', '2-9-2025 5:20 PM', 'logout'),
(1388, 'ADMIN: Admin Admin | LOGIN', '2-9-2025 11:20 PM', 'login'),
(1389, 'ADMIN: Admin Admin | LOGOUT', '2-9-2025 5:20 PM', 'logout'),
(1390, 'OFFICIAL: Secretary Secretary | LOGIN', '2-9-2025 11:20 PM', 'login'),
(1391, 'OFFICIAL: Secretary Secretary | LOGOUT', '2-9-2025 5:59 PM', 'logout'),
(1392, 'ADMIN: Admin Admin | LOGIN', '2-9-2025 11:59 PM', 'login'),
(1393, 'ADMIN: Admin Admin | LOGOUT', '2-9-2025 6:00 PM', 'logout'),
(1394, 'OFFICIAL: Secretary Secretary | LOGIN', '3-9-2025 12:00 AM', 'login'),
(1395, 'OFFICIAL: Secretary Secretary | LOGOUT', '2-9-2025 6:08 PM', 'logout'),
(1396, 'ADMIN: Admin Admin | LOGIN', '3-9-2025 12:08 AM', 'login'),
(1397, 'ADMIN: UNDELETED OFFICIAL -  0625202514064336021 | CHAIRMAN - mani pogie Shival', '2-9-2025 6:10 PM', 'delete'),
(1398, 'ADMIN: DELETED OFFICIAL -  0625202514064336021 | CHAIRMAN - mani pogie Shival', '2-9-2025 6:10 PM', 'delete'),
(1399, 'ADMIN: Admin Admin | LOGOUT', '2-9-2025 6:11 PM', 'logout'),
(1400, 'OFFICIAL: Secretary Secretary | LOGIN', '3-9-2025 12:11 AM', 'login'),
(1401, 'OFFICIAL: Secretary Secretary | LOGOUT', '2-9-2025 6:15 PM', 'logout'),
(1402, 'ADMIN: Admin Admin | LOGIN', '3-9-2025 12:15 AM', 'login'),
(1403, 'ADMIN: Admin Admin | LOGOUT', '2-9-2025 6:45 PM', 'logout'),
(1404, 'RESIDENT: Mons Garcia | LOGIN', '3-9-2025 12:46 AM', 'login'),
(1405, 'RESIDENT: Mons Garcia | LOGOUT', '3-9-2025 3:14 PM', 'logout'),
(1406, 'RESIDENT: Mons Garcia | LOGIN', '3-9-2025 9:15 PM', 'login'),
(1407, 'RESIDENT: Mons Garcia | LOGIN', '3-9-2025 9:17 PM', 'login'),
(1408, 'RESIDENT: Mons Garcia | LOGOUT', '3-9-2025 3:44 PM', 'logout'),
(1409, 'ADMIN: Admin Admin | LOGIN', '3-9-2025 10:16 PM', 'login'),
(1410, 'ADMIN: UNDELETED OFFICIAL -  0625202514064336021 | CHAIRMAN - mani pogie Shival', '3-9-2025 4:16 PM', 'delete'),
(1411, 'ADMIN: Admin Admin | LOGOUT', '3-9-2025 4:36 PM', 'logout'),
(1412, 'OFFICIAL: Secretary Secretary | LOGIN', '3-9-2025 10:37 PM', 'login'),
(1413, 'OFFICIAL: Secretary Secretary | LOGOUT', '4-9-2025 7:49 AM', 'logout'),
(1414, 'ADMIN: Admin Admin | LOGIN', '4-9-2025 1:49 PM', 'login'),
(1415, 'ADMIN: Admin Admin | LOGIN', '4-9-2025 2:54 PM', 'login'),
(1416, 'ADMIN: Admin Admin | LOGOUT', '4-9-2025 9:11 AM', 'logout'),
(1417, 'OFFICIAL: Secretary Secretary | LOGIN', '4-9-2025 3:11 PM', 'login'),
(1418, 'OFFICIAL: Secretary Secretary | LOGOUT', '4-9-2025 11:18 AM', 'logout'),
(1419, 'OFFICIAL: Secretary Secretary | LOGIN', '6-9-2025 11:12 AM', 'login'),
(1420, 'OFFICIAL: Secretary Secretary | LOGOUT', '6-9-2025 5:13 AM', 'logout'),
(1421, 'ADMIN: Admin Admin | LOGIN', '6-9-2025 11:13 AM', 'login'),
(1422, 'ADMIN: Admin Admin | LOGOUT', '6-9-2025 5:14 AM', 'logout'),
(1423, 'RESIDENT: Mons Garcia | LOGIN', '6-9-2025 11:14 AM', 'login'),
(1424, 'RESIDENT: Mons Garcia | LOGOUT', '6-9-2025 5:15 AM', 'logout'),
(1425, 'ADMIN: Admin Admin | LOGIN', '6-9-2025 11:15 AM', 'login'),
(1426, 'ADMIN: Admin Admin | LOGOUT', '6-9-2025 5:15 AM', 'logout'),
(1427, 'ADMIN: Admin Admin | LOGIN', '11-9-2025 10:08 PM', 'login'),
(1428, 'ADMIN: Admin Admin | LOGOUT', '11-9-2025 4:46 PM', 'logout'),
(1429, 'OFFICIAL: Secretary Secretary | LOGIN', '11-9-2025 10:47 PM', 'login'),
(1430, 'OFFICIAL: Secretary Secretary | LOGOUT', '11-9-2025 5:13 PM', 'logout'),
(1431, 'RESIDENT: Mons Garcia | LOGIN', '13-9-2025 4:44 PM', 'login'),
(1432, 'RESIDENT: Mons Garcia | LOGOUT', '13-9-2025 10:45 AM', 'logout'),
(1433, 'RESIDENT: REGISTER RESIDENT - 12202311826724 |  Jaun Cruz ', '13-9-2025 4:54 PM', 'create'),
(1434, 'RESIDENT: Jaun Cruz | LOGIN', '13-9-2025 4:54 PM', 'login'),
(1435, 'RESIDENT: Jaun Cruz | LOGOUT', '13-9-2025 11:00 AM', 'logout'),
(1436, 'ADMIN: Admin Admin | LOGIN', '13-9-2025 5:00 PM', 'login'),
(1437, 'ADMIN: Admin Admin | LOGOUT', '13-9-2025 11:25 AM', 'logout'),
(1438, 'ADMIN: Admin Admin | LOGIN', '21-9-2025 10:56 PM', 'login'),
(1439, 'ADMIN: Admin Admin | LOGOUT', '21-9-2025 4:57 PM', 'logout'),
(1440, 'RESIDENT: Mons Garcia | LOGIN', '22-9-2025 5:36 PM', 'login'),
(1441, 'RESIDENT: Mons Garcia | LOGOUT', '22-9-2025 11:39 AM', 'logout'),
(1442, 'RESIDENT: Mons Garcia | LOGIN', '22-9-2025 5:39 PM', 'login'),
(1443, 'RESIDENT: Mons Garcia | LOGOUT', '22-9-2025 11:40 AM', 'logout'),
(1444, 'RESIDENT: Mons Garcia | LOGIN', '22-9-2025 5:41 PM', 'login'),
(1445, 'RESIDENT: Mons Garcia | LOGOUT', '23-9-2025 2:14 PM', 'logout'),
(1446, 'ADMIN: Admin Admin | LOGIN', '23-9-2025 8:14 PM', 'login'),
(1447, 'ADMIN: Admin Admin | LOGOUT', '23-9-2025 2:16 PM', 'logout'),
(1448, 'ADMIN: Admin Admin | LOGIN', '24-9-2025 5:41 PM', 'login'),
(1449, 'ADMIN: Admin Admin | LOGOUT', '24-9-2025 11:41 AM', 'logout'),
(1450, 'ADMIN: Admin Admin | LOGIN', '29-9-2025 3:41 PM', 'login'),
(1451, 'ADMIN: Admin Admin | LOGOUT', '29-9-2025 9:43 AM', 'logout'),
(1452, 'RESIDENT: Mons Garcia | LOGIN', '29-9-2025 3:49 PM', 'login'),
(1453, 'RESIDENT: Mons Garcia | LOGOUT', '30-9-2025 12:27 PM', 'logout'),
(1454, 'ADMIN: Admin Admin | LOGIN', '30-9-2025 6:27 PM', 'login'),
(1455, 'ADMIN: Admin Admin | LOGIN', '30-9-2025 6:27 PM', 'login'),
(1456, 'ADMIN: Admin Admin | LOGOUT', '30-9-2025 12:28 PM', 'logout'),
(1457, 'ADMIN: Admin Admin | LOGIN', '13-10-2025 11:15 AM', 'login'),
(1458, 'ADMIN: Admin Admin | LOGOUT', '13-10-2025 5:17 AM', 'logout'),
(1459, 'OFFICIAL: Secretary Secretary | LOGIN', '13-10-2025 11:17 AM', 'login'),
(1460, 'OFFICIAL: Secretary Secretary | LOGOUT', '13-10-2025 5:18 AM', 'logout'),
(1461, 'ADMIN: Admin Admin | LOGIN', '13-10-2025 11:18 AM', 'login'),
(1462, 'ADMIN: Admin Admin | LOGOUT', '13-10-2025 5:18 AM', 'logout'),
(1463, 'OFFICIAL: Secretary Secretary | LOGIN', '13-10-2025 11:18 AM', 'login'),
(1464, 'OFFICIAL: Secretary Secretary | LOGOUT', '13-10-2025 5:19 AM', 'logout'),
(1465, 'RESIDENT: Mons Garcia | LOGIN', '13-10-2025 11:29 AM', 'login'),
(1466, 'RESIDENT - 31548169432031: Mons Garcia | REQUEST CERTIFICATE - BARANGAY CERTIFICATE FOR SOLO PARENTS', '13-10-2025 12:25 PM', 'create'),
(1467, 'RESIDENT - 31548169432031: Mons Garcia | REQUEST CERTIFICATE - BARANGAY CERTIFICATE OF RESIDENCY', '13-10-2025 12:26 PM', 'create'),
(1468, 'RESIDENT: Mons Garcia | LOGOUT', '13-10-2025 7:16 AM', 'logout'),
(1469, 'ADMIN: Admin Admin | LOGIN', '13-10-2025 1:44 PM', 'login'),
(1470, 'ADMIN: Admin Admin | LOGOUT', '13-10-2025 7:49 AM', 'logout'),
(1471, 'RESIDENT: Mons Garcia | LOGIN', '13-10-2025 1:49 PM', 'login'),
(1472, 'RESIDENT: Mons Garcia | LOGOUT', '13-10-2025 7:52 AM', 'logout'),
(1473, 'ADMIN: Admin Admin | LOGIN', '13-10-2025 1:52 PM', 'login'),
(1474, 'ADMIN: Admin Admin | LOGOUT', '13-10-2025 4:04 PM', 'logout'),
(1475, 'RESIDENT: REGISTER RESIDENT - 24389959441883 |  Augusto Hernandez ', '13-10-2025 10:50 PM', 'create'),
(1476, 'RESIDENT: Augusto Hernandez | LOGIN', '13-10-2025 10:51 PM', 'login'),
(1477, 'RESIDENT - 24389959441883: Augusto Hernandez | REQUEST CERTIFICATE - BARANGAY CERTIFICATE OF RESIDENCY', '13-10-2025 11:01 PM', 'create'),
(1478, 'RESIDENT: Augusto Hernandez | LOGOUT', '13-10-2025 5:03 PM', 'logout'),
(1479, 'ADMIN: Admin Admin | LOGIN', '13-10-2025 11:03 PM', 'login'),
(1480, 'ADMIN: DELETED OFFICIAL -  0625202514064336021 | CHAIRMAN - mani pogie Shival', '13-10-2025 5:06 PM', 'delete'),
(1481, 'ADMIN: UNDELETED OFFICIAL -  0625202514064336021 | CHAIRMAN - mani pogie Shival', '13-10-2025 5:06 PM', 'delete'),
(1482, 'ADMIN: DELETED OFFICIAL -  0625202514064336021 | CHAIRMAN - Mannie Shevy', '13-10-2025 5:14 PM', 'delete'),
(1483, 'ADMIN: UNDELETED OFFICIAL -  0625202514064336021 | CHAIRMAN - Mannie Shevy', '13-10-2025 5:15 PM', 'delete'),
(1484, 'ADMIN: DELETED OFFICIAL -  0625202514064336021 | CHAIRMAN - Mannie Shevy', '13-10-2025 5:15 PM', 'delete'),
(1485, 'ADMIN: UNDELETED OFFICIAL -  0625202514064336021 | CHAIRMAN - Mannie Shevy', '13-10-2025 5:15 PM', 'delete'),
(1486, 'ADMIN: Admin Admin | LOGOUT', '13-10-2025 5:19 PM', 'logout'),
(1487, 'OFFICIAL: Secretary Secretary | LOGIN', '13-10-2025 11:19 PM', 'login'),
(1488, 'OFFICIAL: Secretary Secretary | LOGOUT', '14-10-2025 4:26 AM', 'logout'),
(1489, 'RESIDENT: REGISTER RESIDENT - 56562352027011 |  Aria Antonina ', '14-10-2025 11:00 AM', 'create'),
(1490, 'RESIDENT: Aria Antonina | LOGIN', '14-10-2025 11:00 AM', 'login'),
(1491, 'RESIDENT: Aria Antonina | LOGOUT', '14-10-2025 5:00 AM', 'logout'),
(1492, 'RESIDENT: REGISTER RESIDENT - 71850183277563 |  Chris Sandoval ', '14-10-2025 11:10 AM', 'create'),
(1493, 'RESIDENT: Chris Sandoval | LOGIN', '14-10-2025 11:11 AM', 'login'),
(1494, 'RESIDENT - 71850183277563: Chris Sandoval | REQUEST CERTIFICATE - BARANGAY CERTIFICATE OF RESIDENCY', '14-10-2025 11:19 AM', 'create'),
(1495, 'RESIDENT: Chris Sandoval | LOGOUT', '14-10-2025 8:21 AM', 'logout'),
(1496, 'ADMIN: Admin Admin | LOGIN', '14-10-2025 2:21 PM', 'login'),
(1497, 'ADMIN: Admin Admin | LOGOUT', '14-10-2025 8:27 AM', 'logout'),
(1498, 'RESIDENT: Chris Sandoval | LOGIN', '14-10-2025 2:27 PM', 'login'),
(1499, 'RESIDENT: Chris Sandoval | LOGOUT', '14-10-2025 12:02 PM', 'logout'),
(1500, 'RESIDENT: REGISTER RESIDENT - 87582342756244 |  David Santos ', '14-10-2025 6:09 PM', 'create'),
(1501, 'RESIDENT: David Santos | LOGIN', '14-10-2025 6:10 PM', 'login'),
(1502, 'RESIDENT: David Santos | LOGOUT', '14-10-2025 12:11 PM', 'logout'),
(1503, 'ADMIN: Admin Admin | LOGIN', '14-10-2025 6:11 PM', 'login'),
(1504, 'ADMIN: RESIDENT REQUEST CERTIFICATE ACCEPTED - 71850183277563 | PURPOSE BARANGAY CERTIFICATE OF RESIDENCY | MESSAGE Good to go | DATE ISSUED 2025-10-14 | DATE EXPIRED 2025-11-14', '14-10-2025 12:12 PM', 'updated'),
(1505, 'ADMIN: Admin Admin | LOGOUT', '14-10-2025 12:14 PM', 'logout'),
(1506, 'RESIDENT: REGISTER RESIDENT - 1724586413346 |  Lawrence John Hinanay ', '20-10-2025 2:09 PM', 'create'),
(1507, 'ADMIN: Admin Admin | LOGIN', '20-10-2025 2:38 PM', 'login'),
(1508, 'ADMIN: Admin Admin | LOGOUT', '20-10-2025 8:39 AM', 'logout'),
(1509, 'RESIDENT: REGISTER RESIDENT - 49240644105535 |  Lilo Luna ', '20-10-2025 2:41 PM', 'create');

-- --------------------------------------------------------

--
-- Table structure for table `ambulance_units`
--

CREATE TABLE `ambulance_units` (
  `id` int(11) NOT NULL,
  `ambulance_number` varchar(50) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `driver_name` varchar(100) DEFAULT NULL,
  `status` enum('Available','In Use','Under Maintenance') DEFAULT 'Available',
  `specifications` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ambulance_units`
--

INSERT INTO `ambulance_units` (`id`, `ambulance_number`, `location`, `contact_number`, `driver_name`, `status`, `specifications`, `created_at`, `updated_at`) VALUES
(1, 'AMB-001', 'Barangay Health Center', '0922-333-3333', 'Mark Reyes', 'Available', 'Basic Life Support', '2025-10-13 03:13:35', '2025-10-13 03:13:35'),
(2, 'AMB-002', 'City Hospital', '0918-444-4444', 'Ana Lim', 'Available', 'Advanced Life Support', '2025-10-13 03:13:35', '2025-10-13 03:13:35'),
(3, 'AMB-003', 'Substation', '0917-777-7777', 'Carlo Dela Vega', 'Under Maintenance', 'Basic Life Support', '2025-10-13 03:13:35', '2025-10-13 03:13:35');

-- --------------------------------------------------------

--
-- Table structure for table `backup`
--

CREATE TABLE `backup` (
  `id` int(11) NOT NULL,
  `path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `backup`
--

INSERT INTO `backup` (`id`, `path`) VALUES
(170, 'BackupFile-04012025_071908.sql');

-- --------------------------------------------------------

--
-- Table structure for table `barangay_information`
--

CREATE TABLE `barangay_information` (
  `id` varchar(255) NOT NULL,
  `barangay` varchar(255) NOT NULL DEFAULT 'none',
  `zone` varchar(255) NOT NULL DEFAULT 'none',
  `district` varchar(255) NOT NULL DEFAULT 'none',
  `address` varchar(69) NOT NULL DEFAULT 'none',
  `postal_address` varchar(255) NOT NULL DEFAULT 'none',
  `image` varchar(255) NOT NULL DEFAULT 'none',
  `image_path` varchar(255) NOT NULL DEFAULT 'none'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barangay_information`
--

INSERT INTO `barangay_information` (`id`, `barangay`, `zone`, `district`, `address`, `postal_address`, `image`, `image_path`) VALUES
('32432432432432432', 'Barangay kalusugan', 'Area 213', 'District IV', 'Quezon CIty ', '1102', '165897181867eb5acf2e8c4.jpg', '../assets/dist/img/165897181867eb5acf2e8c4.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `blotter_complainant`
--

CREATE TABLE `blotter_complainant` (
  `id` varchar(255) NOT NULL,
  `blotter_main` varchar(255) NOT NULL,
  `complainant_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blotter_complainant`
--

INSERT INTO `blotter_complainant` (`id`, `blotter_main`, `complainant_id`) VALUES
('', '0', '71850183277563');

-- --------------------------------------------------------

--
-- Table structure for table `blotter_info`
--

CREATE TABLE `blotter_info` (
  `id` varchar(255) NOT NULL,
  `blotter_main_id` varchar(255) NOT NULL,
  `blotter_person_id` varchar(255) NOT NULL,
  `blotter_complainant_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `blotter_record`
--

CREATE TABLE `blotter_record` (
  `blotter_id` varchar(255) NOT NULL,
  `complainant_not_residence` varchar(255) NOT NULL DEFAULT 'none',
  `statement` varchar(255) NOT NULL DEFAULT 'none',
  `respodent` varchar(255) NOT NULL DEFAULT 'none',
  `involved_not_resident` varchar(255) NOT NULL DEFAULT 'none',
  `statement_person` varchar(255) NOT NULL DEFAULT 'none',
  `date_incident` varchar(255) NOT NULL DEFAULT 'none',
  `date_reported` varchar(255) NOT NULL DEFAULT 'none',
  `type_of_incident` varchar(255) NOT NULL DEFAULT 'none',
  `location_incident` varchar(255) NOT NULL DEFAULT 'none',
  `status` varchar(69) NOT NULL DEFAULT 'none',
  `remarks` varchar(69) NOT NULL DEFAULT 'none',
  `date_added` varchar(255) NOT NULL DEFAULT 'none'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blotter_record`
--

INSERT INTO `blotter_record` (`blotter_id`, `complainant_not_residence`, `statement`, `respodent`, `involved_not_resident`, `statement_person`, `date_incident`, `date_reported`, `type_of_incident`, `location_incident`, `status`, `remarks`, `date_added`) VALUES
('', '', 'Loitering', 'Juan Cruz', '', 'Kung saan saan nagkakalat', '2025-10-14 11:50:24', '2025-10-14 11:50:24', 'Complaint', '12th Street Brgy Kalusugan QC', 'NEW', 'OPEN', 'none');

-- --------------------------------------------------------

--
-- Table structure for table `blotter_status`
--

CREATE TABLE `blotter_status` (
  `blotter_id` varchar(255) NOT NULL,
  `blotter_main` varchar(255) NOT NULL,
  `person_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blotter_status`
--

INSERT INTO `blotter_status` (`blotter_id`, `blotter_main`, `person_id`) VALUES
('', '0', '71850183277563');

-- --------------------------------------------------------

--
-- Table structure for table `carousel`
--

CREATE TABLE `carousel` (
  `id` int(11) NOT NULL,
  `banner_title` varchar(255) NOT NULL,
  `banner_image` varchar(255) NOT NULL,
  `banner_image_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `certificate`
--

CREATE TABLE `certificate` (
  `a_i` int(11) NOT NULL,
  `certificate_id` varchar(255) NOT NULL,
  `residence_id` varchar(255) NOT NULL,
  `certificate` varchar(255) NOT NULL,
  `ctc` varchar(255) NOT NULL,
  `issued_at` varchar(255) NOT NULL,
  `or_no` varchar(255) NOT NULL,
  `business_name` varchar(255) NOT NULL,
  `purpose` varchar(255) NOT NULL,
  `control_no` varchar(255) NOT NULL,
  `created_at` varchar(255) NOT NULL,
  `expired_at` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `certificate_request`
--

CREATE TABLE `certificate_request` (
  `a_i` int(11) NOT NULL,
  `id` varchar(255) NOT NULL,
  `residence_id` varchar(255) NOT NULL,
  `certificate_type` varchar(255) NOT NULL DEFAULT 'none',
  `purpose` varchar(255) NOT NULL DEFAULT 'none',
  `message` varchar(255) NOT NULL DEFAULT 'none',
  `date_issued` varchar(255) NOT NULL DEFAULT 'none',
  `date_request` varchar(255) NOT NULL DEFAULT 'none',
  `date_expired` varchar(255) NOT NULL DEFAULT 'none',
  `status` varchar(255) NOT NULL DEFAULT 'none'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `certificate_request`
--

INSERT INTO `certificate_request` (`a_i`, `id`, `residence_id`, `certificate_type`, `purpose`, `message`, `date_issued`, `date_request`, `date_expired`, `status`) VALUES
(64, '2107921662062520251402272672005845910685b90f341635', '43880855422082', 'none', 'CERTIFICATE OF INDIGENCY', 'none', '2025-12-12', '06/25/2025', '2026-12-12', 'ACCEPTED'),
(66, '125716032207182025135508189625762066879e1bc2e296', '31548169432031', 'none', 'Proof of residency', 'none', '', '07/18/2025', '', 'PENDING'),
(67, '6241257400718202515130357610408522366879f3ff8ccf8', '65640009717929', 'none', 'PROOF OF RESIDENCY', 'none', '2222-12-22', '07/18/2025', '2223-12-21', 'ACCEPTED'),
(68, '11638577671013202512253972281535555368ec7f43b05b0', '31548169432031', 'none', 'Barangay Certificate for Solo Parents', 'none', '', '10/13/2025', '', 'PENDING'),
(69, '118087995310132025122606130184707416568ec7f5e1fe79', '31548169432031', 'none', 'Barangay Certificate of Residency', 'none', '', '10/13/2025', '', 'PENDING'),
(70, '199100794110132025230125858100382375168ed1445d1ad1', '24389959441883', 'none', 'BARANGAY CERTIFICATE OF RESIDENCY', 'none', '', '10/13/2025', '', 'PENDING'),
(71, '120158615010142025111920190193419410468edc1382e97c', '71850183277563', 'none', 'BARANGAY CERTIFICATE OF RESIDENCY', 'Good to go', '2025-10-14', '10/14/2025', '2025-11-14', 'ACCEPTED');

-- --------------------------------------------------------

--
-- Table structure for table `evacuation_status`
--

CREATE TABLE `evacuation_status` (
  `residence_id` varchar(50) NOT NULL,
  `status` enum('Arrived','Missing') NOT NULL DEFAULT 'Missing'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `evacuation_status`
--

INSERT INTO `evacuation_status` (`residence_id`, `status`) VALUES
('12202311826724', 'Missing'),
('24389959441883', 'Arrived'),
('43880855422082', 'Arrived'),
('44920364405135', 'Arrived'),
('87582342756244', 'Arrived');

-- --------------------------------------------------------

--
-- Table structure for table `fire_hydrants`
--

CREATE TABLE `fire_hydrants` (
  `id` int(11) NOT NULL,
  `hydrant_number` varchar(50) NOT NULL,
  `location` varchar(255) NOT NULL,
  `last_inspection_date` date DEFAULT NULL,
  `status` enum('Operational','Under Maintenance','Out of Service') DEFAULT 'Operational',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fire_hydrants`
--

INSERT INTO `fire_hydrants` (`id`, `hydrant_number`, `location`, `last_inspection_date`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'FH-001', 'Purok 1 Street', '2025-01-15', 'Operational', 'Near barangay hall', '2025-10-13 03:11:16', '2025-10-13 03:11:16'),
(2, 'FH-002', 'Barangay Hall corner', '2025-01-10', 'Operational', 'Main entrance', '2025-10-13 03:11:16', '2025-10-13 03:11:16'),
(3, 'FH-003', 'Market Area', '2025-01-20', 'Operational', 'Public market', '2025-10-13 03:11:16', '2025-10-13 03:11:16'),
(4, 'FH-004', 'Riverside Road', '2025-01-05', 'Under Maintenance', 'Needs repair', '2025-10-13 03:11:16', '2025-10-13 03:11:16');

-- --------------------------------------------------------

--
-- Table structure for table `first_aid_officers`
--

CREATE TABLE `first_aid_officers` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `position` varchar(100) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `years_of_service` int(11) DEFAULT NULL,
  `specialization` varchar(255) DEFAULT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `first_aid_officers`
--

INSERT INTO `first_aid_officers` (`id`, `first_name`, `last_name`, `position`, `contact_number`, `years_of_service`, `specialization`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Juan', 'Dela Cruz', 'Senior Medic', '0917-111-1111', 1, 'Emergency Medicine', 'Active', '2025-10-13 03:12:29', '2025-10-13 03:12:29'),
(2, 'Maria', 'Santos', 'Volunteer Nurse', '0917-222-2222', 2, 'First Aid', 'Active', '2025-10-13 03:12:29', '2025-10-13 03:12:29'),
(3, 'Pedro', 'Cruz', 'EMT', '0917-333-3333', 3, 'Emergency Response', 'Active', '2025-10-13 03:12:29', '2025-10-13 03:12:29');

-- --------------------------------------------------------

--
-- Table structure for table `house_holds`
--

CREATE TABLE `house_holds` (
  `a_i` int(11) NOT NULL,
  `house_hold_id` varchar(255) NOT NULL DEFAULT 'none',
  `hold_unique` varchar(255) NOT NULL,
  `purok_id` varchar(255) NOT NULL,
  `residence_id` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `birth_date` varchar(255) NOT NULL,
  `gender` varchar(255) NOT NULL,
  `educ_attainment` varchar(255) NOT NULL,
  `occupation` varchar(255) NOT NULL,
  `nawasa` varchar(255) NOT NULL,
  `water_pump` varchar(255) NOT NULL,
  `water_sealed` varchar(255) NOT NULL,
  `flush` varchar(255) NOT NULL,
  `religion` varchar(255) NOT NULL,
  `ethnicity` varchar(255) NOT NULL,
  `sangkap_seal` varchar(255) NOT NULL,
  `is_approved` varchar(255) NOT NULL,
  `is_resident` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `house_holds`
--

INSERT INTO `house_holds` (`a_i`, `house_hold_id`, `hold_unique`, `purok_id`, `residence_id`, `first_name`, `middle_name`, `last_name`, `birth_date`, `gender`, `educ_attainment`, `occupation`, `nawasa`, `water_pump`, `water_sealed`, `flush`, `religion`, `ethnicity`, `sangkap_seal`, `is_approved`, `is_resident`) VALUES
(119, '765720104206', '90635228440419', '916259339179300507242022155033612', '16455182440138', ' First name', 'Middle name', 'Last name', '2022-10-08', 'Female', 'educ', 'Occupation', 'YES', 'YES', 'YES', 'YES', 'Religion', 'Ethnicity', 'YES', 'APPROVED', 'NO'),
(120, '377220153950', '90635228440419', '916259339179300507242022155033612', '16455182440138', 'qe', 'qwe', 'qweqwe', '2022-10-12', 'Male', 'qweqw', 'wqe', 'YES', 'YES', 'YES', 'YES', 'qwe', 'eqwe', 'YES', 'APPROVED', 'NO'),
(121, '377220153950', '90635228440419', '916259339179300507242022155033612', '16455182440138', 'First Name', 'Middle Name', 'Last Name', '2022-10-27', 'Male', 'qwewqe', 'Occupation', 'YES', 'YES', 'YES', 'NO', 'Religion', 'qwe', 'YES', 'APPROVED', 'YES'),
(122, '530011579769', '70838125253292', '916259339179300507242022155033612', '54278971251733', ' First name', 'Middle name', 'Last name', '2022-10-08', 'Male', 'Educational Attainment', 'Occupation', 'YES', 'YES', 'YES', 'YES', 'Religion', 'Ethnicity', 'YES', 'PENDING', 'NO'),
(123, '85351248693', '70838125253292', '916259339179300507242022155033612', '54278971251733', 'qwe', 'wqe', 'wqewqe', '2022-10-15', 'Female', 'wqe', 'wqe', 'YES', 'YES', 'YES', 'YES', 'qwe', 'qwe', 'YES', 'PENDING', 'NO'),
(124, '85351248693', '70838125253292', '916259339179300507242022155033612', '54278971251733', 'Eugine', 'Palce', 'ROsillon', '1997-09-06', 'Male', 'Educational Attainment', 'Wala', 'YES', 'YES', 'YES', 'YES', 'Catholic', 'Ethnicity', 'YES', 'PENDING', 'YES');

-- --------------------------------------------------------

--
-- Table structure for table `mobile_hq_units`
--

CREATE TABLE `mobile_hq_units` (
  `id` int(11) NOT NULL,
  `unit_number` varchar(50) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `officer_in_charge` varchar(100) DEFAULT NULL,
  `status` enum('Available','Deployed','Under Maintenance') DEFAULT 'Available',
  `equipment_included` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `official_end_information`
--

CREATE TABLE `official_end_information` (
  `official_id` varchar(255) NOT NULL,
  `first_name` varchar(69) NOT NULL DEFAULT 'none',
  `middle_name` varchar(69) NOT NULL DEFAULT 'none',
  `last_name` varchar(69) NOT NULL DEFAULT 'none',
  `suffix` varchar(69) NOT NULL DEFAULT 'none',
  `birth_date` varchar(69) NOT NULL DEFAULT 'none',
  `birth_place` varchar(69) NOT NULL DEFAULT 'none',
  `gender` varchar(69) NOT NULL DEFAULT 'none',
  `age` varchar(69) NOT NULL DEFAULT 'none',
  `civil_status` varchar(69) NOT NULL DEFAULT 'none',
  `religion` varchar(69) NOT NULL DEFAULT 'none',
  `nationality` varchar(69) NOT NULL DEFAULT 'none',
  `municipality` varchar(69) NOT NULL DEFAULT 'none',
  `zip` varchar(69) NOT NULL DEFAULT 'none',
  `barangay` varchar(69) NOT NULL DEFAULT 'none',
  `house_number` varchar(69) NOT NULL DEFAULT 'none',
  `street` varchar(69) NOT NULL DEFAULT 'none',
  `address` varchar(69) NOT NULL DEFAULT 'none',
  `email_address` varchar(69) NOT NULL DEFAULT 'none',
  `contact_number` varchar(69) NOT NULL DEFAULT 'none',
  `fathers_name` varchar(69) NOT NULL DEFAULT 'none',
  `mothers_name` varchar(69) NOT NULL DEFAULT 'none',
  `guardian` varchar(69) NOT NULL DEFAULT 'none',
  `guardian_contact` varchar(69) NOT NULL DEFAULT 'none',
  `image` varchar(255) NOT NULL DEFAULT 'none',
  `image_path` varchar(255) NOT NULL DEFAULT 'none'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `official_end_status`
--

CREATE TABLE `official_end_status` (
  `official_id` varchar(255) NOT NULL,
  `position` varchar(69) NOT NULL DEFAULT 'none',
  `purok_id` varchar(255) NOT NULL,
  `senior` varchar(69) NOT NULL DEFAULT 'none',
  `term_from` varchar(69) NOT NULL DEFAULT 'none',
  `term_to` varchar(69) NOT NULL DEFAULT 'none',
  `pwd` varchar(69) NOT NULL DEFAULT 'none',
  `pwd_info` varchar(255) NOT NULL DEFAULT 'none',
  `single_parent` varchar(69) NOT NULL DEFAULT 'none',
  `status` varchar(69) NOT NULL DEFAULT 'none',
  `voters` varchar(69) NOT NULL DEFAULT 'none',
  `date_deleted` varchar(69) NOT NULL DEFAULT 'none'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `official_information`
--

CREATE TABLE `official_information` (
  `a_i` int(11) NOT NULL,
  `official_id` varchar(255) NOT NULL,
  `first_name` varchar(69) NOT NULL DEFAULT 'none',
  `middle_name` varchar(69) NOT NULL DEFAULT 'none',
  `last_name` varchar(69) NOT NULL DEFAULT 'none',
  `suffix` varchar(69) NOT NULL DEFAULT 'none',
  `birth_date` varchar(69) NOT NULL DEFAULT 'none',
  `birth_place` varchar(69) NOT NULL DEFAULT 'none',
  `gender` varchar(69) NOT NULL DEFAULT 'none',
  `age` varchar(69) NOT NULL DEFAULT 'none',
  `civil_status` varchar(69) NOT NULL DEFAULT 'none',
  `religion` varchar(69) NOT NULL DEFAULT 'none',
  `nationality` varchar(69) NOT NULL DEFAULT 'none',
  `municipality` varchar(69) NOT NULL DEFAULT 'none',
  `zip` varchar(69) NOT NULL DEFAULT 'none',
  `barangay` varchar(69) NOT NULL DEFAULT 'none',
  `house_number` varchar(69) NOT NULL DEFAULT 'none',
  `street` varchar(69) NOT NULL DEFAULT 'none',
  `address` varchar(69) NOT NULL DEFAULT 'none',
  `email_address` varchar(69) NOT NULL DEFAULT 'none',
  `contact_number` varchar(69) NOT NULL DEFAULT 'none',
  `fathers_name` varchar(69) NOT NULL DEFAULT 'none',
  `mothers_name` varchar(69) NOT NULL DEFAULT 'none',
  `guardian` varchar(69) NOT NULL DEFAULT 'none',
  `guardian_contact` varchar(69) NOT NULL DEFAULT 'none',
  `image` varchar(255) NOT NULL DEFAULT 'none',
  `image_path` varchar(255) NOT NULL DEFAULT 'none'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `official_information`
--

INSERT INTO `official_information` (`a_i`, `official_id`, `first_name`, `middle_name`, `last_name`, `suffix`, `birth_date`, `birth_place`, `gender`, `age`, `civil_status`, `religion`, `nationality`, `municipality`, `zip`, `barangay`, `house_number`, `street`, `address`, `email_address`, `contact_number`, `fathers_name`, `mothers_name`, `guardian`, `guardian_contact`, `image`, `image_path`) VALUES
(70, '0625202514064336021', 'Mannie', 'Barrio', 'Shevy', '', '2003-12-12', 'Manila', 'Male', '21', 'Single', 'Catholic', 'Filipino', '', '1102', 'Kalusugan', 'awaw', 'qqRQWR', 'sagvQARv', 'mani@gmail.com', '25352353252', 'deads', 'deads', 'papa j', '315135123124125', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `official_status`
--

CREATE TABLE `official_status` (
  `a_i` int(11) NOT NULL,
  `official_id` varchar(255) NOT NULL,
  `position` varchar(69) NOT NULL DEFAULT 'none',
  `purok_id` varchar(255) NOT NULL,
  `senior` varchar(69) NOT NULL DEFAULT 'none',
  `term_from` varchar(69) NOT NULL DEFAULT 'none',
  `term_to` varchar(69) NOT NULL DEFAULT 'none',
  `pwd` varchar(69) NOT NULL DEFAULT 'none',
  `pwd_info` varchar(255) NOT NULL DEFAULT 'none',
  `status` varchar(69) NOT NULL DEFAULT 'none',
  `voters` varchar(69) NOT NULL DEFAULT 'none',
  `single_parent` varchar(255) NOT NULL DEFAULT 'none',
  `date_added` varchar(69) NOT NULL DEFAULT 'none',
  `date_undeleted` varchar(255) NOT NULL DEFAULT 'none'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `official_status`
--

INSERT INTO `official_status` (`a_i`, `official_id`, `position`, `purok_id`, `senior`, `term_from`, `term_to`, `pwd`, `pwd_info`, `status`, `voters`, `single_parent`, `date_added`, `date_undeleted`) VALUES
(64, '0625202514064336021', '619131249471207208162022141229307', '', 'NO', '2025-12-12', '2026-12-12', 'YES', 'Impaired Hands', 'INACTIVE', 'YES', 'YES', 'none', '10/13/2025 05:15 PM');

-- --------------------------------------------------------

--
-- Table structure for table `position`
--

CREATE TABLE `position` (
  `a_i` int(11) NOT NULL,
  `position_id` varchar(255) NOT NULL,
  `position` varchar(69) NOT NULL DEFAULT 'none',
  `position_limit` varchar(69) NOT NULL DEFAULT 'none',
  `position_description` varchar(255) NOT NULL DEFAULT 'none',
  `color` varchar(255) NOT NULL DEFAULT 'none'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `position`
--

INSERT INTO `position` (`a_i`, `position_id`, `position`, `position_limit`, `position_description`, `color`) VALUES
(20, '268778674891281501142022025704271', 'kagawad', '7', '', '#50d425'),
(21, '811981911875128801142022163118246', 'sk kagawad', '7', '', '#3bc173'),
(22, '619131249471207208162022141229307', 'chairman', '1', '', '#4fb42e');

-- --------------------------------------------------------

--
-- Table structure for table `precint`
--

CREATE TABLE `precint` (
  `a_i` int(11) NOT NULL,
  `precint_id` varchar(255) NOT NULL,
  `precint` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `precint`
--

INSERT INTO `precint` (`a_i`, `precint_id`, `precint`) VALUES
(1, '112430277815139107242022164651634', '12313200'),
(5, '834679331034411909122022012433363', 'Test 123');

-- --------------------------------------------------------

--
-- Table structure for table `purok`
--

CREATE TABLE `purok` (
  `a_i` int(11) NOT NULL,
  `purok_id` varchar(255) NOT NULL,
  `purok` varchar(255) NOT NULL,
  `leader` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purok`
--

INSERT INTO `purok` (`a_i`, `purok_id`, `purok`, `leader`) VALUES
(2, '916259339179300507242022155033612', 'puirok', 'qweqwe'),
(5, '74710938236700907272022172121040', 'ewqe', 'wqewqeq');

-- --------------------------------------------------------

--
-- Table structure for table `rescue_boats`
--

CREATE TABLE `rescue_boats` (
  `id` int(11) NOT NULL,
  `boat_number` varchar(50) NOT NULL,
  `condition` enum('Good','Fair','Under Maintenance','Poor') DEFAULT 'Good',
  `status` enum('Available','In Use','Under Maintenance') DEFAULT 'Available',
  `storage_location` varchar(255) DEFAULT NULL,
  `capacity` int(11) DEFAULT NULL,
  `specifications` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rescue_boats`
--

INSERT INTO `rescue_boats` (`id`, `boat_number`, `condition`, `status`, `storage_location`, `capacity`, `specifications`, `created_at`, `updated_at`) VALUES
(1, 'RB-001', 'Good', 'Available', 'Barangay Office', 5, 'Fiberglass, 25HP engine', '2025-10-13 03:12:20', '2025-10-13 03:12:20'),
(2, 'RB-002', 'Under Maintenance', 'Under Maintenance', 'Purok 3 Warehouse', 6, 'Aluminum, 30HP engine', '2025-10-13 03:12:20', '2025-10-13 03:12:20');

-- --------------------------------------------------------

--
-- Table structure for table `rescue_equipment`
--

CREATE TABLE `rescue_equipment` (
  `id` int(11) NOT NULL,
  `equipment_type` varchar(100) NOT NULL,
  `quantity` int(11) DEFAULT 0,
  `condition` enum('Good','Fair','Poor','Needs Replacement') DEFAULT 'Good',
  `location` varchar(255) DEFAULT NULL,
  `last_maintenance_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rescue_equipment`
--

INSERT INTO `rescue_equipment` (`id`, `equipment_type`, `quantity`, `condition`, `location`, `last_maintenance_date`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'Life Vests', 4, 'Good', 'Storage Room A', '2024-12-01', NULL, '2025-10-13 03:13:27', '2025-10-13 03:13:27'),
(2, 'Ropes', 9, 'Good', 'Storage Room B', '2024-11-15', NULL, '2025-10-13 03:13:27', '2025-10-13 03:13:27'),
(3, 'Flashlights', 13, 'Good', 'Emergency Cabinet', '2025-01-10', NULL, '2025-10-13 03:13:27', '2025-10-13 03:13:27'),
(4, 'Megaphones', 5, 'Fair', 'Barangay Hall', '2024-10-20', NULL, '2025-10-13 03:13:27', '2025-10-13 03:13:27'),
(5, 'Hard Hats', 10, 'Good', 'Storage Room A', '2025-01-05', NULL, '2025-10-13 03:13:27', '2025-10-13 03:13:27');

-- --------------------------------------------------------

--
-- Table structure for table `residence_information`
--

CREATE TABLE `residence_information` (
  `a_i` int(11) NOT NULL,
  `residence_id` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL DEFAULT 'none',
  `middle_name` varchar(255) NOT NULL DEFAULT 'none',
  `last_name` varchar(255) NOT NULL DEFAULT 'none',
  `age` varchar(11) NOT NULL,
  `suffix` varchar(255) NOT NULL DEFAULT 'none',
  `alias` varchar(255) NOT NULL,
  `gender` varchar(255) NOT NULL DEFAULT 'none',
  `civil_status` varchar(36) NOT NULL DEFAULT 'none',
  `religion` varchar(36) NOT NULL DEFAULT 'none',
  `nationality` varchar(255) NOT NULL DEFAULT 'none',
  `contact_number` varchar(69) NOT NULL DEFAULT 'none',
  `email_address` varchar(255) NOT NULL DEFAULT 'none',
  `address` varchar(255) NOT NULL DEFAULT 'none',
  `birth_date` varchar(255) NOT NULL DEFAULT 'none',
  `birth_place` varchar(255) NOT NULL DEFAULT 'none',
  `municipality` varchar(69) NOT NULL DEFAULT 'none',
  `zip` varchar(69) NOT NULL DEFAULT 'none',
  `barangay` varchar(69) NOT NULL DEFAULT 'none',
  `house_number` varchar(69) NOT NULL DEFAULT 'none',
  `street` varchar(69) NOT NULL DEFAULT 'none',
  `fathers_name` varchar(255) NOT NULL DEFAULT 'none',
  `mothers_name` varchar(255) NOT NULL DEFAULT 'none',
  `guardian` varchar(69) NOT NULL DEFAULT 'none',
  `guardian_contact` varchar(69) NOT NULL DEFAULT 'none',
  `occupation` varchar(255) NOT NULL,
  `employer_name` varchar(255) NOT NULL,
  `family_relation` varchar(255) NOT NULL,
  `national_number` varchar(255) NOT NULL,
  `sss_number` varchar(255) NOT NULL,
  `tin_number` varchar(255) NOT NULL,
  `gsis_number` varchar(255) NOT NULL,
  `pagibig_number` varchar(255) NOT NULL,
  `philhealth_number` varchar(255) NOT NULL,
  `bloodtype` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL DEFAULT 'none',
  `image_path` varchar(255) NOT NULL DEFAULT 'none'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `residence_information`
--

INSERT INTO `residence_information` (`a_i`, `residence_id`, `first_name`, `middle_name`, `last_name`, `age`, `suffix`, `alias`, `gender`, `civil_status`, `religion`, `nationality`, `contact_number`, `email_address`, `address`, `birth_date`, `birth_place`, `municipality`, `zip`, `barangay`, `house_number`, `street`, `fathers_name`, `mothers_name`, `guardian`, `guardian_contact`, `occupation`, `employer_name`, `family_relation`, `national_number`, `sss_number`, `tin_number`, `gsis_number`, `pagibig_number`, `philhealth_number`, `bloodtype`, `image`, `image_path`) VALUES
(182, '43880855422082', 'Jabidi', 'Khalid', 'Santos', '21', '', '', 'Male', 'Single', 'Catholic', 'Filipino', '01931243252', 'serg@gmail.com', '92', '2003-09-13', '  Manila', 'sanuusad', '1102', 'dinapala', '12', 'dinamalayan', 'Dagul Naegar', 'Pokie Naegar', 'Pikachu', '3154525234', '', '', '', '', '', '', '', '', '', '', '', ''),
(183, '44920364405135', 'Ali', 'Ai', 'Ga', '20', '', '', 'Male', 'Single', 'Roman Catholic', 'Filipino', '09183748713', 'aliaiga@gmail.com', '10 Ginhawa Street, Barangay Kalusugan, QC', '2004-10-29', 'Quezon City', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(184, '31548169432031', 'Mons', '', 'Garcia', '25', '', '', '', '', 'Catholic', 'Filipino', '09376487316', 'monsgarcia@gmail.com', 'pasay qc', '2000-09-21', 'Pasay', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '170349703968ec932ad4fc9.png', '../assets/dist/img/170349703968ec932ad4fc9.png'),
(185, '65640009717929', 'Cass', '', 'Pansacola', '41', '', '', 'Female', 'Single', 'Catholic', 'Filipino', '09982746776', 'cass@gmail.com', 'Kalusugan', '1984-06-01', ' Quezon Province', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(186, '12202311826724', 'Jaun', '', 'Cruz', '14', '', '', 'Male', 'Single', '', '', '09938493849', '', 'Brgy Kalusugan', '2010-10-10', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(187, '24389959441883', 'Augusto', '', 'Hernandez', '10', '', '', 'Male', 'Single', 'Roman Catholic', 'Filipino', '09946587246', 'augustohernandez@gmail.com', '12, 16th Street, Brgy Kalusugan, QC', '2015-08-18', 'SLMC, QC', 'Quezon City', '1112', 'Kalusugan', '12', '16th', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(188, '56562352027011', 'Aria', '', 'Antonina', '24', '', '', 'Female', 'Single', '', 'Filipino', '09972465782', '', '37 Sta. Ignacia Street, Brgy Kalusugan, QC', '2001-05-23', 'EAMC, QC', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(189, '71850183277563', 'Chris', '', 'Sandoval', '33', '', '', 'Male', 'Single', '', '', '09994757774', '', '12 19th Street, Brgy kalusugan, QC', '1991-11-06', 'De Los Santos Medical Center, QC', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(190, '87582342756244', 'David', '', 'Santos', '23', '', '', 'Male', 'Single', 'Roman Catholic', 'Filipino', '09984766478', 'davidsantos@gmail.com', '5 16th Street, Barangay Kalusugan, Quezon City', '2002-04-04', 'SLMC, QC', 'Quezon City', '1102', 'Kalusugan', '5', '16th', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
(192, '49240644105535', 'Lilo', '', 'Luna', '', '', '', 'Female', 'Single', '', '', '09376582765', 'ljbalong29@gmail.com', 'Pasong Lawa, QC', '2025-10-20', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `residence_status`
--

CREATE TABLE `residence_status` (
  `a_i` int(11) NOT NULL,
  `residence_id` varchar(255) NOT NULL,
  `status` varchar(69) NOT NULL DEFAULT 'none',
  `is_approved` varchar(255) NOT NULL,
  `voters` varchar(69) NOT NULL DEFAULT 'none',
  `pwd` varchar(69) NOT NULL DEFAULT 'none',
  `pwd_info` varchar(255) NOT NULL DEFAULT 'none',
  `senior` varchar(69) NOT NULL DEFAULT 'none',
  `single_parent` varchar(69) NOT NULL DEFAULT 'none',
  `wra` varchar(255) NOT NULL,
  `4ps` varchar(255) NOT NULL,
  `purok_id` varchar(255) NOT NULL,
  `precint_id` varchar(255) NOT NULL,
  `archive` varchar(69) NOT NULL DEFAULT 'none',
  `date_added` varchar(69) NOT NULL DEFAULT 'none',
  `date_archive` varchar(69) NOT NULL DEFAULT 'none',
  `date_unarchive` varchar(69) NOT NULL DEFAULT 'none'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `residence_status`
--

INSERT INTO `residence_status` (`a_i`, `residence_id`, `status`, `is_approved`, `voters`, `pwd`, `pwd_info`, `senior`, `single_parent`, `wra`, `4ps`, `purok_id`, `precint_id`, `archive`, `date_added`, `date_archive`, `date_unarchive`) VALUES
(182, '43880855422082', 'ACTIVE', '', 'YES', 'NO', '', 'NO', 'NO', '', '', '', '', 'NO', '06/25/2025 02:00 PM', 'none', 'none'),
(183, '44920364405135', 'INACTIVE', '', 'YES', 'NO', '', 'NO', 'NO', '', '', '', '', 'YES', '07/18/2025 10:41 AM', '07/18/2025 09:18 AM', 'none'),
(184, '31548169432031', 'ACTIVE', '', '', '', '', 'NO', '', '', '', '', '', 'NO', '07/18/2025 01:52 PM', 'none', 'none'),
(185, '65640009717929', 'ACTIVE', '', 'YES', 'YES', 'bulag', 'NO', 'NO', '', '', '', '', 'NO', '07/18/2025 03:07 PM', 'none', 'none'),
(186, '12202311826724', 'ACTIVE', '', 'NO', 'NO', '', 'NO', 'NO', '', '', '', '', 'NO', '09/13/2025 04:54 PM', 'none', 'none'),
(187, '24389959441883', 'ACTIVE', '', 'YES', 'NO', '', 'NO', 'NO', '', '', '', '', 'NO', '10/13/2025 10:50 PM', 'none', 'none'),
(188, '56562352027011', 'ACTIVE', '', 'YES', 'NO', '', 'NO', '', '', '', '', '', 'NO', '10/14/2025 11:00 AM', 'none', 'none'),
(189, '71850183277563', 'ACTIVE', '', 'YES', 'NO', '', 'NO', '', '', '', '', '', 'NO', '10/14/2025 11:10 AM', 'none', 'none'),
(190, '87582342756244', 'ACTIVE', '', 'NO', 'NO', '', 'NO', 'NO', '', '', '', '', 'NO', '10/14/2025 06:09 PM', 'none', 'none'),
(192, '49240644105535', 'ACTIVE', '', 'YES', 'NO', '', 'NO', '', '', '', '', '', 'NO', '10/20/2025 02:41 PM', 'none', 'none');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `a_i` int(11) NOT NULL,
  `id` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL DEFAULT 'none',
  `middle_name` varchar(255) NOT NULL DEFAULT 'none',
  `last_name` varchar(255) NOT NULL DEFAULT 'none',
  `username` varchar(255) NOT NULL DEFAULT 'none',
  `password` varchar(255) NOT NULL DEFAULT 'none',
  `user_type` varchar(255) NOT NULL DEFAULT 'none',
  `contact_number` varchar(255) NOT NULL DEFAULT 'none',
  `image` varchar(255) NOT NULL DEFAULT 'none',
  `image_path` varchar(255) NOT NULL DEFAULT 'none'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`a_i`, `id`, `first_name`, `middle_name`, `last_name`, `username`, `password`, `user_type`, `contact_number`, `image`, `image_path`) VALUES
(52, '1506135735699', 'Admin', 'Admin', 'Admin', 'admin123', 'admin123', 'admin', '11111111111', '182708071361a0f053c94fb.png', '../assets/dist/img/182708071361a0f053c94fb.png'),
(195, '174668789044820710152022021619941', 'Secretary', 'Secretary', 'Secretary', 'secretary123', 'secretary123', 'secretary', '99999999999', '', ''),
(205, '43880855422082', 'Jabidi', 'Khalid', 'Santos', 'Naegar123', '2022303672', 'resident', '01931243252', '', ''),
(206, '44920364405135', 'Ali', 'Ai', 'Ga', 'aliaiga1121', 'aliaiga1121', 'resident', '09183748713', '', ''),
(207, '31548169432031', 'Mons', '', 'Garcia', 'monsgarcia', 'monsgarcia', 'resident', '09376487316', '170349703968ec932ad4fc9.png', '../assets/dist/img/170349703968ec932ad4fc9.png'),
(208, '65640009717929', 'Cass', '', 'Pansacola', 'kirstencassey', 'kirstencassey', 'resident', '09982746776', '', ''),
(209, '12202311826724', 'Jaun', '', 'Cruz', 'jauncruz', 'jauncruz', 'resident', '09938493849', '', ''),
(210, '24389959441883', 'Augusto', '', 'Hernandez', 'augustohernandez', 'augustoqc', 'resident', '09946587246', '', ''),
(211, '56562352027011', 'Aria', '', 'Antonina', 'ariantonina', 'ariantonina', 'resident', '09972465782', '', ''),
(212, '71850183277563', 'Chris', '', 'Sandoval', 'chrissandoval', 'chrissandoval', 'resident', '09994757774', '', ''),
(213, '87582342756244', 'David', '', 'Santos', 'davidsantos', 'davidsantos', 'resident', '09984766478', '', ''),
(215, '49240644105535', 'Lilo', '', 'Luna', 'liloluna', 'liloluna', 'resident', '09376582765', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `vaccine`
--

CREATE TABLE `vaccine` (
  `a_i` int(11) NOT NULL,
  `vaccine_id` varchar(255) NOT NULL,
  `residence_id` varchar(255) NOT NULL,
  `vaccine` varchar(255) NOT NULL,
  `second_vaccine` varchar(255) NOT NULL,
  `first_dose_date` varchar(255) NOT NULL,
  `second_dose_date` varchar(255) NOT NULL,
  `booster` varchar(255) NOT NULL,
  `booster_date` varchar(255) NOT NULL,
  `second_booster` varchar(255) NOT NULL,
  `second_booster_date` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vaccine`
--

INSERT INTO `vaccine` (`a_i`, `vaccine_id`, `residence_id`, `vaccine`, `second_vaccine`, `first_dose_date`, `second_dose_date`, `booster`, `booster_date`, `second_booster`, `second_booster_date`) VALUES
(35, '3267818051106726', '16455182440138', 'first', 'second', '2022-10-01', '2022-10-02', 'first b', '2022-10-03', 'second b', '2022-10-04'),
(36, '9517807083807772', '54278971251733', 'first', 'second', '2022-10-01', '2022-10-02', 'first b', '2022-10-03', 'second b', '2022-10-04');

-- --------------------------------------------------------

--
-- Table structure for table `wra`
--

CREATE TABLE `wra` (
  `a_i` int(11) NOT NULL,
  `resident_id` varchar(255) NOT NULL,
  `nhts` varchar(255) NOT NULL,
  `pregnant` varchar(255) NOT NULL,
  `menopause` varchar(255) NOT NULL,
  `achieving` varchar(255) NOT NULL,
  `ofw` varchar(255) NOT NULL,
  `fp_method` varchar(255) NOT NULL,
  `desire_limit` varchar(255) NOT NULL,
  `desire_space` varchar(255) NOT NULL,
  `remarks` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wra`
--

INSERT INTO `wra` (`a_i`, `resident_id`, `nhts`, `pregnant`, `menopause`, `achieving`, `ofw`, `fp_method`, `desire_limit`, `desire_space`, `remarks`) VALUES
(62, '16455182440138', 'NTHS', 'YES', 'YES', 'YES', 'YES', 'FP Method', 'YES', 'YES', ''),
(63, '54278971251733', 'NTHS', 'YES', 'YES', 'YES', 'YES', 'FP Method', 'YES', 'YES', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `ambulance_units`
--
ALTER TABLE `ambulance_units`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `backup`
--
ALTER TABLE `backup`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `barangay_information`
--
ALTER TABLE `barangay_information`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blotter_complainant`
--
ALTER TABLE `blotter_complainant`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blotter_info`
--
ALTER TABLE `blotter_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blotter_record`
--
ALTER TABLE `blotter_record`
  ADD PRIMARY KEY (`blotter_id`);

--
-- Indexes for table `blotter_status`
--
ALTER TABLE `blotter_status`
  ADD PRIMARY KEY (`blotter_id`);

--
-- Indexes for table `carousel`
--
ALTER TABLE `carousel`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `certificate`
--
ALTER TABLE `certificate`
  ADD PRIMARY KEY (`a_i`);

--
-- Indexes for table `certificate_request`
--
ALTER TABLE `certificate_request`
  ADD UNIQUE KEY `a_i` (`a_i`);

--
-- Indexes for table `evacuation_status`
--
ALTER TABLE `evacuation_status`
  ADD PRIMARY KEY (`residence_id`);

--
-- Indexes for table `fire_hydrants`
--
ALTER TABLE `fire_hydrants`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `first_aid_officers`
--
ALTER TABLE `first_aid_officers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `house_holds`
--
ALTER TABLE `house_holds`
  ADD PRIMARY KEY (`a_i`);

--
-- Indexes for table `mobile_hq_units`
--
ALTER TABLE `mobile_hq_units`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `official_end_information`
--
ALTER TABLE `official_end_information`
  ADD PRIMARY KEY (`official_id`);

--
-- Indexes for table `official_end_status`
--
ALTER TABLE `official_end_status`
  ADD PRIMARY KEY (`official_id`);

--
-- Indexes for table `official_information`
--
ALTER TABLE `official_information`
  ADD UNIQUE KEY `a_i` (`a_i`);

--
-- Indexes for table `official_status`
--
ALTER TABLE `official_status`
  ADD UNIQUE KEY `a_i` (`a_i`);

--
-- Indexes for table `position`
--
ALTER TABLE `position`
  ADD UNIQUE KEY `a_i` (`a_i`);

--
-- Indexes for table `precint`
--
ALTER TABLE `precint`
  ADD PRIMARY KEY (`a_i`);

--
-- Indexes for table `purok`
--
ALTER TABLE `purok`
  ADD PRIMARY KEY (`a_i`);

--
-- Indexes for table `rescue_boats`
--
ALTER TABLE `rescue_boats`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rescue_equipment`
--
ALTER TABLE `rescue_equipment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `residence_information`
--
ALTER TABLE `residence_information`
  ADD UNIQUE KEY `a_` (`a_i`);

--
-- Indexes for table `residence_status`
--
ALTER TABLE `residence_status`
  ADD UNIQUE KEY `a_i` (`a_i`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD UNIQUE KEY `a_i` (`a_i`);

--
-- Indexes for table `vaccine`
--
ALTER TABLE `vaccine`
  ADD PRIMARY KEY (`a_i`);

--
-- Indexes for table `wra`
--
ALTER TABLE `wra`
  ADD PRIMARY KEY (`a_i`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1510;

--
-- AUTO_INCREMENT for table `ambulance_units`
--
ALTER TABLE `ambulance_units`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `backup`
--
ALTER TABLE `backup`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=171;

--
-- AUTO_INCREMENT for table `carousel`
--
ALTER TABLE `carousel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `certificate`
--
ALTER TABLE `certificate`
  MODIFY `a_i` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `certificate_request`
--
ALTER TABLE `certificate_request`
  MODIFY `a_i` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `fire_hydrants`
--
ALTER TABLE `fire_hydrants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `first_aid_officers`
--
ALTER TABLE `first_aid_officers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `house_holds`
--
ALTER TABLE `house_holds`
  MODIFY `a_i` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=125;

--
-- AUTO_INCREMENT for table `mobile_hq_units`
--
ALTER TABLE `mobile_hq_units`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `official_information`
--
ALTER TABLE `official_information`
  MODIFY `a_i` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `official_status`
--
ALTER TABLE `official_status`
  MODIFY `a_i` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `position`
--
ALTER TABLE `position`
  MODIFY `a_i` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `precint`
--
ALTER TABLE `precint`
  MODIFY `a_i` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `purok`
--
ALTER TABLE `purok`
  MODIFY `a_i` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `rescue_boats`
--
ALTER TABLE `rescue_boats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `rescue_equipment`
--
ALTER TABLE `rescue_equipment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `residence_information`
--
ALTER TABLE `residence_information`
  MODIFY `a_i` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=193;

--
-- AUTO_INCREMENT for table `residence_status`
--
ALTER TABLE `residence_status`
  MODIFY `a_i` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=193;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `a_i` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=216;

--
-- AUTO_INCREMENT for table `vaccine`
--
ALTER TABLE `vaccine`
  MODIFY `a_i` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `wra`
--
ALTER TABLE `wra`
  MODIFY `a_i` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
