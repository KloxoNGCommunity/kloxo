<?php

class Cron extends Lxdb
{
	static $__table = 'cron';

	// Core
	static $__desc = array("", "", "cron_scheduled_task");

	static $__desc_nname = array("", "", "min");
	static $__desc_minute = array("", "", "minute");
	static $__desc_hour = array("", "", "hour");
//	static $__desc_weekday = array("", "", "day_of_week");
	static $__desc_weekday = array("", "", "week");
	static $__desc_ddate = array("", "", "date");
	static $__desc_month = array("", "", "month");
	static $__desc_command = array("n", "", "command", URL_SHOW);
	static $__desc_ttype_v_simple = array("", "", "simple");
	static $__desc_ttype_v_complex = array("", "", "standard");
	static $__desc_ttype = array("", "", "type");
	static $__desc_cron_day_hour = array("", "", "if_every_day_the_hour");
	static $__desc_simple_cron = array("", "", "period");
	static $__desc_argument = array("", "", "argument");
	static $__desc_username = array("", "", "user_name");
	static $__desc_syncserver = array("", "", "syncserver");
	static $__desc_mailto = array("", "", "");
	static $__acdesc_update_update = array("", "", "cron scheduled_task");

	static $minutelist = null;
	static $hourlist = null;
	static $ddatelist = null;
	static $monthlist = null;
	static $weekdaylist = null;

	function display($var)
	{
		if ($this->ttype === 'simple') {
			if ($this->simple_cron === 'every-day') {
				if ($var === 'minute') {
					$x = '0';
				} elseif ($var === 'hour') {
					$x = $this->cron_day_hour;
				} elseif ($var === 'ddate') {
					$x = '--all--';
				} else {
					$x = $this->$var;
				}
			} elseif ($this->simple_cron === 'every-hour') {
				if ($var === 'minute') {
					$x = '0';
				} elseif ($var === 'hour') {
					$x = '--all--';
				} elseif ($var === 'ddate') {
					$x = '--all--';
				} else {
					$x = $this->$var;
				}
			} elseif ($this->simple_cron === 'every-minute') {
				if ($var === 'minute') {
					$x = '--all--';
				} elseif ($var === 'hour') {
					$x = '--all--';
				} elseif ($var === 'ddate') {
					$x = '--all--';
				} else {
					$x = $this->$var;
				}
			}
		} else {
			if (stripos($this->$var, '--all--') !== false) {
				$x = '--all--';
			} else {
				$x = $this->$var;
			}
		}

		if ($x === null) {
			$x = '-';
		}

		return $x;
	}

	static function createListAlist($parent, $class)
	{
		global $gbl, $sgbl, $login, $ghtml;

		if ($parent->getClass() !== 'pserver') {
			if (!file_exists("../etc/flag/enablecronforall.flg")) {
				return;
			}
		}

		$alist[] = "a=list&c=$class";

		$alist[] = "a=addform&c=$class&dta[var]=ttype&dta[val]=simple";
		$alist[] = "a=addform&c=$class&dta[var]=ttype&dta[val]=complex";

		return $alist;
	}

	function __construct($masterserver, $readserver, $name)
	{
		if (!self::$minutelist) {
			self::$minutelist[] = '--all--';

			foreach (range(0, 59) as $i) {
				self::$minutelist[] = $i;
			}

			self::$hourlist[] = '--all--';

			foreach (range(0, 23) as $i) {
				self::$hourlist[] = $i;
			}
			
			self::$ddatelist[] = '--all--';

			foreach (range(1, 31) as $i) {
				self::$ddatelist[] = $i;
			}

			self::$weekdaylist[] = '--all--';
			self::$weekdaylist[] = 'sunday';
			self::$weekdaylist[] = 'monday';
			self::$weekdaylist[] = 'tuesday';
			self::$weekdaylist[] = 'wednesday';
			self::$weekdaylist[] = 'thursday';
			self::$weekdaylist[] = 'friday';
			self::$weekdaylist[] = 'saturday';

			self::$monthlist[] = '--all--';
			self::$monthlist[] = 'January';
			self::$monthlist[] = 'February';
			self::$monthlist[] = 'March';
			self::$monthlist[] = 'April';
			self::$monthlist[] = 'May';
			self::$monthlist[] = 'June';
			self::$monthlist[] = 'July';
			self::$monthlist[] = 'August';
			self::$monthlist[] = 'September';
			self::$monthlist[] = 'October';
			self::$monthlist[] = 'November';
			self::$monthlist[] = 'December';
		}

		parent::__construct($masterserver, $readserver, $name);
	}

	// Objects
	
	function createExtraVariables()
	{
		global $gbl, $sgbl, $login, $ghtml;
		$this->__var_mailto = $this->getParentO()->cron_mailto;
		$mydb = new Sqlite($this->__masterserver, "cron");
		$parent = $this->getParentO();
		$this->__var_cron_list = $mydb->getRowsWhere("username = '{$parent->username}'");

		$mydb = new Sqlite($this->__masterserver, "uuser");
		$userlist = $mydb->getRowsWhere("nname = '{$parent->username}'");
		$this->__var_user_list = $userlist[0];
	}

