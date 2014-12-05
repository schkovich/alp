<?php
// string getting function <- technical term

function get_lang($desc) {
	global $lang;
	if (!empty($lang[$desc])) {
		return $lang[$desc];
	}
    return '';
}
?>