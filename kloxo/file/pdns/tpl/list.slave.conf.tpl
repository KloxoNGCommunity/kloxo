<?php

	foreach($domains as $k => $v) {
		$t = explode(':', $v);

		$d1names[] = $t[0];
		$d1ips[] = $t[1];
	}

	$conn = new mysqli('localhost', 'root', $rootpass, 'powerdns');

	if ($query = $conn->query("SELECT * FROM domains WHERE type='SLAVE';")) {
		while ($row = $query->fetch_object()) {
			$d2names[] = $row->name;
			$d2ids[] = $row->id;
		}
	}

	$d2olds = array_diff($d2names, $d1names);

	// MR -- delete unwanted domains
	if (!empty($d2olds)) {
		foreach ($d2olds as $k => $v) {
			$id = $d2ids[$k];

			$conn->query("DELETE FROM domains WHERE name='{$v}'");
			$conn->query("DELETE FROM zones WHERE domain_id='{$id}'");
			$conn->query("DELETE FROM domainmetadata WHERE domain_id='{$id}'");
			$conn->query("DELETE FROM supermasters WHERE nameserver LIKE '{$v}'");
			$conn->query("DELETE FROM records WHERE domain_id='{$id}'");
		}
	}

	if (!empty($d1names)) {
		foreach ($d1names as $k => $v) {
			if ($result = $conn->query("SELECT * FROM domains WHERE name='{$v}' AND type='SLAVE';")) {
				if ($result->num_rows !== 0) {
					continue;
				}
			}

			$ip = $d1ips[$k];

			$conn->query("INSERT INTO domains (name, master, type) VALUES ('{$v}', '{$ip}', 'SLAVE');");

			if ($result = $conn->query("SELECT * FROM domains WHERE name='{$v}' AND type='SLAVE';")) {
				while ($row = $result->fetch_object()) {
					$domain_id = $row->id;
				}
			}

			$conn->query("INSERT INTO zones (domain_id, owner) VALUES ('{$domain_id}', '1');");
		}
	}

	$conn->close();
