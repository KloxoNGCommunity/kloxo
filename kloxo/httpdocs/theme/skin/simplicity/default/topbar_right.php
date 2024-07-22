<?php
	$status_title = $this->print_message('simplicity');

	if ($status_title !== '') {
			$status_title = str_replace(":  ", ":\n<ul style=\"margin-left:-20px\"><li>", $status_title);
	}

	// MR -- must be double if
	if ($status_title !== '') {
		$status_title .= "</li></ul>";

		$divclass = "div_header_message";
	} else {
		$divclass = "div_header_message_inactive";
	}

	if ($status_title != '') {
?>
				<div id="div_cfstatus" class="div_header_message_status">
				<?=$status_title;?>
				</div>
<?php
	}
?>

				<div class="div_fixed_right">
					<div style="float: left">
						<div id="div_status" class="<?=$divclass;?>" onMouseOver="this.style.backgroundColor='#fff'; this.style.color='#000'; displayStatus('div_cfstatus', 'inline');" onMouseOut="this.style.backgroundColor='#707070'; this.style.color='#fff'; displayStatus('div_cfstatus', 'none');">&nbsp;<?= $login->getKeywordUc('status'); ?>&nbsp;</div>
					</div>
					<div style="float: left">
						<a href="#" onClick="toggleVisibilityById('mmm');">
							<div id="div_showhide" class="div_header_message" onMouseOver="this.style.backgroundColor='#fff'; this.style.color='#000';" onMouseOut="this.style.backgroundColor='#707070'; this.style.color='#fff';">&nbsp;<?= $login->getKeywordUc('showhide') ?>&nbsp;</div>
						</a>
					</div>
					<div style="float: left">
						<div id="clock_div" class="div_header_message">
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