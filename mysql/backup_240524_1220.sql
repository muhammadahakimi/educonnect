-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 23, 2024 at 06:20 PM
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

--
-- Dumping data for table `chat`
--

INSERT INTO `chat` (`rowid`, `from`, `to_user`, `to_group`, `type`, `text`, `image`, `file`, `create_date`) VALUES
(1, 1, 7, 0, 'text', 'hi, anak encik nakal', '', '', '2024-05-23 11:18:34'),
(2, 2, 1, 0, 'text', 'hi, cikgu aiman.', '', '', '2024-05-23 11:19:30'),
(3, 2, 1, 0, 'image', '', '20ddd3d3e5d09e3d08f5744f3ea305deeb5fc502.jpg', '', '2024-05-23 11:19:55'),
(4, 2, 5, 0, 'text', 'hg jangan duk buat style sangat', '', '', '2024-05-23 11:25:28'),
(5, 2, 5, 0, 'text', 'cg duk lama simpan', '', '', '2024-05-23 11:25:50'),
(6, 1, 2, 0, 'text', 'cikgu tiba tiba send gambar dekat saya dah kenapa', '', '', '2024-05-23 11:31:07');

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

--
-- Dumping data for table `chat_unread`
--

INSERT INTO `chat_unread` (`rowid`, `user_rowid`, `from_rowid`, `group_rowid`, `date`, `qty`) VALUES
(1, 7, 1, NULL, '2024-05-23 11:18:34', 1),
(2, 1, 2, NULL, '2024-05-23 23:39:02', 0),
(3, 5, 2, NULL, '2024-05-23 11:25:50', 2),
(4, 2, 1, NULL, '2024-05-23 11:31:07', 1);

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

--
-- Dumping data for table `class`
--

INSERT INTO `class` (`rowid`, `teacher_rowid`, `subject_rowid`, `name`, `create_date`) VALUES
(1, 1, 2, 'Science Form 1 Arif 2024', '2024-05-23 11:15:14'),
(2, 1, 1, 'Mathematics Form 1 Arif 2024', '2024-05-23 22:44:08');

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
  `submit_date` datetime NOT NULL DEFAULT current_timestamp(),
  `mark` int(10) NOT NULL DEFAULT 0 COMMENT 'in percentage'
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

--
-- Dumping data for table `class_student`
--

INSERT INTO `class_student` (`rowid`, `class_rowid`, `student_rowid`, `create_date`) VALUES
(1, 1, 5, '2024-05-23 11:15:18'),
(2, 1, 3, '2024-05-23 11:15:22'),
(3, 1, 6, '2024-05-23 11:15:25'),
(4, 1, 4, '2024-05-23 11:15:27'),
(5, 2, 3, '2024-05-23 22:44:16'),
(6, 2, 6, '2024-05-23 22:44:21');

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

--
-- Dumping data for table `exam`
--

INSERT INTO `exam` (`rowid`, `class_rowid`, `student_rowid`, `mark`, `create_date`, `create_user`) VALUES
(1, 1, 5, 0, '2024-05-23 23:36:00', 1),
(2, 1, 3, 80, '2024-05-23 23:36:00', 1),
(3, 1, 6, 97, '2024-05-23 23:36:00', 1),
(4, 1, 4, 68, '2024-05-23 23:36:00', 1);

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
  `subject_rowid` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Refer to subject.rowid',
  `name` varchar(100) NOT NULL,
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

--
-- Dumping data for table `subject`
--

INSERT INTO `subject` (`rowid`, `name`, `create_date`, `create_user`) VALUES
(1, 'Mathematics Form 1', '2024-05-23 11:13:48', 1),
(2, 'Science Form 1', '2024-05-23 11:14:06', 1);

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
(1, 'aiman', '9d4e1e23bd5b727046a9e3b4b7db57bd8d6ee684', '0a11c0bc20b648b84d2f6583b9ce0d3ea8e2780c', 'teacher', 'Muhammad Aiman', 'male', '010101020303', '2001-01-01', '2024-05-23 21:18:49', 'Y'),
(2, 'faris', '9d4e1e23bd5b727046a9e3b4b7db57bd8d6ee684', 'aa10f7c647e793acde02ebd62778cd5261cda9e9', 'teacher', '', NULL, NULL, NULL, '2024-05-23 11:18:50', 'Y'),
(3, 'aidil', '9d4e1e23bd5b727046a9e3b4b7db57bd8d6ee684', '15f5d94372d151c853d0c271fbd2d0f0f6d944cd', 'student', '', NULL, NULL, NULL, '2024-05-24 00:16:34', 'Y'),
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

--
-- Dumping data for table `userlog`
--

INSERT INTO `userlog` (`user_rowid`, `datetime`) VALUES
(1, '2024-05-23 11:11:30'),
(2, '2024-05-23 11:12:06'),
(3, '2024-05-23 11:12:17'),
(4, '2024-05-23 11:12:27'),
(5, '2024-05-23 11:12:36'),
(6, '2024-05-23 11:12:44'),
(7, '2024-05-23 11:12:51'),
(1, '2024-05-23 11:12:58'),
(2, '2024-05-23 11:18:50'),
(1, '2024-05-23 11:29:47'),
(1, '2024-05-23 11:42:32'),
(1, '2024-05-23 21:18:49'),
(3, '2024-05-24 00:16:34');

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
-- Dumping data for table `user_relate`
--

INSERT INTO `user_relate` (`rowid`, `student_rowid`, `heir_rowid`, `type`, `create_date`, `create_user`) VALUES
(1, 3, 7, 'father', '2024-05-23 11:13:20', 1);

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
  MODIFY `rowid` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `chat_unread`
--
ALTER TABLE `chat_unread`
  MODIFY `rowid` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `class`
--
ALTER TABLE `class`
  MODIFY `rowid` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `class_homework`
--
ALTER TABLE `class_homework`
  MODIFY `rowid` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `class_student`
--
ALTER TABLE `class_student`
  MODIFY `rowid` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `exam`
--
ALTER TABLE `exam`
  MODIFY `rowid` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
  MODIFY `rowid` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  MODIFY `rowid` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
