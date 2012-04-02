### begin content - please not remove this line
<?php

$ipports = '';

if ($reverseproxy) {
    $port = '30080';
    $portssl = '30443';
} else {
    $port = '80';
    $portssl = '443';
}
/*
foreach ($iplist as &$ip) {
    $ipports .= "    {$ip}:{$port} {$ip}:{$portssl}\\\n";
}

$ipports .= "    127.0.0.1:{$port}";
*/

$ipports = "    *:{$port} *:{$portssl}";

if ($setdefaults === 'webmail') {
    if ($webmailappdefault) {
        $docroot = "/home/kloxo/httpd/webmail/{$webmailappdefault}";
    } else {
        $docroot = "/home/kloxo/httpd/webmail";
    }
} else {
    $docroot = "/home/kloxo/httpd/{$setdefaults}";
}

// MR -- don't use $_SERVER[] or apache_get_version because not work
exec("rpm -q httpd", $out, $ret);

$webver = str_replace('httpd-', '', $out[0]);

$isVer24 = (version_compare($webver, '2.4.0', '>=')) ? true : false;

if ($indexorder) {
    $indexorder = implode(' ', $indexorder);
}

?>

<?php
if ($setdefaults === 'ssl') {
?>

<IfModule mod_ssl.c>
<?php
    foreach ($certlist as &$cert) {
?> 
    <Virtualhost \
        <?php echo $cert['ip']; ?>:<?php echo $portssl; ?>\
            >

        SSLEngine On
        SSLCertificateFile /home/kloxo/httpd/ssl/<?php echo $cert['cert']; ?>.crt
        SSLCertificateKeyFile /home/kloxo/httpd/ssl/<?php echo $cert['cert']; ?>.key
        SSLCACertificatefile /home/kloxo/httpd/ssl/<?php echo $cert['cert']; ?>.ca

    </Virtualhost>
<?php
    }
?>

</IfModule>

DirectoryIndex <?php echo $indexorder; ?> 

<?php
} else {
    if ($setdefaults === 'init') {
/*
        foreach ($iplist as &$ip) {
?> 
Listen <?php echo $ip ?>:<?php echo $port ?> 
Listen <?php echo $ip ?>:<?php echo $portssl ?> 
<?php
        }
?> 
Listen 127.0.0.1:<?php echo $port ?> 
<?php
        if (!$isVer24) {
            foreach ($iplist as &$ip) {
?> 
NameVirtualHost <?php echo $ip ?>:<?php echo $port ?> 
NameVirtualHost <?php echo $ip ?>:<?php echo $portssl ?> 
<?php
            }
?> 
NameVirtualHost 127.0.0.1:<?php echo $port ?> 

<?php
        }
*/
?>
Listen *:<?php echo $port ?> 
Listen *:<?php echo $portssl ?> 

NameVirtualHost *:<?php echo $port ?> 
NameVirtualHost *:<?php echo $portssl ?> 
<?php
    } else {
?> 
<VirtualHost \
<?php echo $ipports; ?>\
        >

    ServerName <?php echo $setdefaults; ?> 
    ServerAlias <?php echo $setdefaults; ?>.*

    DocumentRoot "<?php echo $docroot; ?>/"

    DirectoryIndex <?php echo $indexorder; ?>

<?php
        if ($setdefaults === 'default') {
?> 
    <Ifmodule mod_userdir.c>
        UserDir enabled
        UserDir "public_html"
<?php
            foreach ($userlist as &$user) {
?>
        <Location /~<?php echo $user; ?>>
            <IfModule mod_suphp.c>
                SuPhp_UserGroup <?php echo $user; ?> <?php echo $user; ?>

            </IfModule>
        </Location>
<?php
            }
?>
    </Ifmodule>
<?php
        }
?> 
    <IfModule mod_suphp.c>
        SuPhp_UserGroup lxlabs lxlabs
    </IfModule>

    <IfModule mod_fastcgi.c>
        Alias /<?php echo $setdefaults; ?>.fake <?php echo $docroot; ?>/<?php echo $setdefaults; ?>.fake
        FastCGIExternalServer <?php echo $docroot; ?>/<?php echo $setdefaults; ?>.fake -host 127.0.0.1:50000
        AddType application/x-httpd-fastphp .php
        Action application/x-httpd-fastphp /<?php echo $setdefaults; ?>.fake
    </IfModule>

    <Directory "<?php echo $docroot; ?>/">
        AllowOverride All
        allow from all
        Options +Indexes +FollowSymlinks
    </Directory>

</VirtualHost>

<?php
    }
}
?>

### end content - please not remove this line
