<?php
    exec("ip -6 addr show", $out);

    if ($out[0]) {
        $IPv6Enable = true;
    } else {
        $IPv6Enable = false;
    }

    $ports = array('7778', '7777');

    $count = 0;

    foreach ($ports as &$p) {
?>
server {
    listen 0.0.0.0:<?php echo $p; ?>;
<?php
        if ($IPv6Enable) {
?>
    listen [::]:<?php echo $p; ?>;
<?php
        }

        if ($count > 0) {
?>

    ssl on;
    ssl_certificate /usr/local/lxlabs/kloxo/etc/program.crt;
    ssl_certificate_key /usr/local/lxlabs/kloxo/etc/program.key;
    ssl_protocols SSLv3 TLSv1 TLSv1.1 TLSv1.2;
    ssl_ciphers HIGH:!aNULL:!MD5;
<?php
        ?
?>

    server_name _;

    set $rootdir '/usr/local/lxlabs/kloxo/httpdocs';

    root $rootdir;

    index index.php index.html index.htm index.pl;

    access_log /usr/local/lxlabs/kloxo/log/nginx-access;
    error_log  /usr/local/lxlabs/kloxo/log/nginx-error;

    fastcgi_connect_timeout 2h;
    fastcgi_send_timeout 2h;
    fastcgi_read_timeout 2h;
    fastcgi_buffer_size 256k;
    fastcgi_buffers 8 256k;

    location / {
        try_files $uri $uri/ /index.php;
    }

    location ~ \.php$ {
        include fastcgi_params;

    #    fastcgi_pass   127.0.0.1:9999;
        fastcgi_pass   unix:/usr/local/lxlabs/kloxo/sock/kloxo.sock;
        fastcgi_index  index.php;

        fastcgi_param  REDIRECT_STATUS 200;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;

        fastcgi_param  PATH_INFO          $fastcgi_path_info;
        fastcgi_param  SCRIPT_FILENAME    $document_root$fastcgi_script_name;
        fastcgi_param  PATH_TRANSLATED    $document_root$fastcgi_path_info;
 
        fastcgi_param  QUERY_STRING       $query_string;
        fastcgi_param  REQUEST_METHOD     $request_method;
        fastcgi_param  CONTENT_TYPE       $content_type;
        fastcgi_param  CONTENT_LENGTH     $content_length;
 
        fastcgi_param  SCRIPT_NAME        $fastcgi_script_name;
        fastcgi_param  REQUEST_URI        $request_uri;
        fastcgi_param  DOCUMENT_URI       $document_uri;
        fastcgi_param  DOCUMENT_ROOT      $rootdir;
        fastcgi_param  SERVER_PROTOCOL    $server_protocol;
 
        fastcgi_param  GATEWAY_INTERFACE  CGI/1.1;
        fastcgi_param  SERVER_SOFTWARE    nginx;
 
        fastcgi_param  REMOTE_ADDR        $remote_addr;
        fastcgi_param  REMOTE_PORT        $remote_port;
        fastcgi_param  SERVER_ADDR        $server_addr;
        fastcgi_param  SERVER_PORT        $server_port;
        fastcgi_param  SERVER_NAME        $server_name;
    }
}

<?php
        $count++;
    }
?>
