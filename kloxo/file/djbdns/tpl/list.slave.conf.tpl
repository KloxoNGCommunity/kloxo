<?php
	if (file_exists("/opt/djbdns/bin/axfr-get")) {
		$axfr_get = "/opt/djbdns/bin/axfr-get"; 
	} else {
		$axfr_get = "/bin/axfr-get"; 
	}

	$spath = "/opt/configs/dnsslave_tmp";
	$dirs = glob("{$spath}/*");

	$tpath = "/opt/configs/djbdns/conf/slave";

	exec("'rm' -rf {$tpath}/*");

	foreach ($dirs as $d) {
		$c = trim(file_get_contents($d));
		$d = str_replace("{$spath}/", "", $d);

		touch("{$tpath}/{$d}");

		exec("tcpclient -v {$c} 53 {$axfr_get} {$d} {$tpath}/{$d} {$tpath}/{$d}.tmp 2>&1");
	}

	$datadir = "/opt/configs/djbdns/tinydns/root";
	$datafile = "{$datadir}/slave";

	$dirs = glob("{$tpath}/*");

//	exec("echo '' > {$datafile}");
	exec("'rm' -f {$datafile}");

	foreach ($dirs as $d) {
		$n = str_replace("{$datafile}/", "", $d);
		$e  = "### begin - dns of '{$n}' - do not remove/modify this line\n\n";
		$e .= file_get_contents($d);
		$e .= "\n### en - dns of '{$n}' - do not remove/modify this line\n\n";

		exec("echo '{$e}' >> {$datafile}");
	}

