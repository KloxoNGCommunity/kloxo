<?php

class Certificate_b extends LxaClass
{
	static $__desc_private_key = array("", "", "private_key");
	static $__desc_certificate = array("", "", "certificate");
	static $__desc_ca_certificate = array("", "", "ca_certificates");
}

class Ssl_data_b extends LxaClass
{
	static $__desc_countryName_r = array("n", "", "countryname");
	static $__desc_stateOrProvinceName_r = array("n", "", "state");
	static $__desc_localityName_r = array("n", "", "city");
	static $__desc_organizationName_r = array("n", "", "organization");
	static $__desc_organizationalUnitName_r = array("n", "", "department_name");
	static $__desc_commonName_r = array("n", "", "common_name");
	static $__desc_emailAddress_r = array("n", "", "email_address");
	static $__desc_subjectAltName_r = array("n", "", "subject_alt_name");
}

class SslCert extends Lxdb
{

	static $__desc = array("", "", "ssl_certificate");

	static $__desc_key_bits = array("", "", "ssl_key_bits");

	static $__desc_nname = array("n", "", "ssl_certificate_name", URL_SHOW);
	static $__desc_certname = array("n", "", "ssl_certificate_name", URL_SHOW);
	static $__desc_syncserver = array("", "", "");
	static $__desc_slave_id = array("", "", "slave_ID (master_is_localhost)");
	static $__desc_text_csr_content = array("t", "", "CSR");
	static $__desc_text_key_content = array("t", "", "Key");
	static $__desc_text_crt_content = array("t", "", "Certificate");
	static $__desc_text_ca_content = array("t", "", "CACert");
	static $__desc_ssl_key_file_f = array("n:F", "", "key_file");
	static $__desc_ssl_crt_file_f = array("n:F", "", "certificate_file");
	static $__desc_ssl_ca_file_f = array("F", "", "certificate_CA_file");
//	static $__desc_ssl_ca_file_f =  array("F", "",  "authority_file");
	static $__desc_upload = array("", "", "data");
	static $__desc_upload_v_uploadfile = array("", "", "upload File");
	static $__desc_upload_v_uploadtxt = array("", "", "upload Txt");

	static $__acdesc_update_update = array("", "", "certificate_info");
	static $__acdesc_update_ssl_kloxo = array("", "", "set_ssl_for_kloxo");
	static $__acdesc_update_ssl_hypervm = array("", "", "set_ssl_for_hypervm");

	function updateform($subaction, $param)
	{
		if (csa($subaction, "ssl_")) {
			$this->slave_id = "localhost";
			$vlist['slave_id'] = array('s', get_all_pserver());

			return $vlist;
		}

		if ($this->isOn('upload_status')) {
			$string = null;
			$res = openssl_x509_read($this->text_crt_content);
			$ar = openssl_x509_parse($res);
			$string .= "{$ar['name']} {$ar['subject']['CN']}";
			$vlist['upload'] = array('M', $string);
			$vlist['text_crt_content'] = null;
			$vlist['text_key_content'] = null;
			$vlist['text_ca_content'] = null;
		} else {
			$vlist['nname'] = $this->certname;
			$vlist["ssl_data_b_s_commonName_r"] = null;
			$vlist["ssl_data_b_s_countryName_r"] = null;
			$vlist["ssl_data_b_s_stateOrProvinceName_r"] = null;
			$vlist["ssl_data_b_s_localityName_r"] = null;
			$vlist["ssl_data_b_s_organizationName_r"] = null;
			$vlist["ssl_data_b_s_organizationalUnitName_r"] = null;
			$vlist["ssl_data_b_s_emailAddress_r"] = null;
			$vlist["ssl_data_b_s_subjectAltName_r"] = null;
			$this->convertToUnmodifiable($vlist);
			$vlist['text_crt_content'] = null;
			$vlist['text_key_content'] = null;
			$vlist['text_csr_content'] = null;
		}

		$vlist['__v_button'] = array();

		return $vlist;
	}

