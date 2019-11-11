<?php

chdir("../");

include_once "lib/html/displayinclude.php";


header_main();

function header_main()
{
	global $gbl, $sgbl, $login, $ghtml; 

	initProgram();
	init_language();

	print_meta_lan();

	print_header();
}

function print_one_link($name)
{
	global $gdata;

	$s = $gdata[$name];
	$desc = $s[0];
	$url = $s[1];
	$img = $s[2];
	$target = null;

	if (!csa($url, "javascript")) {
		$onclickstring = "onClick=\"top.mainframe.location='$url';\";";
	} else {
		$onclickstring = "onClick=\"$url\"";
	}
?>
	<td ><span title='<?= $desc ?>' <?= $onclickstring ?> OnMouseOver="style.cursor='pointer'"><img src="/theme/skin/feather/default/images/<?= $img ?>"></span> </td>
<?php
}

function print_logout()
{
?>
<td OnMouseOver="style.cursor='pointer'" onClick="javascript:top.mainframe.logOut();"> <span title=Logout> <img width=15 height=14 src="/theme/skin/feather/default/images/logout.png"> Logout </span> </td>
<?php
}

function print_header()
{
	global $gbl, $sgbl, $login, $ghtml; 
	$lightskincolor = $login->getLightSkinColor();
	createHeaderData();
?>
<body topmargin="0" leftmargin="0" bottommargin="0" rightmargin="0">
<!-- <div id=statusbar style='background: #<?= $lightskincolor ?>; scroll: auto; height: 26px; width:100%; border-bottom:4px solid #b1cfed; margin: 2px; vertical-align: top; text-align: top'> -->
<div id=statusbar style='background: #<?= $lightskincolor ?>; scroll: auto; height: 26px; width:100%; border-bottom:4px solid #ddddff; vertical-align: top; text-align: top'>
<?php
$alist[] = "a=show";
$alist = $login->createShowAlist($alist);
/*
if ($login->isLte('reseller')) {
	$alist[] = "a=list&c=all_domain";
	$alist[] = "a=list&c=client";
}
$alist[] = "a=show&k[class]=ffile&k[nname]=/";
$alist[] = "a=updateform&sa=password";
*/
$gbl->__c_object = $login;
?>
<table cellpadding="0" cellspacing="0"> <tr>
<?php
$count = 0;
$icount = 0;

foreach($alist as $k => $v) {
	if (csa($k, "__title")) { $count++ ; continue; }

//	if ($count >= 2) { break; }

	$icount++;

	if ($icount > 8) { continue; }

	$v = $ghtml->getFullUrl($v);
	$ghtml->print_div_button_on_header(null, true, $k, $v);
}
?>
<td nowrap style='width:40px'></td>
<?php
$v = "a=list&c=ndskshortcut";
$v = $ghtml->getFullUrl($v);

$ghtml->print_div_button_on_header(null, true, 0, $v);
$ghtml->print_toolbar();
?>
<td width="100%"> </td>
<?php
$v =  $ghtml->getFullUrl("a=list&c=ssessionlist");
$ghtml->print_div_button_on_header(null, true, $k, $v);
$v =  create_simpleObject(array('url' => "javascript:top.mainframe.logOut()", 'purl' => '&a=updateform&sa=logout', 'target' => null));
$ghtml->print_div_button_on_header(null, true, $k, $v);
?>
</tr> </table>
</div> </body>
<?php
return;
?> 
<body topmargin=0 bottommargin=0 leftmargin=0 rightmargin=0 class="bdy1" onload="foc()">
	<link href="/theme/css/header.css" rel="stylesheet" type="text/css" />
<table id="tab1" border="0" cellpadding="0" cellspacing="0">
<tr><td class="top2"><div class="menuover" style="margin-top:2px;margin-left:0%">

<?php 
	$list[] = "a=show";

	if ($login->isLte('reseller')) {
		$list[] = "a=list&c=all_domain";
		$list[] = "a=list&c=client";
	} 

	$list[] = "k[class]=ffile&k[nname]=/&a=show";
	$list[] = "a=list&c=ticket";

	$list = null;
	$list[] = "home";
	$list[] = "ffile";
	$list[] = "ticket";

	foreach($list as $k) {
		print_one_link($k);
	}
?>
	<span style='margin-left:39%;'> </span>
<?php
	foreach(array("ssession", "help", "logout") as $k) {
		print_one_link($k);
	}
?>
	</div></td></tr>
	</table>
<?php
}

