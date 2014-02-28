				<div style="position: fixed; top: 2px; right: 2px">
					<div style="float: left">
						<a href='javascript:alert("<?=$status_title_1;?>");'>
							<div style="color: <?=$status_color;?>; margin:2px; padding: 3px; background-color: #3498db; border:0;" onMouseOver="this.style.backgroundColor='#fff'; this.style.color='#000';" onMouseOut="this.style.backgroundColor='#3498db'; this.style.color='<?=$status_color;?>';" title="<?=$status_title_2;?>">&nbsp;<?= $login->getKeywordUc('status'); ?>&nbsp;</div>
						</a>
					</div>
					<div style="float: left">
						<a href="#" onClick="toggleVisibilityByClass('mmm');">
							<div style="color: #fff; margin:2px; padding: 3px; background-color: #3498db; border:0;" onMouseOver="this.style.backgroundColor='#fff'; this.style.color='#000';" onMouseOut="this.style.backgroundColor='#3498db'; this.style.color='#fff';">&nbsp;<?= $login->getKeywordUc('showhide') ?>&nbsp;</div>
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