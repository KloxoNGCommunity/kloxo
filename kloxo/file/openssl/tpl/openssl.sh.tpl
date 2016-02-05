<?php
	$subj_txt = '';

	if ($emailAddress === 'N/A') {
		$subj_txt .= '';
	} else {
		$subj_txt .= "/emailAddress={$emailAddress}";
	}

	$subj_txt .= "/CN={$commonName}";

	if ($countryName === 'N/A') {
		$subj_txt .= '';
	} else {
		$subj_txt .= "/C={$countryName}";
	}

	if ($stateOrProvinceName === 'N/A') {
		$subj_txt .= '';
	} else {
		$subj_txt .= "/ST={$stateOrProvinceName}";
	}

	if ($localityName === 'N/A') {
		$subj_txt .= '';
	} else {
		$subj_txt .= "/L={$localityName}";
	}

	if ($organizationName === 'N/A') {
		$subj_txt .= '';
	} else {
		$subj_txt .= "/O={$organizationName}";
	}

	if ($organizationalUnitName === 'N/A') {
		$subj_txt .= '';
	} else {
		$subj_txt .= "/OU={$organizationalUnitName}";
	}
?>
#!/bin/sh

openssl req -new -newkey rsa:<?php echo $key_bits; ?> -days 365 -sha256 -nodes -x509 \
	-subj '<?php echo $subj_txt;?>' \
	-keyout <?php echo $name; ?>.key \
	-out <?php echo $name; ?>.crt

openssl req -new -sha256 \
	-subj '<?php echo $subj_txt;?>' \
	-key <?php echo $name; ?>.key \
	-out <?php echo $name; ?>.csr

cat <?php echo $name; ?>.key <?php echo $name; ?>.crt > <?php echo $name; ?>.pem
