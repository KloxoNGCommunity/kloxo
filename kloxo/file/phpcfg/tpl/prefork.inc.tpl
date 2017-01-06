<?php
    // can use $user and $domain vars
	
	if (!isset($upload_max_filesize)) {
?>
php_admin_value upload_max_filesize 16M
php_admin_value max_execution_time 120
php_admin_value max_input_time 120
php_admin_value memory_limit 128M
php_admin_value post_max_size 32M
php_flag display_errors on
php_flag file_uploads on
php_flag log_errors on
php_flag output_buffering off
php_flag mysql.allow_persistent off
php_flag allow_url_fopen on
php_flag cgi.force_redirect on
php_flag enable_dl on
php_admin_value max_input_vars 3000
php_admin_value date.timezone Europe/London
<?php
	} else {
?>
php_admin_value upload_max_filesize <?php echo $upload_max_filesize; ?>

php_admin_value max_execution_time <?php echo $max_execution_time_flag; ?>

php_admin_value max_input_time <?php echo $max_input_time_flag; ?>

php_admin_value memory_limit <?php echo $memory_limit_flag; ?>

php_admin_value post_max_size <?php echo $post_max_size_flag; ?>

php_flag display_errors <?php echo $display_error_flag; ?>

php_flag file_uploads <?php echo $file_uploads_flag; ?>

php_flag log_errors <?php echo $log_errors_flag; ?>

php_flag output_buffering <?php echo $output_buffering_flag; ?>

php_flag allow_url_fopen <?php echo $allow_url_fopen_flag; ?>

php_flag cgi.force_redirect <?php echo $cgi_force_redirect_flag; ?>

php_flag enable_dl <?php echo $enable_dl_flag; ?>

php_admin_value max_input_vars <?php echo $max_input_vars_flag; ?>

php_admin_value date.timezone <?php echo $date_timezone_flag; ?>

<?php
	}
?>

