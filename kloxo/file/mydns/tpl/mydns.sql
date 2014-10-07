-- phpMyAdmin SQL Dump
-- version 4.0.10
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 06, 2014 at 12:21 PM
-- Server version: 5.5.39
-- PHP Version: 5.4.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `mydns`
--
CREATE DATABASE IF NOT EXISTS `mydns` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `mydns`;

-- --------------------------------------------------------

--
-- Table structure for table `rr`
--

CREATE TABLE IF NOT EXISTS `rr` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `zone` int(10) unsigned NOT NULL,
  `name` char(200) NOT NULL,
  `data` varbinary(128) NOT NULL,
  `aux` int(10) unsigned NOT NULL,
  `ttl` int(10) unsigned NOT NULL DEFAULT '86400',
  `type` enum('A','AAAA','ALIAS','CNAME','HINFO','MX','NAPTR','NS','PTR','RP','SRV','TXT') DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rr` (`zone`,`name`,`type`,`data`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `soa`
--

CREATE TABLE IF NOT EXISTS `soa` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `origin` char(255) NOT NULL,
  `ns` char(255) NOT NULL,
  `mbox` char(255) NOT NULL,
  `serial` int(10) unsigned NOT NULL DEFAULT '1',
  `refresh` int(10) unsigned NOT NULL DEFAULT '28800',
  `retry` int(10) unsigned NOT NULL DEFAULT '7200',
  `expire` int(10) unsigned NOT NULL DEFAULT '604800',
  `minimum` int(10) unsigned NOT NULL DEFAULT '86400',
  `ttl` int(10) unsigned NOT NULL DEFAULT '86400',
  PRIMARY KEY (`id`),
  UNIQUE KEY `origin` (`origin`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
