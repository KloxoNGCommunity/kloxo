<?php

class sshconfig__linux extends lxDriverClass 
{
	function dbactionUpdate($subaction)
	{
		global $login;

		switch ($subaction) {
			case "ssh_port":
				if ($this->main->ssh_port && !($this->main->ssh_port > 0)) {
					throw new lxException($login->getThrow('invalid_ssh_port'), '', $this->main->ssh_port);
				}

				dprint($this->main->ssh_port);

				$this->main->ssh_port = trim($this->main->ssh_port);

				if (!$this->main->ssh_port) {
					$port = "22";
				} else {
					$port = $this->main->ssh_port;
				}

				$str = lfile_get_contents("../file/template/sshd_config");

				$str = str_replace("%ssh_port%", $port, $str);
		
				if ($this->main->isOn('without_password_flag')) {
					$wt = 'without-password';
				} else {
					$wt = 'yes';
				}

				if ($this->main->isOn('disable_password_flag')) {
					$pwa = 'no';
				} else {
					$pwa = 'yes';
				}

				$str = str_replace("%permit_root_login%", $wt , $str);
				$str = str_replace("%permit_password%", $pwa , $str);
				$ret = lfile_put_contents("/etc/ssh/sshd_config", $str);

				if (!$ret) {
					exec_with_all_closed("sh /script/load-wrapper >/dev/null 2>&1 &");
					throw new lxException($login->getThrow('could_not_write_config_file'), '', '/etc/ssh/sshd_config');
				}

				exec_with_all_closed("service sshd restart");

				break;
			case "ssh_password":
				validate_password($this->main->password);

				exec("echo -e \"{$this->main->password}\n{$this->main->password}\n\" | passwd root");

				break;
		}
	}
}
