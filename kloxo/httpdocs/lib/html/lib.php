<?php

function getNumForString($name)
{
	$num = 0;

	for ($i = 0; $i < strlen($name); $i++) {
		$num += ord($name[$i]) * $i;
	}

	$num = $num % 99999999;
	$num = intval($num);

	return $num;
}

function is_openvz()
{
	return lxfile_exists("/proc/user_beancounters");
}

function auto_update()
{
	global $sgbl, $login;

	$gen = $login->getObject('general');

	if ($gen->generalmisc_b->isOn('autoupdate')) {
		dprint("Auto Updating\n");

		if (!checkIfLatest()) {
			exec_with_all_closed("lxphp.exe ../bin/update.php");
		}
	} else {
		// Remove timezone warning
		date_default_timezone_set("UTC");

		if ((date('d') == 10) && !checkIfLatest()) {
			$latest = getLatestVersion();
			$msg = "New Version $latest Available for {$sgbl->__var_program_name}";
			send_mail_to_admin($msg, $msg);
		}
	}
}

function getIncrementedValueFromTable($table, $column)
{
	$sq = new Sqlite(null, $table);
	$res = $sq->rawQuery("select $column from $table order by ($column + 0) DESC limit 1");
	$value = $res[0][$column] + 1;

	return $value;
}

