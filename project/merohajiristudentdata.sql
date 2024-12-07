-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 07, 2024 at 12:27 PM
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
-- Database: `merohajiristudentdata`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `admin_id` varchar(50) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `first_name`, `last_name`, `admin_id`, `phone`, `email`, `password`) VALUES
(1, 'Sishir', 'Dangi', '211811', '9841185811', 'sishirdangi83@gmail.com', 'scrypt:32768:8:1$Tfam0LJ4gJnDhYXX$3ca175dd4208693bbce5fcf3a6ff04b145aa091851179e9829abd90bf96396c49f0346cc9ef65b820d42b834a3d855f6ac39ecf97b1a6787b1b5cf31bc459a1a');

-- --------------------------------------------------------

--
-- Table structure for table `studentattendance`
--

CREATE TABLE `studentattendance` (
  `id` int(11) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `course` varchar(50) NOT NULL,
  `batch` varchar(50) NOT NULL,
  `attendance_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `studentattendance`
--

INSERT INTO `studentattendance` (`id`, `student_id`, `full_name`, `course`, `batch`, `attendance_time`) VALUES
(1, '211811', 'Sishir Dangi', 'Bsc.CSIT', '2077', '2024-11-25 07:06:27'),
(3, '21', 'Sishant Dangi', 'BBA', '2078', '2024-11-25 07:19:12'),
(4, '211811', 'Sishir Dangi', 'Bsc.CSIT', '2077', '2024-11-25 09:43:28'),
(5, '21', 'Sishant Dangi', 'BBA', '2078', '2024-11-28 18:43:15'),
(6, '211811', 'Sishir Dangi', 'Bsc.CSIT', '2077', '2024-11-28 18:44:40'),
(7, '21', 'Sishant Dangi', 'BBA', '2078', '2024-11-28 19:10:27'),
(8, '21', 'Sishant Dangi', 'BBA', '2078', '2024-11-28 19:19:29'),
(9, '21', 'Sishant Dangi', 'BBA', '2078', '2024-11-28 19:31:05'),
(10, '21', 'Sishant Dangi', 'BBA', '2078', '2024-11-28 19:31:40'),
(11, '211811', 'Sishir Dangi', 'Bsc.CSIT', '2077', '2024-11-28 19:31:47'),
(12, '211811', 'Sishir Dangi', 'Bsc.CSIT', '2077', '2024-11-28 19:45:46'),
(13, '211811', 'Sishir Dangi', 'Bsc.CSIT', '2077', '2024-11-28 19:47:34'),
(14, '211811', 'Sishir Dangi', 'Bsc.CSIT', '2077', '2024-11-28 19:47:41'),
(15, '211811', 'Sishir Dangi', 'Bsc.CSIT', '2077', '2024-11-28 19:47:49'),
(16, '211811', 'Sishir Dangi', 'Bsc.CSIT', '2077', '2024-11-28 20:30:34'),
(17, '21', 'Sishant Dangi', 'BBA', '2078', '2024-11-29 09:45:19'),
(18, '21', 'Sishant Dangi', 'BBA', '2078', '2024-11-29 09:45:41'),
(19, '21', 'Sishant Dangi', 'BBA', '2078', '2024-11-29 09:46:35'),
(20, '211811', 'Sishir Dangi', 'Bsc.CSIT', '2077', '2024-11-29 13:43:50'),
(21, '211811', 'Sishir Dangi', 'Bsc.CSIT', '2077', '2024-12-05 19:07:45'),
(22, '21', 'Sishant Dangi', 'BBA', '2078', '2024-12-05 19:18:45'),
(32, '211811', 'Sishir Dangi', 'Bsc.CSIT', '2077', '2024-12-07 12:27:27'),
(33, '211811', 'Sishir Dangi', 'Bsc.CSIT', '2077', '2024-12-07 12:31:40'),
(34, '211811', 'Sishir Dangi', 'Bsc.CSIT', '2077', '2024-12-07 12:32:13'),
(35, '211811', 'Sishir Dangi', 'Bsc.CSIT', '2077', '2024-12-07 15:00:47');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `batch` varchar(50) NOT NULL,
  `course` varchar(50) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `first_name`, `last_name`, `batch`, `course`, `student_id`, `phone`, `email`, `password`) VALUES
(1, 'Sishir', 'Dangi', '2077', 'Bsc.CSIT', '211811', '9841185811', 'sishirdangi83@gmail.com', 'scrypt:32768:8:1$k7zGdTEMVf8nfuMF$099812a44066ab750576378cc5dee75ad2bacb6f1f4ca8f8cfd0f2b99e3478653fd0cb0f44b85ca88965f974004be2296d139cc63805088c6afd8eae7a387eb4'),
(2, 'Sishant', 'Dangi', '2078', 'BBA', '21', '9811987608', 'sishant@gmail.com', 'scrypt:32768:8:1$LSRtzj9DP29unEsi$60666f69c0e1728382a5208a5eff904ffdc6d4ab385c6d1eed7104d0a8adc4abd8590fca7e114105b783061ff6486eda1733531e963d9cfb87e23f3226c40279'),
(4, 'Sunil', 'Thapa', '2077', 'Bsc.CSIT', '211888', '987663242', 'sunil@gmail.com', 'scrypt:32768:8:1$fvzTE6tYxhMmpF3I$79d75e5d6367b49aea5b97e9ca41feadf7d7219361724f2023f248a34897ebba8f9288e2ec5fa7a6604cc07b6f68f104125efc4432f80d800ec5b84656441e32');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admin_id` (`admin_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `studentattendance`
--
ALTER TABLE `studentattendance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `studentattendance`
--
ALTER TABLE `studentattendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
