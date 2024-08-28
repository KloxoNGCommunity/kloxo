USE kloxo;

DROP TABLE IF EXISTS `service`;
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

INSERT IGNORE INTO `service` (`nname`, `parent_clname`, `parent_cmlist`, `servicename`, `description`, `grepstring`, `syncserver`, `oldsyncserver`, `olddeleteflag`) VALUES
('qmail___localhost', 'pserver-localhost', '', 'qmail', 'Qmail-toaster Mail Server', 'qmail', 'localhost', '', ''),
('named___localhost', 'pserver-localhost', '', 'named', 'Bind Dns Server', 'named', 'localhost', '', ''),
('djbdns___localhost', 'pserver-localhost', '', 'djbdns', 'DjbDns Dns Server', 'tinydns', 'localhost', '', ''),
('pdns___localhost', 'pserver-localhost', '', 'pdns', 'PowerDNS Dns Server', 'pdns', 'localhost', '', ''),
('nsd___localhost', 'pserver-localhost', '', 'nsd', 'NSD Dns Server', 'nsd', 'localhost', '', ''),
('yadifa___localhost', 'pserver-localhost', '', 'yadifad', 'YADIFA Dns Server', 'yadifad', 'localhost', '', ''),
('php-fpm___localhost', 'pserver-localhost', '', 'php-fpm', 'Php Fastcgi Process Manager (Php Used)', 'php-fpm', 'localhost', '', ''),
('httpd___localhost', 'pserver-localhost', '', 'httpd', 'Apache Web Server', 'httpd', 'localhost', '', ''),
('lighttpd___localhost', 'pserver-localhost', '', 'lighttpd', 'Lighttpd Web Server', 'lighttpd', 'localhost', '', ''),
('nginx___localhost', 'pserver-localhost', '', 'nginx', 'Nginx Web Server', 'nginx', 'localhost', '', ''),
('hiawatha___localhost', 'pserver-localhost', '', 'hiawatha', 'Hiawatha Web Server (use by KloxoNG)', 'hiawatha', 'localhost', '', ''),
('varnish___localhost', 'pserver-localhost', '', 'varnish', 'Varnish Web Cache', 'varnish', 'localhost', '', ''),
('squid___localhost', 'pserver-localhost', '', 'squid', 'Squid Web Cache', 'squid', 'localhost', '', ''),
('trafficserver___localhost', 'pserver-localhost', '', 'trafficserver', 'Apache Traffic Server Web Cache', 'trafficserver', 'localhost', '', ''),
('mysql___localhost', 'pserver-localhost', '', 'mysqld', 'MySQL Database', 'mysqld', 'localhost', '', ''),
('mariadb___localhost', 'pserver-localhost', '', 'mysql', 'MariaDB Database', 'mysql', 'localhost', '', ''),
('pureftpd___localhost', 'pserver-localhost', '', 'pure-ftpd', 'Pure-FTPD FTP server', 'pure-ftpd', 'localhost', '', ''),
('firewalld___localhost', 'pserver-localhost', '', 'firewalld', 'FirewallD', 'firewalld', 'localhost', '', ''),
('iptables___localhost', 'pserver-localhost', '', 'iptables', 'IPTables Firewall', 'iptables', 'localhost', '', '');

UPDATE `service` SET `description`='Php Fastcgi Process Manager (Php Used)' WHERE `nname`='php-fpm___localhost';

CREATE TABLE IF NOT EXISTS `jailed` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `enable_jailed` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_jailed` (`parent_clname`)
) DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `dnsslave` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `master_ip` varchar(255) DEFAULT NULL,
  `syncserver` varchar(255) DEFAULT NULL,
  `serial` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_dnsslave` (`parent_clname`)
) DEFAULT CHARSET=latin1;

ALTER TABLE `client` ADD COLUMN IF NOT EXISTS `priv_q_totalinode_flag` VARCHAR(255) DEFAULT NULL AFTER `priv_q_ftpuser_num`;
ALTER TABLE `client` ADD COLUMN IF NOT EXISTS `used_q_totalinode_flag` VARCHAR(255) DEFAULT NULL AFTER `used_q_ftpuser_num`;

