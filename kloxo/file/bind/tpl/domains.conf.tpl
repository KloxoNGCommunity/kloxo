;;; begin - dns of '<?php echo $domainname; ?>' - do not remove/modify this line

<?php
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
    $minimum = isset($minimum) && strlen($minimum) > 0 ? $minimum : 1800;
?>
$TTL <?php echo $ttl; ?>

@ IN SOA <?php echo $nameserver; ?>. <?php echo $email; ?>. (
    <?php echo $serial; ?> ; Serial
    <?php echo $refresh; ?>   ; Refresh
    <?php echo $retry; ?>    ; Retry
    <?php echo $expire; ?>  ; Expire
    <?php echo $minimum; ?> ) ; Minimum

<?php
    foreach($dns_records as $k => $o) {
        $ttl = isset($o->ttl) && strlen($o->ttl) ? $o->ttl : $ttl;

        switch($o->ttype) {
            case "ns":
                $value = $o->param;
?>
<?php echo $domainname; ?>. IN  NS <?php echo $value; ?>.
<?php
                break;
            case "mx":
                $v = $o->priority;
                $value = $o->param;
?>
<?php echo $domainname; ?>. <?php echo $ttl; ?> IN MX <?php echo $v; ?> <?php echo $value; ?>.
<?php
                break;
            case "aaaa":
                $key = $o->hostname;
                $value = $o->param;

                if ($key === '*') {
?>
* <?php echo $ttl; ?> IN AAAA <?php echo $value; ?>

<?php
                    break;
                }

                if ($key !== "__base__") {
                    $key = "$key.$domainname.";
                } else {
                    $key = "$domainname.";
                }
?>
<?php echo $key; ?> IN <?php echo $ttl; ?> AAAA <?php echo $value; ?>

<?php
                break;
            case "ddns":
                if ($o->offline === 'on')
                    break;
            case "a":
                $key = $o->hostname;
                $value = $o->param;

                if ($key === '*') {
?>
* <?php echo $ttl; ?> IN A <?php echo $value; ?>

<?php
                    break;
                }

                if ($key !== "__base__") {
                    $key = "$key.$domainname.";
                } else {
                    $key = "$domainname.";
                }
?>
<?php echo $key; ?> <?php echo $ttl; ?> IN A <?php echo $value; ?>

<?php
                break;
            case "cn":
            case "cname":
                $key = $o->hostname;
                $value = $o->param;

                if (isset($arecord[$value])) {
                    $rvalue = $arecord[$value];

                    if ($key === '*') {
?>
* <?php echo $ttl; ?> IN A <?php echo $rvalue; ?>

<?php
                        break;
                    }

                    $key .= ".$domainname.";
?>
<?php echo $key; ?> <?php echo $ttl; ?> IN A <?php echo $rvalue; ?>

<?php
                    break;
                }

                $key .= ".$domainname.";

                if ($value !== "__base__") {
                    $value = "$value.$domainname.";
                } else {
                    $value = "$domainname.";
                }

            /*
                if ($key === '*') {
?>
* IN CNAME <?php echo $value; ?>

<?php
                    break;
                }
            */
?>
<?php echo $key; ?> <?php echo $ttl; ?> IN CNAME <?php echo $value; ?>

<?php
                break;

            case "fcname":
                $key = $o->hostname;
                $value = $o->param;
                $key .= ".$domainname.";

                if ($value !== "__base__") {
                    if (!cse($value, ".")) {
                        $value = "$value.";
                    }
                } else {
                    $value = "$domainname.";
                }
?>
<?php echo $key; ?> <?php echo $ttl; ?> IN CNAME <?php echo $value; ?>

<?php
                break;

            case "txt":
                $key = $o->hostname;
                $value = $o->param;

                if($value === null) {continue; }    

                if ($key !== "__base__") {
                    $key = "$key.$domainname.";
                } else {
                    $key = "$domainname.";
                }

                $value = str_replace("<%domain>", $domainname, $value);
?>
<?php echo $key; ?> <?php echo $ttl; ?> IN TXT "<?php echo $value; ?>"
<?php
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
                $key = "$key.$domainname";
            } else {
                $key = "$domainname";
            }

            $weight = ($o->weight == null || strlen($o->weight) == 0) ? 0 : $o->weight;
?>
_<?php echo $service; ?>._<?php echo $proto; ?>.<?php echo $key; ?>. <?php echo $ttl; ?> IN SRV <?php echo $priority; ?> <?php echo $weight; ?> <?php echo $port; ?> <?php echo $param; ?>.

<?php
            break;
        }
    }
?>

;;; end - dns of '<?php echo $domainname; ?>' - do not remove/modify this line

