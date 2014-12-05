<?php
include_once 'include/cl_table.php';
class form {
	var $_elements;
	var $_crutch;
	var $_formatcounter = 1;
	var $_rowcounter = 0;
	var $_current_table = array();
	var $_current_table_cells = array();
	var $_displaycounter = 0;
	
	function form($crutch='')
    {
		$this->_elements = array();
		$this->_crutch = $crutch;
	}
    
	function format($type)
    {
		if($type == 'table') {
			$this->_current_table[$this->_formatcounter] = new formatting_table;
			$this->_elements['secretformattingkeydonotuse'.$this->_formatcounter] = 'table';
		} elseif($type == 'endtable') {
			$this->_elements['secretformattingkeydonotuse'.$this->_formatcounter] = 'endtable';
		} elseif($type == 'row') {
			$this->_elements['secretformattingkeydonotuse'.$this->_formatcounter] = 'row';
			$this->_current_table_cells[$this->_rowcounter] = 0;
		} elseif($type == 'endrow') {
			$this->_elements['secretformattingkeydonotuse'.$this->_formatcounter] = 'endrow';
			$this->_rowcounter++;
		} elseif($type == 'cell') {
			$this->_elements['secretformattingkeydonotuse'.$this->_formatcounter] = 'cell';
			$this->_current_table_cells[$this->_rowcounter]++;
		} elseif($type == 'endcell') {
			$this->_elements['secretformattingkeydonotuse'.$this->_formatcounter] = 'endcell';
		}
		$this->_formatcounter++;
	}
    
	function startform($form_action, $method)
    { ?>
		<form action="<?php echo $form_action; ?>" method="<?php echo $method; ?>">
		<?php
	}
    
	function add_text		($name,$req,$mod,$crutch,$desc,$errors=array(),$max_length,$unclean=0)
    {
		$this->_elements[$name] = new form_element('text',$name,$req,$mod,$crutch,$desc,$errors,$max_length,$unclean);
	}
    
	function add_select		($name,$req,$mod,$crutch,$desc,$errors=array(),$array_choices,$empty_entry=1)
    {
		$this->_elements[$name] = new form_element('select',$name,$req,$mod,$crutch,$desc,$errors,$array_choices,0,$empty_entry);
	}
    
	function add_selectlist	($name,$req,$mod,$crutch,$desc,$errors=array(),$query,$id_field,$display_field,$empty_entry=1)
    {
		$this->_elements[$name] = new form_element('selectlist',$name,$req,$mod,$crutch,$desc,$errors,array($query,$id_field,$display_field),0,$empty_entry);
	}
    
	function add_radio		($name,$req,$mod,$crutch,$desc,$errors=array(),$array_choices)
    {
		$this->_elements[$name] = new form_element('radio',$name,$req,$mod,$crutch,$desc,$errors,$array_choices,0);	
	}
    
	function add_radiolist	($name,$req,$mod,$crutch,$desc,$errors=array(),$query,$id_field,$display_field)
    {
		$this->_elements[$name] = new form_element('radiolist',$name,$req,$mod,$crutch,$desc,$errors,array($query,$id_field,$display_field),0);	
	}
    
	function add_textarea	($name,$req,$mod,$crutch,$desc,$errors=array(),$rows,$unclean=0)
    {
		$this->_elements[$name] = new form_element('textarea',$name,$req,$mod,$crutch,$desc,$errors,$rows,$unclean);
	}
    
	function add_datetime	($name,$req,$mod,$crutch,$desc,$errors=array(),$array_comparison)
    {
		$this->_elements[$name] = new form_element('datetime',$name,$req,$mod,$crutch,$desc,$errors,$array_comparison,0);
	}
    
	function add_checkbox	($name,$req,$mod,$crutch,$desc,$errors=array(),$highlander=0)
    {
		// highlander is whether there can be only one in the database with a 'true' value.
		$this->_elements[$name] = new form_element('checkbox',$name,$req,$mod,$crutch,$desc,$errors,$highlander,0);
	}
    
