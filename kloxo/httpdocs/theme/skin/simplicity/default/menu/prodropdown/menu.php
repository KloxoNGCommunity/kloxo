<link rel="stylesheet" type="text/css" href="/theme/skin/simplicity/default/menu/prodropdown/css/style.css"/>

<script src="/theme/skin/simplicity/default/menu/prodropdown/js/script.js" type="text/javascript"></script>

<div style="float:left">
	<ul id="nav">
		<li class="top"><a class="top_link" href="/display.php?frm_action=show">Home</a></li>

		<li class="top"><a class="top_link" href="#">Administration</a>
			<ul class="sub">
				<li><a class="fly" href="#">List All</a>
					<ul>
						<li><a href="/display.php?frm_action=list&frm_o_cname=all_domain">Domains</a></li>
						<li><a href="/display.php?frm_action=list&frm_o_cname=all_addondomain">Pointer Domains</a></li>
						<li><a href="/display.php?frm_action=list&frm_o_cname=all_mailaccount">Mailaccounts</a></li>
						<li><a href="/display.php?frm_action=list&frm_o_cname=all_mailforward">Mailforwards</a></li>

						<li><a href="/display.php?frm_action=list&frm_o_cname=all_mysqldb">Mysql Databases</a></li>
						<li><a href="/display.php?frm_action=list&frm_o_cname=all_cron">Scheduled Tasks</a></li>
						<li><a href="/display.php?frm_action=list&frm_o_cname=all_ftpuser">Ftpusers</a></li>
						<li><a href="/display.php?frm_action=list&frm_o_cname=all_mailinglist">Mailing Lists</a></li>
					</ul>
				</li>

				<li><a href="/display.php?frm_action=list&frm_o_cname=actionlog">Action Log</a></li>

				<li><a class="fly" href="#">Resource Plan</a>
					<ul>
						<li><a href="/display.php?frm_action=list&frm_o_cname=resourceplan">List Resource Plan</a></li>
						<li><a href="/display.php?frm_action=addform&frm_o_cname=resourceplan">Add Resource Plan</a>
						</li>
					</ul>
				</li>

				<li><a class="fly" href="#">Help Desk</a>
					<ul>
						<li><a href="/display.php?frm_action=list&frm_o_cname=ticket">List Ticket</a></li>
						<li><a href="/display.php?frm_action=addform&frm_o_cname=ticket">Add Ticket</a></li>
					</ul>
				</li>

				<li><a class="fly" href="#">Message</a>
					<ul>
						<li><a href="/display.php?frm_action=list&frm_o_cname=smessage">List Message</a></li>
						<li><a href="/display.php?frm_action=addform&frm_o_cname=smessage">Add Message</a></li>
					</ul>
				</li>

				<li><a href="/display.php?frm_action=updateform&frm_subaction=password">Password</a></li>

				<li><a class="fly" href="#">Custom Button</a>
					<ul>
						<li><a href="/display.php?frm_action=list&frm_o_cname=custombutton">List Custom Button</a></li>
						<li><a href="/display.php?frm_action=addform&frm_o_cname=custombutton">Add Custom Button</a>
						</li>
					</ul>
				</li>

				<li><a href="/display.php?frm_action=list&frm_o_cname=information">Information</a></li>

				<li><a class="fly" href="#">Reverse Dns</a>
					<ul>
						<li><a href="/display.php?frm_action=list&frm_o_cname=reversedns">Reverse Dns</a></li>
						<li>
							<a href="/display.php?frm_action=updateform&frm_subaction=reversedns&frm_o_o[0][class]=general">Dns
								Config</a></li>
					</ul>
				</li>
