### begin - web of initial - do not remove/modify this line

<?php

if ($reverseproxy) {
    $ports[] = '30080';
    $ports[] = '30443';
} else {
    if (($webcache === 'none') || (!$webcache)) {
        $ports[] = '80';
        $ports[] = '443';
    } else {
        $ports[] = '8080';
        $ports[] = '8443';
    }
}

if ($reverseproxy) {
    $tmp_ip = '127.0.0.1';

    foreach ($certnamelist as $ip => $certname) {
        $tmp_certname = $certname;
        break;
    }

    $certnamelist = null;

    $certnamelist[$tmp_ip] = $tmp_certname;
}

$defaultdocroot = "/home/kloxo/httpd/default";
$cpdocroot = "/home/kloxo/httpd/cp";

if ($indexorder) {
    $indexorder = implode(' ', $indexorder);
}

// MR -- for future purpose, apache user have uid 50000
// $userinfoapache = posix_getpwnam('apache');
// $fpmportapache = (50000 + $userinfoapache['uid']);
$fpmportapache = 50000;

foreach ($certnamelist as $ip => $certname) {
?>

Listen <?php echo $ip; ?>:<?php echo $ports[0]; ?>

Listen <?php echo $ip; ?>:<?php echo $ports[1]; ?>


<IfVersion < 2.4>
    NameVirtualHost <?php echo $ip; ?>:<?php echo $ports[0]; ?>

    NameVirtualHost <?php echo $ip; ?>:<?php echo $ports[1]; ?>

</IfVersion>
<?php
}
?>

