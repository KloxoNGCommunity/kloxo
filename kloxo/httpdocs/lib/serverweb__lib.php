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

		$kfapath = '/usr/local/lxlabs/kloxo/file/apache';
		$kfppath = '/usr/local/lxlabs/kloxo/file/php-fpm';
		$hcpath = '/etc/httpd/conf';
		$hcdpath = '/etc/httpd/conf.d';
		$aophp = '/usr/local/lxlabs/kloxo/bin/fix/apache-optimize.php';
		$hacpath = '/home/apache/conf';
		$fcphp = '/usr/local/lxlabs/kloxo/bin/fix/fix-chownchmod.php';
		$mcphp = '/usr/local/lxlabs/kloxo/bin/fix/mysql-convert.php';


		// MR -- because call slave_get_driver('web') not work
		// identify apache/proxy with existing $this->main->php_type

	//	if ($t) {
		if (isWebProxyOrApache()) {
			//-- old structure
			lxfile_rm_rec("/etc/httpd/conf/kloxo");
			lxfile_rm_rec("/home/httpd/conf");
			lxfile_rm_rec("{$hacpath}/exclusive");
			lxfile_rm_rec("{$hacpath}/redirects");
			lxfile_rm_rec("{$hacpath}/webmails");
			lxfile_rm_rec("{$hacpath}/wildcards");

			//-- new structure	
			lxfile_mkdir("{$hacpath}");
			lxfile_mkdir("{$hacpath}/defaults");
			lxfile_mkdir("{$hacpath}/domains");
			lxfile_mkdir("{$hacpath}/globals");

			//--- some vps include /etc/httpd/conf.d/swtune.conf
			lxshell_return("rm", "-f", "/etc/httpd/conf.d/swtune.conf");

			if (!lfile_exists("{$hcdpath}/~lxcenter.conf")) {
				lxfile_cp("{$kfapath}/~lxcenter.conf", "{$hcdpath}/~lxcenter.conf");
				lxfile_cp("{$kfapath}/custom.~lxcenter.conf", "{$hcdpath}/~lxcenter.conf");

				lxfile_cp("{$kfapath}/httpd.conf", "{$hcpath}/httpd.conf");
				lxfile_cp("{$kfapath}/custom.httpd.conf", "{$hcpath}/httpd.conf");
			}

			sleep(10);

			//--- don't use '=== true' but '!== false'
			if (stripos($t, 'mod_php') !== false) {
				lxfile_mv("{$hcdpath}/php.nonconf", "{$hcdpath}/php.conf");
				lxfile_mv("{$hcdpath}/fastcgi.conf", "{$hcdpath}/fastgi.nonconf");
			//	lxfile_mv("{$hcdpath}/fcgid.conf", "{$hcdpath}/fcgid.nonconf");
				lxfile_mv("{$hcdpath}/ruid2.conf", "{$hcdpath}/ruid2.nonconf");
				lxfile_mv("{$hcdpath}/suphp.conf", "{$hcdpath}/suphp.nonconf");
				lxfile_mv("{$hcdpath}/proxy_fcgi.conf", "{$hcdpath}/proxy_fcgi.nonconf");

				// use > that equal to lxfile_rm + echo >>
				exec("echo 'HTTPD=/usr/sbin/httpd' >/etc/sysconfig/httpd");

				if ($t === 'mod_php') {
					// nothing
				} elseif ($t === 'mod_php_ruid2') {
					lxshell_return("yum", "-y", "install", "mod_ruid2");
					lxshell_return("yum", "-y", "update", "mod_ruid2");
					lxfile_mv("{$hcdpath}/ruid2.nonconf", "{$hcdpath}/ruid2.conf");
					lxfile_cp("{$kfapath}/ruid2.conf", "{$hcdpath}/ruid2.conf");
				} elseif ($t === 'mod_php_itk') {
					exec("echo 'HTTPD=/usr/sbin/httpd.itk' >/etc/sysconfig/httpd");
				}

			} elseif (stripos($t, 'suphp') !== false) {
				lxshell_return("yum", "-y", "install", "mod_suphp");
				lxshell_return("yum", "-y", "update", "mod_suphp");

				exec("rpm -q php", $out, $ret);

				$ver = str_replace("php-", "", $out[0]);

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
					lxfile_cp("{$kfapath}/etc_suphp_pure.conf", "/etc/suphp.conf");
				} else {
					lxfile_cp("{$kfapath}/etc_suphp.conf", "/etc/suphp.conf");
				}

				lxfile_mv("{$hcdpath}/php.conf", "{$hcdpath}/php.nonconf");
				lxfile_mv("{$hcdpath}/fastcgi.conf", "{$hcdpath}/fastgi.nonconf");
			//	lxfile_mv("{$hcdpath}/fcgid.conf", "{$hcdpath}/fcgid.nonconf");
				lxfile_mv("{$hcdpath}/ruid2.conf", "{$hcdpath}/ruid2.nonconf");
				lxfile_mv("{$hcdpath}/suphp.nonconf", "{$hcdpath}/suphp.conf");
				lxfile_mv("{$hcdpath}/proxy_fcgi.conf", "{$hcdpath}/proxy_fcgi.nonconf");

				lxfile_cp("{$kfapath}/suphp.conf", "{$hcdpath}/suphp.conf");

			} elseif (stripos($t, 'php-fpm') !== false) {
				exec("rpm -q httpd", $out, $ret);

				$ver = str_replace("httpd-", "", $out[0]);

				if (version_compare($ver, "2.4.1", "<")) {
					lxshell_return("yum", "-y", "install", "mod_fastcgi", "php-fpm");
					lxshell_return("yum", "-y", "update", "mod_fastcgi", "php-fpm");
					lxfile_cp("{$kfapath}/fastcgi.conf", "{$hcdpath}/fastcgi.conf");
					lxfile_rm("{$hcdpath}/fastcgi.nonconf");
					lxfile_cp("{$kfapath}/php.fcgi", "{$hacpath}/globals/php.fcgi");
				} else {
					lxfile_cp("{$kfapath}/proxy_fcgi.conf", "{$hcdpath}/proxy_fcgi.conf");
					lxfile_rm("{$hcdpath}/proxy_fcgi.nonconf");
				}

				lxfile_mv("{$hcdpath}/php.conf", "{$hcdpath}/php.nonconf");
			//	lxfile_mv("{$hcdpath}/fcgid.conf", "{$hcdpath}/fcgid.nonconf");
				lxfile_mv("{$hcdpath}/ruid2.conf", "{$hcdpath}/ruid2.nonconf");
				lxfile_mv("{$hcdpath}/suphp.conf", "{$hcdpath}/suphp.nonconf");

				if (!file_exists('/etc/php-fpm.conf')) {
					lxfile_cp("{$kfppath}/php-fpm.conf", "/etc/php-fpm.conf");
				}
			}

			lxfile_rm("{$hcdpath}/fcgid.nonconf");
			lxfile_rm("{$hcdpath}/fcgid.conf");

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
		}

		$nolog = 'yes';

		if ($a === 'optimize') {
			lxshell_return("lxphp.exe", $aophp, "--select=optimize", $nolog);
		}

		if ($f === 'fix-ownership') {
			lxshell_return("lxphp.exe", $fcphp, "--select=chown", $nolog);
		} elseif ($f === 'fix-permissions') {
			lxshell_return("lxphp.exe", $fcphp, "--select=chmod", $nolog);
		} elseif ($f === 'fix-ALL') {
			lxshell_return("lxphp.exe", $fcphp, "--select=all", $nolog);
		}

		if ($m === 'to-myisam') {
			lxshell_return("lxphp.exe", $mcphp, "--engine=myisam", $nolog);
		} elseif ($m === 'to-innodb') {
			lxshell_return("lxphp.exe", $mcphp, "--engine=innodb", $nolog);
		}

	}
}