<?php
if ($userid == 'admin') {
?>
					<li><a class="fly" href="#">Update</a>
						<ul>
							<li><a href="/display.php?frm_action=show&frm_o_o[0][class]=lxupdate">Update Home</a></li>
							<li>
								<a href="/display.php?frm_action=list&frm_o_o[0][class]=lxupdate&frm_o_cname=releasenote">Release
									Notes</a></li>
						</ul>
					</li>


					<li>
						<a href="/display.php?frm_action=updateform&frm_subaction=skin&frm_o_o[0][class]=sp_specialplay">Appearance</a>
					</li>
<?php
}
?>
			</ul>
		</li>

		<li class="top"><a class="top_link" href="#">Resources</a>
			<ul class="sub">
				<li><a href="/display.php?frm_action=updateform&frm_subaction=update&frm_o_o[0][class]=domaindefault">Domain
						Defaults</a></li>

				<li><a class="fly" href="#">Auxiliary</a>
					<ul>
						<li><a href="/display.php?frm_action=list&frm_o_cname=auxiliary">List Auxiliary Login</a></li>
						<li><a href="/display.php?frm_action=addform&frm_o_cname=auxiliary">Add Auxiliary Login</a></li>
						<li><a href="/display.php?frm_action=list&frm_o_cname=all_auxiliary">All Auxiliaries</a></li>
					</ul>
				</li>

				<li><a href="/display.php?frm_action=list&frm_o_cname=utmp">Login History</a></li>

				<li>
					<a href="/display.php?frm_action=list&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_cname=ftpsession">Ftp
						Sessions</a></li>

				<li><a href="/display.php?frm_action=list&frm_o_cname=utmp">Shell Access</a></li>

				<li><a class="fly" href="#">DNS Template</a>
					<ul>
						<li><a href="/display.php?frm_action=list&frm_o_cname=dnstemplate">List DNS Template</a></li>
						<li><a href="/display.php?frm_action=addform&frm_o_cname=dnstemplate">Add DNS Template</a></li>
					</ul>
				</li>

				<li><a class="fly" href="#">Backup</a>
					<ul>
						<li><a href="/display.php?frm_action=show&frm_o_o[0][class]=lxbackup">Backup Home</a></li>
						<li>
							<a href="/display.php?frm_action=updateform&frm_subaction=ftp_conf&frm_o_o[0][class]=lxbackup">Ftp
								Configuration</a></li>
						<li>
							<a href="/display.php?frm_action=updateform&frm_subaction=schedule_conf&frm_o_o[0][class]=lxbackup">Schedule
								Configuration</a></li>
						<li>
							<a href="/display.php?frm_action=show&frm_o_o[0][class]=lxbackup&frm_o_o[1][class]=ffile&frm_o_o[1][nname]=/">File
								Manager</a></li>
						<li>
							<a href="/display.php?frm_action=updateform&frm_subaction=upload&frm_o_o[0][class]=lxbackup&frm_o_o[1][class]=ffile&frm_o_o[1][nname]=/">Upload</a>
						</li>
					</ul>
				</li>
			</ul>
		</li>

		<li class="top"><a class="top_link" href="#">Advanced</a>
			<ul class="sub">
<?php
if ($userid == 'admin') {
?>
				<li><a href="/display.php?frm_action=updateform&frm_subaction=scavengetime&frm_o_o[0][class]=general">Scavenge Time</a></li>
				<li><a href="/display.php?frm_action=updateform&frm_subaction=generalsetting&frm_o_o[0][class]=general">General Settings</a></li>
				<li><a href="/display.php?frm_action=updateform&frm_subaction=maintenance&frm_o_o[0][class]=general">System Under Maintenance</a></li>
				<li><a href="/display.php?frm_action=updateform&frm_subaction=selfbackupconfig&frm_o_o[0][class]=general">Config Self Backup</a></li>
				<li><a href="/display.php?frm_action=list&frm_o_o[0][class]=genlist&frm_o_cname=dirindexlist_a">Directory Indexes</a></li>
<?php
}
?>
				<li><a href="/display.php?frm_action=updateform&frm_subaction=miscinfo">Details</a></li>