function http_is_self_ssl()
{
	return (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on'));
}

function get_other_driver($class, $driverapp)
{
	include "../file/driver/rhel.inc";

	$ret = null;

	if (is_array($driver[$class])) {
		foreach ($driver[$class] as $l) {
			if ($l !== $driverapp) {
				$ret[] = $l;
			}
		}
	}

	return $ret;
}

function csainlist($string, $ssl)
{
	foreach ($ssl as $ss) {
		if (csa($string, $ss)) {
			return true;
		}
	}

	return false;
}

function file_put_between_comments($username, $stlist, $endlist, $startstring, $endstring, $file, $string, $nowarning = null)
{
	global $sgbl;

	if (empty($string)) {
		dprint("ERROR: Function file_put_between_comments\nERROR: File " . $file . " has empty \$string\n");

		return;
	}

	$startcomment = '';

//	if ($nowarning !== true) {
	if ($nowarning !== false) {
		// no action
	} else {
		$prgm = $sgbl->__var_program_name;

		$startcomment = "\n### Please Don't edit these comments or the content in between. " .
			"{$prgm} uses this to recognize the lines it writes to the the file. " .
			"If the above line is corrupted, it may fail to recognize them, leading to multiple lines.";
	}

	$outlist = null;
	$afterlist = null;
	$outstring = null;
	$afterstring = null;
	$afterend = false;
	

	if (lxfile_exists($file)) {
		$list = lfile_trim($file);
		$inside = false;

		foreach ($list as $l) {
			if (csainlist($l, $stlist)) {
				$inside = true;
			}

			if (csainlist($l, $endlist)) {
				$inside = false;
				$afterend = true;
				continue;
			}

			if ($inside) {
				continue;
			}

			if ($afterend) {
				$afterlist[] = $l;
			} else {
				$outlist[] = $l;
			}
		}
	}

	if (count($outlist) > 0) {
		$outstring = implode("\n", $outlist);
	}

	if (count($afterlist) > 0) {
		$afterstring = implode("\n", $afterlist);
	}

	$outstring = str_replace("\n\n", "\n", "{$outstring}\n{$startstring}\n{$startcomment}\n{$string}\n{$endstring}\n$afterstring\n");

	lxuser_put_contents($username, $file, $outstring);
}

function lxfile_cp_if_not_exists($src, $dst)
{
	if (!lxfile_exists($dst)) {
		lxfile_cp($src, $dst);
	}
}

function db_set_value($table, $set, $where, $extra = null)
{
	$sq = new Sqlite(null, $table);

	if ($extra) {
		$extra = "AND $extra";
	}

	$count = db_get_count($table, $where);

	if ($count < 1) {
		$data = str_replace("AND", ",", $set . " " . $extra);

		$sq->rawQuery("INSERT INTO $table SET $data");
	} else {
		$sq->rawQuery("UPDATE $table SET $set WHERE $where $extra");
	}
}

function db_get_value($table, $nname, $var)
{
	$sql = new Sqlite(null, $table);

	if (is_array($var)) {
		$row = $sql->getRowsWhere("nname = '$nname'", $var);

		return $row[0];
	} else {
		$row = $sql->getRowsWhere("nname = '$nname'", array($var));

		return $row[0][$var];
	}
}

function db_get_count($table, $query)
{
	$sql = new Sqlite(null, $table);
	$count = $sql->getCountWhere($query);

	return $count;
}

function db_del_value($table, $nname)
{
	$sql = new Sqlite(null, $table);
	$del = $sql->delRow($nname, $value);

	return $del;
}

function monitor_load()
{
	$val = os_getLoadAvg(true);

	$rmt = lfile_get_unserialize("../etc/data/loadmonitor");
	$threshold = 0;

	if ($rmt) {
		$threshold = $rmt->load_threshold;
	}

	if (!$threshold) {
		$threshold = 20;
	}

	if ($val < $threshold) {
		return;
	}

	dprint("load $val is greater than $threshold\n");

	$myname = trim(`hostname`);

	$time = date("Y-m-d H:m");
	$mess = "Load on $myname is $val at $time which is greater than $threshold\n";
	$mess .= "\n ------- Top ---------- \n";
	$topout = lxshell_output("top -n 1 -b");
	$mess .= $topout;
	$rmt = new Remote();
	$rmt->cmd = "sendemail";
	$rmt->subject = "Load Warning on {$myname}";
	$rmt->message = $mess;
	send_to_master($rmt);
}

function log_load()
{
	$mess = os_getLoadAvg();

	if (!is_string($mess)) {
		$mess = var_export($mess, true);
	}
	$mess = trim($mess);

//	$rf = "__path_program_root/log/$file";

	$endstr = "\n";

	lfile_put_contents("/var/log/loadvg.log", time() . ' ' . @ date("H:i:M/d/Y") . ": $mess$endstr", FILE_APPEND);
}

function lxGetTimeFromString($line)
{
	///2006-03-10 07:00:01
	$line = trimSpaces($line);
	$list = explode(" ", $line);

	return $list[0];
}

function recursively_get_file($dir, $file)
{
	if (lxfile_exists("$dir/$file")) {
		return "$dir/$file";
	}

	$list = lscandir_without_dot($dir);

	if (!$list) {
		return null;
	}

	foreach ($list as $l) {
		if (lxfile_exists("$dir/$l/$file")) {
			return "$dir/$l/$file";
		}
	}

	return recursively_get_file("$dir/$l", $file);
}

function get_com_ob($obj)
{
	$ob = new Remote();
	$ob->com_object = $obj;

	return $ob;
}

function make_hidden_if_one($dlist)
{
	if (count($dlist) === 1) {
		return array('h', getFirstFromList($dlist));
	}

	return array('s', $dlist);
}

function addtoEtcHost($request, $ip)
{
//	$iplist = os_get_allips();
//	$ip = $iplist[0];

	$comment = "added by kloxo dnsless preview";
	lfile_put_contents("/etc/hosts", "$ip $request #$comment\n", FILE_APPEND);
}

function fill_string($string, $num = 33)
{
	for ($i = strlen($string); $i < $num; $i++) {
		$string .= ".";
	}

	return $string;
}

function removeFromEtcHost($request)
{
	$comment = "added by kloxo dnsless preview";
	$list = lfile_trim("/etc/hosts");
	$nlist = null;

	foreach ($list as $l) {
		if (csa($l, "$request #$comment")) {
			continue;
		}

		$nlist[] = $l;
	}

	$out = implode("\n", $nlist);
	lfile_put_contents("/etc/hosts", "$out\n");
}

function find_php_version()
{
	$t = explode(".", getPhpVersion());
	$ret = $t[0] . "." . $t[1];

	return $ret;
}

function createHtpasswordFile($object, $sdir, $list)
{
	$dir = "__path_httpd_root/{$object->main->getParentName()}/$sdir/";
	$loc = $object->main->directory;
	$file = get_file_from_path($loc);
	$dirfile = "$dir/$file";

	if (!lxfile_exists($dir)) {
		lxfile_mkdir($dir);
		lxfile_unix_chown($dir, $object->main->__var_username);
	}

	$fstr = null;

	foreach ($list as $k => $p) {
		$cr = crypt($p, '$1$'.randomString(8).'$');
		$fstr .= "$k:$cr\n";
	}

	dprint($fstr);

	lfile_write_content($dirfile, $fstr, $object->main->__var_username);
	lxfile_unix_chmod($dirfile, "0755");
}

function get_file_from_path($path)
{
	return str_replace("/", "_", "slash_$path");
}

function get_total_files_in_directory($dir)
{
	dprint("$dir\n");

	$dir = expand_real_root($dir);
	$list = lscandir_without_dot($dir);

	return count($list);
}

function convert_favorite()
{
	lxshell_php("../bin/common/favoriteconvert.php");
}

function fix_meta_character($v)
{
	for ($i = 0; $i < strlen($v); $i++) {
		if (ord($v[$i]) > 128) {
			$nv[] = strtolower(urlencode($v[$i]));
		} else {
			$nv[] = $v[$i];
		}
	}

	return implode("", $nv);
}

function changeDriver($server, $class, $pgm)
{
	global $sgbl;

	// Temporary hack. Somehow mysql doesnt' work in the backend.

	lxshell_return($sgbl->__path_php_path, "../bin/common/setdriver.php", "--server={$server}", "--class={$class}", "--driver={$pgm}");

	return;
}

function changeDriverFunc($server, $class, $pgm)
{
	global $login;

	$server = $login->getFromList('pserver', $server);

//	$os = $server->ostype;
//	include "../file/driver/$os.inc";

	include "../file/driver/rhel.inc";

	if (is_array($driver[$class])) {
		if (!array_search_bool($pgm, $driver[$class])) {
			$str = "'" . implode("', '", $driver[$class]) . "'";
			print("\nAvailable drivers for '{$class}': '{$str}'\n");

			return;
		}
	} else {
		if ($driver[$class] !== $pgm) {
			print("\nAvailable driver for '{$class}': '{$driver[$class]}'\n");

			return;
		}
	}

	$dr = $server->getObject('driver');

	$v = "pg_{$class}";
	$dr->driver_b->$v = $pgm;

	$dr->setUpdateSubaction();

	$dr->write();

	print("Successfully changed driver for '{$class}' on '{$server->nname}' to '{$pgm}'\n");
}

function slave_get_db_pass()
{
//	global $login;

//	$rmt = rl_exec_get('localhost', $login->syncserver, 'lfile_get_unserialize', array('../etc/slavedb/dbadmin'));

	$rmt = lfile_get_unserialize("../etc/slavedb/dbadmin");

	return $rmt->data['mysql']['dbpassword'];
}

function slave_get_driver($class)
{
//	global $login;

//	$rmt = rl_exec_get('localhost', $login->syncserver, 'lfile_get_unserialize', array('../etc/slavedb/driver'));

	$rmt = lfile_get_unserialize("../etc/slavedb/driver");

	return $rmt->data[$class];
}

function slave_get_db_contactemail()
{
//	global $login;

//	$rmt = rl_exec_get('localhost', $login->syncserver, 'lfile_get_unserialize', array('../etc/slavedb/contactemail'));

	$rmt = lfile_get_unserialize("../etc/slavedb/contactemail");

	return $rmt->data['admin']['contactemail'];
}

function PreparePowerdnsDb($nolog = null)
{
	log_cleanup("Prepare PowerDNS database", $nolog);

	log_cleanup("- Install MySQL and Geo Backend", $nolog);

	$pass = slave_get_db_pass();
	$user = "root";
	$host = "localhost";

	$link = new mysqli($host, $user, $pass);

	if (!$link) {
		log_cleanup("- Mysql root password incorrect", $nolog);

		exit;
	}

	$pstring = null;

	if ($pass) {
		$pstring = "-p\"$pass\"";
	}

	log_cleanup("- Fix MySQL commands in import files", $nolog);

	$pdnspath = "/opt/configs/pdns";

	exec("mysql -f -u root {$pstring} < {$pdnspath}/tpl/pdns.sql >/dev/null 2>&1");

	$sfile = getLinkCustomfile("{$pdnspath}/etc/conf", "pdns.conf");
	$tfile = "/etc/pdns/pdns.conf";

	$content = file_get_contents($sfile);

	log_cleanup("- Generate password", $nolog);
	$pass = randomString(8);

	$link->query("GRANT ALL ON powerdns.* TO powerdns@localhost IDENTIFIED BY '{$pass}'");
	$link->query("flush privileges");

	$content = str_replace("gmysql-password=powerdns", "gmysql-password={$pass}", $content);

	log_cleanup("- Add Password to configuration file", $nolog);

	file_put_contents($tfile, $content);
}

function PrepareMyDnsDb($nolog = null)
{
	log_cleanup("Prepare MyDns database", $nolog);

	$pass = slave_get_db_pass();
	$user = "root";
	$host = "localhost";

	$link = new mysqli($host, $user, $pass);

	if (!$link) {
		log_cleanup("- Mysql root password incorrect", $nolog);

		exit;
	}

	$pstring = null;

	if ($pass) {
		$pstring = "-p\"$pass\"";
	}

	log_cleanup("- Fix MySQL commands in import files", $nolog);

	$mydnspath = "/opt/configs/mydns";

	exec(" mysqladmin -u root {$pstring} create mydns 2>&1");

	$sfile = getLinkCustomfile("{$mydnspath}/etc/conf", "mydns.conf");
	$tfile = "/etc/mydns.conf";

	$content = file_get_contents($sfile);

	log_cleanup("- Generate password", $nolog);
	$pass = randomString(8);

	$link->query("GRANT ALL ON mydns.* TO mydns@localhost IDENTIFIED BY '{$pass}'");
	$link->query("flush privileges");

	$content = str_replace("db-password = mydns", "db-password = {$pass}", $content);

	log_cleanup("- Add Password to configuration file", $nolog);

	file_put_contents($tfile, $content);

	$mydns = null;

	if (file_exists("/opt/mydns/usr/sbin/mydns-mysql")) {
		$mydns = "/opt/mydns/usr/sbin/mydns-mysql";
	} elseif (file_exists("/usr/sbin/mydns-mysql")) {
		$mydns = "/usr/sbin/mydns-mysql";
	}

	if ($mydns) {
		exec("{$mydns} --create-tables | mysql -u mydns -p'{$pass}'");
	}
}

function run_mail_to_ticket()
{
	global $sgbl, $login;

	if (!$sgbl->is_this_master()) {
		return;
	}

	if (!$login) {
		initProgram('admin');
	}
	$ob = $login->getObject('ticketconfig');

	if (!$ob->isOn('mail_enable')) {
		return;
	}

	$portstring = null;
	$sslstring = null;
	if ($ob->isOn('mail_ssl_flag')) {
		$portstring = "and port 995";
		$sslstring = "with ssl";
	}

	$string = <<<FTC
set postmaster "postmaster"
set bouncemail
set properties ""
poll $ob->mail_server with proto POP3 $portstring user '$ob->mail_account' password '$ob->mail_password' is root here mda "lxphp.exe ../bin/common/mailtoticket.php" options fetchall $sslstring
FTC;

	$tmp = lx_tmp_file("fetch");

	lfile_put_contents($tmp, $string);

	lxfile_generic_chown($tmp, "root:root");
	lxfile_generic_chmod($tmp, "0710");

//	exec("pkill -f fetchmail");
//	sleep(10);
	exec_with_all_closed("fetchmail -d0 -e 15 -f $tmp; rm -rf $tmp");
//	sleep(20);
//	lunlink($tmp);
}

function send_system_monitor_message_to_admin($prog)
{
	$hst = trim(`hostname`);
	$dt = @ date('M-d h:i');
	$mess = "Host: $hst\nDate: $dt\n$prog\n\n\n";
	$rmt = new Remote();
	$rmt->cmd = "sendemail";
	$rmt->subject = "System Monitor on $hst";
	$rmt->message = $mess;
	send_to_master($rmt);

}

function check_if_port_on($port)
{
	$list = explode("||", $port);

	$ret = false;

	foreach ($list as $k => $v) {
		if (strpos($v, '.sock') !== false) {
			// unix socket -> /var/lib/mysql/mysql.sock for mysql
			// $socket = fsockopen('unix://{$v}', '-1', $errno, $errstr, 5);
			$socket = fsockopen('unix://{$v}', '-1', $errno, $errstr);

			if ($socket) {
				fclose($socket);

				$ret = true;

			}
		} elseif (strpos($v, '.pid') !== false) {
			if (filesize($v) !== 0) {
				$ret = true;
			}
		} elseif (strpos($v, ' status') !== false) {
			exec($v, $out, $ret);

		//	if (strpos($out[0], '(pid ') !== false) {
		//	if (strpos($out[0], 'running') !== false) {
			if (count($out) > 0) {
				$ret = true;
			}
		} elseif (strpos($v, 'pgrep') !== false) {
			exec($v, $out, $ret);

			if (count($out) > 0) {
				return true;
			}
		} else {
			// standard port -> 3306 for mysql
			$socket = fsockopen('127.0.0.1', $v, $errno, $errstr, 5);

			if ($socket) {
				fclose($socket);

				$ret = true;
			}

		}
	}

	return $ret;
}

function EasyinstallerPHP($var, $cmd)
{
	// TODO LxCenter: The created dir and file should be owned by the user
	global $sgbl;

	$domain = $var['domain'];
	$appname = $var['appname'];

	lxfile_mkdir("/home/httpd/$domain/httpdocs/__easyinstallerlog");
	$i = 0;

	while (1) {
		$file = "/home/httpd/$domain/httpdocs/__easyinstallerlog/$appname$i.html";
		if (!lxfile_exists($file)) {
			break;
		}
		$i++;
	}

	if ($sgbl->dbg > 0) {
		//	$cmd = "$cmd | elinks -no-home 1 -dump ";
		$cmd = "php $cmd | lynx -stdin -dump ";
	} else {
		$cmd = "php $cmd > $file";
	}
	exec($cmd);
	dprint("\n*************************************************************************\n");

}

function validate_ipaddress($name, $ret = null)
{
	global $login;

	// Validates both ipv4 and ipv6
	if (!preg_match('/^(?:(?>(?>([a-f0-9]{1,4})(?>:(?1)){7})|(?>(?!(?:.*[a-f0-9](?>:|$)){8,})((?1)(?>:' .
			'(?1)){0,6})?::(?2)?))|(?>(?>(?>(?1)(?>:(?1)){5}:)|(?>(?!(?:.*[a-f0-9]:){6,})((?1)(?>:(?1)){0,4})?:' .
			':(?>(?3):)?))?(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]?[0-9])(?>\.(?4)){3}))$/iD', $name)
	) {
		if ($ret) {
			return false;
		} else {
			throw new lxException($login->getThrow('invalid_ip_address'), '', $name);
		}
	} else {
		return true;
	}
}

function full_validate_ipaddress($ip, $variable = 'ipaddress')
{
	global $login, $global_dontlogshell;

	$global_dontlogshell = true;

	validate_ipaddress($ip);

	$ret = lxshell_return("ping", "-n", "-c", "1", "-w", "5", $ip);

	if (!$ret) {
		throw new lxException($login->getThrow("some_other_host_uses_this_ip"), $ip);
	}

	$global_dontlogshell = false;
}


function validate_domain_name($name, $bypass = null)
{
	global $sgbl, $login;

	if (!$bypass) {
		if ($name === 'lxlabs.com' || $name === 'lxcenter.org' || $name === 'kloxong.org') {
			if (!$sgbl->isDebug()) {
				throw new lxException($login->getThrow('can_not_be_added'), '', $name);
			}
		}
	}

	if (csb($name, "www.")) {
		throw new lxException($login->getThrow('add_without_www'), '', $name);
	}

	if (!preg_match('/^([a-z0-9]([a-z0-9-]{0,61}[a-z0-9])?\.)+(([a-z]{2,16})|(xn--[a-z0-9]{4,14}))$/i', $name)) {
		throw new lxException($login->getThrow('invalid_domain_name'), '', $name);
	}

	if (strlen($name) > 255) {
		throw new lxException($login->getThrow('more_than_255_chars'), '', $name);
	}
}

function validate_prefix_domain($name, $bypass = null)
{
	global $login;

	if (preg_match('/^(webmail\.|mail\.|lists\.|stats\.|cp\.|www\.|default\.+)(.*)/i', $name)) {
		throw new lxException($login->getThrow('not_permit_as_subdomain'), '', $name);
	}
}

function validate_hostname_name($name, $bypass = null)
{
	global $login;

	if (!preg_match('/^([0-9a-z_]{1,1}[0-9a-z\_\-\.]{0,126}[0-9a-z]{0,1})$/i', $name) && $name != "__base__") {
		throw new lxException($login->getThrow('invalid_subdomain'), '', $name);
	}

	if (strlen($name) > 128) {
		throw new lxException($login->getThrow('more_than_128_chars'), '', $name);
	}
}

function validate_server_alias($name, $bypass = null)
{
	global $login;

	// MR -- enough * for all subdomain!
	if (!preg_match('/^([0-9a-z_]{1,1}[0-9a-z\_\-\.]{0,126}[0-9a-z]{0,1})$/i', $name) && $name != "*") {
		throw new lxException($login->getThrow('invalid_subdomain'), '', $name);
	}

	if (strlen($name) > 128) {
		throw new lxException($login->getThrow('more_than_128_chars'), '', $name);
	}
}

function validate_client_name($name)
{
	global $login;

	// MR -- Centos using max length to 31 chars; pure-ftpd need no more then 32
	if (!preg_match('/^([a-z]){1,1}([_a-z0-9]){0,29}([a-z0-9]){1,1}$/', $name)) {
		throw new lxException($login->getThrow('invalid_client_name'), '', $name);
	}
}

function validate_database_name($name)
{
	global $login;

	if (!preg_match('/^([a-z0-9\_]){1,63}([a-z0-9]){1,1}$/', $name)) {
		throw new lxException($login->getThrow('invalid_database_name'), '', $name);
	}
}

function validate_password($name)
{
	global $login;

	if (!preg_match('/^([a-zA-Z0-9\!\@\#\$\%\&\*\?\_\-\.]){8,64}$/', $name)) {
		throw new lxException($login->getThrow('invalid_password'), '', $name);
	}
}

function validate_domain_owned($name)
{
	global $login;

	// MR -- idn_to_ascii only work in php 5.3.0+
//	if (checkdnsrr(idn_to_ascii($name), "MX")) {
	if (checkdnsrr($name, "MX")) {
		throw new lxException($login->getThrow('domain_is_already_owned'), '', $name);
	}
}

function validate_docroot($docroot)
{
	global $login;

	///#656 When adding a subdomain, the Document Root field is not being validated
	if (csa($docroot, " /")) {
		throw new lxException($login->getThrow("document_root_may_not_contain_spaces"), '', $docroot);
	} else {
		$domain_validation = str_split($docroot);
		$domain_validation_num = strlen($docroot) - 1;

		if ($domain_validation[$domain_validation_num] == " ") {
			throw new lxException($login->getThrow("document_root_may_not_contain_spaces"), '', $docroot);
		}

		if (strpos($docroot, "..") !== false) {
			throw new lxException($login->getThrow("document_root_may_not_contain_doubledots"), '', $docroot);
		}

		if (strpos($docroot, "./") !== false) {
			throw new lxException($login->getThrow("document_root_may_not_contain_dotslash"), '', $docroot);
		}

		if (strpos($docroot, "/.") !== false) {
			throw new lxException($login->getThrow("document_root_may_not_contain_slashdot"), '', $docroot);
		}

		if (strpos($docroot, "~") !== false) {
			throw new lxException($login->getThrow("document_root_may_not_contain_tilde"), '', $docroot);
		}
	}
}

function validate_filename($filename)
{
	global $login;

	if (!preg_match('/[^a-zA-Z0-9-_\.]$/', $filename)) {
		throw new lxException($login->getThrow('invalid_filename'), '', $filename);
	}

}

// MR -- as the same as validate_client_name except throw message
function validate_plan_name($name)
{
	global $login;

	if (!preg_match('/^([a-z]){1,1}([_a-z0-9]){0,29}([a-z0-9]){1,1}$/', $name)) {
		throw new lxException($login->getThrow('invalid_plan_name'), '', $name);
	}
}


function execEasyinstallerPhp($domain, $appname, $cmd)
{
	// TODO LxCenter: The created dir and file should be owned by the user
	global $sgbl;

	lxfile_mkdir("/home/httpd/$domain/httpdocs/__easyinstallerlog");
	$i = 0;

	while (1) {
		$file = "/home/httpd/$domain/httpdocs/__easyinstallerlog/$appname$i.html";

		if (!lxfile_exists($file)) {
			break;
		}

		$i++;
	}

	if ($sgbl->dbg > 0) {
		$cmd = "$cmd | lynx -stdin -dump ";
	} else {
		$cmd = "$cmd > $file";
	}

	exec($cmd);

	dprint("\n*************************************************************************\n");
}

function update_self()
{
	exec_with_all_closed("lxphp.exe ../bin/update.php");
}

function get_name_without_template($name)
{
	if (cse($name, "template")) {
		return strtil($name, "template");
	} else {
		return $name;
	}
}

function check_smtp_port()
{
	global $sgbl;

	if ($sgbl->is_this_slave()) {
		return;
	}

	$sq = new Sqlite(null, 'client');

	if (!check_if_port_on(25)) {
		$sq->rawQuery("update client set smtp_server_flag = 'off' where nname = 'admin'");
	} else {
		$sq->rawQuery("update client set smtp_server_flag = 'on' where nname = 'admin'");
	}
}

function getRealPidlist($arg)
{
	global $global_dontlogshell;

	$global_dontlogshell = true;

	$nlist = null;
	$list = lxshell_output("pgrep", "-f", $arg);

	$ret = lxshell_return("vzlist", "-a");

	$in_openvz_node = false;

	if (!$ret) {
		$in_openvz_node = true;
	}

	$list = explode("\n", $list);

	foreach ($list as $l) {
		$l = trim($l);

		if (!$l) {
			continue;
		}

		if (posix_getpid() == $l) {
			continue;
		}

		if ($in_openvz_node) {
			$res = lxshell_output("sh", "../bin/common/misc/vzpid.sh", $l);
			$res = trim($res);

			if ($res != "0" && $res != "") {
				continue;
			}
		}

		$nlist[] = $l;
	}

	return $nlist;

}

function get_double_hex($i)
{
	$hex = dechex($i);

	if (strlen($hex) === 1) {
		$hex = "0$hex";
	}

	return $hex;
}

function merge_array_object_not_deleted($array, $object)
{
	$ret = null;

	if (is_array($array)) {
		foreach ($array as $a) {
			if ($a['nname'] !== $object->nname) {
				$ret[] = $a;
			}
		}
	} else {
		if ($array['nname'] !== $object->nname) {
			$ret[] = $array;
		}
	}

	if ($object->isDeleted()) {
		return $ret;
	}

	foreach ($object as $k => $v) {
		if (!is_object($v)) {
			$nl[$k] = $v;
		}
	}

	$ret[] = $nl;

	return $ret;
}

function call_with_flag($func)
{
	$file = "__path_program_etc/flag/$func.flg";

	if (lxfile_exists($file)) {
		return;
	}

	// MR --- the problem is no /usr/local/lxlabs/kloxo/etc/flag dir in slave
	// need more investigate about it that no flag dir in slave
	// meanwhile use this logic

	$path = "../flag";

	call_user_func($func);

	if (lxfile_exists($path)) {
		lxfile_touch($file);
	}
}


function check_disable_admin($cgi_clientname)
{
	$sq = new Sqlite(null, 'general');
	$list = $sq->getRowsWhere("nname = 'admin'", array("disable_admin"));
	$val = $list[0]['disable_admin'];

	if ($cgi_clientname === 'admin' && $val === 'on') {
		return true;
	}

	return false;
}

function check_if_many_server()
{
	$sql = new Sqlite(null, "pserver");
	$res = $sql->getTable(array('nname'));
	$rs = get_namelist_from_arraylist($res);

	if (count($rs) > 1) {
		return true;
	}

	return false;
}

function get_all_client()
{
	$sql = new Sqlite(null, "client");
	$res = $sql->getTable(array('nname'));
	$rs = get_namelist_from_arraylist($res);

	return $rs;
}

function get_all_pserver()
{
	$sql = new Sqlite(null, "pserver");
	$res = $sql->getTable(array('nname'));
	$rs = get_namelist_from_arraylist($res);

	return $rs;
}

function change_config($file, $var, $val)
{
	$list = lfile_trim($file);
	$match = false;

	foreach ($list as &$__l) {
		if (csb($__l, "$var=") || csb($__l, "$var =")) {
			$__l = "$var=\"$val\"";
			$match = true;
		}
	}

	if (!$match) {
		$list[] = "$var=\"$val\"";
	}

	lfile_put_contents($file, implode("\n", $list));
}

function removeQuotes($val)
{
	$val = strfrom($val, '"');
	$val = strtil($val, '"');

	return $val;
}

function checkExistingUpdate()
{
	exit_if_another_instance_running();
}

function listFile($path)
{
	global $global_list_path;

	if (lis_dir($path)) {
		return;
	}

	$path = strfrom($path, "/usr/share/zoneinfo/");
	$path = trim($path, "/");

	if (ctype_lower($path[0])) {
		return;
	}

	$global_list_path[] = $path;
}

function execCom($ob, $func, $exception)
{
	try {
		$ret = $ob->$func();
	} catch (Exception $e) {
		if (!$exception) {
			return null;
		}

		throw new lxException($exception, '');
	}

	return $ret;
}


function fix_vgname($vgname)
{
	if (csa($vgname, "lvm:")) {
		$vgname = strfrom($vgname, "lvm:");
	}

	return $vgname;
}

function restart_mysql()
{
	if (isServiceExists('mysqld')) {
		exec_with_all_closed("service mysqld restart >/dev/null 2>&1");
	} else {
		exec_with_all_closed("service mysql restart >/dev/null 2>&1");
	}
}

function restart_service($service)
{
	exec_with_all_closed("service $service restart >/dev/null 2>&1");
}

function remove_old_serve_file()
{
	global $sgbl;

	log_log("remove_oldfile", "Remove old files");
	$list = lscandir_without_dot("{$sgbl->__path_serverfile}/tmp");

	foreach ($list as $l) {
		remove_if_older_than_a_day("{$sgbl->__path_serverfile}/tmp/$l");
	}
}

function fix_flag_variable($table, $flagvariable)
{
	$sq = new Sqlite(null, $table);
	$sq->rawQuery("update $table set $flagvariable = 'done' where $flagvariable = 'doing'");
}

function upload_file_to_db($dbtype, $dbhost, $dbuser, $dbpassword, $dbname, $file)
{
	mysql_upload_file_to_db($dbhost, $dbuser, $dbpassword, $dbname, $file);
}

function calculateRealTotal($inout)
{
	foreach ($inout as $k => $v) {
		$sum = 0;

		foreach ($v as $kk => $vv) {
			$sum += $vv;
		}

		$realtotalinout[$k] = $sum;
	}

	return $realtotalinout;
}

function mysql_upload_file_to_db($dbhost, $dbuser, $dbpassword, $dbname, $file)
{
	global $login;

	$rs = new mysqli($dbhost, $dbuser, $dbpassword);

	if (!$rs) {
		throw new lxException($login->getThrow('no_mysql_connection_while_uploading_file'));
	}

	$rs->select_db($dbname);

	$res = lfile_get_contents($file);

	$res = $rs->query($res);

	if (!$res) {
		throw new lxException($login->getThrow('no_mysql_connection_while_uploading_file'));
	}
}

function testAllServersWithMessage()
{
	print("Test All servers.... ");

	try {
		testAllServers();
	} catch (Exception $e) {
		print("Connect to these servers failed due to....\n");
		print_r($e->value);

		return false;
	}

	print("Done....\n");

	return true;
}


function testAllServers()
{
	$sq = new Sqlite(null, 'pserver');
	$res = $sq->getTable(array('nname'));
	$nlist = get_namelist_from_arraylist($res);

	$flist = null;

	foreach ($nlist as $l) {
		try {
			rl_exec_get(null, $l, 'test_remote_func', null);
		} catch (Exception $e) {
			$flist[$l] = $e->getMessage();
		}
	}

	if ($flist) {
		throw new lxException($e->getMessage(), '', $flist);
	}
}

function exec_with_all_closed($cmd)
{
	global $sgbl;

	$string = null;

	log_shell("Closed Exec {$sgbl->__path_program_root}/cexe/closeallinput '{$cmd}' >/dev/null 2>&1 &");
	chmod("{$sgbl->__path_program_root}/cexe/closeallinput", 0755);
	exec("{$sgbl->__path_program_root}/cexe/closeallinput '{$cmd}' >/dev/null 2>&1 &");
}

function exec_with_all_closed_output($cmd)
{
	global $sgbl;

	chmod("{$sgbl->__path_program_root}/cexe/closeallinput", 0755);
	$res = shell_exec("{$sgbl->__path_program_root}/cexe/closeallinput '{$cmd}' 2>/dev/null");
	log_shell("Closed Exec output: {$res} :  {$sgbl->__path_program_root}/cexe/closeallinput '{$cmd}'");

	return trim($res);
}

// Convert Com to Php Array.
function convertCOMarray($array)
{
	foreach ($array as $v) {
		$res[] = "$v";
	}

	return $res;
}

function mycount($olist)
{
	return count($olist);
}

function do_actionlog($login, $object, $action, $subaction)
{
	global $gbl;

	if ($subaction === 'customermode') {
		return;
	}
	if (csb($subaction, 'boxpos')) {
		return;
	}

//	if (!$object->is__table('domain') && !$object->is__table('client') && !$object->is__table('vps')) {
	if ($object->getClass() !== 'domain' && $object->getClass() !== 'client' && $object->getClass() !== 'vps') {
		return;
	}

	$d = microtime(true);
	$alog = new ActionLog(null, null, $d);
	$res['login'] = $login->nname;
	$res['loginclname'] = $login->getClName();
	$aux = $login->getAuxiliaryId();
	$res['auxiliary_id'] = $aux;
	$res['ipaddress'] = $gbl->c_session->ip_address;
	$res['class'] = $object->get__table();
	$res['objectname'] = $object->nname;
	$res['action'] = $action;
	$res['subaction'] = $subaction;
	$res['ddate'] = time();
	$alog->create($res);
	$alog->write();
}

function validate_email($email)
{
	$regexp = "/^([\w\!\#$\%\&\'\*\+\-\/\=\?\^\`{\|\}\~]+\.)*[\w\!\#$\%\&\'\*\+\-\/\=\?\^\`{\|\}\~]+@" . "((((([a-z0-9]{1}[a-z0-9\-]{0,62}[a-z0-9]{1})|[a-z])\.)+[a-z]{2,16})|(\d{1,3}\.){3}\d{1,3}(\:\d{1,5})?)$/i";

	if (!preg_match($regexp, $email)) {
		return false;
	}

	return true;
}

function make_sure_directory_is_lxlabs($file)
{

}

function addToUtmp($ses, $dbaction)
{
	$nname = implode('_', array($ses->nname, $ses->parent_clname));
	$nname = str_replace(array(",", ":"), "_", $nname);
	$utmp = new Utmp(null, null, $nname);

	if ($dbaction === 'add') {
		$utmp->setFromObject($ses);
		$utmp->dbaction = 'add';
		$utmp->ssession_name = $ses->nname;
		$utmp->logouttime = 'Still Logged';
		$utmp->logoutreason = '-';
	} else {
		$utmp->get();
		$utmp->timeout = $ses->timeout;
		$utmp->setUpdateSubaction();
	}

	$utmp->write();
}

function getRealhostName($name)
{
	if ($name !== 'localhost') {
		return $name;
	}

	$sq = new Sqlite(null, 'pserver');
	$res = $sq->getRowsWhere("nname = '$name'", array('realhostname'));

	if (!$res[0]['realhostname']) {
		return 'localhost';
	}

	return $res[0]['realhostname'];
}

// This is mainly used for filserver. If the remote system is localhost, then return localhost itself,
// which means the whole thing is local. Otherwise return one of the ips that can be used to communicate with our server.
// The $v is actually the remote server that we are sending to.
function getOneIPForLocalhost($v)
{
	if (isLocalhost($v)) {
		return 'localhost';
	}

	if (is_secondary_master()) {
		$list = os_get_allips();
		$ip = getFirstFromList($list);

		return $ip;
	}

	return getFQDNforServer('localhost');
}

function getInternalNetworkIp($v)
{
	$sql = new Sqlite(null, "pserver");

	$server = $sql->rawQuery("select * from pserver where nname = '$v'");

	$servername = trim($server[0]['internalnetworkip']);

	if ($servername) {
		return $servername;
	}

	return getFQDNforServer($v);
}

function get_form_variable_name($descr)
{
	return getNthToken($descr, 1);
}

function is_disabled($var)
{
	return ($var === '--Disabled--');
}

function is_disabled_or_null($var)
{
	return (!$var || $var === '--Disabled--');
}

function getFQDNforServer($v)
{
	$sql = new Sqlite(null, "pserver");

	$server = $sql->rawQuery("select * from pserver where nname = '$v'");

	$servername = trim($server[0]['realhostname']);

	if ($servername) {
		return $servername;
	}

	return getOneIPForServer($v);
}

function getOneIPForServer($v)
{
	$sql = new Sqlite(null, "pserver");
	$ipaddr = $sql->rawQuery("select * from ipaddress where syncserver = '$v'");

	foreach ($ipaddr as $ip) {
		if (!csb($ip['ipaddr'], "127") && !csb($ip['ipaddr'], "172") && !csb($ip['ipaddr'], "192.168")) {
			return $ip['ipaddr'];
		}
	}

	// Try once more if no non-local ips were found...

	foreach ($ipaddr as $ip) {
		if (!csb($ip['ipaddr'], "127")) {
			return $ip['ipaddr'];
		}
	}

	return null;
}

function zip_to_fileserv($dir, $fillist, $logto = null)
{
	$file = do_zip_to_fileserv('zip', array($dir, $fillist), $logto);

	return cp_fileserv($file);
}

function tar_to_fileserv($dir, $fillist, $logto = null)
{
	$file = do_zip_to_fileserv('tar', array($dir, $fillist), $logto);

	return cp_fileserv($file);
}

function tgz_to_fileserv($dir, $fillist, $logto = null)
{
	$file = do_zip_to_fileserv('tgz', array($dir, $fillist), $logto);

	return cp_fileserv($file);
}

function tbz2_to_fileserv($dir, $fillist, $logto = null)
{
	$file = do_zip_to_fileserv('tbz2', array($dir, $fillist), $logto);

	return cp_fileserv($file);
}

function txz_to_fileserv($dir, $fillist, $logto = null)
{
	$file = do_zip_to_fileserv('txz', array($dir, $fillist), $logto);

	return cp_fileserv($file);
}

function p7z_to_fileserv($dir, $fillist, $logto = null)
{
	$file = do_zip_to_fileserv('p7z', array($dir, $fillist), $logto);

	return cp_fileserv($file);
}

function rar_to_fileserv($dir, $fillist, $logto = null)
{
	$file = do_zip_to_fileserv('rar', array($dir, $fillist), $logto);

	return cp_fileserv($file);
}

function get_admin_license_var()
{
	$list = get_license_resource();

	foreach ($list as &$__l) {
		$__l = "used_q_$__l";
	}

	$sq = new Sqlite(null, 'client');
	$res = $sq->getRowsWhere("nname = 'admin'", $list);

	return $res[0];
}

function get_license_resource()
{
	global $sgbl;

	if ($sgbl->isKloxo()) {
		return array("maindomain_num");
	} else {
		return array("vps_num");
	}
}

function cp_fileserv_list($root, $list)
{
	foreach ($list as $l) {
		$fp = "$root/$l";
		$res[$fp] = cp_fileserv($fp);
	}

	return $res;
}

function cp_fileserv($file)
{
	global $sgbl;

	$path = $sgbl->__path_serverfile;

	lxfile_mkdir($path);
	lxfile_generic_chown($path, "lxlabs:lxlabs");

	$file = expand_real_root($file);
	dprint("Fileserv copy file $file\n");

	if (is_dir($file)) {
		$list = lscandir_without_dot($file);
		$res = tar_to_fileserv($file, $list);
		$res['type'] = "dir";

		return $res;
	} else {
		$res['type'] = 'file';
	}

	$basebase = basename($file);
	$base = basename(ltempnam($sgbl->__path_serverfile, $basebase));
	$pass = md5($file . time());
	$ar = array('filename' => $file, 'password' => $pass);
	lfile_put_serialize("{$path}/$base", $ar);
	lxfile_generic_chown("{$path}/$base", "lxlabs");
	$res['file'] = $base;
	$res['pass'] = $pass;
//	$stat = llstat("{$path}/$base");
	$res['size'] = lxfile_size($file);

	return $res;
}

function do_zip_to_fileserv($type, $arg, $logto = null)
{
	global $sgbl, $login;

	$path = $sgbl->__path_serverfile;

	lxfile_mkdir("{$path}/tmp");
	lxfile_unix_chown_rec("{$path}", "lxlabs");

	$basebase = basename($arg[0]);

	$base = basename(ltempnam("{$sgbl->__path_serverfile}/tmp", $basebase));

	// Create the pass file now itself so that it isn't unwittingly created again.

	$vd = $arg[0];
	$list = $arg[1];

	switch ($type) {
		case 'zip':
			dprint("zipping $vd: " . $vd . " \n");
			$ret = lxshell_zip($vd, "{$path}/tmp/$base.tmp", $list);
			break;
		case 'tgz':
			dprint("tarring $vd: " . $vd . " \n");
			$ret = lxshell_tgz($vd, "{$path}/tmp/$base.tmp", $list);
			break;
		case 'tbz2':
			dprint("tarring $vd: " . $vd . " \n");
			$ret = lxshell_tbz2($vd, "{$path}/tmp/$base.tmp", $list);
			break;
		case 'txz':
			dprint("tarring $vd: " . $vd . " \n");
			$ret = lxshell_txz($vd, "{$path}/tmp/$base.tmp", $list);
			break;
		case 'p7z':
			dprint("p7zzing $vd: " . $vd . " \n");
			$ret = lxshell_p7z($vd, "{$path}/tmp/$base.tmp", $list);
			break;
		case 'rar':
			dprint("rarring $vd: " . $vd . " \n");
			$ret = lxshell_rar($vd, "{$path}/tmp/$base.tmp", $list);
			break;
		case 'tar':
		default:
			dprint("tarring $vd: " . $vd . " \n");
			$ret = lxshell_tar($vd, "{$path}/tmp/$base.tmp", $list);
			break;
	}

	lrename("{$path}/tmp/{$base}.tmp", "{$path}/tmp/{$base}");

	if ($ret) {
		if ($logto) {
			log_log($logto, "- Could not zip for '{$vd}'");
		} else {
			exec_with_all_closed("sh /script/load-wrapper >/dev/null 2>&1 &");
			throw new lxException($login->getThrow("could_not_zip_dir"), '', $vd);
		}
	} else {
		if ($logto) {
			log_log("backup", "- Succeeded zip for '{$vd}'");
		}
	}

	return "{$path}/tmp/{$base}";
}

function fileserv_unlink_if_tmp($file)
{
	global $sgbl;

	$base = dirname($file);

	if (expand_real_root($base) === expand_real_root("{$sgbl->__path_serverfile}/tmp")) {
		log_log("servfile", "Delete tmp servfile $file");
		lunlink($file);
	}
}

function getFromRemote($server, $filepass, $dt, $p)
{
	$bp = basename($p);

	if ($filepass['type'] === 'dir') {
		$tfile = lx_tmp_file("__path_tmp", "lx_$bp");
		getFromFileserv($server, $filepass, $tfile);
		lxfile_mkdir("$dt/$bp");
		lxshell_unzip_with_throw("$dt/$bp", $tfile);
		lunlink($tfile);
	} else {
		getFromFileserv($server, $filepass, "$dt/$bp");
	}
}

function exit_if_not_system_user()
{
	if (!os_isSelfSystemUser()) {
		print("Need to be system user\n");

		exit;
	}
}

function getFromFileserv($serv, $filepass, $copyto)
{
	doRealGetFromFileServ("file", $serv, $filepass, $copyto);
}


function printFromFileServ($serv, $filepass)
{
	doRealGetFromFileServ("fileprint", $serv, $filepass);
}

function doRealGetFromFileServ($cmd, $serv, $filepass, $copyto = null)
{
	global $sgbl;

	$file = $filepass['file'];
//	$pass = $filepass['pass'];
//	$size = $filepass['size'];
	$base = basename($file);

	$path = "{$sgbl->__path_serverfile}/{$base}";

	if ($serv === 'localhost') {
		$array = lfile_get_unserialize($path);
		$realfile = $array['filename'];
		log_log("servfile", "get local file $realfile");

		if (lxfile_exists($realfile) && lis_readable($realfile)) {
			lunlink($path);

			if ($cmd === 'fileprint') {
				slow_print($realfile);
			} else {
				lxfile_mkdir(dirname($copyto));
				lxfile_cp($realfile, $copyto);
			}

			fileserv_unlink_if_tmp($realfile);

			return;
		}
		if (os_isSelfSystemUser()) {
			log_log("servfile", "is System User, but can't access $realfile returning");
		} else {
			log_log("servfile", "is Not system user, can't access so will get $realfile through backend");
		}

	}

	$fd = null;

	if ($copyto) {
		lxfile_mkdir(dirname($copyto));
		$fd = fopen($copyto, "wb");

		if (!$fd) {
			log_log("servfile", "Could not write to $copyto... Returning.");

			return;
		}

		lxfile_generic_chmod($copyto, "0700");
	}

	doGetOrPrintFromFileServ($serv, $filepass, $cmd, $fd);

	if ($fd) {
		fclose($fd);
	}
}

function doGetOrPrintFromFileServ($serv, $filepass, $type, $fd)
{
	$file = $filepass['file'];
	$pass = $filepass['pass'];
	$size = $filepass['size'];

	$info = new Remote;
	$info->password = $pass;
	$info->filename = $file;
	log_log("servfile", "Start Get $serv $type $file $size");

	$val = base64_encode(serialize($info));
	$string = "__file::$val";

	$totalsize = send_to_some_stream_server($type, $size, $serv, $string, $fd);

	log_log("servfile", "Got $serv $type $file $size (Totalsize willbe +1) $totalsize");
}

function trimSpaces($val)
{
	$val = trim($val);
	$val = preg_replace("/\s+/", " ", $val);

	return $val;
}

function execRrdTraffic($filename, $tot, $inc, $out)
{
	global $global_dontlogshell;
//	global $global_shell_error, $global_shell_ret, $global_shell_out;

	$global_dontlogshell = true;

	$file = "__path_program_root/data/traffic/$filename.rrd";
	lxfile_mkdir("__path_program_root/data/traffic");

	if (!lxfile_exists($file)) {
		lxshell_return("rrdtool", 'create', $file, 'DS:total:ABSOLUTE:800:-1125000000:1125000000', 'DS:incoming:ABSOLUTE:800:-1125000000:1125000000', 'DS:outgoing:ABSOLUTE:800:-1125000000:1125000000', 'RRA:AVERAGE:0.5:1:600', 'RRA:AVERAGE:0.5:6:700', 'RRA:AVERAGE:0.5:24:775', 'RRA:AVERAGE:0.5:288:797');
	}

	lxshell_return("rrdtool", "update", $file, "N:$tot:$inc:$out");
}

function set_login_skin_to_feather()
{
	global $sgbl, $login;

	if (!$sgbl->isKloxo()) {
		return;
	}

	$obj = $login->getObject('sp_specialplay');
	$obj->specialplay_b->skin_name = 'feather';
	$obj->specialplay_b->skin_color = 'default';
	$obj->specialplay_b->icon_name = 'collage';
	$obj->specialplay_b->show_direction = 'vertical';
//	$obj->specialplay_b->skin_background = 'nature_004.jpg';
	$obj->specialplay_b->skin_background = '';
	$obj->specialplay_b->button_type = 'font';
	$obj->setUpdateSubaction();
	$obj->write();

	$obj = $login->getObject('sp_childspecialplay');
	$obj->specialplay_b->skin_name = 'feather';
	$obj->specialplay_b->skin_color = 'default';
	$obj->specialplay_b->icon_name = 'collage';
	$obj->specialplay_b->show_direction = 'vertical';
//	$obj->specialplay_b->skin_background = 'nature_004.jpg';
	$obj->specialplay_b->skin_background = '';
	$obj->specialplay_b->button_type = 'font';
	$obj->setUpdateSubaction();
	$obj->write();
}

function set_login_skin_to_simplicity()
{
	global $sgbl, $login;

	if (!$sgbl->isKloxo()) {
		return;
	}

	$obj = $login->getObject('sp_specialplay');
	$obj->specialplay_b->skin_name = 'simplicity';
	$obj->specialplay_b->skin_color = 'default';
	$obj->specialplay_b->icon_name = 'collage';
	$obj->specialplay_b->show_direction = 'vertical';
	$obj->specialplay_b->button_type = 'font';
//	$obj->specialplay_b->skin_background = 'nature_004.jpg';
	$obj->specialplay_b->skin_background = '';
	$obj->setUpdateSubaction();
	$obj->write();

	$obj = $login->getObject('sp_childspecialplay');
	$obj->specialplay_b->skin_name = 'simplicity';
	$obj->specialplay_b->skin_color = 'default';
	$obj->specialplay_b->icon_name = 'collage';
	$obj->specialplay_b->show_direction = 'vertical';
	$obj->specialplay_b->button_type = 'font';
//	$obj->specialplay_b->skin_background = 'nature_004.jpg';
	$obj->specialplay_b->skin_background = '';
	$obj->setUpdateSubaction();
	$obj->write();
}

function get_kloxo_port($type)
{
	global $sgbl;

	$port = db_get_value("general", "admin", "ser_portconfig_b");
	$port = unserialize(base64_decode($port));

	if ($type === 'ssl') {
		if (isset($port->sslport)) {
			$ret = $port->sslport;
		} else {
			$ret = $sgbl->__var_prog_ssl_port;
		}
	} else {
		if (isset($port->nonsslport)) {
			$ret = $port->nonsslport;
		} else {
			$ret = $sgbl->__var_prog_port;
		}
	}

	return $ret;
}

function execRrdSingle($name, $func, $filename, $tot)
{
	global $global_dontlogshell;
//	global $global_shell_error, $global_shell_ret, $global_shell_out;

	$global_dontlogshell = true;

	$tot = round($tot);
	$file = "__path_program_root/data/$name/$filename.rrd";
	lxfile_mkdir("__path_program_root/data/$name");

	if (!lxfile_exists($file)) {
		lxshell_return("rrdtool", 'create', $file, "DS:$name:$func:800:0:999999999999", 'RRA:AVERAGE:0.5:1:600', 'RRA:AVERAGE:0.5:6:700', 'RRA:AVERAGE:0.5:24:775', 'RRA:AVERAGE:0.5:288:797');
	}

	lxshell_return("rrdtool", "update", $file, "N:$tot");
}


function get_num_for_month($month)
{
	$list = array("", "jan", "feb", "mar", "apr", "may", "jun", "jul", "aug", "sep", "oct", "nov", "dec");

	return array_search(strtolower($month), $list);
}

function rrd_graph_single($type, $file, $time)
{
	global $login;

	global $global_dontlogshell;
	global $global_shell_error;

	$global_dontlogshell = true;

	$dir = strtilfirst($type, " ");
	$file = "__path_program_root/data/$dir/$file.rrd";
	$file = expand_real_root($file);
	$graphfile = ltempnam("/tmp", "lx_graph");

	if (!lxfile_exists($file)) {
		throw new lxException($login->getThrow("no_graph_data"), '', $file);
	}

	if ($time >= 7 * 24 * 3600) {
		$grid = 'HOUR:12:DAY:2:WEEK:8:0:%X';
	} else {
		if ($time >= 24 * 3600) {
			$grid = 'MINUTE:30:HOUR:2:HOUR:8:0:%X';
		} else {
			$grid = 'MINUTE:3:MINUTE:30:HOUR:1:0:%X';
		}
	}

	$ret = lxshell_return('rrdtool', 'graph', $graphfile, '--start', "-$time", '-w', '600', '-h', '200', '--x-grid', $grid, "--vertical-label=$type", "DEF:dss1=$file:$dir:AVERAGE", "LINE1:dss1#FF0000:$dir\\r");

	if ($ret) {
		exec_with_all_closed("sh /script/load-wrapper >/dev/null 2>&1 &");
		throw new lxException($login->getThrow("could_not_get_graph_data"), '', $global_shell_error);
	}

	$content = lfile_get_contents($graphfile);
	lunlink($graphfile);

	$global_dontlogshell = false;

	return $content;
}

function rrd_graph_vps($type, $file, $time)
{
	global $login;

	global $global_dontlogshell;
	global $global_shell_error;

	$global_dontlogshell = true;

	$file = "__path_program_root/data/$type/$file";
	$file = expand_real_root($file);
	$graphfile = ltempnam("/tmp", "lx_graph");

	if (!lxfile_exists($file)) {
		throw new lxException($login->getThrow("no_traffic_data"), '', $file);
	}

	if ($time >= 7 * 24 * 3600) {
		$grid = 'HOUR:12:DAY:2:WEEK:8:0:%X';
	} else {
		if ($time >= 24 * 3600) {
			$grid = 'MINUTE:30:HOUR:2:HOUR:8:0:%X';
		} else {
			$grid = 'MINUTE:3:MINUTE:30:HOUR:1:0:%X';
		}
	}

	switch ($type) {
		case "traffic":
			$ret = lxshell_return('rrdtool', 'graph', $graphfile, '--start', "-$time", '-w', '600', '-h', '200', '--x-grid', $grid, '--vertical-label=Bytes/s', "DEF:dss0=$file:total:AVERAGE", "DEF:dss1=$file:incoming:AVERAGE", "DEF:dss2=$file:outgoing:AVERAGE", 'LINE1:dss0#00FF00:Total traffic', 'LINE1:dss1#FF0000:In traffic\\r', 'LINE1:dss2#0000FF:Out traffic\\r');
			break;

		default:
			$ret = lxshell_return('rrdtool', 'graph', $graphfile, '--start', "-$time", '-w', '600', '-h', '200', '--x-grid', $grid, "--vertical-label=$type", "DEF:dss1=$file:$type:AVERAGE", "LINE1:dss1#FF0000:$type\\r");
			break;
	}

	if ($ret) {
		throw new lxException($login->getThrow("could_not_get_traffic_data"), '', $global_shell_error);
	}

	$content = lfile_get_contents($graphfile);
	lunlink($graphfile);

	$global_dontlogshell = false;

	return $content;
}

function rrd_graph_server($type, $list, $time)
{
	global $sgbl, $login;
	global $global_dontlogshell, $global_shell_error;

	$global_dontlogshell = true;

	$graphfile = ltempnam("/tmp", "lx_graph");

	$color = array("000000", "b54f6f", "00bb00", "a0ad00", "0090bf", "56a656", "00bbbf",
		"bfbfbf", "458325", "f04050", "a0b2c5", "cf0f00", "a070ad", "cf8085", "af93af",
		"90bb9f", "00d500", "00ff00", "aaffaa", "00ffff", "aa00ff", "ffff00", "aaff00",
		"faff00", "0aff00", "6aff00", "eaffa0", "abff0a", "afffaa", "deab3d", "333333",
		"894367", "234567", "fbdead", "fadec1", "fa3d9c", "f54398", "f278d3", "f512d3",
		"43f3f9", "f643f9");

	if ($time >= 7 * 24 * 3600) {
		$grid = 'HOUR:12:DAY:2:WEEK:8:0:%X';
	} else {
		if ($time >= 24 * 3600) {
			$grid = 'MINUTE:30:HOUR:2:HOUR:8:0:%X';
		} else {
			$grid = 'MINUTE:3:MINUTE:30:HOUR:1:0:%X';
		}
	}

	switch ($type) {
		case "traffic":
			$i = 0;

			foreach ($list as $k => $file) {
				$i++;
				$fullpath = "$sgbl->__path_program_root/data/$type/$file.rrd";

				if (!lxfile_exists($fullpath)) {
					continue;
				}

				$arg[] = "DEF:dss$i=$fullpath:total:AVERAGE";

				if (isset($color[$i])) {
					$arg[] = "LINE1:dss$i#$color[$i]:$k";
				} else {
					$arg[] = "LINE1:dss$i#000000:$k";
				}

			}

			$arglist = array('rrdtool', 'graph', $graphfile, '--start', "-$time", '-w', '600', '-h', '200', '--x-grid', $grid, '--vertical-label=Bytes/s');

			$arglist = lx_array_merge(array($arglist, $arg));

			$ret = call_user_func_array("lxshell_return", $arglist);

			break;

		default:
			$i = 0;

			foreach ($list as $k => $file) {
				$i++;
				$fullpath = "{$sgbl->__path_program_root}/data/{$type}/{$file}.rrd";
				$arg[] = "DEF:dss$i=$fullpath:$type:AVERAGE";

				if (isset($color[$i])) {
					$arg[] = "LINE1:dss$i#$color[$i]:$k";
				} else {
					$arg[] = "LINE1:dss$i#000000:$k";
				}

			}

			$arglist = array('rrdtool', 'graph', $graphfile, '--start', "-$time", '-w', '600', '-h', '200', '--x-grid', $grid, "--vertical-label=$type");
			$arglist = lx_array_merge(array($arglist, $arg));
			$ret = call_user_func_array("lxshell_return", $arglist);

			break;
	}

	if ($ret) {
		throw new lxException($login->getThrow("graph_generation_failed"), '', $global_shell_error);
	}

	$content = lfile_get_contents($graphfile);
	lunlink($graphfile);

	$global_dontlogshell = false;

	return $content;
}

function slow_print($file)
{
	$fp = lfopen($file, "rb");

	while (!feof($fp)) {
		print(fread($fp, 8092));
		flush();
		//	usleep(600 * 1000);
		//	sleep(1);
	}

	fclose($fp);
}

function createTempDir($dir, $name)
{
	global $login;

	$dir = expand_real_root($dir);
	$vd = tempnam($dir, $name);

	if (!$vd) {
		exec_with_all_closed("sh /script/load-wrapper >/dev/null 2>&1 &");
		throw new lxException($login->getThrow('could_not_create_tmp_dir'), '', $vd);
	}

	unlink($vd);
	mkdir($vd);
	lxfile_generic_chmod($vd, "0700");

	return $vd;
}

function getObjectFromFileWithThrow($file)
{
	global $login;

	$rem = unserialize(lfile_get_contents($file));

	if (!$rem) {
		throw new lxException($login->getThrow('corrupted_file'), '', $file);
	}

	return $rem;
}

function checkIfVariablesSetOr($p, &$param, $v, $list)
{
	global $login;

	foreach ($list as $l) {
		if (isset($p[$l]) && $p[$l]) {
			$param[$v] = $p[$l];

			return;
		}
	}

	throw new lxException($login->getThrow("need"), '', $list[0]);
}

function checkIfVariablesSet($p, $list)
{
	global $login;

	foreach ($list as $l) {
		if (!isset($p[$l]) || !$p[$l]) {
			$n = str_replace("-", "_", $l);

			throw new lxException($login->getThrow("need"), '', $n);
		}
	}
}

function get_variable($list)
{
	$vlist = null;

	foreach ($list as $k => $v) {
		if (csb($k, "v-")) {
			$vlist[strfrom($k, "v-")] = $v;
		}
	}

	return $vlist;
}


function parse_opt($argv)
{
	unset($argv[0]);

	if (!$argv) {
		return null;
	}

	foreach ($argv as $v) {
		if (!csb($v, "--")) {
			$ret['final'] = $v;
			continue;
		}

		$v = strfrom($v, "--");

		if (csa($v, "=")) {
			$opt = explode("=", $v);
			$ret[$opt[0]] = $opt[1];
		} else {
			$ret[$v] = $v;
		}
	}

	return $ret;
}

function mkdir_ifnotExist($name)
{
}

function opt_get_single_flag($opt, $var)
{
	$ret = false;

	if (isset($opt[$var]) && $opt[$var] === $var) {
		$ret = true;
	}

	return $ret;
}

function opt_get_default_or_set($opt, $val, $def)
{
	if (!isset($opt[$val])) {
		return $def;
	} else {
		return $opt[$val];
	}
}

function is_running_secondary()
{
	return lxfile_exists("../etc/running_secondary");
}

function exit_if_running_secondary()
{
	if (is_running_secondary()) {
		print("This is Running secondary\n");

		exit;
	}
}

function is_secondary_master()
{
	return lxfile_exists("../etc/secondary_master");
}

function exit_if_secondary_master()
{
	if (is_secondary_master()) {
		print("This is secondary Master\n");

		exit;
	}
}

function exit_if_another_instance_running()
{
	if (lx_core_lock()) {
		print("Another Copy of the same program is currently Running on pid\n");

		exit;
	}
}

function lx_core_lock($file = null)
{
	global $argv, $sgbl;

	$prog = basename($argv[0]);

	// This is a hack.. If we can't get the arg, then that means we are in the cgi mode,
	// and that means our process name is display.php.
	if (!$prog) {
		$prog = "display.php";
	}

	lxfile_mkdir("../pid");

	if (!$file) {
		$file = "$prog.pid";
	} else {
		$file = basename($file);
	}

	$pidfile = "{$sgbl->__path_program_root}/pid/{$file}";
	$pid = null;

	if (lxfile_exists($pidfile)) {
		$pid = lfile_get_contents($pidfile);
	}

	$str = "-----------------------------\n";

	$str .= "PID#:  " . $pid . "\n";

	if (!$pid) {
		$str .= "$prog:$file\nNo pid file $pidfile detected..\n";
		lfile_put_contents($pidfile, os_getpid());

		return false;
	}

	$pid = trim($pid);
	$name = os_get_commandname($pid);

	if ($name) {
		$name = basename($name);
	}

	if (!$name || $name !== $prog) {
		if (!$name) {
			$str .= "$prog:$file\nStale Lock file detected.\n$pidfile\nRemoving it...\n";
		} else {
			$str .= "$prog:$file\nStale lock file found.\nAnother program $name is running on it..\n";
		}

		lxfile_rm($pidfile);
		lfile_put_contents($pidfile, os_getpid());

		$ret = false;
	} else {
		$ret = true;
	}

	$str .= "-----------------------------\n";

	dprint($str);

	return $ret;
}

function lx_core_lock_check_only($prog, $file = null)
{
	lxfile_mkdir("../pid");

	if (!$file) {
		$file = basename($prog) . ".pid";
	} else {
		$file = basename($file);
	}

	$pidfile = "__path_program_root/pid/$file";

	if (!lxfile_exists($pidfile)) {
		return false;
	}

	$pid = lfile_get_contents($pidfile);
	dprint($pid . "\n");

	if (!$pid) {
		dprint("\n$prog:$file\nNo pid in file detected..\n");

		return false;
	}

	$pid = trim($pid);
	$name = os_get_commandname($pid);

	if ($name) {
		$name = basename($name);
	}

	if (!$name || $name !== $prog) {
		if (!$name) {
			dprint("\n$prog:$file\nStale Lock file detected.\n$pidfile\nRemoving it...\n ");
		} else {
			dprint("\n$prog:$file\nStale lock file found.\nAnother program $name is running on it..\n");
		}

		lxfile_rm($pidfile);

		return false;
	}

	return true;
}

function appvault_dbfilter($inputfile, $outputfile, $cont)
{
	$val = lfile_get_contents($inputfile);
	$fullurl = "{$cont['domain']}/{$cont['installdir']}";
	$fullurl = trim($fullurl, "/");
	$full_install_path = "{$cont['full_document_root']}/{$cont['installdir']}";
	$full_install_path = remove_extra_slash($full_install_path);
	$full_install_path = trim($full_install_path, "/");
	$full_install_path = "/$full_install_path";
	$install_dir = $cont['installdir'];
	$install_dir = trim($install_dir, "/");
	$full_doc_root = $cont['full_document_root'];
	$full_doc_root = trim($full_doc_root, "/");
	$full_doc_root = "/$full_doc_root";

	if (isset($cont['relative_script_path'])) {
		$relative_script_path = $cont['relative_script_path'];
		$relative_script_path = remove_extra_slash("/$relative_script_path");
	} else {
		if (isset($cont['executable_file_path'])) {
			$execpath = $cont['executable_file_path'];
			$relative_script_path = remove_extra_slash("/$install_dir/$execpath");
		} else {
			$relative_script_path = $install_dir;
		}
	}

	$val = str_replace("__lx_full_url", $fullurl, $val);
	$val = str_replace("__lx_full_installdir", $full_install_path, $val);
	$val = str_replace("__lx_full_script_path", $full_install_path, $val);
	$val = str_replace("__lx_document_root", $full_doc_root, $val);
	$val = str_replace("__lx_installdir", $install_dir, $val);
	$val = str_replace("__lx_relative_script_path", $relative_script_path, $val);

	$val = str_replace("__lx_title", $cont['title'], $val);
	$val = str_replace("__lx_admin_email", $cont['email'], $val);
	$val = str_replace("__lx_admin_company", $cont['company'], $val);
	$val = str_replace("__lx_real_name", $cont['realname'], $val);
	$val = str_replace("__lx_install_flag", $cont['install_flag'], $val);
	$val = str_replace("__lx_admin_name", $cont['adminname'], $val);
	$val = str_replace("__lx_submit_value", $cont['submit_value'], $val);
	$val = str_replace("__lx_client_path", "/home/{$cont['customer_name']}", $val);
	$val = str_replace("__lx_adminemail_login", $cont['admin_email_login'], $val);
	$val = str_replace("__lx_admin_pass", $cont['adminpass'], $val);
	$val = str_replace("__lx_md5_adminpass", md5($cont['adminpass']), $val);
	$val = str_replace("__lx_db_host", $cont['realhost'], $val);
	$val = str_replace("__lx_db_name", $cont['dbname'], $val);
	$val = str_replace("__lx_db_pass", $cont['dbpass'], $val);
	$val = str_replace("__lx_db_user", $cont['dbuser'], $val);
	$val = str_replace("__lx_db_type", $cont['dbtype'], $val);
	$val = str_replace("__lx_url", $cont['domain'], $val);
	$val = str_replace("__lx_domain_name", $cont['domain'], $val);
	$val = str_replace("__lx_action", $cont['action'], $val);

	lfile_put_contents($outputfile, $val);
}

function installLxetc()
{
	// TODO: Remove this function
	return;
}

function lightyApacheLimit($server, $var)
{
	if (!$server) {
		return true;
	}

	if ($var === 'phpfcgi_flag' || $var === 'phpfcgiprocess_num') {
		// MR - always true because change to php-fpm purpose!
		return true;
	}

	if ($var === 'dotnet_flag') {
		$v = db_get_value("pserver", $server, "ostype");

		return ($v !== 'rhel');
	}

	return true;
}

function createRestartFile($servar)
{
	global $sgbl;

	if ($servar === 'none') {
		return;
	}

	if (strpos($servar, 'proxy') !== false) {
		return;
	}

	$servarn = "__var_progservice_$servar";

	if (isset($sgbl->$servarn)) {
		$service = $sgbl->$servarn;
	} else {
		$service = $servar;
	}

	$file = "__path_program_etc/.restart";
	lxfile_mkdir($file);
	$file .= "/._restart_" . $service;
	lfile_put_contents($file, "a");
}

function getLastFromList(&$list)
{
	if (!$list) {
		return null;
	}

	foreach ($list as &$l) {
	}

	return $l;
}

function getFirstKeyFromList(&$list)
{
	if (!$list) {
		return null;
	}

	foreach ($list as $k => $v) {
		return $k;
	}
}

function getFirstFromList(&$list)
{
	if (!$list) {
		return null;
	}

	foreach ($list as &$l) {
		return $l;
	}
}

function getBestLocationFromServer($server, $list)
{
	return rl_exec_get(null, $server, 'get_best_location', array($list));
}

function get_best_location($list)
{
	dprintr($list);

	$lvmlist = null;

	foreach ($list as $l) {
		if (csb($l, "lvm:")) {
			$lvmlist[] = $l;
		} else {
			$normallist[] = $l;
		}
	}

	if ($lvmlist) {
		foreach ($lvmlist as $l) {
			$out[$l] = vg_diskfree($l);
		}
	} else {
		foreach ($normallist as $l) {
			$out[$l] = lxfile_disk_free_space($l);
		}
	}

	dprintr($out);
	arsort($out);
	dprintr($out);

	foreach ($out as $k => $v) {
		return array('location' => $k, 'size' => $v);
	}
}

function vg_complete()
{
	if (!lxfile_exists("/usr/sbin/vgdisplay")) {
		return;
	}

	$out = exec_with_all_closed_output("vgdisplay -c");
	$list = explode("\n", $out);
	$ret = null;

	foreach ($list as $l) {
		$l = trim($l);

		if (!$l) {
			continue;
		}

		if (!csa($l, ":")) {
			continue;
		}

		$nlist = explode(":", $l);
		$res['nname'] = $nlist[0];
		$res['total'] = ($nlist[13] * $nlist[12]) / 1024;
		$res['used'] = ($nlist[14] * $nlist[12]) / 1024;

		$ret[] = $res;
	}

	return $ret;
}

function vg_diskfree($vgname)
{
	if (!lxfile_exists("/usr/sbin/vgdisplay")) {
		return;
	}

	$vgname = fix_vgname($vgname);
	$out = exec_with_all_closed_output("vgdisplay -c $vgname");
	$out = trim($out);

	$list = explode(":", $out);

	$per = $list[12];
	$num = $list[15];

	return ($per * $num) / 1024;
}

function lvm_disksize($lvmpath)
{
//	$out = exec_with_all_closed_output("lvdisplay -c /dev/$vgname/$lvmname");
//	$out = explode(":", $out);
//	return $out[6] / 1024;

	$out = exec_with_all_closed_output("/usr/sbin/lvs --nosuffix --units b --noheadings -o lv_size $lvmpath");
	$out = trim($out);

	return $out / (1024 * 1024);
}

function lo_remove($loop)
{
	lxshell_return("losetup", "-d", $loop);
}

function lvm_remove($lvmpath)
{
	lxshell_return("lvremove", "-f", $lvmpath);
}

function lvm_create($vgname, $lvmname, $size)
{
	$vgname = fix_vgname($vgname);
	$lvmname = basename($lvmname);

	return lxshell_return("lvcreate", "-L{$size}M", "-n$lvmname", $vgname);
}

function lvm_extend($lvpath, $size)
{
	global $gbl;
	global $global_shell_error;

	$cursize = lvm_disksize($lvpath);
	$extra = $size - $cursize;

	if ($extra > 0) {
		$ret = lxshell_return("lvextend", "-L+{$extra}M", $lvpath);

		if ($ret) {
			$gbl->setWarning('extending_failed', '', $global_shell_error);
		}
	}
}

function curl_get_file($file)
{
	$res = curl_get_file_contents($file);
	$res = trim($res);

	if (!$res) {
		return null;
	}

	$data = explode("\n", $res);

	return $data;
}

function curl_get_file_contents($file)
{
	$server = getDownloadServer();
	$ch = curl_init("$server/$file");

	ob_start();

	curl_setopt($ch, CURLOPT_FAILONERROR, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

	// MR -- possible fix download/upload issue in php 5.3
	curl_setopt($ch, CURLOPT_SSLVERSION, 3);
	curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'SSLv3');

	curl_exec($ch);

	$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

	if ($code !== 200) {
		return null;
	}

	dprint(curl_error($ch));
	curl_close($ch);
	$retrievedhtml = ob_get_contents();

	ob_end_clean();

	return $retrievedhtml;
}

function install_if_package_not_exist($name, $nolog = null)
{
	if ($name === '') {
		return;
	}

//	$ret = lxshell_return("rpm", "-q", $name);
	$ret = lxshell_return("yum", "list", "installed", $name);


	if ($ret) {
		log_cleanup("- Install for {$name} package", $nolog);
		lxshell_return("yum", "-y", "install", $name);
	} else {
		log_cleanup("- {$name} package already installed", $nolog);
	}
}

function curl_general_get($url)
{
	$ch = curl_init($url);

	ob_start();

	curl_setopt($ch, CURLOPT_FAILONERROR, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_exec($ch);

	$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

	if ($code !== 200) {
		return null;
	}

	dprint(curl_error($ch));
	curl_close($ch);
	$retrievedhtml = ob_get_contents();

	ob_end_clean();

	return $retrievedhtml;
}


function getFullVersionList($till = null)
{
	global $sgbl;

	$ver = $sgbl->__ver_major_minor_release;

	return array($ver);
}

function getVersionList($till = null)
{
	$list = getFullVersionList($till);

	foreach ($list as $k => $l) {
		if (preg_match("/2$/", $l) && ($k !== count($list) - 1)) {
			continue;
		}

		$nnlist[] = $l;
	}

	$nlist = $nnlist;

	return $nlist;
}

function checkIfLatest()
{
	global $sgbl;

	$latest = getLatestVersion();

	return ($latest === $sgbl->__ver_major_minor_release);
}

function getLatestVersion()
{
	exec("yum check-update kloxong|grep kloxong|awk '{print $2}'", $out, $ret);

	if ($ret === 0) {
		$ver = getInstalledVersion();
	} else {
		$ver = str_replace(".mr", "", $out[0]);
	}

	return $ver;

}

function getInstalledVersion()
{
	exec("cd /; yum list installed kloxong|grep kloxong|awk '{print $2}'", $out, $ret);

	$ver = str_replace(".mr", "", $out[0]);

	return $ver;
}

function getDownloadServer()
{
	global $sgbl;

	$progname = $sgbl->__var_program_name;

	$maj = $sgbl->__ver_major_minor;

	$server = "http://download.lxcenter.org/download/$progname/$maj";

	return $server;
}

function download_source($file)
{
	$server = getDownloadServer();

	download_file("$server/$file");
}

function download_remote($url, $user, $pass, $localfile = null)
{
	list($protocol, $rest) = explode("://", $url);

	switch ($protocol) {
		case 'ftp':
		case 'ftps':
			download_from_ftp($url, $user, $pass, $localfile);
			break;
		case 'scp':
		case 'sftp':
			download_from_scp($url, $user, $pass, $localfile);
			break;
		default:
			download_file($url, $localfile);
			break;
	}
}

function download_from_ftp($url, $user, $pass, $localfile = null)
{
	global $login;

	log_log("download", "$url $localfile");

	if (!$localfile) {
		$localfile = basename($url);
	}

	$fn = lxftp_connect($url);
	$auth = ftp_login($fn, $user, $pass);

	if (!$auth) {
		exec_with_all_closed("sh /script/load-wrapper >/dev/null 2>&1 &");
		throw new lxException($login->getThrow('could_not_connect_to_server'), '', $url);
	}

	ftp_pasv($fn, true);
	$fp = lfopen($localfile, "w");

	if (!ftp_fget($fn, $fp, $localfile, FTP_BINARY)) {
		throw new lxException($login->getThrow('file_download_failed'), '', $localfile);
	}

	fclose($fp);
}

function download_from_scp($url, $user, $pass, $localfile = null)
{
	global $login;

	log_log("download", "$url $localfile");

	if (!$localfile) {
		$localfile = basename($url);
	}

	$fn = lxscp_connect($url);
	$auth = ssh2_auth_password($fn, $user, $pass);

	if (!$auth) {
		exec_with_all_closed("sh /script/load-wrapper >/dev/null 2>&1 &");
		throw new lxException($login->getThrow('could_not_connect_to_server'), '', $url);
	}

	$fp = lfopen($localfile, "w");

	if (!ssh2_scp_recv($fn, $fp, $localfile)) {
		throw new lxException($login->getThrow('file_download_failed'), '', $localfile);
	}

	fclose($fp);
}

function download_file($url, $localfile = null)
{
	log_log("download", "$url $localfile");

	$ch = curl_init($url);

	if (!$localfile) {
		$localfile = basename($url);
	}

	$fp = null;

	if ($localfile !== 'devnull') {
		$fp = fopen($localfile, "w");
		curl_setopt($ch, CURLOPT_FILE, $fp);
	}

	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_exec($ch);

	dprint("Curl Message: " . curl_error($ch) . "\n");

	curl_close($ch);

	if ($fp) {
		fclose($fp);
	}
}

function incrementVar($table, $var, $min, $increment)
{
	$sq = new Sqlite(null, $table);
	$res = $sq->rawQuery("select $var from $table order by ($var + 0) DESC limit 1");

	if (!$res) {
		$ret = $min;
	} else {
		$ret = $res[0][$var] + $increment;
	}

	return $ret;
}

function se_submit($contact, $dom, $email)
{
	$tmpfile = lx_tmp_file("se_submit_$dom");

	include "./sesubmit/engines.php";

	foreach ($enginelist as $e => $k) {
		$k = str_replace("[>URL<]", "http://$dom", $k);
		$k = str_replace("[>EMAIL<]", $email, $k);
		download_file($k, $tmpfile);
		$var .= "\n\n-----------Submitting to $e-------------\n\n";
		$var .= lfile_get_contents($tmpfile);
	}

	lunlink($tmpfile);

//	lx_mail("kloxo", $contact, "Search Submission Info", $var);
	callInBackground("lx_mail", array(null, $contact, "Search Submission Info", $var));

	lfile_put_contents("/tmp/mine", $var);
}

function remove_if_older_than_a_day_dir($dir, $day = 1)
{
	if (!lis_dir($dir)) {
		return;
	}

	$list = lscandir_without_dot($dir);

	foreach ($list as $l) {
		remove_if_older_than_a_day("$dir/$l", $day);
	}
}

function remove_if_older_than_a_day($file, $day = 1)
{
	$stat = llstat($file);

	if ($stat['mtime'] && ((time() - $stat['mtime']) > $day * 24 * 3600)) {
		lunlink($file);
	}
}

function remove_directory_if_older_than_a_day($dir, $day = 1)
{
	$stat = llstat($dir);

	if ($stat['mtime'] && ((time() - $stat['mtime']) > $day * 24 * 3600)) {
		lxfile_rm_rec($dir);
	}
}

function remove_if_older_than_a_minute_dir($dir)
{
	$list = lscandir_without_dot($dir);

	foreach ($list as $l) {
		remove_if_older_than_a_minute("$dir/$l");
	}
}

function remove_if_older_than_a_minute($file)
{
	$stat = llstat($file);

	if ($stat['mtime'] && ((time() - $stat['mtime']) > 60)) {
		lunlink($file);
	}
}

function lx_mail($from, $to, $subject, $message, $extra = null)
{
	if (!$from) {
		$server = getFQDNforServer('localhost');
		$from = "admin@{$server}";
	}

	$header = "From: {$from}\n";

	$header .= "MIME-Version: 1.0\n";
	$header .= "Content-type: text/html; charset=utf-8\n";

	if ($extra) {
		$header .= "{$extra}\n";
	}

	$message = str_replace("\n", "<br>\n", $message);

	log_log("mail_send", "Sending Mail to {$to} {$subject} from {$from}");

	mail($to, $subject, $message, $header);
}

function download_and_print_file($server, $file)
{
	$ch = curl_init("$server/$file");

	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_exec($ch);
	curl_close($ch);
}

function get_title()
{
	global $sgbl, $login;

	$gen = $login->getObject('general')->generalmisc_b;

	if ($login->isAdmin()) {
		$host = os_get_hostname();
		$host = strtilfirst($host, ".");
	} else {
		$host = $login->nname;
	}

	if (isset($gen->htmltitle) && $gen->htmltitle) {
		$progname = $gen->htmltitle;
	} else {
		$progname = ucfirst($sgbl->__var_program_name);
	}

	$title = null;

	if ($login->isAdmin()) {
		$title = "{$sgbl->__ver_major}.{$sgbl->__ver_minor}.{$sgbl->__ver_release} {$sgbl->__ver_extra}";
	}

	if (check_if_many_server()) {
		$enterprise = "Enterprise";
	} else {
		$enterprise = "Single Server";
	}

	if (file_exists(".svn")) {
		$enterprise .= " Development";
	}

	$title = "$host $progname $enterprise $title";

	return $title;
}

function send_mail_to_admin($subject, $message)
{
	global $sgbl;

	$progname = $sgbl->__var_program_name;

	$rawdb = new Sqlite(null, "client");
	$email = $rawdb->rawQuery("select contactemail from client where cttype = 'admin'");
	$email = $email[0]['contactemail'];

	callInBackground("lx_mail", array($progname, $email, $subject, $message));
}

function save_admin_email($nolog = null)
{
	log_cleanup("Set admin contact email", $nolog);
	log_cleanup("- Set process", $nolog);

	$a = null;
	$email = db_get_value("client", "admin", "contactemail");
	$a['admin']['contactemail'] = $email;

	slave_save_db("contactemail", $a);
}

function getKloxoLicenseInfo($nolog = null)
{
	log_cleanup("Get Kloxo License info", $nolog);
	log_cleanup("- Get process", $nolog);

	lxshell_php("theme/lbin/getlicense.php");
}

function createDatabaseInterfaceTemplate($nolog = null)
{
	log_cleanup("- Create database interface template (Forced)", $nolog);

	exec("mysql -u kloxo -p`cat ../etc/conf/kloxo.pass` kloxo < ../file/interface/interface_template.dump");

//	exec("sh /script/fix-missing-admin");
}

function callInChild($func, $arglist)
{
	global $sgbl;

	$res = new Remote();
	$res->__type = 'function';
	$res->func = $func;
	$res->arglist = $arglist;
	$name = tempnam("/tmp", "lxchild");
	lxfile_generic_chmod($name, "700");
	lfile_put_contents($name, serialize($res));
	$var = lxshell_output($sgbl->__path_php_path, "../bin/common/child.php", $name);
	$rmt = unserialize(base64_decode($var));

	return $rmt;
}

function callInBackground($func, $arglist)
{
	global $sgbl;

	$res = new Remote();
	$res->__type = 'function';
	$res->func = $func;
	$res->arglist = $arglist;
	$name = tempnam("/tmp", "background");
	lxfile_generic_chmod($name, "700");
	lfile_put_contents($name, serialize($res));

	lxshell_background($sgbl->__path_php_path, "../bin/common/background.php", $name);
}

function callObjectInBackground($object, $func)
{
	$res = new Remote();
	$res->__type = 'object';
	$res->__exec_object = $object;
	$res->func = $func;
	$name = tempnam("/tmp", "background");
	lxfile_generic_chmod($name, "700");
	lfile_put_contents($name, serialize($res));

	lxshell_background("__path_php_path", "../bin/common/background.php", $name);
}

function get_with_cache($file, $cmdarglist)
{
	global $sgbl;

	$stat = @ llstat($file);

	lxfile_mkdir("{$sgbl->__path_program_root}/cache");

	$tim = 120;

	$c = lfile_get_contents($file);

	if (((time() - $stat['mtime']) > $tim) || !$c) {
		// Hack hack.. The lxshell_output does not take strings. You need to supply them together.
		$val = call_user_func_array('lxshell_output', $cmdarglist);
		lfile_put_contents($file, $val);

		return $val;
	}

	return lfile_get_contents($file);
}

function copy_script($nolog = null)
{
	log_cleanup("Initialize /script/ dir", $nolog);
	log_cleanup("- Initialize processes", $nolog);
	exec("'rm' -rf /script; ln -sf /usr/local/lxlabs/kloxo/pscript /script");
}

function getAdminDbPass()
{
	$pass = lfile_get_contents("__path_admin_pass");

	return trim($pass);
}

function change_underscore($var)
{
	$var = str_replace("_", " ", $var);

	if (csa($var, ":")) {
		$n = strpos($var, ":");
		$var[$n + 1] = strtoupper($var[$n + 1]);
	}

	return ucwords($var);
}

function getIpaddressList($master, $servername)
{
	$sql = new Sqlite($master, 'ipaddress');

	if (!$servername) {
		$servername = 'localhost';
	}

	$list = $sql->getRowsWhere("syncserver = '$servername'");

	foreach ($list as $l) {
		$ret[] = $l['ipaddr'];
	}

	return $ret;
}

function if_customer_complain_and_exit()
{
	global $sgbl, $login;

	if ($login->isLte('reseller')) {
		return;
	}

	$progname = $sgbl->__var_program_name;

	print("You are trying to access Protected Area. This incident will be reported\n <br> ");

	$message = "At " . lxgettime(time()) . " $login->nname tried to Access a region that is prohibited for Normal Users\n";

	send_mail_to_admin("$progname Warning: Unauthorized Access by $login->nname", $message);

	exit(0);
}

function getClassAndName($name)
{
	return getParentNameAndClass($name);
}

function getParentNameAndClass($pclname)
{
	return dogetParentNameAndClass($pclname);
}

function dogetParentNameAndClass($pclname)
{
	if (csa($pclname, "-")) {
		$string = "-";
	} else {
		$string = "_s_vv_p_";
	}

//	$vlist = explode("_s_vv_p_", $pclname);
	$vlist = explode($string, $pclname);
	$pclass = array_shift($vlist);
//	$pname = implode("_s_vv_p_", $vlist);
	$pname = implode($string, $vlist);

//	dprint($pclass);

	return array($pclass, $pname);
}

function doOldgetParentNameAndClass($pclname)
{
	if (csa($pclname, "_s_vv_p_")) {
		$string = "_s_vv_p_";
	} else {
		$string = "-";
	}

//	$vlist = explode("_s_vv_p_", $pclname);
	$vlist = explode($string, $pclname);
	$pclass = array_shift($vlist);
//	$pname = implode("_s_vv_p_", $vlist);
	$pname = implode($string, $vlist);

//	dprint($pclass);

	return array($pclass, $pname);
}

function if_not_admin_complain_and_exit()
{
	global $sgbl, $login;

	$progname = $sgbl->__var_program_name;
	if ($login->isLteAdmin()) {
		return;
	}

	print("You are trying to access Protected Area. This incident will be reported\n <br> ");
	debugBacktrace();

	$message = "At " . lxgettime(time()) . " $login->nname tried to Access a region that is prohibited for Normal Users\n";

	send_mail_to_admin("$progname Warning: Unauthorized Access by $login->nname", $message);

	exit(0);

}

function initProgram($ctype = null)
{
	initProgramlib($ctype);
}

function getKBOrMB($val)
{
	$val = (float)$val;

	if ($val > 1014) {
		return round($val / 1024, 2) . " MB";
	}

	return "$val KB";
}

function getGBOrMB($val)
{
//	$val = (isset($val)) ? $val : 0;
	$val = (float)$val;

	if ($val > 1048576) {
		return number_format(round($val / 1048576, 2), 2) . " TB";
	} else if ($val > 1014) {
		return number_format(round($val / 1024, 2), 2) . " GB";
	} else {
		return number_format(round($val, 2), 2) . " MB";
	}
}

function createClName($class, $name)
{
	return "{$class}-$name";
}

function createParentName($class, $name)
{
	return $class . "-" . $name;
}

function exists_in_coma($cmlist, $name)
{
	return (csa($cmlist, ",$name,"));
}

function exit_program()
{
	print_time('full', "Page Generation Took: ");

	exit_programlib();
}

function install_general($value)
{
	$value = implode(" ", $value);
	print("Install $value ....\n");
	exec("up2date-nox --nosig $value");
}

function readlastline($fp, $pos, $size)
{
	$t = " ";

	while ($t != "\n") {
		fseek($fp, $pos, SEEK_END);
		$t = fgetc($fp);
		$pos = $pos - 1;

		if ($pos === -$size) {
			$pos = null;

			break;
		}
	}

	$t = fgets($fp);

	return $t;
}

function getMainQuotaVar($vlist)
{
	$vlist['disk_usage'] = "";
	$vlist['traffic_usage'] = "";
	$vlist['mailaccount_num'] = "";
	$vlist['subweb_a_num'] = "";
	$vlist['ftpuser_num'] = "";
	$vlist['ddatabase_num'] = "";
	$vlist['subweb_a_num'] = "";
	$vlist['ssl_flag'] = "";
	$vlist['inc_flag'] = "";
	$vlist['php_flag'] = "";
	$vlist['modperl_flag'] = "";
	$vlist['cgi_flag'] = "";
	$vlist['frontpage_flag'] = "";
	$vlist['dns_manage_flag'] = "";
	$vlist['maildisk_usage'] = "";

	return $vlist;
}

function get_domain_client_temp_list($class)
{
	global $login;

	$temp = Array();
	$list = $login->getList($class);

	foreach ($list as $d) {
		$temp[$d->nname] = $d;
	}

	return $temp;
}

function manage_service($service, $state)
{
	global $gbl;

	print("Send $state to $service\n");
	$servicename = "__var_programname_$service";
	$program = $gbl->$servicename;

	if (isServiceExists($program)) {
		exec_with_all_closed("service {$program} restart >/dev/null 2>&1");
	}
}

function recursively_remove($directory)
{
	$directory = trim($directory);

	if ($directory[strlen($directory) - 1] === '/') {
		$string = "$directory: Directory ends in a slash. Will not recursively delete";
		dprint(' <br> ' . $string . "<br> ");
		log_shell_error($string);

		return;
	}

	lxfile_rm_rec($directory);
}

function checkIfRightTime($time, $first, $second)
{
	if ($time === $first || $time === $second || ($time > $first && $time < $second)) {
		return 0;
	}

	if ($time > $second) {
		return 1;
	}

	if ($time < $first) {
		return -1;
	}
}

function is_ip($ipf, $ip)
{
	$if = explode(".", $ipf);
	$ii = explode(".", $ip);

	foreach ($if as $k => $v) {
		if ($v === '*') {
			continue;
		}

		if ($v !== $ii[$k]) {
			return false;
		}
	}

	return true;
}

function get_star_password()
{
	return "****";
}

function is_star_password($pass)
{
	return ($pass === "****");
}

function FindRightPosition($fp, $fsize, $oldtime, $newtime, $func)
{
	$cur = $fsize / 2;
	$beg = 0;
	$end = $fsize;

	dprint($cur . "\n");

	$string = fgets($fp);
	$begtime = call_user_func($func, $string);

	if ($newtime < $begtime) {
		dprint("End time $newtime < $begtime Less than Beginning. \n");
//		print("Date: " . @ date('Y-m-d: H:i:s', $newtime) . " " . @ date('Y-m-d: h:i:s', $begtime) . "\n");
		print("<div align='center'>Date: " . @ date('Y-m-d: h:i:s', $begtime) . "(begin) - " . @ date('Y-m-d: H:i:s', $newtime) . " (end)</div>\n");

		return -1;
	}

	/*
		 // This logic is actually wrong. This is returning if the oldtime is less than first time,
		 // but that isn't is a necessary criteria. The file could be so small as to start from middle of the day.
		 if ($time < $readtime) {
			 dprint("Less than Beginning. \n");
			 return 0;
		 }
	 */

	fseek($fp, 0, SEEK_END);
	takeToStartOfLine($fp);
	$string = fgets($fp);

	$endtime = call_user_func($func, $string);

	if ($oldtime > $endtime) {
		$ot = @ date("Y-m-d:h-i", $oldtime);
		dprint(" $ot $oldtime $string More than End. \n");

		return -1;
	}

	rewind($fp);

	if ($oldtime < $begtime) {
		return 1;
	}

	$count = 0;

	while (true) {
		$count++;

		if ($count > 1000) {
			return -1;
		}

		dprint("At position $cur: \n");
		fseek($fp, $cur);

		takeToStartOfLine($fp);

		$string1 = fgets($fp);
		$readtime1 = call_user_func($func, $string1);
		$string2 = fgets($fp);
		$readtime2 = call_user_func($func, $string2);

		dprint("Position: $oldtime $readtime1 $readtime2\n");

		if ($readtime2 - $readtime1 >= 100) {
			dprint("Somethings wrong $string1 $string2 \n");
		}

		$ret = checkIfRightTime($oldtime, $readtime1, $readtime2);

		if ($ret === 0) {
			takeToStartOfLine($fp);

			return 1;
		} else {
			if ($ret < 0) {
				dprint("Going Up\n");
				$end = $cur;
				$cur = $cur - ($cur - $beg) / 2;
				$cur = round($cur);
			} else {
				dprint("Going Down\n");
				$beg = $cur;
				$cur = $cur + ($end - $cur) / 2;
				$cur = round($cur);
			}
		}
	}
}

function lxlabs_marker_fgets($fp)
{
	global $sgbl;

	while (!feof($fp)) {
		$s = fgets($fp);

		if (csa($s, $sgbl->__var_lxlabs_marker)) {
			dprint("found marker\n");

			return $s;
		}
	}

	return null;
}

function lxlabs_marker_getime($string)
{
	$str = strtilfirst($string, " ");
	$str = trim($str);

	return $str;
}

function lxlabs_marker_firstofline($fp)
{
	global $sgbl;

	while (!feof($fp)) {
		if (ftell($fp) <= 2) {
			return;
		}

		takeToStartOfLine($fp);
		takeToStartOfLine($fp);
		$string = fgets($fp);

		if (csa($string, $sgbl->__var_lxlabs_marker)) {
			takeToStartOfLine($fp);

			return;
		}
	}
}

function lxlabsFindRightPosition($fp, $fsize, $oldtime, $newtime)
{
	$cur = $fsize / 2;
	$beg = 0;
	$end = $fsize;

	dprint($cur . "\n");

	$string = lxlabs_marker_fgets($fp);

	if (!$string) {
		dprint("Got nothing\n");

		return -1;
	}

	$begtime = lxlabs_marker_getime($string);

	if ($newtime < $begtime) {
		dprint("ENd time $newtime < $begtime Less than Beginning. \n");
		print("Date: " . @ date('Y-m-d: H:i:s', $newtime) . " " . @ date('Y-m-d: h:i:s', $begtime) . "\n");

		return -1;
	}

/*
	 // This logic is actually wrong. This is returning if the oldtime is less than first time,
	 // but that isn't is a necessary criteria. The file could be so small as to start from middle of the day.
	 if ($time < $readtime) {
		 dprint("Less than Beginning. \n");
		 return 0;
	 }
*/

	fseek($fp, 0, SEEK_END);
	lxlabs_marker_firstofline($fp);

	$string = lxlabs_marker_fgets($fp);

	$endtime = lxlabs_marker_getime($string);
	if ($oldtime > $endtime) {
		$ot = @ date("Y-m-d:h-i", $oldtime);
		dprint(" $ot $oldtime $string More than End. \n");

		return -1;
	}

	rewind($fp);

	if ($oldtime < $begtime) {
		return 1;
	}

	$count = 0;

	while (true) {
		$count++;

		if ($count > 1000) {
			return -1;
		}

		dprint("At position $cur: \n");
		fseek($fp, $cur);

		lxlabs_marker_firstofline($fp);

		$string1 = lxlabs_marker_fgets($fp);
		$readtime1 = lxlabs_marker_getime($string1);
		$string2 = lxlabs_marker_fgets($fp);
		$readtime2 = lxlabs_marker_getime($string2);

		dprint("Position: $oldtime $readtime1 $readtime2\n");

		if ($readtime2 - $readtime1 >= 10 * 300) {
			dprint("Somethings wrong $string1 $string2 \n");
		}

		$ret = checkIfRightTime($oldtime, $readtime1, $readtime2);

		if ($ret === 0) {
			lxlabs_marker_firstofline($fp);

			return 1;
		} else {
			if ($ret < 0) {
				dprint("Going Up\n");
				$end = $cur;
				$cur = $cur - ($cur - $beg) / 2;
				$cur = round($cur);
			} else {
				dprint("Going Down\n");
				$beg = $cur;
				$cur = $cur + ($end - $cur) / 2;
				$cur = round($cur);
			}
		}
	}
}

function monthToInt($month)
{
	$m = MonthList();

	return str_pad((array_search($month, $m) + 1), 2, 0, STR_PAD_LEFT);
}

function intToMonth($month)
{
	$m = MonthList();

	return $m[intval($month) - 1];
}

function MonthList()
{
	return array("Jan", "Feb", "Mar", "Apr", "May", "Jun",
		"Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
}

function readfirstline($file)
{
	$firstline = fgets($file);
	fclose($fp);

	return $firstline;
}

function getNotexistingFile($dir, $file)
{
	foreach (range(1, 100) as $i) {
		if (!lxfile_exists($dir . "/" . $file . "-" . $i)) {
			return $dir . "/" . $file . "-" . $i;
		}
	}

	return $dir . "/" . $file . "-" . $i;
}

function clearLxbackup($backup)
{
	$backup->setUpdateSubaction();
	$backup->write();
}

function createrows($list)
{
	$fields = lx_array_merge(array(get_default_fields(), $list));

	if (array_search_bool("syncserver", $fields)) {
		$fields[] = 'oldsyncserver';
		$fields[] = 'olddeleteflag';
	}

	return $fields;
}

function initDbLoginPre()
{
	$log_pre = "<p> Welcome to <%programname%>  </p><p>Use a valid username and password to gain access to the console. </p> ";
	db_set_default('general', 'login_pre', $log_pre);
}

function fixResourcePlan()
{
	global $login;

	$login->loadAllObjects('resourceplan');
	$list = $login->getList('resourceplan');

	foreach ($list as $l) {
		$qv = getQuotaListForClass('client');
		$write = false;

		foreach ($qv as $k => $v) {
			if ($k === 'centralbackup_flag') {
				if (!isset($l->priv->centralbackup_flag)) {
					$l->priv->centralbackup_flag = $l->centralbackup_flag;
					$write = true;
				}

				continue;
			}

			if (!isset($l->priv->$k)) {
				if (cse($k, "_flag")) {
					if (is_default_quota_flag_on($k)) {
						$l->priv->$k = 'on';
						$write = true;
					}
				}
			}
		}

		if ($write) {
			$l->setUpdateSubaction();
			$l->write();

			$write = false;
		}
	}
}

function is_default_quota_flag_on($v)
{
	if ($v === 'mailonly_flag') {
		return false;
	}

	return true;
}

function db_set_default($table, $variable, $default, $extra = null)
{
	$sq = new Sqlite(null, $table);

	if ($extra) {
		$extra = "AND $extra";
	}

	$sq->rawQuery("update $table set $variable = '$default' where $variable = '' $extra");
	$sq->rawQuery("update $table set $variable = '$default' where $variable is null $extra");
}

function db_set_default_variable_diskusage($table, $variable, $default, $extra = null)
{
	$sq = new Sqlite(null, $table);

	if ($extra) {
		$extra = "AND $extra";
	}

	$sq->rawQuery("update $table set $variable = $default where $variable = '' $extra");
	$sq->rawQuery("update $table set $variable = $default where $variable is null $extra");
	$sq->rawQuery("update $table set $variable = $default where $variable = '-' $extra");
}

function db_set_default_variable($table, $variable, $default, $extra = null)
{
	$sq = new Sqlite(null, $table);

	if ($extra) {
		$extra = "AND $extra";
	}

	$sq->rawQuery("update $table set $variable = $default where $variable = '' $extra");
	$sq->rawQuery("update $table set $variable = $default where $variable is null $extra");
//	$sq->rawQuery("update $table set $variable = $default where $variable = '-' $extra");
}

function updateTableProperly($__db, $table, $rr, $content)
{
	foreach ($content as $column) {
		if (isset($rr[$column])) {
			//dprint("Column $column Already exists in table $table\n");
			continue;
		}

		if (csb($column, "text_") || csb($column, "ser_") || csb($column, "coma_")) {
			$type = "text";
		} else {
			$type = "varchar(255)";
		}

		dprint("Adding column $column to $table ...\n");

		$__db->rawQuery("alter table $table add column $column $type");
	}

	return true;
}

function add_http_if_not_exist($url)
{
	if (!csb($url, "http:/") && !csb($url, "https:/")) {
		$url = "http://$url";
	}

	return $url;
}

function getAllIpaddress()
{
	$mydb = new Sqlite(null, 'ipaddress');
	$res = $mydb->getTable(array('ipaddr', 'nname'));

	foreach ($res as $r) {
		$list[] = $r['ipaddr'];
	}

	return $list;
}

function fix_getParentNameAndClass($v)
{
	if (csa($v, "___") && !csa($v, "__last_access_")) {
		$vv = explode("___", $v);

		if (!csa($vv[0], "_s_vv_p_")) {
			return false;
		} else {
			return doOldgetParentNameAndClass($v);
		}
	} else {
		if (!csa($v, "_s_vv_p_")) {
			return false;
		} else {
			return doOldgetParentNameAndClass($v);
		}
	}
}

function get_table_from_class($class)
{
	$table = get_class_variable($class, "__table");

	if (!$table) {
		return $class;
	}

	return $table;
}

function get_class_for_table($table)
{
	if ($table === 'domain') {
		return array('domaina', 'subdomain');
	}

	return null;
}

function is_centosfive()
{
	$find = find_os_pointversion();
	$check = strpos($find, 'centos-5');

	if ($check !== false) {
		return true;
	} else {
		return false;
	}
}

function migrateResourceplan($class)
{
	$ss = new Sqlite(null, "resourceplan");
	$r = $ss->getTable();

	if ($r) {
		return;
	}

	$sq = new Sqlite(null, 'clienttemplate');
	$cres = $sq->getTable();

	if ($class) {
		$nsq = new Sqlite(null, "{$class}template");
		$dres = $nsq->getTable();
		$total = lx_array_merge(array($cres, $dres));
	} else {
		$total = $cres;
	}

	foreach ($total as $t) {
		$string = $ss->createQueryStringAdd($t);
		$addstring = "insert into resourceplan $string;";
		$ss->rawQuery($addstring);
	}
}

function fprint($var, $type = 0)
{
	global $sgbl;

	if ($type > $sgbl->dbg) {
		return;
	}

	$string = var_export($var, true);

	file_put_contents("file.txt", $string . "\n", FILE_APPEND);
}

function print_and_exit($rem)
{
	$val = base64_encode(serialize($rem));
	ob_end_clean();

	print($val);

	flush();

	exit;
}

function getOsForServer($servername)
{
	if (!$servername) {
		$servername = 'localhost';
	}

	$sq = new Sqlite(null, 'pserver');

	$res = $sq->getRowsWhere("nname = '$servername'", array('ostype'));

	return $res[0]['ostype'];
}

function rl_exec_in_driver($parent, $class, $function, $arglist)
{
	global $gbl;

	$syncserver = $parent->getSyncServerForChild($class);
	$driverapp = $gbl->getSyncClass($parent->__masterserver, $syncserver, $class);
	$res = rl_exec_get($parent->__masterserver, $syncserver, array("{$class}__$driverapp", $function), $arglist);

	return $res;
}

function vpopmail_get_path($domain)
{
	return trim(lxshell_output("__path_mail_root/bin/vdominfo", "-d", $domain));
}

function addLineIfNotExistPattern($filename, $searchpattern, $pattern)
{
	$cont = lfile_get_contents($filename);

	if (!preg_match("+$searchpattern+i", $cont)) {
		lfile_put_contents($filename, "\n", FILE_APPEND);
		lfile_put_contents($filename, $pattern, FILE_APPEND);
		lfile_put_contents($filename, "\n\n\n", FILE_APPEND);
	} else {
		dprint("Pattern '$searchpattern' Already present in $filename\n");
	}

}

function remove_line($filename, $pattern)
{
	$list = lfile($filename);

	foreach ($list as $k => $l) {
		if (csa($l, $pattern)) {
			unset($list[$k]);
		}
	}

	lfile_put_contents($filename, implode("", $list));
}

function add_line($filename, $pattern)
{
	lfile_put_contents($filename, "$pattern\n", FILE_APPEND);
}

function addLineIfNotExistInside($filename, $pattern, $comment)
{
	$cont = lfile_get_contents($filename);

	if (!csa(strtolower($cont), strtolower($pattern))) {
		if ($comment) {
			lfile_put_contents($filename, "\n$comment \n\n", FILE_APPEND);
		}

		lfile_put_contents($filename, "$pattern\n", FILE_APPEND);

		if ($comment) {
			lfile_put_contents($filename, "\n\n\n", FILE_APPEND);
		}
	} else {
	//	dprint("Pattern '$pattern' Already present in $filename\n");
	}

}

function fix_all_mysql_root_password()
{
	$rs = get_all_pserver();

	foreach ($rs as $r) {
		fix_mysql_root_password($r);
	}
}

function fix_mysql_root_password($server)
{
	global $login;

	$pass = $login->password;
	$pass = fix_nname_to_be_variable($pass);
	$pass = substr($pass, 3, 11);

	$dbadmin = new Dbadmin(null, $server, "mysql___$server");
	$dbadmin->get();

	if ($dbadmin->dbaction === 'add') {
		$dbadmin->syncserver = $server;
		$dbadmin->ttype = 'mysql';
		$dbadmin->dbtype = 'mysql';
		$dbadmin->dbadmin_name = 'root';
		$dbadmin->parent_clname = createParentName("pserver", $server);
		$dbadmin->write();
		$dbadmin->get();
		$dbadmin->dbaction = 'clean';
	}

	if ($dbadmin->dbpassword) {
		dprint("Mysql Password is not null\n");

		return;
	}

	$dbadmin->dbpassword = $pass;
	$dbadmin->setUpdateSubaction('update');

	try {
		$dbadmin->was();
	} catch (Exception $e) {
	}
}

function slave_save_db($file, $list)
{
//	global $login;

	$rmt = new Remote();
	$rmt->data = $list;

	lxfile_mkdir("../etc/slavedb");
	lfile_put_serialize("../etc/slavedb/$file", $rmt);

//	rl_exec_get('localhost', $login->syncserver, 'lxfile_mkdir', array('../etc/slavedb'));
//	rl_exec_get('localhost', $login->syncserver, 'lfile_put_serialize', array("../etc/slavedb/{$file}", $rmt));
}

function securityBlanketExec($table, $nname, $variable, $func, $arglist)
{
	global $sgbl;

	$rem = new Remote();
	$rem->table = $table;
	$rem->nname = $nname;
	$rem->flagvariable = $variable;
	$rem->func = $func;
	$rem->arglist = $arglist;
	$name = tempnam("/tmp", "security");

	lxfile_generic_chmod($name, "700");
	lfile_put_contents($name, serialize($rem));

	lxshell_background($sgbl->__path_php_path, "../bin/common/securityblanket.php", $name);
}

function checkClusterDiskQuota()
{
	global $gbl, $login;

	$maclist = $login->getList('pserver');

	$mess = null;

	foreach ($maclist as $mc) {
		try {
			rl_exec_get(null, $mc->nname, "remove_old_serve_file", null);
		} catch (Exception $e) {
		}

		$driverapp = $gbl->getSyncClass(null, $mc->nname, 'diskusage');

		try {
			$list = rl_exec_get(null, $mc->nname, array("diskusage__$driverapp", "getDiskUsage"));
		} catch (Exception $e) {
			$mess .= "Failed to connect to Slave $mc->nname: {$e->getMessage()}\n";
			continue;
		}

		foreach ($list as $l) {
			if (intval($l['pused']) >= 87) {
				$mess .= "Filesystem  {$l['mountedon']} ({$l['nname']}) on {$mc->nname} is using {$l['pused']}%\n";
			}
		}
	}

	dprint($mess);
	dprint("\n");

	if ($mess) {
	//	lx_mail(null, $login->contactemail, "Filesystem Warning", $mess);
		callInBackground("lx_mail", array(null, $login->contactemail, "Filesystem Warning", $mess));
	}

	lxfile_generic_chown("..", "lxlabs");
}

function find_closest_mirror()
{
	// TODO LxCenter: No call to this function found.
	dprint("find_closest_mirror htmllib>lib>lib.php\n");

	$v = curl_general_get("lxlabs.com/mirrorlist/");
	$v = trim($v);
	$vv = explode("\n", $v);
	$out = null;

	foreach ($vv as $k => $l) {
		$l = trim($l);

		if (!$l) {
			continue;
		}

		$verify = curl_general_get("$l/verify.txt");
		$verify = trim($verify);

		if (csa($verify, "lxlabs_mirror_verify")) {
			$out[] = $l;
		}
	}

	if (!$out) {
		return null;
	}

	foreach ($out as $l) {
		$hop[$l] = find_hop($l);
	}

	asort($hop);
	$v = getFirstKeyFromList($hop);

	return $v;
}

function find_hop($l)
{
	global $global_dontlogshell;

	$global_dontlogshell = true;

	$out = lxshell_output("ping -c 1 $l");
	$list = explode("\n", $out);

	foreach ($list as $l) {
		$l = trim($l);

		if (csb($l, "rtt")) {
			continue;
		}

		$l = trimSpaces($l);
		$ll = explode(" ", $l);
		$lll = explode("/", $ll[3]);

		return round($lll[1], 1);
	}
}

function file_server($fd, $string)
{
	$string = strfrom($string, "__file::");
	$rem = unserialize(base64_decode($string));

	if (!$rem) {
		return;
	}

	return do_serve_file($fd, $rem);
}

function print_or_write($fd, $buff)
{
	if ($fd) {
		return fwrite($fd, $buff);
	} else {
		print($buff);
		flush();

		// Lighttpd bug. Lighty doesn't flush even if you do a flush.
		//	sleep(2);

		return 1;
	}
}

function get_warning_for_server_info($o, $psi)
{
	if ($o->isAdmin()) {
		$psi = "\n Only the servers that are visible in the main server list will be shown here. So if you have done some search in the main servers page, only search results will be seen. Just go to the main servers page, and limit the servers to the ones you want to see. \n$psi";
	}

	return $psi;
}

function load_database_file($dbtype, $dbhost, $dbname, $dbuser, $dbpass, $dbfile)
{
	exec("$dbtype -h $dbhost -u $dbuser -p$dbpass $dbname < $dbfile");
}

function do_serve_file($fd, $rem)
{
	global $sgbl;

	$file = $rem->filename;

	$file = basename($file);
	$file = "{$sgbl->__path_serverfile}/{$file}";

	if (!lxfile_exists($file)) {
		log_log("servfile", "datafile $file dosn't exist, exiting");
		print_or_write($fd, "fFile Doesn't $file Exist...\n\n\n\n");

		return false;
	}

	$array = lfile_get_unserialize($file);
	lunlink($file);
	$realfile = $array['filename'];
	$pass = $array['password'];

	if ($fd) {
		dprint("Got request for $file, realfile: $realfile\n");
	}

	log_log("servfile", "Got request for $file realfile $realfile");
	if (!($pass && $pass === $rem->password)) {
		print_or_write($fd, "fPassword doesn't match\n\n");

		return false;
	}

	if (is_dir($realfile)) {
		// This should neverhappen. The directories are zipped at cp-fileserv and tar_to_filserved then itself.
		$b = basename($realfile);
		lxfile_mkdir("{$sgbl->__path_serverfile}/tmp/");
		$tfile = tempnam("{$sgbl->__path_serverfile}/tmp/", "{$b}.tar");
		$list = lscandir_without_dot($realfile);
		lxshell_tar($realfile, $tfile, $list);
		$realfile = $tfile;
	}

	$fpr = lfopen($realfile, "rb");

	if (!$fpr) {
		print_or_write($fd, "fCouldn't open $realfile\n\n");

		return false;
	}

	print_or_write($fd, "s");

	while (!feof($fpr)) {
		$written = print_or_write($fd, fread($fpr, 8092));
		if ($written <= 0) {
			break;
		}
	}

	// Just send a newline so that the fgets will break after reading.
	// This has to be removed after the file is read.
	print_or_write($fd, "\n");

	fclose($fpr);

	fileserv_unlink_if_tmp($realfile);

	return true;
}

function notify_admin($action, $parent, $child)
{
	$cclass = $child->get__table();
	$cname = $child->nname;
	$pclass = $parent->getClass();
	$pname = $parent->nname;

	$not = new notification(null, null, 'client-admin');
	$not->get();

	if (!array_search_bool($cclass, $not->class_list)) {
		return;
	}

	$subject = "$cclass $cname was $action to $pclass $pname ";
	send_mail_to_admin($subject, $subject);
}

function trafficGetIndividualObjectTotal($list, $firstofmonth, $today, $name)
{
	$tot = 0;

	foreach ((array)$list as $t) {
		list($nname, $oldtime, $newtime) = explode(":", $t->nname);

		if ($oldtime >= $firstofmonth && $oldtime < $today) {
			dprint(@ strftime("%c", "$oldtime") . ": ");
			dprint($t->traffic_usage);
			dprint("\n");

			$tot += $t->traffic_usage;
		}
	}

	return $tot;
}

function get_last_month_and_year()
{
	$month = @ date("n");
	$year = @ date("Y");

	if ($month == 1) {
		$month = 12;
		$year = $year - 1;
	} else {
		$month = $month - 1;
	}

	return array($month, $year);
}

function add_to_log($file)
{
	global $sgbl;

	$string = time();
	$d = @ date("Y-M-d H:i");
	$string = "{$string} {$d} {$sgbl->__lxlabs_marker}\n";

	lfile_put_contents($file, $string, FILE_APPEND);
}

function findServerTraffic()
{
	global $login;

	$sq = new Sqlite(null, 'vps');
	$list = $login->getList('pserver');

	foreach ($list as $l) {
		$res = $sq->getRowsWhere("syncserver = '$l->nname'",
			array('used_q_traffic_usage', 'used_q_traffic_last_usage'));
		$tusage = 0;
		$tlastusage = 0;

		foreach ($res as $r) {
			$tusage += $r['used_q_traffic_usage'];
			$tlastusage += $r['used_q_traffic_last_usage'];
		}

		$l->used->server_traffic_usage = $tusage;
		$l->used->server_traffic_last_usage = $tlastusage;
		$l->setUpdateSubaction();
		$l->write();
	}
}

function createMultipLeVps($param)
{
	global $$sgbl;

	$adminpass = $param['vps_admin_password_f'];
	$template = $param['vps_template_name_f'];
	$one_ip = $param['vps_one_ipaddress_f'];
	$base = $param['vps_basename_f'];
	$count = $param['vps_count_f'];

	lxshell_background($sgbl->__path_php_path, "../bin/multicreate.php", "--admin-password={$adminpass}", "--v-template_name={$template}", "--count=$count", "--basename=$base", "--v-one_ipaddress=$one_ip");
}

function collect_quota_later()
{
	createRestartFile("lxcollectquota");
}

function exec_justdb_collectquota()
{
	lxshell_background("__path_php_path", "../bin/collectquota.php", "--just-db=true");
}

function setup_ssh_channel($source, $destination, $actualname)
{
	$cont = rl_exec_get(null, $source, "get_scpid", array());
	$cont = rl_exec_get(null, $destination, "setup_scpid", array($cont));
	$cont = rl_exec_get(null, $source, "setup_knownhosts", array("$actualname, $cont"));

	return $cont;
}

function exec_vzmigrate($vpsid, $newserver, $ssh_port)
{
	global $global_shell_error;

	$username = '__system__';

	$ssh_port = trim($ssh_port);
	$ssh_string = null;

	if ($ssh_port !== "22") {
		$ssh_string = "--ssh=\"-p $ssh_port\"";
	}

	do_exec_exec($username, null, "vzmigrate $ssh_string -r yes $newserver $vpsid", $out, $err, $ret, null);

	return array($ret, $global_shell_error);
}

function getResourceOstemplate(&$vlist, $ttype = 'all')
{
	$olist = vps::getVpsOsimage(null, "openvz");
	$olist = array_keys($olist);
	$xlist = vps::getVpsOsimage(null, "xen");
	$xlist = array_keys($xlist);

	if ($ttype === 'openvz' || $ttype === 'all') {
		$vlist['openvzostemplate_list'] = array('U', $olist);
	}

	if ($ttype === 'xen' || $ttype === 'all') {
		$vlist['xenostemplate_list'] = array('U', $xlist);
	}
}

function get_scpid()
{
	$home = os_get_home_dir("root");
	$file = "$home/.ssh/id_dsa";

	if (!lxfile_exists($file)) {
		lxshell_return("ssh-keygen", "-d", "-q", "-N", null, "-f", $file);
	}

	return lfile_get_contents("$file.pub");
}

function setup_knownhosts($cont)
{
	$home = os_get_home_dir("root");
	lfile_put_contents("$home/.ssh/known_hosts", "$cont\n", FILE_APPEND);
}

function setup_scpid($cont)
{
	global $global_dontlogshell;

	$global_dontlogshell = true;
	$home = os_get_home_dir("root");
	$file = "$home/.ssh/authorized_keys2";

	lxfile_mkdir("$home/.ssh");
	lxfile_unix_chmod("$home/.ssh", "0700");
	addLineIfNotExistInside($file, "\n$cont", '');
	lxfile_unix_chmod($file, "0700");
	$global_dontlogshell = false;

	return lfile_get_contents("/etc/ssh/ssh_host_rsa_key.pub");
}

function remove_scpid($cont)
{
	$home = os_get_home_dir("root");
	$file = "$home/.ssh/authorized_keys2";
	$list = lfile_trim($file);

	foreach ($list as $l) {
		if (!$l) {
			continue;
		}
		if ($l === $cont) {
			continue;
		}
		$nlist[] = $l;
	}

	lfile_put_contents($file, implode("\n", $nlist) . "\n");
}

function lxguard_clear($list)
{

}

function lxguard_main($clearflag = false, $since = false)
{
	global $sgbl;


	$hl_file = "/home/kloxo/lxguard/hitlist.info";

	if ((file_exists($hl_file)) && (strpos(file_get_contents($hl_file), "\n\"") !== false)) {
		exec("sh /script/fix-lxguardhit-db");

	}

	include "./lib/html/lxguardincludelib.php";

	$lxgpath = "{$sgbl->__path_home_root}/lxguard";
	lxfile_mkdir($lxgpath);

	$newtime = time();

	if ($since !== false) {
		$oldtime = time() - intval($since);
	} else {
		if (file_exists("{$lxgpath}/hitlist.info")) {
			// MR -- since 10 minutes
			$oldtime = time() - (60 * 10);
		} else {
			// MR -- 3 months -- change 1 month
			$oldtime = time() - (60 * 60 * 24 * 30 * 1);
		}
	}

	$rmt =  array_map('trim', lfile_get_unserialize("{$lxgpath}/hitlist.info"));

	if ($rmt) {
		$oldtime = max((int)$oldtime, (int)$rmt->ddate);
	}

	$list = array_map('trim', lfile_get_unserialize("{$lxgpath}/access.info"));

	$type = array('sshd' => '/var/log/secure', 'pure-ftpd' => '/var/log/messages', 'vpopmail' => '/var/log/maillog');

	foreach ($type as $key => $file) {
		if (file_exists($file)) {
			$fp = fopen($file, "r");
			$fsize = filesize($file);

			$ret = FindRightPosition($fp, $fsize, $oldtime, $newtime, "getTimeFromSysLogString");

			if ($ret) {
				if ($key === 'sshd') {
					parse_ssh_log($fp, $list);
				} elseif ($key === 'pure-ftpd') {
					parse_ftp_log($fp, $list);
				} elseif ($key === 'vpopmail') {
					parse_smtp_log($fp, $list);
				}

				lfile_put_serialize("{$lxgpath}/access.info", $list);
			}
		}
	}

	get_total($list, $total);

//	dprintr($list['192.168.1.11']);

	dprint_r("Debug: Total: " . count($total) . "\n");

	$deny = get_deny_list($total);
	$hdn = array_map('trim', lfile_get_unserialize("{$lxgpath}/hostdeny.info"));
	$deny = lx_array_merge(array($deny, $hdn));

	$str_host = null;
	$str_tcprules = null;
	$str_spamdyke = null;

	$note = "## blocked IP enough to use 'null routing'\n";

	// MR -- remove blackhole blocked
	exec("sh /script/remove-blackhole-block");

//	exec("cat /etc/tcprules.d/tcp.smtp|grep -v ':deny'|grep -v '# MR'|grep -E ':allow,|:deny,'", $out);
	exec("cat /etc/tcprules.d/tcp.smtp|grep -E ':allow,|:deny,' > /etc/tcprules.d/tcp.smtp2; mv -f /etc/tcprules.d/tcp.smtp2 /etc/tcprules.d/tcp.smtp");

	foreach ($deny as $k => $v) {
		if (csb($k, "127")) {
			continue;
		}

		// MR -- add blackhole blocked
		exec("sh /script/add-blackhole-block {$k}");

		// MR -- make sure no LF
		$k = str_replace("\n", "", $k);

		$str_host .= "ALL : $k\n";
		$str_tcprules .= "$k:deny\n";
	//	$str_tcprules .= "$k:allow,RBLSMTP=\"Your Are Blocked. Go away!\"\n";
		$str_spamdyke .= "$k\n";
	}

	dprint("Debug: \str_host is:\n$str_host\n");

	$start_host[] = "###Start Program Hostdeny config Area";
	$start_str_host = $start_host[0];
	$end_host[] = "###End Program HostDeny config Area";
	$end_str_host = $end_host[0];

	// MR -- no need this action where enough 'route host'
	file_put_between_comments("root", $start_host, $end_host, $start_str_host, $end_str_host, "/etc/hosts.deny", $str_host);
//	file_put_between_comments("root", $start_host, $end_host, $start_str_host, $end_str_host, "/etc/hosts.deny", $note);

	// MR -- no need this action where enough 'route host'
	$start_tcprules[] = "###Start Program tcp.smtp config Area";
	$start_str_tcprules = $start_tcprules[0];
	$end_tcprules[] = "###End Program tcp.smtp config Area";
	$end_str_tcprules = $end_tcprules[0];

	file_put_between_comments("root", $start_tcprules, $end_tcprules, $start_str_tcprules, $end_str_tcprules, "/etc/tcprules.d/tcp.smtp", $str_tcprules);
//	file_put_between_comments("root", $start_tcprules, $end_tcprules, $start_str_tcprules, $end_str_tcprules, "/etc/tcprules.d/tcp.smtp", $note);
	exec("/usr/bin/qmailctl cdb");

	// MR -- no need this action where enough 'route host'
	file_put_contents('/var/qmail/spamdyke/blacklist_ip', $str_spamdyke);
//	file_put_contents('/var/qmail/spamdyke/blacklist_ip', '');

	if ($clearflag) {
		lxfile_rm("{$lxgpath}/access.info");
		$rmt = new Remote();
		$rmt->hl = $total;
		$rmt->ddate = time();
		lfile_put_serialize("{$lxgpath}/hitlist.info", $rmt);
	}

	return $list;
}

function lxguard_save_hitlist($hl)
{
	global $sgbl;

	include "./lib/html/lxguardincludelib.php";

	$lxgpath = "{$sgbl->__path_home_root}/lxguard";
	lxfile_mkdir($lxgpath);

	$rmt = new Remote();
	$rmt->hl = $hl;
	$rmt->ddate = time();

	lfile_put_serialize("{$lxgpath}/hitlist.info", $rmt);

	lxguard_main();
}

// --- move from kloxo/httpdocs/lib/html/updatelib.php

function fix_domainkey($nolog = null)
{
	$c = db_get_count('domain', "nname LIKE '%%'");

	if (intval($c) > 0) {
		log_cleanup("Fix Domainkeys", $nolog);
		log_cleanup("- Fix process", $nolog);

		$svm = new ServerMail(null, null, "localhost");
		$svm->get();
		$svm->domainkey_flag = 'on';
		$svm->setUpdateSubaction('update');
		$svm->was();
	}
}

function fix_move_to_client()
{
	lxshell_php("../bin/fix/fixmovetoclient.php");
}

function addcustomername()
{
	global $sgbl;

	lxshell_return($sgbl->__path_php_path, "../bin/misc/addcustomername.php");
}

function fix_phpini($nolog = null)
{
	global $sgbl;

	log_cleanup("Fix php.ini", $nolog);
	log_cleanup("- Fix process", $nolog);

	lxshell_return($sgbl->__path_php_path, "../bin/fix/fixphpini.php");
}

function switchtoaliasnext()
{
	global $gbl, $sgbl;

	$driverapp = $gbl->getSyncClass(null, 'localhost', 'web');

	if ($driverapp !== 'lighttpd') {
		return;
	}

	lxfile_cp("../file/lighttpd/lighttpd.conf", "/etc/lighttpd/lighttpd.conf");
	lxshell_return($sgbl->__path_php_path, "../bin/fix/fixweb.php");

}

function fix_awstats($nolog = null)
{
	global $sgbl;

	log_cleanup("Fix awstats", $nolog);
	log_cleanup("- Fix process", $nolog);

	lxshell_return($sgbl->__path_php_path, "../bin/fix/fixweb.php");
}

function fixdomainipissue()
{
	global $sgbl;

	lxshell_return($sgbl->__path_php_path, "../bin/fix/fixweb.php");
}

function fixrootquota()
{
	exec("setquota -u root 0 0 0 0 -a");
}

function fixtotaldiskusageplan()
{
	global $login;

	initProgram('admin');

	$login->loadAllObjects('resourceplan');

	$list = $login->getList('resourceplan');

	foreach ($list as $l) {
		if (!$l->priv->totaldisk_usage || $l->priv->totaldisk_usage === '-') {
			$l->priv->totaldisk_usage = $l->priv->disk_usage;
			$l->setUpdateSubaction();
			$l->write();
		}
	}
}

function fixcmlistagain()
{
	global $sgbl;

	lxshell_return($sgbl->__path_php_path, "../bin/common/generatecmlist.php");
}

function fixcmlist()
{
	global $sgbl;

	lxshell_return($sgbl->__path_php_path, "../bin/common/generatecmlist.php");
}

function fixcgibin()
{
	global $sgbl;

	lxshell_return($sgbl->__path_php_path, "../bin/fix/fixcgibin.php");
}

function fixsimpledocroot()
{
	global $sgbl;

	lxshell_return($sgbl->__path_php_path, "../bin/fix/fixsimpledocroot.php");
}

function fixadminuser()
{
	global $sgbl;

	lxshell_return($sgbl->__path_php_path, "../bin/fix/fixadminuser.php");
}

function fixphpinfo()
{
	global $sgbl;

	lxshell_return($sgbl->__path_php_path, "../bin/fix/fixweb.php");
}

function fixdirprotectagain()
{
	global $sgbl;

	lxshell_return($sgbl->__path_php_path, "../bin/fix/fixweb.php");
}

function fixdomainhomepermission()
{
	global $sgbl;

	lxshell_return($sgbl->__path_php_path, "../bin/fix/fixweb.php");
}

function createOSUserAdmin($nolog = null)
{
	log_cleanup("- Create OS system user admin", $nolog);

	if (!posix_getpwnam('admin')) {
		log_cleanup("- User admin created", $nolog);
		os_create_system_user('admin', randomString(7), 'admin', '/sbin/nologin', "/home/admin");
	} else {
		log_cleanup("- User admin exists", $nolog);
	}

	exec("chmod 751 /home/admin");
}

function setWatchdogDefaults($nolog = null)
{
	log_cleanup("Set Watchdog defaults", $nolog);
	log_cleanup("- Set process", $nolog);

	watchdog::addDefaultWatchdog('localhost');
}

function fixMySQLRootPassword($nolog = null)
{
	log_cleanup("Fix MySQL root password", $nolog);
	log_cleanup("- Fix process", $nolog);

	$a = null;
	fix_mysql_root_password('localhost');
	$dbadmin = new Dbadmin(null, 'localhost', "mysql___localhost");
	$dbadmin->get();
	$pass = $dbadmin->dbpassword;
	$a['mysql']['dbpassword'] = $pass;

	slave_save_db("dbadmin", $a);
}

function createFlagDir($nolog = null)
{
	log_cleanup("- Create flag dir", $nolog);
	lxfile_mkdir("__path_program_etc/flag");
}

function fixIpAddress($nolog = null)
{
	log_cleanup("Fix IP Address", $nolog);
	log_cleanup("- Fix process", $nolog);

	lxshell_return("lxphp.exe", "../bin/fixIpAddress.php");
}

function fixservice($nolog = null)
{
	log_cleanup("Fix Services", $nolog);
	log_cleanup("- Fix process", $nolog);

	lxshell_return("__path_php_path", "../bin/fix/fixservice.php");
}

function fixsslca()
{
	lxshell_return("__path_php_path", "../bin/fix/fixweb.php");
}

function dirprotectfix()
{
	lxshell_return("__path_php_path", "../bin/fix/fixdirprotect.php");
}

function cronfix()
{
	lxshell_return("__path_php_path", "../bin/cronfix.php");
}

function changetoclient()
{
	global $gbl, $sgbl;

//	exec("service xinetd stop");
	exec("service pure-ftpd stop");
	lxshell_return($sgbl->__path_php_path, "../bin/changetoclientlogin.php");
//	lxshell_return($sgbl->__path_php_path, "../bin/misc/fixftpuserclient.php");
	lxshell_return($sgbl->__path_php_path, "../bin/fix/fixftpuser.php");
//	restart_service("xinetd");
	exec("service pure-ftpd start");
//	$driverapp = $gbl->getSyncClass(null, 'localhost', 'web');

//	createRestartFile($driverapp);
	createRestartFile("restart-web");
}

function fix_dns_zones()
{
//	global $gbl, $sgbl, $login, $ghtml;

	return;
/*
	initProgram('admin');

	$flag = "__path_program_root/etc/flag/dns_zone_fix.flag";

	if (lxfile_exists($flag)) {
		return;
	}

	lxfile_touch($flag);

	$login->loadAllObjects('dns');
	$list = $login->getList('dns');

	foreach ($list as $l) {
		fixupDnsRec($l);
	}

	$login->loadAllObjects('dnstemplate');
	$list = $login->getList('dnstemplate');

	foreach ($list as $l) {
		fixupDnsRec($l);
	}
*/
}

function fixupDnsRec($l)
{
	$l->dns_record_a = null;

	foreach ($l->cn_rec_a as $k => $v) {
		$tot = new dns_record_a(null, null, "cn_$v->nname");
		$tot->ttype = "cname";
		$tot->hostname = $v->nname;
		$tot->param = $v->param;
		$l->dns_record_a["cn_$v->nname"] = $tot;
	}

	foreach ($l->mx_rec_a as $k => $v) {
		$tot = new dns_record_a(null, null, "mx_$v->nname");
		$tot->ttype = "mx";
		$tot->hostname = $l->nname;
		$tot->param = $v->param;
		$tot->priority = $v->nname;
		$l->dns_record_a["mx_$v->nname"] = $tot;
	}

	foreach ($l->ns_rec_a as $k => $v) {
		$tot = new dns_record_a(null, null, "ns_$v->nname");
		$tot->ttype = "ns";
		$tot->hostname = $v->nname;
		$tot->param = $v->nname;
		$l->dns_record_a["ns_$v->nname"] = $tot;
	}

	foreach ($l->txt_rec_a as $k => $v) {
		$tot = new dns_record_a(null, null, "txt_$v->nname");
		$tot->ttype = "txt";
		$tot->hostname = $v->nname;
		$tot->param = $v->param;
		$l->dns_record_a["txt_$v->nname"] = $tot;
	}

	foreach ($l->a_rec_a as $k => $v) {
		$tot = new dns_record_a(null, null, "a_$v->nname");
		$tot->ttype = "a";
		$tot->hostname = $v->nname;
		$tot->param = $v->param;
		$l->dns_record_a["a_$v->nname"] = $tot;
	}

	$l->setUpdateSubaction();

	$l->write();
}

function installEasyinstaller($nolog = null)
{
	global $sgbl, $login;

	// Install/Update easyinstaller if needed or remove easyinstaller when easyinstaller is disabled.
	// Added in Kloxo 6.1.4

	log_cleanup("Initialize 'Easy Installer'", $nolog);

	//--- trick for no install on kloxo install process
	if (lxfile_exists("/var/cache/kloxo/kloxo-install-disableeasyinstaller.flg")) {
		log_cleanup("- 'Easy Installer' is disabled by Flag", $nolog);
		exec("echo 1 > ../etc/flag/disableeasyinstaller.flg");

		return;
	}

	if ($sgbl->is_this_master()) {
		$gen = $login->getObject('general')->generalmisc_b;
	//	$diflag = $gen->isOn('disableeasyinstaller');
		log_cleanup("- 'Easy Installer' is disabled by Flag", $nolog);
		exec("echo 1 > ../etc/flag/disableeasyinstaller.flg");
	} else {
	//	$diflag = false;
		log_cleanup("- 'Easy Installer' is not disabled by Flag", $nolog);
		lxfile_rm("../etc/flag/disableeasyinstaller.flg");
	}

	if (lxfile_exists("../etc/flag/disableeasyinstaller.flg")) {
		log_cleanup("- 'Easy Installer' is disabled, removing 'Easy Installer'", $nolog);
		lxfile_rm_rec("/home/kloxo/httpd/easyinstaller/");
		lxfile_rm_rec("/home/kloxo/httpd/easyinstallerdata/");
		exec("cd /var/cache/kloxo/ ; rm -f easyinstaller*.tar.gz;");

		return;
	} else {
		if (!lxfile_exists("__path_kloxo_httpd_root/easyinstallerdata")) {
			log_cleanup("- Update 'Easy Installer' data", $nolog);
			easyinstaller_data_update();
		}

		if (lfile_exists("../etc/remote_easyinstaller")) {
			log_cleanup("- Remote 'Easy Installer' detected, removing 'Easy Installer'", $nolog);
			lxfile_rm_rec("/home/kloxo/httpd/easyinstaller/");
			exec("cd /var/cache/kloxo/ ; rm -f easyinstaller*.tar.gz;");

			return;
		}

		// Line below Removed in Kloxo 6.1.4
		return;
	/*
		log_cleanup("- Create easyinstaller dir", $nolog);
		lxfile_mkdir("__path_kloxo_httpd_root/easyinstaller");

		if (!lxfile_exists("__path_kloxo_httpd_root/easyinstaller/wordpress")) {
			log_cleanup("- Install/Update easyinstaller", $nolog);
			lxshell_php("../bin/easyinstaller-update.php");
		}

		return;
	*/
	}
}

function setDefaultPages($nolog = null)
{
	log_cleanup("Initialize some skeletons", $nolog);

	$httpdpath = "/home/kloxo/httpd";
	$basefilepath = "../file";
	$filepath = "{$basefilepath}/pages";
	$hdocspath = "../httpdocs";

	$sourcezip = "{$basefilepath}/skeleton.zip";
	$targetzip = "{$httpdpath}/skeleton.zip";

	$pages = array("default", "disable", "webmail", "cp", "error");

	$newer = false;

	if (file_exists($sourcezip)) {
		if (!checkIdenticalFile($sourcezip, $targetzip)) {
		//	log_cleanup("- Copy $sourcezip to $targetzip", $nolog);
			log_cleanup("- Copy $sourcezip to $httpdpath", $nolog);
			exec("'cp' -rf $sourcezip $targetzip");
			$newer = true;
		}
	}

	foreach ($pages as $k => $p) {
		if (!file_exists("{$httpdpath}/{$p}")) {
			lxfile_mkdir("{$httpdpath}/{$p}");
		}

		if ($p !== 'error') {
			log_cleanup("- Php files for {$p} web page", $nolog);
			lxfile_cp(getLinkCustomfile($filepath, "{$p}_inc.php"), "{$httpdpath}/{$p}/inc.php");
			lxfile_cp(getLinkCustomfile($filepath, "default_index.php"), "{$httpdpath}/{$p}/index.php");
		}

		log_cleanup("- Skeleton for {$p} web page", $nolog);
		lxshell_unzip("__system__", "{$httpdpath}/{$p}/", $targetzip);

		log_cleanup("- robots.txt for {$p} web page", $nolog);
		lxfile_cp(getLinkCustomfile($filepath, "default_robots.txt"), "{$httpdpath}/{$p}/robots.txt");

	}

	setKloxoHttpdChownChmod($nolog);

	log_cleanup("- Php files for login web page", $nolog);

	if (!file_exists("{$hdocspath}/login")) {
		lxfile_mkdir("{$hdocspath}/login");
		lxfile_unix_chown("{$hdocspath}/login", "lxlabs:lxlabs");
		lxfile_unix_chmod("{$hdocspath}/login", "0755");
	}

	lxfile_cp(getLinkCustomfile($filepath, "default_index.php"), "{$hdocspath}/login/index.php");
	lxfile_cp(getLinkCustomfile($filepath, "login_inc.php"), "{$hdocspath}/login/inc.php");
	lxfile_cp(getLinkCustomfile($filepath, "login_inc2.php"), "{$hdocspath}/login/inc2.php");

	lxfile_unix_chown("{$hdocspath}/login/index.php", "lxlabs:lxlabs");
	lxfile_unix_chmod("{$hdocspath}/login/index.php", "0644");
	lxfile_unix_chown("{$hdocspath}/login/inc.php", "lxlabs:lxlabs");
	lxfile_unix_chmod("{$hdocspath}/login/inc.php", "0644");
	lxfile_unix_chown("{$hdocspath}/login/inc2.php", "lxlabs:lxlabs");
	lxfile_unix_chmod("{$hdocspath}/login/inc2.php", "0644");

	log_cleanup("- Skeleton for panel login web page", $nolog);
	lxshell_unzip("__system__", "{$hdocspath}/login", $sourcezip);

	log_cleanup("- Skeleton for panel root web page", $nolog);
	lxshell_unzip("__system__", "{$hdocspath}", $sourcezip);

	log_cleanup("- Files for error web pages", $nolog);
	lxfile_unix_chown("{$hdocspath}/error", "lxlabs:lxlabs");
	lxfile_unix_chmod("{$hdocspath}/error", "0755");

	log_cleanup("- Skeleton for error web pages", $nolog);
	lxshell_unzip("__system__", "{$hdocspath}/error", $sourcezip);

	log_cleanup("- Copy error web pages to '{$httpdpath}/error'", $nolog);
	exec("cp -rf {$hdocspath}/error $httpdpath");

	$usersourcezip = "{$basefilepath}/user-skeleton.zip";
	$usertargetzip = "{$httpdpath}/user-skeleton.zip";

	if (lxfile_exists($usersourcezip)) {
		if (!checkIdenticalFile($usersourcezip, $usertargetzip)) {
			log_cleanup("- Copy $usersourcezip to $usertargetzip", $nolog);
			exec("'cp' -rf $usersourcezip $usertargetzip");
		} else {
			log_cleanup("- No new user-skeleton", $nolog);
		}
	} else {
		log_cleanup("- No exists user-skeleton", $nolog);
	}

	$sourcelogo = "{$basefilepath}/file/user-logo.png";
	$targetlogo = "{$httpdpath}/user-logo.png";

	if (lxfile_exists($sourcelogo)) {
		if (!checkIdenticalFile($sourcelogo, $targetlogo)) {
			lxfile_cp($sourcelogo, $targetlogo);

			foreach ($pages as $k => $p) {
				log_cleanup("- Copy user-logo for {$p}", $nolog);
				lxfile_cp($targetlogo, "{$httpdpath}/{$p}/images/logo.png");
			}
		} else {
			log_cleanup("- No new user-logo", $nolog);
		}
	} else {
		log_cleanup("- No exists user-logo", $nolog);
	}
}

function setDomainPages($nolog = null)
{
	// MR -- TODO: on next version (6.5.1)
}

function getPhpVersion()
{
	exec("php -v|grep 'PHP'|grep '(built:'|awk '{print $2}'", $out, $ret);

	// MR -- 'php -v' may not work when php 5.4/5.5 using php.ini from 5.2/5.3
	if ($ret === 0) {
		return $out[0];
	} else {
		return '5.4.0';
	}
}

function getRpmBranchInstalled($rpm)
{
	$a = getRpmBranchList($rpm);

	if (!$a) {
		return;
	}

	foreach ($a as $k => $e) {
		if (strpos($e, 'php') !== false) {
			if (isRpmInstalled($e)) {
				return $e;
			} else {
				if (isRpmInstalled("{$e}-cli")) {
					return $e;
				}
			}
		} else {
			if (isRpmInstalled($e)) {
				return $e;
			}
		}
	}
}

function getRpmBranchInstalledOnList($rpm)
{
	$a = getListOnList($rpm);

	foreach ($a as $k => $e) {
		$s = preg_replace('/(.*)\_\(as\_(.*)\)/', '$1', $e);

		if (strpos($s, 'php') !== false) {
			if (isRpmInstalled($s)) {
				return $e;
			} else {
				if (isRpmInstalled("{$s}-cli")) {
					return $e;
				}
			}
		} else {
			if (isRpmInstalled($s)) {
				return $e;
			}
		}

	}
}

function getRpmBranchList($pname)
{
	$p = "../etc/list";
	$f = getLinkCustomfile($p, "{$pname}.lst");

//	if (!file_exists($f)) { return; }
	if (!file_exists($f)) {
		$c = $pname;
	} else {
		$c = trimSpaces(file_get_contents($f));
	}

	$a = explode(",", $c);

	if (!$a) {
		$a = array($c);
	}

	$n = array();

	foreach ($a as $b) {
		$t = preg_replace('/(.*)\_\(as\_(.*)\)/', '$1', $b);
		$n[] = $t;
	}

	return $n;
}

function getRpmVersion($rpmname)
{

	// MR -- use '-qa' because need no output if package not exits
	exec("rpm -qa --qf '%{VERSION}\n' {$rpmname}", $out);

	if (count($out) > 0) {
		$ver = $out[0];
	} else {
		$ver = '0.0.0';
	}

	return $ver;
}

function getRpmVersionFromYum($rpmname)
{
	exec("yum list {$rpmname}|grep '{$rpmname}.'|awk '{print \$2}'|awk -F'-'  '{print \$1}'", $ver);

	if (strpos($ver[0], 'Error:') !== false) {
		$ret = '0.0.0';
	} else {
		$ret = $ver[0];
	}

	return $ret;
}

function setRpmInstalled($rpmname)
{
	lxshell_return("yum", "-y", "install", $rpmname);
}

function setRpmRemoved($rpmname)
{
	global $login;

	if (!isRpmInstalled($rpmname)) {
		return;
	}

	$ret = lxshell_return("rpm", "-e", "--nodeps", $rpmname);

	if ($ret) {
		throw new lxException($login->getThrow("remove_failed"), '', $rpmname);
	}
}

function setRpmRemovedViaYum($rpmname)
{
	global $login;

	if (!isRpmInstalled($rpmname)) {
		return;
	}

	$ret = lxshell_return("yum", "-y", "remove", $rpmname);

	if ($ret) {
		throw new lxException($login->getThrow("remove_failed"), '', $rpmname);
	}
}

function setRpmReplaced($rpmname, $replacewith)
{
	global $login;

	$ret = lxshell_return("yum", "-y", "replace", $rpmname, "--replace-with={$replacewith}");

	if ($ret) {
		throw new lxException($login->getThrow("replace_failed"), '', "{$rpmname} => {$replacewith}");
	}
}

function isRpmInstalled($rpmname)
{
	exec("rpm -qa {$rpmname}", $out);

	if (count($out) > 0) {
		return true;
	} else {
		return false;
	}
}

function isPhpModuleInstalled($module)
{
	$phpbranch = getRpmBranchInstalled('php');

	$list = array("{$phpbranch}-{$module}", "php-{$module}");

	foreach ($list as &$l) {
		$ret = isRpmInstalled($l);

		if (!$ret) {
			return true;
		}
	}

	return false;
}

function isPhpModuleActive($module, $ininamelist = null)
{
//	$srcpath = '/opt/configs/phpini/etc/php.d';
	$trgtpath = '/etc/php.d';

	$ininamelist = ($ininamelist) ? $ininamelist : array($module);

	$installed = isPhpModuleInstalled($module);

	if ($installed) {
		foreach ($ininamelist as &$i) {
			if (file_exists("{$trgtpath}/{$i}.noini")) {
				lxfile_rm("{$trgtpath}/{$i}.noini");
			}

			if (file_exists("{$trgtpath}/{$i}.ini")) {
				return true;
			}
		}
	}

	return false;
}

function setPhpModuleActive($module, $ininamelist = null)
{
	$phpbranch = getRpmBranchInstalled('php');

	$list = array("{$phpbranch}-{$module}", "php-{$module}");

	$srcpath = '/opt/configs/phpini/etc/php.d';
	$trgtpath = '/etc/php.d';

	$ininamelist = ($ininamelist) ? $ininamelist : array($module);

	$installed = isPhpModuleInstalled($module);

	if (!$installed) {
		foreach ($list as &$l) {
			setRpmInstalled($l);
		}
	}

	foreach ($ininamelist as &$i) {
		if (!file_exists("{$srcpath}/{$i}.ini")) {
			lxfile_cp(getLinkCustomfile("{$srcpath}", "{$i}.ini"), "{$trgtpath}/{$i}.ini");
		}
	}
}

function setPhpModuleInactive($module, $ininamelist = null)
{
//	$srcpath = '/opt/configs/phpini/etc/php.d';
	$trgtpath = '/etc/php.d';

	$ininamelist = ($ininamelist) ? $ininamelist : array($module);

	foreach ($ininamelist as &$i) {
		if (file_exists("{$trgtpath}/{$i}.ini")) {
			lxfile_mv("{$trgtpath}/{$i}.ini", "{$trgtpath}/{$i}.nonini");
		}
	}
}

function setInitialAllDnsConfigs($nolog = null)
{
	$list = getAllRealDnsDriverList();

	foreach ($list as $k => $v) {
		setInitialDnsConfig($v, $nolog);
	}
}

function setInitialDnsConfig($type, $nolog = null)
{
	$fpath = "../file";

	if (!file_exists("{$fpath}/{$type}")) {
		return;
	}

	setCopyDnsConfFiles($type);

	if ($type === 'pdns') {
		PreparePowerdnsDb($nolog);
	} elseif ($type === 'mydns') {
		PrepareMyDnsDb($nolog);
	} else {
		$path = "/opt/configs/{$type}/conf";

		if (!file_exists("{$path}/defaults")) {
			lxfile_mkdir("{$path}/defaults");
		}

		if (($type === 'nsd') || ($type === 'djbdns')) {
			$newlist = array("master", "slave", "reverse");

			foreach ($newlist as &$n) {
				if (!file_exists("{$path}/{$n}")) {
					lxfile_mkdir("{$path}/{$n}");
				}
			}
		}

		if ($type === 'yadifa') {
			$newlist = array("keys", "xfr");

			foreach ($newlist as &$n) {
				if (!file_exists("{$path}/{$n}")) {
					lxfile_mkdir("{$path}/{$n}");
				}
			}
		}

		if ($type === 'bind') {
			if (!file_exists("/var/log/named")) {
				exec("mkdir -p /var/log/named");
				exec("chown named:root /var/log/named; chmod 1777 /var/log/named");
			}
		}
	}

	// MR -- remove old dirs
	if ($type !== 'djbdns') {
		$htpath_old = "/home/{$type}";
		lxfile_rm_rec($htpath_old);
	}
}

function setInitialAllWebConfigs($nolog = null)
{
	$list = getAllRealWebDriverList();

	foreach ($list as $k => $v) {
		setInitialWebConfig($v, $nolog);
		setWebDriverChownChmod($v, $nolog);
	}
}

function setInitialWebConfig($type, $nolog = null)
{
	$fpath = "../file";

	if (!file_exists("{$fpath}/{$type}")) {
		return;
	}

	if ($type === 'apache') {
		$atype = 'httpd';
	} else {
		$atype = $type;
	}

	$htpath = "/opt/configs/{$type}";
	$eatpath = "/etc/{$atype}";

	$htcpath = "{$htpath}/conf";

	log_cleanup("Initialize {$type} config", $nolog);

	$newlist = array("{$eatpath}/conf.d", "{$htpath}/tpl",
		"{$htpath}/conf", "{$htpath}/etc", "{$htpath}/etc/conf", "{$htpath}/etc/conf.d");

	foreach ($newlist as &$n) {
		if (!lxfile_exists("{$n}")) {
			log_cleanup("- Create {$n} dir", $nolog);

			lxfile_mkdir("{$n}");
		} else {
			log_cleanup("- {$n} dir already exists", $nolog);
		}
	}

	$list = array("defaults", "domains", "proxies", "globals", "toolkits");

	foreach ($list as $k => $l) {
		if (!lxfile_exists("{$htcpath}/{$l}")) {
			log_cleanup("- Create {$htcpath}/{$l} dir", $nolog);

			lxfile_mkdir("{$htcpath}/{$l}");
		}
	}

	exec("echo '## MR -- blank only' > {$htcpath}/domains/__blank__.conf");

	$oldlist = array("{$htcpath}/redirects", "{$htcpath}/exclusive", "{$htcpath}/wildcards",
		"{$htcpath}/webmails", "{$htpath}/sock", "{$htpath}/socks", "{$eatpath}/conf/kloxo",
		"{$htpath}/tmp", "{$htpath}/logs", "{$htpath}/cache");

	foreach ($oldlist as &$l) {
		if (lxfile_exists("{$l}")) {
			log_cleanup("- Remove {$l} dir", $nolog);

			lxfile_rm_rec($l);
		}
	}

	setCopyWebConfFiles($type);

	// MR -- remove old dirs
	$htpath_old = "/home/{$type}";

	lxfile_rm_rec($htpath_old);
}

function setInitialAllWebCacheConfigs($nolog = null)
{
	$list = getAllRealWebCacheDriverList();
	
	foreach ($list as $k => $v) {
		setInitialWebCacheConfig($v, $nolog);
	}
}

function setInitialWebCacheConfig($type, $nolog = null)
{
	setCopyWebCacheConfFiles($type, $nolog);

	// MR -- remove old dirs
	$htpath_old = "/home/{$type}";

	lxfile_rm_rec($htpath_old);
}

function setInitialPhpIniConfig($nolog = null)
{
	$fpath = "../file/phpini";
//	$inipath = "/opt/configs/phpini";

	exec("'cp' -rf {$fpath} /opt/configs");
}

function getInitialPhpFpmConfig($nolog = null)
{
	$d = getMultiplePhpList();

	if (isset($d)) {
		foreach ($d as $k => $v) {
			if ($v === 'php52m') {
				unset($d[$k]);
			}
		}

		$d = array_merge(array('php'), $d);
	} else {
		$d = array('php');
	}

	$a = glob("../etc/flag/use_php*.flg");

	if (count($a) > 0) {
		$b1 = basename($a[0]);
		$b2 = str_replace('.flg', '', $b1);
		$b3 = str_replace('use_', '', $b2);

		exec("sh /script/set-php-fpm {$b3}");

		return $b3;
	} else {
		foreach ($d as $k => $v) {
			exec("sh /script/set-php-fpm {$v}");

			return $v;
		}

	}

	return 'php';
}

function setKloxoCexeChownChmod($nolog = null)
{
	$webdirchmod = '755';
	$cexepath = '../cexe';

	log_cleanup("- chmod {$webdirchmod} FOR {$cexepath} AND INSIDE", $nolog);
	lxfile_unix_chmod_rec("{$cexepath}/", $webdirchmod);
}

function setWebDriverChownChmod($type, $nolog = null)
{
	if (!file_exists("/opt/configs/{$type}")) {
		return;
	}

	$webdirchmod = '755';
	$webdirchown = "root:root";

	log_cleanup("- chown {$webdirchown} FOR /opt/configs/{$type}/ AND INSIDE", $nolog);
	lxfile_unix_chown_rec("/opt/configs/{$type}/", $webdirchown);

	exec("find /opt/configs/{$type}/ -type f -name \"*.sh\" -exec chmod {$webdirchmod} \{\} \\;");
	log_cleanup("- chmod {$webdirchmod} FOR *.sh INSIDE /opt/configs/{$type}/", $nolog);
}

function setKloxoHttpdChownChmod($nolog = null)
{
	$hkhpath = "/home/kloxo/httpd";

//	log_cleanup("Set ownership and permissions for {$hkhpath} dir", $nolog);

	$httpddirchmod = '771'; // need to change to 771 for nginx-proxy
	$phpfilechmod = '644';
	$domdirchmod = '755';

//	$hkhown = 'lxlabs:lxlabs';
	$hkhown = 'apache:apache';

	lxfile_unix_chown_rec("{$hkhpath}/", "{$hkhown}");
	log_cleanup("- chown {$hkhown} FOR {$hkhpath}/ AND INSIDE", $nolog);

	exec("find {$hkhpath}/ -type f -name \"*.php*\" -exec chmod {$phpfilechmod} \{\} \\;");
	log_cleanup("- chmod {$phpfilechmod} FOR *.php* INSIDE {$hkhpath}/", $nolog);

	exec("find {$hkhpath}/ -type f  -regex " . '".*\.\(pl\|cgi\|py\|rb\)"' . " -exec chmod {$domdirchmod} \{\} \\;");
	log_cleanup("- chmod {$domdirchmod} FOR *.pl/cgi/py/rb INSIDE {$hkhpath}/", $nolog);

	exec("find {$hkhpath}/ -type d -exec chmod {$domdirchmod} \{\} \\;");
	log_cleanup("- chmod {$domdirchmod} FOR {$hkhpath}/ AND INSIDE", $nolog);

	lxfile_unix_chmod("{$hkhpath}/", $httpddirchmod);
	log_cleanup("- chmod {$httpddirchmod} FOR {$hkhpath}/", $nolog);

	if (file_exists("/home/vpopmail")) {
		exec("chmod {$domdirchmod} /home/vpopmail");
		exec("chmod {$domdirchmod} /home/vpopmail/domains");
	}

	if (file_exists("/home/lxadmin/mail")) {
		exec("chmod {$domdirchmod} /home/lxadmin");
		exec("chmod {$domdirchmod} /home/lxadmin/mail");
		exec("chmod {$domdirchmod} /home/lxadmin/mail/domains");
	}
}

function setFixChownChmod($select, $nolog = null)
{
	global $login;

	$login->loadAllObjects('client');
	$list = $login->getList('client');

	// --- for /home/kloxo/httpd dirs (defaults pages)

//	log_cleanup("Fix file permission problems for defaults pages (chown/chmod files)", $nolog);

	setKloxoCexeChownChmod($nolog);
	setKloxoHttpdChownChmod($nolog);

	$webs = array('apache', 'lighttpd', 'nginx', 'hiawatha');

	foreach ($webs as $k => $v) {
		setWebDriverChownChmod($v, $nolog);
	}

	// --- for domain dirs

	foreach ($list as $c) {
		setFixChownChmodWebPerUser($select, $c->nname, $nolog);
		setFixChownChmodMailPerUser($select, $c->nname, $nolog);
	}
}

function setFixChownChmodWebPerUser($select, $user, $nolog = null)
{
	global $login;

	$login->loadAllObjects('client');
	$list = $login->getList('client');

	foreach ($list as $c) {
		if ($c->nname === $user) {
			$clname = $c->getPathFromName('nname');

			$cdir = "/home/{$clname}";
			$dlist = $c->getList('domaina');

			break;
		}
	}

	$sdir = "/home/httpd";

	$userdirchmod = '751'; // need to change to 751 for nginx-proxy
	$phpfilechmod = '644';
	$domdirchmod = '755';
	$statsdirchmod = '777';

	exec("chown {$clname}:apache {$cdir}/");
	log_cleanup("- chown {$clname}:apache FOR {$cdir}/", $nolog);

	exec("chmod {$userdirchmod} {$cdir}/");
	log_cleanup("- chmod {$userdirchmod} FOR {$cdir}/", $nolog);

	$ks = "kloxoscript";

	if (file_exists("{$cdir}/{$ks}")) {
		exec("find {$cdir}/{$ks} -not -user apache -not -group apache -exec chown {$clname}:{$clname} \{\} \\;");
		log_cleanup("- chown {$clname}:{$clname} FOR FILES/DIRS INSIDE {$cdir}/{$ks}/ EXCEPT apache:apache", $nolog);

		exec("chown {$clname}:apache {$cdir}/{$ks}/");
		log_cleanup("- chown {$clname}:apache FOR {$cdir}/{$ks}/ DIR", $nolog);

		exec("find {$cdir}/{$ks}/ -type f -name \"*.php*\" -exec chmod {$phpfilechmod} \{\} \\;");
		log_cleanup("- chmod {$phpfilechmod} FOR *.php* FILES INSIDE {$cdir}/{$ks}/", $nolog);

		exec("find {$cdir}/{$ks} -type d -exec chmod {$domdirchmod} \{\} \\;");
		log_cleanup("- chmod {$domdirchmod} FOR {$cdir}/{$ks}/ DIR AND INSIDE", $nolog);
	}

	$docrootlist = array();

	foreach ((array)$dlist as $l) {
		$web = $l->getObject('web');
		$docroot = $web->docroot;

		if (in_array("{$cdir}/{$docroot}", $docrootlist)) {
			continue;
		}

		$dom = $web->nname;

		if (($select === "all") || ($select === 'chown')) {
			exec("find {$cdir}/{$docroot}/ -not -user apache -not -group apache -exec chown {$clname}:{$clname} \{\} \\;");
			log_cleanup("- chown {$clname}:{$clname} FOR FILES/DIRS INSIDE {$cdir}/{$docroot}/ EXCEPT apache:apache", $nolog);
		}

		if (($select === "all") || ($select === 'chmod')) {
			exec("find {$cdir}/{$docroot}/ -type f -name \"*.php*\" -exec chmod {$phpfilechmod} \{\} \\;");
			log_cleanup("- chmod {$phpfilechmod} FOR *.php* FILES INSIDE {$cdir}/{$docroot}/", $nolog);

			exec("find {$cdir}/{$docroot}/ -type f  -regex " . '".*\.\(pl\|cgi\|py\|rb\)"' . " -exec chmod {$domdirchmod} \{\} \\;");
			log_cleanup("- chmod {$domdirchmod} FOR *.pl/cgi/py/rb FILES INSIDE {$cdir}/{$docroot}/", $nolog);

			exec("find {$cdir}/{$docroot}/ -type d -exec chmod {$domdirchmod} \{\} \\;");
			log_cleanup("- chmod {$domdirchmod} FOR {$cdir}/{$docroot}/ DIR AND INSIDE", $nolog);

			// MR -- fix nginx permissions issue
			exec("find {$sdir}/{$dom}/stats -type d -exec chmod {$statsdirchmod} \{\} \\;");
			log_cleanup("- chmod {$statsdirchmod} FOR {$sdir}/{$dom}/stats DIR AND INSIDE", $nolog);
		}

		exec("chown {$clname}:{$clname} {$cdir}/{$docroot}/");
		log_cleanup("- chown {$clname}:{$clname} FOR {$cdir}/{$docroot}/ DIR", $nolog);

		if (lxfile_exists("{$cdir}/{$docroot}/cgi-bin")) {
			exec("chmod -R {$domdirchmod} {$cdir}/{$docroot}/cgi-bin");
			log_cleanup("- chmod {$domdirchmod} FOR {$cdir}/{$docroot}/cgi-bin DIR AND FILES", $nolog);
		}

		$docrootlist[] = "{$cdir}/{$docroot}";

		array_unique($docrootlist);
	}
}

function setFixChownChmodMailPerUser($select, $user, $nolog = null)
{
	global $login;

	$login->loadAllObjects('client');
	$list = $login->getList('client');

	foreach ($list as $c) {
		if ($c->nname === $user) {
			$clname = $c->getPathFromName('nname');

			$dlist = $c->getList('domaina');

			break;
		}
	}

	$mdir = "/home/lxadmin/mail/domains";
	$mailfilechmod = "600";
	$maildirchmod = "700";

	foreach ((array)$dlist as $l) {
		$web = $l->getObject('web');
		$dom = $web->nname;

		exec("chown {$clname}:{$clname} {$mdir}/{$dom}/");
		log_cleanup("- chown {$clname}:{$clname} FOR {$mdir}/{$dom}/ DIR", $nolog);

		if (($select === "all") || ($select === 'chown')) {
			exec("chown -R {$clname}:{$clname} {$mdir}/{$dom}/");
			log_cleanup("- chown {$clname}:{$clname} FOR DIR/FILES INSIDE {$mdir}/{$dom}/", $nolog);
		}

		if (($select === "all") || ($select === 'chmod')) {
			exec("find {$mdir}/{$dom}/ -type f -name \"*\" -exec chmod {$mailfilechmod} \{\} \\;");
			log_cleanup("- chmod {$mailfilechmod} FOR * FILES INSIDE {$mdir}/{$dom}/", $nolog);

			exec("find {$mdir}/{$dom}/ -type d -exec chmod {$maildirchmod} \{\} \\;");
			log_cleanup("- chmod {$maildirchmod} FOR {$mdir}/{$dom}/ DIR AND INSIDE", $nolog);
		}
	}
}

function getAllClientList($servername = null)
{
	if (!$servername) {
		$servername = "localhost";
	}

	$clientdb = new Sqlite(null, 'client');
	$sync = "syncserver = '{$servername}'";

	$cdb = $clientdb->getRowsWhere($sync, array('nname', 'cttype'));

	$users = array();

	foreach ($cdb as $k => $v) {
		$users[] = $v['nname'];
	}

	$users = array_unique($users);

	return $users;
}

function getIpfromARecord($servername, $nobase = null)
{
	$dnsdb = new Sqlite(null, 'dns');

	if (!$servername) {
		$servername = "localhost";
	}

	$sync = "syncserver = '{$servername}'";

	$d = $dnsdb->getRowsWhere($sync, array('nname', 'zone_type', 'ser_dns_record_a'));

	$z = array();

	foreach ($d as $dk => $dv) {
		$w = unserialize(base64_decode($dv['ser_dns_record_a']));

		if ($nobase) {
			foreach ($w as $wk => $wv) {
				if (($wv->ttype === 'a') || ($wv->ttype === 'aaa')) {
					if ($wv->hostname === '__base__') {
						$base = $wv->param;
						break;
					}
				}
			}
		}

		foreach ($w as $wk => $wv) {
			if (($wv->ttype === 'a') || ($wv->ttype === 'aaa')) {
				if ($nobase) {
					if ($wv->param !== $base) {
						$z[] = $wv->param;
					}
				} else {
					$z[] = $wv->param;
				}
			}
		}
	}

	$z = array_unique($z);

	return $z;
}

function getDnsMasters($servername)
{
	if (!$servername) {
		$servername = "localhost";
	}

	$dnsdb = new Sqlite(null, 'dns');
	$sync = "syncserver = '{$servername}'";

	$d = $dnsdb->getRowsWhere($sync, array('nname'));

	if (!$d) {
		return;
	}

	$addondb = new Sqlite(null, 'addondomain');

	foreach ($d as $k => $v) {
		foreach ($v as $k2 => $v2) {
			//skip if empty record
			if (!$v2) {
				continue;
			}
			
			$e[] = $v2;

			$p = "parent_clname = 'domain-{$v2}'";

			$a = $addondb->getRowsWhere($p, array('nname'));

			if (!$a) {
				continue;
			}

			foreach ($a as $k3 => $v3) {
				foreach ($v3 as $k4 => $v4) {
					//skip if empty record
					if (!$v4) {
						continue;
					}
					$e[] = $v4;
				}
			}
		}
	}

	return $e;
}

function getDnsSlaves($servername)
{
	if (!$servername) {
		$servername = "localhost";
	}

	$dnsdb = new Sqlite(null, 'dnsslave');
	$sync = "syncserver = '{$servername}'";

	$d = $dnsdb->getRowsWhere($sync, array('nname', 'master_ip'));

	if (!$d) {
		return;
	}

	$e = array();

	foreach ($d as $k => $v) {
		$t = '';
		foreach ($v as $k2 => $v2) {
			if ($t === '') {
				$t = $v2;
			} else {
				$t = $t . ':' . $v2;
				$e[] = $t;
			}
		}
	}

	return $e;
}

function getDnsReverses($servername)
{
	if (!$servername) {
		$servername = "localhost";
	}

	$dnsdb = new Sqlite(null, 'reverse');
	$sync = "syncserver = '{$servername}'";

	$d = $dnsdb->getRowsWhere($sync, array('reversename', 'nname'));

	if (!$d) {
		return;
	}

	$e = array();

	foreach ($d as $k => $v) {
		$t = '';
		foreach ($v as $k2 => $v2) {
			if ($t === '') {
				$t = $v2;
			} else {
				$t = $t . ':' . $v2;
				$e[] = $t;
			}
		}
	}

	return $e;
}

function setInitialPureftpConfig($nolog = null)
{
	log_cleanup("Initialize PureFtp service", $nolog);
	log_cleanup("- Initialize process", $nolog);

	if (!isRpmInstalled("xinetd")) {
		exec("yum install xinetd -y");
	}

	if (lxfile_exists("/etc/xinetd.d/pure-ftpd")) {
		log_cleanup("- Remove /etc/xinetd.d/pure-ftpd service file", $nolog);
		@lxfile_rm("/etc/xinetd.d/pure-ftpd");
	}

	if (lxfile_exists("/etc/xinetd.d/pureftp")) {
		log_cleanup("- Remove /etc/xinetd.d/pureftp service file", $nolog);
		@lxfile_rm("/etc/xinetd.d/pureftp");
		exec("service xinetd restart");
	}

	if (!lxfile_real("/etc/pki/pure-ftpd/pure-ftpd.pem")) {
		log_cleanup("- Install pure-ftpd ssl/tls key", $nolog);
		lxfile_mkdir("/etc/pki/pure-ftpd/");
		lxfile_cp("../file/ssl/program.pem", "/etc/pki/pure-ftpd/pure-ftpd.pem");
	}

	if (!lxfile_exists("/etc/pure-ftpd/pureftpd.pdb")) {
		log_cleanup("Make pure-ftpd user database", $nolog);
		lxfile_touch("/etc/pure-ftpd/pureftpd.passwd");
		lxshell_return("pure-pw", "mkdb");
	}

	// MR -- pure-ftpd high version (1.0.47+) pure-config.pl didn't exists

	if (is_link("/usr/sbin/pure-config.pl")) {
		unlink("/usr/sbin/pure-config.pl");
	}

	if (!lxfile_exists("/usr/sbin/pure-config.pl")) {
		$sfile = "{$sgbl->__path_program_root}/file/pure-ftd/usr/sbin/pure-config.pl";
		log_cleanup("Copy pure-config.pl to /usr/sbin", $nolog);
		copy($sfile, "/usr/sbin/pure-config.pl");
		chown($sfile, "root:root");
		chmod($sfile, 0755);
	}

	if (getServiceType('pure-ftpd') === 'init') {
		lxfile_cp("../file/pure-ftpd/etc/init.d/pure-ftpd.init", "/etc/rc.d/init.d/pure-ftpd");
	//	exec("chkconfig pure-ftpd on >/dev/null 2>&1; chmod 0755 /etc/rc.d/init.d/pure-ftpd");
		exec("sh /script/enable-service pure-ftpd >/dev/null 2>&1; chmod 0755 /etc/rc.d/init.d/pure-ftpd");
	}

	log_cleanup("- Restart pure-ftpd service", $nolog);
	createRestartFile('restart-ftp');
}

function setInitialPhpMyAdmin($nolog = null)
{
	// MR -- kloxo.pass does not exist in slave
	if (!lxfile_exists("../etc/conf/kloxo.pass")) {
		return;
	}

	log_cleanup("Initialize phpMyAdmin configfile", $nolog);
	lxfile_cp("../file/phpmyadmin/config.inc.php", "thirdparty/phpMyAdmin/config.inc.php");

	log_cleanup("- phpMyAdmin: Set db password in configfile", $nolog);
	$DbPass = file_get_contents("../etc/conf/kloxo.pass");
	$phpMyAdminCfg = "../httpdocs/thirdparty/phpMyAdmin/config.inc.php";
	$content = file_get_contents($phpMyAdminCfg);
	$content = str_replace("# Kloxo-Marker",
		"# Kloxo-Marker\n\$cfg['Servers'][\$i]['controlpass'] = '" .
		$DbPass . "';", $content);

	lfile_put_contents($phpMyAdminCfg, $content);
/*
	 // TODO: Need another way to do this (use root pass)
	 log_cleanup("- phpMyAdmin: Import PMA Database and create tables if they do not exist", $nolog);
	 exec("../sbin/kloxodb < ../httpdocs/sql/phpMyAdmin/phpMyAdmin.sql");
*/
}

function setRemoveOldDirs($nolog = null)
{
	log_cleanup("Remove Old dirs", $nolog);
	log_cleanup("- Remove process");

	if (lxfile_exists("/home/admin/domain")) {
		log_cleanup("- Remove dir /home/admin/domain/ if exists", $nolog);
		lxfile_rm_rec("/home/admin/domain/");
	}

	if (lxfile_exists("/home/admin/old")) {
		log_cleanup("- Remove dir /home/admin/old/ if exists", $nolog);
		lxfile_rm_rec("/home/admin/old/");
	}

	if (lxfile_exists("/home/admin/cgi-bin")) {
		log_cleanup("- Remove dir /home/admin/cgi-bin/ if exists", $nolog);
		lxfile_rm_rec("/home/admin/cgi-bin/");
	}

	if (lxfile_exists("/etc/skel/Maildir")) {
		log_cleanup("- Remove dir /etc/skel/Maildir/ if exists", $nolog);
		lxfile_rm_rec("/etc/skel/Maildir/new");
		lxfile_rm_rec("/etc/skel/Maildir/cur");
		lxfile_rm_rec("/etc/skel/Maildir/tmp");
		lxfile_rm_rec("/etc/skel/Maildir/");
	}

	if (lxfile_exists('kloxo.sql')) {
		log_cleanup("- Remove file kloxo.sql", $nolog);
		lunlink('kloxo.sql');
	}
}

function setInitialBinary($nolog = null)
{

	log_cleanup("Initialize Some Binary files", $nolog);

	// MR -- because no need lxrestart (also lxsuexec) so remove if exist
	exec("'rm' -rf /usr/sbin/lxrestart");

	if (!lxfile_exists("/usr/local/bin/php")) {
		log_cleanup("- Create Symlink /usr/bin/php to /usr/local/bin/php", $nolog);
		lxfile_symlink("/usr/bin/php", "/usr/local/bin/php");
	} else {
		log_cleanup("- Symlink /usr/local/bin/php already exists", $nolog);
	}
}

function setCheckPackages($nolog = null)
{
	$phpbranch = getRpmBranchInstalled('php');

	log_cleanup("Check for rpm packages", $nolog);

	// MR --remove spamdyke-utils because conflict with djbdns and not needed
	// remove qmail-pop3d-toaster because pop3 already include in courier-imap/dovecot
	$list = array("autorespond-toaster", "courier-imap-toaster", "dovecot-toaster", "daemontools-toaster",
		"ezmlm-toaster", "libdomainkeys-toaster", "libsrs2-toaster", "maildrop-toaster",
		"qmail-toaster", "ripmime", "ucspi-tcp-toaster", "vpopmail-toaster", "fetchmail", "bogofilter", "spamdyke",
		"pure-ftpd", "webalizer", "dos2unix", "rrdtool", "xinetd", "lxjailshell");

	foreach ($list as $l) {
		if ($l === '') {
			continue;
		}

		install_if_package_not_exist($l);
	}
}

function setInstallMailserver($nolog = null)
{
	log_cleanup("Initialize Mail service", $nolog);
	log_cleanup("- Initialize process", $nolog);

	if (!lxfile_exists("/etc/lxrestricted")) {
		log_cleanup("- Install /etc/lxrestricted file (lxjailshell commands restrictions)", $nolog);
		lxfile_cp("../file/lxjailshell/lxrestricted", "/etc/lxrestricted");
	}
}

function setInitialServer($nolog = null)
{
	// MR -- modified sysctl.conf because using socket instead port for php-fpm
	$pattern = "fs.file-max";
	$sysctlconf = file_get_contents("/etc/sysctl.conf");

	// MR - https://bbs.archlinux.org/viewtopic.php?pid=1002264
	// also add 'fs.aio-max-nr' for mysql 5.5 innodb aio issue
	$patch = "\n### begin -- add by Kloxo-MR\n" .
		"fs.aio-max-nr = 1048576\n" .
		"fs.file-max = 1048576\n" .
		"#vm.swappiness = 10\n" .
		"#vm.vfs_cache_pressure = 50\n" .
		"#vm.dirty_background_ratio = 15\n" .
		"#vm.dirty_ratio = 5\n" .
		"### end -- add by Kloxo-MR\n";

	if (strpos($sysctlconf, $pattern) !== false) {
		//
	} else {
		// MR -- problem with for openvz
		exec("grep envID /proc/self/status", $out, $ret);

		if ($ret === 0) {
			// no action
		} else {
			system("echo '{$patch}' >> /etc/sysctl.conf; sysctl -e -p");
		}
	}

	// MR - Change to different purpose
	// install php52s + hiawatha (also kloxong specific component) and their setting for Kloxo-MR

	// MR -- remove old Kloxo ext
	$packages = array("lxphp", "lxzend", "lxlighttpd");

	$list = implode(" ", $packages);

	exec("yum -y remove $list >/dev/null 2>&1");
	
	//Remove old webmail packages
	$packages = array(
		"kloxong-webmail-squirrelmail",
		"kloxong-webmail-telaen",
		"kloxong-webmail-horde",
		"kloxong-webmail-t-dah"
	);

	$list = implode(" ", $packages);

	exec("yum -y remove $list >/dev/null 2>&1");

	$packages = array(
		"kloxong-webmail-*.noarch",
		"kloxong-thirdparty-*.noarch", 
		"kloxong-thirdparty-*.noarch", 
		"kloxong-stats-*.noarch", 
		"kloxong-editor-*.noarch", 
		"hiawatha",
		"--exclude=kloxong-thirdparty-phpmyadmin-*.noarch",
		"--exclude=kloxong-webmail-squirrelmail.noarch",
		"--exclude=kloxong-webmail-telaen.noarch",
		"--exclude=kloxong-webmail-horde.noarch",
		"--exclude=kloxong-webmail-t-dah.noarch"
	);

	$list = implode(" ", $packages);

	exec("yum -y install $list >/dev/null 2>&1");

	exec("sh /script/fixlxphpexe");

	fix_hiawatha();
}

function fix_hiawatha()
{
	if (isServiceExists('hiawatha')) {
		$webdrv = slave_get_driver('web');

		if (strpos($webdrv, 'hiawatha') !== false) {
			exec("chkconfig hiawatha on >/dev/null 2>&1");
		} else {
			exec("chkconfig hiawatha off >/dev/null 2>&1; service hiawatha stop >/dev/null 2>&1");
		}
	}
}

function setSomePermissions($nolog = null)
{
	log_cleanup("Install/Fix Services/Permissions/Configfiles", $nolog);

	log_cleanup("- Set permissions for /usr/bin/php-cgi", $nolog);
	@lxfile_unix_chmod("/usr/bin/php-cgi", "0755");

	log_cleanup("- Set permissions for closeinput binary", $nolog);
	@lxfile_unix_chmod("../cexe/closeinput", "0755");

	log_cleanup("- Set permissions for phpsuexec.sh script", $nolog);
	@lxfile_unix_chmod("../file/phpsuexec.sh", "0755");

	log_cleanup("- Set permissions for /var/lib/php/session/ dir", $nolog);
	@lxfile_unix_chmod("/var/lib/php/session/", "777");
	exec("chmod o+t /var/lib/php/session/");

	log_cleanup("- Set permissions for /var/bogofilter/ dir", $nolog);
	if (!file_exists("/var/bogofilter")) {
		mkdir("/var/bogofilter");
	}
	lxfile_unix_chmod("/var/bogofilter/", "777");
	exec("chmod o+t /var/bogofilter/");

	log_cleanup("- Kill sisinfoc system process", $nolog);
	exec("pkill -f sisinfoc");
}

function setJailshellSystem($nolog = null)
{
	log_cleanup("Install jailshell to system", $nolog);

	if (!lxfile_exists("/usr/bin/execzsh.sh")) {
		log_cleanup("- Install process", $nolog);
		addLineIfNotExistInside("/etc/shells", "/usr/bin/lxjailshell", "");
		lxfile_cp("theme/filecore/execzsh.sh", "/usr/bin/execzsh.sh");
		lxfile_unix_chmod("/usr/bin/execzsh.sh", "0755");
	} else {
		log_cleanup("- Already exists", $nolog);
	}
}

function setSomeScript($nolog = null)
{
	log_cleanup("Execute/remove/initialize/install script", $nolog);

	if (isRpmInstalled('qmail-toaster')) {
		log_cleanup("- Execute vpopmail.sh", $nolog);
		exec("sh ../bin/misc/vpopmail.sh");
	} else {
		log_cleanup("- Execute lxpopuser.sh", $nolog);
		exec("sh ../bin/misc/lxpopuser.sh");
	}

	log_cleanup("- Initialize /home/kloxo/httpd/script dir", $nolog);
	lxfile_mkdir("/home/kloxo/httpd/script");

	log_cleanup("- Remove /home/kloxo/httpd/script dir", $nolog);
	lxfile_rm_content("/home/kloxo/httpd/script/");

	log_cleanup("- Set ownership apache:apache for /home/kloxo/httpd/script dir", $nolog);
	lxfile_unix_chown_rec("/home/kloxo/httpd/script", "apache:apache");

	log_cleanup("- Install phpinfo.php into /home/kloxo/httpd/script dir", $nolog);
	lxfile_cp("../file/script/phpinfo.php", "/home/kloxo/httpd/script/phpinfo.php");
}

function setInitialLogrotate($nolog = null)
{
	log_cleanup("Initialize logrotates", $nolog);
	log_cleanup("- Initialize process", $nolog);

	exec("cp -f ../file/logrotate/etc/logrotate.d/* /etc/logrotate.d");

	// MR -- sometimes this file corrupt and make high cpu usage
	lxfile_rm("/var/lib/logrotate.status");
}

function restart_xinetd_for_pureftp($nolog = null)
{
	log_cleanup("Restart xinetd for pureftp", $nolog);
	log_cleanup("- Restart process", $nolog);

	createRestartFile("xinetd");
}

function install_bogofilter($nolog = null)
{
	log_cleanup("Check for bogofilter", $nolog);

	if (!lxfile_exists("/var/bogofilter")) {
		log_cleanup("- Create /var/bogofilter dir if needed", $nolog);
		lxfile_mkdir("/var/bogofilter");
	}

	$dir = "/var/bogofilter";
	$wordlist = "$dir/wordlist.db";
	$kloxo_wordlist = "$dir/kloxo.wordlist.db";

	if (lxfile_exists($kloxo_wordlist)) {
		log_cleanup("- wordlist.db already exists", $nolog);

		return;
	} else {
		log_cleanup("- Prepare and download wordlist.db", $nolog);
	}


	lxfile_mkdir($dir);

	lxfile_rm($wordlist);
	$content = file_get_contents("http://download.lxcenter.org/download/wordlist.db");
	file_put_contents($wordlist, $content);
	lxfile_unix_chown_rec($dir, "lxpopuser:lxpopgroup");
	lxfile_cp($wordlist, $kloxo_wordlist);
}

function removeOtherDrivers($class = null, $nolog = null)
{
	log_cleanup("Enable the correct drivers (Service daemons)", $nolog);

	include "../file/driver/rhel.inc";

	if ($class) {
		$list[$class] = $driver[$class];
	} else {
		$list = $driver;
	}

	foreach ($list as $k => $v) {
		$driverapp = slave_get_driver($k);

		if (!$driverapp) {
			continue;
		}

		$otherlist = get_other_driver($k, $driverapp);

		if ($otherlist) {
			foreach ($otherlist as $o) {
				// MR -- TODO: need better code for web proxy
				if (strpos($o, 'proxy') !== false) {
					continue;
				}

				if (class_exists("{$k}__{$o}")) {
					if ($o === 'hiawatha') {
						exec_with_all_closed("service hiawatha stop; chkconfig hiawatha off >/dev/null 2>&1");
						log_cleanup("- Deactivated {$k}__{$o}", $nolog);
					} else {
						log_cleanup("- Uninstall {$k}__{$o}", $nolog);
						exec_class_method("{$k}__{$o}", "uninstallMe");
					}
				}
			}
		}
	}
}

function removeWebOtherDrivers($nolog = null)
{
	removeOtherDrivers($class = 'web', $nolog = 'true');
}

function removeWebCacheOtherDrivers($nolog = null)
{
	removeOtherDrivers($class = 'webcache', $nolog = 'true');
}

function removeDnsOtherDrivers($nolog = null)
{
	removeOtherDrivers($class = 'dns', $nolog = 'true');
}

function setInitialAdminAccount($nolog = null)
{
	log_cleanup("Initialize OS admin account description", $nolog);
	log_cleanup("- Initialize process", $nolog);

	$desc = uuser::getUserDescription('admin');
	$list = posix_getpwnam('admin');

	if ($list && ($list['gecos'] !== $desc)) {
		lxshell_return("usermod", "-c", $desc, "admin");
	}
}

function updateApplicableToSlaveToo()
{
	os_updateApplicableToSlaveToo();
}

function fix_secure_log($nolog = null)
{
	if (!file_exists("var/log/secure")) {
		return;
	}

	log_cleanup("Fix secure log", $nolog);
	log_cleanup("- Fix process", $nolog);

	if (file_exists("var/log/secure")) {
		lxfile_mv("/var/log/secure", "/var/log/secure.lxback");
	}

	lxfile_cp("../file/linux/syslog.conf", "/etc/syslog.conf");
	lxfile_cp("../file/linux/rsyslog.conf", "/etc/rsyslog.conf");

	if (isServiceExists('syslog')) {
		createRestartFile('syslog');
	} else {
		createRestartFile('rsyslog');
	}
	exec("sed -i 's:-/var/log/:/var/log/:g' /etc/*syslog.conf");
}

function fix_cname($nolog = null)
{
	log_cleanup("Initialize OS admin account description", $nolog);
	log_cleanup("- Initialize process", $nolog);

	lxshell_return("sh", "/script/fixdns");
}

function installChooser($nolog = null)
{
	log_cleanup("Install Webmail chooser", $nolog);
	log_cleanup("- Install process", $nolog);

	$path = "/home/kloxo/httpd/webmail/";
	lxfile_mkdir("/home/kloxo/httpd/webmail/img");

	// MR -- make webmail redirect to 'universal'
	exec("'rm' -f {$path}/redirect-to-*.php");
	$dirs = glob("{$path}/*");

	foreach ($dirs as $dir) {
		$name = str_replace("{$path}/", "", $dir);
		if ($name != "img" && $name != "images" && $name != "disabled" && is_dir($dir)) {
			lfile_put_contents("{$path}/redirect-to-{$name}.php", "<?php\nheader(\"Location: /{$name}\");\n");

		}
	}

	lxfile_unix_chown_rec($path, "apache:apache");
}

function fix_suexec($nolog = null)
{
	log_cleanup("Fix suexec", $nolog);
	log_cleanup("- Fix process", $nolog);

	// MR -- because no need lxsuexec (also lxrestart) so remove if exist
	exec("'rm' -rf /usr/bin/lxsuexec");
}

function enable_xinetd($nolog = null)
{
	log_cleanup("Enable xinetd", $nolog);
	log_cleanup("- enable process", $nolog);

	createRestartFile("qmail");
	exec("service pure-ftpd stop");
	createRestartFile("xinetd");
}

function fix_mailaccount_only($nolog = null)
{
	global $login;

	log_cleanup("Fix mailaccount only", $nolog);
	log_cleanup("- Fix process", $nolog);

	lxfile_unix_chown_rec("/var/bogofilter", "lxpopuser:lxpopgroup");
	$login->loadAllObjects('mailaccount');
	$list = $login->getList('mailaccount');

	foreach ($list as $l) {
		$l->setUpdateSubaction('full_update');
		$l->was();
	}
}

function change_spam_to_bogofilter_next_next()
{
	global $login;

	exec("rpm -e --nodeps spamassassin-toaster");
	exec("yum -y install bogofilter");

	$drv = $login->getFromList('pserver', 'localhost')->getObject('driver');
	$drv->driver_b->pg_spam = 'bogofilter';
	$drv->setUpdateSubaction();
	$drv->write();

	$login->loadAllObjects('mailaccount');
	$list = $login->getList('mailaccount');

	foreach ($list as $l) {
		$s = $l->getObject('spam');
		$s->setUpdateSubaction('update');
		$s->was();
		$l->setUpdateSubaction('full_update');
		$l->was();
	}
}

function fix_mysql_name_problem()
{
	$sq = new Sqlite(null, 'mysqldb');
	$res = $sq->getTable();

	foreach ($res as $r) {
		if (!csa($r['nname'], "___")) {
			return;
		}
		$sq->rawQuery("update mysqldb set nname = '{$r['dbname']}' where dbname = '{$r['dbname']}'");
	}
}

function fix_mysql_username_problem()
{
	$sq = new Sqlite(null, 'mysqldbuser');
	$res = $sq->getTable();

	foreach ($res as $r) {
		if (!csa($r['nname'], "___")) {
			return;
		}

		$sq->rawQuery("update mysqldbuser set nname = '{$r['username']}' where username = '{$r['username']}'");
	}
}

function add_domain_backup_dir($nolog = null)
{
	log_cleanup("Create domain backup dirs", $nolog);
	log_cleanup("- Create process", $nolog);

	// must set this mkdir if want without php warning when cleanup
	lxfile_mkdir("__path_program_home/domain");

	lxfile_generic_chown("__path_program_home/domain", "lxlabs");

	if (lxfile_exists("__path_program_home/domain")) {
		dprint("Domain backupdir exists... returning\n");

		return;
	}

	$sq = new Sqlite(null, 'domain');

	$res = $sq->getTable(array('nname'));

	foreach ($res as $r) {
		lxfile_mkdir("__path_program_home/domain/{$r['nname']}/__backup");
		lxfile_generic_chown("__path_program_home/domain/{$r['nname']}/", "lxlabs");
		lxfile_generic_chown("__path_program_home/domain/{$r['nname']}/__backup", "lxlabs");
	}
}

function changeColumn($tbl_name, $changelist)
{
	dprint("Change Column.............\n");
	$db = new Sqlite($tbl_name);
	$columnold = $db->getColumnTypes();
//	$oldcolumns = array_keys($columnold);
	$conlist = array_flip($changelist);
	$query = "select * from" . " " . $tbl_name;
	$res = $db->rawQuery($query);

	foreach ($columnold as $l) {
		$check = array_search($l, $conlist);

		if ($check) {
			$newcollist[] = $changelist[$l];
		} else {
			$newcollist[] = $l;
		}
	}

	$newfields = implode(",", $newcollist);
	changeValues($res, $tbl_name, $db, $newfields);
}

function changeValues($res, $tbl_name, $db, $newfields)
{
	dprint("$newfields");
	dprint("\n\n");
	$query = "create table lxt_" . $tbl_name . "(" . $newfields . ")";
	$db->rawQuery($query);

	foreach ($res as $r) {
		$newtemp = "";
		foreach ($r as $r1) {
			$newtemp[] = "'" . $r1 . "'";
		}
		$t = implode(",", $newtemp);
		$db->rawQuery("insert into lxt_" . $tbl_name . " values" . "(" . $t . ")");
	}
	$db->rawQuery("drop table " . $tbl_name);
	$db->rawQuery("create table " . $tbl_name . " as select * from lxt_" . "$tbl_name");
	$db->rawQuery("drop table lxt_" . $tbl_name);
	dprint("Table Information of $tbl_name  Updated with New Fields\n\n");
}

function droptable($tbl_name)
{
	dprint("Drop table...............\n");
	$db = new Sqlite($tbl_name);
	$db->rawQuery("drop table " . $tbl_name);
}

function dropcolumn($tbl_name, $column)
{
	dprint("Drop Column...............\n");

	$db = new Sqlite($tbl_name);
	$columnold = $db->getColumnTypes();
	$oldcolumns = array_keys($columnold);

	foreach ($oldcolumns as $key => $l) {
		$t = array_search(trim($l), $column);

		if (!empty($t)) {
			dprint("value $oldcolumns[$key] has deleted\n");
			unset($oldcolumns[$key]);
		} else {
			$newcollist[] = $l;
		}
	}

	$newfields = implode(",", $newcollist);
	dprint("New fields are \n");
	$query = "select " . $newfields . " from" . " " . $tbl_name;
	$res = $db->rawQuery($query);
	changeValues($res, $tbl_name, $db, $newfields);
}

function getTabledetails($tbl_name)
{
	dprint("table. values are ..........\n");
	$db = new Sqlite($tbl_name);
	$res = $db->rawQuery("select * from " . $tbl_name);
	print_r($res);
}

function construct_uuser_nname($list)
{
	global $sgbl;

	return $list['nname'] . $sgbl->__var_nname_impstr . $list['servername'];
}

function getVersionNumber($ver)
{
	$ver = trim($ver);
	$ver = str_replace("\n", "", $ver);
	$ver = str_replace("\r", "", $ver);

	return $ver;
}

// ref: http://ideone.com/JWKIf
function is_64bit()
{
	$int = "9223372036854775807";
	$int = intval($int);

	if ($int == 9223372036854775807) {
		return true; /* 64bit */
	} elseif ($int == 2147483647) {
		return false; /* 32bit */
	} else {
		return "error"; /* error */
	}
}

function checkIdenticalFile($file1, $file2)
{
	$ret = false;

	if (!file_exists($file1)) {
		return false;
	}

	if (!file_exists($file2)) {
		return false;
	}

	if (filesize($file1) === filesize($file2)) {
		$ret = true;
	} else {
		return false;
	}

	if (md5_file($file1) === md5_file($file2)) {
		$ret = true;
	} else {
		return false;
	}

	return $ret;
}

// Issue #798 - Check for Core packages (rpm) when running upcp
// MR - execute inside tmpupdatecleanup.php for upcp
function setUpdateServices($list, $nolog = null)
{
	if (!is_array($list)) {
		$l = array($list);
	} else {
		$l = $list;
	}

	log_cleanup('Update Core packages', $nolog);

	foreach ($l as $k => $v) {
		exec("yum list installed {$v}", $out, $ret);

		if ($ret === 0) {
			exec("yum update {$v} -y >/dev/null 2>&1");
			log_cleanup("- New {$v} version installed", $nolog);
		} else {
			log_cleanup("- No '{$v}' update found/not installed", $nolog);
		}
	}
}

// Issue #769 - Fix services when updating Kloxo
// MR -- TODO: automatic update found different version of config

function setUpdateConfigWithVersionCheck($list, $servertype = null, $nolog = null)
{
	$fixstr = "";

	foreach ($list as $k => $v) {
		log_cleanup("- Fix {$v} services", $nolog);

		$fixstr = "sh /script/fix{$v} --server=all";

		if ($servertype !== 'slave') {
			exec($fixstr);
		}
	}
}

function updatecleanup($nolog = null)
{
	setPrepareKloxo($nolog);

	// MR -- disable (from 'old' Kloxo style)
//	install_bogofilter($nolog);

	setRemoveOldDirs($nolog);

	setInitialBinary($nolog);

	log_cleanup("Remove lighttpd errorlog", $nolog);
	log_cleanup("- Remove process", $nolog);
	remove_lighttpd_error_log();

	log_cleanup("Fix the secure logfile", $nolog);
	log_cleanup("- Fix process", $nolog);
	call_with_flag("fix_secure_log");

	log_cleanup("Clean hosts.deny", $nolog);
	log_cleanup("- Clean process", $nolog);
	call_with_flag("remove_host_deny");

	if (isServiceExists('gpm')) {
		log_cleanup("Turn off mouse daemon", $nolog);
		log_cleanup("- Turn off process", $nolog);
		exec("chkconfig gpm off >/dev/null 2>&1");
	}

	if (lxfile_exists("phpinfo.php")) {
		log_cleanup("Remove phpinfo.php", $nolog);
		log_cleanup("- Remove process", $nolog);
		lxfile_rm("phpinfo.php");
	}
/*
	log_cleanup("Kill gettraffic system process", $nolog);
	log_cleanup("- Kill process", $nolog);
	exec("pkill -f gettraffic");
*/
	setSyncDrivers($nolog);

	setRealServiceBranchList($nolog);

	setCheckPackages($nolog);

	copy_script($nolog);

	setJailshellSystem($nolog);

	log_cleanup("Set /home permission to 0755", $nolog);
	log_cleanup("- Set process", $nolog);
	lxfile_unix_chmod("/home", "0755");

	setKloxoHttpdChownChmod($nolog);
/*
	log_cleanup("Enable xinetd service", $nolog);
	log_cleanup("- Enable process", $nolog);
	call_with_flag("enable_xinetd");
*/
	fix_suexec($nolog);

	if (!lxfile_exists("/usr/bin/php-cgi")) {
		log_cleanup("Initialize php-cgi binary", $nolog);
		log_cleanup("- Initialize process", $nolog);
		lxfile_cp("/usr/bin/php", "/usr/bin/php-cgi");
	}

	setSomePermissions($nolog);

	setSomeScript($nolog);

	log_cleanup("Remove cache dir", $nolog);
	log_cleanup("- Remove process", $nolog);
	lxfile_rm_rec("__path_program_root/cache");

	log_cleanup("Initialize awstats dirdata", $nolog);
	log_cleanup("- Initialize process", $nolog);
	lxfile_mkdir("/home/kloxo/httpd/awstats/dirdata");

	log_cleanup("Update Kloxo database", $nolog);
	log_cleanup("- Update process", $nolog);
	update_database();

	log_cleanup("Remove old lxlabs ssh key", $nolog);
	log_cleanup("- Remove process", $nolog);
	remove_ssh_self_host_key();

//	installEasyinstaller($nolog);
}

function setConfigsServices($nolog = null)
{
	setInitialAllDnsConfigs($nolog);
	setInitialAllWebConfigs($nolog);
	setInitialAllWebCacheConfigs($nolog);

	setInitialPhpIniConfig($nolog);
	getInitialPhpFpmConfig($nolog);
}

function setInitialServices($nolog = null)
{
	// MR -- no needed because using disable temporal alias (\cp, \mv and \rm)
	// -- not include rm because conflict with \r
//	setRemoveAlias($nolog);

	// MR -- disabled until auto-edit /etc/fstab
//	setEnableQuota($nolog);

	setInitialServer($nolog);

	setRemoveHttpdocsSymlink($nolog);

	setHostsFile($nolog);

	setDefaultPages($nolog);

	setInitialPhpMyAdmin($nolog);

	setInitialAdminAccount($nolog);

	setInitialAllDnsConfigs($nolog);
	setInitialAllWebConfigs($nolog);
	setInitialAllWebCacheConfigs($nolog);

	setAllDnsServerInstall($nolog);
	setAllInactivateDnsServer($nolog);
	setActivateDnsServer($nolog);

	setAllWebServerInstall($nolog);
	setAllInactivateWebServer($nolog);
	setActivateWebServer($nolog);

	setPhpUpdate();

	setInitialPhpIniConfig($nolog);
	getInitialPhpFpmConfig($nolog);

	setInitialPureftpConfig($nolog);

	setInitialLogrotate($nolog);

	setCopyIndexFileToAwstatsDir($nolog);

	installChooser($nolog);

	setInstallMailserver($nolog);

	setAllSSLPortions($nolog);

	setHttpry($nolog);

	setCronBackup($nolog);

	setWatchdogDefaults($nolog);
}

function setRemoveAlias($nolog = null)
{
	log_cleanup("Remove cp/mv/rm alias", $nolog);

	// MR -- importance for Centos 6
	log_cleanup("- Unalias process", $nolog);
	exec("unalias cp > /dev/null 2>&1; unalias mv > /dev/null 2>&1; unalias rm > /dev/null 2>&1");
}

function setPrepareKloxo($nolog = null)
{
	log_cleanup("Prepare for Kloxo", $nolog);

	log_cleanup("- OS Create Kloxo init.d service file", $nolog);
	os_create_program_service();

	log_cleanup("- OS Fix programroot path permissions", $nolog);
	os_fix_lxlabs_permission();

	log_cleanup("- OS Restart Kloxo service", $nolog);
	os_restart_program();
}

function update_all_slave()
{
	$db = new Sqlite(null, "pserver");

	$list = $db->getTable(array("nname"));

	foreach ($list as $l) {
		if ($l['nname'] === 'localhost') {
			continue;
		}
		try {
			print("Upgrade Slave {$l['nname']}...\n");
			rl_exec_get(null, $l['nname'], 'remotetestfunc', null);
		} catch (Exception $e) {
			print($e->getMessage());
			print("\n");
		}
	}
}

function findNextVersion($lastversion = null)
{
	global $sgbl;

	$thisversion = $sgbl->__ver_major_minor_release;

	$upgrade = null;
	$nlist = getVersionList($lastversion);
	dprintr($nlist);
	$k = 0;
	print("Found version(s):");

	foreach ($nlist as $l) {
		print(" $l");

		if (version_cmp($thisversion, $l) === -1) {
			$upgrade = $l;
			break;
		}

		$k++;
	}

	print("\n");

	if (!$upgrade) {
		return 0;
	}

	print("Upgrade from $thisversion to $upgrade\n");

	return $upgrade;
}

function getParseTpl($template, $var_array)
{
	$search = preg_match_all('/{.*?}/', $template, $matches);

	for ($i = 0; $i < $search; $i++) {
		$matches[0][$i] = str_replace(array('%', '%'), null, $matches[0][$i]);
	}

	foreach ($matches[0] as $value) {
		$template = str_replace('%' . $value . '%', $var_array[$value], $template);
	}

	return $template;
}

function getLinkCustomfile($path, $file)
{
	if (file_exists("{$path}/custom.{$file}")) {
		$custom = 'custom.';
	} else {
		$custom = '';
	}

	return "{$path}/{$custom}{$file}";
}

function getParseInlinePhp($template, $input)
{
	extract($input, EXTR_OVERWRITE);

	$ret = null;

//	if (!ob_get_status()) {
		ob_start();
//	}

	eval('?>' . $template);

	$ret = ob_get_contents();

	ob_end_clean();

	// MR -- important because process on panel include html code!
	$splitter = explode('### begin', $ret);
	$ret = (count($splitter) === 2) ? '### begin' . $splitter[1] : $ret;
	$splitter = explode(';;; begin', $ret);
	$ret = (count($splitter) === 2) ? ';;; begin' . $splitter[1] : $ret;
	$splitter = explode('/// begin', $ret);
	$ret = (count($splitter) === 2) ? '/// begin' . $splitter[1] : $ret;

	return $ret;
}

function setCopyDnsConfFiles($dnsdriver, $nolog = null)
{
	if ($dnsdriver === 'none') {
		return;
	}

	$aliasdriver = ($dnsdriver === 'bind') ? 'named' : $dnsdriver;

	$pathsrc = "../file/{$dnsdriver}";
	$pathdrv = "/opt/configs/{$dnsdriver}";
	$pathetc = "/etc";

	log_cleanup("Copy all contents from {$pathsrc}", $nolog);

	log_cleanup("- Copy to {$pathdrv}", $nolog);
	exec("'cp' -rf {$pathsrc} /opt/configs");

	if ($aliasdriver === 'djbdns') {
		if (file_exists("/home/djbdns/tinydns")) {
		//	lxfile_mv("/home/djbdns", "/opt/configs/djbdns");
		}
/*
	} elseif ($aliasdriver === 'maradns') {
		$s = "mararc";
		$t = getLinkCustomfile($pathdrv . "/etc", $s);

		log_cleanup("- Copy etc/{$s} to {$pathetc}/mararc", $nolog);
		lxfile_cp($t, "{$pathetc}/mararc");
*/
	} elseif ($aliasdriver === 'named') {
		$pathtarget = "{$pathetc}";

		$t = getLinkCustomfile($pathdrv . "/etc", "{$aliasdriver}.conf");

		log_cleanup("- Copy etc/{$aliasdriver}.conf to {$pathtarget}/{$aliasdriver}.conf", $nolog);
		lxfile_cp($t, "{$pathtarget}/{$aliasdriver}.conf");
	} elseif ($aliasdriver === 'yadifa') {
		$pathtarget = "{$pathetc}";

		$t = getLinkCustomfile($pathdrv . "/etc", "{$aliasdriver}d.conf");

		log_cleanup("- Copy etc/{$aliasdriver}d.conf to {$pathtarget}/{$aliasdriver}d.conf", $nolog);
		lxfile_cp($t, "{$pathtarget}/{$aliasdriver}d.conf");
	} elseif ($aliasdriver === 'nsd') {
		$pathtarget = "{$pathetc}/{$aliasdriver}";

		if (!file_exists($pathtarget)) {
			exec("mkdir -p {$pathtarget}");
		}

		if (file_exists("/usr/sbin/nsd-control")) {
			$s = "{$aliasdriver}4.conf";
			$t = getLinkCustomfile($pathdrv . "/etc/conf", $s);
		} else {
			$s = "{$aliasdriver}3.conf";
			$t = getLinkCustomfile($pathdrv . "/etc/conf", $s);
		}

		log_cleanup("- Copy etc/conf/{$s} to {$pathtarget}/{$aliasdriver}.conf", $nolog);
		lxfile_cp($t, "{$pathtarget}/{$aliasdriver}.conf");

	} else {
		$pathtarget = "{$pathetc}/{$aliasdriver}";

		if (!file_exists($pathtarget)) {
			exec("mkdir -p {$pathtarget}");
		}

		$s = "{$aliasdriver}.conf";
		$t = getLinkCustomfile($pathdrv . "/etc/conf", $s);

		log_cleanup("- Copy etc/conf/{$s} to {$pathtarget}/{$aliasdriver}.conf", $nolog);
		lxfile_cp($t, "{$pathtarget}/{$aliasdriver}.conf");
	}
}

function setCopyWebCacheConfFiles($webcachedriver, $nolog = null)
{
	if ($webcachedriver === 'none') {
		return;
	}

	$pathsrc = "../file/{$webcachedriver}";
	$pathdrv = "/opt/configs/{$webcachedriver}";
	$pathetc = "/etc";

	log_cleanup("Copy all contents from {$pathsrc}", $nolog);

	log_cleanup("- Copy to {$pathdrv}", $nolog);
	exec("'cp' -rf {$pathsrc} /opt/configs");

	$pathconf = "{$pathetc}/{$webcachedriver}";

	if (!file_exists($pathconf)) {
		exec("mkdir -p {$pathconf}");
	}


	if ($webcachedriver === 'varnish') {
		$t = getLinkCustomfile($pathdrv . "/etc/conf", "default.vcl");
		lxfile_cp($t, "$pathetc/{$webcachedriver}/default.vcl");

		$t = getLinkCustomfile($pathdrv . "/etc/sysconfig", "varnish");
		lxfile_cp($t, "$pathetc/sysconfig/varnish");
	} elseif ($webcachedriver === 'trafficserver') {
		$a = array("records.config", "remap.config", "storage.config", "ip_allow.config");

		foreach ($a as $k => $v) {
			$t = getLinkCustomfile($pathdrv . "/etc/conf", $v);
			lxfile_cp($t, "$pathetc/{$webcachedriver}/{$v}");
		}
	} elseif ($webcachedriver === 'squid') {
		// TODO
	}
}


function setCopyWebConfFiles($webdriver, $nolog = null)
{
	if ($webdriver === 'none') {
		return;
	}

	$aliasdriver = ($webdriver === 'apache') ? 'httpd' : $webdriver;

	$pathsrc = "../file/{$webdriver}";
	$pathdrv = "/opt/configs/{$webdriver}";
	$pathetc = "/etc/{$aliasdriver}";
	$pathconfd = "{$pathetc}/conf.d";
	$pathconf = ($webdriver === 'apache') ? "{$pathetc}/conf" : "{$pathetc}";

	log_cleanup("Copy all contents from {$pathsrc}", $nolog);

	log_cleanup("- Copy to {$pathdrv}", $nolog);
	exec("'cp' -rf {$pathsrc} /opt/configs");

	if ($webdriver !== 'hiawatha') {
		$dirs = array($pathconf, $pathconfd);

		foreach ($dirs as &$d) {
			if (!file_exists($d)) {
				exec("mkdir -p {$d}");
			}
		}

		if ($webdriver === 'lighttpd') {
			// MR -- lighttpd problem if /var/log/lighttpd not apache:apache chown
			lxfile_unix_chown("/var/log/{$webdriver}", "apache:apache");
		}

		$addition = '';
	} else {
		if (isWebProxy()) {
			$addition = '_proxy';
		} else {
			$addition = '_standard';
		}
	}

	$s = "{$aliasdriver}{$addition}.conf";
	$t = getLinkCustomfile("{$pathdrv}/etc/conf", $s);

	log_cleanup("- Copy etc/conf/{$s} to {$pathconf}/{$aliasdriver}.conf", $nolog);
	if ($webdriver === 'apache') {
		if (file_exists("../etc/flag/use_apache24.flg")) {
			lxfile_cp(getLinkCustomfile("{$pathdrv}/etc/conf", "{$aliasdriver}{$addition}24.conf"),
				"{$pathconf}/{$aliasdriver}.conf");
		} else {
			lxfile_cp($t, "{$pathconf}/{$aliasdriver}{$addition}.conf");
		}
	} else {
		lxfile_cp($t, "{$pathconf}/{$aliasdriver}{$addition}.conf");
	}
	// MR - remove unwanted files
	if ($webdriver === 'apache') {
		lxfile_rm("{$pathdrv}/etc/conf.d/_mpm.nonconf");
		lxfile_rm("{$pathdrv}/etc/conf.d/perl.conf");
		lxfile_rm("{$pathdrv}/etc/conf.d/perl.conf.original");
	}
	
	// MR -- make sure init.conf is right (especially for apache)
	exec("sh /script/fixweb --target=defaults");
}

function setCopyOpenSSLConfFiles($nolog = null)
{
	$pathsrc = "../file/openssl";
	$pathdrv = "/opt/configs/openssl";

	log_cleanup("Copy all contents from {$pathsrc}", $nolog);

	log_cleanup("- Copy to {$pathdrv}", $nolog);
	exec("'cp' -rf {$pathsrc} /opt/configs");

	if (file_exists("/home/openssl")) {
		lxfile_rm_rec("/home/openssl");
	}
}

function setCopyLetsEncryptConfFiles($nolog = null)
{
	$pathsrc = "../file/letsencrypt";
	$pathdrv = "/opt/configs/letsencrypt";

	log_cleanup("Copy all contents from {$pathsrc}", $nolog);

	log_cleanup("- Copy to {$pathdrv}", $nolog);
	exec("'cp' -rf {$pathsrc} /opt/configs");

	if (file_exists("/home/letsencrypt")) {
		lxfile_rm_rec("/home/letsencrypt");
	}
}

function setCopyAcmeshConfFiles($nolog = null)
{
	$pathsrc = "../file/acme.sh";
	$pathdrv = "/opt/configs/acme.sh";

	log_cleanup("Copy all contents from {$pathsrc}", $nolog);

	log_cleanup("- Copy to {$pathdrv}", $nolog);
	exec("'cp' -rf {$pathsrc} /opt/configs");

	if (file_exists("/home/acme.sh")) {
		lxfile_rm_rec("/home/acme.sh");
	}
}

function setCopyStartapishConfFiles($nolog = null)
{
	$pathsrc = "../file/startapi.sh";
	$pathdrv = "/opt/configs/startapi.sh";

	log_cleanup("Copy all contents from {$pathsrc}", $nolog);

	log_cleanup("- Copy to {$pathdrv}", $nolog);
	exec("'cp' -rf {$pathsrc} /opt/configs");

	if (file_exists("/home/startapi.sh")) {
		lxfile_rm_rec("/home/startapi.sh");
	}
}

function setCopyHttpryConfFiles($nolog = null)
{
	$pathsrc = "../file/httpry";
	$pathdrv = "/opt/configs/httpry";

	log_cleanup("Copy all contents from {$pathsrc}", $nolog);

	log_cleanup("- Copy to {$pathdrv}", $nolog);
	exec("'cp' -rf {$pathsrc} /opt/configs");

	if (file_exists("/home/httpry")) {
		lxfile_rm_rec("/home/httpry");
	}
}

function isWebProxy($drivertype = null)
{
	$driverapp = ($drivertype) ? $drivertype : slave_get_driver('web');

	$ret = (stripos($driverapp, 'proxy') !== false) ? true : false;

	return $ret;
}

function isWebProxyOrApache($drivertype = null)
{
	$driverapp = ($drivertype) ? $drivertype : slave_get_driver('web');

	if ($driverapp === 'apache') {
		$ret = true;
	} else {
		$ret = isWebProxy($driverapp);
	}

	return $ret;
}

function isWebCache($drivertype = null)
{
	$driverapp = ($drivertype) ? $drivertype : slave_get_driver('webcache');

	$ret = ($driverapp !== 'none') ? true : false;

	return $ret;
}


function getWebDriverList($drivertype = null)
{
	$driverapp = ($drivertype) ? $drivertype : slave_get_driver('web');

	if (isWebProxy($driverapp)) {
		$front = str_replace('proxy', '', $driverapp);

		$list = array($front, 'apache');
	} else {
		$list = array($driverapp);
	}

	return $list;
}

function getAllWebDriverList()
{
	include "../file/driver/rhel.inc";

	return $driver['web'];
}

function getAllRealWebDriverList()
{
	$list = getAllWebDriverList();

	$ret = array();

	foreach ($list as $k => $v) {
		if ($v === 'none') { continue; }
		if (strpos($v, 'proxy') !== false) { continue; }

		$ret[] = $v;
	}

	return $ret;
}

function getWebCacheDriverList($drivertype = null)
{
	$driverapp = ($drivertype) ? $drivertype : slave_get_driver('webcache');

	return array($driverapp);
}

function getAllWebCacheDriverList()
{
	include "../file/driver/rhel.inc";

	return $driver['webcache'];
}

function getAllRealWebCacheDriverList()
{
	$list = getAllWebCacheDriverList();

	$ret = array();

	foreach ($list as $k => $v) {
		if ($v === 'none') { continue; }

		$ret[] = $v;
	}

	return $ret;
}

function getDnsDriverList($drivertype = null)
{
	$driverapp = ($drivertype) ? $drivertype : slave_get_driver('dns');

	return array($driverapp);
}

function getAllDnsDriverList()
{
	include "../file/driver/rhel.inc";

	return $driver['dns'];
}

function getAllRealDnsDriverList()
{
	$list = getAllDnsDriverList();

	$ret = array();

	foreach ($list as $k => $v) {
		if ($v === 'none') { continue; }

		$ret[] = $v;
	}

	return $ret;
}

function setRealServiceBranchList($nolog = null)
{
	log_cleanup("Update Services Branch List", $nolog);
	log_cleanup("- Wait to process...", $nolog);

	exec("sh /script/fix-service-list");
}

function getRpmVersionViaYum($rpm)
{
	exec("yum info {$rpm} | grep 'Version' | awk '{print $3}'", $out, $ret);

	if ($ret === 0) {
		return $out[0];
	} else {
		return '';
	}
}

function getRpmReleaseViaYum($rpm)
{
	exec("yum info {$rpm} | grep 'Release' | awk '{print $3}'", $out, $ret);

	if ($ret === 0) {
		return $out[0];
	} else {
		return '';
	}
}

function setPhpBranch($select, $nolog = null)
{
	log_cleanup("- Php Branch replace", $nolog);

	$phpbranch = getRpmBranchInstalled('php');

	if ($select === $phpbranch) {
		log_cleanup("-- It's the same branch ({$select}); no changed", $nolog);

		return null;
	} elseif ($select === '') {
		log_cleanup("-- It's no select entry", $nolog);

		return null;
	} else {
		// MR -- reinstall php modules to make sure replace too; must execute before replace
		exec("cd /; yum list installed php* |grep -P 'php[a-zA-z0-9\-]+'", $phpmodules);

		log_cleanup("-- Replace using 'yum replace {$phpbranch} --replace-with={$select}'", $nolog);
		setRpmReplaced("{$phpbranch}-cli", "{$select}-cli");

		foreach ($phpmodules as $k => $v) {
			if ($phpmodules[0] === $phpbranch) {
				continue;
			}

			$t = str_replace("{$phpbranch}-", "{$select}-", $v);

			if (!isRpmInstalled($t)) {
				log_cleanup("-- Install missing '{$t}' module if exists", $nolog);
				exec("yum install {$t} >/dev/null 2>&1");
			} else {
				log_cleanup("-- '{$t}' module already installed", $nolog);
			}
		}
	}

	exec("sh /script/fixphp");

	if (isServiceExists('php-fpm')) {
		createRestartFile('php-fpm');
	}
}

function getKloxoType()
{
	if (file_exists("/var/lib/mysql/kloxo")) {
		return 'master';
	} elseif (file_exists("../etc/conf/slave-db.db")) {
		return 'slave';
	} else {
		return '';
	}
}

function setHostsFile($nolog = null)
{
	log_cleanup("Add 'hostname' information to '/etc/hosts'", $nolog);
/*
	$begincomment[] = "### begin - add by Kloxo-MR";
	$endcomment[] = "### end - add by Kloxo-MR";

	exec("hostname -s", $hnshort);
	exec("hostname", $hnfull);

	#exec("ifconfig |grep -i 'inet addr:'|grep -v '127.0.0.1'|awk '{print $2}'|sed 's/addr\://'", $hnip);
	exec("hostname -i | awk '{print $1}'", $hnip);

	$content = "{$hnip[0]} {$hnfull[0]} {$hnshort[0]}\n";

	$hnfile = '/etc/hosts';

	log_cleanup("- Add ip, short and full name of 'hostname'", $nolog);

	file_put_between_comments("root:root", $begincomment, $endcomment,
		$begincomment[0], $endcomment[0], $hnfile, $content, $nowarning = true);
*/
	exec("sh /script/set-hosts");
}

// MR -- taken http://stackoverflow.com/questions/6875913/simple-how-to-replace-all-between-with-php
function replace_between($str, $needle_start, $needle_end, $replacement)
{
	$pos = strpos($str, $needle_start);
	$start = $pos === false ? 0 : $pos + strlen($needle_start);

	$pos = strpos($str, $needle_end, $start);
	$end = $pos === false ? strlen($str) : $pos;

	return substr_replace($str, $replacement, $start, $end - $start);
}

// MR -- taken from http://www.webmasterworld.com/forum88/9769.htm
function get_brightness($hex)
{
	// returns brightness value from 0 to 255

	// strip off any leading #
	$hex = str_replace('#', '', $hex);

	$c_r = hexdec(substr($hex, 0, 2));
	$c_g = hexdec(substr($hex, 2, 2));
	$c_b = hexdec(substr($hex, 4, 2));

	return (($c_r * 299) + ($c_g * 587) + ($c_b * 114)) / 1000;
}

// MR -- taken from http://stackoverflow.com/questions/3512311/how-to-generate-lighter-darker-color-with-php
function adjust_brightness($hex, $steps)
{
	// Steps should be between -255 and 255. Negative = darker, positive = lighter
	$steps = max(-255, min(255, $steps));

	// Format the hex color string
	$hex = str_replace('#', '', $hex);

	if (strlen($hex) == 3) {
		$hex = str_repeat(substr($hex, 0, 1), 2) . str_repeat(substr($hex, 1, 1), 2) . str_repeat(substr($hex, 2, 1), 2);
	}

	// Get decimal values
	$r = hexdec(substr($hex, 0, 2));
	$g = hexdec(substr($hex, 2, 2));
	$b = hexdec(substr($hex, 4, 2));

	// Adjust number of steps and keep it inside 0 to 255
	$r = max(0, min(255, $r + $steps));
	$g = max(0, min(255, $g + $steps));
	$b = max(0, min(255, $b + $steps));

	$r_hex = str_pad(dechex($r), 2, '0', STR_PAD_LEFT);
	$g_hex = str_pad(dechex($g), 2, '0', STR_PAD_LEFT);
	$b_hex = str_pad(dechex($b), 2, '0', STR_PAD_LEFT);

	return '#' . $r_hex . $g_hex . $b_hex;
}

// MR -- taken from http://lab.clearpixel.com.au/2008/06/darken-or-lighten-colours-dynamically-using-php/
function colourBrightness($hex, $percent)
{
	// Work out if hash given
	$hash = '';

	if (stristr($hex, '#')) {
		$hex = str_replace('#', '', $hex);
		$hash = '#';
	}

	// Check if shorthand hex value given (eg. #FFF instead of #FFFFFF)
	if (strlen($hex) == 3) {
		$hex = str_repeat(substr($hex, 0, 1), 2) . str_repeat(substr($hex, 1, 1), 2) . str_repeat(substr($hex, 2, 1), 2);
	}

	/// HEX TO RGB
	$rgb = array(hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2)));
	//// CALCULATE

	for ($i = 0; $i < 3; $i++) {
		// See if brighter or darker
		if ($percent > 0) {
			// Lighter
			$rgb[$i] = round($rgb[$i] * $percent) + round(255 * (1 - $percent));
		} else {
			// Darker
			$positivePercent = $percent - ($percent * 2);
			$rgb[$i] = round($rgb[$i] * $positivePercent) + round(0 * (1 - $positivePercent));
		}

		// In case rounding up causes us to go to 256
		if ($rgb[$i] > 255) {
			$rgb[$i] = 255;
		}
	}

	//// RBG to Hex
	$hex = '';

	for ($i = 0; $i < 3; $i++) {
		// Convert the decimal digit to hex
		$hexDigit = dechex($rgb[$i]);
		// Add a leading zero if necessary

		if (strlen($hexDigit) == 1) {
			$hexDigit = "0" . $hexDigit;
		}

		// Append to the hex string
		$hex .= $hexDigit;
	}

	return $hash . $hex;
}

// MR -- this function to replace eval($sgbl->arg_getting_string) in php 5.3
function get_function_arglist($start = 0, $transforming_func)
{
	$arglist = array();

	for ($i = $start; $i < func_num_args(); $i++) {
		if (isset($transforming_func)) {
			$arglist[] = $transforming_func(func_get_arg($i));
		} else {
			$arglist[] = func_get_arg($i);
		}
	}

	return $arglist;
}

function random_string_lcase($length)
{
	$key = '';

	$keys1 = array_merge(range('a', 'z'));
	$keys2 = array_merge(range(0, 9), range('a', 'z'));

	for ($i = 0; $i < $length; $i++) {
		if ($i === 0) {
			$key .= $keys1[array_rand($keys1)];
		} else {
			$key .= $keys2[array_rand($keys2)];
		}
	}

	return $key;
}

function exec_out($input)
{
	exec($input, $out, $ret);

	if ($ret === 0) {
		print(implode("\n", $out) . "\n");
	}

	$out = null;
}

// taken from http://www.binarytides.com/php-check-running-cli/
function is_cli()
{
	if (defined('STDIN')) {
		return true;
	}

	if (empty($_SERVER['REMOTE_ADDR']) and !isset($_SERVER['HTTP_USER_AGENT']) and count($_SERVER['argv']) > 0) {
		return true;
	}

	return false;
}

// taken from http://snipplr.com/view/11410/prevent-remote-form-submit/
function isRemotePost()
{
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		// or possibly, count($_POST) > 0
		$host = preg_replace('#^www\.#', '', $_SERVER['HTTP_HOST']);

		if ($host AND $_SERVER['HTTP_REFERER']) {
			$refparts = @parse_url($_SERVER['HTTP_REFERER']);
			$refhost = $refparts['host'] . ((int)$refparts['port'] ? ':' . (int)$refparts['port'] : '');

			if (strpos($refhost, $host) === false) {
				//	die('POST requests are not permitted from "foreign" domains.');
				return true;
			}
		}
	}

	return false;
}

