<?php
			$status_title = $this->print_message('simplicity');
			$status_title_1 = str_replace(":  ", ':\n\n- ', $status_title);
			$status_title_2 = str_replace(":  ", ":\n- ", $status_title);

			if (strlen($status_title) > 0) {
				$status_color = "#fff";
			} else {
				$status_color = "#3498db";
			}

		if ($status_title_1 != '') {
			$status_title_1 = str_replace(":  ", ':<br /><br />- ', $status_title);
		?>
				<div id="div_cfstatus" style="padding:10px;color:#FFFFFF;background-color: #3498db;position:absolute;z-index:1000;top:5px;right:5px;border:#000000 1px solid">
				<?=$status_title_1;?>
				</div>

		<script>
			function cf_fade() {
				var elemid = document.getElementById("div_cfstatus");var op = 1;
				var timer = setInterval(function () {
					if (op <= 0.1) {
						clearInterval(timer);elemid.style.display = 'none';
					}
					elemid.style.opacity = op;
					elemid.style.filter = 'alpha(opacity=' + op * 100 + ")";
					op -= op * 0.1;
				}, 150);
			}
			cf = setTimeout(function(){cf_fade()},15000);
		</script>

<?php
}
?>

				<div style="position: fixed; top: 2px; right: 2px">
					<div style="float: left">
						<a href='javascript:alert("<?=$status_title_1;?>");'>
							<div id="div_status" style="color: <?=$status_color;?>; margin:2px; padding: 3px; background-color: #3498db; border:0;" onMouseOver="this.style.backgroundColor='#fff'; this.style.color='#000';" onMouseOut="this.style.backgroundColor='#3498db'; this.style.color='<?=$status_color;?>';" title="<?=$status_title_2;?>">&nbsp;<?= $login->getKeywordUc('status'); ?>&nbsp;</div>
						</a>
					</div>
					<div style="float: left">
						<a href="#" onClick="toggleVisibilityByClass('mmm');">
							<div id="div_showhide" style="color: #fff; margin:2px; padding: 3px; background-color: #3498db; border:0;" onMouseOver="this.style.backgroundColor='#fff'; this.style.color='#000';" onMouseOut="this.style.backgroundColor='#3498db'; this.style.color='#fff';">&nbsp;<?= $login->getKeywordUc('showhide') ?>&nbsp;</div>
						</a>
					</div>
					<div style="float: left">
						<div id="clock_div" style="color: #fff; margin:2px; padding: 3px; background-color: #3498db; border:0;">
<?php
		$clock_path = str_replace(getcwd(), "", getLinkCustomfile("{$skin_dir}/js", "clock.js"));

		$this->print_jscript_source($clock_path);

?>
	<script>
		startTime('clock_div');
	</script>
						</div>
					</div>
				</div>