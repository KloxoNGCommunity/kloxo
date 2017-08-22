### begin - dns of '<?php echo $domainname; ?>' - do not remove/modify this line

<?php
$nameserver = null;

foreach($dns_records as $dns) {
    if ($dns->ttype === "ns") {
        if (!$nameserver) {
            $nameserver = $dns->param;
        }
    }

    if ($dns->ttype === 'a') {
        $arecord[$dns->hostname] = $dns->param;
    }
}

if ($soanameserver) {
    $nameserver = $soanameserver;
}
?>
Z<?php echo $domainname; ?>:<?php echo $nameserver; ?>:<?php echo $email; ?>:<?php echo $serial; ?>:::::<?php echo $ttl; ?>

<?php
foreach($dns_records as $k => $o) {
    switch($o->ttype) {
        case "ns":
            $key = $o->hostname;
            $value = $o->param;

            $value = trim($value, '.');

            if ($key === $value) {
                $key = $domainname;
            } else {
                if (($key === '') || (!$key) || ($key === '__base__')) {
                    $key = $domainname;
                } else {
                    if (strpos($key, '__base__') !== false) {
                        $key = str_replace('__base__', $domainname, $key);
                    } else {
                        $key = "{$key}.{$domainname}";
                    }
                }
            }
?>
&<?php echo $key; ?>::<?php echo $value; ?>:<?php echo $ttl; ?>

<?php
            break;
        case "mx":
            $value = $o->param;
            $priority = $o->priority;

            $value = trim($value, '.');
?>
@<?php echo $domainname; ?>::<?php echo $value; ?>:<?php echo $priority; ?>:<?php echo $ttl; ?>

<?php
            break;
        case "a":
            $key = $o->hostname;
            $value = $o->param;

            if ($key === '*') {
?>
+*.<?php echo $domainname; ?>:<?php echo $value; ?>:<?php echo $ttl; ?>

<?php
                break;
            }

            if ($key !== "__base__") {
                $key = "{$key}.{$domainname}";
            } else {
                $key = $domainname;
            }
?>
+<?php echo $key; ?>:<?php echo $value; ?>:<?php echo $ttl; ?>

<?php
            break;
/*
        case "aaaa":
            $key = $o->hostname;
			$value = $o->param;
		//	$value = escaped_hex(ipv6_expand($o->param)); // TODO: unfinish work
			
			
            if ($key === '*') {
?>
:*.<?php echo $domainname; ?>:28:<?php echo $value; ?>:<?php echo $ttl; ?>

<?php
                break;
            }

            if ($key !== "__base__") {
                $key = "{$key}.{$domainname}";
            } else {
                $key = $domainname;
            }
?>
:<?php echo $key; ?>:28:<?php echo $value; ?>:<?php echo $ttl; ?>

<?php
            break;
*/
        case "cn":
        case "cname":
            $key = $o->hostname;
            $value = $o->param;

            $value = trim($value, '.');

            if (isset($arecord[$value])) {
                $rvalue = $arecord[$value];

                if ($key === '*') {
?>
+*.<?php echo $domainname; ?>:<?php echo $rvalue; ?>:<?php echo $ttl; ?>

<?php
                } else {
                    $key .= ".$domainname";
?>
+<?php echo $key; ?>:<?php echo $rvalue; ?>:<?php echo $ttl; ?>

<?php
                }
            } else {
                if ($value !== "__base__") {
                    $value = "{$value}.{$domainname}";
                } else {
                    $value = $domainname;
                }

                if ($key === '*') {
?>
C*.<?php echo $domainname; ?>:<?php echo $value; ?>:<?php echo $ttl; ?>

<?php
                } else {
                    $key .= ".{$domainname}";
?>
C<?php echo $key; ?>:<?php echo $value; ?>:<?php echo $ttl; ?>

<?php
                }
            }

            break;
        case "fcname":
            $key = $o->hostname;
            $value = $o->param;

            $value = trim($value, '.');

            if ($value !== "__base__") {
                $value = $value;
            } else {
                $value = "$domainname";
            }

            $key .= ".{$domainname}";
?>
C<?php echo $key; ?>:<?php echo $value; ?>:<?php echo $ttl; ?>

<?php
            break;
        case "txt":
            $key = $o->hostname;
            $value = $o->param;

            if($o->param === null) continue;

            if ($key !== "__base__") {
                $key = "$key.$domainname";
            } else {
                $key = "$domainname";
            }

            $value = str_replace("<%domain%>", $domainname, $value);
            $value = str_replace("__base__", $domainname, $value);
            $value = str_replace(":", "\\072", $value);
            $value = str_replace(" ", "\\040", $value);
?>
'<?php echo $key; ?>:<?php echo $value; ?>:<?php echo $ttl; ?>

<?php
            break;
        case "srv":
?>
### no implementing yet for SRV record
<?php
            break;
        case "caa":
?>
### no implementing yet for CAA record
<?php
			break;

    }
}
?>

### end - dns of '<?php echo $domainname; ?>' - do not remove/modify this line

