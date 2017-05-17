<?php

class Forward_a extends LxMailClass
{
	static $__desc = array("", "", "mail_forward");
	static $__desc_nname = array("", "", "forward_address");

	function postAdd()
	{
		global $gbl, $sgbl, $login, $ghtml;

		$this->nname = trim($this->nname);

		$this->nname = trim($this->nname, "'");
		$this->nname = trim($this->nname);
		$this->nname = trim($this->nname, '"');
	}

	static function createListAlist($parent, $class)
	{
		$alist[] = "a=list&c=forward_a";
		$alist[] = $parent->getGenToggleUrl('forward');

		return $alist;
	}
}


class Mailaccount extends Lxclient
{
	static $__table = 'mailaccount';

	// Core
	static $__desc = array("", "", "mail_account");

	// Data
	static $__desc_nname = array("", "", "account_name", URL_SHOW);
	static $__desc_maildisk_usage = array("q", "", "mail_disk_usage");
//	static $__desc_autoresponder_num = array("q", "",  "number_of_autoresponders");
	static $__desc_forward_a = array("", "", "forward");
	static $__desc_status = array("e", "", "s:status", URL_TOGGLE_STATUS);
	static $__desc_status_v_on = array("", "", "enabled");
	static $__desc_status_v_off = array("", "", "disabled");

	static $__desc_autores_name = array("", "", "autoresponder_name");
	static $__desc_no_local_copy = array("f", "", "no_local_copy");

	static $__desc_disable_reason = array("", "", "st", 'a=updateForm&sa=limit');
	static $__desc_state = array("e", "", "st", 'a=updateForm&sa=limit');
	static $__desc_state_v_ok = array("", "", "alright");
	static $__desc_state_v_exceed = array("", "", "exceeded");

	static $__desc_autorespond_status = array("e", "", "AR:autorespond_status", "a=update&sa=toggle_autorespond");
	static $__desc_autorespond_status_v_off = array("", "", "autorespond_is_off");
	static $__desc_autorespond_status_v_on = array("", "", "autorespond_is_on");

	static $__desc_forward_status = array("ef", "", "FR:forward_status", "a=update&sa=toggle_forward");
	static $__desc_forward_status_v_off = array("", "", "forward_is_off");
	static $__desc_forward_status_v_on = array("", "", "forward_is_on");

	static $__desc_parent_name = array("", "", "domain_name");
	static $__desc_filter_spam_status = array("", "", "what_to_do_with_spam");

	static $__desc_button_password_f = array('b', '', '', 'a=updateform&sa=password');
	static $__desc_button_limit_f = array('b', '', '', 'a=updateform&sa=limit');
	static $__desc_button_spam_f = array('b', '', '', 'a=show&o=spam');
	static $__desc_button_webmail_f = array('b', '', '', '__stub_webmail_url');
	static $__desc_parent_name_f = array("n", "", "domain");

	static $__desc_maildisk_usage_per_f = array("p", "", "mail_disk_usage");

	static $__desc_spam_o = array("db", "", "");

	// MR -- danger if declate other _o in here - make mail account as website domain!.
//	static $__desc_mmail_o = array("db", "", "");
//	static $__desc_web_o = array("db", "", "");
//	static $__desc_addondomain_l = array("db", "", "");

	static $__acdesc_update_webmail = array("", "", "webmail");
	static $__acdesc_update_autores = array("", "", "set_auto_responder");
	static $__acdesc_update_disable_forward = array("", "", "disable_forward");
	static $__acdesc_update_enable_forward = array("", "", "enable_forward");
	static $__acdesc_update_configuration = array("", "", "configure");

	static $__acdesc_update_disable_autorespond = array("", "", "disable_auto_responder");
	static $__acdesc_update_enable_autorespond = array("", "", "enable_auto_responder");
	static $__acdesc_update_train_as_spam = array("", "", "train_as_spam");
	static $__acdesc_update_train_as_ham = array("", "", "train_as_ham");
	static $__acdesc_update_filter = array("", "", "filter_config");

	static $__acdesc_update_train_as_system_spam = array("", "", "train_as_system_spam");
	static $__acdesc_update_train_as_system_ham = array("", "", "train_as_system_ham");
	static $__acdesc_update_clear_spam_db = array("", "", "clear_spam_db");
	static $__desc_autoresponder_l = array("qbd", "", "", "");
	static $__desc_mailcontent_l = array("", "", "", "");
	static $__desc_ndskshortcut_l = array("d", "", "", "");

	function display($var)
	{
		return parent::display($var);
	}

