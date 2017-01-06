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
php_admin_value upload_max_filesize <?=$upload_max_filesize;?>

php_admin_value max_execution_time <?=$max_execution_time_flag;?>

php_admin_value max_input_time <?=$max_input_time_flag;?>

php_admin_value memory_limit <?=$memory_limit_flag;?>

php_admin_value post_max_size <?=$post_max_size_flag;?>

php_flag display_errors <?=$display_error_flag;?>

php_flag file_uploads <?=$file_uploads_flag;?>

php_flag log_errors <?=$log_errors_flag;?>

php_flag output_buffering <?=$output_buffering_flag;?>

php_flag allow_url_fopen <?=$allow_url_fopen_flag;?>

php_flag cgi.force_redirect <?=$cgi_force_redirect_flag;?>

php_flag enable_dl <?=$enable_dl_flag;?>

php_admin_value max_input_vars <?=$max_input_vars_flag;?>

php_admin_value date.timezone <?=$date_timezone_flag;?>

<?php
}
?>

