<?php 
include_once "lib/html/include.php"; 
initProgram('admin');


$login->loadAllObjects('client');
$list = $login->getList('client');

foreach($list as $c) {
	$dlist = $c->getList('domaina');
	foreach((array) $dlist as $l) {
		$web = $l->getObject('web');
		$rp = $l->getRealClientParentO();
		$web->customer_name = $rp->getPathFromName('nname');
		$web->setUpdateSubaction();
		$web->write();
	}
}

