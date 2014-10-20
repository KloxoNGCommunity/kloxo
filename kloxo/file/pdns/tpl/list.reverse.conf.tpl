<?php

	$d1names = $arpas;

	$conn = new mysqli('localhost', 'root', $rootpass, 'powerdns');

	if ($query = $conn->query("SELECT * FROM domains WHERE type='MASTER';")) {
		while ($row = $query->fetch_object()) {
			$d2names[] = $row->name;
			$d2ids[] = $row->id;
		}
	}

	$d2olds = array_diff($d2names, $d1names);

	// MR -- delete unwanted domains
	if (!empty($d2olds)) {
		foreach ($d2olds as $k => $v) {
			$id = d2ids[$k];

			$conn->query("DELETE FROM domains WHERE name='{$v}'");
			$conn->query("DELETE FROM zones WHERE domain_id='{$id}'");
			$conn->query("DELETE FROM domainmetadata WHERE domain_id='{$id}'");
			$conn->query("DELETE FROM supermasters WHERE nameserver LIKE '{$v}'");
			$conn->query("DELETE FROM records WHERE domain_id='{$id}'");
		}
	}