	function getStubUrl($var)
	{
		if ($var === '__stub_webmail_url') {
			return create_simpleObject(array('url' => "http://webmail." . $this->getParentName(), 'purl' => "a=updateform&sa=webmail&l[class]=mailaccount&l[nname]=$this->nname", 'target' => "target='_blank'"));
		}
	}

	function createExtraVariables()
	{
		global $gbl, $sgbl, $login, $ghtml;
		$this->__var_spam_driver = $gbl->getSyncClass(null, $this->syncserver, 'spam');

		$this->__var_autores_driver = $gbl->getSyncClass(null, $this->syncserver, 'autoresponder');
		$spam = $this->getObject('spam');
		$this->__var_spam_status = $spam->status;
		dprint($spam);
	}

	static function findTotalUsage($driver, $list)
	{
		$class = "mailaccount__$driver";

		foreach ($list as $k => $d) {
			$mdiskusage[$k] = exec_class_method($class, "Mailaccdisk_usage", $d['nname']);
		}

		return $mdiskusage;
	}

	function isRealQuotaVariable($k)
	{
		$list['maildisk_usage'] = 'a';

		return isset($list[$k]);
	}

	function getQuotamaildisk_usage()
	{
		global $gbl, $sgbl, $login, $ghtml;

		if (isset($sgbl->__var_mdiskusage)) {
			return $sgbl->__var_mdiskusage[$this->nname];
		} else {
			return $this->used->maildisk_usage;
		}
	}

	function isSelect()
	{
		return (!csb($this->nname, "postmaster@"));
	}

	static function createListNlist($parent, $view)
	{
		$nlist['cpstatus'] = '3%';
		$nlist['status'] = '3%';
		$nlist['forward_status'] = '3%';
		$nlist['autorespond_status'] = '3%';
		$nlist['abutton_updateform_s_password'] = '3%';
		$nlist['abutton_updateform_s_limit'] = '3%';
		$nlist['abutton_list_s_forward_a'] = '3%';
		$nlist['abutton_list_s_mailcontent'] = '3%';
		$nlist['abutton_list_s_autoresponder'] = '3%';
		$nlist['abutton_updateform_s_filter'] = '3%';
		$nlist['abutton_updateform_s_configuration'] = '3%';
	//	$nlist['button_spam_f'] = '3%';
		$nlist['button_webmail_f'] = '3%';
		$nlist['nname'] = '40%';
		$nlist['maildisk_usage_per_f'] = '30%';

		return $nlist;
	}

	function hasFunctions() { return true; }

	function updateform($subaction, $param)
	{
		global $login;

		switch ($subaction) {
			case 'autores':
				$list = $this->getList('autoresponder');

				if (!$list) {
					throw new lxException($login->getThrow("first_add_some_autoresponders"));
				}

				$nlist = get_namelist_from_objectlist($list, "nname", "autores_name");
				$vlist['autores_name'] = array('A', $nlist);
				
				return $vlist;

			case 'filter':
				$this->setDefaultValue('filter_spam_status', 'mailbox');
				$vlist['filter_spam_status'] = array('s', array('spambox', 'mailbox', 'delete'));
				$vlist['__v_updateall_button'] = array();

				return $vlist;

			case 'configuration':
				$vlist['no_local_copy'] = null;
				$vlist['__v_updateall_button'] = array();

				return $vlist;
		}

		return parent::updateform($subaction, $param);
	}

	function updateAutores($param)
	{
		$v = $param['autores_name'];
	//	$autores = $this->getFromList('autoresponder', "{$v}___{$this->getClName()}");
		$autores = $this->getFromList('autoresponder', $v);
		$this->__var_autores_message = $autores->text_message;
		$this->__var_autores_subject = $autores->reply_subject;
		dprintr($this->__var_autores_message);

		return $param;
	}

	function updateDisable_Autorespond($param)
	{
		$this->autorespond_status = 'off';
		$this->setUpdateSubaction('sync_autorespond');

		return null;
	}

	function updateEnable_Autorespond($param)
	{
		$this->autorespond_status = 'on';
		$this->setUpdateSubaction('sync_autorespond');

		return null;
	}

	function updateToggle_autorespond($param)
	{
		$this->autorespond_status = $this->isOn('autorespond_status') ? 'off' : 'on';
		$this->setUpdateSubaction('sync_autorespond');

		return null;
	}

	function updateToggle_forward($param)
	{
		$this->forward_status = $this->isOn('forward_status') ? 'off' : 'on';
		$this->setUpdateSubaction('sync_forward');
	}

