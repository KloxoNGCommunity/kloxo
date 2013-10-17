<?php
	$syncserver = $login->syncserver;
	$userid = $login->getId();
?>

<link rel="stylesheet" href="/theme/menu/purecss/css/style.css" type="text/css" />

<table width="100%" style="border:0; margin:0; padding:0"><tr><td width="100%">

<ul class="pureCssMenu pureCssMenum">
	<li class="pureCssMenui0"><a class="pureCssMenui0" href="/display.php?frm_action=show">Home</a></li>

	<li class="pureCssMenui0"><a class="pureCssMenui0" href="#"><span>Administration</span><![if gt IE 6]></a><![endif]><!--[if lte IE 6]><table><tr><td><![endif]-->
	<ul class="pureCssMenum">
		<li class="pureCssMenui0"><a class="pureCssMenui0" href="#"><span>List All</span><![if gt IE 6]></a><![endif]><!--[if lte IE 6]><table><tr><td><![endif]-->
		<ul class="pureCssMenum">
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=list&frm_o_cname=all_domain">Domains</a></li>
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=list&frm_o_cname=all_addondomain">Pointer Domains</a></li>
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=list&frm_o_cname=all_mailaccount">Mailaccounts</a></li>
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=list&frm_o_cname=all_mailforward">Mailforwards</a></li>

			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=list&frm_o_cname=all_mysqldb">Mysql Databases</a></li>
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=list&frm_o_cname=all_cron">Scheduled Tasks</a></li>
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=list&frm_o_cname=all_ftpuser">Ftpusers</a></li>
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=list&frm_o_cname=all_mailinglist">Mailing Lists</a></li>
		</ul><!--[if lte IE 6]></td></tr></table></a><![endif]--></li>

		<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=list&frm_o_cname=actionlog">Action Log</a></li>

		<li class="pureCssMenui0"><a class="pureCssMenui0" href="#"><span>Resource Plan</span><![if gt IE 6]></a><![endif]><!--[if lte IE 6]><table><tr><td><![endif]-->
		<ul class="pureCssMenum">
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=list&frm_o_cname=resourceplan">List Resource Plan</a></li>
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=addform&frm_o_cname=resourceplan">Add Resource Plan</a></li>
		</ul><!--[if lte IE 6]></td></tr></table></a><![endif]--></li>

		<li class="pureCssMenui0"><a class="pureCssMenui0" href="#"><span>Help Desk</span><![if gt IE 6]></a><![endif]><!--[if lte IE 6]><table><tr><td><![endif]-->
		<ul class="pureCssMenum">
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=list&frm_o_cname=ticket">List Ticket</a></li>
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=addform&frm_o_cname=ticket">Add Ticket</a></li>
		</ul><!--[if lte IE 6]></td></tr></table></a><![endif]--></li>

		<li class="pureCssMenui0"><a class="pureCssMenui0" href="#"><span>Message</span><![if gt IE 6]></a><![endif]><!--[if lte IE 6]><table><tr><td><![endif]-->
		<ul class="pureCssMenum">
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=list&frm_o_cname=smessage">List Message</a></li>
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=addform&frm_o_cname=smessage">Add Message</a></li>
		</ul><!--[if lte IE 6]></td></tr></table></a><![endif]--></li>

		<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=updateform&frm_subaction=password">Password</a></li>

		<li class="pureCssMenui0"><a class="pureCssMenui0" href="#"><span>Custom Button</span><![if gt IE 6]></a><![endif]><!--[if lte IE 6]><table><tr><td><![endif]-->
		<ul class="pureCssMenum">
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=list&frm_o_cname=custombutton">List Custom Button</a></li>
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=addform&frm_o_cname=custombutton">Add Custom Button</a></li>
		</ul><!--[if lte IE 6]></td></tr></table></a><![endif]--></li>

		<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=list&frm_o_cname=information">Information</a></li>

		<li class="pureCssMenui0"><a class="pureCssMenui0" href="#"><span>Reverse Dns</span><![if gt IE 6]></a><![endif]><!--[if lte IE 6]><table><tr><td><![endif]-->
		<ul class="pureCssMenum">
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=list&frm_o_cname=reversedns">Reverse Dns</a></li>
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=updateform&frm_subaction=reversedns&frm_o_o[0][class]=general">Dns Config</a></li>
		</ul><!--[if lte IE 6]></td></tr></table></a><![endif]--></li>
