### begin - web of initial - do not remove/modify this line

<?php

$ports[] = '80';
$ports[] = '443';

$defaultdocroot = "/home/kloxo/httpd/default";

if ($indexorder) {
    $indexorder = implode(', ', $indexorder);
}

// MR -- for future purpose, apache user have uid 50000
// $userinfoapache = posix_getpwnam('apache');
// $fpmportapache = (50000 + $userinfoapache['uid']);
$fpmportapache = 50000;
?>

UrlToolkit {
    ToolkitID = findindexfile
    Match ^([^?]*)/(\?.*)?$ Rewrite $1/index.php$2 Continue
    RequestURI isfile Return
    Match ^([^?]*)/index\.php(\?.*)?$ Rewrite $1/index.html$2 Continue
    RequestURI isfile Return
    Match ^([^?]*)/index\.html(\?.*)?$ Rewrite $1/index.htm$2 Continue
    RequestURI isfile Return
    Match ^([^?]*)/index\.htm(\?.*)?$ Rewrite $1/$2 Continue
}

UrlToolkit {
    ToolkitID = permalink
    RequestURI exists Return
    ## process for 'special dirs' of Kloxo-MR
    Match ^/(stats|awstats|awstatscss|awstats)(/|$) Return
    ## process for 'special dirs' of Kloxo-MR
    Match ^/(cp|error|webmail|__kloxo|kloxo|kloxononssl|cgi-bin)(/|$) Return
    Match ^/(css|files|images|js)(/|$) Return
    Match ^/(favicon.ico|robots.txt|sitemap.xml)$ Return
    Match /(.*)\?(.*) Rewrite /index.php?path=$1&$2
    Match .*\?(.*) Rewrite /index.php?$1
    Match .* Rewrite /index.php
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
?>

FastCGIserver {
    FastCGIid = php_for_apache
    ConnectTo = /home/php-fpm/sock/apache.sock
    Extension = php
}

#CGIhandler = /usr/bin/perl:pl
CGIhandler = /usr/bin/php-cgi:php
#CGIhandler = /usr/bin/python:py
#CGIhandler = /usr/bin/ruby:rb
#CGIhandler = /usr/bin/ssi-cgi:shtml
CGIextension = php
<?php
foreach ($certnamelist as $ip => $certname) {
    $count = 0;

    foreach ($ports as &$port) {
?>

Binding {
    BindingId = port_<?php echo $ports[$count]; ?>

    Port = <?php echo $ports[$count]; ?>

    #Interface = 0.0.0.0
    MaxKeepAlive = 3600
    TimeForRequest = 3600
    MaxRequestSize = 102400
    ## not able more than 100MB
    MaxUploadSize = 100
<?php
        if ($count !== 0) {
?>

    #RequiredCA = /home/kloxo/httpd/ssl/<?php echo $certname; ?>.ca
    SSLcertFile = /home/kloxo/httpd/ssl/<?php echo $certname; ?>.pem
<?php
        }
?>
}

### 'default' config
<?php
        if ($count === 0) {
?>
#VirtualHost {
<?php
        } else {
?>
VirtualHost {
<?php
        }
?>
    Hostname = 0.0.0.0

    WebsiteRoot = <?php echo $defaultdocroot; ?>


    EnablePathInfo = yes
<?php
        if ($count !== 0) {
?>

    #RequiredCA = /home/kloxo/httpd/ssl/<?php echo $certname; ?>.ca
    SSLcertFile = /home/kloxo/httpd/ssl/<?php echo $certname; ?>.pem
<?php
        }
?>

    TimeForCGI = 3600

    Alias = /error:/home/kloxo/httpd/error
    ErrorHandler = 401:/error/401.html
    ErrorHandler = 403:/error/403.html
    ErrorHandler = 404:/error/404.html
    ErrorHandler = 501:/error/501.html
    ErrorHandler = 503:/error/503.html
<?php
        if ($reverseproxy) {
?>

    ReverseProxy ^/.* http://127.0.0.1:<?php echo $ports[0]; ?>/
<?php
        } else {
?>

    UseFastCGI = php_for_apache
<?php
        }
?>

    #StartFile = index.php
    UseToolkit = findindexfile
    UseToolkit = permalink
<?php
        if ($count === 0) {
?>
#}
<?php
        } else {
?>
}
<?php
        }

        $count++;
    }
}
?>

### end - web of initial - do not remove/modify this line