function getCSRFToken()
{
	global $gbl;

	if (isset($gbl->c_session->ssession_vars['__tmp_csrf_token'])) {
		$token = $gbl->c_session->ssession_vars['__tmp_csrf_token'];
	} else {
		$token = randomString(64);
		//	$gbl->c_session->ssession_vars['__tmp_csrf_token'] = $token;
		$gbl->setSessionV('__tmp_csrf_token', $token);
		$gbl->c_session->write();
	}

	return $token;
}

function isCSRFTokenMatch()
{
	global $gbl;

	$ret = true;

	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		// or possibly, count($_POST) > 0

		$token_post = $_POST['frm_token'];
		$token_session = $gbl->c_session->ssession_vars['__tmp_csrf_token'];

		if ($token_post !== $token_session) {
			$ret = false;
		}
	} else {
	/*
		// MR -- disable for implementation because too much exception and include less of less risk
		$action = $_GET['frm_action'];
		$subaction = $_GET['frm_subaction'];

		if (($action === 'add') || ($action === 'update') || ($action === 'delete')) {
			if (($subaction !== 'toggle_state') && ($subaction !== 'toggle_boot_state') &&
				($subaction !== 'start') && ($subaction !== 'stop') && ($subaction !== 'restart') &&
				($subaction !== 'readipaddress')) {

				$ret = false;
			}
		}
	*/
	}

	return $ret;
}

