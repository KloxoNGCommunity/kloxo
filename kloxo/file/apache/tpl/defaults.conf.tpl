### begin - web of initial - do not remove/modify this line

<?php

$srcconfpath = "/opt/configs/apache/etc/conf";
$srcconfdpath = "/opt/configs/apache/etc/conf.d";
$trgtconfpath = "/etc/httpd/conf";
$trgtconfdpath = "/etc/httpd/conf.d";

// MR -- mod_ruid2 from epel use mod_ruid2.conf
foreach (glob("{$trgtconfdpath}/mod_*.conf") as $file)
{
	$newfile = str_replace('.conf', '.nonconf', $file);
	
	if (file_exists($newfile)) {
		unlink($newfile);
	}
	
	rename($file, $newfile);
}

exec("httpd -v|grep 'version:'|grep '/2.4.'", $out);

if ($out[0] !== null) {
	$httptype="httpd24";
} else {
	$httptype="httpd";
}

if (file_exists("{$srcconfpath}/custom.{$httptype}.conf")) {
	copy("{$srcconfpath}/custom.{$httptype}.conf", "{$trgtconfpath}/httpd.conf");
} else {
	copy("{$srcconfpath}/{$httptype}.conf", "{$trgtconfpath}/httpd.conf");
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
} else {
	$tmp_ip = '*';
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

$globalspath = "/opt/configs/apache/conf/globals";

$portnip = "Define port {$ports[0]}\nDefine portssl {$ports[1]}\nDefine ip {$tmp_ip}\n";

file_put_contents("{$globalspath}/portnip.conf", $portnip);


$defaultdocroot = "/home/kloxo/httpd/default";

if ($indexorder) {
	$indexorder = implode(' ', $indexorder);
}

// MR -- for future purpose, apache user have uid 50000
// $userinfoapache = posix_getpwnam('apache');
// $fpmportapache = (50000 + $userinfoapache['uid']);
$fpmportapache = 50000;

foreach ($certnamelist as $ip => $certname) {
?>

<IfVersion < 2.4>
	Define global::port <?php echo $ports[0]; ?>

	Define global::portssl <?php echo $ports[1]; ?>

	Define global::ip <?php echo $ip; ?>


	Define port ${global::port}
	Define portssl ${global::portssl}
	Define ip ${global::ip}
</IfVersion>

<IfVersion >= 2.4>
	Include <?php echo $globalspath; ?>/portnip.conf
</IfVersion>

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
<VirtualHost ${ip}:<?php echo $portlist[$count]; ?> >

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
		SSLProtocol ALL -SSLv2 -SSLv3
		SSLHonorCipherOrder On
		#SSLCipherSuite ECDH+AESGCM:DH+AESGCM:ECDH+AES256:DH+AES256:ECDH+AES128:DH+AES:ECDH+3DES:DH+3DES:RSA+AESGCM:RSA+AES:RSA+3DES:!aNULL:!MD5:!DSS
		SSLCipherSuite "EECDH+ECDSA+AESGCM EECDH+aRSA+AESGCM EECDH+ECDSA+SHA384 EECDH+ECDSA+SHA256 EECDH+aRSA+SHA384 EECDH+aRSA+SHA256 EECDH+aRSA+RC4 EECDH EDH+aRSA RC4 !aNULL 
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

	<IfModule suexec.c>
		SuexecUserGroup apache apache
	</IfModule>

	<IfModule mod_suphp.c>
		SuPhp_UserGroup apache apache
	</IfModule>

	<IfModule mod_ruid2.c>
		RMode config
		RUidGid apache apache
		RMinUidGid apache apache
	</IfModule>

	<IfModule itk.c>
		AssignUserId apache apache
	</IfModule>

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

	<IfModule mod_fcgid.c>
		<Directory "<?php echo $defaultdocroot; ?>/">
			Options +ExecCGI
			<FilesMatch \.php$>
				SetHandler fcgid-script
			</FilesMatch>
			FCGIWrapper /home/kloxo/client/php5.fcgi .php
		</Directory>
	</IfModule>

	<IfModule mod_proxy_fcgi.c>
		<FilesMatch \.php$>
			SetHandler "proxy:unix:/opt/configs/php-fpm/sock/apache.sock|fcgi://127.0.0.1"
		</FilesMatch>
	</IfModule>

	<Location "/">
		Allow from all
		# Options +Indexes +FollowSymlinks
		Options -Indexes -FollowSymlinks +SymLinksIfOwnerMatch
	</Location>

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

</VirtualHost>

<?php
		$count++;
	}
}
?>

### end - web of initial - do not remove/modify this line
