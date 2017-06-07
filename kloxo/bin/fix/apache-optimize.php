<?php

// by mustafa@bigraf.com for Kloxo-MR

include_once "lib/html/include.php";

// initProgram('admin');

$list = parse_opt($argv);

$select = (isset($list['select'])) ? $list['select'] : 'medium';
$spare  = (isset($list['spare']))  ? (int)$list['spare'] : null;
$keepalive  = (isset($list['keepalive']))  ? strtolower($list['keepalive']) : 'off';
$nolog  = (isset($list['nolog']))  ? $list['nolog'] : null;

setApacheOptimize($select, $spare, $keepalive, $nolog);

/* ****** BEGIN - setApacheOptimize ***** */

function setApacheOptimize($select, $spare = null, $keepalive = null, $nolog = null)
{
	global $login;

	$input['select'] = $select;
	$input['spare'] = $spare;
	$input['keepalive'] = $keepalive;

	print("* Options:\n");
	print("  - Select: {$select}\n");
	if ($spare === null) {
		print("  - Spare: 25%\n");
	} else {
		print("  - Spare: {$spare}\n");
	}
	print("  - Keep Alive: {$keepalive}\n");

	$tplsource = getLinkCustomfile("/opt/configs/apache/tpl", "~lxcenter.conf.tpl");

	$tpltarget = "/etc/httpd/conf.d/~lxcenter.conf";

	$tpl = file_get_contents($tplsource);

	$tplparse = getParseInlinePhp($tpl, $input);

	file_put_contents($tpltarget, $tplparse);

	print("\n* Note: need running 'sh /script/restart-web'\n");
}

/* ****** END - setApacheOptimize ***** */
