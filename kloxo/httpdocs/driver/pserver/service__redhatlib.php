<?php

include_once "driver/pserver/service__linuxlib.php";

class Service__Redhat extends lxDriverClass
{
	// We need to properly port this system to debian. I tried using the chkconfig directly on debian, 
	// but it seems the individual scripts themselves have to support chkconfig if it has to work, 
	// and thus chkconfig fails to run. Now the only way is to use update-rc.d program on debain.

	function dbactionAdd()
	{
	//	lxshell_return("chkconfig", $this->main->servicename, 'on');
		exec("chkconfig {$this->main->servicename} on >/dev/null 2>&1");
	}

	function startStopService($act)
	{
		global $gbl, $sgbl, $login, $ghtml;

		$cmd = $this->main->servicename;

		if (strpos($cmd, 'proxy') !== false) {
			$t = str_replace('proxy', '', $cmd);
			$cmdlist = array($t, 'httpd');
		} else {
			$cmdlist = array($cmd);
		}

		foreach ($cmdlist as $key => $cmd) {
			exec_with_all_closed("service {$cmd} {$act}");
		}
	}

	function dbactionUpdate($subaction)
	{
		switch ($subaction) {
			case "start":
				$this->startStopService("start");

				break;
			case "stop":
				$this->startStopService("stop");

				break;
			case "restart":
				$this->startStopService("stop");
				sleep(2);
				$this->startStopService("start");

				break;

			case "toggle_boot_state":
				if ($this->main->servicename !== 'hiawatha') {
					if ($this->main->isOn('boot_state')) {
					//	lxshell_return("chkconfig", $this->main->servicename, 'on');
						exec("chkconfig {$this->main->servicename} on >/dev/null 2>&1");
					} else {
					//	lxshell_return("chkconfig", $this->main->servicename, 'off');
						exec("chkconfig {$this->main->servicename} off >/dev/null 2>&1");
					}
				}
				
				break;
			case "toggle_state":
				if ($this->main->isOn('state')) {
					$this->startStopService("start");
				} else {
					$this->startStopService("stop");
				}
				
				break;
		}
	}

	static function checkServiceInRc($rc, $service)
	{
		foreach ($rc as $r) {
			if (preg_match("/^S.*{$service}/i", $r)) {
				return true;
			}
		}
		
		return false;
	}

	static function checkServiceOn($service)
	{
		exec("ps --no-headers -o comm 1", $servicetype);

		if ($servicetype[0] === 'systemd') {
			exec("systemctl list-unit-files --type=service|grep ^'{$service}'|grep 'enabled'", $systemd);

			if (count($systemd) > 0) {
				return true;
			}
		}

		exec("chkconfig --list 2>/dev/null|grep ^'{$service}'|grep ':on'", $sysv);

		if (count($sysv) > 0) {
			return true;
		}

		return false;
	}

	static function getServiceDetails($list)
	{
		global $gbl, $sgbl, $login, $ghtml;

		$ps = lxshell_output("ps", "ax");
	//	$run = Service__linux::getRunLevel();
	//	$rclist = lscandir_without_dot("{$sgbl->__path_real_etc_root}/rc$run.d/");

		foreach ($list as &$__l) {
			$__l['install_state'] = 'dull';
			$__l['state'] = 'off';
			$__l['boot_state'] = 'off';
			
			if (isServiceExists($__l['servicename'])) {
				$__l['install_state'] = 'on';
			} else {
				continue;
			}
		//	if (self::checkServiceInRc($rclist, $__l['servicename'])) {
			if (self::checkServiceOn($__l['servicename'])) {
				$__l['boot_state'] = 'on';
			}

			if ($__l['grepstring']) {
				if (preg_match("/[\/ ]{$__l['grepstring']}/i", $ps)) {
					$__l['state'] = 'on';
				}
			}

			// MR -- recheck with 'service status' if state is off
			if ($__l['state'] === 'off') {
				$out = null;
			//	$ret = lxshell_return("/etc/init.d/{$__l['servicename']}", "status");
			//	exec("/etc/init.d/{$__l['servicename']} status|grep '(pid '", $out);
			//	exec("/etc/init.d/{$__l['servicename']} status|grep 'running'", $out);
				exec("pgrep ^{$__l['servicename']}", $out);

			//	if ($ret) {
				if (count($out) > 0) {
					$__l['state'] = 'on';
				} else {
					$__l['state'] = 'off';
				}
			}
		}
		
		return $list;
	}

	static function getServiceList()
	{
		return Service__Linux::getServiceList();
	}
}
