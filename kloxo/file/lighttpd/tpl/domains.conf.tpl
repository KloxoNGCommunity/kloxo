<?php
$altconf = "/opt/configs/lighttpd/conf/customs/{$domainname}.conf";

if (file_exists($altconf)) {
	print("## MR - Use '{$altconf}' instead this file");
	return;
}
?>
### begin - web of '<?= $domainname; ?>' - do not remove/modify this line

<?php

$altconf = "/opt/configs/lighttpd/conf/customs/{$domainname}.conf";

if (file_exists($altconf)) {
	print("## MR - Use {$altconf} instead this file");
	return;
}

$webdocroot = $rootpath;

if (!isset($phpselected)) {
	$phpselected = 'php';
}

if (!isset($timeout)) {
	$timeout = '300';
}

if (($webcache === 'none') || (!$webcache)) {
	$ports[] = '80';
	$ports[] = '443';
} else {
	$ports[] = '8080';
	$ports[] = '8443';
}

foreach ($certnamelist as $ip => $certname) {
	$cert_ip = $ip;

	$sslpath = "/home/kloxo/ssl";

	if (file_exists("{$sslpath}/{$domainname}.key")) {
		$cert_file = "{$sslpath}/{$domainname}";
	} else {
		$cert_file = "{$sslpath}/{$certname}";
	}

}

$statsapp = $stats['app'];
$statsprotect = ($stats['protect']) ? true : false;

$tmpdom = str_replace(".", "\.", $domainname);

$excludedomains = array("cp", "webmail", "mail");

$excludealias = implode("|", $excludedomains);

$serveralias = '';

if ($wildcards) {
	$serveralias .= "(?:^|\.){$tmpdom}$";
} else {
	if ($wwwredirect) {
		$serveralias .= "^(?:www\.){$tmpdom}$";
	} else {
		$serveralias .= "^(?:www\.|){$tmpdom}$";
	}
}

if ($serveraliases) {
	foreach ($serveraliases as &$sa) {
		$tmpdom = str_replace(".", "\.", $sa);
		$serveralias .= "|^(?:www\.|){$tmpdom}$";
	}
}

if ($parkdomains) {
	foreach ($parkdomains as $pk) {
		$pa = $pk['parkdomain'];
		$tmpdom = str_replace(".", "\.", $pa);
		$serveralias .= "|^(?:www\.|){$tmpdom}$";
	}
}

if ($webmailapp) {
	if ($webmailapp === '--Disabled--') {
		$webmaildocroot = "/home/kloxo/httpd/disable";
	} else {
		$webmaildocroot = "/home/kloxo/httpd/webmail/{$webmailapp}";
	}
} else {
	$webmaildocroot = "/home/kloxo/httpd/webmail";
}

$webmailremote = str_replace("http://", "", $webmailremote);
$webmailremote = str_replace("https://", "", $webmailremote);

if ($indexorder) {
	$indexorder = implode(' ', $indexorder);
}

$indexorder = '"' . $indexorder . '"';
$indexorder = str_replace(' ', '", "', $indexorder);

if ($blockips) {
	$biptemp = array();
	foreach ($blockips as &$bip) {
		if (strpos($bip, ".*.*.*") !== false) {
			$bip = str_replace(".*.*.*", ".0.0/8", $bip);
		}
		if (strpos($bip, ".*.*") !== false) {
			$bip = str_replace(".*.*", ".0.0/16", $bip);
		}
		if (strpos($bip, ".*") !== false) {
			$bip = str_replace(".*", ".0/24", $bip);
		}
		$biptemp[] = $bip;
	}
	$blockips = $biptemp;

	$blockips = implode('|', $blockips);
}

$userinfo = posix_getpwnam($user);

if ($userinfo) {
	$fpmport = (50000 + $userinfo['uid']);
} else {
	return false;
}

// MR -- for future purpose, apache user have uid 50000
// $userinfoapache = posix_getpwnam('apache');
// $fpmportapache = (50000 + $userinfoapache['uid']);
$fpmportapache = 50000;

