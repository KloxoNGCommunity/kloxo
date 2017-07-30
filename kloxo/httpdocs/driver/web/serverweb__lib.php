<?php

class serverweb__ extends lxDriverClass
{
	function __construct()
	{
	}

	function dosyncToSystemPre()
	{
	}

	function dbactionUpdate($subaction)
	{
		// We need to write because reads everything from the database.
		$this->main->write();

		switch ($subaction) {
			case "apache_optimize":
				$this->set_apache_optimize();

				break;
			case "fix_chownchmod":
				$this->set_fix_chownchmod();

				break;

			case "fix_chownchmod_user":
				$this->set_fix_chownchmod_user();

				break;
			case "mysql_convert":
				$this->set_mysql_convert();

				break;
			case "php_type":
				$this->set_php_type();

				break;

			case "php_branch":
				$this->set_php_branch();

				break;

			case "multiple_php_install":
				$this->set_multiple_php_install();

				break;

			case "multiple_php_remove":
				$this->set_multiple_php_remove();

				break;

			case "multiple_php_activate":
				$this->set_multiple_php_activate();

				break;

			case "php_used":
				$this->set_php_used();

				break;

			case "enable_php52m_fpm":

				$this->set_php52m_fpm();

				break;

			case "pagespeed_clear_cache":
				$this->set_pagespeed_clear_cache();

				break;
		}
	}

	function set_apache_optimize()
	{
		$scripting = '/script/apache-optimize';

		switch ($this->main->apache_optimize) {
			case 'default':
				$s = "--select=default";

				break;
			case 'low':
				$s = "--select=low";

				break;
			case 'medium':
				$s = "--select=medium";

				break;
			case 'high':
				$s = "--select=high";

				break;
		}

		switch ($this->main->enable_keepalive) {
			case 'off':
				$k = "--keepalive=off";

				break;
			case 'on':
				$k = "--keepalive=on";

				break;
		}

		lxshell_return("sh", $scripting, $s, $k, '--nolog');
	}

	function set_fix_chownchmod()
	{
		$scripting = '/script/fix-chownchmod';

		switch ($this->main->fix_chownchmod) {
			case 'fix-ownership':
				lxshell_return("sh", $scripting, "--select=chmod", '--nolog');

				break;
			case 'fix-permissions':
				lxshell_return("sh", $scripting, "--select=chown", '--nolog');

				break;
			case 'fix-ALL':
				lxshell_return("sh", $scripting, "--select=all", '--nolog');

				break;
		}
	}

	function set_fix_chownchmod_user()
	{
		$scripting = '/script/fix-chownchmod';

		$user = "--client=" . $this->main->getParentO()->nname;

		switch ($this->main->fix_chownchmod_user) {
			case 'fix-ownership':
				lxshell_return("sh", $scripting, "--select=chmod", $user, '--nolog');

				break;
			case 'fix-permissions':
				lxshell_return("sh", $scripting, "--select=chown", $user, '--nolog');

				break;
			case 'fix-ALL':
				lxshell_return("sh", $scripting, "--select=all", $user, '--nolog');

				break;
		}
	}

	function set_mysql_convert()
	{
		$scripting = '/script/mysql-convert';

		if ($this->main->mysql_charset === 'utf8') {
			$charset = '--utf8=yes';
		} else {
			$charset = '';
		}

		switch ($this->main->mysql_convert) {
			case 'to-myisam':
				lxshell_return("sh", $scripting, "--engine=myisam", $charset, '--nolog');

				break;
			case 'to-innodb':
				lxshell_return("sh", $scripting, "--engine=innodb", $charset, '--nolog');

				break;
			case 'to-aria':
				lxshell_return("sh", $scripting, "--engine=aria", '--nolog');

				break;
		}
	}

