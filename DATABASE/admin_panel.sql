-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 19, 2025 at 12:35 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

CREATE DATABASE admin_panel;

USE admin_panel;


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `admin_panel`
--

-- --------------------------------------------------------

--
-- Table structure for table `batches`
--

CREATE TABLE `batches` (
  `id` int(11) NOT NULL,
  `batch_name` varchar(100) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `capacity` int(11) NOT NULL,
  `current_enrollment` int(11) DEFAULT 0,
  `status` enum('active','completed','upcoming') DEFAULT 'upcoming'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `batches`
--

INSERT INTO `batches` (`id`, `batch_name`, `start_date`, `end_date`, `capacity`, `current_enrollment`, `status`) VALUES
(1, 'Web Development Bootcamp', '2025-03-20', '2025-03-28', 30, 0, 'active'),
(2, 'Data Science Fundamentals', '2025-03-11', '2025-03-18', 25, 0, 'active'),
(3, 'Machine Learning Mastery', '2025-03-10', '2025-03-15', 10, 0, 'completed');

-- --------------------------------------------------------

--
-- Table structure for table `batch_enrollments`
--

CREATE TABLE `batch_enrollments` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `batch_id` int(11) NOT NULL,
  `enrollment_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `batch_enrollments`
--

INSERT INTO `batch_enrollments` (`id`, `student_id`, `batch_id`, `enrollment_date`) VALUES
(1, 10, 2, '2025-03-18 12:24:26');

-- --------------------------------------------------------

--
-- Table structure for table `internships`
--

CREATE TABLE `internships` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `duration` int(11) NOT NULL,
  `stipend` decimal(10,2) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `requirements` text DEFAULT NULL,
  `status` enum('active','completed','upcoming') DEFAULT 'upcoming',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `internships`
--

INSERT INTO `internships` (`id`, `title`, `description`, `start_date`, `end_date`, `duration`, `stipend`, `location`, `requirements`, `status`, `created_at`) VALUES
(1, 'Software Engineering Internship', 'Work on full-stack development.', '2025-03-19', '2025-03-25', 90, 5000.00, 'Remote', 'Proficient in JavaScript, Python', 'active', '2025-03-18 09:19:45');

-- --------------------------------------------------------

--
-- Table structure for table `internship_enrollments`
--

CREATE TABLE `internship_enrollments` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `internship_id` int(11) DEFAULT NULL,
  `enrollment_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `internship_enrollments`
--

INSERT INTO `internship_enrollments` (`id`, `student_id`, `internship_id`, `enrollment_date`) VALUES
(1, 4, 1, '2025-03-18 11:35:42'),
(2, 9, 1, '2025-03-18 11:52:37');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `name`, `email`, `phone`) VALUES
(4, 'John Doe', 'johndoe@gmail.com', '9876543210'),
(6, 'Jane Smith', 'janesmith@gmail.com', '9988776655'),
(8, 'Alice Johnson', 'alice.johnson@gmail.com', '9123456789'),
(9, 'Bob Williams', 'bob.williams@gmail.com', '9234567890'),
(10, 'Charlie Brown', 'charlie.brown@gmail.com', '9345678901');

-- --------------------------------------------------------

--
-- Table structure for table `workshops`
--

CREATE TABLE `workshops` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `workshop_date` date NOT NULL,
  `duration` int(11) NOT NULL,
  `capacity` int(11) NOT NULL,
  `instructor` varchar(100) NOT NULL,
  `status` enum('scheduled','completed','cancelled') DEFAULT 'scheduled'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `workshops`
--

INSERT INTO `workshops` (`id`, `title`, `description`, `workshop_date`, `duration`, `capacity`, `instructor`, `status`) VALUES
(1, 'AI in Industry', 'A deep dive into AI applications.', '2025-01-01', 1, 12, 'Dr. Emily Carter', 'scheduled');

-- --------------------------------------------------------

--
-- Table structure for table `workshop_enrollments`
--

CREATE TABLE `workshop_enrollments` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `workshop_id` int(11) DEFAULT NULL,
  `enrollment_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `workshop_enrollments`
--

INSERT INTO `workshop_enrollments` (`id`, `student_id`, `workshop_id`, `enrollment_date`) VALUES
(1, 6, 1, '2025-03-18 11:36:27'),
(2, 8, 1, '2025-03-18 11:43:55');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `batches`
--
ALTER TABLE `batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `batch_enrollments`
--
ALTER TABLE `batch_enrollments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_batch_enrollment` (`student_id`,`batch_id`),
  ADD KEY `batch_id` (`batch_id`);

--
-- Indexes for table `internships`
--
ALTER TABLE `internships`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `internship_enrollments`
--
ALTER TABLE `internship_enrollments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `internship_id` (`internship_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `workshops`
--
ALTER TABLE `workshops`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `workshop_enrollments`
--
ALTER TABLE `workshop_enrollments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `workshop_id` (`workshop_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `batches`
--
ALTER TABLE `batches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `batch_enrollments`
--
ALTER TABLE `batch_enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `internships`
--
ALTER TABLE `internships`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `internship_enrollments`
--
ALTER TABLE `internship_enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `workshops`
--
ALTER TABLE `workshops`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `workshop_enrollments`
--
ALTER TABLE `workshop_enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `batch_enrollments`
--
ALTER TABLE `batch_enrollments`
  ADD CONSTRAINT `batch_enrollments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `batch_enrollments_ibfk_2` FOREIGN KEY (`batch_id`) REFERENCES `batches` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `internship_enrollments`
--
ALTER TABLE `internship_enrollments`
  ADD CONSTRAINT `internship_enrollments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
  ADD CONSTRAINT `internship_enrollments_ibfk_2` FOREIGN KEY (`internship_id`) REFERENCES `internships` (`id`);

--
-- Constraints for table `workshop_enrollments`
--
ALTER TABLE `workshop_enrollments`
  ADD CONSTRAINT `workshop_enrollments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
  ADD CONSTRAINT `workshop_enrollments_ibfk_2` FOREIGN KEY (`workshop_id`) REFERENCES `workshops` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
