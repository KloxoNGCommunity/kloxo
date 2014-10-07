<?php 

include_once "lib/html/include.php"; 

initprogram('admin');

$list = parse_opt($argv);

$server = (isset($list['server'])) ? $list['server'] : 'localhost';
$client = (isset($list['client'])) ? $list['client'] : null;
$nolog  = (isset($list['nolog']))  ? $list['nolog'] : null;

if (file_exists("/etc/pure-ftpd/pureftpd.passwd")) {
	lxfile_mv("/etc/pure-ftpd/pureftpd.passwd", "/etc/pure-ftpd/pureftpd.passwd.oldsaved");

	if (file_exists("/etc/pure-ftpd/pureftpd.pdb")) {
		lunlink("/etc/pure-ftpd/pureftpd.pdb");
	}

	if (file_exists("/etc/pure-ftpd/pureftpd.passwd.tmp")) {
		lunlink("/etc/pure-ftpd/pureftpd.passwd.tmp");
	}
}

$login->loadAllObjects('client');
$list = $login->getList('client');

log_cleanup("Fixing FTP User", $nolog);

foreach($list as $c) {
	if ($client) {
		$ca = explode(",", $client);

		if (!in_array($c->nname, $ca)) { continue; }
	}

	if ($server !== 'all') {
		$sa = explode(",", $server);

		if (!in_array($c->syncserver, $sa)) { continue; }
	}

	$flist = $c->getList('ftpuser');

	foreach($flist as $fl) {
		log_cleanup("- '{$fl->nname}' ('{$c->nname}') at '{$fl->syncserver}'", $nolog);

	//	$fl->dbaction = 'syncadd';
		$fl->setUpdateSubaction('fix');

		$fl->was();
	}
}

exec("pure-pw mkdb");