if ($reverseproxy) {
	$lighttpdextratext = null;
}

$disabledocroot = "/home/kloxo/httpd/disable";
$cpdocroot = "/home/kloxo/httpd/cp";

if ($statsapp === 'webalizer') {
	$statsdocroot = "/home/httpd/{$domainname}/webstats";
} else {
	$statsdocroot_base = "/home/kloxo/httpd/awstats/wwwroot";
	$statsdocroot = "{$statsdocroot_base}/cgi-bin";
}

$globalspath = "/opt/configs/lighttpd/conf/globals";

$acmechallenge_conf = getLinkCustomfile($globalspath, "acme-challenge.conf");

$gzip_base_conf = getLinkCustomfile($globalspath, "gzip.conf");

$generic_conf = getLinkCustomfile($globalspath, "generic.conf");

if ($general_header) {
	$gh = explode("\n", trim($general_header, "\n"));

	$general_header_text = "";

	foreach ($gh as $k => $v) {
		list($key, $value) = explode(" \"", $v);
		$general_header_text .= "\tsetenv.add-response-header += ( \"{$key}\" => \"{$value} )\n";
	}
}

if ($https_header) {
	$hh = explode("\n", trim($https_header, "\n"));

	$https_header_text = "";

	foreach ($hh as $k => $v) {
		list($key, $value) = explode(" \"", $v);
		$https_header_text .= "\t\tsetenv.add-response-header += ( \"{$key}\" => \"{$value} )\n";
	}
}

if (intval($static_files_expire) > -1) {
	$static_files_expire_text = "\t\$HTTP[\"url\"] =~ \".(jpe?g|gif|png|ico|css|pdf|js)\" {\n" .
		"\t\texpire.url = ( \"\" => \"access plus {$static_files_expire} days\" )\n" .
		"\t}";
} else {
	$static_files_expire_text = '# No static files expire';
}

if ($disabled) {
	$sockuser = 'apache';
} else {
	$sockuser = $user;
}

if ($disabled) {
	$cpdocroot = $statsdocroot = $webmaildocroot = $webdocroot = $disabledocroot;
}

?>

## cp for '<?=$domainname;?>'
$HTTP["host"] =~ "^cp\.<?=str_replace(".", "\.", $domainname);?>" {

	server.follow-symlink = "disable"

	include "<?=$acmechallenge_conf;?>"

<?=$general_header_text;?>

	var.user = "apache"
	var.fpmport = "<?=$fpmportapache;?>"
	var.rootdir = "<?=$cpdocroot;?>/"
	var.phpselected = "php"
	var.timeout = "<?=$timeout;?>"

	server.document-root = var.rootdir

	index-file.names = ( <?=$indexorder;?> )

	#include "<?=$globalspath;?>/switch_standard.conf"
	include "<?=$globalspath;?>/php-fpm_standard.conf"

}


## stats for '<?=$domainname;?>'
$HTTP["host"] =~ "^stats\.<?=str_replace(".", "\.", $domainname);?>" {

	server.follow-symlink = "disable"

	include "<?=$acmechallenge_conf;?>"

<?=$general_header_text;?>

	var.domain = "stats.<?=$domainname;?>"
	var.user = "apache"
	var.fpmport = "<?=$fpmportapache;?>"
	var.rootdir = "<?=$statsdocroot;?>/"
	var.phpselected = "php"
	var.timeout = "<?=$timeout;?>"

	server.document-root = var.rootdir

	index-file.names = ( <?=$indexorder;?> )
<?php
if ($enablestats) {
//	if ((!$reverseproxy) || (($reverseproxy) && ($webselected === 'front-end'))) {
?>

	include "<?=$globalspath;?>/stats.conf"
<?php
		if ($statsprotect) {
?>

	include "<?=$globalspath;?>/dirprotect_stats.conf"
<?php
		}
//	}
}
?>

}

