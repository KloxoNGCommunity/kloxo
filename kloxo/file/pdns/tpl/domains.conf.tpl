<?php

$conn = new mysqli('localhost', 'root', $rootpass, 'powerdns');

if (($action === 'delete') || ($action === 'update')) {
	if($query = $conn->query("SELECT * FROM domains WHERE name='{$domainname}';")) {
		while ($row = $query->fetch_object()) {
			$rowid = $row->id;

			$conn->query("DELETE FROM domains WHERE id='{$rowid}'");
			$conn->query("DELETE FROM records WHERE domain_id='{$rowid}'");
			$conn->query("DELETE FROM domainmetadata WHERE domain_id='{$rowid}'");
		}
	}
}

if (($action === 'add') || ($action === 'update')) {
	foreach($dns_records as $dns) {
		if ($dns->ttype === "ns") {
			if (!$nameserver) {
				$nameserver = $dns->param;
			}
		}

		if ($dns->ttype === 'a') {
			$arecord[$dns->hostname] = $dns->param;
		}
	}

	if ($soanameserver) {
		$nameserver = $soanameserver;
	}

	$email = str_replace("@", ".", $email);
	$refresh = isset($refresh) && strlen($refresh) > 0 ? $refresh : 3600;
	$retry = isset($retry) && strlen($retry) > 0 ? $retry : 1800;
	$expire = isset($expire) && strlen($expire) > 0 ? $expire : 604800;
	$minimum = isset($minimum) && strlen($minimum) > 0 ? $minimum : 1800;

	$conn->query("INSERT INTO domains (name,type) values('$domainname', 'MASTER');");

//	if ($conn->insert_id === 0) { return; }

	$domain_id = $conn->insert_id;

	$soa = "{$nameserver} {$email} {$serial} {$refresh} {$retry} {$expire} {$minimum}";

	$conn->query("INSERT INTO records (domain_id, name, content, type, ttl, prio) " .
	"VALUES ('$domain_id', '$domainname', '$soa', 'SOA', '$ttl', 'NULL');");

	foreach($dns_records as $k => $o) {
		switch ($o->ttype) {
			case "ns":
				$conn->query("INSERT INTO records (domain_id, name, content, type, ttl, prio) " .
					"VALUES ('$domain_id', '$domainname', '$o->param', 'NS', '$ttl', 'NULL');");

				break;
			case "mx":
				$v = $o->priority;

				$conn->query("INSERT INTO records (domain_id, name, content, type, ttl, prio) " .
					"VALUES ('$domain_id', '$domainname', '$o->param', 'MX', '$ttl', '$v');");

				break;
			case "a":
				$key = $o->hostname;
				$value = $o->param;

				if ($key !== "__base__") {
					$key = "$key.$domainname";
				} else {
					$key = "$domainname";
				}

				$conn->query("INSERT INTO records (domain_id, name, content, type, ttl, prio) " .
					"VALUES ('$domain_id', '$key', '$value', 'A', '$ttl', 'NULL');");

				break;
			case "cn":
			case "cname":
				$key = $o->hostname;
				$value = $o->param;
				$key .= ".$domainname";

				if (isset($arecord[$value])) {
					$rvalue = $arecord[$value];

					$conn->query("INSERT INTO records (domain_id, name, content, type, ttl, prio) ".
						"VALUES ('$domain_id', '$key', '$rvalue', 'A', '$ttl', 'NULL');");
				} else {
					if ($value !== "__base__") {
						$value = "$value.$domainname";
					} else {
						$value = "$domainname";
					}

					$conn->query("INSERT INTO records (domain_id, name, content, type, ttl, prio) ".
						"VALUES ('$domain_id', '$key', '$value', 'CNAME', '$ttl', 'NULL');");
				}

				break;
			case "fcname":
				$key = $o->hostname;
				$value = $o->param;
				$key .= ".$domainname";

				if ($value !== "__base__") {
					if (!cse($value, ".")) {
						$value = "$value.";
					}
				} else {
					$value = "$domainname";
				}

				$conn->query("INSERT INTO records (domain_id, name, content, type, ttl, prio) " .
					"VALUES ('$domain_id', '$key', '$value', 'CNAME', '$ttl', 'NULL');");

				break;
			case "txt":
				$key = $o->hostname;
				$value = $o->param;

				if ($o->param === null) {
					continue;
				}

				if ($key !== "__base__") {
					$key = "$key.$domainname.";
				} else {
					$key = "$domainname.";
				}

				$value = '"' . str_replace("<%domain>", $domainname, $value) . '"';

				$conn->query("INSERT INTO records (domain_id, name, content, type, ttl, prio) " .
					"VALUES ('$domain_id', '$key', '$value', 'TXT', '$ttl', 'NULL');");

				if (strpos($value, "v=spf1") !== false) {
					$conn->query("INSERT INTO records (domain_id, name, content, type, ttl, prio) " .
						"VALUES ('$domain_id', '$key', '$value', 'SPF', '$ttl', 'NULL');");
				}

				break;
		}
	}

	$conn->query("INSERT INTO domainmetadata (domain_id, kind, content) " .
		"VALUES ('{$domain_id}','ALLOW-AXFR-FROM','AUTO-NS');");
}

$conn->close();