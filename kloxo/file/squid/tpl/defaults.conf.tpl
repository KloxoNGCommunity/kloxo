<?php

$srcconfpath ="/opt/configs/squid/etc/conf";
$trgtconfpath ="/etc/squid";

$list = array('squid.conf', 'cachemgr.conf', 'errorpage.css', 'mime.conf', 'msntauth.conf');

foreach ($list as $k => $v) {
	if (file_exists("{$srcconfpath}/custom.{$v}")) {
		copy("{$srcconfpath}/custom.{$v}", "{$trgtconfpath}/{$v}");
	} else {
		copy("{$srcconfpath}/{$v}", "{$trgtconfpath}/{$v}");
	}
}

$srcsyspath ="/opt/configs/squid/etc/sysconfig";
$trgtsyspath ="/etc/sysconfig";

if (file_exists("{$srsyspath}/custom.squid")) {
	copy("{$srsyspath}/custom.squid", "{$trgtsyspath}/squid");
} else {
	copy("{$srsyspath}/squid", "{$trgtsyspath}/squid");
}

$srclogpath ="/opt/configs/squid/etc/logrotate.d";
$trgtlogpath ="/etc/logrotate.d";

if (file_exists("{$srlogpath}/custom.squid")) {
	copy("{$srlogpath}/custom.squid", "{$trgtlogpath}/squid");
} else {
	copy("{$srlogpath}/squid", "{$trgtlogpath}/squid");
}

$srcpampath ="/opt/configs/squid/etc/pam.d";
$trgtpampath ="/etc/pam.d";

if (file_exists("{$srpampath}/custom.squid")) {
	copy("{$srpampath}/custom.squid", "{$trgtpampath}/squid");
} else {
	copy("{$srpampath}/squid", "{$trgtpampath}/squid");
}

$srcnmpath ="/opt/configs/squid/etc/NetworkManager/dispatcher.d";
$trgtnmpath ="/etc/NetworkManager/dispatcher.d";

if (file_exists("{$srnmpath}/custom.20-squid")) {
	copy("{$srnmpath}/custom.20-squid", "{$trgtnmpath}/20-squid");
} else {
	copy("{$srnmpath}/20-squid", "{$trgtnmpath}/20-squid");
}

$srchttpdpath ="/opt/configs/squid/etc/httpd/conf.d";
$trgthttpdpath ="/etc/httpd/conf.d";

if (file_exists("{$srhttpdpath}/custom.squid.conf")) {
	copy("{$srhttpdpath}/custom.squid.conf", "{$trgthttpdpath}/squid.conf");
} else {
	copy("{$srhttpdpath}/squid.conf", "{$trgthttpdpath}/squid.conf");
}

?>