<?php
if ($webmailremote) {
?>

## webmail for '<?=$domainname;?>'
$HTTP["host"] =~ "^webmail\.<?=str_replace(".", "\.", $domainname);?>|^mail\.<?=str_replace(".", "\.", $domainname);?>" {

	url.redirect = ( "/" =>  "http://<?=$webmailremote;?>/" )

}

<?php
} else {
?>

## webmail for '<?=$domainname;?>'
$HTTP["host"] =~ "^webmail\.<?=str_replace(".", "\.", $domainname);?>|^mail\.<?=str_replace(".", "\.", $domainname);?>" {

	server.follow-symlink = "disable"

	include "<?=$acmechallenge_conf;?>"

<?=$general_header_text;?>

	var.user = "apache"
	var.fpmport = "<?=$fpmportapache;?>"
	var.rootdir = "<?=$webmaildocroot;?>/"
	var.phpselected = "php"
	var.timeout = "<?=$timeout;?>"

	server.document-root = var.rootdir

	index-file.names = ( <?=$indexorder;?> )

	#include "<?=$globalspath;?>/switch_standard.conf"
	include "<?=$globalspath;?>/php-fpm_standard.conf"

}

<?php
}

if ($domainredirect) {
	foreach ($domainredirect as $domredir) {
		$redirdomainname = $domredir['redirdomain'];
		$redirpath = ($domredir['redirpath']) ? $domredir['redirpath'] : null;
		$webmailmap = ($domredir['mailflag'] === 'on') ? true : false;

		if ($redirpath) {
			if ($disabled) {
			 	$$redirfullpath = $disablepath;
		 	} else {
				$redirfullpath = str_replace('//', '/', $webdocroot . '/' . $redirpath);
			}
?>

## web for redirect '<?=$redirdomainname;?>'
$HTTP["host"] =~ "^<?=str_replace(".", "\.", $redirdomainname);?>" {

	server.follow-symlink = "disable"

	include "<?=$acmechallenge_conf;?>"
<?php
		if ((!$reverseproxy) || ($webselected === 'front-end')) {
?>

	include "<?=$gzip_base_conf;?>"
<?php
		}
?>

<?=$general_header_text;?>

	$HTTP["scheme"] == "https" {
<?=$https_header_text;?>
	}

	var.user = "<?=$sockuser;?>"
	var.fpmport = "<?=$fpmport;?>"
	var.rootdir = "<?=$redirfullpath;?>/"
	var.phpselected = "<?=$phpselected;?>"
	var.timeout = "<?=$timeout;?>"

	server.document-root = var.rootdir

	index-file.names = ( <?=$indexorder;?> )
<?php

			if ($enablephp) {
?>

	include "<?=$globalspath;?>/switch_standard.conf"
<?php
			}
?>

}

<?php
		} else {
			if ($disabled) {
				$$redirfullpath = $disablepath;
			} else {
				$redirfullpath = $webdocroot;
			}
?>

## web for redirect '<?=$redirdomainname;?>'
$HTTP["host"] =~ "^<?=str_replace(".", "\.", $redirdomainname);?>" {

	server.follow-symlink = "disable"

	var.rootdir = "<?=$redirfullpath;?>/"

	server.document-root = var.rootdir

	url.redirect = ( "/" =>  "http://<?=$domainname;?>/" )

}

<?php
		}
	}
}

if ($parkdomains) {
	foreach ($parkdomains as $dompark) {
		$parkdomainname = $dompark['parkdomain'];
		$webmailmap = ($dompark['mailflag'] === 'on') ? true : false;

		if ($webmailremote) {
?>

## webmail for parked '<?=$parkdomainname;?>'
$HTTP["host"] =~ "^webmail\.<?=str_replace(".", "\.", $parkdomainname);?>|^mail\.<?=str_replace(".", "\.", $parkdomainname);?>" {

	server.follow-symlink = "disable"

	url.redirect = ( "/" =>  "http://<?=$webmailremote;?>/" )

}

<?php

		} elseif ($webmailmap) {
			if ($webmailapp) {
?>

## webmail for parked '<?=$parkdomainname;?>'
$HTTP["host"] =~ "^webmail\.<?=str_replace(".", "\.", $parkdomainname);?>|^mail\.<?=str_replace(".", "\.", $parkdomainname);?>" {

	server.follow-symlink = "disable"

	include "<?=$acmechallenge_conf;?>"

<?=$general_header_text;?>

	var.user = "apache"
	var.fpmport = "<?=$fpmportapache;?>"
	var.rootdir = "<?=$webmaildocroot;?>/"
	var.phpselected = "php"
	var.timeout = "<?=$timeout;?>"

	server.document-root = var.rootdir

	index-file.names = ( <?=$indexorder;?> )

	include "<?=$globalspath;?>/switch_standard.conf"

}

<?php
   			 }
   		 } else {
?>

## No mail map for parked '<?=$parkdomainname;?>'

<?php
		}
	}
}

