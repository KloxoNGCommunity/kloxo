<?php

class DomaintrafficHistory extends TrafficHistory
{
	static $__desc_ftptraffic_usage  = array('', '', 'ftp_traffic');
	static $__desc_webtraffic_usage  = array('', '', 'web_traffic');
	static $__desc_mailtraffic_usage = array('', '', 'mail_traffic');

	static function createListNlist($parent, $view)
	{
		$nlist['month']             = '20%';
		$nlist['ftptraffic_usage']  = '20%';
		$nlist['mailtraffic_usage'] = '20%';
		$nlist['webtraffic_usage']  = '20%';
		$nlist['traffic_usage']     = '20%';
		return $nlist;
	}

	function isSync()
	{
		return false;
	}

	static function initThisList($parent, $class)
	{
		$result = self::getTrafficMonthly($parent, 'domaintraffic', self::getExtraVar());
		return $result;
	}

	static function getExtraVar()
	{
		return array('ftptraffic_usage', 'webtraffic_usage', 'mailtraffic_usage');
	}

}
