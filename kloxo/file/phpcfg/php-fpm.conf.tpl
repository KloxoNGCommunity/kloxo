<?php
	$poolpath = (isset($poolpath)) ? $poolpath : "/etc/php-fpm.d";
	$phpdesc = (isset($phpdesc)) ? $phpdesc : "/etc/php-fpm.d";
?>
[global]
pid=/var/run/php-fpm/<?=$phpdesc;?>.pid
error_log=/var/log/php-fpm/<?=$phpdesc;?>-error.log
log_level=error

;emergency_restart_threshold=0
;emergency_restart_interval=0
;process_control_timeout=0

emergency_restart_threshold=10
emergency_restart_interval=1m
process_control_timeout=10s

daemonize=yes

include=<?p=$poolpath;?>/*.conf

