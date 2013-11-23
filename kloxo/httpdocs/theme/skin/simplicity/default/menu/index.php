<?php
//	header("X-Hiawatha-Cache: 60");

if (strpos("/display.php", $_SERVER["SCRIPT_NAME"]) === false) {
	print("No permit access directly");
	return;
}

$syncserver = $login->syncserver;
$userid = $login->getId();

//	$syncserver = $_GET['s'];
//	$userid = $_GET['u'];
?>

<link rel="stylesheet" type="text/css" href="/theme/skin/simplicity/default/menu/css/style.css"/>

<div style="float:left">
	<ul class="menuTemplate2 decor2_1">
<?php
	if ($login->isAdmin()) {
?>
		<li><a href="/display.php?frm_action=show"><?= $login->getKeywordUc('home') ?></a></li>
<?php
	} else {
?>
		<li><a href="<?= $ghtml->getFullUrl("a=show") ?>"><?= $login->getKeywordUc('home') ?></a></li>
<?php
	}
?>
		<li class="separator"></li>
		<li><a href="#" class="arrow"><?= $login->getKeywordUc('administration') ?></a>
			<div class="drop decor2_2">
<?php
	if ($login->isAdmin()) {
?>
				<div class='left rightborder'>
					<b><?= $login->getKeywordUc('alllist') ?></b>
					<div>
						<a href="<?= $ghtml->getFullUrl("a=list&c=all_domain") ?>"><?= $ghtml->getTitleOnly("a=list&c=all_domain") ?></a><br/>
						<a href="<?= $ghtml->getFullUrl("a=list&c=all_addondomain") ?>"><?= $ghtml->getTitleOnly("a=list&c=all_addondomain") ?></a><br/>
						<a href="<?= $ghtml->getFullUrl("a=list&c=all_mailaccount") ?>"><?= $ghtml->getTitleOnly("a=list&c=all_mailaccount") ?></a><br/>
						<a href="<?= $ghtml->getFullUrl("a=list&c=all_mailforward") ?>"><?= $ghtml->getTitleOnly("a=list&c=all_mailforward") ?></a><br/>
						<a href="<?= $ghtml->getFullUrl("a=list&c=all_mysqldb") ?>"><?= $ghtml->getTitleOnly("a=list&c=all_mysqldb") ?></a><br/>
						<a href="<?= $ghtml->getFullUrl("a=list&c=all_cron") ?>"><?= $ghtml->getTitleOnly("a=list&c=all_cron") ?></a><br/>
						<a href="<?= $ghtml->getFullUrl("a=list&c=all_ftpuser") ?>"><?= $ghtml->getTitleOnly("a=list&c=all_ftpuser") ?></a><br/>
						<a href="<?= $ghtml->getFullUrl("a=list&c=all_mailinglist") ?>"><?= $ghtml->getTitleOnly("a=list&c=all_mailinglist") ?></a><br/>
					</div>
<?php
	}

	if ($login->isLte('reseller')) {
?>
					<b><?= $login->getKeywordUc('resourceplan') ?></b>
					<div>
						<a href="<?= $ghtml->getFullUrl("a=list&c=resourceplan") ?>"><?= $ghtml->getTitleOnly("a=list&c=resourceplan") ?></a><br/>
					</div>
				</div>
<?php
	}
?>
				<div class='left rightborder'>

					<b><?= $login->getKeywordUc('support') ?></b>
					<div>
						<a href="<?= $ghtml->getFullUrl("a=list&c=ticket") ?>"><?= $ghtml->getTitleOnly("a=list&c=ticket") ?></a><br/>
<?php
	if ($login->isAdmin()) {
?>
						<a href="<?= $ghtml->getFullUrl("a=updateform&sa=ticketconfig&o=ticketconfig") ?>"><?= $ghtml->getTitleOnly("a=updateform&sa=ticketconfig&o=ticketconfig") ?></a><br/>
						<a href="<?= $ghtml->getFullUrl("a=list&c=helpdeskcategory_a&o=general") ?>"><?= $ghtml->getTitleOnly("a=list&c=helpdeskcategory_a&o=general") ?></a><br/>
<?php
	}
