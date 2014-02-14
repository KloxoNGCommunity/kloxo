<?php 

class jailed__linux extends lxDriverClass
{
	function dbactionUpdate($subaction) {
		if (if_demo()) { throw new lxException ("demo", $v); }
	}

}
