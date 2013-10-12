<?php 
include_once "lib/html/include.php"; 


$path = "/var/spool/cron";
$list = lscandir_without_dot($path);

foreach($list as $l) {
	lunlink("$path/$l");
}

initProgram('admin');

$login->loadAllObjects('cron');

$list = $login->getList('cron');

foreach($list as $c) {
	$c->__parent_o = null;
	$w = $c->getParentO();

	$c->username = $w->username;

	$c->setUpdateSubaction('update');
	$c->was();
}
