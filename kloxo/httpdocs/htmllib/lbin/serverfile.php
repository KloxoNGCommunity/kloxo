<?php 

chdir("../../");
include_once "lib/html/include.php";

serverfile_main();

function serverfile_main()
{
	global $gbl, $sgbl, $login, $ghtml; 

	$info = unserialize(base64_decode($ghtml->frm_info));
	//do_serve_file(null, $info);
	exit;


}

