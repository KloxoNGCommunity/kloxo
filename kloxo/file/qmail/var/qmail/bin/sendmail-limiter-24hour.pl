#!/usr/bin/perl

use DBI;
use Mail::Send;

require "/var/qmail/bin/sendmail-limiter-config.pl";

my $dbase = DBI->connect($db_name, $db_uid, $db_pwd);
if (!defined $dbase) { die 'Connection to database failed.'; }

my @reports;

$query = $dbase->prepare("SELECT client_uid, client_name, sm_group, count, last_request FROM `client_sendmail` WHERE (sm_group = 5 AND count > $sm_max[4]) OR (sm_group = 6 AND count > $sm_max[5]);"); 
if (!defined $query) { die 'MySQL select: prepare statement failed.'; }
$query->execute;
while ($row = $query->fetchrow_arrayref) {
    $clientUid     = $row->[0];
    $clientName    = $row->[1];
    $sm_g          = $row->[2];
    $countCur      = $row->[3];
    $lastRequest   = $row->[4]; 
    
    push @reports, "$sm_g:$clientName:$clientUid ($countCur/$sm_max[$sm_g-1]) - $lastRequest";
}

if ((@reports) && ($send_reports > 0)) {
    $message = "The following clients have violated sender limits:\r\n(24 Hour ~ groups 5 and 6)\r\n";
    $message .= "\r\n";
    foreach(@reports) {
        $message .= "$_\r\n";
    }

    $msg = Mail::Send->new();
    $msg->to($report_email);
    $msg->subject('Mail limit report');
    my $fh = $msg->open('sendmail') || die $!;
    print $fh $message;
    $fh->close() || die $!;
}


$update = $dbase->prepare("UPDATE client_sendmail SET count = 0 WHERE sm_group = 5 OR sm_group = 6;");
if (!defined $update) { die 'MySQL update: prepare statement failed.'; }
$update->execute;