	function add_hidden ($name,$value)
    { ?>
		<input type="hidden" name="<?php echo $name; ?>" value="<?php echo $value; ?>">
		<?php
	}
    
	function add_hidden_dos	($name,$value)
    {
		$this->_elements[$name] = new form_element('hidden',$name,0,0,0,'',array(),$value);
	}
    
	function add_displaystring	($value)
    {
		$this->_elements['secretformattingkeydonotuse'.'display'.$this->_displaycounter] = $value;
		$this->_displaycounter++;
	}

	function endform($submit_button,$size=160)
    { 
		global $colors; 
		if($size>0) $thesize = 'width: '.$size.'px;';
		else $thesize = '';
		?>
		<br />
		<div align="<?php echo ($size>0?'right':'center'); ?>"><input type="submit" value="<?php echo $submit_button; ?>" style="<?php echo $thesize.'"'; ?> class="formcolors" /></div>
		</form>
		<?php
	}
    
	function get_elements()
    {
		$temparray = array();
		foreach($this->_elements as $key => $val) {
			if(substr($key,0,27)!='secretformattingkeydonotuse') {
				$temparray[$key] = $val;
			}
		}
		return $temparray;
	}
    
	function num_elements()
    {
		return sizeof($this->_elements); 
	}
    
	function display_element($name,$mysql_fetch_assoc='',$id='')
    {
		// TODO: doesn't yet work with formatting.
		$element = $this->_elements[$name];
		if((func_num_args()==0)||($element->get_is_modifiable()&&!$element->get_is_dep_crutch())||($element->get_is_modifiable()&&$element->get_is_dep_crutch()&&!$mysql_fetch_assoc[$this->_crutch])) {
			$element->display($mysql_fetch_assoc[$element->get_name()],$id);
		} elseif(!$element->get_is_modifiable()) {
			$element->displayvalue($mysql_fetch_assoc[$element->get_name()],$id);
		}
	}
    
	function display_elements($mysql_fetch_assoc='', $id='')
    {
		$current_table = 0;
		$current_row = 0;
		foreach($this->_elements as $key => $val) {
			if(substr($key,0,27)=='secretformattingkeydonotuse') {
				if(substr($key,27,7)=='display') { ?>
					<font size="1"><br /></font>
					<?php echo $val; ?><br /><?php
				} elseif($val == 'table') {
					$current_table = substr($key,27);
					$this->_current_table[$current_table]->start_table();
				} elseif($val == 'endtable') {
					$this->_current_table[$current_table]->end_table();
				} elseif($val == 'row') {
					$this->_current_table[$current_table]->start_row();
				} elseif($val == 'endrow') {
					$this->_current_table[$current_table]->end_row();
					$current_row++;
				} elseif($val == 'cell') {
					$this->_current_table[$current_table]->start_cell();
				} elseif($val == 'endcell') {
					$this->_current_table[$current_table]->end_cell();
				}
			} else {
				if((func_num_args()==0)||($val->get_is_modifiable()&&!$val->get_is_dep_crutch())||($val->get_is_modifiable()&&$val->get_is_dep_crutch()&&empty($mysql_fetch_assoc[$this->_crutch]))) {
					if($current_table>0&&!empty($this->_current_table[$current_table])&&$this->_current_table[$current_table]->get_table_open()) {
						$numcells = $this->_current_table_cells[$current_row];
						$tempwidth = 400/$numcells;
						$val->display($mysql_fetch_assoc[$val->get_name()],$id,$tempwidth);
					} else {
						$ct = !empty($mysql_fetch_assoc[$val->get_name()]) ? $mysql_fetch_assoc[$val->get_name()] : '' ;
						$val->display($ct,$id);
					}
				} elseif(!$val->get_is_modifiable()) {
					$val->displayvalue($mysql_fetch_assoc[$val->get_name()],$id);
				}
			}
		}
	}
    
	function element_exists($name)
    {
		// if element with the name exists, return true.
		if(!empty($this->_elements[$name])) {
			return true;
		} else {
			return false;
		}
	}
    
