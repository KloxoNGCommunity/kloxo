-- phpMyAdmin SQL Dump
-- version 4.0.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 28, 2014 at 04:20 AM
-- Server version: 5.5.35-log
-- PHP Version: 5.2.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET GLOBAL innodb_default_row_format='dynamic';

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `kloxo`
--

CREATE DATABASE IF NOT EXISTS kloxo;

USE kloxo;

-- --------------------------------------------------------

--
-- Table structure for table `actionlog`
--

CREATE TABLE IF NOT EXISTS `actionlog` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `login` varchar(255) DEFAULT NULL,
  `loginclname` varchar(255) DEFAULT NULL,
  `auxiliary_id` varchar(255) DEFAULT NULL,
  `ipaddress` varchar(255) DEFAULT NULL,
  `class` varchar(255) DEFAULT NULL,
  `objectname` varchar(255) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `subaction` varchar(255) DEFAULT NULL,
  `ddate` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_actionlog` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `addondomain`
--

CREATE TABLE IF NOT EXISTS `addondomain` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `ttype` varchar(255) DEFAULT NULL,
  `destinationdir` varchar(255) DEFAULT NULL,
  `mail_flag` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_addondomain` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `allowedip`
--

CREATE TABLE IF NOT EXISTS `allowedip` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `ipaddress` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_allowedip` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `anonftpipaddress`
--

CREATE TABLE IF NOT EXISTS `anonftpipaddress` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `status` varchar(255) DEFAULT NULL,
  `syncserver` varchar(255) DEFAULT NULL,
  `ipaddr` varchar(255) DEFAULT NULL,
  `message` varchar(255) DEFAULT NULL,
  `anondomain` varchar(255) DEFAULT NULL,
  `ser_anonftpmisc_b` longtext DEFAULT '',
  `disk_limit` varchar(255) DEFAULT NULL,
  `connection_limit` varchar(255) DEFAULT NULL,
  `download_limit` varchar(255) DEFAULT NULL,
  `oldsyncserver` varchar(255) DEFAULT NULL,
  `olddeleteflag` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_anonftpipaddress` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `aspnet`
--

CREATE TABLE IF NOT EXISTS `aspnet` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `version` varchar(255) DEFAULT NULL,
  `syncserver` varchar(255) DEFAULT NULL,
  `session_timeout` varchar(255) DEFAULT NULL,
  `ser_globalization_b` longtext DEFAULT '',
  `ser_aspnetmisc_b` longtext DEFAULT '',
  `oldsyncserver` varchar(255) DEFAULT NULL,
  `olddeleteflag` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_aspnet` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `autoresponder`
--

CREATE TABLE IF NOT EXISTS `autoresponder` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `status` varchar(255) DEFAULT NULL,
  `send_rule` varchar(255) DEFAULT NULL,
  `reply_subject` varchar(255) DEFAULT NULL,
  `text_message` longtext DEFAULT '',
  `autores_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_autoresponder` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `auxiliary`
--

CREATE TABLE IF NOT EXISTS `auxiliary` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `realname` varchar(255) DEFAULT NULL,
  `add_address` varchar(255) DEFAULT NULL,
  `add_city` varchar(255) DEFAULT NULL,
  `add_country` varchar(255) DEFAULT NULL,
  `add_telephone` varchar(255) DEFAULT NULL,
  `add_fax` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `cpstatus` varchar(255) DEFAULT NULL,
  `demo_status` varchar(255) DEFAULT NULL,
  `contactemail` varchar(255) DEFAULT NULL,
  `text_comment` longtext DEFAULT '',
  `disable_per` varchar(255) DEFAULT NULL,
  `ser_hpfilter` longtext DEFAULT '',
  `ddate` varchar(255) DEFAULT NULL,
  `ser_dskhistory` longtext DEFAULT '',
  `ser_dskshortcut_a` longtext DEFAULT '',
  `interface_template` varchar(255) DEFAULT NULL,
  `ser_boxpos` longtext DEFAULT '',
  `dialogsize` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `realpass` varchar(255) DEFAULT NULL,
  `delete_flag` varchar(255) DEFAULT NULL,
  `pserver_flag` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_auxiliary` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `blockedip`
--

