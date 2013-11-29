<?php

if (strpos("/display.php", $_SERVER["SCRIPT_NAME"]) === false) {
	print("No permit access directly");
	return;
}

$syncserver = $login->syncserver;

$loginas = $login->nname;
$clientid = $loginas;

$consumedlogin = "";

if (isset($_GET['frm_consumedlogin'])) {
	if ($_GET['frm_consumedlogin'] === 'true') {
		$consumedlogin = "frm_consumedlogin=true&";
	}
}

if (isset($_GET['frm_o_o'][0]['class']) && ($_GET['frm_o_o'][0]['class'] === 'client')) {
	$clientid = $_GET['frm_o_o'][0]['nname'];

	$clientquery = "frm_o_o[0][class]=client&frm_o_o[0][nname]={$clientid}&";
} else {
	$clientquery = "";
}

if (isset($_GET['frm_o_o'][1]['class']) && ($_GET['frm_o_o'][1]['class'] === 'client')) {
	$clientid2 = $_GET['frm_o_o'][1]['nname'];

	$clientquery2 = "frm_o_o[0][class]=client&frm_o_o[1][nname]={$clientid}&";
} else {
	$clientquery2 = "";
}

$serverquery = "frm_o_o[0][class]=pserver&frm_o_o[0][nname]={$syncserver}&";
$localhostquery = "frm_o_o[0][class]=pserver&frm_o_o[0][nname]=localhost&";

if (($clientquery !== '') || ($syncserver !== 'localhost')) {
	$genwidth = "640";
} else {
	$genwidth = "480";
}

?>

<link rel="stylesheet" type="text/css" href="/theme/skin/simplicity/default/menu/css/style.css"/>

<div style="float:left">
	<ul class="menuTemplate2 decor2_1">
		<li><a href="/display.php?<?= $consumedlogin ?>frm_action=show"><?= $login->getKeywordUc('home') ?></a>
<?php
	if ($clientid != 'admin') {
?>
			<div class="drop2 decor2_2" style="width:320px">
				<div class='left'>
<?php
		if ($clientquery !== "") {
			if ($clientquery2 === "") {
?>
					&#x00bb;&nbsp;<?= $login->getKeywordUc('home') ?>
						&#x00bb;&nbsp;<a href="/display.php?frm_action=show&<?= $clientquery ?>"><?= $clientid ?></a><br/>

<?php
			} else {
?>
					&#x00bb;&nbsp;<?= $login->getKeywordUc('home') ?>
						&#x00bb;&nbsp;<a href="/display.php?frm_action=show&<?= $clientquery ?>"><?= $clientid ?></a>
						&#x00bb;&nbsp;<a href="/display.php?frm_action=show&<?= $clientquery ?><?= $clientquery2 ?>"><?= $clientid2 ?></a><br/>

<?php
			}
		}
?>
<?php
		if ($consumedlogin === '') {
		// MR -- don't include $consumedlogin here!
?>
					&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=update&sa=dologin&o=client") ?>
						&#x00bb;&nbsp;<a href="/display.php?frm_action=show&<?= $clientquery ?>"><?= $clientid ?> (<?= $login->getKeywordUc('cancel') ?>)</a><br/>
<?php
		} else {
?>
					&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=update&sa=dologin&o=client") ?>
						&#x00bb;&nbsp;<a href="/display.php?frm_action=update&frm_subaction=dologin&<?= $clientquery ?>"><?= $clientid ?></a><br/>

<?php
		}
?>
				</div>
			</div>
<?php
	}
?>
		</li>
		<li class="separator"></li>
		<li><a href="#" class="arrow"><?= $login->getKeywordUc('administration') ?></a>
			<div class="drop decor2_2" style="width:<?= $genwidth ?>px">
<?php
	if ($login->isCustomer()) {
?>
				<div class='left'>
<?php
	} else {
?>
				<div class='left rightborder'>
					<b><?= $login->getKeywordUc('alllist') ?></b>
					<div class="dropmenu">
<?php
	if ($clientquery === "") {
?>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&frm_o_cname=all_domain"><?= $ghtml->getTitleOnly("a=list&c=all_domain") ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&frm_o_cname=all_addondomain"><?= $ghtml->getTitleOnly("a=list&c=all_addondomain") ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&frm_o_cname=all_mailaccount"><?= $ghtml->getTitleOnly("a=list&c=all_mailaccount") ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&frm_o_cname=all_mailforward"><?= $ghtml->getTitleOnly("a=list&c=all_mailforward") ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&frm_o_cname=all_mysqldb"><?= $ghtml->getTitleOnly("a=list&c=all_mysqldb") ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&frm_o_cname=all_cron"><?= $ghtml->getTitleOnly("a=list&c=all_cron") ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&frm_o_cname=all_ftpuser"><?= $ghtml->getTitleOnly("a=list&c=all_ftpuser") ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&frm_o_cname=all_maillinglist"><?= $ghtml->getTitleOnly("a=list&c=all_mailinglist") ?></a><br/>
<?php
	} else {
?>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=list&c=all_domain") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&frm_o_cname=all_domain"><?= $loginas ?></a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $clientquery ?>frm_o_cname=all_domain"><?= $clientid ?></a><br/>

						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=list&c=all_addondomain") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&frm_o_cname=all_addondomain"><?= $loginas ?></a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $clientquery ?>frm_o_cname=all_addondomain"><?= $clientid ?></a><br/>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=list&c=all_mailaccount") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&frm_o_cname=all_mailaccount"><?= $loginas ?></a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $clientquery ?>frm_o_cname=all_mailaccount"><?= $clientid ?></a><br/>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=list&c=all_mailforward") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&frm_o_cname=all_mailforward"><?= $loginas ?></a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $clientquery ?>frm_o_cname=all_mailforward"><?= $clientid ?></a><br/>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=list&c=all_mysqldb") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&frm_o_cname=all_mysqldb"><?= $loginas ?></a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $clientquery ?>frm_o_cname=all_mysqldb"><?= $clientid ?></a><br/>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=list&c=all_cron") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&frm_o_cname=all_cron"><?= $loginas ?></a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $clientquery ?>frm_o_cname=all_cron"><?= $clientid ?></a><br/>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=list&c=all_ftpuser") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&frm_o_cname=all_ftpuser"><?= $loginas ?></a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $clientquery ?>frm_o_cname=all_ftpuser"><?= $clientid ?></a><br/>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=list&c=all_mailinglist") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&frm_o_cname=all_maillinglist"><?= $loginas ?></a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $clientquery ?>frm_o_cname=all_maillinglist"><?= $clientid ?></a><br/>

<?php
	}
