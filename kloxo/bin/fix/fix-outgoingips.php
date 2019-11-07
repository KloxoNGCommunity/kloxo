<?php 

include_once "lib/html/include.php"; 

initProgram('admin');

$login->loadAllObjects('client');
$list = $login->getList('client');

log_cleanup("Fixing Mail Outgoing IP",  $nolog = null);

$t = '';

$flgfile = "/usr/local/lxlabs/kloxo/etc/flag/manualoutgoingips.flg";

if (file_exists($flgfile)) {
	print("- No process because '{$flgfile}' exists\n");
} else {
	foreach($list as $c) {
		$dlist = $c->getList('domain');

		foreach($dlist as $l) {
			$dns = $l->getObject('dns');

			foreach($dns->dns_record_a as $drec) {
				if (($drec->ttype === 'a') && ($drec->hostname === '__base__')) {
					print("- For '{$dns->nname}' ('{$c->nname}') at '{$c->syncserver}'\n");

					if (stripos($drec->param, ":") !== false) {
						$ip = "[{$drec->param}]";
					} else {
						$ip = $drec->param;
					}

					$t .= "{$dns->nname}:{$ip}\n";
				}
			}

			$dns->was();
		}
	
		file_put_contents("/var/qmail/control/outgoingips", $t);
	}
}

