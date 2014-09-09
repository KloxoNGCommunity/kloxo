#!/usr/bin/perl
# Title:    vpopmail_fail2drop.pl
# Version:  1.0
# Author:   john at stilen.com
# Purpouse: Block bruteforce attempts on vpopmail authentication
# License:  LGPL
#
# How It Works: 
#  For Vpopmail with mysql backend which records logon attempts.
#  Script parses logon attempts over a window of time,
#  counts number of fails from a remote host,
#  and creates iptables drop rule.
#
# Database:
#  vpopmail, table:vlog, columns:error,remoteip
#  SELECT * FROM vlog WHERE timestamp >= UNIX_TIMESTAMP(NOW() - INTERVAL ? HOUR));
#
# Iptables Prerequisit:
#  make a chain that logs and drops
#    iptables -N LOGDROPVPOP
#    iptables -A LOGDROPVPOP -j LOG --log-prefix "VPOPMAIL DROP: "
#    iptables -A LOGDROPVPOP -j DROP
#
# Logging:
#  logger writes to syslog.
#  Tags entry with VPOPMAIL
#
# Debugging:
#    1. Turn on debugging with $debug="1" below, and run in a console.
#    2. Most steps print out a result 
#    3. No actual action taken
#
# Mod History:
#    20120905: Initial creation.
#
########################
# Load Modules 
use strict;          # Keeps me honest
use Data::Dumper;    # Helps debug datastructures
#use POSIX;          # May need for a date
use DBI;             # Conneting to database
use IPTables::Parse; # Parsing Iptables

############################
# Debug (0 is off, 1 is on)
my $debug = 0;

#############################
# Mysql: debug level
DBI->trace( $debug );

#############################
if( $debug == 1 ){ print "# Set Path to iptables\n"; }
my $ipt_bin = '/sbin/iptables'; # can set this to /sbin/ip6tables

#############################
if( $debug == 1 ){ print "# Get a date\n"; }
my $EventDate;
open (DATE, "date|") || die "\tCould not run date:\t$?\n";
chomp($EventDate=<DATE>);
close (DATE);
if( $debug == 1 ){  print "#\tDATE: $EventDate\n";  }

#########################
if( $debug == 1 ){ print "# Set Variables for MYSQL: meyer_sound database\n"; }
my	  $host='localhost';
my	   $dbd='mysql';
my	    $db='vpopmail';
my     $db_user='vpop_admin';
my $db_password='No_Good_Pass';	 
my $dbh = DBI->connect("dbi:$dbd:$db:$host","$db_user","$db_password")
    || err_trap("Cannot connect to the database:\t$!\n");

#########################
if( $debug == 1 ){ print "# Create Variables to Hold brute force source\n"; }
my %brute;
my $href_brute = \%brute;

#########################
if( $debug == 1 ){ print "# Set window for evaluation\n"; }
my $window = 10; # in HOURS
my $logon_limit = 20; # 10 bad logins

#########################
if( $debug == 1 ){ print "# Create Variable to SELECT list of burtes\n"; }
my $select_old_and_new =q(SELECT * FROM vlog WHERE timestamp >= UNIX_TIMESTAMP(NOW() - INTERVAL ? HOUR));
if( $debug == 1 ){ print "# \tSQL: select_old_and_new=$select_old_and_new"; }

#########################
if( $debug == 1 ){ print "# Initiate the select \n"; }
my $sth = $dbh->prepare( $select_old_and_new );
$sth->execute(
    $window
)|| die "Mysql Error:\t$!\n";

#########################
if( $debug == 1 ){ print "# Set counter to record all transactions over window\n"; }
my $reviewed_counter = 0;

#########################
if( $debug == 1 ){ print "# Process response\n"; }
while(  my $hash_ref = $sth->fetchrow_hashref() ) {
    #-------------------------------------
    # Increment counter
    $reviewed_counter++;
    
    #-------------------------------------
    # Print every reviewed attempt
    if ( $debug == 1 ){
	print "#\t$reviewed_counter:\t"
	    ."ip:".$$hash_ref{"remoteip"}
	    .",error:".$hash_ref->{"error"}
	    .",user:".$hash_ref->{"user"}
	    .",logon:".$hash_ref->{"logon"}
	    .",message:".$hash_ref->{"message"}
	    ."\n";
    }
    
    #-------------------------------------
    # Collect hash of uncuccessful login and count.
    if( $hash_ref->{"error"}  != 2 ){
        if ( exists $href_brute->{ $hash_ref->{"remoteip"} }){
            $href_brute->{ $hash_ref->{"remoteip"} }++;
        } else {
            $href_brute->{ $hash_ref->{"remoteip"} } = 1;
        }
	if( $debug == 1 ){
	    print "#\t\tBrute counter:".$href_brute->{ $hash_ref->{"remoteip"}}."\n";
	}
    }
}
#########################
if( $debug == 1 ){ print "# Disconnect from database.\n"; }
$dbh->disconnect();

