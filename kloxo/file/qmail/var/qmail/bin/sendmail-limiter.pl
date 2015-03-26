#!/usr/bin/perl

# use strict;
use Env;
use DBI;

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

### MR -- action

$arg_str = join(' ',@ARGV,' '); $arg_str =~ s/\s{2,}/ /g;
my $date = `date -R`; chomp $date;
my $uid  = $>; my @info = getpwuid($uid); my $status = 'OK';
if ($info[0] eq 'root' or $info[0] eq 'lxlabs') { $limiter = 0; }

if ($limiter > 0) {
    my $datetime = `date "+%Y-%m-%d %H:%M:%S"`;
    my $dbase = DBI->connect($db_name, $db_uid, $db_pwd);
    if (!defined $dbase) { die 'Connection to database failed.'; }
    
    my $limits = $dbase->prepare("SELECT sm_group, sm_ignore, count FROM client_sendmail WHERE client_uid = '$uid'");
    $limits->execute;
    if ($row = $limits->fetchrow_arrayref) {
      $sm_group = $row->[0]; $sm_ignore = $row->[1];
      $count_cur = ($row->[2]+1); $count_max = $sm_max[$sm_group-1];
      my $LimitUpdate = $dbase->prepare("UPDATE client_sendmail SET count = (count + 1), last_request = '$datetime' WHERE client_uid = '$uid'");
      $LimitUpdate->execute;
      if ($sm_group > 6) { $status = 'BANNED'; }
      if ($count_cur > $count_max) { $status = 'DROPPED' if ($sm_ignore < 1); }
    } else {
      my $LimitInsert = $dbase->prepare("INSERT INTO client_sendmail (client_uid, client_name, last_request) VALUES ('$uid', '$info[0]', '$datetime')");
      $LimitInsert->execute;
    }
}

if ($log_disabled < 1) {
    open(LOG,">>$logfile") || die "Can't append to logfile $logfile:\n $!\n";
    print LOG "[$date] $arg_str - $info[0]:$uid $count_cur/$count_max ($status)\n";
}

close(LOG) if ($log_level > 0);
if ($status ne 'OK') { die; }
exit(0);