?>					</div>
					<b><?= $login->getKeywordUc('resourceplan') ?></b>
					<div class="dropmenu">
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&frm_o_cname=resourceplan"><?= $ghtml->getTitleOnly("a=list&c=resourceplan") ?></a><br/>
<?php
		if ($clientquery !== '') {
?>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=update&sa=change_plan") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=change_plan&<?= $clientquery ?>"><?= $clientid ?></a><br/>
<?php
		}
?>
					</div>
<?php
		if (!$login->isAdmin()) {
?>
				</div>
				<div class='left'>
<?php
		}
?>
					<b><?= $login->getKeywordUc('support') ?></b>
					<div class="dropmenu">
<?php
		if ($clientquery === "") {
?>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&frm_o_cname=ticket"><?= $ghtml->getTitleOnly("a=list&c=ticket") ?></a><br/>
<?php
		} else {
?>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=list&c=ticket") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&frm_o_cname=ticket"><?= $loginas ?></a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $clientquery ?>frm_o_cname=ticket"><?= $clientid ?></a><br/>
<?php
		}

		if ($login->isAdmin()) {
?>
						&#x00bb;&nbsp;<a href="/display.php?frm_action=updateform&frm_subaction=ticketconfig&frm_o_o[0][class]=ticketconfig"><?= $ghtml->getTitleOnly("a=updateform&sa=ticketconfig&o=ticketconfig") ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?frm_action=list&frm_o_o[0][class]=general&frm_o_cname=helpdeskcategory_a"><?= $ghtml->getTitleOnly("a=list&o=general&c=helpdeskcategory_a") ?></a><br/>
<?php
		}
?>
					</div>
<?php
	}
?>
					<b><?= $login->getKeywordUc('message') ?></b>
					<div class="dropmenu">
<?php
		if ($clientquery === '') {
?>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&frm_o_cname=smessage"><?= $ghtml->getTitleOnly("a=list&c=smessage") ?></a><br/>
<?php
		} else {
?>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=list&c=smessage") ?> &#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&frm_o_cname=smessage"><?= $loginas ?></a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $clientquery ?>frm_o_cname=smessage"><?= $clientid ?></a><br/>
<?php
		}
?>
					</div>
<?php

	if ($login->isAdmin()) {
?>
				</div>
				<div class='left'>
<?php
	}
?>

<?php
	if ($login->isAdmin()) {
?>
					<b><?= $login->getKeywordUc('custombutton') ?></b>
					<div class="dropmenu">
						&#x00bb;&nbsp;<a href="/display.php?frm_action=list&frm_o_cname=custombutton"><?= $ghtml->getTitleOnly("a=list&c=custombutton") ?></a><br/>
					</div>
					<b><?= $login->getKeywordUc('reversedns') ?></b>
					<div class="dropmenu">
						&#x00bb;&nbsp;<a href="/display.php?frm_action=list&frm_o_cname=reversedns"><?= $ghtml->getTitleOnly("a=list&c=reversedns") ?></a><br/>
					</div>
<?php
	}
?>
<?php
	if ($login->isAdmin()) {
?>
					<b><?= $login->getKeywordUc('update') ?></b>
					<div class="dropmenu">
						&#x00bb;&nbsp;<a href="/display.php?frm_action=show&frm_o_o[0][class]=lxupdate"><?= $ghtml->getTitleOnly("a=show&o=lxupdate") ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?frm_action=list&frm_o_o[0][class]=lxupdate&frm_o_cname=releasenote"><?= $ghtml->getTitleOnly("a=list&o=lxupdate&c=releasenote") ?></a><br/>
					</div>
<?php
	}

?>
					<b><?= $login->getKeywordUc('other') ?></b>
					<div class="dropmenu">
<?php
	if ($clientquery === '') {
?>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=information"><?= $ghtml->getTitleOnly("a=updateform&sa=information&o=client") ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&frm_o_cname=actionlog"><?= $ghtml->getTitleOnly("a=list&c=actionlog") ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=password"><?= $ghtml->getTitleOnly("a=updateform&sa=password&o=client") ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=skin&frm_o_o[0][class]=sp_specialplay"><?= $ghtml->getTitleOnly("a=updateform&sa=skin&o=sp_specialplay") ?></a><br/>
<?php
	} else {
?>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=updateform&sa=information&o=client") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=information"><?= $loginas ?></a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=information&<?= $clientquery ?>"><?= $clientid ?></a><br/>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=list&c=actionlog") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&frm_o_cname=actionlog"><?= $loginas ?></a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $clientquery ?>frm_o_cname=actionlog"><?= $clientid ?></a><br/>

						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=updateform&sa=password&o=client") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=password"><?= $loginas ?></a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=password&<?= $clientquery ?>"><?= $clientid ?></a><br/>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=updateform&sa=skin&o=sp_specialplay") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_o_o[0][class]=sp_specialplay"><?= $loginas ?></a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&<?= $clientquery ?>frm_o_o[1][class]=sp_specialplay"><?= $clientid ?></a><br/>

<?php
	}
?>
					</div>
				</div>
				<div style='clear: both;'></div>
			</div>
		</li>
		<li class="separator"></li>
		<li><a href="#" class="arrow"><?= $login->getKeywordUc('resource') ?></a>
			<div class="drop decor2_2" style="width:<?= $genwidth ?>px">
				<div class='left rightborder'>
					<b><?= $login->getKeywordUc('auxiliary') ?></b>
					<div class="dropmenu">
<?php
	if ($clientquery === "") {
?>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $clientquery ?>frm_o_cname=auxiliary"><?= $ghtml->getTitleOnly("a=list&c=auxiliary") ?></a><br/>
<?php
	} else {
?>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=list&c=auxiliary") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&frm_o_cname=auxiliary"><?= $loginas ?></a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $clientquery ?>frm_o_cname=auxiliary"><?= $clientid ?></a><br/>
<?php
	}
?>
<?php
	if ($login->isAdmin()) {
?>
<?php
		if ($clientquery === "") {
?>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $clientquery ?>frm_o_cname=all_auxiliary"><?= $ghtml->getTitleOnly("a=list&c=all_auxiliary") ?></a><br/>
<?php
		} else {
?>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=list&c=all_auxiliary") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&frm_o_cname=all_auxiliary"><?= $loginas ?></a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $clientquery ?>frm_o_cname=all_auxiliary"><?= $clientid ?></a><br/>
<?php
		}
?><?php
	}
?>
					</div>
