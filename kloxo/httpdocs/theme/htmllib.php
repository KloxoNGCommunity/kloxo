<?php

class FormVar
{
	function __get($key)
	{
		return null;
	}
}

class HtmlLib
{
	public $__message;

	static function checkForScript($value)
	{
		// [FIXME] replace this function with a sanitization method applied on request data
		// we can use the htmlpurifier project if we need to display html or htmlspecialchars to display text
		// for now we limit to log the attempt and stop the script

		if (csa($value, "<") || csa($value, ">") || csa($value, "(") || csa($value, ")")) {
			log_security("XSS attempt: $value");
			exit;
		}

		if (csa($value, "'")) {
			log_security("SQL injection attempt: $value");
			exit;
		}
	}

	function __construct()
	{
		global $gbl, $sgbl;

		$tmp = array_merge($_GET, $_POST); // [FIXME] We should use $tmp = $_REQUEST;

		if (isset($tmp['frm_o_o']) && $tmp['frm_o_o']) {
			ksort($tmp['frm_o_o']); // [FIXME] Why order? useless?

			foreach ($tmp['frm_o_o'] as $k => $v) {
				if (isset($k)) {
					self::checkForScript($k);
				}
				if (isset($v['class'])) {
					self::checkForScript($v['class']);
				}
				if (isset($v['nname'])) {
					self::checkForScript($v['nname']);
				}
			}
		}

		if (isset($tmp['frm_dttype']) && $tmp['frm_dttype']) {
			foreach ($tmp['frm_dttype'] as $k => $v) if (isset($v)) {
				self::checkForScript($v);
			}
		}

		if (isset($tmp['frm_accountselect'])) {
			self::checkForScript($tmp['frm_accountselect']);
		}

		if (isset($tmp['frm_hpfilter'])) {
			foreach ($tmp['frm_hpfilter'] as $k => $v) if (is_array($v)) {
				foreach ($v as $kk => $vv) self::checkForScript($vv);
			}
		}

		if (isset($tmp['frm_action'])) {
			self::checkForScript($tmp['frm_action']);
		}
		if (isset($tmp['frm_subaction'])) {
			self::checkForScript($tmp['frm_subaction']);
		}
		if (isset($tmp['frm_o_cname'])) {
			self::checkForScript($tmp['frm_o_cname']);
		}

		$hvar = array();

		$this->nname = 'html';

		$gbl->frm_ev_list = null;

		if (isset($tmp['frm_ev_list'])) {
			$gbl->frm_ev_list = $tmp['frm_ev_list'];
			unset($tmp['frm_ev_list']);
		}

		foreach ($tmp as $key => $value) {
			if (char_search_a($key, "_aaa_")) {
				$arvar = substr($key, 0, strpos($key, "_aaa_"));
				$arkey = substr($key, strpos($key, "_aaa_") + 5);
				$arval = $value;
				if (!csa($arvar, "password") && !csa($arvar, "text")) {
					$hvar[$arvar][$arkey] = $arval;
				} else {
					$hvar[$arvar][$arkey] = $arval;
				} // [FIXME] Same behaviour?
			} else {
				if (!is_array($value)) {
					if (!csa($key, "password") && !csa($key, "text")) {
						$hvar[$key] = $value;
					} else {
						$hvar[$key] = $value;
					} // [FIXME] Same behaviour?

				} else {
					$hvar[$key] = $value;
				}
			}
		}

		//FIXME: HACK.. fixing the quota variables from arrays to strings. Moving teh unlimited to the value itself.
		foreach ($hvar as $key => $val) {
			if (csa($key, '_c_priv_s_')) {
				if (!is_array($val)) {
					continue;
				}

				if (cse($key, "_flag")) {
					if (isset($val['checked'])) {
						$hvar[$key] = $val['checked'];
					} else {
						$hvar[$key] = $val['checkname'];
					}

					continue;
				}

				if (isset($val['unlimited'])) {
					$hvar[$key] = "Unlimited";
				} else {
					if (cse($key, "_time")) {
						$hvar[$key] = mktime(0, 0, 0, $val['month'], $val['day'], $val['year']);
					} else {
						if ($val['quotaname'] !== "") {
							$hvar[$key] = $val['quotaname'];
						} else {
							$hvar[$key] = $val['quotamax'];
						}
					}
				}
			}
		}

		foreach ($hvar as $key => $val) {
			if (is_array($val)) {

				if (isset($val['selectandvaluecheckname']) || isset($val['selectandvaluecheckhidden'])) {
					if (isset($val['selectandvaluecheckname'])) {
						$hvar[$key] = $val['selectandvaluecheckname'];
					} else {
						$hvar[$key] = $val['selectandvaluecheckhidden'];
					}
				}

				if (isset($val['checked']) || isset($val['checkname'])) {
					if (isset($val['checked'])) {
						$hvar[$key] = $val['checked'];
					} else {
						$hvar[$key] = $val['checkname'];
					}
				}
			}
		}

		$this->__http_vars = $hvar;
	}

	function do_url_decode(&$hvar)
	{
		foreach ($hvar as $key => &$value) {
			if (is_array($value)) {
				foreach ($value as $k => &$v) {
					if (is_array($v)) {
						foreach ($v as $nk => &$nv) $nv = urldecode($nv);
					} else {
						$v = urldecode($v);
					}
				}
			} else {
				$value = urldecode($value);
			}
		}
	}

	function getpath($key)
	{
		return $this->__path[$key];
	}

	function gfrm($key)
	{
		return (isset($this->__http_vars[$key])) ? $this->__http_vars[$key] : null;
	}

	function cgi($key)
	{
		return (isset($this->__http_vars[$key])) ? $this->__http_vars[$key] : null;
	}

	function isSelectShow()
	{
		return (strtolower($this->frm_action) === 'selectshow');
	}

	function get_htmlvar_details($key, &$class, &$variable, &$extra, &$value)
	{
		$string = $key;

		if (char_search_a($string, "_v_")) {
			$value = substr($string, strpos($string, "_v_") + 3);
			$string = substr($string, 0, strpos($string, "_v_"));
		}

		if (char_search_a($string, "_t_")) {
			$extra = substr($string, strpos($string, "_t_") + 3);
			$string = substr($string, 0, strpos($string, "_t_"));
		}

		if (char_search_a($string, "_c_")) {
			$variable = substr($string, strpos($string, "_c_") + 3);
			$string = substr($string, 0, strpos($string, "_c_"));
		}

		$class = substr($string, 4);
	}

	function getcgikey($key)
	{
		$nkey = substr($key, 4);
		$nkey = "_cgi_" . $nkey;

		return $nkey;
	}

	function getformkey($key)
	{
		$nkey = substr($key, 5);
		$nkey = "frm_" . $nkey;

		return $nkey;
	}

	function __get($key)
	{
		if (char_search_beg($key, "__path")) {
			dprint("Trying to access Path Variable in html $key");
		}

		if (char_search_beg($key, "__var")) {
			dprint("Trying to access Var Variable in html $key");
		}

		if (char_search_beg($key, "__c")) {
			dprint("Trying to access __c Variable in html $key");
		}

		$newkey = $key;

		if (!isset($this->__http_vars[$newkey])) {
			return null;
		}

		$v = $this->__http_vars[$newkey];

		if (is_array($v)) {
			foreach ($v as $kk => $vv) $nv[$kk] = $vv;
		} else {
			$nv = $v;
		}

		return $v;
	}

	function get_server_string($object)
	{
		if (!$object->isLocalhost() && $object->syncserver != $object->nname) {
			return "(on $object->syncserver)";
		}

		return null;
	}

	function print_info_block($obj, $ilist)
	{
?>

		<table class="tableheader" width="95%">
			<tr align="right">
				<td>
					<b><?= get_description($obj) ?> Info for <?= $obj->getId() ?></b>
				</td>
			</tr>
		</table>

		<table cellpadding="0" cellspacing="0" border="0" width="70%" align="center">
			<tr height="20">
<?php
		$class = get_class($obj);

		foreach ($ilist as $i) {
			$desc = "__desc_$i";
			$descr = get_classvar_description($class, $desc);
			$descr[2] = getNthToken($descr[2], 1);
?>
				<td width="16%" align="left">
					<span style="color: #bb3333;"><b><?= $descr[2] ?>: <?= $obj->display($i) ?></b></span>
				</td>
<?php

		}
?>
			</tr>
		</table>

<?php
	}

	function whichTabSelect($alist)
	{
		global $gbl, $sgbl, $login, $ghtml;

		$psuedourl = null;
		$target = null;
		$img_path = $login->getSkinDir();

		$imgtop = $img_path . '/images/top_line.gif';

		$buttonpath = get_image_path();

		foreach ($alist as $key => $url) {
			$check = $this->compare_urls("display.php?{$this->get_get_from_current_post(null)}", $url);
			$ret[$key] = $check;
		}

		return $ret;
	}

	function print_tab_block($alist)
	{
		global $gbl, $sgbl, $login, $ghtml;

		$skin_name = $login->getSpecialObject('sp_specialplay')->skin_name;

		include_once "theme/tab_{$skin_name}.php";

		print_tab_block_start($alist);
	}

	function compare_urls($a, $b)
	{
		$rvar = array("frm_o_o", "frm_dttype", "frm_o_nname", "frm_o_parent", "frm_action", "frm_o_cname", "frm_subaction");

		$this->get_post_from_get($a, $path, $pa);
		$this->get_post_from_get($b, $path, $pb);

		if (isset($pb["frm_o_cname"])) {
			if (exec_class_method($pb['frm_o_cname'], "consumeUnderParent")) {
				if ($pb['frm_action'] === 'list') {
					$pb["frm_o_cname"] = null;
					$pb['frm_action'] = 'show';
				}
			}
		}

		foreach ($rvar as $k) {
			if (!isset($pa[$k])) {
				$pa[$k] = null;
			}
			if (!isset($pb[$k])) {
				$pb[$k] = null;
			}
		}

		foreach ($rvar as $k) if ($pa[$k] != $pb[$k]) {
			return false;
		}

		return true;
	}

	function print_object_action_block($obj, $alist, $num)
	{
		global $gbl, $sgbl, $login, $ghtml;

		$syncserver = $this->get_server_string($obj);

		$buttonpath = get_image_path();

		$image = $this->get_image($buttonpath, '', 'resource', '.gif');
		$this->print_action_block($obj, $obj->get__table(), $alist, $num);
	}

	function create_action_block($class, $alist)
	{
		global $gbl, $sgbl, $login, $ghtml;

		if (!$alist) {
			return null;
		}

		$title = "main";
		$i = 0;
		$n = 0;

		foreach ($alist as $k => $a) {
			if (csb($k, "__title")) {
				$title = $k;
				$ret[$k][$k] = $a;
			}

			$ret[$title][$k] = $a;
			$ret[$title]['open'] = true;
		}

		if (isset($login->boxpos["{$class}_show"])) {
			foreach ($login->boxpos["{$class}_show"] as $k => $v) {
				if (!isset($ret[$k])) {
					continue;
				}

				$nret[$k] = $ret[$k];
				$nret[$k]['open'] = $v;
			}

			foreach ($ret as $k => $v) {
				if (!isset($nret[$k])) {
					$nret[$k] = $ret[$k];
				}
			}
		} else {
			$nret = $ret;
		}

		return $nret;
	}

	function print_style_desktop()
	{
		global $gbl, $sgbl, $login, $ghtml;

		$skindir = $login->getSkinDir();
		$col = $login->getSkinColor();
		// [FIXME] Put this css code on a file, NOT inline
?>

		<style type="text/css">
			.expanded a:hover {
				cursor: pointer;
			}

			.trigger a:hover {
				cursor: pointer;
			}

			.trigger {
				cursor: pointer;
				background: #efe8e0 url(<?=$skindir?>/images/expand.gif);

				border: 1px solid #<?=$col?>;
			}

			.expanded {
				cursor: pointer;
				background: #efe8e0 url(<?=$skindir?>/images/expand.gif);

				border: 1px solid #<?=$col?>;
			}

			.show {
				position: static;
				display: table;
			}

			.hide {
				position: absolute;
				left: -999em;
				height: 1px;
				width: 100px;
				overflow: hidden;
			}

			body {
				font-family: Tahoma, Verdana, Arial, Helvetica, Arial, sans-serif;
				color: #333;
			}

			#boundary {
				border-left: 1px solid #<?=$col?>;
				border-right: 1px solid #<?=$col?>;
				border-bottom: 1px solid #<?=$col?>;
			}

			a {
				color: #369;
			}

			h1 {
				font-family: Tahoma, Verdana, Arial, Helvetica, Arial, sans-serif;
				font-size: 130%;
				border-bottom: 1px solid #999;
			}

			h2 {
				font-family: Tahoma, Verdana, Arial, Helvetica, Arial, sans-serif;
				font-size: 115%;
				color: #003360;
				background: #efe8e0 url(<?=$skindir?>/images/expand.gif);

				margin-bottom: 0
			}

			h3 {
				font-family: Tahoma, Verdana, Arial, Helvetica, Arial, sans-serif;
				font-size: 100%;
			}

			p code {
				font-size: 110%;
				color: #666;
				font-weight: bold;
			}

			pre {
				background: #eee;
				padding: .5em 1em;
				border: 1px solid #<?=$col?>;
			}

			h1 code, h2 code, h3 code {
				font-family: Tahoma, Verdana, Arial, Helvetica, Arial, sans-serif;
			}

			h1 code {
				font-family: Tahoma, Verdana, Arial, Helvetica, Arial, sans-serif;
			}

			#header {
				background: #69c;
				border-top: 1px solid #9cf;
				border-bottom: 1px solid #369;
			}

			#content {
				font-size: 90%;
			}

			#download {
				position: absolute;
				top: 9em;
				width: 15em;
				right: 4em;
			}

			#download ul {
				background: #ccf;
				padding: .5em 0 .5em 1.5em;
			}

			#download h2 {
				background: #369;
				color: #fff;
				font-size: 90%;
				padding: 0.5em;
				margin: .5em 0 0 0;
				border-bottom: 1px solid #036;
				border-right: 1px solid #036;
				border-top: 1px solid #69c;
				border-left: 1px solid #<?=$col?>;
			}

			#download li {
				list-style-type: square;
			}

			#header a img {
				padding: 5px 1em;
			}

			img {
				border: 0;
			}
		</style>

<?php
	}

	function print_style_home()
	{
		global $gbl, $sgbl, $login, $ghtml;

		$skindir = $login->getSkinDir();
		$col = $login->getSkinColor();
		// [FIXME] Put this css code on a file, NOT inline
?>

		<style type="text/css">
			.expanded a:hover {
				cursor: pointer;
			}

			.trigger a:hover {
				cursor: pointer;
			}

			.trigger {
				cursor: pointer;
				background: #efe8e0 url(<?=$skindir?>/images/expand.gif);

				border: 1px solid #<?=$col?>;
				height: 25px;
			}

			.expanded {
				cursor: pointer;
				background: #efe8e0 url(<?=$skindir?>/images/expand.gif);

				border: 1px solid #<?=$col?>;
				height: 25px;
			}

			.show {
				position: static;
				display: table;
			}

			.hide {
				position: absolute;
				left: -999em;
				height: 1px;
				width: 100px;
				overflow: hidden;
			}

			body {
				font-family: Tahoma, Verdana, Arial, Helvetica, Arial, sans-serif;
				color: #333;
				margin: 0;
				padding: 0;
			}

			#boundary {
				margin-left: 20px;
				margin-right: 100px;
				border-left: 1px solid #<?=$col?>;
				border-right: 1px solid #<?=$col?>;
				border-bottom: 1px solid #<?=$col?>;
			}

			a {
				color: #369;
			}

			h1 {
				font-family: Tahoma, Verdana, Arial, Helvetica, Arial, sans-serif;
				font-size: 130%;
				border-bottom: 1px solid #999;
			}

			h2 {
				font-family: Tahoma, Verdana, Arial, Helvetica, Arial, sans-serif;
				font-size: 130%;
				color: #003370;
				background: #efe8e0 url(<?=$skindir?>/images/expand.gif);

				margin-bottom: 10px;
				margin-top: 10px
			}

			h3 {
				font-family: Tahoma, Verdana, Arial, Helvetica, Arial, sans-serif;
				font-size: 100%;
			}

			p code {
				font-size: 110%;
				color: #666;
				font-weight: bold;
			}

			pre {
				background: #eee;
				padding: .5em 1em;
				border: 1px solid #<?=$col?>;
			}

			h1 code, h2 code, h3 code {
				font-family: Tahoma, Verdana, Arial, Helvetica, Arial, sans-serif;
			}

			h1 code {
				font-family: Tahoma, Verdana, Arial, Helvetica, Arial, sans-serif;
			}

			#header {
				padding: 0;
				left: 0;
				top: 0;
				background: #69c;
				margin: 0;
				border-top: 1px solid #9cf;
				border-bottom: 1px solid #369;
			}

			#content {
				font-size: 90%;
				margin-top: 0;
			}

			#download {
				position: absolute;
				top: 9em;
				width: 15em;
				right: 4em;
			}

			#download ul {
				background: #ccf;
				margin: 0;
				padding: .5em 0 .5em 1.5em;
			}

			#download h2 {
				background: #369;
				color: #fff;
				font-size: 90%;
				padding: 0 .5em;
				margin: .5em 0 0 0;
				border-bottom: 1px solid #036;
				border-right: 1px solid #036;
				border-top: 1px solid #69c;
				border-left: 1px solid #<?=$col?>;
			}

			#download li {
				list-style-type: square;
			}

			#header a img {
				border: 0;
				padding: 5px 1em;
			}
		</style>

<?php
	}

	function print_domcollapse($sel)
	{
		global $gbl, $sgbl, $login, $ghtml;

		$skinget = $login->getSkinDir();

		if ($sel == "des") {
			$style = $this->print_style_desktop();
		}

		if ($sel == "hom") {
			$style = $this->print_style_home();
		}
?>

		<script type="text/javascript">
			dc = {
				triggerElements: '*', // elements to trigger the effect
				parentElementId: null, // ID of the parent element (keep null if none)
				uniqueCollapse: false, // is set to true only one element can be open at a time

				// CSS class names
				trigger: 'trigger',
				triggeropen: 'expanded',
				hideClass: 'hide',
				showClass: 'show',
				// pictures and text alternatives
				closedPic: '<?=$skinget?>/images/plus.gif',
				closedAlt: 'expand section',
				openPic: '<?=$skinget?>/images/minus.gif',
				openAlt: 'collapse section',
				right: 'right',
				center: 'center',
				/* Doesn't work with Safari
				 hoverClass:'hover',
				 */
				init: function (e) {
					var temp;
					if (!document.getElementById || !document.createTextNode) {
						return;
					}
					if (!dc.parentElementId) {
						temp = document.getElementsByTagName(dc.triggerElements);
					} else if (document.getElementById(dc.parentElementId)) {
						temp = document.getElementById(dc.parentElementId).getElementsByTagName(dc.triggerElements);
					} else {
						return;
					}
					dc.tempLink = document.createElement('a');
					dc.tempLink.setAttribute('href', '#');
					dc.tempLink.appendChild(document.createElement('img'));
					for (var i = 0; i < temp.length; i++) {
						if (dc.cssjs('check', temp[i], dc.trigger) || dc.cssjs('check', temp[i], dc.triggeropen)) {
							dc.makeTrigger(temp[i], e);
						}
					}
				},
				makeTrigger: function (o, e) {
					var tl = dc.tempLink.cloneNode(true);
					var tohide = o.nextSibling;
					while (tohide.nodeType != 1) {
						tohide = tohide.nextSibling;
					}
					o.tohide = tohide;
					if (!dc.cssjs('check', o, dc.triggeropen)) {
						dc.cssjs('add', tohide, dc.hideClass);
						tl.getElementsByTagName('img')[0].setAttribute('align', dc.right);
						tl.getElementsByTagName('img')[0].setAttribute('src', dc.closedPic);
						tl.getElementsByTagName('img')[0].setAttribute('alt', dc.closedAlt);
						tl.getElementsByTagName('img')[0].setAttribute('title', dc.closedAlt);
						//o.setAttribute('title',dc.closedAlt);
					} else {
						dc.cssjs('add', tohide, dc.showClass);
						tl.getElementsByTagName('img')[0].setAttribute('align', dc.right);
						tl.getElementsByTagName('img')[0].setAttribute('src', dc.openPic);
						tl.getElementsByTagName('img')[0].setAttribute('alt', dc.openAlt);
						tl.getElementsByTagName('img')[0].setAttribute('title', dc.openAlt);
						//o.setAttribute('title',dc.openAlt);
						dc.currentOpen = o;
					}
					//  dc.addEvent(o,'click',dc.addCollapse,false);
					/* Doesn't work with Safari
					 dc.addEvent(o,'mouseover',dc.hover,false);
					 dc.addEvent(o,'mouseout',dc.hover,false);
					 */
					o.insertBefore(tl, o.firstChild);
					dc.addEvent(tl, 'click', dc.addCollapse, false);
					// Safari hacks
					tl.onclick = function () {
						return false;
					};
					o.onclick = function () {
						return false;
					}
				},

				addCollapse: function (e) {
					var action, pic;
					// hack to fix safari's redraw bug
					// as mentioned on http://en.wikipedia.org/wiki/Wikipedia:Browser_notes#Mac_OS_X
					if (self.screenTop && self.screenX) {
						window.resizeTo(self.outerWidth + 1, self.outerHeight);
						window.resizeTo(self.outerWidth - 1, self.outerHeight);
					}
					if (dc.uniqueCollapse && dc.currentOpen) {
						dc.currentOpen.getElementsByTagName('img')[0].setAttribute('align', dc.right);
						dc.currentOpen.getElementsByTagName('img')[0].setAttribute('src', dc.closedPic);
						dc.currentOpen.getElementsByTagName('img')[0].setAttribute('alt', dc.closedAlt);
						dc.currentOpen.setAttribute('img', dc.closedAlt);
						dc.cssjs('swap', dc.currentOpen.tohide, dc.showClass, dc.hideClass);
						dc.cssjs('remove', dc.currentOpen, dc.triggeropen);
						dc.cssjs('add', dc.currentOpen, dc.trigger);
					}
					var o = dc.getTarget(e);
					if (o.tohide) {
						if (dc.cssjs('check', o.tohide, dc.hideClass)) {
							o.getElementsByTagName('img')[0].setAttribute('align', dc.right);
							o.getElementsByTagName('img')[0].setAttribute('src', dc.openPic);
							o.getElementsByTagName('img')[0].setAttribute('alt', dc.openAlt);
							o.getElementsByTagName('img')[0].setAttribute('title', dc.openAlt);
							//o.setAttribute('title',dc.openAlt);
							dc.cssjs('swap', o.tohide, dc.hideClass, dc.showClass);
							dc.cssjs('add', o, dc.triggeropen);
							dc.cssjs('remove', o, dc.trigger);
						} else {
							o.getElementsByTagName('img')[0].setAttribute('align', dc.right);
							o.getElementsByTagName('img')[0].setAttribute('src', dc.closedPic);
							o.getElementsByTagName('img')[0].setAttribute('alt', dc.closedAlt);
							o.getElementsByTagName('img')[0].setAttribute('title', dc.closedAlt);
							//o.setAttribute('title',dc.closedAlt);
							dc.cssjs('swap', o.tohide, dc.showClass, dc.hideClass);
							dc.cssjs('remove', o, dc.triggeropen);
							dc.cssjs('add', o, dc.trigger);
						}
						dc.currentOpen = o;
						dc.cancelClick(e);
						//document.getElementById('debug').innerHTML=o.tohide.className;
					} else {
						dc.cancelClick(e);
					}
				},
				/* helper methods */
				getTarget: function (e) {
					var target = window.event ? window.event.srcElement : e ? e.target : null;
					if (!target) {
						return false;
					}
					while (!target.tohide && target.nodeName.toLowerCase() != 'body') {
						target = target.parentNode;
					}

					return target;
				},
				cancelClick: function (e) {
					if (window.event) {
						window.event.cancelBubble = true;
						window.event.returnValue = false;
						return;
					}
					if (e) {
						e.stopPropagation();
						e.preventDefault();
					}
				},
				addEvent: function (elm, evType, fn, useCapture) {
					if (elm.addEventListener) {
						elm.addEventListener(evType, fn, useCapture);
						return true;
					} else if (elm.attachEvent) {
						return elm.attachEvent('on' + evType, fn);
					} else {
						elm['on' + evType] = fn;
					}
				},
				cssjs: function (a, o, c1, c2) {
					switch (a) {
						case 'swap':
							o.className = !dc.cssjs('check', o, c1) ? o.className.replace(c2, c1) : o.className.replace(c1, c2);
							break;
						case 'add':
							if (!dc.cssjs('check', o, c1)) {
								o.className += o.className ? ' ' + c1 : c1;
							}
							break;
						case 'remove':
							var rep = o.className.match(' ' + c1) ? ' ' + c1 : c1;
							o.className = o.className.replace(rep, '');
							break;
						case 'check':
							return new RegExp("(^|\\s)" + c1 + "(\\s|$)").test(o.className);
							break;
					}
				}
			};
			dc.addEvent(window, 'load', dc.init, false);
		</script>

<?php
	}

	function print_dialog($alist, $obj)
	{
		global $gbl, $sgbl, $login, $ghtml;

		$buttonpath = get_image_path();

		$lclass = $login->get__table();
		$talist = null;

		$dwidth = "600";
		$dheight = "400";

		if ($login->dialogsize) {
			list($dwidth, $dheight) = explode("x", $login->dialogsize);
		}

		foreach ($alist as $k => $a) {
			if (!csb($k, "__v_dialog")) {
				continue;
			}
			$talist[$k] = $a;
		}

		if (!$talist) {
			return;
		}

		$buttonpath = get_image_path();
?>

		<div id="comments-dlg" style="visibility:hidden;">
			<div class="x-dlg-hd"><?= $obj->getId() ?></div>
			<div class="x-dlg-bd">
<?php

		$count = 0;
		$first_tab = null;

		foreach ($talist as $k => $a) {
			$descr = $this->getActionDetails($a, null, $buttonpath, $path, $post, $file, $name, $image, $__t_identity);

			if ($count === 0) {
				$first_tab = $k;
			}

			$count++;
?>
					<div id="<?= $k ?>-tab" class="x-dlg-tab" title="<?= $descr[2] ?>">
						<div id="<?= $k ?>-list" class="inner-tab"></div>
					</div>
<?php
		}
?>

			</div>
			<div class="x-dlg-ft">
				<div id="dlg-msg">
				<span id="post-error" class="posting-msg"><img src="/theme/extjs/warning.gif" width="16" height="16"
				                                               align="absmiddle"/>&nbsp;<span
						id="post-error-msg"></span></span>
					<span id="post-wait" class="posting-msg"><img src="/theme/extjs/default/grid/loading.gif" width="16"
					                                              height="16"
					                                              align="absmiddle"/>&nbsp;Updating...</span>
				</div>
			</div>
		</div>

		<link rel="stylesheet" type="text/css" href="/theme/extjs/css/post.css"/>

		<script>
			var global_formname;
			var Comments = function () {
				var dialog, postLink, viewLink, txtComment;
				var tabs, commentsList, renderer;
				var wait, error, errorMsg;
				var posting = false;

				var global_tabid = '<?=$first_tab?>-tab';

				return {

					init: function () {
						// cache some elements for quick access
						// txtComment = Ext.get('comment');
						wait = Ext.get('post-wait');
						error = Ext.get('post-error');
						errorMsg = Ext.get('post-error-msg');

						this.createDialog();

<?php
		foreach ($talist as $k => $a) {
			$na = str_replace("display.php", "ajax.php", $a);
?>
						<?=$k?>Link = Ext.get('<?=$k?>-comment');
						<?=$k?>Link.on('click', function (e) {
							e.stopEvent();
							var tabname = global_tabid.substr(0, global_tabid.length - 4);

							if (tabname == '<?=$k?>') {
								var tList = Ext.get('<?=$k?>-list');
								// set up the comment renderer, all ajax requests for commentsList
								// go through this render
								var tum = tList.getUpdateManager();
								//tum.update('/ajax.php?frm_action=updateform&frm_subaction=password');
								tum.update('<?=$na?>&r=' + Math.random());
							}

							tabs.activate('<?=$k?>-tab');
							dialog.show(<?=$k?>Link);
						});
<?php
		}
?>

					},

					okComment: function () {
						this.submitComment('ok');
					},

					allComment: function () {
						if (confirm("Do you really want to apply the above settings to all the objects visible in the top right selectbox?")) {
							this.submitComment('all');
						} else {
							return null;
						}
					},

					// submit the comment to the server
					submitComment: function (x) {
						if (!check_for_needed_variables(global_formname)) {
							return;
						}

						g_postBtn.disable();
						g_okBtn.disable();
						//g_allBtn.disable();
						wait.radioClass('active-msg');

						var commentSuccess = function (o) {
							g_postBtn.enable();
							g_okBtn.enable();
							//g_allBtn.enable();

							var data = renderer.parse(o.responseText);
							//alert(o.responseText);
							data = eval('(' + o.responseText + ')');
							// if we got a comment back
							if (data) {
								if (data.returnvalue == 'success') {
									if (data.refresh) {
										top.mainframe.window.location.reload();
									}
									if (x == 'ok' || x == 'all') {
										dialog.hide();
									}
								} else {
									var tabname = global_tabid.substr(0, global_tabid.length - 4);
									var tList = Ext.get(tabname + '-list');
									// set up the comment renderer, all ajax requests for commentsList
									// go through this render
									var tum = tList.getUpdateManager();
									//tum.update('/ajax.php?frm_action=updateform&frm_subaction=password');
									tum.update('/ajax.php?r=' + Math.random() + "&" + data.url);
								}
								wait.removeClass('active-msg');
								renderer.append(data.message);
								return data.returnvalue;
							} else {
								error.radioClass('active-msg');
								errorMsg.update(o.responseText);
								//eval(tabname + "um.update('/ajax.php?frm_action=updateform&frm_subaction=password');");

							}
						};

						var commentFailure = function (o) {
							g_postBtn.enable();
							g_allBtn.enable();
							g_okBtn.enable();
							error.radioClass('active-msg');
							errorMsg.update('Unable to connect.');
						};

						if (x == 'all') {
							var ur = '/ajax.php?frm_change=updateall'
						} else {
							var ur = '/ajax.php'
						}

						Ext.lib.Ajax.formRequest(global_formname, ur, {success: commentSuccess, failure: commentFailure});
					},

					createDialog: function () {
						dialog = new Ext.BasicDialog("comments-dlg", {
							autoTabs: true,
							width:<?=$dwidth?>,
							height:<?=$dheight?>,
							shadow: true,
							minWidth: 300,
							minHeight: 300
						});
						dialog.addKeyListener(27, dialog.hide, dialog);
						g_okBtn = dialog.addButton('OK', this.okComment, this);
						dialog.addButton('Cancel', dialog.hide, dialog);
						g_postBtn = dialog.addButton('Apply', this.submitComment, this);
						g_allBtn = dialog.addButton('All Update', this.allComment, this);


						// clear any messages and indicators when the dialog is closed
						dialog.on('hide', function () {
							wait.removeClass('active-msg');
							error.removeClass('active-msg');
							//txtComment.dom.value = '';
						});

						// stoe a refeence to the tabs
						tabs = dialog.getTabs();

						// auto fit the comment box to the dialog size
						var sizeTextBox = function (x) {
							//txtComment.setSize(dialog.size.width-44, dialog.size.height-264);
							if (x != 'init') {
								Ext.lib.Ajax.request('post', '/ajax.php', {success: null, failure: null },
									'frm_action=update&frm_subaction=dialogsize&frm_<?=$lclass?>_c_dialogsize=' +
										dialog.size.width + 'x' + dialog.size.height);
							}
						};

						sizeTextBox('init');
						dialog.on('resize', sizeTextBox);

						// hide the post button if not on Post tab
						tabs.on('tabchange', function (panel, tab) {
							// postBtn.setVisible(tab.id == 'post-tab');
							global_tabid = tab.id;
						});

<?php
		foreach ($talist as $k => $a) {
			if (!csb($k, "__v_dialog")) {
				continue;
			}

			$na = str_replace("display.php", "ajax.php", $a);
?>
						<?=$k?>List = Ext.get('<?=$k?>-list');
						// set up the comment renderer, all ajax requests for commentsList
						// go through this render
						renderer = new CommentRenderer(<?=$k?>List);
						var <?=$k?>um = <?=$k?>List.getUpdateManager();
						<?=$k?>um.setRenderer(renderer);

						// lazy load the comments when the view tab is activated
						tabs.getTab('<?=$k?>-tab').on('activate', function () {
							<?=$k?>um.update('<?=$na?>&r=' + Math.random());
						});
<?php
		}
?>

					}
				};
			}();

			// This class handles rendering JSON into comments
			var CommentRenderer = function (list) {
				// create a template for each JSON object
				var tpl = new Ext.DomHelper.Template('{lx__form}');

				this.parse = function (json) {
					try {
						return eval('(' + json + ')');
					} catch (e) {
					}
					return null;
				};

				// public render function for use with UpdateManager
				this.render = function (el, response) {
					var data = this.parse(response.responseText);
					if (!data || !data.lx__form || data.lx__form.length < 1) {
						el.update('the_server_didnt_return_a_form: error:' + response.responseText);
						return;
					}
					// clear loading
					el.update('');

					if (data.allbutton) {
						g_allBtn.enable();
					} else {
						g_allBtn.disable();
					}

					if (data.ajax_dismiss) {
						g_allBtn.setVisible(false);
						g_okBtn.setVisible(false);
						g_postBtn.setVisible(false);
					} else {
						g_allBtn.setVisible(true);
						g_okBtn.setVisible(true);
						g_postBtn.setVisible(true);
					}

					global_need_list = new Array();
					global_match_list = new Array();
					for (v in data.ajax_need_var) {
						global_need_list[v] = data.ajax_need_var[v];
					}
					for (v in data.ajax_match_var) {
						global_match_list[v] = data.ajax_match_var[v];
					}
					global_formname = data.ajax_form_name;

					this.append(data);
				};

				// appends a comment
				this.append = function (data) {
					tpl.append(list.dom, data);
				};
			};

			Ext.EventManager.onDocumentReady(Comments.init, Comments, true);
		</script>

<?php
	}

	function print_drag_drop($obj, $ret, $class)
	{
		global $gbl, $sgbl, $login;

		$lclass = $login->get__table();
		$skindir = $login->getSkinDir();
		$col = $login->getSkinColor();
		$plus = "{$skindir}/images/plus.gif";
		$minus = "{$skindir}/images/minus.gif";

		$buttonpath = get_image_path();
?>

		<script>
			(function () {

				var Dom = YAHOO.util.Dom;
				var Event = YAHOO.util.Event;
				var DDM = YAHOO.util.DragDropMgr;

				//////////////////////////////////////////////////////////////////////////////
				// example app
				//////////////////////////////////////////////////////////////////////////////
				YAHOO.example.DDApp = {
					init: function () {
						var dd;

						dd = new YAHOO.util.DDTarget("mainbody");

<?php
		foreach ($ret as $title => $a) {
			$nametitle = strfrom($title, "__title_");
?>
						dd = new YAHOO.example.DDList('item_<?=$nametitle?>');
						dd.setXConstraint(0, 0, 0);
						dd.setHandleElId('handle_<?=$nametitle?>');
<?php
		}
?>

						//Event.on("showButton", "click", this.showOrder);
						//Event.on("switchButton", "click", this.switchStyles);
					},

					showOrder: function () {
					},

					switchStyles: function () {
					}
				};

				//////////////////////////////////////////////////////////////////////////////
				// custom drag and drop implementation
				//////////////////////////////////////////////////////////////////////////////

				YAHOO.example.DDList = function (id, sGroup, config) {

					YAHOO.example.DDList.superclass.constructor.call(this, id, sGroup, config);

					this.logger = this.logger || YAHOO;
					var el = this.getDragEl();
					Dom.setStyle(el, "opacity", 0.67); // The proxy is slightly transparent

					this.goingUp = false;
					this.lastY = 0;
				};

				YAHOO.extend(YAHOO.example.DDList, YAHOO.util.DDProxy, {
					startDrag: function (x, y) {
						this.logger.log(this.id + " startDrag");

						// make the proxy look like the source element
						var dragEl = this.getDragEl();
						var clickEl = this.getEl();
						Dom.setStyle(clickEl, "visibility", "hidden");

						//dragEl.innerHTML = clickEl.innerHTML;
						Dom.setStyle(dragEl, "color", Dom.getStyle(clickEl, "color"));
						Dom.setStyle(dragEl, "backgroundColor", Dom.getStyle(clickEl, "backgroundColor"));
						Dom.setStyle(dragEl, "border", "1px solid #<?=$col?>");
					},

					endDrag: function (e) {

						var srcEl = this.getEl();
						var proxy = this.getDragEl();

						// Show the proxy element and animate it to the src element's location
						Dom.setStyle(proxy, "visibility", "");
						var a = new YAHOO.util.Motion(proxy, {
							points: {
								to: Dom.getXY(srcEl)
							}
						}, 0.2, YAHOO.util.Easing.easeOut);
						var proxyid = proxy.id;
						var thisid = this.id;

						// Hide the proxy and show the source element when finished with the animation
						a.onComplete.subscribe(function () {
							Dom.setStyle(proxyid, "visibility", "hidden");
							Dom.setStyle(thisid, "visibility", "");
						});
						a.animate();
					},

					onDragDrop: function (e, id) {

						// If there is one drop interaction, the li was dropped either on the list,
						// or it was dropped on the current location of the source element.


						var page = document.getElementById('show_page');
						var out = parseList(page, "List 1");
						var url = 'frm_<?=$lclass?>_c_title_class=<?=$class?>\&frm_action=update\&frm_subaction=boxpos\&frm_<?=$lclass?>_c_page=' + out;
						var request = YAHOO.util.Connect.asyncRequest('post', "/ajax.php", callback, url);

					},

					onDrag: function (e) {

						// Keep track of the direction of the drag for use during onDragOver
						var y = Event.getPageY(e);

						if (y < this.lastY) {
							this.goingUp = true;
						} else if (y > this.lastY) {
							this.goingUp = false;
						}

						this.lastY = y;
					},

					onDragOver: function (e, id) {

						var srcEl = this.getEl();
						var destEl = Dom.get(id);

						// We are only concerned with list items, we ignore the dragover
						// notifications for the list.
						if (destEl.id == 'mainbody') {
							return;
						}
						if (destEl.nodeName.toLowerCase() == "div") {
							var orig_p = srcEl.parentNode;
							var p = destEl.parentNode;

							if (this.goingUp) {
								p.insertBefore(srcEl, destEl); // insert above
							} else {
								p.insertBefore(srcEl, destEl.nextSibling); // insert below
							}

							DDM.refreshCache();
						}
					}
				});

				Ext.EventManager.onDocumentReady(YAHOO.example.DDApp.init, YAHOO.example.DDApp, true);

			})();

		</script>

<?php
	}

	function print_div_button($actionlist, $type, $imgflag, $key, $url, $ddate = null)
	{
		global $gbl, $sgbl, $login;

		$button_type = $login->getSpecialObject('sp_specialplay')->button_type;

		$icondir = get_image_path();

		$obj = $gbl->__c_object;
		$psuedourl = null;
		$target = null;

		$buttonpath = get_image_path();

		$linkflag = true;

		if (csa($key, "__var_")) {
			$privar = strfrom($key, "__var_");
			if (!$obj->checkButton($privar)) {
				$linkflag = false;
			}
		}

		$complete = $this->resolve_int_ext($url, $psuedourl, $target);

		if ($complete) {
			$this->get_post_from_get($url, $path, $post);
			$descr = $this->getActionDescr($path, $post, $class, $name, $identity);
			$complete['name'] = str_replace("<", "&lt;", $complete['name']);
			$complete['name'] = str_replace(">", "&gt;", $complete['name']);
			$name = $complete['name'];
			$bname = $complete['bname'];
			$descr[1] = $complete['name'];
			$descr[2] = $complete['name'];
			$descr['desc'] = $complete['name'];
			$file = $class;

			if (lxfile_exists("theme/custom/$bname.gif")) {
				$image = "/theme/custom/$bname.gif";
			} else {
				$image = "$icondir/custom_button.gif";
			}

			$__t_identity = $identity;
		} else {
			$url = str_replace("[%s]", $obj->nname, $url);

			$descr = $this->getActionDetails($url, $psuedourl, $buttonpath, $path, $post, $file, $name, $image, $__t_identity);
		}

		$this->save_non_existant_image($image);

		$str = randomString(8);
		$form_name = $this->createEncForm_name("{$file}_{$name}_$str");
		$form_name = fix_nname_to_be_variable($form_name);

		if (csb($url, "http:/")) {
			$formmethod = "get";
		} else {
			$formmethod = $sgbl->method;
		}

		// Use get always. Only in forms should post be used.
		$formmethod = 'get';

		$dividentity = "searchdiv_{$descr['desc']}";

		$dividentity = str_replace(" ", "_", $dividentity);
		$dividentity = strtolower($dividentity);

		for ($i = 0; $i < 10; $i++) {
			if (!isset($actionlist[$dividentity])) {
				break;
			}

			$dividentity = "{$dividentity}$i";
		}

		if ($button_type === 'reverse-font') {
			$b = $this->get_metro_color();
			$bgcolor = "background-color: {$b[0]}";
		} else {
			$bgcolor = "";
		}

?>

		<div id="<?= $dividentity ?>" style="visibility: visible; float: left; margin: 3px; <?= $bgcolor ?>">
			<a <?= $target ?> href="<?= $path ?>?<?= $this->get_get_from_post(null, $post) ?>">
				<?= $this->print_div_for_divbutton($key, $imgflag, $linkflag, $form_name, $name, $image, $descr) ?> </a>
		</div>

<?php

		return $dividentity;
	}

	function print_action_block($obj, $class, $alist, $num)
	{
		global $gbl, $sgbl, $login;

		$skin_name = $login->getSpecialObject('sp_specialplay')->skin_name;
		$show_direction = $login->getSpecialObject('sp_specialplay')->show_direction;
		$button_type = $login->getSpecialObject('sp_specialplay')->button_type;

		$lclass = $login->get__table();
		$skindir = $login->getSkinDir();
		$talist = $alist;

		$ret = $this->create_action_block($class, $alist);

		$retcount = count($ret);

		//	$col = $login->getSkinColor();
		$col = 'ddd';

		$plus = "{$skindir}/images/plus.gif";
		$minus = "{$skindir}/images/minus.gif";

		$buttonpath = get_image_path();

		if ($sgbl->isDebug()) {
			$outputdisplay = 'inline';
		} else {
			$outputdisplay = 'none';
		}

		if ($login->getSpecialObject('sp_specialplay')->isOn('enable_ajax')) {
			$this->print_dialog($talist, $obj);
			$this->print_drag_drop($obj, $ret, $class);
		}

		if ($sgbl->isBlackBackground()) {
			$backgimage = "{$skindir}/images/black.gif";
			$minus = "{$skindir}/images/black.gif";
			$plus = "{$skindir}/images/black.gif";
			$col = "333";
		} else {
			$backgimage = "{$skindir}/images/expand.gif";
		}

		if (($show_direction === 'vertical') || ($retcount === 1)) {
			$sectionfloat = "inherit";
			$sectionheight = "auto";
			$showpagewidth = "640";
			if ($retcount === 1) {
				$sectionmargin = "0 auto 10px auto";
				$sectionwidth = "800";
			} else {
				$sectionmargin = "0 10px 10px 0";
				$sectionwidth = "640";
			}
		} elseif ($show_direction === 'vertical 2') {
			$sectionwidth = "300";
			$sectionfloat = "inherit";
			$sectionheight = "auto";
			$showpagewidth = "640";
			$sectionmargin = "0 0 10px 15px";
		} elseif ($show_direction === 'horizontal') {
			$sectionwidth = "400";
			$sectionfloat = "left";
			$sectionheight = "415";
			$showpagewidth = "615";
			$sectionmargin = "0 10px 10px 0";
		}

		if ($retcount === 1) {
			$wrapstyle = "width: 800px; margin: 0 auto 20px auto";
		} else {
			$wrapstyle = "float:left; margin: 0 0 20px 15px";
		}
?>

		<style>
			div.section, div#createNew {
				border: 1px solid #<?=$col?>;
				background-color: #fff;
				/* margin: 9px 5px; */
				margin: <?=$sectionmargin?>;
				padding: 0;
				/* width: 520px; */
				width: <?=$sectionwidth?>px;
				resize: both;
				overflow: hidden;
				float: left;
				height: <?=$sectionheight?>;
			}

			div#createNew input {
				margin-left: 5px;
			}

			div#createNew h3, div.section h3 {
				font-size: 12px;
				padding: 2px 5px;
				margin: 0 0 10px 0;
				display: block;
				font-family: Tahoma, Verdana, Arial, Helvetica, Arial, sans-serif;
				color: #003360;
				background: #efe8e0 url(<?=$skindir?>/images/expand.gif);
				border-bottom: 1px solid #<?=$col?>;
			}

			div.section h3 {
				cursor: move;
			}

			div.demo div.example span {
				margin: 0;
				padding: 0;
				font-size: 1.0em;
				text-align: center;
				display: block;
			}

			div.demo {
				margin: 0;
				overflow: visible;
				position: relative;
				width: 100%;
			}

			h1 {
				margin-bottom: 0;
				font-size: 18px;
			}
		</style>

		<div style="<?= $wrapstyle; ?>">
