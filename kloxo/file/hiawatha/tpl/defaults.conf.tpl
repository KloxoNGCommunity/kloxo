### begin - web of initial - do not remove/modify this line


## MR - NOTE:
## add 'header("X-Hiawatha-Cache: 10");' to index.php

<?php

$error_handler = "Alias = /error:/home/kloxo/httpd/error
#ErrorHandler = 400:/error/400.html
ErrorHandler = 401:/error/401.html
ErrorHandler = 403:/error/403.html
ErrorHandler = 404:/error/404.html
#ErrorHandler = 500:/error/500.html
ErrorHandler = 501:/error/501.html
#ErrorHandler = 502:/error/502.html
ErrorHandler = 503:/error/503.html
#ErrorHandler = 504:/error/504.html";

if (!isset($phpselected)) {
	$phpselected = 'php';
}

if (!isset($timeout)) {
	$timeout = '300';
}

if (!file_exists("/var/run/letsencrypt/.well-known/acme-challenge")) {
	exec("mkdir -p /var/run/letsencrypt/.well-known/acme-challenge");
}

$srcconfpath = "/opt/configs/hiawatha/etc/conf";
$trgtconfpath = "/etc/hiawatha";

if ($reverseproxy) {
	$custom_conf = getLinkCustomfile($srcconfpath, "hiawatha_proxy.conf");
	copy($custom_conf, "{$trgtconfpath}/hiawatha.conf");
} else {
	$custom_conf = getLinkCustomfile($srcconfpath, "hiawatha_standard.conf");
	copy($custom_conf, "{$trgtconfpath}/hiawatha.conf");
}

if (($webcache === 'none') || (!$webcache)) {
	$ports[] = '80';
	$ports[] = '443';
} else {
	$ports[] = '8080';
	$ports[] = '8443';
}

$reverseports = array('30080', '30443');

$portnames = array('nonssl', 'ssl');

foreach ($certnamelist as $ip => $certname) {
	$certnamelist[$ip] = "/home/kloxo/ssl/{$certname}";
}

$defaultdocroot = "/home/kloxo/httpd/default";

// MR -- for future purpose, apache user have uid 50000
// $userinfoapache = posix_getpwnam('apache');
// $fpmportapache = (50000 + $userinfoapache['uid']);
$fpmportapache = 50000;
?>

UrlToolkit {
	ToolkitID = monitor
	RequestURI isfile Return
	Match ^/(css|files|fonts|images|js)(/|$) Return
	Match ^/(favicon.ico|robots.txt)$ Return
	Match [^?]*(\?.*)? Rewrite /index.php$1
}

UrlToolkit {
	ToolkitID = block_shellshock
	#Header * \(\)\s+\{ DenyAccess
	Header User-Agent \(\)\s*\{ DenyAccess
	Header Referer \(\)\s*\{ DenyAccess
}

## MR -- ref: https://www.hiawatha-webserver.org/weblog/115
UrlToolkit {
	ToolkitID = block_httpoxy
	Header Proxy .* DenyAccess
}

UrlToolkit {
	ToolkitID = findindexfile
<?php
$v2 = "";

foreach ($indexorder as $k => $v) {
?>
	Match ^([^?]*)/<?=$v2;?>(\?.*)?$ Rewrite $1/<?=$v;?>$2 Continue
	RequestURI isfile Return
<?php
	$v2 = str_replace(".", "\.", $v);
}
?>
	Match ^([^?]*)/<?=$v2;?>(\?.*)?$ Rewrite $1/$2 Continue
}

UrlToolkit {
	ToolkitID = permalink
	RequestURI exists Return
	RequestURI isfile Return
	## process for 'special dirs' of Kloxo-MR
	Match ^/(stats|cp|error|webmail|__kloxo|kloxo|kloxononssl|cgi-bin)(/|$) Return
	Match ^/(css|files|images|js)(/|$) Return
	Match ^/(favicon.ico|robots.txt|sitemap.xml)$ Return
	Match ^/.well-known/(.*) Return
	Match /(.*)\?(.*) Rewrite /index.php?path=$1&$2
	Match .*\?(.*) Rewrite /index.php?$1
	Match .* Rewrite /index.php
}

FastCGIserver {
	FastCGIid = php_apache

	ConnectTo = /opt/configs/php-fpm/sock/php-apache.sock
	Extension = php
	SessionTimeout = <?=$timeout;?>

}

FastCGIserver {
	FastCGIid = cgi_apache

	ConnectTo = /tmp/fcgiwrap.sock
	Extension = pl,cgi,py,rb,shtml
	SessionTimeout = <?=$timeout;?>

}

CGIhandler = /usr/bin/perl:pl
#CGIhandler = /usr/bin/php-cgi:php
CGIhandler = /usr/bin/python:py
CGIhandler = /usr/bin/ruby:rb
CGIhandler = /usr/bin/ssi-cgi:shtml
#CGIextension = cgi
<?php
foreach ($certnamelist as $ip => $certname) {
	$count = 0;

	foreach ($ports as &$port) {
?>

Binding {
	BindingId = port_<?=$portnames[$count];?>

	Port = <?=$ports[$count];?>

	#Interface = 0.0.0.0
	MaxKeepAlive = 120
	TimeForRequest = <?=$timeout;?>

	MaxRequestSize = 2096128
	MaxUploadSize = 2047
<?php
		if ($count !== 0) {
?>

	TLScertFile = <?=$certname;?>.pem
<?php
			if (file_exists("{$certname}.ca")) {
?>
	#RequiredCA = <?=$certname;?>.ca
<?php
			}
		}
?>
}
<?php
		$count++;
	}
}
?>


Alias = /.well-known:/var/run/letsencrypt/.well-known

### 'default' config
set var_user = apache

Hostname = 0.0.0.0, ::
WebsiteRoot = <?=$defaultdocroot;?>


EnablePathInfo = yes
## MR -- need symlink because make possible access to http://ip/domainname
FollowSymlinks = yes

TimeForCGI = <?=$timeout;?>


UseLocalConfig = yes

<?=$error_handler;?>

<?php
if ($reverseproxy) {
?>

UseToolkit = block_shellshock, block_httpoxy

#ReverseProxy !\.(pl|cgi|py|rb|shmtl) http://127.0.0.1:30080/ <?=$timeout;?> keep-alive
ReverseProxy ^/.* http://127.0.0.1:30080/ <?=$timeout;?> keep-alive
<?php
} else {
?>

#UserDirectory = public_html
#UserWebsites = yes

UseFastCGI = php_var_user
UseToolkit = block_shellshock, block_httpoxy, findindexfile, permalink
<?php
}
?>

#StartFile = index.php


### end - web of initial - do not remove/modify this line
