<?php

class serverweb__ extends lxDriverClass
{
	function __construct()
	{
	}

	function dbactionUpdate($subaction)
	{
		global $gbl, $sgbl, $login, $ghtml;

		$t = (isset($this->main->php_type)) ? $this->main->php_type : null;

		$a = (isset($this->main->apache_optimize)) ? $this->main->apache_optimize : null;
		$m = (isset($this->main->mysql_convert)) ? $this->main->mysql_convert : null;
		$f = (isset($this->main->fix_chownchmod)) ? $this->main->fix_chownchmod : null;


		$ullkfapath  = '/usr/local/lxlabs/kloxo/file/apache';
		$ullkfpfpath = '/usr/local/lxlabs/kloxo/file/php-fpm';

		$ehcpath  = '/etc/httpd/conf';
		$ehcdpath = '/etc/httpd/conf.d';
		$ehckpath = '/etc/httpd/conf/kloxo';

		$hhcpath = '/home/httpd/conf';

		$hapath  = '/home/apache';
		$hacpath = '/home/apache/conf';
		$haepath = '/home/apache/etc';
		$haecpath = '/home/apache/etc/conf';
		$haecdpath = '/home/apache/etc/conf.d';

		$ullkbfaophp = '/usr/local/lxlabs/kloxo/bin/fix/apache-optimize.php';
		$ullkbffcphp = '/usr/local/lxlabs/kloxo/bin/fix/fix-chownchmod.php';
		$ullkbfmcphp = '/usr/local/lxlabs/kloxo/bin/fix/mysql-convert.php';
		$ullkbffwphp = '/usr/local/lxlabs/kloxo/bin/fix/fixweb.php';

		if (isWebProxyOrApache()) {
			//-- old structure
			lxfile_rm_rec($ehckpath);
			lxfile_rm_rec($hhcpath);
			lxfile_rm_rec($hacpath."/exclusive");
			lxfile_rm_rec($hacpath."/redirects");
			lxfile_rm_rec($hacpath."/webmails");
			lxfile_rm_rec($hacpath."/wildcards");

			//-- new structure	
			lxfile_mkdir($hacpath);
			lxfile_mkdir($hacpath."/defaults");
			lxfile_mkdir($hacpath."/domains");
			lxfile_mkdir($hacpath."/globals");

			//--- some vps include /etc/httpd/conf.d/swtune.conf
			lxshell_return("rm", "-f", $ehcdpath."/swtune.conf");

		//	lxfile_cp_content($ullkfapath, $hapath);
			exec("yes|cp -rf {$ullkfapath} /home");

			if (!lfile_exists("{$ehcdpath}/~lxcenter.conf")) {
				lxfile_cp(getLinkCustomfile($haecdpath, "~lxcenter.conf"), $ehcdpath."/~lxcenter.conf");
				lxfile_cp(getLinkCustomfile($haecpath, "httpd.conf"), $ehcpath."/httpd.conf");
			}

			sleep(10);

			//--- don't use '=== true' but '!== false'
			if (stripos($t, 'mod_php') !== false) {
				lxfile_mv($ehcdpath."/php.nonconf", $ehcdpath."/php.conf");
				lxfile_mv($ehcdpath."/fastcgi.conf", $ehcdpath."/fastgi.nonconf");
			//	lxfile_mv($ehcdpath."/fcgid.conf", $ehcdpath."/fcgid.nonconf");
				lxfile_mv($ehcdpath."/ruid2.conf", $ehcdpath."/ruid2.nonconf");
				lxfile_mv($ehcdpath."/suphp.conf", $ehcdpath."/suphp.nonconf");
				lxfile_mv($ehcdpath."/proxy_fcgi.conf", $ehcdpath."/proxy_fcgi.nonconf");

				// use > that equal to lxfile_rm + echo >>
				exec("echo 'HTTPD=/usr/sbin/httpd' >/etc/sysconfig/httpd");

				if ($t === 'mod_php') {
					// nothing
				} elseif ($t === 'mod_php_ruid2') {
					lxshell_return("yum", "-y", "install", "mod_ruid2");
					lxshell_return("yum", "-y", "update", "mod_ruid2");
					lxfile_mv($ehcdpath."/ruid2.nonconf", $ehcdpath."/ruid2.conf");
					lxfile_cp($haecdpath."/ruid2.conf", $ehcdpath."/ruid2.conf");
				} elseif ($t === 'mod_php_itk') {
					exec("echo 'HTTPD=/usr/sbin/httpd.itk' >/etc/sysconfig/httpd");
				}

			} elseif (stripos($t, 'suphp') !== false) {
				lxshell_return("yum", "-y", "install", "mod_suphp");
				lxshell_return("yum", "-y", "update", "mod_suphp");

				$ver = getPhpVersion();

				if (version_compare($ver, "5.2.17-1", ">")) {
					if (!file_exists('/usr/bin/php_pure')) {
						exec('rpm -e --nodeps php');
						exec('rpm -e --nodeps php-cli');
						exec('rpm -e --nodeps php-common');
						exec('rpm -e --nodeps php-fpm');
						exec('yum install php-5.2.17-1 -y');
						lxfile_cp('/usr/bin/php', '/usr/bin/php_pure');
						lxfile_cp('/usr/bin/php-cgi', '/usr/bin/php-cgi_pure');
						exec('yum update php -y');
						exec('yum install php-fpm -y');
					}

					if (version_compare($ver, "5.3.2", ">")) {
						lxfile_cp($haepath."/suphp.conf", "/etc/suphp.conf");
					} else {
						lxfile_cp($haepath."/suphp_pure.conf", "/etc/suphp.conf");
					}
				} else {
					lxfile_cp($haepath."/suphp.conf", "/etc/suphp.conf");
				}

				lxfile_mv($ehcdpath."/php.conf", $ehcdpath."/php.nonconf");
				lxfile_mv($ehcdpath."/fastcgi.conf", $ehcdpath."/fastgi.nonconf");
				lxfile_mv($ehcdpath."/fcgid.conf", $ehcdpath."/fcgid.nonconf");
				lxfile_mv($ehcdpath."/ruid2.conf", $ehcdpath."/ruid2.nonconf");
				lxfile_mv($ehcdpath."/suphp.nonconf", $ehcdpath."/suphp.conf");
				lxfile_mv($ehcdpath."/proxy_fcgi.conf", $ehcdpath."/proxy_fcgi.nonconf");

				lxfile_cp(getLinkCustomfile($haecdpath, "suphp.conf"), $ehcdpath."/suphp.conf");


			} elseif (stripos($t, 'php-fpm') !== false) {

				$ver = getRpmVersion('httpd');

				if (version_compare($ver, "2.4.0", ">=")) {
					lxfile_cp(getLinkCustomfile($haecdpath, "proxy_fcgi.conf"), $ehcdpath."/proxy_fcgi.conf");
					lxfile_rm($ehcdpath."/proxy_fcgi.nonconf");
				} else {
					$phpvariant = getPhpVariant();

					lxshell_return("yum", "-y", "install", "mod_fastcgi");
					lxshell_return("yum", "-y", "update", "mod_fastcgi");
					lxshell_return("yum", "-y", "install", "{$phpvariant}-fpm");
					lxshell_return("yum", "-y", "update", "{$phpvariant}-fpm");
					lxfile_cp(getLinkCustomfile($haecdpath, "fastcgi.conf"), $ehcdpath."/fastcgi.conf");
					lxfile_rm($ehcdpath."/fastcgi.nonconf");
				}

				lxfile_mv($ehcdpath."/php.conf", $ehcdpath."/php.nonconf");
				lxfile_mv($ehcdpath."/fcgid.conf", $ehcdpath."/fcgid.nonconf");
				lxfile_mv($ehcdpath."/ruid2.conf", $ehcdpath."/ruid2.nonconf");
				lxfile_mv($ehcdpath."/suphp.conf", $ehcdpath."/suphp.nonconf");
			}

			lxfile_rm($ehcdpath."/fcgid.nonconf");
			lxfile_rm($ehcdpath."/fcgid.conf");

			if (stripos($t, '_worker') !== false) {
				exec("echo 'HTTPD=/usr/sbin/httpd.worker' >/etc/sysconfig/httpd");
			} elseif (stripos($t, '_event') !== false) {
					exec("echo 'HTTPD=/usr/sbin/httpd.event' >/etc/sysconfig/httpd");
			} else {
				exec("echo 'HTTPD=/usr/sbin/httpd' >/etc/sysconfig/httpd");
			}

			$ret = lxshell_return("service", "httpd", "restart");

			if ($ret) {
				throw new lxexception('httpd_restart_failed', 'parent');
			}
			
			lxshell_return("lxphp.exe", $ullkbffwphp, "--target=defaults", "--nolog");
		}

		$nolog = 'yes';

		if ($a === 'optimize') {
			lxshell_return("lxphp.exe", $ullkbfaophp, "--select=optimize", $nolog);
		}

		if ($f === 'fix-ownership') {
			lxshell_return("lxphp.exe", $ullkbffcphp, "--select=chown", $nolog);
		} elseif ($f === 'fix-permissions') {
			lxshell_return("lxphp.exe", $ullkbffcphp, "--select=chmod", $nolog);
		} elseif ($f === 'fix-ALL') {
			lxshell_return("lxphp.exe", $ullkbffcphp, "--select=all", $nolog);
		}

		if ($m === 'to-myisam') {
			lxshell_return("lxphp.exe", $ullkbfmcphp, "--engine=myisam", $nolog);
		} elseif ($m === 'to-innodb') {
			lxshell_return("lxphp.exe", $ullkbfmcphp, "--engine=innodb", $nolog);
		}

	}
}
