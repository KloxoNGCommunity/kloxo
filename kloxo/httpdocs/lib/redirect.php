<?php
	function redirect_to_hostname() {
		$host = $_SERVER["HTTP_HOST"];
		$scheme = $_SERVER["HTTP_SCHEME"];
		$hostname = preg_replace('/(cp\.|webmail\.|www\.|mail\.)(.*)/i', "$2", $host);
		$requesturi = $_SERVER["REQUEST_URI"];

		if (preg_match('/(cp\.|webmail\.|www\.|mail\.)(.*)/i', $host)) {
			header("HTTP/1.1 301 Moved Permanently");
			header("Location: {$scheme}://{$hostname}{$requesturi}");
			exit();
		}
	}

	function redirect_to_ssl() {
		if($_SERVER["HTTPS"] !== "on") {
			$fronthost = explode(":", $_SERVER["HTTP_HOST"]);
			$port = file_get_contents("/usr/local/lxlabs/kloxo/init/port-ssl");
			$requesturi = $_SERVER["REQUEST_URI"];

			header("HTTP/1.1 301 Moved Permanently");
			header("Location: https://{$fronthost[0]}:{$port}{$requesturi}");
			exit();
		}
	}

	if (file_exists("/usr/local/lxlabs/kloxo/httpdocs/login/redirect-to-hostname")) {
		// MR -- use address without www., cp., webmail. and mail. prefix
		redirect_to_hostname();
	}

	if (file_exists("/usr/local/lxlabs/kloxo/httpdocs/login/redirect-to-ssl")) {
		redirect_to_ssl();
	}