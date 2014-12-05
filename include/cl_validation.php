<?php
class validate {
	var $_err;

	function validate() {
		$this->reset_errors();
	}

	function get_value_unclean($field) {
        return ((!empty($_POST[$field]) || ($_POST[$field]==0))?$_POST[$field]:'');
	}
	
	function get_value($field) {
		$this->clean_tags($field);
		$magicquotes = ini_get('magic_quotes_gpc');
        if ($magicquotes){
            $postone = $_POST[$field];
            $getone = $_GET[$field];
        }else{
            $postone = mysql_escape_string($_POST[$field]);
            $getone = mysql_escape_string($_GET[$field]);
        }
        return ((!empty($_POST[$field]) || $_POST[$field]==0)?$postone:$getone);
	}
	
	function set_value($field, $value) {
		$_POST[$field] = $value;
	}
	
	function add_error($msg) {
		if($msg!='') $this->_err[] = $msg;
	}
	
	function is_empty($field, $msg) {
		$value = $this->get_value($field);
		if (trim($value) == "")	{
			$this->add_error($msg);
			return false;
		} else {
			return true;
		}
	}
	
	function is_str($field, $msg)	{
		$value = $this->get_value($field);
		if(!is_string($value)) {
			$this->add_error($msg);
			return false;
		} else {
			return true;
		}
	}
	
	function is_num($field, $msg) {
		$value = $this->get_value($field);
		if(!is_numeric($value))	{
			$this->add_error($msg);
			return false;
		} else {
			return true;
		}
	}

	function is_minimum($field, $msg, $min) {
		$value = $this->get_value($field);
		if(strlen($value)<$min) {
			$this->add_error($msg);
			return false;
		} else {
			return true;
		}
	}
	
	function is_inrange($field, $msg, $min, $max) {
		if(!is_numeric($field)) {
			$value = $this->get_value($field);
		} else {
			$value = $field;
		}
		if(!is_numeric($value) || $value < $min || $value > $max) {
			$this->add_error($msg);
			return false;
		} else {
			return true;
		}
	}
	
	function is_alpha($field, $msg) {
		$value = $this->get_value($field);
		if(preg_match("/^[a-zA-Z]+$/", $value)) {
			return true;
		} else {
			$this->add_error($msg);
			return false;
		}
	}
	
	function is_email($field, $msg) {
		$value = $this->get_value($field);
		if(preg_match("/^([a-zA-Z0-9])+([\.a-zA-Z0-9_-])*@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]+)+/", $value)) {
			return true;
		} else {
			$this->add_error($msg);
			return false;
		}
	}
	
	function is_match($bool,$msg) {
		if($bool) {
			$this->add_error($msg);
			return true;
		} else {
			return false;
		}
	}
	
	function is_same($field_one,$field_two,$msg) {
		if($field_one==$field_two) {
			return true;
		} else {
			$this->add_error($msg);
			return false;
		}
	}
	
	function clean_tags($field) {
		$value = $this->get_value_unclean($field);
		$this->set_value($field,str_replace('<','&lt;',$value));
		$value = $this->get_value_unclean($field);
		$this->set_value($field,str_replace('>','&gt;',$value));
	}

	function is_error() {
		if (sizeof($this->_err) > 0) {
			return true;
		} else {
			return false;
		}
	}
	
	function display_errors() { ?>
		your input sucked due to the following reasons: <br /><br />
		<?php
		foreach($this->_err as $val) { ?>
			&nbsp;&nbsp;<?php echo $val; ?><br />
			<?php
		} ?>
		<br /><br />please press the back button on your browser and try your again.<br />
		<?php
	}
	function get_errors() {
		return $this->_err;
	}
	
	function reset_errors() {
		$this->_err = array();
	}

}
?>