function getTimeZoneList()
{
	global $global_list_path;

	$global_list_path = null;

	do_recurse_dir("/usr/share/zoneinfo/", "listFile", null);

	return $global_list_path;
}

function trimming($data)
{
	if (gettype($data) == 'array') {
		return array_map("trim", $data);
	} else {
		return trim($data);
	}
}

function getFSBlockSizeInKb()
{
	exec("echo ''>/tmp/mustafa.lia.armando;" .
		"du -k /tmp/mustafa.lia.armando|awk '{print $1}';" .
		"'rm' -f /tmp/mustafa.lia.armando", $out);

	return $out[0];
}

// MR -- all exec() will be change with this function because no need 'root' user for php for panel itself
// for security reason, shexec only permit execute with caller from inside /usr/local/lxlabs/kloxo
function shexec($cmd)
{
	$caller = "../sbin/shexec";

	// MR -- $cmd must be full command like: 'rm' -rf /tmp/del.txt
	// no permit with "" (doublequote) because conflict
	exec("{$caller} \"$cmd\"");
}

function shexec_return($cmd)
{
	$caller = "../sbin/shexec";

	exec("{$caller} \"$cmd\"", $out, $ret);

	return $ret;
}

function shexec_output($cmd)
{
	$caller = "../sbin/shexec";

	return shell_exec("{$caller} \"$cmd\"");
}

