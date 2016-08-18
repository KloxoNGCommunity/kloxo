<?php
$altconf = "/opt/configs/nginx/conf/customs/{$domainname}.conf";

if (file_exists($altconf)) {
	print("## MR - Use '{$altconf}' instead this file");
	return;
}
?>
### begin - web of '<?= $domainname; ?>' - do not remove/modify this line

<?php

$conn_timeout = "fastcgi_connect_timeout {$timeout}s;
\tfastcgi_send_timeout {$timeout}s;
\tfastcgi_read_timeout {$timeout}s;

\tproxy_connect_timeout {$timeout}s;
\tproxy_send_timeout {$timeout}s;
\tproxy_read_timeout {$timeout}s;";

$webdocroot = $rootpath;

if (!isset($phpselected)) {
	$phpselected = 'php';
}

if (!isset($timeout)) {
	$timeout = '300';
}

if (($webcache === 'none') || (!$webcache)) {
	$ports[] = '80';
	$ports[] = '443';
} else {
	$ports[] = '8080';
	$ports[] = '8443';
}

$disabledocroot = "/home/kloxo/httpd/disable";
$cpdocroot = "/home/kloxo/httpd/cp";

$globalspath = "/opt/configs/nginx/conf/globals";

if (file_exists("{$globalspath}/custom.gzip.conf")) {
	$gzip_base = "custom.gzip";
} else if (file_exists("{$globalspath}/gzip.conf")) {
	$gzip_base = "gzip";
}

if (file_exists("{$globalspath}/custom.ssl_base.conf")) {
	$ssl_base = "custom.ssl_base";
} else if (file_exists("{$globalspath}/ssl_base.conf")) {
	$ssl_base = "ssl_base";
}

if (file_exists("{$globalspath}/custom.acme-challenge.conf")) {
	$acme_challenge = "custom.acme-challenge";
} else if (file_exists("{$globalspath}/acme-challenge.conf")) {
	$acme_challenge = "acme-challenge";
}

$listens = array('listen_nonssl', 'listen_ssl');

$switches = array('', '_ssl');

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

$serveralias = "{$domainname} www.{$domainname}";

$excludedomains = array("cp", "webmail");

$excludealias = implode("|", $excludedomains);

if ($wildcards) {
	$serveralias .= "\n\t\t*.{$domainname}";
}

if ($serveraliases) {
	foreach ($serveraliases as &$sa) {
		$serveralias .= "\n\t\t{$sa}";
	}
}

