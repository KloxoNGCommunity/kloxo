<?php

function print_tab_block_start($alist)
{
	global $gbl, $sgbl, $login, $ghtml;
	$img_path = $login->getSkinDir();
	$imgtop = $img_path . "/top_line.gif";

	foreach ($alist as $k => $a) {
		$nalist[] = $ghtml->getFullUrl($a);
	}

	$alist = $nalist;

	if ($login->getSpecialObject('sp_specialplay')->isOn('enable_ajax')) {
		$ghtml->print_dialog($alist, $gbl->__c_object);
	}

	$skin_color = $login->getSpecialObject('sp_specialplay')->skin_color;

?>
	<br>
	<div>
<?php
						if (!$sgbl->isBlackBackground()) {
?>

								<div class="tabcompleteleft"> &nbsp; &nbsp; </div>

<?php
						}

						// This gives a list of key value pair, which shows which of the tab is selected.
						// For instance, if the fifth tab is the selected on, then $list[5] will be true,
						// while all the others will be false. This is necessary because, printing will need to
						// know if the next tab is the selected one.

						$list = $ghtml->whichTabSelect($alist);
						$list[-1] = false;
						$list[count($list) - 1] = false;

						foreach ($alist as $k => $a) {
							print_tab_button($k, $a, $list);
						}

						if (!$sgbl->isBlackBackground()) {
/*
?>
<script type="text/javascript">
<!--
	function toggle_wrapper(id) {
		var e = document.getElementById(id);
		if(e.style.display != 'none') {
			e.style.display = 'none';
		} else {
			e.style.display = 'block';
		}
	}
//-->
</script>

	<div class="tabcompleteright"><div style="float:right"><input type="button" value="Show/Hide" style="margin-right: 10px; padding: 0; border: 1px solid #ddd" onClick="toggle_wrapper('content_wrapper');" /></div></div>

<?php
*/
						}
?>
		
	</div>
<!-- "END TAB" -->
<?php
}

function print_tab_button($key, $url, $list)
{
	global $gbl, $sgbl, $login, $ghtml;

	$skin_color = $login->getSpecialObject('sp_specialplay')->skin_color;

	$cobject = $gbl->__c_object;
	static $after_sel = false;

	$psuedourl = null;
	$target = null;
	$img_path = $login->getSkinDir();
	$imgtop = $img_path . "/top_line.gif";

	$buttonpath = get_image_path() . "/button/";
	$bpath = $login->getSkinDir();
//	$bdpath= $login->getSkinColor();
	$bdpath = 'ddd';

	$button = $bpath . "/top_line_medium.gif";

	$ghtml->resolve_int_ext($url, $psuedourl, $target);

	$descr = $ghtml->getActionDetails($url, $psuedourl, $buttonpath, $path, $post, $file, $name, $image, $__t_identity);

	$targetstring = $target;

//	$ghtml->save_non_existant_image($image);

	$form_name = $ghtml->createEncForm_name($file . "_" . $name);
//	$bgcolorstring = null;
//	$sel = null;
//	$borderbottom = "style='border-bottom:1px solid black;'";

	$borderbottom = "style =\"border-bottom:2px solid #$bdpath;\"";
	$borderbot = "style =\"background:url($bpath/tab_select_bg2.gif) 0 0 repeat-x;\"";
	$check = $ghtml->compare_urls("display.php?{$ghtml->get_get_from_current_post(null)}", $url);

	if ($check) {
		$bgcolorstring = "bgcolor=#99aaff";
		$sel = "_select";
		$borderbottom = $borderbot;
	} else {
		$sel = "_select";
		$bgcolorstring = "bgcolor=#99aaff";
	}

	$imageheight = 24;
	$height = 34;

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

	$help = $descr['help'];
	$descstring = "<span title='$help'> &nbsp; &nbsp; $descr[2] &nbsp; &nbsp;</span>";

	if ($sgbl->isBlackBackground()) {
		if ($check) {
			$stylestring = "style='font-weight:bold'";
		} else {
			$stylestring = "style='font-weight:normal'";
		}

		$fcolor = "#999999";
?>
		<a <?= $targetstring ?> href="<?= $url ?>"><font <?= $stylestring ?>
				color="<?= $fcolor ?>"><?= $descstring ?></font> </a>
<?php
		return;
	}

	$lastkey = count($list);

	if ($check) {
?>
		<!-- <td class='tabnew'> -->
			<div class='verb3'><a <?= $targetstring ?> href="<?= $url ?>"><?= $descstring ?></a></div>
		<!-- </td> -->
<?php
	} else {
?>
		<!-- <td class='tabnew1'> -->
			<div nowrap class='verb'><a <?= $targetstring ?> href="<?= $url ?>"><?= $descstring ?></a></div>
		<!-- </td> -->
<?php
	}

	return $check;
}
