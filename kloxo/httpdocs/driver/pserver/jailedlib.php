<?php 

class jailed extends lxdb
{
	static $__desc = array("", "",  "jailed");
	static $__acdesc_show = array("", "",  "jailed");
	static $__desc_enable_jailed = array("f", "",  "enable_jailed");


	static function initThisObjectRule($parent, $class, $name = null) { 
		return $parent->nname;
	}

	function createShowUpdateform()
	{
		$uflist['update'] = null;

		return $uflist;
	}


	function updateform($subaction, $param)
	{
		$vlist['enable_jailed'] = "";
	//	$this->setDefaultValue("enable_jailed", "on");

		return $vlist;
	}
}
