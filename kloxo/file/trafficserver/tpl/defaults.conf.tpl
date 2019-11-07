<?php

$list = array('ip_allow', 'records', 'remap', 'storage');

foreach ($list as $k => $v) {
	copy(getLinkCustomfile('/opt/configs/trafficserver/etc/conf', "{$v}.config"), "/etc/trafficserver/{$v}.config");
}

?>