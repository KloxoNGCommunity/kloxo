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

//	$sclist['ffile'] = array('kloxo/maillog' => 'Maillog', "kloxo/smtp.log" => "SMTP.log", 'httpd/access_log' => 'Http Log', 'mysqld.log' => 'Mysql Log');
/*
	$sclist['ffile'] = array(
		'kloxo/courier' => 'Courier', 'kloxo/maillog' => 'Mail', "kloxo/smtp.log" => "SMTP", 
		'clamav/freshclam.log' => 'Freshclam', 'audit/audit.log' => 'Audit', "kloxo/smtp.log" => "SMTP", 
		'httpd/access_log' => 'HTTP Access', 'httpd/error_log' => 'HTTP Error', 
		'nginx/access.log' => 'Nginx Access', 'nginx/error.log' => 'Nginx Error', 
		'lighttpd/access.log' => 'Lighttpd Access', 'lighttpd/error.log' => 'Lighttpd Error', 
		'php-fpm/error.log' => 'PHP-FPM Error', 'php-fpm/slow.log' => 'PHP-FPM Slow', 
		'mysqld.log' => 'MySQL');
*/
	$sclist['ffile'] = array(
	//	'/usr/local/lxlabs/ext/php/error.log' => 'LxPhp Error',
		'audit/audit.log' => 'Audit',
		'clamav/freshclam.log' => 'Freshclam',
		'qmail/authlib/current' => 'Qmail-toaster Authlib',
		'qmail/clamd/current' => 'Qmail-toaster Clamd',
		'qmail/imap4/current' => 'Qmail-toaster IMAP4',
		'qmail/imap4-ssl/current' => 'Qmail-toaster IMAP4-SSL',
		'qmail/pop3/current' => 'Qmail-toaster POP3',
		'qmail/pop3-ssl/current' => 'Qmail-toaster POP3-SSL',
		'qmail/send/current' => 'Qmail-toaster Send',
		'qmail/smtp/current' => 'Qmail-toaster SMTP',
		'qmail/spamd/current' => 'Qmail-toaster Spamd',
		'qmail/submission/current' => 'Qmail-toaster Submission',
		'httpd/access_log' => 'HTTP Access', 'httpd/error_log' => 'HTTP Error', 
		'nginx/access.log' => 'Nginx Access', 'nginx/error.log' => 'Nginx Error', 
		'lighttpd/access.log' => 'Lighttpd Access', 'lighttpd/error.log' => 'Lighttpd Error', 
		'php-fpm/error.log' => 'PHP-FPM Error', 'php-fpm/slow.log' => 'PHP-FPM Slow', 
		'mysqld.log' => 'MySQL',
		'pureftpd.log' => 'Pure-ftp',
		'rkhunter/rkhunter.log' => 'RKHunter');

	return $sclist;
}



}