<?php
	if ($login->isAdmin()) {
?>
					<b><?= $login->getKeywordUc('ssl') ?></b>
					<div class="dropmenu">
						&#x00bb;&nbsp;<a href="/display.php?frm_action=list&frm_o_cname=sslcert"><?= $ghtml->getTitleOnly("a=list&c=sslcert") ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?frm_action=addform&frm_o_cname=sslcert"><?= $ghtml->getTitleOnly("a=addform&c=sslcert") ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?frm_action=addform&frm_dttype[var]=upload&frm_dttype[val]=uploadfile&frm_o_cname=sslcert"><?= $ghtml->getTitleOnly("a=addform&dta[var]=upload&dta[val]=uploadfile&c=sslcert") ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?frm_action=addform&frm_dttype[var]=upload&frm_dttype[val]=uploadtxt&frm_o_cname=sslcert"><?= $ghtml->getTitleOnly("a=addform&c=sslcerta=addform&dta[var]=upload&dta[val]=uploadtxt&c=sslcert") ?></a><br/>
					</div>
<?php
	}
?>
					<b><?= $ghtml->getTitleOnly("a=list&c=ipaddress") ?></b>
					<div class="dropmenu">
<?php
	if ($clientquery === "") {
?>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $clientquery ?>frm_o_cname=ipaddress"><?= $ghtml->getTitleOnly("a=list&c=ipaddress") ?></a><br/>
<?php
	} else {
?>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=list&c=ipaddress") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&frm_o_cname=ipaddress"><?= $loginas ?></a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $clientquery ?>frm_o_cname=ipaddress"><?= $clientid ?></a><br/>
<?php
	}

	if ($login->isAdmin()) {
?>
<?php
		if ((check_if_many_server()) && ($syncserver !== 'localhost')) {	
?>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=list&c=ipaddress") ?>
							&#x00bb;&nbsp;<a href="/display.php?frm_action=list&<?= $localhostquery ?>frm_o_cname=service"> &#x00bb; localhost</a>
							&#x00bb;&nbsp;<a href="/display.php?frm_action=list&<?= $serverquery ?>frm_o_cname=service"><?= $syncserver ?></a><br/>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=addform&c=ipaddress") ?>
							&#x00bb;&nbsp;<a href="/display.php?frm_action=addform&<?= $localhostquery ?>frm_o_cname=ipaddress"> &#x00bb; localhost</a>
							&#x00bb;&nbsp;<a href="/display.php?frm_action=addform&<?= $serverquery ?>frm_o_cname=ipaddress"><?= $syncserver ?></a><br/>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=update&sa=readipaddress&o=pserver") ?>
							&#x00bb;&nbsp;<a href="/display.php?frm_action=update&frm_subaction=readipaddress&<?= $localhostquery ?>"> &#x00bb; localhost</a>
							&#x00bb;&nbsp;<a href="/display.php?frm_action=update&frm_subaction=readipaddress&<?= $serverquery ?>"><?= $syncserver ?></a><br/>

<?php
		} else {
?>
						&#x00bb;&nbsp;<a href="/display.php?frm_action=list&<?= $serverquery ?>frm_o_cname=service"><?= $ghtml->getTitleOnly("a=list&c=ipaddress") ?> &#x00bb;&nbsp;<?= $syncserver ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?frm_action=addform&<?= $serverquery ?>frm_o_cname=ipaddress"><?= $ghtml->getTitleOnly("a=addform&c=ipaddress") ?> &#x00bb;&nbsp;<?= $syncserver ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?frm_action=update&frm_subaction=readipaddress&<?= $serverquery ?>"><?= $ghtml->getTitleOnly("a=update&sa=readipaddress&o=pserver") ?> &#x00bb;&nbsp;<?= $syncserver ?></a><br/>
<?php
		}
	}
?>
					</div>
<?php
	if ($login->isAdmin()) {
?>
				</div>
				<div class='left'>
					<b><?= $login->getKeywordUc('dnstemplate') ?></b>
					<div class="dropmenu">
						&#x00bb;&nbsp;<a href="/display.php?frm_action=list&frm_o_cname=dnstemplate"><?= $ghtml->getTitleOnly("a=list&c=dnstemplate") ?></a><br/>

<?php
		if ($clientquery !== "") {
?>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=updateform&sa=dnstemplatelist") ?>
							&#x00bb;&nbsp;<a href="/display.php?frm_action=updateform&frm_subaction=dnstemplatelist&<?= $clientquery ?>"><?= $clientid ?></a><br/>
<?php
		}
	}
?>
					</div>
					<b><?= $login->getKeywordUc('backuprestore') ?></b>
					<div class="dropmenu">
<?php
	if ($clientquery === "") {
?>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=show&frm_o_o[0][class]=lxbackup"><?= $ghtml->getTitleOnly("a=show&o=lxbackup") ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=ftp_conf&frm_o_o[0][class]=lxbackup"><?= $ghtml->getTitleOnly("a=updateform&sa=ftp_conf&o=lxbackup") ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=schedule_conf&frm_o_o[0][class]=lxbackup"><?= $ghtml->getTitleOnly("a=updateform&sa=schedule_conf&o=lxbackup") ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=show&frm_o_o[0][class]=lxbackup&frm_o_o[1][class]=ffile&frm_o_o[1][nname]=/"><?= $ghtml->getTitleOnly("a=show&o=lxbackup&l[class]=ffile&l[nname]=/") ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=upload&frm_o_o[0][class]=lxbackup&frm_o_o[1][class]=ffile&frm_o_o[1][nname]=/"><?= $ghtml->getTitleOnly("a=updateform&sa=upload&o=lxbackup&l[class]=ffile&l[nname]=/") ?></a><br>

<?php
	} else {
?>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=show&o=lxbackup") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=show&frm_o_o[0][class]=lxbackup"><?= $loginas ?></a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=show&<?= $clientquery ?>frm_o_o[1][class]=lxbackup"><?= $clientid ?></a><br/>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=updateform&sa=ftp_conf&o=lxbackup") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=ftp_conf&frm_o_o[0][class]=lxbackup"><?= $loginas ?></a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=ftp_conf&<?= $clientquery ?>frm_o_o[1][class]=lxbackup"><?= $clientid ?></a><br/>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=updateform&sa=schedule_conf&o=lxbackup") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=schedule_conf&frm_o_o[0][class]=lxbackup"><?= $loginas ?></a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=schedule_conf&<?= $clientquery ?>frm_o_o[1][class]=lxbackup"><?= $clientid ?></a><br/>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=show&o=lxbackup&l[class]=ffile&l[nname]=/") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=show&frm_o_o[0][class]=lxbackup&frm_o_o[1][class]=ffile&frm_o_o[1][nname]=/"><?= $loginas ?></a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=show&<?= $clientquery ?>frm_o_o[1][class]=lxbackup&frm_o_o[2][class]=ffile&frm_o_o[2][nname]=/"><?= $clientid ?></a><br/>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=updateform&sa=upload&o=lxbackup&l[class]=ffile&l[nname]=/") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=upload&frm_o_o[0][class]=lxbackup&frm_o_o[1][class]=ffile&frm_o_o[1][nname]=/"><?= $loginas ?></a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=upload&<?= $clientquery ?>frm_o_o[1][class]=lxbackup&frm_o_o[2][class]=ffile&frm_o_o[2][nname]=/"><?= $clientid ?></a><br/>

