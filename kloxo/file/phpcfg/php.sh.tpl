<?php
    $maxchildren = (isset($maxchildren)) ? $maxchildren : '6';
    $maxrequests = (isset($maxrequests)) ? $maxrequests : '1000';

    $php_rc_path = (isset($php_rc_path)) ? $php_rc_path : "/etc";
    $php_scan_path = (isset($php_scan_path)) ? $php_scan_path : "/etc/php.d";

    $php_prog_type = (isset($php_prog_type)) ? $php_prog_type : 'php-cli';
    $php_prog = (isset($php_prog)) ? $php_prog : '/usr/bin/php';
?>
#!/bin/sh

php_rc='<?=$php_rc_path;?>'
php_scan='<?=$php_scan_path;?>'
php_prog='<?=$php_prog;?>'

php_max_children='<?=$maxchildren;?>'
php_max_requests='<?=$maxrequests;?>'

<?php
    if ($php_program_type === 'php-cli') {
?>
exec env -i PHP_INI_SCAN_DIR=$php_scan $php_prog -c $php_rc $*
<?php

    } else {
?>
#export PHPRC=$php_rc
export PHP_INI_SCAN_DIR=$php_scan
<?php
        if ($php_program_type === 'php-ls') {
?>
export PHP_LSAPI_CHILDREN=$php_max_children
export PHP_LSAPI_MAX_REQUESTS=$php_max_requests
export LSAPI_MAX_PROCESS_TIME=300

exec <?=$php_prog;?> -c $php_rc -b <?=$php_rc;?>/<?=$user;?>.sock $*
<?php
        } else {
            if ($php_program_type === 'php-cgi') {
?>
export PHP_FCGI_CHILDREN=$php_max_children
export PHP_FCGI_MAX_REQUESTS=$php_max_requests
<?php
            }
?>
exec $php_prog -c $php_rc $*
<?php
         }
    }
?>