<?php

$srcconfpath ="/opt/configs/varnish/etc/conf";
$trgtconfpath ="/etc/varnish";

if (file_exists("{$srcconfpath}/custom.default.vcl")) {
	copy("{$srcconfpath}/custom.default.vcl", "{$trgtconfpath}/default.vcl");
} else {
	copy("{$srcconfpath}/default.vcl", "{$trgtconfpath}/default.vcl");
}

//if (file_exists("{$srcconfpath}/custom.boosted-varnish.vcl")) {
//	copy("{$srcconfpath}/custom.boosted-varnish.vcl", "{$trgtconfpath}/boosted-varnish.vcl");
//} else {
//	copy("{$srcconfpath}/boosted-varnish.vcl", "{$trgtconfpath}/boosted-varnish.vcl");
//}

$srcsyspath ="/opt/configs/varnish/etc/sysconfig";
$trgtsyspath ="/etc/sysconfig";

if (file_exists("{$srsyspath}/custom.varnish")) {
	copy("{$srsyspath}/custom.varnish", "{$trgtsyspath}/varnish");
} else {
	copy("{$srsyspath}/varnish", "{$trgtsyspath}/varnish");
}
?>