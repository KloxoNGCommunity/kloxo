-- phpMyAdmin SQL Dump
-- version 3.5.7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 23, 2013 at 07:39 AM
-- Server version: 5.5.29-MariaDB
-- PHP Version: 5.2.17

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `kloxo`
--

-- --------------------------------------------------------

--
-- Table structure for table `watchdog`
--

DROP TABLE IF EXISTS `watchdog`;
CREATE TABLE IF NOT EXISTS `watchdog` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text,
  `servicename` varchar(255) DEFAULT NULL,
  `syncserver` varchar(255) DEFAULT NULL,
  `port` varchar(255) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `added_by_system` varchar(255) DEFAULT NULL,
  `oldsyncserver` varchar(255) DEFAULT NULL,
  `olddeleteflag` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_watchdog` (`parent_clname`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `watchdog`
--

INSERT INTO `watchdog` (`nname`, `parent_clname`, `parent_cmlist`, `servicename`, `syncserver`, `port`, `action`, `status`, `added_by_system`, `oldsyncserver`, `olddeleteflag`) VALUES
('web___localhost', 'pserver-localhost', '', 'web', 'localhost', '80', '__driver_web', 'on', 'on', '', ''),
('phpfpm___localhost', 'pserver-localhost', '', 'php-fpm', 'localhost', '50000', '/etc/init.d/qmail restart', 'on', 'on', '', ''),
('smtp___localhost', 'pserver-localhost', '', 'smtp', 'localhost', '25', '/etc/init.d/qmail restart', 'on', 'on', '', ''),
('pop___localhost', 'pserver-localhost', '', 'pop', 'localhost', '110', '/etc/init.d/dovecot restart', 'on', 'on', '', ''),
('imap___localhost', 'pserver-localhost', '', 'imap', 'localhost', '143', '/etc/init.d/dovecot restart', 'on', 'on', '', ''),
('mysql___localhost', 'pserver-localhost', '', 'mysql', 'localhost', '3306', '/etc/init.d/mysqld restart', 'on', 'on', '', '');
('mariadb___localhost', 'pserver-localhost', '', 'mysql', 'localhost', '3306', '/etc/init.d/mysql restart', 'on', 'on', '', '');
('ftp___localhost', 'pserver-localhost', '', 'ftp', 'localhost', '21', '/etc/init.d/xinetd restart', 'on', 'on', '', ''),

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