<?php
	}
?>
					</div>
<?php
	if (!$login->isAdmin()) {
?>
				</div>
				<div class='left'>
<?php
	}
?>
					<b><?= $login->getKeywordUc('other') ?></b>
					<div class="dropmenu">
<?php
	if ($clientquery === "") {
?>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=update&frm_o_o[0][class]=domaindefault"><?= $ghtml->getTitleOnly("a=updateform&sa=update&o=domaindefault") ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $serverquery ?>frm_o_cname=ftpsession"><?= $ghtml->getTitleOnly("a=list&c=ftpsession") ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=shell_access"><?= $ghtml->getTitleOnly("a=updateform&sa=shell_access&o=client") ?></a><br/>
<?php
	} else {
?>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=updateform&sa=update&o=domaindefault") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=update&frm_o_o[0][class]=domaindefault"><?= $loginas ?></a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=update&<?= $clientquery ?>frm_o_o[1][class]=domaindefault"><?= $clientid ?></a><br/>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=list&c=ftpsession") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $serverquery ?>frm_o_cname=ftpsession"><?= $loginas ?></a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $clientquery ?>frm_o_cname=ftpsession"><?= $clientid ?></a><br/>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=updateform&sa=shell_access&o=client") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=shell_access"><?= $loginas ?></a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=shell_access&<?= $clientquery ?>"><?= $clientid ?></a><br/>

<?php
	}
?>
					</div>
				</div>
				<div style='clear: both;'></div>
			</div>
		</li>
		<li class="separator"></li>
		<li><a href="#"><?= $login->getKeywordUc('advanced') ?></a>
			<div class="drop decor2_2" style="width:<?= $genwidth ?>px">
				<div class='left rightborder'>
					<b><?= $login->getKeywordUc('general') ?></b>
					<div class="dropmenu">
<?php
	if ($login->isAdmin()) {
?>
						&#x00bb;&nbsp;<a href="/display.php?frm_action=updateform&frm_subaction=scavengetime&frm_o_o[0][class]=general"><?= $ghtml->getTitleOnly("a=updateform&sa=scavengetime&o=general") ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?frm_action=updateform&frm_subaction=generalsetting&frm_o_o[0][class]=general"><?= $ghtml->getTitleOnly("a=updateform&sa=generalsetting&o=general") ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?frm_action=updateform&frm_subaction=selfbackupconfig&frm_o_o[0][class]=general"><?= $ghtml->getTitleOnly("a=updateform&sa=selfbackupconfig&o=general") ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?frm_action=list&frm_o_o[0][class]=genlist&frm_o_cname=dirindexlist_a"><?= $ghtml->getTitleOnly("a=list&o=genlist&c=dirindexlist_a") ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?frm_action=updateform&frm_subaction=portconfig&frm_o_o[0][class]=general"><?= $ghtml->getTitleOnly("a=updateform&sa=portconfig&o=general") ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?frm_action=updateform&frm_subaction=disable_skeleton"><?= $ghtml->getTitleOnly("a=updateform&sa=disable_skeleton&c=client") ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?frm_action=updateform&frm_subaction=login_options&frm_o_o[0][class]=sp_specialplay"><?= $ghtml->getTitleOnly("a=updateform&sa=upload_logo&o=sp_specialplay") ?></a><br/>
<?php
	}
?>
<?php
	if ($clientquery === "") {
?>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=show&frm_o_o[0][class]=notification"><?= $ghtml->getTitleOnly("a=show&o=notification") ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=login_options&frm_o_o[0][class]=sp_specialplay"><?= $ghtml->getTitleOnly("a=updateform&sa=login_options&o=sp_specialplay") ?></a><br/>

<?php
	} else {
?>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=show&o=notification") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=show&frm_o_o[0][class]=notification"><?= $loginas ?></a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=show&<?= $clientquery ?>frm_o_o[1][class]=notification"><?= $clientid ?></a><br/>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=updateform&sa=login_options&o=sp_specialplay") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_o_o[0][class]=sp_specialplay"><?= $loginas ?></a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&<?= $clientquery ?>frm_o_o[1][class]=sp_specialplay"><?= $clientid ?></a><br/>
<?php
	}
?>
					</div>
<?php
	if (!$login->isCustomer()) {
?>
				</div>
				<div class='left'>
<?php
	}
?>
					<b><?= $login->getKeywordUc('blockedallowedips') ?></b>
					<div class="dropmenu">
<?php
	if ($clientquery === "") {
?>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&frm_o_cname=allowedip"><?= $ghtml->getTitleOnly("a=list&c=allowedip") ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&frm_o_cname=blockedip"><?= $ghtml->getTitleOnly("a=list&c=blockedip") ?></a><br/>
<?php
	} else {
?>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=list&c=allowedip") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&frm_o_cname=allowedip"><?= $loginas ?></a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $clientid ?>frm_o_cname=allowedip"><?= $clientid ?></a><br/>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=list&c=blockedip") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&frm_o_cname=blockedip"><?= $loginas ?></a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $clientid ?>frm_o_cname=blockedip"><?= $clientid ?></a><br/>

<?php
	}
?>					</div>
<?php
	if ($login->isAdmin()) {
?>
					<b><?= $login->getKeywordUc('maintenance') ?></b>
					<div class="dropmenu">
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=maintenance&frm_o_o[0][class]=general"><?= $ghtml->getTitleOnly("a=updateform&sa=maintenance&o=general") ?></a><br/>

					</div>
<?php
	}

	if ($login->isCustomer()) {
?>
				</div>
				<div class='left'>
<?php
	}
?>
					<b><?= $login->getKeywordUc('other') ?></b>
					<div class="dropmenu">
<?php
	if ($clientquery === "") {
?>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=miscinfo"><?= $ghtml->getTitleOnly("a=updateform&sa=miscinfo&o=pserver") ?></a><br/>
<?php
	} else {
?>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=updateform&sa=miscinfo&o=pserver") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=miscinfo"><?= $loginas ?></a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&<?= $clientquery ?>frm_subaction=miscinfo"><?= $clientid ?></a><br/>
<?php
	}

	if ($login->isAdmin()) {
?>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=skin&frm_o_o[0][class]=sp_childspecialplay"><?= $ghtml->getTitleOnly("a=updateform&sa=skin&o=sp_childspecialplay") ?></a><br/>
<?php
	}
