-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 02, 2014 at 04:36 PM
-- Server version: 5.5.33
-- PHP Version: 5.4.4-14+deb7u7

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `radio`
--

-- --------------------------------------------------------

--
-- Table structure for table `stations`
--

CREATE TABLE IF NOT EXISTS `stations` (
  `StationID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `StationURL` varchar(400) COLLATE utf8_unicode_ci DEFAULT NULL,
  `FileName` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`StationID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=9 ;

--
-- Dumping data for table `stations`
--

INSERT INTO `stations` (`StationID`, `Name`, `StationURL`, `FileName`) VALUES
(1, 'KCRW Los Angles', 'http://kcrw.ic.llnwd.net/stream/kcrw_live', 'kcrw.png'),
(2, 'The Current Minnesota Public Radio', 'http://current.stream.publicradio.org/kcmp.mp3', 'the_current.png'),
(3, 'CBC Radio One Toronto', 'http://playerservices.streamtheworld.com/pls/CBC_R1_TOR_L.pls', 'cbc.png'),
(4, 'WRN English North America', 'http://193.42.152.215:8000/listen.pls', 'wrn.png'),
(5, 'WRN English Europe', 'http://193.42.152.215:8026/listen.pls', 'wrn.png'),
(6, 'WRN English Africa & Asia Pacific', 'http://193.42.152.215:8012/listen.pls', 'wrn.png'),
(7, 'ABC Radio Australia', 'http://www.abc.net.AU/res/streaming/audio/windows/radio_australia_eng_asia.asx', 'abc_ra.png');

CREATE TABLE IF NOT EXISTS `NowPlaying` (
    `StationID` int(11)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=0 ;


CREATE TABLE IF NOT EXISTS `alarms` (
  `AlarmID` int(11) NOT NULL AUTO_INCREMENT,
  `StationID` int(11) DEFAULT NULL,
  `Date` date DEFAULT NULL,
  `Time` time DEFAULT NULL,
  `fRecurring` int(11) DEFAULT NULL,
  PRIMARY KEY (`AlarmID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=9 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
