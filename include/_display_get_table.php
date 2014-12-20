<?php
// each table entry in $array will have the format:
// 			array('stylename','value')
global $dbc;

$array = array();
if(empty($_GET)||empty($_GET["show"])) {
	$temp = 0;
} else {
	$temp = $_GET["show"];
}
$r_counter = 1; // row counter
$c_counter = 1; // column counter;

$r_counter++;
$counter = 1;
$data = $dbc->database_query($query);
while($row = $dbc->database_fetch_assoc($data)) {
	$c_counter = 0;
	if(!empty($this->_count_bool)) $array[$r_counter][$c_counter] = array('', $counter); 
	$counter++;
	if(!empty($this->_default)) { 
		foreach($this->_default as $key => $field) {
			$interp = $field->get_interp();
			if(sizeof($field->get_link())>0) {
				$t = $field->get_link();
				if(stristr($t[0],"[".$key."]")) {
					$u = str_replace("[".$key."]",$row[$key],$t[0]);
				} else {
					$u = str_replace("[".$this->_id."]",$row[$this->_id],$t[0]);
				}
				if(sizeof($interp)==3) { 
					$tempor = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM ".$interp[0]." WHERE ".$interp[1]."='".$row[$key]."'"));
					$holder = $tempor[$interp[2]];
				} else {
					$holder = $row[$key];
				}
				$bt = '<a href="'.$u.'">'.(sizeof($t)==1?$holder:(sizeof($t)==2?"<b>".$t[1]."</b>":"")).'</a>';
			} else { 
				if(sizeof($interp)==3) { 
					$tempor = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM ".$interp[0]." WHERE ".$interp[1]."='".$row[$key]."'"));
					$bt = $tempor[$interp[2]];
				} else {
					$bt = $row[$key];
				}
			}
			$array[$r_counter][$c_counter] = array('',$bt);
			$c_counter++;
		}
	}
	foreach($this->_tables[$temp] as $key => $field) {
		if(current_security_level()>=$field->get_security()) { 
			$interp = $field->get_interp();
			$crutch = $field->get_crutch();
			if(!empty($row[$key])&&(sizeof($crutch)==0||$dbc->database_num_rows($dbc->database_query("SELECT * FROM ".$crutch[1]." WHERE ".$crutch[0]." AND ".$crutch[2]."=".$row[$this->_id])))||(!empty($key)&&sizeof($field->get_link())>0)) {
				if(sizeof($field->get_list())>0) {
					$t = $field->get_list();
					if(!empty($t[$row[$key]])) {
						$bt = $t[$row[$key]];
					} else {
						$bt = $row[$key];
					}
				} elseif(sizeof($field->get_link())>0) {
					$t = $field->get_link();
					if(stristr($t[0],"[".$key."]")) {
						$u = str_replace("[".$key."]",urlencode($row[$key]),$t[0]);
					} elseif(stristr($t[0],"[".$this->_id."]")) {
						$u = str_replace("[".$this->_id."]",urlencode($row[$this->_id]),$t[0]);
					} else {
						$u = $t[0];
						foreach($row as $i_key => $i_val) {
							if(stristr($u,"[".$i_key."]")) {
								$u = str_replace("[".$i_key."]",urlencode($row[$i_key]),$u);
							}
						}
					}
					if(sizeof($interp)==3) { 
						$tempor = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM ".$interp[0]." WHERE ".$interp[1]."='".$row[$key]."'"));
						$holder = $tempor[$interp[2]];
					} else {
						$holder = $row[$key];
					}
					$bt = "<a href=\"".urldecode($u)."\">".(sizeof($t)==1?$holder:(sizeof($t)==2?"<b>".$t[1]."</b>":""))."</a>";
				} elseif($field->get_date()!="") {
					$bt = "<font color=\"".(date("U",strtotime($row[$key]))>date("U")?$colors["primary"]:(date("U",strtotime($row[$key]))<date("U")?$colors["secondary"]:""))."\">".date($field->get_date(),strtotime($row[$key]))."</font>";
					if((date("U",strtotime($row[$key]))-date("U"))<3600&&(date("U",strtotime($row[$key]))-date("U"))>0) {
						$bt .= "&nbsp;&nbsp;".round((date("U",strtotime($row[$key]))-date("U"))/60)." minutes";
					}
				} else { 
					if(sizeof($interp)==3) { 
						$tempor = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM ".$interp[0]." WHERE ".$interp[1]."='".$row[$key]."'"));
						$bt = $tempor[$interp[2]];
					} else {
						$bt = $row[$key];
					}
				}
			} else {
				$bt = '&nbsp;';
			}
			$array[$r_counter][$c_counter] = array('',$bt);
			$c_counter++;
		}
    }
	$r_counter++;
} ?>