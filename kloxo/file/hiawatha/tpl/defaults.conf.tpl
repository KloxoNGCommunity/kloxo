### begin - web of initial - do not remove/modify this line

<?php

if (($webcache === 'none') || (!$webcache)) {
	$ports[] = '80';
	$ports[] = '443';
} else {
	$ports[] = '8080';
	$ports[] = '8443';
}

foreach ($certnamelist as $ip => $certname) {
	$certnamelist[$ip] = "/home/kloxo/httpd/ssl/{$certname}";
}

$defaultdocroot = "/home/kloxo/httpd/default";

// MR -- for future purpose, apache user have uid 50000
// $userinfoapache = posix_getpwnam('apache');
// $fpmportapache = (50000 + $userinfoapache['uid']);
$fpmportapache = 50000;
?>

UrlToolkit {
	ToolkitID = findindexfile
<?php
	$v2 = "";

	foreach ($indexorder as $k => $v) {
?>
	Match ^([^?]*)/<?php echo $v2; ?>(\?.*)?$ Rewrite $1/<?php echo $v; ?>$2 Continue
	RequestURI isfile Return
<?php
		$v2 = str_replace(".", "\.", $v);
	}
?>
	Match ^([^?]*)/<?php echo $v2; ?>(\?.*)?$ Rewrite $1/$2 Continue
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

<?php
foreach ($certnamelist as $ip => $certname) {
	$count = 0;

	foreach ($ports as &$port) {
?>

Binding {
	BindingId = port_<?php echo $ports[$count]; ?>

	Port = <?php echo $ports[$count]; ?>

	#Interface = 0.0.0.0
	MaxKeepAlive = 120
	TimeForRequest = 480
	MaxRequestSize = 102400
	## not able more than 100MB; hiawatha-9.3-2+ able until 2GB
	MaxUploadSize = 2000
<?php
		if ($count !== 0) {
			if (file_exists("{$certname}.ca")) {
?>

	RequiredCA = <?php echo $certname; ?>.ca
<?php
			}
?>

	SSLcertFile = <?php echo $certname; ?>.pem
<?php
		}
?>
}

CGIhandler = /usr/bin/perl:pl
#CGIhandler = /usr/bin/php5-cgi:php
CGIhandler = /usr/bin/python:py
CGIhandler = /usr/bin/ruby:rb
CGIhandler = /usr/bin/ssi-cgi:shtml
#CGIextension = cgi

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
	UseGZfile = yes
	FollowSymlinks = no
	
	Hostname = 0.0.0.0

	WebsiteRoot = <?php echo $defaultdocroot; ?>

	EnablePathInfo = yes
<?php
		if ($count !== 0) {
			if (file_exists("{$certname}.ca")) {
?>

	RequiredCA = <?php echo $certname; ?>.ca
<?php
			}
?>

	SSLcertFile = <?php echo $certname; ?>.pem
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

	#ReverseProxy ^/.* http://127.0.0.1:30080/ 300
	ReverseProxy (^\/$|^\/.*\.php.*$|^\/([a-z0-9-]+\/?)*$) http://127.0.0.1:30080/ 300
<?php
		} else {
?>

	#UserDirectory = public_html
	#UserWebsites = yes

	UseFastCGI = php_for_apache
<?php
		}
?>

	#StartFile = index.php
<?php
		if ($reverseproxy) {
?>
	UseToolkit = findindexfile
<?php
		} else {
?>
	UseToolkit = findindexfile, permalink
<?php
		}
?>
	## still not work for 'microcache'
	## add 'header("X-Hiawatha-Cache: 10");' to index.php
	#CustomHeader = X-Hiawatha-Cache:10
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