?>
					</div>
					<b><?= $login->getKeywordUc('message') ?></b>
					<div>
						<a href="<?= $ghtml->getFullUrl("a=list&c=smessage") ?>"><?= $ghtml->getTitleOnly("a=list&c=smessage") ?></a><br/>
					</div>
<?php
	if ($login->isAdmin()) {
?>
					<b><?= $login->getKeywordUc('custombutton') ?></b>
					<div>
						<a href="<?= $ghtml->getFullUrl("a=list&c=custombutton") ?>"><?= $ghtml->getTitleOnly("a=list&c=custombutton") ?></a><br/>
					</div>
<?php
	}
?>
					<b><?= $login->getKeywordUc('reversedns') ?></b>
					<div>
						<a href="<?= $ghtml->getFullUrl("a=list&c=reversedns") ?>"><?= $ghtml->getTitleOnly("a=list&c=reversedns") ?></a><br/>
						<!-- <a href="<?= $ghtml->getFullUrl("a=list&sa=reversedns&o=general") ?>"><?= $ghtml->getTitleOnly("a=list&sa=reversedns&o=general") ?></a><br/> -->
					</div>
				</div>
				<div class='left'>
<?php
	if ($login->isAdmin()) {
?>
					<b><?= $login->getKeywordUc('update') ?></b>
					<div>
						<a href="<?= $ghtml->getFullUrl("a=show&o=lxupdate") ?>"><?= $ghtml->getTitleOnly("a=show&o=lxupdate") ?></a><br/>
						<a href="<?= $ghtml->getFullUrl("a=list&o=lxupdate&c=releasenote") ?>"><?= $ghtml->getTitleOnly("a=list&o=lxupdate&c=releasenote") ?></a><br/>
					</div>
<?php
	}
?>
					<b><?= $login->getKeywordUc('other') ?></b>
					<div>
						<a href="<?= $ghtml->getFullUrl("a=updateform&sa=information") ?>"><?= $ghtml->getTitleOnly("a=updateform&sa=information&o=client") ?></a><br/>
						<a href="<?= $ghtml->getFullUrl("a=list&c=actionlog") ?>"><?= $ghtml->getTitleOnly("a=list&c=actionlog") ?></a><br/>
						<a href="<?= $ghtml->getFullUrl("a=updateform&sa=password") ?>"><?= $ghtml->getTitleOnly("a=updateform&sa=password&o=client") ?></a><br/>
						<a href="<?= $ghtml->getFullUrl("a=updateform&sa=skin&o=sp_specialplay") ?>"><?= $ghtml->getTitleOnly("a=updateform&sa=skin&o=sp_specialplay") ?></a><br/>
					</div>
				</div>
				<div style='clear: both;'></div>
			</div>
		</li>
		<li class="separator"></li>
		<li><a href="#" class="arrow"><?= $login->getKeywordUc('resource') ?></a>
			<div class="drop decor2_2">
				<div class='left rightborder'>
					<b><?= $login->getKeywordUc('auxiliary') ?></b>
					<div>
						<a href="<?= $ghtml->getFullUrl("a=list&c=auxiliary") ?>"><?= $ghtml->getTitleOnly("a=list&c=auxiliary") ?></a><br/>
						<a href="<?= $ghtml->getFullUrl("a=list&c=all_auxiliary") ?>"><?= $ghtml->getTitleOnly("a=list&c=all_auxiliary") ?></a>
					</div>
					<b><?= $login->getKeywordUc('dnstemplate') ?></b>
					<div>
						<a href="<?= $ghtml->getFullUrl("a=list&c=dnstemplate") ?>"><?= $ghtml->getTitleOnly("a=list&c=dnstemplate") ?></a><br/>
					</div>
				</div>
				<div class='left rightborder'>
					<b><?= $login->getKeywordUc('backuprestore') ?></b>
					<div>
						<a href="<?= $ghtml->getFullUrl("a=show&o=lxbackup") ?>"><?= $ghtml->getTitleOnly("a=show&o=lxbackup") ?></a><br/>
						<a href="<?= $ghtml->getFullUrl("a=updateform&sa=ftp_conf&o=lxbackup") ?>"><?= $ghtml->getTitleOnly("a=updateform&sa=ftp_conf&o=lxbackup") ?></a><br/>
						<a href="<?= $ghtml->getFullUrl("a=updateform&sa=schedule_conf&o=lxbackup") ?>"><?= $ghtml->getTitleOnly("a=updateform&sa=schedule_conf&o=lxbackup") ?></a><br/>
						<a href="<?= $ghtml->getFullUrl("a=show&o=lxbackup&l[class]=ffile&l[nname]=/") ?>"><?= $ghtml->getTitleOnly("a=show&o=lxbackup&l[class]=ffile&l[nname]=/") ?></a><br/>
						<a href="<?= $ghtml->getFullUrl("a=updateform&sa=upload&o=lxbackup&l[class]=ffile&l[nname]=/") ?>"><?= $ghtml->getTitleOnly("a=updateform&sa=upload&o=lxbackup&l[class]=ffile&l[nname]=/") ?></a>
					</div>
				</div>
				<div class='left'>
					<div>
						<b><?= $login->getKeywordUc('other') ?></b>
						<div>
							<a href="<?= $ghtml->getFullUrl("a=list&c=ipaddress") ?>"><?= $ghtml->getTitleOnly("a=list&c=ipaddress") ?></a><br/>
							<a href="<?= $ghtml->getFullUrl("a=updateform&sa=update&o=domaindefault") ?>"><?= $ghtml->getTitleOnly("a=updateform&sa=update&o=domaindefault") ?></a><br/>
							<a href="<?= $ghtml->getFullUrl("a=list&c=utmp") ?>"><?= $ghtml->getTitleOnly("a=list&c=utmp") ?></a><br/>
							<a href="<?= $ghtml->getFullUrl("a=list&c=ftpsession") ?>"><?= $ghtml->getTitleOnly("a=list&c=ftpsession") ?></a><br/>
							<a href="<?= $ghtml->getFullUrl("a=updateform&sa=shell_access") ?>"><?= $ghtml->getTitleOnly("a=updateform&sa=shell_access&o=client") ?></a>
						</div>
					</div>
				</div>
				<div style='clear: both;'></div>
			</div>
		</li>
		<li class="separator"></li>
		<li><a href="#"><?= $login->getKeywordUc('advanced') ?></a>
			<div class="drop decor2_2">
				<div class='left rightborder'>
					<b><?= $login->getKeywordUc('general') ?></b>
					<div>