	function getGenToggleUrl($var)
	{
		if ($this->isOn("{$var}_status")) {
			$sa = "disable_$var";
		} else {
			$sa = "enable_$var";
		}

		$url = "a=update&sa=$sa";

		return $url;
	}


	function updateDisable_Forward($param)
	{
		$this->forward_status = 'off';
		$this->setUpdateSubaction('sync_forward');

		return null;
	}

	function updateEnable_Forward($param)
	{
		$this->forward_status = 'on';
		$this->setUpdateSubaction('sync_forward');

		return null;
	}

	function updateToggleForward($param)
	{
		$this->forward_status = $this->isOn('forward_status') ? 'off' : 'on';
		$this->setUpdateSubaction('sync_forward');

		return null;
	}

	function getFfileFromVirtualList($name)
	{
		global $gbl, $sgbl, $login, $ghtml;

		list($mailacc, $domain) = explode("@", $this->nname);

		$mailpath = mmail__qmail::getDir($domain);
		$mailpath = str_replace($sgbl->__path_mail_root, $sgbl->__path_mail_data, $mailpath);

		$name = coreFfile::getRealpath($name);
		$name = '/' . $name;
		$ffile = new Ffile($this->__masterserver, $this->__readserver, "$mailpath/$mailacc", $name, mmail__qmail::getUserGroup($domain));
		$ffile->__parent_o = $this;
		$ffile->get();

		return $ffile;
	}

	static function createListAlist($parent, $class)
	{
		global $gbl, $sgbl, $login, $ghtml;

		if ($parent->isClient()) {
			$parent->createShowPropertyList($alist);

			return $alist['property'];
		}

		$parent->getParentO()->getObject('web')->createShowPropertyList($alist);

		return null;

	}

	static function initThisListRule($parent, $class)
	{
		if ($parent->isClient()) {
			$ret = lxdb::initThisOutOfBand($parent, 'domain', 'mmail', $class);

			return $ret;

		}

		return lxdb::initThisListRule($parent, $class);
	}

	function createShowPropertyList(&$alist)
	{
		global $gbl, $sgbl, $login, $ghtml;

		if ($ghtml->frm_subaction === 'password') {
			$alist['property'][] = "a=updateform&sa=password";
		} elseif ($ghtml->frm_subaction === 'limit') {
			$alist['property'][] = "a=updateform&sa=limit";
		} elseif ($ghtml->frm_o_cname === 'forward_a') {
			$alist['property'][] = "a=list&c=forward_a";
		} elseif ($ghtml->frm_subaction === 'filter') {
			$alist['property'][] = "a=updateform&sa=filter";
		} elseif ($ghtml->frm_subaction === 'configuration') {
			$alist['property'][] = "a=updateform&sa=configuration";
		} elseif ($ghtml->frm_o_cname === 'mailcontent') {
			$alist['property'][] = "a=list&c=mailcontent";
		} elseif ($ghtml->frm_o_cname === 'autoresponder') {
			$alist['property'][] = "a=list&c=autoresponder";
		} else {
			$alist['property'][] = "a=show";
		}

		return $alist;
	}

	function isAction($var)
	{
		global $gbl, $sgbl, $login;

		if (($var === 'nname' || $var === 'dtype') && $this->getParentO()->isCustomer()) {
			return true;
		}

		return true;
	}

	function createShowAlist(&$alist, $subaction = null)
	{
		global $gbl, $sgbl, $login, $ghtml;

		// MR -- set 'appearence' as the same as parent
		$this->getParentO()->getSpecialObject('sp_specialplay');

		$alist['__title_mailaccount'] = "Mailaccount &#x00bb; $this->nname";

	//	$this->getCPToggleUrl($alist);

		$alist[] = "a=updateform&sa=password";

		if (!$this->isLogin()) {
			$alist[] = "a=updateform&sa=limit";
		}

		$alist[] = "a=list&c=forward_a";
		$alist[] = "a=updateform&sa=filter";
		$alist[] = "a=updateform&sa=configuration";
		$alist[] = "a=list&c=mailcontent";
		$driverapp = $gbl->getSyncClass($this->__masterserver, $this->syncserver, 'autoresponder');

		$alist[] = "a=list&c=autoresponder";
		$alist[] = create_simpleObject(array('url' => "http://webmail." . $this->getParentName(), 'purl' => "a=updateform&sa=webmail&l[class]=mailaccount&l[nname]=$this->nname", 'target' => "target='_blank'"));

		return $alist;
	}

	static function AddListForm($parent, $class)
	{
		return self::addform($parent, $class);
	}