	static function checkAndThrow($publickey, $privatekey, $throwname = null)
	{
		global $gbl, $sgbl, $login, $ghtml;

		if (!self::checkKeyCert($publickey, $privatekey)) {
			if ($gbl->__restore_flag) {
				log_log("restore", "certificate_key_file_corrupted");
			} else {
				throw new lxException($login->getThrow("certificate_key_file_corrupted"), '', $throwname);
			}
		}

	//	dprint("Succeesffully tested <br> <br> ");
	}

	static function checkKeyCert($public_key, $privatekey)
	{
		$pubkey_res = openssl_get_publickey($public_key);

		$s = "mystring";
		$priv_key_res = openssl_get_privatekey($privatekey, "");
		openssl_private_encrypt($s, $encrypted_string, $priv_key_res);
		openssl_public_decrypt($encrypted_string, $decrypted_string, $public_key);

		return ($decrypted_string === $s);
	}

	static function createListNlist($parent, $view)
	{
		$nlist['nname'] = '80%';
		$nlist['syncserver'] = '20%';

		return $nlist;
	}

	function createShowUpdateform()
	{
		$uflist['update'] = null;

		return $uflist;
	}

	static function createListAlist($parent, $class)
	{
		$alist[] = "a=list&c=$class";
		$alist[] = "a=addform&c=$class";
		$alist[] = "a=addform&c=$class&dta[var]=upload&dta[val]=uploadfile";
		$alist[] = "a=addform&c=$class&dta[var]=upload&dta[val]=uploadtxt";

		return $alist;
	}

	function createShowPropertyList(&$alist)
	{
		global $gbl, $sgbl, $login, $ghtml;

		$prgm = $sgbl->__var_program_name;

		$alist['property'][] = 'a=show';

		if ($login->isAdmin()) {
			$alist['property'][] = "a=updateform&sa=ssl_$prgm";
		}
	}

	function updateSetProgramSSL($param)
	{
		global $login;

		$contentscer = $this->text_crt_content;
		$contentskey = $this->text_key_content;
		$contentsca = trim($this->text_ca_content);

		if (!$contentscer || !$contentskey) {
			throw new lxException($login->getThrow("certificate_key_file_empty"));
		}

		self::checkAndThrow($contentscer, $contentskey, null);

	//	$contentpem = "$contentscer\n$contentskey";

		// MR -- make the same as program.pem; like inside lighttpd.conf example inside
		$contentpem = "$contentskey\n$contentscer";

		rl_exec_get(null, $param['slave_id'], array("sslcert", "setProgramSsl"), array($contentpem, $contentsca, $contentscer, $contentskey));
	}


	static function setProgramSsl($contentpem, $contentsca, $contentscrt, $contentskey)
	{
		lfile_put_contents("../etc/program.pem", $contentpem);
		lfile_put_contents("../etc/program.crt", $contentscrt);
		lfile_put_contents("../etc/program.key", $contentskey);

		lxfile_unix_chown("../etc/program.pem", "lxlabs:lxlabs");
		lxfile_unix_chown("../etc/program.crt", "lxlabs:lxlabs");
		lxfile_unix_chown("../etc/program.key", "lxlabs:lxlabs");

		if ($contentsca) {
			lfile_put_contents("../etc/program.ca", $contentsca);
			lxfile_unix_chown("../etc/program.ca", "lxlabs:lxlabs");
		}
	}

	function updatessl_hypervm($param)
	{
		$this->updateSetProgramSSL($param);
	}

	function updatessl_kloxo($param)
	{
		$this->updateSetProgramSSL($param);
	}

	function deleteSpecific()
	{
		global $gbl;

		$parent = $this->getParentO();

		if ($parent->getClass() === 'web') {
			$name = $parent->nname;
			$user = $parent->customer_name;

		//	$path = "/home/{$user}/ssl";
			$path = "/home/kloxo/client/{$user}/ssl";

			exec("'rm' -rf {$path}/{$name}.*");

			lxshell_return("sh", "/script/fixweb", "--domain={$name}", "--nolog");
		//	createRestartFile($gbl->getSyncClass(null, $this->syncserver, 'web'));
			createRestartFile("restart-web");
		}
	}