	static function createListNlist($parent, $view)
	{
	//	$nlist["nname"] = "5%";
		$nlist["username"] = "10%";
		$nlist["command"] = "80%";
		$nlist["syncserver"] = "10%";
		$nlist["ttype"] = "10%";
		$nlist["minute"] = "20%";
		$nlist["hour"] = "20%";
		$nlist["ddate"] = "20%";
		$nlist["weekday"] = "20%";
		$nlist["month"] = "20%";

		return $nlist;
	}

	function postUpdate()
	{
		// We need to write because reads everything from the database.
		$this->write();

		if (!$this->isSimple()) {
			$this->convertAll();
			$this->checkIfNull();
		}
	}

	function isSimple()
	{
		return ($this->ttype === 'simple');
	}

	function update($subaction, $param)
	{
		$param['minute'] = self::convertToAllIfExists($param['minute']);
		$param['hour'] = self::convertToAllIfExists($param['hour']);
		$param['ddate'] = self::convertToAllIfExists($param['ddate']);
		$param['weekday'] = self::convertToAllIfExists($param['weekday']);
		$param['month'] = self::convertToAllIfExists($param['month']);

		return $param;
	}

	function updateform($subaction, $param)
	{
		global $gbl, $sgbl, $login, $ghtml;

		// MR --- for security reason, only enable for admin
		if (!file_exists("../etc/flag/enablecronforall.flg")) {
			if ($login->nname !== 'admin') { return; }
		}

		$parent = $this->getParentO();

		// This is a hack to fix the cron migrated from web to client.
		if ($this->parent_clname !== $parent->getClName()) {
			$this->parent_clname = $parent->getClName();
			$this->setUpdateSubaction();
			$this->write();
		}

		if ($this->isSimple()) {
			$vlist['simple_cron'] = array('M', $this->simple_cron);

			if ($this->simple_cron === 'every-day') {
				$vlist['cron_day_hour'] = array('M', $this->cron_day_hour);
			}

			return $vlist;
		}

		$this->convertBack();

		$vlist["username"] = array('M', $this->username);

		if ($parent->isClass('pserver') || $parent->getClientParentO()->priv->isOn('cron_minute_flag')) {
			$vlist['minute'] = array('U', self::$minutelist);
		} else {
			$vlist['minute'] = array('M', $this->minute[0]);
		}

		$vlist["hour"] = array('U', self::$hourlist);
		$vlist["ddate"] = array('U', self::$ddatelist);
		$vlist["weekday"] = array('U', self::$weekdaylist);
		$vlist["month"] = array('U', self::$monthlist);
		$vlist["command"] = null;

		return $vlist;
	}

	static function addform($parent, $class, $typetd = null)
	{
		global $gbl, $sgbl, $login, $ghtml;

		// MR --- for security reason, only enable for admin
		if (!file_exists("../etc/flag/enablecronforall.flg")) {
			if ($login->nname !== 'admin') { return; }
		}
		
		// This is to make sure that the static variables 'monthlist, weekdaylist' etc, are initialized. 
		// There is no other way to do it.
		$tmp = new Cron($parent->__masterserver, $parent->__readserver, '__tmp__');

		if ($typetd['val'] === 'simple') {
			if ($parent->isClass('pserver') || $parent->priv->isOn('cron_minute_flag')) {
				$list['every-minute'] = 'Every Minute';
			}

			$list['every-hour'] = 'Every Hour';
			$list['every-day'] = 'Every Day';
			$vlist['simple_cron'] = array('A', $list);
			$v = self::$hourlist;
			unset($v[0]);
			$vlist['cron_day_hour'] = array('s', $v);
		} else {
			$vlist["username"] = array('M', $parent->username);

			if ($parent->isClass('pserver') || $parent->priv->isOn('cron_minute_flag')) {
				$vlist['minute'] = array('U', self::$minutelist);
			} else {
				$vlist['minute'] = array('m', 0);
			}

			$vlist["hour"] = array('U', self::$hourlist);

			$vlist["ddate"] = array('U', self::$ddatelist);
			$vlist["weekday"] = array('U', self::$weekdaylist);
			$vlist["month"] = array('U', self::$monthlist);
		}

		$vlist["command"] = null;

		$ret['action'] = 'add';
		$ret['variable'] = $vlist;

		return $ret;
	}

	function createShowUpdateform()
	{
		$ulist['update'] = null;

		return $ulist;
	}

	function checkIfAll($v)
	{
		return ($v === '--all--');
	}

	function convertAll()
	{
		$this->month = self::convertCronList($this->month, self::$monthlist);
		$this->weekday = self::convertCronList($this->weekday, self::$weekdaylist);
		$this->ddate = self::convertCronList($this->ddate, null);
		$this->hour = self::convertCronList($this->hour, null);
		$this->minute = self::convertCronList($this->minute, null);
	}

