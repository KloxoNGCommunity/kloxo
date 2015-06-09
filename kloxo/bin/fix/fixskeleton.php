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

log_cleanup("Fixing Skeleton", $nolog);

$clist = array();
$slist = array();

$counter = 0;

foreach($list as $c) {
	$driverapp = $gbl->getSyncClass(null, $c->syncserver, 'web');

	if ($driverapp === 'none') {
		log_cleanup("- No process because using 'NONE' driver for '{$c->syncserver}'", $nolog);
		continue;
	}

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
		$counter++;

		$web = $l->getObject('web');

		if ($domain) {
			$da = explode(",", $domain);
			if (!in_array($web->nname, $da)) { continue; }
		}

		if (($target === 'all') || ($target === 'domains')) {
			log_cleanup("- Skeleton for '{$web->nname}' ('{$c->nname}') at '{$web->syncserver}'", $nolog);
			$web->setUpdateSubaction('skeleton_update');
		}

		$web->was();
	}
}

