-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 09, 2026 at 08:57 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `weather_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `weather_records`
--

CREATE TABLE `weather_records` (
  `id` int(11) NOT NULL,
  `city` varchar(100) DEFAULT NULL,
  `temperature` float DEFAULT NULL,
  `humidity` int(11) DEFAULT NULL,
  `weather_condition` varchar(100) DEFAULT NULL,
  `recorded_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `weather_records`
--

INSERT INTO `weather_records` (`id`, `city`, `temperature`, `humidity`, `weather_condition`, `recorded_at`) VALUES
(8, 'Pune', 17.9, 40, 'Clear', '2026-01-10 01:11:21'),
(11, 'Mumbai', 24.2, 50, 'Overcast', '2026-01-10 01:16:38'),
(12, 'Delhi', 11.9, 69, 'Light drizzle', '2026-01-10 01:17:24');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `weather_records`
--
ALTER TABLE `weather_records`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `weather_records`
--
ALTER TABLE `weather_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