// MR -- needed especially in install step
// more priority using slavedb as primary instead table
function setSyncDrivers($nolog = null)
{
	global $gbl;

	log_cleanup("Synchronize driver between table and slavedb", $nolog);

//	include "../file/driver/rhel.inc";

	$classlist = array('web' => 'apache', 'webcache' => 'none', 'dns' => 'bind',
		'pop3' => 'courier', 'smtp' => 'qmail', 'spam' => 'bogofilter');

	$nodriver = false;

	if (!file_exists("../etc/slavedb/driver")) {
		$nodriver = true;
		slave_save_db("driver", $classlist);
	}

	foreach ($classlist as $key => $val) {
		if ($nodriver) {
			$driver_from_slavedb = $val;
		} else {
			$driver_from_slavedb = slave_get_driver($key);

			if (!$driver_from_slavedb) {
				$driver_from_slavedb = $val;
			}
		}

		$driver_from_table = $gbl->getSyncClass(null, 'localhost', $key);

		if ($driver_from_table !== $driver_from_slavedb) {
		/*
			if (!$driver_from_table) {
				$realval[$key] = $val;
			} else {
				$realval[$key] = $driver_from_table;
			}
		*/
			$realval[$key] = $driver_from_slavedb;

			log_cleanup("- Synchronize for '{$key}' to '{$realval[$key]}'", $nolog);
		} else {
			$realval[$key] = $driver_from_table;

			log_cleanup("- No need synchronize for '{$key}' - already using '{$realval[$key]}'", $nolog);
		}

		exec("sh /script/setdriver --server=localhost --class={$key} --driver={$realval[$key]}");
	}

	slave_save_db('driver', $realval);
}

