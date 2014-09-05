<?php

$srcconfpath ="/opt/configs/trafficserver/etc/conf";
$trgtconfpath ="/etc/trafficserver";

$list = array('ip_allow', 'records', 'remap', 'storage');

foreach ($list as $k => $v) {
	if (file_exists("{$srcconfpath}/custom.{$v}.config")) {
		copy("{$srcconfpath}/custom.{$v}.config", "{$trgtconfpath}/{$v}.config");
	} else {
		copy("{$srcconfpath}/{$v}.config", "{$trgtconfpath}/{$v}.config");
	}
}

?>