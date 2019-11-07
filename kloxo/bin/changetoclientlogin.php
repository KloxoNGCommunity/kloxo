<?php 

include_once "lib/html/include.php"; 
 
initprogram('admin');

$login->loadAllObjects('client');

$list = $login->getList('client');

foreach($list as $l) {
	$l->username = str_replace(" ", "", $l->nname);
	$l->setUpdateSubaction('createuser');
	$l->was();
}

foreach($list as $c) {

	$dlist = $c->getList('domain');

	foreach($dlist as $d) {
		$w = $d->getObject('web');

		if ($w->ftpusername) {
			continue;
		}

		$hpath = "/home/httpd/{$w->nname}/httpdocs";

		if (is_link($hpath)) {
			continue;
		}

		$uuser = $w->getObject('uuser');
		$w->ftpusername = $w->username;
		$flist = $w->getList('ftpuser');
		$ftpuser = new Ftpuser(null, $w->syncserver, $w->ftpusername);
		$ftpuser->initThisdef();
		$ftpuser->dbaction = 'add';
		$ftpuser->syncserver = $w->syncserver;
		$ftpuser->createSyncClass();
		$clientname = $w->getRealClientParentO()->getPathFromName('nname');
		$ftpuser->realpass = $uuser->realpass;
		$w->addObject('ftpuser', $ftpuser);
		$ftpuser->password = crypt($uuser->realpass, '$1$'.randomString(8).'$');
		$w->username = $w->getRealClientParentO()->username;
		$w->setUpdateSubaction('full_update');

		$cpath = "{$sgbl->__path_customer_root}/{$clientname}";

		lxfile_mkdir("{$cpath}/domain");
		lxfile_unix_chown($cpath, "{$w->username}:apache");
		lxfile_unix_chmod($cpath, "750");

		print("moving {$w->nname} to {$cpath}/domain\n");
		$ret = lxshell_return("mv", "-f", $hpath, "{$cpath}/{$w->nname}");
		
		if ($ret) {
			print("Couldnt move {$w->nname} to {$cpath}\n");
			//continue;
		}

		lxshell_return("ln", "-sf", "{$cpath}/domain/{$w->nname}", $hpath);
		lxfile_unix_chown_rec("{$cpath}/domain/{$w->nname}", "{$w->username}:{$w->username}");

		$dirp = $w->getList('dirprotect');
		
		foreach($dirp as $d) {
			$d->setUpdateSubaction('full_update');
			$d->was();
		}
		
		$w->was();
	}
}
 
$sq = new Sqlite(null, 'client');
$sq->rawQuery("update client set username = 'admin' where nname = 'admin';");
