-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 24, 2024 at 07:03 PM
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
  `from` bigint(20) DEFAULT 0 COMMENT 'Refer to user.rowid',
  `to_user` bigint(20) DEFAULT 0 COMMENT 'Refer to user.rowid',
  `to_group` bigint(20) DEFAULT 0 COMMENT 'Refer to user.rowid',
  `type` varchar(20) NOT NULL DEFAULT 'text' COMMENT 'text / image / file / homework',
  `text` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  `create_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chat_unread`
--

CREATE TABLE `chat_unread` (
  `rowid` bigint(20) NOT NULL,
  `user_rowid` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Refer to user.rowid',
  `from_rowid` bigint(20) DEFAULT NULL COMMENT 'Refer to user.rowid',
  `group_rowid` bigint(20) DEFAULT NULL COMMENT 'Refer to group.rowid',
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `qty` int(20) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `class`
--

CREATE TABLE `class` (
  `rowid` bigint(20) NOT NULL,
  `teacher_rowid` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Refer to user.rowid',
  `subject_rowid` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Refer to subject.rowid',
  `name` varchar(100) NOT NULL,
  `create_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `class_homework`
--

CREATE TABLE `class_homework` (
  `rowid` bigint(20) NOT NULL,
  `student_rowid` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Refer to user.rowid',
  `homework_rowid` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Refer to homework.rowid',
  `status` varchar(20) NOT NULL DEFAULT 'pending' COMMENT 'pending / submited',
  `file` varchar(255) DEFAULT NULL,
  `submit_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `class_student`
--

CREATE TABLE `class_student` (
  `rowid` bigint(20) NOT NULL,
  `class_rowid` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Refer to class.rowid',
  `student_rowid` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Refer to user.rowid',
  `create_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exam`
--

CREATE TABLE `exam` (
  `rowid` bigint(20) NOT NULL,
  `class_rowid` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Refer to class.rowid',
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
  `create_date` datetime NOT NULL DEFAULT current_timestamp(),
  `create_user` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Refer to user.rowid'
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
  `class_rowid` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Refer to class.rowid',
  `name` varchar(100) NOT NULL,
  `duedate` date DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
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
  `fullname` varchar(255) DEFAULT '',
  `gender` varchar(10) DEFAULT NULL COMMENT 'male / female',
  `ic` varchar(12) DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `lastlog` datetime NOT NULL,
  `active` varchar(1) NOT NULL DEFAULT 'N' COMMENT 'Y=Yes, N=No'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`rowid`, `userid`, `password`, `otp`, `role`, `fullname`, `gender`, `ic`, `birthday`, `lastlog`, `active`) VALUES
(1, 'aiman', '9d4e1e23bd5b727046a9e3b4b7db57bd8d6ee684', '9e0c7721f42156b0555e184b8dd5ba39de10beb0', 'teacher', 'Muhammad Aiman', 'male', '010101020303', '2001-01-01', '2024-05-25 00:55:24', 'Y'),
(2, 'faris', '9d4e1e23bd5b727046a9e3b4b7db57bd8d6ee684', 'aa10f7c647e793acde02ebd62778cd5261cda9e9', 'teacher', '', NULL, NULL, NULL, '2024-05-23 11:18:50', 'Y'),
(3, 'aidil', '9d4e1e23bd5b727046a9e3b4b7db57bd8d6ee684', '221fe674c17f3f0f88d23d777c7570bcd9227e5f', 'student', '', NULL, NULL, NULL, '2024-05-25 00:53:43', 'Y'),
(4, 'mubin', '9d4e1e23bd5b727046a9e3b4b7db57bd8d6ee684', '81004cb80606fa1a219fb20958201ae502a21cf0', 'student', '', NULL, NULL, NULL, '2024-05-23 11:12:27', 'Y'),
(5, 'adzim', '9d4e1e23bd5b727046a9e3b4b7db57bd8d6ee684', 'ed7070a29935fe6bc0a4372d629d220b244781d7', 'student', '', NULL, NULL, NULL, '2024-05-23 11:12:36', 'Y'),
(6, 'danish', '9d4e1e23bd5b727046a9e3b4b7db57bd8d6ee684', 'a7f76326aeaa4e077f7a65083a74e79d68f641d2', 'student', '', NULL, NULL, NULL, '2024-05-23 11:12:44', 'Y'),
(7, 'ismail', '9d4e1e23bd5b727046a9e3b4b7db57bd8d6ee684', '7c6d9d51354cd39721f1a66cb4a1b68c08a7bfb8', 'heir', '', NULL, NULL, NULL, '2024-05-23 11:12:51', 'Y');

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
-- Indexes for table `chat_unread`
--
ALTER TABLE `chat_unread`
  ADD PRIMARY KEY (`rowid`);

--
-- Indexes for table `class`
--
ALTER TABLE `class`
  ADD PRIMARY KEY (`rowid`);

--
-- Indexes for table `class_homework`
--
ALTER TABLE `class_homework`
  ADD PRIMARY KEY (`rowid`);

--
-- Indexes for table `class_student`
--
ALTER TABLE `class_student`
  ADD PRIMARY KEY (`rowid`);

--
-- Indexes for table `exam`
--
ALTER TABLE `exam`
  ADD PRIMARY KEY (`rowid`);

--
-- Indexes for table `group`
--
ALTER TABLE `group`
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
-- AUTO_INCREMENT for table `chat_unread`
--
ALTER TABLE `chat_unread`
  MODIFY `rowid` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `class`
--
ALTER TABLE `class`
  MODIFY `rowid` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `class_homework`
--
ALTER TABLE `class_homework`
  MODIFY `rowid` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `class_student`
--
ALTER TABLE `class_student`
  MODIFY `rowid` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exam`
--
ALTER TABLE `exam`
  MODIFY `rowid` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `group`
--
ALTER TABLE `group`
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
  MODIFY `rowid` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
