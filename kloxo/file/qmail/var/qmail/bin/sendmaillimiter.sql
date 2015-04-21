-- phpMyAdmin SQL Dump
-- version 4.4.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 21, 2015 at 02:18 PM
-- Server version: 10.0.17-MariaDB
-- PHP Version: 5.4.39

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `sendmaillimiter`
--

-- --------------------------------------------------------

--
-- Table structure for table `client_sendmail`
--

CREATE TABLE IF NOT EXISTS `client_sendmail` (
  `id` int(11) NOT NULL,
  `client_uid` int(10) unsigned NOT NULL DEFAULT '0',
  `client_name` varchar(50) NOT NULL,
  `client_loglevel` int(1) unsigned NOT NULL DEFAULT '1',
  `sm_group` int(1) unsigned NOT NULL DEFAULT '1',
  `sm_ignore` tinyint(1) NOT NULL DEFAULT '0',
  `count` mediumint(8) unsigned NOT NULL DEFAULT '1',
  `first_request` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_request` datetime NOT NULL DEFAULT '1975-02-18 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `client_sendmail`
--
ALTER TABLE `client_sendmail`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `client_uid` (`client_uid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `client_sendmail`
--
ALTER TABLE `client_sendmail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
