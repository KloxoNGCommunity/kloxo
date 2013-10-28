<?php

function print_tab_block_start($alist)
{
	global $gbl, $sgbl, $login, $ghtml;
	$img_path = $login->getSkinDir();

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
<!-- "START TAB" -->
	<div>
<?php
	if (!$sgbl->isBlackBackground()) {
?>

		<div class="tabcompleteleft">&nbsp;&nbsp;</div>

		<div class="shadow_nonbottom">
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

?>
		</div>	
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

	$buttonpath = '';
	$bpath = $login->getSkinDir();
	$bdpath = 'ddd';

	$button = '';

	$ghtml->resolve_int_ext($url, $psuedourl, $target);

	$descr = $ghtml->getActionDetails($url, $psuedourl, $buttonpath, $path, $post, $file, $name, $image, $__t_identity);

	$targetstring = $target;

	$form_name = $ghtml->createEncForm_name($file . "_" . $name);

	$check = $ghtml->compare_urls("display.php?{$ghtml->get_get_from_current_post(null)}", $url);

	$help = $descr['help'];
	$descstring = "<span title='$help'> &nbsp; &nbsp; $descr[2] &nbsp; &nbsp;</span>";

	if ($sgbl->isBlackBackground()) {
		if ($check) {
			$stylestring = "font-weight:bold; color:#999999";
		} else {
			$stylestring = "font-weight:normal; color:#999999";
		}

?>
		<a <?= $targetstring ?> href="<?= $url ?>"><span style="<?= $stylestring ?>"><?= $descstring ?></span> </a>
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
			<div class='verb'><a <?= $targetstring ?> href="<?= $url ?>"><?= $descstring ?></a></div>
		<!-- </td> -->
<?php
	}

	return $check;
}
