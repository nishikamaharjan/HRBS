-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 06, 2026 at 11:37 AM
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
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending',
  `check_in` date DEFAULT NULL,
  `check_out` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `user_id`, `room_id`, `room_type`, `days`, `persons`, `booking_date`, `total_price`, `status`, `check_in`, `check_out`) VALUES
(1, 1, 1, 'Deluxe Room', 4, 3, '2024-12-04', 12000.00, 'pending', NULL, NULL),
(2, 1, 2, 'Suite Room', 3, 4, '2024-12-05', 15000.00, 'pending', NULL, NULL),
(3, 2, 1, 'Deluxe Room', 4, 2, '2024-12-12', 12000.00, 'pending', NULL, NULL),
(4, 2, 2, 'Suite Room', 5, 3, '2024-12-14', 25000.00, 'pending', NULL, NULL),
(5, 8, 1, 'Deluxe Room', 3, 10, '2024-12-11', 9000.00, 'pending', NULL, NULL),
(6, 7, 1, 'Deluxe Room', 3, 5, '2024-12-04', 9000.00, 'cancelled', NULL, NULL),
(7, 9, 4, 'Luxury Room', 5, 2, '2024-12-03', 30000.00, 'pending', NULL, NULL),
(8, 7, 1, 'Deluxe Room', 3, 5, '2024-12-04', 9000.00, 'cancelled', NULL, NULL),
(9, 11, 1, 'Deluxe Room', 7, 2, '2004-12-04', 21000.00, 'pending', NULL, NULL),
(10, 13, 2, 'Suite Room', 1, 3, '2024-12-02', 5000.00, 'pending', NULL, NULL),
(11, 13, 2, 'Suite Room', 5, 3, '2024-12-05', 25000.00, 'pending', NULL, NULL),
(12, 11, 4, 'Luxury Room', 2, 2, '2024-12-24', 12000.00, 'pending', NULL, NULL),
(15, 14, 1, 'Normal Room', 3, 2, '2025-08-20', 4500.00, 'confirmed', NULL, NULL),
(16, 14, 1, 'Normal Room', 4, 3, '2025-08-20', 6000.00, 'pending', NULL, NULL),
(17, 14, 1, 'Normal Room', 2, 2, '2025-08-21', 3000.00, 'confirmed', NULL, NULL),
(18, 14, 1, 'Normal Room', 2, 4, '2025-08-21', 3000.00, 'confirmed', NULL, NULL),
(19, 15, 1, 'Normal Room', 8, 2, '2026-01-08', 12000.00, 'pending', NULL, NULL),
(20, 18, 1, 'Normal Room', 28, 2, '2026-01-08', 42000.00, 'confirmed', NULL, NULL),
(21, 3, 1, 'Premium Room', 2, 2, '2026-01-09', 9000.00, 'cancelled', NULL, NULL),
(22, 3, 1, 'Normal Room', 2, 4, '2026-01-09', 3000.00, 'cancelled', NULL, NULL),
(23, 3, 1, 'Normal Room', 2, 4, '2026-01-09', 3000.00, 'confirmed', NULL, NULL),
(24, 15, 1, 'Normal Room', 1, 2, '2026-01-10', 3217.50, 'confirmed', NULL, NULL),
(25, 15, 1, 'Premium Room', 3, 2, '2026-01-10', 22507.88, 'confirmed', NULL, NULL),
(26, 15, 1, 'Normal Room', 2, 2, '2026-01-12', 4680.00, 'cancelled', NULL, NULL),
(27, 22, 1, 'Normal Room', 4, 2, '2026-01-12', 8731.13, 'cancelled', NULL, NULL),
(28, 22, 1, 'Normal Room', 3, 2, '2026-01-28', 7502.63, 'confirmed', NULL, NULL),
(29, 22, 1, 'Suite Room', 1, 2, '2026-01-29', 7800.00, 'confirmed', NULL, NULL),
(30, 3, 1, 'Normal Room', 2, 2, '2026-01-29', 5557.50, 'cancelled', NULL, NULL),
(31, 23, 1, 'Normal Room', 1, 2, '2026-01-29', 2340.00, 'pending', NULL, NULL),
(32, 24, 1, 'Normal Room', 7, 2, '2026-01-29', 14088.75, 'cancelled', NULL, NULL),
(33, 25, 1, 'Luxury Room', 1, 2, '2026-01-30', 8580.00, 'confirmed', NULL, NULL),
(34, 26, 1, 'Premium Room', 2, 2, '2026-01-31', 16672.50, 'confirmed', NULL, NULL),
(35, 28, 1, 'Single Room', 2, 1, '2026-02-03', 3120.00, 'confirmed', NULL, NULL),
(36, 29, 1, 'Single Room', 2, 1, '2026-02-03', 2470.00, 'confirmed', NULL, NULL),
(37, 30, 1, 'Single Room', 2, 1, '2026-02-03', 2340.00, 'confirmed', '2026-02-25', '2026-02-27'),
(38, 31, 3, 'Deluxe Room', 2, 4, '2026-02-05', 7800.00, 'cancelled', '2026-02-11', '2026-02-13'),
(39, 31, 3, 'Deluxe Room', 2, 4, '2026-02-05', 8550.00, 'cancelled', '2026-02-12', '2026-02-14'),
(40, 24, 3, 'Deluxe Room', 1, 2, '2026-02-06', 4650.00, 'cancelled', '2026-02-06', '2026-02-07');

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
  `available` tinyint(1) DEFAULT 1,
  `room_number` varchar(20) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `capacity` int(11) DEFAULT 2,
  `facilities` text DEFAULT NULL,
  `status` enum('available','booked','maintenance') DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `room_type`, `price`, `image_url`, `description`, `available`, `room_number`, `type`, `capacity`, `facilities`, `status`) VALUES