<?php
	if ($login->isAdmin()) {
?>
						<a href="<?= $ghtml->getFullUrl("a=updateform&sa=scavengetime&o=general") ?>"><?= $ghtml->getTitleOnly("a=updateform&sa=scavengetime&o=general") ?></a><br/>
						<a href="<?= $ghtml->getFullUrl("a=updateform&sa=generalsetting&o=general") ?>"><?= $ghtml->getTitleOnly("a=updateform&sa=generalsetting&o=general") ?></a><br/>
						<a href="<?= $ghtml->getFullUrl("a=updateform&sa=selfbackupconfig&o=general") ?>"><?= $ghtml->getTitleOnly("a=updateform&sa=selfbackupconfig&o=general") ?></a><br/>
						<a href="<?= $ghtml->getFullUrl("a=list&o=genlist&c=dirindexlist_a") ?>"><?= $ghtml->getTitleOnly("a=list&o=genlist&c=dirindexlist_a") ?></a><br/>
						<a href="<?= $ghtml->getFullUrl("a=updateform&sa=upload_logo&o=sp_specialplay") ?>"><?= $ghtml->getTitleOnly("a=updateform&sa=upload_logo&o=sp_specialplay") ?></a><br/>
						<a href="<?= $ghtml->getFullUrl("a=updateform&sa=portconfig&o=general") ?>"><?= $ghtml->getTitleOnly("a=updateform&sa=portconfig&o=general") ?></a><br/>
						<a href="<?= $ghtml->getFullUrl("a=updateform&sa=disable_skeleton&c=client") ?>"><?= $ghtml->getTitleOnly("a=updateform&sa=disable_skeleton&c=client") ?></a><br/>
<?php
	}
