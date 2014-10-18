### begin - web of initial - do not remove/modify this line

<?php

$srcconfpath = "/opt/configs/apache/etc/conf";
$srcconfdpath = "/opt/configs/apache/etc/conf.d";
$trgtconfpath = "/etc/httpd/conf";
$trgtconfdpath = "/etc/httpd/conf.d";

if (file_exists("{$srcconfpath}/custom.httpd.conf")) {
	copy("{$srcconfpath}/custom.httpd.conf", "{$trgtconfpath}/httpd.conf");
} else {
	copy("{$srcconfpath}/httpd.conf", "{$trgtconfpath}/httpd.conf");
}

$modlist = array("~lxcenter", "ssl", "__version", "perl", "rpaf", "define", "_inactive_");

foreach ($modlist as $k => $v) {
	if (file_exists("{$srcconfdpath}/custom.{$v}.conf")) {
		copy("{$srcconfdpath}/custom.{$v}.conf", "{$trgtconfdpath}/{$v}.conf");
	} else {
		if ($v !== '~lxcenter') {
			copy("{$srcconfdpath}/{$v}.conf", "{$trgtconfdpath}/{$v}.conf");
		}
	}
}

// MR -- because 'pure' mod_php disabled (security reason)
if (file_exists("{$srcconfdpath}/custom._inactive_.conf")) {
	copy("{$srcconfdpath}/custom._inactive_.conf", "{$trgtconfdpath}/php.conf");
} else {
	copy("{$srcconfdpath}/_inactive_.conf", "{$trgtconfdpath}/php.conf");
}

$typelist = array('ruid2', 'suphp', 'fcgid', 'fastcgi', 'proxy_fcgi');

foreach ($typelist as $k => $v) {
	if ($v === 'fastcgi') {
		$w = 'php-fpm';
	} else {
		$w = $v;
	}

	if (strpos($phptype, "{$w}") !== false) {
		if (file_exists("{$srcconfdpath}/custom.{$v}.conf")) {
			copy("{$srcconfdpath}/custom.{$v}.conf", "{$trgtconfdpath}/{$v}.conf");
		} else {
			copy("{$srcconfdpath}/{$v}.conf", "{$trgtconfdpath}/{$v}.conf");
		}
	} else {
		if (file_exists("{$srcconfdpath}/custom._inactive_.conf")) {
			copy("{$srcconfdpath}/custom._inactive_.conf", "{$trgtconfdpath}/{$v}.conf");
		} else {
			copy("{$srcconfdpath}/_inactive_.conf", "{$trgtconfdpath}/{$v}.conf");
		}
	}
}

$mpmlist = array('event', 'worker', 'itk');

// as 'httpd' as default mpm
exec("echo 'HTTPD=/usr/sbin/httpd' > /etc/sysconfig/httpd");

foreach ($mpmlist as $k => $v) {
	if (strpos($phptype, "{$v}") !== false) {
		exec("echo 'HTTPD=/usr/sbin/httpd.{$v}' > /etc/sysconfig/httpd");
	}
}

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

foreach ($certnamelist as $ip => $certname) {
	$certnamelist[$ip] = "/home/kloxo/httpd/ssl/{$certname}";
}

if ($reverseproxy) {
	$tmp_ip = '127.0.0.1';

	foreach ($certnamelist as $ip => $certname) {
		$tmp_certname = $certname;
		break;
	}

	$certnamelist = null;

	$certnamelist[$tmp_ip] = $tmp_certname;
}

$defaultdocroot = "/home/kloxo/httpd/default";
$cpdocroot = "/home/kloxo/httpd/cp";

if ($indexorder) {
	$indexorder = implode(' ', $indexorder);
}

// MR -- for future purpose, apache user have uid 50000
// $userinfoapache = posix_getpwnam('apache');
// $fpmportapache = (50000 + $userinfoapache['uid']);
$fpmportapache = 50000;

foreach ($certnamelist as $ip => $certname) {
?>

Define global::port <?php echo $ports[0]; ?>

Define global::portssl <?php echo $ports[1]; ?>

Define global::ip <?php echo $ip; ?>


Define port ${global::port}
Define portssl ${global::portssl}
Define ip ${global::ip}

Listen ${ip}:${port}
Listen ${ip}:${portssl}

<IfVersion < 2.4>
	NameVirtualHost ${ip}:${port}
	NameVirtualHost ${ip}:${portssl}
</IfVersion>
<?php
}
?>

