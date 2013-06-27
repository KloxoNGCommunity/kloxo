USE kloxo;

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
) DEFAULT CHARSET=latin1;

INSERT INTO `watchdog` (`nname`, `parent_clname`, `parent_cmlist`, `servicename`, `syncserver`, `port`, `action`, `status`, `added_by_system`, `oldsyncserver`, `olddeleteflag`) VALUES
('web___localhost', 'pserver-localhost', '', 'web', 'localhost', '80', '__driver_web', 'on', 'on', '', ''),
('smtp___localhost', 'pserver-localhost', '', 'smtp', 'localhost', '25', '/etc/init.d/qmail restart', 'on', 'on', '', ''),
('pop___localhost', 'pserver-localhost', '', 'pop', 'localhost', '110', '/etc/init.d/dovecot restart', 'on', 'on', '', ''),
('imap___localhost', 'pserver-localhost', '', 'imap', 'localhost', '143', '/etc/init.d/dovecot restart', 'on', 'on', '', ''),
('mysql___localhost', 'pserver-localhost', '', 'mysql', 'localhost', '3306', '/etc/init.d/mysqld restart', 'on', 'on', '', ''),
('mariadb___localhost', 'pserver-localhost', '', 'mariadb', 'localhost', '3306', '/etc/init.d/mysql restart', 'on', 'on', '', ''),
('ftp___localhost', 'pserver-localhost', '', 'ftp', 'localhost', '21', '/etc/init.d/xinetd restart', 'on', 'on', '', '');

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
) DEFAULT CHARSET=latin1;

INSERT INTO `service` (`nname`, `parent_clname`, `parent_cmlist`, `servicename`, `description`, `grepstring`, `syncserver`, `oldsyncserver`, `olddeleteflag`) VALUES
('qmail___localhost', 'pserver-localhost', '', 'qmail', 'Qmail-toaster Mail Server', 'qmail', 'localhost', '', ''),
('djbdns___localhost', 'pserver-localhost', '', 'djbdns', 'Djbdns Dns Server', 'tinydns', 'localhost', '', ''),
('named___localhost', 'pserver-localhost', '', 'named', 'Bind Dns Server', 'named', 'localhost', '', ''),
('php-fpm___localhost', 'pserver-localhost', '', 'php-fpm', 'Php Fastcgi Process Manager', 'php-fpm', 'localhost', '', ''),
('lighttpd___localhost', 'pserver-localhost', '', 'lighttpd', 'Lighttpd Web Server', 'lighttpd', 'localhost', '', ''),
('nginx___localhost', 'pserver-localhost', '', 'nginx', 'Nginx Web Server', 'nginx', 'localhost', '', ''),
('httpd___localhost', 'pserver-localhost', '', 'httpd', 'Apache Web Server', 'httpd', 'localhost', '', ''),
('iptables___localhost', 'pserver-localhost', '', 'iptables', 'IPTables Firewall', 'iptables', 'localhost', '', '');

