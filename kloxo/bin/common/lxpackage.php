<?php 

lxpackage_main();

function lxpackage_main()
{
	global $argv, $sgbl;

	$list = lfile_get_unserialize("$sgbl->__path_package_root/pkglist.lst");

	$pkg = $list['pkg'];

//  $ver = $pkg[$argv[1]);
}