<Ifmodule mod_userdir.c>
	UserDir enabled
	UserDir /home/*/public_html
<?php
foreach ($userlist as &$user) {
	$userinfo = posix_getpwnam($user);

	if (!$userinfo) {
		continue;
	}
?>

	<Location "/~<?php echo $user; ?>">
		<IfModule mod_suphp.c>
			SuPhp_UserGroup <?php echo $user; ?> <?php echo $user; ?>

		</IfModule>
	</Location>
<?php
}
?>
</Ifmodule>

<?php
foreach ($certnamelist as $ip => $certname) {
	$count = 0;

	foreach ($ports as &$port) {
?>

### 'default' config
<VirtualHost ${ip}:<?php echo $portlist[$count]; ?>>

	SetEnvIf X-Forwarded-Proto https HTTPS=1

	ServerName default

	ServerAlias default.*

	DocumentRoot "<?php echo $defaultdocroot; ?>"

	DirectoryIndex <?php echo $indexorder; ?>

<?php
		if ($count !== 0) {
?>

	<IfModule mod_ssl.c>
		SSLEngine On
		SSLProtocol ALL -SSLv2
		SSLHonorCipherOrder On
		SSLCipherSuite ECDH+AESGCM:DH+AESGCM:ECDH+AES256:DH+AES256:ECDH+AES128:DH+AES:ECDH+3DES:DH+3DES:RSA+AESGCM:RSA+AES:RSA+3DES:!aNULL:!MD5:!DSS
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
		Alias /default.<?php echo $count; ?>fake "<?php echo $defaultdocroot; ?>/default.<?php echo $count; ?>fake"
		#FastCGIExternalServer "<?php echo $defaultdocroot; ?>/default.<?php echo $count; ?>fake" -host 127.0.0.1:<?php echo $fpmportapache; ?> -idle-timeout 90 -pass-header Authorization
		FastCGIExternalServer "<?php echo $defaultdocroot; ?>/default.<?php echo $count; ?>fake" -socket /opt/configs/php-fpm/sock/apache.sock -idle-timeout 90 -pass-header Authorization
		AddType application/x-httpd-fastphp .php
		Action application/x-httpd-fastphp /default.<?php echo $count; ?>fake
		<Files "default.<?php echo $count; ?>fake">
			RewriteCond %{REQUEST_URI} !default.<?php echo $count; ?>fake
		</Files>
	</IfModule>
<?php
		//} elseif (strpos($phptype, 'fcgid_') !== false) {
?>

	<IfModule mod_fcgid.c>
		<Directory "<?php echo $defaultdocroot; ?>/">
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

	<Directory "<?php echo $defaultdocroot; ?>/">
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


### cp config
<VirtualHost ${ip}:<?php echo $portlist[$count]; ?>>

	SetEnvIf X-Forwarded-Proto https HTTPS=1

	ServerName cp

	ServerAlias cp.*

	DocumentRoot "<?php echo $cpdocroot; ?>"

	DirectoryIndex <?php echo $indexorder; ?>

<?php
		if ($count !== 0) {
?>

	<IfModule mod_ssl.c>
		SSLEngine on
		SSLProtocol ALL -SSLv2
		SSLHonorCipherOrder On
		SSLCipherSuite ECDH+AESGCM:DH+AESGCM:ECDH+AES256:DH+AES256:ECDH+AES128:DH+AES:ECDH+3DES:DH+3DES:RSA+AESGCM:RSA+AES:RSA+3DES:!aNULL:!MD5:!DSS
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
		Alias /cp.<?php echo $count; ?>fake "<?php echo $cpdocroot; ?>/cp.<?php echo $count; ?>fake"
		#FastCGIExternalServer "<?php echo $cpdocroot; ?>/cp.<?php echo $count; ?>fake" -host 127.0.0.1:<?php echo $fpmportapache; ?> -idle-timeout 90 -pass-header Authorization
		FastCGIExternalServer "<?php echo $cpdocroot; ?>/cp.<?php echo $count; ?>fake" -socket /opt/configs/php-fpm/sock/apache.sock -idle-timeout 90 -pass-header Authorization
		AddType application/x-httpd-fastphp .php
		Action application/x-httpd-fastphp /cp.<?php echo $count; ?>fake
		<Files "cp.<?php echo $count; ?>fake">
			RewriteCond %{REQUEST_URI} !cp.<?php echo $count; ?>fake
		</Files>
	</IfModule>
<?php
	   //} elseif (strpos($phptype, 'fcgid_') !== false) {
?>

	<IfModule mod_fcgid.c>
		<Directory "<?php echo $defaultdocroot; ?>/">
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

	<Directory "<?php echo $defaultdocroot; ?>/">
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
		$count++;
	}
}
?>

### end - web of initial - do not remove/modify this line
