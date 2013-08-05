;;; begin content - please not remove this line

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
$TTL <?php echo $ttl; ?>

@ IN SOA <?php echo $nameserver; ?>. <?php echo $email; ?>. (
    <?php echo $serial; ?>; Serial
    10800; Refresh
    3600; Retry
    604800; Expire
    86400); Minimum
<?php
foreach ($dns_records as $k => $o) {
    switch ($o->ttype) {
        case "ns":
            $nameserver = $o->param;
?>
<?php echo $domainname; ?>. IN NS <?php echo $nameserver; ?>. 
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
<?php echo $key; ?>. IN A <?php echo $param; ?>

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
<?php echo $key; ?>. IN AAA <?php echo $param; ?>

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
<?php echo $hostname; ?>.<?php echo $domainname; ?>. IN CNAME <?php echo $key; ?>. 
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
<?php echo $hostname; ?>.<?php echo $domainname; ?>. IN CNAME <?php echo $key; ?>. 
<?php
            break;
        case "mx":
            $priority = $o->priority;
            $param = $o->param;
?>
<?php echo $domainname; ?>. IN MX <?php echo $priority; ?> <?php echo $param; ?>.
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
?>
<?php echo $key; ?>. IN TXT "<?php echo $param; ?>"
<?php
            break;
    }
}
?>

;;; end content - please not remove this line

