<?php

$list = array('squid.conf', 'cachemgr.conf', 'errorpage.css', 'mime.conf', 'msntauth.conf');

$sdir = "/opt/configs/squid";

foreach ($list as $k => $v) {
	copy(getLinkCustomfile("{$sdir}/etc/squid", $v), "/etc/squid/{$v}");
}

$a = array("sysconfig" => "squid", "logrotate.d" => "squid", "pam.d" => "squid", 
	"NetworkManager/dispatcher.d" => "20-squid", "httpd/conf.d" => "squid.conf");

foreach ($a as $k2 => $v2) {
	copy(getLinkCustomfile("{$sdir}/etc/{$k2}", $v2), "/etc/{$k2}/{$v2}");
}
