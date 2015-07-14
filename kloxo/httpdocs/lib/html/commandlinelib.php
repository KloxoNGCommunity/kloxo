<?php 

function __cmd_desc_add($p, $parent = null)
{
	global $gbl, $sgbl, $login, $ghtml; 

	validate_cmd($p);

	if (!$parent) {
		if (isset($p['parent-class']) && isset($p['parent-name'])) {
			$parent = new $p['parent-class'](null, 'localhost', $p['parent-name']);
			dprint("$parent->nname\n");
			$parent->get();

			if ($parent->dbaction === 'add') {
				throw new lxException($login->getThrow("no_parent_object"), '', $p['parent-name']);
			}

			if (!$parent->checkIfSomeParent($login->getClName())) {
				throw new lxException($login->getThrow("not_owner_of_parent_object"), '', $p['parent-name']);
			}

		} else {
			$parent = $login;
		}
	}

	copy_nname_to_name($p);
	$class = $p['class'];

	$var = get_variable($p);

	if (isset($p['count'])) {
		$oldname = $p['name'];
		for($i = 0; $i < $p['count']; $i++) {
			if ($class === 'domain') {
				$p['name'] = "$oldname$i.com";
			} else {
				$p['name'] = "$oldname$i";
			}

			$p = exec_class_method($class, "addCommand", $parent, $class, $p);
			unset($var['template-name']);
			$p = lx_array_merge(array($p, $var));

			do_desc_add($parent, $class, $p);
		}

		$parent->was();

		exit;
	}

	$p = exec_class_method($class, "addCommand", $parent, $class, $p);

	unset($var['template-name']);
	$p = lx_array_merge(array($p, $var));
	do_desc_add($parent, $class, $p);

	$parent->was();
}

function __cmd_desc_delete($p)
{
	global $gbl, $sgbl, $login, $ghtml; 

	validate_cmd($p);

	$class = $p['class'];
	$name = $p['name'];

	$object = new $class(null, 'localhost', $name);
	$object->get();

	if ($object->dbaction === 'add') {
		throw new lxException($login->getThrow('no_object'), '', $name);
	}

	if (!$object->checkIfSomeParent($login->getClName())) {
		throw new lxException($login->getThrow("no_object_under_current_user"), '', $object->nname);
	}

	do_desc_delete_single($object);

	$object->was();
}

function __cmd_desc_simplelist($p)
{
	global $gbl, $sgbl, $login, $ghtml; 

	validate_cmd($p);

	ob_start();
	$resource = $p['resource'];

	$parent = null;

	if (!$parent) {
		if (isset($p['parent-class']) && isset($p['parent-name'])) {
			$parent = new $p['parent-class'](null, 'localhost', $p['parent-name']);
			dprint($parent->nname);
			$parent->get();

			if ($parent->dbaction === 'add') {
				throw new lxException($login->getThrow("no_parent_object"), '', $p['parent-name']);
			}

			if (!$parent->checkIfSomeParent($login->getClName())) {
				throw new lxException($login->getThrow("not_owner_of_parent_object"), '', $p['parent-name']);
			}

		} else {
			$parent = $login;
		}
	}

	$list = $parent->getCommandResource($resource);

	if (!$list) {
		// Fix for WHMCS needing pserver in client.

		if (!$parent->isAdmin() && $sgbl->isKloxo() && $resource === 'pserver') {
			$list['localhost'] = 'localhost';
			return $list;
		}

		$list = $parent->getList($resource);

		if (isset($p['v-filter'])) {
			list($var, $val) = explode(":", $p['v-filter']);

			foreach($list as $k => $l) {
				if ($l->$var !== $val) {
					unset($list[$k]);
				}
			}
		}

		if (!$list) {
			json_print("error", $p, "__error_no_resource_for_$resource");
			exit;
		}

		$list = get_namelist_from_objectlist($list, "nname", "nname");
	}

	ob_end_clean();

	return $list;
}

function copy_nname_to_name(&$p)
{
	if (isset($p['nname']) && !isset($p['name'])) {
		$p['name'] = $p['nname'];
	}
}

function __cmd_desc_update($p)
{
	global $gbl, $sgbl, $login, $ghtml;

	validate_cmd($p);

	copy_nname_to_name($p);
	$object = new $p['class'](null, 'localhost', $p['name']);
	$object->get();

	if ($object->dbaction === 'add') {
		throw new lxException($login->getThrow("no_object"), '', $p['name']);
	}

	if (!$object->checkIfSomeParent($login->getClName())) {
		throw new lxException($login->getThrow("no_object_under_current_user"), '', $object->nname);
	}

	$tparam = get_variable($p);
	$subaction = $p['subaction'];

	$tparam = $object->commandUpdate($subaction, $tparam);

	$p = array();

	foreach($tparam as $k => $v) {
		$k = str_replace("-", "_s_", $k);
		$p[$k] = $v;
	}

	dprintr($p);
	do_desc_update($object, $subaction, $p);

	$object->was();
}

function __cmd_desc_getproperty($p)
{
	global $gbl, $sgbl, $login, $ghtml; 

	validate_cmd($p);

	if (isset($p['name']) && isset($p['class'])) {
		$name = $p['name'];
		$class = $p['class'];
		$object = new $class(null, 'localhost', $name);
		$object->get();

		if ($object->dbaction === 'add') {
			throw new lxException($login->getThrow('no_object'), '', $name);
		}
	} else {
		$object = $login;
	}
 
	$object->getHardProperty();

	$vlist = get_variable($p);

	foreach($vlist as $k => $v) {
		$nv = $k;

		if (csa($nv, "-")) {
			$cc = explode("-", $nv);
			$result["v-$k"] = $object->{$cc[0]}->$cc[1];

			continue;
		}

		if ($nv === 'priv' || $nv === 'used') {
			foreach($object->$nv as $kk => $nnv) {
				if ($object->isQuotaVariable($kk)) {
					$result["v-$nv-$kk"] =  $nnv;
				}
			}

			continue;
		}

		$result["v-$nv"] =  $object->$nv;
	}

	return $result;
}

function validate_cmd($p)
{
	global $gbl, $sgbl, $login, $ghtml; 

	if (($p['login-class'] === 'client') && ($p['login-name'] === 'admin')) {
		//
	} elseif (($p['login-class'] === 'auxiliary') && (stripos($p['login-name'], '.aux') !== false)) {
		//
	} else {
		throw new lxException($login->getThrow('only_permit_for_admin_or_auxiliary_login'), '', $p['login-name']);	
	}
}
