<?php

function __cmd_desc_add($p, $parent = null)
{
	if (!$parent) {
		$parent = validate_and_get_parent($p);
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

			$param = exec_class_method($class, "addCommand", $parent, $class, $p);
			unset($var['template-name']);
			## MR -- must use lx_array_merge instead array_merge
			$param = lx_array_merge(array($param, $var));

			do_desc_add($parent, $class, $param);
		}
	} else {
		$param = exec_class_method($class, "addCommand", $parent, $class, $p);
		unset($var['template-name']);
		## MR -- must use lx_array_merge instead array_merge
		$param = lx_array_merge(array($param, $var));

		do_desc_add($parent, $class, $param);
	}

	$parent->was();
}

function __cmd_desc_delete($p)
{
	validate_and_get_parent($p);

	$class = $p['class'];
	$name = $p['name'];

	$o = new $class(null, 'localhost', $name);
	$o->get();

	validate_current($p, $o);

	do_desc_delete_single($o);

	$o->was();
}

function __cmd_desc_simplelist($p)
{
	global $sgbl;

	ob_start();

	$resource = $p['resource'];

	$parent = null;

	$parent = validate_and_get_parent($p);

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
	validate_and_get_parent($p);

	copy_nname_to_name($p);

	$o = new $p['class'](null, 'localhost', $p['name']);
	$o->get();

	validate_current($p, $o);

	$tparam = get_variable($p);
	$subaction = $p['subaction'];

	$tparam = $o->commandUpdate($subaction, $tparam);

	$p = array();

	foreach($tparam as $k => $v) {
		$k = str_replace("-", "_s_", $k);
		$p[$k] = $v;
	}

	do_desc_update($o, $subaction, $p);

	$o->was();
}

function __cmd_desc_getproperty($p)
{
	global $login;

	validate_and_get_parent($p);

	if (isset($p['name']) && isset($p['class'])) {
		$name = $p['name'];
		$class = $p['class'];

		$o = new $class(null, 'localhost', $name);
		$o->get();

		validate_current($p, $o);
	} else {
		$o = $login;
	}

	$o->getHardProperty();

	$vlist = get_variable($p);

	$result = null;

	foreach($vlist as $k => $v) {
		$nv = $k;

		if (csa($nv, "-")) {
			$cc = explode("-", $nv);
			$result["v-$k"] = $o->{$cc[0]}->$cc[1];

			continue;
		}

		if ($nv === 'priv' || $nv === 'used') {
			foreach($o->$nv as $kk => $nnv) {
				if ($o->isQuotaVariable($kk)) {
					$result["v-$nv-$kk"] =  $nnv;
				}
			}

			continue;
		}

		$result["v-$nv"] =  $o->$nv;
	}

	return $result;
}

function validate_and_get_parent($p)
{
	global $login;

	if (isset($p['parent-class']) && isset($p['parent-name'])) {
		$parent = new $p['parent-class'](null, 'localhost', $p['parent-name']);
		$parent->get();

		if ($parent->dbaction === 'add') {
			throw new lxException("parent_doesnt_exist", "nname", $p['parent-name']);
		}

		if (!$parent->checkIfSomeParent($login->getClName())) {
			throw new lxException("you_are_not_the_owner_of_parent", "", $p['parent-name']);
		}
	} else {
		$parent = $login;
	}

	return $parent;
}

function validate_current($p, $o)
{
	global $login;

	if ($o->dbaction === 'add') {
		throw new lxException("parent_doesnt_exist", "nname", $p['parent-name']);
	}

	if (!$o->checkIfSomeParent($login->getClName())) {
		throw new lxException("you_are_not_the_owner_of_parent", "", $p['parent-name']);
	}

	// MR -- only admin/auxiliary permit to know it
	if (isset($p['v-parent_clname'])) {
		if ((!$login->isAdmin()) && (!$login->isAuxiliary())) {
			foreach($p as $k => $v) {
				if ((stripos($k, 'v-') !== false) && ($k !== 'v-parent_clname')) {
					throw new lxException("no_permit_get_value_for_parent_clname", "v-parent_clname", $p['v-parent_clname']);
				}
			}
		}
	}

	// MR -- only admin/auxiliary permit to know it
	if (isset($p['dbadmin'])) {
		if ((!$login->isAdmin()) && (!$login->isAuxiliary())) {
			throw new lxException("only_permit_for_admin_or_auxiliary", "dbadmin", $p['dbadmin']);
		}
	}

	// MR -- only admin/auxiliary permit to change cttype
	if (isset($p['v-cttype'])) {
		if ((!$login->isAdmin()) && (!$login->isAuxiliary())) {
			throw new lxException("only_permit_for_admin_or_auxiliary", "v-cttype", $p['v-cttype']);
		}
	}

	if ($p['class'] === 'auxiliary') {
		if ((!$login->isAdmin()) && (!$login->isAuxiliary())) {
			throw new lxException("only_permit_for_admin_or_auxiliary", "class", $p['class']);
		}
	}
}
