<?php
	$status_title = $this->print_message('simplicity');

	if ($status_title !== '') {
			$status_title = str_replace(":  ", ":\n<ul style=\"margin-left:-20px\"><li>", $status_title);
	}

	// MR -- must be double if
	if ($status_title !== '') {
		$status_title .= "</li></ul>";

		$status_color = "#fff";
	} else {
		$status_color = "#3498db";
	}

	if ($status_title != '') {
?>
				<div id="div_cfstatus" style="width: 240px;padding: 10px;color: #FFFFFF;background-color: #9834db;position: absolute;z-index: 1000;top: 35px;right: 5px;border: 1px solid #888">
				<?=$status_title;?>
				</div>
<?php
	}
?>

				<div style="position: fixed; top: 2px; right: 2px">
					<div style="float: left">
						<!-- <a href='javascript:alert("<?=$status_title_1;?>");'> -->
							<div id="div_status" style="color: <?=$status_color;?>; margin:2px; padding: 3px; background-color: #3498db; border:0;" onMouseOver="this.style.backgroundColor='#fff'; this.style.color='#000'; displayStatus('div_cfstatus', 'inline');" onMouseOut="this.style.backgroundColor='#3498db'; this.style.color='<?=$status_color;?>'; displayStatus('div_cfstatus', 'none');">&nbsp;<?= $login->getKeywordUc('status'); ?>&nbsp;</div>
						<!-- </a> -->
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
			function cf_fade() {
				var elemid = document.getElementById("div_cfstatus");
				var op = 1;

				var timer = setInterval(function () {
					if (op <= 0.1) {
						clearInterval(timer);

						if (elemid.style.filter) {
							elemid.style.filter = 'alpha(opacity=100)';
						} else {
							// MR -- not work
							elemid.style.opacity = 1;
						}

						elemid.style.display = 'none';
					}

					if (elemid.style.filter) {
						elemid.style.filter = 'alpha(opacity=' + op * 100 + ')';
					} else {
						elemid.style.opacity = op;
					}

					op -= op * 0.1;
				}, 150);
			}

			function displayStatus(id, state) {
				var elemid = document.getElementById(id);

				if (elemid.style.filter) {
					elemid.style.filter = 'alpha(opacity=100)';
				} else {
					// MR -- it's work
					elemid.style.opacity = 1;
				}

				elemid.style.display = state;
			}

			cf = setTimeout(function(){cf_fade()},15000);

			startTime('clock_div');
		</script>
						</div>
					</div>
				</div>