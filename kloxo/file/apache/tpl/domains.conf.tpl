### begin - web of '<?php echo $domainname; ?>' - do not remove/modify this line

<?php

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

$portlist = array('${global::port}', '${global::portssl}');

if ($reverseproxy) {
	$tmp_ip = '127.0.0.1';

	foreach ($certnamelist as $ip => $certname) {
		$tmp_certname = $certname;
		break;
	}

	$certnamelist = null;

	$certnamelist[$tmp_ip] = $tmp_certname;
}

foreach ($certnamelist as $ip => $certname) {
	if (file_exists("/home/{$user}/ssl/{$domainname}.key")) {
		$certnamelist[$ip] = "/home/{$user}/ssl/{$domainname}";
	} else {
		$certnamelist[$ip] = "/home/kloxo/httpd/ssl/{$certname}";
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

if ($indexorder) {
	$indexorder = implode(' ', $indexorder);
}

if ($blockips) {
	$biptemp = array();
	foreach ($blockips as &$bip) {
		if (strpos($bip, ".*.*.*") !== false) { $bip = str_replace(".*.*.*", ".0.0/8", $bip); }
		if (strpos($bip, ".*.*") !== false) { $bip = str_replace(".*.*", ".0.0/16", $bip); }
		if (strpos($bip, ".*") !== false)  { $bip = str_replace(".*", ".0/24", $bip); }
		$biptemp[] = $bip;
	}
	$blockips = $biptemp;

	$blockips = implode(' ', $blockips);
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

$disablepath = "/home/kloxo/httpd/disable";

if ($disabled) {
	$sockuser = 'apache';
} else {
	$sockuser = $user;
}

if (!$reverseproxy) {
	foreach ($certnamelist as $ip => $certname) {
		if ($ip !== '*') {
?>

Define ip <$php echo $ip; ?>


NameVirtualHost ${ip}:${global::port}
NameVirtualHost ${ip}:${global::portssl}

<?php
		} else {
?>

Define ip ${global::ip}
<?php
		}
	}
} else {
?>

Define ip ${global::ip}
<?php
}

foreach ($certnamelist as $ip => $certname) {
	$count = 0;

	foreach ($ports as &$port) {
		$protocol = ($count === 0) ? "http://" : "https://";

		if ($disabled) {
?>

## webmail for '<?php echo $domainname; ?>'
<VirtualHost ${ip}:<?php echo $portlist[$count]; ?>>

	SetEnvIf X-Forwarded-Proto https HTTPS=1

	ServerName webmail.<?php echo $domainname; ?>


	DocumentRoot "<?php echo $disablepath; ?>"

	DirectoryIndex <?php echo $indexorder; ?>

<?php
				if ($count !== 0) {
?>

	<IfModule mod_ssl.c>
		SSLEngine On
		SSLCertificateFile <?php echo $certname; ?>.pem
		SSLCertificateKeyFile <?php echo $certname; ?>.key
<?php
					if (file_exists("{$certname}.ca")) {

?>
		SSLCACertificatefile <?php echo $certname; ?>.ca
<?php
					}
?>
	</IfModule>
<?php
				}

				//if (strpos($phptype, '_suphp') !== false) {
?>

	<IfModule suexec.c>
		SuexecUserGroup apache apache
	</IfModule>

	<IfModule mod_suphp.c>
		SuPhp_UserGroup apache apache
	</IfModule>
<?php
				//} elseif (strpos($phptype, '_ruid2') !== false) {
				//if (strpos($phptype, '_ruid2') !== false) {
?>

	<IfModule mod_ruid2.c>
		RMode config
		RUidGid apache apache
		RMinUidGid apache apache
	</IfModule>
<?php
				//} elseif (strpos($phptype, '_itk') !== false) {
?>

	<IfModule itk.c>
		AssignUserId apache apache
	</IfModule>
<?php
				//} elseif (strpos($phptype, 'php-fpm_') !== false) {
?>

	<IfModule mod_fastcgi.c>
		Alias /webmail.<?php echo $domainname; ?>.<?php echo $count; ?>fake "<?php echo $disablepath; ?>/webmail.<?php echo $domainname; ?>.<?php echo $count; ?>fake"
		#FastCGIExternalServer "<?php echo $disablepath; ?>/webmail.<?php echo $domainname; ?>.<?php echo $count; ?>fake" -host 127.0.0.1:<?php echo $fpmportapache; ?> -idle-timeout 90 -pass-header Authorization
		FastCGIExternalServer "<?php echo $disablepath; ?>/webmail.<?php echo $domainname; ?>.<?php echo $count; ?>fake" -socket /home/php-fpm/sock/apache.sock -idle-timeout 90 -pass-header Authorization
		AddType application/x-httpd-fastphp .php
		Action application/x-httpd-fastphp /webmail.<?php echo $domainname; ?>.<?php echo $count; ?>fake
		<Files "webmail.<?php echo $domainname; ?>.<?php echo $count; ?>fake">
			RewriteCond %{REQUEST_URI} !webmail.<?php echo $domainname; ?>.<?php echo $count; ?>fake
		</Files>
	</IfModule>
<?php
				//} elseif (strpos($phptype, 'fcgid_') !== false) {
?>

	<IfModule mod_fcgid.c>
		<Directory "<?php echo $disablepath; ?>/">
			Options +ExecCGI
			AddHandler fcgid-script .php
			FCGIWrapper /home/kloxo/client/php5.fcgi .php
		</Directory>
	</IfModule>
<?php
				//} elseif (strpos($phptype, 'proxy-fcgi_') !== false) {
?>

	<IfModule mod_proxy_fcgi.c>
		ProxyPass / fcgi://127.0.0.1:<?php echo $fpmportapache; ?>/
		ProxyPassReverse / fcgi://127.0.0.1:<?php echo $fpmportapache; ?>/
	</IfModule>
<?php
				//}
?>

	<Location />
		Allow from all
		# Options +Indexes +FollowSymlinks
		Options -Indexes -FollowSymlinks +SymLinksIfOwnerMatch
	</Location>
<?php
				//if (strpos($phptype, 'fcgid_') === false) {
?>

	<Directory "<?php echo $disablepath; ?>/">
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
				//}
?>

</VirtualHost>

<?php
		} else {
			if ($webmailremote) {
?>

## webmail for '<?php echo $domainname; ?>'
<VirtualHost ${ip}:<?php echo $portlist[$count]; ?>>

	SetEnvIf X-Forwarded-Proto https HTTPS=1

	ServerName webmail.<?php echo $domainname; ?>


	Redirect / "<?php echo $protocol; ?><?php echo $webmailremote; ?>"
<?php
					if ($count !== 0) {
?>

	<IfModule mod_ssl.c>
		SSLEngine On
		SSLCertificateFile <?php echo $certname; ?>.pem
		SSLCertificateKeyFile <?php echo $certname; ?>.key
<?php
						if (file_exists("{$certname}.ca")) {

?>
		SSLCACertificatefile <?php echo $certname; ?>.ca
<?php
						}
?>
	</IfModule>
<?php
					}
?>

</VirtualHost>

<?php
			} else {
?>

## webmail for '<?php echo $domainname; ?>'
<VirtualHost ${ip}:<?php echo $portlist[$count]; ?>>

	SetEnvIf X-Forwarded-Proto https HTTPS=1

	ServerName webmail.<?php echo $domainname; ?>


	DocumentRoot "<?php echo $webmaildocroot; ?>"

	DirectoryIndex <?php echo $indexorder; ?>

<?php
					if ($count !== 0) {
?>

	<IfModule mod_ssl.c>
		SSLEngine On
		SSLCertificateFile <?php echo $certname; ?>.pem
		SSLCertificateKeyFile <?php echo $certname; ?>.key
<?php
						if (file_exists("{$certname}.ca")) {

?>
		SSLCACertificatefile <?php echo $certname; ?>.ca
<?php
						}
?>
	</IfModule>
<?php
					}

					//if (strpos($phptype, '_suphp') !== false) {
?>

	<IfModule suexec.c>
		SuexecUserGroup apache apache
	</IfModule>

	<IfModule mod_suphp.c>
		SuPhp_UserGroup apache apache
	</IfModule>
<?php
					//} elseif (strpos($phptype, '_ruid2') !== false) {
					//if (strpos($phptype, '_ruid2') !== false) {
?>

	<IfModule mod_ruid2.c>
		RMode config
		RUidGid apache apache
		RMinUidGid apache apache
	</IfModule>
<?php
					//} elseif (strpos($phptype, '_itk') !== false) {
?>

	<IfModule itk.c>
		AssignUserId apache apache
	</IfModule>
<?php
					//} elseif (strpos($phptype, 'php-fpm_') !== false) {
?>

	<IfModule mod_fastcgi.c>
		Alias /webmail.<?php echo $domainname; ?>.<?php echo $count; ?>fake "<?php echo $webmaildocroot; ?>/webmail.<?php echo $domainname; ?>.<?php echo $count; ?>fake"
		#FastCGIExternalServer "<?php echo $webmaildocroot; ?>/webmail.<?php echo $domainname; ?>.<?php echo $count; ?>fake" -host 127.0.0.1:<?php echo $fpmportapache; ?> -idle-timeout 90 -pass-header Authorization
		FastCGIExternalServer "<?php echo $webmaildocroot; ?>/webmail.<?php echo $domainname; ?>.<?php echo $count; ?>fake" -socket /home/php-fpm/sock/apache.sock -idle-timeout 90 -pass-header Authorization
		AddType application/x-httpd-fastphp .php
		Action application/x-httpd-fastphp /webmail.<?php echo $domainname; ?>.<?php echo $count; ?>fake
		<Files "webmail.<?php echo $domainname; ?>.<?php echo $count; ?>fake">
			RewriteCond %{REQUEST_URI} !webmail.<?php echo $domainname; ?>.<?php echo $count; ?>fake
		</Files>
	</IfModule>
<?php
					//} elseif (strpos($phptype, 'fcgid_') !== false) {
?>

	<IfModule mod_fcgid.c>
		<Directory "<?php echo $webmaildocroot; ?>/">
			Options +ExecCGI
			AddHandler fcgid-script .php
			FCGIWrapper /home/kloxo/client/php5.fcgi .php
		</Directory>
	</IfModule>
<?php
					//} elseif (strpos($phptype, 'proxy-fcgi_') !== false) {
?>

	<IfModule mod_proxy_fcgi.c>
		ProxyPass / fcgi://127.0.0.1:<?php echo $fpmportapache; ?>/
		ProxyPassReverse / fcgi://127.0.0.1:<?php echo $fpmportapache; ?>/
	</IfModule>
<?php
					//}
?>

	<Location />
		Allow from all
		# Options +Indexes +FollowSymlinks
		Options -Indexes -FollowSymlinks +SymLinksIfOwnerMatch
	</Location>
<?php
					//if (strpos($phptype, 'fcgid_') === false) {
?>

	<Directory "<?php echo $webmaildocroot; ?>/">
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
					//}
?>

</VirtualHost>

<?php
			}
		}
?>

## web for '<?php echo $domainname; ?>'
<VirtualHost ${ip}:<?php echo $portlist[$count]; ?>>

	SetEnvIf X-Forwarded-Proto https HTTPS=1

	ServerAdmin webmaster@<?php echo $domainname; ?>


	ServerName <?php echo $domainname; ?>


	ServerAlias <?php echo $serveralias; ?>

<?php
			if ($count !== 0) {
?>

	<IfModule mod_ssl.c>
		SSLEngine On
		SSLCertificateFile <?php echo $certname; ?>.pem
		SSLCertificateKeyFile <?php echo $certname; ?>.key
<?php
				if (file_exists("{$certname}.ca")) {

?>
		SSLCACertificatefile <?php echo $certname; ?>.ca
<?php
				}
?>
	</IfModule>
<?php
			}

			if ($wwwredirect) {
?>

	RewriteEngine On
	RewriteCond %{HTTP_HOST} ^<?php echo str_replace('.', '\.', $domainname); ?>$ [NC]
	RewriteRule ^(.*)/$ <?php echo $protocol; ?>www.<?php echo $domainname; ?>/$1 [R=301,L]
<?php
			}

			if ($disabled) {
				$rootpath = $disablepath;
			}
?>

	DocumentRoot "<?php echo $rootpath; ?>"

	DirectoryIndex <?php echo $indexorder; ?>


	Alias /__kloxo "/home/<?php echo $user; ?>/kloxoscript/"

	Redirect /kloxo "https://cp.<?php echo $domainname; ?>:7777"
	Redirect /kloxononssl "http://cp.<?php echo $domainname; ?>:7778"

	Redirect /webmail "<?php echo $protocol; ?>webmail.<?php echo $domainname; ?>"
<?php
			if (($enablecgi) && ($webdriverlist[0] !== 'hiawatha')) {
?>

	ScriptAlias /cgi-bin/ "/home/<?php echo $user; ?>/<?php echo $domainname; ?>/cgi-bin/"
<?php
			}

			if ($redirectionlocal) {
				foreach ($redirectionlocal as $rl) {
?>

	Alias <?php echo $rl[0]; ?> "<?php echo $rootpath; ?><?php echo $rl[1]; ?>/"
<?php
				}
			}

			if ($redirectionremote) {
				foreach ($redirectionremote as $rr) {
					if ($rr[2] === 'both') {
?>

	Redirect <?php echo $rr[0]; ?> "<?php echo $protocol; ?><?php echo $rr[1]; ?>"
<?php
					} else {
						$protocol2 = ($rr[2] === 'https') ? "https://" : "http://";
?>

	Redirect <?php echo $rr[0]; ?> "<?php echo $protocol2; ?><?php echo $rr[1]; ?>"
<?php
					}
				}
			}

			//if (strpos($phptype, '_suphp') !== false) {
?>

	<IfModule suexec.c>
		SuexecUserGroup <?php echo $sockuser; ?> <?php echo $sockuser; ?>

	</IfModule>

	<IfModule mod_suphp.c>
		SuPhp_UserGroup <?php echo $sockuser; ?> <?php echo $sockuser; ?>

		suPHP_Configpath "/home/httpd/<?php echo $domainname; ?>/"
	</IfModule>
<?php
			//} elseif (strpos($phptype, '_ruid2') !== false) {
			//if (strpos($phptype, '_ruid2') !== false) {
?>

	<IfModule mod_ruid2.c>
		RMode config
		RUidGid <?php echo $sockuser; ?> <?php echo $sockuser; ?>

		RMinUidGid <?php echo $sockuser; ?> <?php echo $sockuser; ?>

	</IfModule>
<?php
			//} elseif (strpos($phptype, '_itk') !== false) {
?>

	<IfModule itk.c>
		AssignUserId <?php echo $sockuser; ?> <?php echo $sockuser; ?>


		<Location "/awstats/">
			AssignUserId apache apache
		</Location>
	</IfModule>
<?php
			//} elseif (strpos($phptype, 'php-fpm_') !== false) {
?>

	<IfModule mod_fastcgi.c>
		Alias /<?php echo $domainname; ?>.<?php echo $count; ?>fake "<?php echo $rootpath; ?>/<?php echo $domainname; ?>.<?php echo $count; ?>fake"
		#FastCGIExternalServer "<?php echo $rootpath; ?>/<?php echo $domainname; ?>.<?php echo $count; ?>fake" -host 127.0.0.1:<?php echo $fpmport; ?> -idle-timeout 90 -pass-header Authorization
		FastCGIExternalServer "<?php echo $rootpath; ?>/<?php echo $domainname; ?>.<?php echo $count; ?>fake" -socket /home/php-fpm/sock/<?php echo $sockuser; ?>.sock -idle-timeout 90 -pass-header Authorization
		AddType application/x-httpd-fastphp .php
		Action application/x-httpd-fastphp /<?php echo $domainname; ?>.<?php echo $count; ?>fake
		<Files "<?php echo $domainname; ?>.<?php echo $count; ?>fake">
			RewriteCond %{REQUEST_URI} !<?php echo $domainname; ?>.<?php echo $count; ?>fake
		</Files>
	</IfModule>
<?php
			//} elseif (strpos($phptype, 'fcgid_') !== false) {
?>

	<IfModule mod_fcgid.c>
		<Directory "<?php echo $rootpath; ?>/">
			Options +ExecCGI
			AddHandler fcgid-script .php
			FCGIWrapper /home/kloxo/client/<?php echo $user; ?>/php5.fcgi .php
		</Directory>
	</IfModule>
<?php
			//} elseif (strpos($phptype, 'proxy-fcgi_') !== false) {
?>

	<IfModule mod_proxy_fcgi.c>
		ProxyPass / fcgi://127.0.0.1:<?php echo $fpmport; ?>/
		ProxyPassReverse / fcgi://127.0.0.1:<?php echo $fpmport; ?>/
	</IfModule>
<?php
			//}

			//if (strpos($phptype, 'fcgid_') === false) {
?>

	<Directory "<?php echo $rootpath; ?>/">
		AllowOverride All
		<IfVersion < 2.4>
			Order allow,deny
			Allow from all
		</IfVersion>
		<IfVersion >= 2.4>
			Require all granted
		</IfVersion>
<?php
			if (($enablecgi) && ($webdriverlist[0] !== 'hiawatha')) {
?>
		Options +ExecCGI
		AddHandler cgi-script .cgi .pl
<?php
			}
?>
	</Directory>
<?php
			//}
?>

	<IfModule mod_php5.c>
		php_admin_value sendmail_path "/usr/sbin/sendmail -t -i"
		php_admin_value sendmail_from "<?php echo $domainname; ?>"
	</IfModule>

	<Location />
		Allow from all
		# Options +Indexes +FollowSymlinks
		Options -Indexes -FollowSymlinks +SymLinksIfOwnerMatch

		<IfModule mod_php5.c>
			php_admin_value open_basedir "/home/<?php echo $user; ?>:/tmp:/usr/share/pear:/var/lib/php/session/:/home/kloxo/httpd/script:/home/kloxo/httpd/disable/:<?php echo $extrabasedir; ?>"
		</IfModule>
	</Location>

	CustomLog "/home/httpd/<?php echo $domainname ?>/stats/<?php echo $domainname ?>-custom_log" combined
	ErrorLog "/home/httpd/<?php echo $domainname ?>/stats/<?php echo $domainname ?>-error_log"
<?php
			if ($statsapp === 'awstats') {
?>

	ScriptAlias /awstats/ "/home/kloxo/httpd/awstats/wwwroot/cgi-bin/"

	Alias /awstatscss "/home/kloxo/httpd/awstats/wwwroot/css/"
	Alias /awstatsicons "/home/kloxo/httpd/awstats/wwwroot/icon/"

	Redirect /stats "<?php echo $protocol; ?><?php echo $domainname; ?>/awstats/awstats.pl"
	Redirect /stats/ "<?php echo $protocol; ?><?php echo $domainname; ?>/awstats/awstats.pl"

	<Location "/stats/">
	          Options +Indexes
	</Location>
<?php
	   		if ($statsprotect) {
		     ?>

	<Location "/awstats/">
		AuthType Basic
		AuthName "Awstats"
		#AuthUserFile "/home/<?php echo $user; ?>/__dirprotect/__stats"
		AuthUserFile "/home/httpd/<?php echo $domainname ?>/__dirprotect/__stats"
		require valid-user
	</Location>
<?php
	   		}
		} elseif ($statsapp === 'webalizer') {
?>

	Alias /stats "/home/httpd/<?php echo $domainname; ?>/webstats/"

	<Location "/stats/">
		Options +Indexes
	</Location>
<?php
			if ($statsprotect) {
?>

	<Location "/stats/">
		AuthType Basic
		AuthName "stats"
		#AuthUserFile "/home/<?php echo $user; ?>/__dirprotect/__stats"
		AuthUserFile "/home/httpd/<?php echo $domainname ?>/__dirprotect/__stats"
		require valid-user
	</Location>
<?php
			}
		}

		if ($apacheextratext) {
?>

	# Extra Tags - begin
<?php echo $apacheextratext; ?>

	# Extra Tags - end
<?php
		}

		if ($disablephp) {
?>
	AddType application/x-httpd-php-source .php
<?php
		}

		if ($dirprotect) {
			foreach ($dirprotect as $k) {
				$protectpath = $k['path'];
				$protectauthname = $k['authname'];
				$protectfile = str_replace('/', '_', $protectpath) . '_';
?>

	<Location "/<?php echo $protectpath; ?>/">
		AuthType Basic
		AuthName "<?php echo $protectauthname; ?>"
		AuthUserFile "/home/httpd/<?php echo $domainname; ?>/__dirprotect/<?php echo $protectfile; ?>"
		require valid-user
	</Location>
<?php
	   		}
		}

		if ($blockips) {
?>

	<Location />
		Order deny,allow
		Deny from <?php echo $blockips; ?>

		Allow from all
		Options -Indexes -FollowSymlinks +SymLinksIfOwnerMatch
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

				if ($redirpath) {
					if ($disabled) {
						$$redirfullpath = $disablepath;
					} else {
						$redirfullpath = str_replace('//', '/', $rootpath . '/' . $redirpath);
					}
?>

## web for redirect '<?php echo $redirdomainname; ?>'
<VirtualHost ${ip}:<?php echo $portlist[$count]; ?>>

	SetEnvIf X-Forwarded-Proto https HTTPS=1

	ServerName <?php echo $redirdomainname; ?>


	ServerAlias www.<?php echo $redirdomainname; ?>


	DocumentRoot "<?php echo $redirfullpath; ?>"

	DirectoryIndex <?php echo $indexorder; ?>

<?php
					if ($count !== 0) {
?>

	<IfModule mod_ssl.c>
		SSLEngine On
		SSLCertificateFile <?php echo $certname; ?>.pem
		SSLCertificateKeyFile <?php echo $certname; ?>.key
<?php
						if (file_exists("{$certname}.ca")) {

?>
		SSLCACertificatefile <?php echo $certname; ?>.ca
<?php
						}
?>
	</IfModule>
<?php
					}

					//if (strpos($phptype, '_suphp') !== false) {
?>

	<IfModule suexec.c>
		SuexecUserGroup <?php echo $sockuser; ?> <?php echo $sockuser; ?>

	</IfModule>

	<IfModule mod_suphp.c>
		SuPhp_UserGroup <?php echo $sockuser; ?> <?php echo $sockuser; ?>

		suPHP_Configpath "/home/httpd/<?php echo $domainname; ?>/"
	</IfModule>
<?php
					//} elseif (strpos($phptype, '_ruid2') !== false) {
					//if (strpos($phptype, '_ruid2') !== false) {
?>

	<IfModule mod_ruid2.c>
		RMode config
		RUidGid <?php echo $sockuser; ?> <?php echo $sockuser; ?>

		RMinUidGid <?php echo $sockuser; ?> <?php echo $sockuser; ?>

	</IfModule>
<?php
					//} elseif (strpos($phptype, '_itk') !== false) {
?>

	<IfModule itk.c>
		AssignUserId <?php echo $sockuser; ?> <?php echo $sockuser; ?>

		<Location "/awstats/">
			AssignUserId apache apache
		</Location>
	</IfModule>
<?php
					//} elseif (strpos($phptype, 'php-fpm_') !== false) {
?>

	<IfModule mod_fastcgi.c>
		Alias /<?php echo $redirdomainname; ?>.<?php echo $count; ?>fake "<?php echo $redirfullpath; ?>/<?php echo $redirdomainname; ?>.<?php echo $count; ?>fake"
		#FastCGIExternalServer "<?php echo $redirfullpath; ?>/<?php echo $redirdomainname; ?>.<?php echo $count; ?>fake" -host 127.0.0.1:<?php echo $fpmport; ?> -idle-timeout 90 -pass-header Authorization
		FastCGIExternalServer "<?php echo $redirfullpath; ?>/<?php echo $redirdomainname; ?>.<?php echo $count; ?>fake" -socket /home/php-fpm/sock/<?php echo $sockuser; ?>.sock -idle-timeout 90 -pass-header Authorization
		AddType application/x-httpd-fastphp .php
		Action application/x-httpd-fastphp /<?php echo $redirdomainname; ?>.<?php echo $count; ?>fake
		<Files "<?php echo $redirdomainname; ?>.<?php echo $count; ?>fake">
			RewriteCond %{REQUEST_URI} !<?php echo $redirdomainname; ?>.<?php echo $count; ?>fake
		</Files>
	</IfModule>
<?php
					//} elseif (strpos($phptype, 'fcgid_') !== false) {
?>

	<IfModule mod_fcgid.c>
		<Directory "<?php echo $redirfullpath; ?>/">
			Options +ExecCGI
			AddHandler fcgid-script .php
			FCGIWrapper /home/kloxo/client/<?php echo $user; ?>/php5.fcgi .php
		</Directory>
	</IfModule>
<?php
					//} elseif (strpos($phptype, 'proxy-fcgi_') !== false) {
?>

	<IfModule mod_proxy_fcgi.c>
		ProxyPass / fcgi://127.0.0.1:<?php echo $fpmport; ?>/
		ProxyPassReverse / fcgi://127.0.0.1:<?php echo $fpmport; ?>/
	</IfModule>
<?php
					//}

					//if (strpos($phptype, 'fcgid_') === false) {
?>


	<Directory "<?php echo $redirfullpath; ?>/">
		AllowOverride All
		<IfVersion < 2.4>
			Order allow,deny
			Allow from all
		</IfVersion>
		<IfVersion >= 2.4>
			Require all granted
		</IfVersion>
<?php
			if (($enablecgi) && ($webdriverlist[0] !== 'hiawatha')) {
?>
		Options +ExecCGI
		AddHandler cgi-script .cgi .pl
<?php
			}
?>
	</Directory>
<?php
					//}

?>

</VirtualHost>

<?php
				} else {
?>

## web for redirect '<?php echo $redirdomainname; ?>'
<VirtualHost ${ip}:<?php echo $portlist[$count]; ?>>

	SetEnvIf X-Forwarded-Proto https HTTPS=1

	ServerName <?php echo $redirdomainname; ?>


	ServerAlias www.<?php echo $redirdomainname; ?>


	Redirect / "<?php echo $protocol; ?><?php echo $domainname; ?>/"
<?php
					if ($count !== 0) {
?>

	<IfModule mod_ssl.c>
		SSLEngine On
		SSLCertificateFile <?php echo $certname; ?>.pem
		SSLCertificateKeyFile <?php echo $certname; ?>.key
<?php
						if (file_exists("{$certname}.ca")) {

?>
		SSLCACertificatefile <?php echo $certname; ?>.ca
<?php
						}
?>
	</IfModule>
<?php
					}
?>

</VirtualHost>

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
<VirtualHost ${ip}:<?php echo $portlist[$count]; ?>>

	SetEnvIf X-Forwarded-Proto https HTTPS=1

	ServerName webmail.<?php echo $parkdomainname; ?>


	DocumentRoot "<?php echo $disablepath; ?>"

	DirectoryIndex <?php echo $indexorder; ?>

<?php
					if ($count !== 0) {
?>

	<IfModule mod_ssl.c>
		SSLEngine On
		SSLCertificateFile <?php echo $certname; ?>.pem
		SSLCertificateKeyFile <?php echo $certname; ?>.key
<?php
						if (file_exists("{$certname}.ca")) {

?>
		SSLCACertificatefile <?php echo $certname; ?>.ca
<?php
						}
?>
	</IfModule>
<?php
					}

					//if (strpos($phptype, '_suphp') !== false) {
?>

	<IfModule suexec.c>
		SuexecUserGroup apache apache
	</IfModule>

	<IfModule mod_suphp.c>
		SuPhp_UserGroup apache apache
	</IfModule>
<?php
					//} elseif (strpos($phptype, '_ruid2') !== false) {
					//if (strpos($phptype, '_ruid2') !== false) {
?>

	<IfModule mod_ruid2.c>
		RMode config
		RUidGid apache apache
		RMinUidGid apache apache
	</IfModule>
<?php
					//} elseif (strpos($phptype, '_itk') !== false) {
?>

	<IfModule itk.c>
		AssignUserId apache apache
	</IfModule>
<?php
					//} elseif (strpos($phptype, 'php-fpm_') !== false) {
?>

	<IfModule mod_fastcgi.c>
		Alias /webmailwebmail.<?php echo $parkdomainname; ?>.<?php echo $count; ?>fake "<?php echo $disablepath; ?>/webmail.<?php echo $parkdomainname; ?>.<?php echo $count; ?>fake"
		#FastCGIExternalServer "<?php echo $disablepath; ?>/webmail.<?php echo $parkdomainname; ?>.<?php echo $count; ?>fake" -host 127.0.0.1:<?php echo $fpmportapache; ?> -idle-timeout 90 -pass-header Authorization
		FastCGIExternalServer "<?php echo $disablepath; ?>/webmail.<?php echo $parkdomainname; ?>.<?php echo $count; ?>fake" -socket /home/php-fpm/sock/apache.sock -idle-timeout 90 -pass-header Authorization
		AddType application/x-httpd-fastphp .php
		Action application/x-httpd-fastphp /webmail.<?php echo $parkdomainname; ?>.<?php echo $count; ?>fake
		<Files "webmail.<?php echo $parkdomainname; ?>.<?php echo $count; ?>fake">
			RewriteCond %{REQUEST_URI} !webmail.<?php echo $parkdomainname; ?>.<?php echo $count; ?>fake
		</Files>
	</IfModule>
<?php
					//} elseif (strpos($phptype, 'fcgid_') !== false) {
?>

	<IfModule mod_fcgid.c>
		<Directory "<?php echo $disablepath; ?>/">
			Options +ExecCGI
			AddHandler fcgid-script .php
			FCGIWrapper /home/kloxo/client/php5.fcgi .php
		</Directory>
	</IfModule>
<?php
					//} elseif (strpos($phptype, 'proxy-fcgi_') !== false) {
?>

	<IfModule mod_proxy_fcgi.c>
		ProxyPass / fcgi://127.0.0.1:<?php echo $fpmportapache; ?>/
		ProxyPassReverse / fcgi://127.0.0.1:<?php echo $fpmportapache; ?>/
	</IfModule>
<?php
					//}
?>

	<Location />
		Allow from all
		# Options +Indexes +FollowSymlinks
		Options -Indexes -FollowSymlinks +SymLinksIfOwnerMatch
	</Location>
<?php
					//if (strpos($phptype, 'fcgid_') === false) {
?>

	<Directory "<?php echo $disablepath; ?>/">
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
					//}
?>

</VirtualHost>

<?php
				} else {
					if ($webmailremote) {
?>

## webmail for parked '<?php echo $parkdomainname; ?>'
<VirtualHost ${ip}:<?php echo $portlist[$count]; ?>>

	SetEnvIf X-Forwarded-Proto https HTTPS=1

	ServerName webmail.<?php echo $parkdomainname; ?>


	Redirect / "<?php echo $protocol; ?><?php echo $webmailremote; ?>"
<?php
						if ($count !== 0) {
?>

	<IfModule mod_ssl.c>
		SSLEngine On
		SSLCertificateFile <?php echo $certname; ?>.pem
		SSLCertificateKeyFile <?php echo $certname; ?>.key
<?php
							if (file_exists("{$certname}.ca")) {

?>
		SSLCACertificatefile <?php echo $certname; ?>.ca
<?php
							}
?>
	</IfModule>
<?php
						}
?>

</VirtualHost>

<?php
					} elseif ($webmailmap) {
?>

## webmail for parked '<?php echo $parkdomainname; ?>'
<VirtualHost ${ip}:<?php echo $portlist[$count]; ?>>

	SetEnvIf X-Forwarded-Proto https HTTPS=1

	ServerName webmail.<?php echo $parkdomainname; ?>


	DocumentRoot "<?php echo $webmaildocroot; ?>"

	DirectoryIndex <?php echo $indexorder; ?>

<?php
						if ($count !== 0) {
?>

	<IfModule mod_ssl.c>
		SSLEngine On
		SSLCertificateFile <?php echo $certname; ?>.pem
		SSLCertificateKeyFile <?php echo $certname; ?>.key
<?php
							if (file_exists("{$certname}.ca")) {

?>
		SSLCACertificatefile <?php echo $certname; ?>.ca
<?php
							}
?>
	</IfModule>
<?php
						}

						//if (strpos($phptype, '_suphp') !== false) {
?>

	<IfModule suexec.c>
		SuexecUserGroup apache apache
	</IfModule>

	<IfModule mod_suphp.c>
		SuPhp_UserGroup apache apache
	</IfModule>
<?php
						//} elseif (strpos($phptype, '_ruid2') !== false) {
						//if (strpos($phptype, '_ruid2') !== false) {
?>

	<IfModule mod_ruid2.c>
		RMode config
		RUidGid apache apache
		RMinUidGid apache apache
	</IfModule>
<?php
						//} elseif (strpos($phptype, '_itk') !== false) {
?>

	<IfModule itk.c>
		AssignUserId apache apache
	</IfModule>
<?php
						//} elseif (strpos($phptype, 'php-fpm_') !== false) {
?>

	<IfModule mod_fastcgi.c>
		Alias /webmail.<?php echo $parkdomainname; ?>.<?php echo $count; ?>fake "<?php echo $webmaildocroot; ?>/webmail.<?php echo $parkdomainname; ?>.<?php echo $count; ?>fake"
		#FastCGIExternalServer "<?php echo $webmaildocroot; ?>/webmail.<?php echo $parkdomainname; ?>.<?php echo $count; ?>fake" -host 127.0.0.1:<?php echo $fpmportapache; ?> -idle-timeout 90 -pass-header Authorization
		FastCGIExternalServer "<?php echo $webmaildocroot; ?>/webmail.<?php echo $parkdomainname; ?>.<?php echo $count; ?>fake" -socket /home/php-fpm/sock/apache.sock -idle-timeout 90 -pass-header Authorization
		AddType application/x-httpd-fastphp .php
		Action application/x-httpd-fastphp /webmail.<?php echo $parkdomainname; ?>.<?php echo $count; ?>fake
		<Files "webmail.<?php echo $parkdomainname; ?>.<?php echo $count; ?>fake">
			RewriteCond %{REQUEST_URI} !webmail.<?php echo $parkdomainname; ?>.<?php echo $count; ?>fake
		</Files>
	</IfModule>
<?php
						//} elseif (strpos($phptype, 'fcgid_') !== false) {
?>

	<IfModule mod_fcgid.c>
		<Directory "<?php echo $webmaildocroot; ?>/">
			Options +ExecCGI
			AddHandler fcgid-script .php
			FCGIWrapper /home/kloxo/client/php5.fcgi .php
		</Directory>
	</IfModule>
<?php
						//} elseif (strpos($phptype, 'proxy-fcgi_') !== false) {
?>

	<IfModule mod_proxy_fcgi.c>
		ProxyPass / fcgi://127.0.0.1:<?php echo $fpmportapache; ?>/
		ProxyPassReverse / fcgi://127.0.0.1:<?php echo $fpmportapache; ?>/
	</IfModule>
<?php
						//}
?>

	<Location />
		Allow from all
		# Options +Indexes +FollowSymlinks
		Options -Indexes -FollowSymlinks +SymLinksIfOwnerMatch
	</Location>
<?php
						//if (strpos($phptype, 'fcgid_') === false) {
?>

	<Directory "<?php echo $webmaildocroot; ?>/">
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
						//}
?>

</VirtualHost>

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
<VirtualHost ${ip}:<?php echo $portlist[$count]; ?>>

	SetEnvIf X-Forwarded-Proto https HTTPS=1

	ServerName webmail.<?php echo $redirdomainname; ?>


	DocumentRoot "<?php echo $disablepath; ?>"

	DirectoryIndex <?php echo $indexorder; ?>

<?php
					if ($count !== 0) {
?>

	<IfModule mod_ssl.c>
		SSLEngine On
		SSLCertificateFile <?php echo $certname; ?>.pem
		SSLCertificateKeyFile <?php echo $certname; ?>.key
<?php
						if (file_exists("{$certname}.ca")) {

?>
		SSLCACertificatefile <?php echo $certname; ?>.ca
<?php
						}
?>
	</IfModule>
<?php
					}

					//if (strpos($phptype, '_suphp') !== false) {
?>

	<IfModule suexec.c>
		SuexecUserGroup apache apache
	</IfModule>

	<IfModule mod_suphp.c>
		SuPhp_UserGroup apache apache
	</IfModule>
<?php
					//} elseif (strpos($phptype, '_ruid2') !== false) {
					//if (strpos($phptype, '_ruid2') !== false) {
?>

	<IfModule mod_ruid2.c>
		RMode config
		RUidGid apache apache
		RMinUidGid apache apache
	</IfModule>
<?php
					//} elseif (strpos($phptype, '_itk') !== false) {
?>

	<IfModule itk.c>
		AssignUserId apache apache
	</IfModule>
<?php
					//} elseif (strpos($phptype, 'php-fpm_') !== false) {
?>

	<IfModule mod_fastcgi.c>
		Alias /webmail.<?php echo $redirdomainname; ?>.<?php echo $count; ?>fake "<?php echo $disablepath; ?>/webmail.<?php echo $redirdomainname; ?>.<?php echo $count; ?>fake"
		#FastCGIExternalServer "<?php echo $disablepath; ?>/webmail.<?php echo $redirdomainname; ?>.<?php echo $count; ?>fake" -host 127.0.0.1:<?php echo $fpmportapache; ?> -idle-timeout 90 -pass-header Authorization
		FastCGIExternalServer "<?php echo $disablepath; ?>/webmail.<?php echo $redirdomainname; ?>.<?php echo $count; ?>fake" -socket /home/php-fpm/sock/apache.sock -idle-timeout 90 -pass-header Authorization
		AddType application/x-httpd-fastphp .php
		Action application/x-httpd-fastphp /webmail.<?php echo $redirdomainname; ?>.<?php echo $count; ?>fake
		<Files "webmail.<?php echo $redirdomainname; ?>.<?php echo $count; ?>fake">
			RewriteCond %{REQUEST_URI} !webmail.<?php echo $redirdomainname; ?>.<?php echo $count; ?>fake
		</Files>
	</IfModule>
<?php
					//} elseif (strpos($phptype, 'fcgid_') !== false) {
?>

	<IfModule mod_fcgid.c>
		<Directory "<?php echo $disablepath; ?>/">
			Options +ExecCGI
			AddHandler fcgid-script .php
			FCGIWrapper /home/kloxo/client/php5.fcgi .php
		</Directory>
	</IfModule>
<?php
					//} elseif (strpos($phptype, 'proxy-fcgi_') !== false) {
?>

	<IfModule mod_proxy_fcgi.c>
		ProxyPass / fcgi://127.0.0.1:<?php echo $fpmportapache; ?>/
		ProxyPassReverse / fcgi://127.0.0.1:<?php echo $fpmportapache; ?>/
	</IfModule>
<?php
					//}
?>

	<Location />
		Allow from all
		# Options +Indexes +FollowSymlinks
		Options -Indexes -FollowSymlinks +SymLinksIfOwnerMatch
	</Location>
<?php
					//if (strpos($phptype, 'fcgid_') === false) {
?>

	<Directory "<?php echo $disablepath; ?>/">
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
					//}
?>

					</VirtualHost>

<?php
				} else {
					if ($webmailremote) {
?>

## webmail for redirect '<?php echo $redirdomainname; ?>'
<VirtualHost ${ip}:<?php echo $portlist[$count]; ?>>

	SetEnvIf X-Forwarded-Proto https HTTPS=1

	ServerName webmail.<?php echo $redirdomainname; ?>


	Redirect / "<?php echo $protocol; ?><?php echo $webmailremote; ?>"
<?php
						if ($count !== 0) {
?>

	<IfModule mod_ssl.c>
		SSLEngine On
		SSLCertificateFile <?php echo $certname; ?>.pem
		SSLCertificateKeyFile <?php echo $certname; ?>.key
<?php
							if (file_exists("{$certname}.ca")) {

?>
		SSLCACertificatefile <?php echo $certname; ?>.ca
<?php
							}
?>
	</IfModule>
<?php
						}
?>

</VirtualHost>

<?php
					} elseif ($webmailmap) {
?>

## webmail for redirect '<?php echo $redirdomainname; ?>'
<VirtualHost ${ip}:<?php echo $portlist[$count]; ?>>

	SetEnvIf X-Forwarded-Proto https HTTPS=1

	ServerName webmail.<?php echo $redirdomainname; ?>


	DocumentRoot "<?php echo $webmaildocroot; ?>"

	DirectoryIndex <?php echo $indexorder; ?>

<?php
						if ($count !== 0) {
?>

	<IfModule mod_ssl.c>
		SSLEngine On
		SSLCertificateFile <?php echo $certname; ?>.pem
		SSLCertificateKeyFile <?php echo $certname; ?>.key
<?php
							if (file_exists("{$certname}.ca")) {

?>
		SSLCACertificatefile <?php echo $certname; ?>.ca
<?php
							}
?>
	</IfModule>
<?php
						}

						//if (strpos($phptype, '_suphp') !== false) {
?>

	<IfModule suexec.c>
		SuexecUserGroup apache apache
	</IfModule>

	<IfModule mod_suphp.c>
		SuPhp_UserGroup apache apache
	</IfModule>
<?php
						//} elseif (strpos($phptype, '_ruid2') !== false) {
						//if (strpos($phptype, '_ruid2') !== false) {
?>

	<IfModule mod_ruid2.c>
		RMode config
		RUidGid apache apache
		RMinUidGid apache apache
	</IfModule>
<?php
						//} elseif (strpos($phptype, '_itk') !== false) {
?>

	<IfModule itk.c>
		AssignUserId apache apache
	</IfModule>
<?php
						//} elseif (strpos($phptype, 'php-fpm_') !== false) {
?>

	<IfModule mod_fastcgi.c>
		Alias /webmail.<?php echo $redirdomainname; ?>.<?php echo $count; ?>fake "<?php echo $webmaildocroot; ?>/webmail.<?php echo $redirdomainname; ?>.<?php echo $count; ?>fake"
		#FastCGIExternalServer "<?php echo $webmaildocroot; ?>/webmail.<?php echo $redirdomainname; ?>.<?php echo $count; ?>fake" -host 127.0.0.1:<?php echo $fpmportapache; ?> -idle-timeout 90 -pass-header Authorization
		FastCGIExternalServer "<?php echo $webmaildocroot; ?>/webmail.<?php echo $redirdomainname; ?>.<?php echo $count; ?>fake" -socket /home/php-fpm/sock/apache.sock -idle-timeout 90 -pass-header Authorization
		AddType application/x-httpd-fastphp .php
		Action application/x-httpd-fastphp /webmail.<?php echo $redirdomainname; ?>.<?php echo $count; ?>fake
		<Files "webmail.<?php echo $redirdomainname; ?>.<?php echo $count; ?>fake">
			RewriteCond %{REQUEST_URI} !webmail.<?php echo $redirdomainname; ?>.<?php echo $count; ?>fake
		</Files>
	</IfModule>
<?php
						//} elseif (strpos($phptype, 'fcgid_') !== false) {
?>

	<IfModule mod_fcgid.c>
		<Directory "<?php echo $webmaildocroot; ?>/">
			Options +ExecCGI
			AddHandler fcgid-script .php
			FCGIWrapper /home/kloxo/client/php5.fcgi .php
		</Directory>
	</IfModule>
<?php
						//} elseif (strpos($phptype, 'proxy-fcgi_') !== false) {
?>

	<IfModule mod_proxy_fcgi.c>
		ProxyPass / fcgi://127.0.0.1:<?php echo $fpmportapache; ?>/
		ProxyPassReverse / fcgi://127.0.0.1:<?php echo $fpmportapache; ?>/
	</IfModule>
<?php
						//}
?>

	<Location />
		Allow from all
		# Options +Indexes +FollowSymlinks
		Options -Indexes -FollowSymlinks +SymLinksIfOwnerMatch
	</Location>
<?php
						//if (strpos($phptype, 'fcgid_') === false) {
?>

	<Directory "<?php echo $webmaildocroot; ?>/">
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
						//}
?>

</VirtualHost>

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
