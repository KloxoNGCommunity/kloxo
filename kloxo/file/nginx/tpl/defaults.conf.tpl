### begin content - please not remove this line
<?php

$port = '80';
$portssl = '443';

$iplist = array('*');

if ($setdefaults === 'webmail') {
    if ($webmailappdefault) {
        $rootpath = "/home/kloxo/httpd/webmail/{$webmailappdeffault}";
    } else {
        $rootpath = "/home/kloxo/httpd/webmail";
    }
} else {
    $rootpath = "/home/kloxo/httpd/{$setdefaults}";
}

if ($indexorder) {
    $indexorder = implode(' ', $indexorder);
}

$disablepath = "/home/kloxo/httpd/disable";

$globalspath = "/home/nginx/conf/globals";

if (file_exists("{$globalspath}/custom.proxy.conf")) {
    $proxyconf = 'custom.proxy.conf';
} else {
    $proxyconf = 'proxy.conf';
}

if (file_exists("{$globalspath}/custom.phpfpm.conf")) {
    $phpfpmconf = 'custom.php-fpm.conf';
} else {
    $phpfpmconf = 'php-fpm.conf';
}

?>

<?php 
if ($setdefaults === 'ssl') {
    $counter = 0;

    foreach ($certlist as &$cert) {
        if ($counter === 0) { 
            $ssltext = 'default ssl';
        } else {
            $ssltext = 'ssl';
        }

        $counter++;
?> 
server {
    listen <?php echo $cert['ip']; ?>:443 <?php echo $ssltext; ?>;

#    server_name _;

    ssl on;
    ssl_certificate /home/kloxo/httpd/ssl/<?php echo $cert['cert']; ?>.crt;
    ssl_certificate_key /home/kloxo/httpd/ssl/<?php echo $cert['cert']; ?>.key;
    ssl_protocols SSLv3 TLSv1 TLSv1.1 TLSv1.2;
    ssl_ciphers HIGH:!aNULL:!MD5;

    return 403;
}

index <?php echo $indexorder; ?>;
<?php 
    }
} elseif ($setdefaults === 'init') {
/*
?>
server {
<?php
    foreach ($iplist as &$ip) {
?> 
#    listen <?php echo $ip ?>:<?php echo $port ?>;
#    listen <?php echo $ip ?>:<?php echo $portssl ?>;
<?php 
    }
?>
}
<?php
*/
} else {
?> 
server {
<?php
    foreach ($iplist as &$ip) {
?>
    listen <?php echo $ip ?>:<?php echo $port ?>;
    listen <?php echo $ip ?>:<?php echo $portssl ?>;

<?php 
    }

    if ($setdefaults === 'default') {
?>
    server_name _;

    index <?php echo $indexorder; ?>;

    location ~ ^/~(.+)/(.*)$ {
        alias /home/$1/public_html/$2;
    }
<?php
    } else {
?>
    server_name <?php echo $setdefaults; ?>.*;

    index <?php echo $indexorder; ?>;

    set $rootdir '<?php echo $rootpath; ?>';

    root $rootdir;

    set $domain '';

    set $user 'apache';
<?php 
    }

    if ($reverseproxy) {
?>

    include '<?php echo $globalspath; ?>/<?php echo $proxyconf; ?>';
<?php 
    } else {
?>

    set $fpmport '50000';

    include '<?php echo $globalspath; ?>/<?php echo $phpfpmconf; ?>';
<?php 
    }
?>
}
<?php
}
?>


### end content - please not remove this line