CREATE TABLE IF NOT EXISTS `blockedip` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `ipaddress` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_blockedip` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `client`
--

CREATE TABLE IF NOT EXISTS `client` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `priv_q_pserver_num` varchar(255) DEFAULT NULL,
  `used_q_pserver_num` varchar(255) DEFAULT NULL,
  `priv_q_client_num` varchar(255) DEFAULT NULL,
  `used_q_client_num` varchar(255) DEFAULT NULL,
  `priv_q_maindomain_num` varchar(255) DEFAULT NULL,
  `used_q_maindomain_num` varchar(255) DEFAULT NULL,
  `priv_q_domain_num` varchar(255) DEFAULT NULL,
  `used_q_domain_num` varchar(255) DEFAULT NULL,
  `priv_q_subdomain_num` varchar(255) DEFAULT NULL,
  `used_q_subdomain_num` varchar(255) DEFAULT NULL,
  `priv_q_clientdisk_usage` varchar(255) DEFAULT NULL,
  `used_q_clientdisk_usage` varchar(255) DEFAULT NULL,
  `priv_q_domain_add_flag` varchar(255) DEFAULT NULL,
  `used_q_domain_add_flag` varchar(255) DEFAULT NULL,
  `priv_q_can_change_limit_flag` varchar(255) DEFAULT NULL,
  `used_q_can_change_limit_flag` varchar(255) DEFAULT NULL,
  `priv_q_can_set_disabled_flag` varchar(255) DEFAULT NULL,
  `used_q_can_set_disabled_flag` varchar(255) DEFAULT NULL,
  `priv_q_can_change_password_flag` varchar(255) DEFAULT NULL,
  `used_q_can_change_password_flag` varchar(255) DEFAULT NULL,
  `priv_q_document_root_flag` varchar(255) DEFAULT NULL,
  `used_q_document_root_flag` varchar(255) DEFAULT NULL,
  `priv_q_runstats_flag` varchar(255) DEFAULT NULL,
  `used_q_runstats_flag` varchar(255) DEFAULT NULL,
  `priv_q_traffic_usage` varchar(255) DEFAULT NULL,
  `used_q_traffic_usage` varchar(255) DEFAULT NULL,
  `priv_q_totaldisk_usage` varchar(255) DEFAULT NULL,
  `used_q_totaldisk_usage` varchar(255) DEFAULT NULL,
  `priv_q_ssl_flag` varchar(255) DEFAULT NULL,
  `used_q_ssl_flag` varchar(255) DEFAULT NULL,
  `priv_q_rubyfcgiprocess_num` varchar(255) DEFAULT NULL,
  `used_q_rubyfcgiprocess_num` varchar(255) DEFAULT NULL,
  `priv_q_disk_usage` varchar(255) DEFAULT NULL,
  `used_q_disk_usage` varchar(255) DEFAULT NULL,
  `priv_q_logo_manage_flag` varchar(255) DEFAULT NULL,
  `used_q_logo_manage_flag` varchar(255) DEFAULT NULL,
  `priv_q_ftpuser_num` varchar(255) DEFAULT NULL,
  `used_q_ftpuser_num` varchar(255) DEFAULT NULL,
  `priv_q_frontpage_flag` varchar(255) DEFAULT NULL,
  `used_q_frontpage_flag` varchar(255) DEFAULT NULL,
  `priv_q_php_manage_flag` varchar(255) DEFAULT NULL,
  `used_q_php_manage_flag` varchar(255) DEFAULT NULL,
  `priv_q_inc_flag` varchar(255) DEFAULT NULL,
  `used_q_inc_flag` varchar(255) DEFAULT NULL,
  `priv_q_awstats_flag` varchar(255) DEFAULT NULL,
  `used_q_awstats_flag` varchar(255) DEFAULT NULL,
  `priv_q_easyinstallerp_flag` varchar(255) DEFAULT NULL,
  `used_q_easyinstallerp_flag` varchar(255) DEFAULT NULL,
  `priv_q_modperl_flag` varchar(255) DEFAULT NULL,
  `used_q_modperl_flag` varchar(255) DEFAULT NULL,
  `priv_q_cgi_flag` varchar(255) DEFAULT NULL,
  `used_q_cgi_flag` varchar(255) DEFAULT NULL,
  `priv_q_php_flag` varchar(255) DEFAULT NULL,
  `used_q_php_flag` varchar(255) DEFAULT NULL,
  `priv_q_phpunsafe_flag` varchar(255) DEFAULT NULL,
  `used_q_phpunsafe_flag` varchar(255) DEFAULT NULL,
  `priv_q_subweb_a_num` varchar(255) DEFAULT NULL,
  `used_q_subweb_a_num` varchar(255) DEFAULT NULL,
  `priv_q_dotnet_flag` varchar(255) DEFAULT NULL,
  `used_q_dotnet_flag` varchar(255) DEFAULT NULL,
  `priv_q_cron_num` varchar(255) DEFAULT NULL,
  `used_q_cron_num` varchar(255) DEFAULT NULL,
  `priv_q_cron_minute_flag` varchar(255) DEFAULT NULL,
  `used_q_cron_minute_flag` varchar(255) DEFAULT NULL,
  `priv_q_cron_manage_flag` varchar(255) DEFAULT NULL,
  `used_q_cron_manage_flag` varchar(255) DEFAULT NULL,
  `priv_q_phpfcgi_flag` varchar(255) DEFAULT NULL,
  `used_q_phpfcgi_flag` varchar(255) DEFAULT NULL,
  `priv_q_rubyrails_num` varchar(255) DEFAULT NULL,
  `used_q_rubyrails_num` varchar(255) DEFAULT NULL,
  `priv_q_phpfcgiprocess_num` varchar(255) DEFAULT NULL,
  `used_q_phpfcgiprocess_num` varchar(255) DEFAULT NULL,
  `priv_q_maildisk_usage` varchar(255) DEFAULT NULL,
  `used_q_maildisk_usage` varchar(255) DEFAULT NULL,
  `priv_q_autoresponder_num` varchar(255) DEFAULT NULL,
  `used_q_autoresponder_num` varchar(255) DEFAULT NULL,
  `priv_q_mailaccount_num` varchar(255) DEFAULT NULL,
  `used_q_mailaccount_num` varchar(255) DEFAULT NULL,
  `priv_q_mailinglist_num` varchar(255) DEFAULT NULL,
  `used_q_mailinglist_num` varchar(255) DEFAULT NULL,
  `priv_q_mysqldb_usage` varchar(255) DEFAULT NULL,
  `used_q_mysqldb_usage` varchar(255) DEFAULT NULL,
  `priv_q_mssqldb_usage` varchar(255) DEFAULT NULL,
  `used_q_mssqldb_usage` varchar(255) DEFAULT NULL,
  `priv_q_backupschedule_flag` varchar(255) DEFAULT NULL,
  `used_q_backupschedule_flag` varchar(255) DEFAULT NULL,
  `priv_q_traffic_last_usage` varchar(255) DEFAULT NULL,
  `used_q_traffic_last_usage` varchar(255) DEFAULT NULL,
  `priv_q_backup_flag` varchar(255) DEFAULT NULL,
  `used_q_backup_flag` varchar(255) DEFAULT NULL,
  `priv_q_dns_manage_flag` varchar(255) DEFAULT NULL,
  `used_q_dns_manage_flag` varchar(255) DEFAULT NULL,
  `priv_q_mysqldb_num` varchar(255) DEFAULT NULL,
  `used_q_mysqldb_num` varchar(255) DEFAULT NULL,
  `priv_q_mssqldb_num` varchar(255) DEFAULT NULL,
  `used_q_mssqldb_num` varchar(255) DEFAULT NULL,
  `priv_q_addondomain_num` varchar(255) DEFAULT NULL,
  `used_q_addondomain_num` varchar(255) DEFAULT NULL,
  `priv_q_webhosting_flag` varchar(255) DEFAULT NULL,
  `used_q_webhosting_flag` varchar(255) DEFAULT NULL,
  `realname` varchar(255) DEFAULT NULL,
  `add_address` varchar(255) DEFAULT NULL,
  `add_city` varchar(255) DEFAULT NULL,
  `add_country` varchar(255) DEFAULT NULL,
  `add_telephone` varchar(255) DEFAULT NULL,
  `add_fax` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `cpstatus` varchar(255) DEFAULT NULL,
  `demo_status` varchar(255) DEFAULT NULL,
  `contactemail` varchar(255) DEFAULT NULL,
  `text_comment` longtext DEFAULT '',
  `disable_per` varchar(255) DEFAULT NULL,
  `ser_hpfilter` longtext DEFAULT '',
  `ddate` varchar(255) DEFAULT NULL,
  `ser_dskhistory` longtext DEFAULT '',
  `ser_dskshortcut_a` longtext DEFAULT '',
  `interface_template` varchar(255) DEFAULT NULL,
  `ser_boxpos` longtext DEFAULT '',
  `dialogsize` varchar(255) DEFAULT NULL,
  `cttype` varchar(255) DEFAULT NULL,
  `ser_listpriv` longtext DEFAULT '',
  `skeletonarchive` varchar(255) DEFAULT NULL,
  `ser_dnstemplate_list` longtext DEFAULT '',
  `state` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `disable_reason` varchar(255) DEFAULT NULL,
  `disable_url` varchar(255) DEFAULT NULL,
  `template_used` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `realpass` varchar(255) DEFAULT NULL,
  `shell` varchar(255) DEFAULT NULL,
  `default_domain` varchar(255) DEFAULT NULL,
  `resourceplan_used` varchar(255) DEFAULT NULL,
  `websyncserver` varchar(255) DEFAULT NULL,
  `coma_dnssyncserver_list` text DEFAULT '',
  `mmailsyncserver` varchar(255) DEFAULT NULL,
  `mysqldbsyncserver` varchar(255) DEFAULT NULL,
  `syncserver` varchar(255) DEFAULT NULL,
  `cron_mailto` varchar(255) DEFAULT NULL,
  `dnstemplate_name` varchar(255) DEFAULT NULL,
  `corerootdir` varchar(255) DEFAULT NULL,
  `disable_system_flag` varchar(255) DEFAULT NULL,
  `oldsyncserver` varchar(255) DEFAULT NULL,
  `olddeleteflag` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_client` (`parent_clname`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `clienttemplate`
--

CREATE TABLE IF NOT EXISTS `clienttemplate` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `ser_priv` longtext DEFAULT '',
  `share_status` varchar(255) DEFAULT NULL,
  `disable_per` varchar(255) DEFAULT NULL,
  `skin_name` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `ddate` varchar(255) DEFAULT NULL,
  `ser_listpriv` longtext DEFAULT '',
  `ttype` varchar(255) DEFAULT NULL,
  `ser_dnstemplate_list` longtext DEFAULT '',
  PRIMARY KEY (`nname`),
  KEY `parent_clname_clienttemplate` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `component`
--

CREATE TABLE IF NOT EXISTS `component` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `componentname` varchar(255) DEFAULT NULL,
  `syncserver` varchar(255) DEFAULT NULL,
  `oldsyncserver` varchar(255) DEFAULT NULL,
  `olddeleteflag` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_component` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `cron`
--

CREATE TABLE IF NOT EXISTS `cron` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `syncserver` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `ser_minute` longtext DEFAULT '',
  `ser_hour` longtext DEFAULT '',
  `ser_ddate` longtext DEFAULT '',
  `ser_month` longtext DEFAULT '',
  `ser_weekday` longtext DEFAULT '',
  `jobid` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `command` varchar(255) DEFAULT NULL,
  `argument` varchar(255) DEFAULT NULL,
  `ttype` varchar(255) DEFAULT NULL,
  `simple_cron` varchar(255) DEFAULT NULL,
  `cron_day_hour` varchar(255) DEFAULT NULL,
  `oldsyncserver` varchar(255) DEFAULT NULL,
  `olddeleteflag` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_cron` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `customaction`
--

CREATE TABLE IF NOT EXISTS `customaction` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `class` varchar(255) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `subaction` varchar(255) DEFAULT NULL,
  `exec` varchar(255) DEFAULT NULL,
  `where_to_exec` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_customaction` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `custombutton`
--

CREATE TABLE IF NOT EXISTS `custombutton` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `status` varchar(255) DEFAULT NULL,
  `class` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_custombutton` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `davuser`
--

CREATE TABLE IF NOT EXISTS `davuser` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `realname` varchar(255) DEFAULT NULL,
  `add_address` varchar(255) DEFAULT NULL,
  `add_city` varchar(255) DEFAULT NULL,
  `add_country` varchar(255) DEFAULT NULL,
  `add_telephone` varchar(255) DEFAULT NULL,
  `add_fax` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `cpstatus` varchar(255) DEFAULT NULL,
  `demo_status` varchar(255) DEFAULT NULL,
  `contactemail` varchar(255) DEFAULT NULL,
  `text_comment` longtext DEFAULT '',
  `disable_per` varchar(255) DEFAULT NULL,
  `ser_hpfilter` longtext DEFAULT '',
  `ddate` varchar(255) DEFAULT NULL,
  `ser_dskhistory` longtext DEFAULT '',
  `ser_dskshortcut_a` longtext DEFAULT '',
  `interface_template` varchar(255) DEFAULT NULL,
  `ser_boxpos` longtext DEFAULT '',
  `dialogsize` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `disable_reason` varchar(255) DEFAULT NULL,
  `realpass` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `directory` varchar(255) DEFAULT NULL,
  `syncserver` varchar(255) DEFAULT NULL,
  `oldsyncserver` varchar(255) DEFAULT NULL,
  `olddeleteflag` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_davuser` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `dbadmin`
--

CREATE TABLE IF NOT EXISTS `dbadmin` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `dbtype` varchar(255) DEFAULT NULL,
  `dbadmin_name` varchar(255) DEFAULT NULL,
  `dbpassword` varchar(255) DEFAULT NULL,
  `syncserver` varchar(255) DEFAULT NULL,
  `oldsyncserver` varchar(255) DEFAULT NULL,
  `olddeleteflag` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_dbadmin` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `dirprotect`
--

CREATE TABLE IF NOT EXISTS `dirprotect` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `syncserver` varchar(255) DEFAULT NULL,
  `authname` varchar(255) DEFAULT NULL,
  `subweb` varchar(255) DEFAULT NULL,
  `path` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `ser_diruser_a` longtext DEFAULT '',
  `oldsyncserver` varchar(255) DEFAULT NULL,
  `olddeleteflag` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_dirprotect` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `dns`
--

CREATE TABLE IF NOT EXISTS `dns` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `ser_mx_rec_a` longtext DEFAULT '',
  `ser_ns_rec_a` longtext DEFAULT '',
  `ser_a_rec_a` longtext DEFAULT '',
  `ser_cn_rec_a` longtext DEFAULT '',
  `ser_txt_rec_a` longtext DEFAULT '',
  `ttl` varchar(255) DEFAULT NULL,
  `soanameserver` varchar(255) DEFAULT NULL,
  `zone_type` varchar(255) DEFAULT NULL,
  `ser_dns_record_a` longtext DEFAULT '',
  `syncserver` varchar(255) DEFAULT NULL,
  `serial` varchar(255) DEFAULT NULL,
  `oldsyncserver` varchar(255) DEFAULT NULL,
  `olddeleteflag` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_dns` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `dnstemplate`
--

CREATE TABLE IF NOT EXISTS `dnstemplate` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `ser_mx_rec_a` longtext DEFAULT '',
  `ser_ns_rec_a` longtext DEFAULT '',
  `ser_a_rec_a` longtext DEFAULT '',
  `ser_cn_rec_a` longtext DEFAULT '',
  `ser_txt_rec_a` longtext DEFAULT '',
  `ttl` varchar(255) DEFAULT NULL,
  `soanameserver` varchar(255) DEFAULT NULL,
  `zone_type` varchar(255) DEFAULT NULL,
  `ser_dns_record_a` longtext DEFAULT '',
  `webipaddress` varchar(255) DEFAULT NULL,
  `mmailipaddress` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_dnstemplate` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `domain`
--

CREATE TABLE IF NOT EXISTS `domain` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `priv_q_traffic_usage` varchar(255) DEFAULT NULL,
  `used_q_traffic_usage` varchar(255) DEFAULT NULL,
  `priv_q_totaldisk_usage` varchar(255) DEFAULT NULL,
  `used_q_totaldisk_usage` varchar(255) DEFAULT NULL,
  `priv_q_ssl_flag` varchar(255) DEFAULT NULL,
  `used_q_ssl_flag` varchar(255) DEFAULT NULL,
  `priv_q_rubyfcgiprocess_num` varchar(255) DEFAULT NULL,
  `used_q_rubyfcgiprocess_num` varchar(255) DEFAULT NULL,
  `priv_q_disk_usage` varchar(255) DEFAULT NULL,
  `used_q_disk_usage` varchar(255) DEFAULT NULL,
  `priv_q_logo_manage_flag` varchar(255) DEFAULT NULL,
  `used_q_logo_manage_flag` varchar(255) DEFAULT NULL,
  `priv_q_ftpuser_num` varchar(255) DEFAULT NULL,
  `used_q_ftpuser_num` varchar(255) DEFAULT NULL,
  `priv_q_frontpage_flag` varchar(255) DEFAULT NULL,
  `used_q_frontpage_flag` varchar(255) DEFAULT NULL,
  `priv_q_php_manage_flag` varchar(255) DEFAULT NULL,
  `used_q_php_manage_flag` varchar(255) DEFAULT NULL,
  `priv_q_inc_flag` varchar(255) DEFAULT NULL,
  `used_q_inc_flag` varchar(255) DEFAULT NULL,
  `priv_q_awstats_flag` varchar(255) DEFAULT NULL,
  `used_q_awstats_flag` varchar(255) DEFAULT NULL,
  `priv_q_easyinstallerp_flag` varchar(255) DEFAULT NULL,
  `used_q_easyinstallerp_flag` varchar(255) DEFAULT NULL,
  `priv_q_modperl_flag` varchar(255) DEFAULT NULL,
  `used_q_modperl_flag` varchar(255) DEFAULT NULL,
  `priv_q_cgi_flag` varchar(255) DEFAULT NULL,
  `used_q_cgi_flag` varchar(255) DEFAULT NULL,
  `priv_q_php_flag` varchar(255) DEFAULT NULL,
  `used_q_php_flag` varchar(255) DEFAULT NULL,
  `priv_q_phpunsafe_flag` varchar(255) DEFAULT NULL,
  `used_q_phpunsafe_flag` varchar(255) DEFAULT NULL,
  `priv_q_subweb_a_num` varchar(255) DEFAULT NULL,
  `used_q_subweb_a_num` varchar(255) DEFAULT NULL,
  `priv_q_dotnet_flag` varchar(255) DEFAULT NULL,
  `used_q_dotnet_flag` varchar(255) DEFAULT NULL,
  `priv_q_cron_num` varchar(255) DEFAULT NULL,
  `used_q_cron_num` varchar(255) DEFAULT NULL,
  `priv_q_cron_minute_flag` varchar(255) DEFAULT NULL,
  `used_q_cron_minute_flag` varchar(255) DEFAULT NULL,
  `priv_q_cron_manage_flag` varchar(255) DEFAULT NULL,
  `used_q_cron_manage_flag` varchar(255) DEFAULT NULL,
  `priv_q_phpfcgi_flag` varchar(255) DEFAULT NULL,
  `used_q_phpfcgi_flag` varchar(255) DEFAULT NULL,
  `priv_q_rubyrails_num` varchar(255) DEFAULT NULL,
  `used_q_rubyrails_num` varchar(255) DEFAULT NULL,
  `priv_q_phpfcgiprocess_num` varchar(255) DEFAULT NULL,
  `used_q_phpfcgiprocess_num` varchar(255) DEFAULT NULL,
  `priv_q_maildisk_usage` varchar(255) DEFAULT NULL,
  `used_q_maildisk_usage` varchar(255) DEFAULT NULL,
  `priv_q_autoresponder_num` varchar(255) DEFAULT NULL,
  `used_q_autoresponder_num` varchar(255) DEFAULT NULL,
  `priv_q_mailaccount_num` varchar(255) DEFAULT NULL,
  `used_q_mailaccount_num` varchar(255) DEFAULT NULL,
  `priv_q_mailinglist_num` varchar(255) DEFAULT NULL,
  `used_q_mailinglist_num` varchar(255) DEFAULT NULL,
  `priv_q_mysqldb_usage` varchar(255) DEFAULT NULL,
  `used_q_mysqldb_usage` varchar(255) DEFAULT NULL,
  `priv_q_mssqldb_usage` varchar(255) DEFAULT NULL,
  `used_q_mssqldb_usage` varchar(255) DEFAULT NULL,
  `priv_q_backupschedule_flag` varchar(255) DEFAULT NULL,
  `used_q_backupschedule_flag` varchar(255) DEFAULT NULL,
  `priv_q_traffic_last_usage` varchar(255) DEFAULT NULL,
  `used_q_traffic_last_usage` varchar(255) DEFAULT NULL,
  `priv_q_backup_flag` varchar(255) DEFAULT NULL,
  `used_q_backup_flag` varchar(255) DEFAULT NULL,
  `priv_q_dns_manage_flag` varchar(255) DEFAULT NULL,
  `used_q_dns_manage_flag` varchar(255) DEFAULT NULL,
  `priv_q_mysqldb_num` varchar(255) DEFAULT NULL,
  `used_q_mysqldb_num` varchar(255) DEFAULT NULL,
  `priv_q_mssqldb_num` varchar(255) DEFAULT NULL,
  `used_q_mssqldb_num` varchar(255) DEFAULT NULL,
  `priv_q_addondomain_num` varchar(255) DEFAULT NULL,
  `used_q_addondomain_num` varchar(255) DEFAULT NULL,
  `realname` varchar(255) DEFAULT NULL,
  `add_address` varchar(255) DEFAULT NULL,
  `add_city` varchar(255) DEFAULT NULL,
  `add_country` varchar(255) DEFAULT NULL,
  `add_telephone` varchar(255) DEFAULT NULL,
  `add_fax` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `cpstatus` varchar(255) DEFAULT NULL,
  `demo_status` varchar(255) DEFAULT NULL,
  `contactemail` varchar(255) DEFAULT NULL,
  `text_comment` longtext DEFAULT '',
  `disable_per` varchar(255) DEFAULT NULL,
  `ser_hpfilter` longtext DEFAULT '',
  `ddate` varchar(255) DEFAULT NULL,
  `ser_dskhistory` longtext DEFAULT '',
  `ser_dskshortcut_a` longtext DEFAULT '',
  `interface_template` varchar(255) DEFAULT NULL,
  `ser_boxpos` longtext DEFAULT '',
  `dialogsize` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `disable_reason` varchar(255) DEFAULT NULL,
  `ser_listpriv` longtext DEFAULT '',
  `mmailpserver` varchar(255) DEFAULT NULL,
  `webpserver` varchar(255) DEFAULT NULL,
  `dnspserver` varchar(255) DEFAULT NULL,
  `secdnspserver` varchar(255) DEFAULT NULL,
  `ttype` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `nameserver` varchar(255) DEFAULT NULL,
  `redirect_domain` varchar(255) DEFAULT NULL,
  `template_used` varchar(255) DEFAULT NULL,
  `realpass` varchar(255) DEFAULT NULL,
  `dtype` varchar(255) DEFAULT NULL,
  `subdomain_parent` varchar(255) DEFAULT NULL,
  `resourceplan_used` varchar(255) DEFAULT NULL,
  `previewdomain` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_domain` (`parent_clname`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `domaindefault`
--

CREATE TABLE IF NOT EXISTS `domaindefault` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `remove_processed_stats` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_domaindefault` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `domainipaddress`
--

CREATE TABLE IF NOT EXISTS `domainipaddress` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `domain` varchar(255) DEFAULT NULL,
  `ipaddr` varchar(255) DEFAULT NULL,
  `syncserver` varchar(255) DEFAULT NULL,
  `oldsyncserver` varchar(255) DEFAULT NULL,
  `olddeleteflag` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_domainipaddress` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `domaintemplate`
--

CREATE TABLE IF NOT EXISTS `domaintemplate` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `ser_priv` longtext DEFAULT '',
  `share_status` varchar(255) DEFAULT NULL,
  `disable_per` varchar(255) DEFAULT NULL,
  `skin_name` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `ddate` varchar(255) DEFAULT NULL,
  `ser_listpriv` longtext DEFAULT '',
  `ttype` varchar(255) DEFAULT NULL,
  `ser_dnstemplate_list` longtext DEFAULT '',
  `dnstemplate` varchar(255) DEFAULT NULL,
  `ipaddress` varchar(255) DEFAULT NULL,
  `redirect_domain` varchar(255) DEFAULT NULL,
  `catchall` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_domaintemplate` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `domaintraffic`
--

CREATE TABLE IF NOT EXISTS `domaintraffic` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `ddate` varchar(255) DEFAULT NULL,
  `oldtimestamp` varchar(255) DEFAULT NULL,
  `timestamp` varchar(255) DEFAULT NULL,
  `webtraffic_usage` varchar(255) DEFAULT NULL,
  `mailtraffic_usage` varchar(255) DEFAULT NULL,
  `ftptraffic_usage` varchar(255) DEFAULT NULL,
  `traffic_usage` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_domaintraffic` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `driver`
--

CREATE TABLE IF NOT EXISTS `driver` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `ser_driver_b` longtext DEFAULT '',
  PRIMARY KEY (`nname`),
  KEY `parent_clname_driver` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `firewall`
--

CREATE TABLE IF NOT EXISTS `firewall` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `status` varchar(255) DEFAULT NULL,
  `id` varchar(255) DEFAULT NULL,
  `syncserver` varchar(255) DEFAULT NULL,
  `from_address` varchar(255) DEFAULT NULL,
  `from_port` varchar(255) DEFAULT NULL,
  `to_address` varchar(255) DEFAULT NULL,
  `to_port` varchar(255) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `oldsyncserver` varchar(255) DEFAULT NULL,
  `olddeleteflag` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_firewall` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ftpuser`
--

CREATE TABLE IF NOT EXISTS `ftpuser` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `priv_q_logo_manage_flag` varchar(255) DEFAULT NULL,
  `used_q_logo_manage_flag` varchar(255) DEFAULT NULL,
  `realname` varchar(255) DEFAULT NULL,
  `add_address` varchar(255) DEFAULT NULL,
  `add_city` varchar(255) DEFAULT NULL,
  `add_country` varchar(255) DEFAULT NULL,
  `add_telephone` varchar(255) DEFAULT NULL,
  `add_fax` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `cpstatus` varchar(255) DEFAULT NULL,
  `demo_status` varchar(255) DEFAULT NULL,
  `contactemail` varchar(255) DEFAULT NULL,
  `text_comment` longtext DEFAULT '',
  `disable_per` varchar(255) DEFAULT NULL,
  `ser_hpfilter` longtext DEFAULT '',
  `ddate` varchar(255) DEFAULT NULL,
  `ser_dskhistory` longtext DEFAULT '',
  `ser_dskshortcut_a` longtext DEFAULT '',
  `interface_template` varchar(255) DEFAULT NULL,
  `ser_boxpos` longtext DEFAULT '',
  `dialogsize` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `disable_reason` varchar(255) DEFAULT NULL,
  `realpass` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `directory` varchar(255) DEFAULT NULL,
  `syncserver` varchar(255) DEFAULT NULL,
  `ftp_disk_usage` varchar(255) DEFAULT NULL,
  `oldsyncserver` varchar(255) DEFAULT NULL,
  `olddeleteflag` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_ftpuser` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `general`
--

CREATE TABLE IF NOT EXISTS `general` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `ser_generalmisc_b` longtext DEFAULT '',
  `ser_helpdeskcategory_a` longtext DEFAULT '',
  `ser_reversedns_b` longtext DEFAULT '',
  `ser_selfbackupparam_b` longtext DEFAULT '',
  `ser_hackbuttonconfig_b` longtext DEFAULT '',
  `ser_customaction_b` longtext DEFAULT '',
  `text_maintenance_message` longtext DEFAULT '',
  `ser_portconfig_b` longtext DEFAULT '',
  `ser_kloxoconfig_b` longtext DEFAULT '',
  `ser_browsebackup_b` longtext DEFAULT '',
  `login_pre` varchar(255) DEFAULT NULL,
  `ser_lxadminconfig_b` longtext DEFAULT '',
  `disable_admin` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_general` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `genlist`
--

CREATE TABLE IF NOT EXISTS `genlist` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `ser_dirindexlist_a` longtext DEFAULT '',
  PRIMARY KEY (`nname`),
  KEY `parent_clname_genlist` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `hostdeny`
--

CREATE TABLE IF NOT EXISTS `hostdeny` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `syncserver` varchar(255) DEFAULT NULL,
  `hostname` varchar(255) DEFAULT NULL,
  `oldsyncserver` varchar(255) DEFAULT NULL,
  `olddeleteflag` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_hostdeny` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `installsoft`
--

CREATE TABLE IF NOT EXISTS `installsoft` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `appname` varchar(255) DEFAULT NULL,
  `ddate` varchar(255) DEFAULT NULL,
  `dbprefix` varchar(255) DEFAULT NULL,
  `dbname` varchar(255) DEFAULT NULL,
  `installdir` varchar(255) DEFAULT NULL,
  `version` varchar(255) DEFAULT NULL,
  `dbhost` varchar(255) DEFAULT NULL,
  `realhost` varchar(255) DEFAULT NULL,
  `ser_installsoftmisc_b` longtext DEFAULT '',
  `syncserver` varchar(255) DEFAULT NULL,
  `oldsyncserver` varchar(255) DEFAULT NULL,
  `olddeleteflag` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_installsoft` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `interface_template`
--

CREATE TABLE IF NOT EXISTS `interface_template` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `ser_domain_show` text DEFAULT '',
  `ser_client_show` text DEFAULT '',
  `ser_vps_show` text DEFAULT '',
  `ser_domain_show_list` text DEFAULT '',
  `ser_client_show_list` text DEFAULT '',
  `ser_vps_show_list` text DEFAULT '',
  PRIMARY KEY (`nname`),
  KEY `parent_clname_interface_template` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ipaddress`
--

CREATE TABLE IF NOT EXISTS `ipaddress` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `devname` varchar(255) DEFAULT NULL,
  `bproto` varchar(255) DEFAULT NULL,
  `ipaddr` varchar(255) DEFAULT NULL,
  `client_num` varchar(255) DEFAULT NULL,
  `shared` varchar(255) DEFAULT NULL,
  `netmask` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `userctl` varchar(255) DEFAULT NULL,
  `peerdns` varchar(255) DEFAULT NULL,
  `gateway` varchar(255) DEFAULT NULL,
  `itype` varchar(255) DEFAULT NULL,
  `ipv6init` varchar(255) DEFAULT NULL,
  `syncserver` varchar(255) DEFAULT NULL,
  `clientname` varchar(255) DEFAULT NULL,
  `oldsyncserver` varchar(255) DEFAULT NULL,
  `olddeleteflag` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_ipaddress` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `license`
--

CREATE TABLE IF NOT EXISTS `license` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `ser_licensecom_b` longtext DEFAULT '',
  `text_license_content` longtext DEFAULT '',
  PRIMARY KEY (`nname`),
  KEY `parent_clname_license` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `llog`
--

CREATE TABLE IF NOT EXISTS `llog` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `syncserver` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `period` varchar(255) DEFAULT NULL,
  `oldsyncserver` varchar(255) DEFAULT NULL,
  `olddeleteflag` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_llog` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `loginattempt`
--

CREATE TABLE IF NOT EXISTS `loginattempt` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `count` varchar(255) DEFAULT NULL,
  `client_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_loginattempt` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `lxbackup`
--

CREATE TABLE IF NOT EXISTS `lxbackup` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `priv_q_backupschedule_flag` varchar(255) DEFAULT NULL,
  `used_q_backupschedule_flag` varchar(255) DEFAULT NULL,
  `ftp_server` varchar(255) DEFAULT NULL,
  `ssh_server` varchar(255) DEFAULT NULL,
  `rm_username` varchar(255) DEFAULT NULL,
  `rm_password` varchar(255) DEFAULT NULL,
  `rm_directory` varchar(255) DEFAULT NULL,
  `upload_type` varchar(255) DEFAULT NULL,
  `send_email` varchar(255) DEFAULT NULL,
  `upload_to_ftp` varchar(255) DEFAULT NULL,
  `backupstage` varchar(255) DEFAULT NULL,
  `backuptype` varchar(255) DEFAULT NULL,
  `backupschedule_type` varchar(255) DEFAULT NULL,
  `rm_last_number` varchar(255) DEFAULT NULL,
  `ser_lxbackupmisc_b` longtext DEFAULT '',
  `restorestage` varchar(255) DEFAULT NULL,
  `no_local_copy_flag` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_lxbackup` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `lxguard`
--

CREATE TABLE IF NOT EXISTS `lxguard` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `configure_flag` varchar(255) DEFAULT NULL,
  `disablehit` varchar(255) DEFAULT NULL,
  `syncserver` varchar(255) DEFAULT NULL,
  `oldsyncserver` varchar(255) DEFAULT NULL,
  `olddeleteflag` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_lxguard` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `lxguardhit`
--

CREATE TABLE IF NOT EXISTS `lxguardhit` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `access` varchar(255) DEFAULT NULL,
  `service` varchar(255) DEFAULT NULL,
  `ddate` varchar(255) DEFAULT NULL,
  `ipaddress` varchar(255) DEFAULT NULL,
  `user` varchar(255) DEFAULT NULL,
  `syncserver` varchar(255) DEFAULT NULL,
  `oldsyncserver` varchar(255) DEFAULT NULL,
  `olddeleteflag` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_lxguardhit` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `lxguardwhitelist`
--

CREATE TABLE IF NOT EXISTS `lxguardwhitelist` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `ipaddress` varchar(255) DEFAULT NULL,
  `syncserver` varchar(255) DEFAULT NULL,
  `oldsyncserver` varchar(255) DEFAULT NULL,
  `olddeleteflag` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_lxguardwhitelist` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `lxupdate`
--

CREATE TABLE IF NOT EXISTS `lxupdate` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `schedule` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_lxupdate` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mailaccount`
--

CREATE TABLE IF NOT EXISTS `mailaccount` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `priv_q_logo_manage_flag` varchar(255) DEFAULT NULL,
  `used_q_logo_manage_flag` varchar(255) DEFAULT NULL,
  `priv_q_maildisk_usage` varchar(255) DEFAULT NULL,
  `used_q_maildisk_usage` varchar(255) DEFAULT NULL,
  `priv_q_autoresponder_num` varchar(255) DEFAULT NULL,
  `used_q_autoresponder_num` varchar(255) DEFAULT NULL,
  `realname` varchar(255) DEFAULT NULL,
  `add_address` varchar(255) DEFAULT NULL,
  `add_city` varchar(255) DEFAULT NULL,
  `add_country` varchar(255) DEFAULT NULL,
  `add_telephone` varchar(255) DEFAULT NULL,
  `add_fax` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `cpstatus` varchar(255) DEFAULT NULL,
  `demo_status` varchar(255) DEFAULT NULL,
  `contactemail` varchar(255) DEFAULT NULL,
  `text_comment` longtext DEFAULT '',
  `disable_per` varchar(255) DEFAULT NULL,
  `ser_hpfilter` longtext DEFAULT '',
  `ddate` varchar(255) DEFAULT NULL,
  `ser_dskhistory` longtext DEFAULT '',
  `ser_dskshortcut_a` longtext DEFAULT '',
  `interface_template` varchar(255) DEFAULT NULL,
  `ser_boxpos` longtext DEFAULT '',
  `dialogsize` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `disable_reason` varchar(255) DEFAULT NULL,
  `realpass` varchar(255) DEFAULT NULL,
  `syncserver` varchar(255) DEFAULT NULL,
  `forward_status` varchar(255) DEFAULT NULL,
  `ser_forward_a` longtext DEFAULT '',
  `autorespond_status` varchar(255) DEFAULT NULL,
  `autores_name` varchar(255) DEFAULT NULL,
  `filter_spam_status` varchar(255) DEFAULT NULL,
  `no_local_copy` varchar(255) DEFAULT NULL,
  `oldsyncserver` varchar(255) DEFAULT NULL,
  `olddeleteflag` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_mailaccount` (`parent_clname`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mailfilter`
--

CREATE TABLE IF NOT EXISTS `mailfilter` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `rule` varchar(255) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_mailfilter` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mailforward`
--

CREATE TABLE IF NOT EXISTS `mailforward` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `accountname` varchar(255) DEFAULT NULL,
  `forwardaddress` varchar(255) DEFAULT NULL,
  `syncserver` varchar(255) DEFAULT NULL,
  `oldsyncserver` varchar(255) DEFAULT NULL,
  `olddeleteflag` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_mailforward` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mailinglist`
--

CREATE TABLE IF NOT EXISTS `mailinglist` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `syncserver` varchar(255) DEFAULT NULL,
  `listname` varchar(255) DEFAULT NULL,
  `adminemail` varchar(255) DEFAULT NULL,
  `lang` varchar(255) DEFAULT NULL,
  `post_members_only_flag` varchar(255) DEFAULT NULL,
  `post_moderated_flag` varchar(255) DEFAULT NULL,
  `post_moderator_only_flag` varchar(255) DEFAULT NULL,
  `archived_flag` varchar(255) DEFAULT NULL,
  `archive_blocked_flag` varchar(255) DEFAULT NULL,
  `archive_guarded_flag` varchar(255) DEFAULT NULL,
  `digest_flag` varchar(255) DEFAULT NULL,
  `jumpoff_flag` varchar(255) DEFAULT NULL,
  `subscriberlist_flag` varchar(255) DEFAULT NULL,
  `remote_admin_flag` varchar(255) DEFAULT NULL,
  `subscription_mod_flag` varchar(255) DEFAULT NULL,
  `edit_text_flag` varchar(255) DEFAULT NULL,
  `coma_mailinglist_mod_a` text DEFAULT '',
  `text_trailer` longtext DEFAULT '',
  `text_prefix` longtext DEFAULT '',
  `max_msg_size` varchar(255) DEFAULT NULL,
  `min_msg_size` varchar(255) DEFAULT NULL,
  `text_mimeremove` longtext DEFAULT '',
  `oldsyncserver` varchar(255) DEFAULT NULL,
  `olddeleteflag` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_mailinglist` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mimetype`
--

CREATE TABLE IF NOT EXISTS `mimetype` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `domainname` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `extension` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_mimetype` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mmail`
--

CREATE TABLE IF NOT EXISTS `mmail` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `priv_q_logo_manage_flag` varchar(255) DEFAULT NULL,
  `used_q_logo_manage_flag` varchar(255) DEFAULT NULL,
  `priv_q_maildisk_usage` varchar(255) DEFAULT NULL,
  `used_q_maildisk_usage` varchar(255) DEFAULT NULL,
  `priv_q_autoresponder_num` varchar(255) DEFAULT NULL,
  `used_q_autoresponder_num` varchar(255) DEFAULT NULL,
  `priv_q_mailaccount_num` varchar(255) DEFAULT NULL,
  `used_q_mailaccount_num` varchar(255) DEFAULT NULL,
  `priv_q_mailinglist_num` varchar(255) DEFAULT NULL,
  `used_q_mailinglist_num` varchar(255) DEFAULT NULL,
  `webmailprog` varchar(255) DEFAULT NULL,
  `catchall` varchar(255) DEFAULT NULL,
  `remotelocalflag` varchar(255) DEFAULT NULL,
  `catchall_status` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `redirect_address` varchar(255) DEFAULT NULL,
  `syncserver` varchar(255) DEFAULT NULL,
  `ttype` varchar(255) DEFAULT NULL,
  `redirect_domain` varchar(255) DEFAULT NULL,
  `webmail_url` varchar(255) DEFAULT NULL,
  `systemuser` varchar(255) DEFAULT NULL,
  `enable_spf_flag` varchar(255) DEFAULT NULL,
  `exclude_all` varchar(255) DEFAULT NULL,
  `text_spf_domain` longtext DEFAULT '',
  `text_spf_ip` longtext DEFAULT '',
  `oldsyncserver` varchar(255) DEFAULT NULL,
  `olddeleteflag` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_mmail` (`parent_clname`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `module`
--

CREATE TABLE IF NOT EXISTS `module` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `status` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_module` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mssqldb`
--

CREATE TABLE IF NOT EXISTS `mssqldb` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `priv_q_mssqldb_usage` varchar(255) DEFAULT NULL,
  `used_q_mssqldb_usage` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `dbname` varchar(255) DEFAULT NULL,
  `dbtype` varchar(255) DEFAULT NULL,
  `dbpassword` varchar(255) DEFAULT NULL,
  `installsoft_flag` varchar(255) DEFAULT NULL,
  `installsoft_app` varchar(255) DEFAULT NULL,
  `syncserver` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `oldsyncserver` varchar(255) DEFAULT NULL,
  `olddeleteflag` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_mssqldb` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mssqldbuser`
--

CREATE TABLE IF NOT EXISTS `mssqldbuser` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `username` varchar(255) DEFAULT NULL,
  `dbname` varchar(255) DEFAULT NULL,
  `dbpassword` varchar(255) DEFAULT NULL,
  `ser_dbpermission_b` longtext DEFAULT '',
  `syncserver` varchar(255) DEFAULT NULL,
  `ser_dbhostlist_a` longtext DEFAULT '',
  `password` varchar(255) DEFAULT NULL,
  `oldsyncserver` varchar(255) DEFAULT NULL,
  `olddeleteflag` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_mssqldbuser` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mysqldb`
--

CREATE TABLE IF NOT EXISTS `mysqldb` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `priv_q_mysqldb_usage` varchar(255) DEFAULT NULL,
  `used_q_mysqldb_usage` varchar(255) DEFAULT NULL,
  `primarydb` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `dbname` varchar(255) DEFAULT NULL,
  `dbtype` varchar(255) DEFAULT NULL,
  `dbpassword` varchar(255) DEFAULT NULL,
  `installsoft_flag` varchar(255) DEFAULT NULL,
  `installsoft_app` varchar(255) DEFAULT NULL,
  `syncserver` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `no_backup_flag` varchar(255) DEFAULT NULL,
  `oldsyncserver` varchar(255) DEFAULT NULL,
  `olddeleteflag` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_mysqldb` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mysqldbuser`
--

CREATE TABLE IF NOT EXISTS `mysqldbuser` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `username` varchar(255) DEFAULT NULL,
  `dbname` varchar(255) DEFAULT NULL,
  `dbpassword` varchar(255) DEFAULT NULL,
  `ser_dbpermission_b` longtext DEFAULT '',
  `syncserver` varchar(255) DEFAULT NULL,
  `ser_dbhostlist_a` longtext DEFAULT '',
  `password` varchar(255) DEFAULT NULL,
  `oldsyncserver` varchar(255) DEFAULT NULL,
  `olddeleteflag` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_mysqldbuser` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ndskshortcut`
--

CREATE TABLE IF NOT EXISTS `ndskshortcut` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `ddate` varchar(255) DEFAULT NULL,
  `ttype` varchar(255) DEFAULT NULL,
  `sortid` varchar(255) DEFAULT NULL,
  `separatorid` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `external` varchar(255) DEFAULT NULL,
  `vpsparent_clname` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_ndskshortcut` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ndsktoolbar`
--

CREATE TABLE IF NOT EXISTS `ndsktoolbar` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `ddate` varchar(255) DEFAULT NULL,
  `ttype` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `external` varchar(255) DEFAULT NULL,
  `vpsparent_clname` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_ndsktoolbar` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE IF NOT EXISTS `notification` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `ser_notflag_b` longtext DEFAULT '',
  `text_newsubject` longtext DEFAULT '',
  `text_newaccountmessage` longtext DEFAULT '',
  `fromaddress` varchar(255) DEFAULT NULL,
  `coma_class_list` text DEFAULT '',
  PRIMARY KEY (`nname`),
  KEY `parent_clname_notification` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `odbc`
--

CREATE TABLE IF NOT EXISTS `odbc` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `odbcname` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `driver` varchar(255) DEFAULT NULL,
  `ser_odbcdetails_b` longtext DEFAULT '',
  `syncserver` varchar(255) DEFAULT NULL,
  `oldsyncserver` varchar(255) DEFAULT NULL,
  `olddeleteflag` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_odbc` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `phpini`
--

CREATE TABLE IF NOT EXISTS `phpini` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `php_manage_flag` varchar(255) DEFAULT NULL,
  `enable_zend_flag` varchar(255) DEFAULT NULL,
  `enable_ioncube_flag` varchar(255) DEFAULT NULL,
  `register_global_flag` varchar(255) DEFAULT NULL,
  `syncserver` varchar(255) DEFAULT NULL,
  `display_error_flag` varchar(255) DEFAULT NULL,
  `ser_phpini_flag_b` longtext DEFAULT '',
  `oldsyncserver` varchar(255) DEFAULT NULL,
  `olddeleteflag` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_phpini` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `proxy`
--

CREATE TABLE IF NOT EXISTS `proxy` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  PRIMARY KEY (`nname`),
  KEY `parent_clname_proxy` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `proxyacl`
--

CREATE TABLE IF NOT EXISTS `proxyacl` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `syncserver` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `ttype` varchar(255) DEFAULT NULL,
  `classid` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `http` varchar(255) DEFAULT NULL,
  `ftp` varchar(255) DEFAULT NULL,
  `oldsyncserver` varchar(255) DEFAULT NULL,
  `olddeleteflag` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_proxyacl` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pserver`
--

CREATE TABLE IF NOT EXISTS `pserver` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `priv_q_logo_manage_flag` varchar(255) DEFAULT NULL,
  `used_q_logo_manage_flag` varchar(255) DEFAULT NULL,
  `realname` varchar(255) DEFAULT NULL,
  `add_address` varchar(255) DEFAULT NULL,
  `add_city` varchar(255) DEFAULT NULL,
  `add_country` varchar(255) DEFAULT NULL,
  `add_telephone` varchar(255) DEFAULT NULL,
  `add_fax` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `cpstatus` varchar(255) DEFAULT NULL,
  `demo_status` varchar(255) DEFAULT NULL,
  `contactemail` varchar(255) DEFAULT NULL,
  `text_comment` longtext DEFAULT '',
  `disable_per` varchar(255) DEFAULT NULL,
  `ser_hpfilter` longtext DEFAULT '',
  `ddate` varchar(255) DEFAULT NULL,
  `ser_dskhistory` longtext DEFAULT '',
  `ser_dskshortcut_a` longtext DEFAULT '',
  `interface_template` varchar(255) DEFAULT NULL,
  `ser_boxpos` longtext DEFAULT '',
  `dialogsize` varchar(255) DEFAULT NULL,
  `ostype` varchar(255) DEFAULT NULL,
  `osversion` varchar(255) DEFAULT NULL,
  `dbadmin` varchar(255) DEFAULT NULL,
  `dbpassword` varchar(255) DEFAULT NULL,
  `realpass` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `ser_rolelist` longtext DEFAULT '',
  `cron_mailto` varchar(255) DEFAULT NULL,
  `syncserver` varchar(255) DEFAULT NULL,
  `ser_pserverconf_b` longtext DEFAULT '',
  `hostname` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `realhostname` varchar(255) DEFAULT NULL,
  `timezone` varchar(255) DEFAULT NULL,
  `coma_psrole_a` text DEFAULT '',
  `load_threshold` varchar(255) DEFAULT NULL,
  `oldsyncserver` varchar(255) DEFAULT NULL,
  `olddeleteflag` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_pserver` (`parent_clname`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `rdnsrange`
--

CREATE TABLE IF NOT EXISTS `rdnsrange` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `firstip` varchar(255) DEFAULT NULL,
  `lastip` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_rdnsrange` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `resourceplan`
--

CREATE TABLE IF NOT EXISTS `resourceplan` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `realname` varchar(255) DEFAULT NULL,
  `ser_priv` longtext DEFAULT '',
  `description` varchar(255) DEFAULT NULL,
  `ddate` varchar(255) DEFAULT NULL,
  `ser_listpriv` longtext DEFAULT '',
  `ser_dnstemplate_list` longtext DEFAULT '',
  `status` varchar(255) DEFAULT NULL,
  `disable_per` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_resourceplan` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `reversedns`
--

CREATE TABLE IF NOT EXISTS `reversedns` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `reversename` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_reversedns` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `rubyrails`
--

CREATE TABLE IF NOT EXISTS `rubyrails` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `priv_q_rubyfcgiprocess_num` varchar(255) DEFAULT NULL,
  `used_q_rubyfcgiprocess_num` varchar(255) DEFAULT NULL,
  `appname` varchar(255) DEFAULT NULL,
  `port` varchar(255) DEFAULT NULL,
  `syncserver` varchar(255) DEFAULT NULL,
  `accessible_directly` varchar(255) DEFAULT NULL,
  `oldsyncserver` varchar(255) DEFAULT NULL,
  `olddeleteflag` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_rubyrails` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `serverftp`
--

CREATE TABLE IF NOT EXISTS `serverftp` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `maxclient` varchar(255) DEFAULT NULL,
  `highport` varchar(255) DEFAULT NULL,
  `lowport` varchar(255) DEFAULT NULL,
  `enable_anon_ftp` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_serverftp` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `servermail`
--

CREATE TABLE IF NOT EXISTS `servermail` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `smtp_instance` varchar(255) DEFAULT NULL,
  `enable_maps` varchar(255) DEFAULT NULL,
  `domainkey_flag` varchar(255) DEFAULT NULL,
  `additional_smtp_port` varchar(255) DEFAULT NULL,
  `queuelifetime` varchar(255) DEFAULT NULL,
  `concurrencyremote` varchar(255) DEFAULT NULL,
  `spamdyke_flag` varchar(255) DEFAULT NULL,
  `syncserver` varchar(255) DEFAULT NULL,
  `myname` varchar(255) DEFAULT NULL,
  `virus_scan_flag` varchar(255) DEFAULT NULL,
  `max_size` varchar(255) DEFAULT NULL,
  `greet_delay` varchar(255) DEFAULT NULL,
  `graylist_flag` varchar(255) DEFAULT NULL,
  `graylist_max_secs` varchar(255) DEFAULT NULL,
  `graylist_min_secs` varchar(255) DEFAULT NULL,
  `coma_mail_graylist_wlist_a` text DEFAULT '',
  `max_rcpnts` varchar(255) DEFAULT NULL,
  `reject_unresolvable_rdns_flag` varchar(255) DEFAULT NULL,
  `reject_missing_sender_mx_flag` varchar(255) DEFAULT NULL,
  `reject_ip_in_cc_rdns_flag` varchar(255) DEFAULT NULL,
  `reject_empty_rdns_flag` varchar(255) DEFAULT NULL,
  `dns_blacklists` varchar(255) DEFAULT NULL,
  `blacklist_headers` varchar(1023) DEFAULT NULL,
  `alt_smtp_sdyke_flag` varchar(255) DEFAULT NULL,
  `oldsyncserver` varchar(255) DEFAULT NULL,
  `olddeleteflag` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_servermail` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `serverspam`
--

CREATE TABLE IF NOT EXISTS `serverspam` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `status` varchar(255) DEFAULT NULL,
  `spam_hit` varchar(255) DEFAULT NULL,
  `subject_tag` varchar(255) DEFAULT NULL,
  `ser_wlist_a` longtext DEFAULT '',
  `ser_blist_a` longtext DEFAULT '',
  `syncserver` varchar(255) DEFAULT NULL,
  `oldsyncserver` varchar(255) DEFAULT NULL,
  `olddeleteflag` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_serverspam` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `serverweb`
--

CREATE TABLE IF NOT EXISTS `serverweb` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `php_type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_serverweb` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `service`
--

CREATE TABLE IF NOT EXISTS `service` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `servicename` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `grepstring` varchar(255) DEFAULT NULL,
  `syncserver` varchar(255) DEFAULT NULL,
  `oldsyncserver` varchar(255) DEFAULT NULL,
  `olddeleteflag` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_service` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `skipbackup`
--

CREATE TABLE IF NOT EXISTS `skipbackup` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `clname` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_skipbackup` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `smessage`
--

CREATE TABLE IF NOT EXISTS `smessage` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `made_by` varchar(255) DEFAULT NULL,
  `text_readby_cmlist` longtext DEFAULT '',
  `text_sent_to_cmlist` longtext DEFAULT '',
  `subject` varchar(255) DEFAULT NULL,
  `text_description` longtext DEFAULT '',
  `category` varchar(255) DEFAULT NULL,
  `ddate` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_smessage` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `spam`
--

CREATE TABLE IF NOT EXISTS `spam` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `status` varchar(255) DEFAULT NULL,
  `spam_hit` varchar(255) DEFAULT NULL,
  `subject_tag` varchar(255) DEFAULT NULL,
  `ser_wlist_a` longtext DEFAULT '',
  `ser_blist_a` longtext DEFAULT '',
  `syncserver` varchar(255) DEFAULT NULL,
  `oldsyncserver` varchar(255) DEFAULT NULL,
  `olddeleteflag` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_spam` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sp_childspecialplay`
--

CREATE TABLE IF NOT EXISTS `sp_childspecialplay` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `ser_specialplay_b` longtext DEFAULT '',
  PRIMARY KEY (`nname`),
  KEY `parent_clname_sp_childspecialplay` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sp_lstclass`
--

CREATE TABLE IF NOT EXISTS `sp_lstclass` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `ser_lst_client_list` longtext DEFAULT '',
  `ser_lst_vps_list` longtext DEFAULT '',
  PRIMARY KEY (`nname`),
  KEY `parent_clname_sp_lstclass` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sp_specialplay`
--

CREATE TABLE IF NOT EXISTS `sp_specialplay` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `ser_specialplay_b` longtext DEFAULT '',
  PRIMARY KEY (`nname`),
  KEY `parent_clname_sp_specialplay` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ssession`
--

CREATE TABLE IF NOT EXISTS `ssession` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `cttype` varchar(255) DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `timeout` varchar(255) DEFAULT NULL,
  `last_access` varchar(255) DEFAULT NULL,
  `logintime` varchar(255) DEFAULT NULL,
  `ser_http_vars` longtext DEFAULT '',
  `ser_ssession_vars` longtext DEFAULT '',
  `tsessionid` varchar(255) DEFAULT NULL,
  `auxiliary_id` varchar(255) DEFAULT NULL,
  `ser_ssl_param` longtext DEFAULT '',
  `consuming_parent` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_ssession` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sshconfig`
--

CREATE TABLE IF NOT EXISTS `sshconfig` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `ssh_port` varchar(255) DEFAULT NULL,
  `without_password_flag` varchar(255) DEFAULT NULL,
  `disable_password_flag` varchar(255) DEFAULT NULL,
  `config_flag` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_sshconfig` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sslcert`
--

CREATE TABLE IF NOT EXISTS `sslcert` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `upload_status` varchar(255) DEFAULT NULL,
  `certname` varchar(255) DEFAULT NULL,
  `syncserver` varchar(255) DEFAULT NULL,
  `ser_ssl_data_b` longtext DEFAULT '',
  `text_crt_content` longtext DEFAULT '',
  `text_key_content` longtext DEFAULT '',
  `text_csr_content` longtext DEFAULT '',
  `text_ca_content` longtext DEFAULT '',
  `oldsyncserver` varchar(255) DEFAULT NULL,
  `olddeleteflag` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_sslcert` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sslipaddress`
--

CREATE TABLE IF NOT EXISTS `sslipaddress` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `devname` varchar(255) DEFAULT NULL,
  `ipaddr` varchar(255) DEFAULT NULL,
  `sslclient` varchar(255) DEFAULT NULL,
  `ssldomain` varchar(255) DEFAULT NULL,
  `sslcert` varchar(255) DEFAULT NULL,
  `syncserver` varchar(255) DEFAULT NULL,
  `oldsyncserver` varchar(255) DEFAULT NULL,
  `olddeleteflag` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_sslipaddress` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ticket`
--

CREATE TABLE IF NOT EXISTS `ticket` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `password` varchar(255) DEFAULT NULL,
  `escalate` varchar(255) DEFAULT NULL,
  `realpass` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `cpstatus` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `priority` varchar(255) DEFAULT NULL,
  `responsible` varchar(255) DEFAULT NULL,
  `made_by` varchar(255) DEFAULT NULL,
  `sent_to` varchar(255) DEFAULT NULL,
  `date_modified` varchar(255) DEFAULT NULL,
  `unread_flag` varchar(255) DEFAULT NULL,
  `history_num` varchar(255) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `ddate` varchar(255) DEFAULT NULL,
  `mail_messageid` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_ticket` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ticketconfig`
--

CREATE TABLE IF NOT EXISTS `ticketconfig` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `ticketid` varchar(255) DEFAULT NULL,
  `ser_category_list_a` longtext DEFAULT '',
  `mail_account` varchar(255) DEFAULT NULL,
  `mail_server` varchar(255) DEFAULT NULL,
  `mail_password` varchar(255) DEFAULT NULL,
  `mail_period` varchar(255) DEFAULT NULL,
  `mail_enable` varchar(255) DEFAULT NULL,
  `mail_ssl_flag` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_ticketconfig` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tickethistory`
--

CREATE TABLE IF NOT EXISTS `tickethistory` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `made_by` varchar(255) DEFAULT NULL,
  `state_from` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `text_reason` longtext DEFAULT '',
  `ddate` varchar(255) DEFAULT NULL,
  `from_ad` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_tickethistory` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `utmp`
--

CREATE TABLE IF NOT EXISTS `utmp` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `ssession_name` varchar(255) DEFAULT NULL,
  `cttype` varchar(255) DEFAULT NULL,
  `logintime` varchar(255) DEFAULT NULL,
  `timeout` varchar(255) DEFAULT NULL,
  `logouttime` varchar(255) DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `logoutreason` varchar(255) DEFAULT NULL,
  `auxiliary_id` varchar(255) DEFAULT NULL,
  `consuming_parent` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_utmp` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `uuser`
--

CREATE TABLE IF NOT EXISTS `uuser` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `priv_q_disk_usage` varchar(255) DEFAULT NULL,
  `used_q_disk_usage` varchar(255) DEFAULT NULL,
  `priv_q_logo_manage_flag` varchar(255) DEFAULT NULL,
  `used_q_logo_manage_flag` varchar(255) DEFAULT NULL,
  `realname` varchar(255) DEFAULT NULL,
  `add_address` varchar(255) DEFAULT NULL,
  `add_city` varchar(255) DEFAULT NULL,
  `add_country` varchar(255) DEFAULT NULL,
  `add_telephone` varchar(255) DEFAULT NULL,
  `add_fax` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `cpstatus` varchar(255) DEFAULT NULL,
  `demo_status` varchar(255) DEFAULT NULL,
  `contactemail` varchar(255) DEFAULT NULL,
  `text_comment` longtext DEFAULT '',
  `disable_per` varchar(255) DEFAULT NULL,
  `ser_hpfilter` longtext DEFAULT '',
  `ddate` varchar(255) DEFAULT NULL,
  `ser_dskhistory` longtext DEFAULT '',
  `ser_dskshortcut_a` longtext DEFAULT '',
  `interface_template` varchar(255) DEFAULT NULL,
  `ser_boxpos` longtext DEFAULT '',
  `dialogsize` varchar(255) DEFAULT NULL,
  `realpass` varchar(255) DEFAULT NULL,
  `shellflag` varchar(255) DEFAULT NULL,
  `shell` varchar(255) DEFAULT NULL,
  `syncserver` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `oldsyncserver` varchar(255) DEFAULT NULL,
  `olddeleteflag` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_uuser` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `version`
--

CREATE TABLE IF NOT EXISTS `version` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `major` varchar(255) DEFAULT NULL,
  `minor` varchar(255) DEFAULT NULL,
  `releasen` varchar(255) DEFAULT NULL,
  `extra` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_version` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `watchdog`
--

CREATE TABLE IF NOT EXISTS `watchdog` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
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
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `web`
--

CREATE TABLE IF NOT EXISTS `web` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `priv_q_totaldisk_usage` varchar(255) DEFAULT NULL,
  `used_q_totaldisk_usage` varchar(255) DEFAULT NULL,
  `priv_q_ssl_flag` varchar(255) DEFAULT NULL,
  `used_q_ssl_flag` varchar(255) DEFAULT NULL,
  `priv_q_rubyfcgiprocess_num` varchar(255) DEFAULT NULL,
  `used_q_rubyfcgiprocess_num` varchar(255) DEFAULT NULL,
  `priv_q_disk_usage` varchar(255) DEFAULT NULL,
  `used_q_disk_usage` varchar(255) DEFAULT NULL,
  `priv_q_logo_manage_flag` varchar(255) DEFAULT NULL,
  `used_q_logo_manage_flag` varchar(255) DEFAULT NULL,
  `priv_q_ftpuser_num` varchar(255) DEFAULT NULL,
  `used_q_ftpuser_num` varchar(255) DEFAULT NULL,
  `priv_q_frontpage_flag` varchar(255) DEFAULT NULL,
  `used_q_frontpage_flag` varchar(255) DEFAULT NULL,
  `priv_q_php_manage_flag` varchar(255) DEFAULT NULL,
  `used_q_php_manage_flag` varchar(255) DEFAULT NULL,
  `priv_q_inc_flag` varchar(255) DEFAULT NULL,
  `used_q_inc_flag` varchar(255) DEFAULT NULL,
  `priv_q_awstats_flag` varchar(255) DEFAULT NULL,
  `used_q_awstats_flag` varchar(255) DEFAULT NULL,
  `priv_q_easyinstallerp_flag` varchar(255) DEFAULT NULL,
  `used_q_easyinstallerp_flag` varchar(255) DEFAULT NULL,
  `priv_q_modperl_flag` varchar(255) DEFAULT NULL,
  `used_q_modperl_flag` varchar(255) DEFAULT NULL,
  `priv_q_cgi_flag` varchar(255) DEFAULT NULL,
  `used_q_cgi_flag` varchar(255) DEFAULT NULL,
  `priv_q_php_flag` varchar(255) DEFAULT NULL,
  `used_q_php_flag` varchar(255) DEFAULT NULL,
  `priv_q_phpunsafe_flag` varchar(255) DEFAULT NULL,
  `used_q_phpunsafe_flag` varchar(255) DEFAULT NULL,
  `priv_q_subweb_a_num` varchar(255) DEFAULT NULL,
  `used_q_subweb_a_num` varchar(255) DEFAULT NULL,
  `priv_q_dotnet_flag` varchar(255) DEFAULT NULL,
  `used_q_dotnet_flag` varchar(255) DEFAULT NULL,
  `priv_q_cron_num` varchar(255) DEFAULT NULL,
  `used_q_cron_num` varchar(255) DEFAULT NULL,
  `priv_q_cron_minute_flag` varchar(255) DEFAULT NULL,
  `used_q_cron_minute_flag` varchar(255) DEFAULT NULL,
  `priv_q_cron_manage_flag` varchar(255) DEFAULT NULL,
  `used_q_cron_manage_flag` varchar(255) DEFAULT NULL,
  `priv_q_phpfcgi_flag` varchar(255) DEFAULT NULL,
  `used_q_phpfcgi_flag` varchar(255) DEFAULT NULL,
  `priv_q_rubyrails_num` varchar(255) DEFAULT NULL,
  `used_q_rubyrails_num` varchar(255) DEFAULT NULL,
  `priv_q_phpfcgiprocess_num` varchar(255) DEFAULT NULL,
  `used_q_phpfcgiprocess_num` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `iisid` varchar(255) DEFAULT NULL,
  `ser_server_alias_a` longtext DEFAULT '',
  `ser_subweb_a` longtext DEFAULT '',
  `ser_redirect_a` longtext DEFAULT '',
  `stats_username` varchar(255) DEFAULT NULL,
  `stats_password` varchar(255) DEFAULT NULL,
  `ttype` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `ipaddress` varchar(255) DEFAULT NULL,
  `ser_webmisc_b` longtext DEFAULT '',
  `redirect_domain` varchar(255) DEFAULT NULL,
  `text_extra_tag` longtext DEFAULT '',
  `ser_customerror_b` longtext DEFAULT '',
  `frontpage_flag` varchar(255) DEFAULT NULL,
  `syncserver` varchar(255) DEFAULT NULL,
  `cron_mailto` varchar(255) DEFAULT NULL,
  `ser_aspnetconf_b` longtext DEFAULT '',
  `ser_webindexdir_a` longtext DEFAULT '',
  `webmail_url` varchar(255) DEFAULT NULL,
  `text_lighty_rewrite` longtext DEFAULT '',
  `text_nginx_rewrite` longtext DEFAULT '',
  `ftpusername` varchar(255) DEFAULT NULL,
  `hotlink_flag` varchar(255) DEFAULT NULL,
  `text_hotlink_allowed` longtext DEFAULT '',
  `hotlink_redirect` varchar(255) DEFAULT NULL,
  `remove_processed_stats` varchar(255) DEFAULT NULL,
  `ser_indexfile_list` longtext DEFAULT '',
  `fcgi_children` varchar(255) DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `text_blockip` longtext DEFAULT '',
  `docroot` varchar(255) DEFAULT NULL,
  `force_www_redirect` varchar(255) DEFAULT NULL,
  `oldsyncserver` varchar(255) DEFAULT NULL,
  `olddeleteflag` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_web` (`parent_clname`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `webhandler`
--

CREATE TABLE IF NOT EXISTS `webhandler` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `syncserver` varchar(255) DEFAULT NULL,
  `mimehandler` varchar(255) DEFAULT NULL,
  `extension` varchar(255) DEFAULT NULL,
  `oldsyncserver` varchar(255) DEFAULT NULL,
  `olddeleteflag` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_webhandler` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `webmimetype`
--

CREATE TABLE IF NOT EXISTS `webmimetype` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `syncserver` varchar(255) DEFAULT NULL,
  `mimehandler` varchar(255) DEFAULT NULL,
  `extension` varchar(255) DEFAULT NULL,
  `oldsyncserver` varchar(255) DEFAULT NULL,
  `olddeleteflag` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_webmimetype` (`parent_clname`)
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `jailed`
--

CREATE TABLE IF NOT EXISTS `jailed` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `enable_jailed` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_jailed` (`parent_clname`)
) DEFAULT CHARSET=latin1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
