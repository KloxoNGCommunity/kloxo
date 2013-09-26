### begin - web of initial - do not remove/modify this line

<?php

$ports[] = '80';
$ports[] = '443';

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

// MR -- for future purpose, apache user have uid 50000
// $userinfoapache = posix_getpwnam('apache');
// $fpmportapache = (50000 + $userinfoapache['uid']);
$fpmportapache = 50000;

foreach ($certnamelistfree as $ip => $certname) {
?>

$SERVER["socket"] == "<?php echo $ip; ?>:<?php echo $ports[1]; ?>" {

    ssl.engine = "enable"

    ssl.pemfile = "/home/kloxo/httpd/ssl/<?php echo $certname; ?>.pem"
    ssl.ca-file = "/home/kloxo/httpd/ssl/<?php echo $certname; ?>.ca"
    ssl.use-sslv2 = "disable"

}

<?php
}
?>

$HTTP["host"] =~ "^cp\.*" { 

    var.rootdir = "/home/kloxo/httpd/cp/"

    server.document-root = var.rootdir

    index-file.names = ( <?php echo $indexorder; ?> )
<?php
    if ($reverseproxy) {
?>

    include "<?php echo $globalspath; ?>/<?php echo $proxyconf; ?>"
<?php
    } else {
?>

    var.user = "apache"
    var.fpmport = "<?php echo $fpmportapache; ?>"

    include "<?php echo $globalspath; ?>/<?php echo $phpfpmconf; ?>"
<?php
    }
?>

}


$HTTP["host"] =~ "^default\.*" { 

    var.rootdir = "/home/kloxo/httpd/default/"

    server.document-root = var.rootdir

    index-file.names = ( <?php echo $indexorder; ?> )
<?php
    if ($reverseproxy) {
?>

    include "<?php echo $globalspath; ?>/<?php echo $proxyconf; ?>"
<?php
    } else {
?>

    var.user = "apache"
    var.fpmport = "<?php echo $fpmportapache; ?>"

    include "<?php echo $globalspath; ?>/<?php echo $phpfpmconf; ?>"
<?php
    }
?>

}


### end - web of initial - do not remove/modify this line
