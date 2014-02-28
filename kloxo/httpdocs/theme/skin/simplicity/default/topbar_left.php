				<div style="position: fixed; top: 2px; left: 2px">
					<div style="float: left">
						<a href="<?="/display.php?frm_action=list&frm_o_cname=smessage";?>">
							<div style="color: #fff; margin:2px; padding: 3px; background-color: #3498db; border:0;" onMouseOver="this.style.backgroundColor='#fff'; this.style.color='#000';" onMouseOut="this.style.backgroundColor='#3498db'; this.style.color='#fff';" title="<?= $login->getKeywordUc('message_title'); ?>">&nbsp;<?=$message_text;?>&nbsp;</div>
						</a>
					</div>
					<div style="float: left">
						<a href="<?="/display.php?frm_action=list&frm_o_cname=ticket";?>">
							<div style="color: #fff; margin:2px; padding: 3px; background-color: #3498db; border:0;" onMouseOver="this.style.backgroundColor='#fff'; this.style.color='#000';" onMouseOut="this.style.backgroundColor='#3498db'; this.style.color='#fff';" title="<?= $login->getKeywordUc('ticket_title'); ?>">&nbsp;<?=$ticket_text;?>&nbsp;</div>
						</a>
					</div>
				</div>