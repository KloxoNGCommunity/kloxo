<?php
	$subj_txt = '';

	if ($countryName === '.') {
		$subj_txt .= '';
	} else {
		$subj_txt .= "/C={$countryName}";
	}

	if ($stateOrProvinceName === '.') {
		$subj_txt .= '';
	} else {
		$subj_txt .= "/ST={$stateOrProvinceName}";
	}

	if ($localityName === '.') {
		$subj_txt .= '';
	} else {
		$subj_txt .= "/L={$localityName}";
	}

	if ($organizationName === '.') {
		$subj_txt .= '';
	} else {
		$subj_txt .= "/O={$organizationName}";
	}

	if ($organizationalUnitName === '.') {
		$subj_txt .= '';
	} else {
		$subj_txt .= "/OU={$organizationalUnitName}";
	}

	if ($emailAddress === '.') {
		$subj_txt .= '';
	} else {
		$subj_txt .= "/emailAddress={$emailAddress}";
	}
	
	$cn_lst = explode(" ", $commonName);
	
	foreach ($cn_lst as $k => $v) {
		$subj_txt .= "/CN={$v}";
	}

?>
#!/bin/sh

openssl req -new -newkey rsa:<?php echo $key_bits; ?> -days 365 -sha256 -nodes -x509 \
	-keyout <?php echo $nname; ?>.key \
	-out <?php echo $nname; ?>.crt \
	-subj '<?php echo $subj_txt;?>'

openssl req -new -sha256 \
	-subj '<?php echo $subj_txt;?>' \
	-key <?php echo $nname; ?>.key \
	-out <?php echo $nname; ?>.csr
