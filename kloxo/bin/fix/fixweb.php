<?php 

include_once "htmllib/lib/include.php";
initProgram('admin');

$list = parse_opt($argv);

$server = (isset($list['server'])) ? $list['server'] : 'localhost';
$client = (isset($list['client'])) ? $list['client'] : null;
$nolog  = (isset($list['nolog'])) ? $list['nolog'] : null;

$target = (isset($list['target'])) ? $list['target'] : 'all';

$login->loadAllObjects('client');
$list = $login->getList('client');

log_cleanup("Fixing Web server config", $nolog);

$slist = array();

foreach($list as $c) {
	if ($client) {
		$ca = explode(",", $client);
		if (!in_array($c->nname, $ca)) { continue; }
		$server = 'all';
	}

	if ($server !== 'all') {
		$sa = explode(",", $server);
		if (!in_array($c->syncserver, $sa)) { continue; }
	}

	$dlist = $c->getList('domaina');

	foreach((array) $dlist as $l) {
		$web = $l->getObject('web');

		if (!in_array($web->syncserver, $slist)) {
			if (($target === 'all') || ($target === 'defaults')) {
				$web->setUpdateSubaction('static_config_update');
				log_cleanup("- php-fpm and 'defaults' at '{$web->syncserver}'", $nolog);
			//	$web->setUpdateSubaction('fix_phpfpm');
			//	log_cleanup("- php-fpm at '{$web->syncserver}'", $nolog);
			}

			$slist[] = $web->syncserver;
			array_unique($slist);
		}

		if (($target === 'all') || ($target === 'domains')) {
			$web->setUpdateSubaction('full_update');
			log_cleanup("- domain '{$web->nname}' ('{$c->nname}') at '{$web->syncserver}'", $nolog);
		}

		$web->was();
	}
}

// MR - fix for php-fpm and fastcgi session issue
mkdir("/var/log/php-fpm",0755);
chmod("/var/lib/php/session", 0777);

// MR - also fix for lighttpd
mkdir("/var/log/lighttpd",0777);
chmod("/var/log/lighttpd", 0777);