##########################
if ( $debug == 1 ){ print "# Lets see what we have\n";
    print "# Reviewed:$reviewed_counter\n"; 
    if (scalar %$href_brute) {
        print "#\tFound Brutes:\n";
	for my $key (keys %$href_brute) {
	    print "#\t$key:$$href_brute{$key}\n";
	}
    } else {
        print "#\tNo Brutes. Yay!\n";
    }
}

##########################
if ( $debug == 1 ){ print "# Drop these brutes\n"; }
for my $key (keys %$href_brute) {
    if ( $$href_brute{$key} > $logon_limit ){
        
	########################
	# Check if rule exists
	# return 0 if present.
	# return 1 if not present
	my $need_rule = &have_rule(\$key);
	if ( $need_rule == 1 ){
	    if( $debug == 1 ){ print "#\t\tMake Rule\n"; }
	} else {
	    if( $debug == 1 ){ print "#\t\tHave Rule Already\n"; }
	}
	########################
	# Make rule
	if ( $need_rule == 1){
	    &make_rule(\$key);
	    &log_rule(\$key,\$$href_brute{$key});
	} else {
	    if ( $debug == 1 ){ print "No rule needed\n"; }
	}
    }
}

##########################
if ( $debug == 1 ){ print "# Finished Processing VPOPMAIL logins.\n"; }

##########################
sub have_rule(){
    my $ip = shift();
    if ( $debug == 1 ){ print "#\tLook for existing rule:$$ip\n"; }

    my %opts = (
      'iptables' => $ipt_bin,
      'iptout'   => 'iptables.out',
      'ipterr'   => 'iptables.err',
      'debug'    => 0,
      'verbose'  => 0
    );
    my $ipt_obj = new IPTables::Parse(%opts)
        or die "[*] Could not acquire IPTables::Parse object";
    my $table = 'filter';
    my $chain = 'INPUT';
    my $ipt_aref = $ipt_obj->chain_rules($table, $chain);

    if( $debug == 1 ){ print Dumper($ipt_aref); }
    
    for my $rule (@$ipt_aref){
        if( $$rule{'target'} =~ m/LOGDROPVPOP/){
	    if ( $$rule{'raw'} =~ m/$$ip/ ){	
		if( $debug == 1 ){
        	    print "#\t\tFound Rule: $$ip\n";
		}
		return 0;
	    }
	}
    }
    if( $debug == 1 ){
        print "#\t\tNo Rule:$$ip\n";
    }
    return 1;
}
##########################
sub make_rule(){
    my $ip = shift();
    if ( $debug == 1 ){ print "#\tMake rule for:$$ip\n"; }

    # Make rule string
    my $drop_rule = "/sbin/iptables -I INPUT -s $$ip -j LOGDROPVPOP";

    if ( $debug == 1 ){ 
        print "#\tDebug On: not rule made!\n";
        print "#\t\tRule: $drop_rule\n";
    } else {
        open( DROP_RULE, "$drop_rule|") || die "Can't run drop rule:$!\n";
        while(<DROP_RULE>){
            print "\t$_\n";
        }
        close(DROP_RULE)|| die "Can't end drop rule:$!\n";
    }        
    return;	
}
##########################
sub log_rule(){
    my $ip = shift();
    my $attempts = shift();
    if ( $debug == 1 ){ print "#\tLog rule for:$$ip\n"; }

    ########################
    # Log it	
    my $log_message = "Drop IP:$$ip\tAttempts:$$attempts";	
    if ( $debug == 1 ){
	 print "#\tDebug On: not log made!\n";
	 print "#\t\tlog: $log_message\n";
    } else {
	open( LOG, "logger -t VPOPMAIL $log_message|") || die "Can't log drop rule:$!\n";
	while(<LOG>){
	    print "\t$_\n";
	}
	close(LOG)|| die "Can't end drop rule:$!\n";
    }
    return;
}
