### begin - web of initial - do not remove/modify this line

<?php

// MR -- disable cgi module
if (file_exists('/etc/httpd/conf.modules.d/01-cgi.conf')) {
	exec("'cp' -f /opt/configs/apache/etc/conf.modules.d/01-cgi.conf /etc/httpd/conf.modules.d/01-cgi.conf");
}

if (!file_exists("/var/run/letsencrypt/.well-known/acme-challenge")) {
	exec("mkdir -p /var/run/letsencrypt/.well-known/acme-challenge");
}

if (!isset($phpselected)) {
	$phpselected = 'php';
}

if (!isset($timeout)) {
	$timeout = '300';
}

$srcpath = "/opt/configs/apache/etc";
$srccpath = "/opt/configs/apache/etc/conf";
$srccdpath = "/opt/configs/apache/etc/conf.d";
$srccmdpath = "/opt/configs/apache/etc/conf.modules.d";
$trgtpath = "/etc";
$trgtcpath = "/etc/httpd/conf";
$trgtcdpath = "/etc/httpd/conf.d";
$trgtcmdpath = "/etc/httpd/conf.modules.d";

$sslpath = "/home/kloxo/ssl";
$kloxopath = "/usr/local/lxlabs/kloxo";

// MR -- fix error 'Directory / is not owned by admin' for suphp
exec("chown root.root /");

// MR -- mod_ruid2 from epel use mod_ruid2.conf
foreach (glob("{$trgtcdpath}/mod_*.conf") as $file)
{
	$newfile = str_replace('.conf', '.nonconf', $file);
	
	if (file_exists($newfile)) {
		unlink($newfile);
	}
	
	rename($file, $newfile);
}

$mpmlist = array('prefork', 'itk', 'event', 'worker');

// @exec("httpd -v|grep 'version:'|grep '/2.4.'", $out);
// @exec("rpm -qa|grep -E '^httpd24-2.4', $out);


if (file_exists("{$kloxopath}/etc/flag/use_apache24.flg")) {
	$use_httpd24 = true;

	exec("'cp' -f {$srccpath}/httpd24.conf /{$trgtcpath}/httpd.conf");

	if (file_exists("{$trgtcmdpath}/00-base.conf")) {
		exec("sed -i 's/^LoadModule deflate_module/#LoadModule deflate_module/' {$trgtcmdpath}/00-base.conf");
	}

	foreach ($mpmlist as $k => $v) {
		if (strpos($phptype, $v) !== false) {
			if (file_exists("{$trgtcmdpath}/00-mpm.conf")) {
				exec("echo 'LoadModule mpm_{$v}_module modules/mod_mpm_{$v}.so' > {$trgtcmdpath}/00-mpm.conf");
				break;
			}
		}
	}

	// MR -- disable cgi module
	if (file_exists("{$trgtcmdpath}/01-cgi.conf")) {
		exec("sed -i 's/^LoadModule cgid_module/#LoadModule cgid_module/' {$trgtcmdpath}/01-cgi.conf");
	}
	
	// MR -- make blank content
	exec("echo '' > /etc/sysconfig/httpd");
} else {
	$use_httpd24 = false;

	exec("'cp' -f {$srccpath}/httpd.conf {$trgtcpath}/httpd.conf");

	// as 'httpd' as default mpm
	exec("echo 'HTTPD=/usr/sbin/httpd' > /etc/sysconfig/httpd");

	foreach ($mpmlist as $k => $v) {
		if (strpos($phptype, $v) !== false) {
			exec("echo 'HTTPD=/usr/sbin/httpd.{$v}' > /etc/sysconfig/httpd");
			break;
		}
	}

	// MR -- disable cgi module
	exec("sed -i 's/^LoadModule cgi_module/#LoadModule cgi_module/' {$trgtcpath}/httpd.conf");
}

if ($use_httpd24) {
	$custom_conf = getLinkCustomfile($srccpath, "httpd24.conf");
	copy($custom_conf, "{$trgtcpath}/httpd.conf");
} else {
	$custom_conf = getLinkCustomfile($srccpath, "httpd.conf");
	copy($custom_conf, "{$trgtcpath}/httpd.conf");
}

$modlist = array("~lxcenter", "ssl", "__version", "perl", "rpaf", "define", "_inactive_");

foreach ($modlist as $k => $v) {
	$custom_conf = getLinkCustomfile($srccdpath, "{$v}.conf");

	if (strpos($custom_conf, "/~lxcenter.conf") !== false) {
		// no action because handle by ~lxcenter.conf.tpl
	} else {
		copy($custom_conf, "{$trgtcdpath}/{$v}.conf");
	}
}

// MR -- because 'pure' mod_php disabled (security reason)
$custom_conf = getLinkCustomfile($srccdpath, "_inactive_.conf");
copy($custom_conf, "{$trgtcdpath}/php.conf");

$typelist = array('ruid2', 'suphp', 'fcgid', 'fastcgi', 'proxy_fcgi');

