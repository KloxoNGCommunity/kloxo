<?php

$list = array('squid.conf', 'cachemgr.conf', 'errorpage.css', 'mime.conf', 'msntauth.conf');

foreach ($list as $k => $v) {
	$scfile=getLinkCustomfile('/opt/configs/squid/etc/conf', $v);
	copy($scfile, "/etc/squid/{$v}");
}

copy(getLinkCustomfile('/opt/configs/squid/etc/sysconfig', "squid"), "/etc/sysconfig/squid");

copy(getLinkCustomfile('/opt/configs/squid/etc/logrotate.d', "squid"), "/etc/logrotate.d/squid");

copy(getLinkCustomfile('/opt/configs/squid/etc/pam.d', "squid"), "/etc/pam.d/squid");

copy(getLinkCustomfile('/opt/configs/squid/etc/NetworkManager/dispatcher.d', "20-squid"), "/etc/NetworkManager/dispatcher.d/20-squid");

copy(getLinkCustomfile('/opt/configs/squid/etc/httpd/conf.d', "squid.conf"), "/etc/httpd/conf.d/squid.conf");
?>