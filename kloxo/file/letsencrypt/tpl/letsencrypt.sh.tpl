<?php
	$req = '';
	$dom = '';

	// MR -- this is for testing
//	$req .= "--staging ";

	$req .= "--rsa-key-size {$key_bits} ";

	if ($emailAddress === 'N/A') {
		$req .= '';
	} else {
		$req .= "--email {$emailAddress} ";
	}

/*
	$san = str_replace(" ", ",", $subjectAltName);

	$dom .= "--domains {$san} ";
*/

	$doms = explode(" ", $subjectAltName);

	foreach ($doms as $k => $v) {
		$dom .= "\t--domain $v  \\\n";
	}
?>
#!/bin/sh

letsencrypt-auto certonly --agree-tos --text --renew-by-default  \
	--webroot --webroot-path /var/run/letsencrypt  \
	<?php echo $req; ?> \
<?php echo $dom; ?>
	|| exit 1
