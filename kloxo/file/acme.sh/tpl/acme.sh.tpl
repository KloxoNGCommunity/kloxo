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

logdir="/var/log/acme.sh"
rootpath="/root/.acme.sh"
sslpath="/home/kloxo/ssl"
maindom="<?php echo $basedom; ?>"

if [ -f ${rootpath}/${maindom}/ca.cer ] ; then
	action="--force --renew"
else
	action="--issue"
fi

## MR -- change '--webroot /var/run/letsencrypt' to '--standalone'
/usr/bin/acme.sh ${action} --standalone  \
<?php echo $dom; ?>
	<?php echo $req; ?> >> ${logdir}/acme.sh.log \
	&> ${logdir}/acme.sh_temp.log

if [ -f ${logdir}/acme.sh_temp.log ] ; then
	cat ${logdir}/acme.sh_temp.log >> ${logdir}/acme.sh.log
	'rm' -f ${logdir}/acme.sh_temp.log
fi

if [ -f ${rootpath}/${maindom}/ca.cer ] ; then
	cd ${rootpath}/${maindom}

	cat ${maindom}.key ${maindom}.cer ca.cer > ${maindom}.pem

	for i in .ca .crt .key .pem ; do
		if [ "${i}" == ".ca" ] ; then
			slink="ln -sf ${rootpath}/${maindom}/ca.cer ${sslpath}/${maindom}${i}"
		elif [ "${i}" == ".crt" ] ; then
			slink="ln -sf ${rootpath}/${maindom}/${maindom}.cer ${sslpath}/${maindom}${i}"
		else
			slink="ln -sf ${rootpath}/${maindom}/${maindom}${i} ${sslpath}/${maindom}${i}"
		fi

		echo "[$(date)] ${slink}" >> ${logdir}/acme.sh.log
		${slink}
	done
fi