?>
						<a href="<?= $ghtml->getFullUrl("a=show&o=notification") ?>"><?= $ghtml->getTitleOnly("a=show&o=notification") ?></a><br/>
						<a href="<?= $ghtml->getFullUrl("a=updateform&sa=login_options&o=sp_specialplay") ?>"><?= $ghtml->getTitleOnly("a=updateform&sa=login_options&o=sp_specialplay") ?></a><br/>
						<!-- <a href="<?= $ghtml->getFullUrl("a=updateform&sa=license&o=license") ?>"><?= $ghtml->getTitleOnly("a=updateform&sa=license&o=license") ?></a><br /> -->
					</div>
				</div>
				<div class='left rightborder'>
					<b><?= $login->getKeywordUc('blockedallowedips') ?></b>
					<div>
						<a href="<?= $ghtml->getFullUrl("a=list&c=allowedip") ?>"><?= $ghtml->getTitleOnly("a=list&c=allowedip") ?></a><br/>
						<a href="<?= $ghtml->getFullUrl("a=list&c=blockedip") ?>"><?= $ghtml->getTitleOnly("a=list&c=blockedip") ?></a><br/>
					</div>
<?php
	if ($login->isAdmin()) {
?>
					<b><?= $login->getKeywordUc('maintenance') ?></b>
					<div>
						<a href="<?= $ghtml->getFullUrl("a=updateform&sa=maintenance&o=general") ?>"><?= $ghtml->getTitleOnly("a=updateform&sa=maintenance&o=general") ?></a><br/>

					</div>
<?php
	}

?>
				</div>
				<div class='left'>
					<b><?= $login->getKeywordUc('other') ?></b>
					<div>
						<a href="/display.php?frm_action=updateform&frm_subaction=miscinfo"><?= $ghtml->getTitleOnly("a=updateform&sa=miscinfo&o=pserver") ?></a><br/>