	function set_php_type()
	{
		global $login;

		$t = (isset($this->main->php_type)) ? $this->main->php_type : null;

		if (((stripos($t, '_ruid2') !== false)) || (stripos($t, '_itk') !== false)) {
			if ($this->main->secondary_php === 'on') {
				throw new lxException($login->getThrow("secondary_php_not_work_for"), '', $t);
			}
		}

		$ullkfapath = '../file/apache';

		$ehcpath = '/etc/httpd/conf';
		$ehcdpath = '/etc/httpd/conf.d';

		$haecpath = '/opt/configs/apache/etc/conf';
		$haecdpath = '/opt/configs/apache/etc/conf.d';

		if (isWebProxyOrApache()) {
			//--- some vps include /etc/httpd/conf.d/swtune.conf
			exec("'rm' -rf {$ehcdpath}/swtune.conf");

			lxshell_return("'cp' -rf {$ullkfapath} /opt/configs");

			if (!lfile_exists("{$ehcdpath}/~lxcenter.conf")) {
				lxfile_cp(getLinkCustomfile($haecdpath, "~lxcenter.conf"), $ehcdpath . "/~lxcenter.conf");
				lxfile_cp(getLinkCustomfile($haecpath, "httpd.conf"), $ehcpath . "/httpd.conf");
			}

			if (!lfile_exists("{$ehcdpath}/__version.conf")) {
				lxfile_cp(getLinkCustomfile($haecdpath, "__version.conf"), $ehcdpath . "/__version.conf");
			}

			//--- don't use '=== true' but '!== false'
			if (stripos($t, 'mod_php') !== false) {
				$this->set_modphp($t);
			} elseif (stripos($t, 'suphp') !== false) {
				$this->set_suphp();
			} elseif (stripos($t, 'php-fpm') !== false) {
				$this->set_phpfpm();
			} elseif (stripos($t, 'fcgid') !== false) {
				$this->set_fcgid();
			} elseif (stripos($t, 'proxy_fcgi') !== false) {
				$this->set_proxyfcgi();
			}

			if (stripos($t, 'php-fpm') !== false) {
				exec("chkconfig php-fpm on >/dev/null 2>&1");
			} else {
				exec("chkconfig php-fpm off >/dev/null 2>&1");
			}

		//	$this->set_mpm($t);
		}

		$this->set_secondary_php();

		createRestartFile("restart-web");
	}

	function set_modphp($type)
	{
		$ehcdpath = '/etc/httpd/conf.d';
		$haecdpath = '/opt/configs/apache/etc/conf.d';

		$this->rename_to_nonconf();

		$this->set_mpm('prefork');

		if ($type === 'mod_php') {
			// no action here
		} elseif ($type === 'mod_php_ruid2') {
			lxfile_cp(getLinkCustomfile($haecdpath, "ruid2.conf"), $ehcdpath . "/ruid2.conf");
			lxfile_rm("{$ehcdpath}/ruid2.nonconf");
		} elseif ($type === 'mod_php_itk') {
			lxfile_cp(getLinkCustomfile($haecdpath, "itk.conf"), $ehcdpath . "/itk.conf");
			exec("echo 'HTTPD=/usr/sbin/httpd.itk' >/etc/sysconfig/httpd");
		}

		lxfile_cp(getLinkCustomfile($haecdpath, "php.conf"), $ehcdpath . "/php.conf");
		lxfile_rm("{$ehcdpath}/php.nonconf");

		$this->remove_phpfpm();
	}

	function set_suphp()
	{
		$ehcdpath = '/etc/httpd/conf.d';

		$epath = '/etc';
		$haepath = '/opt/configs/apache/etc';

		$this->rename_to_nonconf();

		lxfile_cp(getLinkCustomfile($haepath, "suphp.conf"), $epath . "/suphp.conf");

		$this->remove_phpfpm();

		lxshell_return("sh", "/script/fixphp", "--nolog");

		lxfile_rm("{$ehcdpath}/suphp.nonconf");
	}

