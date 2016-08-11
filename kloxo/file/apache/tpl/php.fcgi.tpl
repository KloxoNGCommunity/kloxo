<?php
$phpinipath = (isset($phpinipath)) ? $phpinipath : "/etc";
$phpscanpath = (isset($phpscanpath)) ? $phpscanpath : "/etc/php.d";

$maxchildren = (isset($maxchildren)) ? $maxchildren : '6';
$maxrequests = (isset($maxrequests)) ? $maxrequests : '1000';
$phpcgipath = (isset($phpcgipath)) ? $phpcgipath : '/usr/bin/php-cgi';
?>
#!/bin/sh

## MR -- for future generic wrapper
#ps_out=$(ps aux|grep $0|grep -v 'grep')
#user_out=$(echo $ps_out|awk '{print $1}')

php_rc='<?=$phpinipath;?>/php.ini'
php_scan='<?=$phpscanpath;?>'
php_prog='<?=$phpcgipath;?>'

#export PHPRC=$php_rc
export PHP_INI_SCAN_DIR=$php_scan
export PHP_FCGI_CHILDREN=<?=$maxchildren;?>

export PHP_FCGI_MAX_REQUESTS=<?=$maxrequests;?>


exec $php_prog -c $php_rc $*