?>
					</div>
				</div>
				<div style='clear: both;'></div>
			</div>
		</li>
<?php
	if ($login->isAdmin()) {
?>
		<li class="separator"></li>
		<li><a href="#"><?= $login->getKeywordUc('server') ?></a>
			<div class="drop decor2_2" style="width:<?= $genwidth ?>px">
				<div class='left rightborder'>
					<b><?= $login->getKeywordUc('server') ?></b>
					<div class="dropmenu">
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&frm_o_cname=pserver"><?= $ghtml->getTitleOnly("a=list&c=pserver") ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=forcedeletepserver&frm_o_cname=client"><?= $ghtml->getTitleOnly("a=updateform&sa=forcedeletepserver&c=client") ?></a><br/>
<?php
		if (check_if_many_server()) {
?>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&frm_o_cname=psrole_a"><?= $ghtml->getTitleOnly("a=list&c=psrole_a") ?></a><br/>
<?php
		}
?>
<?php
		if ((check_if_many_server()) && ($syncserver !== 'localhost')) {	
?>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=updateform&sa=reboot&l[class]=pserver&l[nname]={$syncserver}") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=reboot&<?= $localhostquery ?>">&#x00bb; localhost</a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=reboot&<?= $serverquery ?>">&#x00bb;&nbsp;<?= $syncserver ?></a><br/>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=updateform&sa=reboot&l[class]=pserver&l[nname]={$syncserver}") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=poweroff&<?= $localhostquery ?>">&#x00bb; localhost</a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=poweroff&<?= $serverquery ?>">&#x00bb;&nbsp;<?= $syncserver ?></a><br/>
<?php
		} else {
?>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=reboot&<?= $serverquery ?>"><?= $ghtml->getTitleOnly("a=updateform&sa=reboot&l[class]=pserver&l[nname]={$syncserver}") ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=poweroff&<?= $serverquery ?>"><?= $ghtml->getTitleOnly("a=updateform&sa=poweroff&l[class]=pserver&l[nname]={$syncserver}") ?></a><br/>
<?php
		}
?>
					</div>
					<b><?= $login->getKeywordUc('show') ?></b>
					<div class="dropmenu">
<?php
		if ((check_if_many_server()) && ($syncserver !== 'localhost')) {	
?>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=list&c=service") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $localhostquery ?>frm_o_cname=service">&#x00bb; localhost</a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $serverquery ?>frm_o_cname=service">&#x00bb;&nbsp;<?= $syncserver ?></a><br/>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=list&c=process") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $localhostquery ?>frm_o_cname=process">&#x00bb; localhost</a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $serverquery ?>frm_o_cname=process">&#x00bb;&nbsp;<?= $syncserver ?></a><br/>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=list&c=component") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $localhostquery ?>frm_o_cname=component">&#x00bb; localhost</a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $serverquery ?>frm_o_cname=component">&#x00bb;&nbsp;<?= $syncserver ?></a><br/>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=show&o=llog") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=show&<?= $localhostquery ?>frm_o_o[1][class]=llog">&#x00bb; localhost</a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=show&<?= $serverquery ?>frm_o_o[1][class]=llog">&#x00bb;&nbsp;<?= $syncserver ?></a><br/>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=updateform&sa=update&o=driver") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=update&<?= $localhostquery ?>frm_o_o[1][class]=driver">&#x00bb; localhost</a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=update&<?= $serverquery ?>frm_o_o[1][class]=driver">&#x00bb;&nbsp;<?= $syncserver ?></a><br/>


<?php
		} else {
?>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $serverquery ?>frm_o_cname=service"><?= $ghtml->getTitleOnly("a=list&c=service") ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $serverquery ?>frm_o_cname=process"><?= $ghtml->getTitleOnly("a=list&c=process") ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $serverquery ?>frm_o_cname=component"><?= $ghtml->getTitleOnly("a=list&c=component") ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=show&<?= $serverquery ?>frm_o_o[1][class]=llog"><?= $ghtml->getTitleOnly("a=show&o=llog") ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=update&<?= $serverquery ?>frm_o_o[1][class]=driver"><?= $ghtml->getTitleOnly("a=updateform&sa=update&o=driver") ?></a><br/>
<?php
		}
?>
					</div>
				</div>
				<div class='left'>
					<b><?= $login->getKeywordUc('other') ?></b>
					<div class="dropmenu">
<?php
		if ((check_if_many_server()) && ($syncserver !== 'localhost')) {	
?>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=updateform&sa=switchprogram&l[class]=pserver&l[nname]={$syncserver}") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=switchprogram&<?= $localhostquery ?>">&#x00bb; localhost</a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=switchprogram&<?= $serverquery ?>">&#x00bb;&nbsp;<?= $syncserver ?></a><br/>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=updateform&sa=timezone&l[class]=pserver&l[nname]={$syncserver}") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=timezone&<?= $localhostquery ?>">&#x00bb; localhost</a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=timezone&<?= $serverquery ?>">&#x00bb;&nbsp;<?= $syncserver ?></a><br/>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=updateform&sa=commandcenter") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=commandcenter&<?= $localhostquery ?>">&#x00bb; localhost</a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=commandcenter&<?= $serverquery ?>">&#x00bb;&nbsp;<?= $syncserver ?></a><br/>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=show&o=sshclient") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=show&<?= $localhostquery ?>frm_o_o[1][class]=sshclient">&#x00bb; localhost</a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=show&<?= $serverquery ?>frm_o_o[1][class]=sshclient">&#x00bb;&nbsp;<?= $syncserver ?></a><br/>

<?php
		} else {
?>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=switchprogram&<?= $serverquery ?>"><?= $ghtml->getTitleOnly("a=updateform&sa=switchprogram&l[class]=pserver&l[nname]={$syncserver}") ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=timezone&<?= $serverquery ?>"><?= $ghtml->getTitleOnly("a=updateform&sa=timezone&l[class]=pserver&l[nname]={$syncserver}") ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=commandcenter&<?= $serverquery ?>"><?= $ghtml->getTitleOnly("a=updateform&sa=commandcenter") ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=show&<?= $serverquery ?>frm_o_o[1][class]=sshclient"><?= $ghtml->getTitleOnly("a=show&o=sshclient") ?></a><br/>
<?php
		}

		if ($clientquery === "") {
?>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&frm_o_cname=traceroute"><?= $ghtml->getTitleOnly("a=list&c=traceroute") ?></a><br/>
<?php
		} else {
?>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=list&c=traceroute") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&frm_o_cname=traceroute"><?= $loginas ?></a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $clientquery ?>frm_o_cname=traceroute"><?= $clientid ?></a><br/>
<?php
		}
?>					</div>
				</div>
				<div style='clear: both;'></div>
			</div>
		</li>
		<li class="separator"></li>
		<li><a href="#"><?= $login->getKeywordUc('security') ?></a>
			<div class="drop decor2_2" style="width:<?= $genwidth ?>px">
				<div class='left rightborder'>
					<b><?= $login->getKeywordUc('lxguard') ?></b>
					<div class="dropmenu">
<?php
		if ((check_if_many_server()) && ($syncserver !== 'localhost')) {	
?>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=show&o=lxguard") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=show&<?= $localhostquery ?>frm_o_o[1][class]=lxguard">&#x00bb; localhost</a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=show&<?= $serverquery ?>frm_o_o[1][class]=lxguard">&#x00bb;&nbsp;<?= $syncserver ?></a><br/>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=list&o=lxguard&c=lxguardhitdisplay") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $localhostquery ?>frm_o_o[1][class]=lxguard&frm_o_cname=lxguardhitdisplay">&#x00bb; localhost</a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $serverquery ?>frm_o_o[1][class]=lxguard&frm_o_cname=lxguardhitdisplay">&#x00bb;&nbsp;<?= $syncserver ?></a><br/>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=list&o=lxguard&c=rawlxguardhit") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $localhostquery ?>frm_o_o[1][class]=lxguard&frm_o_cname=rawlxguardhit">&#x00bb; localhost</a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $serverquery ?>frm_o_o[1][class]=lxguard&frm_o_cname=rawlxguardhit">&#x00bb;&nbsp;<?= $syncserver ?></a><br/>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=list&o=lxguard&c=lxguardwhitelist") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $localhostquery ?>frm_o_o[1][class]=lxguard&frm_o_cname=lxguardwhitelist">&#x00bb; localhost</a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $serverquery ?>frm_o_o[1][class]=lxguard&frm_o_cname=lxguardwhitelist">&#x00bb;&nbsp;<?= $syncserver ?></a><br/>

<?php
		} else {
?>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=show&<?= $serverquery ?>frm_o_o[1][class]=lxguard"><?= $ghtml->getTitleOnly("a=show&o=lxguard") ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $serverquery ?>frm_o_o[1][class]=lxguard&frm_o_cname=lxguardhitdisplay"><?= $ghtml->getTitleOnly("a=list&o=lxguard&c=lxguardhitdisplay") ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $serverquery ?>frm_o_o[1][class]=lxguard&frm_o_cname=rawlxguardhit"><?= $ghtml->getTitleOnly("a=list&o=lxguard&c=rawlxguardhit") ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $serverquery ?>frm_o_o[1][class]=lxguard&frm_o_cname=lxguardwhitelist"><?= $ghtml->getTitleOnly("a=list&o=lxguard&c=lxguardwhitelist") ?></a><br/>
<?php
		}
?>					</div>
				</div>
				<div class='left'>
					<b><?= $login->getKeywordUc('other') ?></b>
					<div class="dropmenu">
<?php
		if ((check_if_many_server()) && ($syncserver !== 'localhost')) {	
?>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=show&o=sshconfig") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=show&<?= $localhostquery ?>frm_o_o[1][class]=sshconfig">&#x00bb; localhost</a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=show&<?= $serverquery ?>frm_o_o[1][class]=sshconfig">&#x00bb;&nbsp;<?= $syncserver ?></a><br/>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=list&c=watchdog") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $localhostquery ?>frm_o_cname=watchdog">&#x00bb; localhost</a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $serverquery ?>frm_o_cname=watchdog">&#x00bb;&nbsp;<?= $syncserver ?></a><br/>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=list&c=hostdeny") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $localhostquery ?>frm_o_cname=hostdeny">&#x00bb; localhost</a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $serverquery ?>frm_o_cname=hostdeny">&#x00bb;&nbsp;<?= $syncserver ?></a><br/>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=list&c=sshauthorizedkey") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $localhostquery ?>frm_o_cname=sshauthorizedkey">&#x00bb; localhost</a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $serverquery ?>frm_o_cname=sshauthorizedkey">&#x00bb;&nbsp;<?= $syncserver ?></a><br/>

<?php
		} else {
?>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=show&<?= $serverquery ?>frm_o_o[1][class]=sshconfig"><?= $ghtml->getTitleOnly("a=show&o=sshconfig") ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $serverquery ?>frm_o_cname=watchdog"><?= $ghtml->getTitleOnly("a=list&c=watchdog") ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $serverquery ?>frm_o_cname=hostdeny"><?= $ghtml->getTitleOnly("a=list&c=hostdeny") ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $serverquery ?>frm_o_cname=sshauthorizedkey"><?= $ghtml->getTitleOnly("a=list&c=sshauthorizedkey") ?></a><br/>
<?php
		}
?>
					</div>
				</div>
				<div style='clear: both;'></div>
			</div>
		</li>
<?php
	}
