### begin content - please not remove this line
<?php

if ($setdefaults === 'webmail') {
    if ($webmailappdefault) {
        $docroot = "/home/kloxo/httpd/webmail/{$webmailappdefault}";
    } else {
        $docroot = "/home/kloxo/httpd/webmail";
    }
} else {
    $docroot = "/home/kloxo/httpd/{$setdefaults}";
}

$port = '80';
$portssl = '443';

if ($indexorder) {
    $indexorder = implode(' ', $indexorder);
}

$indexorder = '"' . $indexorder . '"';
$indexorder = str_replace(' ', '", "', $indexorder);

$globalspath = "/home/lighttpd/conf/globals";

if (file_exists("{$globalspath}/custom.proxy.conf")) {
    $proxyconf = 'custom.proxy.conf';
} else {
    $proxyconf = 'proxy.conf';
}

if (file_exists("{$globalspath}/custom.php-fpm.conf")) {
    $phpfpmconf = 'custom.php-fpm.conf';
} else {
    $phpfpmconf = 'php-fpm.conf';
}

if (file_exists("{$globalspath}/custom.nobody.conf")) {
    $nobodyconf = 'custom.nobody.conf';
} else {
    $nobodyconf = 'nobody.conf';
}

$userinfo = posix_getpwnam('apache');
$fpmport = (50000 + $userinfo['uid']);

?>

<?php
if ($setdefaults === 'ssl') {
    foreach ($certlist as &$cert) {
?>

$SERVER["socket"] == "<?php echo $cert['ip']; ?>:<?php echo $portssl; ?>" {

    ssl.engine = "enable"

    ssl.pemfile = "/home/kloxo/httpd/ssl/<?php echo $cert['cert']; ?>.pem"
    ssl.ca-file = "/home/kloxo/httpd/ssl/<?php echo $cert['cert']; ?>.ca"

}
<?php
    }
} elseif ($setdefaults === 'init') {
?>

## not implementing yet
<?php
} else {
?>

$HTTP["host"] =~ "^<?php echo $setdefaults; ?>\.*" { 

    var.rootdir = "/home/kloxo/httpd/<?php echo $setdefaults; ?>/"

    server.document-root = var.rootdir

    index-file.names = ( <?php echo $indexorder; ?> )
<?php
    if ($reverseproxy) {
?>

    include "<?php echo $globalspath; ?>/<?php echo $proxyconf; ?>"
<?php
    } else {
?>

    var.fpmport = "<?php echo $fpmport; ?>"

    include "<?php echo $globalspath; ?>/<?php echo $phpfpmconf; ?>"

#    include "<?php echo $globalspath; ?>/<?php echo $nobodyconf; ?>"
<?php
    }
?>

}
<?php
}
?>


### end content - please not remove this line