<?php
	if ($login->isAdmin()) {
?>
						<a href="/display.php?frm_action=updateform&frm_subaction=skin&frm_o_o[0][class]=sp_childspecialplay"><?= $ghtml->getTitleOnly("a=updateform&sa=skin&o=sp_childspecialplay") ?></a><br/>
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
			<div class="drop decor2_2">
				<div class='left rightborder'>
					<b><?= $login->getKeywordUc('server') ?></b>
					<div>
						<a href="<?= $ghtml->getFullUrl("a=list&c=pserver") ?>"><?= $ghtml->getTitleOnly("a=list&c=pserver") ?></a><br/>
						<a href="<?= $ghtml->getFullUrl("a=updateform&sa=forcedeletepserver&c=client") ?>"><?= $ghtml->getTitleOnly("a=updateform&sa=forcedeletepserver&c=client") ?></a><br/>
<?php
	if (check_if_many_server()) {
?>
						<a href="/display.php?frm_action=list&frm_o_cname=psrole_a"><?= $ghtml->getTitleOnly("a=list&c=psrole_a") ?></a><br/>
<?php
	}
?>
						<a href="/display.php?frm_action=updateform&frm_subaction=reboot&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>"><?= $ghtml->getTitleOnly("a=updateform&sa=reboot&l[class]=pserver&l[nname]={$syncserver}") ?></a><br/>
						<a href="/display.php?frm_action=updateform&frm_subaction=poweroff&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>"><?= $ghtml->getTitleOnly("a=updateform&sa=poweroff&l[class]=pserver&l[nname]={$syncserver}") ?></a><br/>
					</div>
				</div>
				<div class='left rightborder'>
					<b><?= $login->getKeywordUc('show') ?></b>
					<div>
						<a href="<?= $ghtml->getFullUrl("a=list&c=service&l[class]=pserver&l[nname]={$syncserver}") ?>"><?= $ghtml->getTitleOnly("a=list&c=service") ?></a><br/>
						<a href="<?= $ghtml->getFullUrl("a=list&c=process&l[class]=pserver&l[nname]={$syncserver}") ?>"><?= $ghtml->getTitleOnly("a=list&c=process") ?></a><br/>
						<a href="<?= $ghtml->getFullUrl("a=list&c=component&l[class]=pserver&l[nname]={$syncserver}") ?>"><?= $ghtml->getTitleOnly("a=list&c=component") ?></a><br/>
						<a href="/display.php?frm_action=show&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_o[1][class]=llog"><?= $ghtml->getTitleOnly("a=show&o=llog") ?></a><br/>
						<a href="<?= $ghtml->getFullUrl("a=updateform&sa=update&o=driver") ?>"><?= $ghtml->getTitleOnly("a=updateform&sa=update&o=driver") ?></a><br/>
					</div>
				</div>
				<div class='left'>
					<b><?= $login->getKeywordUc('other') ?></b>
					<div>
						<a href="/display.php?frm_action=updateform&frm_subaction=switchprogram&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>"><?= $ghtml->getTitleOnly("a=updateform&sa=switchprogram&l[class]=pserver&l[nname]={$syncserver}") ?></a><br/>
						<a href="/display.php?frm_action=updateform&frm_subaction=timezone&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>"><?= $ghtml->getTitleOnly("a=updateform&sa=timezone&l[class]=pserver&l[nname]={$syncserver}") ?></a><br/>
						<a href="/display.php?frm_action=updateform&frm_subaction=commandcenter&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>"><?= $ghtml->getTitleOnly("a=updateform&sa=commandcenter") ?></a><br/>
						<a href="/display.php?frm_action=show&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_o[1][class]=sshclient"><?= $ghtml->getTitleOnly("a=show&o=sshclient") ?></a><br/>
						<a href="<?= $ghtml->getFullUrl("a=list&c=traceroute") ?>"><?= $ghtml->getTitleOnly("a=list&c=traceroute") ?></a><br/>
					</div>
				</div>
				<div style='clear: both;'></div>
			</div>
		</li>
		<li class="separator"></li>
		<li><a href="#"><?= $login->getKeywordUc('security') ?></a>
			<div class="drop decor2_2">
				<div class='left rightborder'>
					<b><?= $login->getKeywordUc('lxguard') ?></b>
					<div>
						<a href="<?= $ghtml->getFullUrl("a=show&o=lxguard") ?>"><?= $ghtml->getTitleOnly("a=show&o=lxguard") ?></a><br/>
						<a href="<?= $ghtml->getFullUrl("a=list&o=lxguard&c=lxguardhitdisplay") ?>"><?= $ghtml->getTitleOnly("a=list&o=lxguard&c=lxguardhitdisplay") ?></a><br/>
						<a href="<?= $ghtml->getFullUrl("a=list&o=lxguard&c=rawlxguardhit") ?>"><?= $ghtml->getTitleOnly("a=list&o=lxguard&c=rawlxguardhit") ?></a><br/>
						<a href="<?= $ghtml->getFullUrl("a=list&o=lxguard&c=lxguardwhitelist") ?>"><?= $ghtml->getTitleOnly("a=list&o=lxguard&c=lxguardwhitelist") ?></a><br/>
					</div>
				</div>
				<div class='left'>
					<b><?= $login->getKeywordUc('other') ?></b>
					<div>
						<a href="<?= $ghtml->getFullUrl("a=show&o=sshconfig") ?>"><?= $ghtml->getTitleOnly("a=show&o=sshconfig") ?></a><br/>
						<a href="<?= $ghtml->getFullUrl("a=list&c=watchdog") ?>"><?= $ghtml->getTitleOnly("a=list&c=watchdog") ?></a><br/>
						<a href="<?= $ghtml->getFullUrl("a=list&c=hostdeny") ?>"><?= $ghtml->getTitleOnly("a=list&c=hostdeny") ?></a><br/>
						<a href="<?= $ghtml->getFullUrl("a=list&c=sshauthorizedkey") ?>"><?= $ghtml->getTitleOnly("a=list&c=sshauthorizedkey") ?></a><br/>
					</div>
				</div>
				<div style='clear: both;'></div>
			</div>
		</li>
<?php
	}
?>
		<li class="separator"></li>
		<li><a href="#"><?= $login->getKeywordUc('webmailanddb') ?></a>
			<div class="drop decor2_2">
<?php
	if ($login->isAdmin()) {
?>
				<div class='left rightborder'>
<?php
	} else {
?>
				<div class='left'>
<?php
	}
?>
					<b><?= $login->getKeywordUc('configure') ?></b>
					<div>
						<a href="<?= $ghtml->getFullUrl("a=show&o=phpini") ?>"><?= $ghtml->getTitleOnly("a=show&o=phpini") ?></a><br/>
						<a href="<?= $ghtml->getFullUrl("a=updateform&sa=extraedit&o=phpini") ?>"><?= $ghtml->getTitleOnly("a=updateform&sa=extraedit&o=phpini") ?></a><br/>
