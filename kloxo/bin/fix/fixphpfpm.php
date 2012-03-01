<?php 

include_once "htmllib/lib/include.php"; 
initProgram('admin');

$list = parse_opt($argv);

$server = (isset($list['server'])) ? $list['server'] : 'localhost';
$nolog  = (isset($list['nolog'])) ? $list['nolog'] : null;

$login->loadAllObjects('client');
$list = $login->getList('client');

$prevsyncserver = '';
$currsyncserver = '';

foreach($list as $c) {
	if ($server !== 'all') {
	//	if ($c->syncserver !== $server) { continue; }
		$sa = explode(",", $server);
		if (!in_array($c->syncserver, $sa)) { continue; }
	}

	$dlist = $c->getList('domaina');

	foreach((array) $dlist as $l) {
		$web = $l->getObject('web');

		$currsyncserver = $web->syncserver;

		if ($prevsyncserver !== $currsyncserver) {
			if (!$nolog) {
				$web->setUpdateSubaction('fix_phpfpm');
			} else {
				$web->setUpdateSubaction('fix_phpfpm_nolog');
			}

			$prevsyncserver = $currsyncserver;
		}

		$web->was();
	}
}


