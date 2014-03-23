-- phpMyAdmin SQL Dump
-- version 4.1.7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 19, 2014 at 02:11 PM
-- Server version: 5.5.36
-- PHP Version: 5.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `vpopmail`
--
CREATE DATABASE IF NOT EXISTS `vpopmail` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `vpopmail`;

-- --------------------------------------------------------

--
-- Table structure for table `dir_control`
--

CREATE TABLE IF NOT EXISTS `dir_control` (
  `domain` char(96) NOT NULL,
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
  `the_dir` char(160) DEFAULT NULL,
  PRIMARY KEY (`domain`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `lastauth`
--

CREATE TABLE IF NOT EXISTS `lastauth` (
  `user` char(32) NOT NULL,
  `domain` char(96) NOT NULL,
  `remote_ip` char(18) NOT NULL,
  `timestamp` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user`,`domain`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `valias`
--

CREATE TABLE IF NOT EXISTS `valias` (
  `alias` char(32) NOT NULL,
  `domain` char(96) NOT NULL,
  `valias_line` text NOT NULL,
  KEY `alias` (`alias`,`domain`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `vpopmail`
--

CREATE TABLE IF NOT EXISTS `vpopmail` (
  `pw_name` char(32) NOT NULL,
  `pw_domain` char(96) NOT NULL,
  `pw_passwd` char(40) DEFAULT NULL,
  `pw_uid` int(11) DEFAULT NULL,
  `pw_gid` int(11) DEFAULT NULL,
  `pw_gecos` char(48) DEFAULT NULL,
  `pw_dir` char(160) DEFAULT NULL,
  `pw_shell` char(20) DEFAULT NULL,
  `pw_clear_passwd` char(16) DEFAULT NULL,
  PRIMARY KEY (`pw_name`,`pw_domain`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