<?php
	if ($login->isAdmin()) {
?>
						<a href="<?= $ghtml->getFullUrl("a=show&o=serverweb") ?>"><?= $ghtml->getTitleOnly("a=show&o=serverweb") ?></a><br/>
						<a href="<?= $ghtml->getFullUrl("a=show&o=serverftp") ?>"><?= $ghtml->getTitleOnly("a=show&o=serverftp") ?></a><br/>
<?php
	}
?>
					</div>
				</div>
<?php
	if ($login->isAdmin()) {
?>
				<div class='left rightborder'>
					<b><?= $login->getKeywordUc('mail') ?></b>
					<div>
						<a href="<?= $ghtml->getFullUrl("a=updateform&sa=update&o=servermail") ?>"><?= $ghtml->getTitleOnly("a=updateform&sa=update&o=servermail") ?></a><br/>
						<a href="<?= $ghtml->getFullUrl("a=updateform&sa=spamdyke&o=servermail") ?>"><?= $ghtml->getTitleOnly("a=updateform&sa=spamdyke&o=servermail") ?></a><br/>
						<a href="<?= $ghtml->getFullUrl("a=list&c=mail_graylist_wlist_a") ?>"><?= $ghtml->getTitleOnly("a=list&c=mail_graylist_wlist_a") ?></a><br/>
						<a href="<?= $ghtml->getFullUrl("a=list&c=mailqueue") ?>"><?= $ghtml->getTitleOnly("a=list&c=mailqueue") ?></a><br/>
						<a href="<?= $ghtml->getFullUrl("a=list&c=clientmail") ?>"><?= $ghtml->getTitleOnly("a=list&c=clientmail") ?></a><br/>
					</div>
				</div>
				<div class='left'>
					<b><?= $login->getKeywordUc('database') ?></b>
					<div>
						<a href="<?= $ghtml->getFullUrl("a=list&c=dbadmin") ?>"><?= $ghtml->getTitleOnly("a=list&c=dbadmin") ?></a><br/>
						<a href="<?= $ghtml->getFullUrl("a=updateform&sa=mysqlpasswordreset&l[class]=pserver&l[nname]={$syncserver}") ?>"><?= $ghtml->getTitleOnly("a=updateform&sa=mysqlpasswordreset&o=pserver") ?></a><br/>
					</div>
				</div>
<?php
	}
?>
				<div style='clear: both;'></div>
			</div>
		</li>

		<li class="separator"></li>
		<li><a href="#"><?= $login->getKeywordUc('task') ?></a>

			<div class="drop decor2_2">
				<div class='left rightborder'>
					<b><?= $login->getKeywordUc('domain') ?></b>
					<div>
						<a href="<?= $ghtml->getFullUrl("a=list&c=domain") ?>"><?= $ghtml->getTitleOnly("a=list&c=domain") ?></a><br/>
						<a href="<?= $ghtml->getFullUrl("a=list&c=subdomain") ?>"><?= $ghtml->getTitleOnly("a=list&c=subdomain") ?></a><br/>
						<a href="<?= $ghtml->getFullUrl("a=updateform&sa=default_domain&o=client") ?>"><?= $ghtml->getTitleOnly("a=updateform&sa=default_domain&o=client") ?></a><br/>
					</div>
					<b><?= $login->getKeywordUc('ftpmaildatabase') ?></b>
					<div>
						<a href="<?= $ghtml->getFullUrl("a=list&c=ftpuser") ?>"><?= $ghtml->getTitleOnly("a=list&c=ftpuser") ?></a><br/>
						<a href="<?= $ghtml->getFullUrl("a=list&c=mailaccount") ?>"><?= $ghtml->getTitleOnly("a=list&c=mailaccount") ?></a><br/>
						<a href="<?= $ghtml->getFullUrl("a=list&c=mysqldb") ?>"><?= $ghtml->getTitleOnly("a=list&c=mysqldb") ?></a><br/>
					</div>
				</div>
<?php
	if ($login->isAdmin()) {
?>
				<div class='left rightborder'>
<?php
	} else {
?>
				<div class='left'>
<?php
	}
