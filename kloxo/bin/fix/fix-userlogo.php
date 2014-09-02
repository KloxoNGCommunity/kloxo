<?php 

// by mustafa@bigraf.com for Kloxo-MR

include_once "lib/html/include.php";

initProgram('admin');

$list = parse_opt($argv);

$select = (isset($list['select'])) ? $list['select'] : 'all';
$nolog  = (isset($list['nolog'])) ? $list['nolog'] : null;

setFixUserlogo($select);

/* ****** BEGIN - setFixUserlogo ***** */

function setFixUserlogo($select)
{
	global $gbl, $sgbl, $login, $ghtml;

	log_cleanup("Fix user logo", $nolog);

	if (file_exists("/usr/local/lxlabs/kloxo/httpdocs/user-logo.png")) {
		system("\\cp -rf /usr/local/lxlabs/kloxo/httpdocs/user-logo.png /home/kloxo/httpd/user-logo.png");
		log_cleanup("- User logo copy from -> /usr/local/lxlabs/kloxo/user-logo.png", $nolog);
		log_cleanup("- User logo copy to -> /home/kloxo/httpd/user-logo.png", $nolog);
	}
	else {
		log_cleanup("- Cleaned user logo source at /usr/local/lxlabs/kloxo/file/user-logo.png", $nolog);
		exit;
	}

	if ($select === 'defaults') {
		setFixUserlogoDefaultPages();
	}
	else if ($select === 'domains') {
		setFixUserlogoDomainPages();
	}
	else if ($select === 'all') {
		setFixUserlogoDefaultPages();
		setFixUserlogoDomainPages();
	}
	else {
		log_cleanup("- Wrong --select= entry");
	}
}

function setFixUserlogoDefaultPages()
{
	$list = array('cp', 'default', 'disable', 'webmail');
	
	foreach($list as $k => $l) {
		system("\\cp -rf /home/kloxo/httpd/user-logo.png /home/kloxo/httpd/{$l}/images/user-logo.png");
		system("\\cp -rf /home/kloxo/httpd/user-logo.png /home/kloxo/httpd/{$l}/images/logo.png");
		log_cleanup("- User logo for default pages copy to -> /home/kloxo/httpd/{$l}/images/logo.png", $nolog);
	}
	
	system("\\cp -rf /home/kloxo/httpd/user-logo.png /usr/local/lxlabs/kloxo/httpdocs/login/images/user-logo.png");
	system("\\cp -rf /home/kloxo/httpd/user-logo.png /usr/local/lxlabs/kloxo/httpdocs/login/images/logo.png");
	log_cleanup("- User logo copy to -> /usr/local/lxlabs/kloxo/httpdocs/login/images/user-logo.png", $nolog);
}

function setFixUserlogoDomainPages()
{
	global $gbl, $sgbl, $login, $ghtml;
	
	$login->loadAllObjects('client');
	$list = $login->getList('client');
	
	foreach($list as $c) {
		$cinfo = posix_getpwnam($c->nname);

		if (!$cinfo) { continue; }

		$clname = $c->getPathFromName('nname');
		$cdir = "/home/{$clname}";
		$dlist = $c->getList('domaina');

		foreach((array) $dlist as $l) {
			$web = $l->getObject('web');
			$docroot = $web->getFullDocRoot();

			if (file_exists("$docroot/images")) {
				system("\\cp -rf /home/kloxo/httpd/user-logo.png $docroot/images/user-logo.png");
				system("\\cp -rf /home/kloxo/httpd/user-logo.png $docroot/images/logo.png");
			}
		}
	}
}

/* ****** END - setFixUserlogo ***** */

