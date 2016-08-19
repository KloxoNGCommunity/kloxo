[global]
logfile=/var/log/httpd/suphp_log
loglevel=info
webserver_user=apache
docroot=/
;; MR - chroot still not work!
;chroot=/home/*/
env_path=/bin:/usr/bin
umask=0022
min_uid=48
min_gid=48

;; Security options
allow_file_group_writeable=true
allow_file_others_writeable=false
allow_directory_group_writeable=true
allow_directory_others_writeable=false

;; Check wheter script is within DOCUMENT_ROOT
;; MR -- trouble if true for usedir (~user)
check_vhost_docroot=false

;Send minor error messages to browser
errors_to_browser=false

[handlers]
;; Handler for php-scripts
x-httpd-php="php:/usr/bin/php-cgi"
<?php
		foreach($phpmlist as $k => $v) {
?>
x-httpd-php52="php:/usr/bin/<?=$v;?>-cgi"
<?php
		}
?>

;; Handler for CGI-scripts
x-suphp-cgi="execute:!self"
