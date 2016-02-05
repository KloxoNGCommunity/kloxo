<?php
	$req = '';

	if ($action === 'test') || ($action === 'staging')) {
		$req .= "----staging ";
	}

	$req .= "--rsa-key-size {$key_bits} ";

	if ($emailAddress === 'N/A') {
		$req .= '';
	} else {
		$req .= "--email {$emailAddress} ";
	}

	$san_lst = explode(" ", $subjectAltName);
	
	foreach ($san_lst as $k => $v) {
		$req .= "--domain {$v} ";
	}
?>
#!/bin/sh

letsencrypt-auto certonly --agree-tos --text --renew-by-default \
	--duplicate --webroot --webroot-path /var/run/letsencrypt \
	<?php echo $req; ?>