	function set_phpfpm()
	{
		$ehcdpath = '/etc/httpd/conf.d';
		$haecdpath = '/opt/configs/apache/etc/conf.d';

		$this->rename_to_nonconf();

		lxfile_rm("{$ehcdpath}/proxy_fcgi.nonconf");
		lxfile_rm("{$ehcdpath}/proxy_fcgi.conf");
		lxfile_rm("{$ehcdpath}/fastcgi.nonconf");

		lxfile_cp(getLinkCustomfile($haecdpath, "_inactive_.conf"), $ehcdpath . "/php.conf");
		lxfile_cp(getLinkCustomfile($haecdpath, "fastcgi.conf"), $ehcdpath . "/fastcgi.conf");

	//	lxshell_return("chkconfig", "php-fpm", "on");
		exec("chkconfig php-fpm on >/dev/null 2>&1");
	}

	function set_fcgid()
	{
		$ehcdpath = '/etc/httpd/conf.d';
		$haecdpath = '/opt/configs/apache/etc/conf.d';

		$this->remove_phpfpm();

		$this->rename_to_nonconf();

		lxfile_cp(getLinkCustomfile($haecdpath, "fcgid.conf"), $ehcdpath . "/fcgid.conf");
		lxfile_rm("{$ehcdpath}/fcgid.nonconf");
	}

	function set_proxyfcgi()
	{
		$ehcmdpath = '/etc/httpd/conf.modules.d';
		$haecmdpath = '/opt/configs/apache/etc/conf.modules.d';

		$this->remove_phpfpm();

		$this->rename_to_nonconf();

		lxfile_cp(getLinkCustomfile($haecmdpath, "00-proxy.conf"), $ehcmdpath . "/00-proxy.conf");
		lxfile_rm("{$ehcmdpath}/00-proxy.nonconf");
	}

	function remove_phpfpm()
	{
		// MR -- no remove and just off/disable
		exec("chkconfig php-fpm off >/dev/null 2>&1; service php-fpm stop");
	}

	function rename_to_nonconf()
	{
		// MR -- use overwrite with 'inactive' content instead rename
		// minimize 'effect' when running 'yum update'
		$list = array('php', 'fastcgi', 'fcgid', 'ruid2', 'suphp', 'proxy_fcgi', 'itk');

		foreach ($list as &$l) {
			if ($l === 'proxy_fcgi') {
				$tpath = '/etc/httpd/conf.modules.d';
				$spath = '/opt/configs/apache/etc/conf.modules.d';
			} else {
				$tpath = '/etc/httpd/conf.d';
				$spath = '/opt/configs/apache/etc/conf.d';
			}

			$source = getLinkCustomfile($spath, "_inactive_.conf");
			if ($l === 'proxy_fcgi') {
				lxfile_cp($source, "{$tpath}/00-proxy.conf");
				lxfile_rm("{$tpath}/00-proxy.nonconf");
			} else {
				lxfile_cp($source, "{$tpath}/{$l}.conf");
				lxfile_rm("{$tpath}/{$l}.nonconf");
			}
		}
	}

	function set_mpm($type)
	{
		if (stripos($type, '_worker') !== false) {
			$a = '.worker';
		} elseif (stripos($type, '_event') !== false) {
			$a = '.event';
		} elseif (stripos($type, '_itk') !== false) {
			$a = '.itk';
		} else {
			$a = '';
		}

		if (file_exists("../etc/use_apache24.flg")) {
			$a = str_replace('.', '', $a);

			if ($a === '') { $a = 'prefork'; }

		//	exec("echo 'LoadModule mpm_{$a}_module modules/mod_mpm_{$a}.so' >/etc/httpd/conf.modules.d/00-mpm.conf");
			file_put_contents("/etc/httpd/conf.modules.d/00-mpm.conf",
				"LoadModule mpm_{$a}_module modules/mod_mpm_{$a}.so");
		} else {
		//	exec("echo 'HTTPD=/usr/sbin/httpd{$a}' >/etc/sysconfig/httpd");
			file_put_contents("/etc/sysconfig/httpd.conf", "HTTPD=/usr/sbin/httpd{$a}");
		}

		$scripting = '/script/fixweb';

		lxshell_return("sh", $scripting, "--target=defaults", "--nolog");

		setRpmInstalled("httpd");
	}