<?php
	if ($userid == 'admin') {
?>
		<li class="pureCssMenui0"><a class="pureCssMenui0" href="#"><span>Update</span><![if gt IE 6]></a><![endif]><!--[if lte IE 6]><table><tr><td><![endif]-->
		<ul class="pureCssMenum">
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=show&frm_o_o[0][class]=lxupdate">Update Home</a></li>
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=list&frm_o_o[0][class]=lxupdate&frm_o_cname=releasenote">Release Notes</a></li>
		</ul><!--[if lte IE 6]></td></tr></table></a><![endif]--></li>


		<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=updateform&frm_subaction=skin&frm_o_o[0][class]=sp_specialplay">Appearance</a></li>
<?php
	}
?>
	</ul><!--[if lte IE 6]></td></tr></table></a><![endif]--></li>

	<li class="pureCssMenui0"><a class="pureCssMenui0" href="#"><span>Resources</span><![if gt IE 6]></a><![endif]><!--[if lte IE 6]><table><tr><td><![endif]-->
	<ul class="pureCssMenum">
		<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=updateform&frm_subaction=update&frm_o_o[0][class]=domaindefault">Domain Defaults</a></li>

		<li class="pureCssMenui0"><a class="pureCssMenui0" href="#"><span>Auxiliary</span><![if gt IE 6]></a><![endif]><!--[if lte IE 6]><table><tr><td><![endif]-->
		<ul class="pureCssMenum">
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=list&frm_o_cname=auxiliary">List Auxiliary Login</a></li>
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=addform&frm_o_cname=auxiliary">Add Auxiliary Login</a></li>
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=list&frm_o_cname=all_auxiliary">All Auxiliaries</a></li>
		</ul><!--[if lte IE 6]></td></tr></table></a><![endif]--></li>

		<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=list&frm_o_cname=utmp">Login History</a></li>

		<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=list&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_cname=ftpsession">Ftp Sessions</a></li>

		<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=list&frm_o_cname=utmp">Shell Access</a></li>

		<li class="pureCssMenui0"><a class="pureCssMenui0" href="#"><span>DNS Template</span><![if gt IE 6]></a><![endif]><!--[if lte IE 6]><table><tr><td><![endif]-->
		<ul class="pureCssMenum">
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=list&frm_o_cname=dnstemplate">List DNS Template</a></li>
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=addform&frm_o_cname=dnstemplate">Add DNS Template</a></li>
		</ul><!--[if lte IE 6]></td></tr></table></a><![endif]--></li>

		<li class="pureCssMenui0"><a class="pureCssMenui0" href="#"><span>Backup</span><![if gt IE 6]></a><![endif]><!--[if lte IE 6]><table><tr><td><![endif]-->
		<ul class="pureCssMenum">
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=show&frm_o_o[0][class]=lxbackup">Backup Home</a></li>
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=updateform&frm_subaction=ftp_conf&frm_o_o[0][class]=lxbackup">Ftp Configuration</a></li>
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=updateform&frm_subaction=schedule_conf&frm_o_o[0][class]=lxbackup">Schedule Configuration</a></li>
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=show&frm_o_o[0][class]=lxbackup&frm_o_o[1][class]=ffile&frm_o_o[1][nname]=/">File Manager</a></li>
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=updateform&frm_subaction=upload&frm_o_o[0][class]=lxbackup&frm_o_o[1][class]=ffile&frm_o_o[1][nname]=/">Upload</a></li>
		</ul><!--[if lte IE 6]></td></tr></table></a><![endif]--></li>
	</ul></li>

	<li class="pureCssMenui0"><a class="pureCssMenui0" href="#"><span>Advanced</span><![if gt IE 6]></a><![endif]><!--[if lte IE 6]><table><tr><td><![endif]-->
	<ul class="pureCssMenum">
