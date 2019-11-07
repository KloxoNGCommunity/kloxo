### begin - dns of '<?=$domainname;?>' - do not remove/modify this line

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
Z<?=$domainname;?>:<?=$nameserver;?>:<?=$email;?>:<?=$serial;?>:::::<?=$ttl;?>

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
&<?=$key;?>::<?=$value;?>:<?=$ttl;?>

<?php
            break;
        case "mx":
            $value = $o->param;
            $priority = $o->priority;

            $value = trim($value, '.');
?>
@<?=$domainname;?>::<?=$value;?>:<?=$priority;?>:<?=$ttl;?>

<?php
            break;
        case "a":
            $key = $o->hostname;
            $value = $o->param;

            if ($key === '*') {
?>
+*.<?=$domainname;?>:<?=$value;?>:<?=$ttl;?>

<?php
                break;
            }

            if ($key !== "__base__") {
                $key = "{$key}.{$domainname}";
            } else {
                $key = $domainname;
            }
?>
+<?=$key;?>:<?=$value;?>:<?=$ttl;?>

<?php
            break;
/*
        case "aaaa":
            $key = $o->hostname;
			$value = $o->param;
		//	$value = escaped_hex(ipv6_expand($o->param)); // TODO: unfinish work
			
			
            if ($key === '*') {
?>
:*.<?=$domainname;?>:28:<?=$value;?>:<?=$ttl;?>

<?php
                break;
            }

            if ($key !== "__base__") {
                $key = "{$key}.{$domainname}";
            } else {
                $key = $domainname;
            }
?>
:<?=$key;?>:28:<?=$value;?>:<?=$ttl;?>

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
+*.<?=$domainname;?>:<?=$rvalue;?>:<?=$ttl;?>

<?php
                } else {
                    $key .= ".$domainname";
?>
+<?=$key;?>:<?=$rvalue;?>:<?=$ttl;?>

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
C*.<?=$domainname;?>:<?=$value;?>:<?=$ttl;?>

<?php
                } else {
                    $key .= ".{$domainname}";
?>
C<?=$key;?>:<?=$value;?>:<?=$ttl;?>

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
C<?=$key;?>:<?=$value;?>:<?=$ttl;?>

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
'<?=$key;?>:<?=$value;?>:<?=$ttl;?>

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

### end - dns of '<?=$domainname;?>' - do not remove/modify this line

