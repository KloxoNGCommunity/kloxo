<?php
	$a = trim(explode(',', trim($subjectAltName)));

	foreach ($a as $k => $v) {
		$a[$k] = 'DNS:' . $v;
	}

	$SAN = implode(', ', $a);
?>
[req]
distinguished_name = req_distinguished_name
req_extensions = v3_req

[req_distinguished_name]
countryName = <?php echo $countryName; ?>

stateOrProvinceName = <?php echo $stateOrProvinceName; ?>

localityName = <?php echo $localityName; ?>

organizationalName = <?php echo $organizationName; ?>

organizationalUnitName = <?php echo $organizationalUnitName; ?>

emailAddress = <?php echo $emailAddress; ?>

commonName = <?php echo $commonName; ?>

commonName_max = 64

[v3_req]
basicConstraints = CA:FALSE
keyUsage = nonRepudiation, digitalSignature, keyEncipherment
# subjectAltName = @alt_names
subjectAltName = "<?php echo $SAN; ?>"


[alt_names]
<?php
	$b = explode(',', trim($subjectAltName));

	foreach ($b as $k => $v) {
		$c = (int)$k + 1
?>
DNS.<?php echo $c; ?> = <?php echo $v; ?>

<?php
	}
?>
