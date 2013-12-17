### begin - web of initial - do not remove/modify this line

<?php

if (($webcache === 'none') || (!$webcache)) {
    $ports[] = '80';
    $ports[] = '443';
} else {
    $ports[] = '8080';
    $ports[] = '8443';
}

$iplist = array('*');

$defaultdocroot = "/home/kloxo/httpd/default";
$cpdocroot = "/home/kloxo/httpd/cp";

$globalspath = "/home/nginx/conf/globals";

if ($indexorder) {
    $indexorder = implode(' ', $indexorder);
}

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

if (file_exists("{$globalspath}/custom.perl.conf")) {
    $perlconf = 'custom.perl.conf';
} else {
    $perlconf = 'perl.conf';
}

// MR -- for future purpose, apache user have uid 50000
// $userinfoapache = posix_getpwnam('apache');
// $fpmportapache = (50000 + $userinfoapache['uid']);
$fpmportapache = 50000;

exec("ip -6 addr show", $out);

if ($out[0]) {
    $IPv6Enable = true;
} else {
    $IPv6Enable = false;
}

foreach ($certnamelist as $ip => $certname) {
    $count = 0;

    foreach ($ports as &$port) {
?>

## 'cp' config
server {
<?php
        if ($ip === '*') {
            if ($IPv6Enable) {
?>
    listen 0.0.0.0:<?php echo $port; ?>;
    listen [::]:<?php echo $port; ?>;
<?php
            } else {
?>
    listen <?php echo $ip; ?>:<?php echo $port; ?>;
<?php
            }
        } else {
?>
    listen <?php echo $ip; ?>:<?php echo $port; ?>;
<?php
        }

        if ($count !== 0) {
?>

    ssl on;
    ssl_certificate /home/kloxo/httpd/ssl/<?php echo $certname; ?>.crt;
    ssl_certificate_key /home/kloxo/httpd/ssl/<?php echo $certname; ?>.key;
    ssl_protocols SSLv3 TLSv1 TLSv1.1 TLSv1.2;
    ssl_ciphers HIGH:!aNULL:!MD5;
<?php
        }
?>

    server_name cp.*;

    set $domain '';

    index <?php echo $indexorder; ?>;

    set $rootdir '<?php echo $cpdocroot; ?>';

    root $rootdir;

    set $user 'apache';
<?php
        if ($reverseproxy) {
?>

    include '<?php echo $globalspath; ?>/<?php echo $proxyconf; ?>';
<?php
        } else {
?>

    include '<?php echo $globalspath; ?>/<?php echo $perlconf; ?>';

    set $fpmport '<?php echo $fpmportapache; ?>';

    include '<?php echo $globalspath; ?>/<?php echo $phpfpmconf; ?>';
<?php

        }
?>
}


## 'default' config
server {
<?php
        if ($ip === '*') {
            if ($IPv6Enable) {
?>
    listen 0.0.0.0:<?php echo $port; ?> default;
    listen [::]:<?php echo $port; ?> default;
<?php
            } else {
?>
    listen <?php echo $ip; ?>:<?php echo $port; ?> default;
<?php
            }
        } else {
?>
    listen <?php echo $ip; ?>:<?php echo $port; ?> default;
<?php
        }

        if ($count !== 0) {
?>

    ssl on;
    ssl_certificate /home/kloxo/httpd/ssl/<?php echo $certname; ?>.crt;
    ssl_certificate_key /home/kloxo/httpd/ssl/<?php echo $certname; ?>.key;
    ssl_protocols SSLv3 TLSv1 TLSv1.1 TLSv1.2;
    ssl_ciphers HIGH:!aNULL:!MD5;
<?php
        }

?>

    server_name _;

    set $domain '';

    index <?php echo $indexorder; ?>;

    set $rootdir '<?php echo $defaultdocroot; ?>';

    root $rootdir;

    set $user 'apache';
<?php
        if ($reverseproxy) {
?>

    include '<?php echo $globalspath; ?>/<?php echo $proxyconf; ?>';
<?php
        } else {
?>

    include '<?php echo $globalspath; ?>/<?php echo $perlconf; ?>';

    set $fpmport '<?php echo $fpmportapache; ?>';

    include '<?php echo $globalspath; ?>/<?php echo $phpfpmconf; ?>';
<?php

        }

        $count++;
?>
}

<?php
    }
}
?>

### end - web of initial - do not remove/modify this line