?>
<?php
	if ($login->isAdmin()) {
?>
		<li class="separator"></li>
		<li><a href="#"><?= $login->getKeywordUc('webmailanddb') ?></a>
			<div class="drop decor2_2" style="width:<?= $genwidth ?>px">
				<div class='left rightborder'>
					<b><?= $login->getKeywordUc('configure') ?></b>
					<div class="dropmenu">
<?php
		if ((check_if_many_server()) && ($syncserver !== 'localhost')) {	
?>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=show&o=phpini") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=show&<?= $localhostquery ?>frm_o_o[1][class]=phpini">&#x00bb; localhost</a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=show&<?= $serverquery ?>frm_o_o[1][class]=phpini">&#x00bb;&nbsp;<?= $syncserver ?></a><br/>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=updateform&sa=extraedit&o=phpini") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=extraedit&<?= $localhostquery ?>frm_o_o[1][class]=phpini">&#x00bb; localhost</a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=extraedit&<?= $serverquery ?>frm_o_o[1][class]=phpini">&#x00bb;&nbsp;<?= $syncserver ?></a><br/>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=show&o=serverweb") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=show&<?= $localhostquery ?>frm_o_o[1][class]=serverweb">&#x00bb; localhost</a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=show&<?= $serverquery ?>frm_o_o[1][class]=serverweb">&#x00bb;&nbsp;<?= $syncserver ?></a><br/>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=show&o=serverftp") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=show&<?= $localhostquery ?>frm_o_o[1][class]=serverftp">&#x00bb; localhost</a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=show&<?= $serverquery ?>frm_o_o[1][class]=serverftp">&#x00bb;&nbsp;<?= $syncserver ?></a><br/>

<?php
		} else {
?>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=show&<?= $serverquery ?>frm_o_o[1][class]=phpini"><?= $ghtml->getTitleOnly("a=show&o=phpini") ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=extraedit&<?= $serverquery ?>frm_o_o[1][class]=phpini"><?= $ghtml->getTitleOnly("a=updateform&sa=extraedit&o=phpini") ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=show&<?= $serverquery ?>frm_o_o[1][class]=serverweb"><?= $ghtml->getTitleOnly("a=show&o=serverweb") ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=show&<?= $serverquery ?>frm_o_o[1][class]=serverftp"><?= $ghtml->getTitleOnly("a=show&o=serverftp") ?></a><br/>
<?php
		}
?>
					</div>
					<b><?= $login->getKeywordUc('database') ?></b>
					<div class="dropmenu">
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&frm_o_cname=dbadmin"><?= $ghtml->getTitleOnly("a=list&c=dbadmin") ?></a><br/>
<?php
		if ((check_if_many_server()) && ($syncserver !== 'localhost')) {	
?>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=updateform&sa=mysqlpasswordreset&o=pserver") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=mysqlpasswordreset&<?= $localhostquery ?>">&#x00bb; localhost</a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=mysqlpasswordreset&<?= $serverquery ?>">&#x00bb;&nbsp;<?= $syncserver ?></a><br/>

<?php
		} else {
?>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=mysqlpasswordreset&<?= $serverquery ?>"><?= $ghtml->getTitleOnly("a=updateform&sa=mysqlpasswordreset&o=pserver") ?></a><br/>
<?php
		}
?>
					</div>
				</div>
				<div class='left'>
					<b><?= $login->getKeywordUc('mail') ?></b>
					<div class="dropmenu">
<?php
		if ((check_if_many_server()) && ($syncserver !== 'localhost')) {	
?>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=updateform&sa=update&o=servermail") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=update&<?= $localhostquery ?>frm_o_o[1][class]=servermail">&#x00bb; localhost</a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=update&<?= $serverquery ?>frm_o_o[1][class]=servermail">&#x00bb;&nbsp;<?= $syncserver ?></a><br/>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=updateform&sa=update&o=spamdyke") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=spamdyke&<?= $localhostquery ?>frm_o_o[1][class]=servermail">&#x00bb; localhost</a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=spamdyke&<?= $serverquery ?>frm_o_o[1][class]=servermail">&#x00bb;&nbsp;<?= $syncserver ?></a><br/>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=list&c=mail_graylist_wlist_a") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $localhostquery ?>frm_o_o[1][class]=servermail&frm_o_cname=mail_graylist_wlist_a">&#x00bb; localhost</a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $serverquery ?>frm_o_o[1][class]=servermail&frm_o_cname=mail_graylist_wlist_a">&#x00bb;&nbsp;<?= $syncserver ?></a><br/>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=list&c=mailqueue") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $localhostquery ?>frm_o_cname=mailqueue">&#x00bb; localhost</a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $serverquery ?>frm_o_cname=mailqueue">&#x00bb;&nbsp;<?= $syncserver ?></a><br/>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=list&c=clientmail") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $localhostquery ?>frm_o_cname=clientmail">&#x00bb; localhost</a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $serverquery ?>frm_o_cname=clientmail">&#x00bb;&nbsp;<?= $syncserver ?></a><br/>

<?php
		} else {
?>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=update&<?= $serverquery ?>frm_o_o[1][class]=servermail"><?= $ghtml->getTitleOnly("a=updateform&sa=update&o=servermail") ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=spamdyke&<?= $serverquery ?>frm_o_o[1][class]=servermail"><?= $ghtml->getTitleOnly("a=updateform&sa=spamdyke&o=servermail") ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $serverquery ?>frm_o_o[1][class]=servermail&frm_o_cname=mail_graylist_wlist_a"><?= $ghtml->getTitleOnly("a=list&c=mail_graylist_wlist_a") ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $serverquery ?>frm_o_cname=mailqueue"><?= $ghtml->getTitleOnly("a=list&c=mailqueue") ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $serverquery ?>frm_o_cname=clientmail"><?= $ghtml->getTitleOnly("a=list&c=clientmail") ?></a><br/>
<?php
		}
?>
					</div>
				</div>
				<div style='clear: both;'></div>
			</div>
		</li>
<?php
	}
