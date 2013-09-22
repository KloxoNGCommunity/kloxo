#!/usr/bin/perl -w

# usage: dnsnotify zone slave [...]
# example: dnsnotify example.org 1.2.3.4 1.2.3.5

use Net::DNS;

$zone = shift;
@master_ns = @ARGV;

$res = new Net::DNS::Resolver;

foreach $ns (@master_ns) {
	$packet = new Net::DNS::Packet($zone, "SOA", "IN");
	die unless defined $packet;

	($packet->header)->opcode("NS_NOTIFY_OP");
	($packet->header)->rd(0);
	($packet->header)->aa(1);

	$res->nameservers($ns);

	# Prints outgoing packet - the NOTIFY
	# $packet->print;

	$reply = $res->send($packet);

	if (defined $reply) {
		
			print "Received NOTIFY answer from " . $reply->answerfrom . "\n";
			# Print received packet - the answer
			# $reply->print;
	
	} else {
	
		warn "\$res->send indicates NOTIFY error for $ns\n";
	}
}

exit 0;
