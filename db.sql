-- MySQL dump 10.13  Distrib 5.5.43, for debian-linux-gnu (x86_64)
--
-- Host: 127.0.0.1    Database: rainbowmondays
-- ------------------------------------------------------
-- Server version	5.5.43-0ubuntu0.14.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `districts`
--

DROP TABLE IF EXISTS `districts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `districts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `districtId` int(11) DEFAULT NULL,
  `name` longtext,
  `longitude` text NOT NULL,
  `latitude` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=86 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `districts`
--

LOCK TABLES `districts` WRITE;
/*!40000 ALTER TABLE `districts` DISABLE KEYS */;
INSERT INTO `districts` VALUES (7,1,'Far North','173.5070203','-35.1290228'),(8,3,'Kaipara','174.979741','-37.0583107'),(9,2,'Whangarei','174.323708','-35.7251117'),(10,7,'Auckland City','174.7633315','-36.8484597'),(11,10,'Franklin','175.2009162','-37.9894571'),(12,81,'Hauraki Gulf Islands','175.1894045','-36.4263135'),(13,8,'Manukau City','174.87266','-36.9941017'),(14,5,'North Shore City','175.063378','-36.949327'),(15,9,'Papakura','174.9436176','-37.0677049'),(16,4,'Rodney','172.7','-42.5333333'),(17,77,'Waiheke Island','175.1127916','-36.7978867'),(18,6,'Waitakere City','174.5425402','-36.8504238'),(19,16,'Hamilton','175.279253','-37.7870012'),(20,12,'Hauraki','175.1894045','-36.4263135'),(21,15,'Matamata-Piako','175.684578','-37.6450708'),(22,18,'Otorohanga','175.2098888','-38.1888076'),(23,19,'South Waikato','175.4375574','-38.0594263'),(24,21,'Taupo','176.0702098','-38.6856924'),(25,11,'Thames-Coromandel','175.684578','-37.1332657'),(26,13,'Waikato','175.4375574','-38.0594263'),(27,17,'Waipa','175.135366','-37.7527014'),(28,20,'Waitomo','175.1145451','-38.2615305'),(29,26,'Kawerau','176.6989753','-38.0846351'),(30,27,'Opotiki','177.2871279','-38.007228'),(31,24,'Rotorua','176.2497461','-38.1368478'),(32,23,'Tauranga','176.1651295','-37.6877975'),(33,22,'Western Bay Of Plenty','176.2158497','-37.8754556'),(34,25,'Whakatane','176.9908015','-37.9534115'),(35,28,'Gisborne','178.017649','-38.662334'),(36,32,'Central Hawke\'s Bay','176.4996546','-40.0106163'),(37,30,'Hastings','176.8392322','-39.6395783'),(38,31,'Napier','176.9120178','-39.4928444'),(39,29,'Wairoa','177.4180311','-39.035173'),(40,33,'New Plymouth','174.0752278','-39.0556253'),(41,35,'South Taranaki','174.4382721','-39.3538149'),(42,34,'Stratford','174.2838238','-39.3370781'),(43,42,'Horowhenua','175.3136218','-40.5804655'),(44,39,'Manawatu','175.7131269','-40.3854367'),(45,40,'Palmerston North','175.6082145','-40.3523065'),(46,38,'Rangitikei','175.5708311','-40.0066834'),(47,36,'Ruapehu','175.5685104','-39.2817207'),(48,41,'Tararua','176.0121573','-40.4848018'),(49,37,'Wanganui','175.0478901','-39.9300887'),(50,49,'Carterton','175.5235181','-41.0291733'),(51,43,'Kapiti Coast','174.9845636','-40.9003641'),(52,46,'Lower Hutt','174.9080557','-41.2091655'),(53,48,'Masterton','175.6573502','-40.9511118'),(54,44,'Porirua','174.8406006','-41.1338998'),(55,50,'South Wairarapa','175.6622383','-40.9502313'),(56,45,'Upper Hutt','175.0707834','-41.1244327'),(57,47,'Wellington','174.776236','-41.2864603'),(58,52,'Nelson','173.2839653','-41.2706319'),(59,51,'Tasman','172.7347142','-41.2122123'),(60,82,'Blenheim','173.9612498','-41.5134425'),(61,54,'Kaikoura','173.681386','-42.4008174'),(62,53,'Marlborough','173.4216613','-41.57269'),(63,55,'Buller','171.8598683','-41.7954153'),(64,56,'Grey','170.1833333','-43.5666667'),(65,57,'Westland','170.4241514','-45.9024478'),(66,63,'Ashburton','171.7485672','-43.9083813'),(67,61,'Banks Peninsula','173','-43.75'),(68,60,'Christchurch City','172.6362254','-43.5320544'),(69,58,'Hurunui','172.7347142','-42.7891728'),(70,65,'Mackenzie','170.0945534','-44.2579661'),(71,62,'Selwyn','172.226327','-43.648714'),(72,64,'Timaru','171.2549729','-44.3969718'),(73,59,'Waimakariri','172.5898469','-43.3041127'),(74,66,'Waimate','171.048135','-44.7326402'),(75,69,'Central Otago','169.6567993','-45.2828265'),(76,72,'Clutha','169.526244','-45.700066'),(77,71,'Dunedin','170.5027976','-45.8787605'),(78,70,'Queenstown-Lakes','168.6626435','-45.0311622'),(79,79,'South Otago','169.6345253','-44.8280041'),(80,68,'Waitaki','170.7909442','-44.8168644'),(81,83,'Wanaka','169.1320981','-44.7031813'),(82,78,'Catlins','175.6058288','-40.3309037'),(83,74,'Gore','168.945819','-46.0987992'),(84,75,'Invercargill','168.3537731','-46.4131866'),(85,73,'Southland','167.6755387','-45.8489159');
/*!40000 ALTER TABLE `districts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `batchId` int(11) NOT NULL,
  `jobId` int(11) NOT NULL,
  `locationId` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
INSERT INTO `jobs` VALUES (10,1,4183980,19);
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-07-23 16:36:23