	static function add($parent, $class, $param)
	{
		global $login;

		if (isset($param['upload'])) {
			if ($param['upload'] === 'uploadfile') {
				$key_file = $_FILES['ssl_key_file_f']['tmp_name'];
				$crt_file = $_FILES['ssl_crt_file_f']['tmp_name'];
				$ca_file = $_FILES['ssl_ca_file_f']['tmp_name'];

				if (!$key_file || !$crt_file) {
					throw new lxException($login->getThrow("key_crt_files_needed"));
				}

				$param['text_key_content'] = lfile_get_contents($key_file);
				$param['text_crt_content'] = lfile_get_contents($crt_file);

				if ($ca_file && lxfile_exists($ca_file)) {
					$param['text_ca_content'] = lfile_get_contents($ca_file);
				}
			}

			self::checkAndThrow($param['text_crt_content'], $param['text_key_content']);

			$param['upload_status'] = 'on';
		} else {
			$param['upload_status'] = 'off';

			if ($parent->getClass() === 'web') {
				$param['nname'] = $parent->nname;
			//	$param['ssl_data_b_s_commonName_r'] = '*.' . $parent->nname;
				$param['ssl_data_b_s_commonName_r'] = $parent->nname;
				$param['ssl_data_b_s_subjectAltName_r'] = $parent->nname;
			}
		}

		$param['certname'] = $param['nname'];

		return $param;
	}

	function postAdd()
	{
		$parent = $this->getParentO();

		if (!$this->isOn('upload_status')) {
			$this->createNewcertificate();
		}

		if ($parent->getClass() === 'web') {
			$this->createDomainSSL();
		}
	}

	function createDomainSSL()
	{
		global $gbl, $login;

		$parent = $this->getParentO();
		$name = $parent->nname;
		$user = $parent->customer_name;

	//	$path = "/home/{$user}/ssl";
		$path = "/home/kloxo/client/{$user}/ssl";

		$contentscrt = $this->text_crt_content;
		$contentskey = $this->text_key_content;
		$contentscsr = $this->text_csr_content;
		$contentsca = trim($this->text_ca_content);

		if (!$contentscrt || !$contentskey) {
			throw new lxException($login->getThrow("certificate_key_file_empty"));
		}

		self::checkAndThrow($contentscrt, $contentskey, $name);

		lfile_put_contents("{$path}/{$name}.crt", $contentscrt);
		lfile_put_contents("{$path}/{$name}.key", $contentskey);
		lfile_put_contents("{$path}/{$name}.csr", $contentscsr);

		$contentpem = "$contentskey\n$contentscrt";

		lfile_put_contents("$path/$name.pem", $contentpem);

		if ($contentsca) {
			lfile_put_contents("{$path}/{$name}.ca", $contentsca);
		}

		exec("sh /script/fixweb --domain={$name} --nolog");
	//	createRestartFile($gbl->getSyncClass(null, $this->syncserver, 'web'));
		createRestartFile("restart-web");
	}

	function isSelect()
	{
		return true;

		$db = new Sqlite($this->__masterserver, "sslipaddress");
		$res = $db->getRowsWhere("sslcert = '$this->certname'", array('nname'));

		return ($res ? false : true);
	}