function setEnableQuota($nolog = null)
{
	log_cleanup("Enable Disk Quota\n", $nolog);

	if (isRpmInstalled('quota')) {
		log_cleanup("- Already installed\n", $nolog);
	} else {
		log_cleanup("- Install process\n", $nolog);
		setRpmInstalled('quota');
	}

	exec("quotaon -pa|grep 'off'", $out);

	if ($out[0] !== '') {
		log_cleanup("- set to enable\n", $nolog);
		exec("quotaon -vaug, $nolog");
	} else {
		log_cleanup("- Already enabled\n", $nolog);
	}
}

function setRemoveHttpdocsSymlink($nolog)
{
	log_cleanup("Remove /home/httpd/*/httpdocs\n", $nolog);

	log_cleanup("- Remove process\n", $nolog);

	// MR -- script also remove /home/httpd/*/conf
	exec("sh /script/remove-httpdocs-symlink");
}

function ipv6_expand($ip)
{
	$hex = unpack("H*hex", inet_pton($ip));
	$ip = substr(preg_replace("/([A-f0-9]{4})/", "$1:", $hex['hex']), 0, -1);

	return $ip;
}

function getBranchPhpList()
{
	$d = glob("/opt/*s/usr/bin/php");

	if (empty($d)) {
		return array();
	}

	foreach ($d as $k => $v) {
		$e = str_replace('/opt/', '', $v);
		$e = str_replace('/usr/bin/php', '', $e);
		$f[] = $e;
	}

	return $f;
}


