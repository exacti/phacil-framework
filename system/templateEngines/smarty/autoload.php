<?php

include __DIR__."/Smarty.class.php";

function translate($params = '', $content = '', $smarty = '', &$repeat = '', $template = '') {
	if (class_exists('Translate')) {
		$trans = new Translate();
		return ($trans->translation($content));
	} else {
		return $content;
	}// do something translatey here...
}