<?php

class custombutton extends lxdb 
{
	static $__desc = array("", "",  "custom_button");
	static $__desc_nname = array("", "",  "name", "a=show");
	static $__desc_description = array("", "",  "description", "a=show");
	static $__desc_class = array("", "",  "class");
	static $__desc_upload = array("F", "",  "upload");
	static $__desc_image = array("F", "",  "image");
	static $__desc_url = array("", "",  "url");
	static $__acdesc_update_update = array("", "",  "update");

	static function add($parent, $class, $param)
	{
	//	$param['nname'] = incrementVar($class, "nname", 1, 1);
		
		return $param;
	}

	function createShowUpdateform()
	{
		$alist["update"] = null;
		
		return $alist;
	}

	static function createListAlist($parent, $class)
	{
		$alist[] = "a=list&c=$class";
	//	$alist['__v_dialog_add'] = "a=addform&c=$class";
		
		return $alist;
	}

	static function createListNlist($parent, $view)
	{
		$nlist['nname'] = '10%';
		$nlist['description'] = '100%';
		
		return $nlist;
	}

	function updateUpdate($param)
	{
		lxfile_mkdir("theme/custom");

		if ($_FILES['upload']['tmp_name']) {
			lxfile_rm("theme/custom/{$this->nname}.gif");
			lxfile_mv($_FILES['upload']['tmp_name'], "theme/custom/{$this->nname}.gif");
			lxfile_generic_chmod("theme/custom/{$this->nname}.gif", "0755");
		}
		
		return $param;
	}

	function updateform($subaction, $param)
	{
		global $gbl, $sgbl, $login, $ghtml;
		
		$list = array("client"); if ($sgbl->isHyperVm()) { $list = lx_array_merge(array($list, array("vps"))); }
		$vlist['class'] = array('s', $list);
		$vlist['description'] = null;
		$vlist['url'] = null;
		$ipt = "theme/custom/{$this->nname}.gif";
		
		if (lxfile_exists($ipt)) {
			$vlist['image'] = array('I', array('width' => 20, 'height' => 20, 'value' => $ipt));
		} else {
			$vlist['image'] = array('M', "No Image");
		}
		
		$vlist['upload'] = array('F', null);
		
		return $vlist;
	}

	static function AddListForm($parent, $class)
	{
		return self::addform($parent, $class);
	}

	static function addform($parent, $class, $typetd = null)
	{
		global $gbl, $sgbl, $login, $ghtml;
		
		$list = array("client"); if ($sgbl->isHyperVm()) { $list = lx_array_merge(array($list, array("vps"))); }

		$vlist['nname'] = null;
		$vlist['description'] = null;
		$vlist['class'] = array('s', $list);
		$vlist['url'] = array('m', "http://");
		$ret['action'] = 'add';
		$ret['variable'] = $vlist;
		
		return $ret;
	}
}

