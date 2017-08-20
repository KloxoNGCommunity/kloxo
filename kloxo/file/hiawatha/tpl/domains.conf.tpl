<?php
$altconf = "/opt/configs/hiawatha/conf/customs/{$domainname}.conf";

if (file_exists($altconf)) {
	print("## MR - Use '{$altconf}' instead this file");
	return;
}
?>
### begin - web of '<?= $domainname; ?>' - do not remove/modify this line

<?php

$altconf = "/opt/configs/hiawatha/conf/customs/{$domainname}.conf";

if (file_exists($altconf)) {
	print("## MR - Use {$altconf} instead this file");
	return;
}

$webdocroot = $rootpath;

$mfile = "{$webdocroot}/{$microcache_insert_into}";

if (file_exists($mfile)) {
	@exec("sed -i '/X-Hiawatha-Cache:/d' {$mfile}");

	if (intval($microcache_time) > -1) {
		@exec("sed -i '1s/^/<" . "?php header(\"X-Hiawatha-Cache: {$microcache_time}\"); " . "?>\\n/' {$mfile}");
	} else {
		@exec("sed -i '/header(\"X-Hiawatha-Cache:/d' {$mfile}");
	}
}

if ($general_header) {
	$x = array();

	$gh = explode("\n", trim($general_header, "\n"));

	foreach ($gh as $k => $v) {
		$v = str_replace(" ", "", str_replace("\"", "", str_replace(" \"", ":", $v)));
		$x[] = "\tCustomHeader = {$v}";
	}

	$x[] = "\tCustomHeader = X-Supported-By:Kloxo-MR 7.0";

	$general_header_text = implode("\n", $x);
}

if ($https_header) {
	$x = array();

	$hh = explode("\n", trim($https_header, "\n"));

	foreach ($hh as $k => $v) {
		$v = str_replace(" ", "", str_replace("\"", "", str_replace(" \"", ":", $v)));

		$x[] = "\tCustomHeader = {$v}";
	}

	$https_header_text = implode("\n", $x);
}

$error_handler = "Alias = /error:/home/kloxo/httpd/error
\tErrorHandler = 401:/error/401.html
\tErrorHandler = 403:/error/403.html
\tErrorHandler = 404:/error/404.html
\tErrorHandler = 501:/error/501.html
\tErrorHandler = 503:/error/503.html";

if (!isset($phpselected)) {
	$phpselected = 'php';
}

if (!isset($timeout)) {
	$timeout = '300';
}

$domclean = str_replace('-', '_', str_replace('.', '_', $domainname));

if (($webcache === 'none') || (!$webcache)) {
	$ports[] = '80';
	$ports[] = '443';
} else {
	$ports[] = '8080';
	$ports[] = '8443';
}

$reverseports = array('30080', '30443');
$protocols = array('http', 'https');

$portnames = array("nonssl", "ssl");

foreach ($certnamelist as $ip => $certname) {
	$sslpath = "/home/kloxo/ssl";

	if (file_exists("{$sslpath}/{$domainname}.key")) {
		$certnamelist[$ip] = "{$sslpath}/{$domainname}";
	} else {
		$certnamelist[$ip] = "{$sslpath}/{$certname}";
	}
}

$statsapp = $stats['app'];
$statsprotect = ($stats['protect']) ? true : false;

$serveralias = "www.{$domainname}";

if ($wildcards) {
	$serveralias .= ", *.{$domainname}";
}

if ($serveraliases) {
	foreach ($serveraliases as &$sa) {
		$serveralias .= ", {$sa}";
	}
}

if ($parkdomains) {
	foreach ($parkdomains as $pk) {
		$pa = $pk['parkdomain'];
		$serveralias .= ", {$pa}, www.{$pa}";
	}
}

if ($webmailapp) {
	if ($webmailapp === '--Disabled--') {
		$webmaildocroot = "/home/kloxo/httpd/disable";
	} else {
		$webmaildocroot = "/home/kloxo/httpd/webmail/{$webmailapp}";
	}
} else {
	$webmaildocroot = "/home/kloxo/httpd/webmail";
}

