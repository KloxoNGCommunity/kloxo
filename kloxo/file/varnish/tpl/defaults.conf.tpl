<?php

$spath ="/opt/configs/varnish/etc/conf";
$tpath ="/etc/varnish";

$sfile = getLinkCustomfile($spath, "default.vcl");
copy($sfile, "{$tpath}/default.vcl");

// $bfile = getLinkCustomfile($spath, "boosted-varnish.vcl");
// copy($bfile, "{$tpath}/boosted-varnish.vcl);

$sypath ="/opt/configs/varnish/etc/sysconfig";
$typath ="/etc/sysconfig";

$syfile = getLinkCustomfile($sypath, "varnish");
copy($syfile, "{$typath}/varnish");
?>