	static function addform($parent, $class, $typetd = null)
	{
		global $gbl, $sgbl, $login, $ghtml;

		if ($parent->getClass() === 'web') {
			$nname = array('M', $parent->nname);
		//	$cname = array('M', "*.{$parent->nname}");
			$cname = array('M', $parent->nname);
			$saname = array('M', $parent->nname);
		} else {
			$nname = null;
			$cname = null;
			$saname = null;
		}

		if ($typetd['val'] === 'uploadfile') {
			$vlist['nname'] = $nname;
			$vlist['ssl_key_file_f'] = null;
			$vlist['ssl_crt_file_f'] = null;
			$vlist['ssl_ca_file_f'] = null;
			$sgbl->method = 'post';
		} else if ($typetd['val'] === 'uploadtxt') {
			$vlist['nname'] = $nname;
			$vlist['text_crt_content'] = null;
			$vlist['text_key_content'] = null;
			$vlist['text_ca_content'] = null;
		} else {
			include "lib/html/countrycode.inc";

			$vlist['nname'] = $nname;

			// MR -- add key_bits options
			$vlist['key_bits'] = array("s", array("2048", "1024", "512"));

			foreach ($gl_country_code as $key => $name) {
				$temp[] = "$key:$name";
			}

			$vlist["ssl_data_b_s_commonName_r"] = $cname;
		//	$vlist["ssl_data_b_s_subjectAltName_r"] = $saname;

			$vlist["ssl_data_b_s_countryName_r"] = array("s", $temp);
			$vlist["ssl_data_b_s_stateOrProvinceName_r"] = null;
			$vlist["ssl_data_b_s_localityName_r"] = null;
			$vlist["ssl_data_b_s_organizationName_r"] = null;
			$vlist["ssl_data_b_s_organizationalUnitName_r"] = null;
			$vlist["ssl_data_b_s_emailAddress_r"] = null;

		}

		$ret['action'] = 'add';
		$ret['variable'] = $vlist;

		return $ret;
	}

	function createNewcertificate()
	{
		global $gbl, $sgbl, $login, $ghtml;

		foreach ($this->ssl_data_b as $key => $value) {
			if (!cse($key, "_r")) {
				continue;
			}
			$nk = strtil($key, "_r");
			$temp[$nk] = $value;
		}

		$parent = $this->getParentO();

		if ($parent->getClass() === 'web') {
			$name = $parent->nname;
			$user = $parent->customer_name;

			$temp['name'] = $parent->nname;
		//	$temp['commonName'] = "*.{$parent->nname}";
			$temp['commonName'] = $parent->nname;
		//	$temp['subjectAltName'] = $parent->nname;
		}

		foreach ($temp as $key => $t) {
			if ($key === "countryName") {
				$l = explode(":", $t);

				$name = $l[0];
			} else {
				$name = $t;
			}

			$input[$key] = $name;
		}

		$dn = $input;

	//	unset($dn['nname']);

		if ($parent->getClass() === 'web') {
		//	$cnffile = "/home/{$user}/ssl/{$name}.cnf";
			$cnffile = "/home/kloxo/client/{$user}/ssl/{$name}.cnf";
		} else {
			$cnffile = '/tmp/openssl.cnf';
		}


		$config = array('config' => $cnffile,
			'digest_alg' => 'sha256',
			'private_key_bits' => (int)$this->key_bits,
			'private_key_type' => OPENSSL_KEYTYPE_RSA,
			'encrypt_key' => true,
			'encrypt_key_cipher' => OPENSSL_CIPHER_3DES
		);

		$tplsource = getLinkCustomfile("/opt/configs/openssl/tpl", "openssl.cnf.tpl");

		$tpltarget = $cnffile;

		$tpl = lfile_get_contents($tplsource);

		$tplparse = getParseInlinePhp($tpl, $input);

		if ($parent->getClass() === 'web') {
		//	if (!file_exists("/home/{$user}/ssl")) {
			if (!file_exists("/home/kloxo/client/{$user}/ssl")) {
				mkdir("/home/kloxo/client/{$user}/ssl");
			}
		}

		if ($tplparse) {
			lfile_put_contents($tpltarget, $tplparse);
		}

		$privkey = openssl_pkey_new($config);
		openssl_pkey_export($privkey, $text_key_content);
		$csr = openssl_csr_new($dn, $privkey, $config);
		openssl_csr_export($csr, $text_csr_content);
		$sscert = openssl_csr_sign($csr, null, $privkey, 3650);
		openssl_x509_export($sscert, $text_crt_content);

		// MR -- not using openssl error message because so many warning but process still work.
		while (($e = openssl_error_string()) !== false) {
		//	throw new lxException(str_replace(' ', '_', $e), '', $parent->nname);
		//	throw new lxException($e, '', $parent->nname);
		}

		$this->text_key_content = $text_key_content;
		$this->text_csr_content = $text_csr_content;
		$this->text_crt_content = $text_crt_content;
	}

	static function getSslCertnameFromIP($ipname)
	{
		return fix_nname_to_be_variable($ipname);
	}

	function isSync() { return false; }
}

