<?php

//exec("rpm -qa|grep nginx|grep pagespeed", $out, $ret);

$nginx_pagespeed_ready = false;

if (file_exists("/etc/nginx/conf.d/pagespeed.conf")) {
	$nginx_pagespeed_ready = true;
}

$altconf = "/opt/configs/nginx/conf/customs/{$domainname}.conf";

if (file_exists($altconf)) {
	print("## MR - Use '{$altconf}' instead this file");
	return;
}
?>
### begin - web of '<?=$domainname;?>' - do not remove/modify this line

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

$statsapp = $stats['app'];

$statsprotect = ($stats['protect']) ? true : false;

$disabledocroot = "/home/kloxo/httpd/disable";
$cpdocroot = "/home/kloxo/httpd/cp";

if ($statsapp === 'webalizer') {
	$statsdocroot = "/home/httpd/{$domainname}/webstats";
} else {
	$statsdocroot_base = "/home/kloxo/httpd/awstats/wwwroot";
	$statsdocroot = "{$statsdocroot_base}/cgi-bin";
}

$globalspath = "/opt/configs/nginx/conf/globals";

$gzip_base_conf = getLinkCustomfile($globalspath, "gzip.conf");

$ssl_base_conf = getLinkCustomfile($globalspath, "ssl_base.conf");

$acmechallenge_conf = getLinkCustomfile($globalspath, "acme-challenge.conf");

$pagespeed_conf = getLinkCustomfile($globalspath, "pagespeed.conf");

$cgi_conf = getLinkCustomfile($globalspath, "cgi.conf");

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

if ($general_header) {
	$gh = explode("\n", trim($general_header, "\n"));

	$x = array();

	foreach ($gh as $k => $v) {
		$x[] = "\tadd_header {$v};";
	}

	$x[] = "\tadd_header X-Supported-By \"Kloxo-MR 7.0\";";

	$general_header_text = implode("\n", $x);
}

if ($https_header) {
	$hh = explode("\n", trim($https_header, "\n"));

	$x = array();

	foreach ($hh as $k => $v) {
		$x[] = "\tadd_header {$v};";
	}

	$https_header_text = implode("\n", $x);
}

if (intval($static_files_expire) > -1) {
	$static_files_expire_text = "\tlocation ~* ^.+\.(?:jpe?g|gif|png|ico|css|pdf|js)$ {\n" .
		"\t\texpires {$static_files_expire}d;\n" .
		"\t\taccess_log off;\n" .
		"\t\troot \$var_rootdir;\n" .
		"\t}";
} else {
	$static_files_expire_text = "\t# No static files expire";
}

if ($disabled) {
	$cpdocroot = $statsdocroot = $webmaildocroot = $webdocroot = $disabledocroot;
}

