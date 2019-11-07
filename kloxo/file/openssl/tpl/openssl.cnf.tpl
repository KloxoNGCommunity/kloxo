<?php
	$subjectAltName = $a = explode(',', str_replace(' ', '', $subjectAltName));

	foreach ($a as $k => $v) {
		$a[$k] = 'DNS:' . $v;
	}

	$SAN = implode(', ', $a);
?>
[req]
prompt = no
distinguished_name = req_distinguished_name
#req_extensions = v3_req

[req_distinguished_name]
countryName = <?=$countryName;?>

stateOrProvinceName = <?=$stateOrProvinceName;?>

localityName = <?=$localityName;?>

organizationName = <?=$organizationName;?>

organizationalUnitName = <?=$organizationalUnitName;?>

emailAddress = <?=$emailAddress;?>

commonName = <?=$commonName;?>

<?php
/*
?>
[v3_req]
basicConstraints = CA:FALSE
keyUsage = nonRepudiation, digitalSignature, keyEncipherment
subjectAltName = @alt_names
# subjectAltName = "<?=$SAN;?>"

[alt_names]
<?php
	foreach ($subjectAltName as $k => $v) {
		$c = (int)$k + 1;
?>
DNS.<?=$c;?> = <?=$v;?>

<?php
	}
*/
?>
