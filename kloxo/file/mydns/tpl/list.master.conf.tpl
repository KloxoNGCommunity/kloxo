<?php

	$d1names = $domains;

	$conn = new mysqli('localhost', 'root', $rootpass, 'mydns');

	if ($query = $conn->query("SELECT * FROM soa WHERE origin='*';")) {
		while ($row = $query->fetch_object()) {
			$d2names[] = $row->origin;
			$d2ids[] = $row->id;
		}
	}

	$d2olds = array_diff($d2names, $d1names);

	// MR -- delete unwanted domains
	if (!empty($d2olds)) {
		foreach ($d2olds as $k => $v) {
			$id = d2ids[$k];

			$conn->query("DELETE FROM soa WHERE origin='{$v}'");
			$conn->query("DELETE FROM soa WHERE zone='{$id}'");
		}
	}

