### begin - web of '<?php echo $setdefaults; ?>.*' - do not remove/modify this line

<?php
if ($reverseproxy) {
    $ports[] = '30080';
    $ports[] = '30443';
} else {
    $ports[] = '80';
    $ports[] = '443';
}

if ($setdefaults === 'webmail') {
    if ($webmailappdefault) {
        $docroot = "/home/kloxo/httpd/webmail/{$webmailappdefault}";
    } else {
        $docroot = "/home/kloxo/httpd/webmail";
    }
} else {
    $docroot = "/home/kloxo/httpd/{$setdefaults}";
}

if ($indexorder) {
    $indexorder = implode(', ', $indexorder);
}

// MR -- for future purpose, apache user have uid 50000
// $userinfoapache = posix_getpwnam('apache');
// $fpmportapache = (50000 + $userinfoapache['uid']);
$fpmportapache = 50000;

if ($setdefaults === 'init') {
    foreach ($certnamelist as $ip => $certname) {
        if ($ip === '*') {
?>

Binding {
    BindingId = port_nonssl
    Port = 80
    #Interface = 0.0.0.0
    MaxKeepAlive = 3600
    TimeForRequest = 3600
    MaxRequestSize = 102400
    ## not able more than 100MB
    MaxUploadSize = 100
}

Binding {
    BindingId = port_ssl
    Port = 443
    #Interface = 0.0.0.0
    MaxKeepAlive = 3600
    TimeForRequest = 3600
    MaxRequestSize = 102400
    ## not able more than 100MB
    MaxUploadSize = 100
    #RequiredCA = /home/kloxo/httpd/ssl/<?php echo $certname; ?>.ca
    SSLcertFile = /home/kloxo/httpd/ssl/<?php echo $certname; ?>.pem
}
<?php
        }
    }
?>

FastCGIserver {
    FastCGIid = php_for_apache
    ConnectTo = /home/php-fpm/sock/apache.sock
    Extension = php
}
<?php
    foreach ($userlist as &$user) {
        $userinfo = posix_getpwnam($user);

        if (!$userinfo) { continue; }
?>

FastCGIserver {
    FastCGIid = php_for_<?php echo $user; ?>

    ConnectTo = /home/php-fpm/sock/<?php echo $user; ?>.sock
    Extension = php
}
<?php
    }
} elseif ($setdefaults === 'ssl') {
?>

### No needed declare here because certfile directly write to defaults and domains configs

<?php
} else {
    foreach ($certnamelist as $ip => $certname) {
        $count = 0;

        foreach ($ports as &$port) {
            if (($setdefaults === 'default') && ($count === 0)) {
?>

### '<?php echo $setdefaults; ?>' config
Hostname = 0.0.0.0
WebsiteRoot = <?php echo $docroot; ?>

#StartFile = <?php echo $indexorder; ?>

StartFile = index.php

UseFastCGI = php_for_apache
TimeForCGI = 3600

<?php
            } else {
?>

### '<?php echo $setdefaults; ?>' config
VirtualHost {
<?php
                if (($setdefaults === 'default') && ($count !== 0)) {
?>
    Hostname = 0.0.0.0
<?php
                } else {
?>
    Hostname = <?php echo $setdefaults; ?>

<?php
                }
?>
    WebsiteRoot = <?php echo $docroot; ?>

    #StartFile = <?php echo $indexorder; ?>

    StartFile = index.php
<?php
            }

            if ($count !== 0) {
?>

    #RequiredCA = /home/kloxo/httpd/ssl/<?php echo $certname; ?>.ca
    SSLcertFile = /home/kloxo/httpd/ssl/<?php echo $certname; ?>.pem
<?php
            }

            if (($setdefaults !== 'default') || ($count !== 0)) {
?>
    UseFastCGI = php_for_apache
    TimeForCGI = 3600
}

<?php
            }

            $count++;
        }
    }
}
?>

### end - web of '<?php echo $setdefaults; ?>.*' - do not remove/modify this line
