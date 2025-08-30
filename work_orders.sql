-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Aug 30, 2025 at 03:34 PM
-- Server version: 8.3.0
-- PHP Version: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `work_orders`
--

-- --------------------------------------------------------

--
-- Table structure for table `notes_for_next_shift`
--

DROP TABLE IF EXISTS `notes_for_next_shift`;
CREATE TABLE IF NOT EXISTS `notes_for_next_shift` (
  `id` int NOT NULL AUTO_INCREMENT,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `notesfornextshift` varchar(255) NOT NULL,
  `engineer` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `notes_for_next_shift`
--

INSERT INTO `notes_for_next_shift` (`id`, `timestamp`, `notesfornextshift`, `engineer`) VALUES
(6, '2025-08-27 17:11:01', 'hghkljhl;k', 'randy');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `room` int NOT NULL,
  `work_to_be_done` text COLLATE utf8mb3_general_mysql500_ci NOT NULL,
  `photo` varchar(255) COLLATE utf8mb3_general_mysql500_ci DEFAULT NULL,
  `completed` tinyint(1) NOT NULL DEFAULT '0',
  `completed_by` varchar(255) COLLATE utf8mb3_general_mysql500_ci DEFAULT NULL,
  `time_completed` timestamp NULL DEFAULT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `submitted_by` varchar(50) COLLATE utf8mb3_general_mysql500_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_mysql500_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `room`, `work_to_be_done`, `photo`, `completed`, `completed_by`, `time_completed`, `time`, `submitted_by`) VALUES
(3, 0, 'test\r\n', NULL, 1, 'randy', '2025-08-26 06:40:35', '2025-08-26 01:28:14', 'J.Strayer'),
(4, 1108, 'Door frame touch up', NULL, 0, NULL, NULL, '2025-08-26 04:17:49', 'Randy'),
(5, 240, 'Door battery', NULL, 0, NULL, NULL, '2025-08-26 04:18:33', 'Randy'),
(6, 240, 'Door battery', NULL, 0, NULL, NULL, '2025-08-26 04:19:44', 'Randy'),
(7, 0, 'Udhhf', NULL, 0, NULL, NULL, '2025-08-26 04:26:29', 'randy'),
(8, 1838, 'Check cooling', NULL, 0, NULL, NULL, '2025-08-26 05:58:03', 'Randy'),
(9, 1, 'fjghfhgf', NULL, 1, 'randy', '2025-08-27 22:08:06', '2025-08-27 17:08:05', 'randy'),
(10, 0, 'jjj', 'uploads/1988.223.jpg', 1, 'Randy', '2025-08-27 22:22:33', '2025-08-27 17:17:22', 'randy'),
(11, 0, 'Check drain line and defrost', NULL, 0, NULL, NULL, '2025-08-30 01:41:27', 'Randy'),
(12, 0, 'check condensate drain', NULL, 0, NULL, NULL, '2025-08-30 04:21:38', 'randy');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`) VALUES
(1, 'randy', '$2y$10$huPfE7tY2kjoAZcYF08w7uEZX..Tlmi9vps8mQwtQaOJoKTp9GLsC', 'rmkdunn@gmail.com'),
(2, 'J.Strayer', '$2y$10$PTlTlblQAtzMtlUDIKr27.6XpBPskyPdr.ORA2mQ6jS.nYIRFG2sS', 'j@gmail.com'),
(3, 'lola', '$2y$10$1g3Qntr57TszC6lGwiQGZeM8UcUwV3fI.CtuzyYUF/BqR0DrelbGO', 'idkyouremail@yahoo.com');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
