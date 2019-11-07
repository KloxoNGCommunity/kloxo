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

openssl req -new -newkey rsa:<?=$key_bits;?> -days 365 -sha256 -nodes -x509 \
	-subj '<?=$subj_txt;?>' \
	-keyout <?=$name;?>.key \
	-out <?=$name;?>.crt

openssl req -new -sha256 \
	-subj '<?=$subj_txt;?>' \
	-key <?=$name;?>.key \
	-out <?=$name;?>.csr

#openssl dhparam -out <?=$name;?>.dhp <?=$key_bits;?>


#cat <?=$name;?>.key <?=$name;?>.crt <?=$name;?>.dhp > <?=$name;?>.pem
cat <?=$name;?>.key <?=$name;?>.crt > <?=$name;?>.pem
