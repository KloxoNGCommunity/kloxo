<?php 

include_once "lib/html/include.php";

initProgram('admin');

$kloxo_file_path = $sgbl->__path_program_root . "/file/ssl";
$kloxo_ssl_path = "/home/kloxo/ssl";
$kloxo_etc_path = $sgbl->__path_program_root . "/etc";

$list = lscandir_without_dot_or_underscore($kloxo_ssl_path);

foreach($list as $l) {
	if (cse($l, ".crt")) {
		$newlist[] = basename($l, ".crt");
	} else {
		continue;
	}
}

// MR -- using exec because lxshell_return not work!
exec("cat {$kloxo_file_path}/default.key {$kloxo_file_path}/default.crt " .
	"> {$kloxo_file_path}/default.pem");

// MR -- not use .ca because trouble with hiawatha and already expired
foreach($newlist as $n) {
	// MR -- bypass because letsencrypt
	if (is_link("{$kloxo_ssl_path}/$n.pem")) { continue; }

	// MR -- bypass because possible add from file/text
	if (file_exists("{$kloxo_ssl_path}/$n.ca")) { continue; }

	print("- Processing for '$n' ssl files\n");

	$list = array('key', 'crt', 'ca', 'pem');

	foreach ($list as $k => $v) {
		if (file_exists("{$kloxo_file_path}/default.{$v}")) {
			exec("'cp' -f {$kloxo_file_path}/default.{$v} {$kloxo_ssl_path}/$n.{$v}");
		}
	}
}

print("- Processing for 'program' ssl files\n");

if (!is_link("{$kloxo_etc_path}/program.pem")) {
	$list = array('key', 'crt', 'ca', 'pem');

	foreach ($list as $k => $v) {
		if (file_exists("{$kloxo_file_path}/default.{$v}")) {
			exec("'cp' -f {$kloxo_file_path}/default.{$v} {$kloxo_etc_path}/program.{$v}");
		}
	}
}

$login->loadAllObjects('sslcert');
$list = $login->getList('sslcert');

$sslpath = "/home/kloxo/ssl";
$lepath = "/etc/letsencrypt";

foreach($list as $b) {
	if (csb($b->parent_clname, 'web-')) {
		$dom = $b->nname;
		print("- Processing for '{$dom}' ssl files\n");

		// MR -- remove old data for domain in letsencrypt data
		exec("'rm' -rf {$lepath}/{live,archive,renewal}/{$dom}-*");

		exec("'rm' -f {$sslpath}/{$dom}*.{key,crt,ca,pem}");

		if ($b->parent_domain) {
			$par = $b->parent_domain;

			$list = array('key', 'crt', 'ca', 'pem');

			foreach ($list as $k => $v) {
				if (file_exists("{$sslpath}/{$par}.{$v}")) {
					exec("ln -sf {$sslpath}/{$par}.{$v} {$sslpath}/{$dom}.{$v}");
				}
			}
		} else {
			$keyc = $b->text_key_content;
			$crtc = $b->text_crt_content;
			$cac = $b->text_ca_content;

			$list = array('key' => $keyc, 'crt' => $crtc, 'ca' => $cac);

			foreach($list as $k => $v) {
					exec("echo '{$v}' >{$sslpath}/{$dom}.{$k}");
			}

			exec("echo '{$keyc}{$crtc}{$cac}' >{$sslpath}/{$dom}.pem");
		}
	}
}
