<?php
$altconf = "/opt/configs/apache/conf/customs/{$domainname}.conf";

if (file_exists($altconf)) {
	print("## MR - Use '{$altconf}' instead this file");
	return;
}
?>
### begin - web of '<?= $domainname; ?>' - do not remove/modify this line

<?php

$altconf = "/opt/configs/apache/conf/customs/{$domainname}.conf";

if (file_exists($altconf)) {
	print("## MR - Use {$altconf} instead this file");	
}

$webdocroot = $rootpath;

if (!isset($phpselected)) {
	$phpselected = 'php';
}

if (!isset($timeout)) {
	$timeout = '300';
}

$globalspath = "/opt/configs/apache/conf/globals";
$sslpath = "/home/kloxo/ssl";
$kloxopath = "/usr/local/lxlabs/kloxo";

if ($reverseproxy) {
	$ports[] = '30080';
	$ports[] = '30443';
} else {
	if (($webcache === 'none') || (!$webcache)) {
		$ports[] = '80';
		$ports[] = '443';
	} else {
		$ports[] = '8080';
		$ports[] = '8443';
	}
}

$portlist = array('${port}', '${portssl}');

if ($reverseproxy) {
	$tmp_ip = '127.0.0.1';

	foreach ($certnamelist as $ip => $certname) {
		$tmp_certname = $certname;
		break;
	}

	$certnamelist = null;

	$certnamelist[$tmp_ip] = $tmp_certname;
} else {
	$tmp_ip = '*';
}

foreach ($certnamelist as $ip => $certname) {
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
	$serveralias .= " *.{$domainname}";
}

if ($serveraliases) {
	foreach ($serveraliases as &$sa) {
		$serveralias .= "\\\n		{$sa}";
	}
}

