<?php
//	header("X-Hiawatha-Cache: 60");

//	$syncserver = $login->syncserver;
//	$userid = $login->getId();

	$syncserver = $_GET['s'];
	$userid = $_GET['u'];
?>

<link rel="stylesheet" type="text/css" href="/theme/skin/simplicity/default/menu/menutemplate2/css/style.css"/>
<div style="float:left">
<ul class="menuTemplate2 decor2_1">
	<li><a href="/display.php?frm_action=show">Home</a></li>
	<li class="separator"></li>
	<li><a href="#" class="arrow">Administration</a>

		<div class="drop decor2_2" style="width: 560px;">
			<div class='left rightborder'>
				<b>All List</b>
				<div style="list-style:disc">
					<a href="/display.php?frm_action=list&frm_o_cname=all_domain">Domains</a><br/>
					<a href="/display.php?frm_action=list&frm_o_cname=all_addondomain">Pointer Domains</a><br/>
					<a href="/display.php?frm_action=list&frm_o_cname=all_mailaccount">Mailaccounts</a><br/>
					<a href="/display.php?frm_action=list&frm_o_cname=all_mailforward">Mailforwards</a><br/>
					<a href="/display.php?frm_action=list&frm_o_cname=all_mysqldb">Mysql Databases</a><br/>
					<a href="/display.php?frm_action=list&frm_o_cname=all_cron">Scheduled Tasks</a><br/>
					<a href="/display.php?frm_action=list&frm_o_cname=all_ftpuser">Ftpusers</a><br/>
					<a href="/display.php?frm_action=list&frm_o_cname=all_mailinglist">Mailing Lists</a>
				</div>
			</div>
			<div class='left rightborder'>
				<b>Resource Plan</b>
				<div>
					<a href="/display.php?frm_action=list&frm_o_cname=resourceplan">Resource Plans</a><br/>
					<!-- <a href="/display.php?frm_action=addform&frm_o_cname=resourceplan">Add Resource Plan</a> -->
				</div>
				<b>Help Desk</b>
				<div>
					<a href="/display.php?frm_action=list&frm_o_cname=ticket">Tickets</a><br/>
					<!-- <a href="/display.php?frm_action=addform&frm_o_cname=ticket">Add Ticket</a><br/> -->
					<a href="/display.php?frm_action=updateform&frm_subaction=ticketconfig&frm_o_o[0][class]=ticketconfig">Configuration</a><br/>
					<a href="/display.php?frm_action=list&frm_o_o[0][class]=general&frm_o_cname=helpdeskcategory_a">Category</a>
				</div>
			</div>
			<div class='left rightborder'>
				<b>Message</b>
				<div>
					<a href="/display.php?frm_action=list&frm_o_cname=smessage">Messages</a><br/>
					<!-- <a href="/display.php?frm_action=addform&frm_o_cname=smessage">Add Message</a> -->
				</div>
				<b>Custom Button</b>
				<div>
					<a href="/display.php?frm_action=list&frm_o_cname=custombutton">Custom Buttons</a><br/>
					<!-- <a href="/display.php?frm_action=addform&frm_o_cname=custombutton">Add Custom Button</a> -->
				</div>
				<b>Reverse Dns</b>
				<div>
					<a href="/display.php?frm_action=list&frm_o_cname=reversedns">Reverse Dns</a><br/>
					<a href="/display.php?frm_action=updateform&frm_subaction=reversedns&frm_o_o[0][class]=general">Dns Config</a>
				</div>
			</div>
			<div class='left'>
				<b>Update</b>
				<div>
					<a href="/display.php?frm_action=show&frm_o_o[0][class]=lxupdate">Update Home</a><br/>
					<a href="/display.php?frm_action=list&frm_o_o[0][class]=lxupdate&frm_o_cname=releasenote">Release Notes</a>
				</div>
				<b>Other</b>
				<div>
					<a href="/display.php?frm_action=list&frm_o_cname=actionlog">Action Log</a><br/>
					<a href="/display.php?frm_action=list&frm_o_cname=information">Information</a><br/>
					<a href="/display.php?frm_action=updateform&frm_subaction=password">Password</a><br/>
					<a href="/display.php?frm_action=updateform&frm_subaction=skin&frm_o_o[0][class]=sp_specialplay">Appearance</a>
				</div>
			</div>
			<div style='clear: both;'></div>
		</div>
	</li>
	<li class="separator"></li>
	<li><a href="#" class="arrow">Resources</a>
		<div class="drop decor2_2" style="width: 480px;">
			<div class='left rightborder'>
				<b>Auxiliary</b>
				<div>
					<a href="/display.php?frm_action=list&frm_o_cname=auxiliary">Auxiliary Logins</a><br/>
					<!-- <a href="/display.php?frm_action=addform&frm_o_cname=auxiliary">Add Auxiliary Login</a><br/> -->
					<a href="/display.php?frm_action=list&frm_o_cname=all_auxiliary">All Auxiliaries</a>
				</div>
				<b>DNS Template</b>
				<div>
					<a href="/display.php?frm_action=list&frm_o_cname=dnstemplate">DNS Templates</a><br/>
					<!-- <a href="/display.php?frm_action=addform&frm_o_cname=dnstemplate">Add DNS Template</a> -->
				</div>
			</div>
			<div class='left rightborder'>
				<b>Backup</b>
				<div>
					<a href="/display.php?frm_action=show&frm_o_o[0][class]=lxbackup">Backup Home</a><br/>
					<a href="/display.php?frm_action=updateform&frm_subaction=ftp_conf&frm_o_o[0][class]=lxbackup">Ftp Configuration</a><br/>
					<a href="/display.php?frm_action=updateform&frm_subaction=schedule_conf&frm_o_o[0][class]=lxbackup">Schedule Configuration</a><br/>
					<a href="/display.php?frm_action=show&frm_o_o[0][class]=lxbackup&frm_o_o[1][class]=ffile&frm_o_o[1][nname]=/">File Manager</a><br/>
					<a href="/display.php?frm_action=updateform&frm_subaction=upload&frm_o_o[0][class]=lxbackup&frm_o_o[1][class]=ffile&frm_o_o[1][nname]=/">Upload</a>
				</div>
			</div>
			<div class='left'>
				<div>
					<b>Other</b>

					<div>
						<a href="/display.php?frm_action=updateform&frm_subaction=update&frm_o_o[0][class]=domaindefault">Domain Defaults</a><br/>
						<a href="/display.php?frm_action=list&frm_o_cname=utmp">Login History</a><br/>
						<a href="/display.php?frm_action=list&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_cname=ftpsession">Ftp Sessions</a><br/>
						<a href="/display.php?frm_action=list&frm_o_cname=utmp">Shell Access</a>
					</div>
				</div>
			</div>
			<div style='clear: both;'></div>
		</div>
	</li>
	<li class="separator"></li>
	<li><a href="#">Advanced</a>
		<div class="drop decor2_2" style="width: 480px;">
			<div class='left rightborder'>
				<b>Customize</b>
				<div>
					<a href="/display.php?frm_action=updateform&frm_subaction=scavengetime&frm_o_o[0][class]=general">Scavenge Time</a><br/>
					<a href="/display.php?frm_action=updateform&frm_subaction=generalsetting&frm_o_o[0][class]=general">General Settings</a><br/>
					<a href="/display.php?frm_action=updateform&frm_subaction=selfbackupconfig&frm_o_o[0][class]=general">Config Self Backup</a><br/>
					<a href="/display.php?frm_action=list&frm_o_o[0][class]=genlist&frm_o_cname=dirindexlist_a">Directory Indexes</a><br/>
					<a href="/display.php?frm_action=updateForm&frm_subaction=upload_logo&frm_o_o[0][class]=sp_specialplay">Upload Logo</a><br/>
					<a href="/display.php?frm_action=updateform&frm_subaction=portconfig&frm_o_o[0][class]=general">Port Config</a><br/>
					<a href="/display.php?frm_action=updateform&frm_subaction=disable_skeleton">Skeleton And Disable</a><br/>
					<a href="/display.php?frm_action=show&frm_o_o[0][class]=notification">Notification Home</a><br/>
					<a href="/display.php?frm_action=updateform&frm_subaction=login_options&frm_o_o[0][class]=sp_specialplay">Login Options</a><br/>
					<!-- <a href="/display.php?frm_action=updateform&frm_subaction=license&frm_o_o[0][class]=license">License Update</a><br /> -->
				</div>
			</div>
			<div class='left rightborder'>
				<b>Blocked/Allowed IPs</b>
				<div>
					<a href="/display.php?frm_action=list&frm_o_cname=allowedip">Allowed IPs</a><br/>
					<!-- <a href="/display.php?frm_action=addform&frm_o_cname=allowedip">Add Allowed IP</a><br/> -->
					<a href="/display.php?frm_action=list&frm_o_cname=blockedip">Blocked IPs</a><br/>
					<!-- <a href="/display.php?frm_action=addform&frm_o_cname=blockedip">Add Blocked IP</a><br/> -->
				</div>
				<b>Maintenance</b>
				<div>
					<a href="/display.php?frm_action=updateform&frm_subaction=maintenance&frm_o_o[0][class]=general">Under Maintenance</a><br/>

				</div>
			</div>
			<div class='left'>
				<b>Other</b>
				<div>
					<a href="/display.php?frm_action=updateform&frm_subaction=miscinfo">Details</a><br/>
					<a href="/display.php?frm_action=updateform&frm_subaction=skin&frm_o_o[0][class]=sp_childspecialplay">Child Appearance</a><br/>
				</div>
			</div>
			<div style='clear: both;'></div>
		</div>
	</li>
	<li class="separator"></li>
	<li><a href="#">Servers</a>
		<div class="drop decor2_2" style="width: 480px;">
			<div class='left rightborder'>
				<b>Server</b>

				<div>
					<a href="/display.php?frm_action=list&frm_o_cname=pserver">Servers</a><br/>
					<!-- <a href="/display.php?frm_action=addform&frm_o_cname=pserver">Add Server</a><br/> -->
					<a href="/display.php?frm_action=updateform&frm_subaction=forcedeletepserver">Force Delete Server</a><br/>
					<a href="/display.php?frm_action=list&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_cname=psrole_a">Server Roles</a><br/>
					<a href="/display.php?frm_action=updateForm&frm_subaction=reboot&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>">Reboot</a><br/>
					<a href="/display.php?frm_action=updateForm&frm_subaction=poweroff&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>">Poweroff</a><br/>
				</div>
			</div>
			<div class='left rightborder'>
				<b>Show</b>

				<div>
					<a href="/display.php?frm_action=list&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_cname=service">Services</a><br/>
					<a href="/display.php?frm_action=list&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_cname=process">Processes</a><br/>
					<a href="/display.php?frm_action=list&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_cname=component">Component Info</a><br/>
					<a href="/display.php?frm_action=show&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_o[1][class]=llog">Log Manager</a><br/>
					<a href="/display.php?frm_action=updateform&frm_subaction=update&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_o[1][class]=driver">Driver Configuration</a><br/>
				</div>
			</div>
			<div class='left'>
				<b>Other</b>

				<div>
					<a href="/display.php?frm_action=updateform&frm_subaction=switchprogram&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>">Switch Program</a><br/>
					<a href="/display.php?frm_action=updateform&frm_subaction=timezone&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>">Timezone</a><br/>
					<a href="/display.php?frm_action=updateform&frm_subaction=commandcenter&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>">Command Center</a><br/>
					<a href="/display.php?frm_action=show&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_o[1][class]=sshclient">SSH Terminal</a><br/>
					<a href="/display.php?frm_action=list&frm_o_cname=traceroute">Traceroute</a><br/>
				</div>
			</div>
			<div style='clear: both;'></div>
		</div>
	</li>
	<li class="separator"></li>
	<li><a href="#">Security</a>
		<div class="drop decor2_2" style="width: 360px;">
			<div class='left rightborder'>
				<b>LxGuard</b>
				<div>
					<a href="/display.php?frm_action=show&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_o[1][class]=lxguard">LxGuard</a><br/>
					<a href="/display.php?frm_action=list&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=localhost&frm_o_o[1][class]=lxguard&frm_o_cname=lxguardhitdisplay">Connections</a><br/>
					<a href="/display.php?frm_action=list&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=localhost&frm_o_o[1][class]=lxguard&frm_o_cname=rawlxguardhit">Raw Connections</a><br/>
					<a href="/display.php?frm_action=list&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=localhost&frm_o_o[1][class]=lxguard&frm_o_cname=lxguardwhitelist">White List</a><br/>
				</div>
			</div>
			<div class='left'>
				<b>Other</b>
				<div>
					<a href="/display.php?frm_action=show&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_o[1][class]=sshconfig">SSH Config</a><br/>
					<a href="/display.php?frm_action=list&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_cname=watchdog">Watchdog</a><br/>
					<a href="/display.php?frm_action=list&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_cname=hostdeny">Blocked Host</a><br/>
					<a href="/display.php?frm_action=list&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_cname=sshauthorizedkey">SSH Authorized Keys</a><br/>
				</div>
			</div>
			<div style='clear: both;'></div>
		</div>
	</li>
	<li class="separator"></li>
	<li><a href="#">Hosting</a>
		<div class="drop decor2_2" style="width: 560px;">
			<div class='left rightborder'>
				<b>PHP</b>
				<div>
					<a href="/display.php?frm_action=show&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_o[1][class]=phpini">PHP Config</a><br/>
					<a href="/display.php?frm_action=updateform&frm_subaction=extraedit&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_o[1][class]=phpini">Addvanced PHP Config</a><br/>
				</div>
				<b>Web</b>
				<div>
					<a href="/display.php?frm_action=show&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_o[1][class]=serverweb">Webserver Config</a><br/>
				</div>
				<b>FTP</b>
				<div>
					<a href="/display.php?frm_action=show&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_o[1][class]=serverftp">FTP Config</a><br/>
				</div>
			</div>
			<div class='left rightborder'>
				<b>Mail</b>
				<div>
					<a href="/display.php?frm_action=updateform&frm_subaction=update&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_o[1][class]=servermail">Server Mail Settings</a><br/>
					<a href="/display.php?frm_action=updateform&frm_subaction=spamdyke&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_o[1][class]=servermail">Spamdyke</a><br/>
					<a href="/display.php?frm_action=list&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_o[1][class]=servermail&frm_o_cname=mail_graylist_wlist_a">Whitelist IPs</a><br/>
					<a href="/display.php?frm_action=list&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_cname=mailqueue">Mail Queue</a><br/>
					<a href="/display.php?frm_action=list&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_cname=clientmail">Mails Per Client</a><br/>
				</div>
			</div>
			<div class='left'>
				<b>Database</b>
				<div>
					<a href="/display.php?frm_action=list&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_cname=dbadmin">Database Admins</a><br/>
					<!-- <a href="/display.php?frm_action=addform&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_cname=dbadmin">Add Database Admin</a><br/> -->
					<a href="/display.php?frm_action=updateform&frm_subaction=mysqlpasswordreset&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>">Mysql Password Reset</a><br/>
				</div>
			</div>
			<div style='clear: both;'></div>
		</div>
	</li>
	<li class="separator"></li>
	<li><a href="#">Task</a>
		<div class="drop decor2_2" style="width: 560px;">
			<div class='left rightborder'>
				<b>Domain</b>
				<div>
					<a href="/display.php?frm_action=list&frm_o_cname=domain">Domains</a><br/>
					<a href="/display.php?frm_action=list&frm_o_cname=subdomain">Subdomains</a><br/>
					<a href="/display.php?frm_action=updateform&frm_subaction=default_domain">Default Domain</a><br/>
				</div>
				<b>Ftp User</b>
				<div>
					<a href="/display.php?frm_action=list&frm_o_cname=ftpuser">Ftp Users</a><br/>
					<!-- <a href="/display.php?frm_action=addform&frm_o_cname=ftpuser">Add Ftp User</a><br/> -->
				</div>
				<b>Mail</b>
				<div>
					<a href="/display.php?frm_action=list&frm_o_cname=mailaccount">Mail Accounts</a><br/>
				</div>
			</div>
			<div class='left rightborder'>
				<b>Database</b>
				<div>
					<a href="/display.php?frm_action=list&frm_o_cname=mysqldb">MySQL Databases</a><br/>
					<!-- <a href="/display.php?frm_action=addform&frm_o_cname=mysqldb">Add MySQL Database</a><br/> -->
				</div>
				<b>Client</b>
				<div>
					<a href="/display.php?frm_action=list&frm_o_cname=client">Clients</a><br/>
					<a href="/display.php?frm_action=addform&frm_dttype[var]=cttype&frm_dttype[val]=wholesale&frm_o_cname=client">Add Wholesale Reseller</a><br/>
					<a href="/display.php?frm_action=addform&frm_dttype[var]=cttype&frm_dttype[val]=reseller&frm_o_cname=client">Add Reseller</a><br/>
					<a href="/display.php?frm_action=addform&frm_dttype[var]=cttype&frm_dttype[val]=customer&frm_o_cname=client">Add Customer</a><br/>
					<a href="/display.php?frm_action=list&frm_o_cname=all_client&frm_filter[view]=quota">Quota View</a><br/>
				</div>
			</div>
			<div class='left'>
				<b>Cron Task</b>
				<div>
					<a href="/display.php?frm_action=list&frm_o_cname=cron">Cron Scheduled Tasks</a><br/>
					<a href="/display.php?frm_action=addform&frm_dttype[var]=ttype&frm_dttype[val]=simple&frm_o_cname=cron">Add Simple Cron Task</a><br/>
					<a href="/display.php?frm_action=addform&frm_dttype[var]=ttype&frm_dttype[val]=complex&frm_o_cname=cron">Add Standard Cron Task</a><br/>
				</div>
				<b>File Manager</b>
				<div>
					<a href="/display.php?frm_action=show&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_o[1][class]=ffile&frm_o_o[1][nname]=">To Server Root Directory</a><br/>
					<a href="/display.php?frm_action=show&frm_o_o[0][class]=client&frm_o_o[0][nname]=<?= $userid ?>&frm_o_o[1][class]=ffile&frm_o_o[1][nname]=/">To User Home Directory</a><br/>
				</div>
				<b>Other</b>
				<div>
					<a href="/display.php?frm_action=updateform&frm_subaction=skin&frm_o_o[0][class]=client&frm_o_o[0][nname]=<?= $userid ?>&frm_o_o[1][class]=sp_specialplay">Appearance</a><br/>
				</div>
			</div>
			<div style='clear: both;'></div>
		</div>
	</li>
</ul>
</div>
<div style="float:right">
	<ul class="menuTemplate2 decor2_1">
		<li><a href="#" onMouseOver="document.getElementById('infomsg').style.display='inline';" onMouseOut="document.getElementById('infomsg').style.display='none';">Help</a></li>
		<li class="separator"></li>
		<li><a href="javascript:if (confirm('Do You Really Want To Logout?')) { location = '/lib/php/logout.php'; }">Logout</a></li>
	</ul>
</div>