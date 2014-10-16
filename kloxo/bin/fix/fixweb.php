<?php 

include_once "lib/html/include.php";
initProgram('admin');

$list = parse_opt($argv);

$server = (isset($list['server'])) ? $list['server'] : 'localhost';
$client = (isset($list['client'])) ? $list['client'] : null;
$domain = (isset($list['domain'])) ? $list['domain'] : null;
$nolog  = (isset($list['nolog'])) ? $list['nolog'] : null;

$target = (isset($list['target'])) ? $list['target'] : 'all';

$login->loadAllObjects('client');
$list = $login->getList('client');

log_cleanup("Fixing Web server config", $nolog);

web__apache::setInstallPhpfpm();

$clist = array();
$slist = array();

$counter = 0;

foreach($list as $c) {
	$driverapp = $gbl->getSyncClass(null, $c->syncserver, 'web');

	if ($driverapp === 'none') {
		log_cleanup("- No process because using 'NONE' driver for '{$c->syncserver}'", $nolog);
	//	continue;

		return;
	}

	if ($client) {
		$ca = explode(",", $client);

		if (!in_array($c->nname, $ca)) { continue; }
	}

	if ($server !== 'all') {
		$sa = explode(",", $server);

		if (!in_array($c->syncserver, $sa)) { continue; }
	}

	$dlist = $c->getList('domaina');

	foreach((array) $dlist as $l) {
		$web = $l->getObject('web');

		if ($domain) {
			$da = explode(",", $domain);
			if (!in_array($web->nname, $da)) { continue; }
		}

		if (!in_array($web->syncserver, $slist)) {
			if (($target === 'all') || ($target === 'defaults')) {
				log_cleanup("- 'defaults' pages at '{$web->syncserver}'", $nolog);
				$web->setUpdateSubaction('static_config_update');
			}

			if (($target === 'all') || ($target === 'domains')) {
				if (($domain) || ($client)) {
					// no action
				} else {
					log_cleanup("- remove all domains configs at '{$web->syncserver}'", $nolog);
					$web->setUpdateSubaction('remove_all_domain_configs');
				}
			}

			if (strpos($driverapp, 'lighttpd) !== false) {
				// MR - also fix for lighttpd
				if (!file_exists("/var/log/lighttpd")) {
					mkdir("/var/log/lighttpd",0777);
				}

				chmod("/var/log/lighttpd", 0777);
			}

			$slist[] = $web->syncserver;
			array_unique($slist);
		}

		if (($target === 'all') || ($target === 'domains')) {
			log_cleanup("- '{$web->nname}' ('{$c->nname}') at '{$web->syncserver}'", $nolog);
			$web->setUpdateSubaction('full_update');

			// MR -- disabled because include inside fixphp
		//	log_cleanup("- '.htaccess' for '{$web->nname}' ('{$c->nname}') at '{$web->syncserver}'", $nolog);
		//	$web->setUpdateSubaction('htaccess_update');
		}

		$web->was();
	}
}