?>
					<b><?= $login->getKeywordUc('crontask') ?></b>
					<div>
						<a href="<?= $ghtml->getFullUrl("a=list&c=cron") ?>"><?= $ghtml->getTitleOnly("a=list&c=cron") ?></a><br/>
						<a href="<?= $ghtml->getFullUrl("a=addform&c=cron&dta[var]=ttype&dta[val]=simple") ?>"><?= $ghtml->getTitleOnly("a=addform&c=cron&dta[var]=ttype&dta[val]=simple") ?></a><br/>
						<a href="<?= $ghtml->getFullUrl("a=addform&c=cron&dta[var]=ttype&dta[val]=complex") ?>"><?= $ghtml->getTitleOnly("a=addform&c=cron&dta[var]=ttype&dta[val]=complex") ?></a><br/>
					</div>
					<b><?= $login->getKeywordUc('filemanager') ?></b>
					<div>
<?php
	if ($login->isAdmin()) {
?>
						<a href="/display.php?frm_action=show&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_o[1][class]=ffile&frm_o_o[1][nname]="><?= $ghtml->getTitleOnly("a=show&o=ffile") ?> (<?= $login->getKeywordUc('root') ?>)</a><br/>
<?php
	}
?>
						<a href="/display.php?frm_action=show&frm_o_o[0][class]=client&frm_o_o[0][nname]=<?= $userid ?>&frm_o_o[1][class]=ffile&frm_o_o[1][nname]=/"><?= $ghtml->getTitleOnly("a=show&o=ffile") ?> (<?= $login->getKeywordUc('user') ?>)</a></a><br/>
					</div>
				</div>
				<div class='left'>
<?php
	if (!$login->isCustomer()) {
?>
					<b><?= $login->getKeywordUc('client') ?></b>
					<div>
						<a href="<?= $ghtml->getFullUrl("a=list&c=client") ?>"><?= $ghtml->getTitleOnly("a=list&c=client") ?></a><br/>
<?php
		if ($login->isAdmin()) {
?>
						<a href="<?= $ghtml->getFullUrl("a=list&c=all_client") ?>"><?= $ghtml->getTitleOnly("a=list&c=all_client") ?></a><br/>
<?php
			if (check_if_many_server()) {
?>
						<a href="<?= $ghtml->getFullUrl("a=addform&dta[var]=cttype&dta[val]=wholesale&c=client") ?>"><?= $ghtml->getTitleOnly("a=addform&dta[var]=cttype&dta[val]=wholesale&c=client") ?></a><br/>
<?php
			}
?>
						<a href="<?= $ghtml->getFullUrl("a=addform&dta[var]=cttype&dta[val]=reseller&c=client") ?>"><?= $ghtml->getTitleOnly("a=addform&dta[var]=cttype&dta[val]=reseller&c=client") ?></a><br/>
<?php
		}
?>
						<a href="<?= $ghtml->getFullUrl("a=addform&dta[var]=cttype&dta[val]=customer&c=client") ?>"><?= $ghtml->getTitleOnly("a=addform&dta[var]=cttype&dta[val]=customer&c=client") ?></a><br/>
<?php
		if ($login->isAdmin()) {
?>
						<!-- <a href="<?= $ghtml->getFullUrl("a=list&c=all_client&frm_filter[view]=quota") ?>"><?= $ghtml->getTitleOnly("a=list&c=all_client&frm_filter[view]=quota") ?></a><br/> -->
<?php
		}
?>
					</div>
<?php
	}
?>
					<!-- <b><?= $login->getKeywordUc('other') ?></b>
					<div>
						<a href="<?= $ghtml->getFullUrl("a=updateform&sa=skin&o=sp_specialplay") ?>"><?= $ghtml->getTitleOnly("a=updateform&sa=skin&o=sp_specialplay") ?></a><br/>
					</div> -->
<?php
	if (!$login->isCustomer()) {
?>
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
		<li><a title="<?= $login->getKeywordUc('click_here_for') ?> <?= $login->getKeywordUc('logout') ?>"  href="javascript:if (confirm('Do You Really Want To Logout?')) { location = '/lib/php/logout.php'; }"><?= $login->getKeywordUc('logout') ?></a>
		</li>
	</ul>
</div>