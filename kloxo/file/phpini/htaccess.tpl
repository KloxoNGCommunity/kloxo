###Start Kloxo PHP config Area
###Please Don't edit these comments or the content in between

<Ifmodule mod_php5.c>
	php_value upload_max_filesize <?php echo $upload_max_filesize; ?>

	php_value max_execution_time <?php echo $max_execution_time; ?>

	php_value max_input_time <?php echo $max_input_time; ?>

	php_value memory_limit <?php echo $memory_limit; ?>

	php_value post_max_size <?php echo $post_max_size; ?>

	php register_globals <?php echo $register_global; ?>

	php display_errors <?php echo $display_error; ?>

	php file_uploads <?php echo $file_uploads; ?>

	php log_errors <?php echo $log_errors; ?>

	php output_buffering <?php echo $output_buffering; ?>

	php register_argc_argv <?php echo $register_argc_argv; ?>

	php magic_quotes_gpc <?php echo $magic_quotes_gpc; ?>

	php magic_quotes_runtime <?php echo $magic_quotes_runtime; ?>

	php magic_quotes_sybase <?php echo $magic_quotes_sybase; ?>

	php mysql.allow_persistent <?php echo $mysql_allow_persistent; ?>

	php register_long_arrays <?php echo $register_long_arrays; ?>

	php allow_url_fopen <?php echo $allow_url_fopen; ?>

	php cgi.force_redirect <?php echo $cgi_force_redirect; ?>

	php enable_dl <?php echo $enable_dl; ?>

</Ifmodule>

###End Kloxo PHP config Area