ALTER TABLE `client` ADD COLUMN IF NOT EXISTS `priv_q_frontpage_flag` VARCHAR(255) DEFAULT NULL AFTER `priv_q_totalinode_flag`;
ALTER TABLE `client` ADD COLUMN IF NOT EXISTS `used_q_frontpage_flag` VARCHAR(255) DEFAULT NULL AFTER `used_q_totalinode_flag`;

ALTER TABLE `dns` ADD COLUMN IF NOT EXISTS `hostmaster` VARCHAR(255) NULL DEFAULT NULL AFTER `soanameserver`;

ALTER TABLE `mmail` ADD COLUMN IF NOT EXISTS `spf_protocol` VARCHAR(255) NULL DEFAULT NULL AFTER `text_spf_ip`;
ALTER TABLE `mmail` ADD COLUMN IF NOT EXISTS `enable_dmarc_flag` VARCHAR(255) NULL DEFAULT NULL AFTER `spf_protocol`;
ALTER TABLE `mmail` ADD COLUMN IF NOT EXISTS `percentage_filtering` VARCHAR(255) NULL DEFAULT NULL AFTER `enable_dmarc_flag`;
ALTER TABLE `mmail` ADD COLUMN IF NOT EXISTS `receiver_policy` VARCHAR(255) NULL DEFAULT NULL AFTER `percentage_filtering`;
ALTER TABLE `mmail` ADD COLUMN IF NOT EXISTS `mail_feedback` VARCHAR(255) NULL DEFAULT NULL AFTER `receiver_policy`;
ALTER TABLE `mmail` ADD COLUMN IF NOT EXISTS `dmarc_protocol` VARCHAR(255) NULL DEFAULT NULL AFTER `mail_feedback`;

ALTER TABLE `client` ADD COLUMN IF NOT EXISTS `priv_q_totalinode_usage` VARCHAR(255) NULL DEFAULT NULL AFTER `used_q_totaldisk_usage`;
ALTER TABLE `client` ADD COLUMN IF NOT EXISTS `used_q_totalinode_usage` VARCHAR(255) NULL DEFAULT NULL AFTER `priv_q_totalinode_usage`;

ALTER TABLE `serverftp` ADD COLUMN IF NOT EXISTS `defaultport` VARCHAR(255) NULL DEFAULT NULL AFTER `enable_anon_ftp`;

ALTER TABLE `mmail` ADD COLUMN IF NOT EXISTS `enable_spf_autoip` VARCHAR(255) NULL DEFAULT NULL AFTER `text_spf_domain`;
ALTER TABLE `mmail` ADD COLUMN IF NOT EXISTS `text_spf_include` VARCHAR(255) NULL DEFAULT NULL AFTER `enable_spf_flag`;
ALTER TABLE `mmail` ADD COLUMN IF NOT EXISTS `text_spf_redirect` VARCHAR(255) NULL DEFAULT NULL AFTER `text_spf_include`; 

ALTER TABLE `sslcert` ADD COLUMN IF NOT EXISTS `parent_domain` VARCHAR(255) NULL DEFAULT NULL AFTER `upload_status`;
ALTER TABLE `sslcert` ADD COLUMN IF NOT EXISTS `add_type` VARCHAR(255) NULL DEFAULT NULL AFTER `parent_domain`;

ALTER TABLE `web` ADD COLUMN IF NOT EXISTS `force_https_redirect` VARCHAR(255) NULL DEFAULT NULL AFTER `force_www_redirect`;

ALTER TABLE `sslcert` CHANGE COLUMN IF EXISTS `parent_domain` `parent_domain` VARCHAR(255) NULL DEFAULT NULL;

ALTER TABLE `web` ADD COLUMN IF NOT EXISTS `web_selected` VARCHAR(255) NULL DEFAULT NULL AFTER `force_https_redirect`;
ALTER TABLE `web` ADD COLUMN IF NOT EXISTS `php_selected` VARCHAR(255) NULL DEFAULT NULL AFTER `web_selected`;
ALTER TABLE `web` ADD COLUMN IF NOT EXISTS `time_out` VARCHAR(255) NULL DEFAULT NULL AFTER `php_selected`;

DELETE FROM phpini WHERE nname LIKE 'domain-%' OR nname LIKE 'web-%';