<?php
	if ($userid == 'admin') {
?>
		<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=updateform&frm_subaction=scavengetime&frm_o_o[0][class]=general">Scavenge Time</a></li>
		<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=updateform&frm_subaction=generalsetting&frm_o_o[0][class]=general">General Settings</a></li>

		<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=updateform&frm_subaction=maintenance&frm_o_o[0][class]=general">System Under Maintenance</a></li>

		<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=updateform&frm_subaction=selfbackupconfig&frm_o_o[0][class]=general">Config Self Backup</a></li>

		<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=list&frm_o_o[0][class]=genlist&frm_o_cname=dirindexlist_a">Directory Indexes</a></li>
<?php
	}
?>
		<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=updateform&frm_subaction=miscinfo">Details</a></li>

<?php
	if ($userid == 'admin') {
?>
		<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=updateForm&frm_subaction=upload_logo&frm_o_o[0][class]=sp_specialplay">Upload Logo</a></li>

		<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=updateform&frm_subaction=skin&frm_o_o[0][class]=sp_childspecialplay">Child Appearance</a></li>

		<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=updateform&frm_subaction=portconfig&frm_o_o[0][class]=general">Port Config</a></li>

		<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=updateform&frm_subaction=disable_skeleton">Skeleton And Disable</a></li>
<?php
	}
?>
		<li class="pureCssMenui0"><a class="pureCssMenui0" href="#"><span>Blocked/Allowed IPs</span><![if gt IE 6]></a><![endif]><!--[if lte IE 6]><table><tr><td><![endif]-->
		<ul class="pureCssMenum">
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=list&frm_o_cname=blockedip">List Blocked</a></li>
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=addform&frm_o_cname=blockedip">Add Blocked</a></li>
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=list&frm_o_cname=allowedip">List Allowed</a></li>
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=addform&frm_o_cname=allowedip">Add Allowed</a></li>
		</ul><!--[if lte IE 6]></td></tr></table></a><![endif]--></li>

		<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=show&frm_o_o[0][class]=notification">Notification Home</a></li>

		<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=updateform&frm_subaction=login_options&frm_o_o[0][class]=sp_specialplay">Login Options</a></li>
<?php
	if ($userid == 'admin') {
?>
		<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=updateform&frm_subaction=license&frm_o_o[0][class]=license">License Update</a></li>
<?php
	}
?>
	</ul><!--[if lte IE 6]></td></tr></table></a><![endif]--></li>

	<li class="pureCssMenui0"><a class="pureCssMenui0" href="#"><span>Servers</span><![if gt IE 6]></a><![endif]><!--[if lte IE 6]><table><tr><td><![endif]-->
	<ul class="pureCssMenum">
