<?php 

include_once "lib/html/include.php";
initProgram('admin');

$list = parse_opt($argv);

$server = (isset($list['server'])) ? $list['server'] : 'localhost';
$nolog  = (isset($list['nolog'])) ? $list['nolog'] : null;

$login->loadAllObjects('client');
$list = $login->getList('client');

$plist = $login->getList('pserver');

log_cleanup("Fixing WebCache server config", $nolog);

foreach($plist as $s) {
	$conftpl = 'defaults';

	$list = getAllWebCacheDriverList();
	$driver = $gbl->getSyncClass(null, $s->syncserver, 'webcache');;
/*
	if ($driver[0] === 'none') {
		foreach ($driverlist as $k => $v) {
			$srcinitpath = "/opt/configs/{$v}/etc/init.d";
			$trgtinitpath = "/etc/rc.d/init.d";

			if (file_exists("{$trgtinitpath}/{$v}")) {
				exec("service {$v} stop; chkconfig {$v} off");
				unlink("{$trgtinitpath}/{$v}");

				if ($v === 'varnish') {
					unlink("{$trgtinitpath}/{$v}log");
					unlink("{$trgtinitpath}/{$v}ncsa");
				}
			}
		}

		log_cleanup("- No process because using 'NONE' driver for '{$s->syncserver}'", $nolog);

		return;
	}
*/
	$input['driverlist'] = $list;
	$input['driver'] = $driver;

	foreach ($list as &$l) {
		log_cleanup("- '{$l}' at '{$s->syncserver}'", $nolog);
		
		$tplsource = getLinkCustomfile("/opt/configs/{$l}/tpl", "{$conftpl}.conf.tpl");
		$tpl = file_get_contents($tplsource);

		$tplparse = getParseInlinePhp($tpl, $input);
	}
}
