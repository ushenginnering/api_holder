-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 31, 2023 at 11:24 AM
-- Server version: 10.4.14-MariaDB
-- PHP Version: 7.4.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rapid_compiler_v2`
--

-- --------------------------------------------------------

--
-- Table structure for table `contact_requests`
--

CREATE TABLE `contact_requests` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `school_name` varchar(100) DEFAULT NULL,
  `capacity_ranges` varchar(255) DEFAULT NULL,
  `referral_source` varchar(100) DEFAULT NULL,
  `desired_results` text DEFAULT NULL,
  `submission_date` datetime DEFAULT current_timestamp(),
  `email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

CREATE TABLE `packages` (
  `package_id` int(11) NOT NULL,
  `package_name` varchar(50) DEFAULT NULL,
  `package_price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `plan_packages`
--

CREATE TABLE `plan_packages` (
  `id` int(11) NOT NULL,
  `plan_id` int(11) DEFAULT NULL,
  `package_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `pricing_plans`
--

CREATE TABLE `pricing_plans` (
  `plan_id` int(11) NOT NULL,
  `plan_name` varchar(50) DEFAULT NULL,
  `plan_description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `school`
--

CREATE TABLE `school` (
  `school_Id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `session` varchar(50) DEFAULT NULL,
  `term` varchar(50) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `moto` text DEFAULT NULL,
  `influencerId` text DEFAULT NULL,
  `schoolPassword` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `test_schoolaffective`
--

CREATE TABLE `test_schoolaffective` (
  `traitID` int(11) NOT NULL,
  `traitName` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `test_schoolaffectivescore`
--

CREATE TABLE `test_schoolaffectivescore` (
  `affectiveScoreId` int(11) NOT NULL,
  `studentID` int(11) DEFAULT NULL,
  `resultID` int(11) DEFAULT NULL,
  `traitID` int(11) DEFAULT NULL,
  `trait_score` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `test_schoolattendance`
--

CREATE TABLE `test_schoolattendance` (
  `attendanceScoreId` int(11) NOT NULL,
  `studentID` int(11) DEFAULT NULL,
  `resultID` int(11) DEFAULT NULL,
  `times_present` int(11) DEFAULT NULL,
  `times_absent` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `test_schoolautomated_comment`
--

CREATE TABLE `test_schoolautomated_comment` (
  `autocommentid` int(11) NOT NULL,
  `classID` int(11) DEFAULT NULL,
  `teachersComment` text DEFAULT NULL,
  `headTeacherComment` text DEFAULT NULL,
  `averageApplicable` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `test_schoolcomment`
--

CREATE TABLE `test_schoolcomment` (
  `commentScoreId` int(11) NOT NULL,
  `studentID` int(11) DEFAULT NULL,
  `resultID` int(11) DEFAULT NULL,
  `teacherComment` text DEFAULT NULL,
  `headTeacherComment` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `test_schoolprocessresult`
--

CREATE TABLE `test_schoolprocessresult` (
  `processId` int(11) NOT NULL,
  `resultId` int(11) DEFAULT NULL,
  `average` decimal(10,2) DEFAULT NULL,
  `position` int(11) DEFAULT NULL,
  `next_term_begins` datetime DEFAULT NULL,
  `term_end` datetime DEFAULT NULL,
  `this_term_begins` datetime DEFAULT NULL,
  `process_status` varchar(50) DEFAULT NULL,
  `distribute_result_status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `test_school_class`
--

CREATE TABLE `test_school_class` (
  `classID` int(11) NOT NULL,
  `schoolID` int(11) DEFAULT NULL,
  `class_name` varchar(50) DEFAULT NULL,
  `classorder` int(11) DEFAULT NULL,
  `formTeacher` int(11) DEFAULT NULL,
  `promote` tinyint(1) DEFAULT NULL,
  `class_section_id` int(11) DEFAULT NULL,
  `graduating_class` tinyint(1) DEFAULT NULL,
  `mok_test_number` int(11) DEFAULT NULL,
  `mok_test_status` tinyint(1) DEFAULT NULL,
  `result_processing_engine` varchar(255) DEFAULT NULL,
  `max_test_score` decimal(10,2) DEFAULT NULL,
  `max_exam_score` decimal(10,2) DEFAULT NULL,
  `forward` tinyint(1) DEFAULT NULL,
  `max_first_test_score` decimal(10,2) DEFAULT NULL,
  `max_snd_test_score` decimal(10,2) DEFAULT NULL,
  `affective_array` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `test_school_resulttarget`
--

CREATE TABLE `test_school_resulttarget` (
  `resultId` int(11) NOT NULL,
  `session` int(11) DEFAULT NULL,
  `term` int(11) DEFAULT NULL,
  `class` int(11) DEFAULT NULL,
  `schoolID` int(11) DEFAULT NULL,
  `studentID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `test_school_score`
--

CREATE TABLE `test_school_score` (
  `ScoreId` int(11) NOT NULL,
  `StudentId` int(11) DEFAULT NULL,
  `ResultId` int(11) DEFAULT NULL,
  `Subject` varchar(100) DEFAULT NULL,
  `1st_Test_Score` decimal(10,2) DEFAULT NULL,
  `2nd_Test_Score` decimal(10,2) DEFAULT NULL,
  `CA_Score` decimal(10,2) DEFAULT NULL,
  `Exam_Score` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `test_school_students`
--

CREATE TABLE `test_school_students` (
  `id` int(11) NOT NULL,
  `studentId` varchar(50) DEFAULT NULL,
  `firstname` varchar(50) DEFAULT NULL,
  `lastname` varchar(50) DEFAULT NULL,
  `othername` varchar(50) DEFAULT NULL,
  `soo` varchar(50) DEFAULT NULL,
  `lg` varchar(50) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `sex` varchar(10) DEFAULT NULL,
  `class` varchar(50) DEFAULT NULL,
  `sporthouse` varchar(50) DEFAULT NULL,
  `adminnumber` varchar(50) DEFAULT NULL,
  `school` varchar(255) DEFAULT NULL,
  `session` varchar(50) DEFAULT NULL,
  `cur_class` varchar(50) DEFAULT NULL,
  `picture` blob DEFAULT NULL,
  `subclass` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `move` tinyint(1) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `2023_2024_class` varchar(50) DEFAULT NULL,
  `2023_2024_subclass` varchar(50) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `guardians_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `test_school_subclass`
--

CREATE TABLE `test_school_subclass` (
  `subclassID` int(11) NOT NULL,
  `classID` int(11) DEFAULT NULL,
  `subclass_name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `test_school_subject`
--

CREATE TABLE `test_school_subject` (
  `subjectId` int(11) NOT NULL,
  `classID` varchar(50) DEFAULT NULL,
  `subjectName` varchar(100) DEFAULT NULL,
  `teacherId` int(11) DEFAULT NULL,
  `groupname` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userid` int(11) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `schoolID` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `signature` varchar(255) DEFAULT NULL,
  `verified_email` varchar(255) DEFAULT NULL,
  `verification_code` varchar(255) DEFAULT NULL,
  `new_signup_date` datetime DEFAULT current_timestamp(),
  `last_seen_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userid`, `email`, `username`, `password`, `phone`, `schoolID`, `status`, `signature`, `verified_email`, `verification_code`, `new_signup_date`, `last_seen_date`) VALUES
(5, 'uchennaukeh@gmail.com', 'uchenna', '$2y$10$rLQXTEYZ45OR1CCzx1SIweaeCq5UPyyfuP0JMEZM5eLGLd/hgIb1.', '', 0, 'teacher', '', NULL, '1579', '2023-10-30 14:16:15', '2023-10-30 14:16:15');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contact_requests`
--
ALTER TABLE `contact_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`package_id`);

--
-- Indexes for table `plan_packages`
--
ALTER TABLE `plan_packages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pricing_plans`
--
ALTER TABLE `pricing_plans`
  ADD PRIMARY KEY (`plan_id`);

--
-- Indexes for table `school`
--
ALTER TABLE `school`
  ADD PRIMARY KEY (`school_Id`);

--
-- Indexes for table `test_schoolaffective`
--
ALTER TABLE `test_schoolaffective`
  ADD PRIMARY KEY (`traitID`);

--
-- Indexes for table `test_schoolaffectivescore`
--
ALTER TABLE `test_schoolaffectivescore`
  ADD PRIMARY KEY (`affectiveScoreId`);

--
-- Indexes for table `test_schoolattendance`
--
ALTER TABLE `test_schoolattendance`
  ADD PRIMARY KEY (`attendanceScoreId`);

--
-- Indexes for table `test_schoolautomated_comment`
--
ALTER TABLE `test_schoolautomated_comment`
  ADD PRIMARY KEY (`autocommentid`);

--
-- Indexes for table `test_schoolcomment`
--
ALTER TABLE `test_schoolcomment`
  ADD PRIMARY KEY (`commentScoreId`);

--
-- Indexes for table `test_schoolprocessresult`
--
ALTER TABLE `test_schoolprocessresult`
  ADD PRIMARY KEY (`processId`);

--
-- Indexes for table `test_school_class`
--
ALTER TABLE `test_school_class`
  ADD PRIMARY KEY (`classID`);

--
-- Indexes for table `test_school_resulttarget`
--
ALTER TABLE `test_school_resulttarget`
  ADD PRIMARY KEY (`resultId`);

--
-- Indexes for table `test_school_score`
--
ALTER TABLE `test_school_score`
  ADD PRIMARY KEY (`ScoreId`);

--
-- Indexes for table `test_school_students`
--
ALTER TABLE `test_school_students`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `test_school_subclass`
--
ALTER TABLE `test_school_subclass`
  ADD PRIMARY KEY (`subclassID`);

--
-- Indexes for table `test_school_subject`
--
ALTER TABLE `test_school_subject`
  ADD PRIMARY KEY (`subjectId`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contact_requests`
--
ALTER TABLE `contact_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `packages`
--
ALTER TABLE `packages`
  MODIFY `package_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `plan_packages`
--
ALTER TABLE `plan_packages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pricing_plans`
--
ALTER TABLE `pricing_plans`
  MODIFY `plan_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `school`
--
ALTER TABLE `school`
  MODIFY `school_Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `test_schoolaffective`
--
ALTER TABLE `test_schoolaffective`
  MODIFY `traitID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `test_schoolaffectivescore`
--
ALTER TABLE `test_schoolaffectivescore`
  MODIFY `affectiveScoreId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `test_schoolattendance`
--
ALTER TABLE `test_schoolattendance`
  MODIFY `attendanceScoreId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `test_schoolautomated_comment`
--
ALTER TABLE `test_schoolautomated_comment`
  MODIFY `autocommentid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `test_schoolcomment`
--
ALTER TABLE `test_schoolcomment`
  MODIFY `commentScoreId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `test_schoolprocessresult`
--
ALTER TABLE `test_schoolprocessresult`
  MODIFY `processId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `test_school_class`
--
ALTER TABLE `test_school_class`
  MODIFY `classID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `test_school_resulttarget`
--
ALTER TABLE `test_school_resulttarget`
  MODIFY `resultId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `test_school_score`
--
ALTER TABLE `test_school_score`
  MODIFY `ScoreId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `test_school_students`
--
ALTER TABLE `test_school_students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `test_school_subclass`
--
ALTER TABLE `test_school_subclass`
  MODIFY `subclassID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `test_school_subject`
--
ALTER TABLE `test_school_subject`
  MODIFY `subjectId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
