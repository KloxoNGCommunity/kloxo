<?php

include_once "lib/html/include.php";
initProgram('admin');

$login->loadAllObjects('client');
$list = $login->getList('client');

$cron = '';

exec("'rm' -f /etc/cron.d/backup*");

log_log('cron_backup', "*** Fixing Cron Backup *** \n");
echo("*** Fixing Cron Backup *** \n");

foreach($list as $c) {
	$user = $c->nname;

	if ($user === 'rpms') { continue; }
	if ($user === 'backuper') { continue; }
	
	$input = db_get_value('lxbackup', "client-{$user}", array('backupschedule_type', 'backupschedule_time'));

	if (!$input) { continue; }

	if ((!$input['backupschedule_type']) || ($input['backupschedule_type'] === 'disabled')) {
		log_log('cron_backup', "- No cron backup for {$user} because 'disabled'\n");
		echo("- No cron backup for {$user} because 'disabled'\n");
		continue;
	}

	$type = $input['backupschedule_type'];
	$time = $input['backupschedule_time'];

	if (!$time) {
		if ($user === 'admin') {
			$time = 6;
		} else {
			$time = 18;
		}
	}

	$command = "sh /script/backup --class=client --name={$user}";

	switch ($type) {
		case 'monthly' :
			$schedule = "0 {$time} * 1 *";
			break;
		case 'weekly' :
			$schedule = "0 {$time} * * 1";
			break;
		case 'daily' :
			$schedule = "0 {$time} * * *";
			break;
	}

	$cron = $schedule . " root " . $command . " >> /dev/null 2>&1\n";

	$file = "/etc/cron.d/backup_{$user}";
	file_put_contents($file, $cron);

	echo("- Cron backup for {$user} in '{$file}'\n");
}