if ($parkdomains) {
	foreach ($parkdomains as $pk) {
		$pa = $pk['parkdomain'];
		$serveralias .= "\\\n		{$pa} www.{$pa}";
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

if ($indexorder) {
	$indexorder = implode(' ', $indexorder);
}

if ($dirindex) {
	$dirindex = '+Indexes';
} else {
	$dirindex = '-Indexes';
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

	$blockips = implode(' ', $blockips);
}

$acmechallenge_conf = getLinkCustomfile($globalspath, "acme-challenge.conf");

if ($general_header) {
//	if (!reverseproxy) {
		$gh = explode("\n", trim($general_header, "\n"));

		$general_header_text = "<IfModule mod_headers.c>\n";

		foreach ($gh as $k => $v) {
			$general_header_text .= "\t\tHeader always set {$v}\n";
		}

		$general_header_text .= "\t\tHeader always set X-Supported-By \"Kloxo-MR 7.0\"\n" .
			"\t\tRequestHeader unset Proxy early\n" . 
			"\t</IfModule>";
//	} else {
//		$general_header_text = "# No need 'general header' for proxy";
//	}
}

if ($https_header) {
//	if (!reverseproxy) {
		$hh = explode("\n", trim($https_header, "\n"));

		$https_header_text = "<IfModule mod_headers.c>\n";

		foreach ($hh as $k => $v) {
			$https_header_text .= "\t\t\tHeader always set {$v}\n";
		}

		$https_header_text .= "\t\t</IfModule>";
//	} else {
//		$https_header_text = "# No need 'https header' for proxy";
//	}
}

if (intval($static_files_expire) > -1) {
	$static_files_expire_text = "<IfModule mod_expires.c>\n" .
		"\t\tExpiresActive On\n" .
		"\t\tExpiresByType image/x-icon \"access plus {$static_files_expire} days\"\n" .
		"\t\tExpiresByType image/gif \"accesss plus {$static_files_expire} days\"\n" .
		"\t\tExpiresByType image/png \"access plus {$static_files_expire} days\"\n" .
		"\t\tExpiresByType image/jpg \"access plus {$static_files_expire} days\"\n" .
		"\t\tExpiresByType image/jpeg \"access plus {$static_files_expire} days\"\n" .
		"\t\tExpiresByType text/css \"access plus {$static_files_expire} days\"\n" .
		"\t\tExpiresByType application/pdf \"access plus {$static_files_expire} days\"\n" .
		"\t\tExpiresByType text/x-javascript \"access plus {$static_files_expire} days\"\n" .
		"\t\tExpiresDefault \"access plus {$static_files_expire} days\"\n" .
		"\t</IfModule>";
} else {
	$static_files_expire_text = '# No static files expire';
}

if (file_exists("{$kloxopath}/etc/flag/use_apache24.flg")) {
	$use_httpd24 = true;
} else {
	$use_httpd24 = false;
}

$ssl_base_conf = getLinkCustomfile($globalspath, "ssl_base.conf");

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

if ($disabled) {
	$sockuser = 'apache';
} else {
	$sockuser = $user;
}

if ($disabled) {
	$cpdocroot = $webmaildocroot = $webdocroot = $disabledocroot;
}

foreach ($certnamelist as $ip => $certname) {
?>

<IfVersion < 2.4>
	Define port ${global::port}
	Define portssl ${global::portssl}
	Define ip ${global::ip}
</IfVersion>

<IfVersion >= 2.4>
	Include "<?=$globalspath;?>/portnip.conf"
</IfVersion>

<?php
	if (!$reverseproxy) {
		if ($ip !== '*') {
?>
Define ipalloc <?=$ip;?>

<IfVersion < 2.4>
	NameVirtualHost ${ipalloc}:${port}
	NameVirtualHost ${ipalloc}:${portssl}
</IfVersion>

<?php
		}
	}
}

foreach ($certnamelist as $ip => $certname) {
	$count = 0;

	foreach ($ports as &$port) {
		$protocol = ($count === 0) ? "http://" : "https://";
		$kloxoport = ($count === 0) ? $kloxoportnonssl : $kloxoportssl;
?>

## cp for '<?=$domainname;?>'
<VirtualHost ${ip}:<?=$portlist[$count];?> >
<?php
		//	if ($disable_pagespeed) {
?>

	<IfModule pagespeed_module>
		ModPageSpeed unplugged
	</IfModule>
<?php
		//	}
?>

	SetEnvIf X-Forwarded-Proto https HTTPS=1

	ServerName cp.<?=$domainname;?>


	Include "<?=$acmechallenge_conf;?>"

	DocumentRoot "<?=$cpdocroot;?>"

	DirectoryIndex <?=$indexorder;?>


	<?=$general_header_text;?>

<?php
			if ($count !== 0) {
?>

	<IfModule mod_http2.c>
		Protocols h2 http/1.1
	</IfModule>

	<IfModule mod_ssl.c>
		Include "<?=$ssl_base_conf;?>"

		SSLCertificateFile <?=$certname;?>.pem
		SSLCertificateKeyFile <?=$certname;?>.key
<?php
					if (file_exists("{$certname}.ca")) {
?>
		SSLCACertificatefile <?=$certname;?>.ca

		<?=$https_header_text;?>

<?php
					}
?>
	</IfModule>
<?php
			} else {
?>

	<IfModule mod_http2.c>
		Protocols h2c http/1.1
	</IfModule>
<?php
			}
?>

	<IfModule suexec.c>
		SuexecUserGroup apache apache
	</IfModule>

	<IfModule mod_suphp.c>
		SuPhp_UserGroup apache apache
	</IfModule>

	#<IfVersion < 2.4>
		<IfModule mod_ruid2.c>
			RMode config
			RUidGid apache apache
			RMinUidGid apache apache
		</IfModule>

		<IfModule itk.c>
			AssignUserId apache apache
		</IfModule>

		<IfModule mod_fastcgi.c>
			Alias /cp.<?=$domainname;?>.<?=$count;?>fake "<?=$cpdocroot;?>/cp.<?=$domainname;?>.<?=$count;?>fake"
			#FastCGIExternalServer "<?=$cpdocroot;?>/cp.<?=$domainname;?>.<?=$count;?>fake" \
			#	-host 127.0.0.1:<?=$fpmportapache;?> -idle-timeout <?=$timeout;?> -pass-header Authorization
			FastCGIExternalServer "<?=$cpdocroot;?>/cp.<?=$domainname;?>.<?=$count;?>fake" \
				-socket /opt/configs/php-fpm/sock/<?=$phpselected;?>-apache.sock \
				-idle-timeout <?=$timeout;?> -pass-header Authorization
			<FilesMatch \.php$>
				SetHandler application/x-httpd-fastphp
			</FilesMatch>
			Action application/x-httpd-fastphp /cp.<?=$domainname;?>.<?=$count;?>fake
			<Files "cp.<?=$domainname;?>.<?=$count;?>fake">
				RewriteCond %{REQUEST_URI} !cp.<?=$domainname;?>.<?=$count;?>fake
			</Files>
		</IfModule>

		<IfModule !mod_ruid2.c>
			<IfModule !mod_itk.c>
				<IfModule !mod_fastcgi.c>
					<IfModule mod_fcgid.c>
						FcgidIOTimeout <?=$timeout;?>

						<Directory "<?=$cpdocroot;?>/">
							Options +ExecCGI
							<FilesMatch \.php$>
								SetHandler fcgid-script
							</FilesMatch>
							FCGIWrapper /home/kloxo/client/php.fcgi .php
						</Directory>
					</IfModule>
				</IfModule>
			</IfModule>
		</IfModule>
	#</IfVersion>

	<IfVersion >= 2.4>
		<IfModule mod_proxy_fcgi.c>
			ProxyRequests Off
			ProxyErrorOverride On
			ProxyPass /error !
			ErrorDocument 500 /error/500.html
			<FilesMatch \.php$>
				SetHandler "proxy:unix:/opt/configs/php-fpm/sock/<?=$phpselected;?>-apache.sock|fcgi://localhost"
			</FilesMatch>
			<Proxy "fcgi://localhost">
				ProxySet timeout=<?=$timeout;?>

				ProxySet connectiontimeout=<?=$timeout;?>

				#ProxySet enablereuse=on
				ProxySet max=25
				ProxySet retry=0
			</Proxy>
		</IfModule>
	</IfVersion>

	<Location "/">
		# Options +Indexes +FollowSymlinks
		Options -Indexes -FollowSymlinks +SymLinksIfOwnerMatch
	</Location>

	<Directory "<?=$cpdocroot;?>/">
		AllowOverride All
		<IfVersion < 2.4>
			Order allow,deny
			Allow from all
		</IfVersion>
		<IfVersion >= 2.4>
			Require all granted
		</IfVersion>
	</Directory>

</VirtualHost>


## stats for '<?=$domainname;?>'
<VirtualHost ${ip}:<?=$portlist[$count];?> >
<?php
		//	if ($disable_pagespeed) {
?>

	<IfModule pagespeed_module>
		ModPageSpeed unplugged
	</IfModule>
<?php
		//	}
?>

	SetEnvIf X-Forwarded-Proto https HTTPS=1

	ServerName stats.<?=$domainname;?>


	Include "<?=$acmechallenge_conf;?>"

	DocumentRoot "<?=$statsdocroot;?>"

	DirectoryIndex <?=$indexorder;?>


	<?=$general_header_text;?>

<?php
			if ($count !== 0) {
?>

	<IfModule mod_http2.c>
		Protocols h2 http/1.1
	</IfModule>

	<IfModule mod_ssl.c>
		Include "<?=$ssl_base_conf;?>"

		SSLCertificateFile <?=$certname;?>.pem
		SSLCertificateKeyFile <?=$certname;?>.key
<?php
					if (file_exists("{$certname}.ca")) {
?>
		SSLCACertificatefile <?=$certname;?>.ca

		<?=$https_header_text;?>

<?php
					}
?>
	</IfModule>
<?php
			} else {
?>

	<IfModule mod_http2.c>
		Protocols h2c http/1.1
	</IfModule>
<?php
			}
?>

	<IfModule suexec.c>
		SuexecUserGroup apache apache
	</IfModule>

	<IfModule mod_suphp.c>
		SuPhp_UserGroup apache apache
	</IfModule>

	#<IfVersion < 2.4>
		<IfModule mod_ruid2.c>
			RMode config
			RUidGid apache apache
			RMinUidGid apache apache
		</IfModule>

		<IfModule itk.c>
			AssignUserId apache apache
		</IfModule>

		<IfModule mod_fastcgi.c>
			Alias /stats.<?=$domainname;?>.<?=$count;?>fake "<?=$statsdocroot;?>/stats.<?=$domainname;?>.<?=$count;?>fake"
			#FastCGIExternalServer "<?=$statsdocroot;?>/stats.<?=$domainname;?>.<?=$count;?>fake" \
			#	-host 127.0.0.1:<?=$fpmportapache;?> -idle-timeout <?=$timeout;?> -pass-header Authorization
			FastCGIExternalServer "<?=$statsdocroot;?>/stats.<?=$domainname;?>.<?=$count;?>fake" \
				-socket /opt/configs/php-fpm/sock/<?=$phpselected;?>-apache.sock \
				-idle-timeout <?=$timeout;?> -pass-header Authorization
			<FilesMatch \.php$>
				SetHandler application/x-httpd-fastphp
			</FilesMatch>
			Action application/x-httpd-fastphp /stats.<?=$domainname;?>.<?=$count;?>fake
			<Files "stats.<?=$domainname;?>.<?=$count;?>fake">
				RewriteCond %{REQUEST_URI} !stats.<?=$domainname;?>.<?=$count;?>fake
			</Files>
		</IfModule>

		<IfModule !mod_ruid2.c>
			<IfModule !mod_itk.c>
				<IfModule !mod_fastcgi.c>
					<IfModule mod_fcgid.c>
						FcgidIOTimeout <?=$timeout;?>

						<Directory "<?=$cpdocroot;?>/">
							Options +ExecCGI
							<FilesMatch \.php$>
								SetHandler fcgid-script
							</FilesMatch>
							FCGIWrapper /home/kloxo/client/php.fcgi .php
						</Directory>
					</IfModule>
				</IfModule>
			</IfModule>
		</IfModule>
	#</IfVersion>

	<IfVersion >= 2.4>
		<IfModule mod_proxy_fcgi.c>
			ProxyRequests Off
			ProxyErrorOverride On
			ProxyPass /error !
			ErrorDocument 500 /error/500.html
			<FilesMatch \.php$>
				SetHandler "proxy:unix:/opt/configs/php-fpm/sock/<?=$phpselected;?>-apache.sock|fcgi://localhost"
			</FilesMatch>
			<Proxy "fcgi://localhost">
				ProxySet timeout=<?=$timeout;?>

				ProxySet connectiontimeout=<?=$timeout;?>

				#ProxySet enablereuse=on
				ProxySet max=25
				ProxySet retry=0
			</Proxy>
		</IfModule>
	</IfVersion>
<?php
			if ($enablestats) {
				if (!$reverseproxy) {
					if ($statsapp === 'awstats') {
?>

	<IfModule mod_rewrite.c>
		RewriteEngine on
		RewriteRule ^/$ /awstats.pl?config=<?=$domainname;?> [R]
	</IfModule>

	AliasMatch "^/awstatscss(/|$)(.*)" "<?=$statsdocroot_base;?>/css$1$2"
	AliasMatch "^/awstatsicons(/|$)(.*)" "<?=$statsdocroot_base;?>/icon$1$2"

	<Directory "<?=$statsdocroot_base;?>/css/">
		AllowOverride All
		<IfVersion < 2.4>
			Order allow,deny
			Allow from all
		</IfVersion>
		<IfVersion >= 2.4>
			Require all granted
		</IfVersion>
	</Directory>

	<Directory "<?=$statsdocroot_base;?>/icon/">
		AllowOverride All
		<IfVersion < 2.4>
			Order allow,deny
			Allow from all
		</IfVersion>
		<IfVersion >= 2.4>
			Require all granted
		</IfVersion>
	</Directory>
<?php
					}
?>

	<Directory "<?=$statsdocroot;?>/">
		AllowOverride All
		<IfVersion < 2.4>
			Order allow,deny
			Allow from all
		</IfVersion>
		<IfVersion >= 2.4>
			Require all granted
		</IfVersion>

<?php
					if ($statsapp === 'awstats') {
?>
		Options +ExecCGI
		<FilesMatch \.(cgi|pl|py)$>
			#<IfModule !mod_fastcgi.c>
				<IfModule mod_suphp.c>
					SuPhp_UserGroup apache apache
					SetHandler x-suphp-cgi
				</IfModule>
			#</IfModule>
		</FilesMatch>
<?php
					}
?>
	</Directory>

	<Location "/">
		Options -Indexes
<?php
					if ($statsprotect) {
?>

		AuthType Basic
		AuthName "AuthStats"
		AuthUserFile "/home/httpd/<?=$domainname;?>/__dirprotect/__stats"
		Require valid-user
<?php
					}
?>
	</Location>
<?php
				}
			}
?>

</VirtualHost>

<?php
		if ($webmailremote) {
?>

## webmail for '<?=$domainname;?>'
<VirtualHost ${ip}:<?=$portlist[$count];?> >
<?php
		//	if ($disable_pagespeed) {
?>

	<IfModule pagespeed_module>
		ModPageSpeed unplugged
	</IfModule>
<?php
		//	}
?>

	SetEnvIf X-Forwarded-Proto https HTTPS=1

	ServerName webmail.<?=$domainname;?>


	ServerAlias mail.<?=$domainname;?>


	Include "<?=$acmechallenge_conf;?>"

	DocumentRoot "<?=$webmaildocroot;?>"

	Redirect "/" "<?=$protocol;?><?=$webmailremote;?>"

	<?=$general_header_text;?>

<?php
				if ($count !== 0) {
?>

	<IfModule mod_http2.c>
		Protocols h2 http/1.1
	</IfModule>

	<IfModule mod_ssl.c>
		Include "<?=$ssl_base_conf;?>"

		SSLCertificateFile <?=$certname;?>.pem
		SSLCertificateKeyFile <?=$certname;?>.key
<?php
						if (file_exists("{$certname}.ca")) {
?>
		SSLCACertificatefile <?=$certname;?>.ca

		<?=$https_header_text;?>

<?php
						}
?>
	</IfModule>
<?php
				} else {
?>

	<IfModule mod_http2.c>
		Protocols h2c http/1.1
	</IfModule>
<?php
				}
?>

</VirtualHost>

<?php
		} else {
?>

## webmail for '<?=$domainname;?>'
<VirtualHost ${ip}:<?=$portlist[$count];?> >
<?php
		//	if ($disable_pagespeed) {
?>

	<IfModule pagespeed_module>
		ModPageSpeed unplugged
	</IfModule>
<?php
		//	}
?>

	SetEnvIf X-Forwarded-Proto https HTTPS=1

	ServerName webmail.<?=$domainname;?>


	ServerAlias mail.<?=$domainname;?>


	Include "<?=$acmechallenge_conf;?>"

	DocumentRoot "<?=$webmaildocroot;?>"

	DirectoryIndex <?=$indexorder;?>


	<?=$general_header_text;?>

<?php
				if ($count !== 0) {
?>

	<IfModule mod_http2.c>
		Protocols h2 http/1.1
	</IfModule>

	<IfModule mod_ssl.c>
		Include "<?=$ssl_base_conf;?>"

		SSLCertificateFile <?=$certname;?>.pem
		SSLCertificateKeyFile <?=$certname;?>.key
<?php
					if (file_exists("{$certname}.ca")) {
?>
		SSLCACertificatefile <?=$certname;?>.ca

		<?=$https_header_text;?>

<?php
						}
?>
	</IfModule>
<?php
				} else {
?>

	<IfModule mod_http2.c>
		Protocols h2c http/1.1
	</IfModule>
<?php
				}
?>

	<IfModule suexec.c>
		SuexecUserGroup apache apache
	</IfModule>

	<IfModule mod_suphp.c>
		SuPhp_UserGroup apache apache
	</IfModule>

	#<IfVersion < 2.4>
		<IfModule mod_ruid2.c>
			RMode config
			RUidGid apache apache
			RMinUidGid apache apache
		</IfModule>

		<IfModule itk.c>
			AssignUserId apache apache
		</IfModule>

		<IfModule mod_fastcgi.c>
			Alias /webmail.<?=$domainname;?>.<?=$count;?>fake "<?=$webmaildocroot;?>/webmail.<?=$domainname;?>.<?=$count;?>fake"
			#FastCGIExternalServer "<?=$webmaildocroot;?>/webmail.<?=$domainname;?>.<?=$count;?>fake" \
			#	-host 127.0.0.1:<?=$fpmportapache;?> -idle-timeout <?=$timeout;?> -pass-header Authorization
			FastCGIExternalServer "<?=$webmaildocroot;?>/webmail.<?=$domainname;?>.<?=$count;?>fake" \
				-socket /opt/configs/php-fpm/sock/<?=$phpselected;?>-apache.sock \
				-idle-timeout <?=$timeout;?> -pass-header Authorization
			<FilesMatch \.php$>
				SetHandler application/x-httpd-fastphp
			</FilesMatch>
			Action application/x-httpd-fastphp /webmail.<?=$domainname;?>.<?=$count;?>fake
			<Files "webmail.<?=$domainname;?>.<?=$count;?>fake">
				RewriteCond %{REQUEST_URI} !webmail.<?=$domainname;?>.<?=$count;?>fake
			</Files>
		</IfModule>

		<IfModule !mod_ruid2.c>
			<IfModule !mod_itk.c>
				<IfModule !mod_fastcgi.c>
					<IfModule mod_fcgid.c>
						FcgidIOTimeout <?=$timeout;?>

						<Directory "<?=$webmaildocroot;?>/">
							Options +ExecCGI
							<FilesMatch \.php$>
								SetHandler fcgid-script
							</FilesMatch>
							FCGIWrapper /home/kloxo/client/php.fcgi .php
						</Directory>
					</IfModule>
				</IfModule>
			</IfModule>
		</IfModule>
	#</IfVersion>

	<IfVersion >= 2.4>
		<IfModule mod_proxy_fcgi.c>
			ProxyRequests Off
			ProxyErrorOverride On
			ProxyPass /error !
			ErrorDocument 500 /error/500.html
			<FilesMatch \.php$>
				SetHandler "proxy:unix:/opt/configs/php-fpm/sock/<?=$phpselected;?> -apache.sock|fcgi://localhost"
			</FilesMatch>
			<Proxy "fcgi://localhost">
				ProxySet timeout=<?=$timeout;?>

				ProxySet connectiontimeout=<?=$timeout;?>

				#ProxySet enablereuse=on
				ProxySet max=25
				ProxySet retry=0
			</Proxy>
		</IfModule>
	</IfVersion>

	<Location "/">
		Options -Indexes -FollowSymlinks +SymLinksIfOwnerMatch
	</Location>

	<Directory "<?=$webmaildocroot;?>/">
		AllowOverride All
		<IfVersion < 2.4>
			Order allow,deny
			Allow from all
		</IfVersion>
		<IfVersion >= 2.4>
			Require all granted
		</IfVersion>
	</Directory>

</VirtualHost>

<?php

		}

		if (!$reverseproxy) {
			if ($ip !== '*') {
				$ip_special = '${ipalloc}';
			} else {
				$ip_special = '${ip}';
			}
		} else {
			$ip_special = '${ip}';
		}
?>

## web for '<?=$domainname;?>'
<VirtualHost <?=$ip_special;?>:<?=$portlist[$count];?> >

	LimitInternalRecursion 256
<?php
		if ($disable_pagespeed) {
			if (($disable_pagespeed) || (($driver[0] === 'nginx') && (file_exists("/etc/nginx/conf.d/pagespeed.conf")))) {
?>

	<IfModule pagespeed_module>
		ModPageSpeed unplugged
	</IfModule>
<?php
			}
		}
?>

	SetEnvIf X-Forwarded-Proto https HTTPS=1

	ServerAdmin webmaster@<?=$domainname;?>


	ServerName <?=$domainname;?>


	ServerAlias <?=$serveralias;?>


	<?=$general_header_text;?>


	<?=$static_files_expire_text;?>

<?php
		/*
			if (intval($microcache_time) > 0) {
?>

	<IfModule mod_headers.c>
		Header always set X-Hiawatha-Cache "<?=$microcache_time;?>"
	</IfModule>
<?php
			}
		*/
?>

	Include "<?=$acmechallenge_conf;?>"
<?php
			if ($count !== 0) {
				if ($enablessl) {
?>

	<IfModule mod_http2.c>
		Protocols h2 http/1.1
	</IfModule>

	<IfModule mod_ssl.c>
		Include "<?=$ssl_base_conf;?>"

		SSLCertificateFile <?=$certname;?>.pem
		SSLCertificateKeyFile <?=$certname;?>.key
<?php
					if (file_exists("{$certname}.ca")) {
?>
		SSLCACertificatefile <?=$certname;?>.ca

		<?=$https_header_text;?>

<?php
					}
?>
	</IfModule>
<?php
				} else {
?>

	<IfModule mod_http2.c>
		Protocols h2c http/1.1
	</IfModule>
<?php
				}
			}

			if (!$reverseproxy) {
				if ($wwwredirect) {
?>

	RewriteEngine On
	RewriteCond %{HTTP_HOST} ^<?=str_replace('.', '\.', $domainname);?>$ [NC]
	RewriteRule ^(.*)/$ <?=$protocol;?>www.<?=$domainname;?>/$1 [R=301,L]
<?php
				}

				if (($count === 0) && ($httpsredirect)) {
?>

	RewriteEngine On
	RewriteCond %{HTTPS} off
	RewriteRule (.*) https://%{HTTP_HOST}%{REQUEST_URI}
<?php
				}
			}
?>

	DocumentRoot "<?=$webdocroot;?>"

	DirectoryIndex <?=$indexorder;?>


	AliasMatch "/__kloxo(/|$)(.*)" "/home/<?=$user;?>/kloxoscript$1$2"

	Redirect "/kloxo" "<?=$protocol;?><?=$domainname;?>:<?=$kloxoport;?>"
	Redirect "/webmail" "<?=$protocol;?>webmail.<?=$domainname;?>"
	Redirect "/cp" "<?=$protocol;?>cp.<?=$domainname;?>"

	Redirect "/stats" "<?=$protocol;?>stats.<?=$domainname;?>/"
<?php
			if ($redirectionlocal) {
				foreach ($redirectionlocal as $rl) {
?>

	AliasMatch "<?=$rl[0];?>(/|$)(.*)" "<?=$webdocroot;?><?=$rl[1];?>$1$2"
<?php
				}
			}

			if ($redirectionremote) {
				foreach ($redirectionremote as $rr) {
					if ($rr[2] === 'both') {
?>

	Redirect "<?=$rr[0];?>" "<?=$protocol;?><?=$rr[1];?>"
<?php
					} else {
						$protocol2 = ($rr[2] === 'https') ? "https://" : "http://";
?>

	Redirect "<?=$rr[0];?>" "<?=$protocol2;?><?=$rr[1];?>"
<?php
					}
				}
			}

			if ($enablephp) {
?>

	<IfModule suexec.c>
		SuexecUserGroup <?=$sockuser;?> <?=$sockuser;?>

	</IfModule>

	<IfModule mod_suphp.c>
		SuPhp_UserGroup <?=$sockuser;?> <?=$sockuser;?>

		suPHP_Configpath "/home/httpd/<?=$domainname;?>/"
	</IfModule>

	#<IfVersion < 2.4>
		<IfModule mod_ruid2.c>
			RMode config
			RUidGid <?=$sockuser;?> <?=$sockuser;?>

			RMinUidGid <?=$sockuser;?> <?=$sockuser;?>

		</IfModule>

		<IfModule itk.c>
			AssignUserId <?=$sockuser;?> <?=$sockuser;?>

			<Location "/stats/">
				AssignUserId apache apache
			</Location>
		</IfModule>

		<IfModule mod_fastcgi.c>
			Alias /<?=$domainname;?>.<?=$count;?>fake "<?=$webdocroot;?>/<?=$domainname;?>.<?=$count;?>fake"
			#FastCGIExternalServer "<?=$webdocroot;?>/<?=$domainname;?>.<?=$count;?>fake" \
			#	-host 127.0.0.1:<?=$fpmport;?> -idle-timeout <?=$timeout;?> -pass-header Authorization
			FastCGIExternalServer "<?=$webdocroot;?>/<?=$domainname;?>.<?=$count;?>fake" \
				-socket /opt/configs/php-fpm/sock/<?=$phpselected;?>-<?=$sockuser;?>.sock \
				-idle-timeout <?=$timeout;?> -pass-header Authorization
			<FilesMatch \.php$>
				SetHandler application/x-httpd-fastphp
			</FilesMatch>
			Action application/x-httpd-fastphp /<?=$domainname;?>.<?=$count;?>fake
			<Files "<?=$domainname;?>.<?=$count;?>fake">
				RewriteCond %{REQUEST_URI} !<?=$domainname;?>.<?=$count;?>fake
			</Files>
		</IfModule>

		<IfModule !mod_ruid2.c>
			<IfModule !mod_itk.c>
				<IfModule !mod_fastcgi.c>
					<IfModule mod_fcgid.c>
						FcgidIOTimeout <?=$timeout;?>

						<Directory "<?=$webdocroot;?>/">
							Options +ExecCGI
							<FilesMatch \.php$>
								SetHandler fcgid-script
							</FilesMatch>
							FCGIWrapper /home/kloxo/client/<?=$user;?>/php.fcgi .php
						</Directory>
					</IfModule>
				</IfModule>
			</IfModule>
		</IfModule>
	#</IfVersion>

	<IfVersion >= 2.4>
		<IfModule mod_proxy_fcgi.c>
			ProxyRequests Off
			ProxyErrorOverride On
			ProxyPass /error !
			ErrorDocument 500 /error/500.html
			<FilesMatch \.php$>
				SetHandler "proxy:unix:/opt/configs/php-fpm/sock/<?=$phpselected;?>-<?=$sockuser;?>.sock|fcgi://localhost"
			</FilesMatch>
			<Proxy "fcgi://localhost">
				ProxySet timeout=<?=$timeout;?>

				ProxySet connectiontimeout=<?=$timeout;?>

				#ProxySet enablereuse=on
				ProxySet max=25
				ProxySet retry=0
			</Proxy>
		</IfModule>
	</IfVersion>
<?php
			}
?>
	<Directory "<?=$webdocroot;?>/">
		AllowOverride All
		<IfVersion < 2.4>
			Order allow,deny
			Allow from all
		</IfVersion>
		<IfVersion >= 2.4>
			Require all granted
		</IfVersion>
<?php
			if ($enablecgi) {
?>
		Options +ExecCGI
		<FilesMatch \.(cgi|pl|py)$>
			#<IfModule !mod_fastcgi.c>
				<IfModule mod_suphp.c>
					SuPhp_UserGroup <?=$sockuser;?> <?=$sockuser;?>

					SetHandler x-suphp-cgi
				</IfModule>
			#</IfModule>
		</FilesMatch>
<?php
			}
?>
	</Directory>
<?php
		//	if (($enablecgi) && ($driver[0] !== 'hiawatha')) {
			if ($enablecgi) {
?>

	ScriptAliasMatch "/cgi-bin(/|$)(.*)" "/home/<?=$user;?>/<?=$domainname;?>/cgi-bin$1$2"
<?php
			}
?>

	<IfModule mod_php5.c>
		php_admin_value sendmail_path "/usr/sbin/sendmail -t -i"
		php_admin_value sendmail_from "<?=$domainname;?>"
		Include "/home/kloxo/client/<?=$user;?>/prefork.inc"
	</IfModule>

	<Location "/">
		Options <?=$dirindex;?> -FollowSymlinks +SymLinksIfOwnerMatch
		<IfModule mod_php5.c>
			php_admin_value open_basedir "/home/<?=$user;?>:/tmp:/usr/share/pear:/var/lib/php/session/:/home/kloxo/httpd/script:/home/kloxo/httpd/disable/:<?=$extrabasedir;?>"
		</IfModule>
	</Location>
<?php
			if ($enablestats) {
				if (!$reverseproxy) {
?>

	CustomLog "/home/httpd/<?=$domainname;?>/stats/<?=$domainname;?>-custom_log" combined
	ErrorLog "/home/httpd/<?=$domainname;?>/stats/<?=$domainname;?>-error_log"

	Redirect "/stats" "<?=$protocol;?>stats.<?=$domainname;?>/"
	Redirect "/stats/" "<?=$protocol;?>stats.<?=$domainname;?>/"
<?php
				}
			}

			if ($apacheextratext) {
?>

	# Extra Tags - begin
	<?=$apacheextratext;?>

	# Extra Tags - end
<?php
			}

			if (!$enablephp) {
?>
	<FilesMatch \.php$>
		SetHandler application/x-httpd-php-source
	</FilesMatch>
<?php
			}

			if ($dirprotect) {
				foreach ($dirprotect as $k) {
					$protectpath = $k['path'];
					$protectauthname = $k['authname'];
					$protectfile = str_replace('/', '_', $protectpath) . '_';
?>

	<Location "/<?=$protectpath;?>/">
		AuthType Basic
		AuthName "<?=$protectauthname;?>"
		AuthUserFile "/home/httpd/<?=$domainname;?>/__dirprotect/<?=$protectfile;?>"
		Require valid-user
	</Location>
<?php
				}
			}

			if ($blockips) {
?>

	<Location "/">
		Order deny,allow
		Deny from <?=$blockips;?>

	</Location>
<?php
			}
?>

</VirtualHost>

<?php
		if ($domainredirect) {
			foreach ($domainredirect as $domredir) {
				$redirdomainname = $domredir['redirdomain'];
				$redirpath = ($domredir['redirpath']) ? $domredir['redirpath'] : null;
				$webmailmap = ($domredir['mailflag'] === 'on') ? true : false;

				$randnum = rand(0, 32767);

				if ($disabled) {
					$$redirfullpath = $disabledocroot;
				} else {
					if ($redirpath) {
						$redirfullpath = str_replace('//', '/', $webdocroot . '/' . $redirpath);
					} else {
						$redirfullpath = $webdocroot;
					}
				}
?>

## web for redirect '<?=$redirdomainname;?>'
<VirtualHost ${ip}:<?=$portlist[$count];?> >
<?php
			//	if ($disable_pagespeed) {
?>

	<IfModule pagespeed_module>
		ModPageSpeed unplugged
	</IfModule>
<?php
			//	}
?>

	SetEnvIf X-Forwarded-Proto https HTTPS=1

	ServerName <?=$redirdomainname;?>


	ServerAlias www.<?=$redirdomainname;?>


	Include "<?=$acmechallenge_conf;?>"

	DocumentRoot "<?=$webdocroot;?>"

	Redirect "/" "<?=$protocol;?><?=$domainname;?>/"

	<?=$general_header_text;?>

<?php
				if ($count !== 0) {
					if ($enablessl) {
?>

	<IfModule mod_http2.c>
		Protocols h2 http/1.1
	</IfModule>

	<IfModule mod_ssl.c>
		Include "<?=$ssl_base_conf;?>"

		SSLCertificateFile <?=$certname;?>.pem
		SSLCertificateKeyFile <?=$certname;?>.key
<?php
						if (file_exists("{$certname}.ca")) {
?>
		SSLCACertificatefile <?=$certname;?>.ca

		<?=$https_header_text;?>

<?php
						}
?>
	</IfModule>
<?php
					} else {
?>

	<IfModule mod_http2.c>
		Protocols h2c http/1.1
	</IfModule>
<?php
					}
				}
?>

</VirtualHost>

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
<VirtualHost ${ip}:<?=$portlist[$count];?> >
<?php
					if ($disable_pagespeed) {
?>

	<IfModule pagespeed_module>
		ModPageSpeed unplugged
	</IfModule>
<?php
					}
?>

	SetEnvIf X-Forwarded-Proto https HTTPS=1

	ServerName webmail.<?=$parkdomainname;?>


	ServerAlias mail.<?=$parkdomainname;?>


	Include "<?=$acmechallenge_conf;?>"

	DocumentRoot "<?=$webmaildocroot;?>"

	<?=$general_header_text;?>

<?php
					if ($count !== 0) {
?>

	<IfModule mod_http2.c>
		Protocols h2 http/1.1
	</IfModule>

	<IfModule mod_ssl.c>
		Include "<?=$ssl_base_conf;?>"

		SSLCertificateFile <?=$certname;?>.pem
		SSLCertificateKeyFile <?=$certname;?>.key
<?php
						if (file_exists("{$certname}.ca")) {
?>
		SSLCACertificatefile <?=$certname;?>.ca

		<?=$https_header_text;?>

<?php
						}
?>
	</IfModule>
<?php
					} else {
?>

	<IfModule mod_http2.c>
		Protocols h2c http/1.1
	</IfModule>
<?php
					}
						
					if ($webmailremote) {
?>

	Redirect "/" "<?=$protocol;?><?=$webmailremote;?>"
<?php
					} else {
?>

	<IfModule suexec.c>
		SuexecUserGroup apache apache
	</IfModule>

	<IfModule mod_suphp.c>
		SuPhp_UserGroup apache apache
	</IfModule>

	#<IfVersion < 2.4>
		<IfModule mod_ruid2.c>
			RMode config
			RUidGid apache apache
			RMinUidGid apache apache
		</IfModule>

		<IfModule itk.c>
			AssignUserId apache apache
		</IfModule>

		<IfModule mod_fastcgi.c>
			Alias /webmail.<?=$parkdomainname;?>.<?=$count;?>fake "<?=$webmaildocroot;?>/webmail.<?=$parkdomainname;?>.<?=$count;?>fake"
			#FastCGIExternalServer "<?=$webmaildocroot;?>/webmail.<?=$parkdomainname;?>.<?=$count;?>fake" \
			#	-host 127.0.0.1:<?=$fpmportapache;?> -idle-timeout <?=$timeout;?> -pass-header Authorization
			FastCGIExternalServer "<?=$webmaildocroot;?>/webmail.<?=$parkdomainname;?>.<?=$count;?>fake" \
				-socket /opt/configs/php-fpm/sock/<?=$phpselected;?>-apache.sock \
				-idle-timeout <?=$timeout;?> -pass-header Authorization
			<FilesMatch \.php$>
				SetHandler application/x-httpd-fastphp
			</FilesMatch>
			Action application/x-httpd-fastphp /webmail.<?=$parkdomainname;?>.<?=$count;?>fake
			<Files "webmail.<?=$parkdomainname;?>.<?=$count;?>fake">
				RewriteCond %{REQUEST_URI} !webmail.<?=$parkdomainname;?>.<?=$count;?>fake
			</Files>
		</IfModule>

		<IfModule !mod_ruid2.c>
			<IfModule !mod_itk.c>
				<IfModule !mod_fastcgi.c>
					<IfModule mod_fcgid.c>
						FcgidIOTimeout <?=$timeout;?>

						<Directory "<?=$webmaildocroot;?>/">
							Options +ExecCGI
							<FilesMatch \.php$>
								SetHandler fcgid-script
							</FilesMatch>
							FCGIWrapper /home/kloxo/client/php.fcgi .php
						</Directory>
					</IfModule>
				</IfModule>
			</IfModule>
		</IfModule>
	#</IfVersion>

	<IfVersion >= 2.4>
		<IfModule mod_proxy_fcgi.c>
			ProxyRequests Off
			ProxyErrorOverride On
			ProxyPass /error !
			ErrorDocument 500 /error/500.html
			<FilesMatch \.php$>
				SetHandler "proxy:unix:/opt/configs/php-fpm/sock/<?=$phpselected;?>-apache.sock|fcgi://localhost"
			</FilesMatch>
			<Proxy "fcgi://localhost">
				ProxySet timeout=<?=$timeout;?>

				ProxySet connectiontimeout=<?=$timeout;?>

				#ProxySet enablereuse=on
				ProxySet max=25
				ProxySet retry=0
			</Proxy>
		</IfModule>
	</IfVersion>

	<Location "/">
		Options -Indexes -FollowSymlinks +SymLinksIfOwnerMatch
	</Location>

	<Directory "<?=$webmaildocroot;?>/">
		AllowOverride All
		<IfVersion < 2.4>
			Order allow,deny
			Allow from all
		</IfVersion>
		<IfVersion >= 2.4>
			Require all granted
		</IfVersion>
	</Directory>
<?php
					}
?>

</VirtualHost>

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
<VirtualHost ${ip}:<?=$portlist[$count];?> >
<?php
				//	if ($disable_pagespeed) {
?>

	<IfModule pagespeed_module>
		ModPageSpeed unplugged
	</IfModule>
<?php
				//	}
?>

	SetEnvIf X-Forwarded-Proto https HTTPS=1

	ServerName webmail.<?=$redirdomainname;?>


	ServerAlias mail.<?=$redirdomainname;?>


	Include "<?=$acmechallenge_conf;?>"

	DocumentRoot "<?=$webmaildocroot;?>"

	<?=$general_header_text;?>

<?php
					if ($count !== 0) {
?>

	<IfModule mod_http2.c>
		Protocols h2 http/1.1
	</IfModule>

	<IfModule mod_ssl.c>
		Include "<?=$ssl_base_conf;?>"

		SSLCertificateFile <?=$certname;?>.pem
		SSLCertificateKeyFile <?=$certname;?>.key
<?php
						if (file_exists("{$certname}.ca")) {
?>
		SSLCACertificatefile <?=$certname;?>.ca

		<?=$https_header_text;?>

<?php
						}
?>
	</IfModule>
<?php
					} else {
?>

	<IfModule mod_http2.c>
		Protocols h2c http/1.1
	</IfModule>
<?php
					}

					if ($webmailremote) {
?>

	Redirect "/" "<?=$protocol;?><?=$webmailremote;?>"
<?php
					} else {
?>

	<IfModule suexec.c>
		SuexecUserGroup apache apache
	</IfModule>

	<IfModule mod_suphp.c>
		SuPhp_UserGroup apache apache
	</IfModule>

	#<IfVersion < 2.4>
		<IfModule mod_ruid2.c>
			RMode config
			RUidGid apache apache
			RMinUidGid apache apache
		</IfModule>

		<IfModule itk.c>
			AssignUserId apache apache
		</IfModule>

		<IfModule mod_fastcgi.c>
			Alias /webmail.<?=$parkdomainname;?>.<?=$count;?>fake "<?=$webmaildocroot;?>/webmail.<?=$parkdomainname;?>.<?=$count;?>fake"
			#FastCGIExternalServer "<?=$webmaildocroot;?>/webmail.<?=$parkdomainname;?>.<?=$count;?>fake" \
			#	-host 127.0.0.1:<?=$fpmportapache;?> -idle-timeout <?=$timeout;?> -pass-header Authorization
			FastCGIExternalServer "<?=$webmaildocroot;?>/webmail.<?=$parkdomainname;?>.<?=$count;?>fake" \
				-socket /opt/configs/php-fpm/sock/<?=$phpselected;?>-apache.sock \
				-idle-timeout <?=$timeout;?> -pass-header Authorization
			<FilesMatch \.php$>
				SetHandler application/x-httpd-fastphp
			</FilesMatch>
			Action application/x-httpd-fastphp /webmail.<?=$parkdomainname;?>.<?=$count;?>fake
			<Files "webmail.<?=$parkdomainname;?>.<?=$count;?>fake">
				RewriteCond %{REQUEST_URI} !webmail.<?=$parkdomainname;?>.<?=$count;?>fake
			</Files>
		</IfModule>

		<IfModule !mod_ruid2.c>
			<IfModule !mod_itk.c>
				<IfModule !mod_fastcgi.c>
					<IfModule mod_fcgid.c>
						FcgidIOTimeout <?=$timeout;?>

						<Directory "<?=$webmaildocroot;?>/">
							Options +ExecCGI
							<FilesMatch \.php$>
								SetHandler fcgid-script
							</FilesMatch>
							FCGIWrapper /home/kloxo/client/php.fcgi .php
						</Directory>
					</IfModule>
				</IfModule>
			</IfModule>
		</IfModule>
	#</IfVersion>

	<IfVersion >= 2.4>
		<IfModule mod_proxy_fcgi.c>
			ProxyRequests Off
			ProxyErrorOverride On
			ProxyPass /error !
			ErrorDocument 500 /error/500.html
			<FilesMatch \.php$>
				SetHandler "proxy:unix:/opt/configs/php-fpm/sock/<?=$phpselected;?>-apache.sock|fcgi://localhost"
			</FilesMatch>
			<Proxy "fcgi://localhost">
				ProxySet timeout=<?=$timeout;?>

				ProxySet connectiontimeout=<?=$timeout;?>

				#ProxySet enablereuse=on
				ProxySet max=25
				ProxySet retry=0
			</Proxy>
		</IfModule>
	</IfVersion>

	<Location "/">
		Options -Indexes -FollowSymlinks +SymLinksIfOwnerMatch
	</Location>

	<Directory "<?=$webmaildocroot;?>/">
		AllowOverride All
		<IfVersion < 2.4>
			Order allow,deny
			Allow from all
		</IfVersion>
		<IfVersion >= 2.4>
			Require all granted
		</IfVersion>
	</Directory>
<?php
					}
?>

</VirtualHost>

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
