<?php

	function redirect_to_ssl() {
		if($_SERVER["HTTPS"] != "on") {
			header("HTTP/1.1 301 Moved Permanently");
			header("Location: https://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"] . ":" . file_get_contents("../../init/port-ssl"));
			exit();
		}
	}

	if (file_exists("/usr/local/lxlabs/kloxo/httpdocs/login/redirect-to-ssl")) {
		redirect_to_ssl();
	}
?>

	<link href="/theme/css/common.css" rel="stylesheet" type="text/css" />
	<link href="/theme/css/admin_login.css" rel="stylesheet" type="text/css" />

	<script language="javascript" src="/theme/js/login.js"></script>
	<script language="javascript" src="/theme/js/preop.js"></script>
	<script language="javascript" src="/theme/js/lxa.js"></script>
