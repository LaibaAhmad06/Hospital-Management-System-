-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 15, 2025 at 10:59 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `project.db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
CREATE TABLE IF NOT EXISTS `admin` (
  `AdminID` int NOT NULL AUTO_INCREMENT,
  `Username` varchar(50) NOT NULL,
  `Password` varchar(100) NOT NULL,
  PRIMARY KEY (`AdminID`),
  UNIQUE KEY `Username` (`Username`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`AdminID`, `Username`, `Password`) VALUES
(1, 'admin1', 'password123'),
(2, 'admin2', 'password456');

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

DROP TABLE IF EXISTS `appointments`;
CREATE TABLE IF NOT EXISTS `appointments` (
  `AppointmentID` int NOT NULL AUTO_INCREMENT,
  `PatientID` int DEFAULT NULL,
  `DoctorID` int DEFAULT NULL,
  `AppointmentDate` date DEFAULT NULL,
  `Time` time DEFAULT NULL,
  `Added_by` int DEFAULT NULL,
  PRIMARY KEY (`AppointmentID`),
  KEY `PatientID` (`PatientID`),
  KEY `DoctorID` (`DoctorID`),
  KEY `Added_by` (`Added_by`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`AppointmentID`, `PatientID`, `DoctorID`, `AppointmentDate`, `Time`, `Added_by`) VALUES
(1, 1, 1, '2025-06-15', '10:00:00', 1),
(2, 2, 2, '2025-06-16', '11:00:00', 1),
(3, 3, 3, '2025-06-17', '09:00:00', 2),
(4, 4, 4, '2025-06-18', '14:00:00', 2),
(5, 5, 5, '2025-06-19', '10:30:00', 1),
(6, 1, 2, '2025-06-20', '13:00:00', 1),
(7, 2, 1, '2025-06-21', '15:00:00', 2),
(8, 7, 1, '2024-12-01', '10:00:00', 1),
(9, NULL, 6, '2026-07-08', '09:30:00', 1),
(10, 1, 6, '2026-07-08', '09:30:00', 1),
(11, 1, 2, '2026-05-06', '10:00:00', 1),
(12, 1, 3, '2026-07-08', '09:00:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `billing`
--

DROP TABLE IF EXISTS `billing`;
CREATE TABLE IF NOT EXISTS `billing` (
  `BillID` int NOT NULL AUTO_INCREMENT,
  `PatientID` int DEFAULT NULL,
  `Amount` decimal(10,2) NOT NULL,
  `Date` date DEFAULT NULL,
  `Description` text,
  `Status` enum('Paid','Unpaid') NOT NULL,
  `PaymentMethod` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`BillID`),
  KEY `PatientID` (`PatientID`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `billing`
--

INSERT INTO `billing` (`BillID`, `PatientID`, `Amount`, `Date`, `Description`, `Status`, `PaymentMethod`) VALUES
(1, 1, 2000.00, '2025-06-15', 'Consultation Fee', 'Paid', 'Cash'),
(2, 2, 3500.00, '2025-06-16', 'Treatment Fee', 'Unpaid', 'Card'),
(3, 3, 3000.00, '2025-06-17', 'Consultation Fee', 'Unpaid', 'Card'),
(4, 4, 4500.00, '2025-06-18', 'Surgery Fee', 'Paid', 'Cash'),
(5, 5, 2500.00, '2025-06-19', 'Checkup Fee', 'Unpaid', 'Online'),
(6, 1, 1500.00, '2025-06-20', 'Follow-up', 'Paid', 'Card'),
(7, 2, 2000.00, '2025-06-21', 'Lab Test', 'Unpaid', 'Cash');

-- --------------------------------------------------------

--
-- Table structure for table `consultation`
--

DROP TABLE IF EXISTS `consultation`;
CREATE TABLE IF NOT EXISTS `consultation` (
  `name` int NOT NULL,
  `PatientID` int DEFAULT NULL,
  `Diagnosis` text,
  `Prescription` text,
  `FollowupDate` date DEFAULT NULL,
  PRIMARY KEY (`name`),
  KEY `PatientID` (`PatientID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `consultation`
--

INSERT INTO `consultation` (`name`, `PatientID`, `Diagnosis`, `Prescription`, `FollowupDate`) VALUES
(1, 1, 'Chest Pain', 'Rest and Monitor', '2025-06-22'),
(2, 2, 'Headache', 'Pain Relief', '2025-06-23'),
(3, 3, 'Skin Rash', 'Cream Application', '2025-06-24'),
(4, 4, 'Fractured Arm', 'Cast and Rest', '2025-06-25'),
(5, 5, 'X-Ray Needed', 'Schedule Scan', '2025-06-26'),
(6, 1, 'High BP', 'Medication', '2025-06-27'),
(7, 2, 'Migraine', 'Pain Relief', '2025-06-28'),
(0, 1, 'leg pain ', 'physiotherapy ', '2026-07-01');

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

