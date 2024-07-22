<?php
			$mess_url = "/display.php?frm_action=list&frm_o_cname=smessage";

			$loginas = $login->nname;

			$total_message_received = db_get_count("smessage", "text_sent_to_cmlist LIKE '%client-{$loginas}%'");
			$total_message_readby = db_get_count("smessage", "text_readby_cmlist LIKE '%client-{$loginas}%'");
			$total_message_unreadby = $total_message_received - $total_message_readby;

//			$total_message_madeby = db_get_count("smessage", "made_by LIKE '%client-{$loginas}%'");

			$message_text = $login->getKeywordUc('message') . ": {$total_message_unreadby}/{$total_message_received}";

			$total_ticket_received = db_get_count("ticket", "sent_to='client-{$loginas}'");
			$total_ticket_open = db_get_count("ticket", "sent_to='client-{$loginas}' AND state='open'");

			$ticket_text = $login->getKeywordUc('ticket') . ": {$total_ticket_open}/{$total_ticket_received}";
?>

				<div class="div_fixed_left">
					<div style="float: left">
						<a href="<?="/display.php?frm_action=list&frm_o_cname=smessage";?>">
							<div id="div_smessage" class="div_header_message" onMouseOver="this.style.backgroundColor='#fff'; this.style.color='#000';" onMouseOut="this.style.backgroundColor='#707070'; this.style.color='#fff';" title="<?= $login->getKeywordUc('message_title'); ?>">&nbsp;<?=$message_text;?>&nbsp;</div>
						</a>
					</div>
					<div style="float: left">
						<a href="<?="/display.php?frm_action=list&frm_o_cname=ticket";?>">
							<div id="div_ticket" class="div_header_message" onMouseOver="this.style.backgroundColor='#fff'; this.style.color='#000';" onMouseOut="this.style.backgroundColor='#707070'; this.style.color='#fff';" title="<?= $login->getKeywordUc('ticket_title'); ?>">&nbsp;<?=$ticket_text;?>&nbsp;</div>
						</a>
					</div>
				</div>