<?php

class sshclient extends lxclass
{
	static $__desc = array("", "", "ssh_client");
	static $__desc_nname = array("", "", "ssh_client");

	static $__acdesc_show = array("", "",  "ssh_terminal");

	function get() {}
	function write() {}

	function showRawPrint($subaction = null)
	{
		global $gbl, $sgbl, $login, $ghtml;

		$parent = $this->getParentO();

	/*
		$v = lfile_get_contents("theme/filecore/sshterm-applet.htm");

		if ($parent->is__table('pserver')) {
			$v = str_replace("%username%", "root", $v);
			$ip = getFQDNforServer($parent->nname);

			$sshport = db_get_value("sshconfig", $parent->nname, "ssh_port");

			if (!$sshport) { $sshport = "22"; }

			$v = str_replace("%host%", $ip, $v);
			$v = str_replace("%port%", $sshport, $v);
			$v = str_replace("%connectimmediately%", "true", $v);
		} else if ($parent->is__table('client')) {
			if ($parent->isDisabled('shell') || !$parent->shell) {
				$ghtml->print_information("pre", "updateform", "sshclient", "disabled");

				exit;
			}

			$sshport = db_get_value("sshconfig", $parent->websyncserver, "ssh_port");

			if (!$sshport) { $sshport = "22"; }

			$ghtml->print_information("pre", "updateform", "sshclient", "warning");
			$ip = getFQDNforServer("localhost");
			$v = str_replace("%username%", $parent->username, $v);
			$v = str_replace("%host%", $ip, $v);
			$v = str_replace("%port%", $sshport, $v);
			$v = str_replace("%connectimmediately%", "true", $v);
		} else {
			$v = str_replace("%username%", "root", $v);
			$ip = $parent->getOneIP();
			$sshport = db_get_value("sshconfig", $parent->syncserver, "ssh_port");

			if (!$ip) {
				throw new lxException("need_to_add_at_least_one_ip_to_the_vps_for_logging_in");
			}

			if (!$sshport) { $sshport = "22"; }
			
			$v = str_replace("%host%", $ip, $v);
			$v = str_replace("%port%", $sshport, $v);
			$v = str_replace("%connectimmediately%", "true", $v);
		}
		
		print($v);
	*/

		if ($parent->is__table('pserver')) {
			$username = "root";
			$ip = getFQDNforServer($parent->nname);

			$sshport = db_get_value("sshconfig", $parent->nname, "ssh_port");

			if (!$sshport) { $sshport = "22"; }
		} else if ($parent->is__table('client')) {
			if ($parent->isDisabled('shell') || !$parent->shell) {
				exit;
			}

			$username = $parent->username;
			$ip = getFQDNforServer("localhost");

			$sshport = db_get_value("sshconfig", $parent->websyncserver, "ssh_port");

			if (!$sshport) { $sshport = "22"; }
		} else {
			$username = "root";
			$ip = $parent->getOneIP();
			$sshport = db_get_value("sshconfig", $parent->syncserver, "ssh_port");

			if (!$ip) {
				throw new lxException("need_to_add_at_least_one_ip_to_the_vps_for_logging_in");
			}

			if (!$sshport) { $sshport = "22"; }
		}
?>

<div style="text-align:center">
<applet code="com.jcraft.jcterm.JCTermApplet.class" archive="jcterm-0.0.10.jar?167,jsch-0.1.50.jar?835,jzlib-1.1.1.jar?742" codebase="thirdparty/jcterm/" height="480" width="650">   
    <param name="jcterm.font_size" value="13">
    <param name="jcterm.fg_bg" value="#000000:#ffffff,#ffffff:#000000,#00ff00:#000000">
    <!-- <param name="jcterm.config.repository" value="com.jcraft.jcterm.ConfigurationRepositoryFS"> -->
    <param name="jcterm.destinations" value="<?= $parent->username ?>@<?= $ip ?>:<?= $sshport ?>">
</applet>
</div>

<?php
	}

	static function initThisObjectRule($parent, $class) { return "sshclient"; }

	static function initThisObject($parent, $class, $name = null) { return "sshclient"; }
}