ALTER TABLE `serverweb` ADD COLUMN IF NOT EXISTS `php_used` VARCHAR(255) NULL DEFAULT NULL AFTER `php_type`;

ALTER TABLE `serverftp` ADD COLUMN IF NOT EXISTS `enable_tls` VARCHAR(255) NULL DEFAULT NULL AFTER `defaultport`;

ALTER TABLE `sslcert` ADD COLUMN IF NOT EXISTS `upload_status` VARCHAR(255) NULL DEFAULT NULL AFTER `add_type`;

ALTER TABLE `sslcert` ADD COLUMN IF NOT EXISTS `username` VARCHAR(255) NULL DEFAULT NULL AFTER `parent_cmlist`;

UPDATE `lxguardhit` SET `access` = 'smtp' WHERE `access` = 'mail';

ALTER TABLE `client` CHANGE COLUMN IF EXISTS `priv_q_installapp_flag` `priv_q_easyinstaller_flag` VARCHAR(255) NULL DEFAULT NULL;
ALTER TABLE `client` CHANGE COLUMN IF EXISTS `used_q_installapp_flag` `used_q_easyinstaller_flag` VARCHAR(255) NULL DEFAULT NULL;

ALTER TABLE `domain` CHANGE COLUMN IF EXISTS `priv_q_installapp_flag` `priv_q_easyinstaller_flag` VARCHAR(255) NULL DEFAULT NULL;
ALTER TABLE `domain` CHANGE COLUMN IF EXISTS `used_q_installapp_flag` `used_q_easyinstaller_flag` VARCHAR(255) NULL DEFAULT NULL;

ALTER TABLE `web` CHANGE COLUMN IF EXISTS `priv_q_installapp_flag` `priv_q_easyinstaller_flag` VARCHAR(255) NULL DEFAULT NULL;
ALTER TABLE `web` CHANGE COLUMN IF EXISTS `used_q_installapp_flag` `used_q_easyinstaller_flag` VARCHAR(255) NULL DEFAULT NULL;

DROP TABLE IF EXISTS component;

ALTER TABLE `web` ADD COLUMN IF NOT EXISTS `microcache_time` VARCHAR(255) NULL DEFAULT NULL AFTER `time_out`;
ALTER TABLE `web` ADD COLUMN IF NOT EXISTS `microcache_insert_into` VARCHAR(255) NULL DEFAULT NULL AFTER `microcache_time`;

ALTER TABLE `servermail` ADD COLUMN IF NOT EXISTS `smtp_relay` text AFTER `concurrencyremote`;
ALTER TABLE `servermail` ADD COLUMN IF NOT EXISTS `blacklist_headers` VARCHAR(1023) NULL DEFAULT NULL AFTER `dns_blacklists`;

ALTER TABLE `lxbackup` ADD COLUMN IF NOT EXISTS `backupschedule_time` INT NULL AFTER `backupschedule_type`;
ALTER TABLE `lxbackup` CHANGE COLUMN IF EXISTS `backupschedule_time` `backupschedule_time` VARCHAR(255) NULL DEFAULT NULL;

ALTER TABLE `web` ADD COLUMN IF NOT EXISTS `general_header` TEXT NULL DEFAULT NULL AFTER `microcache_insert_into`;
ALTER TABLE `web` ADD COLUMN IF NOT EXISTS `https_header` TEXT NULL DEFAULT NULL AFTER `general_header`;
ALTER TABLE `web` ADD COLUMN IF NOT EXISTS `static_files_expire` VARCHAR(255) NULL DEFAULT NULL AFTER `https_header`;

ALTER TABLE `web` ADD COLUMN IF NOT EXISTS `disable_pagespeed` VARCHAR(255) NULL DEFAULT NULL AFTER `static_files_expire`;

CREATE TABLE IF NOT EXISTS `sendmailban` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text DEFAULT '',
  `target` varchar(255) DEFAULT NULL,
  `syncserver` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_sendmailban` (`parent_clname`)
) DEFAULT CHARSET=latin1;

ALTER TABLE `serverftp` ADD `passiveip` VARCHAR(255) NULL DEFAULT NULL AFTER `lowport`;