<?php
	if ($userid == 'admin') {
?>
		<li class="pureCssMenui0"><a class="pureCssMenui0" href="#"><span>Server</span><![if gt IE 6]></a><![endif]><!--[if lte IE 6]><table><tr><td><![endif]-->
		<ul class="pureCssMenum">
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=list&frm_o_cname=pserver">List Server</a></li>
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=addform&frm_o_cname=pserver">Add Server</a></li>
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=updateform&frm_subaction=forcedeletepserver">Force Delete Server</a></li>
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=list&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_cname=psrole_a">Server Roles</a></li>
		</ul><!--[if lte IE 6]></td></tr></table></a><![endif]--></li>

		<li class="pureCssMenui0"><a class="pureCssMenui0" href="#"><span>Show</span><![if gt IE 6]></a><![endif]><!--[if lte IE 6]><table><tr><td><![endif]-->
		<ul class="pureCssMenum">
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=list&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_cname=service">Show Services</a></li>
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=list&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_cname=process">Show Processes</a></li>
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=list&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_cname=component">Show Component Info</a></li>
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=show&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_o[1][class]=llog">Show Log Manager</a></li>
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=updateform&frm_subaction=update&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_o[1][class]=driver">Driver Configuration</a></li>
		</ul><!--[if lte IE 6]></td></tr></table></a><![endif]--></li>

		<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=updateform&frm_subaction=switchprogram&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>">Switch Program</a></li>
		<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=updateform&frm_subaction=timezone&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>">Timezone</a></li>
		<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=updateform&frm_subaction=commandcenter&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>">Command Center</a></li>
<?php
	}

	if ($userid == 'admin') {
?>
		<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=show&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_o[1][class]=sshclient">SSH Terminal</a></li>
<?php
	} else {
?>
		<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=show&frm_o_o[0][class]=sshclient">SSH Terminal</a></li>
<?php
	}
?>
		<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=list&frm_o_cname=traceroute">Traceroute</a></li>
<?php

	if ($userid == 'admin') {
?>
		<li class="pureCssMenui0"><a class="pureCssMenui0" href="#"><span>Reboot/Poweroff</span><![if gt IE 6]></a><![endif]><!--[if lte IE 6]><table><tr><td><![endif]-->
		<ul class="pureCssMenum">
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=updateForm&frm_subaction=reboot&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>">Reboot</a></li>
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=updateForm&frm_subaction=poweroff&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>">Poweroff</a></li>
		</ul><!--[if lte IE 6]></td></tr></table></a><![endif]--></li>
<?php
	}
?>
	</ul><!--[if lte IE 6]></td></tr></table></a><![endif]--></li>
