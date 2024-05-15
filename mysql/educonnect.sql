-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 15, 2024 at 06:02 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `educonnect`
--

-- --------------------------------------------------------

--
-- Table structure for table `chat`
--

CREATE TABLE `chat` (
  `rowid` bigint(20) NOT NULL,
  `from` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Refer to user.rowid',
  `to_user` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Refer to user.rowid',
  `to_group` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Refer to user.rowid',
  `type` varchar(20) NOT NULL DEFAULT 'text' COMMENT 'text / image / file / homework',
  `text` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  `homework_rowid` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Refer to homework.rowid',
  `create_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exam`
--

CREATE TABLE `exam` (
  `rowid` bigint(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `date` date NOT NULL DEFAULT current_timestamp(),
  `subject_rowid` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Refer to subject.rowid',
  `create_date` datetime NOT NULL DEFAULT current_timestamp(),
  `create_user` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Refer to user.rowid'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exam_score`
--

CREATE TABLE `exam_score` (
  `rowid` bigint(20) NOT NULL,
  `exam_rowid` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Refer to exam.rowid',
  `student_rowid` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Refer to user.rowid',
  `mark` int(10) NOT NULL DEFAULT 0 COMMENT 'in percentage',
  `create_date` datetime NOT NULL DEFAULT current_timestamp(),
  `create_user` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Refer to user.rowid'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `group`
--

CREATE TABLE `group` (
  `rowid` bigint(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `create_date` datetime NOT NULL DEFAULT current_timestamp(),
  `create_user` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Refer to user.rowid'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `group_homework`
--

CREATE TABLE `group_homework` (
  `rowid` bigint(20) NOT NULL,
  `student_rowid` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Refer to user.rowid',
  `homework_rowid` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Refer to homework.rowid',
  `status` varchar(20) NOT NULL DEFAULT 'pending' COMMENT 'pending / submited',
  `file` varchar(255) DEFAULT NULL,
  `submit_date` datetime NOT NULL DEFAULT current_timestamp(),
  `mark` int(10) NOT NULL DEFAULT 0 COMMENT 'in percentage'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `group_member`
--

CREATE TABLE `group_member` (
  `rowid` bigint(20) NOT NULL,
  `user_rowid` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Refer to user.rowid',
  `group_rowid` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Refer to group.rowid',
  `join_date` datetime NOT NULL DEFAULT current_timestamp(),
  `invite_user` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Refer to user.rowid'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `homework`
--

CREATE TABLE `homework` (
  `rowid` bigint(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `duedate` date DEFAULT NULL,
  `create_date` datetime NOT NULL DEFAULT current_timestamp(),
  `create_user` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Refer to user.rowid'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subject`
--

CREATE TABLE `subject` (
  `rowid` bigint(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `create_date` datetime NOT NULL DEFAULT current_timestamp(),
  `create_user` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Refer to user.rowid'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `rowid` bigint(20) NOT NULL,
  `userid` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `otp` varchar(100) DEFAULT NULL,
  `role` varchar(20) NOT NULL COMMENT 'teacher / student / heir',
  `fullname` varchar(255) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL COMMENT 'male / female',
  `ic` varchar(12) DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `lastlog` datetime NOT NULL,
  `active` varchar(1) NOT NULL DEFAULT 'N' COMMENT 'Y=Yes, N=No'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `userlog`
--

CREATE TABLE `userlog` (
  `user_rowid` bigint(20) NOT NULL COMMENT 'Refer to user.rowid',
  `datetime` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_behavior`
--

CREATE TABLE `user_behavior` (
  `rowid` bigint(20) NOT NULL,
  `student_rowid` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Refer to user.rowid',
  `remark` varchar(255) DEFAULT NULL,
  `create_date` datetime NOT NULL DEFAULT current_timestamp(),
  `create_user` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Refer to user.rowid'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_relate`
--

CREATE TABLE `user_relate` (
  `rowid` bigint(20) NOT NULL,
  `student_rowid` bigint(20) NOT NULL COMMENT 'Refer to user.rowid',
  `heir_rowid` bigint(20) NOT NULL COMMENT 'Refer to user.rowid',
  `type` varchar(20) NOT NULL COMMENT 'father / mother / sibling / other',
  `create_date` datetime NOT NULL DEFAULT current_timestamp(),
  `create_user` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Refer to user.rowid'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `chat`
--
ALTER TABLE `chat`
  ADD PRIMARY KEY (`rowid`);

--
-- Indexes for table `exam`
--
ALTER TABLE `exam`
  ADD PRIMARY KEY (`rowid`);

--
-- Indexes for table `exam_score`
--
ALTER TABLE `exam_score`
  ADD PRIMARY KEY (`rowid`);

--
-- Indexes for table `group`
--
ALTER TABLE `group`
  ADD PRIMARY KEY (`rowid`);

--
-- Indexes for table `group_homework`
--
ALTER TABLE `group_homework`
  ADD PRIMARY KEY (`rowid`);

--
-- Indexes for table `group_member`
--
ALTER TABLE `group_member`
  ADD PRIMARY KEY (`rowid`);

--
-- Indexes for table `homework`
--
ALTER TABLE `homework`
  ADD PRIMARY KEY (`rowid`);

--
-- Indexes for table `subject`
--
ALTER TABLE `subject`
  ADD PRIMARY KEY (`rowid`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`rowid`);

--
-- Indexes for table `user_behavior`
--
ALTER TABLE `user_behavior`
  ADD PRIMARY KEY (`rowid`);

--
-- Indexes for table `user_relate`
--
ALTER TABLE `user_relate`
  ADD PRIMARY KEY (`rowid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `chat`
--
ALTER TABLE `chat`
  MODIFY `rowid` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exam`
--
ALTER TABLE `exam`
  MODIFY `rowid` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exam_score`
--
ALTER TABLE `exam_score`
  MODIFY `rowid` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `group`
--
ALTER TABLE `group`
  MODIFY `rowid` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `group_homework`
--
ALTER TABLE `group_homework`
  MODIFY `rowid` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `group_member`
--
ALTER TABLE `group_member`
  MODIFY `rowid` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `homework`
--
ALTER TABLE `homework`
  MODIFY `rowid` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subject`
--
ALTER TABLE `subject`
  MODIFY `rowid` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `rowid` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_behavior`
--
ALTER TABLE `user_behavior`
  MODIFY `rowid` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_relate`
--
ALTER TABLE `user_relate`
  MODIFY `rowid` bigint(20) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