$webmailremote = str_replace("http://", "", $webmailremote);
$webmailremote = str_replace("https://", "", $webmailremote);

$cpdocroot = "/home/kloxo/httpd/cp";

if ($statsapp === 'webalizer') {
	$statsdocroot = "/home/httpd/{$domainname}/webstats";
} else {
	$statsdocroot_base = "/home/kloxo/httpd/awstats/wwwroot";
	$statsdocroot = "{$statsdocroot_base}/cgi-bin";
}

if ($blockips) {
	$biptemp = array();
	foreach ($blockips as &$bip) {
		if (strpos($bip, ".*.*.*") !== false) {
			$bip = str_replace(".*.*.*", ".0.0/8", $bip);
		}
		if (strpos($bip, ".*.*") !== false) {
			$bip = str_replace(".*.*", ".0.0/16", $bip);
		}
		if (strpos($bip, ".*") !== false) {
			$bip = str_replace(".*", ".0/24", $bip);
		}
		$biptemp[] = 'deny ' . $bip;
	}
	$blockips = $biptemp;

	$blockips = implode(', ', $blockips);
}

$userinfo = posix_getpwnam($user);

if ($userinfo) {
	$fpmport = (50000 + $userinfo['uid']);
} else {
	return false;
}

// MR -- for future purpose, apache user have uid 50000
// $userinfoapache = posix_getpwnam('apache');
// $fpmportapache = (50000 + $userinfoapache['uid']);
$fpmportapache = 50000;

$disabledocroot = "/home/kloxo/httpd/disable";

$domcleaner = str_replace('-', '_', str_replace('.', '_', $domainname));

if ($disabled) {
	$cpdocroot = $webmaildocroot = $webdocroot = $disabledocroot;
}

//if (!$reverseproxy) {
?>

Directory {
	DirectoryID = cache_expire_<?=$domclean;?>

	Path = /
<?php
	if (intval($static_files_expire) > -1) {
?>
	Extensions = jpeg, jpg, gif, png, ico, css, js, pdf
	ExpirePeriod <?=$static_files_expire;?> days
<?php
	} else {
?>
	# No static files expire
<?php
	}
?>
}
<?php
if ($enablestats) {
?>

Directory {
	DirectoryId = stats_dir_<?=$domclean;?>

	Path = /
<?php
	if ($statsprotect) {
?>
	PasswordFile = basic:/home/httpd/<?=$domainname;?>/__dirprotect/__stats
<?php
	}
?>
}
<?php
}

if ($dirprotect) {
	foreach ($dirprotect as $k) {
		$protectpath = $k['path'];
		$protectauthname = $k['authname'];
		$protectfile = str_replace('/', '_', $protectpath) . '_';
?>

Directory {
	Path = /<?=$protectpath;?>

	PasswordFile = basic:/home/httpd/<?=$domainname;?>/__dirprotect/<?=$protectfile;?>

}

<?php
	}
}
//}
?>

