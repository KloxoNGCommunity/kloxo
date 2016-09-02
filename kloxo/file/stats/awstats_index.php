<?php
	$d = $_SERVER['HTTP_HOST'];
	$c = str_replace("stats.", "", $d);

	if ($_SERVER["HTTPS"] === 'on') {
		$s = 'https';
	} else {
		$s = 'http';
	}

	header("Location: {$s}://{$d}/awstats.pl?config={$c}");

	exit;