<Ifmodule mod_userdir.c>
    UserDir enabled
    UserDir /home/*/public_html
<?php
foreach ($userlist as &$user) {
    $userinfo = posix_getpwnam($user);

    if (!$userinfo) {
        continue;
    }
?>
    <Location "/~<?php echo $user; ?>">
        <IfModule mod_suphp.c>
            SuPhp_UserGroup <?php echo $user; ?> <?php echo $user; ?>

        </IfModule>
    </Location>
<?php
}
?>
</Ifmodule>
<?php
foreach ($certnamelist as $ip => $certname) {
    $count = 0;

    foreach ($ports as &$port) {
?>


### 'default' config
<VirtualHost <?php echo $ip; ?>:<?php echo $port; ?>>

    ServerName default

    ServerAlias default.*

    DocumentRoot "<?php echo $defaultdocroot; ?>"

    DirectoryIndex <?php echo $indexorder; ?>

<?php
        if ($count !== 0) {
?>

    <IfModule mod_ssl.c>
        SSLEngine On
        SSLCertificateFile /home/kloxo/httpd/ssl/<?php echo $certname; ?>.crt
        SSLCertificateKeyFile /home/kloxo/httpd/ssl/<?php echo $certname; ?>.key
        SSLCACertificatefile /home/kloxo/httpd/ssl/<?php echo $certname; ?>.ca
    </IfModule>
<?php
        }
?>

<?php
        // if (strpos($phptype, '_suphp') !== false) {
?>

    <IfModule suexec.c>
        SuexecUserGroup apache apache
    </IfModule>

    <IfModule mod_suphp.c>
        SuPhp_UserGroup apache apache
    </IfModule>
<?php
        // } elseif (strpos($phptype, '_ruid2') !== false) {
        if (strpos($phptype, '_ruid2') !== false) {
?>

    <IfModule mod_ruid2.c>
        RMode config
        RUidGid apache apache
        RMinUidGid apache apache
    </IfModule>
<?php
        } elseif (strpos($phptype, '_itk') !== false) {
?>

    <IfModule itk.c>
        AssignUserId apache apache
    </IfModule>
<?php
        } elseif (strpos($phptype, 'php-fpm_') !== false) {
?>

    <IfModule mod_fastcgi.c>
        Alias /default.<?php echo $count; ?>fake "<?php echo $defaultdocroot; ?>/default.<?php echo $count; ?>fake"
        #FastCGIExternalServer "<?php echo $defaultdocroot; ?>/default.<?php echo $count; ?>fake" -host 127.0.0.1:<?php echo $fpmportapache; ?> -idle-timeout 180 -pass-header Authorization
        FastCGIExternalServer "<?php echo $defaultdocroot; ?>/default.<?php echo $count; ?>fake" -socket /home/php-fpm/sock/apache.sock -idle-timeout 180 -pass-header Authorization
        AddType application/x-httpd-fastphp .php
        Action application/x-httpd-fastphp /default.<?php echo $count; ?>fake
        <Files "default.<?php echo $count; ?>fake">
            RewriteCond %{REQUEST_URI} !default.<?php echo $count; ?>fake
        </Files>
    </IfModule>
<?php
        } elseif (strpos($phptype, 'fcgid_') !== false) {
?>

    <IfModule mod_fcgid.c>
        <Directory "<?php echo $defaultdocroot; ?>/">
            Options +ExecCGI
            AllowOverride All
            AddHandler fcgid-script .php
            FCGIWrapper /home/kloxo/client/php5.fcgi .php
            <IfVersion < 2.4>
                Order allow,deny
                Allow from all
            </IfVersion>
            <IfVersion >= 2.4>
                Require all granted
            </IfVersion>
        </Directory>
    </IfModule>
<?php
        } elseif (strpos($phptype, 'proxy-fcgi_') !== false) {
?>

    <IfModule mod_proxy_fcgi.c>
        ProxyPass / fcgi://127.0.0.1:<?php echo $fpmportapache; ?>/
        ProxyPassReverse / fcgi://127.0.0.1:<?php echo $fpmportapache; ?>/
    </IfModule>
<?php
        }
?>

    <Location />
        Allow from all
        # Options +Indexes +FollowSymlinks
        Options +Indexes -FollowSymlinks +SymLinksIfOwnerMatch
    </Location>

</VirtualHost>


### 'cp' config
<VirtualHost <?php echo $ip; ?>:<?php echo $port; ?>>

    ServerName cp

    ServerAlias cp.*

    DocumentRoot "<?php echo $cpdocroot; ?>"

    DirectoryIndex <?php echo $indexorder; ?>

<?php
        if ($count !== 0) {
?>

    <IfModule mod_ssl.c>
        SSLEngine On
        SSLCertificateFile /home/kloxo/httpd/ssl/<?php echo $certname; ?>.crt
        SSLCertificateKeyFile /home/kloxo/httpd/ssl/<?php echo $certname; ?>.key
        SSLCACertificatefile /home/kloxo/httpd/ssl/<?php echo $certname; ?>.pem
    </IfModule>
<?php
        }

        // if (strpos($phptype, '_suphp') !== false) {
?>

    <IfModule suexec.c>
        SuexecUserGroup apache apache
    </IfModule>

    <IfModule mod_suphp.c>
        SuPhp_UserGroup apache apache
    </IfModule>
<?php
        // } elseif (strpos($phptype, '_ruid2') !== false) {
        if (strpos($phptype, '_ruid2') !== false) {
?>

    <IfModule mod_ruid2.c>
        RMode config
        RUidGid apache apache
        RMinUidGid apache apache
    </IfModule>
<?php
        } elseif (strpos($phptype, '_itk') !== false) {
?>

    <IfModule itk.c>
        AssignUserId apache apache
    </IfModule>
<?php
        } elseif (strpos($phptype, 'php-fpm_') !== false) {
?>

    <IfModule mod_fastcgi.c>
        Alias /cp.<?php echo $count; ?>fake "<?php echo $cpdocroot; ?>/cp.<?php echo $count; ?>fake"
        #FastCGIExternalServer "<?php echo $cpdocroot; ?>/cp.<?php echo $count; ?>fake" -host 127.0.0.1:<?php echo $fpmportapache; ?> -idle-timeout 180 -pass-header Authorization
        FastCGIExternalServer "<?php echo $cpdocroot; ?>/cp.<?php echo $count; ?>fake" -socket /home/php-fpm/sock/apache.sock -idle-timeout 180 -pass-header Authorization
        AddType application/x-httpd-fastphp .php
        Action application/x-httpd-fastphp /cp.<?php echo $count; ?>fake
        <Files "cp.<?php echo $count; ?>fake">
            RewriteCond %{REQUEST_URI} !cp.<?php echo $count; ?>fake
        </Files>
    </IfModule>
<?php
       } elseif (strpos($phptype, 'fcgid_') !== false) {
?>

    <IfModule mod_fcgid.c>
        <Directory "<?php echo $defaultdocroot; ?>/">
            Options +ExecCGI
            AllowOverride All
            AddHandler fcgid-script .php
            FCGIWrapper /home/kloxo/client/php5.fcgi .php
            <IfVersion < 2.4>
                Order allow,deny
                Allow from all
            </IfVersion>
            <IfVersion >= 2.4>
                Require all granted
            </IfVersion>
        </Directory>
    </IfModule>
<?php
        } elseif (strpos($phptype, 'proxy-fcgi_') !== false) {
?>

    <IfModule mod_proxy_fcgi.c>
        ProxyPass / fcgi://127.0.0.1:<?php echo $fpmportapache; ?>/
        ProxyPassReverse / fcgi://127.0.0.1:<?php echo $fpmportapache; ?>/
    </IfModule>
<?php
        }
?>

    <Location />
        Allow from all
        # Options +Indexes +FollowSymlinks
        Options +Indexes -FollowSymlinks +SymLinksIfOwnerMatch
    </Location>

</VirtualHost>

<?php
        $count++;
    }
}
?>

### end - web of initial - do not remove/modify this line
