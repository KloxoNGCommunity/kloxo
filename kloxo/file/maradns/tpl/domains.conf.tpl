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

//    $email = str_replace("@", ".", $email);
$refresh = isset($refresh) && strlen($refresh) > 0 ? $refresh : 3600;
$retry = isset($retry) && strlen($retry) > 0 ? $retry : 1800;
$expire = isset($expire) && strlen($expire) > 0 ? $expire : 604800;
$minimum = isset($minimum) && strlen($minimum) > 0 ? $minimum : 1800;
?>
/origin <?php echo $domainname; ?>. ~
% SOA <?php echo $nameserver; ?>. <?php echo $email; ?>. <?php echo $serial; ?> <?php echo $refresh; ?> <?php echo $retry; ?> <?php echo $expire; ?> <?php echo $minimum; ?> ~
<?php
foreach($dns_records as $k => $o) {
    $ttl = isset($o->ttl) && strlen($o->ttl) ? $o->ttl : $ttl;

    switch($o->ttype) {
        case "ns":
            $value = $o->param;
            if ($o->param === $o->hostname) {
                $key = $domainname;
            } else {
                if (($o->hostname === '') || (!$o->hostname) || ($o->hostname === '__base__')) {
                    $key = $domainname;
                } else {
                    $key = $o->hostname;
                }
            }
?>
<?php echo $key; ?>. NS <?php echo $value; ?>. ~
<?php
            break;
        case "mx":
            $v = $o->priority;
            $value = $o->param;
?>
% <?php echo $ttl; ?> MX <?php echo $v; ?> <?php echo $value; ?>. ~
<?php
            break;
        case "aaaa":
            $key = $o->hostname;
            $value = $o->param;

            if ($key !== "__base__") {
                $key = "$key.%";
            } else {
                $key = "%";
            }
?>
<?php echo $key; ?> <?php echo $ttl; ?> AAAA <?php echo $value; ?> ~
<?php
            break;
        case "a":
            $key = $o->hostname;
            $value = $o->param;

            if ($key !== "__base__") {
                $key = "$key.%";
            } else {
                $key = "%";
            }
?>
<?php echo $key; ?> <?php echo $ttl; ?> A <?php echo $value; ?> ~
<?php
            break;
        case "cn":
        case "cname":
            $key = $o->hostname;
            $value = $o->param;
            if (isset($arecord[$value])) {
                $rvalue = $arecord[$value];
                $key .= ".%";
?>
<?php echo $key; ?> <?php echo $ttl; ?> A <?php echo $rvalue; ?> ~
<?php
            } else {
                $key .= ".%";

                if ($value !== "__base__") {
                    $value = "$value.%";
                } else {
                    $value = "%";
                }

?>
<?php echo $key; ?> <?php echo $ttl; ?> CNAME <?php echo $value; ?> ~
<?php
            }

            break;
        case "fcname":
            $key = $o->hostname;
            $value = $o->param;
            $key .= ".%";

            if ($value !== "__base__") {
                if (strpos($value, ".") !== false) {
					// no action
				} else {
                    $value = "$value.";
                }
            } else {
                $value = "%";
            }
?>
<?php echo $key; ?> <?php echo $ttl; ?> CNAME <?php echo $value; ?> ~
<?php
            break;
        case "txt":
            $key = $o->hostname;
            $value = str_replace("  ", " ", $o->param);

            if($value === null) { continue; }

            if ($key !== "__base__") {
                $key = "$key.%";
            } else {
                $key = "%";
            }

            $value = str_replace("<%domain>", $domainname, $value);
            $value = str_replace("|", "\\x7c", $value);
            $value = str_replace("#", "\\x23", $value);
?>
<?php echo $key; ?> <?php echo $ttl; ?> TXT '<?php echo $value; ?>' ~
<?php
            if (strpos($value, "v=spf1") !== false) {
?>
<?php echo $key; ?> <?php echo $ttl; ?>  SPF '<?php echo $value; ?>' ~
<?php
            }

            break;
        case "srv":
            $key = $o->hostname;
            $param = $o->param;
            $proto = $o->proto;
            $priority = $o->priority;
            $service = $o->service;
            $port = $o->port;

            if($o->param === null) { continue; }

            if ($key !== "__base__") {
                $key = "$key.%";
            } else {
                $key = "%";
            }

            $weight = ($o->weight == null || strlen($o->weight) == 0) ? 0 : $o->weight;
?>
_<?php echo $service; ?>._<?php echo $proto; ?>.% <?php echo $ttl; ?> IN SRV <?php echo $priority; ?> <?php echo $weight; ?> <?php echo $port; ?> <?php echo $param; ?>. ~
<?php
            break;
    }
}
?>

### end - dns of '<?php echo $domainname; ?>' - do not remove/modify this line