<?php
if ($userid == 'admin') {
?>
				<li><a href="/display.php?frm_action=updateForm&frm_subaction=upload_logo&frm_o_o[0][class]=sp_specialplay">Upload Logo</a></li>
				<li><a href="/display.php?frm_action=updateform&frm_subaction=skin&frm_o_o[0][class]=sp_childspecialplay">Child Appearance</a></li>
				<li><a href="/display.php?frm_action=updateform&frm_subaction=portconfig&frm_o_o[0][class]=general">Port Config</a></li>
				<li><a href="/display.php?frm_action=updateform&frm_subaction=disable_skeleton">Skeleton And Disable</a></li>
<?php
}
?>
				<li><a class="fly" href="#">Blocked/Allowed IPs</a>
					<ul>
						<li><a href="/display.php?frm_action=list&frm_o_cname=blockedip">List Blocked</a></li>
						<li><a href="/display.php?frm_action=addform&frm_o_cname=blockedip">Add Blocked</a></li>
						<li><a href="/display.php?frm_action=list&frm_o_cname=allowedip">List Allowed</a></li>
						<li><a href="/display.php?frm_action=addform&frm_o_cname=allowedip">Add Allowed</a></li>
					</ul>
				</li>

				<li><a href="/display.php?frm_action=show&frm_o_o[0][class]=notification">Notification Home</a></li>
				<li><a href="/display.php?frm_action=updateform&frm_subaction=login_options&frm_o_o[0][class]=sp_specialplay">Login Options</a></li>
<?php
if ($userid == 'admin') {
?>
				<li><a href="/display.php?frm_action=updateform&frm_subaction=license&frm_o_o[0][class]=license">License Update</a></li>
<?php
}
?>
			</ul>
		</li>

		<li class="top"><a class="top_link" href="#">Servers</a>
			<ul class="sub">
<?php
if ($userid == 'admin') {
?>
				<li><a class="fly" href="#">Server</a>
					<ul>
						<li><a href="/display.php?frm_action=list&frm_o_cname=pserver">List Server</a></li>
						<li><a href="/display.php?frm_action=addform&frm_o_cname=pserver">Add Server</a></li>
						<li><a href="/display.php?frm_action=updateform&frm_subaction=forcedeletepserver">Force Delete Server</a></li>
						<li><a href="/display.php?frm_action=list&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_cname=psrole_a">Server Roles</a></li>
					</ul>
				</li>

				<li><a class="fly" href="#">Show</a>
					<ul>
						<li><a href="/display.php?frm_action=list&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_cname=service">Show Services</a></li>
						<li><a href="/display.php?frm_action=list&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_cname=process">Show Processes</a></li>
						<li><a href="/display.php?frm_action=list&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_cname=component">Show Component Info</a></li>
						<li><a href="/display.php?frm_action=show&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_o[1][class]=llog">Show Log Manager</a></li>
						<li><a href="/display.php?frm_action=updateform&frm_subaction=update&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_o[1][class]=driver">Driver Configuration</a></li>
					</ul>
				</li>

				<li><a href="/display.php?frm_action=updateform&frm_subaction=switchprogram&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>">Switch Program</a></li>
				<li><a href="/display.php?frm_action=updateform&frm_subaction=timezone&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>">Timezone</a></li>
				<li><a href="/display.php?frm_action=updateform&frm_subaction=commandcenter&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>">Command Center</a></li>
<?php
}

if ($userid == 'admin') {
?>
				<li><a href="/display.php?frm_action=show&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_o[1][class]=sshclient">SSH Terminal</a></li>
<?php
} else {
?>
				<li><a href="/display.php?frm_action=show&frm_o_o[0][class]=sshclient">SSH Terminal</a></li>
<?php
}
?>
				<li><a href="/display.php?frm_action=list&frm_o_cname=traceroute">Traceroute</a></li>
<?php

if ($userid == 'admin') {
?>
				<li><a class="fly" href="#">Reboot/Poweroff</a>
					<ul>
						<li><a href="/display.php?frm_action=updateForm&frm_subaction=reboot&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>">Reboot</a></li>
						<li><a href="/display.php?frm_action=updateForm&frm_subaction=poweroff&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>">Poweroff</a></li>
					</ul>
				</li>
<?php
}
?>
			</ul>
		</li>
<?php