foreach ($typelist as $k => $v) {
	if ($v === 'fastcgi') {
		$w = 'php-fpm';
	} else {
		$w = $v;
	}

	if (strpos($phptype, "{$w}") !== false) {
		$custom_conf = getLinkCustomfile($srccdpath, "{$v}.conf");
		copy($custom_conf, "{$trgtcdpath}/{$v}.conf");
	} else {
		if ($v === 'proxy_fcgi') {
			$custom_conf = getLinkCustomfile($srccmdpath, "_inactive_.conf");
			copy($custom_conf, "{$trgtcmdpath}/00-proxy.conf");

			if (file_exists("{$trgtcdpath}/{$v}.conf")) {
				unlink("{$trgtcdpath}/{$v}.conf");
				unlink("{$trgtcdpath}/{$v}.nonconf");
			}
		} else {
			$custom_conf = getLinkCustomfile($srccdpath, "_inactive_.conf");
			copy($custom_conf, "{$trgtcdpath}/{$v}.conf");
		}
	}
}

$custom_conf = getLinkCustomfile($srcpath, "suphp.conf");
copy($custom_conf, "{$trgtpath}/suphp.conf");

foreach ($certnamelist as $ip => $certname) {
	$certnamelist[$ip] = "{$sslpath}/{$certname}";
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

$portnip_conf = getLinkCustomfile($globalspath, "portnip.conf");

$defaultdocroot = "/home/kloxo/httpd/default";

if ($indexorder) {
	$indexorder = implode(' ', $indexorder);
}

$acmechallenge_conf = getLinkCustomfile($globalspath, "acme-challenge.conf");

$ssl_base_conf = getLinkCustomfile($globalspath, "ssl_base.conf");

$remoteip_conf = getLinkCustomfile($globalspath, "remoteip.conf");

// MR -- for future purpose, apache user have uid 50000
// $userinfoapache = posix_getpwnam('apache');
// $fpmportapache = (50000 + $userinfoapache['uid']);
$fpmportapache = 50000;

foreach ($certnamelist as $ip => $certname) {
?>

<IfVersion < 2.4>
	Define global::port <?=$ports[0]; ?>

	Define global::portssl <?=$ports[1];?>

	Define global::ip <?=$ip;?>


	Define port ${global::port}
	Define portssl ${global::portssl}
	Define ip ${global::ip}
</IfVersion>

<IfVersion >= 2.4>
	Include "<?=$portnip_conf;?>"
</IfVersion>

Listen ${ip}:${port}
Listen ${ip}:${portssl}

<IfVersion < 2.4>
	NameVirtualHost ${ip}:${port}
	NameVirtualHost ${ip}:${portssl}
</IfVersion>
<?php
}

if ($reverseproxy) {
?>

Include "<?=$remoteip_conf;?>"
<?php
}
?>

## MR -- ruid2 not work for userdir!
<IfModule mod_userdir.c>
	UserDir enabled
	UserDir /home/*/public_html
<?php
	foreach ($userlist as &$user) {
		$userinfo = posix_getpwnam($user);

		if (!$userinfo) {
			continue;
		}
?>
	<Location "/~<?=$user;?>">
		<IfModule mod_suphp.c>
			SuPhp_UserGroup <?=$user;?> <?=$user;?>

		</IfModule>
	</Location>
<?php
}
?>
</IfModule>

<?php
foreach ($certnamelist as $ip => $certname) {
	$count = 0;

	foreach ($ports as &$port) {
?>

### 'default' config
<VirtualHost ${ip}:<?=$portlist[$count];?> >

	<IfModule pagespeed_module>
		ModPageSpeed unplugged
	</IfModule>

	SetEnvIf X-Forwarded-Proto https HTTPS=1

	ServerName default

	ServerAlias default.*

	DocumentRoot "<?=$defaultdocroot;?>"

	Include "<?=$acmechallenge_conf;?>"

	DirectoryIndex <?=$indexorder;?>

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
			Alias /default.<?=$count;?>fake "<?=$defaultdocroot;?>/default.<?=$count;?>fake"
			#FastCGIExternalServer "<?=$defaultdocroot;?>/default.<?=$count;?>fake" \
			#	-host 127.0.0.1:<?=$fpmportapache;?> -idle-timeout <?=$timeout;?> -pass-header Authorization
			FastCGIExternalServer "<?=$defaultdocroot;?>/default.<?=$count;?>fake" \
				-socket /opt/configs/php-fpm/sock/php-apache.sock \
				-idle-timeout <?=$timeout;?> -pass-header Authorization
			<FilesMatch \.php$>
				SetHandler application/x-httpd-fastphp
			</FilesMatch>
			Action application/x-httpd-fastphp /default.<?=$count;?>fake
			<Files "default.<?=$count;?>fake">
				RewriteCond %{REQUEST_URI} !default.<?=$count;?>fake
			</Files>
		</IfModule>

		<IfModule !mod_ruid2.c>
			<IfModule !mod_itk.c>
				<IfModule !mod_fastcgi.c>
					<IfModule mod_fcgid.c>
						<Directory "<?=$defaultdocroot;?>/">
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
				SetHandler "proxy:unix:/opt/configs/php-fpm/sock/php-apache.sock|fcgi://localhost"
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
		# Options -Indexes -FollowSymlinks +SymLinksIfOwnerMatch
		## MR -- need symlink because make possible access to http://ip/domainname
		Options -Indexes
	</Location>

	<Directory "<?=$defaultdocroot;?>/">
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
