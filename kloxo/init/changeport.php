<?php

$kpath = "/usr/local/lxlabs/kloxo";

include_once "{$kpath}/httpdocs/htmllib/lib/include.php";

initProgram('admin');

$sslport = $sgbl->__var_prog_ssl_port;
$nonsslport = $sgbl->__var_prog_port;

$gen = $login->getObject('general')->portconfig_b;

if (isset($gen)) {
	if (isset($gen->sslport) && ($gen->sslport !== '')) {
		$sslport = $gen->sslport;
	}

	if (isset($gen->nonsslport) && ($gen->nonsslport !== '')) {
		$nonsslport = $gen->nonsslport;
	}
}

$sfile = getLinkCustomfile("{$kpath}/init", "hiawatha.conf.base");

$content = file_get_contents($sfile);

$content = str_replace("__nonssl_port__", $nonsslport, $content);
$content = str_replace("__ssl_port__", $sslport, $content);

file_put_contents("{$kpath}/init/hiawatha.conf", $content);