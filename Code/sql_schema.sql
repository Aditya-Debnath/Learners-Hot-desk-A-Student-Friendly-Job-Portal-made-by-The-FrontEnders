CREATE DATABASE IF NOT EXISTS learners-hot-desk;
USE learners-hot-desk;

-- Users Table (All roles in one table)

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

-- Password for all account is 'admin123'
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `mobile`, `is_verified`, `bio`, `profile_pic`, `cv_file`, `created_at`) VALUES
(1, 'Admin', 'admin@gmail.com', '$2y$10$QKDaE3Ij9aj2ffm14VnI0.UeRk2MXEtImRvbfu/yvDca9XsOohd9W', 'admin', NULL, 1, NULL, 'default.png', NULL, '2025-12-19 12:42:53'),
(2, 'Applicant', 'applicant@gmail.com', '$2y$10$QKDaE3Ij9aj2ffm14VnI0.UeRk2MXEtImRvbfu/yvDca9XsOohd9W', 'applicant', '', 1, '', '1766153113_515c7a2bce395f653d000002.png', NULL, '2025-12-19 13:12:29'),
(3, 'Job_dibo', 'provider@gmail.com', '$2y$10$QKDaE3Ij9aj2ffm14VnI0.UeRk2MXEtImRvbfu/yvDca9XsOohd9W', 'provider', '', 1, '', 'default.png', NULL, '2025-12-19 14:24:14');


-- Jobs Table
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
  `benefits` text DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `company_location` varchar(255) DEFAULT NULL,
  `company_website` varchar(255) DEFAULT NULL,
  `is_approved` tinyint(4) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deadline` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `jobs` (`id`, `provider_id`, `title`, `category`, `job_nature`, `vacancy`, `salary`, `location`, `description`, `responsibility`, `qualifications`, `benefits`, `company_name`, `company_location`, `company_website`, `is_approved`, `created_at`, `deadline`) VALUES
(1, 3, 'Tuition', 'Tuition', 'Remote', 1, '5000', 'Basabo, Dhaka', '1 student for Bangla, English', NULL, NULL, NULL, 'non', NULL, NULL, 1, '2025-12-19 14:25:52', NULL),
(2, 3, 'ASP.NET Software Developer (Intern) ', 'Software Development  ', 'Full Time', 5, '8000', 'Dhaka (DOHS Mirpur)', '<--Requirements-->\r\nEducation\r\n\r\n    Bachelor of Science (BSc)\r\n    Diploma in Engineering\r\n\r\n<--Additional Requirements-->\r\n\r\n    Age 22 to 30 years\r\n\r\n    Should have knowledge on .Net Framework (Both on Web Form and MVC) and .Net Core. Should have Knowledge on SQL.\r\n\r\n<--Responsibilities & Context-->\r\n\r\nSynergy Interface provides Customize Software Solution, Insurance ERP, Industrial ERP, Professional Website Design and Development, Web Application and other IT related services.\r\n\r\nSo we prefer candidates who is willing to work with software products. If you think you are accept challenging carrier, you are welcome.\r\n\r\n<--Compensation & Other Benefits-->\r\n\r\n    Mobile bill,Tour allowance,Performance bonus,Profit share,Weekly 2 holidays\r\n    Lunch Facilities: Full Subsidize\r\n    Salary Review: Yearly\r\n    Festival Bonus: 2\r\n\r\nWorkplace\r\n\r\nWork at office\r\n\r\nJob Location\r\n\r\nDhaka (DOHS Mirpur)', NULL, NULL, NULL, 'Synergy Interface Ltd.', NULL, NULL, 1, '2025-12-20 00:22:43', NULL);


-- Applications
CREATE TABLE `applications` (
  `id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `applicant_id` int(11) NOT NULL,
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','viewed','shortlisted') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Messages (Chat)
CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(4) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Notifications
CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `message` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT '#',
  `is_read` tinyint(4) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Reports

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `reporter_id` int(11) DEFAULT NULL,
  `reported_id` int(11) DEFAULT NULL,
  `job_id` int(11) DEFAULT NULL,
  `reason` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `saved_jobs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `saved_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_id` (`job_id`),
  ADD KEY `applicant_id` (`applicant_id`);

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
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `saved_jobs`
--
ALTER TABLE `saved_jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