?>
		<li class="separator"></li>
		<li><a href="#"><?= $login->getKeywordUc('task') ?></a>
			<div class="drop decor2_2" style="width:<?= $genwidth ?>px">
				<div class='left rightborder'>
					<b><?= $login->getKeywordUc('domain') ?></b>
					<div class="dropmenu">
<?php
	if ($clientquery === "") {
?>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $clientquery ?>frm_o_cname=domain"><?= $ghtml->getTitleOnly("a=list&c=domain") ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $clientquery ?>frm_o_cname=subdomain"><?= $ghtml->getTitleOnly("a=list&c=subdomain") ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $clientquery ?>frm_o_cname=addondomain"><?= $ghtml->getTitleOnly("a=list&c=addondomain") ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=default_domain&<?= $clientquery ?>"><?= $ghtml->getTitleOnly("a=updateform&sa=default_domain&o=client") ?></a><br/>
<?php
	} else {
?>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=list&c=domain") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&frm_o_cname=domain"><?= $loginas ?></a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $clientquery ?>frm_o_cname=domain"><?= $clientid ?></a><br/>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=list&c=subdomain") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&frm_o_cname=subdomain"><?= $loginas ?></a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $clientquery ?>frm_o_cname=subdomain"><?= $clientid ?></a><br/>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=list&c=addondomain") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&frm_o_cname=addondomain"><?= $loginas ?></a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $clientquery ?>frm_o_cname=addondomain"><?= $clientid ?></a><br/>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=updateform&sa=default_domain&o=client") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=default_domain"><?= $loginas ?></a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=updateform&frm_subaction=default_domain&<?= $clientquery ?>"><?= $clientid ?></a><br/>

<?php
	}
?>					</div>
					<b><?= $login->getKeywordUc('ftpmaildatabase') ?></b>
					<div class="dropmenu">
<?php
	if ($clientquery === "") {
?>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $clientquery ?>frm_o_cname=ftpuser"><?= $ghtml->getTitleOnly("a=list&c=ftpuser") ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $clientquery ?>frm_o_cname=mailaccount"><?= $ghtml->getTitleOnly("a=list&c=mailaccount") ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $clientquery ?>frm_o_cname=mysqldb"><?= $ghtml->getTitleOnly("a=list&c=mysqldb") ?></a><br/>
<?php
	} else {
?>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=list&c=ftpuser") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&frm_o_cname=ftpuser"><?= $loginas ?></a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $clientquery ?>frm_o_cname=ftpuser"><?= $clientid ?></a><br/>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=list&c=mailaccount") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&frm_o_cname=mailaccount"><?= $loginas ?></a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $clientquery ?>frm_o_cname=mailaccount"><?= $clientid ?></a><br/>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=list&c=mysqldb") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&frm_o_cname=mysqldb"><?= $loginas ?></a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $clientquery ?>frm_o_cname=mysqldb"><?= $clientid ?></a><br/>
<?php
	}
