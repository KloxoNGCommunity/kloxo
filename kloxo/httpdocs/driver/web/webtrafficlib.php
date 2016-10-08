<?php

class webtraffic extends lxclass
{
	function get() { }

	function write() { }

	static function generateGraph($oldtime, $newtime)
	{
		global $global_dontlogshell;

		$oldv = $global_dontlogshell;
		$global_dontlogshell = true;
		$list = lscandir_without_dot("__path_httpd_root");

		foreach ($list as $l) {
			if (!lxfile_exists("__path_httpd_root/$l/stats")) {
				continue;
			}

			$total = webtraffic::getEachwebfilequota("__path_httpd_root/$l/stats/$l-custom_log", $oldtime, $newtime);
			
			execRrdSingle("webtraffic", "ABSOLUTE", $l, $total * 1024 * 1024);
		}

		$global_dontlogshell = $oldv;
	}

	static function run_awstats($statsprog, $list)
	{
		global $gbl, $sgbl, $login, $ghtml;
		global $global_dontlogshell;

		log_log("run_stats", "In awstats");
		$global_dontlogshell = true;

		foreach ($list as $p) {
			log_log("run_stats", "In awstats for $p->nname $statsprog");

			if ($p->priv->isOn('awstats_flag')) {
				lxfile_mkdir("__path_httpd_root/$p->nname/webstats/");

				$name = $p->nname;
				web::createstatsConf($p->nname, $p->stats_username, $p->stats_password);

				if (is_disabled($statsprog)) {
					continue;
				}

				log_log("run_stats", "Execing $statsprog");

				if ($statsprog === 'webalizer') {
					print("webalizer: $p->nname\n");

					lxshell_return("nice", "-n", "10", "webalizer", "-p", "-n", $p->nname, "-t", $p->nname, "-c", 
						"__path_real_etc_root/webalizer/webalizer.{$p->nname}.conf");
				} else {
					print("awstats: $p->nname\n");

					putenv("GATEWAY_INTERFACE=");

					lxshell_return("nice", "-n", "10", "perl", "__path_kloxo_httpd_root/awstats/wwwroot/cgi-bin/awstats.pl", 
						"-update", "-config=$name");
				}
			}
		}
	}

	static function getweb_usage($name, $customer_name, $oldtime, $newtime, $d)
	{
		global $gbl, $sgbl, $login, $ghtml;

		$web_home = "$sgbl->__path_httpd_root";

		$log_path = "$web_home/$name/stats";
		$processedir = "$sgbl->__path_customer_root/$customer_name/__processed_stats/";

		lxfile_mkdir($processedir);

		$dir1 = "$log_path/";

		$files = lscandir_without_dot($dir1);

		$total = 0;

		foreach ($files as $file) {
			if (!strstr($file, "gz")) {
				$total += self::getEachwebfilequota("$dir1/$file", $oldtime, $newtime);
				$stat = stat("$dir1/$file");

				if ($stat['size'] >= 50 * 1024 * 1024) {
					if (isOn($d->remove_processed_stats)) {
						lxfile_rm("$dir1/$file");
					} else {
						lxfile_mv("$dir1/$file", getNotexistingFile($processedir, $file));
					}
				}
			}
		}

		return $total;
	}

	static function apacheLogConvertString($string)
	{
		$p = new ApacheLogRegex();
		$res = $p->parse($string);
		$time = $p->logtime_to_timestamp($res['Time']);
		$size = $res['Bytes-Sent'];

		return array($time, $size);
	}

	static function apacheLogFullString($string)
	{
		$p = new ApacheLogRegex();
		$res = $p->parse($string);
		$res['realtime'] = $p->logtime_to_timestamp($res['Time']);

		return $res;
	}

	static function getTimeFromString($string)
	{
		$p = new ApacheLogRegex();
		$res = $p->parse($string);
		dprintr($res['Time']);
		$value = $p->logtime_to_timestamp($res['Time']);
		dprint("Value: $value\n");

		return $value;
	}

