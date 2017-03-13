<?php 

class sshconfig extends lxdb
{
	static $__desc = array("", "",  "SSH_config");
	static $__acdesc_show = array("", "",  "SSH_config");
	static $__desc_ssh_port = array("", "",  "SSH_port");
	static $__desc_without_password_flag = array("f", "",  "do_not_allow_password_based_access_to_root");
	static $__desc_disable_password_flag = array("f", "",  "completely_disable_password_based_access");
	static $__desc_config_flag = array("f", "",  "dont_warn_me_about_password_access_to_root");

	static $__desc_password = array("", "",  "password");

	static function initThisObjectRule($parent, $class, $name = null) { return $parent->nname; }

	function createShowUpdateform()
	{
	//	$uflist['update'] = null;

		$uflist['ssh_port'] = null;
		$uflist['ssh_password'] = null;

		return $uflist;
	}

	function updateform($subaction, $param)
	{
		switch ($subaction) {
			case "ssh_port":
				$vlist['ssh_port'] = null;

				exec("cat /etc/ssh/sshd_config|grep ^'Port'|awk '{print $2}'", $out);
				if (count($out) > 0) {
					$port = $out[0];
				} else {
					$port = "22";
				}

				$this->setDefaultValue("ssh_port", $port);

				$vlist['without_password_flag'] = null;
				$vlist['disable_password_flag'] = null;
				$vlist['config_flag'] = null;

				break;
			case "ssh_password":
				$vlist['password'] = null;

				break;
		}

		return $vlist;
	}
}
