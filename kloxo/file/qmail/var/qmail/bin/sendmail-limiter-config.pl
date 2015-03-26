#!/usr/bin/perl

### --CONFIGURATION

# DB information
$db_name='DBI:mysql:sendmailwrapper:localhost:3306';
$db_uid='sendmailwrapper';
$db_pwd='zpKHCVirJKe';

### deliver report from cron about usage violations
# 0 = no reports sent
# 1 = reporting on, be sure to set report_email
$send_reports = 1;
$report_email = 'email@wp.pl';

### 1 = disable all logging of sendmail
$log_disabled = 0;

### production mode
# 0  = bypass limiting
# 1+ = limiting per uid on
$limiter=1;

$logfile="/var/log/sendmail-limits.log";

### per hour group limits
# please read sendmail-chrisf.txt for description
# all users default to group one (1)

@sm_max = (100, 100,  500,  1500, 4500, 8000 );