if ($parkdomains) {
	foreach ($parkdomains as $pk) {
		$pa = $pk['parkdomain'];
		$serveralias .= "\n\t\t{$pa} www.{$pa}";
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

if ($indexorder) {
	$indexorder = implode(' ', $indexorder);
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
		$biptemp[] = $bip;
	}
	$blockips = $biptemp;
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

exec("ip -6 addr show", $out);

if (count($out) > 0) {
	$IPv6Enable = true;
} else {
	$IPv6Enable = false;
}

if (file_exists("{$globalspath}/custom.generic.conf")) {
	$generic = 'custom.generic';
} else if (file_exists("{$globalspath}/generic.conf")) {
	$generic = 'generic';
}

if (file_exists("{$globalspath}/custom.header_base.conf")) {
	$header_base = "custom.header_base";
} else if (file_exists("{$globalspath}/header_base.conf")) {
	$header_base = "header_base";
}

if (file_exists("{$globalspath}/custom.header_ssl.conf")) {
	$header_ssl = "custom.header_ssl";
} else if (file_exists("{$globalspath}/header_ssl.conf")) {
	$header_ssl = "header_ssl";
}

if ($disabled) {
	$cpdocroot = $webmaildocroot = $webdocroot = $disabledocroot;
}

foreach ($certnamelist as $ip => $certname) {
	$count = 0;

	foreach ($listens as &$listen) {
		$protocol = ($count === 0) ? "http://" : "https://";
?>

## cp for '<?= $domainname; ?>'
server {
	#disable_symlinks if_not_owner;

	include '<?= $globalspath; ?>/<?= $listen; ?>.conf';

	include '<?= $globalspath; ?>/<?= $gzip_base; ?>.conf';

	include '<?= $globalspath; ?>/<?= $header_base; ?>.conf';
<?php
		if ($count !== 0) {
?>

	include '<?= $globalspath; ?>/<?= $ssl_base; ?>.conf';

	ssl_certificate <?= $certname; ?>.pem;
	ssl_certificate_key <?= $certname; ?>.key;
<?php
			if (file_exists("{$certname}.ca")) {
?>
	ssl_trusted_certificate <?= $certname; ?>.ca;

	include '<?= $globalspath; ?>/<?= $header_ssl; ?>.conf';
<?php
			}
		}
?>

	server_name cp.<?= $domainname; ?>;

	include '<?= $globalspath; ?>/<?= $acme_challenge; ?>.conf';

	index <?= $indexorder; ?>;

	set $var_domain 'cp.<?= $domainname; ?>';
	set $var_rootdir '<?= $cpdocroot; ?>';

	root $var_rootdir;

	set $var_user 'apache';
	set $var_fpmport '<?= $fpmportapache; ?>';
	set $var_phpselected 'php';

	<?= $conn_timeout; ?>


	include '<?= $globalspath; ?>/switch_standard<?= $switches[$count]; ?>.conf';
}


## webmail for '<?= $domainname; ?>'
server {
	#disable_symlinks if_not_owner;

	include '<?= $globalspath; ?>/<?= $listen; ?>.conf';

	include '<?= $globalspath; ?>/<?= $gzip_base; ?>.conf';

	include '<?= $globalspath; ?>/<?= $header_base; ?>.conf';
<?php
		if ($count !== 0) {
?>

	include '<?= $globalspath; ?>/<?= $ssl_base; ?>.conf';

	ssl_certificate <?= $certname; ?>.pem;
	ssl_certificate_key <?= $certname; ?>.key;
<?php
			if (file_exists("{$certname}.ca")) {
?>
	ssl_trusted_certificate <?= $certname; ?>.ca;

	include '<?= $globalspath; ?>/<?= $header_ssl; ?>.conf';
<?php
			}
		}
?>

	server_name webmail.<?= $domainname; ?>;

	include '<?= $globalspath; ?>/<?= $acme_challenge; ?>.conf';

	index <?= $indexorder; ?>;

	set $var_domain 'webmail.<?= $domainname; ?>';
	set $var_rootdir '<?= $webmaildocroot; ?>';

	root $var_rootdir;

	set $var_user 'apache';
	set $var_fpmport '<?= $fpmportapache; ?>';
	set $var_phpselected 'php';

	<?= $conn_timeout; ?>


	include '<?= $globalspath; ?>/switch_standard<?= $switches[$count]; ?>.conf';
<?php

		if ($webmailremote) {
?>

	if ($host != '<?= $webmailremote; ?>') {
		rewrite ^/(.*) '<?= $protocol; ?><?= $webmailremote; ?>/$1' permanent;
	}
<?php
		}

?>
}


## web for '<?= $domainname; ?>'
server {
	#disable_symlinks if_not_owner;

	include '<?= $globalspath; ?>/<?= $listen; ?>.conf';

	include '<?= $globalspath; ?>/<?= $gzip_base; ?>.conf';

	include '<?= $globalspath; ?>/<?= $header_base; ?>.conf';
<?php
		if ($count !== 0) {
			if ($enablessl) {
?>

	include '<?= $globalspath; ?>/<?= $ssl_base; ?>.conf';

	ssl_certificate <?= $certname; ?>.pem;
	ssl_certificate_key <?= $certname; ?>.key;
<?php
				if (file_exists("{$certname}.ca")) {
?>
	ssl_trusted_certificate <?= $certname; ?>.ca;

	include '<?= $globalspath; ?>/<?= $header_ssl; ?>.conf';
<?php
				}
			}
		}

		if ($ip === '*') {
?>

	server_name <?= $serveralias; ?>;
<?php
		} else {
?>

	server_name <?= $serveralias; ?> <?= $ip; ?>;
<?php
		}
?>

	include '<?= $globalspath; ?>/<?= $acme_challenge; ?>.conf';

	index <?= $indexorder; ?>;

	set $var_domain <?= $domainname; ?>;
<?php
		if ($wwwredirect) {
?>

	if ($host ~* ^(<?= $domainname; ?>)$) {
		rewrite ^/(.*) '<?= $protocol; ?>www.<?= $domainname; ?>/$1' permanent;
	}
<?php
		}

		if (($count === 0) && ($httpsredirect)) {
?>

	return 301 https://$host$request_uri;
<?php
		}

		if ($wildcards) {
?>

	set $var_rootdir '<?= $webdocroot; ?>';
<?php
			foreach ($excludedomains as &$ed) {
?>

	if ($host ~* ^(<?= $ed; ?>.<?= $domainname; ?>)$) {
<?php
				if ($ed !== 'webmail') {
?>
		set $var_rootdir '/home/kloxo/httpd/<?= $ed; ?>/';
<?php
				} else {
					if ($webmailremote) {
?>
		rewrite ^/(.*) '<?= $protocol; ?><?= $webmailremote; ?>/$1' permanent;
<?php
					} else {
?>
		set $var_rootdir '<?= $webmaildocroot; ?>';
<?php
					}
				}
?>
	}
<?php
			}
		} else {
?>

	set $var_rootdir '<?= $webdocroot; ?>';
<?php
		}
?>

	root $var_rootdir;
<?php
		if ($enablecgi) {
?>

	include '<?= $globalspath; ?>/cgi.conf';
<?php
		}

		if ($redirectionlocal) {
			foreach ($redirectionlocal as $rl) {
?>

	location ~ ^<?= $rl[0]; ?>/(.*)$ {
		alias <?= str_replace("//", "/", $rl[1]); ?>/$1;
	}
<?php
			}
		}

		if ($redirectionremote) {
			foreach ($redirectionremote as $rr) {
				if ($rr[0] === '/') {
					$rr[0] = '';
				}

				if ($rr[2] === 'both') {
?>

	rewrite ^<?= $rr[0]; ?>/(.*) '<?= $protocol; ?><?= $rr[1]; ?>/$1' permanent;
<?php
				} else {
					$protocol2 = ($rr[2] === 'https') ? "https://" : "http://";
?>

	rewrite ^<?= $rr[0]; ?>/(.*) '<?= $protocol2; ?><?= $rr[1]; ?>/$1' permanent;
<?php
				}
			}
		}
?>

	set $var_user '<?= $user; ?>';
	set $var_fpmport '<?= $fpmport; ?>';
	set $var_phpselected '<?= $phpselected; ?>';

	<?= $conn_timeout; ?>

<?php
		if ($enablestats) {
?>

	include '<?= $globalspath; ?>/stats_log.conf';

	include '<?= $globalspath; ?>/stats.conf';
<?php
			if ($statsprotect) {
?>

	include '<?= $globalspath; ?>/dirprotect_stats.conf';
<?php
			}
		}

		if ($nginxextratext) {
?>

	# Extra Tags - begin
	<?= $nginxextratext; ?>

	# Extra Tags - end
<?php
		}

		if ((!$reverseproxy) && (file_exists("{$globalspath}/{$domainname}.conf"))) {
			if ($enablephp) {
?>

	include '<?= $globalspath; ?>/<?= $domainname; ?>.conf';
<?php
			}
		} else {
			if ($wildcards) {
				if (($reverseproxy) && ($webselected === 'front-end')) {
					if ($enablephp) {
?>

	include '<?= $globalspath; ?>/php-fpm_wildcards<?= $switches[$count]; ?>.conf';
<?php
					}
				} else {
?>

	include '<?= $globalspath; ?>/switch_wildcards<?= $switches[$count]; ?>.conf';
<?php
				}
			} else {
				if (($reverseproxy) && ($webselected === 'front-end')) {
					if ($enablephp) {
?>

	include '<?= $globalspath; ?>/php-fpm_standard<?= $switches[$count]; ?>.conf';
<?php
					}
				} else {
?>

	include '<?= $globalspath; ?>/switch_standard<?= $switches[$count]; ?>.conf';
<?php
				}
			}
		}

		if (!$reverseproxy) {
			if ($dirprotect) {
				foreach ($dirprotect as $k) {
					$protectpath = $k['path'];
					$protectauthname = $k['authname'];
					$protectfile = str_replace('/', '_', $protectpath) . '_';
?>

	set $var_std_protectpath '<?= $protectpath; ?>';
	set $var_std_protectauthname '<?= $protectauthname; ?>';
	set $var_std_protectfile '<?= $protectfile; ?>';

	include '<?= $globalspath; ?>/dirprotect_standard.conf';
<?php
				}
			}
		}

		if ($blockips) {
?>

	location ^~ /(.*) {
<?php
			foreach ($blockips as &$bip) {
?>
		deny   <?= $bip; ?>;
<?php
			}
?>
		allow  all;
			}
<?php
	}
?>

	set $var_kloxoportssl '<?= $kloxoportssl; ?>';
	set $var_kloxoportnonssl '<?= $kloxoportnonssl; ?>';

	include '<?= $globalspath; ?>/<?= $generic; ?>.conf';
<?php
		if (intval($microcache_time) > 0) {
?>

	## for microcache
	fastcgi_cache_valid 200 <?= $microcache_time; ?>s;
	fastcgi_cache_use_stale updating;
	fastcgi_max_temp_file_size 10M;

	proxy_cache_valid 200 <?= $microcache_time; ?>s;
	proxy_cache_use_stale updating;
	proxy_max_temp_file_size 10M;
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

				if ($redirpath) {
					if ($disabled) {
						$redirfullpath = $disablepath;
					} else {
						$redirfullpath = str_replace('//', '/', $webdocroot . '/' . $redirpath);
					}
?>

## web for redirect '<?= $redirdomainname; ?>'
server {
	#disable_symlinks if_not_owner;

	include '<?= $globalspath; ?>/<?= $listen; ?>.conf';

	include '<?= $globalspath; ?>/<?= $gzip_base; ?>.conf';

	include '<?= $globalspath; ?>/<?= $header_base; ?>.conf';
<?php
					if ($count !== 0) {
						if ($enablessl) {
?>

	include '<?= $globalspath; ?>/<?= $ssl_base; ?>.conf';

	ssl_certificate <?= $certname; ?>.pem;
	ssl_certificate_key <?= $certname; ?>.key;
<?php
							if (file_exists("{$certname}.ca")) {
?>
	ssl_trusted_certificate <?= $certname; ?>.ca;

	include '<?= $globalspath; ?>/<?= $header_ssl; ?>.conf';
<?php
							}
						}
					}
?>

	server_name <?= $redirdomainname; ?> www.<?= $redirdomainname; ?>;

	include '<?= $globalspath; ?>/<?= $acme_challenge; ?>.conf';

	index <?= $indexorder; ?>;

	set $var_domain '<?= $redirdomainname; ?>';
	set $var_rootdir '<?= $redirfullpath; ?>';

	root $var_rootdir;
<?php

					if ($enablecgi) {
?>

	include '<?= $globalspath; ?>/cgi.conf';
<?php
					}
?>

	set $var_user '<?= $user; ?>';
	set $var_fpmport '<?= $fpmport; ?>';
	set $var_phpselected '<?= $phpselected; ?>';

	<?= $conn_timeout; ?>

<?php

					if (($reverseproxy) && ($webselected === 'front-end')) {
?>

	include '<?= $globalspath; ?>/php-fpm_standard<?= $switches[$count]; ?>.conf';
<?php
					} else {
?>

	include '<?= $globalspath; ?>/switch_standard<?= $switches[$count]; ?>.conf';
<?php
					}
?>
}

<?php
				} else {
					if ($disabled) {
						$redirfullpath = $disablepath;
					} else {
						$redirfullpath = $webdocroot;
					}
?>

## web for redirect '<?= $redirdomainname; ?>'
server {
	#disable_symlinks if_not_owner;

	include '<?= $globalspath; ?>/<?= $listen; ?>.conf';

	include '<?= $globalspath; ?>/<?= $gzip_base; ?>.conf';

	include '<?= $globalspath; ?>/<?= $header_base; ?>.conf';
<?php
					if ($count !== 0) {
						if ($enablessl) {
?>

	include '<?= $globalspath; ?>/<?= $ssl_base; ?>.conf';

	ssl_certificate <?= $certname; ?>.pem;
	ssl_certificate_key <?= $certname; ?>.key;
<?php
							if (file_exists("{$certname}.ca")) {
?>
	ssl_trusted_certificate <?= $certname; ?>.ca;

	include '<?= $globalspath; ?>/<?= $header_ssl; ?>.conf';
<?php
							}
						}
					}
?>

	server_name <?= $redirdomainname; ?> www.<?= $redirdomainname; ?>;

	include '<?= $globalspath; ?>/<?= $acme_challenge; ?>.conf';

	index <?= $indexorder; ?>;

	set $var_domain '<?= $redirdomainname; ?>';

	set $var_rootdir '<?= $redirfullpath; ?>';

	root $var_rootdir;
<?php
					if ($enablecgi) {
?>

	include '<?= $globalspath; ?>/cgi.conf';
<?php
					}
?>

	if ($host != '<?= $domainname; ?>') {
		rewrite ^/(.*) '<?= $protocol; ?><?= $domainname; ?>/$1';
	}
}

<?php
				}
			}
		}

		if ($parkdomains) {
			foreach ($parkdomains as $dompark) {
				$parkdomainname = $dompark['parkdomain'];
				$webmailmap = ($dompark['mailflag'] === 'on') ? true : false;

				if (($webmailremote) || ($webmailmap)) {
?>

## webmail for parked '<?= $parkdomainname; ?>'
server {
	#disable_symlinks if_not_owner;

	include '<?= $globalspath; ?>/<?= $listen; ?>.conf';

	include '<?= $globalspath; ?>/<?= $gzip_base; ?>.conf';

	include '<?= $globalspath; ?>/<?= $header_base; ?>.conf';
<?php
					if ($count !== 0) {
?>

	include '<?= $globalspath; ?>/<?= $ssl_base; ?>.conf';

	ssl_certificate <?= $certname; ?>.pem;
	ssl_certificate_key <?= $certname; ?>.key;
<?php
						if (file_exists("{$certname}.ca")) {
?>
	ssl_trusted_certificate <?= $certname; ?>.ca;

	include '<?= $globalspath; ?>/<?= $header_ssl; ?>.conf';
<?php
						}
					}
?>

	server_name webmail.<?= $parkdomainname; ?>;

	include '<?= $globalspath; ?>/<?= $acme_challenge; ?>.conf';
<?php
					if ($webmailremote) {
?>

	if ($host != '<?= $webmailremote; ?>') {
		rewrite ^/(.*) '<?= $protocol; ?><?= $webmailremote; ?>/$1';
	}
<?php
					}
?>
}

<?php

				} else {
?>

## No mail map for parked '<?= $parkedomainname; ?>'

<?php
				}

			}
		}

		if ($domainredirect) {
			foreach ($domainredirect as $domredir) {
				$redirdomainname = $domredir['redirdomain'];
				$webmailmap = ($domredir['mailflag'] === 'on') ? true : false;

				if ($webmailremote) {
?>

## webmail for redirect '<?=$redirdomainname;?>'
server {
	#disable_symlinks if_not_owner;

	include '<?= $globalspath; ?>/<?= $listen; ?>.conf';

	include '<?= $globalspath; ?>/<?= $gzip_base; ?>.conf';

	include '<?= $globalspath; ?>/<?= $header_base; ?>.conf';
<?php
					if ($count !== 0) {
?>

	include '<?= $globalspath; ?>/<?= $ssl_base; ?>.conf';

	ssl_certificate <?= $certname; ?>.pem;
	ssl_certificate_key <?= $certname; ?>.key;
<?php
						if (file_exists("{$certname}.ca")) {
?>
	ssl_trusted_certificate <?= $certname; ?>.ca;

	include '<?= $globalspath; ?>/<?= $header_ssl; ?>.conf';
<?php
						}
					}
?>

	server_name webmail.<?= $redirdomainname; ?>;

	include '<?= $globalspath; ?>/<?= $acme_challenge; ?>.conf';
<?php
					if ($webmailremote) {
?>

	if ($host != '<?= $webmailremote; ?>') {
		rewrite ^/(.*) '<?= $protocol; ?><?= $webmailremote; ?>/$1';
	}
<?php
					}
?>
}

<?php

				} else {
?>

## No mail map for redirect '<?= $redirdomainname; ?>'

<?php
				}

			}
		}

		$count++;
	}
}
?>

### end - web of '<?= $domainname; ?>' - do not remove/modify this line
