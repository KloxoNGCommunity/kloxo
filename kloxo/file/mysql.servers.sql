CREATE TABLE IF NOT EXISTS `servers` (
	`Server_name` char(64) NOT NULL,
	`Host` char(64) NOT NULL,
	`Db` char(64) NOT NULL,
	`Username` char(64) NOT NULL,
	`Password` char(64) NOT NULL,
	`Port` int(4) DEFAULT NULL,
	`Socket` char(64) DEFAULT NULL,
	`Wrapper` char(64) NOT NULL,
	`Owner` char(64) NOT NULL,
	PRIMARY KEY (`Server_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='MySQL Foreign Servers table';