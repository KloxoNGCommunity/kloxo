-- phpMyAdmin SQL Dump
-- version 4.4.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 18, 2015 at 05:32 PM
-- Server version: 10.0.17-MariaDB
-- PHP Version: 5.4.39

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `vpopmail`
--

-- --------------------------------------------------------

--
-- Table structure for table `dir_control`
--

CREATE TABLE IF NOT EXISTS `dir_control` (
  `domain` char(96) CHARACTER SET latin1 NOT NULL,
  `cur_users` int(11) DEFAULT NULL,
  `level_cur` int(11) DEFAULT NULL,
  `level_max` int(11) DEFAULT NULL,
  `level_start0` int(11) DEFAULT NULL,
  `level_start1` int(11) DEFAULT NULL,
  `level_start2` int(11) DEFAULT NULL,
  `level_end0` int(11) DEFAULT NULL,
  `level_end1` int(11) DEFAULT NULL,
  `level_end2` int(11) DEFAULT NULL,
  `level_mod0` int(11) DEFAULT NULL,
  `level_mod1` int(11) DEFAULT NULL,
  `level_mod2` int(11) DEFAULT NULL,
  `level_index0` int(11) DEFAULT NULL,
  `level_index1` int(11) DEFAULT NULL,
  `level_index2` int(11) DEFAULT NULL,
  `the_dir` char(160) CHARACTER SET latin1 DEFAULT NULL
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lastauth`
--

CREATE TABLE IF NOT EXISTS `lastauth` (
  `user` char(32) CHARACTER SET latin1 NOT NULL,
  `domain` char(96) CHARACTER SET latin1 NOT NULL,
  `remote_ip` char(18) CHARACTER SET latin1 NOT NULL,
  `timestamp` bigint(20) NOT NULL DEFAULT '0'
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `valias`
--

CREATE TABLE IF NOT EXISTS `valias` (
  `alias` char(32) CHARACTER SET latin1 NOT NULL,
  `domain` char(96) CHARACTER SET latin1 NOT NULL,
  `valias_line` text CHARACTER SET latin1 NOT NULL
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vpopmail`
--

CREATE TABLE IF NOT EXISTS `vpopmail` (
  `pw_name` char(32) CHARACTER SET latin1 NOT NULL,
  `pw_domain` char(96) CHARACTER SET latin1 NOT NULL,
  `pw_passwd` char(40) CHARACTER SET latin1 DEFAULT NULL,
  `pw_uid` int(11) DEFAULT NULL,
  `pw_gid` int(11) DEFAULT NULL,
  `pw_gecos` char(48) CHARACTER SET latin1 DEFAULT NULL,
  `pw_dir` char(160) CHARACTER SET latin1 DEFAULT NULL,
  `pw_shell` char(20) CHARACTER SET latin1 DEFAULT NULL,
  `pw_clear_passwd` char(16) CHARACTER SET latin1 DEFAULT NULL
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dir_control`
--
ALTER TABLE `dir_control`
  ADD PRIMARY KEY (`domain`);

--
-- Indexes for table `lastauth`
--
ALTER TABLE `lastauth`
  ADD PRIMARY KEY (`user`,`domain`);

--
-- Indexes for table `valias`
--
ALTER TABLE `valias`
  ADD KEY `alias` (`alias`,`domain`);

--
-- Indexes for table `vpopmail`
--
ALTER TABLE `vpopmail`
  ADD PRIMARY KEY (`pw_name`,`pw_domain`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
