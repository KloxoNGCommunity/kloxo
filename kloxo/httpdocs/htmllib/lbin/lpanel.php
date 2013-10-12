<?php

chdir("../../");
include_once "lib/html/displayinclude.php";

lpanel_main();

function lpanel_main()
{
	global $gbl, $login, $ghtml;

	initProgram();
	init_language();

?>
<html>
<?php
	print_meta_lan();

	$gbl->__navigmenu = null;
	$gbl->__navig = null;

	$skincolor = $login->getSkinColor();

	// This should be called only in display.php, and not anywhere else. 
	// It doesn't matter anyway, since both lpanel.php, AND header.php never allows any modification to be carried out. 
	// Also, the display.php automatically deletes the login info, so if you click on any link on the header or the lpanel, 
	// you will automatically logged out. check_if_disabled_and_exit();

	$imgbordermain = "{$login->getSkinDir()}/top_line_medium.gif";
	
	if ($gbl->isOn('show_help')) {
		$background = "{$login->getSkinDir()}/top_line_dark.gif";
		$border = null;
	} else {
		$background = null;
	}

	$ghtml->print_include_jscript('left_panel');
?>
	<body topmargin="0" leftmargin="0" bottommargin="0" rightmargin="0" style="background-color:#fafafa">
<?php
	try {
		$ghtml->tab_vheight();
	} catch (exception $e) {
		print("The Resource List could not gathered....{$e->getMessage()}<br> \n");
	}
?>
	</body>
</html>
<?php
}

function print_ext_tree($object)
{
	global $gbl, $sgbl, $login, $ghtml;

	$icondir = get_image_path('/button/');
	$icon = "$icondir/{$object->getClass()}_list.gif";

	?>
	<script>
		Ext.onReady(function () {
			// shorthand
			var Tree = Ext.tree;

			var tree = new Tree.TreePanel('tree-div', {
				animate: true,
				loader: new Tree.TreeLoader({
					//dataUrl:'get-nodes.php'
					dataUrl: '/ajax.php?frm_action=tree'
				}),
				enableDD: true,
				containerScroll: true
			});

			// set the root node
			var root = new Tree.AsyncTreeNode({
				text: '<?=$object->getId()?>',
				href: '<?=$ghtml->getFullUrl('a=show')?>',
				hrefTarget: 'mainframe',
				icon: '<?=$icon?>',
				draggable: false,
				id: '/'
			});
			tree.setRootNode(root);

			// render the tree
			tree.render();
			root.expand();
		});
	</script>
<?php
}