	function getSpecialParentClass()
	{
		return "mmail";
	}

	function postAdd()
	{
		$parent = $this->getTrueParentO();

		$this->realpass = $this->password;
		$this->password = crypt($this->password, '$1$'.randomString(8).'$');

		if ($this->isOn("simple_add_f")) {
			$this->priv = clone $parent->priv;
			$this->password = $parent->getTrueParentO()->password;
			$this->realpass = $parent->getTrueParentO()->realpass;
		}

		$spam = new Spam($this->__masterserver, $this->__readserver, $this->nname);
		$spam->initThisdef();
		$spam->inheritSyncServer($this);
	//	$res['syncserver'] = $this->syncserver;
		$res['spam_hit'] = $parent->getObject('spam')->spam_hit;
		$spam->subject_tag = $parent->getObject('spam')->subject_tag;
		$spam->status = $parent->getObject('spam')->status;
		$spam->create($res);
		$this->cpstatus = 'on';
		$this->forward_status = 'on';
		$this->addObject('spam', $spam);
		$this->lxclientpostAdd();
	}

	static function add($parent, $class, $param)
	{
		global $login;

		if ($parent->isClient()) {
			$param['nname'] = "{$param['nname']}@{$param['real_clparent_f']}";
			$param['syncserver'] = $parent->mmailsyncserver;
		} else {
			$param['nname'] = "{$param['nname']}@$parent->nname";
			$param['syncserver'] = $parent->syncserver;
		}

		if (!validate_email($param['nname'])) {
			throw new lxException($login->getThrow("invalid_email"), '', $param['nname']);
		}

		// Not needed. The child will automatically inherit the syncserver.
	//	$param['syncserver'] = $parent->syncserver;
		$param['nname'] = trim($param['nname']);
	//	$param['parent_clname'] = "mmail-{$param['real_clparent_f']}";

		if (exists_in_db(null, "mailforward", $param['nname'])) {
			throw new lxException($login->getThrow("mailforward_already_exists"), '', $param['nname']);
		}

		$param = parent::add($parent, $class, $param);

		return $param;
	}

	function updateClear_spam_db($param)
	{
		$param['something'] = null;

		return $param;
	}

	function updateTrain_as_spam($param)
	{
		$this->updateAccountSel($param, "train_as_spam");

		return $param;
	}

	function updateTrain_as_system_spam($param)
	{
		$this->updateAccountSel($param, "train_as_system_spam");

		return $param;
	}

	function updateTrain_as_system_ham($param)
	{
		$this->updateAccountSel($param, "train_as_system_ham");

		return $param;
	}

	function updateTrain_as_ham($param)
	{
		$this->updateAccountSel($param, "train_as_ham");

		return $param;
	}

	function createShowClist($subaction)
	{
		$clist = null;

		return $clist;
	}

	static function defaultParentClass($parent)
	{
		return "mmail";
	}

	static function addform($parent, $class, $typetd = null)
	{
		if ($parent->isClient()) {
			$list = get_namelist_from_objectlist($parent->getList('domain'));
			$vv = array('var' => 'real_clparent_f', 'val' => array('s', $list));
			$vlist['nname'] = array('m', array('posttext' => "@", 'postvar' => $vv));
		} else {
			$vlist['nname'] = array('m', array('posttext' => "@$parent->nname"));
		}

		$vlist['password'] = "";

		$qvlist = getQuotaListForClass('mailaccount', array());
		$vlist = lx_array_merge(array($vlist, $qvlist));

		$ret['variable'] = $vlist;
		$ret['action'] = "add";

		return $ret;
	}
}

class all_mailaccount extends mailaccount
{
	static $__desc = array("n", "", "all_mailaccount");
	static $__desc_parent_name_f = array("n", "", "domain");
	static $__desc_parent_clname = array("n", "", "domain");

	function isSelect()
	{
		return false;
	}

	static function initThisListRule($parent, $class)
	{
		global $login;

		if (!$parent->isAdmin()) {
			throw new lxException($login->getThrow("only_admin_can_access"));
		}

		return "__v_table";
	}

	static function createListSlist($parent)
	{
		$nlist['nname'] = null;
		$nlist['parent_clname'] = null;

		return $nlist;
	}

	static function AddListForm($parent, $class)
	{
		return null;
	}

	static function createListAlist($parent, $class)
	{
		return all_domain::createListAlist($parent, $class);
	}

	static function createListNlist($parent, $view)
	{
		$nlist['nname'] = '50%';
		$nlist['parent_name_f'] = '50%';
		
		return $nlist;
	}
}

