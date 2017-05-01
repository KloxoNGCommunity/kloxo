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

		$v = lfile_get_contents("thirdparty/sshterm-applet/sshterm-applet.htm");

	//	if ($parent->is__table('pserver')) {
		if ($parent->getClass() === 'pserver') {
			$username = "root";
			$ip = getFQDNforServer($parent->nname);

			$sshport = db_get_value("sshconfig", $parent->nname, "ssh_port");

			if (!$sshport) { $sshport = "22"; }

			$connectimmediately = "true";
	//	} else if ($parent->is__table('client')) {
		} else if ($parent->getClass() === 'client') {
			if ($parent->isDisabled('shell') || !$parent->shell) {
				exit;
			}

			$username = $parent->username;

			$ip = getFQDNforServer("localhost");

			$sshport = db_get_value("sshconfig", $parent->websyncserver, "ssh_port");

			if (!$sshport) { $sshport = "22"; }

			$connectimmediately = "true";
		} else {
			$username = "root";

			$ip = $parent->getOneIP();

			$sshport = db_get_value("sshconfig", $parent->syncserver, "ssh_port");

			if (!$ip) {
				throw new lxException($login->getThrow("need_to_add_at_least_one_ip_to_vps_for_logging_in"));
			}

			if (!$sshport) { $sshport = "22"; }
			
			$connectimmediately = "true";
		}

		if ($login->nname === 'admin') {
			$ar['ip_address'] = $gbl->c_session->ip_address;
			$ar['session'] = $gbl->c_session->tsessionid;
			lfile_put_serialize("../session/ssh_{$ar['session']}", $ar['ip_address']);
			$servar = base64_encode(serialize($ar));
?>
<div style="text-align:center">
<IFRAME style="width:800px; height:600px" src="web-console/index.php?session=<?php echo $servar; ?>"></IFRAME>
</div>
<?php
		} else {
			if (file_exists("thirdparty/jcterm")) {
?>

<div style="text-align:center">
	<applet code="com.jcraft.jcterm.JCTermApplet.class" 
			archive="jcterm-0.0.10.jar?167,jsch-0.1.46.jar?835,jzlib-1.1.1.jar?742" 
			codebase="thirdparty/jcterm/" height="600" width="800">   
		<param name="jcterm.font_size" value="13">
		<param name="jcterm.fg_bg" value="#000000:#ffffff,#ffffff:#000000,#00ff00:#000000">
		<!-- <param name="jcterm.config.repository" value="com.jcraft.jcterm.ConfigurationRepositoryFS"> -->
		<param name="jcterm.destinations" value="<?= $parent->username ?>@<?= $ip ?>:<?= $sshport ?>">
	</applet>
</div>
<?php
			} else {
?>

<div style="text-align:center; width: 640px; height: 480px; margin: 0 auto; border: 0; padding: 0">
	<applet style="width: 640px; height: 480px; border: 1px solid #ddd" 
		archive="SSHTermApplet-signed.jar,SSHTermApplet-jdkbug-workaround-signed.jar,SSHTermApplet-jdk1.3.1-dependencies-signed.jar"
		code="com.sshtools.sshterm.SshTermApplet" codebase="thirdparty/sshterm-applet/"
		mayscript="mayscript">

		<param name=sshapps.connection.host value="<?= $ip ?>">
		<param name=sshapps.connection.port value="<?= $sshport ?>">
		<param name=sshapps.connection.userName value="<?= $parent->username ?>">
		<param name=sshapps.connection.authenticationMethod value=password>
		<param name=sshapps.connection.connectImmediately value=<?= $connectimmediately ?>">
	</applet>
</div>
<?php
			}
		}
	}

	static function initThisObjectRule($parent, $class) { return "sshclient"; }

	static function initThisObject($parent, $class, $name = null) { return "sshclient"; }
}
