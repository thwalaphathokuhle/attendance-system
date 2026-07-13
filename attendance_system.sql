-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 13, 2026 at 02:49 PM
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
-- Database: `attendance_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `clock_in` time DEFAULT NULL,
  `lunch_out` time DEFAULT NULL,
  `lunch_in` time DEFAULT NULL,
  `clock_out` time DEFAULT NULL,
  `is_late` tinyint(1) DEFAULT 0,
  `lunch_late_minutes` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `user_id`, `date`, `clock_in`, `lunch_out`, `lunch_in`, `clock_out`, `is_late`, `lunch_late_minutes`) VALUES
(1, 2, '2026-07-09', '14:08:26', NULL, NULL, '15:00:08', 1, 0),
(2, 2, '2026-07-10', '14:10:56', '14:10:56', '14:11:09', '14:11:09', 0, 0),
(3, 3, '2026-07-10', '14:16:34', '14:16:34', '14:16:58', '14:16:58', 0, 0),
(4, 4, '2026-07-10', '14:32:06', '14:32:17', '14:32:26', '14:32:32', 0, 0),
(5, 2, '2026-07-11', '10:36:21', NULL, NULL, NULL, 1, 0),
(6, 4, '2026-07-11', '10:39:52', NULL, NULL, NULL, 1, 0),
(7, 3, '2026-07-11', '10:40:18', NULL, NULL, NULL, 1, 0),
(8, 2, '2026-07-13', '10:33:57', '10:42:20', '10:42:31', '11:09:00', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `pin` varchar(4) DEFAULT NULL,
  `role` enum('admin','employee') NOT NULL DEFAULT 'employee',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `pin`, `role`, `created_at`) VALUES
(1, 'Admin User', 'admin@vital.com', '$2b$10$Zfl9CAqszyuHN1I/xCWS7OBTjFuabXSsrWNQ5VXwOvSuDMwcJNm.K', NULL, 'admin', '2026-07-09 12:08:04'),
(2, 'Thabo Mkhize', NULL, NULL, '1234', 'employee', '2026-07-09 12:08:04'),
(3, 'Zanele Dube', NULL, NULL, '5678', 'employee', '2026-07-09 12:08:04'),
(4, 'Sipho Ngcobo', NULL, NULL, '9012', 'employee', '2026-07-09 12:08:04');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
