<?php

class serverweb__ extends lxDriverClass
{
	function __construct()
	{
	}


	function dbactionUpdate($subaction)
	{
		global $gbl, $sgbl, $login, $ghtml;

		switch($subaction) {
			case "apache_optimize":
				$this->set_apacheoptimize();

				break;

			case "fix_chownchmod":
				$this->set_fixchownchmod();

				break;

			case "mysql_convert":
				$this->set_mysqlconvert();

				break;

			case "php_type":
				$this->set_phptype();

				break;
		}
	}

	function set_apacheoptimize()
	{
		global $gbl, $sgbl, $login, $ghtml;

		$nolog = 'yes';

		$ullkbfaophp = '/usr/local/lxlabs/kloxo/bin/fix/apache-optimize.php';

		switch($this->main->apache_optimize) {
			case 'optimize':
				lxshell_return("lxphp.exe", $ullkbfaophp, "--select=optimize", $nolog);
				break;
		}
	}

	function set_fixchownchmod()
	{
		global $gbl, $sgbl, $login, $ghtml;

		$nolog = 'yes';

		$ullkbffcphp = '/usr/local/lxlabs/kloxo/bin/fix/fix-chownchmod.php';

		switch($this->main->fix_chownchmod) {
			case 'fix-ownership':
				lxshell_return("lxphp.exe", $ullkbffcphp, "--select=chmod", $nolog);
				break;
			case 'fix-permissions':
				lxshell_return("lxphp.exe", $ullkbffcphp, "--select=chown", $nolog);
				break;
			case 'fix-ALL':
				lxshell_return("lxphp.exe", $ullkbffcphp, "--select=all", $nolog);
				break;
		}
	}

	function set_mysqlconvert()
	{
		global $gbl, $sgbl, $login, $ghtml;

		$nolog = 'yes';

		$ullkbfmcphp = '/usr/local/lxlabs/kloxo/bin/fix/mysql-convert.php';

		switch($this->main->mysql_convert) {
			case 'to-myisam':
				$t = 'myisam';
				lxshell_return("lxphp.exe", $ullkbfmcphp, "--engine=myisam", $nolog);
				break;
			case 'to-innodb':
				lxshell_return("lxphp.exe", $ullkbfmcphp, "--engine=innodb", $nolog);
				break;
		}
	}

	function set_phptype()
	{
		global $gbl, $sgbl, $login, $ghtml;

		$nolog = 'yes';

		$t = (isset($this->main->php_type)) ? $this->main->php_type : null;

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

		$ullkbffwphp = '/usr/local/lxlabs/kloxo/bin/fix/fixweb.php';

		if (isWebProxyOrApache()) {
			//--- some vps include /etc/httpd/conf.d/swtune.conf
			lxshell_return("rm", "-f", $ehcdpath."/swtune.conf");

		//	lxfile_cp_content($ullkfapath, $hapath);
			exec("yes|cp -rf {$ullkfapath} /home");

			if (!lfile_exists("{$ehcdpath}/~lxcenter.conf")) {
				lxfile_cp(getLinkCustomfile($haecdpath, "~lxcenter.conf"), $ehcdpath."/~lxcenter.conf");
				lxfile_cp(getLinkCustomfile($haecpath, "httpd.conf"), $ehcpath."/httpd.conf");
			}

		//	sleep(10);

			//--- don't use '=== true' but '!== false'
			if (stripos($t, 'mod_php') !== false) {
				$this->set_modphp($t);
			} elseif (stripos($t, 'suphp') !== false) {
				$this->set_suphp();
			} elseif (stripos($t, 'php-fpm') !== false) {
				$this->set_suphp();
				$this->set_phpfpm();
			} elseif (stripos($t, 'fcgid') !== false) {
				$this->set_suphp();
				$this->set_fcgid();
			}

			$this->set_mpm($t);
		}
	}

	function set_modphp($type)
	{
		$ehcdpath = '/etc/httpd/conf.d';
		$haecdpath = '/home/apache/etc/conf.d';

		$this->rename_to_nonconf();

		// use > that equal to lxfile_rm + echo >>
		exec("echo 'HTTPD=/usr/sbin/httpd' >/etc/sysconfig/httpd");

		if ($type === 'mod_php') {
			// no action here
		} elseif ($type === 'mod_php_ruid2') {
			lxshell_return("yum", "-y", "install", "mod_ruid2");
			lxshell_return("yum", "-y", "update", "mod_ruid2");
			lxfile_rm("{$ehcdpath}/ruid2.nonconf");
			lxfile_cp(getLinkCustomfile($haecdpath, "ruid2.conf"), $ehcdpath."/ruid2.conf");
		} elseif ($type === 'mod_php_itk') {
			exec("echo 'HTTPD=/usr/sbin/httpd.itk' >/etc/sysconfig/httpd");
		}

		lxfile_rm("{$ehcdpath}/php.nonconf");
		lxfile_cp(getLinkCustomfile($haecdpath, "php.conf"), $ehcdpath."/php.conf");

		$this->remove_phpfpm();
	}