if ($userid == 'admin') {
?>
		<li class="top"><a class="top_link" href="#">Security</a>
			<ul class="sub">
				<li><a href="/display.php?frm_action=show&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_o[1][class]=sshconfig">SSH Config</a></li>
				<li><a href="/display.php?frm_action=list&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_cname=watchdog">Watchdog</a></li>
				<li><a class="fly" href="#">LxGuard</a>
					<ul>
						<li><a href="/display.php?frm_action=show&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_o[1][class]=lxguard">LxGuard</a></li>
						<li><a href="/display.php?frm_action=list&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=localhost&frm_o_o[1][class]=lxguard&frm_o_cname=lxguardhitdisplay">Connections</a></li>
						<li><a href="/display.php?frm_action=list&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=localhost&frm_o_o[1][class]=lxguard&frm_o_cname=rawlxguardhit">Raw Connections</a></li>
						<li><a href="/display.php?frm_action=list&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=localhost&frm_o_o[1][class]=lxguard&frm_o_cname=lxguardwhitelist">White List</a></li>
					</ul>
				</li>

				<li><a href="/display.php?frm_action=list&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_cname=hostdeny">Blocked Host</a></li>
				<li><a href="/display.php?frm_action=list&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_cname=sshauthorizedkey">SSH Authorized Keys</a></li>
			</ul>
		</li>

		<li class="top"><a class="top_link" href="#">Hosting</a>
			<ul class="sub">
				<li><a class="fly" href="#">PHP</a>
					<ul>
						<li><a href="/display.php?frm_action=show&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_o[1][class]=phpini">PHP Config</a></li>
						<li><a href="/display.php?frm_action=updateform&frm_subaction=extraedit&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_o[1][class]=phpini">Addvanced PHP Config</a></li>
					</ul>
				</li>

				<li><a class="fly" href="#">Web</a>
					<ul>
						<li><a href="/display.php?frm_action=show&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_o[1][class]=serverweb">Webserver Config</a></li>
					</ul>
				</li>

					<li><a class="fly" href="#">FTP</a>
						<ul>
							<li>
								<a href="/display.php?frm_action=show&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_o[1][class]=serverftp">FTP
									Config</a></li>
						</ul>
					</li>

					<li><a class="fly" href="#">Mail</a>
						<ul>
							<li>
								<a href="/display.php?frm_action=updateform&frm_subaction=update&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_o[1][class]=servermail">Server
									Mail Settings</a></li>
							<li>
								<a href="/display.php?frm_action=updateform&frm_subaction=spamdyke&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_o[1][class]=servermail">Spamdyke</a>
							</li>
							<li>
								<a href="/display.php?frm_action=list&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_o[1][class]=servermail&frm_o_cname=mail_graylist_wlist_a">Whitelist
									IPs</a></li>
							<li>
								<a href="/display.php?frm_action=list&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_cname=mailqueue">Mail
									Queue</a></li>
							<li>
								<a href="/display.php?frm_action=list&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_cname=clientmail">Mails
									Per Client</a></li>
						</ul>
					</li>

					<li><a class="fly" href="#">Database</a>
						<ul>
							<li>
								<a href="/display.php?frm_action=updateform&frm_subaction=mysqlpasswordreset&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>">Mysql
									Password Reset</a></li>
							<li>
								<a href="/display.php?frm_action=list&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_cname=dbadmin">List
									Database Admins</a></li>
							<li>
								<a href="/display.php?frm_action=addform&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_cname=dbadmin">Add
									Database Admin</a></li>
						</ul>
					</li>
				</ul>
			</li>
<?php
}
?>
		<li class="top"><a class="top_link" href="#">Task</a>
			<ul class="sub">
				<li><a class="fly" href="#">Domain/Subdomain</a>
					<ul>
						<li><a href="/display.php?frm_action=show&frm_o_o[0][class]=ffile&frm_o_o[0][nname]=/">Default
								Domain</a></li>
						<li><a href="/display.php?frm_action=list&frm_o_cname=domain">Add Domain</a></li>
						<li><a href="/display.php?frm_action=list&frm_o_cname=subdomain">Add Subdomain</a></li>
					</ul>
				</li>

				<li><a class="fly" href="#">Ftp User</a>
					<ul>
						<li><a href="/display.php?frm_action=list&frm_o_cname=ftpuser">List Ftp User</a></li>
						<li><a href="/display.php?frm_action=addform&frm_o_cname=ftpuser">Add Ftp User</a></li>
					</ul>
				</li>

				<li><a class="fly" href="#">Mail</a>
					<ul>
						<li><a href="/display.php?frm_action=list&frm_o_cname=mailaccount">Add Mail Account</a></li>
					</ul>
				</li>

				<li><a class="fly" href="#">Database</a>
					<ul>
						<li><a href="/display.php?frm_action=list&frm_o_cname=ftpuser">List MySQL Database</a></li>
						<li><a href="/display.php?frm_action=addform&frm_o_cname=mysqldb">Add MySQL Database</a></li>
					</ul>
				</li>
