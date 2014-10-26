<?php 

// by mustafa@bigraf.com for Kloxo-MR

include_once "lib/html/include.php"; 

initProgram('admin');

$list = parse_opt($argv);

$server = (isset($list['server'])) ? $list['server'] : 'localhost';
$select = (isset($list['select'])) ? $list['select'] : 'all';
$client = (isset($list['client'])) ? $list['client'] : 'all';

$nolog  = (isset($list['nolog']))  ? $list['nolog'] : null;

log_cleanup("Fixing Chown and Chmod", $nolog);

$login->loadAllObjects('client');
$clist = $login->getList('client');

$prevsyncserver = '';
$currsyncserver = '';

foreach($clist as $c) {
	$cinfo = posix_getpwnam($c->nname);

	if (!$cinfo) { continue; }

	if ($server !== 'all') {
		$sa = explode(",", $server);
		if (!in_array($c->syncserver, $sa)) { continue; }
	}

	if ($client !== 'all') {
		$ca = explode(",", $client);
		if (!in_array($c->nname, $ca)) { continue; }
	}

	rl_exec_get(null, $c->syncserver, "setFixChownChmodWebPerUser", array($select, $c->nname, $nolog));
	rl_exec_get(null, $c->syncserver, "setFixChownChmodMailPerUser", array($select, $c->nname, $nolog));
}