foreach ($certnamelist as $ip => $certname) {
	$count = 0;

	foreach ($listens as &$listen) {
		$protocol = ($count === 0) ? "http://" : "https://";
?>

## cp for '<?=$domainname;?>'
server {
	#disable_symlinks if_not_owner;

	include '<?=$globalspath;?>/<?=$listen;?>.conf';

	include '<?=$gzip_base_conf;?>';

<?=$general_header_text;?>

<?php
		if ($count !== 0) {
?>

	include '<?=$ssl_base_conf;?>';

	ssl_certificate <?=$certname;?>.pem;
	ssl_certificate_key <?=$certname;?>.key;
<?php
			if (file_exists("{$certname}.ca")) {
?>
	ssl_trusted_certificate <?=$certname;?>.ca;

<?=$https_header_text;?>

<?php
			}
		}
?>

	server_name cp.<?=$domainname;?>;

	include '<?=$acmechallenge_conf;?>';

	index <?=$indexorder;?>;

	set $var_domain 'cp.<?=$domainname;?>';
	set $var_rootdir '<?=$cpdocroot;?>';

	root $var_rootdir;

	set $var_user 'apache';
	set $var_fpmport '<?=$fpmportapache;?>';
	set $var_phpselected 'php';

	#include '<?=$globalspath;?>/switch_standard<?=$switches[$count];?>.conf';
	include '<?=$globalspath;?>/php-fpm_standard<?=$switches[$count];?>.conf';
}


## stats for '<?=$domainname;?>'
server {
	#disable_symlinks if_not_owner;

	include '<?=$globalspath;?>/<?=$listen;?>.conf';

	include '<?=$gzip_base_conf;?>';

<?=$general_header_text;?>

<?php
		if ($count !== 0) {
?>

	include '<?=$ssl_base_conf;?>';

	ssl_certificate <?=$certname;?>.pem;
	ssl_certificate_key <?=$certname;?>.key;
<?php
			if (file_exists("{$certname}.ca")) {
?>
	ssl_trusted_certificate <?=$certname;?>.ca;

<?=$https_header_text;?>

<?php
			}
		}
?>

	server_name stats.<?=$domainname;?>;

	include '<?=$acmechallenge_conf;?>';

	index <?=$indexorder;?>;

	set $var_domain 'stats.<?=$domainname;?>';
	set $var_rootdir '<?=$statsdocroot;?>';

	root $var_rootdir;

	set $var_user 'apache';
	set $var_fpmport '<?=$fpmportapache;?>';
	set $var_phpselected 'php';
<?php
		if ($enablestats) {
?>

	include '<?=$globalspath;?>/stats.conf';
<?php
			if ($statsprotect) {
?>

	include '<?=$globalspath;?>/dirprotect_stats.conf';
<?php
			}
		}
?>
}


## webmail for '<?=$domainname;?>'
server {
	#disable_symlinks if_not_owner;

	include '<?=$globalspath;?>/<?=$listen;?>.conf';

	include '<?=$gzip_base_conf;?>';

<?=$general_header_text;?>

<?php
		if ($count !== 0) {
?>

	include '<?=$ssl_base_conf;?>';

	ssl_certificate <?=$certname;?>.pem;
	ssl_certificate_key <?=$certname;?>.key;
<?php
			if (file_exists("{$certname}.ca")) {
?>
	ssl_trusted_certificate <?=$certname;?>.ca;

<?=$https_header_text;?>

<?php
			}
		}
?>

	server_name webmail.<?=$domainname;?> mail.<?=$domainname;?>;

	include '<?=$acmechallenge_conf;?>';

	index <?=$indexorder;?>;

	set $var_domain 'webmail.<?=$domainname;?>';
	set $var_rootdir '<?=$webmaildocroot;?>';

	root $var_rootdir;

	set $var_user 'apache';
	set $var_fpmport '<?=$fpmportapache;?>';
	set $var_phpselected 'php';

	#include '<?=$globalspath;?>/switch_standard<?=$switches[$count];?>.conf';
	include '<?=$globalspath;?>/php-fpm_standard<?=$switches[$count];?>.conf';
<?php

		if ($webmailremote) {
?>

	if ($host != '<?=$webmailremote;?>') {
		rewrite ^/(.*) '<?=$protocol;?><?=$webmailremote;?>/$1' permanent;
	}
<?php
		}

?>
}


## web for '<?=$domainname;?>'
server {
	#disable_symlinks if_not_owner;
<?php
		if ($nginx_pagespeed_ready) {
			if (!$disable_pagespeed) {
?>

	include '<?=$pagespeed_conf;?>';
<?php
			} else {
?>
	pagespeed off;
<?php
			}
		}
?>

	include '<?=$globalspath;?>/<?=$listen;?>.conf';
<?php
	//	if ((!$reverseproxy) || ($webselected === 'front-end')) {
?>

	include '<?=$gzip_base_conf;?>';
<?php
	//	}
?>

<?=$general_header_text;?>

<?php
		if ($dirindex) {
?>

	autoindex on;
<?php
		}

		if ($count !== 0) {
			if ($enablessl) {
?>

	include '<?=$ssl_base_conf;?>';

	ssl_certificate <?=$certname;?>.pem;
	ssl_certificate_key <?=$certname;?>.key;
<?php
				if (file_exists("{$certname}.ca")) {
?>
	ssl_trusted_certificate <?=$certname;?>.ca;

<?=$https_header_text;?>

<?php
				}
			}
		}

		if ($ip === '*') {
?>

	server_name <?=$serveralias;?>;
<?php
		} else {
?>

	server_name <?=$serveralias;?> <?=$ip;?>;
<?php
		}
?>

	include '<?=$acmechallenge_conf;?>';

	index <?=$indexorder;?>;

	set $var_domain <?=$domainname;?>;
<?php
		if ($wwwredirect) {
?>

	if ($host ~* ^(<?=$domainname;?>)$) {
		rewrite ^/(.*) '<?=$protocol;?>www.<?=$domainname;?>/$1' permanent;
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

	set $var_rootdir '<?=$webdocroot;?>';
<?php
			foreach ($excludedomains as &$ed) {
?>

	if ($host ~* ^(<?=$ed;?>.<?=$domainname;?>)$) {
<?php
				if ($ed !== 'webmail') {
?>
		set $var_rootdir '/home/kloxo/httpd/<?=$ed;?>/';
<?php
				} else {
					if ($webmailremote) {
?>
		rewrite ^/(.*) '<?=$protocol;?><?=$webmailremote;?>/$1' permanent;
<?php
					} else {
?>
		set $var_rootdir '<?=$webmaildocroot;?>';
<?php
					}
				}
?>
	}
<?php
			}
		} else {
?>

	set $var_rootdir '<?=$webdocroot;?>';
<?php
		}
?>

	root $var_rootdir;
<?php
		if ($enablecgi) {
?>

	include '<?=$cgi_conf;?>';
<?php
		}

		if ($redirectionlocal) {
			foreach ($redirectionlocal as $rl) {
?>

	location ~ ^<?=$rl[0];?>/(.*)$ {
		alias <?=str_replace("//", "/", $rl[1]);?>/$1;
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

	rewrite ^<?=$rr[0];?>/(.*) '<?=$protocol;?><?=$rr[1];?>/$1' permanent;
<?php
				} else {
					$protocol2 = ($rr[2] === 'https') ? "https://" : "http://";
?>

	rewrite ^<?=$rr[0];?>/(.*) '<?=$protocol2;?><?=$rr[1];?>/$1' permanent;
<?php
				}
			}
		}
?>

	set $var_user '<?=$user;?>';
	set $var_fpmport '<?=$fpmport;?>';
	set $var_phpselected '<?=$phpselected;?>';

	<?=$conn_timeout;?>

<?php
		if ($enablestats) {
		// MR - bug for nginx where error if using 'include stats_log.conf' (use $var_domain)
?>

	#include '<?=$globalspath;?>/stats_log.conf';
	access_log /home/httpd/<?=$domainname;?>/stats/<?=$domainname;?>-custom_log main;
	error_log /home/httpd/<?=$domainname;?>/stats/<?=$domainname;?>-error_log error;

	rewrite ^/stats(/|) <?=$protocol;?>stats.$var_domain/ permanent;
<?php
		}

		if ($nginxextratext) {
?>

	# Extra Tags - begin
	<?=$nginxextratext;?>

	# Extra Tags - end
<?php
		}

		if ((!$reverseproxy) && (file_exists("{$globalspath}/{$domainname}.conf"))) {
			if ($enablephp) {
?>

	include '<?=$globalspath;?>/<?=$domainname;?>.conf';
<?php
			}
		} else {
			if ($wildcards) {
				if (($reverseproxy) && ($webselected === 'front-end')) {
					if ($enablephp) {
?>

	include '<?=$globalspath;?>/php-fpm_wildcards<?=$switches[$count];?>.conf';
<?php
					}
				} else {
?>

	#include '<?=$globalspath;?>/switch_wildcards<?=$switches[$count];?>.conf';
	include '<?=$globalspath;?>/switch_wildcards.conf';
<?php
				}
			} else {
				if (($reverseproxy) && ($webselected === 'front-end')) {
					if ($enablephp) {
?>

	include '<?=$globalspath;?>/php-fpm_standard<?=$switches[$count];?>.conf';
<?php
					}
				} else {
?>

	#include '<?=$globalspath;?>/switch_standard<?=$switches[$count];?>.conf';
	include '<?=$globalspath;?>/switch_standard.conf';
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

	set $var_std_protectpath '<?=$protectpath;?>';
	set $var_std_protectauthname '<?=$protectauthname;?>';
	set $var_std_protectfile '<?=$protectfile;?>';

	include '<?=$globalspath;?>/dirprotect_standard.conf';
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
		deny   <?=$bip;?>;
<?php
			}
?>
		allow  all;
			}
<?php
	}
?>

	set $var_kloxoportssl '<?=$kloxoportssl;?>';
	set $var_kloxoportnonssl '<?=$kloxoportnonssl;?>';

	include '<?=$globalspath;?>/<?=$generic;?>.conf';
<?php
		if (intval($microcache_time) > 0) {
?>

	## for microcache
	fastcgi_cache_valid 200 <?=$microcache_time;?>s;
	fastcgi_cache_use_stale updating;
	fastcgi_max_temp_file_size 10M;

	proxy_cache_valid 200 <?=$microcache_time;?>s;
	proxy_cache_use_stale updating;
	proxy_max_temp_file_size 10M;
<?php
		}
?>

<?=$static_files_expire_text;?>

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

## web for redirect '<?=$redirdomainname;?>'
server {
	#disable_symlinks if_not_owner;

	include '<?=$globalspath;?>/<?=$listen;?>.conf';
<?php
				//	if ((!$reverseproxy) || ($webselected === 'front-end')) {
?>

	include '<?=$gzip_base_conf;?>';
<?php
				//	}
?>

<?=$general_header_text;?>
<?php
					if ($count !== 0) {
						if ($enablessl) {
?>

	include '<?=$ssl_base_conf;?>';

	ssl_certificate <?=$certname;?>.pem;
	ssl_certificate_key <?=$certname;?>.key;
<?php
							if (file_exists("{$certname}.ca")) {
?>
	ssl_trusted_certificate <?=$certname;?>.ca;

<?=$https_header_text;?>

<?php
							}
						}
					}
?>

	server_name <?=$redirdomainname;?> www.<?=$redirdomainname;?>;

	include '<?=$acmechallenge_conf;?>';

	index <?=$indexorder;?>;

	set $var_domain '<?=$redirdomainname;?>';
	set $var_rootdir '<?=$redirfullpath;?>';

	root $var_rootdir;
<?php

					if ($enablecgi) {
?>

	include '<?=$cgi_conf;?>';
<?php
					}
?>

	set $var_user '<?=$user;?>';
	set $var_fpmport '<?=$fpmport;?>';
	set $var_phpselected '<?=$phpselected;?>';

	<?=$conn_timeout;?>

<?php

					if (($reverseproxy) && ($webselected === 'front-end')) {
?>

	include '<?=$globalspath;?>/php-fpm_standard<?=$switches[$count];?>.conf';
<?php
					} else {
?>

	#include '<?=$globalspath;?>/switch_standard<?=$switches[$count];?>.conf';
	include '<?=$globalspath;?>/switch_standard.conf';
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

## web for redirect '<?=$redirdomainname;?>'
server {
	#disable_symlinks if_not_owner;

	include '<?=$globalspath;?>/<?=$listen;?>.conf';
<?php
	//	if ((!$reverseproxy) || ($webselected === 'front-end')) {
?>

	include '<?=$gzip_base_conf;?>';
<?php
	//	}
?>

<?=$general_header_text;?>

<?php
					if ($count !== 0) {
						if ($enablessl) {
?>

	include '<?=$ssl_base_conf;?>';

	ssl_certificate <?=$certname;?>.pem;
	ssl_certificate_key <?=$certname;?>.key;
<?php
							if (file_exists("{$certname}.ca")) {
?>
	ssl_trusted_certificate <?=$certname;?>.ca;

<?=$https_header_text;?>

<?php
							}
						}
					}
?>

	server_name <?=$redirdomainname;?> www.<?=$redirdomainname;?>;

	include '<?=$acmechallenge_conf;?>';

	index <?=$indexorder;?>;

	set $var_domain '<?=$redirdomainname;?>';

	set $var_rootdir '<?=$redirfullpath;?>';

	root $var_rootdir;
<?php
					if ($enablecgi) {
?>

	include '<?=$cgi_conf;?>';
<?php
					}
?>

	if ($host != '<?=$domainname;?>') {
		rewrite ^/(.*) '<?=$protocol;?><?=$domainname;?>/$1';
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

## webmail for parked '<?=$parkdomainname;?>'
server {
	#disable_symlinks if_not_owner;

	include '<?=$globalspath;?>/<?=$listen;?>.conf';

	include '<?=$gzip_base_conf;?>';

<?=$general_header_text;?>

<?php
					if ($count !== 0) {
?>

	include '<?=$ssl_base_conf;?>';

	ssl_certificate <?=$certname;?>.pem;
	ssl_certificate_key <?=$certname;?>.key;
<?php
						if (file_exists("{$certname}.ca")) {
?>
	ssl_trusted_certificate <?=$certname;?>.ca;

<?=$https_header_text;?>

<?php
						}
					}
?>

	server_name webmail.<?=$parkdomainname;?> mail.<?=$parkdomainname;?>;

	include '<?=$acmechallenge_conf;?>';
<?php
					if ($webmailremote) {
?>

	if ($host != '<?=$webmailremote;?>') {
		rewrite ^/(.*) '<?=$protocol;?><?=$webmailremote;?>/$1';
	}
<?php
					}
?>
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

				if ($webmailremote) {
?>

## webmail for redirect '<?=$redirdomainname;?>'
server {
	#disable_symlinks if_not_owner;

	include '<?=$globalspath;?>/<?=$listen;?>.conf';

	include '<?=$gzip_base_conf;?>';

<?=$general_header_text;?>

<?php
					if ($count !== 0) {
?>

	include '<?=$ssl_base_conf;?>';

	ssl_certificate <?=$certname;?>.pem;
	ssl_certificate_key <?=$certname;?>.key;
<?php
						if (file_exists("{$certname}.ca")) {
?>
	ssl_trusted_certificate <?=$certname;?>.ca;

<?=$https_header_text;?>

<?php
						}
					}
?>

	server_name webmail.<?=$redirdomainname;?> mail.<?=$redirdomainname;?>;

	include '<?=$acmechallenge_conf;?>';
<?php
					if ($webmailremote) {
?>

	if ($host != '<?=$webmailremote;?>') {
		rewrite ^/(.*) '<?=$protocol;?><?=$webmailremote;?>/$1';
	}
<?php
					}
?>
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
