-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 20, 2018 at 03:32 AM
-- Server version: 10.2.11-MariaDB-log
-- PHP Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tsbac`
--
CREATE DATABASE IF NOT EXISTS `tsbac` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `tsbac`;

-- --------------------------------------------------------

--
-- Table structure for table `bio`
--

DROP TABLE IF EXISTS `bio`;
CREATE TABLE IF NOT EXISTS `bio` (
  `BioID` int(11) NOT NULL AUTO_INCREMENT,
  `Email` varchar(50) NOT NULL,
  `Birthdate` date DEFAULT NULL,
  `Location` longtext DEFAULT NULL,
  `AboutMe` longtext DEFAULT NULL,
  `Interests` longtext DEFAULT NULL,
  `School` longtext DEFAULT NULL,
  PRIMARY KEY (`BioID`),
  UNIQUE KEY `Email_2` (`Email`),
  KEY `Email` (`Email`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `chatter`
--

DROP TABLE IF EXISTS `chatter`;
CREATE TABLE IF NOT EXISTS `chatter` (
  `MsgID` bigint(20) NOT NULL AUTO_INCREMENT,
  `Nickname` varchar(50) NOT NULL,
  `Email` varchar(50) NOT NULL,
  `DateTime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Message` longtext NOT NULL,
  `recipient` varchar(50) DEFAULT NULL,
  `read` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`MsgID`),
  KEY `recipient` (`recipient`)
) ENGINE=InnoDB AUTO_INCREMENT=123 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
CREATE TABLE IF NOT EXISTS `comments` (
  `CommentID` bigint(20) NOT NULL AUTO_INCREMENT,
  `ImgID` int(11) NOT NULL,
  `CommentDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `Email` varchar(50) NOT NULL,
  `Comment` longtext NOT NULL,
  PRIMARY KEY (`CommentID`),
  KEY `Email` (`Email`),
  KEY `ImgID` (`ImgID`)
) ENGINE=InnoDB AUTO_INCREMENT=167 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Images`
--

DROP TABLE IF EXISTS `Images`;
CREATE TABLE IF NOT EXISTS `Images` (
  `ImgID` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Image ID',
  `Filename` varchar(100) NOT NULL COMMENT 'File Name',
  `Name` varchar(100) DEFAULT NULL COMMENT 'Title',
  `Email` varchar(50) NOT NULL COMMENT 'Submitted By',
  `Date` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Date submitted/created',
  `Desc` longtext DEFAULT NULL COMMENT 'Description',
  `Anonymous` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`ImgID`),
  KEY `Email` (`Email`)
) ENGINE=InnoDB AUTO_INCREMENT=300 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `licks`
--

DROP TABLE IF EXISTS `licks`;
CREATE TABLE IF NOT EXISTS `licks` (
  `lickID` bigint(20) NOT NULL AUTO_INCREMENT,
  `ImgID` int(11) NOT NULL,
  `Email` varchar(50) NOT NULL,
  PRIMARY KEY (`lickID`),
  KEY `ImgID` (`ImgID`,`Email`),
  KEY `Email` (`Email`)
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Series`
--

DROP TABLE IF EXISTS `Series`;
CREATE TABLE IF NOT EXISTS `Series` (
  `SeriesID` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Series ID#',
  `Tag` varchar(25) NOT NULL COMMENT 'Search for',
  `Name` varchar(50) NOT NULL COMMENT 'Name of series',
  `SeriesOf` int(11) DEFAULT NULL COMMENT 'Parent Series ID',
  `Visible` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`SeriesID`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `User`
--

DROP TABLE IF EXISTS `User`;
CREATE TABLE IF NOT EXISTS `User` (
  `Email` varchar(50) NOT NULL COMMENT 'E-Mail Address',
  `DisplayName` varchar(25) NOT NULL,
  `FirstName` varchar(50) DEFAULT NULL,
  `LastName` varchar(50) DEFAULT NULL,
  `Password` text DEFAULT NULL,
  `UserLevelID` int(11) DEFAULT 1,
  `UploadPath` text DEFAULT NULL,
  PRIMARY KEY (`Email`),
  UNIQUE KEY `Email` (`Email`),
  UNIQUE KEY `DisplayName` (`DisplayName`),
  KEY `UserLevelID` (`UserLevelID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `UserLevel`
--

DROP TABLE IF EXISTS `UserLevel`;
CREATE TABLE IF NOT EXISTS `UserLevel` (
  `UserLevelID` int(11) NOT NULL,
  `LevelName` varchar(50) NOT NULL,
  `Description` text NOT NULL,
  PRIMARY KEY (`UserLevelID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `UserLevel`
--

INSERT INTO `UserLevel` (`UserLevelID`, `LevelName`, `Description`) VALUES
(-1, 'Banned', 'User has been banned.'),
(0, 'Silenced', 'Posts are not visible.'),
(1, 'Commenter', 'Able to comment.'),
(2, 'Verified Commenter', 'Commenter has verified their email and set a password.'),
(50, 'Member', 'Able to Upload.  A full fledged member of the site.'),
(100, 'Administrator', 'All powers available to the user.');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `User`
--
ALTER TABLE `User`
  ADD CONSTRAINT `User_ibfk_1` FOREIGN KEY (`UserLevelID`) REFERENCES `UserLevel` (`UserLevelID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