if ($domainredirect) {
	foreach ($domainredirect as $domredir) {
		$redirdomainname = $domredir['redirdomain'];
		$webmailmap = ($domredir['mailflag'] === 'on') ? true : false;

		if ($webmailremote) {
?>

## webmail for redirect '<?=$redirdomainname;?>'
$HTTP["host"] =~ "^webmail\.<?=str_replace(".", "\.", $redirdomainname);?>|^mail\.<?=str_replace(".", "\.", $redirdomainname);?>" {

	server.follow-symlink = "disable"

	url.redirect = ( "/" =>  "http://<?=$webmailremote;?>/" )

}

<?php
		} elseif ($webmailmap) {
			if ($webmailapp) {
?>

## webmail for redirect '<?=$redirdomainname;?>'
$HTTP["host"] =~ "^webmail\.<?=str_replace(".", "\.", $redirdomainname);?>|^mail\.<?=str_replace(".", "\.", $redirdomainname);?>" {

	server.follow-symlink = "disable"

	include "<?=$acmechallenge_conf;?>"

<?=$general_header_text;?>

	var.user = "apache"
	var.fpmport = "<?=$fpmportapache;?>"
	var.rootdir = "<?=$webmaildocroot;?>/"
	var.phpselected = "php"
	var.timeout = "<?=$timeout;?>"

	server.document-root = var.rootdir

	index-file.names = ( <?=$indexorder;?> )

	include "<?=$globalspath;?>/switch_standard.conf"

}

<?php
			}
		} else {
?>

## No mail map for redirect '<?=$redirdomainname;?>'

<?php
		}
	}
}

if ($ip !== '*') {
	$ipssl = "|" . $ip;
} else {
	$ipssl = "";
}