	function convertBack()
	{
		$this->month = self::convertBackCronList($this->month, self::$monthlist);
		$this->weekday = self::convertBackCronList($this->weekday, self::$weekdaylist);
		$this->ddate = self::convertBackCronList($this->ddate, null);
		$this->hour = self::convertBackCronList($this->hour, null);
		$this->minute = self::convertBackCronList($this->minute, null);
	}

	static function convertBackCronList($list, $staticlist)
	{
		if (!isset($list)) {
			$list = array('--all--');
		}

		if (is_string($list)) {
			$list = array($list);
		}

		foreach ($list as $k => $v) {
			if ($v === '--all--') {
				return $list;
			}

			if ($staticlist) {
				$outl[] = $staticlist[$k];
			} else {
				$outl[] = $k;
			}
		}
		
		return $outl;
	}

	static function convertCronList($string, $staticlist)
	{
		if (is_array($string)) {
			return $string;
		}

		$string = trim($string);
		$string = trim($string, ",");
		$list = explode(",", $string);
		
		foreach ($list as $l) {
			if ($l == '--all--') {
				$nel = null;
				$nel[] = '--all--';
				break;
			}

			if ($staticlist) {
				$nel[] = array_search($l, $staticlist);
			} else {
				$nel[] = $l;
			}
		}
		
		return $nel;
	}

	static function convertToAllIfExists($part)
	{
		if ((isset($part)) && (stripos($part, '--all--') !== false)) {
			$part = '--all--';
		}

		if ((!isset($part)) || ($part === '')) {
			$part = '--all--';
		}

		return $part;
	}

	static function add($parent, $class, $param)
	{
		$param['minute'] = self::convertToAllIfExists($param['minute']);
		$param['hour'] = self::convertToAllIfExists($param['hour']);
		$param['ddate'] = self::convertToAllIfExists($param['ddate']);
		$param['weekday'] = self::convertToAllIfExists($param['weekday']);
		$param['month'] = self::convertToAllIfExists($param['month']);

		if (!($parent->isClass('pserver') || $parent->priv->isOn('cron_minute_flag'))) {
			if (!is_numeric($param['minute'])) {
				$param['minute'] = 0;
			}
		}

		$param['username'] = $parent->username;

		$parambase = implode("_", array($param['username'], $param['command']));
		$parambase = fix_nname_to_be_variable($parambase);
		$cronlist = $parent->getList('cron');
		$count = 0;

		while (isset($cronlist[$parambase . "_" . $count])) {
			$count++;
		}
		
		$param['nname'] = $parambase . "_" . $count;

		return $param;
	}

	function postAdd()
	{
		// We need to write because reads everything from the database.
		$this->write();
		
		if (!$this->isSimple()) {
			$this->checkIfNull();
			$this->convertAll();
		}
	}

	function checkIfNull()
	{
		$this->checkIfNullVar('minute');
		$this->checkIfNullVar('hour');
		$this->checkIfNullVar('ddate');
		$this->checkIfNullVar('weekday');
		$this->checkIfNullVar('month');
	}

	function checkIfNullVar($var)
	{
		global $login;

		if (is_array($this->$var)) {
		//	return;
			$this->$var = implode(",", $this->$var);
		}

		if ($this->simple_cron === 'every-day') {
			$this->$ddate = $this->cron_day_hour;
		}

		if (trim($this->$var) === "") {
			throw new lxException($login->getThrow("can_not_be_null"));
			return;
		}
	}

	static function createListUpdateForm($object, $class)
	{
		$update[] = 'cron_mailto';
		
		return $update;
	}

	static function initThisListRule($parent, $class)
	{
	//	if ($parent->is__table('pserver')) {
		if ($parent->getClass() === 'pserver') {
			$res[] = array('syncserver', '=', "'$parent->nname'");
			return $res;
			//$res[] = 'AND';
		}

		$res[] = array("username", '=', "'$parent->username'");
		
		return $res;
	}
}

class all_cron extends cron
{
	static $__desc = array("", "", "all_scheduled_task");
	static $__desc_parent_name_f = array("n", "", "owner");
	static $__desc_parent_clname = array("n", "", "owner");

	function isSelect()
	{
		return false;
	}

	static function createListAlist($parent, $class)
	{
	//	return all_mailaccount::createListAlist($parent, $class);
		return all_domain::createListAlist($parent, $class);
	}

	static function initThisListRule($parent, $class)
	{
		global $login;

		if (!$parent->isAdmin()) {
			throw new lxException($login->getThrow("only_admin_can_access"));
		}

		return "__v_table";
	}

	static function createListUpdateForm($object, $class)
	{
		return null;
	}

	static function createListSlist($parent)
	{
		$nlist['nname'] = null;
		$nlist['parent_clname'] = null;
		return $nlist;
	}
}

