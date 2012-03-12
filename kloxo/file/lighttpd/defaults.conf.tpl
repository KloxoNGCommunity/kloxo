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

    include "/home/lighttpd/conf/globals/proxy.conf"
<?php
    } else {
?>

    var.fpmport = "50000"

    include "/home/lighttpd/conf/globals/php-fpm.conf"

#    include "/home/lighttpd/conf/globals/nobody.conf"
<?php
    }
?>

}
<?php
}
?>


### end content - please not remove this line
