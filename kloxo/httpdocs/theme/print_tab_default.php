<?php 

	function print_tab_block_start($alist)
	{
		global $gbl, $sgbl, $login, $ghtml;

		$img_path = $login->getSkinDir();
		$imgtop = $img_path . "/top_line.gif";

		foreach ($alist as $k => $a) {
			$alist[$k] = $ghtml->getFullUrl($a);
		}

		if ($login->getSpecialObject('sp_specialplay')->isOn('enable_ajax')) {
			$ghtml->print_dialog($alist, $gbl->__c_object);
		}

?>
		<br/>
		<table cellspacing=0 cellpadding=0 width=100% border=0>
			<tr align=left valign=bottom>
				<td width=10>
					<img src='<?= $imgtop ?>' width='10' height='1'></td>
				<td>
					<table cellspacing='0' cellspacing='0'>
						<tr valign=bottom>
<?php
	/*
		if ($login->getSpecialObject('sp_specialplay')->isOn('simple_skin')) {
			$bordering = "border:1px solid #ddd; border-top:0";
		} else {
			$bordering = "border:0";
		}
	*/
		$bordering = "border:0";

		foreach ($alist as $k => $a) {
			$sel = print_tab_button($k, $a);
		}
?>
					</tr>
				</table>
			</td>
					</tr>
				</table>
			</td>
					</tr>
				</table>
			</td>
			<td width='100%'><img src='<?= $imgtop ?>' width='100%' height='1'></td>
		</tr>
	</table>
<!--	<table id="tblmain" cellpadding="0" cellspacing="0" border="0" width="100%" height="100%" style="<?= $bordering ?>; background-color: #fff">
		<tr>
			<td width="100%" align="center" valign="top">
				<br> -->
<?php
	}

	function print_tab_button($key, $url)
	{
		global $gbl, $sgbl, $login, $ghtml;

		$cobject = $gbl->__c_object;
		static $after_sel = false;
		$psuedourl = null;
		$target = null;
		$img_path = $login->getSkinDir();
		$imgtop = $img_path . '/top_line.gif';

		$buttonpath = get_image_path() . '/button/';
		$bpath = $login->getSkinDir();

	//	$bdpath = $login->getSkinColor();
		$bdpath = 'ddd';

		$button = $bpath . '/top_line_medium.gif';

		$descr = $ghtml->getActionDetails($url, $psuedourl, $buttonpath, $path, $post, $file, $name, $image, $__t_identity);

		$form_name = $ghtml->createEncForm_name($file . "_" . $name);

		$borderbottom = "style =\"border-bottom:1px solid #$bdpath;\"";

		$borderbot = "style =\"background:url($bpath/tab_select_bg2.gif) 0 0 repeat-x;\"";

		if ($check = $ghtml->compare_urls("display.php?{$ghtml->get_get_from_current_post(null)}", $url)) {
			$bgcolorstring = "bgcolor=#99aaff";
			$sel = "_select";
			$borderbottom = $borderbot;
		} else {
			$sel = "_select";
			$bgcolorstring = "bgcolor=#99aaff";
		}

		$imageheight = 24;


		if ($check) {
			$height = "36";
			$width = "3";
		} else {
			$height = "34";
			$width = "2";
		}

		$imgp = $login->getSkinDir();

		$imglt = $imgp . "tab{$sel}_lt.gif";
		$imgbg = $imgp . "tab{$sel}_bg.gif";
		$imgrt = $imgp . "tab{$sel}_rt.gif";

		$linkflag = true;

		if (csa($key, "__var_")) {
			$privar = strfrom($key, "__var_");
			if (!$cobject->checkButton($privar)) {
				$linkflag = false;
			}
		}

		$idstring = null;

		if ($login->getSpecialObject('sp_specialplay')->isOn('enable_ajax') && csb($key, "__v_dialog")) {
			$idstring = "id=$key-comment";
		}

?>
		<td>
			<table cellspacing=0 cellpadding=0  <?= $idstring ?> <?= $borderbottom ?> valign=bottom>
				<tr valign=bottom>
					<td valign=middle wrap><img src="<?= $imglt ?>" height="<?= $height ?>" width="<?= $width ?>">
						<a <?= $target ?> href="<?= $path ?>?<?=$ghtml->get_get_from_post(null, $post) ?>">
<?php
		$ghtml->printTabForTabButton($key, $linkflag, $height + 1, $imageheight, $sel, $imgbg, $url, $name, $image, $descr, $check);

?>
						</a>
					</td>
				</tr>
			</table>
		</td>

		<td><img src="<?= $imgrt ?>" width="<?= $width ?>" height="<?= $height ?>"></td>
<?php

		return $sel;
	}


