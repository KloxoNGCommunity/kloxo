<?php 

class Llog extends Lxclass {

static $__desc = array("", "",  "log_manager");
// Data
static $__desc_nname = array("", "",  "server_name", "a=show");
static $__acdesc_show = array("", "",  "log_manager", "a=show");


static $__desc_ffile_l = array('v', '', '', '');


function get() {}
function write() {}

static function initThisObjectRule($parent, $class, $name = null)
{
	return $parent->getClName();
}

function getId()
{
	return $this->getSpecialname();
}

function createShowPropertyList(&$alist)
{
	$alist['property'][] = 'a=show';
	$alist['property'][] = 'a=show&l[class]=ffile&l[nname]=/';
}

function createShowAlist(&$alist, $subaction = null)
{
	return $alist;
}

/** 
* @return void 
* @param 
* @param 
* @desc  Special getfromlist for ffile. The concept is that the the whole directory tree is available virtually under an ffile object, thus enabling us to get any object at any level. This is different from other objects where there is only one level of children.
*/ 
 
function getFfileFromVirtualList($name)
{
	$name = coreFfile::getRealpath($name);
	$name = '/' . $name;
	$ffile= new Ffile($this->__masterserver, $this->__readserver, "__path_log", $name, $this->getParentO()->username);
	$ffile->__parent_o = $this;
	$ffile->get();
	$ffile->readonly = 'on';
	return $ffile;
}


function createShowSclist()
{
	// MR -- only list maillog because change multilog to splogger for qmail-toaster
	$sclist['ffile'] = array(
		'audit/audit.log' => 'Audit',

		'messages' => 'Messages',

		'cron' => 'Cron',

		'secure' => 'Secure',

		'clamav/freshclam.log' => 'Freshclam',
		'maillog' => 'Mail log',

		'httpd/access_log' => 'HTTP Access',
		'httpd/error_log' => 'HTTP Error', 

		'lighttpd/access.log' => 'Lighttpd Access',
		'lighttpd/error.log' => 'Lighttpd Error', 

		'nginx/access.log' => 'Nginx Access',
		'nginx/error.log' => 'Nginx Error', 

		'hiawatha/system.log' => 'Hiawatha System',
		'hiawatha/garbage.log' => 'Hiawatha Garbage', 
		'hiawatha/access.log' => 'Hiawatha Access',
		'hiawatha/error.log' => 'Hiawatha Error', 
		'hiawatha/exploit.log' => 'Hiawatha Exploit',

		'php-fpm/error.log' => 'PHP-FPM Error',
		'php-fpm/slow.log' => 'PHP-FPM Slow', 

		'mysqld.log' => 'MySQL',

		'pureftpd.log' => 'Pure-ftp',

		'rkhunter/rkhunter.log' => 'RKHunter',

		'yum.log' => 'Yum log');

	return $sclist;
}



}
