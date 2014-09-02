<?php

class watchdog__sync extends Lxdriverclass {

	static function watchRun()
	{
		global $gbl, $sgbl, $login, $ghtml;

		if (lx_core_lock_check_only("scavenge.php", "scavenge.php.pid")) {
			log_log("watchdog", "scavenge is running");
			dprint("Savenge is running\n");
			return;
		}

		// Don't restart service while booting.
		$time = os_getUptime();

		if ($time < 600) { return; }

		$list = lfile_get_unserialize("../etc/watchdog.conf");

		foreach((array)$list as $l) {
			if (!isOn($l['status'])) {
				print("{$l['servicename']} is disabled\n");
				continue;
			}

			if (check_if_port_on($l['port'])) {
				continue;
			}

			if (csb($l['action'], "__driver_")) {
				$class = strfrom($l['action'], "__driver_");
				$driverapp = slave_get_driver($class);

				if ($driverapp) {
					if ($driverapp === 'none') { return; }

					// MR -- need handling for webproxy and php-fpm
					if (csb($driverapp, "proxy")) {
						$a = array(str_replace('proxy', '', $driverapp), 'httpd');
					} else {
						$a = array($driverapp);
					}

					foreach ($a as &$v) {
						createRestartFile($v);
					}

					if (file_exists('/etc/init.d/php-fpm')) {
						createRestartFile('php-fpm');
					}
				} else {
					$a[0] = $class;
					createRestartFile('php-fpm');
				}

				$action = "{$a[0]} restart";
			} else {
				$action = $l['action'];
				exec_with_all_closed("$action >/dev/null 2>&1");
			}

			log_log("watchdog", "$action executed for port {$l['port']}");
			send_system_monitor_message_to_admin("Port: {$l['port']}\nAction: $action");
		}
	}

	function dbactionUpdate($subaction)
	{
		$result = $this->main->__var_watchlist;
		unset($this->main->__var_watchlist);
		$result = merge_array_object_not_deleted($result, $this->main);
		lfile_put_serialize("../etc/watchdog.conf", $result);
	}

	function dbactionAdd()
	{
		$this->dbactionUpdate("");
	}
}
