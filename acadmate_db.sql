-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 03, 2026 at 05:43 PM
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
-- Database: `acadmate_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

CREATE TABLE `assignments` (
  `id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `due_date` date DEFAULT NULL,
  `priority` enum('High','Medium','Low') DEFAULT 'Medium',
  `is_done` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `description` text DEFAULT NULL,
  `file_path` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `assignments`
--

INSERT INTO `assignments` (`id`, `subject_id`, `user_id`, `title`, `due_date`, `priority`, `is_done`, `created_at`, `description`, `file_path`) VALUES
(1, 1, 1, 'unit1', '2026-02-25', 'High', 0, '2026-02-24 22:37:19', NULL, NULL),
(2, 2, 2, 'maths', '2026-02-27', 'High', 0, '2026-02-26 16:29:14', NULL, NULL),
(3, 9, 1, 'complete unit 1', '2026-03-03', 'High', 0, '2026-03-02 21:12:58', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `class_slot_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `status` enum('present','absent') DEFAULT 'absent',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `subject_id`, `user_id`, `class_slot_id`, `date`, `status`, `created_at`) VALUES
(1, 3, 2, 6, '2026-02-26', 'absent', '2026-02-26 16:30:30'),
(2, 2, 2, 4, '2026-02-26', 'absent', '2026-02-26 16:30:36'),
(14, 7, 2, 13, '2026-03-02', 'present', '2026-03-02 16:01:54'),
(15, 7, 2, 14, '2026-03-02', 'present', '2026-03-02 16:01:59'),
(16, 2, 2, 2, '2026-03-02', 'present', '2026-03-02 16:02:00'),
(18, 4, 2, 11, '2026-03-02', 'present', '2026-03-02 16:02:41'),
(19, 7, 2, 12, '2026-03-02', 'present', '2026-03-02 16:02:43'),
(23, 3, 2, 5, '2026-03-02', 'absent', '2026-03-02 18:14:57'),
(24, 9, 1, 25, '2026-03-02', 'present', '2026-03-02 21:22:20'),
(27, 9, 1, 26, '2026-03-02', 'present', '2026-03-02 21:31:00'),
(30, 1, 1, 1, '2026-03-02', 'present', '2026-03-02 23:16:34'),
(31, 10, 1, 32, '2026-03-02', 'present', '2026-03-02 23:24:26'),
(32, 10, 1, 33, '2026-03-02', 'present', '2026-03-02 23:24:27'),
(33, 10, 1, 31, '2026-03-02', 'present', '2026-03-02 23:24:31'),
(34, 11, 1, 49, '2026-03-03', 'present', '2026-03-03 00:13:09'),
(35, 11, 1, 50, '2026-03-03', 'present', '2026-03-03 00:13:16'),
(36, 9, 1, 27, '2026-03-03', 'present', '2026-03-03 21:21:12'),
(37, 9, 1, 28, '2026-03-03', 'present', '2026-03-03 21:28:51'),
(38, 13, 1, 66, '2026-03-03', 'present', '2026-03-03 21:32:55'),
(39, 13, 1, 67, '2026-03-03', 'present', '2026-03-03 21:39:42'),
(40, 14, 1, 70, '2026-03-03', 'present', '2026-03-03 21:58:06'),
(41, 15, 1, 71, '2026-03-03', 'present', '2026-03-03 22:12:01');

-- --------------------------------------------------------

--
-- Table structure for table `class_slots`
--

CREATE TABLE `class_slots` (
  `id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `day` varchar(20) NOT NULL,
  `mode` enum('Theory','Practical') DEFAULT 'Theory',
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `room` varchar(100) DEFAULT 'TBD'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `class_slots`
--

INSERT INTO `class_slots` (`id`, `subject_id`, `user_id`, `day`, `mode`, `start_time`, `end_time`, `room`) VALUES
(1, 1, 1, 'Monday', 'Theory', '22:45:00', '22:50:00', 'sec123'),
(2, 2, 2, 'Monday', 'Theory', '16:27:00', '17:27:00', '567'),
(3, 2, 2, 'Wednesday', 'Theory', '16:27:00', '17:27:00', '567'),
(4, 2, 2, 'Thursday', 'Theory', '16:27:00', '17:27:00', '567'),
(5, 3, 2, 'Monday', 'Theory', '16:29:00', '16:31:00', '506'),
(6, 3, 2, 'Thursday', 'Theory', '16:29:00', '16:31:00', '506'),
(7, 3, 2, 'Friday', 'Theory', '16:29:00', '16:31:00', '506'),
(8, 3, 2, 'Tuesday', 'Theory', '16:29:00', '16:31:00', '506'),
(9, 3, 2, 'Saturday', 'Theory', '16:29:00', '16:31:00', '506'),
(10, 3, 2, 'Wednesday', 'Theory', '16:29:00', '16:31:00', '506'),
(11, 4, 2, 'Monday', 'Theory', '22:25:00', '22:31:00', 'sail123'),
(12, 7, 2, 'Monday', 'Theory', '23:30:00', '00:31:00', 'TBD'),
(13, 7, 2, 'Monday', 'Theory', '01:31:00', '02:31:00', 'TBD'),
(14, 7, 2, 'Monday', 'Theory', '02:31:00', '03:31:00', 'TBD'),
(15, 7, 2, 'Tuesday', 'Theory', '23:30:00', '00:31:00', 'TBD'),
(16, 7, 2, 'Tuesday', 'Theory', '01:31:00', '02:31:00', 'TBD'),
(17, 7, 2, 'Tuesday', 'Theory', '02:31:00', '03:31:00', 'TBD'),
(18, 7, 2, 'Wednesday', 'Theory', '23:30:00', '00:31:00', 'TBD'),
(19, 7, 2, 'Wednesday', 'Theory', '01:31:00', '02:31:00', 'TBD'),
(20, 7, 2, 'Wednesday', 'Theory', '02:31:00', '03:31:00', 'TBD'),
(21, 8, 2, 'Monday', 'Theory', '17:08:00', '18:22:00', 'TBD'),
(22, 8, 2, 'Monday', 'Theory', '18:30:00', '19:08:00', 'TBD'),
(23, 8, 2, 'Wednesday', 'Theory', '17:08:00', '18:22:00', 'TBD'),
(24, 8, 2, 'Wednesday', 'Theory', '18:30:00', '19:08:00', 'TBD'),
(25, 9, 1, 'Monday', 'Theory', '21:15:00', '21:20:00', 'TBD'),
(26, 9, 1, 'Monday', 'Theory', '21:21:00', '21:25:00', 'TBD'),
(27, 9, 1, 'Tuesday', 'Theory', '21:15:00', '21:20:00', 'TBD'),
(28, 9, 1, 'Tuesday', 'Theory', '21:21:00', '21:25:00', 'TBD'),
(29, 9, 1, 'Wednesday', 'Theory', '21:15:00', '21:20:00', 'TBD'),
(30, 9, 1, 'Wednesday', 'Theory', '21:21:00', '21:25:00', 'TBD'),
(31, 10, 1, 'Monday', 'Theory', '23:20:00', '23:22:00', 'TBD'),
(32, 10, 1, 'Monday', 'Theory', '23:25:00', '23:26:00', 'TBD'),
(33, 10, 1, 'Monday', 'Theory', '23:27:00', '23:29:00', 'TBD'),
(34, 10, 1, 'Tuesday', 'Theory', '23:20:00', '23:22:00', 'TBD'),
(35, 10, 1, 'Tuesday', 'Theory', '23:25:00', '23:26:00', 'TBD'),
(36, 10, 1, 'Tuesday', 'Theory', '23:27:00', '23:29:00', 'TBD'),
(37, 10, 1, 'Wednesday', 'Theory', '23:20:00', '23:22:00', 'TBD'),
(38, 10, 1, 'Wednesday', 'Theory', '23:25:00', '23:26:00', 'TBD'),
(39, 10, 1, 'Wednesday', 'Theory', '23:27:00', '23:29:00', 'TBD'),
(40, 10, 1, 'Thursday', 'Theory', '23:20:00', '23:22:00', 'TBD'),
(41, 10, 1, 'Thursday', 'Theory', '23:25:00', '23:26:00', 'TBD'),
(42, 10, 1, 'Thursday', 'Theory', '23:27:00', '23:29:00', 'TBD'),
(43, 10, 1, 'Friday', 'Theory', '23:20:00', '23:22:00', 'TBD'),
(44, 10, 1, 'Friday', 'Theory', '23:25:00', '23:26:00', 'TBD'),
(45, 10, 1, 'Friday', 'Theory', '23:27:00', '23:29:00', 'TBD'),
(46, 10, 1, 'Saturday', 'Theory', '23:20:00', '23:22:00', 'TBD'),
(47, 10, 1, 'Saturday', 'Theory', '23:25:00', '23:26:00', 'TBD'),
(48, 10, 1, 'Saturday', 'Theory', '23:27:00', '23:29:00', 'TBD'),
(49, 11, 1, 'Monday', 'Theory', '00:12:00', '00:13:00', 'TBD'),
(50, 11, 1, 'Monday', 'Theory', '00:14:00', '00:15:00', 'TBD'),
(51, 11, 1, 'Thursday', 'Theory', '00:12:00', '00:13:00', 'TBD'),
(52, 11, 1, 'Thursday', 'Theory', '00:14:00', '00:15:00', 'TBD'),
(53, 11, 1, 'Friday', 'Theory', '00:12:00', '00:13:00', 'TBD'),
(54, 11, 1, 'Friday', 'Theory', '00:14:00', '00:15:00', 'TBD'),
(55, 11, 1, 'Saturday', 'Theory', '00:12:00', '00:13:00', 'TBD'),
(56, 11, 1, 'Saturday', 'Theory', '00:14:00', '00:15:00', 'TBD'),
(57, 12, 1, 'Monday', 'Theory', '21:16:00', '21:17:00', 'TBD'),
(58, 12, 1, 'Monday', 'Theory', '21:18:00', '21:20:00', 'TBD'),
(59, 12, 1, 'Monday', 'Theory', '21:25:00', '21:30:00', 'TBD'),
(60, 12, 1, 'Wednesday', 'Theory', '21:16:00', '21:17:00', 'TBD'),
(61, 12, 1, 'Wednesday', 'Theory', '21:18:00', '21:20:00', 'TBD'),
(62, 12, 1, 'Wednesday', 'Theory', '21:25:00', '21:30:00', 'TBD'),
(63, 12, 1, 'Friday', 'Theory', '21:16:00', '21:17:00', 'TBD'),
(64, 12, 1, 'Friday', 'Theory', '21:18:00', '21:20:00', 'TBD'),
(65, 12, 1, 'Friday', 'Theory', '21:25:00', '21:30:00', 'TBD'),
(66, 13, 1, 'Tuesday', 'Theory', '21:32:00', '21:33:00', 'TBD'),
(67, 13, 1, 'Tuesday', 'Theory', '21:34:00', '21:35:00', 'TBD'),
(68, 13, 1, 'Wednesday', 'Theory', '21:32:00', '21:33:00', 'TBD'),
(69, 13, 1, 'Wednesday', 'Theory', '21:34:00', '21:35:00', 'TBD'),
(70, 14, 1, 'Tuesday', 'Theory', '21:57:00', '21:58:00', 'TBD'),
(71, 15, 1, 'Tuesday', 'Theory', '22:11:00', '22:12:00', 'TBD');

-- --------------------------------------------------------

--
-- Table structure for table `exams`
--

CREATE TABLE `exams` (
  `id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('Mid','Final','Quiz') DEFAULT 'Mid',
  `exam_date` date DEFAULT NULL,
  `exam_time` time DEFAULT NULL,
  `location` varchar(200) DEFAULT '',
  `syllabus` text DEFAULT '',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exams`
--

INSERT INTO `exams` (`id`, `subject_id`, `user_id`, `type`, `exam_date`, `exam_time`, `location`, `syllabus`, `created_at`) VALUES
(1, 1, 1, 'Mid', '2026-02-25', '22:38:00', 'sec23', 'all units', '2026-02-24 22:38:14'),
(2, 9, 1, 'Final', '2026-03-03', '21:13:00', 'cbe22', 'all units', '2026-03-02 21:14:01');

-- --------------------------------------------------------

--
-- Table structure for table `notes`
--

CREATE TABLE `notes` (
  `id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `tag` enum('Unit','Revision','Formula') DEFAULT 'Unit',
  `content` text DEFAULT '',
  `file_path` varchar(500) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notes`
--

INSERT INTO `notes` (`id`, `subject_id`, `user_id`, `title`, `tag`, `content`, `file_path`, `created_at`) VALUES
(1, 1, 1, 'get unit5', 'Unit', 'complete it', 'uploads/notes/1/1771952937_upload_812910978011005582_IMG-20260224-WA0009.jpg', '2026-02-24 22:38:57'),
(2, 9, 1, 'practical notes', 'Unit', 'revise it while exams', 'uploads/notes/1/1772466280_upload_3969063211204186085_02.03.2026_AHS_Night_intern_duty_Roster.pdf', '2026-03-02 21:14:40');

-- --------------------------------------------------------

--
-- Table structure for table `practicals`
--

CREATE TABLE `practicals` (
  `id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `lab_number` varchar(50) DEFAULT '',
  `description` text DEFAULT '',
  `submission_date` date DEFAULT NULL,
  `is_done` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `file_path` varchar(500) DEFAULT NULL,
  `reference_link` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `practicals`
--

INSERT INTO `practicals` (`id`, `subject_id`, `user_id`, `title`, `lab_number`, `description`, `submission_date`, `is_done`, `created_at`, `file_path`, `reference_link`) VALUES
(1, 1, 1, 'practical', '123', 'go and keep', '2026-02-25', 0, '2026-02-24 22:37:45', NULL, NULL),
(2, 9, 1, 'complete lab 3', '12', 'complete all required notes', '2026-03-06', 0, '2026-03-02 21:13:35', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `semesters`
--

CREATE TABLE `semesters` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `semesters`
--

INSERT INTO `semesters` (`id`, `user_id`, `name`, `start_date`, `end_date`, `created_at`) VALUES
(1, 1, 'semster1', '2026-02-24', '2026-03-31', '2026-02-24 22:35:13'),
(2, 2, '6', '2026-02-26', '2026-04-30', '2026-02-26 16:27:01'),
(3, 2, '4', '2026-03-02', '2026-05-07', '2026-03-02 17:07:23'),
(4, 1, 'semster2', '2026-03-02', '2026-03-31', '2026-03-02 23:22:05');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `semester_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `code` varchar(50) DEFAULT '',
  `credits` int(11) DEFAULT 3,
  `type` enum('Theory','Practical','Mixed') DEFAULT 'Theory',
  `classes_per_week` int(11) DEFAULT 3,
  `classes_per_day` int(11) NOT NULL DEFAULT 1,
  `min_attendance_pct` int(11) DEFAULT 75,
  `total_classes` int(11) DEFAULT 72,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `semester_id`, `user_id`, `name`, `code`, `credits`, `type`, `classes_per_week`, `classes_per_day`, `min_attendance_pct`, `total_classes`, `created_at`) VALUES
(1, 1, 1, 'Maths', 'UBA123', 4, 'Theory', 4, 1, 80, 96, '2026-02-24 22:36:02'),
(2, 2, 2, 'Maths', '22CS56', 3, 'Practical', 3, 1, 75, 72, '2026-02-26 16:27:16'),
(3, 2, 2, 'Physics', '22424', 3, 'Practical', 3, 1, 75, 72, '2026-02-26 16:29:45'),
(4, 2, 2, 'sanskrit', 'san2334', 3, 'Mixed', 3, 1, 75, 72, '2026-03-01 22:19:29'),
(5, 2, 2, 'suresh', 'ddd', 3, 'Mixed', 3, 1, 75, 72, '2026-03-01 22:36:12'),
(6, 2, 2, 'Web dev', 'cse123', 3, 'Mixed', 3, 3, 75, 216, '2026-03-01 23:27:52'),
(7, 2, 2, 'Web Development', 'cse233', 3, 'Mixed', 3, 3, 75, 216, '2026-03-01 23:29:03'),
(8, 3, 2, 'Marvel', 'CS245', 3, 'Mixed', 3, 2, 75, 144, '2026-03-02 17:08:17'),
(9, 1, 1, 'Web', 'CSE123', 3, 'Mixed', 3, 1, 75, 144, '2026-03-02 21:08:12'),
(10, 1, 1, 'Check', 'XSE12', 3, 'Theory', 6, 1, 75, 432, '2026-03-02 23:18:37'),
(11, 1, 1, 'Check 2', 'CSW24', 4, 'Mixed', 4, 1, 75, 192, '2026-03-03 00:09:08'),
(12, 1, 1, 'Check3', 'CSE3', 3, 'Mixed', 3, 1, 75, 216, '2026-03-03 21:13:52'),
(13, 1, 1, 'Che4', 'GWG', 3, 'Theory', 3, 1, 75, 144, '2026-03-03 21:29:55'),
(14, 1, 1, 'C5', 'TT', 3, 'Theory', 3, 1, 75, 72, '2026-03-03 21:57:25'),
(15, 1, 1, 'C6', 'FG', 3, 'Theory', 3, 1, 75, 72, '2026-03-03 22:10:29');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `email` varchar(200) NOT NULL,
  `course` varchar(200) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `auth_token` varchar(64) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `course`, `password_hash`, `auth_token`, `created_at`) VALUES
(1, 'Mohanuu', 'mohan@gmail.com', 'Cse', '$2y$10$Xij3KyeqFnfB9VAc3pOZjO1FQ16zDWqxUmiPh5Rg/mqBm/VHEoXka', '1126c174c7640cdf458a9ee14ea063cfe9336467cea1a49be701da1678e4bc0c', '2026-02-24 22:34:14'),
(2, 'Bhanuteja', 'bhanutej512@gmail.com', 'Cse', '$2y$10$raN69z6m9v.a1gmb6FLWo.a0tRgvro/Fdm8aZQ5dONq3VtrWVrFMa', '0d3a9ef16bd5d442debf968a8969fb1df81bca3e0da0d3b5cdd0c946c792e9cd', '2026-02-26 16:26:28');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_attendance` (`subject_id`,`class_slot_id`,`date`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `class_slot_id` (`class_slot_id`);

--
-- Indexes for table `class_slots`
--
ALTER TABLE `class_slots`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_day` (`day`);

--
-- Indexes for table `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `notes`
--
ALTER TABLE `notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `practicals`
--
ALTER TABLE `practicals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `semesters`
--
ALTER TABLE `semesters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `semester_id` (`semester_id`),
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
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `class_slots`
--
ALTER TABLE `class_slots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `exams`
--
ALTER TABLE `exams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `notes`
--
ALTER TABLE `notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `practicals`
--
ALTER TABLE `practicals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `semesters`
--
ALTER TABLE `semesters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assignments`
--
ALTER TABLE `assignments`
  ADD CONSTRAINT `assignments_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assignments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendance_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendance_ibfk_3` FOREIGN KEY (`class_slot_id`) REFERENCES `class_slots` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `class_slots`
--
ALTER TABLE `class_slots`
  ADD CONSTRAINT `class_slots_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `class_slots_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exams`
--
ALTER TABLE `exams`
  ADD CONSTRAINT `exams_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exams_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notes`
--
ALTER TABLE `notes`
  ADD CONSTRAINT `notes_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `practicals`
--
ALTER TABLE `practicals`
  ADD CONSTRAINT `practicals_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `practicals_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `semesters`
--
ALTER TABLE `semesters`
  ADD CONSTRAINT `semesters_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subjects`
--
ALTER TABLE `subjects`
  ADD CONSTRAINT `subjects_ibfk_1` FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subjects_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