<?php
if ($userid == 'admin') {
?>
					<li><a class="fly" href="#">Client</a>
						<ul>
							<li><a href="/display.php?frm_action=list&frm_o_cname=client">List Client</a></li>
							<li>
								<a href="/display.php?frm_action=addform&frm_dttype[var]=cttype&frm_dttype[val]=wholesale&frm_o_cname=client">Add
									Wholesale Reseller</a></li>
							<li>
								<a href="/display.php?frm_action=addform&frm_dttype[var]=cttype&frm_dttype[val]=reseller&frm_o_cname=client">Add
									Reseller</a></li>
							<li>
								<a href="/display.php?frm_action=addform&frm_dttype[var]=cttype&frm_dttype[val]=customer&frm_o_cname=client">Add
									Customer</a></li>
							<li><a href="/display.php?frm_action=list&frm_o_cname=all_client&frm_filter[view]=quota">Quota
									View</a></li>
						</ul>
					</li>
<?php

}
?>
				<li><a class="fly" href="#">Cron Task</a>
					<ul>
						<li><a href="/display.php?frm_action=list&frm_o_cname=cron">List Cron Scheduled Task</a></li>
						<li>
							<a href="/display.php?frm_action=addform&frm_dttype[var]=ttype&frm_dttype[val]=simple&frm_o_cname=cron">Add
								Simple Cron Task</a></li>
						<li>
							<a href="/display.php?frm_action=addform&frm_dttype[var]=ttype&frm_dttype[val]=complex&frm_o_cname=cron">Add
								Standard Cron Task</a></li>
					</ul>
				</li>

				<li><a class="fly" href="#">File Manager</a>
					<ul>
						<li>
							<a href="/display.php?frm_action=show&frm_o_o[0][class]=pserver&frm_o_o[0][nname]=<?= $syncserver ?>&frm_o_o[1][class]=ffile&frm_o_o[1][nname]=">To
								Server Root Directory</a></li>
						<li>
							<a href="/display.php?frm_action=show&frm_o_o[0][class]=client&frm_o_o[0][nname]=<?= $userid ?>&frm_o_o[1][class]=ffile&frm_o_o[1][nname]=/">To
								User Home Directory</a></li>
					</ul>
				</li>

				<li>
					<a href="/display.php?frm_action=updateform&frm_subaction=skin&frm_o_o[0][class]=client&frm_o_o[0][nname]=<?= $userid ?>&frm_o_o[1][class]=sp_specialplay">Appearance</a>
				</li>
			</ul>
		</li>
	</ul>
</div>
<div style="float:right">
	<div style="float:left">
		<ul id="nav">
			<li class="top"><a class="top_link" href="#"
			  onMouseOver="document.getElementById('infomsg').style.display='inline';"
			  onMouseOut="document.getElementById('infomsg').style.display='none';">Help</a></li>
		</ul>
	</div>
	<div style="float:right">
		<ul id="nav">
			<li class="top"><a class="top_link"
			  href="javascript:if (confirm('Do You Really Want To Logout?')) { location = '/lib/php/logout.php'; }">Logout</a>
			</li>
		</ul>
	</div>
</div>

</div>