	function set_secondary_php()
	{
		$ehcdpath = '/etc/httpd/conf.d';
		$haecdpath = '/opt/configs/apache/etc/conf.d';

		$epath = '/etc';
		$haepath = '/opt/configs/apache/etc';

		// MR -- remove old files
		lxfile_rm("{$ehcdpath}/suphp52.nonconf");
		lxfile_rm("{$ehcdpath}/suphp52.conf");

		if ($this->main->secondary_php === 'on') {
			if (stripos($this->main->php_type, 'suphp') !== false) {
				lxfile_cp(getLinkCustomfile($haecdpath, "suphp2.conf"), $ehcdpath . "/suphp.conf");
				lxfile_cp(getLinkCustomfile($haecdpath, "_inactive_.conf"), $ehcdpath . "/suphp2.conf");
			} else {
				lxfile_cp(getLinkCustomfile($haecdpath, "_inactive_.conf"), $ehcdpath . "/suphp.conf");
				lxfile_cp(getLinkCustomfile($haecdpath, "suphp2.conf"), $ehcdpath . "/suphp2.conf");
			}

			if (isWebProxy()) {
				if (stripos($this->main->php_type, 'fcgid') !== false) {
					lxfile_cp(getLinkCustomfile($haecdpath, "fcgid2.conf"), $ehcdpath . "/fcgid.conf");
					lxfile_cp(getLinkCustomfile($haecdpath, "_inactive_.conf"), $ehcdpath . "/fcgid2.conf");
				} else {
					lxfile_cp(getLinkCustomfile($haecdpath, "_inactive_.conf"), $ehcdpath . "/fcgid.conf");
					lxfile_cp(getLinkCustomfile($haecdpath, "fcgid2.conf"), $ehcdpath . "/fcgid2.conf");
				}
			}
		} else {
			lxfile_rm("{$ehcdpath}/suphp2.conf");
			lxfile_rm("{$ehcdpath}/fcgid2.conf");

			if (stripos($this->main->php_type, 'suphp') !== false) {
				lxfile_cp(getLinkCustomfile($haepath, "suphp.conf"), $epath . "/suphp.conf");
				lxfile_cp(getLinkCustomfile($haecdpath, "suphp.conf"), $ehcdpath . "/suphp.conf");
			} else {
				lxfile_cp(getLinkCustomfile($haecdpath, "_inactive_.conf"), $ehcdpath . "/suphp.conf");
			}

			if (stripos($this->main->php_type, 'fcgid') !== false) {
				lxfile_cp(getLinkCustomfile($haecdpath, "fcgid.conf"), $ehcdpath . "/fcgid.conf");
			} else {
				lxfile_cp(getLinkCustomfile($haecdpath, "_inactive_.conf"), $ehcdpath . "/fcgid.conf");
			}
		}

		lxfile_cp(getLinkCustomfile($haepath, "suphp.conf"), $epath . "/suphp.conf");
	}