	static function  getEachwebfilequota($file, $oldtime, $newtime)
	{
		global $gbl, $sgbl, $login, $ghtml;

		$fp = @lfopen($file, "r");
		$total = 0;

		print("\n$file: " . @ date('Y-m-d-H', $oldtime) . " " . @ date('Y-m-d-H', $newtime) . "\n");

		if (!$fp) {
			print("File Does Not Exist:returning Zero");

			return 0;
		}

		$fsize = lfilesize($file);

		if ($fsize <= 10) {
			print("File Size is Less Than Zero and Returning Zero:\n");

			return 0;
		}

		print("File Size is :$fsize\n\n\n");

		$ret = FindRightPosition($fp, $fsize, $oldtime, $newtime, array("webtraffic", "getTimeFromString"));

		if ($ret < 0) {
			return;
		}

		print("Current Position " . (ftell($fp) / $fsize) * 100 . "\n");

		$total = 0;
		$count = 0;
		$break = 1000;

		while (!feof($fp)) {
			$count++;
			$line = fgets($fp);
			list($time, $size) = self::apacheLogConvertString($line);
			if ($time > $newtime) {
				break;
			}
			$total += $size;
			if ($count > 100000) {
				$break = 10000;
			}
			if (!($count % $break)) {
				print("Count $count $newtime $time $total $size\n");
			}
		}

		print("$count lines actually processed in $file\n");

		$total = $total / (1024 * 1024);
		$total = round($total, 1);
		fclose($fp);

		print("Returning Total From OUT SIDE This File: $total \n");

		return $total;
	}

	static function findTotaltrafficwebUsage($driverapp, $statsprog, $list, $oldtime, $newtime)
	{
		// run awstats only if it is today.
		global $gbl, $sgbl, $login, $ghtml;

		if ($sgbl->isDebug()) {
			if ((time() - $newtime) < 24 * 3600 * 2) {
				self::run_awstats($statsprog, $list);
			}
		} else {
			self::run_awstats($statsprog, $list);
		}

		if (!isset($oldtime)) {
			return null;
		}

		foreach ($list as $d) {
			$tlist[$d->nname] = self::getweb_usage($d->nname, $d->customer_name, $oldtime, $newtime, $d);
		}

		foreach ($tlist as $key => $t) {
			if (!isset($t)) {
				$t = 0;
			}
			$temp[$key] = $t;
		}

		dprintr($temp);
		
//		createRestartFile($driverapp);
		createRestartFile("restart-web");
		return $temp;
	}
}

class ApacheLogRegex
{
	private $_format;

	private $_regex_string;

	private $_regex_fields;

	private $_num_fields;

	public function __construct()
	{
		$format = '%h %l %u %t \"%r\" %>s %b \"%{Referer}i\" \"%{User-Agent}i\"';

		if (gettype($format) !== 'string') {
			trigger_error(__CLASS__ . '::' . __FUNCTION__ . '(): ' . 'Paramater #1 expected to be a string but found ' 
				. gettype($format), E_USER_WARNING);

			return null;
		} elseif (strlen(trim($format)) == 0) {
			trigger_error(__CLASS__ . '::' . __FUNCTION__ . '(): ' . 'Paramater #1 is empty', E_USER_WARNING);

			return null;
		}

		$this->_format = $format;

		$this->_regex_string = '';

		$this->_regex_fields = array();

		$this->_parse_format();

		$this->_num_fields = count($this->_regex_fields);

		if ($this->_num_fields == 0) {
			trigger_error(__CLASS__ . '::' . __FUNCTION__ . '(): ' . 
				'Unable to parse ANY fields from Log format', E_USER_WARNING);

			return null;
		}
	}

