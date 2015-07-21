<?php

function __cmd_desc_add($p, $parent = null)
{
	if (!$parent) {
		$parent = validate_and_get_parent($p);
	}

	copy_nname_to_name($p);

	$class = $p['class'];

	if (isset($p['count'])) {
		$oldname = $p['name'];

		for($i = 0; $i < $p['count']; $i++) {
			if ($class === 'domain') {
				$p['name'] = "$oldname$i.com";
			} else {
				$p['name'] = "$oldname$i";
			}

			$var = exec_class_method($class, "addCommand", $parent, $class, $p);
			unset($var['template-name']);
			$p = array_merge(array($p, $var));

			do_desc_add($parent, $class, $p);
		}

		$parent->was();
	} else {
		$var = exec_class_method($class, "addCommand", $parent, $class, $p);

		unset($var['template-name']);
		$p = array_merge(array($p, $var));

		do_desc_add($parent, $class, $p);

		$parent->was();
	}
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
			throw new lxException($login->getThrow("not_owner_of_{$p['parent-name']}_parent_object"));
		}

		if (!$parent->checkIfSomeParent($login->getClName())) {
			throw new lxException($login->getThrow("not_owner_of_{$p['parent-name']}_parent_object"));
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
		throw new lxException($login->getThrow("no_object_for_{$p['name']}"));
	}

	if (!$o->checkIfSomeParent($login->getClName())) {
		throw new lxException($login->getThrow("no_object_under_{$p['login-name']}"));
	}

	// MR -- make possible only know parent nname
	if (isset($p['v-parent_clname'])) {
		foreach($p as $k => $v) {
			if ((stripos($k, 'v-') !== false) && ($k !== 'v-parent_clname')) {
				throw new lxException($login->getThrow("no_permit_get_value_for_{$p['v-parent_clname']}"));
			}
		}
	}

	// MR -- only admin to know it
	if ((isset($p['dbadmin']) && (!$login->isAdmin()))) {
		throw new lxException($login->getThrow("only_permit_for_admin"));
	}

	// MR -- only admin permit to change cttype
	if ((isset($p['v-cttype']) && (!$login->isAdmin()))) {
		throw new lxException($login->getThrow("only_permit_for_admin"));
	}

	if (($p['class'] === 'auxiliary') && (!$login->isAdmin())) {
		throw new lxException($login->getThrow("only_permit_for_admin"));
	}
}
