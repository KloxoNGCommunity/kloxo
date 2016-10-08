<?php

$conn = new mysqli('localhost', 'root', $rootpass, 'mydns');

if ($query = $conn->query("SELECT * FROM soa WHERE origin='{$domainname}';")) {
	if ($query->num_rows !== 0) {
		while ($row = $query->fetch_object()) {
			$rowid = $row->id;

			$conn->query("DELETE FROM rr WHERE zone='{$rowid}'");
		}
	}
}

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

$email = str_replace("@", ".", $email);
$refresh = isset($refresh) && strlen($refresh) > 0 ? $refresh : 3600;
$retry = isset($retry) && strlen($retry) > 0 ? $retry : 1800;
$expire = isset($expire) && strlen($expire) > 0 ? $expire : 604800;
$minimum = isset($minimum) && strlen($minimum) > 0 ? $minimum : 3600;

if (!$rowid) {
	$conn->query("INSERT INTO soa (origin, ns, mbox, serial, refresh, retry, expire, minimum, ttl) " .
		"values('{$domainname}, '{$nameserver}', '{$email}', '{$serial}', " .
		"'{$refresh}', '{$retry}', '{$expire}', '{$minimum}', '{$ttl}');");
	$zone = $conn->insert_id;
} else {
	$zone = $rowid;
}

foreach($dns_records as $k => $o) {
	switch ($o->ttype) {
		case "ns":
            $key = $o->hostname;
            $value = $o->param;

            if ($key === $value) {
                $key = $domainname;
            } else {
                if (($key === '') || (!$key) || ($key === '__base__')) {
                    $key = $domainname;
                } else {
                    if (strpos($key, '__base__') !== false) {
                        $key = str_replace('__base__', $domainname, $key);
                    } else {
                        $key = "{$key}.{$domainname}";
                    }
                }
            }

			$conn->query("INSERT INTO rr (zone, name, data, aux, ttl, type) " .
				"VALUES ('{$zone}', '{$key}', '{$value}', 'NULL', '{$ttl}', 'NS');");

			break;
		case "mx":
			$key = $domainname;
			$value = $o->param;
			$prio = $o->priority;
			$conn->query("INSERT INTO rr (zone, name, data, aux, ttl, type) " .
				"VALUES ('{$zone}', '{$key}', '{$value}', '{$prio}', '{$ttl}', 'MX');");

			break;
		case "a":
			$key = $o->hostname;
			$value = $o->param;

			if ($key !== "__base__") {
				$key = "{$key}.{$domainname}";
			} else {
				$key = $domainname;
			}

			$conn->query("INSERT INTO rr (zone, name, data, aux, ttl, type) " .
				"VALUES ('{$zone}', '{$key}', '{$value}', 'NULL', '{$ttl}', 'A');");

			break;
		case "aaaa":
			$key = $o->hostname;
			$value = $o->param;

			if ($key !== "__base__") {
				$key = "{$key}.{$domainname}";
			} else {
				$key = $domainname;
			}

			$conn->query("INSERT INTO rr (zone, name, data, aux, ttl, type) " .
				"VALUES ('{$zone}', '{$key}', '{$value}', 'NULL', '{$ttl}', 'AAAA');");

			break;
		case "cn":
		case "cname":
			$key = $o->hostname;
			$value = $o->param;
			$key .= ".{$domainname}";

			if (isset($arecord[$value])) {
				$rvalue = $arecord[$value];

				$conn->query("INSERT INTO rr (zone, name, data, aux, ttl, type) " .
					"VALUES ('{$zone}', '{$key}', '{$rvalue}', 'NULL', '{$ttl}', 'A');");
			} else {
				if ($value !== "__base__") {
					$value = "{$value}.{$domainname}";
				} else {
					$value = $domainname;
				}

				$conn->query("INSERT INTO rr (zone, name, data, aux, ttl, type) " .
					"VALUES ('{$zone}', '{$key}', '{$value}', 'NULL', '{$ttl}', 'CNAME');");
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
				$value = $domainname;
			}

			$conn->query("INSERT INTO rr (zone, name, data, aux, ttl, type) " .
				"VALUES ('{$zone}', '{$key}', '{$value}', 'NULL', '{$ttl}', 'CNAME');");

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
				$key = $domainname;
			}

			$value = str_replace("<%domain%>", $domainname, $value);
			$value = str_replace("__base__", $domainname, $value);
			$value = '"' . $value . '"';

			$conn->query("INSERT INTO rr (zone, name, data, aux, ttl, type) " .
				"VALUES ('{$zone}', '{$key}', '{$value}', 'NULL', '{$ttl}', 'TXT');");

		/*
			if (strpos($value, "v=spf1") !== false) {
				$conn->query("INSERT INTO rr (zone, name, data, aux, ttl, type) " .
					"VALUES ('{$zone}', '{$key}', '{$value}', 'NULL', '{$ttl}', 'SPF');");
			}
		*/
			break;
	}
}

$conn->close();