(1, 'Single Room', 1000.00, 'uploads/rooms/room_697b545a1edda_1769690202.jpg', 'SIngle room', 1, '205', NULL, 1, NULL, 'available'),
(2, 'Single Room', 1200.00, 'uploads/rooms/room_697cd421272cc_1769788449.jpg', 'Single Room', 1, '202', NULL, 1, NULL, 'available'),
(3, 'Deluxe Room', 3000.00, 'uploads/rooms/room_6981cfc106695_1770115009.jpeg', '', 1, '300', NULL, 4, NULL, 'available'),
(4, 'Family Room', 4000.00, 'uploads/rooms/room_6985a43939a1e_1770366009.jpg', 'Family', 1, '201', NULL, 6, NULL, 'available'),
(5, 'Luxury Room', 2000.00, 'uploads/rooms/room_6985a5705add5_1770366320.jpg', 'Luxury room', 1, '203', NULL, 4, NULL, 'available'),
(6, 'Premium Room', 2500.00, 'uploads/rooms/room_6985a6409f0c7_1770366528.jpg', 'room', 1, '204', NULL, 5, NULL, 'available');

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
(3, 'Nishika Maharjan', 'nishika@gmail.com', '9841083133', '2003-12-24', 'Female', '$2y$10$hVGvZTx0bxM5qzAY4AoKdeH0dEBpOJkKaYwZEFS0P8cCnOoDnf8Xq', 'Admin', '2024-12-03 03:55:16'),
(8, 'Bidur sapkota', 'bidur@gmail.com', '9865711881', '2024-12-12', 'Male', '$2y$10$tOhUukNjfZ4k76neU5z1duPC7wQJjdXPNS5AUXt14wz4SWXgzGUz2', 'User', '2024-12-03 06:01:19'),
(9, 'Shovan Dahal', 'shovan@gmail.com', '9861035712', '2005-12-22', 'Male', '$2y$10$sAKxEgfzssdKrDlSaaa3JOm.vLleJPzBLO5tRMG5j/CnvVjPacvgC', 'User', '2024-12-03 11:02:38'),
(11, 'Pukar Balami', 'pukar@gmail.com', '9852360145', '2004-12-23', 'Other', '$2y$10$xaU1Hnbd5MEdw99elCUfyuqJwosUszvAaILJDfK0RJ2pSOuI1grJK', 'User', '2024-12-04 03:38:31'),
(13, 'Ram Maharjan', 'ram1@gmail.com', '98568899999', '2025-01-25', 'Male', '$2y$10$iNjYIENjJh9bNSEZsBoAU.6l6IwAUkrRsa7I2dOttfB2HXXVGyIuu', 'User', '2024-12-04 07:29:14'),
(14, 'Hira Maharjan', 'Hiramaharjan@123', '9841083210', '2014-03-20', 'Female', '$2y$10$XEOcosw0H4heFN0Q4eOcYuAUx07YuIEW5i9Ab95Di7f3hbXM30Wzm', 'User', '2025-08-20 10:43:46'),
(15, 'Rina Maharjan', 'rina123@gmail.com', '9845280162', '2004-07-01', 'Female', '$2y$10$xP83EBYbzTrRgGYG.OMcv.7N9qWSpqEspfkW6Raas6q1gWNw2ut7e', 'User', '2026-01-07 14:22:07'),
(16, 'Reshma Maharjan', 'reshma124@gmail.com', '9835562727', '2015-07-24', 'Female', '$2y$10$mtdDeXsV9J0.wG04xcEED.r4hJJqgleZP7b2EZFb7gzS3AkL3NAe6', 'User', '2026-01-08 02:16:35'),
(17, 'sameer', 'samewr@gmail.com', '9845280162', '2007-01-30', 'Male', '$2y$10$CReONtU/E2hh28Q8CmM.nuK3e9QxlKD8su00I74RRB8idT9AkgJQ.', 'User', '2026-01-08 02:29:20'),
(18, 'Hisi Maharjan', 'hisi@gmail.com', '9848503066', '2003-01-30', 'Female', '$2y$10$PZDHHX7sv5uRoytb3EbywuWkUg8Nzq4PFetm1B0XWRHH/k1lbRkES', 'User', '2026-01-08 02:31:54'),
(19, 'Gelek Tamang', 'gelek@gmail.com', '9812345678', '2003-01-23', 'Male', '$2y$10$8IExACXIWSbj1PwZzygaEe0K15VwdlPe2bazwi9RklOh2O8ZbMCRa', 'User', '2026-01-08 02:36:00'),
(20, 'Rohini Maharjan', 'rohini@gmail.com', '9812345678', '2003-02-03', 'Female', '$2y$10$JGbXimK3Tzd7UBnFH/hEUOQJWrxVf.r4uGe6Wd4EY8CV3Wl9jCFSu', 'User', '2026-01-08 02:38:30'),
(21, 'Niku Maharjan', 'nikumaharjan@22gmail.com', '9854298771', '2012-06-14', 'Female', '$2y$10$OPjlK4G3PBtVAHOXjq4XnORXZg2Q/JulY.6gbMfG8Ikfe1U2bdFTy', 'User', '2026-01-10 16:59:18'),
(22, 'Reema Maharjan', 'reema@123gmail.com', '9845233223', '2005-03-16', 'Female', '$2y$10$goCPYwSFtlns1zTUsbxH6.wes1NlDIt7fWGDaKQjpOYowApYyer8a', 'User', '2026-01-12 05:21:27'),
(23, 'Hina Khan', 'hina@123gmail.com', '9853452770', '2007-07-11', 'Female', '$2y$10$eq9EZdoGSJ0HC.N8K4EPHeUaLg7wnOZ2BINexhWp3fnCFCFX.rcvq', 'User', '2026-01-29 08:37:38'),
(24, 'Shreeya Shrestha', 'shreeya123@gmail.com', '9845280162', '2014-02-27', 'Female', '$2y$10$7hcpd71Jeq7dchAHO4blsex3sLAft43LguBedY6ztMi0ENPS3u8/K', 'User', '2026-01-29 10:22:28'),
(25, 'Nuzen Shrestha', 'nuzen1234@gmail.com', '9812345678', '2020-02-10', 'Male', '$2y$10$/w8oOMyCqG1y4xPEyxryVOfmjN/rjqlV8QNXsGL9lAmYaTaApZ.Dq', 'User', '2026-01-30 12:33:16'),
(26, 'Gita Maharjan', 'gitamaharjan123@gmail.com', '9835576462', '2020-08-01', 'Female', '$2y$10$LkSF0tI4fN9PMSaM0oHbxunqLnQWndN9BUNYOsnjQ8IArYnoTnu9u', 'User', '2026-01-31 08:42:00'),
(27, 'Asmita Maharjan', 'asmitamaharjan123@gmail.com', '9863566235', '2017-06-09', 'Female', '$2y$10$Xfgv/dS4h6k/PEVzlsNVR.8QVFrDjIu97VhNln6f9NQXDmW2tdjyO', 'User', '2026-01-31 09:10:09'),
(28, 'Rita Hamal', 'ritahamal1234@gmail.com', '9873467880', '2019-12-18', 'Female', '$2y$10$Xiy.w2zMC3mnhwA9GbKk.eaXjhu24agui.RE4v5CxV12oM7sXVyz6', 'User', '2026-02-03 10:24:11'),
(29, 'Sarita Maharjan', 'saritamaharjan12345@gmail.com', '9835707457', '2012-03-16', 'Female', '$2y$10$8kklLc3Eelr9ANXmEm4vMucA.ni8qejYmLOG/xgou3lNdwn16p.WC', 'User', '2026-02-03 11:05:35'),
(30, 'Sabina Shrestha', 'sabinashrestha123@gmail.com', '9845637737', '2022-06-10', 'Female', '$2y$10$aS5Gi7Fj./gh/xdNWqrs7.rbiN5H63mViCkbcPmG05.cuTc7E5XnC', 'User', '2026-02-03 11:17:57'),
(31, 'Sita Ram Khanal', 'sitaram123@gmail.com', '9065257909', '2016-10-14', 'Female', '$2y$10$tJph4IbquRmG6afV/SXdL.s5pX5BmhL2uOCNPup2CaqMXcKbWnbay', 'User', '2026-02-05 06:48:14'),
(32, 'Nishika Mrhz', 'nishikamrhz1234@gmail.com', '9835735890', '2019-11-14', 'Female', '$2y$10$bGOZvsjSZcdOQzkL6YCYgOiDM1YrlnRsP6QNCfX9EwAkX08tY/7Fq', 'User', '2026-02-05 14:38:52'),
(35, 'New Admin', 'newadmin@gmail.com', '9800000000', '1990-01-01', 'Other', '$2y$10$XLfMTeEHsk88KrDZlg9mnOGUDUZwj5WNlgPRQ6g4Mnrs8H6h30nc.', 'Admin', '2026-02-05 14:44:14'),
(36, 'Nishika Maharjan', 'nishikamaharjan@gmail.com', '9800000000', '1999-01-01', 'Female', '$2y$10$$2y$10$76Tc3ezWJXPK1mIVSwBEvOoRrXAPUI8ZPJT1u9xKtI3H9MlKFbQWC', 'Admin', '2026-02-05 16:47:18'),
(37, 'Nirjala Maharjan', 'nirjalamaharjan929@gmail.com', '9873565788', '2024-07-12', 'Female', '$2y$10$PIS2iJ5tARL0OZvjSG9CEeOMEMPfnVbol6Nw3OK1mokwy.JhMRmpK', 'User', '2026-02-05 17:02:38'),
(38, 'Suraj Maharjan', 'surajmaharjan123@gmail.com', '9825748776', '2021-03-05', 'Male', '$2y$10$Aus8gfevpCBN6eW8phuA/.OgDYJTbrH1sWiYtgCDWW9.ntom.wE7O', 'User', '2026-02-06 09:36:23');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`);

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
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