function getMultiplePhpList()
{
	$d = glob("/opt/*m/usr/bin/php");

	if (empty($d)) {
		return array();
	}

	foreach ($d as $k => $v) {
		$e = str_replace('/opt/', '', $v);
		$e = str_replace('/usr/bin/php', '', $e);
		$f[] = $e;
	}

	return $f;
}

function getCleanRpmBranchListOnList($branchtype)
{
	$a = getListOnList($branchtype);

	$c = array();

	foreach ($a as $k => $v) {
		
		
		
		if (strpos(str_replace('-php', '', $a[$k]), 'php_(') !== false ) {
			unset($a[$k]);
		} else {
			$b = explode('_(', $v);
			$a[$k] = $b[0];

			if (strrpos($a[$k], 'u') !== false) {
				$c[] = str_replace('u', '', $a[$k]);
			} elseif (strrpos($a[$k], 'w') !== false) {
				$c[] = str_replace('w', '', $a[$k]);
			}elseif (strrpos($a[$k], '-php') !== false) {
			    $c[] = str_replace('-php', '', $a[$k]);
			}
			
		}
	}

	$a = array_diff($a, $c);

	foreach ($a as $k => $v) {
		$a[$k] = str_replace('w', '', str_replace('u', '', str_replace('-php', '', $v)) . "m");
	}

	return array_unique($a);
}

