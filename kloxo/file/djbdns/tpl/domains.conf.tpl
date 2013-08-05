### begin content - please not remove this line
<?php
    if ($soanameserver !== '') {
        $nameserver = $soanameserver;
    } else {
        foreach ($dns_records as $dns) {
            if ($dns->ttype === "ns") {
                $nameserver = $dns->param;
            }
        }
    }
?>
Z<?php echo $domainname; ?>:<?php echo $nameserver; ?>:<?php echo $email; ?>:<?php echo $serial; ?>:::::<?php echo $ttl; ?>

<?php
foreach ($dns_records as $k => $o) {
    switch ($o->ttype) {
        case "ns":
            $nameserver = $o->param;
?>
&<?php echo $domainname; ?>::<?php echo $nameserver; ?>:<?php echo $ttl; ?>

<?php
            break;
        case "a":
            $hostname = $o->hostname;
            $param = $o->param;

            if ($hostname === '__base__') {
                $key = $domainname;
            } else {
                $key = $hostname. '.' . $domainname;
            }
?>
+<?php echo $key; ?>:<?php echo $param; ?>:<?php echo $ttl; ?>

<?php
            break;
        case "aaa":
            $hostname = $o->hostname;
            $param = $o->param;

            if ($hostname === '__base__') {
                $key = $domainname;
            } else {
                $key = $hostname. '.' . $domainname;
            }
?>
+<?php echo $key; ?>:<?php echo $param; ?>:<?php echo $ttl; ?>

<?php
            break;
        case "cn":
        case "cname":
            $hostname = $o->hostname;
            $param = $o->param;

            if ($param === '__base__') {
                $key = $domainname;
            } else {
                $key = $param. '.' . $domainname;
            }
?>
C<?php echo $hostname; ?>.<?php echo $domainname; ?>:<?php echo $key; ?>:<?php echo $ttl; ?>

<?php
            break;
        case "fcname":
            $hostname = $o->hostname;
            $param = $o->param;

            if ($param === '__base__') {
                $key = $domainname;
            } else {
                $key = $param. '.' . $domainname;
            }
?>
C<?php echo $hostname; ?>.<?php echo $domainname; ?>:<?php echo $key; ?>:<?php echo $ttl; ?>

<?php
            break;
        case "mx":
            $priority = $o->priority;
            $param = $o->param;
?>
@<?php echo $domainname; ?>::<?php echo $param; ?>:<?php echo $priority; ?>:<?php echo $ttl; ?>

<?php
            break;
        case "txt":
            $hostname = $o->hostname;
            $param = $o->param;

            if ($param === null) { continue; }

            $param = str_replace("<%domain>", $domainname, $param);

            if ($hostname === '__base__') {
                $key = $domainname;
            } else {
                $key = $hostname. '.' . $domainname;
            }

            $param = str_replace(":", "\\072", $param);
?>
'<?php echo $key; ?>:<?php echo $param; ?>:<?php echo $ttl; ?>

<?php
            break;
    }
}
?>
### end content - please not remove this line

