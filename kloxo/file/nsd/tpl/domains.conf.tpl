;;; begin - dns of '<?php echo $domainname; ?>' - do not remove/modify this line

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

$email = str_replace("@", ".", $email);
$refresh = isset($refresh) && strlen($refresh) > 0 ? $refresh : 3600;
$retry = isset($retry) && strlen($retry) > 0 ? $retry : 1800;
$expire = isset($expire) && strlen($expire) > 0 ? $expire : 604800;
$minimum = isset($minimum) && strlen($minimum) > 0 ? $minimum : 3600;
?>
$ORIGIN <?php echo $domainname; ?>.
$TTL <?php echo $ttl; ?>

@ IN SOA <?php echo $nameserver; ?>. <?php echo $email; ?>. ( <?php echo $serial; ?> <?php echo $refresh; ?> <?php echo $retry; ?> <?php echo $expire; ?> <?php echo $minimum; ?> )
<?php
foreach($dns_records as $k => $o) {
    $ttl = isset($o->ttl) && strlen($o->ttl) ? $o->ttl : $ttl;

    switch($o->ttype) {
        case "ns":
            $key = $o->hostname;
            $value = $o->param;

            $value = trim($value, '.');

            if ($key === $value) {
                $key = '@';
            } else {
                if (($key === '') || (!$key) || ($key === '__base__') || ($key === $domainname)) {
                    $key = '@';
                } else {
                    if (strpos($key, '.__base__') !== false) {
                        $key = str_replace('.__base__', '', $key);
                    }

                    if (strpos($key, ".{$domainname}") !== false) {
                        $key = str_replace(".{$domainname}.", '', $key);
                        $key = str_replace(".{$domainname}", '', $key);
                    }
                }
            }
?>
<?php echo $key; ?> IN NS <?php echo $value; ?>.
<?php
            break;
        case "mx":
            $key = '@';
            $priority = $o->priority;
            $value = $o->param;

            $value = trim($value, '.');
?>
<?php echo $key; ?> IN MX <?php echo $priority; ?> <?php echo $value; ?>.
<?php
            break;
        case "aaaa":
            $key = $o->hostname;
            $value = $o->param;

            if ($key === "__base__") {
                $key = '@';
            }
?>
<?php echo $key; ?> IN AAAA <?php echo $value; ?>

<?php
            break;
        case "a":
            $key = $o->hostname;
            $value = $o->param;

            if ($key === "__base__") {
                $key = '@';
            }
?>
<?php echo $key; ?> IN A <?php echo $value; ?>

<?php
            break;
        case "cn":
        case "cname":
            $key = $o->hostname;
            $value = $o->param;

            $value = trim($value, '.');

            if ($key === "__base__") {
                $key = '@';
            }

            if (isset($arecord[$value])) {
                $rvalue = $arecord[$value];
?>
<?php echo $key; ?> IN A <?php echo $rvalue; ?>

<?php
            } else {
                if ($value !== "__base__") {
                    $value = $value;
                } else {
                    $value = $domainname;
                }
?>
<?php echo $key; ?> IN CNAME <?php echo $value; ?>.
<?php
            }

            break;

        case "fcname":
            $key = $o->hostname;
            $value = $o->param;

            $value = trim($value, '.');

            if ($key === "__base__") {
                $key = '@';
            }

            if ($value !== "__base__") {
                if (strpos($value, ".") !== false) {
                    // no action
                } else {
                    $value = "$value";
                }
            } else {
                $value = $domainname;
            }
?>
<?php echo $key; ?> IN CNAME <?php echo $value; ?>.
<?php
            break;

        case "txt":
            $key = $o->hostname;
            $value = str_replace("  ", " ", $o->param);

            if($value === null) {continue; }

            if ($key === "__base__") {
                $key = '@';
            }

            $value = str_replace("<%domain%>", $domainname, $value);
            $value = str_replace("__base__", $domainname, $value);

            if (strpos($value, "v=DKIM1") !== false) {
                // format for long dkim - quick and dirty
                $x = explode(' p=', $value);

                $v = str_split($x[1], 64);
                $str  = '("' . $x[0] . ' "';
                $str .= "\n    \"p=";
                $itmp = 0;

                foreach ($v as $t) {
                    if (!$itmp) {
                        $str .= $t . '"';
                    } else {
                        $str .= "\n    \"" . $t . '"';
                    }

                    $itmp++;
                }

                $str .= ')';

                $value = $str;

?>
<?php echo $key; ?> IN TXT <?php echo $value; ?>

<?php
            } else {
?>
<?php echo $key; ?> IN TXT "<?php echo $value; ?>"
<?php
/*
                if (strpos($value, "v=spf1") !== false) {
?>
<?php echo $key; ?> IN SPF "<?php echo $value; ?>"
<?php
                }
*/
            }

            break;
        case "srv":
            $key = $o->hostname;
            $param = $o->param;
            $priority = $o->priority;
            $port = $o->port;

            if($o->param === null) { continue; }

            if ($key === "__base__") {
                $key = '@';
            }

            $weight = ($o->weight == null || strlen($o->weight) == 0) ? 0 : $o->weight;
?>
<?php echo $key; ?> IN SRV <?php echo $priority; ?> <?php echo $weight; ?> <?php echo $port; ?> <?php echo $param; ?>.

<?php
            break;
        case "caa":
            $key = $o->hostname;
            $value = $o->param; // example: letsencrypt.org
            $flag = $o->flag; // 0 or 1 or 128
            $tag = $o->tag; // issue or issuewild or iodef
            $value = $o->param;

            if($o->param === null) { continue; }

            if ($key === "__base__") {
                $key = '@';
            }
?>
<?php echo $key; ?> IN CAA <?php echo $flag; ?> <?php echo $tag; ?> "<?php echo $value; ?>"
<?php
            break;
    }
}
?>

;;; end - dns of '<?php echo $domainname; ?>' - do not remove/modify this line