<?php
		if (($show_direction !== 'horizontal') || ($retcount === 1)) {
?>
		<div id="show_page" style="float:left; width: <?= $showpagewidth ?>px; margin: 0 auto 0 auto">
<?php

		} else {
			$horiz_width = 15 + ($retcount * ($sectionwidth + 10)) + 15;
?>

			<div id="show_page"
			     style="background-color: #f0f8ff; padding:10px; border: 1px solid #ddd; float:left; width: <?= $showpagewidth ?>px; height: <?= $sectionheight + 20; ?>px; overflow: auto; white-space: nowarp; margin: 0 auto 0 auto">
				<div id='horiz_scroll' style="width: <?= $horiz_width ?>px">
<?php
		}

		if (!$login->getSpecialObject('sp_specialplay')->isOn('enable_ajax')) {
			$dragstring = "Enable Ajax to Drag";
		} else {
			$dragstring = "Drag";
		}

		$div_id_list = null;
		$completedivlist = null;

		$count = 1;

		foreach ($ret as $title => $a) {
			$count++;

			if (!isset($a[$title])) {
				continue;
			}

			$dispstring = "display:none";

			if ($a['open']) {
				$dispstring = "";
			}

			unset($a['open']);
			$nametitle = strfrom($title, "__title_");

			if (($count % 2 === 0) && ($show_direction === 'vertical 2')) {
?>
							<div id="XYZ" style="float:inherit">
<?php
			}
?>
						<div id="item_<?= $nametitle ?>" class="section">
							<table cellpadding='0' cellspacing='0'>
<?php
			if ($show_direction !== 'horizontal') {
?>
								<tr class='handle' id="handle_<?= $nametitle ?>"
								    style="background:#efe8e0 url(<?= $backgimage ?>)"
								    onMouseover="document.getElementById('font_<?= $nametitle ?>').style.visibility='visible'; this.style.background='#efe8e0 url(<?= $backgimage ?>)'"
								    onMouseout="document.getElementById('font_<?= $nametitle ?>').style.visibility='hidden'; this.style.background='#efe8e0 url(<?= $backgimage ?>)'">
<?php
			} else {
?>
								<tr class='handle' id="handle_<?= $nametitle ?>"
								    style="background:#efe8e0 url(<?= $backgimage ?>)">

<?php
			}
?>
									<td width='100%' style="cursor: move;" align='center'><span
											style='font-weight: bold' title='<?= $dragstring ?>'>&nbsp;<?= $a[$title] ?>
											&nbsp;</span></td>
<?php
		if (($show_direction !== 'horizontal') || ($retcount === 1)) {
?>
										<td class='handle' style='cursor: pointer'
										    onclick="blindUpOrDown('<?= $lclass ?>', '<?= $class ?>', '<?= $skindir ?>', '<?= $nametitle ?>')">
											<img id="img_<?= $nametitle ?>" name="img_<?= $nametitle ?>"
											     src="<?= $minus ?>"></td>
<?php
			} else {
?>
										<td class='handle'>&nbsp;</td>
<?php
			}
?>
								</tr>
							</table>

							<div style="<?= $dispstring ?>" id="internal_<?= $nametitle ?>">

<?php
			array_shift($a);
			$n = 0;

			foreach ($a as $k => $u) {
				$n++;

				$ret = $this->print_div_button($completedivlist, "block", true, $k, $u);
				$completedivlist[$ret] = $ret;
				$div_id_list[$nametitle][$ret] = $ret;
			}
?>

							</div>
						</div>
<?php
			if (($count % 2 !== 0) && ($show_direction === 'vertical 2')) {
?>
							</div>
<?php
			}
		}

		if (($show_direction !== 'horizontal') || ($retcount === 1)) {
?>
				</div>
			</div>
<?php
		} else {
?>
		</div>

		<script type='text/javascript' src='/theme/js/dragdivscroll.js'></script>

		<script type='text/javascript'>

			new DragDivScroll('show_page', 'mouseWheelX noStatus noXBarHide');

		</script>

<?php
		}
?>

		<script>
<?php
		$count = 0;
?>

			global_action_box = new Array();
<?php
		foreach ($div_id_list as $k => $v) {
?>

			global_action_box[<?=$count?>] = new Array();
			global_action_box[<?=$count?>][0] = '<?=$k?>';
<?php
			$j = 1;
			
			foreach ($v as $kk => $vv) {
?>

			global_action_box[<?=$count?>][<?=$j?>] = '<?=$vv?>';
<?php
				$j++;
			}
			
			$count++;
		}
?>
		</script>

