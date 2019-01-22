-- MySQL dump 10.16  Distrib 10.1.37-MariaDB, for debian-linux-gnueabihf (armv8l)
--
-- Host: localhost    Database: radio
-- ------------------------------------------------------
-- Server version	10.1.37-MariaDB-0+deb9u1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `NowPlaying`
--

DROP TABLE IF EXISTS `NowPlaying`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `NowPlaying` (
  `StationID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `NowPlaying`
--

LOCK TABLES `NowPlaying` WRITE;
/*!40000 ALTER TABLE `NowPlaying` DISABLE KEYS */;
INSERT INTO `NowPlaying` VALUES (3);
/*!40000 ALTER TABLE `NowPlaying` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `alarms`
--

DROP TABLE IF EXISTS `alarms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alarms` (
  `AlarmID` int(11) NOT NULL AUTO_INCREMENT,
  `StationID` int(11) DEFAULT NULL,
  `Date` date DEFAULT NULL,
  `Time` time DEFAULT NULL,
  `fRecurring` int(11) DEFAULT NULL,
  PRIMARY KEY (`AlarmID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alarms`
--

LOCK TABLES `alarms` WRITE;
/*!40000 ALTER TABLE `alarms` DISABLE KEYS */;
/*!40000 ALTER TABLE `alarms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `format`
--

DROP TABLE IF EXISTS `format`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `format` (
  `FormatID` int(11) NOT NULL AUTO_INCREMENT,
  `StationID` int(11) DEFAULT NULL,
  `fFormat` varchar(5) DEFAULT 'Talk',
  UNIQUE KEY `FormatID` (`FormatID`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `format`
--

LOCK TABLES `format` WRITE;
/*!40000 ALTER TABLE `format` DISABLE KEYS */;
INSERT INTO `format` VALUES (1,1,'Talk'),(2,2,'Music'),(3,3,'Talk'),(4,4,'Talk'),(5,5,'Talk'),(6,6,'Talk'),(7,7,'Talk'),(8,10,'Talk'),(9,12,'Music'),(10,13,'Music'),(11,14,'Talk'),(12,15,'Talk'),(13,16,'Talk'),(14,20,'Talk'),(15,22,'Talk'),(16,23,'Talk'),(17,24,'Music'),(18,25,'Talk'),(19,26,'Talk'),(20,27,'Talk'),(21,28,'Talk'),(22,29,'Music'),(23,30,'Talk'),(24,31,'Music'),(25,32,'Talk'),(26,33,'Talk'),(27,34,'Talk'),(28,35,'Talk'),(29,36,'Talk');
/*!40000 ALTER TABLE `format` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stations`
--

DROP TABLE IF EXISTS `stations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stations` (
  `StationID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `StationURL` varchar(400) COLLATE utf8_unicode_ci DEFAULT NULL,
  `FileName` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`StationID`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stations`
--

LOCK TABLES `stations` WRITE;
/*!40000 ALTER TABLE `stations` DISABLE KEYS */;
INSERT INTO `stations` VALUES (1,'KCRW Los Angles','http://newmedia.kcrw.com/legacy/pls/kcrwsimulcast.pls','kcrw.png'),(2,'The Current Minnesota Public Radio','http://current.stream.publicradio.org/kcmp.mp3','the_current.png'),(3,'CBC Radio One Toronto','http://cbc_r1_tor.akacast.akamaistream.net/7/632/451661/v1/rc.akacast.akamaistream.net/cbc_r1_tor','cbc.png'),(4,'WRN English North America','http://193.42.152.215:8000/listen.pls','wrn.png'),(5,'WRN English Europe','http://193.42.152.215:8026/listen.pls','wrn.png'),(6,'WRN English Africa & Asia Pacific','http://193.42.152.215:8012/listen.pls','wrn.png'),(7,'ABC Radio Australia','http://abcradiolivehls-lh.akamaihd.net/i/raeng_1@433109/master.m3u8','abc_ra.png'),(10,'BBC World Service','http://www.bbc.co.uk/worldservice/meta/live/mp3/eneuk.pls','bbcws.png'),(12,'WQXR Classical New York','http://www.wqxr.org/stream/wqxr/mp3.pls','wqxr.png'),(13,'KEXP Seattle','http://live-mp3-128.kexp.org:8000/listen.pls','kexp.png'),(14,'WNYC New York','http://fm939.wnyc.org/wnycfm','download.png'),(15,'WBEZ Chicago','http://wbez.ic.llnwd.net/stream/wbez_91_5_fm.pls','wbez.png'),(16,'WBUR Boston','http://wbur-sc.streamguys.com/wbur.mp3','wbur-logo.png'),(20,'Radio Havana Cuba','http://media.enet.cu/radiohabanacuba?MSWMExt=.asf','radio-havana-logo.png'),(22,'RUV 1 Iceland','http://sip-live.hds.adaptive.level3.net/hls-live/ruv-ras1/_definst_/live.m3u8','ruv-default.png'),(23,'KCRW News','http://newmedia.kcrw.com/legacy/pls/kcrwnews.pls','generic_radio.png'),(24,'KCRW music','http://newmedia.kcrw.com/legacy/pls/kcrwmusic.pls','generic_radio.png'),(25,'KQED San Francisco','http://www.kqed.org/radio/listen/kqedradio.m3u','generic_radio.png'),(26,'Radio New Zealand International','http://www.radionz.co.nz/audio/live/rnzi.asx','mq1.jpg'),(27,'RUV 2 Iceland','http://sip-live.hds.adaptive.level3.net/hls-live/ruv-ras2/_definst_/live.m3u8','ruv-default.png'),(28,'Metro Morning (Current or Last Show)','http://192.168.40.59/cbc_recording/cbc.asf','metromorning.jpg'),(29,'X-TRA (Iceland)','http://stream.radio.is:443/fmxtra','s211679q.png'),(30,'Capital Public Radio','http://playerservices.streamtheworld.com/api/livestream-redirect/KXJZ.mp3','cpr.jpg'),(31,'KAKX Mendocino High School Radio','http://kakx.mcn.org:8000/KAKX_Live.m3u','kakx.jpg'),(32,'KUBU Access Sacramento','http://crystalout.surfernetwork.com:8001/KUBU-LP_MP3','kubu.png'),(33,'KVMR Nevada City','http://live2.kvmr.org:8190/kvmr.m3u','kvmr.png'),(34,'LCC Radio WLNZ','https://ice64.securenetsystems.net/WLNZ?&playSessionID=C7903D63-B977-507D-FBE0FDDA8E0DE550','lcc_radio_logo.png'),(35,'Lansing (MSU) NPR WKAR Talk','https://streaming.wkar.msu.edu/wkar-am-mp3','wkar.png'),(36,'Michigan Radio NPR','http://playerservices.streamtheworld.com/pls/WUOMFM.pls','mir-logo__1_.jpg');
/*!40000 ALTER TABLE `stations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `timeshift`
--

DROP TABLE IF EXISTS `timeshift`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `timeshift` (
  `timeshiftID` int(11) NOT NULL AUTO_INCREMENT,
  `StationID` int(11) NOT NULL,
  PRIMARY KEY (`timeshiftID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `timeshift`
--

LOCK TABLES `timeshift` WRITE;
/*!40000 ALTER TABLE `timeshift` DISABLE KEYS */;
INSERT INTO `timeshift` VALUES (1,28);
/*!40000 ALTER TABLE `timeshift` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'radio'
--
