-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 29, 2025 at 08:45 AM
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
-- Database: `udho_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `document_routing`
--

CREATE TABLE `document_routing` (
  `id` int(11) NOT NULL,
  `routing_slip_id` int(11) DEFAULT NULL,
  `route_date` date DEFAULT NULL,
  `route_from` varchar(100) DEFAULT NULL,
  `route_to` varchar(100) DEFAULT NULL,
  `instructions` text DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `action_taken` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `routing_slips`
--

CREATE TABLE `routing_slips` (
  `id` int(11) NOT NULL,
  `control_no` varchar(50) NOT NULL,
  `direction` enum('Incoming','Outgoing') NOT NULL,
  `document_type` text NOT NULL,
  `copy_type` enum('Original','Photocopy','Scanned') NOT NULL,
  `priority` text DEFAULT NULL,
  `sender` varchar(100) NOT NULL,
  `date_time` datetime NOT NULL,
  `contact_no` varchar(20) NOT NULL,
  `subject` text NOT NULL,
  `routing_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`routing_data`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` varchar(50) DEFAULT 'Pending',
  `document_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `routing_slips`
--

INSERT INTO `routing_slips` (`id`, `control_no`, `direction`, `document_type`, `copy_type`, `priority`, `sender`, `date_time`, `contact_no`, `subject`, `routing_data`, `created_at`, `updated_at`, `status`, `document_path`) VALUES
(21, 'UDHO-2025-0001', 'Incoming', 'Memo Letter', 'Original', '', 'Urban Development Deparment Office', '2025-07-29 01:23:00', '0992231', 'adawdada', '[]', '2025-07-29 01:23:48', '2025-07-29 01:23:48', 'Pending', NULL),
(27, 'UDHO-2025-0002', 'Incoming', 'Referral Request', 'Photocopy', '', 'Sample Data', '2025-07-28 17:18:00', '0992231', 'Sample Data', '[{\"date\":\"2025-07-29\",\"from\":\"GSO\",\"to\":\"UDHO\",\"actions\":\"\",\"due_date\":\"\",\"action_taken\":\"\"}]', '2025-07-29 03:17:48', '2025-07-29 03:17:48', 'Completed', NULL),
(28, 'UDHO-2025-0002', 'Outgoing', 'Memo Letter', 'Photocopy', '3 days', 'Urban Development Deparment Office', '2025-07-29 06:33:00', '0992231', 'Sample Data', '[{\"date\":\"2025-07-29\",\"from\":\"Sample\",\"to\":\"Smaple\",\"actions\":\"\",\"due_date\":\"\",\"action_taken\":\"\"}]', '2025-07-29 06:33:37', '2025-07-29 06:33:37', 'Pending', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Admin','Operation','Admin Executive','HOA','Enumerator') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_picture` varchar(255) DEFAULT 'default_profile.jpg',
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password_changed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`, `profile_picture`, `email`, `phone`, `password_changed_at`) VALUES
(1, '', 'admin001', 'Enumerator', '2025-07-27 23:41:31', '68886da015b35.png', 'spencerrestoso27@gmail.com', '0955193799', '2025-07-29 01:49:42'),
(2, 'operator', 'operator', 'Operation', '2025-07-27 23:41:31', 'operator_profile.jpg', 'operator@udho.gov.ph', '+639123456788', '2025-07-29 01:49:42'),
(3, 'executive', 'executive', 'Admin Executive', '2025-07-27 23:41:31', 'executive_profile.jpg', 'executive@udho.gov.ph', '+639123456787', '2025-07-29 01:49:42'),
(4, 'hoa', 'hoa', 'HOA', '2025-07-27 23:41:31', 'hoa_profile.jpg', 'hoa@udho.gov.ph', '+639123456786', '2025-07-29 01:49:42'),
(5, 'enumerator', 'enumerator', 'Enumerator', '2025-07-27 23:41:31', 'enumerator_profile.jpg', 'enumerator@udho.gov.ph', '+639123456785', '2025-07-29 01:49:42');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `document_routing`
--
ALTER TABLE `document_routing`
  ADD PRIMARY KEY (`id`),
  ADD KEY `routing_slip_id` (`routing_slip_id`);

--
-- Indexes for table `routing_slips`
--
ALTER TABLE `routing_slips`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `control_no_direction` (`control_no`,`direction`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `document_routing`
--
ALTER TABLE `document_routing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `routing_slips`
--
ALTER TABLE `routing_slips`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `document_routing`
--
ALTER TABLE `document_routing`
  ADD CONSTRAINT `document_routing_ibfk_1` FOREIGN KEY (`routing_slip_id`) REFERENCES `routing_slips` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
