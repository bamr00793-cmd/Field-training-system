-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 18 يونيو 2026 الساعة 23:30
-- إصدار الخادم: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `field_training_system`
--

-- --------------------------------------------------------

--
-- بنية الجدول `attachments`
--

CREATE TABLE `attachments` (
  `id` int(11) NOT NULL,
  `report_id` int(11) NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `attachments`
--

INSERT INTO `attachments` (`id`, `report_id`, `file_name`, `file_path`) VALUES
(1, 6, '1781262613_سكرين توثيق.png', NULL),
(2, 7, '1781807568_٢٠٢٥٠٧٢٦_١٣٠٢٤٤.jpg', NULL),
(3, 8, '1781810886_لقطة الشاشة 2026-06-18 222743.png', NULL);

-- --------------------------------------------------------

--
-- بنية الجدول `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `attendance_date` date DEFAULT NULL,
  `status` enum('Present','Absent') DEFAULT 'Present'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `daily_reports`
--

CREATE TABLE `daily_reports` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `report_date` date DEFAULT NULL,
  `tasks` text DEFAULT NULL,
  `skills` text DEFAULT NULL,
  `difficulties` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `daily_reports`
--

INSERT INTO `daily_reports` (`id`, `student_id`, `report_date`, `tasks`, `skills`, `difficulties`) VALUES
(1, 14, '2026-06-23', 'توصيل اقسام مختلفة ', 'تقسيم الشبكة ', 'لا يوجد \r\n'),
(2, 11, '2026-06-28', 'ضبط الip ', 'تنظيم الشبكات', 'لا يوجد'),
(3, 11, '2026-06-30', 'اعداد  printer', 'network  sharing', 'null'),
(4, 11, '2026-06-18', 'لابيل', 'يشسش', 'يشس'),
(5, 11, '2026-07-31', 'فحص ', 'المهمة ', 'لا يوجد\r\n'),
(6, 11, '2026-06-30', 'تعديل ip', 'كتابة على cmd', 'nothing'),
(7, 11, '2026-06-18', 'editing the ports', 'editing on cmd', 'noth'),
(8, 10, '2026-06-20', 'تنزيل برنامج بديل للواي فاي hotspot', 'التعامل مع المشاكل بسرعة وبطرق اخرى ', 'لا يوجد ');

-- --------------------------------------------------------

--
-- بنية الجدول `evaluations`
--

CREATE TABLE `evaluations` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `strengths` text DEFAULT NULL,
  `weaknesses` text DEFAULT NULL,
  `recommendation` enum('Excellent','Good','Average','Weak') DEFAULT NULL,
  `final_grade` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `evaluations`
--

INSERT INTO `evaluations` (`id`, `student_id`, `strengths`, `weaknesses`, `recommendation`, `final_grade`) VALUES
(1, 10, 'سريع الحفظ', 'حل المسائل المنطثية', '', 94.00);

-- --------------------------------------------------------

--
-- بنية الجدول `organizations`
--

CREATE TABLE `organizations` (
  `id` int(11) NOT NULL,
  `organization_name` varchar(255) NOT NULL,
  `department` varchar(255) DEFAULT NULL,
  `supervisor_name` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `address` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `organizations`
--

INSERT INTO `organizations` (`id`, `organization_name`, `department`, `supervisor_name`, `phone`, `email`, `address`) VALUES
(1, 'ahli arabi', 'cui', 'ibrahim otman', '0592772123', 'ibrahimotman@test.com', 'gaza'),
(2, 'carrer gates', 'eng', 'ziyad zagout', '0566100218', 'zozz@gmail.com', 'gaza_remal'),
(3, 'carrer gates', 'eng', 'ziyad zagout', '0566100218', 'zozz@gmail.com', 'gaza_remal'),
(4, 'saqa', NULL, NULL, '0567755331', 'saqa@test.com', 'غزة الرمال');

-- --------------------------------------------------------

--
-- بنية الجدول `training_requests`
--

CREATE TABLE `training_requests` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `organization_id` int(11) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `pledge` tinyint(4) DEFAULT 0,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `training_requests`
--

INSERT INTO `training_requests` (`id`, `student_id`, `organization_id`, `start_date`, `end_date`, `pledge`, `status`, `created_at`) VALUES
(7, 10, 1, '2026-06-03', '2026-08-11', 0, 'Approved', '2026-06-11 19:33:38'),
(8, 14, 1, '2026-06-17', '2026-08-21', 0, 'Approved', '2026-06-11 20:22:07'),
(9, 14, 1, '2026-06-17', '2026-08-21', 0, 'Approved', '2026-06-11 20:22:15'),
(10, 14, 1, '2026-06-17', '2026-08-04', 0, 'Approved', '2026-06-11 20:22:37'),
(11, 15, 1, '2026-06-23', '2026-09-25', 0, 'Pending', '2026-06-12 10:49:37');

-- --------------------------------------------------------

--
-- بنية الجدول `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `role`, `created_at`) VALUES
(3, 'dr.ramzi', 'ram656@gmail.com', 'Rr1020304050@', '0592166616', 2, '2026-06-08 08:18:41'),
(5, 'عبود عامر', 'bamr00793@gmail.com', '$2y$10$UgBpkFR0stSZ8/x7BG5c.eEElTS8lCdefGIpwv/.zUx19ivx0XT4i', '0567051701', 1, '2026-06-11 13:37:03'),
(6, 'Test User', 'test123@test.com', '$2y$10$R.art3TtfXjy7jmbRMCASu9l3nb4BIooAG2J0a2XOF8XNmAKQlPK6', '0599999999', 0, '2026-06-11 13:38:22'),
(10, 'ibrahim', 'ibrahim@test.com', '$2y$10$JFi1Ewk6W6Jgm3td5hJWNOj9/Nqlb//PSwqR.Py5wYcsDjt6L9pOy', '0599999999', 0, '2026-06-11 15:33:09'),
(11, 'ibrahim otman', 'ibrahimothman@test.com', '$2y$10$7/UcKuhtTr7d01rgl7D/NuF0xZmgo4GMvBMXknJbAa/BfHcQNh0Jy', '056666666', 1, '2026-06-11 19:08:45'),
(14, 'عبود عامر', 'abod@gmail.com', '$2y$10$Fik95Phn/yMhDCrT0DIlE.oDBLhAtuDfCpsMGi3yIfJiyrk4QulOi', '1320225521', 0, '2026-06-11 20:09:48'),
(15, 'saleh', 'saleh@gmail.com', '$2y$10$jMz9np9YAIMBZxNepjth.OUCiSxRCec9HA4rkr5XaaN5spS3dwpva', '1320225519', 0, '2026-06-12 10:48:35'),
(17, 'ahli arabi', 'norg@gmail.com', '$2y$10$BlLFdtLeWKSLqyx39LkLSe.tY/RgjexB8rro.c8VBNL069hv3QBOe', '0561234567', 2, '2026-06-16 21:40:42'),
(18, 'عماد الصوالحي', 'emad@test.com', '$2y$10$E9oU/E4ZrrvJ6qqmAYcqDe7ai/YZfGdLB6KZ7vViJ.ucHiOpKW9WK', '0592222777', 3, '2026-06-18 20:41:44');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attachments`
--
ALTER TABLE `attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `report_id` (`report_id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `daily_reports`
--
ALTER TABLE `daily_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `evaluations`
--
ALTER TABLE `evaluations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `organizations`
--
ALTER TABLE `organizations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `training_requests`
--
ALTER TABLE `training_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `organization_id` (`organization_id`);

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
-- AUTO_INCREMENT for table `attachments`
--
ALTER TABLE `attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `daily_reports`
--
ALTER TABLE `daily_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `evaluations`
--
ALTER TABLE `evaluations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `organizations`
--
ALTER TABLE `organizations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `training_requests`
--
ALTER TABLE `training_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- قيود الجداول المُلقاة.
--

--
-- قيود الجداول `attachments`
--
ALTER TABLE `attachments`
  ADD CONSTRAINT `attachments_ibfk_1` FOREIGN KEY (`report_id`) REFERENCES `daily_reports` (`id`) ON DELETE CASCADE;

--
-- قيود الجداول `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- قيود الجداول `daily_reports`
--
ALTER TABLE `daily_reports`
  ADD CONSTRAINT `daily_reports_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- قيود الجداول `evaluations`
--
ALTER TABLE `evaluations`
  ADD CONSTRAINT `evaluations_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- قيود الجداول `training_requests`
--
ALTER TABLE `training_requests`
  ADD CONSTRAINT `training_requests_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `training_requests_ibfk_2` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
