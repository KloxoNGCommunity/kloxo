<?php

$conn = new mysqli('localhost', 'root', $rootpass, 'powerdns');

if ($query = $conn->query("SELECT * FROM domains WHERE name='{$domainname}' AND type='MASTER';")) {
    if ($query->num_rows !== 0) {
        while ($row = $query->fetch_object()) {
            $rowid = $row->id;

            $conn->query("DELETE FROM zones WHERE domain_id='{$rowid}'");
            $conn->query("DELETE FROM domainmetadata WHERE domain_id='{$rowid}'");
            $conn->query("DELETE FROM supermasters WHERE nameserver LIKE '{$nameserver}'");
            $conn->query("DELETE FROM records WHERE domain_id='{$rowid}'");
        }
    }
}

$nameserver = null;

foreach($dns_records as $dns) {
    if ($dns->ttype === "ns") {
        if (!$nameserver) {
            $nameserver = $dns->param;
        }
    }

    if ($dns->ttype === 'a') {
        $arecord[$dns->hostname] = $dns->param;

        if ($dns->hostname === '__base__') {
            $baseip = $dns->param;
        }
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

if (!$rowid) {
    $conn->query("INSERT INTO domains (name,type) values('{$domainname}', 'MASTER');");
    $domain_id = $conn->insert_id;
} else {
    $domain_id = $rowid;
}

$soa = "{$nameserver} {$email} {$serial} {$refresh} {$retry} {$expire} {$minimum}";

$conn->query("INSERT INTO records (domain_id, name, type, content, ttl, prio) " .
    "VALUES ('{$domain_id}', '{$domainname}', 'SOA', '{$soa}', '{$ttl}', NULL);");

foreach($dns_records as $k => $o) {
    switch ($o->ttype) {
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

            $conn->query("INSERT INTO records (domain_id, name, type, content, ttl, prio) " .
                "VALUES ('{$domain_id}', '{$key}', 'NS', '{$value}', '{$ttl}', NULL);");

            break;
        case "mx":
            $key = $domainname;
            $value = $o->param;
            $prio = $o->priority;

            $value = trim($value, '.');

            $conn->query("INSERT INTO records (domain_id, name, type, content, ttl, prio) " .
                "VALUES ('{$domain_id}', '{$key}', 'MX', '{$value}', '{$ttl}', '{$prio}');");

            break;
        case "a":
            $key = $o->hostname;
            $value = $o->param;

            if ($key !== "__base__") {
                $key = "{$key}.{$domainname}";
            } else {
                $key = $domainname;
            }

            $conn->query("INSERT INTO records (domain_id, name, type, content, ttl, prio) " .
                "VALUES ('{$domain_id}', '{$key}', 'A', '{$value}', '{$ttl}', NULL);");

            break;
        case "aaaa":
            $key = $o->hostname;
            $value = $o->param;

            if ($key !== "__base__") {
                $key = "{$key}.{$domainname}";
            } else {
                $key = $domainname;
            }

            $conn->query("INSERT INTO records (domain_id, name, type, content, ttl, prio) " .
                "VALUES ('{$domain_id}', '{$key}', 'AAAA', '{$value}', '{$ttl}', NULL);");

            break;
        case "cn":
        case "cname":
            $key = $o->hostname;
            $value = $o->param;
            $key .= ".{$domainname}";

            $value = trim($value, '.');

            if (isset($arecord[$value])) {
                $rvalue = $arecord[$value];

                $conn->query("INSERT INTO records (domain_id, name, type, content, ttl, prio) " .
                    "VALUES ('{$domain_id}', '{$key}', 'A', '{$rvalue}', '{$ttl}', NULL);");
            } else {
                if ($value !== "__base__") {
                    $value = "{$value}.{$domainname}";
                } else {
                    $value = $domainname;
                }

                $conn->query("INSERT INTO records (domain_id, name, type, content, ttl, prio) " .
                    "VALUES ('{$domain_id}', '{$key}', 'CNAME', '{$value}', '{$ttl}', NULL);");
            }

            break;
        case "fcname":
            $key = $o->hostname;
            $value = $o->param;
            $key .= ".{$domainname}";

            $value = trim($value, '.');

            if ($value !== "__base__") {
                if (strpos($value, ".") !== false) {
                    // no action
                } else {
                    $value = "{$value}.";
                }
            } else {
                $value = $domainname;
            }

            $conn->query("INSERT INTO records (domain_id, name, type, content, ttl, prio) " .
                "VALUES ('{$domain_id}', '{$key}', 'CNAME', '{$value}', '{$ttl}', NULL);");

            break;
        case "txt":
            $key = $o->hostname;
            $value = $o->param;

            if($value === null) { continue; }

            if ($key !== "__base__") {
                $key = "{$key}.{$domainname}";
            } else {
                $key = $domainname;
            }

            $value = str_replace("<%domain%>", $domainname, $value);
            $value = str_replace("__base__", $domainname, $value);
            $value = '"' . $value . '"';

            $conn->query("INSERT INTO records (domain_id, name, type, content, ttl, prio) " .
                "VALUES ('{$domain_id}', '{$key}', 'TXT', '{$value}', '{$ttl}', NULL);");

        /*
            if (strpos($value, "v=spf1") !== false) {
            $conn->query("INSERT INTO records (domain_id, name, type, content, ttl, prio) " .
                    "VALUES ('{$domain_id}', '{$key}', 'SPF', '{$value}', '{$ttl}', NULL);");
            }
        */
            break;
        case "caa":
            $key = $o->hostname;
            $value = $o->param; // example: letsencrypt.org
            $flag = $o->flag; // 0 or 1 or 128
            $tag = $o->tag; // issue or issuewild or iodef
            $value = $o->param;

            if($value === null) { continue; }

            if ($key !== "__base__") {
                $key = "{$key}.{$domainname}";
            } else {
                $key = $domainname;
            }

            $value = '"' . $value . '"';

            $conn->query("INSERT INTO records (domain_id, name, type, content, ttl, prio) " .
                "VALUES ('{$domain_id}', '{$key}', 'CAA', '{$flag} {$tag} {$value}', '{$ttl}', NULL);");

            break;
        case "srv":
            $key = $o->hostname;
            $value = $o->param;
            $prio = $o->priority;
            $port = $o->port;

            if($value === null) { continue; }

            if ($key !== "__base__") {
                $key = "{$key}.{$domainname}";
            } else {
                $key = $domainname;
            }

            $weight = ($o->weight == null || strlen($o->weight) == 0) ? 0 : $o->weight;

            $value = '"' . $value . '"';

            $conn->query("INSERT INTO records (domain_id, name, type, content, ttl, prio) " .
                "VALUES ('{$domain_id}', '{$key}', 'SRV', '{$weight} {$port} {$value}', '{$ttl}', '{$prio}');");

    }
}

$conn->query("INSERT INTO domainmetadata (domain_id, kind, content) " .
    "VALUES ({$domain_id}, 'ALLOW-AXFR-FROM', 'AUTO-NS');");

$conn->query("INSERT INTO zones (domain_id, owner) " .
    "VALUES ('{$domain_id}', 1);");

// MR -- account not implementing yet; importance for poweradmin to multiple use!
$account = 'admin';

$conn->query("INSERT INTO supermasters (ip, nameserver, account) " .
    "VALUES ('{$baseip}', '{$nameserver}', '{$account}');");

$conn->close();