	function set_php_branch($branch = null)
	{
		$ehcdpath = '/etc/httpd/conf.d';
		$haecdpath = '/opt/configs/apache/etc/conf.d';
	/*
		$installed = isRpmInstalled('yum-plugin-replace');

		if (!$installed) {
			setRpmInstalled("yum-plugin-replace");
		}
	*/
		$scripting = '/script/set-php-branch';

		if ($branch) {
			$branchselect = $branch;
		} else {
			$branchselect = $this->main->php_branch;
		}

		$branchselect = preg_replace('/(.*)\_\(as\_(.*)\)/', '$1', $branchselect);

		lxshell_return("sh", $scripting, $branchselect);

		$scripting = '/script/fixweb';

		lxshell_return("sh", $scripting, '--nolog');
	/*
		$installed = isRpmInstalled("{$branchselect}-fpm");

		if ($installed) {
		//	lxshell_return("chkconfig", "php-fpm", "on");
			createRestartFile("restart-web");
		}
	*/
		createRestartFile("restart-web");

		if (stripos('mod_php', $this->main->php_type) === false) {
			lxfile_mv(getLinkCustomfile($haecdpath, "_inactive_.conf"), $ehcdpath . "/php.conf");

		}
	}

	function set_php_used()
	{
		global $login;

		$v = $this->main->php_used;

		if (isWebProxyOrApache()) {
			$p = $this->main->php_type;

			if ($v !== '--PHP Branch--') {

				if (strpos($p, 'php-fpm') !== false) {
					// no action
				} else {
					throw new lxException($login->getThrow("only_work_for_php-type_for_php-fpm"), '', $p);
				}
			}
		}

		switch ($v) {
			case "--PHP Branch--":
				lxshell_return("sh", "/script/set-php-fpm", "php");
				break;
			default:
				lxshell_return("sh", "/script/set-php-fpm", $v);
				break;
		}
	}

	function set_multiple_php_install()
	{
		global $login;

		$i = $this->main->multiple_php_install;

		if ($i !== '') {
			$c = '/tmp/phpm-install-process.sh';

			if (file_exists($c)) {
				throw new lxException($login->getThrow('other_install_process_still_running'), '', $this->main->syncserver);
				return;
			}

			$list = explode(',', $i);

			$b = '';

			foreach ($list as $k => $v) {
				$b .= "sh /script/phpm-installer {$v} -y\n";
			}

			$b .= "sh /script/fixphp\n";
			$b .= "'rm' -f {$c}\n";

			file_put_contents($c, $b);

			lxshell_background("sh", $c);

			if (file_exists($c)) {
				throw new lxException($login->getThrow('install_process_running_in_background'), '', $this->main->syncserver);
			}
		}
	}

	function set_multiple_php_remove()
	{
		global $login;

		$rem = $this->main->multiple_php_remove;

		if ($rem === '') {
			throw new lxException($login->getThrow('no_options_selected'), '', 'blank');
		}

		$list = explode(',', $rem);

		foreach ($list as $k => $v) {
			if ($v === $this->main->php_used) {
				throw new lxException($login->getThrow('php_already_in_used'), '', $v);
			}
		}

		exec("sh /script/stop-php-fpm");

		foreach ($list as $k => $v) {
			exec("sh /script/phpm-remover {$v}");
		}

		exec("sh /script/start-php-fpm");
	}

	function set_multiple_php_activate()
	{
		global $login;

		$e = $this->main->multiple_php_flag;

		if ($e === 'on') {
			@touch('../etc/flag/enablemultiplephp.flg');
		} else {
			@unlink('../etc/flag/enablemultiplephp.flg');
		}

		exec("sh /script/enable-php-fpm; sh /script/add-start-queue restart-php-fpm");
	}

	function set_php52m_fpm()
	{
		global $login;

		$e = $this->main->enable_php52m_fpm;

		if ($e === 'on') {
			@touch('../etc/flag/enable_php52m-fpm.flg');
		} else {
			@unlink('../etc/flag/enable_php52m-fpm.flg');
		}

		exec("sh /script/enable-php-fpm; sh /script/add-start-queue restart-php-fpm");
	}

	function set_pagespeed_clear_cache()
	{
		global $login;

		$rem = $this->main->pagespeed_cache;

		if ($rem === '') {
			throw new lxException($login->getThrow('no_options_selected'), '', 'blank');
		} else {
			exec("sh /script/clearcache-pagespeed");
		}
	}
}
