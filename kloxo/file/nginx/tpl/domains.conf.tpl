### begin - web of '<?php echo $domainname; ?>' - do not remove/modify this line

<?php

$listens = array('listen_nonssl', 'listen_ssl');

foreach ($certnamelist as $ip => $certname) {
	if (file_exists("/home/{$user}/ssl/{$domainname}.key")) {
		$certnamelist[$ip] = "/home/{$user}/ssl/{$domainname}";
	} else {
		$certnamelist[$ip] = "/home/kloxo/httpd/ssl/{$certname}";
	}
}

$statsapp = $stats['app'];

$statsprotect = ($stats['protect']) ? true : false;

$serveralias = "{$domainname} www.{$domainname}";

$excludedomains = array("cp","webmail");

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

if ($out[0]) {
	$IPv6Enable = true;
} else {
	$IPv6Enable = false;
}

$disabledocroot = "/home/kloxo/httpd/disable";
$cpdocroot = "/home/kloxo/httpd/cp";

$globalspath = "/opt/configs/nginx/conf/globals";

if (file_exists("{$globalspath}/custom.generic.conf")) {
	$genericconf = 'custom.generic.conf';
} else {
	$genericconf = 'generic.conf';
}

if ($disabled) {
	$user = 'apache';
}

$count = 0;