	function element_is_modifiable($name)
    {
		return $this->_elements[$name]->get_is_modifiable();
	}
    
	function element_description($name)
    {
		return $this->_elements[$name]->get_description();
	}
}

class form_element {
	var $_type, $_name, $_is_required, $_description, $_is_modifiable, $_is_dep_crutch, $_unclean, $_empty_entry;
	var $_error;
	var $_specific;
    
	function form_element($type, $name, $req=0, $mod=0, $crutch=0, $desc="", $errors=array(), $extra="", $unclean=0, $empty_entry=1)
    {
		$this->_type = $type;
		$this->_name = $name;
		$this->_is_required = $req;
		$this->_is_modifiable = $mod;
		$this->_is_dep_crutch = $crutch;
		$this->_description = $desc;
		$this->_error = array();
		if(sizeof($errors)>0) {
			foreach($errors as $key => $val) {
				if(!empty($val)) $this->_error[$key] = $val;
			}
		}
		$this->_specific = $extra;
		$this->_unclean = $unclean;
		$this->_empty_entry = $empty_entry;
	}
    
	function displayvalue($value="",$id="") { 
		global $colors, $images, $dbc;
		if($this->_type=='text' || $this->_type=='textarea') { 
			$temp = $value;
		} elseif($this->_type=='select' || $this->_type=='radio') {
			$temp = $this->_specific[$value];
		} elseif($this->_type=='selectlist' || $this->_type=='radiolist') {
			$data = $dbc->database_query($this->_specific[0]);
			while($row = $dbc->database_fetch_assoc($data)) {
				if($value==$row[$this->_specific[1]]) {
					$temp = $row[$this->_specific[2]];
				}
			}
		} elseif($this->_type=='checkbox') {
			$temp = $this->_description.": ".($value?'yes':'no');
		} elseif($this->_type=='datetime') {
			$temp = date('d M Y',strtotime($value)).' at '.date('h:i A',strtotime($value));
		}
		if(!empty($temp)) {
			?>
			<img src="img/pxt.gif" width="1" height="2" border="0" alt="" /><br />
			<font size="1"><b><?php echo $this->_description; ?></b>: <font color="<?php echo $colors['primary']; ?>">(unmodifiable)</font><br /></font>
			<?php get_arrow(); ?>&nbsp;<b><?php echo $temp; ?></b><br />
			<img src="img/pxt.gif" width="1" height="2" border="0" alt="" /><br /><?php
		}
	}
	
