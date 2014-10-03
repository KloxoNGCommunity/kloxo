<?php

$conn = new mysqli($syncserver, 'root', $rootpass, 'powerdns');

$nameserver = null;

foreach($dns_records as $dns) {
	if ($dns->ttype === "ns") {
		if (!$nameserver) {
			$nameserver = $dns->param;
		}
	}

	if ($dns->ttype === 'a') {
		$arecord[$dns->hostname] = $dns->param;

		if ($dns->hostname === '__base__') {
			$baseip = $dns->param;
		}
	}
}

if ($soanameserver) {
	$nameserver = $soanameserver;
}

// MR -- master dns portion
if (($action === 'delete') || ($action === 'update')) {
	if($query = $conn->query("SELECT * FROM domains WHERE name='{$domainname}' AND type='MASTER'")) {
		while ($row = $query->fetch_object()) {
			$rowid = $row->id;

			$conn->query("DELETE FROM records WHERE domain_id='{$rowid}'");
			$conn->query("DELETE FROM domainmetadata WHERE domain_id='{$rowid}'");
			$conn->query("DELETE FROM domains WHERE id='{$rowid}' AND type='MASTER';");
			$conn->query("DELETE FROM zones WHERE domain_id='{$rowid}' AND type='MASTER';");
		}
	}
}

if (($action === 'add') || ($action === 'update')) {
	$email = str_replace("@", ".", $email);
	$refresh = isset($refresh) && strlen($refresh) > 0 ? $refresh : 3600;
	$retry = isset($retry) && strlen($retry) > 0 ? $retry : 1800;
	$expire = isset($expire) && strlen($expire) > 0 ? $expire : 604800;
	$minimum = isset($minimum) && strlen($minimum) > 0 ? $minimum : 1800;

	// MR -- this table for SLAVE, so delete it if exists
	$conn->query("DELETE FROM supermasters WHERE nameserver LIKE '{$nameserver}'");


	$conn->query("INSERT INTO domains (name, type) values('{$domainname}', 'MASTER');");

	$domain_id = $conn->insert_id;

//	$conn->query("INSERT INTO zones (domain_id, owner) values('{$domain_id}', '1');");

	$soa = "{$nameserver} {$email} {$serial} {$refresh} {$retry} {$expire} {$minimum}";

	$conn->query("INSERT INTO records (domain_id, name, content, type, ttl, prio) " .
		"VALUES ('{$domain_id}', '{$domainname}', '{$soa}', 'SOA', '{$ttl}', 'NULL');");

	foreach($dns_records as $k => $o) {
		switch ($o->ttype) {
			case "ns":
				$value = $o->param;
				if ($o->param === $o->hostname) {
					$key = $domainname;
				} else {
					if (($o->hostname === '') || (!$o->hostname) || ($o->hostname === '__base__')) {
						$key = $domainname;
					} else {
						$key = $o->hostname;
 					}
				}

				$conn->query("INSERT INTO records (domain_id, name, content, type, ttl, prio) " .
					"VALUES ('{$domain_id}', '{$key}', '{$o->param}', 'NS', '{$ttl}', 'NULL');");

				break;
			case "mx":
				$v = $o->priority;

				$conn->query("INSERT INTO records (domain_id, name, content, type, ttl, prio) " .
					"VALUES ('{$domain_id}', '{$domainname}', '{$o->param}', 'MX', '{$ttl}', '{$v}');");

				break;
			case "a":
				$key = $o->hostname;
				$value = $o->param;

				if ($key !== "__base__") {
					$key = "{$key}.{$domainname}";
				} else {
					$key = "{$domainname}";
				}

				$conn->query("INSERT INTO records (domain_id, name, content, type, ttl, prio) " .
					"VALUES ('{$domain_id}', '{$key}', '{$value}', 'A', '{$ttl}', 'NULL');");

				break;
			case "cn":
			case "cname":
				$key = $o->hostname;
				$value = $o->param;
				$key .= ".{$domainname}";

				if (isset($arecord[$value])) {
					$rvalue = $arecord[$value];

					$conn->query("INSERT INTO records (domain_id, name, content, type, ttl, prio) ".
						"VALUES ('{$domain_id}', '{$key}', '{$rvalue}', 'A', '{$ttl}', 'NULL');");
				} else {
					if ($value !== "__base__") {
						$value = "{$value}.{$domainname}";
					} else {
						$value = "{$domainname}";
					}

					$conn->query("INSERT INTO records (domain_id, name, content, type, ttl, prio) ".
						"VALUES ('{$domain_id}', '{$key}', '{$value}', 'CNAME', '{$ttl}', 'NULL');");
				}

				break;
			case "fcname":
				$key = $o->hostname;
				$value = $o->param;
				$key .= ".{$domainname}";

				if ($value !== "__base__") {
					if (strpos($value, ".") !== false) {
						// no action
					} else {
						$value = "{$value}.";
					}
				} else {
					$value = "{$domainname}";
				}

				$conn->query("INSERT INTO records (domain_id, name, content, type, ttl, prio) " .
					"VALUES ('{$domain_id}', '{$key}', '{$value}', 'CNAME', '{$ttl}', 'NULL');");

				break;
			case "txt":
				$key = $o->hostname;
				$value = $o->param;

				if ($o->param === null) {
					continue;
				}

				if ($key !== "__base__") {
					$key = "{$key}.{$domainname}";
				} else {
					$key = "{$domainname}";
				}

				$value = '"' . str_replace("<%domain>", $domainname, $value) . '"';

				$conn->query("INSERT INTO records (domain_id, name, content, type, ttl, prio) " .
					"VALUES ('{$domain_id}', '{$key}', '{$value}', 'TXT', '{$ttl}', 'NULL');");

				if (strpos($value, "v=spf1") !== false) {
					$conn->query("INSERT INTO records (domain_id, name, content, type, ttl, prio) " .
						"VALUES ('{$domain_id}', '{$key}', '{$value}', 'SPF', '{$ttl}', 'NULL');");
				}

				break;
		}
	}

	$conn->query("INSERT INTO domainmetadata (domain_id, kind, content) " .
		"VALUES ('{$domain_id}','ALLOW-AXFR-FROM','AUTO-NS');");
}

