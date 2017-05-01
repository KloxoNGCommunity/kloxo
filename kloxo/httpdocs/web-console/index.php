<?php

$v = $_REQUEST['session'];

$v = unserialize(base64_decode($v));

$r = unserialize(file_get_contents("../../session/ssh_{$v['session']}"));

if ($r !== $_SERVER['REMOTE_ADDR']) {
	print("No Session. You can access this only through kloxo and needs proper authentication. " .
		"If you are indeed accessing from Inside Kloxo, then please logout and login again, " .
		"so that a new session is created properly.\n");

	exit;
} else {
	include_once "web-console.php";
}