<?php

	if ($userid == 'admin') {
?>
	<li class="pureCssMenui0"><a class="pureCssMenui0" href="#"><span>Security</span><![if gt IE 6]></a><![endif]><!--[if lte IE 6]><table><tr><td><![endif]-->
	<ul class="pureCssMenum">
		<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=show&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_o[1][class]=sshconfig">SSH Config</a></li>
		<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=list&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_cname=watchdog">Watchdog</a></li>

		<li class="pureCssMenui0"><a class="pureCssMenui0" href="#"><span>LxGuard</span><![if gt IE 6]></a><![endif]><!--[if lte IE 6]><table><tr><td><![endif]-->
		<ul class="pureCssMenum">
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=show&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_o[1][class]=lxguard">LxGuard</a></li>
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=list&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=localhost&frm_o_o[1][class]=lxguard&frm_o_cname=lxguardhitdisplay">Connections</a></li>
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=list&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=localhost&frm_o_o[1][class]=lxguard&frm_o_cname=rawlxguardhit">Raw Connections</a></li>
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=list&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=localhost&frm_o_o[1][class]=lxguard&frm_o_cname=lxguardwhitelist">White List</a></li>
		</ul><!--[if lte IE 6]></td></tr></table></a><![endif]--></li>

		<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=list&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_cname=hostdeny">Blocked Host</a></li>
		<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=list&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_cname=sshauthorizedkey">SSH Authorized Keys</a></li>
	</ul><!--[if lte IE 6]></td></tr></table></a><![endif]--></li>

	<li class="pureCssMenui0"><a class="pureCssMenui0" href="#"><span>Hosting</span><![if gt IE 6]></a><![endif]><!--[if lte IE 6]><table><tr><td><![endif]-->
	<ul class="pureCssMenum">
		<li class="pureCssMenui0"><a class="pureCssMenui0" href="#"><span>PHP</span><![if gt IE 6]></a><![endif]><!--[if lte IE 6]><table><tr><td><![endif]-->
		<ul class="pureCssMenum">
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=show&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_o[1][class]=phpini">PHP Config</a></li>
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=updateform&frm_subaction=extraedit&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_o[1][class]=phpini">Addvanced PHP Config</a></li>
		</ul><!--[if lte IE 6]></td></tr></table></a><![endif]--></li>

		<li class="pureCssMenui0"><a class="pureCssMenui0" href="#"><span>Web</span><![if gt IE 6]></a><![endif]><!--[if lte IE 6]><table><tr><td><![endif]-->
		<ul class="pureCssMenum">
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=show&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_o[1][class]=serverweb">Webserver Config</a></li>
		</ul><!--[if lte IE 6]></td></tr></table></a><![endif]--></li>

		<li class="pureCssMenui0"><a class="pureCssMenui0" href="#"><span>FTP</span><![if gt IE 6]></a><![endif]><!--[if lte IE 6]><table><tr><td><![endif]-->
		<ul class="pureCssMenum">
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=show&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_o[1][class]=serverftp">FTP Config</a></li>
		</ul><!--[if lte IE 6]></td></tr></table></a><![endif]--></li>

		<li class="pureCssMenui0"><a class="pureCssMenui0" href="#"><span>Mail</span><![if gt IE 6]></a><![endif]><!--[if lte IE 6]><table><tr><td><![endif]-->
		<ul class="pureCssMenum">
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=updateform&frm_subaction=update&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_o[1][class]=servermail">Server Mail Settings</a></li>
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=updateform&frm_subaction=spamdyke&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_o[1][class]=servermail">Spamdyke</a></li>
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=list&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_o[1][class]=servermail&frm_o_cname=mail_graylist_wlist_a">Whitelist IPs</a></li>
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=list&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_cname=mailqueue">Mail Queue</a></li>
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=list&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_cname=clientmail">Mails Per Client</a></li>
		</ul><!--[if lte IE 6]></td></tr></table></a><![endif]--></li>

		<li class="pureCssMenui0"><a class="pureCssMenui0" href="#"><span>Database</span><![if gt IE 6]></a><![endif]><!--[if lte IE 6]><table><tr><td><![endif]-->
		<ul class="pureCssMenum">
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=updateform&frm_subaction=mysqlpasswordreset&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>">Mysql Password Reset</a></li>
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=list&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_cname=dbadmin">List Database Admins</a></li>
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=addform&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_cname=dbadmin">Add Database Admin</a></li>
		</ul><!--[if lte IE 6]></td></tr></table></a><![endif]--></li>
	</ul><!--[if lte IE 6]></td></tr></table></a><![endif]--></li>
<?php
	}
?>
	<li class="pureCssMenui0"><a class="pureCssMenui0" href="#"><span>Task</span><![if gt IE 6]></a><![endif]><!--[if lte IE 6]><table><tr><td><![endif]-->
	<ul class="pureCssMenum">
		<li class="pureCssMenui0"><a class="pureCssMenui0" href="#"><span>Domain/Subdomain</span><![if gt IE 6]></a><![endif]><!--[if lte IE 6]><table><tr><td><![endif]-->
		<ul class="pureCssMenum">
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=show&frm_o_o[0][class]=ffile&frm_o_o[0][nname]=/">Default Domain</a></li>
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=list&frm_o_cname=domain">Add Domain</a></li>
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=list&frm_o_cname=subdomain">Add Subdomain</a></li>
		</ul><!--[if lte IE 6]></td></tr></table></a><![endif]--></li>

		<li class="pureCssMenui0"><a class="pureCssMenui0" href="#"><span>Ftp User</span><![if gt IE 6]></a><![endif]><!--[if lte IE 6]><table><tr><td><![endif]-->
		<ul class="pureCssMenum">
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=list&frm_o_cname=ftpuser">List Ftp User</a></li>
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=addform&frm_o_cname=ftpuser">Add Ftp User</a></li>
		</ul><!--[if lte IE 6]></td></tr></table></a><![endif]--></li>

		<li class="pureCssMenui0"><a class="pureCssMenui0" href="#"><span>Mail</span><![if gt IE 6]></a><![endif]><!--[if lte IE 6]><table><tr><td><![endif]-->
		<ul class="pureCssMenum">
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=list&frm_o_cname=mailaccount">Add Mail Account</a></li>
		</ul><!--[if lte IE 6]></td></tr></table></a><![endif]--></li>

		<li class="pureCssMenui0"><a class="pureCssMenui0" href="#"><span>Database</span><![if gt IE 6]></a><![endif]><!--[if lte IE 6]><table><tr><td><![endif]-->
		<ul class="pureCssMenum">
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=list&frm_o_cname=ftpuser">List MySQL Database</a></li>
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=addform&frm_o_cname=mysqldb">Add MySQL Database</a></li>
		</ul><!--[if lte IE 6]></td></tr></table></a><![endif]--></li>
