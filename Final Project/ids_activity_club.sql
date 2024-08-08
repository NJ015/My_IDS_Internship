-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Aug 05, 2024 at 09:58 PM
-- Server version: 8.2.0
-- PHP Version: 8.2.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ids_activity_club`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
CREATE TABLE IF NOT EXISTS `admin` (
  `UserID` int NOT NULL,
  PRIMARY KEY (`UserID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`UserID`) VALUES
(1);

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
CREATE TABLE IF NOT EXISTS `events` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `Category` varchar(50) DEFAULT NULL,
  `Name` varchar(100) DEFAULT NULL,
  `Destination` varchar(100) DEFAULT NULL,
  `Date_from` date DEFAULT NULL,
  `Date_to` date DEFAULT NULL,
  `Description` text,
  `Status` varchar(50) DEFAULT NULL,
  `Cost` decimal(10,2) DEFAULT NULL,
  `AdminID` int DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `AdminID` (`AdminID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guide`
--

DROP TABLE IF EXISTS `guide`;
CREATE TABLE IF NOT EXISTS `guide` (
  `UserID` int NOT NULL,
  `Responsible_events` int DEFAULT NULL,
  PRIMARY KEY (`UserID`),
  KEY `Responsible_events` (`Responsible_events`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `guide`
--

INSERT INTO `guide` (`UserID`, `Responsible_events`) VALUES
(8, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `guide_event`
--

DROP TABLE IF EXISTS `guide_event`;
CREATE TABLE IF NOT EXISTS `guide_event` (
  `GuideID` int NOT NULL,
  `EventID` int NOT NULL,
  PRIMARY KEY (`GuideID`,`EventID`),
  KEY `EventID` (`EventID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lookup`
--

DROP TABLE IF EXISTS `lookup`;
CREATE TABLE IF NOT EXISTS `lookup` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(50) DEFAULT NULL,
  `Order` int DEFAULT NULL,
  `Code` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `member`
--

DROP TABLE IF EXISTS `member`;
CREATE TABLE IF NOT EXISTS `member` (
  `UserID` int NOT NULL,
  `Joined_events` int DEFAULT NULL,
  PRIMARY KEY (`UserID`),
  KEY `Joined_events` (`Joined_events`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `member`
--

INSERT INTO `member` (`UserID`, `Joined_events`) VALUES
(2, NULL),
(3, NULL),
(4, NULL),
(5, NULL),
(6, NULL),
(7, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `member_event`
--

DROP TABLE IF EXISTS `member_event`;
CREATE TABLE IF NOT EXISTS `member_event` (
  `MemberID` int NOT NULL,
  `EventID` int NOT NULL,
  PRIMARY KEY (`MemberID`,`EventID`),
  KEY `EventID` (`EventID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `Role` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `GenderID` int DEFAULT NULL,
  `FirstName` varchar(50) DEFAULT NULL,
  `MiddleName` varchar(50) DEFAULT NULL,
  `LastName` varchar(50) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `Password` varchar(100) DEFAULT NULL,
  `DOB` date DEFAULT NULL,
  `Age` int DEFAULT NULL,
  `Joining_Date` date DEFAULT NULL,
  `Profession` varchar(100) DEFAULT NULL,
  `Photo` varchar(255) DEFAULT NULL,
  `Emergency_number` varchar(15) DEFAULT NULL,
  `Phone_nb` varchar(15) DEFAULT NULL,
  `Nationality` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Email` (`Email`),
  KEY `GenderID` (`GenderID`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`ID`, `Role`, `GenderID`, `FirstName`, `MiddleName`, `LastName`, `Email`, `Password`, `DOB`, `Age`, `Joining_Date`, `Profession`, `Photo`, `Emergency_number`, `Phone_nb`, `Nationality`) VALUES
(1, 'Admin', 2, 'Nour', 'Imad', 'Jalloul', 'nourj2005@gmail.com', '$2y$10$cfN41VgzIrzdUsKeYBWadOMpSDLOX7ZQuRjiURhhwSdDW4QMFJRoq', '2005-01-05', 19, '2024-07-20', 'Programmer', '', '03202500', '70166557', 'Lebanese'),
(2, 'Member', 2, 'Jon', 'Stewart', 'Doe', 'test@example.us', '$2y$10$hPPTCyGBo3CF5MMAgoP7g.UlSoJKujQIT3VOeefp0gJfjldi.9HHe', '2024-07-29', 0, '2024-08-04', '', NULL, NULL, NULL, NULL),
(3, 'Member', 2, 'Juan Francisco', 'Stewart', 'García Flores', 'ejemplo@ejemplo.mx', '$2y$10$LiWYMkKoKf2t7F7xdcUhnOTXgd0kc4rK42OXjjRaEWGtGsBO5ET2S', '2024-07-29', 0, '2024-08-04', '', '', NULL, NULL, NULL),
(4, 'Member', 2, 'João', 'Stewart', 'Souza Silva', 'teste@exemplo.us', '$2y$10$z94hrAZCz3vkB86WY3FIzOoOpZhSO4D5PCQcZkDs6v6d7m5B/mXcq', '2024-07-29', 0, '2024-08-04', '', '', NULL, NULL, NULL),
(5, 'Member', 2, 'Gottfried', 'Wilhelm', 'Leibniz', 'test@beispiel.de', '$2y$10$jq3lwWiOsJJC1G2CtTITzOjk0izQ82rC7680uRluOK9N8lbRqkRFy', '2024-07-29', 0, '2024-08-04', 'sfdgfc', '', NULL, NULL, NULL),
(7, 'Member', 1, 'ali', 'idk', 'berro', 'ali@gmail.com', '$2y$10$hx8DJvhva3BOIhlQAThZkOMICftXlNPseOy8mYw6pKnbRTrf40XVa', '2024-07-30', 0, '2024-08-05', 'Programmer', '', '123409876', '12345678', 'Lebanese'),
(8, 'Guide', 2, 'tasneem', 'I', 'Jalloul', 't@gmail.com', '$2y$10$pumjCRg2DkgxhT406RTlU.NH4aSF3XkPmxLT94MBNWYa4JShzzdKq', '2024-07-29', 0, '2024-08-05', 'Student', 'https://robohash.org/t%40gmail.com', '09876543221', NULL, 'Lebanese');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