foreach ($certnamelist as $ip => $certname) {
	$count = 0;

	foreach ($listens as &$listen) {
		$protocol = ($count === 0) ? "http://" : "https://";

		if ($disabled) {
?>

## cp for '<?php echo $domainname; ?>'
server {
	#disable_symlinks if_not_owner;
	
	include '<?php echo $globalspath; ?>/<?php echo $listen; ?>.conf';
<?php
			if ($count !== 0) {
?>

	ssl on;
	ssl_certificate <?php echo $certname; ?>.pem;
	ssl_certificate_key <?php echo $certname; ?>.key;
	ssl_protocols SSLv3 TLSv1 TLSv1.1 TLSv1.2;
	ssl_ciphers HIGH:!aNULL:!MD5;
<?php
			}
?>

	server_name cp.<?php echo $domainname; ?>;

	index <?php echo $indexorder; ?>;

	set $var_rootdir '<?php echo $disabledocroot; ?>';

	root $var_rootdir;

	set $var_user 'apache';

	set $var_fpmport '<?php echo $fpmportapache; ?>';

	include '<?php echo $globalspath; ?>/switch_standard.conf';
}


## webmail for '<?php echo $domainname; ?>'
server {
	#disable_symlinks if_not_owner;

	include '<?php echo $globalspath; ?>/<?php echo $listen; ?>.conf';
<?php
			if ($count !== 0) {
?>

	ssl on;
	ssl_certificate <?php echo $certname; ?>.pem;
	ssl_certificate_key <?php echo $certname; ?>.key;
	ssl_protocols SSLv3 TLSv1 TLSv1.1 TLSv1.2;
	ssl_ciphers HIGH:!aNULL:!MD5;
<?php
			}
?>

	server_name webmail.<?php echo $domainname; ?>;

	index <?php echo $indexorder; ?>;

	set $var_rootdir '<?php echo $disabledocroot; ?>';

	root $var_rootdir;

	set $var_user 'apache';
	set $var_fpmport '<?php echo $fpmportapache; ?>';

	include '<?php echo $globalspath; ?>/switch_standard.conf';
}

<?php
		} else {
?>

## cp for '<?php echo $domainname; ?>'
server {
	#disable_symlinks if_not_owner;
	
	include '<?php echo $globalspath; ?>/<?php echo $listen; ?>.conf';
<?php
			if ($count !== 0) {
?>

	ssl on;
	ssl_certificate <?php echo $certname; ?>.pem;
	ssl_certificate_key <?php echo $certname; ?>.key;
	ssl_protocols SSLv3 TLSv1 TLSv1.1 TLSv1.2;
	ssl_ciphers HIGH:!aNULL:!MD5;
<?php
			}
?>

	server_name cp.<?php echo $domainname; ?>;

	index <?php echo $indexorder; ?>;

	set $var_rootdir '<?php echo $cpdocroot; ?>';

	root $var_rootdir;

	set $var_user 'apache';

	set $var_fpmport '<?php echo $fpmportapache; ?>';

	include '<?php echo $globalspath; ?>/switch_standard.conf';
}

<?php
			if ($webmailremote) {
?>

## webmail for '<?php echo $domainname; ?>'
server {
	#disable_symlinks if_not_owner;

	include '<?php echo $globalspath; ?>/<?php echo $listen; ?>.conf';
<?php
				if ($count !== 0) {
?>

	ssl on;
	ssl_certificate <?php echo $certname; ?>.pem;
	ssl_certificate_key <?php echo $certname; ?>.key;
	ssl_protocols SSLv3 TLSv1 TLSv1.1 TLSv1.2;
	ssl_ciphers HIGH:!aNULL:!MD5;
<?php
				}
?>

	server_name webmail.<?php echo $domainname; ?>;

	if ($host != '<?php echo $webmailremote; ?>') {
		rewrite ^/(.*) '<?php echo $protocol; ?><?php echo $webmailremote; ?>/$1' permanent;
	}
}

<?php
			} else {
?>

## webmail for '<?php echo $domainname; ?>'
server {
	#disable_symlinks if_not_owner;

	include '<?php echo $globalspath; ?>/<?php echo $listen; ?>.conf';
<?php
				if ($count !== 0) {
?>

	ssl on;
	ssl_certificate <?php echo $certname; ?>.pem;
	ssl_certificate_key <?php echo $certname; ?>.key;
	ssl_protocols SSLv3 TLSv1 TLSv1.1 TLSv1.2;
	ssl_ciphers HIGH:!aNULL:!MD5;
<?php
				}
?>

	server_name webmail.<?php echo $domainname; ?>;

	index <?php echo $indexorder; ?>;

	set $var_rootdir '<?php echo $webmaildocroot; ?>';

	root $var_rootdir;

	set $var_user 'apache';
	set $var_fpmport '<?php echo $fpmportapache; ?>';

	include '<?php echo $globalspath; ?>/switch_standard.conf';
}

<?php
			}
		}
?>

## web for '<?php echo $domainname; ?>'
server {
	#disable_symlinks if_not_owner;
<?php
		if ($enablecgi) {
?>

	## MR -- 'enable-cgi' not implementing yet
<?php
		}

		if ($ip === '*') {
?>

	include '<?php echo $globalspath; ?>/<?php echo $listen; ?>.conf';
<?php
		} else {
?>

	listen <?php echo $ip; ?>:<?php echo $port; ?>;
<?php
		}

		if ($count !== 0) {
?>

	ssl on;
	ssl_certificate <?php echo $certname; ?>.pem;
	ssl_certificate_key <?php echo $certname; ?>.key;
	ssl_protocols SSLv3 TLSv1 TLSv1.1 TLSv1.2;
	ssl_ciphers HIGH:!aNULL:!MD5;
<?php
		}
?>

	server_name <?php echo $serveralias; ?>;

	index <?php echo $indexorder; ?>;

	set $var_domain '<?php echo $domainname; ?>';
<?php
		if ($wwwredirect) {
?>

	if ($host ~* ^(<?php echo $domainname; ?>)$) {
		rewrite ^/(.*) '<?php echo $protocol; ?>www.<?php echo $domainname; ?>/$1' permanent;
	}
<?php
		}

		if ($disabled) {
?>

	set $var_rootdir '<?php echo $disabledocroot; ?>';
<?php
		} else {
			if ($wildcards) {
?>

	set $var_rootdir '<?php echo $rootpath; ?>';
<?php
				foreach ($excludedomains as &$ed) {
?>

	if ($host ~* ^(<?php echo $ed; ?>.<?php echo $domainname; ?>)$) {
<?php
					if ($ed !== 'webmail') {
?>
		set $var_rootdir '/home/kloxo/httpd/<?php echo $ed; ?>/';
<?php
					} else {
			  			if ($webmailremote) {
?>
		rewrite ^/(.*) '<?php echo $protocol; ?><?php echo $webmailremote; ?>/$1' permanent;
<?php
			  			} else {
?>
		set $var_rootdir '<?php echo $webmaildocroot; ?>';
<?php
			  			}
  				  	}
?>
	}
<?php
				}
			} else {
?>

	set $var_rootdir '<?php echo $rootpath; ?>';
<?php
			}
		}
?>

	root $var_rootdir;
<?php
		if ($redirectionlocal) {
			foreach ($redirectionlocal as $rl) {
?>

	location ~ ^<?php echo $rl[0]; ?>/(.*)$ {
		alias <?php echo str_replace("//", "/", $rl[1]); ?>/$1;
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

	rewrite ^<?php echo $rr[0]; ?>/(.*) '<?php echo $protocol; ?><?php echo $rr[1]; ?>/$1' permanent;
<?php
				} else {
					$protocol2 = ($rr[2] === 'https') ? "https://" : "http://";
?>

	rewrite ^<?php echo $rr[0]; ?>/(.*) '<?php echo $protocol2; ?><?php echo $rr[1]; ?>/$1' permanent;
<?php
				}
			}
		}
?>

	set $var_user '<?php echo $user; ?>';
	set $var_fpmport '<?php echo $fpmport; ?>';

	include '<?php echo $globalspath; ?>/stats.conf';
<?php
		if ($statsprotect) {
?>

	include '<?php echo $globalspath; ?>/dirprotect_stats.conf';
<?php
		}

		if ($nginxextratext) {
?>

	# Extra Tags - begin
	<?php echo $nginxextratext; ?>

	# Extra Tags - end

	set $var_fpmport '<?php echo $fpmport; ?>';
	set $var_domain <?php echo $domainname; ?>

<?php
		}

		if (!$disablephp) {
			if ($wildcards) {
?>

	include '<?php echo $globalspath; ?>/switch_wildcards.conf';
<?php
			} else {
?>

	include '<?php echo $globalspath; ?>/switch_standard.conf';
<?php
			}
		}

		if (!$reverseproxy) {
			if ($dirprotect) {
				foreach ($dirprotect as $k) {
					$protectpath = $k['path'];
					$protectauthname = $k['authname'];
					$protectfile = str_replace('/', '_', $protectpath) . '_';
?>

	set $var_protectpath     '<?php echo $protectpath; ?>';
	set $var_protectauthname '<?php echo $protectauthname; ?>';
	set $var_protectfile     '<?php echo $protectfile; ?>';

	include '<?php echo $globalspath; ?>/dirprotect_standard.conf';
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
		deny   <?php echo $bip; ?>;
<?php
			}
?>
		allow  all;
	}
<?php
		}
?>

	include '<?php echo $globalspath; ?>/<?php echo $genericconf; ?>';
}

<?php

		if ($domainredirect) {
			foreach ($domainredirect as $domredir) {
				$redirdomainname = $domredir['redirdomain'];
				$redirpath = ($domredir['redirpath']) ? $domredir['redirpath'] : null;
				$webmailmap = ($domredir['mailflag'] === 'on') ? true : false;

				if ($redirpath) {
					if ($disabled) {
			  		  $$redirfullpath = $disablepath;
					} else {
			  		  $redirfullpath = str_replace('//', '/', $rootpath . '/' . $redirpath);
					}
?>

## web for redirect '<?php echo $redirdomainname; ?>'
server {
	#disable_symlinks if_not_owner;

<?php
					if ($enablecgi) {
?>

	## MR -- 'enable-cgi' not implementing yet
<?php
					}

					if ($ip === '*') {
?>

	include '<?php echo $globalspath; ?>/<?php echo $listen; ?>.conf';
<?php
					} else {
?>

	listen <?php echo $ip; ?>:<?php echo $port; ?>;
<?php
					}

					if ($count !== 0) {
?>

	ssl on;
	ssl_certificate <?php echo $certname; ?>.pem;
	ssl_certificate_key <?php echo $certname; ?>.key;
	ssl_protocols SSLv3 TLSv1 TLSv1.1 TLSv1.2;
	ssl_ciphers HIGH:!aNULL:!MD5;
<?php
					}
?>

	server_name <?php echo $redirdomainname; ?> www.<?php echo $redirdomainname; ?>;

	index <?php echo $indexorder; ?>;

	set $var_rootdir '<?php echo $redirfullpath; ?>';

	root $var_rootdir;

	set $var_user '<?php echo $user; ?>';
	set $var_fpmport '<?php echo $fpmport; ?>';

	include '<?php echo $globalspath; ?>/switch_standard.conf';
}

<?php
				} else {
					if ($disabled) {
			  			$$redirfullpath = $disablepath;
					} else {
			  			$redirfullpath = $rootpath;
					}
?>

## web for redirect '<?php echo $redirdomainname; ?>'
server {
	#disable_symlinks if_not_owner;

<?php
					if ($enablecgi) {
?>

	## MR -- 'enable-cgi' not implementing yet
<?php
					}

					if ($ip === '*') {
?>

	include '<?php echo $globalspath; ?>/<?php echo $listen; ?>.conf';
<?php
					} else {
?>

	listen <?php echo $ip; ?>:<?php echo $port; ?>;
<?php
					}

					if ($count !== 0) {
?>

	ssl on;
	ssl_certificate <?php echo $certname; ?>.pem;
	ssl_certificate_key <?php echo $certname; ?>.key;
	ssl_protocols SSLv3 TLSv1 TLSv1.1 TLSv1.2;
	ssl_ciphers HIGH:!aNULL:!MD5;
<?php
					}
?>

	server_name <?php echo $redirdomainname; ?> www.<?php echo $redirdomainname; ?>;

	index <?php echo $indexorder; ?>;

	set $var_rootdir '<?php echo $redirfullpath; ?>';

	root $var_rootdir;

	if ($host != '<?php echo $domainname; ?>') {
		rewrite ^/(.*) '<?php echo $protocol; ?><?php echo $domainname; ?>/$1';
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

				if ($disabled) {
?>

## webmail for parked '<?php echo $parkdomainname; ?>'
server {
	#disable_symlinks if_not_owner;

	include '<?php echo $globalspath; ?>/<?php echo $listen; ?>.conf';
<?php
					if ($count !== 0) {
?>

	ssl on;
	ssl_certificate <?php echo $certname; ?>.pem;
	ssl_certificate_key <?php echo $certname; ?>.key;
	ssl_protocols SSLv3 TLSv1 TLSv1.1 TLSv1.2;
	ssl_ciphers HIGH:!aNULL:!MD5;
<?php
					}
?>

	server_name webmail.<?php echo $parkdomainname; ?>;

	index <?php echo $indexorder; ?>;

	set $var_rootdir '<?php echo $disabledocroot; ?>';

	root $var_rootdir;

	set $var_user 'apache';
	set $var_fpmport '<?php echo $fpmportapache; ?>';

	include '<?php echo $globalspath; ?>/switch_standard.conf';
}

<?php
				} else {
					if ($webmailremote) {
?>

## webmail for parked '<?php echo $parkdomainname; ?>'
server {
	#disable_symlinks if_not_owner;

	include '<?php echo $globalspath; ?>/<?php echo $listen; ?>.conf';
<?php
			  		  if ($count !== 0) {
?>

	ssl on;
	ssl_certificate <?php echo $certname; ?>.pem;
	ssl_certificate_key <?php echo $certname; ?>.key;
	ssl_protocols SSLv3 TLSv1 TLSv1.1 TLSv1.2;
	ssl_ciphers HIGH:!aNULL:!MD5;
<?php
			  		  }
?>

	server_name webmail.<?php echo $parkdomainname; ?>;

	if ($host != '<?php echo $webmailremote; ?>') {
		rewrite ^/(.*) '<?php echo $protocol; ?><?php echo $webmailremote; ?>/$1';
	}
}

<?php

					} elseif ($webmailmap) {

?>

## webmail for parked '<?php echo $parkdomainname; ?>'
server {
	#disable_symlinks if_not_owner;

	include '<?php echo $globalspath; ?>/<?php echo $listen; ?>.conf';
<?php
			  			if ($count !== 0) {
?>

	ssl on;
	ssl_certificate <?php echo $certname; ?>.pem;
	ssl_certificate_key <?php echo $certname; ?>.key;
	ssl_protocols SSLv3 TLSv1 TLSv1.1 TLSv1.2;
	ssl_ciphers HIGH:!aNULL:!MD5;
<?php
			  			}
?>

	server_name webmail.<?php echo $parkdomainname; ?>;

	index <?php echo $indexorder; ?>;

	set $var_rootdir '<?php echo $webmaildocroot; ?>';

	root $var_rootdir;

	set $var_user 'apache';
	set $var_fpmport '<?php echo $fpmportapache; ?>';

	include '<?php echo $globalspath; ?>/switch_standard.conf';
}

<?php

					} else {
?>

## No mail map for parked '<?php echo $parkdomainname; ?>'

<?php
					}
				}
			}
		}

		if ($domainredirect) {
			foreach ($domainredirect as $domredir) {
				$redirdomainname = $domredir['redirdomain'];
				$webmailmap = ($domredir['mailflag'] === 'on') ? true : false;

				if ($disabled) {
?>

## webmail for redirect '<?php echo $redirdomainname; ?>'
server {
	#disable_symlinks if_not_owner;

	include '<?php echo $globalspath; ?>/<?php echo $listen; ?>.conf';
<?php
					if ($count !== 0) {
?>

	ssl on;
	ssl_certificate <?php echo $certname; ?>.pem;
	ssl_certificate_key <?php echo $certname; ?>.key;
	ssl_protocols SSLv3 TLSv1 TLSv1.1 TLSv1.2;
	ssl_ciphers HIGH:!aNULL:!MD5;
<?php
					}
?>

	server_name webmail.<?php echo $redirdomainname; ?>;

	index <?php echo $indexorder; ?>;

	set $var_rootdir '<?php echo $disabledocroot; ?>';

	root $var_rootdir;

	set $var_user 'apache';
	set $var_fpmport '<?php echo $fpmportapache; ?>';

	include '<?php echo $globalspath; ?>/switch_standard.conf';
}

<?php
				} else {
					if ($webmailremote) {
?>

## webmail for redirect '<?php echo $redirdomainname; ?>'
server {
	#disable_symlinks if_not_owner;

	include '<?php echo $globalspath; ?>/<?php echo $listen; ?>.conf';
<?php
			  			if ($count !== 0) {
?>

	ssl on;
	ssl_certificate <?php echo $certname; ?>.pem;
	ssl_certificate_key <?php echo $certname; ?>.key;
	ssl_protocols SSLv3 TLSv1 TLSv1.1 TLSv1.2;
	ssl_ciphers HIGH:!aNULL:!MD5;
<?php
			  			}
?>

	server_name webmail.<?php echo $redirdomainname; ?>;

	if ($host != '<?php echo $webmailremote; ?>') {
		rewrite ^/(.*) '<?php echo $protocol; ?><?php echo $webmailremote; ?>/$1';
	}
}

<?php
					} elseif ($webmailmap) {
?>

## webmail for redirect '<?php echo $redirdomainname; ?>'
server {
	#disable_symlinks if_not_owner;

	include '<?php echo $globalspath; ?>/<?php echo $listen; ?>.conf';
<?php
			  			if ($count !== 0) {
?>

	ssl on;
	ssl_certificate <?php echo $certname; ?>.pem;
	ssl_certificate_key <?php echo $certname; ?>.key;
	ssl_protocols SSLv3 TLSv1 TLSv1.1 TLSv1.2;
	ssl_ciphers HIGH:!aNULL:!MD5;
<?php
			  			}
?>

	server_name webmail.<?php echo $redirdomainname; ?>;

	index <?php echo $indexorder; ?>;

	set $var_rootdir '<?php echo $webmaildocroot; ?>';

	root $var_rootdir;

	set $var_user 'apache';
	set $var_fpmport '<?php echo $fpmportapache; ?>';

	include '<?php echo $globalspath; ?>/switch_standard.conf';
}

<?php
					} else {
?>

## No mail map for redirect '<?php echo $redirdomainname; ?>'

<?php
					}
				}
			}
		}

		$count++;
	}
}
?>

### end - web of '<?php echo $domainname; ?>' - do not remove/modify this line
