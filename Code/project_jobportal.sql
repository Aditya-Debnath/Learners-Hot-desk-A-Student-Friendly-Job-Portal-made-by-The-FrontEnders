-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 20, 2025 at 01:09 PM
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
-- Database: `project_jobportal`
--

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `applicant_id` int(11) NOT NULL,
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','viewed','shortlisted') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`id`, `job_id`, `applicant_id`, `applied_at`, `status`) VALUES
(1, 1, 2, '2025-12-19 22:19:40', 'viewed');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Engineering'),
(2, 'Accountant'),
(3, 'Information Technology'),
(4, 'Fashion Designing'),
(5, 'Sales'),
(6, 'Marketing'),
(7, 'Teaching & Education'),
(8, 'Medical & Healthcare');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` int(11) NOT NULL,
  `provider_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `job_nature` enum('Full Time','Part Time','Remote','Freelance') NOT NULL,
  `vacancy` int(11) DEFAULT NULL,
  `salary` varchar(100) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `responsibility` text DEFAULT NULL,
  `qualifications` text DEFAULT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `benefits` text DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `company_location` varchar(255) DEFAULT NULL,
  `company_website` varchar(255) DEFAULT NULL,
  `is_approved` tinyint(4) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deadline` date DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `provider_id`, `title`, `category`, `job_nature`, `vacancy`, `salary`, `location`, `description`, `responsibility`, `qualifications`, `keywords`, `benefits`, `company_name`, `company_location`, `company_website`, `is_approved`, `created_at`, `deadline`, `is_active`) VALUES
(1, 3, 'Tuition', 'Tuition', 'Remote', 1, '5000', 'Basabo, Dhaka', '1 student for Bangla, English', NULL, NULL, NULL, NULL, 'non', NULL, NULL, 1, '2025-12-19 14:25:52', NULL, 1),
(2, 3, 'ASP.NET Software Developer (Intern) ', 'Software Development  ', 'Full Time', 5, '8000', 'Dhaka (DOHS Mirpur)', '<--Requirements-->\r\nEducation\r\n\r\n    Bachelor of Science (BSc)\r\n    Diploma in Engineering\r\n\r\n<--Additional Requirements-->\r\n\r\n    Age 22 to 30 years\r\n\r\n    Should have knowledge on .Net Framework (Both on Web Form and MVC) and .Net Core. Should have Knowledge on SQL.\r\n\r\n<--Responsibilities & Context-->\r\n\r\nSynergy Interface provides Customize Software Solution, Insurance ERP, Industrial ERP, Professional Website Design and Development, Web Application and other IT related services.\r\n\r\nSo we prefer candidates who is willing to work with software products. If you think you are accept challenging carrier, you are welcome.\r\n\r\n<--Compensation & Other Benefits-->\r\n\r\n    Mobile bill,Tour allowance,Performance bonus,Profit share,Weekly 2 holidays\r\n    Lunch Facilities: Full Subsidize\r\n    Salary Review: Yearly\r\n    Festival Bonus: 2\r\n\r\nWorkplace\r\n\r\nWork at office\r\n\r\nJob Location\r\n\r\nDhaka (DOHS Mirpur)', NULL, NULL, NULL, NULL, 'Synergy Interface Ltd.', NULL, NULL, 1, '2025-12-20 00:22:43', NULL, 1),
(3, 3, 'A', '1', 'Full Time', 10, '10K', 'Plot 16, Block B, Aftabuddin Ahmed Road, Bashundhara R/A, Dhaka, Bangladesh', 'A', 'B', 'C', 'Java', 'B', 'Independent University, Bangladesh (IUB)', 'Plot 16, Block B, Aftabuddin Ahmed Road, Bashundhara R/A, Dhaka, Banglades', 'https://iub.ac.bd', 1, '2025-12-20 11:03:27', '2025-12-25', 0);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(4) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `message`, `is_read`, `created_at`) VALUES
(1, 3, 2, 'Hello Aditya!', 1, '2025-12-19 22:52:25'),
(2, 2, 3, 'Hi!', 1, '2025-12-19 23:20:36');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `message` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT '#',
  `is_read` tinyint(4) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `message`, `link`, `is_read`, `created_at`) VALUES
(1, 3, 'Your job post has been approved!', 'job-detail.php?id=1', 1, '2025-12-19 14:32:33'),
(2, 3, 'New applicant for: Tuition', 'dashboard.php?view=my_jobs', 1, '2025-12-19 22:19:40'),
(3, 2, 'New message from Job_dibo', 'chat.php?user_id=3', 1, '2025-12-19 22:52:25'),
(4, 3, 'New message from Aditya Debnath', 'chat.php?user_id=2', 1, '2025-12-19 23:20:36'),
(5, 3, 'Your job \'ASP.NET Software Developer (Intern) \' was Approved.', 'job-detail.php?id=2', 1, '2025-12-20 00:32:13'),
(6, 3, 'Your job \'A\' was Approved.', 'job-detail.php?id=3', 0, '2025-12-20 11:06:10');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `reporter_id` int(11) DEFAULT NULL,
  `reported_id` int(11) DEFAULT NULL,
  `job_id` int(11) DEFAULT NULL,
  `reason` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`id`, `reporter_id`, `reported_id`, `job_id`, `reason`, `created_at`) VALUES
(1, 3, 2, NULL, 'No responsive', '2025-12-20 10:18:17');

-- --------------------------------------------------------

--
-- Table structure for table `saved_jobs`
--

CREATE TABLE `saved_jobs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `saved_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `saved_jobs`
--

INSERT INTO `saved_jobs` (`id`, `user_id`, `job_id`, `saved_at`) VALUES
(1, 2, 1, '2025-12-19 14:34:27');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','provider','applicant') DEFAULT 'applicant',
  `mobile` varchar(20) DEFAULT NULL,
  `is_verified` tinyint(4) DEFAULT 0,
  `bio` text DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT 'default.png',
  `cv_file` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `mobile`, `is_verified`, `bio`, `profile_pic`, `cv_file`, `created_at`) VALUES
(1, 'Super Admin', 'admin@admin.com', '$2y$10$QKDaE3Ij9aj2ffm14VnI0.UeRk2MXEtImRvbfu/yvDca9XsOohd9W', 'admin', NULL, 1, NULL, 'default.png', NULL, '2025-12-19 12:42:53'),
(2, 'Aditya Debnath', 'adityadebnathtirtha@gmail.com', '$2y$10$QKDaE3Ij9aj2ffm14VnI0.UeRk2MXEtImRvbfu/yvDca9XsOohd9W', 'applicant', '01932023752', 1, '', '1766153113_515c7a2bce395f653d000002.png', '1766182758_TanzilCV.pdf', '2025-12-19 13:12:29'),
(3, 'Job_dibo', 'provider@gmail.com', '$2y$10$QKDaE3Ij9aj2ffm14VnI0.UeRk2MXEtImRvbfu/yvDca9XsOohd9W', 'provider', '+8801883914663', 1, '', 'default.png', NULL, '2025-12-19 14:24:14'),
(5, 'umme', 'sepav77474@abaot.com', '$2y$10$AbFh9NAw8ScBp4UdNe8Kq.s7E2vg2gLx2JK.HRn9w6i43V7wDJJBW', 'applicant', NULL, 0, NULL, 'default.png', NULL, '2025-12-20 11:54:18');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_id` (`job_id`),
  ADD KEY `applicant_id` (`applicant_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `provider_id` (`provider_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reporter_id` (`reporter_id`);

--
-- Indexes for table `saved_jobs`
--
ALTER TABLE `saved_jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `job_id` (`job_id`);

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
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `saved_jobs`
--
ALTER TABLE `saved_jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `applications_ibfk_2` FOREIGN KEY (`applicant_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `jobs`
--
ALTER TABLE `jobs`
  ADD CONSTRAINT `jobs_ibfk_1` FOREIGN KEY (`provider_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`reporter_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `saved_jobs`
--
ALTER TABLE `saved_jobs`
  ADD CONSTRAINT `saved_jobs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `saved_jobs_ibfk_2` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