<?php
	}

	function cginum($key)
	{
		return (isset($this->__http_vars[$key])) ? $this->__http_vars[$key] : 0;
	}

	function cgiset($key, $value)
	{
		// Needs to be Fixed.
		$this->__http_vars[$key] = $value;
	}

	function frmiset($key)
	{
		return (isset($this->__http_vars[$key])) ? 1 : 0;
	}

	function iset($key)
	{
		return (isset($this->__http_vars[$key])) ? 1 : 0;
	}


	function get_image($path, $class, $variable, $extension)
	{
		return add_http_host($this->get_image_without_host($path, $class, $variable, $extension));
	}

	function createMissingName($name)
	{
		global $gbl, $sgbl, $login;

		$val = 0;

		if ($sgbl->isKloxo()) {
			return '/theme/general/default/default.gif';
		}

		for ($i = 0; $i < strlen($name); $i++) {
			$val += ord($name[$i]);
		}

		$val = $val % 10;

		return "/theme/general/default/default_$val.gif";
	}

	function get_image_without_host($path, $class, $variable, $extension)
	{
		global $gbl, $sgbl, $login;

		$variable = strtolower($variable);
		$class = strtolower($class);

		$realv = $variable;

		//hack hack...
		if ($class === 'installapp') {
			if (strstr($variable, "addform")) {
				$variable = strfrom($variable, "_");
			}
		}

		if (csa($variable, "_nn_")) {
			$variable = substr($variable, 0, strpos($variable, '_nn_'));
		}

		$name = ($class) ? $class . "_" . $variable : $variable;

		$fpath = $path . "/" . $name . $extension;

		if (lfile_exists(getreal($fpath))) {
			return $fpath;
		}

		$name = $variable;

		$fnpath = $path . "/" . $name . $extension;

		if (lfile_exists(getreal($fnpath))) {
			return $fnpath;
		}

		$name = substr($variable, 0, strpos($variable, "_"));
		$fnpath = $path . "/" . $name . $extension;

		if (lfile_exists(getreal($fnpath))) {
			return $fnpath;
		}

		$name = substr($variable, strrpos($variable, "_") + 1);
		$fnpath = $path . "/" . $name . $extension;

		if (lfile_exists(getreal($fnpath))) {
			return $fnpath;
		}

		if ($realv === 'show') {
			return $this->get_image_without_host($path, $class, "list", $extension);
		}

		if (csb($realv, "update_")) {
			$qname = strfrom($realv, "update_");
			$qname = "updateform_$qname";

			return $this->get_image_without_host($path, $class, $qname, $extension);
		}

		$name = strfrom("{$class}_$variable", "all_");
		$fnpath = "$path/$name$extension";

		if (lfile_exists(getreal($fnpath))) {
			return $fnpath;
		}

		if ($sgbl->dbg < 0) {
			$imgname = $this->createMissingName($fpath);

			return $imgname;
		}

		return $fpath;
	}

	function save_non_existant_image($path)
	{
		global $gbl, $sgbl, $login;

		return; // [FIXME]

		// We need only the form images, and the normal non form action images need not be saved.
		if (!csa($path, "list") && !csa($path, "form")) {
			return;
		}

		if ($sgbl->dbg <= 1) {
			return;
		}

		if (lfile_exists(getreal($path))) {
			return;
		}

		$cont = null;

		$icon = $login->getSpecialObject('sp_specialplay')->icon_name;

		$file = "__path_program_htmlbase/$icon.missing_image.txt";

		if (lfile_exists($file)) {
			$cont = lfile($file);
			foreach ($cont as $k => &$__c) {
				$__c = trim($__c);
				if (!$__c) {
					unset($cont[$k]);
				}
			}
		}

		$cont = array_push_unique($cont, $path);
		$cont = implode("\n", $cont);
		$cont .= "\n";

		lfile_put_contents($file, $cont);
	}

	function get_date()
	{
		return array(date('d'), date('m'), date('Y'));
	}

	function get_post_from_get($url, &$path, &$post)
	{
		$post = null;
		$array = parse_url($url);
		$path = '';

		if (isset($array['host'])) {
			$path .= $array['scheme'] . '://' . $array['host'];
		}

		if (isset($array['port'])) {
			$path .= ':' . $array['port'];
		}

		if (isset($array['path'])) {
			$path .= $array['path'];
		}

		if (isset($array['query'])) {
			parse_str($array['query'], $post);
		}

		return $post;
	}

	function createCurrentParam($class)
	{
		$param = null;

		foreach ($this->__http_vars as $key => $val) {
			if (csb($key, "__m_")) {
				$param[$key] = $val;
				continue;
			}

			if (!csa($key, "_c_")) {
				continue;
			}

			$realname = substr($key, strlen('frm_'));
			$this->get_htmlvar_details($key, $newclass, $variable, $extra, $htmlvalue);

			$param[$variable] = $val;
		}

		check_for_select_one($param);

		return $param;
	}

	function get_form_variable_name($descr)
	{
		return getNthToken($descr, 1);
	}

	function fix_stuff_or_class($stuff, $variable, &$class, &$value)
	{
		$value = null;

		if (is_object($stuff)) {
			$class = lget_class($stuff);
			lxclass::resolve_class_differences($class, $variable, $dclass, $dvariable);

			if ($dclass != $class && cse($dclass, "_b")) {
				$value = $stuff->$dclass->$dvariable;
			} else {
				if ($stuff->isQuotaVariable($variable)) {
					$value = $stuff->priv->$variable;
				} elseif ($stuff->isListQuotaVariable($variable)) {
					$value = $stuff->listpriv->$variable;
				} elseif (!cse($variable, "_f")) {
					$value = $stuff->getVariable($variable);
				}
			}
		} else {
			$class = $stuff;
		}

		if (!is_array($value)) {
			$value = htmlspecialchars($value);
		}
	}

	function print_file_permissions($ffile)
	{
		global $gbl, $sgbl, $login;

		$ffile->getPermissions($perm);

		if ($perm[0] === '') {
			$user = 0;
		} else {
			$user = $perm[0];
		}

		if ($perm[1] === '') {
			$group = 0;
		} else {
			$group = $perm[1];
		}

		if ($perm[2] === '') {
			$other = 0;
		} else {
			$other = $perm[2];
		}

		$imgheadleft = $login->getSkinDir() . '/images/top_lt.gif';
		$imgheadright = $login->getSkinDir() . '/images/top_rt.gif';
		$imgheadbg = $login->getSkinDir() . 'top_bg.gif';
		$imgtopline = $login->getSkinDir() . '/images/top_line.gif';
		$tablerow_head = $login->getSkinDir() . '/images/tablerow_head.gif';
?>

		<script>
			function sendchmode(a, b) {
				b.frm_ffile_c_file_permission_f.value = a.user.value + a.group.value + a.other.value;
				if (a.frm_ffile_c_recursive_f.checked) {
					if (confirm("Do You Really want to set this permission Recursively?")) /* [FIXME] Harcode string translate */
					{
						b.frm_ffile_c_recursive_f.value = 'on';
					} else {
						b.frm_ffile_c_recursive_f.value = 'off';
					}
				} else {
					b.frm_ffile_c_recursive_f.value = 'off';
				}
				b.submit();
			}
		</script>

		<form name="frmsendchmod" action="/display.php" accept-charset="utf-8">
			<input type="hidden" name="frm_ffile_c_file_permission_f">
<?php
		$post['frm_o_o'] = $this->__http_vars['frm_o_o'];
		$this->print_input_vars($post);
?>
			<input type="hidden" name="frm_ffile_c_recursive_f" value="Off">
			<input type="hidden" name="frm_action" value="update">
			<input type="hidden" name="frm_subaction" value="perm">
		</form>

		<table cellpadding="0" cellspacing="0" border="0" width="325">
			<tr>
				<td width="60%" valign="bottom">
					<table cellpadding="0" cellspacing="0" border="0" width="100%">
						<tr>
							<td width="100%" height="2" background="<?= $imgtopline ?>"></td>
						</tr>
					</table>
				</td>
				<td align="right">
					<table cellpadding="0" cellspacing="0" border="0" width="100%">
						<tr>
							<td>
								<img src="<?= $imgheadleft ?>">
							</td>
							<td nowrap width="100%" background="<?= $imgheadbg ?>">
								<b><span style="color:"#ffffff">Change Permissions</span>
								</b><? // [FIXME] Harcode translation string?>
							</td>
							<td>
								<img src="<?= $imgheadright ?>">
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>

		<form name="chmod" method=<?= $sgbl->method ?> action="" accept-charset="utf-8">
			<table cellpadding="0" cellspacing="0" border="0" width="325">
				<tr style="background:#efe8e0 url(<?= $tablerow_head ?>)">
					<td width="100" class="col"></td>
					<td width=75 align=center>User</td><? // [FIXME] Harcode translation string?>
					<td width=75 align=center>Group</td><? // [FIXME] Harcode translation string?>
					<td align=center width=75>Others</td><? // [FIXME] Harcode translation string?>
				</tr>
				<tr style="background:#efe8e0 url(<?= $tablerow_head ?>)">
					<td width=100 class="col"></td>
					<td align="center">
						<input type="checkbox" name="userall" onclick="allrights(document.chmod,this,'user');">
					</td>
					<td align="center">
						<input type="checkbox" name="groupall" onclick="allrights(document.chmod,this,'group');">
					</td>
					<td align="center">
						<input type="checkbox" name="otherall" onclick="allrights(document.chmod,this,'other');">
					</td>
				</tr>
			</table>
			<table cellpadding="0" cellspacing="0" border="0" width="325">
				<tr class="tablerow0">
					<td class="col" width="100">Write</td><? // [FIXME] Harcode translation string?>
					<td align="center">
						<input type="checkbox" name="wu" onclick="changerights(document.chmod,this,'user',2);">
					</td>
					<td align="center">
						<input type="checkbox" name="wg" onclick="changerights(document.chmod,this,'group',2);">
					</td>
					<td align="center">
						<input type="checkbox" name="wo" onclick="changerights(document.chmod,this,'other',2);">
					</td>
				</tr>
				<tr class="tablerow1">
					<td class="col" width="100">Execute</td><? // [FIXME] Harcode translation string?>
					<td width="75" align="center">
						<input type="checkbox" name="eu" onclick="changerights(document.chmod,this,'user',1);">
					</td>
					<td width="75" align="center">
						<input type="checkbox" name="eg" onclick="changerights(document.chmod,this,'group',1);">
					</td>
					<td width="75" align="center">
						<input type="checkbox" name="eo" onclick="changerights(document.chmod,this,'other',1);">
					</td>
				</tr>
				<tr class="tablerow0">
					<td class="col" width="100">Read</td><? // [FIXME] Harcode translation string?>
					<td align="center">
						<input type="checkbox" name="ru" onclick="changerights(document.chmod,this,'user',4);">
					</td>
					<td align="center">
						<input type="checkbox" name="rg" onclick="changerights(document.chmod,this,'group',4);">
					</td>
					<td align="center">
						<input type="checkbox" name="ro" onclick="changerights(document.chmod,this,'other',4);">
					</td>
				</tr>
			</table>
			<table cellpadding="0" cellspacing="0" border="0" width="325">
				<tr>
					<td colspan="4" bgcolor="#ffffff" height="2"></td>
				</tr>
				<tr class="tablerow1">
					<td class="tableheadtext" width="100">&nbsp;&nbsp;Total
					</td> <? // [FIXME] Harcode translation string?>
					<td align="center" width="75">
						<input type="text" size="1" name="user" class="textchmoddisable" value="<?= $user ?>">
					</td>
					<td width="75" align="center">
						<input type="text" size="1" name="group" class="textchmoddisable" value="<?= $group ?>">
					</td>
					<td width="75" align="center">
						<input type="text" size="1" name="other" class="textchmoddisable" value="<?= $other ?>">
					</td>
				</tr>
				<tr>
					<td colspan="3">&nbsp; <b>Change Permssion
							Recursively</b> <? // [FIXME] Harcode translation string?>
					</td>
					<td>
						<input type="checkbox" name="frm_ffile_c_recursive_f">
					</td>
				</tr>

				<tr>
					<td colspan="4" bgcolor="#ffffff" height="4"></td>
				</tr>
				<tr>
					<td colspan="4" align="right">
						<input type="button" onclick="sendchmode(document.chmod,document.frmsendchmod)"
						       class="submitbutton" name="change" value="&nbsp;&nbsp;Change&nbsp;&nbsp;">
					</td>
				</tr>
				<tr>
					<td colspan="2" bgcolor="#ffffff" height="4"></td>
				</tr>
				<tr>
					<td colspan="4" style="background:#efe8e0 url(<?= $imgtopline ?>)" height="1"></td>
				</tr>
			</table>
		</form>

		<script>
			document.chmod.user.disabled = true;
			document.chmod.group.disabled = true;
			document.chmod.other.disabled = true;

			setpermission(document.chmod, 'user', <?=$user;?>);
			setpermission(document.chmod, 'group', <?=$group?>);
			setpermission(document.chmod, 'other', <?=$other?>);
		</script>

<?php
	}

	function object_variable_file($stuff, $variable)
	{
		global $gbl, $sgbl, $login, $ghtml;

		$sgbl->method = 'post';

		$this->fix_stuff_or_class($stuff, $variable, $class, $value);
		$descr = $this->get_classvar_description_after_overload($class, $variable);

		$rv = new FormVar();
		$rv->name = $variable;
		$rv->desc = $descr[2];
		$rv->type = 'file';

		return $rv;
	}

	function object_variable_fileselect($stuff, $variable, $opt = null)
	{
		$valstring = null;

		$this->fix_stuff_or_class($stuff, $variable, $class, $value);

		$rvr = new formVar();

		if ($value) {
			$rvr->value = $value;
		}

		$needstr = null;
		$descr = $this->get_classvar_description_after_overload($class, $variable);

		$desc = getNthToken($descr[2], 1);
		if (char_search_a($descr[0], 'n')) {
			$rvr->need = 'yes';
		}

		$estring = null;
		if ($opt) {
			foreach ($opt as $key => $val) $rvr->$key = $val;
		}

		$rvr->name = "frm_{$class}_c_$variable";
		$rvr->desc = $desc;
		$rvr->type = 'fileselect';

		return $rvr;
	}

	function object_variable_image($stuff, $variable, $opt = null)
	{
		$valstring = null;

		$this->fix_stuff_or_class($stuff, $variable, $class, $value);

		$needstr = null;
		$descr = $this->get_classvar_description_after_overload($class, $variable);

		$desc = getNthToken($descr[2], 1);

		$rvr = new formVar();
		$estring = null;

		if ($opt) {
			foreach ($opt as $key => $val) $rvr->$key = $val;
		}

		$rvr->name = "frm_{$class}_c_$variable";
		$rvr->desc = $desc;
		$rvr->type = 'image';

		return $rvr;
	}

	function url_encode($value) // [FIXME] Remove this useless function and rewrite the code
	{
		return urlencode($value);
	}

	function object_variable_modify($stuff, $variable, $opt = null)
	{
		$valstring = null;

		$this->fix_stuff_or_class($stuff, $variable, $class, $value);

		$rvr = new FormVar();

		if ($value) {
			$rvr->value = $value;
		}

		$needstr = null;
		$descr = $this->get_classvar_description_after_overload($class, $variable);

		$desc = getNthToken($descr[2], 1);
		if (char_search_a($descr[0], 'n')) {
			$rvr->need = 'yes';
		}

		$estring = null;

		if ($opt) {
			foreach ($opt as $key => $value) if ($key === 'postvar') {
				$postvar = new FormVar();
				$postvar->option = $value['val'][1];
				$postvar->name = "frm_{$class}_c_{$value['var']}";
				$postvar->type = 'select';
				$rvr->postvar = $postvar;
			} else {
				$rvr->$key = $value;
			}
		}

		$rvr->name = "frm_{$class}_c_$variable";
		$rvr->desc = $desc;
		$rvr->type = 'modify';

		return $rvr;
	}

	function object_variable_show_select($stuff, $variable, $list)
	{
		$value = null;

		$rvr = new FormVar();
		$rvr->name = $variable;
		$rvr->desc = 'Show';
		$rvr->type = 'select';

		$rvr->option = $this->object_variable_option(false, $list, $value, true);

		return $rvr;
	}

	function is_special_url($stuff)
	{
		return is_object($stuff);
	}

	function is_special_variable($stuff)
	{
		return is_object($stuff);
	}

	function object_variable_select($stuff, $variable, $list, $assoc = false)
	{
		$value = null;

		$this->fix_stuff_or_class($stuff, $variable, $class, $value);

		if (!is_object($stuff)) {
			$flist = $assoc ? array_keys($list) : $list;
			$value = getFirstFromList($flist);
		}

		if ($this->is_special_variable($list)) {
			$descr = $list->descr;
			$list = $list->list;
		} else {
			$descr = $this->get_classvar_description_after_overload($class, $variable);
		}

		$desc = $this->get_form_variable_name($descr[2]);
		$string = $this->do_object_variable_select($class, $variable, $desc, $list, $value, $assoc);

		return $string;
	}

	function do_object_variable_select($class, $variable, $desc, $list, $value, $assoc = false)
	{
		$rvr = new FormVar();
		$rvr->name = "frm_{$class}_c_$variable";
		$rvr->desc = $desc;
		$rvr->type = 'select';

		$rvr->option = $this->object_variable_option(false, $list, $value, $assoc);

		return $rvr;
	}

	function object_variable_multiselect($stuff, $variable, $list)
	{
		$value = null;

		$this->fix_stuff_or_class($stuff, $variable, $class, $value);


		$descr = $this->get_classvar_description_after_overload($class, $variable);

		$desc = $this->get_form_variable_name($descr[2]);

		$string = $this->do_object_variable_multiselect($class, $variable, $desc, $list, $value);

		return $string;
	}

	function do_object_variable_multiselect($class, $variable, $desc, $list, $value)
	{
		$ret = new FormVar();
		$ret->name = "frm_{$class}_c_$variable";
		$ret->desc = $desc;
		$ret->type = 'multiselect';

		$rvr = new FormVar();
		$rvr->name = "frm_{$class}_a_$variable";
		$rvr->option = $this->object_variable_option(true, $list);
		$ret->variable1 = $rvr;

		$rvr = new FormVar();
		$rvr->name = "frm_{$class}_b_$variable";
		$rvr->option = $this->object_variable_option(true, $value);
		$ret->variable2 = $rvr;

		return $ret;
	}

	function object_variable_nomodify($stuff, $variable, $value = null)
	{
		$this->fix_stuff_or_class($stuff, $variable, $class, $svalue);

		if ($value === null) {
			$value = $svalue;
		}

		if ($this->is_special_variable($value)) {
			$descr = $value->descr;
			$value = $value->value;
		} else {
			$descr = $this->get_classvar_description_after_overload($class, $variable);
		}

		$desc = $descr[2];

		if (is_array($value)) {
			$value = implode('\n', $value);
		}

		$rvr = new FormVar();
		$rvr->name = "frm_{$class}_c_$variable";
		$rvr->desc = $desc;
		$rvr->type = 'nomodify';
		$rvr->value = $value;

		return $rvr;
	}

	function xml_variable_endblock()
	{
		return ' </block> </start>';
	}

	function object_variable_button($name)
	{
		$name = ucfirst($name);
		$rvr = new FormVar();
		$rvr->type = 'button';
		$rvr->name = 'frm_change';
		$rvr->value = $name;

		return $rvr;
	}

	function object_variable_check($stuff, $variable, $def = null)
	{
		$value = null;

		$this->fix_stuff_or_class($stuff, $variable, $class, $value);

		//Hack Hack... Handling used separately...
		if (csb($variable, 'used_s_')) {
			$nclass = $class;
			$nvariable = strfrom($variable, 'used_s_');
			$value = $stuff->used->$nvariable;
		} else {
			$nvariable = $variable;
			$nclass = $class;
		}

		if ($value === 'on') {
			$value = 'yes';
		}

		$descr = $this->get_classvar_description_after_overload($nclass, $nvariable);

		$rvr = new FormVar();
		$rvr->name = "frm_{$class}_c_{$variable}_aaa_checkname";
		$rvr->desc = $descr[2];
		$rvr->type = 'hidden';
		$rvr->value = 'off';
		$ret[] = $rvr;

		$rvr = new FormVar();
		$rvr->name = "frm_{$class}_c_{$variable}_aaa_checked";
		$rvr->desc = $descr[2];
		$rvr->type = 'checkbox';
		$rvr->checked = $value;
		$rvr->value = 'on';
		$ret[] = $rvr;

		return $ret;
	}

	function object_variable_hidden($key, $value)
	{
		$string = null;

		if (is_array($value)) {
			foreach ($value as $k => $v) {
				$str = "{$key}" . "[$k]";
				$rvr = new FormVar();
				$rvr->name = $str;
				$rvr->value = $v;
				$rvr->type = 'hidden';
				$ret[] = $rvr;
			}
		} else {
			$rvr = new FormVar();
			$rvr->name = $key;
			$rvr->value = $value;
			$rvr->type = 'hidden';
			$ret[] = $rvr;
		}

		return $ret;
	}

	function object_variable_hiddenlist($hlist)
	{
		foreach ($hlist as $key => $val) $a[] = $this->object_variable_hidden($key, $val);

		return lx_array_merge($a);
	}

	function object_variable_htmltextarea($stuff, $variable, $value = null, $nonameflag = false)
	{
		$this->fix_stuff_or_class($stuff, $variable, $class, $nvalue);
		$name = "frm_{$class}_c_{$variable}";

		if (!$value) {
			$value = $nvalue;
		}

		if ($nonameflag) {
			$name = null;
		}

		$descr = $this->get_classvar_description_after_overload($class, $variable);
		$val = exec_class_method($class, 'getTextAreaProperties', $variable);

		$rvr->name = $name;
		$rvr->desc = $descr[2];
		$rvr->height = $val['height'];
		$rvr->width = $val['width'];
		$rvr->value = $value;
		$rvr->type = 'htmltextarea';

		return $rvr;
	}

	function object_variable_textarea($stuff, $variable, $value = null, $nonameflag = false)
	{
		$this->fix_stuff_or_class($stuff, $variable, $class, $nvalue);
		$name = "frm_{$class}_c_{$variable}";

		if (!$value) {
			$value = $nvalue;
		}
		if ($nonameflag) {
			$name = null;
		}

		$descr = $this->get_classvar_description_after_overload($class, $variable);
		$val = exec_class_method($class, 'getTextAreaProperties', $variable);

		$rvr = new FormVar();
		$rvr->name = $name;
		$rvr->desc = $descr[2];
		$rvr->height = $val['height'];
		$rvr->width = $val['width'];
		$rvr->value = $value;
		$rvr->type = 'textarea';

		return $rvr;
	}

	function object_variable_command($type, $desc)
	{
		$rvr = new FormVar();
		$rvr->type = $type;
		$rvr->desc = $desc;

		return $rvr;
	}

	function object_inherit_filter()
	{
		return null; // Don't inherit hpfilter.
	}

	function object_inherit_accountselect()
	{
		return $this->object_variable_inherit('frm_accountselect');
	}

	function object_inherit_classpath()
	{
		$a1 = $this->object_variable_inherit('frm_o_o');
		$a2 = $this->object_variable_inherit('frm_consumedlogin');

		return lx_merge_good($a1, $a2);
	}

	function html_variable_inherit($var = null)
	{
		$string = null;

		foreach ($this->__http_vars as $key => $value) {
			if ($var && $var != $key) {
				continue;
			} elseif (!char_search_a($key, "_c_")) {
				continue;
			}

			if (is_array($value)) {
				foreach ($value as $k => $v) {
					if (is_array($v)) {
						foreach ($v as $nk => $nv) {
							$str = "{$key}" . "[$k][$nk]";
?>

							<input type="hidden" name="<?= $str ?>" value="<?= $nv ?>">
<?php
						}
					} else {
						$str = "{$key}" . "[$k]";
?>

						<input type="hidden" name="<?= $str ?>" value="<?= $v ?>">
<?php
					}
				}
			} else {
				if (!$value) {
					continue;
				}
?>

				<input type="hidden" name="<?= $str ?>" value="<?= $value ?>">
<?php
			}
		}

		return $string;
	}

	function object_variable_inherit($var = null)
	{
		$ret = null;

		foreach ($this->__http_vars as $key => $value) {
			if ($var) {
				if ($var != $key) {
					continue;
				}
			} else {
				if (!char_search_a($key, "_c_")) {
					continue;
				}
			}

			if (is_array($value)) {
				foreach ($value as $k => $v) {
					if (is_array($v)) {
						foreach ($v as $nk => $nv) {
							$rvr = new FormVar();
							$str = "{$key}" . "[$k][$nk]";
							$rvr->name = $str;
							$rvr->value = $nv;
							$rvr->type = "hidden";

							$ret[] = $rvr;
						}

					} else {
						$rvr = new FormVar();
						$str = "{$key}" . "[$k]";
						$rvr->name = $str;
						$rvr->value = $v;
						$rvr->type = "hidden";

						$ret[] = $rvr;
					}
				}
			} else {

				if (!$value) {
					continue;
				}

				$rvr = new FormVar();
				$rvr->name = $key;
				$rvr->value = $value;
				$rvr->type = "hidden";
				$ret[] = $rvr;
			}

		}

		return $ret;
	}

	function object_variable_listquota($parent, $stuff, $variable, $list = null)
	{
		global $gbl, $sgbl, $ghtml;

		$value = null;

		$this->fix_stuff_or_class($stuff, $variable, $class, $value);

		$descr = $this->get_classvar_description_after_overload($class, $variable);
		$desc = $this->get_form_variable_name($descr[2]);

		$cvar = $variable;

		$listvariable = "listpriv_s_" . $variable;

		if (cse($cvar, "_sing")) {
			$realvar = strtil($cvar, "_sing");
			$listvar = $realvar . "_list";
			if (!$list) {
				$list = $parent->listpriv->$listvar;
			}
			$string = $this->do_object_variable_select($class, $listvariable, $desc, $list, $value);
		} else {
			$listvar = $cvar;
			if (!$list) {
				$list = $parent->listpriv->$listvar;
			}
			$string = $this->do_object_variable_multiselect($class, $listvariable, $desc, $list, $value);
		}

		return $string;
	}

	function object_variable_quota($parent, $stuff, $variable)
	{
		global $gbl, $sgbl, $ghtml;

		$parent = $parent->getClientParentO();

		$value = null;

		$this->fix_stuff_or_class($stuff, $variable, $class, $value);

		$descr = $this->get_classvar_description_after_overload($class, $variable);

		$cvar = $variable;

		if ($value === 'Unlimited') {
			$value = null;
		}

		$check = (trim($value) !== "") ? 'no' : 'yes';

		if (is_object($stuff)) {
			if (isOn($value)) {
				$chval = 'yes';
			} else {
				$chval = 'no';
			}
		} else {
			$cl = get_name_without_template($stuff);
			if (isOn(exec_class_method($cl, "getDefaultValue", $variable))) {
				$chval = 'yes';
			} else {
				$chval = "no";
			}
		}

		if (cse($variable, "_flag")) {
			$rvr = new FormVar();
			$rvr->name = "frm_{$class}_c_priv_s_{$variable}_aaa_checkname";
			$rvr->desc = $descr[2];
			$rvr->type = "hidden";
			$rvr->value = "off";
			$ret[] = $rvr;

			if ($parent->priv->isOn($variable)) {
				$rvr = new FormVar();
				$rvr->name = "frm_{$class}_c_priv_s_{$variable}_aaa_checked";
				$rvr->desc = $descr[2];
				$rvr->type = "checkbox";
				$rvr->checked = $chval;
				$rvr->value = "on";
				$ret[] = $rvr;
			} else {
				$rvr = new FormVar();
				$rvr->name = "frm_{$class}_c_priv_s_{$variable}_aaa_checked";
				$rvr->desc = $descr[2];
				$rvr->type = "checkbox";
				$rvr->checked = "disabled";
				$rvr->value = "off";
				$ret[] = $rvr;
			}

			return $ret;
		}

		if (is_unlimited($parent->priv->$cvar)) {
			$rvr = new FormVar();
			$rvr->name = "frm_{$class}_c_$variable";
			$rvr->type = "checkboxwithtext";
			$rvr->desc = $descr[2];
			$rvr->mode = "or";

			$text = new FormVar();
			$text->name = "frm_{$class}_c_priv_s_{$variable}_aaa_quotaname";
			$text->value = $value;
			$rvr->text = $text;

			$checkbox = new FormVar();
			$checkbox->desc = "Unlimited";
			$checkbox->name = "frm_{$class}_c_priv_s_{$variable}_aaa_unlimited";
			$checkbox->checked = $check;
			$checkbox->value = "yes";
			$rvr->checkbox = $checkbox;
			$ret[] = $rvr;

			$rvr = new FormVar();
			$rvr->type = "hidden";
			$rvr->value = "Unlimited";
			$rvr->name = "frm_{$class}_c_priv_s_{$variable}_aaa_quotamax";
			$ret[] = $rvr;
		} else {
			$quotaleft = $parent->getEffectivePriv($cvar, $class);

			if (isHardQuotaVariableInClass($class, $cvar)) {
				$quotaleft += $value;
			}

			$totalstring = null;
			$totalstring = "Total: " . $parent->priv->$cvar;

			if (cse($class, "template")) {
				$totalstring = null;
				$quotaleft = $parent->priv->$cvar;
			}

			if ($value === "") {
				$value = $quotaleft;
			}

			$rvr = new FormVar();
			$rvr->type = "modify";
			$rvr->texttype = "text";
			$rvr->value = $value;
			$rvr->desc = $descr[2];
			$rvr->name = "frm_{$class}_c_priv_s_{$variable}_aaa_quotaname";
			$rvr->posttext = "Max $quotaleft $totalstring";
			$rvr->format = "integer";
			$ret[] = $rvr;

			$rvr = new FormVar();
			$rvr->type = "hidden";
			$rvr->value = $quotaleft;
			$rvr->name = "frm_{$class}_c_priv_s_{$variable}_aaa_quotamax";
			$ret[] = $rvr;

		}

		return $ret;
	}

	function object_variable_startblock($obj, $class, $title, $url = null)
	{
		if (!$url) {
			$url = $_SERVER['PHP_SELF'];
		}

		if (!$class) {
			$class = get_class($obj);
		}

		$domdesc = get_classvar_description($class);
		$server = $this->print_machine($obj);

		$formname = fix_nname_to_be_variable("$title{$obj->getId()}");

		$header = new FormVar();
		$header->form = $formname;
		$header->formtype = "enctype=\"multipart/form-data\"";

		if ($title) {
			$header->title = "$title for {$obj->getId()} $server";
		} else {
			$header->title = null;
		}

		$header->url = $url;

		return $header;
	}

	function object_variable_oldpassword($class, $var, $descr)
	{
		$rvr = new FormVar();
		$rvr->name = "frm_{$class}_c_{$var}";
		$rvr->desc = $descr[2];
		$rvr->texttype = "password";
		$rvr->valid = "yes";
		$rvr->type = "modify";
		$rvr->need = "yes";

		return $rvr;
	}

	function object_variable_password($class, $var)
	{
		$desc = get_classvar_description($class, $var);

		$rvr = new FormVar();
		$rvr->name = "frm_{$class}_c_{$var}";
		$rvr->desc = $desc[2];
		$rvr->texttype = "password";
		$rvr->confirm_password = true;
		$rvr->valid = "yes";
		$rvr->type = "modify";
		$rvr->need = "yes";
		$ret[] = $rvr;

		$rvr = new FormVar();
		$rvr->name = "frm_confirm_password";
		$rvr->desc = "Confirm Password";
		$rvr->texttype = "password";
		$rvr->type = "modify";
		$rvr->valid = "yes";
		$rvr->need = "yes";
		$rvr->match = "frm_{$class}_c_{$var}";
		$rvr->matchdesc = $desc[2];
		$ret[] = $rvr;

		return $ret;
	}

	function object_variable_option($multi, $list, $select = null, $assoc = null)
	{
		$string = null;
		$sel = null;

		if (!$list) {
			return null;
		}

		$match = false;

		foreach ((array)$list as $k => $l) {
			$value = ($assoc) ? $k : $l;

			if ($l === '--Disabled--') {
				$match = true;
			}

			if ($select !== "" && "$value" === "$select") {
				$match = true;
				$option["__v_selected_$value"] = $l;
			} else {
				$option[$value] = $l;
			}
		}

		// IF the select is nonnull and the the damn thing doesn't match, then there is some problem.
		// That is the current value isn't in the list of acceptable values.
		if (!$match && !$multi) {
			if ($select) {
				$sel['--Select One--'] = "--Select One ($select not in List)--";
			} else {
				$sel['--Select One--'] = '--Select One--';
			}
		}

		if ($sel) {
			$option = $sel + $option;
		}

		return $option;
	}

	function isSelectOne($var)
	{
		return ($var === '--Select One--');
	}

	function print_current_input_vars($ignore)
	{
		$this->print_input_vars($this->__http_vars, $ignore);
	}

	function print_current_input_var_unset_filter($key1, $arr)
	{
		if (!isset($this->__http_vars['frm_hpfilter'])) {
			return;
		}

		$post['frm_hpfilter'] = $this->__http_vars['frm_hpfilter'];

		foreach ($arr as $key2) {
			if (isset($post['frm_hpfilter'][$key1][$key2])) {
				unset($post['frm_hpfilter'][$key1][$key2]);
			}
		}

		$this->print_input_vars($post);
	}

	function print_input_vars($post, $ignore = array())
	{
		foreach ((array)$post as $key => $value) {
			if (array_search_bool($key, $ignore)) {
				continue;
			}

			if (is_array($value)) {
				foreach ($value as $k => $v) {
					if (is_array($v)) {
						foreach ($v as $nk => $nv) {
?>
							<input type="hidden" name="<?= $key ?>[<?= $k ?>][<?= $nk ?>]" value="<?= $nv ?>">
<?php
						}

					} else {
?>
						<input type="hidden" name="<?= $key ?>[<?= $k ?>]" value="<?= $v ?>">
<?php
					}
				}
			} else {
?>
				<input type="hidden" name="<?= $key ?>" value="<?= $value ?>">
<?php
			}
		}
	}

	function get_get_from_post($ignore, $list)
	{
		$string = "";

		if (!$list) {
			return $string;
		}

		foreach ($list as $key => $value) {
			if ($ignore && array_search_bool($key, $ignore)) {
				continue;
			}
			
			if (is_array($value)) {
				foreach ($value as $k => $v) {
					if (is_array($v)) {
						foreach ($v as $nk => $nv) {
							$string .= $key . "[" . $k . "]" . "[" . $nk . "]=" . $nv . "&";
						}
					} else {
						$string .= $key . "[" . $k . "]=" . $v . "&";
					}
				}
			} else {
				$string .= "$key=$value&";
			}

		}

		$string = preg_replace("/&$/", "", $string);

		return $string;
	}

	function get_get_from_current_post($ignore)
	{
		return $this->get_get_from_post($ignore, $this->__http_vars);
	}

	function get_classvar_description_after_overload($class, $property)
	{
		global $gbl, $sgbl, $login;

		lxclass::resolve_class_differences($class, $property, $dclass, $dproperty);

		$classdesc = get_classvar_description($dclass);
		$prop_descr = get_classvar_description($dclass, $dproperty);
		$this->fix_variable_overload($prop_descr, $classdesc[2]);
		$prop_descr[2] = getNthToken($prop_descr[2], 1);

		return $prop_descr;
	}

	function fix_variable_overload(&$descr, $classdesc)
	{
		foreach ($descr as &$d) if (strstr($d, "[%v]") !== false) {
			$d = str_replace("[%v]", $classdesc, $d);
		}
	}

	function generateParentListUrl($nobject)
	{
		$object = clone $nobject;
		$object->__parent_o = null;
		$parent = $object->getParentO();

		if (!$parent) {
			log_log("parent_state", "object {$object->getClName()} has no parent...");

			return null;
		}

		$plist[] = $parent;

		while (is_object($parent) && !$parent->isAdmin()) {
			$parent = $parent->getParentO();
			$plist[] = $parent;
		}

		$plist = array_reverse($plist);
		$i = 0;
		$string = null;
		$str = null;

		foreach ($plist as $p) {
			if (!$p || $p->isAdmin()) {
				continue;
			}

			if ($p->getParentO() && $p->getParentO()->isSingleObject($p->getClass())) {
				$string[] = "frm_o_o[$i][class]={$p->get__table()}";
			} else {
				$string[] = "frm_o_o[$i][class]={$p->get__table()}&frm_o_o[$i][nname]={$p->nname}";
			}

			$i++;
		}

		$class = strfrom($object->getClass(), "all_");

		if ($string) {
			$str = implode("&", $string);
		}

		return "?frm_action=list&frm_o_cname=$class&$str";
	}

	function generateEntireUrl($nobject, $top)
	{
		$object = clone $nobject;
		$object->__parent_o = null;
		$parent = $object->getParentO();

		if (!$parent) {
			log_log("parent_state", "object {$object->getClName()} has no parent...");

			return null;
		}

		$plist[] = $parent;

		while (!$parent->hasSameId($top)) {
			$parent = $parent->getParentO();

			if (!$parent) {
				log_log("parent_state", "object {$object->getClName()} has no parent...");

				return null;
			}
			$plist[] = $parent;
		}

		$plist = array_reverse($plist);
		$i = 0;

		foreach ($plist as $p) {
			if ($p->hasSameId($top)) {
				continue;
			}

			if ($p->getParentO() && $p->getParentO()->isSingleObject($p->getClass())) {
				$string[] = "frm_o_o[$i][class]={$p->get__table()}";
			} else {
				$string[] = "frm_o_o[$i][class]={$p->get__table()}&frm_o_o[$i][nname]={$p->nname}";
			}

			$i++;
		}

		$class = strfrom($object->getClass(), "all_");
		$string[] = "frm_o_o[$i][class]=$class&frm_o_o[$i][nname]={$object->nname}";

		$str = implode("&", $string);

		return "?frm_action=show&$str";
	}

	function getFullUrl($url, $p = "default")
	{
		if (is_array($url) || $this->is_special_url($url) || csb($url, "?") || csa($url, "display.php")) {
			return $url;
		}

		if ($p === "default") {
			$p = $this->frm_o_o;
		}

		$np = array();

		$url = "display.php?" . $url;
		$this->get_post_from_get($url, $path, $post);

		$k = 0;
		$k = count($p);

		if (isset($post['goback'])) {
			for ($i = 0; $i < $post['goback']; $i++) {
				unset($p[--$k]);
			}
		}

		if (isset($post['j'])) {
			$desc = get_classvar_description($post['j']['class']);

			if (csa($desc[0], "N")) {
				if ($p[$k - 1]['class'] === $post['j']['class']) {
					$k--;
				}
			}

			$p[$k]['class'] = $post['j']['class'];
			$p[$k]['nname'] = $post['j']['nname'];

			$k++;
		}

		if (isset($post['n'])) {
			$obj = $post['n'];

			if (csa($obj, "_s_")) {
				$l = explode("_s_", $obj);
				foreach ($l as $o) {
					$p[$k++]['class'] = $o;
				}
			} else {
				$p[$k++]['class'] = $post['n'];
			}
		}

		// Ka has to come AFTER n. Otherwise it won't work in the getshowalist, especially for web/installapp combo.
		if (isset($post['k'])) {
			$desc = get_classvar_description($post['k']['class']);

			if (csa($desc[0], "N")) {
				if ($p[$k - 1]['class'] === $post['k']['class']) {
					$k--;
				}
			}

			$p[$k]['class'] = $post['k']['class'];
			$p[$k]['nname'] = $post['k']['nname'];
			$k++;
		}

		if (isset($post['o'])) {
			$obj = $post['o'];

			if (csa($obj, "_s_")) {
				$l = explode("_s_", $obj);
				foreach ($l as $o) {
					$p[$k++]['class'] = $o;
				}
			} else {
				$p[$k++]['class'] = $post['o'];
			}
		}

		if (isset($post['l'])) {
			$desc = get_classvar_description($post['l']['class']);

			if (csa($desc[0], "N")) {
				if ($p[$k - 1]['class'] === $post['l']['class']) {
					$k--;
				}
			}

			$p[$k]['class'] = $post['l']['class'];
			$p[$k]['nname'] = $post['l']['nname'];
		}

		$npost['frm_action'] = $post['a'];

		if (isset($post['sa'])) {
			$npost['frm_subaction'] = $post['sa'];
		}

		if (isset($post['dta'])) {
			$npost['frm_dttype']['var'] = $post['dta']['var'];
			$npost['frm_dttype']['val'] = $post['dta']['val'];
		}

		foreach ((array)$post as $k => $v) {
			if (csa($k, "_c_")) {
				$npost[$k] = $v;
			}
		}

		if ($p) {
			$npost['frm_o_o'] = $p;
		}

		if (isset($post['c'])) {
			$npost['frm_o_cname'] = $post['c'];
		}

		if (isset($post['frm_filter'])) {
			$npost['frm_filter'] = $post['frm_filter'];
		}

		if ($this->frm_consumedlogin) {
			$npost['frm_consumedlogin'] = 'true';
		}

		$url = "/display.php?" . $this->get_get_from_post(null, $npost);

		return $url;
	}

	function printObjectElement($parent, $class, $classdesc, $obj, $name, $width, $descr, $colcount)
	{
		global $gbl, $sgbl, $login;

		$skin_name = $login->getSpecialObject('sp_specialplay')->skin_name;

		$rclass = $class;

		list($graphtype, $graphwidth) = exec_class_method($rclass, "getGraphType");

		if ($name === 'syncserver') {
			$serverdiscr = pserver::createServerInfo(array($obj->syncserver), $class);
		}

		$__external = 0;

		$iconpath = get_image_path();

		if (isset($descr[$name]) && (csa($descr[$name][0], 'q') || csa($descr[$name][0], "D"))) {
			// For hard quota you need priv. For soft quota, you use used.
			if (csa($descr[$name][0], 'h')) {
				$pname = $obj->priv->display($name);
			} else {
				$pname = $obj->used->display($name);
			}
		} else {
			if (isset($descr[$name]) && csa($descr[$name][0], 'p')) {
				if (cse($name, "_per_f")) {
					$qrname = strtil($name, "_per_f");
					$pname = array($obj->priv->$qrname, $obj->used->$qrname, null);
				} else {
					$pname = $obj->perDisplay($name);
				}
			} else {
				$pname = $obj->display($name);
				$pname = Htmllib::fix_lt_gt($pname);

				if (csa($pname, "_lximg:")) {
					$pname = preg_replace("/_lximg:([^:]*):([^:]*):([^:]*):/", "<img src='$1' width='$2' height='$3'>", $pname);
				}

				if (csa($pname, "_lxspan:")) {
					$pname = preg_replace("/_lxspan:([^:]*):([^:]*):/", "<span title='$2'>$1</span>", $pname);
				}

				if (csa($pname, "_lxurl:")) {
					$pname = preg_replace("/_lxurl:([^:]*):([^:]*):/", "<a class='insidelist' target='_blank' href='http://$1'> $2 </a>", $pname);
				}

				if (csa($pname, "_lxinurl:")) {
					$url = preg_replace("/_lxinurl:([^:]*):([^:]*):/", "$1", $pname);
					$url = $this->getFullUrl($url);
					$url = "\"$url\"";
					$pname = preg_replace("/_lxinurl:([^:]*):([^:]*):/", "<a class='insidelist' href=$url> $2 </a>", $pname);
				}

				if ($name === 'syncserver') {
					$pname = "<span title='$serverdiscr'>$pname</span>";
				}
			}
		}

		//	$wrapstr = ($width === "100%") ? "wrap" : "nowrap";
		$wrapstr = ($width === "100%") ? "" : "nowrap";

		$target = null;
		$purl = null;

		$url = null;

		$__full_url = false;

		if ($name === 'parent_name_f' && csb($class, "all_")) {
			$url = $this->generateParentListUrl($obj);
			$ac_descr = $this->getActionDetails($url, $purl, $iconpath, $path, $post, $_t_file, $_t_name, $_t_image, $__t_identity);
			$__full_url = true;
			$__full_url_t_identity = $__t_identity;
		}

		if (isset($descr[$name][3]) || csa($name, "abutton_")) {
			if (csa($name, "abutton_")) {
				$urlname = $obj->nname;
				$str = strfrom($name, "abutton_");
				$_tv = explode("_s_", $str);

				if ($_tv[0] === 'list') {
					$url = "a=list&c={$_tv[1]}";
				} else {
					if ($_tv[0] === 'show') {
						$url = "a=show&o={$_tv[1]}";
					} else {
						$url = "a=$_tv[0]&sa={$_tv[1]}";
					}
				}

				$url = "&k[class]=$class&k[nname]=$urlname&$url";

			} else {
				if ($this->is_special_url($descr[$name][3])) {
					$url = $descr[$name][3];
				} else {
					if (csb($descr[$name][3], "__stub")) {
						$url = $obj->getStubUrl($descr[$name][3]);
					} else {
						if (csb($class, "all_")) {
							$url = $this->generateEntireUrl($obj, $login);

							if (!$url) {
								/// That means that the object is dangling and has no parent.
								throw new lxException("object_found_without_proper_parent");
							}
						} else {
							$urlname = $obj->nname;
							$url = $descr[$name][3] . "&k[class]=$class&k[nname]=$urlname";
						}
					}
				}
			}

			if ($this->is_special_url($url)) {
				$purl = $url->purl;
				$target = $url->target;
				$url = $url->url;
				$purl = $this->getFullUrl($purl);
				$url = str_replace("[%s]", $obj->nname, $url);

				if (strpos($url, "http:/") !== false) {
					$__external = 1;
				}
			} else {
				$url = $this->getFullUrl($url);
			}

			$ac_descr = $this->getActionDetails($url, $purl, $iconpath, $path, $post, $_t_file, $_t_name, $_t_image, $__t_identity);

		}

		$align = "align='left'";
		$valign = "valign='middle'";
		$image = 0;

		if (csa($descr[$name][0], "e")) {
			$pname = strtolower($pname);
			$property = "{$name}_v_$pname";
			$prop_descr = get_classvar_description($rclass, $property);

			if (!$prop_descr) {
				dprint("Property Description for $rclass $property not Found <br /> \n");
			}

			$this->fix_variable_overload($prop_descr, $classdesc[2]);
			$image = $this->get_image($iconpath, $class, $property, ".gif");
			$help = $this->get_full_help($prop_descr['help'], $obj->getId());
			$alt = lx_strip_tags($help);
			$help = $this->get_action_or_display_help($help, "notice");

			// MR -- need this process to fix title
			$alt = preg_replace("/_lxspan:([^:]*):([^:]*):/", "$2", $alt);

			$align = "center onmouseover=\"changeContent('help',' $help')\" onmouseout=\"changeContent('help','helparea')\"";

			if (!$sgbl->isBlackBackground()) {
				if ($skin_name === 'feather') {
					$pname = " <span title='$alt'><img src='$image' width='16' height='16'></span>";
				} else {
					$spancolor = '#ddd';
					$spanchar = '&#x2739;';

					if ($pname === 'on') {
						$spancolor = '#2d2';
						$spanchar = '&#x2739;';
					}
					if ($pname === 'off') {
						$spancolor = '#d22';
						$spanchar = '&#x2739;';
					}
					if ($pname === 'dull') {
						$spancolor = '#aaa';
						$spanchar = '&#x2739;';
					}

					if ($pname === 'ok') {
						$spancolor = '#2d2';
						$spanchar = '&#x2739;';
					}
					if ($pname === 'exceed') {
						$spancolor = '#d82';
						$spanchar = '&#x2739;';
					}

					//	if (strpos($pname, 'customer') !== false) { $spancolor = '#22d'; $spanchar = '&#x263A'; }
					if ($pname === 'customer') {
						$spancolor = '#22d';
						$spanchar = '&#x263A';
					}

					$pname = "<span title='$alt'><span style='font-size: 1.5em; color:{$spancolor}'>{$spanchar}</span></span>";
				}
			}

			$this->save_non_existant_image($image);
			$image = 1;
		}

		if (!$obj->isAction($name) && char_search_a($descr[$name][0], "b")) {
			$pname = "";
		}

		$bgcolorstring = null;
		$forecolorstring = null;

		if ($sgbl->isBlackBackground()) {
			$bgcolorstring = "bgcolor=#000";
			$forecolorstring = "color=#999999";
		}

		if ($url && $obj->isAction($name)) {
			$urlhelp = "";

			if (!$image) {
				$this->fix_variable_overload($ac_descr, $classdesc[2]);
				// When it is showing the parent name, it is showing the resource under that parent, nad not under this object.
				if ($__full_url) {
					$help = $this->get_full_help($ac_descr[2], $__full_url_t_identity);
				} else {
					$help = $this->get_full_help($ac_descr[2], $obj->getId());
				}

				$alt = lx_strip_tags($help);
				$help = $this->get_action_or_display_help($help, "action");
				$urlhelp = "onmouseover=\"changeContent('help',' $help')\" onmouseout=\"changeContent('help','helparea')\"";

				if (strstr($descr[$name][0], "b") != null || csb($name, "abutton")) {
					if ($obj->isButton($name)) {
						if ($sgbl->isBlackBackground()) {
							$pname = "b";
						} else {
							if ($skin_name === 'feather') {
								$pname = " <span title='$alt'><img src='$_t_image' height=15 width=15></span>";
							} else {
								$txt = '<span style="font-size:1.5em">&#x25C9;</span>';

								if (strpos($_t_image, '/start.gif')) {
									$txt = '<span style="font-size:1.5em">&#x21E7;</span>';
								}
								if (strpos($_t_image, '/stop.gif')) {
									$txt = '<span style="font-size:1.5em">&#x21E9;</span>';
								}
								if (strpos($_t_image, '/restart.gif')) {
									$txt = '<span style="font-size:1.5em">&#x21F3;</span>';
								}


								//	if (strpos($_t_image, '/start.gif')) { $txt = '<span style="font-size:1.5em">&#x25C9;</span>'; }
								//	if (strpos($_t_image, '/stop.gif')) { $txt = '<span style="font-size:1.5em">&#x25CE;</span>'; }
								//	if (strpos($_t_image, '/restart.gif')) { $txt = '<span style="font-size:1.5em">&#x262A;</span>'; }

								$pname = "<span title='$alt'>{$txt}</span>";

								//	$pname = "<span title='$alt' style='font-size:1.5em; color:#68a'>&#x25C9;</span>";
							}
						}

						$align = "center";
					} else {
						$pname = "";
					}
				}
			}
?>

			<td <?= $bgcolorstring ?> <?= $wrapstr ?> <?= $align ?> class="collist"> <span title='<?= $alt ?>'>
<?php
			$method = ($__external) ? "get" : $sgbl->method;

			if ($this->frm_action === 'selectshow') {
				$post['frm_action'] = 'selectshow';
				$post['frm_selectshowbase'] = $this->frm_selectshowbase;

			}

			$this->print_input_vars($post);
?>

					<a class="insidelist" <?= $target ?> <?= $urlhelp ?> href="<?= $url ?>"> <?= $pname ?> </a>
			</td>
<?php

		} else {
			if (char_search_a($descr[$name][0], "p")) {

?>
				<td <?= $bgcolorstring ?> <?= $wrapstr ?> <?= $align ?> class="collist">
<?php
					$arr = $pname;
					$this->show_graph($arr[0], $arr[1], null, $graphwidth, $arr[2], $graphtype, $obj->getId(), $name);
?>

				</td>
<?php

			} else {
				if (csa($descr[$name][0], "W")) {
					$pname = str_replace("\n", "<br />\n", $pname);
					$pname = str_replace("[code]", "<div style='padding: 10px; margin: 10px; border: 1px solid #43a1a1'>", $pname);
					$pname = str_replace("[quote]", "<div style='background:#eee; padding: 10px; margin: 10px; border: 1px solid #aaa'> [b] QUOTE [/b]", $pname);
					$pname = str_replace("[b]", "<span style='font-weight:bold'>", $pname);
					$pname = str_replace("[/b]", "</span>", $pname);
					$pname = str_replace("[/code]", "</div>", $pname);
					$pname = str_replace("[/quote]", "</div>", $pname);
					$pname = "<table width='100%' style='background:white;padding:20px; margin-top: 8px; border: 1px solid grey;' cellpadding='0' cellspacing='0'> <tr> <td> $pname </td> </tr> </table>  ";
				}

				$pname = str_replace("Unlimited", "&#x221E;", $pname);
?>

				<td <?= $bgcolorstring ?> <?= $wrapstr ?> <?= $align ?> class="collist"> <?= $pname ?> </td>
<?php
			}
		}
	}

	function print_input($type, $name, $value, $extra = null)
	{
?>

		<input type="<?= $type ?>" name="<?= $name ?>" value="<?= $value ?>" <?= $extra ?> />
<?php
	}

	function print_next_previous_link($object, $class, $place, $iconpath, $name, $page_value)
	{
		global $gbl, $sgbl, $login;

		$filtername = $object->getFilterVariableForThis($class);
?>

		<form name="form<?= $name ?>_page_<?= $place ?>" method="<?= $sgbl->method ?>"
		      action="<?= $_SERVER["PHP_SELF"] ?>" accept-charset="utf-8">
<?php
			$this->print_current_input_var_unset_filter($filtername, array('pagenum'));
			$this->print_current_input_vars(array('frm_hpfilter'));
			$this->print_input("hidden", "frm_hpfilter[$filtername][pagenum]", $page_value);
?>

		</form>

<?php
		$help = "<span class=bold> Action: </span> <br /> <br /> ";

		if ($name === "forward" || $name === "rewind") {
			$help .= ucfirst($name) . "  a few Pages.";
		} else {
			$help .= "Go To " . ucfirst($name) . " Page.";
		}

		$link = "<a onmouseover=\"changeContent('help','$help')\" " .
			"onmouseout=\"changeContent('help','helparea')\" href=javascript:document.form{$name}_page_$place.submit()>" .
			"<img src=$iconpath/{$name}_page.gif align=absbottom></a> ";

		return $link;
	}

	function print_next_previous($object, $class, $place, $cgi_pagenum, $total_num, $pagesize)
	{
		global $gbl, $sgbl, $login;

		$iconpath = get_general_image_path() . "/icon";

		$search_brack_o = "<b>";
		$search_brack_c = "</b>";

		$prev_link = null;
		$first_link = null;
		$rewind_link = null;
?>

		<table width="10">
			<tr>
				<td bgcolor="#ffffff" nowrap>
<?php
		$first_link = $this->print_next_previous_link($object, $class, $place, $iconpath, "first", 1);

		$rewind_link = $this->print_next_previous_link($object, $class, $place, $iconpath, "rewind", ($cgi_pagenum + $cgi_pagenum % 2) / 2);
		$prev_link = $this->print_next_previous_link($object, $class, $place, $iconpath, "prev", max(1, ($cgi_pagenum - 1)));

		$next_link = null;
		$forward_link = null;
		$last_link = null;

		if ($total_num > $pagesize) {
			$page = $total_num / $pagesize;
			$page = explode('.', $page);
			$page = (isset($page[1])) ? $page[0] + 1 : $page[0];

			$left = $page - $cgi_pagenum;
			$next_link = $this->print_next_previous_link($object, $class, $place, $iconpath, "next", min(($cgi_pagenum + 1), $page));
			$forward_link = $this->print_next_previous_link($object, $class, $place, $iconpath, "forward", min($cgi_pagenum + ($left + ($left % 2)) / 2, $page));
			$last_link = $this->print_next_previous_link($object, $class, $place, $iconpath, "last", $page);

?>

						<?= $first_link ?> &nbsp; <?= $rewind_link ?> &nbsp; <?= $prev_link ?> <b><span class=pagetext>&nbsp;Page <?= $cgi_pagenum ?>
								(of <?= $page ?>)</span></b> <?= $next_link ?> <?= $forward_link ?> <?= $last_link ?>
<?php
			$search_brack_o = "  &nbsp;  (";
			$search_brack_c = ") ";
		}
?>
				</td>
			</tr>
		</table>

<?php
	}

	function print_machine($object)
	{
		if (!$object->isClass('client') && !$object->isLocalhost() && $object->syncserver != $object->nname) {
			return "(on $object->syncserver)";
		}

		return '';
	}

	function printSearchTable($name_list, $parent, $class)
	{
		global $gbl, $sgbl, $login;

		$this->print_real_search($name_list, $parent, $class);
	}

	function get_class_description($class, $display = null)
	{
		$classdesc = get_classvar_description($class);

		if (!$classdesc) {
?>
			Cannot access <?= $class ?>::\$__desc
<?php

			exit(0);
		}

		return $classdesc;
	}

	function display_count(&$obj_list, $disp)
	{
		global $gbl, $sgbl, $login;

		$n = 0;

		if (!$obj_list) {
			return $n;
		}

		$filter = $this->frm_filter;

		if (!$filter && !$this->frm_searchstring) {
			return count($obj_list);
		}

		foreach ($obj_list as $o) {
			if (if_search_continue($o) || !$o->isDisplay($filter)) {
				$obj_list[$o->nname] = null;
				unset($obj_list[$o->nname]);

				continue;
			}

			$n++;
		}

		return $n;
	}

	function printListAddForm($parent, $class)
	{
		global $gbl, $sgbl, $login;

		$vlist = exec_class_method($class, "addListForm", $parent, $class);

		$skin_color = $login->getSkinColor();

		if (!$vlist) {
			return;
		}

		$unique_name = "{$parent->getClName()}_$class";
		$showstring = "Show/Hide";
		$show_all_string = null;

		if ($login->getSpecialObject('sp_specialplay')->isOn('close_add_form')) {
			$visiblity = "visibility:hidden;display:none";
		} else {
			$visiblity = "visibility:visible;display:block";
		}

		$cdesc = get_description($class);
		$cdesc .= " for $parent->nname";

		$backgroundstring = "background:#fff;";
		$fontcolor = "black";
		$bordertop = "#d0d0d0";

		if ($sgbl->isBlackBackground()) {
			$backgroundstring = "background:#000;";
			$fontcolor = "#333333";
			$bordertop = "#444444";
		}
?>

		<div style="background: #<?= $skin_color ?> url(<?= $col ?>); padding: 4px; margin: 0 25px; text-align: center">&nbsp;>>>> <a
				href="javascript:toggleVisibility('listaddform_<?= $unique_name ?>');"> Click Here to
				Add <?= $cdesc ?> (<?= $showstring ?>)</a> <?= $show_all_string ?> <<<<&nbsp;</div>

		<br/>

		<div id="listaddform_<?= $unique_name ?>" style="<?= $visiblity ?>; width: 910px; margin: 0 auto 0 auto">
			<div><?= do_addform($parent, $class, null, true) ?></div>
		</div>

<?php
	}

	function print_real_search($name_list, $parent, $class)
	{
		global $gbl, $sgbl, $login;

		$col = $login->getSkinColor();

		$rclass = $class;
		$filtername = $parent->getFilterVariableForThis($class);
		$url = $_SERVER['PHP_SELF'];
		$gen_image_path = get_general_image_path();

		$btnpath = $gen_image_path . "/icon/";

		$classdesc = $this->get_class_description($rclass);

		$unique_name = trim($parent->nname) . trim($class) . trim($classdesc[2]);
		$unique_name = fix_nname_to_be_variable($unique_name);

		$imgpath = $login->getSkinDir();
		$buttonpath = get_image_path();

		$img = $this->get_image($buttonpath, $rclass, "list", ".gif");

		$global_visible = false;
		$value = null;

		foreach ($name_list as $name => $width) {
			if (isset($this->frm_hpfilter[$filtername]["{$name}_o_cont"])) {
				$value = $this->frm_hpfilter[$filtername]["{$name}_o_cont"];
			} else {
				if ($login->issetHpFilter($filtername, "{$name}_o_cont")) {
					$value = $login->getHPFilter($filtername, "{$name}_o_cont");
				}
			}

			if ($value && $value !== '--any--') {
				$global_visible = true;
				break;
			}
		}

		if ($global_visible) {
			$visiblity = "visibility:visible;display:block";
			$showstring = null;
			$show_all_string = "(Click on show-all to hide)";
		} else {
			$showstring = "Show/Hide";
			$show_all_string = null;
			$visiblity = "visibility:hidden;display:none";
		}

		$backgroundstring = "background:#fffafa;";
		$backgroundnullstring = null;
		$bordertop = "#d0d0d0";

		if ($sgbl->isBlackBackground()) {
			$backgroundstring = "background:gray;";
			$backgroundnullstring = "background:gray;";
			$bordertop = "#333";
		}
?>

		<div style="width: 910px; margin: 0 auto 0 auto;">
			<fieldset
				style='<?= $backgroundnullstring ?> padding: 0; text-align: center; margin: 0; border: 0; border-top: 1px solid <?= $bordertop ?>'>
				<legend><span style='font-weight:bold'>Advanced Search <a
							href="javascript:toggleVisibility('search_<?= $unique_name ?>');"><?= $showstring ?> </a> <?= $show_all_string ?>
					</span>
				</legend>
			</fieldset>
		</div>

		<div id=search_<?= $unique_name ?> style='<?= $visiblity ?>; width: 910px; margin: 0 auto 0 auto'>
			<form name="lpfform_rsearch" method="<?= $sgbl->method ?>" action="<?= $url ?>"
			      onsubmit="return true;" accept-charset="utf-8">
				<table width=100% border=0 align="center" cellpadding=0
				       style='<?= $backgroundstring ?> border: 1px solid #<?= $col ?>'>
					<tr>
						<td><img width=26 height=26 src="<?= $img ?>"></td>
					</tr>
					<tr>
						<td width=10> &nbsp; </td>
						<td>
							<table width="100%" height="100%" cellpadding="0" cellspacing="0">

<?php
		$filarr[] = 'pagenum';
		$count = 0;

		foreach ($name_list as $name => $width) {
			$count++;
			$desc = "__desc_{$name}";
			$descr[$name] = get_classvar_description($rclass, $desc);

			if (!$descr[$name]) {
?>
									Cannot access static variable <?= $rclass ?>::<?= $desc ?>
<?php

				exit(0);
			}

			if (csa($descr[$name][2], ':')) {
				$_tlist = explode(':', $descr[$name][2]);
				$descr[$name][2] = $_tlist[1];
			}

			foreach ($descr[$name] as &$d) {
				if ($this->is_special_url($d)) {
					continue;
				}

				if (strstr($d, "%v") !== false) {
					$d = str_replace("[%v]", $classdesc[2], $d);
				}
			}
?>
								<td nowrap align="right"><span
										style="font-weight: bold"><?= $descr[$name][2] ?> </span> &nbsp;
								</td>
								<td>
<?php
			$filarr[] = "{$name}_o_cont";
			$value = null;

			if (isset($this->frm_hpfilter[$filtername]["{$name}_o_cont"])) {
				$value = $this->frm_hpfilter[$filtername]["{$name}_o_cont"];
			} else {
				if ($login->issetHpFilter($filtername, "{$name}_o_cont")) {
					$value = $login->getHPFilter($filtername, "{$name}_o_cont");
				}
			}

			if ($width) {
				if ($width[0] === 's') {
?>

											<select
												name="frm_hpfilter[<?= $filtername ?>][<?= $name ?>_o_cont]"
												class="searchbox" size="1" width="10" maxlength="30">
<?php
					foreach ($width[1] as $v) {
						$sel = '';

						if ($v === $value) {
							$sel = 'SELECTED';
						}
?>
													<option <?= $sel ?>
														value="<?= $v ?>"><?= $v ?></option>';
<?php
					}
?>
											</select>
<?php
										}
			} else {
?>
										<input type="text"
										       name="frm_hpfilter[<?= $filtername ?>][<?= $name ?>_o_cont]"
										       value="<?= $value ?>" class="searchbox" size="11"
										       maxlength="30">
<?php
			}
?>
								</td>
<?php
			if ($count === 3) {
				$count = 0;
?>
								</tr>
								<tr>
<?php
			}
		}

									$this->print_current_input_var_unset_filter($filtername, $filarr);
									$this->print_current_input_vars(array('frm_hpfilter'));
?>

									</td> </tr>
							</table>

						</td>
						<td>
							<input type=submit class=submitbutton name=Search
							       value="&nbsp;&nbsp;Search&nbsp;&nbsp;">

						</td>
					</tr>
					<tr>
						<td width=10> &nbsp; </td>
					</tr>
				</table>
			</form>
		</div>

<?php
	}

	function isResourceClass($class)
	{
		return ($class === 'permission' || $class === 'resource' || $class === 'information');
	}

	function checkIfFilter($filter)
	{
		foreach ($filter as $k => $f) {
			if ($k !== 'view' && $k !== 'pagesize' && $k !== 'pagenum' && $k !== 'sortby' && $k !== 'sortdir' && $f && $f !== '--any--') {
				return true;
			}
		}

		return false;
	}

	function printObjectTable($name_list, $parent, $class, $blist = array(), $display = null)
	{
		global $gbl, $sgbl, $login;

		$col = $login->getSkinColor();

		$skin_name = $login->getSpecialObject('sp_specialplay')->skin_name;

		$view = null;

		if (exec_class_method($class, "hasViews")) {
			$blist[] = array($this->getFullUrl("a=list&c=$class&frm_filter[view]=quota"), 1);
			$blist[] = array($this->getFullUrl("a=list&c=$class&frm_filter[view]=normal"), 1);
		}

		print_time("$class.objecttable");

		$rclass = $class;

		if ($this->frm_accountselect !== null) {
			$sellist = explode(',', $this->frm_accountselect);
		} else {
			$sellist = null;
		}

		$filtername = $parent->getFilterVariableForThis($class);
		$sortdir = null;
		$sortby = null;
		$fil = $login->getHPFilter();

		if (isset($fil[$filtername]['sortby'])) {
			$sortby = $fil[$filtername]['sortby'];
		}

		if (isset($fil[$filtername]['sortdir'])) {
			$sortdir = $fil[$filtername]['sortdir'];
		}

		$pagesize = (int)$login->issetHpFilter($filtername, 'pagesize') ? $login->getHPFilter($filtername, 'pagesize') : exec_class_method($rclass, "perPage");

		if (!(int)$pagesize) {
			$pagesize = 10;
		}

		$view = null;

		if (isset($fil[$filtername]['view'])) {
			$view = $fil[$filtername]['view'];
			dprintr($view);
		}

		if (!$name_list) {
			if (csa($class, "all_")) {
				$__tcl = strfrom($class, "all_");
				$name_list = exec_class_method($__tcl, "createListNlist", $parent, $view);

				foreach ($name_list as $k => $v) {
					if (csa($k, "abutton")) {
						unset($name_list[$k]);
					}
				}

				$name_list = lx_merge_good(array('parent_name_f' => '10%'), $name_list);
			} else {
				$name_list = exec_class_method($class, "createListNlist", $parent, $view);
			}
		}

		$iconpath = get_image_path();

		$buttonpath = get_image_path();

		$nlcount = count($name_list) + 1;
		$imgheadleft = $login->getSkinDir() . "/images/top_lt.gif";
		$imgheadleft = $login->getSkinDir() . "/images/top_lt.gif";
		$imgheadleft2 = $login->getSkinDir() . "/images/top_lt.gif";
		$imgheadright = $login->getSkinDir() . "/images/top_slope_rt.gif";
		$imgheadbg = $login->getSkinDir() . "/images/top_bg.gif";
		$imgbtnbg = $login->getSkinDir() . "/images/btn_bg.gif";
		$imgtablerowhead = $login->getSkinDir() . "/images/tablerow_head.gif";
		$imgtablerowheadselect = $login->getSkinDir() . "/images/top_line_medium.gif";
		$imgbtncrv = $login->getSkinDir() . "/images/btn_crv_right.gif";
		$imgtopline = $login->getSkinDir() . "/images/top_line.gif";
		$skindir = $login->getSkinDir();

		$classdesc = $this->get_class_description($rclass, $display);

		$unique_name = trim($parent->nname) . trim($class) . trim($display) . trim($classdesc[2]);

		$unique_name = fix_nname_to_be_variable($unique_name);
?>

		<script> var ckcount<?=$unique_name?>; </script>
<?php
		if (!$sortby) {
			$sortby = exec_class_method($rclass, "defaultSort");
		}

		if (!$sortdir) {
			$sortdir = exec_class_method($rclass, "defaultSortDir");
		}

		$obj_list = $parent->getVirtualList($class, $total_num, $sortby, $sortdir);

		if (exec_class_method($rclass, "isdefaultHardRefresh")) {
			exec_class_method($rclass, "getExtraParameters", $parent, $obj_list);

		} else {
			if ($this->frm_hardrefresh === 'yes') {
				exec_class_method($rclass, "getExtraParameters", $parent, $obj_list);
			}
		}

		$pluraldesc = get_plural($classdesc[2]);

		if ($login->issetHpFilter($filtername, 'pagenum')) {
			$cgi_pagenum = $login->getHPFilter($filtername, 'pagenum');
		} else {
			$cgi_pagenum = 1;
		}

		$showvar = null;

		if ($login->issetHpFilter($filtername, 'show')) {
			$showvar = $login->getHPFilter($filtername, 'show');
		}

		if ($showvar) {
			$showvar = "(" . ucfirst($showvar) . ")";
		}

		$filterundermes = null;

		if ($login->issetHpFilter($filtername) && $this->checkIfFilter($login->getHPFilter($filtername))) {
			$filterundermes = "({$login->getKeywordUc('search_on')}";

			if ($total_num == 0) {
				$filterundermes .= ". Click on show all to see all the objects";
			}

			$filterundermes .= ")";
		}

		$perpageof = null;

		$lower = $pagesize * ($cgi_pagenum - 1) + 1;

		if ($lower > $total_num) {
			$lower = $total_num;
		}

		if ($pagesize * $cgi_pagenum > $total_num) {
			$upper = $total_num;
		} else {
			$upper = $pagesize * $cgi_pagenum;
		}

		$total_page = strtil($total_num / $pagesize, ".") + 1;
		$perpageof = "$lower to $upper of ";

		if ($sgbl->isBlackBackground()) {
			$backgroundstring = "background:#222222;";
			$stylebackgroundstring = "style='background-color:#000000; background:#000000;'";
			$filteropacitystringspan = "<span style='background:black'> ";
			$filteropacitystring = "style='FILTER:progid;-moz-opacity:0.5'";
			$filteropacitystringspanend = "</span>";

			$backgroundcolorstring = "#000000";
			$imgtopline = $login->getSkinDir() . "/images/black.gif";
			$blackstyle = "style='background:black;color:gray;border:1px solid gray;'";
			$imgtablerowhead = null;
			$col = "333";
			$bordertop = "#444444";

		} else {
			$blackstyle = null;
			$backgroundstring = "background:#fffafa;";
			$stylebackgroundstring = null;
			$filteropacitystring = null;
			$filteropacitystringspan = null;
			$filteropacitystringspanend = null;
			$backgroundcolorstring = "#ffffff";
			$bordertop = "#d0d0d0";
		}

		if (!$sellist && !$this->isResourceClass($class)) {
?>
			<!-- "I am here 4" -->
			<br/>
			<div style="width: 910px; margin: 0 auto 0 auto;">
				<fieldset
					style="<?= $backgroundstring ?> padding: 0 ; text-align: center ; margin: 0; border: 0; border-top: 1px solid <?= $bordertop ?>">
					<legend>
				<span
					style='font-weight:bold'><?= $pluraldesc ?> <?= $showvar ?> <?= $login->getKeyword('under') ?> <?= $parent->getId() ?>
					<span style="color:red"><?= $filterundermes ?></span> <?= $this->print_machine($parent) ?>
					(<?= $perpageof ?><?= $total_num ?>)</span></legend>
				</fieldset>
			</div>
<?php
		}

		if (!$sellist && !$this->isResourceClass($class) && !$gbl->__inside_ajax) {
?>
			<!-- "I am here 5" -->
			<div style="width: 910px; margin: 0 auto 0 auto">
				<table width="100%" cellpadding="0" cellspacing="0" border="0"
				       style="<?= $backgroundstring ?>  border: 1px solid #<?= $col ?>;">
					<tr>
						<td valign="bottom" height="10" colspan="4"> &nbsp;  </td>
					</tr>
					<tr>
						<td width="10"> &nbsp; </td>
						<td> <?= $this->print_list_submit($class, $blist, $unique_name) ?> </td>
						<td> <?= $this->print_search($parent, $class) ?> </td>
						<td width="10"> &nbsp; </td>
					</tr>
					<tr>
						<td height="10" colspan="4"> &nbsp; </td>
					</tr>
				</table>
			</div>
<?php
		}

		if (!$sellist && !$this->isResourceClass($class) && !$gbl->__inside_ajax) {
			$imgshow = get_general_image_path() . "/button/btn_show.gif";
?>
			<div style="width: 910px; margin: 0 auto 0 auto">
				<table cellpadding="0" cellspacing="0" width="100%" border=0 valign="middle">
					<tr>
						<td colspan="100" height="6"></td>
					</tr>
					<tr valign="middle">
<?php
			$imgbtm1 = get_general_image_path() . "/button/btm_01.gif";
			$imgbtm2 = get_general_image_path() . "/button/btm_02.gif";
			$imgbtm3 = get_general_image_path() . "/button/btm_03.gif";
			$imgshow = get_general_image_path() . "/button/btn_show.gif";

						$rpagesize = exec_class_method($rclass, "perPage");

			if ($rpagesize > 1000) {
				$width = 50;
			} else {
				$width = 70;
			}
?>

						<td><b>Page</b>&nbsp;</td>
<?php
			$last = false;

			foreach (range(1, $total_page) as $i) {
				if ($i > 6) {
					$last = true;
				}

				if ($sgbl->isBlackBackground()) {
					$col = "333";
				}

				if ($i == $cgi_pagenum) {
					$bgcolorstring = "background: #$col";
				} else {
					$bgcolorstring = "";
				}
?>

							<td width="6" style="border: 1px solid #<?= $col ?>; <?= $bgcolorstring ?>">
								<form name="page<?= $unique_name ?><?= $i ?>" method="<?= $sgbl->method ?>"
								      action="" accept-charset="utf-8">
<?php
				$this->print_current_input_var_unset_filter($filtername, array('pagenum'));
				$this->print_current_input_vars(array('frm_hpfilter'));

				if ($last) {
?>

										<input type="hidden" name="frm_hpfilter[<?= $filtername ?>][pagenum]"
										       value="<?= $total_page ?> class="small">
			<a href="javascript:page<?= $unique_name ?><?= $i ?>.submit()">...Last&nbsp;</a>
<?php
				} else {
?>
										<input type="hidden" name="frm_hpfilter[<?= $filtername ?>][pagenum]"
										       value=<?= $i ?> class="small">
										<a href="javascript:page<?= $unique_name ?><?= $i ?>.submit()">&nbsp;<?= $i ?>&nbsp;</a>
<?php
				}
?>
								</form>
							</td>
<?php
				if ($last) {
					break;
				}
			}
?>
						<td width="100%"></td>
						<td nowrap><b>Show</b>&nbsp;</td>
<?php
			$f_page = (int)$login->issetHpFilter($filtername, 'pagesize') ? $login->getHPFilter($filtername, 'pagesize') : $pagesize;

			if ($rpagesize < 1000) {
				$list = array($rpagesize / 2, $rpagesize, $rpagesize * 2, $rpagesize * 4, $rpagesize * 8, $rpagesize * 16);
				$i = 0;

				foreach ($list as $l) {
					$i++;

					if ($l === $f_page) {
						$bgcolorstring = "background: #$col";
					} else {
						$bgcolorstring = "";
					}
?>

								<td width="6" style="border: 1px solid #<?= $col ?>; <?= $bgcolorstring ?>">
									<form name="perpage_<?= $i ?><?= $unique_name ?>" method="<?= $sgbl->method ?>"
									      action="/display.php" accept-charset="utf-8">
<?php
					$this->print_current_input_var_unset_filter($filtername, array('pagesize', 'pagenum'));
					$this->print_current_input_vars(array('frm_hpfilter'));
?>
										<input type="hidden" name="frm_hpfilter[<?= $filtername ?>][pagesize]" value="<?= $l ?>">
									</form>
									<a href="javascript:perpage_<?= $i ?><?= $unique_name ?>.submit()">&nbsp;<?= $l ?>&nbsp;</a>
								</td>
<?php

				}
			}
?>
					</tr>
				</table>
			</div>

<?php
		}

		if ($this->isResourceClass($class)) {
			$divwidth = "240px";
		} else {
			$divwidth = "910px";
		}
?>

		<div style="width: <?= $divwidth ?>; margin: 0 auto 0 auto; background-color:#fff">
			<table cellspacing="1" cellpadding="3" width="100%" align="center">
				<tr>
					<td colspan="<?= $nlcount ?>"></td>
				</tr>
				<tr height="25" valign="middle">
<?php
		if (!$this->isResourceClass($class) && !$gbl->__inside_ajax) {
			//	$checked = "checked disabled";
			$checked = "";
?>

						<td width=10 style="background:#cde url(<?= $imgtablerowhead ?>)">
							<form name="formselectall<?= $unique_name ?>" value="hello"
							      accept-charset="utf-8"> <?= $filteropacitystringspan ?>
								<input <?= $filteropacitystring ?> type=checkbox name="selectall<?= $unique_name ?>"
								                                   value=on <?= $checked ?>
								                                   onclick="calljselectall<?= $unique_name ?> ()">
								<?= $filteropacitystringspanend ?>
							</form>
						</td>
<?php
		}

		$imguparrow = get_general_image_path() . '/button/uparrow.gif';
		$imgdownarrow = get_general_image_path() . '/button/downarrow.gif';

		foreach ($name_list as $name => $width) {
			$desc = "__desc_{$name}";

			if (csa($name, "abutton")) {
				$descr[$name] = array("b", "", "", "", 'help' => "");
			} else {
				$descr[$name] = get_classvar_description($rclass, $desc);
			}

			if (!$descr[$name]) {
?>

							Cannot access static variable <?= $rclass ?>::<?= $desc ?>
<?php

				exit(0);
			}

			if (csa($descr[$name][2], ':')) {
				$_tlist = explode(':', $descr[$name][2]);
				$descr[$name][2] = $_tlist[0];
			}

			foreach ($descr[$name] as &$d) {
				if ($this->is_special_url($d)) {
					continue;
				}

				if (strstr($d, "%v") !== false) {
					$d = str_replace("[%v]", $classdesc[2], $d);
				}
			}

			if ($width === "100%") {
				//	$wrapstr = "wrap";
				$wrapstr = "";
			} else {
				$wrapstr = "nowrap";
			}

			if ($sortby && $sortby === $name) {
				if ($sgbl->isBlackBackground()) {
					$wrapstr .= " style='background:gray'";
				} else {
					$wrapstr .= " style='background:#efe8e0 url({$skindir}/images/listsort.gif)'";
				}
?>
							<!-- "I am here 8" -->
							<td <?= $wrapstr ?> width="<?= $width ?>">
							<table cellspacing="0" cellpadding="2"  border="0"><tr>
							<td class="collist" <?= $wrapstr ?> rowspan="2">

<?php
			} else {
				if ($sgbl->isBlackBackground()) {
					$wrapstr .= " style='background:#efe8e0'";
				} else {
					$wrapstr .= " style='background:#efe8e0 url({$skindir}/images/expand.gif)'";
				}
?>
							<!-- "I am here 9" -->
							<td width="<?= $width ?>" <?= $wrapstr ?> class="collist">

<?php
			}
?>

						<b><?= $this->print_sortby($parent, $class, $unique_name, $name, $descr[$name]) ?></b>
<?php
			$imgarrow = ($sortdir === "desc") ? $imgdownarrow : $imguparrow;

			if ($sortby && $sortby === $name) {
?>

							</td>
							<td width="15"><img src="<?= $imgarrow ?>"></td>
							<td></td></tr></table>
<?php
			} else {
?>
							</td>

<?php
			}
		}

					$count = 0;
					$rowcount = 0;
?>

				</tr>
<?php
		print_time('loop');

		$n = 1;

		foreach ((array)$obj_list as $okey => $obj) {
			if (!$obj) {
				continue;
			}

			// Admin object should not be listed ever.
			if ($obj->isAdmin() && $obj->isClient()) {
				continue;
			}

			$checked = $obj->isSelect() ? "" : "disabled";

			// Fix This.
			if ($sellist) {
				$checked = "checked disabled";

				if (!array_search_bool($obj->nname, $sellist)) {
					continue;
				}
			}

			$imgpointer = get_general_image_path() . "/button/pointer.gif";
			$imgblank = get_general_image_path() . "/button/blank.gif";

			$rowuniqueid = "tr$unique_name$rowcount";
		/*
			// MR -- don't know for what, so disable it.
?>

								<script> loadImage('<?=$imgpointer?>') </script>
								<script> loadImage('<?=$imgblank?>') </script>
<?php
		*/
?>
					<tr height=22 id=<?= $rowuniqueid ?> class=tablerow<?= $count ?>

					    onmouseover=" swapImage('imgpoint<?= $rowcount ?>','','<?= $imgpointer ?>',1);document.getElementById('<?= $rowuniqueid ?>').className='tablerowhilite';"
					    onmouseout="swapImgRestore();restoreListOnMouseOver('<?= $rowuniqueid ?>', 'tablerow<?= $count ?>','ckbox<?= $unique_name . $rowcount ?>')">
<?php

			if (!$this->isResourceClass($class) && !$gbl->__inside_ajax) {
?>

							<td width=10 style='<?= $backgroundstring ?>'> <?= $filteropacitystringspan ?>
								<input <?= $filteropacitystring ?> id="ckbox<?= $unique_name ?><?= $rowcount ?>"
								                                   class="ch1"
								                                   type="checkbox" <?= $checked ?>
								                                   name="frm_accountselect"
								                                   onclick="hiliteRowColor('tr<?= $unique_name ?><?= $rowcount ?>','tablerow<?= $count ?>',document.formselectall<?= $unique_name ?>.selectall<?= $unique_name ?>)"
								                                   value="<?= $obj->nname ?>"> <?= $filteropacitystringspanend ?>
							</td>
<?php
			}

			$colcount = 1;

			foreach ($name_list as $name => $width) {
				try {
					$this->printObjectElement($parent, $class, $classdesc, $obj, $name, $width, $descr, $colcount . "_" . $rowcount);
				} catch (exception $e) {
					break;
				}

				$colcount++;
			}
?>

					</tr>
<?php

			if ($count === 0) {
				$count = 1;
			} else {
				$count = 0;
			}

			$rowcount++;

			if (!$sellist) {
				if ($n === ($pagesize * $cgi_pagenum)) {
					break;
				}
			}

			$n++;
		}
?>

				<tr>
					<td colspan="<?= $nlcount ?>">
<?php
		if (!$rowcount) {
			if ($login->issetHpFilter($filtername, 'searchstring') && $login->getHPFilter($filtername, 'searchstring')) {
?>

								<div style="width: 100%; text-align: center">
									<b><?= $login->getKeyword('no_matches_found') ?></b></div>
<?php

			} else {
				$filtermessagstring = null;

				if ($login->issetHpFilter($filtername)) {
					$filtermessagstring = $login->getKeyword('search_note');
?>

									<div style="width: 100%; text-align: center"><b><?= $filtermessagstring ?></b></div>
<?php
				} else {
?>

									<div style="width: 100%; text-align: center"><b><?= $login->getKeyword('no') ?>
											&nbsp;<?= get_plural($classdesc[2]) ?>
											&nbsp;<?= $login->getKeyword('under') ?>&nbsp;<?= $parent->getId() ?></b>
									</div>

<?php

				}
			}
		}
?>

					</td>
				</tr>
				<tr>
					<td colspan="<?= $nlcount ?>">
						<table cellpadding="0" cellspacing="0" border="0" width="100%">
							<tr height="1" style="background:#efe8e0 url(<?= $imgtopline ?>)">
							</tr>
							<tr>
								<td>
<?php

		if ($this->frm_action === 'selectshow') {
			return;
		}
?>

									<script>ckcount<?=$unique_name;?> = <?=$rowcount . ";  ";?>
											function calljselectall<?=$unique_name?>() {
												jselectall(document.formselectall<?=$unique_name?>.selectall<?=$unique_name?>, ckcount<?=$unique_name?>, '<?=$unique_name;?>')
											}
									</script>
<?php
		if ($sellist) {
?>

										<table <?= $blackstyle ?>>
											<tr>
												<td>
													<form method="<?= $sgbl->method ?>"
													      action="<?= $_SERVER["PHP_SELF"] ?>" accept-charset="utf-8">
<?php
				$this->print_current_input_vars(array("frm_confirmed"));
				$this->print_input("hidden", "frm_confirmed", "yes");
				$this->print_input("submit", "Confrm", "Confirm", "class=submitbutton");
?>
													</form>

												</td>
												<td width="30"> &nbsp; </td>
												<td>
													<form method="<? $sgbl->method ?>" action="/display.php"
													      accept-charset="utf-8">
<?php
				$this->print_current_input_vars(array("frm_action", "frm_accountselect"));
				$this->print_input("hidden", "frm_action", "list");
				$this->print_input("submit", "Cancel", "Cancel", "class=submitbutton");
?>
													</form>

												</td>
											</tr>
										</table>
<?php
		}

		if ($sgbl->isBlackBackground()) {
?>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			</td></tr></table>
<?php
			return;
		}

		if (!$sellist && !$this->isResourceClass($class) && !$gbl->__inside_ajax) {
			$imgbtm1 = get_general_image_path() . "/button/btm_01.gif";
			$imgbtm2 = get_general_image_path() . "/button/btm_02.gif";
			$imgbtm3 = get_general_image_path() . "/button/btm_03.gif";
			$imgshow = get_general_image_path() . "/button/btn_show.gif";
?>
			<table cellpadding="0" cellspacing="0" border="0" width="100%" style="border-top: 1px solid #ddd">
				<tr>
					<td>
						<form name="perpage_<?= $unique_name ?>" method="<?= $sgbl->method ?>" action=""
						      accept-charset="utf-8">
							<table cellpadding="0" cellspacing="0" border="0">
								<tr>
<?php
			if ($skin_name === 'feather') {
?>
									<td><img src="<?= $imgbtm1 ?>"></td>
								<td background="<?= $imgbtm2 ?>">
<?php
			} else {
?>
									<td style="background: #eee;">
<?php
			}

										$rpagesize = exec_class_method($rclass, "perPage");

			if ($rpagesize > 1000) {
				$width = 50;
			} else {
				$width = 70;
			}
?>

										<table width="100%" cellpadding="0" cellspacing="0">
											<tr>
<?php
			if ($skin_name === 'feather') {
?>
												<td width="40">&nbsp;<b>Show</b>&nbsp;</td>
												<td width="<?= $width ?>">
<?php
			} else {
?>
												<td>&nbsp;<b>Show</b>&nbsp;</td>
												<td>
<?php
			}
													$this->print_current_input_var_unset_filter($filtername, array('pagesize', 'pagenum'));
													$this->print_current_input_vars(array('frm_hpfilter'));
													$f_page = (int)$login->issetHpFilter($filtername, 'pagesize') ? $login->getHPFilter($filtername, 'pagesize') : $pagesize;

			if ($rpagesize < 1000) {
?>

														<select class="textbox"
														        onchange="document.perpage_<?= $unique_name ?>.submit()"
														        style="width:40px; border: 1px solid #888"
														        name="frm_hpfilter[<?= $filtername ?>][pagesize]">
<?php
				$list = array($rpagesize / 2, $rpagesize, $rpagesize * 2, $rpagesize * 4, $rpagesize * 8, $rpagesize * 16);

				foreach ($list as $l) {
					$sel = '';

					if ($l == $f_page) {
						$sel = 'SELECTED';
					}
?>

																<option <?= $sel ?> value="<?= $l ?>"><?= $l ?></option>
<?php
				}

?>

														</select>
<?php
			} else {
?>

														<input type="text" class="textbox" style="width:25px"
														       name="frm_hpfilter[<?= $filtername ?>][pagesize]"
														       value="<?= $f_page ?>">
<?php
			}
				
			if ($skin_name === 'feather') {
?>

												</td>
												<td><input type="image" src="<?= $imgshow ?>">
<?php
			} else {
?>
												</td>
												<td><input type="button" value="Go"
												           style="border: 1px solid #ddd; margin: 2px; background-color: #ced">
<?php
			}
?>
												</td>
											</tr>
										</table>
									</td>
<?php
			if ($skin_name === 'feather') {
?>
										<td><img src="<?= $imgbtm3 ?>"></td>
<?php
			} else {
?>
										<td>&nbsp;</td>
<?php
			}
?>
								</tr>
							</table>
						</form>
					</td>
					<td align="right">
<?php

			if ($rpagesize < 1000) {
?>

							<form method="<?= $sgbl->method ?>" action="" accept-charset="utf-8">
								<table cellpadding="0" cellspacing="0" border="0" valign="middle">
									<tr valign="middle">
										<td style="background: #eee;">&nbsp;<b>Page</b>
<?php
											$this->print_current_input_var_unset_filter($filtername, array('pagenum'));
											$this->print_current_input_vars(array('frm_hpfilter'));
?>
											<input class="textbox small" style="width:25px; border: 1px solid #888"
											       name="frm_hpfilter[<?= $filtername ?>][pagenum]" type="text"
											       value="<?= $cgi_pagenum ?>"></td>
<?php
				if ($skin_name === 'feather') {
?>
											<td>
												<input type="image" src="<?= $imgshow ?>"></td>
<?php
				} else {
?>
											<td style="background: #eee;">
												<input type="button" value="Go"
												       style="border: 1px solid #ddd; margin: 2px; background-color: #ced">
											</td>
<?php
				}
?>
									</tr>
								</table>
							</form>
<?php
			}
		}

?>

					</td>
				</tr>
			</table>
			</td></tr></table>
			<br/>
		</div>
<?php
	}

	function getInheritVar()
	{
		$v = array("frm_o_o", "frm_o_cname", 'frm_action', 'frm_sortby', 'frm_sortdir', 'frm_searchstring', "frm_selectshowbase", "frm_consumedlogin");

		return $v;
	}

	function getCurrentInheritVar()
	{
		$inherit_var = $this->getInheritVar();

		foreach ($inherit_var as $v) {
			if (isset($this->__http_vars[$v])) {
				$refreshpost[$v] = $this->__http_vars[$v];
			}
		}

		return $refreshpost;
	}

	function print_list_submit($class, $blist, $uniquename)
	{
		$rclass = $class;

?>

		<table height="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse;">
			<tr align="left">

<?php
		foreach ((array)$blist as $b) {
			$this->print_list_submit_middle($b, $uniquename);
		}

		$refreshpost = $this->getCurrentInheritVar();
		$refreshpost['frm_list_refresh'] = 'yes';
		$url = $this->get_get_from_post(null, $refreshpost);

		$refresh = create_simpleObject(array('url' => "display.php?$url", 'purl' => "/display.php?frm_action=refresh&frm_o_cname=ffile", 'target' => ''));

		$this->print_list_submit_middle(array($refresh, 1), $uniquename);

		if (exec_class_method($rclass, "isHardRefresh")) {
			$hardrefresh = create_simpleObject(array('url' => "display.php?$url&frm_hardrefresh=yes", 'purl' => "/display.php?frm_action=hardrefresh&frm_o_cname=ffile", 'target' => ''));
			$this->print_list_submit_middle(array($hardrefresh, 1), $uniquename);
		}
?>

			</tr>
		</table>

<?php
	}

	function print_list_submit_middle($button, $uniquename)
	{
		global $gbl, $sgbl, $login, $ghtml;

		$iconpath = get_image_path();

		$url = $button[0];
		$purl = null;

		if ($this->is_special_url($url)) {
			$purl = $url->purl;
			$target = $url->target;
			$url = $url->url;
		}

		$ac_descr = $this->getActionDetails($url, $purl, $iconpath, $path, $post, $_t_file, $_t_name, $_t_image, $__t_identity);

		$descr = $ac_descr;
		$var = $_t_name;
		$file = $_t_file;

		$help = $this->get_full_help($descr['help']);

		$name = $descr[2];
		$form_name = str_replace(" ", "_", $file . "_" . $var);

		$form_name = $this->createEncForm_name($form_name);

		$image = $_t_image;
		$this->save_non_existant_image($image);

		$noselect = (isset($button[1]) && $button[1]) ? 1 : 0;
		$doconfirm = (isset($button[3]) && $button[3]) ? 1 : 0;
		$imgbtnsep = $login->getSkinDir() . "/images/btn_sep.gif";

?>

		<td width=10></td>
		<td align=center valign=bottom>

			<form name="form<?= $form_name ?>" action="<?= $path ?>">
<?php

		$this->print_input_vars($post);

		if (!$noselect) {
?>
					<input id="accountsel" name="frm_accountselect" type="hidden">
<?php
		}

		if (!isset($button[2])) {
			$button[2] = null;
		}

		if (!$button[2]) {
?>
				<span title="<?= $help ?>"> <a class=button
				                               href="javascript:storevalue(document.form<?= $form_name ?>,'accountsel','ckbox<?= $uniquename ?>',ckcount<?= $uniquename ?>, <?= $noselect ?>, <?= $doconfirm ?>)">
<?php

		}

		if (!$sgbl->isBlackBackground()) {
?>
							<img height="15" width="15" src="<?= $image ?>">
<?php
			$colorstring = null;
		} else {
			$colorstring = "color=#999999";
		}
?>
						<br/> <span <?= $colorstring ?> class="lightandthin"><?= $name ?></span>
<?php
		if (!$button[2]) {
?>
					</a> </span>
<?php
		}
?>
			</form>
		</td>
		<td width="10"> &nbsp; </td>

<?php
	}

	function get_filter_var()
	{
		global $gbl, $sgbl, $login, $ghtml;

		$post['frm_hpfilter'] = $login->getHPFilter();
		$string = $this->get_get_from_post(null, $post);

		return $string;
	}

	function get_help_url()
	{
		// TODO: Remove not used function
	}

	function get_session_vars()
	{
		global $gbl, $sgbl, $login, $ghtml;

		$this->__http_vars = $login->c_session->http_vars;
	}

	function print_defintion($value)
	{
		// TODO: Remove not used function
	}

	function get_action_or_display_help($help, $flag)
	{
		// TODO: Remove not used function
	}

	function print_jscript_source($jsource)
	{
?>

		<script language="javascript" src="<?= $jsource ?>"></script>

<?php
	}

	function print_css_source($csource)
	{
?>

		<link href="<?= $csource ?>" rel="stylesheet" type="text/css">

<?php
	}

	function printNavHistMenu()
	{
		global $gbl, $sgbl, $login, $ghtml;

?>

		<script>
<?php
			
			if ($login->getSpecialObject('sp_specialplay')->isOn('ultra_navig')) {
				foreach ((array)$gbl->__navigmenu as $n => $v) {
					create_navmenu($n, $v[0], $v[1]);
				}
			}
?>

			window.histlist = new Menu('histlist', 210);
<?php
			
			if (isset($gbl->__histlist)) {
				end($gbl->__histlist);
				$this->print_pmenu('histlist', key($gbl->__histlist), null, null, true);
			
				while (($val = prev($gbl->__histlist))) {
					$this->print_pmenu('histlist', key($gbl->__histlist), null, null, true);
				}
			
				reset($gbl->__histlist);
			} else {
				$this->print_pmenu('histlist', '__blank|No History');
			}
?>

		</script>
<?php
	}

	function print_refresh_key()
	{
		global $gbl, $sgbl, $login, $ghtml;

?>

		<script>
			document.onkeydown = function (e) {
				e = e || window.event;

				if (e.keyCode == 27) {
					var b = document.getElementById('showimage');

					if (b) {
						b.style.visibility = 'hidden';
					}

					var c = document.getElementById('esmessage');

					if (c) {
						c.style.visibility = 'hidden';
					}
				}

				return true;
			}
		</script>

<?php
		if ($sgbl->dbg <= 0) {
			return;
		}
?>

		<script>
			document.onkeyup = function (e) {
				e = e || window.event;

				if (e.keyCode == 86 && e.ctrlKey) {
					top.mainframe.window.location.reload();
				}

				return true;
			}
		</script>
<?php
	}

	function print_include_jscript($header = null)
	{
		global $gbl, $sgbl, $login;

		// MR -- move to do_display_init()
		// print('<meta http-equiv="expires" content="Wed, 26 Feb 1997 08:21:57 GMT">');

		$this->print_refresh_key();
		$this->print_jscript_source("/theme/js/lxa.js");
		$this->print_jscript_source("/theme/js/helptext.js");
		$this->print_jscript_source("/theme/js/preop.js");

		if (!$login->getSpecialObject('sp_specialplay')->isOn('enable_ajax') && ($header !== 'left_panel')) {
			//
		} else {
			$this->print_jscript_source("/theme/extjs/js/yui-utilities.js");
			$this->print_jscript_source("/theme/extjs/js/ext-yui-adapter.js");
			$this->print_jscript_source("/theme/extjs/js/ext-all.js");
			$this->print_jscript_source("/theme/js/dragdrop.js");
		}

		$this->print_jscript_source("/theme/js/drag.js");

		$func = null;

		if (!$header) {
			$func = "onLoad='lxLoadBody();'";
		}

		if (!$header) {
			$descr = $this->getActionDescr($_SERVER['PHP_SELF'], $this->__http_vars, $class, $var, $identity);
			$help = $this->get_full_help($descr[2]);
			$help = $this->get_action_or_display_help($help, "display");
			$this->print_defintion($help);
		}

		$skin = $login->getSkinDir();
		$css = "{$skin}/css/style.css";

		if (!lfile_exists(getreal($css))) {
			$css = "/theme/css/base.css";
		}

		$this->print_css_source("/theme/css/common.css");
		$this->print_css_source($css);
		$this->print_css_source("/theme/css/ext-all.css");

		$l = @ getdate();
		$hours = $l['hours'];
		$minutes = $l['minutes'];

		if ($header === 'left_panel') {
?>
		<script type="text/javascript">
			var gl_helpUrl;
			gl_tDate = new Date();
			var clockTimeZoneMinutes = <?=$l['minutes']?> -gl_tDate.getMinutes();
			var clockTimeZoneHours = <?=$l['hours']?> -gl_tDate.getHours();

			function program_help() {
				window.open(top.mainframe.jsFindHelpUrl());
			}

			function lxCallEnd() {
			}

		</script>
<?php
		}
?>
		<script>
			function jsFindFilterVar() {
				gl_filtervar = '<?=$this->get_filter_var()?>';

				return gl_filtervar;
			}

			function jsFindHelpUrl() {
				if (document.all || document.getElementById) {
					gl_helpUrl = '<?=$this->get_help_url()?>';

					return gl_helpUrl;
				}
			}

			function lxLoadBody() {
				if (top.topframe && typeof top.topframe.changeLogo == 'function') {
					top.topframe.changeLogo(0);
				}

				changeContent('help', 'helparea');
			}
		</script>

		<script>
			var gl_skin_directory = '<?=$login->getSkinDir();?>';
		</script>
<?php
		if ($header === 'left_panel') {
?>

		<script>lxCallEnd();</script>
<?php
		} // [FIXME] This call a lxCallEnd a empty function
?>

<?php
	}

	function print_refresh()
	{
?>
		<script> top.mainframe.window.location.reload(); </script>
<?php
	}

	function print_redirect_back($message, $variable, $value = null)
	{
		global $gbl, $sgbl, $login;

		$vstring = null;

		if ($value) {
			$value = htmlspecialchars($value);
			$vstring = "&frm_m_emessage_data=$value";
		}

		$parm = "frm_emessage=$message$vstring";

		if ($variable) {
			$parm .= "&frm_ev_list=$variable";
		}

		$last_page = $gbl->getSessionV("lx_http_referer");

		if (!$last_page) {
			$last_page = "/display.php?frm_action=show";
		}

		$current_url = $this->get_get_from_current_post(null);

		if ($last_page === $current_url) {
			log_log("redirect_error", "$last_page is same as the current url...\n");
			$last_page = "/display.php?frm_action=show";
		}

		$this->get_post_from_get($last_page, $path, $post);

		$get = $this->get_get_from_post(array("frm_ev_list"), $post);

		$this->print_redirect("$path?$get&$parm");
	}

	function print_redirect_back_success($message, $variable, $value = null)
	{
		global $gbl, $sgbl, $login;

		$vstring = null;

		if ($value) {
			$value = htmlspecialchars($value);
			$vstring = "&frm_m_smessage_data=$value";
		}

		$parm = "frm_smessage=$message$vstring";

		if ($variable) {
			$parm .= "&frm_ev_list=$variable";
		}

		$last_page = $gbl->getSessionV("lx_http_referer");

		if (!$last_page) {
			$last_page = "/display.php?frm_action=show";
		}

		$this->get_post_from_get($last_page, $path, $post);

		$get = $this->get_get_from_post(array("frm_ev_list"), $post);

		$this->print_redirect("$path?$get&$parm");
	}

	function print_redirect_to($red)
	{
		global $gbl, $sgbl, $login, $ghtml;

		if ($gbl->isetSessionV("redirect_to")) {
			$this->print_redirect($gbl->getSessionV("redirect_to"));
			$gbl->unsetSessionV("redirect_to");

			return;
		}

		$this->print_redirect($red);
	}

	function print_redirect($redirect_url, $windowurl = null)
	{
		global $gbl, $sgbl;

		$current_url = $this->get_get_from_current_post(null);

		if (ifSplashScreen() || $windowurl) {
			dprint("<br /> <br /> Redirect called with splash <br /> ");
			dprint(" <b><br /> <br />  Click <a href=\"$redirect_url\"><b> xhere to go Continue. </a> </b> \n");

			if ($sgbl->dbg < 0 || (isset($gbl->__no_debug_redirect) && $gbl->__no_debug_redirect)) {
?>
			<head>
				<meta http-equiv="expires" content="Wed, 26 Feb 1997 08:21:57 GMT">
				<META HTTP-EQUIV="Refresh" CONTENT="0;URL=<?= $redirect_url ?>">
<?php
				if ($windowurl) {
?>
				<script>
					window.open('<?=$windowurl?>');
				</script>
				</head>
<?php
				}
			} else {
				if ($windowurl) {
?>
				<script>
					window.open('<?=$windowurl?>');
				</script>

<?php
				}
			}

			exit(0);
		}

		if (($sgbl->dbg > 0) && !(isset($gbl->__no_debug_redirect) && $gbl->__no_debug_redirect)) {
			$cont = ob_get_contents();

			if ($gbl->__fvar_dont_redirect || csa($cont, "Notice") || csa($cont, "Warning") || csa($cont, "Parse error")) {
				print_time('full', "Page Generation Took: ");
?>

				<b><br/> <br/> Looks Like there are some errors... Or Been asked not to redirect Not redirecting...
					<br/>
					Click <a href="<?= $redirect_url ?>"> xHere to go there Anyways.</b>
<?php
			} else {
				print_time('full', "Page Generation Took: ");
?>

				<b><br/> <br/> Looks Like there are some errors... Or Been asked not to redirect Not redirecting...
					<br/>
					Click <a href="<?= $redirect_url ?>"> xHere to go there Anyways.</b>
<?php
			}
		} else {
			header("Location:$redirect_url");
?>

			<head>
				<meta http-equiv="expires" content="Wed, 26 Feb 1997 08:21:57 GMT">
				<META HTTP-EQUIV="Refresh" CONTENT="0;URL=<?= $redirect_url ?>">
			</head>
<?php
		}

		exit(0);
	}

	function print_redirect_left_panel($redirect_url)
	{
?>

		<script> top.leftframe.location = "<?=$redirect_url?>"; </script>
<?php

		exit(1);
	}

	function print_redirect_self($redirect_url)
	{
?>

		<script> top.location = "<?=$redirect_url?>"; </script>
<?php
		exit(1);
	}

	function print_table_header($heading)
	{
		global $gbl, $sgbl, $login;

?>

		<br/><br/>
		<table cellpadding="0" cellspacing="0" border="0" width="20%">
			<tr>
				<td bgcolor="<?= $login->skin->table_title_color ?>"><b><?= $heading ?></b>
				</td>
			</tr>
			<tr>
				<td bgcolor="#A5C7E7"></td>
			</tr>
		</table>

<?php
	}

	function get_full_help($help, $name = null)
	{
		global $gbl, $sgbl, $login, $ghtml;

		$classdesc = null;

		if (!$help) {
			return null;
		}

		if ($name) {
			$val = " <span style='color:blue'> " . $name . "</span>";
			if (preg_match("/\[%s\]/", $help)) {
				$help = str_replace("[%s]", $val, $help);
			} else {
				if ($help[strlen($help) - 1] != '.') {
					$help = "$help for $val.";
				}
			}
			if ($classdesc) {
				$tmp = array(&$help);
				$this->fix_variable_overload($tmp, $classdesc[1]);

			}

			return $help;
		}

		return $help;
	}


	function printGraphSelect($list)
	{
		global $gbl, $sgbl, $login, $ghtml;

		if (!$list) {
			return;
		}

		$cgi_o_o = $this->frm_o_o;

		$oldname = $this->frm_c_graph_time;

		$subactionstr = null;

		if ($this->frm_subaction) {
			$subactionstr = "<input type=hidden name=frm_subaction value={$this->frm_subaction}>";
		}

		$cnamestr = null;

		if ($this->frm_o_cname) {
			$cnamestr = "<input type=hidden name=frm_o_cname value={$this->frm_o_cname}>";
		}

		$dttypestr = null;

		// This needs to be an array.
		if ($this->frm_dttype) {
			$dttypestr = "<input type=hidden name=frm_dttype[val] value={$this->frm_dttype['val']}>";
			$dttypestr = "<input type=hidden name=frm_dttype[var] value={$this->frm_dttype['var']}>";
		}

		$frm_action = $this->frm_action;
		$filter = null;
		$hpfilter = $login->getHPFilter();

		if ($hpfilter) {
			$filter['frm_hpfilter'] = $hpfilter;
		}
?>

		<table width=100%>
			<tr>
				<td width=10></td>
				<td align=left>
					<form name="graphselectjump" method="<?= $sgbl->method; ?>" action="display.php"
					      accept-charset="utf-8">

<?php
		foreach ($cgi_o_o as $k => $v) {
?>

							<input type=hidden name='frm_o_o[<?= $k ?>][class]' value=<?= $v['class'] ?>>
<?php
			if (isset($v['nname'])) {
?>

								<input type=hidden name='frm_o_o[<?= $k ?>][nname]' value=<?= $v['nname'] ?>>
<?php
			}
		}
?>

						<input type=hidden name=frm_action value=<?= $frm_action ?>>
						<?= $subactionstr ?>
						<?= $cnamestr ?>
						<?= $dttypestr ?>
						<?= $this->print_input_vars($filter) ?>
						Period <select class=textbox onChange='document.graphselectjump.submit()'
						               name='frm_c_graph_time'>
<?php
		foreach ($list as $k => $l) {
			$sssl = '';

			if ($k == $oldname) {
				$sssl = 'SELECTED';
			}

?>
								<option <?= $sssl ?> value="<?= $k ?>"><?= $l ?></option>
<?php
		}
?>

						</select>
					</form>
				</td>
			</tr>
		</table>
<?php
	}

	function printShowSelectBox($list)
	{
		global $gbl, $sgbl, $login, $ghtml;

		$skin_color = $login->getSkinColor();

		if (!$list) {
			return;
		}

		$cgi_o_o = $this->frm_o_o;

		$filteropacitystringspan = null;
		$filteropacitystringspanend = null;
		$filteropacitystring = null;

		if ($sgbl->isBlackBackground()) {
			$filteropacitystringspanend = "</span>";
			$filteropacitystringspan = "<span style='background:black'> ";
			$filteropacitystring = "style='background:black;color:#999999;FILTER:progid;-moz-opacity:0.5'";
		}


		$num = count($cgi_o_o) - 1;

		while ($num >= 0) {
			$class = $cgi_o_o[$num]['class'];
			$desc = $this->get_class_description($class);
			if (isset($cgi_o_o[$num]['nname']) && !csa($desc[0], 'P')) {
				break;
			}
			$num--;
		}

		if ($num < 0) {
			return;
		}

		$oldname = $cgi_o_o[$num]['nname'];

		$subactionstr = null;

		if ($this->frm_subaction) {
			$subactionstr = "<input type=hidden name=frm_subaction value={$this->frm_subaction}>\n";
		}

		if ($this->frm_consumedlogin) {
			$subactionstr .= "<input type=hidden name=frm_consumedlogin value={$this->frm_consumedlogin}>";
		}

		$cnamestr = null;

		if ($this->frm_o_cname) {
			$cnamestr = "<input type=hidden name=frm_o_cname value={$this->frm_o_cname}>";
		}

		$dttypestr = null;

		// This needs to be an array.
		if ($this->frm_dttype) {
			$dttypestr = "<input type=hidden name=frm_dttype[val] value={$this->frm_dttype['val']}>";
			$dttypestr .= "<input type=hidden name=frm_dttype[var] value={$this->frm_dttype['var']}>";
		}

		$frm_action = $this->frm_action;
		$filter = null;

		$hpfilter = $login->getHPFilter();

		if ($hpfilter) {
			$filter['frm_hpfilter'] = $hpfilter;
		}

		$skindir = $login->getSkinDir();
		$forecolorstring = null;

		if ($sgbl->isBlackBackground()) {
			$forecolorstring = "color=gray";
		}

		$ststring = null;

		if ($sgbl->isBlackBackground()) {
			$ststring = "style='background:black;color:gray'";
		}

		if (!$sgbl->isBlackBackground()) {
			$col = "{$skindir}/images/expand.gif";
		}
?>
		<div style="background: #<?= $skin_color ?> url(<?= $col ?>); height: 24px; margin: 0 25px;">
			<div style="float:left; padding: 4px"><span <?= $forecolorstring ?> style='font-weight:bold;'>&nbsp;Switch To Another&nbsp;</span>
			</div>
			<div style="float:left;">
				<form name="topjumpselect" method="<?= $sgbl->method ?>" action='/display.php' accept-charset="utf-8">
<?php
		foreach ($cgi_o_o as $k => $v) {
?>

						<input type="hidden" name="frm_o_o[<?= $k ?>][class]" value="<?= $v['class'] ?>"/>
<?php
			if ($k != $num && isset($v['nname'])) {
?>

							<input type="hidden" name="frm_o_o[<?= $k ?>][nname]" value="<?= $v['nname'] ?>"/>
<?php

			}
		}
?>

					<input type="hidden" name="frm_action" value="<?= $frm_action ?>"/>
					<?= $subactionstr ?>

					<?= $cnamestr ?>

					<?= $dttypestr ?>

					<?= $this->print_input_vars($filter) ?>

					<?= $filteropacitystringspan ?>

					<select
						style="border: 1px solid #aaaaaa; margin: 2px" <?= $filteropacitystring ?> <?= $ststring ?>
						class="textbox"
						onChange='document.topjumpselect.submit()' name='frm_o_o[<?= $num ?>][nname]'>

<?php
		foreach ($list as $k => $l) {
			$tdisp = $l->getId();

			if ($sgbl->isDebug()) {
				$tdisp = $l->getClName();
			}

			$selected = '';

			if ($k == $oldname) {
				$selected = 'SELECTED';
			}
?>

							<option <?= $selected ?> value="<?= $k ?>"><?= $tdisp ?></option>
<?php
		}
?>

					</select> <?= $filteropacitystringspan ?>
				</form>
			</div>
		</div>
		<br/>
<?php
	}

	function getActionDescr($path, $post, &$class, &$var, &$nname)
	{
		global $gbl, $sgbl, $login, $ghtml;

		$laclass = $suclass = null;

		if (isset($post['frm_o_cname'])) {
			$laclass = $post['frm_o_cname'];
		}

		if (isset($post['frm_o_o']) && $post['frm_o_o']) {
			$p = $post['frm_o_o'];
			$suclass = $p[count($p) - 1]['class'];

			$p = $post['frm_o_o'];

			for ($i = count($p) - 1; $i >= 0; $i--) {
				if (isset($p[$i]['nname'])) {
					$nname = exec_class_method($suclass, 'getClassId', $p[$i]['nname']);

					break;
				}
			}
		} else {
			$nname = $login->nname;
		}

		$name = "<span style='color:blue'> $nname </span>";

		if (!$laclass && !$suclass) {
			$laclass = lget_class($login);
			$suclass = lget_class($login);
		}

		$var = null;

		if (isset($post['frm_action'])) {
			$var = strtolower($post["frm_action"]);
		}

		if ($var === "delete") {
			$class = $laclass;

			return array("", "", "Delete", "", 'desc' => "Delete", 'help' => $login->getKeywordUc('delete'), "{$login->getKeywordUc('delete')} $laclass");
		}

		if ($var === "list") {
			$class = $laclass;
			$desc = get_classvar_description($class, "__acdesc_list");

			if (!$desc) {

				$desc = get_classvar_description($laclass);
				$descr = $desc[2];
				$descri = get_plural($desc[2]);
				$help = "{$login->getKeywordUc('list')} $descri";
			} else {
				$descri = $desc[2];
				$help = $desc[2];
			}

			if (isset($post['frm_filter']['show'])) {
				$dvar = 'filter_show_' . $post['frm_filter']['show'];
				$desc = get_classvar_description($laclass, $dvar);
				$descri = $desc[2];
				$var = "list_" . '_filter_show_' . $post['frm_filter']['show'];
			}

			if (isset($post['frm_filter']['view'])) {
				$dvar = 'filter_view_' . $post['frm_filter']['view'];
				$desc = get_classvar_description($laclass, $dvar);
				$descri = $desc[2];
				$var = "list_" . '_filter_view_' . $post['frm_filter']['view'];
			}

			return array("", "", $descri, 'desc' => $descri, $help, 'help' => $desc['help']);
		}

		if ($var === "searchform") {
			$class = $laclass;
			$desc = get_classvar_description($laclass);
			$descr = $desc[2];
			$descri = get_plural($desc[2]);

			if (isset($post['frm_hpfilter'])) {
				$dvar = 'filter_show_' . $post['frm_hpfilter']['show'];
				$desc = get_classvar_description($laclass, $dvar);
				$descri = $desc[2];
				$var = "list_" . '_filter_show_' . $post['frm_hpfilter']['show'];
			}

			return array("", "", "$descr Search", 'desc' => "$descr Search", 'help' => "$descr Search", "Search $descr");
		}

		if ($var === "addform") {
			$class = $laclass;

			if (isset($post['frm_dttype'])) {
				$subvar = $post['frm_dttype']['var'];
				$sub = $post['frm_dttype']['val'];
			} else {
				$sub = null;
			}

			if ($sub) {
				$desc = get_classvar_description($laclass, "{$subvar}_v_$sub");
			} else {
				$desc = get_classvar_description($laclass);
			}

			if ($sub) {
				$var = $sub . "_" . $var;
			}

			$descr = $desc[2];

			return array("", "", "Add $descr", 'desc' => "Add $descr", 'help' => "{$login->getKeywordUc('add')} $descr", "{$login->getKeywordUc('add')} $descr");
		}

		if ($var === "updateform" || $var === "update") {
			if (isset($laclass)) {
				$class = $laclass;
			} else {
				$class = $suclass;
			}

			if (isset($post['frm_subaction'])) {
				$sub = "_" . $post['frm_subaction'];
			} else {
				$sub = null;
			}

			$var = $var . $sub;
			$desc = get_classvar_description($class, "__acdesc_update" . $sub);

			if ($desc) {
				if (csa($desc[2], "[%s]")) {
					$desc[2] = str_replace("[%s]", $name, $desc[3]);
				} else {
					$desc[2] .= "";
				}

				$desc['desc'] = $desc[2];

				return $desc;
			} else {
				$descr = "Update $sub";
			}

			return array("", '', $descr, 'desc' => $descr, 'help' => $desc['help']);
		}

		if ($var === "show" || $var === 'graph') {
			$realvar = $var;
			$class = $suclass;

			if (isset($post['frm_subaction'])) {
				$sub = "_" . $post['frm_subaction'];
			} else {
				$sub = null;
			}

			$var = $var . $sub;
			$desc = get_classvar_description($suclass, "__acdesc_$realvar" . $sub);

			if (!$desc) {
				$desc = get_classvar_description($suclass);

				if (csa($desc[0], "N")) {
					$count = count($post['frm_o_o']) - 1;
					$var .= "_nn_" . fix_nname_to_be_variable($post['frm_o_o'][$count]['nname']);
				}
				$descr = "{$desc[2]} {$login->getKeywordUc('home')} ";
				$help = "{$login->getKeywordUc('show')} {$desc[2]} details";
			} else {
				$descr = $desc[2];
				$help = $desc[2];
			}

			$desc = get_classvar_description($suclass);

			if (csa($desc[0], "N")) {
				$count = count($post['frm_o_o']) - 1;
			}

			return array("", '', $descr, 'desc' => $descr, 'help' => $help);
		}

		$descvar = "__ac_desc_" . $class . "_" . $var;

		$dvar = ucfirst($var);

		return array("", '', $dvar, 'desc' => $dvar, 'help' => $dvar);
	}

	function getActionDetails($url, $psuedourl, $buttonpath, &$path, &$post, &$class, &$name, &$image, &$identity)
	{
		global $gbl, $sgbl, $login, $ghtml;

		if (!$psuedourl) {
			$psuedourl = $url;
		}

		$this->get_post_from_get($psuedourl, $path, $post);

		$descr = $this->getActionDescr($path, $post, $class, $name, $identity);
		$descr['desc'] = $descr[2];
		$image = $this->get_image($buttonpath, $class, $name, ".gif");

		$this->get_post_from_get($url, $path, $post);

		return $descr;
	}

	function print_div_for_divbutton($key, $imgflag, $linkflag, $formname, $name, $imagesrc, $descr)
	{
		global $gbl, $sgbl, $login;

		$button_type = $login->getSpecialObject('sp_specialplay')->button_type;

		$skincolor = $login->getSkinColor();
		$shevron = "{$login->getSkinDir()}/images/shevron_line.gif";

		$help = $this->get_full_help($descr[2]);
		$help = $this->get_action_or_display_help($help, "action");

		$dummyimg = get_image_path() . "/untitled.gif";

		$help = $descr['help'];

		$selectcolor = '#abc';
		$blackbordercolor = 'white';
		$bgcolorstring = null;
		
		if ($button_type === 'reverse-font') {
			$forecolorstring = "color:#fff";
		} else {
			$forecolorstring = "color:#002244";
		}

		if ($sgbl->isBlackBackground()) {
			$bgcolorstring = "bgcolor=#000";
			$forecolorstring = "color:#999999";
			$selectcolor = '#444444';
			$skincolor = '#000000';
			$blackbordercolor = '#000000';
			$imgflag = false;
		}

		if ($linkflag) {
			$displayvar = "<span style='$forecolorstring' class='icontextlink' id='aaid_$formname' " .
				"href=\"javascript:document.form_$formname.submit()\" onmouseover=\" style.textDecoration='underline';\" " .
				"onmouseout=\"style.textDecoration='none'\"> $descr[2] </span>";
			$onclickvar = "onClick=\"document.form_$formname.submit()\"";
			$alt = $help;
		} else {
			$displayvar = "<span title=\"You don't have permission\" class='icontextlink'>{$descr[2]} (disabled)</span>";
			$alt = "You dont have permission";
			$onclickvar = null;
		}

		$idvar = null;

		if ($login->getSpecialObject('sp_specialplay')->isOn('enable_ajax') && csb($key, "__v_dialog")) {
			$onclickvar = null;
			$idvar = "id=$key-comment";
		}

		if ($imgflag) {
			if ($button_type === 'image') {
				if ($linkflag) {
					$imgvar = "<img width='32' height='32' class='icontextlink' " .
						"onMouseOver=\"getElementById('aaid_$formname').style.textDecoration='underline'; \" " .
						"onMouseOut=\"getElementById('aaid_$formname').style.textDecoration='none'; \" src=\"$imagesrc\">";
				} else {
					$imgvar = "<img width='32' height='32' class='icontextlink' src=\"$imagesrc\">";
				}

				$txtalign = "text-align: center;";
			} else {
				$b = $this->get_metro_color();

				$a = explode("_", $formname);

				if (strpos($a[0], 'client') !== false) {
					if ($a[2] === 'all') {
						$x = "f134";
					} elseif ($a[2] === 'disable') {
						$x = "f5d1";
					} else {
						$x = "f161";
					}
				} elseif ($a[0] === 'auxiliary') {
					$x = "f133";
				} elseif ($a[0] === 'resourceplan') {
					$x = "f4c1";
				} elseif ($a[0] === 'custombutton') {
					$x = "f43d";
				} elseif ($a[0] === 'cron') {
					$x = "f0a1";
				} elseif ($a[0] === 'traceroute') {
					$x = "f0c5";
				} elseif ($a[0] === 'watchdog') {
					$x = "f67f";
				} elseif ($a[0] === 'lxguard') {
					$x = "f02e";
				} elseif ($a[0] === 'driver') {
					$x = "f09a";
				} elseif (strpos($a[0], 'server') !== false) {
					$x = "f027";
				} elseif ($a[0] === 'service') {
					$x = "1f4bb";
				} elseif ($a[0] === 'process') {
					$x = "1f4bb";
				} elseif ($a[0] === 'component') {
					$x = "1f4bb";
				} elseif ($a[0] === 'general') {
					$x = "f781";
				} elseif ($a[0] === 'genlist') {
					$x = "f781";
				} elseif (strpos($a[0], 'sp') !== false) {
					$x = "f159";
				} elseif (strpos($a[0], 'domain') !== false) {
					$x = "f052";
				} elseif (strpos($a[0], 'web') !== false) {
					$x = "f01c";
				} elseif (strpos($a[0], 'ftp') !== false) {
					$x = "f029";
				} elseif (strpos($a[0], 'php') !== false) {
					$x = "f09c";
				} elseif (strpos($a[0], 'mysql') !== false) {
					$x = "f5fc";
				} elseif (strpos($a[0], 'db') !== false) {
					$x = "f00b";
				} elseif (strpos($a[0], 'ffile') !== false) {
					$x = "f095";
				} elseif (strpos($a[0], 'ipaddr') !== false) {
					$x = "f08b";
				} elseif (strpos($a[0], 'ssl') !== false) {
					$x = "f04f";
				} elseif (strpos($a[0], 'ticket') !== false) {
					$x = "f3dc";
				} elseif (strpos($a[0], 'message') !== false) {
					$x = "f676";
				} elseif (strpos($a[0], 'notification') !== false) {
					$x = "f145";
				} elseif (strpos($a[0], 'dirindexlist') !== false) {
					$x = "f41a";
				} elseif (strpos($a[0], 'dirprotect') !== false) {
					$x = "f04d";
				} elseif (strpos($a[0], 'redirect') !== false) {
					$x = "f054";
				} elseif (strpos($a[0], 'dns') !== false) {
					$x = "f409";
				} elseif (strpos($a[0], 'backup') !== false) {
					$x = "f05f";
				} elseif (strpos($a[0], 'ssh') !== false) {
					$x = "f04e";
				} elseif (strpos($a[0], 'block') !== false) {
					$x = "f313";
				} elseif (strpos($a[0], 'hostdeny') !== false) {
					$x = "f313";
				} elseif (strpos($a[0], 'log') !== false) {
					$x = "f0c1";
				} elseif (strpos($a[0], 'utmp') !== false) {
					$x = "f765";
				} elseif (strpos($a[0], 'mail') !== false) {
					$x = "f136";
				} elseif ($a[0] === 'autoresponder') {
					$x = "2709";
				} elseif ($a[0] === 'forward') {
					$x = "2709";
				} elseif ($a[0] === 'general') {
					$x = "2353";
				} else {
					$x = "1f4d6";
				}

				if ($button_type === 'reverse-font') {
					$imgvar = "<span title='{$a[0]}' class='if32' style='color: #fff;'>&#x{$x};</span>";
				} else {
					$imgvar = "<span title='{$a[0]}' class='if32' style='color: {$b[0]};'>&#x{$x};</span>";
				}

				if ($a[1] === 'show') {
					$x = "f0d5";
				} elseif ($a[1] === 'list') {
					$x = "f111";
				} elseif ($a[1] === 'updateform') {
					$x = "f47c";
				} elseif ($a[1] === 'addform') {
					$x = "f1b2";
				} else {
					$x = "f04a";
				}

				if ($button_type === 'reverse-font') {
					$imgvar .= "<span title='{$a[1]}' class='if16' style='color: #ccc'>&#x{$x};</span>";
					$txtalign = "display: table-cell; vertical-align: bottom; padding-bottom: 3px;";
				} else {
					$imgvar .= "<span title='{$a[1]}' class='if16' style='color: {$b[1]};'>&#x{$x};</span>";
					$txtalign = "text-align: center;";
				}
			}
		} else {
			$imgvar = null;
		}

?>

		<div <?= $idvar ?>  <?= $onclickvar ?> style='cursor: pointer; width: 90px; height: 90px; padding: 1px; margin: 1px;'
			onmouseover="getElementById('aaid_<?= $formname ?>').style.textDecoration='none'; this.style.backgroundColor='<?= $selectcolor ?>';"
			onmouseout="this.style.backgroundColor=''; getElementById('aaid_<?= $formname ?>').style.textDecoration='none'">

			<div style="margin: 1px auto; height: 40px; text-align: center;"><span title='<?= $alt ?>'><?= $imgvar ?></span></div>
			<div style="margin: 1px auto; height: 50px; <?= $txtalign ?>"><span title='<?= $alt ?>'><?= $displayvar ?></span></div>
		</div>
<?php
	}

	function get_metro_color()
	{
		// MR -- metro color from http://flatuicolors.com/
	/*
		$c = array('#1abc9c', '#40d47e', '#3498db', '#9b59b6', '#34495e',
			'#16a085', '#27ae60', '#2980b9', '#8e44ad', '#2c3e50',
			'#f1c40f', '#e67e22', '#e74c3c', '#ecf0f1', '#95a5a6',
			'#f39c12', '#d35400', '#c0392b', '#bdc3c7', '#7f8c8d');
	*/
		$c = array('#1abc9c', '#40d47e', '#3498db', '#9b59b6', '#34495e',
			'#16a085', '#27ae60', '#2980b9', '#8e44ad', '#2c3e50',
			'#f1c40f', '#e67e22', '#e74c3c', '#95a5a6',
			'#f39c12', '#d35400', '#c0392b', '#bdc3c7', '#7f8c8d');

		$i = count($c) - 1;
		$r = rand(0, $i);
		$b = $c[$r];

		$ret[0] = $b;

		$d = $r + 2;

		if ($d > $i) { $d = $d - $i; }

		$b = $c[$r + 2];

		$ret[1] = $b;

		return $ret;
	}

	function createEncForm_name($name)
	{
		global $gbl, $sgbl;

		return $name;

		if ($sgbl->dbg > 0) {
			return $name;
		}

		$name = str_replace("_", "", $name);
		$name = str_replace("php", "", $name);
		$name = str_replace("a", "z", $name);
		$name = str_replace("e", "r", $name);
		$name = str_replace("i", "x", $name);
		$name = str_replace("s", "q", $name);
		$name = str_replace("o", "p", $name);
		$name = str_replace("r", "j", $name);

		return $name;
	}

	function resolve_int_ext(&$url, &$psuedourl, &$target)
	{
		if($this->is_special_url($url)) {
			if(isset($url->custom) && $url->custom) {
				$complete['url'] = $url->url;
				$complete['name'] = $url->name;
				$complete['bname'] = $url->bname;
				$target = $url->target;

				$url = $url->url;

				return $complete;
			} else {
				$nurl = $url->url;
				$psuedourl = $url->purl;
				$target = $url->target;
				$psuedourl = $this->getFullUrl($psuedourl);

				if(isset($url->__internal)) {
					$nurl = $this->getFullUrl($nurl);
				}

				$url = $nurl;
			}
		}
	}


	function print_toolbar()
	{
		$list = $this->get_favorite("ndskshortcut");

		foreach ((array)$list as $l) {
			if ($l['ttype'] === 'separator') {
?>

				<td nowrap width="20"></td>
<?php
				continue;
			}

?>

			<td valign="middle" align="left" width="5">
				<form>
<?php
			$l['ac_descr']['desc'] = "{$l['fullstr']} {$l['tag']}";
			$this->print_div_for_divbutton_on_header($l['url'], $l['target'], null, true, true, $l['url'], $l['__t_identity'], $l['_t_image'], $l['ac_descr']);
?>

				</form>
			</td>
<?php
		}
	}

	function print_div_button_on_header($type, $imgflag, $key, $url, $ddate = null)
	{
		global $gbl, $sgbl, $login, $ghtml;

		$icondir = get_image_path();

		$obj = $gbl->__c_object;
		$psuedourl = null;
		$target = null;

		$buttonpath = get_image_path();

		$linkflag = true;

		if (csa($key, "__var_")) {
			$privar = strfrom($key, "__var_");
			if (!$obj->checkButton($privar)) {
				$linkflag = false;
			}
		}

		$complete = $this->resolve_int_ext($url, $psuedourl, $target);

		if (!$target) {
			$target = "mainframe";
		}

		if ($complete) {
			$this->get_post_from_get($url, $path, $post);
			$descr = $this->getActionDescr($path, $post, $class, $name, $identity);
			$complete['name'] = str_replace($complete['name'], "<", "&lt;");
			$complete['name'] = str_replace($complete['name'], ">", "&gt;");
			$name = $complete['name'];
			$bname = $complete['bname'];
			$descr[1] = $complete['name'];
			$descr[2] = $complete['name'];

			$file = $class;

			if (lxfile_exists("theme/custom/$bname.gif")) {
				$image = "/theme/custom/$bname.gif";
			} else {
				$image = "$icondir/custom_button.gif";
			}

			$__t_identity = $identity;
		} else {
			$url = str_replace("[%s]", $obj->nname, $url);

			$descr = $this->getActionDetails($url, $psuedourl, $buttonpath, $path, $post, $file, $name, $image, $__t_identity);
		}

		$str = randomString(8);
		$form_name = $this->createEncForm_name("{$file}_{$name}_$str");
		$form_name = fix_nname_to_be_variable($form_name);

		if (csb($url, "http:/")) {
			$formmethod = "get";
		} else {
			$formmethod = $sgbl->method;
		}

		// Use get always. Only in forms should post be used.
		$formmethod = 'get';
?>

		<td valign="middle" align="left" width=5>
<?php

		if (csa($url, "javascript")) {
			$form_name = $url;
		}

		$this->print_div_for_divbutton_on_header($url, $target, $key, $imgflag, $linkflag, $form_name, $name, $image, $descr);
?>

		</td>
<?php
	}

	function print_div_for_divbutton_on_header($url, $target, $key, $imgflag, $linkflag, $formname, $name, $imagesrc, $descr)
	{
		global $gbl, $sgbl, $login;

		$skincolor = $login->getSkinColor();
		$shevron = "{$login->getSkinDir()}/images/shevron_line.gif";

		$help = $this->get_full_help($descr[2]);
		$help = $this->get_action_or_display_help($help, "action");

		$dummyimg = get_image_path() . "/untitled.gif";

		$help = $descr['desc'];

		if ($linkflag) {
			$displayvar = "<span style='color:#002244' class=icontextlink id=aaid_$formname " .
				"onmouseover=\"style.textDecoration='underline';\" onmouseout=\"style.textDecoration='none'\">" .
				"</span>";

			if (csa($formname, "javascript")) {
				$onclickvar = "onClick=\"$formname\"";
			} else {
				if ($target == 'mainframe') {
					$onclickvar = "onClick=\"top.mainframe.window.location='$url'\"";
				} else {
					$onclickvar = "onClick=\"top.window.open('$url')\"";
				}
			}

			$alt = $help;
		} else {
			$displayvar = "<span title=\"You don't have permission\" class=icontextlink>{$descr[2]} (disabled)</span>";
			$alt = "You dont have permission";
			$onclickvar = null;
		}

		$idvar = null;

		if ($imgflag) {
			if ($linkflag) {
				$imgvar = "<img width=15 height=15 class=icontextlink " .
					"onMouseOver=\"getElementById('aaid_$formname').style.textDecoration='underline'; \" " .
					"onMouseOut=\"getElementById('aaid_$formname').style.textDecoration='none'; \" src=\"$imagesrc\">";
			} else {
				$imgvar = "<img width=15 height=15 class=icontextlink src=\"$imagesrc\">";
			}

		} else {
			$imgvar = null;
		}
?>

		<span title='<?= $alt ?>'>
		<a target="<?= $target ?>" href="<?= $url ?>">
			<table <?= $idvar ?> style='border: 1px solid #<?= $skincolor ?>; cursor: pointer'
			                     onmouseover="getElementById('aaid_<?= $formname ?>').style.textDecoration='none'; this.style.backgroundColor='#fff'; this.style.border='1px solid #<?= $skincolor ?>';"
			                     onmouseout="this.style.border='1px solid #<?= $skincolor ?>'; this.style.backgroundColor=''; getElementById('aaid_<?= $formname ?>').style.textDecoration='none';"
			                     cellpadding=3 cellspacing=3 height=10 width=10 valign=top>
				<tr>
					<td valign=top align=center> <?= $imgvar ?> </td>
				</tr>
				<tr valign=top height=100%>
					<td width=10 align=center><span title='<?= $alt ?>'><?= $displayvar ?></span></td>
				</tr>
			</table>
		</a>
	</span>

<?php
	}

	function getUrlInfo($url)
	{
		$buttonpath = get_image_path();

		if ($this->is_special_url($url)) {
			$psuedourl = $url->purl;
			$url = $url->url;
		} else {
			$psuedourl = $url;
		}

		$descr = $this->getActionDetails($url, $psuedourl, $buttonpath, $path, $post, $file, $name, $image, $__t_identity);
		$ret['description'] = $descr;
		$ret['image'] = $image;

		return $ret;
	}

	function show_graph($maxval, $val, $info, $tabwidth = null, $unit = "MB", $type = "normal", $name = null, $varname = null)
	{
		global $gbl, $sgbl, $login, $ghtml;

		if ($sgbl->isBlackBackground()) {
			return;
		}

		if (!is_unlimited($maxval) && $maxval == 0) {
			return;
		}

		if ($tabwidth > 0 && $tabwidth != null) {
			$width = $tabwidth;
		} else {
			$width = 100;
		}

		$path = get_general_image_path() . "/icon/";

		$gwhite = $path . "g_white.gif";
		$gorange = $path . "g_orange.gif";
		$gyellow = $path . "g_yellow.gif";
		$ggreen = $path . "g_green.gif";
		$gred = $path . "g_red.gif";

		$percentage_val = 0;

		if (is_unlimited($maxval) || $maxval === 'Na') {
			$percentage_val = 0;
		} else {
			if ($maxval) {
				$percentage_val = $val / $maxval;
			}
		}

		$usedval = round($percentage_val * 100);

		$realval = $usedval;

		$usedval = min(110, $usedval);

		$quotaimg = null;

		if ($usedval > 90) {
			$quotaimg = $gred;
		}

		if ($usedval > 75 && $usedval <= 90) {
			$quotaimg = $gorange;
		}

		if ($usedval > 50 && $usedval <= 75) {
			$quotaimg = $gyellow;
		}

		if ($usedval >= 0 && $usedval <= 50) {
			$quotaimg = $ggreen;
		}

		$text = "<span class=last><span size=1></span></span>";
		$help = null;
		$alt = null;
		$maxval = Resource::privdisplay($varname, null, $maxval);
		$val = Resource::privdisplay($varname, null, $val);

		if ($type === "small") {
			$help = "<br /> <br /> <span style=color:blue>$name </span> uses $val $unit ($realval%) of $maxval";
			$alt = lx_strip_tags($help);
			$help = "<b> Message: </b>" . $help;
			$help = "onmouseover=\"changeContent('help',' $help')\" onmouseout=\"changeContent('help','helparea')\"";
		} else {
			$text = "<span class=last> <span size=1>$realval%</span></span>";
		}

		if ($info != null) {
?>

			<div style="width:50px; float:left"><b><?= $info ?></b></div>
<?php
		}

		// MR -- also need this process to fix title
		$alt = preg_replace("/_lxspan:([^:]*):([^:]*):/", "$2", $alt);
?>

		<div <?= $help ?> style="float: left">
			<div id="quotameter" class="smallroundedmodule lowquota">
				<div class="first">
					<span class="first"></span>
					<span class="last"></span>
				</div>
				<div>
							<span id="quotausagebar" title='<?= $alt ?>'>
								<span class="first"
								      style="background-image: url(<?= $quotaimg ?>); width:<?= $usedval ?>%;">
									<?= $text; ?>
								</span>
							</span>
				</div>
				<div class="last">
					<span class="first"></span>
					<span class="last"></span>
				</div>
			</div>
		</div>

<?php
	}

	function form_header($title)
	{
		global $gbl, $sgbl, $login, $ghtml;

		$imgheadleft = $login->getSkinDir() . "/images/top_lt.gif";
		$imgheadright = $login->getSkinDir() . "/images/top_rt.gif";
		$imgheadbg = $login->getSkinDir() . "/images/top_bg.gif";
		$imgtopline = $login->getSkinDir() . "/images/top_line.gif";

?>

		<table cellpadding=0 cellspacing=0 border=0 width=100%>
			<tr>
				<td width=60% valign=bottom>
					<table cellpadding=0 cellspacing=0 border=0 width=100%>
						<tr>
							<td width=100% height=2 background="<?= $imgtopline ?>"></td>
						</tr>
					</table>
				</td>
				<td align=right width=1%>
					<table cellpadding=0 cellspacing=0 border=0 width=100%>
						<tr>
							<td><img src="<?= $imgheadleft ?>"></td>
							<td nowrap width=100% background="<?= $imgheadbg ?>"><b><span
										style="color:#ffffff"><?= $title ?></span></b></td>
							<td><img src="<?= $imgheadright ?>"></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>

<?php
	}

	function form_footer()
	{
?>

		<table cellpadding=0 cellspacing=0 border=0 width=100%>
			<tr>
				<td height=2 bgcolor="#a5c7e7"></td>
			</tr>
		</table>

<?php
	}

	function getgroupvarselect($_multivarname)
	{
		global $gbl, $sgbl, $login, $ghtml;

		if (isset($gbl->__group_mode) && $gbl->__group_mode) {
			return "
		<td>
			<select class=\"textbox\" name=\"$_multivarname\">
				<option value=\"nochange\"> Dont Change </option>
				<option value=\"change\"> Change </option>
			</select>\n
			<br />
		</td> ";
		} else {
			return null;
		}
	}


	function print_fancy_select($class, $src, $dst)
	{
		$variablename = "frm_interface_template_c_{$class}_show_list";
		$ts_name = "ts_$variablename";
		$ts_name2 = "ts_{$variablename}2";
		$variable_description = "";
		$dstname = "destination";

		$form = "fancy_select";

		$stylestring = "style='width: 300;' size=20";
		$iconpath = get_image_path();
?>

		<form name="<?= $form ?>" action="/display.php" accept-charset="utf-8">
			<table cellpadding=0 cellspacing=0>
				<tr>
					<td></td>
					<td>  <?= $variable_description ?>   </td>
					<td>
						<table width=100% cellspacing=0 cellpadding=0>
							<tr align=center>
								<td><b>Available</b></td>
								<td></td>
								<td><b>Selected</b></td>
							</tr>
							<tr height=20 valign=middle>


								<input type=hidden name="<?= trim($variablename) ?>">
								<input type=hidden name=frm_action value="update">
								<input type=hidden name=frm_subaction value="update">
								<?= $this->html_variable_inherit("frm_o_o") ?>


								<td class=col width=100% align=center valign=middle><select
										class=textbox <?= $stylestring ?>
										id=<?= $ts_name ?>  multiple
										class=textbox
										name=<?= trim($srcname) ?>>
<?php
		foreach ($src as $k => $s) {
			if (csb($k, "__title")) {
				$desc = "----$k-----";
				$_t_image = null;
				$key = $k;
			} else {
				$key = base64_encode($s);
				$s = "j[class]=$class&$j[nname]=name&$s";
				$s = $this->getFullUrl($s, null);
				$ac_descr = $this->getActionDetails($s, null, $iconpath, $path, $post, $_t_file, $_t_name, $_t_image, $__t_identity);
				$desc = $ac_descr[2];
				$_t_image = dirname($_t_image) . "/small/" . basename($_t_image);
			}
?>

											<option value="<?= $key ?>"
											        style="vertical-align:middle;padding:0 0 0 25px; width:300px; height:20px; background:#efe8e0 url(<?= $_t_image ?>) no-repeat;"><?= $desc ?></option>
<?php
		}
?>

									</select>

								</td>
								<td class=col width=15% align=center>
									<table align=center>
										<tr>
											<td><INPUT TYPE=button class=submitbutton
											           onClick="multiSelectPopulate('<?= $form ?>', '<?= trim($variablename) ?>',  '<?= $ts_name ?>', '<?= $ts_name2 ?>')"
											           VALUE="&nbsp;&nbsp;>>&nbsp;&nbsp;">

											</td>
										</tr>
										<tr>
											<td>
												<INPUT TYPE=button class=submitbutton
												       onClick="multiSelectRemove('<?= $form ?>', '<?= trim($variablename) ?>', '<?= $ts_name2 ?>')"
												       VALUE="&nbsp;&nbsp;<<&nbsp;&nbsp;">

											</td>
										</tr>
									</table>


								</td>

								<td class=col align=center width=30%>
									<select id="<?= $ts_name2 ?>" <?= $stylestring ?> class="textbox" multiple
									        name="<?= trim($dstname) ?>">
<?php

		foreach ($dst as $k => $d) {
			if (csb($d, "__title")) {
				$desc = $d;
				$_t_image = null;
			} else {
				$s = "j[class]=$class&$j[nname]=name&$d";
				$s = $this->getFullUrl($s, null);
				$ac_descr = $this->getActionDetails($s, null, $iconpath, $path, $post, $_t_file, $_t_name, $_t_image, $__t_identity);
				$d = base64_encode($d);
				$_t_image = dirname($_t_image) . "/small/" . basename($_t_image);
				$desc = $ac_descr[2];
			}
?>

											<option value="<?= $d ?>"
											        style="vertical-align:middle; padding:0 0 0 25px; width:300px; height:20px; background:#efe8e0 url(<?= $_t_image ?>) no-repeat;"><?= $desc ?></option>
<?php
		}
?>

									</select>
									<script>
										createFormVariable('<?=$form?>', '<?=trim($variablename)?>', '<?=$ts_name2?>');
									</script>

								</td>
								<td><input type="button" class=submitbutton value="&nbsp;&nbsp;Up&nbsp;&nbsp;"
								           onclick="shiftOptionUp('<?= $form ?>', '<?= $variablename ?>', <?= $dstname ?>)"/><br/><br/>
									<input type="button" class=submitbutton value="&nbsp;&nbsp;Down&nbsp;&nbsp;"
									       onclick="shiftOptionDown('<?= $form ?>', '<?= $variablename ?>', <?= $dstname ?>)"/><br/><br/>
								</td>
							</tr>

						</table>
					</td>
				</tr>

				<tr>
					<td colspan=100 align=right><input type="submit" class="submitbutton"
					                                   value="&nbsp;&nbsp;Update&nbsp;&nbsp;"></td>
				</tr>
			</table>
		</form>
<?php
	}

	static function fix_lt_gt($value)
	{
		$value = str_replace(array("<", ">"), array("&lt;", "&gt;"), $value);

		return $value;
	}

	function print_find($object)
	{
		global $gbl, $sgbl, $login, $ghtml;

		if ($sgbl->isBlackBackground()) {
			return;
		}

		$rows = 100;
		$cols = 240;
		$skindir = $login->getSkinDir();
		$value = $object->text_comment;
		$rclass = "frmtextarea";
		$variable = "frm_{$object->getClass()}_c_text_comment";
?>

		<div style="width: 240px; border: 1px solid #ddd; margin: 0 auto 0 auto;">
			<div style="padding: 4px; text-align: center; background:#efe8e0 url(<?= $skindir ?>/images/expand.gif)">
				<span style="font-weight:bold">&nbsp;Find</span></div>
			<div><input style="width: 100%; border:0; padding:2px;" type='text' name='find' onKeyUp="searchpage(this)">
			</div>
		</div>

		<br/>
<?php
	}

	function print_note($object)
	{
		global $gbl, $sgbl, $login, $ghtml;

		$rows = 100;
		$cols = 240;
		$blackstyle = null;

		if ($sgbl->isBlackBackground()) {
			$blackstyle = "style='background:black;color:gray;border:1px solid gray;'";
?>

			<span style='color:gray'> note area </span>
<?php

			return;
		}

		$skindir = $login->getSkinDir();
		$value = $object->text_comment;
		$rclass = "frmtextarea";
		$url = $this->getFullUrl("a=updateform&sa=information");
		$variable = "frm_{$object->getClass()}_c_text_comment";
?>

		<div style="width: 240px; border: 1px solid #ddd; margin: 0 auto 0 auto;">
			<div style="padding: 4px; text-align: center; background:#efe8e0 url(<?= $skindir ?>/images/expand.gif)">
				<span style='font-weight:bold'>&nbsp;Comments<a href="<?= $url ?>"> [edit] </a></div>
			<div><textarea nowrap id="textarea" class="<?= $rclass ?>" rows="<?= $rows ?>"
			               style="border: 0; margin: 0; width: <?= $cols ?>; height: 100px;"
			               name="<?= $variable ?>" size="30"><?= $value ?></textarea></div>
		</div>
		<br/>
<?php
	}

	function print_multiselect($form, $variable, $rowuniqueid, $rowclass, $rowcount)
	{
		global $gbl, $sgbl, $login, $ghtml;

		$_t_name = $this->getcgikey($variable->name);
		$this->checkForScript($this->$_t_name);
		$m_value = $this->$_t_name;
		$ts_name = "ts_$variable->name";
		$ts_name2 = "ts_{$variable->name}2";
		$size = $variable->count;
		$variable1 = $variable->variable1;
		$variable2 = $variable->variable2;

		$prevvar = $gbl->getSessionV('__tmp_redirect_var');

		if (isset($prevvar[$variable->name])) {
			$v2 = $prevvar[$variable->name];
			$v2 = explode(",", $v2);
		}
?>

		<?= $variable->desc ?>

		<table width=100% cellpadding=0 cellspacing=0>
			<tr>
				<td><b>Available</b></td>
				<td colspan=1></td>
				<td><b>Selected</b></td>
				<td colspan=1></td>
			</tr>
			<tr>
				<td>
					<input type=hidden name="<?= $variable->name ?>">
					<select class=textbox id=<?= $ts_name ?>  multiple size=5 class=textbox
					        name=<?= $variable1->name ?>>
<?php
		foreach ($variable1->option as $k => $option) {
?>

							<option value="<?= $k ?>"><?= $option ?></option>
<?php
		}
?>

					</select>
				</td>
				<td>
					<INPUT TYPE="button" class="submitbutton"
					       onClick="multiSelectPopulate('<?= $form ?>', '<?= trim($variable->name) ?>',  '<?= $ts_name ?>', '<?= $ts_name2 ?>')"
					       VALUE="&nbsp;&nbsp;>>&nbsp;&nbsp;">
					<INPUT TYPE="button" class="submitbutton"
					       onClick="multiSelectRemove('<?= $form ?>', '<?= trim($variable->name) ?>', '<?= $ts_name2 ?>')"
					       VALUE="&nbsp;&nbsp;<<&nbsp;&nbsp;">
				</td>
				<td>
					<select id=<?= $ts_name2 ?> class=textbox size=5 multiple name=<?= trim($variable2->name) ?>>
<?php
		$v2count = 0;

		foreach ($v2 as $k => $option) {
			$v2count++;

?>

							<option value="<?= $option ?>"><?= $option ?></option>
<?php
		}

		if (!$v2count) {
			foreach ((array)$variable2->option as $k => $option) {
?>

								<option value="<?= $option ?>"><?= $option ?></option>
<?php
			}
		}
?>

					</select>
					<script>
						createFormVariable('<?=$form?>', '<?=$variable->name?>', '<?=$ts_name2?>');
					</script>
				</td>
				<td>
					<input type="button" name="upbotton" class="submitbutton" value="&nbsp;&nbsp;Up&nbsp;&nbsp;"
					       onclick="shiftOptionUp('<?= $form ?>', '<?= $variable->name ?>', <?= $variable2->name ?>)"/>
					<input type="button" name="downbutton" class=submitbutton value="&nbsp;&nbsp;Down&nbsp;&nbsp;"
					       onclick="shiftOptionDown('<?= $form ?>', '<?= $variable->name ?>', <?= $variable2->name ?>)"/>

				</td>
			</tr>
		</table>

<?php
	}

	function print_checkboxwithtext($form, $variable, $rowuniqueid, $rowclass, $rowcount)
	{
		if ($variable->mode === "or") {
			$txtval = "true";
			$txtval1 = "false";
			$txtcn = "'textdisable'";
			$txtcn1 = "'textenable'";
			$ckclass = "ckbox1";
			$tdash1 = "-";
			$tdash2 = "";
		}

		$blockcount = "bdd";
		$variable_description = "$variable->desc";

		if ($variable->mode === "depend") {
			$txtval = "false";
			$txtval1 = "true";
			$txtcn = "'textenable'";
			$txtcn1 = "'textdisable'";
			$ckclass = "ckbox2";
			$tdash1 = "";
			$tdash2 = "-";
		}

		if ($variable->checkbox->checked === "yes") {
			$tclass = "textdisable";
			$tdisabled = "disabled";
		} else {
			$tclass = "textenable";
			$tdisabled = "";
		}

		if ($variable->text->value != "" && $variable->text->value != "-") {
			$tval = $variable->text->value;
		} else {
			$tval = "";
		}

		if ($variable->checkbox->checked === "yes") {
			$checked = " CHECKED  ";
		}
?>

		<?= $variable_description ?> <br/>

		<input class="<?= $tclass ?>" <?= $tdisabled ?> type="text"
		       name="<?= $variable->text->name ?>" value="<?= $variable->text->value ?>" size="20">
		<span class="small"><?= $variable->text->text ?></span>
		<?= $variable->checkbox->desc ?>
		<input class="<?= $ckclass ?>" type="checkbox" name="<?= $variable->checkbox->name ?>"
		       value="<?= trim($variable->checkbox->value) ?>" <?= $checked ?>
		       onclick="<?= "checkBoxTextToggle('$form', '{$variable->checkbox->name}', '{$variable->text->name}', '{$variable->checkbox->value}', '{$variable->text->value}');" ?>">

<?php
	}

	function xml_print_page($full)
	{
		global $gbl, $sgbl, $ghtml, $login;

		$frmvalidcount = -1;

		$skincolor = $login->getSkinColor();

		$backgroundcolor = '#fff';
		$bordertop = "#d0d0d0";

		if ($sgbl->isBlackBackground()) {
			$skincolor = '333';
			$backgroundcolor = '#000';
			$bordertop = "#333";
		}

		$rowcount = -1;
		$rowclass = 1;
		$frmvalidcount++;
		$blockcount = "count";
		$width = "100%";

		$block = array_shift($full);

		if ($gbl->__inside_ajax) {
			$onsubmit = "onsubmit='return false;'";
			$gbl->__ajax_form_name = $block->form;
		} else {
			$onsubmit = "onsubmit=\"return check_for_needed_variables('$block->form');\"";
?>

			<script>
				global_need_list = new Array();
				global_match_list = new Array();
				global_desc_list = new Array();
			</script>
<?php
		}
?>

		<div style="width: 600px; margin: 0 auto 0 auto;">
			<div>
				<form name="<?= $block->form ?>" id="<?= $block->form ?>"
				      action="<?= $block->url ?>" <?= $block->formtype ?>
				      method="<?= $sgbl->method ?>" <?= $onsubmit ?> accept-charset="utf-8">
<?php
		//	dprint($block->form);

		$full = array_flatten($full);
		//	dprintr($full);

		$totalwidth = '600';

		foreach ($full as $variable) {
			if ($variable->type === 'textarea' && $variable->width === '90%') {
				$totalwidth = '100%';

				break;
			}
		}

		if ($block->title) {
?>

						<div style="width: 100%; margin: 0">
							<fieldset
								style="background-color:<?= $backgroundcolor ?>; border: 0; padding: 10px 0 10px 0; border-top: 1px solid #<?= $bordertop ?>">
								<legend style='font-weight:normal; border: 0'><span
										style='color: #303030; font-weight:bold'><?= $block->title ?></span>
								</legend>
							</fieldset>
						</div>
<?php
		}

		$buttonexist = false;

		foreach ($full as $variable) {
			if ($variable->type === 'button') {
				$buttonexist = true;
			}
		}

		if (!$buttonexist) {
			return;
		}
?>

					<div align=left style="background-color:<?= $backgroundcolor ?>; width:100%">
						<div align=left style="width:<?= $totalwidth ?>px; border: 1px solid #<?= $skincolor ?>;">
<?php
		$total = count($full);
		$count = 0;

		foreach ($full as $variable) {
			if ($variable->type == "subtitle") {
?>

						</div>
						<div style='padding: 10px'><span style='font-weight:bold'><?= $variable->desc ?></span>
						</div>
						<div align=left
						     style='display:hidden; width:<?= $totalwidth ?>; border: 1px solid #<?= $skincolor ?>'>
<?php
				$count = 0;

				continue;
			}

			if ($variable->type === 'hidden') {
?>

								<input type="hidden" name="<?= $variable->name ?>" value="<?= $variable->value ?>">
<?php

				continue;
			}

			if ($variable->need === 'yes') {
				if ($gbl->__inside_ajax) {
					if (!isset($gbl->__ajax_need_var)) {
						$gbl->__ajax_need_var = array();
					}

					$gbl->__ajax_need_var[$variable->name] = $variable->desc;
				} else {
?>

									<script> global_need_list['<?=$variable->name?>'] = '<?=$variable->desc?>'; </script>
<?php
				}

			}

			if (isset($variable->match)) {
				if ($gbl->__inside_ajax) {
					if (!isset($gbl->__ajax_match_var)) {
						$gbl->__ajax_match_var = array();
					}

					if (!isset($gbl->__ajax_desc_var)) {
						$gbl->__ajax_desc_var = array();
					}

					$gbl->__ajax_match_var[$variable->name] = $variable->match;
					$gbl->__ajax_desc_var[$variable->name] = $variable->desc;
					$gbl->__ajax_desc_var[$variable->match] = $variable->matchdesc;
				} else {
?>

									<script>
										global_match_list['<?=$variable->name?>'] = '<?=$variable->match?>';
										global_desc_list['<?=$variable->name?>'] = '<?=$variable->desc?>';
										global_desc_list['<?=$variable->match?>'] = '<?=$variable->matchdesc?>';
									</script>
<?php
				}
			}

			$this->print_variable($block, $variable, $count);
			$count++;
		}
?>

						</div>
					</div>
				</form>
			</div>
			<div>&nbsp;</div>
		</div>

<?php
	}

	function print_modify($form, $variable, $rowuniqueid, $rowclass, $rowcount)
	{
		global $gbl, $sgbl, $login, $ghtml;

		$icondir = get_image_path();

		$prevvar = $gbl->getSessionV('__tmp_redirect_var');

		$myneedstring = null;

		if ($variable->need === "yes") {
			$myneedstring = "<span style='color:red'><sup>*</sup></span>";
		}

		$variable_description = "$variable->desc";
		$blackstyle = null;

		if ($sgbl->isBlackBackground()) {
			$variable_description = "<span style='color:#999999'> $variable_description </span> ";
			$blackstyle = "style='background:black;color:gray;border:1px solid gray;'";
		}

		if ($variable->type === 'fileselect') {
			$fvalue = trim($variable->fvalue);
			$url = $this->getFullUrl("a=selectshow&l[class]=ffile&l[nname]=$fvalue");
			$url .= "&frm_selectshowbase=$fvalue";
		}

		$m_value = "";
		$realname = trim($variable->name);
		$realname = substr($realname, strlen('frm_'));

		// Don't Create extra variables 'pre-$var' and 'post-$var' if there are extra texts.

		if ($variable->value != "") {
			$this->checkForScript($variable->value);
			$m_value = $variable->value;
		} else {
			if (trim($variable->texttype) != "password") {
				$m_value = null;
				$index = trim($variable->name);

				if (isset($prevvar[$index])) {
					$this->checkForScript($prevvar[$index]);
					$m_value = $prevvar[$index];
				}
			}
		}

		if (trim($variable->text) != "") {
			$tbsize = 18;
		} else {
			$tbsize = 30;
		}

		if (trim($variable->texttype) == "") {
			$texttype = "text";
		} else {
			$texttype = $variable->texttype;
		}

?>

		<?= $variable_description ?> <?= $myneedstring ?> <br/>
		<?= $variable->pretext ?>
		<input class="<?= $variable->name ?> textbox" type="<?= $texttype ?>"
		       style="width: 45%; border: 1px solid #aaaaaa; margin: 2px 0 2px 0; padding: 2px 0 2px 0"
		       name="<?= $variable->name ?>"
		       value="<?= $m_value ?>"> <?= $variable->posttext ?>
<?php

		if ($variable->type === 'fileselect') {
			/*--- issue #609 - "'<?=$url?>';);"><img" to "'<?=$url?>');"><img;" ---*/
?>

			<a href="javascript:void(0);"
			   onclick="selectFolder(<?= trim($form) ?>.<?= trim($variable->name) ?>, '', '<?= $url ?>');"><img
					width="15" height="15" src="<?= $icondir ?>/ffile_ttype_v_directory.gif" border="0"
					alt="Select Folder" align="absmiddle"></a>
<?php
		}

		if (isset($variable->confirm_password) && $variable->confirm_password) {
?>

			<script language="javascript" src="/theme/js/divpop.js"></script>

			<div id="showimage"
			     style="visibility: hidden; position: absolute; width: 250px; left: 50%; top: 300px; margin: 0 auto 0 -125px">
				<div style="background-color: #4488CC; border: 1px solid #ddd; cursor:hand; cursor:pointer"
				     onMousedown="password_initializedrag(event)">
					<div style="height: 16px">
						<div id="dragbar" style="float:left; width: 200px">
							<div style="width:100%" onSelectStart="return false">
								<div style="width:100%" onMouseover="dragswitch=1;" onMouseout="dragswitch=0">
									<span style="color:#FFFFFF">&nbsp;Password Box</span>
								</div>
							</div>
						</div>
						<div style="float:right">
							<a href="#" onClick="password_hidebox('showimage');return false">
								<span style="color:#FFFFFF; padding:2px">X</span></a>
						</div>
					</div>

					<!-- PUT YOUR CONTENT BETWEEN HERE -->
					<div style="background-color: #FFEEDD; padding: 4px">
						<div id="password_container"></div>
					</div>
					<!-- END YOUR CONTENT HERE -->
				</div>

			</div>
			<input style="margin: 2px; border: 1px solid #aaaaaa; background-color: #eeeeee; width: 120px;"
			       class=textbox type=button value="Generate Password"
			       onclick="generatePass('<?= $form ?>', '<?= $variable->name ?>');" width="10">
<?php
		}

		$postvar = $variable->postvar;

		if ($postvar) {
?>

			<select style="width: 45%; margin: 2px" name="<?= $postvar->name ?>" value="">
<?php
				foreach ($postvar->option as $vv) {
?>

					<option value="<?= $vv ?>"><?= $vv ?></option>
<?php
				}
?>

			</select>
<?php
		}
?>

<?php
	}

	function print_radio($form, $variable, $list, $rowuniqueid, $rowclass, $rowcount)
	{
?>

<?php
		foreach ($list as $k => $l) {
?>

			<input type="radio" name="radio_<?= $variable ?>" value="<?= $k ?>"> <?= $l ?> <br/>
<?php
		}
?>

		<input type="radio" name="radio_<?= $variable ?>" value="__provide__"> Provide
		<input type="textbox" name="<?= $variable ?> value="">

<?php
	}

	function print_variable($block, $variable, $count)
	{
		global $gbl, $sgbl, $login, $ghtml;

		static $rowclass, $rowcount;

		if ($gbl->__inside_ajax && $variable->type === 'button') {
			if (strtolower($variable->value) === 'updateall') {
				$gbl->__ajax_allbutton = true;
			}

			return;
		}

		$skincolor = $login->getSkinColor();

		$skindir = $login->getSkinDir();

		$imgheadleft = "{$skindir}/images/top_lt.gif";
		$imgheadright = "{$skindir}/images/top_rt.gif";
		$imgheadleft = "{$skindir}/images/top_lt.gif";
		$imgtablerowhead = "{$skindir}/images/tablerow_head.gif";
		$imgheadbg = "{$skindir}/images/top_bg.gif";
		$imgtopline = "{$skindir}/images/top_line.gif";
		$imgsubtitle1 = "{$skindir}/images/subtitle1.gif";
		$imgsubtitle2 = "{$skindir}/images/subtitle2.gif";
		$imgsubtitle3 = "{$skindir}/images/subtitle3.gif";
		$imgpointer = get_general_image_path("/button/pointer.gif");
		$imgblank = get_general_image_path("/button/blank.gif");

		$prevvar = $gbl->getSessionV('__tmp_redirect_var');

		$_error_list = array();

		if (isset($gbl->frm_ev_list)) {
			$_error_list = explode(",", $gbl->frm_ev_list);
		}

		$myneedstring = null;

		if ($variable->need === "yes") {
			$myneedstring = "<span style='color:red'><sup>*</sup></span>";
		}

		$variable_description = "$variable->desc";

		$vname = $variable->name;

		if (csa($vname, "_aaa_")) {
			$vname = strtil($vname, "_aaa_");
		}

		$blackstyle = null;
		$filteropacitystringspan = null;
		$filteropacitystringspanend = null;
		$filteropacitystring = null;

		if ($sgbl->isBlackBackground()) {
			$variable_description = "<span style='color:#999999'> $variable_description </span> ";
			$blackstyle = "style='background:black;color:gray;border:1px solid gray;'";
			$filteropacitystringspanend = "</span>";
			$filteropacitystringspan = "<span style='background:black'> ";
			$filteropacitystring = "style='background:black;color:#999999;FILTER:progid;-moz-opacity:0.5'";
		}

		if (preg_match("/frm_.*_c_/", $vname)) {
			$vname = preg_replace("/frm_.*_c_/i", "", $vname);
		}

		if ($vname && array_search_bool($vname, $_error_list)) {
			$divstyle = 'background-color:#ffd7d7';
		} else {
			$borb = null;

			if ($count) {
				$borb = "border-top:1px solid #aaaaaa;";

				if ($sgbl->isBlackBackground()) {
					$borb = "border-top:1px solid #333;";
				}
			}

			if ($rowclass) {
				$divstyle = "$borb background-color:#ffffff";
			} else {
				$divstyle = "$borb background-color:#faf8f8";
			}

			if ($sgbl->isBlackBackground()) {
				$divstyle = "$borb background-color:#000";
			}

			if ($variable->type === 'button') {
				if ($sgbl->isBlackBackground()) {
					$divstyle = "text-align:right;";
				} else {
					$divstyle = "text-align:right;$borb background:#eef url({$skindir}/images/expand.gif)";
				}
			}

			$rowclass = $rowclass ? 0 : 1;
		}

		$rowuniqueid = "id$vname";
		$rowcount++;

?>

		<div align="left" style="padding:10px; <?= $divstyle ?>; display:block">
<?php

		$variable_description = ucwords($variable_description);

		switch ($variable->type) {
			case "checkbox":
				$m_value = null;

				if (isset($prevvar[trim($variable->name)])) {
					$this->checkForScript($prevvar[trim($variable->name)]);
					$m_value = $prevvar[trim($variable->name)];
				}

				$checkedvalue = trim($variable->checked);
				$checkv = null;

				if ($checkedvalue === "yes") {
					$checkv = " CHECKED ";
				} else {
					if ($checkedvalue === 'disabled') {
						$checkv = " DISABLED";
					}
				}
?>

					<?= $filteropacitystringspan ?>

					<input style="border: 1px solid #aaaaaa;" <?= $filteropacitystring ?> <?= $blackstyle ?>
					       type=checkbox name="<?= $variable->name ?>" <?= $checkv ?> value="<?= $variable->value ?>">
					<?= $variable_description ?> <?= $filteropacitystringspanend ?>
<?php

				break;
			case "select":
				$m_value = null;

				if (isset($prevvar[trim($variable->name)])) {
					$this->checkForScript($prevvar[trim($variable->name)]);
					$m_value = $prevvar[trim($variable->name)];
				}

				$v = $variable->name;
?>
					<?= $variable_description ?> <br/>
					<?= $filteropacitystringspan ?> <select
					style="border: 1px solid #aaaaaa; margin: 2px" <?= $filteropacitystring ?> class="textbox"
					name="<?= $v ?>">
<?php
				foreach ($variable->option as $k => $option) {
					$issel = false;

					if (csb($k, "__v_selected_")) {
						$k = strfrom($k, "__v_selected_");
						$issel = true;
					}

					$sel = '';

					if ($issel && !$m_value) {
						$sel = 'SELECTED';
					}

					if ($k === $m_value) {
						$sel = 'SELECTED';
					}

?>

						<option <?= $sel ?> value="<?= $k ?>"><?= $option ?></option>
<?php
				}
?>

				</select> <?= $filteropacitystringspanend ?>
<?php

				break;
			case "multiselect":
				$this->print_multiselect($block->form, $variable, $rowuniqueid, $rowclass, $rowcount);

				break;
			case "checkboxwithtext":
				$this->print_checkboxwithtext($block->form, $variable, $rowuniqueid, $rowclass, $rowcount);

				break;
			default:
			case "nomodify" :
				$value = $variable->value;
				$value = self::fix_lt_gt($value);

				if ($sgbl->isLxlabsClient()) {
					$value = preg_replace("+(https://[^ \n]*)+", "<a href=$1 target=_blank style='text-decoration:underline'> Click Here </a>", $value);
				}

				$value = str_replace("\n", "\n<br /> ", $value);
				$ttname = $variable->name;

				// Don't ever make this hidden. It is absolutely not necessary. The value is available directly itself.
?>
					<?= $variable_description ?>: &nbsp;
					<?= $value ?>

<?php
				break;
			case "image" :
				$width = trim($variable->width);
				$height = trim($variable->height);
?>

					<?= $variable_description ?> <br/>
					<img src="<?= $variable->value ?>" width="<?= $width ?>" height="<?= $height ?>">
<?php

				break;
			case "fileselect":
			case "modify":
				$this->print_modify($block->form, $variable, $rowuniqueid, $rowclass, $rowcount);

				break;
			case "file":
?>

					<?= $variable_description ?> <?= $myneedstring ?> <br/>
					<input class="filebox" type="file" name="<?= $variable->name ?>" size="30">
<?php

				break;
			case "htmltextarea":
?>

					<tr>
						<td colspan="1000">
<?php
				if ($variable->height != "") {
					$rows = $variable->height;
				} else {
					$rows = "5";
				}

				if ($variable->width != "") {
					$cols = $variable->width;
				} else {
					$cols = "90%";
				}

				if (trim($variable->readonly) === "yes") {
					$readonly = " readonly ";
					$rclass = "frmtextareadisable";
				} else {
					$readonly = " ";
					$rclass = "frmtextarea";
				}

				$value = "$variable->value";

				if (!$value) {
					if (isset($prevvar[trim($variable->name)])) {
						$value = $prevvar[trim($variable->name)];
					}
				}

				include("theme/fckeditor/fckeditor_php5.php");


				$oFCKeditor = new FCKeditor($variable->name);
				$oFCKeditor->BasePath = '/theme/fckeditor/';
				$oFCKeditor->Value = $value;
				$oFCKeditor->Create();
?>

						</td>
					</tr>
<?php

				break;
			case "textarea":
?>

					<?= $variable_description ?> <?= $myneedstring ?> <br/>
<?php

				if ($variable->height != "") {
					$rows = trim($variable->height);
				} else {
					$rows = "5";
				}

				if ($variable->width != "") {
					$cols = trim($variable->width);
				} else {
					$cols = "85%";
				}

				if (trim($variable->readonly) === "yes") {
					$readonly = " readonly ";
					$rclass = "frmtextareadisable";
				} else {
					$readonly = " ";
					$rclass = "frmtextarea";
				}

				$value = "$variable->value";

				if (!$value) {
					if (isset($prevvar[$variable->name])) {
						$value = $prevvar[$variable->name];
					}
				}
?>

					<textarea nowrap id="textarea_<?= $variable->name ?>" class="<?= $rclass ?>" rows="<?= $rows ?>"
					          style="margin:2px 0 2px 0;width:<?= $cols ?>;height:120px; border: 1px solid #aaa; padding: 2px;"
					          name="<?= $variable->name ?>" <?= $readonly ?> size="30"><?= $value ?></textarea>

					<script type="text/javascript"> // createTextAreaWithLines('textarea_
						<?=$variable->name?>');</script>

					<style>
						.textAreaWithLines {
							display: block;
							margin: 0;
							font-family: Tahoma, Verdana, Arial, Helvetica, Arial, sans-serif;
							font-size: 11px;
							border: 1px solid #aaaaaa;
							border-right: 1px solid #<?=$skincolor?>;
							background: #<?=$skincolor?>;
						}
					</style>
<?php

				break;
			case "button":
				$string = null;
				$bgcolor = null;
				$onclick = null;

				if (strtolower($variable->value) === 'updateall') {
					$string = "Click Here to Update all the objects that appear in the top selectbox with the above values";
					$bgcolor = "bgcolor=$skincolor";
					$onclick = "onclick='return updateallWarning();'";
				}
?>

					<?= $string ?>
					<input <?= $blackstyle ?> class="submitbutton" type="submit" <?= $onclick ?>
					                          name="<? $variable->name ?>"
					                          value="&nbsp;&nbsp;<?= $variable->value ?>&nbsp;&nbsp;">
<?php

				break;
		}
?>

		</div>

<?php
	}

	function print_information($place, $type, $class, $extr, $vlist = null)
	{
		global $gbl, $sgbl, $login, $ghtml;
		global $g_language_mes;

		$skin_name = $login->getSpecialObject('sp_specialplay')->skin_name;

		$pinfo = null;

		if ($vlist) {
			$info = $vlist;
		} else {
			$info = implode("_", array($class, $type, $extr, $place));
		}

		if (isset($g_language_mes->__commonhelp[$info])) {
			$info = $g_language_mes->__commonhelp[$info];
		}

		if ($place !== 'post') {
			if (isset($g_language_mes->__information[$info])) {
				$pinfo = $g_language_mes->__information[$info];
			}
		} else {
			dprint($info);

			if (isset($g_language_mes->__information[$info])) {
				$pinfo = $g_language_mes->__information[$info];
			}
		}

		if (!$pinfo) {
			$info = implode("_", array($type, $extr, $place));

			if ($place !== 'post') {
				if (isset($g_language_mes->__information[$info])) {
					$pinfo = $g_language_mes->__information[$info];
				}
			} else {
				dprint($info);
				if (lxfile_exists("__path_program_htmlbase/help/$info.dart")) {
					$pinfo = lfile_get_contents("__path_program_htmlbase/help/$info.dart");
				}
			}
		}

		if ($skin_name === 'feather') {
			if (!$pinfo) {
				return;
			}
		}

		$pinfo = str_replace("<%program%>", $sgbl->__var_program_name, $pinfo);

		$pinfo = explode("\n", $pinfo);
		$skip = false;

		foreach ($pinfo as $p) {
			$p = trim($p);

			if (csb($p, "<%ifblock:")) {
				$name = strfrom($p, "<%ifblock:");
				$name = strtil($name, "%>");

				$forward = true;

				if ($name[0] === '!') {
					$forward = false;
					$name = strfrom($name, "!");
				}

				if (method_exists($login, $name)) {
					if ($forward) {
						if (!$login->$name()) {
							$skip = true;
						}
					} else {
						if ($login->$name()) {
							$skip = true;
						}
					}
				} else {
					$skip = true;
				}

				continue;
			}

			if ($p === "</%ifblock%>") {
				$skip = false;
				continue;
			}

			if ($skip) {
				continue;
			}

			$out[] = $p;
		}

		$pinfo = implode("\n", $out);

		$fontcolor = "#000000";

		$pinfo = str_replace("\n", "<br />", $pinfo);
		$pinfo = str_replace("[b]", "<span style='font-weight: bold'>", $pinfo);
		$pinfo = str_replace("[/b]", "</span>", $pinfo);

		$ret = preg_match("/<url:([^>]*)>([^<]*)<\/url>/", $pinfo, $matches);

		if ($ret) {
			$fullurl = $this->getFullUrl(trim($matches[1]));
			$pinfo = preg_replace("/<url:([^>]*)>([^<]*)<\/url>/", "<a class='insidelist' href=$fullurl> $matches[2] </a>", $pinfo);
		}

		if ($sgbl->isBlackBackground()) {
			$fontcolor = "#999999";
		}
?>

<?php
		if ($skin_name === 'feather') {
?>
		<div style="width: 600px; margin: 0 180px; border: 0; padding: 0;">
			<div id="infomsg" style="display: none;">
<?php
			$this->print_curvy_table_start();
		} else {
			if ($pinfo !== '') {
?>

		<div id="infomsg" style="display:none; position:fixed; width: 600px; top: 45px; left: 50%; margin: 0 auto 0 -300px; padding:15px; background-color:#dfe; border:3px double #e22; text-align:left">
<?php
			}

		}

		if ($sgbl->isBlackBackground()) {
?>

		<span style="color:#999999">
<?php
		}

		if ($pinfo !== '') {
?>

			<?= $pinfo ?><br/>
<?php
		}

		if ($sgbl->isBlackBackground()) {
?>

		</span>;
<?php
		}

		if ($login->getSpecialObject('sp_specialplay')->skin_name === 'feather') {
			$this->print_curvy_table_end();
?>
			</div>
		</div>
<?php
		} else {
			if ($pinfo !== '') {
?>
		</div>
<?php
			}
		}
	}

	// function print_curvy_table_start($width = "100")
	function print_curvy_table_start($width = "11")
	{
		global $gbl, $sgbl, $login;

		$a = $login->getSkinDir();

		$skin_col_dir = $login->getSpecialObject('sp_specialplay')->skin_color;

		// MR -- to minimize space, use default

		$a = str_replace($skin_col_dir, 'default', $a);

		if ($sgbl->isBlackBackground()) {
			return;
		}
?>

		<table cellpadding="0" align="center" cellspacing="0" width="100%">
		<tr>
			<td width="<?= $width ?>" align="right"><img src="<?= $a ?>/images/tl.gif" align="center"></td>
			<td style="background: url(<?= $a ?>/images/dot.gif) 0 0 repeat-x"></td>
			<td width="<?= $width ?>" align="left"><img src="<?= $a ?>/images/tr.gif" align="center"></td>
		</tr>
		<tr>
		<td style="background: url(<?= $a ?>/images/dot.gif) 0 0 repeat-y">
		</td>
		<td align="left">

<?php
	}

	// function print_curvy_table_end($width = "100")
	function print_curvy_table_end($width = "11")
	{
		global $gbl, $sgbl, $login;

		$a = $login->getSkinDir();

		$skin_col_dir = $login->getSpecialObject('sp_specialplay')->skin_color;

		// MR -- to minimize space, use default

		$a = str_replace($skin_col_dir, 'default', $a);

		if ($sgbl->isBlackBackground()) {
			return;
		}
?>

		</td>
		<td style="background: url(<?= $a ?>/images/dot.gif) 100% 0 repeat-y">
		</td>
		</tr>
		<tr>
			<td width="<?= $width ?>" align="right"><img src="<?= $a ?>/images/bl.gif" align="center"></td>
			<td style="background: url(<?= $a ?>/images/dot.gif) 0 95% repeat-x"></td>
			<td width="<?= $width ?>" align="left"><img src="<?= $a ?>/images/br.gif" align="center"></td>
		</tr>
		</table>&nbsp;

<?php
	}

	function print_on_status_bar($message)
	{
?>

		<script> top.bottomframe.updateStatusBar("<?=$message?>") </script>
<?php
	}

	function print_message()
	{
		global $gbl, $sgbl, $login;

		global $g_language_mes;

		$img_path = get_general_image_path();

		$cgi_message = $this->cgi("frm_emessage");
		$this->checkForScript($cgi_message);

		$cgi_frm_smessage = $this->frm_smessage;

		if ($cgi_frm_smessage) {
			$value = $this->frm_m_smessage_data;

			if (isset($g_language_mes->__emessage[$cgi_frm_smessage])) {
				$mess = $g_language_mes->__emessage[$cgi_frm_smessage];
			} else {
				$mess = $cgi_frm_smessage;
			}

			$imgfile = $img_path . "/button/okpic.gif";

			unset($this->__http_vars['frm_smessage']);
			unset($this->__http_vars['frm_m_smessage_data']);

			$color = 'green';
			$message = "<span style='color:green'><b>Information: </b></span> ";
			$style = 'border: 1px solid green; background:#fff;';
			$fontstyle = 'color: #000';
			$mess = $this->format_message($mess, $value, true);
			$this->print_on_status_bar("$message $mess");
		}

		if ($cgi_message) {
			$value = $this->frm_m_emessage_data;

			if (isset($g_language_mes->__emessage[$cgi_message])) {
				$mess = $g_language_mes->__emessage[$cgi_message];
			} else {
				$mess = $cgi_message;
				if ($value) {
					$mess .= " [$value]";
				}
			}

			unset($this->__http_vars['frm_emessage']);
			unset($this->__http_vars['frm_m_emessage_data']);

			$imgfile = $img_path . "/button/warningpic.gif";
			$color = 'brown';
			$message = "<span style='color:red'><b>Alert: </b></span> ";
			$style = 'border: 1px solid red; background:#ffd7d7;';
			$fontstyle = 'color: #000';

			// In the status bar, you should print with mainframe. But in the main page, it should be simple url.
			$pmess = $this->format_message($mess, $value, false);
			$this->show_error_message($pmess, $message, $imgfile, $color, $style, $fontstyle);

			$pmess = $this->format_message($mess, $value, true);
			$pmess = substr($pmess, 0, 270);
			$this->print_on_status_bar("$message $pmess...");
		}
	}

	function show_error_message($mess, $message = null, $imgfile = null, $color = null, $style = null, $fontstyle = null)
	{
		if (!$imgfile) {
			$img_path = get_general_image_path();
			$imgfile = $img_path . "/button/warningpic.gif";
			$color = 'brown';
			$message = "<span style='color:red'><b> Error: </b></span>";
			$style = 'border: 1px solid red; background:#ffd7d7;';
			$fontstyle = 'color: #000';
		}

		// MR -- impossible for login page with get_image_path()
		//	$icondir = get_image_path();
		$icondir = "/theme/icon/collage";
?>

		<div id="esmessage"
		     style="visibility:visible;width:400px; position:absolute; top: 320px; left:0; right:0; margin-left:auto; margin-right:auto;">
			<table width='400' style='<?= $style ?>' cellpadding='4' cellspacing='5'>
				<tr height='10'>
					<td nowrap><a href="javascript:hide_a_div_box('esmessage')"><img src="<?= $icondir ?>/close.gif">
							<span style='small'>Press Esc to close </span></a></td>
					<td></td>
				</tr>
				<tr>
					<td><img src="<?= $imgfile ?>"><span style='<?= $fontstyle ?>'><?= $message ?> <?= $mess ?></span>
					</td>
				</tr>
				<tr height="10">
					<td></td>
				</tr>
			</table>
			<br/>
		</div>

<?php
	}

	function replace_url($mess, $mainframeflag)
	{
		$tstring = null;

		if ($mainframeflag) {
			$tstring = "target=mainframe";
		}

		$ret = preg_match("/<url:([^>]*)>([^<]*)<\/url>/", $mess, $matches);

		if ($ret) {
			$fullurl = $this->getFullUrl(trim($matches[1]));
			$mess = preg_replace("/<url:([^>]*)>([^<]*)<\/url>/", "<a class=insidelist $tstring href=$fullurl> $matches[2] </a>", $mess);
		} else {
			$ret = preg_match("/<burl:([^>]*)>([^<]*)<\/burl>/", $mess, $matches);
			if ($ret) {
				$fullurl = $this->getFullUrl(trim($matches[1]), null);
				$mess = preg_replace("/<burl:([^>]*)>([^<]*)<\/burl>/", "<a class=insidelist $tstring href=$fullurl> $matches[2] </a>", $mess);
			}
		}

		return $mess;
	}

	function format_message($mess, $value, $mainframeflag)
	{
		$mess = str_replace("[b]", "<span style='font-weight:bold'>", $mess);
		$mess = str_replace("[/b]", "</span>", $mess);
		$mess = str_replace("[%s]", "<span style='font-weight:bold; color:black'>$value</span>", $mess);
		$mess = str_replace("[%cs]", $value, $mess);
		$mess = $this->replace_url($mess, $mainframeflag);

		return $mess;
	}

	function print_xpsingle($treename, $url, $psuedourl = null, $target = null, $nameflag = false)
	{
		if ($url === 'a=show') {
			$home = true;
		}

		$img_path = get_image_path();

		$buttonpath = $iconpath = $img_path;

		$descr = $this->getActionDetails($url, $psuedourl, $buttonpath, $path, $post, $file, $name, $image, $__t_identity);

		$desc = $descr[2];

		if ($nameflag) {
			$desc = "$__t_identity";
		}

		$help = $descr[3];

		$help = $this->get_action_or_display_help($help, "action");

		$desc = trim($desc);
		$open = 'false';
		$name = $file . "_" . $name;
?>

		createSubMenu(<?= $treename ?>, '<span
		style="text-decoration: underline;"><?= $desc ?></span>', '<?= $url ?>', '', '', '<?= $image ?>', 'mainframe');

<?php
	}

	function do_full_resource($object, $depth, $alistflag)
	{
		$treename = fix_nname_to_be_variable($object->nname);
		$this->do_resource(null, $object, $depth, $alistflag, "getResourceChildList", true, true);
	}

	function do_resource($tree, $object, $depth, $alistflag, $func, $complex = true, $showurlflag = true)
	{
		global $gbl, $sgbl, $login, $ghtml;

		static $scriptdone;

		if (!$scriptdone && $complex) {
?>

			<link href="/theme/js/tree/dtree.css" rel="stylesheet" type="text/css"/>
<?php
			$this->print_jscript_source("/theme/js/tree/dtree.js");
			$scriptdone = true;
		}

		$treename = "_" . fix_nname_to_be_variable($object->nname);
?>

		<div>
<?php
		if ($complex) {
?>

			<div class='dtree'>
				<script>
					<?=$treename?>
					= new dTree('<?=$treename?>');
				</script>
<?php
		}

		$val = -1;

		if (!$tree) {
			$tree = $this->print_resource(null, $object, $this->frm_o_o, $object, $depth, $alistflag, $func, false, $showurlflag);
		}

		if ($complex) {
?>

				<script>
<?php
			if (isset($gbl->__tmp_checkbox_value)) {
?>
					var __treecheckboxcount = <?=$gbl->__tmp_checkbox_value?>;
<?php
			}
		}
					
		$total = -1;
		print_time('tree');
		$this->print_tree($treename, $tree, $total, $val, $complex);
					
		if ($complex) {
?>
					document.write(<?=$treename?>);
				</script>
			</div>
<?php
		}

		print_time('tree', "Tree", 2);
?>
		</div>

		<form name="__treeForm" id="__treeForm" method="get" action="/display.php" accept-charset="utf-8">
			<input type="hidden" name="frm_accountselect" value="">
<?php
		$this->print_current_input_vars(array('frm_action', 'frm_subaction'));

		if (cse($this->frm_subaction, "confirm_confirm")) {
			$this->print_input("hidden", "frm_action", "update");
			$sub = $this->frm_subaction;
			$actionimg = "finish.gif";
		} else {
			$this->print_input("hidden", "frm_action", "updateform");
			$sub = $this->frm_subaction . "_confirm";
			$actionimg = "next.gif";
		}

		$this->print_input("hidden", "frm_subaction", "$sub");

		if (isset($gbl->__tmp_checkbox_value)) {
?>

				<a href="javascript:treeStoreValue()"> <img src="/theme/general/button/<?= $actionimg ?>"> </a>
<?php
		}
?>

		</form>
<?php
	}

	function print_tree($treename, $tree, &$total, $level, $complex = true)
	{
		$tlist = $tree->getList('tree');
		$open = $tree->open ? $tree->open : 'false';
		$open = 'false';

		if ($tree->imgstr) {
			$total++;

			if ($complex) {
?>

				<?= $treename ?>.add(<?= $total ?>, <?= $level ?>, '<?= $tree->imgstr ?>', '<?= $tree->url ?>', '', 'mainframe', '<?= $tree->img ?>', '<?= $tree->img ?>', <?= $open ?>, '<?= $tree->help ?>', '<?= $tree->alt ?>');
<?php
			} else {
				for ($i = 0; $i < $level; $i++) {
					dprint("Hello\n");
?>

					&nbsp;
					<?= $imgstr ?> <br/>
<?php
				}
			}
		}

		$level = $total;

		if ($tlist) {
			foreach ($tlist as $t) {
				$this->print_tree($treename, $t, $total, $level, $complex);
			}
		}
	}

	function print_resource($tree, $object, $cgi_o_o, $toplevelobject, $depth, $alistflag, $func, $childobjectflag = false, $showurlflag = true)
	{
		global $gbl, $sgbl, $login, $ghtml;

		$bgcolor = null;

		$path = $bpath = get_image_path();

		$class = $object->getClass();

		if (!$tree) {
			$tree = createTreeObject('name', null, null, null, null, null, null);
			$level = -1;
		} else {
			$level = 1;
		}

		$cnl = $object->$func();

		$alist = null;

		if ($level != -1) {
			if ($childobjectflag) {
				$url = $this->getFullUrl("a=show&o=$class", $cgi_o_o);
				$num = count($cgi_o_o);
				$cgi_o_o[$num]['class'] = $class;
			} else {
				$urlname = $object->nname;
				$url = $this->getFullUrl("a=show&l[class]=$class&l[nname]=$urlname", $cgi_o_o);
				$num = count($cgi_o_o);
				$cgi_o_o[$num]['class'] = $class;
				$cgi_o_o[$num]['nname'] = $object->nname;
			}

			$open = 'false';
			$alist = null;
		} else {
			$url = $this->getFullUrl("a=show", $cgi_o_o);
			$alist = $object->createShowAlist($alist);
			$open = 'true';
		}

		$list = $object->createShowTypeList();

		foreach ($list as $k => $v) {
			$type = $object->$k;
			$vtype = $k;
		}

		if ($childobjectflag) {
			$img = $this->get_image($path, $class, "show", ".gif");
		} else {
			$img = $this->get_image($path, $class, "{$vtype}_v_$type", ".gif");
		}

		if (isset($object->status) && $object->status) {
			if ($object->isOn('status')) {
				$hstr = "and is Enabled";
				$status = 'on';
			} else {
				$hstr = "and is Disabled";
				$status = 'off';
			}

			$stimg = $this->get_image($path, $class, "status_v_" . $status, ".gif");
			$imgstr = "<img height=8 width=8 src='$stimg'>";
		} else {
			$imgstr = null;
			$hstr = null;
		}

		$homeimg = $this->get_image($path, $class, "show", ".gif");

		if ($childobjectflag) {
			$name = $this->get_class_description($class);
			$name = $name[2];
		} else {
			$name = $object->getId();
		}

		$help = "$class <span style='color:blue'> $name </span> is of Type $type $hstr";
		$alt = lx_strip_tags($help);
		$inputstr = null;

		if (!$showurlflag) {
			$url = null;
		}

		$imgstr = "$inputstr <img src='$img' width=14 height=14>   $imgstr $name";

		if (isset($object->__v_message)) {
			$imgstr .= " " . $object->__v_message;
		}

		$pttr = createTreeObject($name, $img, $imgstr, $url, $open, $help, $alt);
		$tree->addToList('tree', $pttr);

		$childdepth = 1;
		$ppp = $object;

		//	dprintr($depth);

		if ($object !== $toplevelobject) {
			while ($ppp = $ppp->getParentO()) {
				if ($ppp === $toplevelobject) {
					break;
				}

				$childdepth++;
			}

			if ($depth && ($childdepth >= $depth)) {
				return null;
			}
		}

		if ($alist && $alistflag) {
			$open = 'false';
			$imgstr = "<img src='$homeimg' width=14 height=14> <span style='color:#5958aa'><b>Functions</b></span> ";
			$ttr = createTreeObject($name, '', $imgstr, $url, $open, $help, $alt);
			$pttr->addToList('tree', $ttr);
			$this->print_resourcelist($ttr, $alist, null);
			$open = 'true';
		}

		foreach ((array)$cnl as $v) {
			$name = $object->getChildNameFromDes($v);

			if (cse($v, "_o")) {
				$c = null;

				if ($object->isRealChild($name)) {
					$c = $object->getObject($name);
				}

				if ($c) {
					$this->print_resource($pttr, $c, $cgi_o_o, $toplevelobject, $depth, $alistflag, $func, true, $showurlflag);
				}

				continue;
			}

			$img = $this->get_image($path, $name, "list", ".gif");
			$url = $this->getFullUrl("a=list&c=$name");
			$desc = $this->get_class_description($name);
			$printname = get_plural($desc[2]);
			$help = "Click to Show $printname";
			$alt = $help;
			$npttr = $pttr;

			if ($object === $toplevelobject) {
				$open = 'true';
				$gbl->__navigmenu[$level + 2] = array('show', $object);
				$gbl->__navig[$level + 2] = $this->get_post_from_get($url, $__tpath, $__tpost);
				$imgstr = "<img src='$img' width=20 height=20>$printname";

				if (!$showurlflag) {
					$url = null;
				}

				$npttr = createTreeObject($name, $homeimg, $imgstr, $url, $open, $help, $alt);
				$pttr->addToList('tree', $npttr);

				if ($alistflag) {
					$open = 'false';
					$imgstr = 'Functions';
					$nttr = createTreeObject($name, $homeimg, $imgstr, $url, $open, $help, $alt);
					$npttr->addToList('tree', $nttr);
					$lalist = exec_class_method($name, 'createListAlist', $object, $name);
					$this->print_resourcelist($nttr, $lalist, null);
				}
			}

			$open = 'true';

			$filtername = $object->getFilterVariableForThis($name);

			$pagesize = (int)$login->issetHpFilter($filtername, 'pagesize') ? $login->gethpfilter($filtername, 'pagesize') : exec_class_method($class, "perPage");

			if (isset($sgbl->__var_main_resource) && $sgbl->__var_main_resource) {
				$cl = $object->getList($name);
				$count = count($cl);

				$halfflag = false;
			} else {
				$halfflag = true;
				$cl = $object->getVirtualList($name, $count);
			}

			if ($object->isVirtual($name)) {
				continue;
			}

			if ($cl) {
				//Setting $prev to -ll; this is done to initialize prev.
				if ($object === $toplevelobject && $login->getSpecialObject('sp_specialplay')->isOn('lpanel_group_resource') && $alistflag) {
					$prev = "-ll";

					foreach ($cl as $c) {
						if ($c->nname[0] != $prev[0]) {
							$imgstr = "<b>{$c->nname[0]} ....</b> ";
							$ttr = createTreeObject($name, $homeimg, $imgstr, $url, $open, $help, $alt);
							$npttr->addToList('tree', $ttr);
						}

						$prev = $c->nname;
						$this->print_resource($ttr, $c, $cgi_o_o, $toplevelobject, $depth, $alistflag, $func, false, $showurlflag);
					}

				} else {
					foreach ($cl as $c) {
						$this->print_resource($npttr, $c, $cgi_o_o, $toplevelobject, $depth, $alistflag, $func, false, $showurlflag);
					}
				}

				if ($halfflag && $count > $pagesize) {
					$url = $this->getFullUrl("a=list&c=$name", $cgi_o_o);
					$ttr = createTreeObject($name, $homeimg, "More (Showing $pagesize of $count)", $url, $open, $help, $alt);
					$npttr->addToList('tree', $ttr);
				}
			}
		}

		// At the top client Make all the children virtual..
		// This assures that after viewing resources, the cache doesn't hogg the system.
		return $tree;
	}

	function getMenuDescrString($img, $descr, $endimg = null)
	{
		$endstr = null;
		$imgstr = null;

		if ($endimg) {
			$endstr = "<td> <img src='$endimg'> </td> <td width='4'> &nbsp; </td> ";
		}

		if ($img) {
			$imgstr = "<img width=14 height=14 src='$img'> ";
		}

		// hack hack using the hilite class ( the lighter one for images.. and the image class is used for hilite)
		$string = "<table width='100%' cellpadding='0' cellspacing='0'> <tr> " .
			"<td valign='middle' align='center' style='padding:0' height='25' nowrap width='30' class='menuhilite'> " .
			"{$imgstr} </td> <td style='size:7pt' width='100%'>&nbsp;&nbsp;<span style='size:8pt'>$descr</span> " .
			"</td> $endstr </tr></table> ";

		return $string;
	}

	function print_resourcelist($tree, $alist, $base)
	{
		global $gbl, $sgbl, $login, $ghtml;

		$skindir = $login->getSkinDir();

		$image = null;
		$open = 'false';
		$help = null;
		$alt = null;

		foreach ((array)$alist as $k => $a) {
			if (is_array($a)) {
				if ($k === 'home') {
					continue;
				}

				if (csb($k, "__title")) {
					$open = 'false';
					$a = strtil($a, "(");
					$a = strtil($a, "[");
					$ttr = createTreeObject($name, $image, $a, '', $open, $help, $alt);
					$tree->addToList($ttr);

					continue;
				}

				$desc = get_plural($k);
				$image = "$skindir/browse.gif";
				$endimg = "$skindir/right_point.gif";

				$desc = "$desc";
				$open = 'false';
				$help = $desc;
				$alt = lx_strip_tags($help);

				$ttr = createTreeObject('name', $image, $desc, null, $open, $help, $alt);
				$tree->addToList('tree', $ttr);

				foreach ($a as $nk => $nv) {
					$nv = $this->getFullUrl($nv, $base);
					$this->print_ressingle($ttr, $nv);
				}
			}
		}

		foreach ((array)$alist as $k => $a) {
			if ($k === 'home') {
				continue;
			}

			if (csb($k, "__title")) {
				$open = 'false';
				$a = strtil($a, "(");
				$a = strtil($a, "[");
				$a = strtil($a, ":");
				$a = strtil($a, ":");
				$ttr = createTreeObject('name', $image, $a, null, $open, $help, $alt);
				$tree->addToList('tree', $ttr);
				continue;
			}

			if (is_array($a)) {
				//
			} else {
				if (!csb($k, "__v_")) {
					$a = $this->getFullUrl($a, $base);

					if (isset($ttr)) {
						$this->print_ressingle($ttr, $a);
					} else {
						$this->print_ressingle($tree, $a);
					}
				}
			}
		}
	}

	function print_ressingle($tree, $url, $psuedourl = null, $target = null, $nameflag = false)
	{
		$img_path = get_image_path();
		$buttonpath = $iconpath = $img_path;

		$descr = $this->getActionDetails($url, $psuedourl, $buttonpath, $path, $post, $file, $name, $image, $__t_identity);

		$desc = $descr[2];

		if ($nameflag) {
			$desc = $descr[2] . "  ($__t_identity)";
		}

		$help = $descr[2];

		$alt = lx_strip_tags($help);
		$help = $this->get_action_or_display_help($help, "action");

		$open = 'false';
		$name = $file . "_" . $name;

		// Hack hack... Just not setting frame for navigation menus. THis is not the way to do it.
		// a flag should be passed... is the correct way...
		$imgstr = "<img src='$image' width=14 height=14>$descr[2]";
		$ttr = createTreeObject($name, $image, $imgstr, $url, $open, $help, $alt);
		$tree->addToList('tree', $ttr);
	}

	function print_menulist($name, $alist, $base, $type)
	{
		global $gbl, $sgbl, $login, $ghtml;

		$skindir = $login->getSkinDir();

		foreach ((array)$alist as $k => $a) {
			if (is_array($a)) {
				continue;

				if ($k === 'home') {
					continue;
				}
				if ($this->is_special_url($a)) {
					continue;
				}


				if (csb($k, "__title")) {
					continue;
				}

				$desc = get_plural($k);
				$menuimg = "$skindir/browse.gif";
				$endimg = "$skindir/right_point.gif";

				$desc = "<span style=font-weight:bold>$desc</span>";
				$mnu = $this->getMenuDescrString($menuimg, $desc, $endimg);

?>

				window.<?= $name ?><?= $k ?> = new Menu("<?= $mnu ?>", 100);
<?php

				foreach ($a as $nk => $nv) {
					$nv = $this->getFullUrl($nv, $base);
					$this->print_pmenu("$name$k", $nv);
				}
			}
		}

?>

		window.<?= $name ?> = new Menu('<?= $name ?>',130);
<?php

		if ($type === 'slist') {
			$aa = $this->getFullUrl('a=show', $base);
			$this->print_pmenu($name, $aa);
		}

		foreach ((array)$alist as $k => $a) {
			if (!strcmp($k, 'home')) {
				continue;
			}

			if ($this->is_special_variable($a)) {
				continue;
			}

			if (csb($k, "__title")) {
				continue;
			}

			if (is_array($a)) {
				// Dont print property etc...
				continue;

				$aa = $this->getFullUrl($a[0], $base);
?>

				<?= $name ?>.addMenuItem(<?= $name ?><?= $k ?>, frame1+"<?= $aa ?>", 'Properties', 'mainframe');
<?php
			} else {
				// Hack hack...  NOt showing addforms in the top Menu... Also not showing in the tree view..
				if (csa(strtolower($a), "addform") || (csa(strtolower($a), 'update') && !csa(strtolower($a), 'updateform'))) {
					continue;
				}

				if (!csb($k, "__v_")) {
					$a = $this->getFullUrl($a, $base);
					$this->print_pmenu($name, $a);
				}
			}
		}
	}

	function print_pmenu($menu, $url, $psuedourl = null, $target = null, $nameflag = false)
	{
		global $gbl, $sgbl, $login;

		$img_path = get_image_path();
		$buttonpath = $iconpath = $img_path;

		if (csb($url, "__blank|")) {
			$url = substr($url, 8);
			$image = $buttonpath . "/delete.gif";
			$string = $this->getMenuDescrString($image, $url);
?>

			<?= $menu ?>.addMenuItem("<?= $string ?>", "", "0", "There is No History at this Point.", "0");
<?php

			return;
		}

		$descr = $this->getActionDetails($url, $psuedourl, $buttonpath, $path, $post, $file, $name, $image, $__t_identity);

		$desc = $descr[2];

		if ($nameflag) {
			$desc = $descr[2] . "  ($__t_identity)";
		}

		$help = $descr[3];

		$help = $this->get_action_or_display_help($help, "action");

		$name = $file . "_" . $name;

		/// Hack hack... Just not setting frame for navigation menus.
		//THis is not the way to do it. a flag should be passed... is the correct way...
		$frame = null;
		$string = $this->getMenuDescrString($image, $desc);

		if (csb($menu, 'navig') || csb($menu, 'hist')) {
?>

			<?= $menu ?>.addMenuItem("<?= $string ?>", "window.location='<?= $url ?>';", "0", "<?= $help ?>", "0");
<?php
		} else {
?>

			<?= $menu ?>.addMenuItem("<?= $string ?>", frame1+"'<?= $url ?>';", "0", "<?= $help ?>", "0");
<?php
		}
	}

	function getUrl()
	{
		$url = ($_SERVER["HTTPS"] != 'on') ? 'http://' . $_SERVER["SERVER_HOST"] : 'https://' . $_SERVER["SERVER_HOST"];
		$url .= ($_SERVER["SERVER_PORT"] !== 80) ? ":" . $_SERVER["SERVER_PORT"] : "";

		//	$url .= $_SERVER["REQUEST_URI"];

		return $url;
	}

	function print_real_beginning()
	{
		global $gbl, $sgbl, $login, $ghtml;

		$skin_name = $login->getSpecialObject('sp_specialplay')->skin_name;

		$as_simple_skin = $login->getSpecialObject('sp_specialplay')->isOn('simple_skin');

		$skin_color = $login->getSkinColor();

		$skin_dir = $login->getSkinDir();

	//	$lightskincolor = $login->getSkinColor();
		$lightskincolor = "818fb0";

		$syncserver = $login->syncserver;
		$userid = $login->getId();

		if ($skin_name === 'feather') {
			$func = "onLoad=\"lxLoadBody();\"";
		} else {
			//	$func = "onLoad=\"lxLoadBody(); menu_load('{$skin_dir}/menu/purecss/menu.php' , '?s={$syncserver}&u={$userid}', 'menu_div');\"";
			//	$func = "onLoad=\"lxLoadBody(); menu_load('{$skin_dir}/menu/prodropdown/menu.php' , '?s={$syncserver}&u={$userid}', 'menu_div');\"";
			//	$func = "onLoad=\"lxLoadBody(); menu_load('{$skin_dir}/menu/menutemplate2/menu.php' , '?s={$syncserver}&u={$userid}', 'menu_div');\"";
			$func = "";
		}

		if ($sgbl->isBlackBackground()) {
			$bodycolor = "000";
		}

		if ($skin_name === 'simplicity') {
			$skin_background = $login->getSpecialObject('sp_specialplay')->skin_background;
			$bodybackground = "url(/theme/background/{$skin_background}) center; background-size: cover; background-attachment: fixed";
			$bodycolor = "ffffff";
		} else {
			$bodybackground = "";
			if ($as_simple_skin) {
				$bodycolor = $lightskincolor;
			} else {
				$bodycolor = "fff";
			}
		}
?>

		<body <?= $func ?> style="border:0; margin:0; padding:0; background:#<?= $bodycolor ?> <?= $bodybackground ?>;">
		<!-- "START TOP MENU + LOGO" -->
<?php
		if ($skin_name === 'simplicity') {
			// MR -- mod from http://www.daniweb.com/web-development/javascript-dhtml-ajax/threads/184021/loading-an-html-file-into-a-div-from-a-link
		/*
?>
		<script type="text/javascript">
		<!--
			function load_process(path, query, target) {
				url = window.location.protocol + "//" + window.location.host + path + query;

			//	document.getElementById(target).innerHTML = ' Fetching data...';
				document.getElementById(target).innerHTML = '<span style="color:#fff;">Wait for menu loading...</span>';

				if (window.XMLHttpRequest) {
					req = new XMLHttpRequest();
				} else if (window.ActiveXObject) {
					req = new ActiveXObject("Microsoft.XMLHTTP");
				}

				if (req != undefined) {
					req.onreadystatechange = function() {
						load_done(url, target);
					};

					req.open("GET", url, true);
					req.send("");
				}
			}

			function load_done(url, target) {
				if (req.readyState == 4) { // only if req is "loaded"
					if (req.status == 200) { // only if "OK"
						document.getElementById(target).innerHTML = req.responseText;
					} else {
						document.getElementById(target).innerHTML = "Error: " + req.status + req.statusText + " " + url;
					}
				}
			}

			function menu_load(path, query, div) {
				load_process(path, query, div);
				return false;
			}
		//-->
		</script>
<?php
		*/
?>

			<div
				style="position: fixed; width:100%; top:0; height:30px; margin:0; padding:0; background-color: #e74c3c;"
				class="shadow_all">
				<div id="menu_div"
				     style="width:720px; background-color: #16a085; border: 0; margin:0 auto 0 auto; height:30px; padding:5px; vertical-align:middle"
				     class="shadow_all"><? include_once "theme/skin/simplicity/default/menu/menutemplate2/menu.php" ?></div>

				<script type="text/javascript">
					<!--
					function toggle_wrapper(id) {
						var e = document.getElementById(id);
						if (e.style.display != 'none') {
							e.style.display = 'none';
						} else {
							e.style.display = 'block';
						}
					}
					//-->
				</script>

				<div style="position: fixed; top: 3px; right: 3px"><a href="#"
				                                                      onClick="javascript:toggle_wrapper('mmm');">
						<div style="color: #fff; margin:2px; padding: 3px; background-color: #3498db; border:0;"
						     onMouseOver="this.style.backgroundColor='#fff'; this.style.color='#000';"
						     onMouseOut="this.style.backgroundColor='#3498db'; this.style.color='#fff';">&nbsp;Show/Hide&nbsp;</div>
					</a></div>

			</div>

			<div style="position: fixed; right:10px; top:40px;"><a href="http://mratwork.com"><img
						src="/login/images/kloxo-mr.png" height="60"/></a></div>
			<!-- "END TOP MENU + LOGO" -->
<?php
		}

		if (($as_simple_skin) || ($skin_name === 'simplicity')) {
			if ($skin_name === 'simplicity') {
				$margin_top = '60';
				$border = '0';
				$bgcolor = "";
				$bgcolor = "background-color:#f0f8ff";
			} else {
				$margin_top = '10';
				$border = '4px double #ddd';
				$bgcolor = "background-color:#fff";
			}
?>

			<!-- "START TAB + CONTENT" -->
			<div class="shadow_all" id="mmm" style="padding:0; width:960px; margin:<?= $margin_top ?>px auto 10px auto; border: <?= $border ?>; <?= $bgcolor ?>">
<?php
		}

		return;
	}

	function print_splash_js_function()
	{
?>

		<script>
			function coverScreen(flag) {
				var coverob = document.getElementById('coverscreen');

				if (!coverob) {
					return;
				}

				var x, y;

				if (self.innerHeight) {
					x = self.innerWidth;
					y = self.innerHeight;
				} else if (document.documentElement && document.documentElement.clientHeight) {
					x = document.documentElement.clientWidth;
					y = document.documentElement.clientHeight;
				} else if (document.body) {
					x = document.body.clientWidth;
					y = document.body.clientHeight;
				}

				x = x - 20;

				coverob.style.zIndex = 2;
				coverob.style.position = 'absolute';
				coverob.style.left = 0;
				coverob.style.top = 0;
				coverob.style.width = x;
				coverob.style.height = y;

				if (!flag) {
					coverob.style.display = 'none';
					coverob.style.visibility = 'hidden';
				} else {
					coverob.style.display = 'block';
					coverob.style.visibility = 'visible';
				}
			}

			function splashScreen(flag) {
				var splashob = document.getElementById('splashscreen');

				if (!splashob) {
					return;
				}

				var x, y;

				if (self.innerHeight) {
					x = self.innerWidth;
					y = self.innerHeight;
				} else if (document.documentElement && document.documentElement.clientHeight) {
					x = document.documentElement.clientWidth;
					y = document.documentElement.clientHeight;
				} else if (document.body) {
					x = document.body.clientWidth;
					y = document.body.clientHeight;
				}

				var top = 0;
				var left = x - 215;
				if (left <= 0) {
					left = 5;
				}

				if (flag) {
					splashob.style.visibility = 'visible';
					splashob.style.display = 'block';
				} else {
					splashob.style.visibility = 'hidden';
					splashob.style.display = 'none';
				}

				splashob.style.zIndex = 5;
				splashob.style.left = left + "px";
				splashob.style.top = top + "px";
				splashob.style.position = 'absolute';
			}
		</script>
<?php
	}

	function print_splash()
	{
	/*
			// MR -- it's only for 'default' skin
?>

			<script>
				if (top.topframe && typeof top.topframe.changeLogo == 'function') {
					top.topframe.changeLogo(1);
				}
			</script>

<?php
	*/
	}

	function print_start()
	{
		global $gbl, $sgbl, $login;

		$img_path = $login->getSkinDir();
		$tbg = $img_path . "/images/lp_bg.gif";
		$imgbordermain = $img_path . "/images/top_line_medium.gif";

		$this->print_include_jscript();

		if ($login->getSpecialObject('sp_specialplay')->skin_name === 'feather') {
			$bgcolor = "bgcolor=#fff";
		} else {
			$bgcolor = "";
		}
	}

	function fix_post_pre_stuff($key)
	{
		$val = $this->__http_vars[$key];
		$realname = substr($key, strlen('frm_'));
		$prevar = 'frm_pre_' . $realname;

		if ($this->frmiset($prevar)) {
			$val = $this->gfrm($prevar) . $val;
		}

		$prevar = 'frm_post_' . $realname;

		if ($this->frmiset($prevar)) {
			$val .= $this->gfrm($prevar);
		}

		return $val;
	}

	function do_modify_obj($cobj)
	{
		$class = strtolower(get_class($cobj));
	}

	function modify_object($cobj)
	{
		$this->do_modify_obj($cobj);
		$cobj->dbaction = "modify";
	}

	function print_about()
	{
		global $gbl, $sgbl, $login, $ghtml;

		$img_path = $login->getSkinDir();

		$tbg = $img_path . "/images/lp_bg.gif";

		$imgbordermain = $img_path . "/images/top_line.gif";

		$icondir = get_image_path();
?>

		<table cellpadding=0 cellspacing=0>
			<tr>
				<td><img src='<?= $icondir ?>/aboutus.jpg'></td>
			</tr>
		</table>
<?php
	}

	function print_end()
	{
		global $gbl, $sgbl, $login;

		$img_path = $login->getSkinDir();
		$tbg = $img_path . "/images/lp_bg.gif";

		$imgbordermain = $img_path . "/images/top_line.gif";

		return;
	}

	function print_sortby($parent, $class, $unique_name, $sortby, $descr)
	{
		global $gbl, $sgbl, $login, $ghtml;

		$filtername = $parent->getFilterVariableForThis($class);

		$desc = $descr[2];

		$help = $descr['help'];

		$alt = lx_strip_tags($help);
		$url = $_SERVER['PHP_SELF'];

		if (!$desc) {
			$desc = ucfirst($sortby);
		}

		$help = str_replace('"', '', $help);
		$help = str_replace("'", "", $help);

		if (char_search_a($descr[0], "b") || char_search_a($descr[0], "S")) {
			// hack...
			if (!$alt) {
				$d = $alt;
			} else {
				$d = $desc;
			}

?>

			<span class="tableheadtext"
			      onmouseover="changeContent('help','<b>Message </b>: <br /> <br /> <?= $help ?>')"
			      onmouseout="changeContent('help','helparea')"> <?= $d ?> </span>

<?php

			return;
		}

		$fil = $login->getHPFilter();
		$sortdir = null;
		$nsortby = null;

		if (isset($fil[$filtername]['sortby'])) {
			$nsortby = $fil[$filtername]['sortby'];
		}

		if (isset($fil[$filtername]['sortdir'])) {
			$sortdir = $fil[$filtername]['sortdir'];
		}

		if ($nsortby === $sortby) {
			$sortdir = ($sortdir === "desc") ? "asc" : "desc";
		}

		$formname = 'lpform_' . $unique_name . $sortby;
?>

		<form name="<?= $formname ?>" method="<?= $sgbl->method ?>" action="<?= $url ?>" accept-charset="utf-8">
			<?= $this->print_current_input_vars(array('frm_hpfilter')) ?>

			<input name="frm_hpfilter[<?= $filtername ?>][sortby]" type="hidden" value="<?= $sortby ?>">
			<input name="frm_hpfilter[<?= $filtername ?>][sortdir]" type="hidden" value="<?= $sortdir ?>">
		</form>

		<span title='<?= $alt ?>'><a class='tableheadtext'
		                             href="javascript:document.<?= $formname ?>.submit()"><?= $desc ?> </a> </span>

<?php
	}

	function print_search($parent, $class)
	{
		global $gbl, $sgbl, $login;

		$url = $_SERVER['PHP_SELF'];
		$gen_image_path = get_general_image_path();
		$btnpath = $gen_image_path . "/icon/";

		$filtername = $parent->getFilterVariableForThis($class);
		$blackstyle = null;

		if ($sgbl->isBlackBackground()) {
			$blackstyle = "style='background:black;color:gray;border:1px solid gray;'";
		}

		$value = null;

		if ($login->issetHpFilter($filtername, 'searchstring')) {
			$value = $login->getHPFilter($filtername, 'searchstring');
		}

		$showallimg = "$btnpath/showall_b.gif";
		$searchimg = "$btnpath/search_b.gif";

		if ($sgbl->isBlackBackground()) {
			$showallimg = null;
			$searchimg = null;
		}
?>

		<table width="100%" border="0" cellpadding="0">
			<tr>
				<td>
					<table width="100%" cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td width="60%">&nbsp;</td>
							<td height="22" width="40%" align="right">
								<table cellpadding="0" cellspacing="0" border="0" width="200">
									<tr>
										<td width="10" height="22"></td>
										<td height="22">
											<form name="lpform_search" method="<?= $sgbl->method ?>"
											      action="<?= $url ?>"
											      onsubmit="return checksearch(this,1);" accept-charset="utf-8">

												<?= $this->print_current_input_var_unset_filter($filtername, array('sortby', 'sortdir', 'pagenum')) ?>
												<?= $this->print_current_input_vars(array("frm_hpfilter")) ?>

												<input <?= $blackstyle ?> type="text"
												                          name="frm_hpfilter[<?= $filtername ?>][searchstring]"
												                          value="<?= $value ?>" class=searchbox
												                          size="18">
											</form>
										</td>
										<td width="10" height="22">&nbsp;</td>
										<td height="22" width="20"><a href='javascript:document.lpform_search.submit()'><img
													border="0" alt="Search" title="Search" name="search"
													src="<?= $searchimg ?>" height="15" width="15"
													onMouseOver="changeContent('help','search');"
													onMouseOut="changeContent('help','helparea');"></a></td>
										<td width="30" height="22">&nbsp;&nbsp;&nbsp;</td>
										<td height="22" width="70">
											<form name="lpform_showall" method="<?= $sgbl->method ?>"
											      action="<?= $url ?>" accept-charset="utf-8">
												<?= $this->print_current_input_vars(array("frm_hpfilter")) ?>

												<input type="hidden" name="frm_clear_filter" value="true">
												<table cellpadding="0" cellspacing="0" border="0" width="100%"
												       height="22">
													<tr>
														<td height="22" width="31%" align="center" nowrap>&nbsp;<a
																href="javascript:document.lpform_showall.submit();"><img
																	alt="Show All" title="Show all" name="showall"
																	src="<?= $showallimg ?>"
																	onMouseOver="changeContent('help','showall');"
																	onMouseOut="changeContent('help','helparea');"></a>
														</td>
														<td width="69%" height="22" nowrap>&nbsp;<a
																href="javascript:document.lpform_showall.submit();"
																onMouseOver="changeContent('help','showall');"
																onMouseOut="changeContent('help','helparea');"><span
																	class="small">Show All</span></a></td>
													</tr>
												</table>
											</form>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>

				</td>
			</tr>
		</table>

<?php
	}

	function getClass()
	{
		return 'Html';
	}

//	function printTabForTabButton($key, $linkflag, $height, $imageheight, $sel, $imgbg, $formname, $name, $imagesrc, $descr, $check)
	function printTabForTabButton($key, $linkflag, $height, $imageheight, $sel, $imgbg, $url, $name, $imagesrc, $descr, $check)
	{
		global $gbl, $sgbl, $login;

		$help = $descr['help'];
		$imgstr = null;

		if ($imagesrc) {
			$imgstr = "<img width='$imageheight' height='$imageheight' src='$imagesrc'>";
		}

		if ($linkflag) {
			if ($login->getSpecialObject('sp_specialplay')->isOn('enable_ajax') && csb($key, "__v_dialog")) {
				$displaystring = "<span title='$help'>  $descr[2] </span>";
			} else {
				//	$displaystring = "<span title='$help'> <a href=\"javascript:document.form_{$formname}.submit()\"> $descr[2]</a> </span>";
				$displaystring = "<span title='$help'> <a href=\"$url\"> $descr[2]</a> </span>";
			}

		} else {
			$displaystring = "<span title=\"You don't have permission\">$descr[2] </span>";
		}

		if ($check) {
?>

			<td height="34" wrap class="alink"
			    style='cursor:pointer; padding:3px 0 0 0; vertical-align:middle'><?= $imgstr ?> </td>
			<td height="<?= $height ?>" nowrap class="alink"
			    style='cursor:pointer; padding:3px 0 0 0; vertical-align:middle'><span size=-1><?= $displaystring ?>
			</td>
<?php
		} else {
?>

			<td height="34" wrap class=alink
			    style='cursor:pointer;background:#efe8e0 url(<?= $imgbg ?>); padding:3px 0 0 0; vertical-align:middle'><?= $imgstr ?> </td>
			<td height="<?= $height ?>" nowrap class=alink
			    style='cursor:pointer;background:#efe8e0 url(<?= $imgbg ?>); padding:3px 0 0 0; vertical-align:middle'><span
					size=-1><?= $displaystring ?></td>
<?php
		}
	}

	function print_content_begin()
	{
		global $gbl, $sgbl, $login;

		$skin_name = $login->getSpecialObject('sp_specialplay')->skin_name;
		$as_simple_skin = $login->getSpecialObject('sp_specialplay')->isOn('simple_skin');

		$bordering = "border: 1px solid #ddd; border-top:0";

		if ($skin_name === 'feather') {
			if (!$as_simple_skin) {
				$bordering = "border: 0";
			}
		}

		// MR -- trouble for height if using div-div; so change to div-table
?>

	<!-- "START CONTENT" -->
	<div id="content_wrapper" style="min-height: 100%; height:auto !important; height:100%; width:100%; overflow:hidden">
		<!-- <div style="text-align:center; width:100%; height: 100%; min-height: 100%; height: auto !important;<?= $bordering ?>; background-color: #fff"> -->
		<table style="width: 100%; height: 100%; border: 0; margin: 0; padding: 0; <?= $bordering ?>; background-color: #fff"><tr><td style="vertical-align:top;">
		<br/>
<?php
	}

	function print_content_end()
	{
?>
			</td>
		</tr>
	</table>
		<!-- </div> -->
<?php

	}

	function print_favorites()
	{
		global $gbl, $sgbl, $login, $ghtml;

		$back = $login->getSkinDir();
		$list = $this->get_favorite("ndskshortcut");

		$vvar_list = array('ttype', '_t_image', 'url', 'target', '__t_identity', 'ac_descr', 'str', 'tag');

		$res = null;

		foreach ((array)$list as $l) {
			foreach ($vvar_list as $vvar) {
				$vvar = isset($l[$vvar]) ? $l[$vvar] : '';
			}

			if ($ttype == 'separator') {
				$res .= "<tr valign=top style=\"border-width:1px; background:url($back/a.gif);\"> <td ></td> </tr>";
			} else {
				$res .= "<tr valign=top style=\"border-width:1px; background:url($back/a.gif);\"> <td > <span title=\"$ac_descr[2] for $__t_identity\"> <img width=16 height=16 src=$_t_image> <a href=$url target=$target>  $str $tag</a></span></td> </tr>";
			}
		}

		return $res;
	}

	function print_quick_action($class)
	{
		global $gbl, $sgbl, $login;

		$iconpath = get_image_path();

		if ($class === 'self') {
			$object = $login;
			$class = $login->getClass();
		} else {
			$list = $login->getVirtualList($class, $count);
			$object = getFirstFromList($list);
		}

		if (!$object) {
			return "No Object";
		}

		$namelist = get_namelist_from_objectlist($list);

		$alist = $this->get_quick_action_list($object);

		foreach ($alist as $a) {
			$ac_descr = $this->getActionDetails($a, null, $iconpath, $path, $post, $_t_file, $_t_name, $_t_image, $__t_identity);
		}

		$stylestr = "style=\"font-size: 10px\"";

		$res = null;
		$res .= " <tr style=\"background:#d6dff7\"> <td >";
		$res .= "<form name=quickaction method={$sgbl->method} target=mainframe action=\"/theme/lbin/redirect.php\">";

		$desc = $this->get_class_description($class);
		//	$res .= "$desc[2] <br /> ";

		if (!$object->isLogin()) {
			$res .= "<select $stylestr name=frm_redirectname>";

			foreach ($namelist as $l) {
				$pl = substr($l, 0, 26);
				$res .= '<option ' . $stylestr . ' value="' . $l . '" >' . $pl . '</option>';
			}

			$res .= "</select> </td> </tr>  ";
		}

		$res .= " <tr style=\"background:#d6dff7\"> <td ><select $stylestr name=frm_redirectaction>";

		foreach ($alist as $k => $a) {
			if (csb($k, "__title")) {
				$res .= '<option value="" >------' . $a . '----</option>';

				continue;
			}

			$ac_descr = $this->getActionDetails($a, null, $iconpath, $path, $post, $_t_file, $_t_name, $_t_image, $__t_identity);

			$a = base64_encode($a);
			//	$res .= "<option value=$a style='background-image: url($_t_image); background-repeat:no-repeat; ";
			//	$res .= "left-padding: 35px; text-align:right'>  $ac_descr[2] </option>";

			$desc = substr($ac_descr[2], 0, 20);
			$res .= '<option ' . $stylestr . ' value="' . $a . '" >' . $desc . '</option>';
		}

		$res .= "</select> </td> </tr> ";
		$res .= "</form> <tr > <td align=right> <a href=javascript:quickaction.submit() > Go </a> </td> </tr> ";

		return $res;
	}

	function get_favorite($class)
	{
		global $gbl, $sgbl, $login;

		$shortcut = $login->getVirtualList($class, $count);

		$res = null;
		$ret = null;
		$iconpath = get_image_path();

		if ($shortcut) {
			foreach ($shortcut as $k => $h) {
				if (!is_object($h)) {
					continue;
				}

				if ($h->isSeparator()) {
					$res['ttype'] = 'separator';
					$ret[] = $res;
					continue;
				}

				$res['ttype'] = 'favorite';

				$url = base64_decode($h->url);

				// If the link is from kloxo, it shouldn't throw up a lot of errors. Needs to fix this properly..
				$ac_descr = $this->getActionDetails($url, null, $iconpath, $path, $post, $_t_file, $_t_name, $_t_image, $__t_identity);

				if ($sgbl->isHyperVM() && $h->vpsparent_clname) {
					$url = kloxo::generateKloxoUrl($h->vpsparent_clname, null, $url);
					$tag = "(l)";
				} else {
					$tag = null;
				}

				if (isset($h->description)) {
					$str = $h->description;
				} else {
					$str = "$ac_descr[2] $__t_identity";
				}

				$fullstr = $str;

				if (strlen($str) > 18) {
					$str = substr($str, 0, 18);
					$str .= "..";
				}

				$str = htmlspecialchars($str);
				$target = "mainframe";

				if (is_object($h) && $h->isOn('external')) {
					$target = "_blank";
				}

				$vvar_list = array('_t_image', 'url', 'target', '__t_identity', 'ac_descr', 'str', 'tag', 'fullstr');

				foreach ($vvar_list as $vvar) {
					$res[$vvar] = $vvar;
				}

				$ret[] = $res;
			}
		}

		return $ret;
	}

	function get_quick_action_list($object)
	{
		global $gbl, $sgbl, $login, $ghtml;

		$class = $object->getClass();

		$object->createShowAlist($alist);

		foreach ($alist as $k => $v) {
			if (csb($k, "__title")) {
				$nalist[$k] = $v;
				continue;
			}

			if ($this->is_special_url($v)) {
				continue;
			}

			if (csa($v, "a=update&")) {
				continue;
			}

			if ($object->isLogin()) {
				$nalist[$k] = $this->getFullUrl($v);
			} else {
				$nalist[$k] = $this->getFullUrl("j[class]=$class&j[nname]=__tmp_lx_name__&$v");
			}
		}

		return $nalist;
	}
}

