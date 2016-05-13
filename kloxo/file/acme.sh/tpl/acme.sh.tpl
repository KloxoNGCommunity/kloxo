<?php
	$req = '';
	$dom = '';

	// MR -- this is for testing
//	$req .= "--staging ";

	$req .= "--keylength {$key_bits} ";

	if ($emailAddress === 'N/A') {
		$req .= '';
	} else {
		$req .= "--accountemail {$emailAddress} ";
	}

	$doms = explode(" ", $subjectAltName);

	$count = 1;

	foreach ($doms as $k => $v) {
		if ($count === 1) {
			$basedom = $v;
		}

		$dom .= "\t--domain $v  \\\n";

		$count++;
	}
?>
#!/bin/sh

/usr/bin/acme.sh --issue --webroot /var/run/letsencrypt  \
<?php echo $dom; ?>
	<?php echo $req; ?> >> /var/log/acme.sh/acme.sh.log \
	&> /var/log/acme.sh/acme.sh.log

if [ -f /root/.acme.sh/<?php echo $basedom; ?>/ca.cer ] ; then
	cd /root/.acme.sh/<?php echo $basedom; ?>

	cat <?php echo $basedom; ?>.key <?php echo $basedom; ?>.cer ca.cer > <?php echo $basedom; ?>.pem
fi