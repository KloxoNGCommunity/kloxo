<?php

class sshauthorizedkey__sync extends Lxdriverclass
{
	function writeAuthorizedKey($key)
	{
		$username = $this->main->username;

	//	lxfile_mkdir($username, "/root/.ssh");
	//	lxuser_chmod($username, "/root/.ssh", "0755");

		if (!file_exists('/root/.ssh')) {
			exec("mkdir -p /root/.ssh");
		}

		$f = "/root/.ssh/{$username}.authorized_keys";

		file_put_contents($f, $key);

	//	lxuser_chmod($username, $f, "0644");

		$t = '';

		$dirs = glob("/root/.ssh/*.authorized_keys");

		foreach ($dirs as $k => $v) {
			$x = file_get_contents($v);
			$y = explode(" ", $x);
			$z = explode("@", $y[2]);

			$a = gethostname();
			$b = explode(".", $a);

			$s['user'] = $z[0];
			$s['hostname'] = $a;
			$s['host'] = $b[0];
			$s['file'] = $v;

			$t .= "Host " . $b[0] . "\n";
			$t .= "HostName " . $a . "\n";
			$t .= "IdentityFile " . str_replace("/root/", "~/", $v) . "\n";
			$t .= "User " . $z[0] . "\n\n";
		}

		file_put_contents("/root/.ssh/config", $t);
	}

	static function readAuthorizedKey($username)
	{
		$f = "/root/.ssh/{$username}.authorized_keys";

		if (file_exists($f)) {
			return file_get_contents($f);
		} else {
			return;
		}
	}

	static function readProtoAuthorizedKey($username)
	{
		$s = "/root/{$username}.id.rsa";

	//	lxshell_return("rm", "-f", "{$s}*");
		exec("'rm' -f {$s}*");
	
		$r = randomString(10);

		lxshell_return("ssh-keygen", "-t", "rsa", "-b", '4096', "-N", $r, "-f", $s);
		// MR -- because php running under lxlabs, need change
		lxshell_return("sed", "-i", "'s:lxlabs@:{$username}@:'", "{$s}.pub");

		$f = "{$s}.pub";

		if (lxfile_exists("{$f}2")) {
			$s = file_get_contents("{$f}2");
			$s = "\n{$s}\n";
		//	lfile_put_contents($username, $f, $s, FILE_APPEND);
			file_put_contents($f, $s);
			lunlink("{$f}2");
		}

		return file_get_contents($f);
	}

	function getCurrentAuthKey()
	{
		$res = self::getAuthorizedKey($this->main->username);

		foreach($res as $k => $v) {
			if ("{$this->main->syncserver}___{$v['nname']}" === $this->main->nname) {
				continue;
			}

			$output[] = $v['full_key'];
		}

		return $output;
	}

	function dbactionAdd()
	{
		// MR -- no need because no append
	//	$output = $this->getCurrentAuthKey();

		$output[] = $this->main->full_key;
		$output = implode("\n", $output);
		$this->writeAuthorizedKey($output);
	}

	function dbactionDelete()
	{
		$output = $this->getCurrentAuthKey();
		$output = implode("\n", $output);
		$this->writeAuthorizedKey($output);
	}

	static function getAuthorizedKey($username, $type = null)
	{
		if ($type === 'proto') {
			$v = self::readProtoAuthorizedKey($username);
		} else {
			$v = self::readAuthorizedKey($username);
		}

		if (!$v) { return; }

		$list = explode("\n", $v);

		foreach($list as $l) {
			$l = trim($l);

			if (!$l) { continue; }

			$l = trimSpaces($l);
			$vv = explode(" ", $l);

			$r['nname'] = fix_nname_to_be_variable_without_lowercase($vv[1]);
			$r['full_key'] = $l;
		//	$r['key'] = substr($vv[1], 0, 50);
		//	$r['key'] .= " .....";
			$r['key'] = $vv[1];

			$r['hostname'] = $vv[2];

			$r['username'] = $username;
			$r['type'] = $vv[0];

			$res[$r['nname']] = $r;
		}

		return $res;
	}	
}

