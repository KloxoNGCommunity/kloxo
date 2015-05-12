#!/usr/bin/perl

use CGI;

print "Content-type: text/plain\n\n";

%cgi = (CGI->new)->Vars;

if ( $cgi{'type'} eq "TXT" ) {
    $result = "\'" . escapeText( $cgi{'domain'} ) . ":" . escapeText( $cgi{'text'} ) . ":" . $cgi{'ttl'};
    print $result;

}
elsif ( $cgi{'type'} eq "SPF" ) {
    $result = ":" . escapeText( $cgi{'domain'} ) . ":16:" . characterCount( $cgi{'text'} ) . escapeText( $cgi{'text'} ) . ":" . $cgi{'ttl'};
    print $result;

}
elsif ( $cgi{'type'} eq "SRV" ) {
    # :sip.tcp.example.com:33:\000\001\000\002\023\304\003pbx\007example\003com\000
    if ( ( $cgi{'priority'} >= 0 && $cgi{'priority'} <= 65535 ) &&
	 ( $cgi{'weight'} >= 0 && $cgi{'weight'} <= 65535 ) &&
	 ( $cgi{'port'} >= 0 && $cgi{'port'} <= 65535 ) ) {
	$target = "";
	@chunks = split /\./, $cgi{'target'};
	foreach $chunk ( @chunks ) {
	    $target = $target . characterCount( $chunk ) . $chunk;

	}
	$result = ":" . escapeText( $cgi{'service'} ) . ":33:" . escapeNumber( $cgi{'priority'} ) . 
	    escapeNumber( $cgi{'weight'} ) . escapeNumber( $cgi{'port'} ) . 
	    $target . "\\000" . ":" . $cgi{'ttl'};
	print $result;

    }
    else {
	print "priority, weight or port not within 0 - 65535\n";

    }

}
elsif ( $cgi{'type'} eq "NAPTR" ) {
    # :comunip.com:35:\000\012\000\144\001u\007E2U+sip\036!^.*$!sip\072info@comunip.com.br!\000:300
    #                 |-order-|-pref--|flag|-services-|---------------regexp---------------|re-| 
    if ( ( $cgi{'order'} >= 0 && $cgi{'order'} <= 65535 ) &&
         ( $cgi{'prefrence'} >= 0 && $cgi{'prefrence'} <= 65535 ) ) {
        $result = ":" . escapeText( $cgi{'domain'} ) . ":35:" . escapeNumber( $cgi{'order'} ) .
            escapeNumber( $cgi{'prefrence'} ) . characterCount( $cgi{'flag'} ) . $cgi{'flag'} .
            characterCount( $cgi{'services'} ) . escapeText( $cgi{'services'} ) .
            characterCount( $cgi{'regexp'} ) . escapeText( $cgi{'regexp'} );

        if ( $cgi{'replacement'} ne "" ) {
            $result = $result . characterCount( $cgi{'replacement'} ) . escapeText( $cgi{'replacement'} );

        }
        $result = $result . "\\000:" . $cgi{'ttl'};

	print $result;

    }
    else {
        print "order or prefrence not within 0 - 65535\n";

    }

}
elsif ( $cgi{'type'} eq "domainKeys" ) {
    # :joe._domainkey.anders.com:16:\341k=rsa; p=MIGfMA0GCSqGSIb3DQ ... E2hHCvoVwXqyZ/MbQIDAQAB
    #                               |lt|  |typ|  |-key----------------------------------------|
    if ( $cgi{'key'} ne "" ) {
	$key = $cgi{'key'};
	$key =~ s/\r//g;
	$key =~ s/\n//g;
        $line = "v=DKIM; k=" . $cgi{'encryptionType'} . "; p=" . $key;
	$result = ":" . escapeText( $cgi{'domain'} ) . ":16:" . characterCount( $line ) . 
	    escapeText( $line ) . ":" . $cgi{'ttl'};
	print $result;

    }
    else {
        print "didn't get a valid key for the key field\n";

    }

}
elsif ( $cgi{'type'} eq "AAAA" ) {
    # ffff:1234:5678:9abc:def0:1234:0:0
    # :example.com:28:\377\377\022\064\126\170\232\274\336\360\022\064\000\000\000\000
    if ( $cgi{'address'} ne "" && $cgi{'domain'} ne "" ) {
        $colons = $cgi{'address'} =~ tr/:/:/;
        if ($colons < 7) { $cgi{'address'} =~ s/::/':' x (9-$colons)/e; }
	( $a, $b, $c, $d, $e, $f, $g, $h ) = split /:/, $cgi{'address'};
	if ( ! defined $h ) {
	    print "Didn't get a valid-looking IPv6 address\n";

	}
	else {
	    $a = escapeHex( sprintf "%04s", $a );
	    $b = escapeHex( sprintf "%04s", $b );
	    $c = escapeHex( sprintf "%04s", $c );
	    $d = escapeHex( sprintf "%04s", $d );
	    $e = escapeHex( sprintf "%04s", $e );
	    $f = escapeHex( sprintf "%04s", $f );
	    $g = escapeHex( sprintf "%04s", $g );
	    $h = escapeHex( sprintf "%04s", $h );
	    $result = ":" . escapeText( $cgi{'domain'} ) . ":28:" . "$a$b$c$d$e$f$g$h" . 
		":" . $cgi{'ttl'};
	    print "$result\n";

	    # now generate rfc3152 ip6.arpa reverse DNS delegation (see rfc4159 also)
	    # ffff:1234:5678:9abc:def0:1234:0:0
	    # ^0.0.0.0.0.0.0.0.4.3.2.1.0.f.e.d.c.b.a.9.8.7.6.5.4.3.2.1.f.f.f.f.ip6.arpa:example.com
            # Thanks to Matija Nalis for this and support for expanding :: notation.

	    @quads = split /:/, $cgi{'address'};
	    $reverse = 'ip6.arpa';
	    for my $n (@quads) {
	        my ($n1, $n2, $n3, $n4) = split //, sprintf ('%04s', $n);
	        $reverse = "$n4.$n3.$n2.$n1.$reverse";
	    }
	    $result = "^" . $reverse . ":" . escapeText( $cgi{'domain'} ) . ":" . $cgi{'ttl'};
	    print "$result\n";
	}

    }
    else {
        print "didn't get a valid address or domain\n";

    }

}
else {
    print "didn't get a valid record type\n";

}

sub escapeText {
    my $line = pop @_;
    my $out;
    my @chars = split //, $line;

    foreach $char ( @chars ) {
	if ( $char =~ /[\r\n\t: \\\/]/ ) {
	    $out = $out . sprintf "\\%.3lo", ord $char;

	}
	else {
	    $out = $out . $char;

	}

    }
    return( $out );

}

sub escapeNumber {
    my $number = pop @_;
    my $highNumber = 0;

    if ( $number - 256 >= 0 ) {
	$highNumber = int( $number / 256 );
	$number = $number - ( $highNumber * 256 );

    }
    $out = sprintf "\\%.3lo", $highNumber;
    $out = $out . sprintf "\\%.3lo", $number;

    return( $out );

}

sub escapeHex {
    # takes a 4 character hex value and converts it to two excaped numbers
    my $line = pop @_;
    my @chars = split //, $line;

    $out = sprintf "\\%.3lo", hex "$chars[0]$chars[1]";
    $out = $out . sprintf "\\%.3lo", hex "$chars[2]$chars[3]";

    return( $out );

}

sub characterCount {
    my $line = pop @_;
    my @chars = split //, $line;
    my $count = @chars;

    return( sprintf "\\%.3lo", $count );

}