UrlToolkit {
	ToolkitID = findindexfile_<?=$domcleaner;?>

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
<?php
if ($webmailremote) {
	foreach ($protocols as $k => $v) {
?>

UrlToolkit {
	ToolkitID = redirect_<?=str_replace('.', '_', $webmailremote);?>_<?=$v;?>

	#RequestURI exists Return
	Match ^/(.*) Redirect <?=$v;?>://<?=$webmailremote;?>/$1
}
<?php
	}
}
?>

UrlToolkit {
	ToolkitID = cgi_<?=$domcleaner;?>

	Match ^/.*\.(pl|cgi|py)(/|$) UseFastCGI cgi_apache
}
<?php
foreach ($protocols as $k => $v) {
?>

UrlToolkit {
	ToolkitID = redirect_<?=$domcleaner;?>_<?=$v;?>

	#RequestURI exists Return
<?php
	if ($redirectionremote) {
		foreach ($redirectionremote as $rr) {
			if ($rr[2] === 'both') {
				if ($rr[0] === '/') {
					$rr0 = '^/(.*)';
				} else {
					$rr0 = '^/'.$rr[0].'/(.*)';
				}
?>
	Match <?=$rr0;?> Redirect <?=$v;?>://<?=$rr[1];?>/$1
<?php
			} else {
				$protocol2 = ($rr[2] === 'https') ? "https" : "http";
?>
	Match <?=$rr0;?> Redirect <?=$protocol2;?>://<?=$rr[1];?>/$1
<?php
			}
		}
	}
?>
	Match ^/kloxo(|/(.*))$ Redirect <?=$v;?>://<?=$domainname;?>:<?=$kloxoportssl;?>/$1
	Match ^/webmail(|/(.*))$ Redirect <?=$v;?>://webmail.<?=$domainname;?>/$1
	Match ^/cp(|/(.*))$ Redirect <?=$v;?>://cp.<?=$domainname;?>/$1

	Match ^/stats(|/(.*))$ Redirect <?=$v;?>://stats.<?=$domainname;?>/$1
<?php
	if ($wwwredirect) {
?>

	Match ^/(.*)$ Redirect <?=$v;?>://www.<?=$domainname;?>/$1
<?php
	}
?>
}
<?php
	if ($statsapp === 'awstats') {
?>

UrlToolkit {
	ToolkitID = redirect_stats_<?=$domcleaner;?>_<?=$v;?>

	#Match .* Rewrite /awstats.pl?config=<?=$domainname;?>

}
<?php
	}
}
?>

FastCGIserver {
	FastCGIid = php_<?=$domclean;?>

<?php
if ($disabled) {
?>
	ConnectTo = /opt/configs/php-fpm/sock/<?=$phpselected;?>-apache.sock
<?php
} else {
?>
	ConnectTo = /opt/configs/php-fpm/sock/<?=$phpselected;?>-<?=$user;?>.sock
<?php
}
?>
	Extension = php
	SessionTimeout = <?=$timeout;?>

}

<?php
foreach ($certnamelist as $ip => $certname) {
	$count = 0;

	foreach ($ports as &$port) {

	//	if ($count !== 0) { continue; }

		$protocol = ($count === 0) ? "http" : "https";

?>

## cp for '<?=$domainname;?>'
VirtualHost {
	RequiredBinding = port_<?=$portnames[$count];?>


	Alias = /.well-known:/var/run/letsencrypt/.well-known

<?=$general_header_text;?>

<?php
		if ($count !== 0) {
?>

	TLScertFile = <?=$certname;?>.pem
<?php
			if (file_exists("{$certname}.ca")) {
?>
	#RequiredCA = <?=$certname;?>.ca

<?=$https_header_text;?>

<?php
			}
?>

	SecureURL = no
	#MinTLSversion = 1.0
<?php
		}
?>

	set var_user = apache

	FollowSymlinks = no

	Hostname = cp.<?=$domainname;?>


	WebsiteRoot = <?=$cpdocroot;?>


	EnablePathInfo = yes

	TimeForCGI = <?=$timeout;?>


	<?=$error_handler;?>


	ExecuteCGI = yes
<?php
/*
		if (($reverseproxy) && ($webselected === 'back-end')) {
?>

	UseLocalConfig = no

	UseToolkit = block_shellshock, block_httpoxy

	#ReverseProxy ^/.* http://127.0.0.1:30080/ <?=$timeout;?>

	#ReverseProxy !\.(pl|cgi|py|rb|shmtl) <?=$protocols[$count];?>://127.0.0.1:<?=$reverseports[$count];?>/ <?=$timeout;?>

	#ReverseProxy ^/.* <?=$protocols[$count];?>://127.0.0.1:<?=$reverseports[$count];?>/ <?=$timeout;?>

<?php
		} else {
*/
?>

	UseLocalConfig = yes

	UseFastCGI = php_apache
	UseToolkit = block_shellshock, block_httpoxy, findindexfile_<?=$domcleaner;?>, permalink
<?php
//		}
?>

	#StartFile = index.php
}


## stats for '<?=$domainname;?>'
VirtualHost {
	RequiredBinding = port_<?=$portnames[$count];?>


	Alias = /.well-known:/var/run/letsencrypt/.well-known

<?=$general_header_text;?>

<?php
		if ($count !== 0) {
?>

	TLScertFile = <?=$certname;?>.pem
<?php
			if (file_exists("{$certname}.ca")) {
?>
	#RequiredCA = <?=$certname;?>.ca

<?=$https_header_text;?>

<?php
			}
?>

	SecureURL = no
	#MinTLSversion = 1.0
<?php
		}
?>

	set var_user = apache

	FollowSymlinks = no

	Hostname = stats.<?=$domainname;?>


	WebsiteRoot = <?=$statsdocroot;?>

<?php
		if ($statsapp === 'awstats') {
?>

	Alias = /awstatscss:<?=$statsdocroot_base;?>/css
	Alias = /awstatsicons:<?=$statsdocroot_base;?>/icon
<?php
		}
?>

	EnablePathInfo = yes

	TimeForCGI = <?=$timeout;?>


	<?=$error_handler;?>


	ExecuteCGI = yes
<?php
/*
		if (($reverseproxy) && ($webselected === 'back-end')) {
?>

	UseLocalConfig = no

	UseToolkit = block_shellshock, block_httpoxy

	#ReverseProxy ^/.* http://127.0.0.1:30080/ <?=$timeout;?>

	#ReverseProxy !\.(pl|cgi|py|rb|shmtl) <?=$protocols[$count];?>://127.0.0.1:<?=$reverseports[$count];?>/ <?=$timeout;?>

	ReverseProxy ^/.* <?=$protocols[$count];?>://127.0.0.1:<?=$reverseports[$count];?>/ <?=$timeout;?>

<?php
		} else {
*/
?>

	UseLocalConfig = yes

	UseFastCGI = php_apache
<?php
			if ($statsapp === 'awstats') {
?>
	UseToolkit = block_shellshock, block_httpoxy, findindexfile_<?=$domcleaner;?>, redirect_stats_<?=$domcleaner;?>_<?=$protocol;?>

<?php
			} else {
?>
	UseToolkit = block_shellshock, block_httpoxy, findindexfile_<?=$domcleaner;?>

<?php
			}
//		}
?>
	UseDirectory = stats_dir_<?=$domclean;?>


	#StartFile = index.php
}

## webmail for '<?=$domainname;?>'
VirtualHost {
	RequiredBinding = port_<?=$portnames[$count];?>


	Alias = /.well-known:/var/run/letsencrypt/.well-known

<?=$general_header_text;?>

<?php
		if ($count !== 0) {
?>

	TLScertFile = <?=$certname;?>.pem
<?php
			if (file_exists("{$certname}.ca")) {
?>
	#RequiredCA = <?=$certname;?>.ca

<?=$https_header_text;?>

<?php
			}
?>

	SecureURL = no
	#MinTLSversion = 1.0
<?php
		}
?>

	set var_user = apache

	FollowSymlinks = no

	Hostname = webmail.<?=$domainname;?>, mail.<?=$domainname;?>


	WebsiteRoot = <?=$webmaildocroot;?>


	EnablePathInfo = yes

	TimeForCGI = <?=$timeout;?>


	<?=$error_handler;?>


	ExecuteCGI = yes
<?php
/*
		if (($reverseproxy) && ($webselected === 'back-end')) {
?>

	UseLocalConfig = no

	UseToolkit = block_shellshock, block_httpoxy

	ReverseProxy ^/.* http://127.0.0.1:30080/ <?=$timeout;?>

	#ReverseProxy !\.(pl|cgi|py|rb|shmtl) <?=$protocols[$count];?>://127.0.0.1:<?=$reverseports[$count];?>/ <?=$timeout;?>

	#ReverseProxy ^/.* <?=$protocols[$count];?>://127.0.0.1:<?=$reverseports[$count];?>/ <?=$timeout;?>

<?php
		} else {
*/
?>

	UseLocalConfig = yes

	UseFastCGI = php_apache
<?php
			if ($webmailremote) {
?>
	UseToolkit = block_shellshock, block_httpoxy, redirect_<?=str_replace('.', '_', $webmailremote);?>_<?=$protocols[$count];?>

<?php
			} else {
?>
	UseToolkit = block_shellshock, block_httpoxy, findindexfile_<?=$domcleaner;?>, permalink
<?php
			}
//		}
?>

	#StartFile = index.php
}


## web for '<?=$domainname;?>'
VirtualHost {
	RequiredBinding = port_<?=$portnames[$count];?>


	Alias = /.well-known:/var/run/letsencrypt/.well-known

<?=$general_header_text;?>

<?php
		if ($count !== 0) {
			if ($enablessl) {
?>

	TLScertFile = <?=$certname;?>.pem
<?php
				if (file_exists("{$certname}.ca")) {
?>
	#RequiredCA = <?=$certname;?>.ca

<?=$https_header_text;?>

<?php
				}
?>

	SecureURL = no
	#MinTLSversion = 1.0
<?php
			}
		}
?>

	set var_user = <?=$user;?>


	FollowSymlinks = no
<?php
		if (($count === 0) && ($httpsredirect)) {
?>

	RequireTLS = yes
<?php
		}

		if ($ip !== '*') {
?>

	Hostname = <?=$domainname;?>, <?=$serveralias;?>, <?=$ip;?>

<?php
		} else {
?>

	Hostname = <?=$domainname;?>, <?=$serveralias;?>

<?php
		}
?>

	WebsiteRoot = <?=$webdocroot;?>


	UseDirectory = cache_expire_<?=$domclean;?>


	EnablePathInfo = yes

	Alias = /__kloxo:/home/<?=$user;?>/kloxoscript
<?php
		if ($enablecgi) {
?>

	#WrapCGI = <?=$user;?>_wrapper
	#UseToolkit = cgi_<?=$domcleaner;?>
<?php
		}

		if ($redirectionlocal) {
			foreach ($redirectionlocal as $rl) {
?>

	Alias = <?=$rl[0];?>:<?=$webdocroot;?><?=$rl[1];?>

<?php
			}
		}

		if ($enablestats) {
?>

	AccessLogfile = /home/httpd/<?=$domainname;?>/stats/<?=$domainname;?>-custom_log
	ErrorLogfile = /home/httpd/<?=$domainname;?>/stats/<?=$domainname;?>-error_log
<?php
		//	if ((!$reverseproxy) || (($reverseproxy) && ($webselected === 'front-end'))) {
/*
				if ($statsapp === 'awstats') {
?>

	Alias = /awstats:/home/kloxo/httpd/awstats/wwwroot/cgi-bin

	Alias = /awstatscss:/home/kloxo/httpd/awstats/wwwroot/css
	Alias = /awstatsicons:/home/kloxo/httpd/awstats/wwwroot/icon
<?php
				} elseif ($statsapp === 'webalizer') {
?>

	Alias = /stats:/home/httpd/<?=$domainname;?>/webstats
<?php
				}
*/
				if ($blockips) {
?>

			# BanlistMask = <?=$blockips;?>

			AccessList = <?=$blockips;?>

<?php
				}
?>

	UserWebsites = yes

	TimeForCGI = <?=$timeout;?>


	<?=$error_handler;?>


	ExecuteCGI = yes
<?php
				if (($reverseproxy) && ($webselected === 'back-end')) {
?>

	UseLocalConfig = no
<?php
					if ($enablephp) {
?>

	UseToolkit = block_shellshock, block_httpoxy

	#ReverseProxy ^/.* http://127.0.0.1:30080/ <?=$timeout;?>

	#ReverseProxy !\.(pl|cgi|py|rb|shmtl) <?=$protocols[$count];?>://127.0.0.1:<?=$reverseports[$count];?>/ <?=$timeout;?>

	ReverseProxy ^/.* <?=$protocols[$count];?>://127.0.0.1:<?=$reverseports[$count];?>/ <?=$timeout;?>

<?php
					}
				} else {
?>

	UseLocalConfig = yes

	UseFastCGI = php_<?=$domclean;?>

	UseToolkit = block_shellshock, block_httpoxy, redirect_<?=$domcleaner;?>_<?=$protocol;?>, findindexfile_<?=$domcleaner;?>, permalink
<?php
				}
		//	}
		}
?>

	#StartFile = index.php
<?php
		if ($dirindex) {
?>

	ShowIndex = yes
<?php
		} else {
?>

	ShowIndex = no
<?php
		}

		if (intval($microcache_time) > 0) {
?>

	## MR -- it's not work from here. Need insert to php file (usually index.php)
	#CustomHeader = X-Hiawatha-Cache:<?=$microcache_time;?>

<?php
		}
?>
}

<?php
		if ($domainredirect) {
			foreach ($domainredirect as $domredir) {
				$redirdomainname = $domredir['redirdomain'];
				$redirpath = ($domredir['redirpath']) ? $domredir['redirpath'] : null;
				$webmailmap = ($domredir['mailflag'] === 'on') ? true : false;

				$randnum = rand(0, 32767);

				if ($disabled) {
					$redirfullpath = $disabledocroot;
				} else {
					if ($redirpath) {
						$redirfullpath = str_replace('//', '/', $webdocroot . '/' . $redirpath);
					} else {
						$redirfullpath = $webdocroot;
					}
				}
?>

## web for redirect '<?=$redirdomainname;?>'
VirtualHost {
	RequiredBinding = port_<?=$portnames[$count];?>


	Alias = /.well-known:/var/run/letsencrypt/.well-known

<?=$general_header_text;?>

<?php
				if ($count !== 0) {
					if ($enablessl) {
?>

	TLScertFile = <?=$certname;?>.pem
<?php
						if (file_exists("{$certname}.ca")) {
?>
	#RequiredCA = <?=$certname;?>.ca

<?=$https_header_text;?>

<?php
						}
?>

	SecureURL = no
	#MinTLSversion = 1.0
<?php
					}
				}
?>

	set var_user = <?=$user;?>


	FollowSymlinks = no

	Hostname = <?=$redirdomainname;?>, www.<?=$redirdomainname;?>


	WebsiteRoot = <?=$redirfullpath;?>


	EnablePathInfo = yes

	UserWebsites = yes

	TimeForCGI = <?=$timeout;?>


	<?=$error_handler;?>


	ExecuteCGI = yes

<?php
				if (($reverseproxy) && ($webselected === 'back-end')) {
?>

	UseLocalConfig = no

	UseToolkit = block_shellshock, block_httpoxy

	#ReverseProxy ^/.* http://127.0.0.1:30080/ <?=$timeout;?>

	#ReverseProxy !\.(pl|cgi|py|rb|shmtl) <?=$protocols[$count];?>://127.0.0.1:<?=$reverseports[$count];?>/ <?=$timeout;?>

	ReverseProxy ^/.* <?=$protocols[$count];?>://127.0.0.1:<?=$reverseports[$count];?>/ <?=$timeout;?>

<?php
				} else {
?>

	UseLocalConfig = yes

	UseFastCGI = php_<?=$domclean;?>

	UseToolkit = block_shellshock, block_httpoxy, findindexfile_<?=$domcleaner;?>, permalink
<?php
				}
?>

	#StartFile = index.php
}

<?php
			}
		}

		if ($parkdomains) {
			foreach ($parkdomains as $dompark) {
				$parkdomainname = $dompark['parkdomain'];
				$webmailmap = ($dompark['mailflag'] === 'on') ? true : false;

				if (($webmailremote) || ($webmailmap)) {
?>

## webmail for parked '<?=$parkdomainname;?>'
VirtualHost {
	RequiredBinding = port_<?=$portnames[$count];?>


	Alias = /.well-known:/var/run/letsencrypt/.well-known

<?=$general_header_text;?>

<?php
					if ($count !== 0) {
?>

	TLScertFile = <?=$certname;?>.pem
<?php
						if (file_exists("{$certname}.ca")) {
?>
	#RequiredCA = <?=$certname;?>.ca

<?=$https_header_text;?>

<?php
						}
?>

	SecureURL = no
	#MinTLSversion = 1.0
<?php
					}
?>

	set var_user = apache

	FollowSymlinks = no

	Hostname = webmail.<?=$parkdomainname;?>, mail.<?=$parkdomainname;?>


	WebsiteRoot = <?=$webmaildocroot;?>


	TimeForCGI = <?=$timeout;?>


	<?=$error_handler;?>


	ExecuteCGI = yes

<?php
					if (($reverseproxy) && ($webselected === 'back-end')) {
?>

	UseLocalConfig = no

	UseToolkit = block_shellshock, block_httpoxy

	ReverseProxy ^/.* http://127.0.0.1:30080/ <?=$timeout;?>

	#ReverseProxy !\.(pl|cgi|py|rb|shmtl) <?=$protocols[$count];?>://127.0.0.1:<?=$reverseports[$count];?>/ <?=$timeout;?>

	#ReverseProxy ^/.* <?=$protocols[$count];?>://127.0.0.1:<?=$reverseports[$count];?>/ <?=$timeout;?>

<?php
					} else {
?>

	UseLocalConfig = yes

	UseFastCGI = php_apache
<?php
						if ($webmailremote) {
?>
	UseToolkit = block_shellshock, block_httpoxy, redirect_<?=str_replace('.', '_', $webmailremote);?>_<?=$protocols[$count];?>

<?php
						} else {
?>
	UseToolkit = block_shellshock, block_httpoxy, findindexfile_<?=$domcleaner;?>, permalink
<?php
						}
					}
?>

	#StartFile = index.php
}

<?php
				} else {
?>

## No mail map for parked '<?=$parkdomainname;?>'

<?php
				}

			}
		}

		if ($domainredirect) {
			foreach ($domainredirect as $domredir) {
				$redirdomainname = $domredir['redirdomain'];
				$webmailmap = ($domredir['mailflag'] === 'on') ? true : false;


				if (($webmailremote) || ($webmailmap)) {
?>

## webmail for redirect '<?=$redirdomainname;?>'
VirtualHost {
	RequiredBinding = port_<?=$portnames[$count];?>


	Alias = /.well-known:/var/run/letsencrypt/.well-known

<?=$general_header_text;?>

<?php
					if ($count !== 0) {
?>

	TLScertFile = <?=$certname;?>.pem
<?php
						if (file_exists("{$certname}.ca")) {
?>
	#RequiredCA = <?=$certname;?>.ca

<?=$https_header_text;?>

<?php
						}
?>

	SecureURL = no
	#MinTLSversion = 1.0
<?php
					}
?>

	set var_user = apache

	FollowSymlinks = no

	Hostname = webmail.<?=$redirdomainname;?>, mail.<?=$redirdomainname;?>


	WebsiteRoot = <?=$webmaildocroot;?>


	EnablePathInfo = yes

	TimeForCGI = <?=$timeout;?>


	<?=$error_handler;?>


	ExecuteCGI = yes

<?php
					if (($reverseproxy) && ($webselected === 'back-end')) {
?>

	UseLocalConfig = no

	UseToolkit = block_shellshock, block_httpoxy

	ReverseProxy ^/.* http://127.0.0.1:30080/ <?=$timeout;?>

	#ReverseProxy !\.(pl|cgi|py|rb|shmtl) <?=$protocols[$count];?>://127.0.0.1:<?=$reverseports[$count];?>/ <?=$timeout;?>

	#ReverseProxy ^/.* <?=$protocols[$count];?>://127.0.0.1:<?=$reverseports[$count];?>/ <?=$timeout;?>

<?php
					} else {
?>

	UseLocalConfig = yes

	UseFastCGI = php_apache
<?php
						if ($webmailremote) {
?>
	UseToolkit = block_shellshock, block_httpoxy, redirect_<?=str_replace('.', '_', $webmailremote);?>_<?=$protocols[$count];?>

<?php
						} else {
?>
	UseToolkit = block_shellshock, block_httpoxy, findindexfile_<?=$domcleaner;?>, permalink
<?php
						}
					}
?>

	#StartFile = index.php
}

<?php
				} else {
?>

## No mail map for redirect '<?=$redirdomainname;?>'

<?php
				}

			}
		}

		$count++;
	}
}
?>

### end - web of '<?=$domainname;?>' - do not remove/modify this line