	function display($value="",$id="",$width=400,$widthpercent=0)
    {
		global $dbc, $colors, $master, $start, $end;
		if($this->_type=='text') { 
			if(!empty($this->_description)) { ?>
				<font size="1"><b><?php echo $this->_description; ?></b><?php echo (!$this->_is_modifiable?" <font color=".$colors["primary"].">(unmodifiable)</font>":""); ?><?php echo ($this->_is_required?" <font color=".$colors["primary"].">(required)</font>":"");?><br /></font>
				<?php
			} ?>
			<input type="text" name="<?php echo (!empty($id)?$id."_":""); ?><?php echo $this->_name; ?>" maxlength="<?php echo $this->_specific; ?>" style="width: <?php echo $width.($widthpercent?"%":"px"); ?>"<?php echo (!empty($value)?" value=\"".$value."\"":""); ?> /><br />
			<?php
		} elseif($this->_type=='select') {
			if(!empty($this->_description)) { ?>
				<font size="1"><b><?php echo $this->_description; ?></b> <?php echo (!$this->_is_modifiable?" <font color=".$colors["primary"].">(unmodifiable)</font>":""); ?><?php echo ($this->_is_required?" <font color=".$colors["primary"].">(required)</font>":""); ?><br /></font>
				<?php
			} 
			if(sizeof($this->_specific)>1) { ?>
				<select name="<?php echo (!empty($id)?$id."_":""); ?><?php echo $this->_name; ?>" style="width: <?php echo $width.($widthpercent?"%":"px"); ?>"><?php if($this->_empty_entry) { ?><option value=""></option><?php } ?>
				<?php
				foreach($this->_specific as $key => $val) { ?>
					<option value="<?php echo $key; ?>"<?php echo ($value==$key?" selected":""); ?>><?php echo $val; ?></option>
					<?php
				} ?>
				</select><br />
				<?php
			} elseif(sizeof($this->_specific)==1) { 
				foreach($this->_specific as $key => $val) { 
					?><input type="hidden" name="<?php echo (!empty($id)?$id."_":""); ?><?php echo $this->_name; ?>" value="<?php echo $key; ?>">&nbsp;&nbsp;<b><?php echo $val; ?></b><br /><?php
				}
			} 
		} elseif($this->_type=='selectlist') {
			if(!empty($this->_description)) { ?>
				<font size="1"><b><?php echo $this->_description; ?></b> <?php echo (!$this->_is_modifiable?" <font color=".$colors["primary"].">(unmodifiable)</font>":""); ?><?php echo ($this->_is_required?" <font color=".$colors["primary"].">(required)</font>":""); ?><br /></font>
				<?php
			} 
			$data = $dbc->database_query($this->_specific[0]);
			if($dbc->database_num_rows($data)>0) { ?>
				<select name="<?php echo (!empty($id)?$id."_":""); ?><?php echo $this->_name; ?>" style="width: <?php echo $width.($widthpercent?"%":"px"); ?>"><?php if($this->_empty_entry) { ?><option value=""></option><?php } ?>
				<?php
				while($row = $dbc->database_fetch_assoc($data)) { ?>
					<option value="<?php echo $row[$this->_specific[1]]; ?>"<?php echo ($value==$row[$this->_specific[1]]?" selected":""); ?>><?php echo $row[$this->_specific[2]]; ?></option>
					<?php
				} ?>
				</select><br />
				<?php
			/* Not needed for now
			} elseif($dbc->database_num_rows($data)==1) {
				while($row = $dbc->database_fetch_assoc($data)) { 
					?><input type="hidden" name="<?php echo (!empty($id)?$id."_":""); ?><?php echo $this->_name; ?>" value="<?php echo $row[$this->_specific[1]]; ?>">&nbsp;&nbsp;<b><?php echo $row[$this->_specific[2]]; ?></b><br /><?php
				} */
			}
		} elseif($this->_type=='radio') { ?>
			<table width="100%" cellpadding="3" cellspacing="0"><tr>
				<?php
				if(!empty($this->_description)) { ?>
					<td><font size="1"><b><?php echo $this->_description; ?></b> <?php echo (!$this->_is_modifiable?" <font color=".$colors["primary"].">(unmodifiable)</font>":""); ?><?php echo ($this->_is_required?" <font color=".$colors["primary"].">(required)</font>":""); ?></font></td>
					<?php
			} ?>
				<td align="right"><font size="1">
					<?php
					foreach($this->_specific as $key => $val) { ?>
						<input type="radio" name="<?php echo (!empty($id)?$id."_":""); ?><?php echo $this->_name; ?>" value="<?php echo $key; ?>" class="radio"<?php echo ($value==$key?" checked":""); ?> /> <?php echo $val; ?>&nbsp;&nbsp;
						<?php
					} ?><br /></font>
				</td>
			</tr></table>
			<?php
		} elseif($this->_type=="radiolist") {
			if(!empty($this->_description)) { ?>
				<font size="1"><b><?php echo $this->_description; ?></b> <?php echo (!$this->_is_modifiable?" <font color=".$colors["primary"].">(unmodifiable)</font>":""); ?><?php echo ($this->_is_required?" <font color=".$colors["primary"].">(required)</font>":""); ?><br /></font>
				<?php
			}
			$data = $dbc->database_query($this->_specific[0]);
			while($row = $dbc->database_fetch_assoc($data)) { ?>
				&nbsp;&nbsp;<input type="radio" name="<?php echo (!empty($id)?$id."_":""); ?><?php echo $this->_name; ?>" value="<?php echo $row[$this->_specific[1]]; ?>" class="radio"<?php echo ($value==$row[$this->_specific[1]]?" checked":""); ?> /> <?php echo $row[$this->_specific[2]]; ?><br />
				<?php
			} 
		} elseif($this->_type=="textarea") {
			if(!empty($this->_description)) { ?>
				<font size="1"><b><?php echo $this->_description; ?></b> <?php echo (!$this->_is_modifiable?" <font color=".$colors["primary"].">(unmodifiable)</font>":""); ?><?php echo ($this->_is_required?" <font color=".$colors["primary"].">(required)</font>":""); ?><br /></font>
				<?php
			} ?>
			<textarea cols="" rows="<?php echo $this->_specific; ?>" name="<?php echo (!empty($id)?$id."_":""); ?><?php echo $this->_name; ?>" style="width: <?php echo $width.($widthpercent?"%":"px"); ?>"><?php echo $value; ?></textarea><br />
			<?php
		} elseif($this->_type=="checkbox") { ?>
			<img src="img/pxt.gif" width="1" height="6" border="0" alt="" /><br />
			<input name="<?php echo (!empty($id)?$id."_":""); ?><?php echo $this->_name; ?>" type="checkbox" value="1" class="radio"<?php echo ($value?" checked":""); ?> /> <b><?php echo $this->_description; ?></b> <?php echo (!$this->_is_modifiable?" <font color=".$colors["primary"].">(unmodifiable)</font>":""); ?><?php echo ($this->_is_required?" <font color=".$colors["primary"].">(required)</font>":""); ?><br />
			<img src="img/pxt.gif" width="1" height="6" border="0" alt="" /><br />
			<?php
		} elseif($this->_type=="datetime") {
			if(!empty($this->_description)) { ?>
				<font size="1"><b><?php echo $this->_description; ?></b><?php echo (!$this->_is_modifiable?" <font color=".$colors["primary"].">(unmodifiable)</font>":""); ?><?php echo ($this->_is_required?" <font color=".$colors["primary"].">(required)</font>":""); ?><br /></font>
				<?php
			}
			$i = 0;
			while($i<sizeof($this->_specific)&&!empty($this->_specific[$i])) {
				$holder = $this->_specific[$i];
				$i++;
			}
			if(!empty($value)) {
				$holder = strtotime($value);
			} ?>
			&nbsp;&nbsp;<font class="sm">date:</font><select name="<?php echo (!empty($id)?$id."_":""); ?><?php echo $this->_name; ?>_year"><?php
				if(!$master["alldates"] && !ALP_TOURNAMENT_MODE) {
					$starty = date('Y',$start);
					$endd = date("Y",$end)-date("Y",$start)+1;
				} else {
					$starty = date('Y');
					$endd = 6;
				}
				for($i=0;$i<$endd;$i++) {
					echo "<option value=".($starty + $i)."".(($starty+$i)==date("Y",$holder)?" selected":"").">".($starty+$i)."</option>";
				} ?>
			</select>-<select name="<?php echo (!empty($id)?$id."_":""); ?><?php echo $this->_name; ?>_month"><?php
				$months = array("","Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
				if((date("n",$end)-date("n",$start))<0||date("Y",$end)!=date("Y",$start)||$master["alldates"] || ALP_TOURNAMENT_MODE) {
					for($i=1;$i<sizeof($months);$i++) {
						echo "<option value=".$i."".($i==date("n",$holder)?" selected":"").">".$months[$i]."</option>";
					}
				} else { 
					for($i=0;$i<(date("n",$end)-date("n",$start)+1);$i++) {
						echo "<option value=".(date("n",$start)+$i)."".((date("n",$start)+$i)==date("n",$holder)?" selected":"").">".$months[date("n",$start)+$i]."</option>";
					}
				} ?>
			</select>-<select name="<?php echo (!empty($id)?$id."_":""); ?><?php echo $this->_name; ?>_day"><?php
				if((date("j",$end)-date("j",$start))<0||(date("n",$start)!=date("n",$end))||(date("Y",$start)!=date("Y",$end))||$master["alldates"] || ALP_TOURNAMENT_MODE) {
					for($i=1;$i<32;$i++) {
						echo "<option value=".$i."".($i==date("j",$holder)?" selected":"").">".($i<10?"0":"").$i."</option>";
					}
				} else {
					for($i=0;$i<(date("j",$end)-date("j",$start)+1);$i++) {
						echo "<option value=".(date("j",$start)+$i)."".((date("j",$start)+$i)==date("j",$holder)?" selected":"").">".((date("j",$start)+$i)<10?"0":"").(date("j",$start)+$i)."</option>";
					}
				} ?>
			</select>&nbsp;&nbsp;&nbsp;<font class="sm">time:</font><select name="<?php echo (!empty($id)?$id."_":""); ?><?php echo $this->_name; ?>_hour"><?php
				if((date("G",$end)-date("G",$start))<0||(date("j",$start)!=date("j",$end))||(date("n",$start)!=date("n",$end))||(date("Y",$start)!=date("Y",$end))||$master["alldates"] || ALP_TOURNAMENT_MODE) {
					for($i=0;$i<24;$i++) {
						echo "<option value=".$i."".($i==date("G",$holder)?" selected":"").">";
						if ($i==0) {
							echo "12";
						} elseif ($i<13) {
							echo " $i";
						} else {
							echo $i-12;
						}
						echo ($i<12?" am":" pm");
						echo "</option>";
					}
				} else { 
					for($i=0;$i<(date("G",$end)-date("G",$start)+1);$i++) {
						echo "<option value=".(date("G",$start)+$i)."".((date("G",$start)+$i)==date("G",$holder)?" selected":"").">".(date("G",$start)+$i)."</option>";
					}
				} ?>
			</select>:<select name="<?php echo (!empty($id)?$id."_":""); ?><?php echo $this->_name; ?>_minute"><?php
				if((date("i",$end)-date("i",$start))<0||(date("G",$start)!=date("G",$end))||(date("j",$start)!=date("j",$end))||(date("n",$start)!=date("n",$end))||(date("Y",$start)!=date("Y",$end))||$master["alldates"] || ALP_TOURNAMENT_MODE) {
					for($i=0;$i<60;$i++) {
						echo "<option value=".$i."".($i==date("i",$holder)?" selected":"").">".($i<10?"0":"").$i."</option>";
					}
				} else { 
					for($i=0;$i<(date("i",$end)-date("i",$start)+1);$i++) {
						echo "<option value=".(date("i",$start)+$i)."".((date("i",$start)+$i)==date("i",$holder)?" selected":"").">".($i<10?"0":"").(date("i",$start)+$i)."</option>";
					}
				} ?>
			</select><input type="hidden" name="<?php echo (!empty($id)?$id."_":""); ?><?php echo $this->_name; ?>_second" value="0"><br />
			<?php
		} elseif($this->_type=="hidden") {
			?>
		<input type="hidden" name="<?php echo $this->_name; ?>" value="<?php echo $this->_specific; ?>">
		<?php
		}
	}
	function get_type() {
		// return string
		return $this->_type;
	}
	function get_name() {
		// return string
		return $this->_name;
	}
	function get_is_required() {
		// return boolean
		return $this->_is_required;
	}
	function get_is_unclean() {
		return $this->_unclean;
	}
	function get_is_modifiable() {
		// return boolean
		return $this->_is_modifiable;
	}
	function get_is_dep_crutch() {
		// return boolean
		return $this->_is_dep_crutch;
	}
	function get_description() {
		// return string
		return $this->_description;
	}
	function get_error($type) {
		// return string
		return $this->_error[$type];
	}
	function get_specific() {
		// text: maxlength integer
		// hidden: static value
		// select: choices array
		// selectlist: array(mysql query, id, display)
		// textarea: rows integer
		// datearea: comparison array for selection
		// checkbox: highlander (only one true)
		return $this->_specific;
	}
}
?>