<?php 

include_once "lib/html/include.php"; 
initProgram('admin');

$login->loadAllObjects('client');
$list = $login->getList('client');

foreach($list as $c) {
	$dlist = $c->getList('domain');
	foreach($dlist as $l) {
		$web = $l->getObject('web');
		$web->setUpdateSubaction('full_update');
		$dirp = $web->getList('dirprotect');

		foreach($dirp as $dp) {
			$dp->setUpdateSubaction('full_update');

			$dp->was();
		}

		$web->was();
	}
}
