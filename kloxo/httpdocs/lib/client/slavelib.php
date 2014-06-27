<?php 

class Slave extends Lxclient
{
	static $__desc = array("","",  "slave"); 
	static $__desc_nname = array("", "",  "");

	function getFfileFromVirtualList($name)
	{
		global $login;

		$root = $gbl->getSessionV('ffile_root');

		if (!$root) {
			throw new lxException($login->getThrow("no_root_dir_specified"));
		}

		$name = coreFfile::getRealpath($name);
		$name = '/' . $name;
		$ffile= new Ffile($this->__masterserver, $this->__readserver, $root, $name, "lxlabs");
		$ffile->__parent_o = $this;
		$ffile->get();

		return $ffile;
	}
}
