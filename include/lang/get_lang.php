<?php
// string getting function <- technical term
function get_lang($desc, $echo =0) {
	global $lang;
	$filename = substr(get_script_name(),strrpos(get_script_name(),"/")+1,-4);
	if (!empty($lang[$filename][$desc])) {
		if($echo == 1) {
			echo $lang[$filename][$desc];
			return true;
		} else {		
			return $lang[$filename][$desc];
		}
	}
	if (!empty($lang["global"][$desc])) {
		if($echo == 1) {
			echo $lang["global"][$desc];
			return true;
		} else {	
			return $lang["global"][$desc];
		}
	}
    return '';
}
?>