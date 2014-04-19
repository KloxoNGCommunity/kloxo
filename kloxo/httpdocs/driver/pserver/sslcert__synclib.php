<?php 

class sslCert__sync extends lxDriverClass
{
	function dbactionDelete()
	{
		$parent = $this->main->getParentO();

		if ($parent->getClass() === 'web') {
			$name = $parent->nname;
			$user = $parent->customer_name;

			$path = "/home/{$user}/ssl";

			exec("rm -f {$path}/{$name}.*");
		}
	}

}