// MR -- slave dns portion

if($query = $conn->query("SELECT * FROM domains WHERE type='SLAVE'")) {
	while ($row = $query->fetch_object()) {
		$rowid = $row->id;

		$conn->query("DELETE FROM records WHERE domain_id='{$rowid}'");
		$conn->query("DELETE FROM domainmetadata WHERE domain_id='{$rowid}'");
		$conn->query("DELETE FROM domains WHERE id='{$rowid}' AND type='SLAVE';");
		$conn->query("DELETE FROM zones WHERE domain_id='{$rowid}' AND type='MASTER';");
	}
}

$path = "/opt/configs/dnsslave_tmp";
$dirs = glob("{$path}/*");

$str = '';

$doms = array();

foreach ($dirs as $d) {
	$c = trim(file_get_contents($d));
	$d = str_replace("{$path}/", "", $d);

	$doms[] = $d;
}

foreach ($doms as $k => $v) {
	$ip = trim(file_get_contents("{$path}/{$v}"));

	$conn->query("INSERT INTO domains (name, master, type) values('{$v}', '{$ip}', 'SLAVE');");

	// MR -- account not implementing yet; importance for poweradmin to multiple use!
	$account = 'admin';

	// MR -- don't know nameserver, so use domain name
	$conn->query("INSERT INTO supermasters (ip, nameserver, account) " .
		"VALUES ('{$baseip}','{$v}','{$account}');");

//	$conn->query("INSERT INTO zones (domain_id, owner) values('{$v}', '1');");
}

$conn->close();