<?php
	if ($userid == 'admin') {
?>
		<li class="pureCssMenui0"><a class="pureCssMenui0" href="#"><span>Client</span><![if gt IE 6]></a><![endif]><!--[if lte IE 6]><table><tr><td><![endif]-->
		<ul class="pureCssMenum">
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=list&frm_o_cname=client">List Client</a></li>
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=addform&frm_dttype[var]=cttype&frm_dttype[val]=wholesale&frm_o_cname=client">Add Wholesale Reseller</a></li>
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=addform&frm_dttype[var]=cttype&frm_dttype[val]=reseller&frm_o_cname=client">Add Reseller</a></li>
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=addform&frm_dttype[var]=cttype&frm_dttype[val]=customer&frm_o_cname=client">Add Customer</a></li>
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=list&frm_o_cname=all_client&frm_filter[view]=quota">Quota View</a></li>
		</ul><!--[if lte IE 6]></td></tr></table></a><![endif]--></li>
<?php

	}
?>
		<li class="pureCssMenui0"><a class="pureCssMenui0" href="#"><span>Cron Task</span><![if gt IE 6]></a><![endif]><!--[if lte IE 6]><table><tr><td><![endif]-->
		<ul class="pureCssMenum">
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=list&frm_o_cname=cron">List Cron Scheduled Task</a></li>
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=addform&frm_dttype[var]=ttype&frm_dttype[val]=simple&frm_o_cname=cron">Add Simple Cron Task</a></li>
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=addform&frm_dttype[var]=ttype&frm_dttype[val]=complex&frm_o_cname=cron">Add Standard Cron Task</a></li>
		</ul><!--[if lte IE 6]></td></tr></table></a><![endif]--></li>

		<li class="pureCssMenui0"><a class="pureCssMenui0" href="#"><span>File Manager</span><![if gt IE 6]></a><![endif]><!--[if lte IE 6]><table><tr><td><![endif]-->
		<ul class="pureCssMenum">
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=show&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_o[1][class]=ffile&frm_o_o[1][nname]=">To Server Root Directory</a></li>
			<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=show&frm_o_o[0][class]=client&frm_o_o[0][nname]=<?= $userid ?>&frm_o_o[1][class]=ffile&frm_o_o[1][nname]=/">To User Home Directory</a></li>
		</ul><!--[if lte IE 6]></td></tr></table></a><![endif]--></li>

		<li class="pureCssMenui"><a class="pureCssMenui" href="/display.php?frm_action=updateform&frm_subaction=skin&frm_o_o[0][class]=client&frm_o_o[0][nname]=<?= $userid ?>&frm_o_o[1][class]=sp_specialplay">Appearance</a></li>
	</ul><!--[if lte IE 6]></td></tr></table></a><![endif]--></li>
</ul>

</td>
<td>
<ul class="pureCssMenu pureCssMenum">
	<li class="pureCssMenui0"><a class="pureCssMenui0" href="javascript:if (confirm('Do You Really Want To Logout?')) { location = '/lib/php/logout.php'; }">Logout</a></li>
</ul>
</td></tr></table>
