-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 23, 2025 at 09:48 AM
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
-- Database: `hrs`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `room_type` varchar(50) NOT NULL,
  `days` int(11) NOT NULL,
  `persons` int(11) NOT NULL,
  `booking_date` date NOT NULL,
  `check_in` date DEFAULT NULL,
  `check_out` date DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `user_id`, `room_id`, `room_type`, `days`, `persons`, `booking_date`, `total_price`, `status`) VALUES
(1, 1, 1, 'Deluxe Room', 4, 3, '2024-12-04', 12000.00, 'pending'),
(2, 1, 2, 'Suite Room', 3, 4, '2024-12-05', 15000.00, 'pending'),
(3, 2, 1, 'Deluxe Room', 4, 2, '2024-12-12', 12000.00, 'pending'),
(4, 2, 2, 'Suite Room', 5, 3, '2024-12-14', 25000.00, 'pending'),
(5, 8, 1, 'Deluxe Room', 3, 10, '2024-12-11', 9000.00, 'pending'),
(6, 7, 1, 'Deluxe Room', 3, 5, '2024-12-04', 9000.00, 'pending'),
(7, 9, 4, 'Luxury Room', 5, 2, '2024-12-03', 30000.00, 'pending'),
(8, 7, 1, 'Deluxe Room', 3, 5, '2024-12-04', 9000.00, 'pending'),
(9, 11, 1, 'Deluxe Room', 7, 2, '2004-12-04', 21000.00, 'pending'),
(10, 13, 2, 'Suite Room', 1, 3, '2024-12-02', 5000.00, 'pending'),
(11, 13, 2, 'Suite Room', 5, 3, '2024-12-05', 25000.00, 'pending'),
(12, 11, 4, 'Luxury Room', 2, 2, '2024-12-24', 12000.00, 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `room_type` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `available` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `dob` date NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Admin','User') DEFAULT 'User',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `phone_number`, `dob`, `gender`, `password`, `role`, `created_at`) VALUES
(1, 'Gelek Namgyal Tamang', 'geleknamgyal51@gmail.com', '9863164952', '2004-03-25', 'Male', '$2y$10$XVrMElHmYVVm11i1oclg4eq5y.jk7mMm4B7FPdHL9KHuA2ucAncv6', 'User', '2024-12-03 03:02:08'),
(2, 'Hisi Maharjan', 'hisimaharjan1@gmail.com', '9848503066', '2003-03-26', 'Female', '$2y$10$fMYHFixjzagQKytnUqD/wuHJ1MpTd9hhXba0coIzEPMqz3yZukfVO', 'User', '2024-12-03 03:13:13'),
(3, 'Nishika Maharjan', 'nishika@gmail.com', '9841083133', '2003-12-24', 'Female', '$2y$10$NDDz3SZhsEq6m6tGkbzM4.yOQgAYwzM7EvEJuWZi4Aj95VP5t.nJ.', 'Admin', '2024-12-03 03:55:16'),
(8, 'Bidur sapkota', 'bidur@gmail.com', '9865711881', '2024-12-12', 'Male', '$2y$10$tOhUukNjfZ4k76neU5z1duPC7wQJjdXPNS5AUXt14wz4SWXgzGUz2', 'User', '2024-12-03 06:01:19'),
(9, 'Shovan Dahal', 'shovan@gmail.com', '9861035712', '2005-12-22', 'Male', '$2y$10$sAKxEgfzssdKrDlSaaa3JOm.vLleJPzBLO5tRMG5j/CnvVjPacvgC', 'User', '2024-12-03 11:02:38'),
(11, 'Pukar Balami', 'pukar@gmail.com', '9852360145', '2004-12-23', 'Other', '$2y$10$xaU1Hnbd5MEdw99elCUfyuqJwosUszvAaILJDfK0RJ2pSOuI1grJK', 'User', '2024-12-04 03:38:31'),
(13, 'Ram Maharjan', 'ram1@gmail.com', 'ABCDDD', '2025-01-25', 'Male', '$2y$10$iNjYIENjJh9bNSEZsBoAU.6l6IwAUkrRsa7I2dOttfB2HXXVGyIuu', 'User', '2024-12-04 07:29:14');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `idx_booking_dates` (`check_in`,`check_out`,`status`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