function glob_recursive($pattern, $flags = 0)
{
	// Does not support flag GLOB_BRACE

	$files = glob($pattern, $flags);

	foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
		$files = array_merge($files, glob_recursive($dir . '/' . basename($pattern), $flags));
	}

	return $files;
}

function replace_to_space($text)
{
	$text = str_replace("\n", " ", $text);
	$text = str_replace("\r", " ", $text);
	$text = str_replace(",", " ", $text);
	$text = str_replace("  ", " ", $text);
	$text = trim($text);

	return $text;
}

function setFixSSLPath($nolog = null)
{
	exec("sh /script/fixsslpath");
}

function setInstallLetsencrypt($nolog = null)
{
	if (!file_exists("/usr/bin/letsencrypt-auto")) {
		exec("sh /script/letsencrypt-installer");
	}
}

function setRemoveLetsencrypt($nolog = null)
{
	exec("sh /script/letsencrypt-remover");
}

function setInstallAcmesh($nolog = null)
{
	exec("sh /script/acme.sh-installer");
}

function setRemoveAcmesh($nolog = null)
{
	exec("sh /script/acme.sh-remover");
}

function setInstallStartapish($nolog = null)
{
	if (!file_exists("/root/.startapi.sh/startapi.sh")) {
		exec("sh /script/startapi.sh-installer");
	}
}

function setRemoveStartapish($nolog = null)
{
	exec("sh /script/startapi.sh-remover");
}

function setInstallHttpry($nolog = null)
{
	exec("sh /script/httpry-installer");
}

function setAllSSLPortions($nolog = null)
{
	log_cleanup("Set All SSL Portions", $nolog);

	log_cleanup("- Copy 'openssl' config Files", $nolog);
	setCopyOpenSSLConfFiles();

//	log_cleanup("- Copy 'letsencrypt-auto' config Files", $nolog);
//	setCopyLetsEncryptConfFiles();

	log_cleanup("- Copy 'acme.sh' config Files", $nolog);
	setCopyAcmeshConfFiles();

	// MR -- StartAPI already stopped. So disabled
//	log_cleanup("- Copy 'startapi.sh' config Files", $nolog);
//	setCopyStartapishConfFiles();

//	log_cleanup("- Install Letsencrypt-auto", $nolog);
//	setInstallLetsencrypt($nolog);

//	log_cleanup("- Remove Letsencrypt-auto", $nolog);
//	setRemoveLetsencrypt($nolog);

	log_cleanup("- Install acme.sh", $nolog);
	setInstallAcmesh($nolog);

	// MR -- StartAPI already stopped. So disabled
//	log_cleanup("- Install startapi.sh", $nolog);
//	setInstallStartapish($nolog);

	log_cleanup("- Fix SSL path", $nolog);
	setFixSSLPath($nolog);
}

function setHttpry($nolog = null)
{
	log_cleanup("- Copy 'httpry' config Files", $nolog);
	setCopyHttpryConfFiles();

	log_cleanup("- Install httpry", $nolog);
	setInstallHttpry($nolog);
}

function setCronBackup($nolog = null)
{
	log_cleanup("- Fix 'Cron Backup' Files", $nolog);
	exec("sh /script/fix-cron-backup");
}

function getListOnList($pname)
{
	$p = "../etc/list";
	$f = getLinkCustomfile($p, "{$pname}.lst");
	$c = trimSpaces(file_get_contents($f));

	$a = explode(",", $c);

	if (!$a) {
		$a = array($c);
	}

	return $a;
}

function callWithSudo($res, $username = null)
{
	if (!isset($username)) {
		$username = $res->arglist[0];
	}

	if (isset($res->func)) {
		log_log("sudo_action", "Running: " . serialize($res->func) . " as $username ");
	} else if (isset($res->robject)) {
		log_log("sudo_action", "Running: " . serialize($res->robject) . " as $username ");
	}

	$var = lxshell_output("sudo", "-u", $username, "__path_php_path", "../bin/common/sudo_action.php",
		escapeshellarg(base64_encode(serialize($res))));

	$rmt = unserialize(base64_decode($var));

	return $rmt;
}

function setAllWebServerInstall($nolog = null)
{
	log_cleanup("Install All Web servers", $nolog);

	$list = getAllRealWebDriverList();

	$ws = array('nginx' => 'nginx nginx-module* GeoIP spawn-fcgi fcgiwrap', 'lighttpd' => 'lighttpd lighttpd-fastcgi',
		'hiawatha' => 'hiawatha hiawatha-addons', 'httpd' => 'httpd httpd-tools',
		'httpd24u' => 'httpd24u httpd24u-tools httpd24u-filesystem httpd24u-mod_security2');

	$hm = array('httpd' => 'mod_ssl mod_rpaf mod_ruid2 mod_suphp mod_fastcgi mod_fcgid mod_define',
		'httpd24u' => 'httpd24u-mod_ssl httpd24u-mod_session mod24u_suphp mod24u_ruid2 mod24u_fcgid mod24u_fastcgi mod24u_evasive');

	if (file_exists("../etc/flag/use_apache24.flg")) {
		$use_apache24 = true;
	} else {
		if (version_compare(getRpmVersion('httpd'), '2.4.0', '>') || version_compare(getRpmVersion('httpd24u'), '2.4.0', '>') ) {
			$use_apache24 = true;
			exec("echo '' > ../etc/flag/use_apache24.flg");
		} else {
			$use_apache24 = false;
		}
	}

	foreach ($list as $k => $v) {
		$confpath = "/opt/configs/{$v}/etc/conf";

		if ($v === 'apache') {
			$a24mpath = "/etc/httpd/conf.modules.d";
			if ($use_apache24) {
				if (isRpmInstalled('httpd')) {
					if (file_exists("{$a24mpath}/00-base.conf")) {
						exec("'mv' -f {$a24mpath}/00-base.conf {$a24mpath}/00-base.conf.rpmold");
					}

					exec("yum -y replace httpd --replace-with=httpd24u >/dev/null 2>&1;" .
						"yum -y remove {$hm['httpd']} >/dev/null 2>&1;" .
						"yum -y install {$hm['httpd24u']} >/dev/null 2>&1");

					log_cleanup("- Replace for 'apache' (to 'httpd24u')", $nolog);
				} else {
					log_cleanup("- No process for 'apache' ('httpd24')", $nolog);
				}

				$conffile = getLinkCustomfile("{$confpath}", "httpd24.conf");
				exec("'cp' -f {$conffile} /etc/httpd/conf/httpd.conf");
			} else {
				if (isRpmInstalled('httpd24u')) {
					if (file_exists("{$a24mpath}/00-base.conf")) {
						exec("'mv' -f {$a24mpath}/00-base.conf {$a24mpath}/00-base.conf.rpmold");
					}

					exec("yum -y replace httpd24u --replace-with=httpd >/dev/null 2>&1;" .
						"yum -y remove {$hm['httpd24u']} >/dev/null 2>&1;" .
						"yum -y install {$hm['httpd']} >/dev/null 2>&1");

					log_cleanup("- Replace for 'apache' (to 'httpd24u')", $nolog);
				} else {
					log_cleanup("- No process for 'apache' ('httpd')", $nolog);
				}

				$conffile = getLinkCustomfile("{$confpath}", "httpd.conf");
				exec("'cp' -f {$conffile} /etc/httpd/conf/httpd.conf");
			}

			if (file_exists("../etc/flag/use_pagespeed.flg")) {
				// MR -- this is a trick to use isRpmInstalled
				if (!isRpmInstalled('| grep pagespeed')) {
					exec("yum -y install mod-pagespeed-stable");
				}

				lxfile_cp(getLinkCustomfile("/opt/configs/apache/etc/conf.d", "pagespeed.conf"),
					"/etc/httpd/conf.d/pagespeed.conf");
			} else {
				lxfile_cp("/opt/configs/apache/etc/conf.d/_inactive_.conf", "/etc/httpd/conf.d/pagespeed.conf");
			}
		} else {
			$t = $ws[$v];

			if (isRpmInstalled($v)) {
				if (isServiceExists($v)) {
					log_cleanup("- No process for '{$v}'", $nolog);
				}
			} else {
				exec("yum -y install {$t} >/dev/null 2>&1");
				log_cleanup("- Install '{$v}'", $nolog);
			}
		}
	}
}

function setAllInactivateWebServer($nolog = null)
{
	log_cleanup("Inactivate Web servers", $nolog);

	$list = getAllRealWebDriverList();

	foreach ($list as $k => $v) {
		if ($v === 'apache') {
			$a = 'httpd';
		} else {
			$a = $v;
		}

		log_cleanup("- Inactivate '{$v}'", $nolog);
		exec("chkconfig {$a} off >/dev/null 2>&1");

		exec("chkconfig spawn-fcgi off >/dev/null 2>&1");
	}
}

function setActivateWebServer($nolog = null)
{
	log_cleanup("Activate Web servers", $nolog);

	$list = getWebDriverList();

	foreach ($list as $k => $v) {
		if ($v === 'apache') {
			$a = 'httpd';
		} else {
			$a = $v;
		}

		log_cleanup("- Activate '{$v}' as Web server", $nolog);
		exec("chkconfig {$a} on >/dev/null 2>&1");

		if ($v === 'nginx') {
			exec("chkconfig spawn-fcgi on >/dev/null 2>&1");
		}
	}
}

function setAllDnsServerInstall($nolog = null)
{
	log_cleanup("Install All Dns servers", $nolog);

	$list = getAllRealDnsDriverList();

	$ds = array('bind' => 'bind bind-utils bind-libs', 'djbdns' => 'djbdns',
		'nsd' => 'nsd', 'pdns' => 'pdns pdns-backend-mysql pdns-tools pdns-geo',
		'yadifa' => 'yadifa yadifa-tools');


	foreach ($list as $k => $v) {
		// MR -- remove because may conflict with djbdns
		if ($v === 'djbdns') {
			if (isRpmInstalled('spamdyke-utils')) { 
				setRpmRemovedViaYum("spamdyke-utils");
			}
		}

		$confpath = "/opt/configs/{$v}/etc/conf";

		$t = $ds[$v];

		$a = ($v === 'bind') ? 'named' : $v;
		$a = ($v === 'yadifa') ? 'yadifad' : $a;

		if (isRpmInstalled($v)) {
			if (isServiceExists($a)) {
				log_cleanup("- No process for '{$v}'", $nolog);
			}
		} else {
			exec("yum -y install {$t} >/dev/null 2>&1");
			log_cleanup("- Install '{$v}'", $nolog);

			if ($v === 'djbdns') {
				exec("sh /script/setup-djbdns");
			} elseif ($v === 'pdns') {
				PreparePowerdnsDb($nolog);
			} elseif ($v === 'yadifa') {
				if (!isServiceExists($a)) {
					exec("yum -y install yadifa-tools >/dev/null 2>&1");
				}
			}
		}
	}
}

function setAllInactivateDnsServer($nolog = null)
{
	log_cleanup("Inactivate DNS servers", $nolog);

	$list = getAllRealDnsDriverList();

	foreach ($list as $k => $v) {
		if ($v === 'bind') {
			$a = 'named';
		} elseif ($v === 'yadifa') {
			$a = 'yadifad';
		} else {
			$a = $v;
		}

		log_cleanup("- Inactivate '{$v}'", $nolog);
		exec("chkconfig {$a} off >/dev/null 2>&1");
	}
}

function setActivateDnsServer($nolog = null)
{
	log_cleanup("Activate Dns servers", $nolog);

	$list = getDnsDriverList();

	foreach ($list as $k => $v) {
		if ($v === 'bind') {
			$a = 'named';
		} elseif ($v === 'yadifa') {
			$a = 'yadifad';
		} else {
			$a = $v;
		}

		log_cleanup("- Activate '{$v}' as Dns server", $nolog);
		exec("chkconfig {$a} on >/dev/null 2>&1");
	}
}

function setPhpUpdate($nolog = null)
{
	log_cleanup("Update All Php (branch and multiple)", $nolog);
	log_cleanup("- Update process", $nolog);
	exec("yum -y update php*; sh /script/phpm-updater");
}

function setCopyIndexFileToAwstatsDir($nolog = null)
{
	$tdir = "/home/kloxo/httpd/awstats/wwwroot/cgi-bin";
	$sdir = "../file/stats";

	if (file_exists($tdir)) {
		log_cleanup("Copy awstats_index.php to {$tdir}", $nolog);
		$file = getLinkCustomfile($sdir, "awstats_index.php");
		copy($file, "{$tdir}/index.php");
	}
}

function getRemoteIp()
{
	if(!empty($_SERVER['HTTP_CLIENT_IP'])) { # check ip from share internet
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) { # to check ip is pass from proxy
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}

	return $ip;
}

function isServiceExists($target)
{
	if ((file_exists("/etc/rc.d/init.d/{$target}")) ||
			(file_exists("/usr/lib/systemd/system/{$target}.service"))) {
		return true;
	} else {
		return false;
	}
}

function isServiceEnabled($target)
{
	$ret = false;

	exec("command -v systemctl", $test);

	if (count($test) > 0) {
		exec("systemctl list-unit-files --type=service|grep ^{$target}|grep 'enabled'", $val2);

		if (count($val2) > 0) {
			$ret = true;
		}
	}

	exec("chkconfig --list 2>/dev/null|grep ^{$target}|grep ':on'", $val1);

	if (count($val1) > 0) {
		$ret = true;
	}
	
	return $ret;
}

function getServiceType($target = null)
{
	$ret = '';

	if ($target) {
		exec("command -v systemctl", $test);

		if (count($test) > 0) {
			exec("systemctl list-unit-files --type=service|grep ^{$target}", $val2);

			if (count($val2) > 0) {
				$ret = 'systemd';
			}
		}

		exec("chkconfig --list 2>/dev/null|grep ^{$target}", $val1);

		if (count($val1) > 0) {
			$ret = 'init';
		}
	} else {
		exec("ps --no-headers -o comm 1", $test);
		
		$ret = $test[0];
	}

	return $ret;
}

function isServiceRunning($srvc)
{
//	$ret = lxshell_return("service", $srvc, "status", "|", "grep", "'(pid'");
//	$ret = lxshell_return("service", $srvc, "status", "|", "grep", "'running'");
	$ret = lxshell_return("pgrep", "^'{$srvc}'");

	if ($ret) {
		return false;
	} else {
		return true;
	}
}

## MR -- taken from http://stackoverflow.com/questions/5695145/how-to-read-and-write-to-an-ini-file-with-php

function write_ini($array, $file)
{
	$res = array();

	foreach($array as $key => $val) {
		if(is_array($val)) {
			$res[] = "[$key]";

			foreach ($val as $skey => $sval) {
				$res[] = "$skey = ".(is_numeric($sval) ? $sval : '"'.$sval.'"');
			}
		} else {
			$res[] = "$key = ".(is_numeric($val) ? $val : '"'.$val.'"');
		}
	}

	safefilerewrite($file, implode("\r\n", $res));
}

function safefilerewrite($fileName, $dataToSave)
{
	if ($fp = fopen($fileName, 'w')) {
		$startTime = microtime(TRUE);

		do {
			$canWrite = flock($fp, LOCK_EX);

			if (!$canWrite) {
				// If lock not obtained sleep for 0 - 100 milliseconds, to avoid collision and CPU load
				usleep(round(rand(0, 100)*1000));
			}
		} while ((!$canWrite)and((microtime(TRUE)-$startTime) < 5));

		//file was locked so now we can store information
		if ($canWrite) {
			fwrite($fp, $dataToSave);
			flock($fp, LOCK_UN);
		}

		fclose($fp);
	}
}
