#!/usr/bin/perl -w

# ------------------------------------------------------------------------------
# "THE BEER-WARE LICENSE" (Revision 42):
# <giversen@contix.dk> wrote this file. As long as you retain this notice you
# can do whatever you want with this stuff. If we meet some day, and you think
# this stuff is worth it, you can buy me a beer in return. Anders Giversen
# ------------------------------------------------------------------------------

use Net::DNS;
use strict;


my $DEBUG = 0;

my $MY_IP = "";


sub error {
	print STDERR "$_[0]\n";
	exit 1;
}


if (! @ARGV) {
	print "$0: Use $0 zone slave\n";
	exit 1;
}

my $zone = shift;

my $res = new Net::DNS::Resolver;
$res->srcaddr($MY_IP);    # Sets the source address from which we send queries

for my $slave (@ARGV) {
	my $packet = new Net::DNS::Packet($zone, "SOA", "IN") or error("new Net::DNS::Packet failed");
	$packet->header->opcode("NS_NOTIFY_OP");    # Sets the query opcode (the purpose of the query)
  	$packet->header->aa(1);    # Sets the authoritative answer flag
	$packet->header->rd(0);    # Sets the recursion desired flag
	
	if ($DEBUG) {
		print "Packet:\n";
		$packet->print;
	}
	
	# Specify which name server to use
	$res->nameservers($slave);
	
	my $reply = $res->send($packet);
	if (defined $reply ) {
		if ($DEBUG) {
			print "\nAnswer:\n";
			$reply->print;
		}

		print "Received NOTIFY answer from " . $reply->answerfrom . " for " . $zone ."\n";

	} else {
		print "TIMED OUT\n" if $DEBUG;
	}
}

exit 0;