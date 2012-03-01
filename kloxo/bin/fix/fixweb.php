<?php 

include_once "htmllib/lib/include.php"; 
initProgram('admin');

$list = parse_opt($argv);

$server = (isset($list['server'])) ? $list['server'] : 'localhost';
$client = (isset($list['client'])) ? $list['client'] : null;
$nolog  = (isset($list['nolog'])) ? $list['nolog'] : null;

$login->loadAllObjects('client');
$list = $login->getList('client');

log_cleanup("Fixing Web server config", $nolog);

$prevsyncserver = '';
$currsyncserver = '';

foreach($list as $c) {
	if ($client) {
	//	if ($client !== $c->nname) { continue; }
		$ca = explode(",", $client);
		if (!in_array($c->nname, $ca)) { continue; }
		$server = 'all';
	}

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
			$web->setUpdateSubaction('static_config_update');
			log_cleanup("- inside static (defaults/webmail) directory at '{$currsyncserver}'", 
					$nolog);
			$prevsyncserver = $currsyncserver;
		}

		$web->setUpdateSubaction('full_update');
		log_cleanup("- '{$web->nname}' ('{$c->nname}') at '{$web->syncserver}'", $nolog);

		$web->was();
	}
}

print("\nNote: also fixing php-fpm config\n");

if (!$nolog) {
	lxshell_return("lxphp.exe", "/usr/local/lxlabs/kloxo/bin/fix/fixphpfpm.php");
} else {
	lxshell_return("lxphp.exe", "/usr/local/lxlabs/kloxo/bin/fix/fixphpfpm.php", "--nolog");
}