if ($wwwredirect) {
?>

## web for '<?=$domainname;?>'
$HTTP["host"] =~ "<?=$domainname;?><?=$ipssl;?>" {

	server.follow-symlink = "disable"

	url.redirect = ( "^/(.*)" => "http://www.<?=$domainname;?>/$1" )
}


## web for '<?=$domainname;?>'
$HTTP["host"] =~ "<?=$serveralias;?><?=$ipssl;?>" {

	server.follow-symlink = "disable"
<?php
} else {
?>

## web for '<?=$domainname;?>'
$HTTP["host"] =~ "<?=$serveralias;?><?=$ipssl;?>" {

	server.follow-symlink = "disable"

	include "<?=$acmechallenge_conf;?>"
<?php
		if ((!$reverseproxy) || ($webselected === 'front-end')) {
?>

	include "<?=$gzip_base_conf;?>"
<?php
		}
?>

<?=$general_header_text;?>

	$HTTP["scheme"] == "https" {
<?=$https_header_text;?>

	}
<?php
}
?>

	var.domain = "<?=$domainname;?>"
	var.user = "<?=$sockuser;?>"
	var.fpmport = "<?=$fpmport;?>"
	var.phpselected = "<?=$phpselected;?>"
	var.timeout = "<?=$timeout;?>"

	var.rootdir = "<?=$webdocroot;?>/"

	server.document-root = var.rootdir

	index-file.names = ( <?=$indexorder;?> )
<?php
if ($redirectionlocal) {
	foreach ($redirectionlocal as $rl) {
?>

	alias.url  += ( "<?=$rl[0];?>/" => "$rootdir<?=str_replace("//", "/", $rl[1]);?>" )
<?php
	}
}

if ($redirectionremote) {
	foreach ($redirectionremote as $rr) {
		if ($rr[0] === '/') {
			$rr[0] = '';
		}

		if ($rr[2] === 'both') {
?>

	url.redirect += ( "^(<?=$rr[0];?>/|<?=$rr[0];?>$)" => "http://<?=$rr[1];?>" )
<?php
		} else {
			$protocol2 = ($rr[2] === 'https') ? "https://" : "http://";
?>

	url.redirect += ( "^(/<?=$rr[0];?>/|/<?=$rr[0];?>$)" => "<?=$protocol2;?><?=$rr[1];?>" )
<?php
		}
	}
}

	if ($lighttpdextratext) {
?>

	# Extra Tags - begin
<?=$lighttpdextratext;?>

	# Extra Tags - end
<?php
	}

	if ((!$reverseproxy) && (file_exists("{$globalspath}/{$domainname}.conf"))) {
		if ($enablephp) {
?>

	include "<?=$globalspath;?>/<?=$domainname;?>.conf"
<?php
		}
	} else {
		if (($reverseproxy) && ($webselected === 'front-end')) {
			if ($enablephp) {
?>

	include "<?=$globalspath;?>/php-fpm_standard.conf"
<?php
			}
		} else {
?>

	include "<?=$globalspath;?>/switch_standard.conf"
<?php
		}
	}

	if (!$reverseproxy) {
		if ($dirprotect) {
			foreach ($dirprotect as $k) {
				$protectpath = $k['path'];
				$protectauthname = $k['authname'];
				$protectfile = str_replace('/', '_', $protectpath) . '_';
?>

	$HTTP["url"] =~ "^/<?=$protectpath;?>[/$]" {
		auth.backend = "htpasswd"
		auth.backend.htpasswd.userfile = "/home/httpd/" + var.domain + "/__dirprotect/<?=$protectfile;?>"
		auth.require = ( "/<?=$protectpath;?>" => (
		"method" => "basic",
		"realm" => "<?=$protectauthname;?>",
		"require" => "valid-user"
		))
	}
<?php
			}
		}
	}

	if ($blockips) {
?>

	$HTTP["remoteip"] =~ "{<?=$blockips;?>}" {
		url.access-deny = ( "" )
	}
<?php
	}
?>

	var.kloxoportssl = "<?=$kloxoportssl;?>"
	var.kloxoportnonssl = "<?=$kloxoportnonssl;?>"

	include "<?=$generic_conf;?>"

	alias.url += ( "/" => var.rootdir )
<?php
	if ($enablecgi) {
?>

	$HTTP["url"] =~ "^/cgi-bin" {
		#cgi.assign = ( "" => "/home/httpd/" + var.domain + "/perlsuexec.sh" )
		cgi.assign = ( ".pl" => "/usr/bin/perl", ".py" => "/usr/bin/python", ".cgi" => "" )
	}
<?php
	}

	if ($enablestats) {
?>

	include "<?=$globalspath;?>/stats_log.conf"

	url.redirect += ( "^(/stats/|/stats$)" => "//stats." + var.domain + "/" )
<?php
	}
?>

	$HTTP["url"] =~ "^/" {
<?php
	if ($enablecgi) {
?>
		#cgi.assign = ( ".pl" => "/home/httpd/" + var.domain + "/perlsuexec.sh" )
		cgi.assign = ( ".pl" => "/usr/bin/perl", ".py" => "/usr/bin/python", ".cgi" => "" )
<?php
	}

	if ($dirindex) {
?>
		dir-listing.activate = "enable"
<?php
	}
?>

		## trick using 'microcache' not work; no different performance!
		#expire.url = ( "" => "access 10 seconds" )
	}

<?=$static_files_expire_text;?>


}


### end - web of '<?=$domainname;?>' - do not remove/modify this line
