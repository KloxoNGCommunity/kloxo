<?php
    $phpinipath = (isset($phpinipath)) ? $phpinipath : "/etc";

    $maxchildren = (isset($maxchildren)) ? $maxchildren : '6';
    $maxrequests = (isset($maxrequests)) ? $maxrequests : '1000';
    $phpcgipath = (isset($phpcgipath)) ? $phpcgipath : '/usr/bin/php-cgi';
?>
#!/bin/sh
export PHPRC="<?php echo $phpinipath; ?>"
export PHP_FCGI_CHILDREN=<?php echo $maxchildren; ?>

export PHP_FCGI_MAX_REQUESTS=<?php echo $maxrequests; ?>

exec <?php echo $phpcgipath; ?>