	function set_suphp()
	{
		$ehcdpath = '/etc/httpd/conf.d';
		$haepath = '/home/apache/etc';
		$haecdpath = '/home/apache/etc/conf.d';

		$this->rename_to_nonconf();

		lxshell_return("yum", "-y", "install", "mod_suphp");
		$ret = lxshell_return("yum", "-y", "update", "mod_suphp");

		if ($ret) {
			throw new lxexception('mod_suphp_update_failed', 'parent');
		}

		$ver = getPhpVersion();

		$phpbranch = getPhpBranch();

		if (!file_exists('/usr/bin/php_pure')) {
			exec("rpm -e --nodeps {$phpbranch}");
			exec("rpm -e --nodeps {$phpbranch}-cli");
			exec("rpm -e --nodeps {$phpbranch}-common");
			exec("rpm -e --nodeps {$phpbranch}-fpm");

			$ret = lxshell_return("yum", "-y", "install", "php-5.2.17-1");

			if ($ret) {
				throw new lxexception('php-5.2.17-1_update_failed', 'parent');
			}

			lxfile_cp('/usr/bin/php', '/usr/bin/php_pure');
			lxfile_cp('/usr/bin/php-cgi', '/usr/bin/php-cgi_pure');

			$ret = lxshell_return("yum", "-y", "install", "{$phpbranch}", "{$phpbranch}-fpm");

			if ($ret) {
				throw new lxexception('{$phpbranch}_or_{$phpbranch}-fpm_install_failed', 'parent');
			}
		}

		if (version_compare($ver, "5.3.2", ">")) {
			lxfile_cp(getLinkCustomfile($haepath, "suphp.conf"), "/etc/suphp.conf");
		} else {
			lxfile_cp(getLinkCustomfile($haepath, "suphp_pure.conf"), "/etc/suphp.conf");
		}

		exec("sh /script/fixphp --nolog");

		lxfile_rm("{$ehcdpath}/suphp.nonconf");
		lxfile_cp(getLinkCustomfile($haecdpath, "suphp.conf"), $ehcdpath."/suphp.conf");

		$this->remove_phpfpm();
	}

	function set_phpfpm()
	{
		$ehcdpath = '/etc/httpd/conf.d';
		$haepath = '/home/apache/etc';
		$haecdpath = '/home/apache/etc/conf.d';

		$this->rename_to_nonconf();

		$ver = getRpmVersion('httpd');

		if (version_compare($ver, "2.4.0", ">=") !== false) {
			lxfile_cp(getLinkCustomfile($haecdpath, "proxy_fcgi.conf"), $ehcdpath."/proxy_fcgi.conf");
			lxfile_rm($ehcdpath."/proxy_fcgi.nonconf");
		} else {
			$phpbranch = getPhpBranch();

			lxshell_return("yum", "-y", "install", "mod_fastcgi");
			$ret = lxshell_return("yum", "-y", "update", "mod_fastcgi");

			if ($ret) {
				throw new lxexception('mod_fastcgi_update_failed', 'parent');
			}

			lxshell_return("yum", "-y", "install", "{$phpbranch}-fpm");
			$ret = lxshell_return("yum", "-y", "update", "{$phpbranch}-fpm");

			if ($ret) {
				throw new lxexception('{$phpbranch}-fpm_update_failed', 'parent');
			}

			lxfile_cp(getLinkCustomfile($haecdpath, "fastcgi.conf"), $ehcdpath."/fastcgi.conf");
			lxfile_rm($ehcdpath."/fastcgi.nonconf");
		}

		lxshell_return("chkconfig", "php-fpm", "on");
		$ret = lxshell_return("service", "php-fpm", "restart");

		if ($ret) {
			throw new lxexception('php-fpm_restart_failed', 'parent');
		}

	}

	function set_fcgid()
	{
		$ehcdpath = '/etc/httpd/conf.d';
		$haepath = '/home/apache/etc';
		$haecdpath = '/home/apache/etc/conf.d';

		$this->rename_to_nonconf();

		lxshell_return("yum", "-y", "install", "mod_fcgid");
		$ret = lxshell_return("yum", "-y", "update", "mod_fcgid");

		if ($ret) {
			throw new lxexception('mod_fcgid_update_failed', 'parent');
		}

		lxfile_cp(getLinkCustomfile($haecdpath, "fcgid.conf"), $ehcdpath."/fcgid.conf");
		lxfile_rm($ehcdpath."/fcgid.nonconf");

		$this->remove_phpfpm();
	}

	function remove_phpfpm()
	{
		$phpbranch = getPhpBranch();

		$ret = lxshell_return("yum", "-y", "remove", "{$phpbranch}-fpm");

		if ($ret) {
			throw new lxexception('{$phpbranch}-fpm_update_failed', 'parent');
		}
	}

	function rename_to_nonconf()
	{
		$ehcdpath = '/etc/httpd/conf.d';

		lxfile_mv($ehcdpath."/php.conf", $ehcdpath."/php.nonconf");
		lxfile_mv($ehcdpath."/fastcgi.conf", $ehcdpath."/fastgi.nonconf");
		lxfile_mv($ehcdpath."/fcgid.conf", $ehcdpath."/fcgid.nonconf");
		lxfile_mv($ehcdpath."/ruid2.conf", $ehcdpath."/ruid2.nonconf");
		lxfile_mv($ehcdpath."/suphp.conf", $ehcdpath."/suphp.nonconf");
		lxfile_mv($ehcdpath."/proxy_fcgi.conf", $ehcdpath."/proxy_fcgi.nonconf");
	}

	function set_mpm($type)
	{
		$ullkbffwphp = '/usr/local/lxlabs/kloxo/bin/fix/fixweb.php';

		if (stripos($type, '_worker') !== false) {
			exec("echo 'HTTPD=/usr/sbin/httpd.worker' >/etc/sysconfig/httpd");
		} elseif (stripos($type, '_event') !== false) {
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
}
