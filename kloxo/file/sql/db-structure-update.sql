USE kloxo;

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

UPDATE `watchdog` SET `nname`='dns___localhost',`parent_clname`='pserver-localhost',`parent_cmlist`='',`servicename`='dns',`syncserver`='localhost',`port`='53',`action`='__driver_dns',`status`='on',`added_by_system`='on',`oldsyncserver`='',`olddeleteflag`='' WHERE `nname`='dns___localhost';
UPDATE `watchdog` SET `nname`='web___localhost',`parent_clname`='pserver-localhost',`parent_cmlist`='',`servicename`='web',`syncserver`='localhost',`port`='80',`action`='__driver_web',`status`='on',`added_by_system`='on',`oldsyncserver`='',`olddeleteflag`='' WHERE `nname`='web___localhost';
UPDATE `watchdog` SET `nname`='mail___localhost',`parent_clname`='pserver-localhost',`parent_cmlist`='',`servicename`='mail',`syncserver`='localhost',`port`='25',`action`='__driver_qmail',`status`='on',`added_by_system`='on',`oldsyncserver`='',`olddeleteflag`='' WHERE `nname`='mail___localhost';
UPDATE `watchdog` SET `nname`='mysql___localhost',`parent_clname`='pserver-localhost',`parent_cmlist`='',`servicename`='mysql',`syncserver`='localhost',`port`='3306',`action`='__driver_mysql',`status`='on',`added_by_system`='on',`oldsyncserver`='',`olddeleteflag`='' WHERE `nname`='mysql___localhost';


DROP TABLE IF EXISTS `service`;
CREATE TABLE IF NOT EXISTS `service` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text,
  `servicename` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `grepstring` varchar(255) DEFAULT NULL,
  `syncserver` varchar(255) DEFAULT NULL,
  `oldsyncserver` varchar(255) DEFAULT NULL,
  `olddeleteflag` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_service` (`parent_clname`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `service` (`nname`, `parent_clname`, `parent_cmlist`, `servicename`, `description`, `grepstring`, `syncserver`, `oldsyncserver`, `olddeleteflag`) VALUES
('qmail___localhost', 'pserver-localhost', '', 'qmail', 'Qmail-toaster Mail Server', 'qmail', 'localhost', '', ''),
('named___localhost', 'pserver-localhost', '', 'named', 'Bind Dns Server', 'named', 'localhost', '', ''),
('djbdns___localhost', 'pserver-localhost', '', 'djbdns', 'DjbDns Dns Server', 'tinydns', 'localhost', '', ''),
('pdns___localhost', 'pserver-localhost', '', 'pdns', 'PowerDNS Dns Server', 'pdns', 'localhost', '', ''),
('nsd___localhost', 'pserver-localhost', '', 'nsd', 'NSD Dns Server', 'nsd', 'localhost', '', ''),
('mydns___localhost', 'pserver-localhost', '', 'mydns', 'MyDNS Dns Server', 'mydns', 'localhost', '', ''),
('yadifa___localhost', 'pserver-localhost', '', 'yadifad', 'YADIFA Dns Server', 'yadifad', 'localhost', '', ''),
('php-fpm___localhost', 'pserver-localhost', '', 'php-fpm', 'Php Fastcgi Process Manager', 'php-fpm', 'localhost', '', ''),
('httpd___localhost', 'pserver-localhost', '', 'httpd', 'Apache Web Server', 'httpd', 'localhost', '', ''),
('lighttpd___localhost', 'pserver-localhost', '', 'lighttpd', 'Lighttpd Web Server', 'lighttpd', 'localhost', '', ''),
('nginx___localhost', 'pserver-localhost', '', 'nginx', 'Nginx Web Server', 'nginx', 'localhost', '', ''),
('hiawatha___localhost', 'pserver-localhost', '', 'hiawatha', 'Hiawatha Web Server (use by Kloxo-MR)', 'hiawatha', 'localhost', '', ''),
('varnish___localhost', 'pserver-localhost', '', 'varnish', 'Varnish Web Cache', 'varnish', 'localhost', '', ''),
('squid___localhost', 'pserver-localhost', '', 'squid', 'Squid Web Cache', 'squid', 'localhost', '', ''),
('trafficserver___localhost', 'pserver-localhost', '', 'trafficserver', 'Apache Traffic Server Web Cache', 'trafficserver', 'localhost', '', ''),
('mysql___localhost', 'pserver-localhost', '', 'mysqld', 'MySQL Database', 'mysqld', 'localhost', '', ''),
('mariadb___localhost', 'pserver-localhost', '', 'mysql', 'MariaDB Database', 'mysql', 'localhost', '', ''),
('iptables___localhost', 'pserver-localhost', '', 'iptables', 'IPTables Firewall', 'iptables', 'localhost', '', '');

CREATE TABLE IF NOT EXISTS `jailed` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text,
  `enable_jailed` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_jailed` (`parent_clname`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `dnsslave` (
  `nname` varchar(255) NOT NULL,
  `parent_clname` varchar(255) DEFAULT NULL,
  `parent_cmlist` text,
  `master_ip` varchar(255) DEFAULT NULL,
  `syncserver` varchar(255) DEFAULT NULL,
  `serial` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`nname`),
  KEY `parent_clname_dnsslave` (`parent_clname`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

ALTER TABLE `client` CHANGE COLUMN `priv_q_frontpage_flag` `priv_q_totalinode_flag` VARCHAR(255) DEFAULT NULL;
ALTER TABLE `client` CHANGE COLUMN `used_q_frontpage_flag` `used_q_totalinode_flag` VARCHAR(255) DEFAULT NULL;

ALTER TABLE `dns` ADD `hostmaster` VARCHAR(255) AFTER `soanameserver`;

ALTER TABLE `mmail` ADD `spf_protocol` VARCHAR(255) NULL DEFAULT NULL AFTER `text_spf_ip`;
ALTER TABLE `mmail` ADD `enable_dmarc_flag` VARCHAR(255) NULL DEFAULT NULL AFTER `spf_protocol`;
ALTER TABLE `mmail` ADD `percentage_filtering` VARCHAR(255) NULL DEFAULT NULL AFTER `enable_dmarc_flag`;
ALTER TABLE `mmail` ADD `receiver_policy` VARCHAR(255) NULL DEFAULT NULL AFTER `percentage_filtering`;
ALTER TABLE `mmail` ADD `mail_feedback` VARCHAR(255) NULL DEFAULT NULL AFTER `receiver_policy`;
ALTER TABLE `mmail` ADD `dmarc_protocol` VARCHAR(255) NULL DEFAULT NULL AFTER `mail_feedback`;

ALTER TABLE `client` ADD `priv_q_totalinode_usage` VARCHAR(255) NULL DEFAULT NULL AFTER `used_q_totaldisk_usage`;
ALTER TABLE `client` ADD `used_q_totalinode_usage` VARCHAR(255) NULL DEFAULT NULL AFTER `priv_q_totalinode_usage`;

ALTER TABLE `serverftp` ADD `defaultport` VARCHAR(255) NULL DEFAULT NULL AFTER `enable_anon_ftp`;