function createHeaderData()
{
	global $gbl, $sgbl, $login, $ghtml; 
	global $gdata;

	$homedesc = $login->getKeywordUc('home');
	$deskdesc = $login->getKeywordUc('desktop');
	$aboutdesc = $login->getKeywordUc('about');

	$domaindesc = get_plural(get_description('domain'));
	$clientdesc = get_plural(get_description('client'));
	$slavedesc = get_description('pserver');
	$ticketdesc = get_plural(get_description('ticket'));
	$ssessiondesc = get_description('ssession');
	$systemdesc = $login->getKeywordUc('system');
	$logoutdesc = $login->getKeywordUc('logout');
	$helpdesc = $login->getKeywordUc('help');
	$ffiledesc = get_plural(get_description("ffile"));
	$alldesc = $login->getKeywordUc('all');

	if ($login->isAdmin()) {
		$doctype = "admin";
		$domainclass = "domain";
	} else  {
		$doctype = "client";
		$domainclass = "domain";
	}

	if (check_if_many_server()) {
		$serverurl = $ghtml->getFullUrl('a=list&c=pserver');
		$slavedesc = get_plural($slavedesc);
	} else {
		$serverurl = $ghtml->getFullUrl('k[class]=pserver&k[nname]=localhost&a=show');
	}

//	if ($login->is__table('client')) {
	if ($login->getClass() === 'client') {
		$ffileurl = $ghtml->getFullUrl('k[class]=ffile&k[nname]=/&a=show');
	} else {
		$ffileurl = $ghtml->getFullUrl('n=web&k[class]=ffile&k[nname]=/&a=show');
	}
	$gob = $login->getObject('general')->generalmisc_b;
	if (isset($gob->ticket_url) && $gob->ticket_url) {
		$url = $gob->ticket_url;
		$url = add_http_if_not_exist($url);
		$ticket_url = "javascript:window.open('$url')";
	} else {
		$ticket_url = "/display.php?frm_action=list&frm_o_cname=ticket";
	}

//	$helpurl = "http://wiki.lxcenter.org";
	$helpurl = "https://www.facebook.com/groups/KloxoNextGeneration";

	$gdata = array(
		"desktop" => array($deskdesc, "/display.php?frm_action=desktop", "client_list.gif"),
		"home" => array($homedesc, "/display.php?frm_action=show", "home.png"),
		"all" => array($alldesc, "/display.php?frm_action=list&frm_o_cname=all_domain", "file.png"),
		"domain" => array($domaindesc, "/display.php?frm_action=list&frm_o_cname=$domainclass", "domain_list.gif"),
		"system" => array($systemdesc, "/display.php?frm_action=show&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=localhost", "pserver_list.gif"),
		"client" => array($clientdesc, "/display.php?frm_action=list&frm_o_cname=client", "file.png"),
		"ffile" => array($ffiledesc, $ffileurl, "file.png"),
		"pserver" => array($slavedesc, $serverurl, "pserver_list.gif"),
		"ticket" => array($ticketdesc, $ticket_url, "ticket.png"),
		"ssession" => array($ssessiondesc, "/display.php?frm_action=list&frm_o_cname=ssessionlist", "session.png"),
		"about" => array($aboutdesc, "/display.php?frm_action=about", "ssession_list.gif"),
		"help" => array($helpdesc, "javascript:window.open('$helpurl/')", "help.png"),
		"logout" => array("$logoutdesc", "javascript:top.mainframe.logOut();", "logout.png")
	);
}