	private function _parse_format()
	{
		$this->_format = trim($this->_format);

		$this->_format = preg_replace(array('/[ \t]+/', '/^ /', '/ $/'), array(' ', '', ''), $this->_format);

		$regex_elements = array();

		foreach (explode(' ', $this->_format) as $element) {
			$quotes = preg_match('/^\\\"/', $element) ? true : false;

			if ($quotes) {
				$element = preg_replace(array('/^\\\"/', '/\\\"$/'), '', $element);
			}

			$this->_regex_fields[] = $this->rename_this_name($element);

			if ($quotes) {
				if ($element == '%r' or preg_match('/{Referer}/', $element) or preg_match('/{User-Agent}/', $element)) {
					$x = '\"([^\"\\\\]*(?:\\\\.[^\"\\\\]*)*)\"';
				} else {
					$x = '\"([^\"]*)\"';
				}
			} elseif (preg_match('/^%.*t$/', $element)) {
				$x = '(\[[^\]]+\])';
			} else {
				$x = '(\S*)';
			}

			$regex_elements[] = $x;
		}

		$this->_regex_string = '/^' . implode(' ', $regex_elements) . '$/';

	}

	public function parse($line)
	{

		if (preg_match($this->_regex_string, $line, $matches) !== 1) {
			return null;
		}

		$out = array();

		for ($n = 0; $n < $this->_num_fields; ++$n) {
			$out[$this->_regex_fields[$n]] = $matches[$n + 1];
		}

		return $out;
	}


	public function parse_n($line)
	{
		if (preg_match($this->_regex_string, $line, $matches) !== 1) {
			return null;
		}

		return array_slice($matches, 1);
	}

	public function names()
	{
		return $this->_regex_fields;
	}

	public function regex()
	{
		return $this->_regex_string;
	}

	public function rename_this_name($field)
	{
		static $orig_val_default = array('s', 'U', 'T', 'D', 'r');

		static $trans_names = array(
			'%' => '',
			'a' => 'Remote-IP',
			'A' => 'Local-IP',
			'B' => 'Bytes-Sent-X',
			'b' => 'Bytes-Sent',
			'c' => 'Connection-Status', // <= 1.3
			'C' => 'Cookie', // >= 2.0
			'D' => 'Time-Taken-MS',
			'e' => 'Env-Var',
			'f' => 'Filename',
			'h' => 'Remote-Host',
			'H' => 'Request-Protocol',
			'i' => 'Request-Header',
			'I' => 'Bytes-Recieved', // >= 2.0
			'l' => 'Remote-Logname',
			'm' => 'Request-Method',
			'n' => 'Note',
			'o' => 'Reply-Header',
			'O' => 'Bytes-Sent', // >= 2.0
			'p' => 'Port',
			'P' => 'Process-Id', // {format} >= 2.0
			'q' => 'Query-String',
			'r' => 'Request',
			's' => 'Status',
			't' => 'Time',
			'T' => 'Time-Taken-S',
			'u' => 'Remote-User',
			'U' => 'Request-Path',
			'v' => 'Server-Name',
			'V' => 'Server-Name-X',
			'X' => 'Connection-Status', // >= 2.0
		);

		foreach ($trans_names as $find => $name) {
			$pattern = "/^%([!\d,]+)*([<>])?(?:\\{([^\\}]*)\\})?$find$/";

			if (preg_match($pattern, $field, $matches)) {
				if (!empty($matches[2])    and $matches[2] === '<'    and !in_array($find, $orig_val_default, true)) {
					$chooser = "Origional-";
				} elseif (!empty($matches[2]) and $matches[2] === '>'    and in_array($find, $orig_val_default, true)) {
					$chooser = "Final-";
				} else {
					$chooser = '';
				}

				$name = "{$chooser}" . (!empty($matches[3]) ? "$matches[3]" : $name) . (!empty($matches[1]) ? "($matches[1])" : '');

				break;
			}
		}

		if (empty($name)) {
			return $field;
		}

		return $name;
	}

	public function logtime_to_timestamp($time)
	{
		static $months = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');

		$time_format = '/\[([\d]{2})\/([\w]{3})\/([\d]{4}):([\d]{2}):([\d]{2}):([\d]{2}) ([\+\-])([\d]{2})([\d]{2})\]/';

		$m = array(); //matches

		if (!preg_match($time_format, $time, $m) || count($m) != 10) {
			return null;
		}

		return @ mktime($m[4], $m[5], $m[6], 1 + array_search($m[2], $months), $m[1], $m[3]);
	}
}
