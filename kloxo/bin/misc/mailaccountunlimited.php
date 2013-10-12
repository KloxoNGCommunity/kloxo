<?php 
include_once "lib/html/include.php"; 
initProgram('admin');
$login->loadAllObjects('mailaccount');
$list = $login->getList('mailaccount');
foreach($list as $l) {
	$l->priv->maildisk_usage = "Unlimited";
	$l->setUpdateSubaction('full_update');
	$l->was();
}