?>					</div>
					<b><?= $login->getKeywordUc('filemanager') ?></b>
					<div class="dropmenu">
<?php
	if ($login->isAdmin()) {
?>
<?php
		if ((check_if_many_server()) && ($syncserver !== 'localhost')) {	
?>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=show&o=ffile") ?>
							&#x00bb;&nbsp;<a href="/display.php?frm_action=show&<?= $localhostquery ?>frm_o_o[1][class]=ffile&frm_o_o[1][nname]=">&#x00bb; localhost</a>
							&#x00bb;&nbsp;<a href="/display.php?frm_action=show&<?= $serverquery ?>frm_o_o[1][class]=ffile&frm_o_o[1][nname]=">&#x00bb;&nbsp;<?= $syncserver ?></a><br/>

<?php
		} else {
?>
						&#x00bb;&nbsp;<a href="/display.php?frm_action=show&<?= $serverquery ?>frm_o_o[1][class]=ffile&frm_o_o[1][nname]="><?= $ghtml->getTitleOnly("a=show&o=ffile") ?> &#x00bb;&nbsp;<?= $syncserver ?></a><br/>
<?php
		}
?><?php
	}
?>

<?php
	if ($clientquery === "") {
?>
						&#x00bb;&nbsp;<a href="/display.php?frm_action=show&frm_o_o[0][class]=ffile&frm_o_o[0][nname]=/"><?= $ghtml->getTitleOnly("a=show&o=ffile") ?> &#x00bb;&nbsp;<?= $clientid ?></a><br/>
<?php
	} else {
?>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=show&o=ffile") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=show&frm_o_o[0][class]=ffile&frm_o_o[1][nname]=/"><?= $loginas ?></a></a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=show&<?= $clientquery ?>frm_o_o[1][class]=ffile&frm_o_o[1][nname]=/"><?= $clientid ?></a></a>
<?php
	}
?>
					</div>
				</div>
				<div class='left'>
					<b><?= $login->getKeywordUc('crontask') ?></b>
					<div class="dropmenu">
<?php
	if ($clientquery === "") {
?>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&frm_o_cname=cron"><?= $ghtml->getTitleOnly("a=list&c=cron") ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=addform&frm_dttype[var]=ttype&frm_dttype[val]=simple&frm_o_cname=cron"><?= $ghtml->getTitleOnly("a=addform&c=cron&dta[var]=ttype&dta[val]=simple") ?></a><br/>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=addform&frm_dttype[var]=ttype&frm_dttype[val]=complex&frm_o_cname=cron"><?= $ghtml->getTitleOnly("a=addform&c=cron&dta[var]=ttype&dta[val]=complex") ?></a><br/>
<?php
	} else {
?>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=list&c=cron") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&frm_o_cname=cron"><?= $loginas ?></a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $clientquery ?>frm_o_cname=cron"><?= $clientid ?></a><br/>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=addform&c=cron&dta[var]=ttype&dta[val]=simple") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=addform&frm_dttype[var]=ttype&frm_dttype[val]=simple&frm_o_cname=cron"><?= $loginas ?></a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=addform&<?= $clientquery ?>frm_dttype[var]=ttype&frm_dttype[val]=simple&frm_o_cname=cron"><?= $clientid ?></a><br/>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=addform&c=cron&dta[var]=ttype&dta[val]=complex") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=addform&frm_dttype[var]=ttype&frm_dttype[val]=complex&frm_o_cname=cron"><?= $loginas ?></a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=addform&<?= $clientquery ?>frm_dttype[var]=ttype&frm_dttype[val]=complex&frm_o_cname=cron"><?= $clientid ?></a><br/>

<?php
	}
?>
					</div>


<?php
	if (!$login->isCustomer()) {
?>
					<b><?= $login->getKeywordUc('client') ?></b>
					<div class="dropmenu">
<?php
		if ($clientquery === "") {
?>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&frm_o_cname=client"><?= $ghtml->getTitleOnly("a=list&c=client") ?></a><br/>
<?php
		} else {
?>
						&#x00bb;&nbsp;<?= $ghtml->getTitleOnly("a=list&c=client") ?>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&frm_o_cname=client"><?= $loginas ?></a>
							&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&<?= $clientquery ?>frm_o_cname=client"><?= $clientid ?></a><br/>

<?php
		}

		if ($login->isAdmin()) {
?>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=list&frm_o_cname=all_client"><?= $ghtml->getTitleOnly("a=list&c=all_client") ?></a><br/>
<?php
			if (check_if_many_server()) {
?>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=addform&frm_dttype[var]=cttype&frm_dttype[val]=wholesale&frm_o_cname=client"><?= $ghtml->getTitleOnly("a=addform&dta[var]=cttype&dta[val]=wholesale&c=client") ?></a><br/>
<?php
			}
?>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=addform&frm_dttype[var]=cttype&frm_dttype[val]=reseller&frm_o_cname=client"><?= $ghtml->getTitleOnly("a=addform&dta[var]=cttype&dta[val]=reseller&c=client") ?></a><br/>
<?php
		}
?>
						&#x00bb;&nbsp;<a href="/display.php?<?= $consumedlogin ?>frm_action=addform&frm_dttype[var]=cttype&frm_dttype[val]=customer&frm_o_cname=client"><?= $ghtml->getTitleOnly("a=addform&dta[var]=cttype&dta[val]=customer&c=client") ?></a><br/>
					</div>
				</div>
<?php
	}
?>
				<div style='clear: both;'></div>
			</div>
		</li>
	</ul>
</div>
<div style="float:right">
	<ul class="menuTemplate2 decor2_1">
		<!-- <li><a href="#" onMouseOver="document.getElementById('infomsg').style.display='inline';" onMouseOut="document.getElementById('infomsg').style.display='none';"><?= $login->getKeywordUc('help') ?></a></li> -->
		<li><a title="<?= $login->getKeywordUc('click_here_for') ?> <?= $login->getKeywordUc('help') ?>" href="#" onClick="toggleVisibilityByClass('infomsg');"><?= $login->getKeywordUc('help') ?></a></li>
		<li class="separator"></li>
		<li><a title="<?= $login->getKeywordUc('click_here_for') ?> <?= $login->getKeywordUc('logout') ?>"  href="javascript:if (confirm('<?= $login->getKeywordUc('is_want_logout') ?>')) { location = '/lib/php/logout.php'; }"><?= $login->getKeywordUc('logout') ?></a>
		</li>
	</ul>
</div>