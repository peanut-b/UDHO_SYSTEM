-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Aug 12, 2025 at 04:12 AM
-- Server version: 10.11.10-MariaDB
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u687661100_udho_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `certificates`
--

CREATE TABLE `certificates` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `control_number` varchar(50) NOT NULL,
  `date_issued` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `request_count` int(11) DEFAULT 1,
  `previous_request_date` datetime DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `certificates`
--

INSERT INTO `certificates` (`id`, `name`, `address`, `control_number`, `date_issued`, `created_at`, `request_count`, `previous_request_date`, `updated_at`) VALUES
(13, 'Spencer Restoso', '27 - Jasmin Street, Brgy 184, zone 19, Maricaban, Pasay City', '2025-001', '2025-08-07 16:57:58', '2025-08-07 16:57:58', 1, NULL, '2025-08-07 08:57:58'),
(14, 'Spencer Restoso', '27 - Jasmin Street, Brgy 184, zone 19, Maricaban, Pasay City', '2025-002', '2025-08-07 16:58:44', '2025-08-07 16:58:44', 2, '2025-08-07 16:57:58', '2025-08-07 08:58:44'),
(15, 'Spencer G. Restoso', '27 - C Jasmin Street. Brgy 184, zone 19, Maricaban, Pasay City', '2025-003', '2025-08-09 15:34:52', '2025-08-09 15:34:52', 1, NULL, '2025-08-09 07:34:52');

-- --------------------------------------------------------

--
-- Table structure for table `hoa_payments`
--

CREATE TABLE `hoa_payments` (
  `id` int(11) NOT NULL,
  `hoa_id` int(11) NOT NULL,
  `hoa_name` varchar(255) NOT NULL,
  `payment_period` varchar(100) NOT NULL,
  `due_date` date NOT NULL,
  `amount_due` decimal(10,2) NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `status` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hoa_payments`
--

INSERT INTO `hoa_payments` (`id`, `hoa_id`, `hoa_name`, `payment_period`, `due_date`, `amount_due`, `amount_paid`, `status`, `created_at`) VALUES
(1, 1, 'Geronimo Association', 'August 2025', '2025-08-19', 1500.00, 1500.00, 'paid', '2025-08-12 03:33:55');

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
(32, 'UDHO-2025-0001', 'Incoming', 'Memo Letter', 'Original', '', 'Urban Development Deparment Office', '2025-08-03 10:50:00', 'Sample', 'Sample Data', '[]', '2025-08-03 10:50:53', '2025-08-03 10:50:53', 'Pending', NULL),
(33, 'UDHO-2025-0002', 'Incoming', 'Memo Letter', 'Original', '', 'Urban Development Deparment Office', '2025-08-03 11:08:00', '0992231', 'sample', '[]', '2025-08-03 11:08:16', '2025-08-03 11:08:16', 'Completed', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `survey_responses`
--

CREATE TABLE `survey_responses` (
  `id` int(11) NOT NULL,
  `submitted_at` timestamp NULL DEFAULT current_timestamp(),
  `respondent_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `location_lat` decimal(10,8) DEFAULT NULL,
  `location_lng` decimal(11,8) DEFAULT NULL,
  `answers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`answers`)),
  `signature` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(2, 'operator', 'operator', 'Operation', '2025-07-27 23:41:31', 'operator_profile.jpg', 'operator@udho.gov.ph', '+639123456788', '2025-07-29 01:49:42'),
(3, 'executive', 'executive', 'Admin Executive', '2025-07-27 23:41:31', 'executive_profile.jpg', 'executive@udho.gov.ph', '+639123456787', '2025-07-29 01:49:42'),
(4, 'hoa', 'hoa', 'HOA', '2025-07-27 23:41:31', 'hoa_profile.jpg', 'hoa@udho.gov.ph', '+639123456786', '2025-07-29 01:49:42'),
(5, 'enumeration', 'enumerator', 'Enumerator', '2025-07-27 23:41:31', 'enumerator_profile.jpg', 'enumerator@udho.gov.ph', '+639123456785', '2025-07-29 01:49:42'),
(6, 'admin', 'securepassword123', 'Admin', '2025-08-03 10:17:52', 'admin_profile.jpg', 'admin@udho.gov.ph', '+639123456789', '2025-08-03 10:17:52'),
(1234512345, 'wer', '123456', 'Enumerator', '2025-08-10 11:22:25', 'PROFILE_SAMPLE.jpg', 'natalioropert091003@gmail.com', '09954206596', '2025-08-10 11:22:25'),
(1234561234, 'why', '12345678', 'Enumerator', '2025-08-11 14:08:06', 'PROFILE_SAMPLE.jpg', 'rtnatalio@paterostechnologicalcollege.edu.ph', '09954206222', '2025-08-11 14:08:06');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `certificates`
--
ALTER TABLE `certificates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `control_number` (`control_number`);

--
-- Indexes for table `hoa_payments`
--
ALTER TABLE `hoa_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hoa_id` (`hoa_id`),
  ADD KEY `status` (`status`),
  ADD KEY `due_date` (`due_date`);

--
-- Indexes for table `routing_slips`
--
ALTER TABLE `routing_slips`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `control_no_direction` (`control_no`,`direction`);

--
-- Indexes for table `survey_responses`
--
ALTER TABLE `survey_responses`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `certificates`
--
ALTER TABLE `certificates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `hoa_payments`
--
ALTER TABLE `hoa_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `routing_slips`
--
ALTER TABLE `routing_slips`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `survey_responses`
--
ALTER TABLE `survey_responses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2147483648;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
