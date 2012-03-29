### begin content - please not remove this line
<?php

$port = '80';
$portssl = '443';

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
/*
    foreach ($iplist as &$ip) {
?>
    listen <?php echo $ip ?>:<?php echo $port ?>;
    listen <?php echo $ip ?>:<?php echo $portssl ?>;

<?php 
    }
*/
?>
    listen *:<?php echo $port ?>;
    listen *:<?php echo $portssl ?>;

<?php
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

    include '/home/nginx/conf/globals/proxy.conf';
<?php 
    } else {
?>

    set $fpmport '50000';

    include '/home/nginx/conf/globals/php-fpm.conf';
<?php 
    }
?>
}
<?php
}
?>


### end content - please not remove this line