DROP TABLE IF EXISTS `department`;
CREATE TABLE IF NOT EXISTS `department` (
  `Name` varchar(100) NOT NULL,
  `Location` varchar(100) DEFAULT NULL,
  `HOD` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`Name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`Name`, `Location`, `HOD`) VALUES
('Cardiology', 'Block A', 'Dr. Ahmed Khan'),
('Neurology', 'Block B', 'Dr. Sara Ahmed'),
('Pediatrics', 'Block C', 'Dr. Fatima Zahra'),
('Orthopedics', 'Block D', 'Dr. Rizwan Ahmed'),
('Dermatology', 'Block E', 'Dr. Nadia Javed');

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

DROP TABLE IF EXISTS `doctors`;
CREATE TABLE IF NOT EXISTS `doctors` (
  `DoctorID` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) NOT NULL,
  `Specialization` varchar(100) DEFAULT NULL,
  `PhoneNumber` varchar(15) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `Password` varchar(255) DEFAULT NULL,
  `Department` varchar(100) DEFAULT NULL,
  `LicenceNumber` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`DoctorID`),
  UNIQUE KEY `Email` (`Email`),
  UNIQUE KEY `LicenceNumber` (`LicenceNumber`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`DoctorID`, `Name`, `Specialization`, `PhoneNumber`, `Email`, `Password`, `Department`, `LicenceNumber`) VALUES
(1, 'Dr. Ahmed Khan', 'Cardiology', '03009876543', 'ahmed@example.com', 'docpass1', 'Cardiology', 'LIC001'),
(2, 'Dr. Sara Ahmed', 'Neurology', '03119876542', 'sara@example.com', 'docpass2', 'Neurology', 'LIC002'),
(3, 'Dr. Fatima Zahra', 'Pediatrics', '03098765432', 'fatima.z@example.com', 'docpass3', 'Pediatrics', 'LIC003'),
(4, 'Dr. Rizwan Ahmed', 'Orthopedics', '03187654321', 'rizwan@example.com', 'docpass4', 'Orthopedics', 'LIC004'),
(5, 'Dr. Nadia Javed', 'Dermatology', '03276543210', 'nadia@example.com', 'docpass5', 'Dermatology', 'LIC005'),
(6, 'Dr. Imran Siddiqui', 'Oncology', '03365432109', 'imran@example.com', 'docpass6', 'Oncology', 'LIC006'),
(7, 'Dr. Sana Malik', 'Radiology', '03454321098', 'sana@example.com', 'docpass7', 'Radiology', 'LIC007');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

DROP TABLE IF EXISTS `inventory`;
CREATE TABLE IF NOT EXISTS `inventory` (
  `ItemID` int NOT NULL AUTO_INCREMENT,
  `ItemName` varchar(100) NOT NULL,
  `Category` varchar(50) DEFAULT NULL,
  `Quantity` int DEFAULT NULL,
  `ItemCode` varchar(20) DEFAULT NULL,
  `Price` decimal(10,2) NOT NULL,
  `OrderLevel` int DEFAULT NULL,
  PRIMARY KEY (`ItemID`),
  UNIQUE KEY `ItemCode` (`ItemCode`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`ItemID`, `ItemName`, `Category`, `Quantity`, `ItemCode`, `Price`, `OrderLevel`) VALUES
(1, 'Paracetamol', 'Medicine', 50, 'MED001', 15.02, 10),
(2, 'Bandage', 'Supplies', 35, 'SUP001', 5.00, 5),
(3, 'Aspirin', 'Medicine', 40, 'MED002', 15.00, 15),
(4, 'Syringe', 'Supplies', 20, 'SUP002', 2.00, 10),
(5, 'Gloves', 'Supplies', 100, 'SUP003', 1.50, 50),
(6, 'Antibiotic', 'Medicine', 30, 'MED003', 25.00, 20),
(7, 'Cotton Rolls', 'Supplies', 60, 'SUP004', 3.00, 30);

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

DROP TABLE IF EXISTS `patients`;
CREATE TABLE IF NOT EXISTS `patients` (
  `PatientID` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) NOT NULL,
  `Age` int NOT NULL,
  `Gender` enum('Male','Female','Other') NOT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `Password` varchar(100) DEFAULT NULL,
  `PhoneNumber` varchar(15) DEFAULT NULL,
  `Address` text,
  `CNIC` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`PatientID`),
  UNIQUE KEY `Email` (`Email`),
  UNIQUE KEY `CNIC` (`CNIC`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`PatientID`, `Name`, `Age`, `Gender`, `Email`, `Password`, `PhoneNumber`, `Address`, `CNIC`) VALUES
(1, 'Ali Raza', 28, 'Male', 'ali@example.com', 'patpass1', '03001234567', 'Lahore', '12345-1234567-1'),
(2, 'Fatima Noor', 34, 'Female', 'fatima@example.com', 'patpass2', '03111234567', 'Karachi', '12345-1234567-2'),
(3, 'Hassan Iqbal', 45, 'Male', 'hassan@example.com', 'patpass3', '03012345678', 'Islamabad', '12345-1234567-3'),
(4, 'Ayesha Malik', 29, 'Female', 'ayesha@example.com', 'patpass4', '03123456789', 'Rawalpindi', '12345-1234567-4'),
(5, 'Omar Farooq', 60, 'Male', 'omar@example.com', 'patpass5', '03234567890', 'Multan', '12345-1234567-5'),
(6, 'Zainab Khan', 32, 'Female', 'zainab@example.com', 'patpass6', '03345678901', 'Faisalabad', '12345-1234567-6'),
(7, 'Usman Ali', 37, 'Male', 'usman@example.com', 'patpass7', '03456789012', 'Peshawar', '12345-1234567-7'),
(8, 'tooba Batool', 20, 'Female', 'toobabatoolmehar@gmail.com', '$2y$10$OK1bWAbw24FPVhguNxS0WOnbZLKs07g0./1Jje..1gBTPwMJ8YaFu', '03184118085', 'tung tung sahur , ohio ', '404');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
