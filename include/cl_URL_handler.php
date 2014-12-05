<?php
class URL_handler {
	var $_GET_str = '';
	
	function URL_handler() {
		$this->_GET_str = $this->create_GET_str();
	}
	
	function create_GET_str($replace_key = '', $replace_value = '', $excluding = array()) {
		if(sizeof($_GET) > 0) {
			$counter = 0;
			$str = '?';
			foreach($_GET AS $key => $val) {
				$excluded = false;
				if(sizeof($excluding) > 0) {
					foreach($excluding AS $d_val) {
						if($key == $d_val) $excluded = true;
					}
				}
				if(!$excluded) {
					$str .= ($counter!=0 ? '&' : '') . $key . '=' . ($key === $replace_key ? $replace_value : $val);
					$counter++;
				}
			}
			return $str;
		} elseif(!empty($replace_key) && !empty($replace_value)) {
			$str = '?';
			$str .= $replace_key . '=' . $replace_value;
			return $str;
		} else {
			return '';
		}
	}
	
	function add_GET_to_str($key, $value, $excluding = array()) {
		if(isset($_GET[$key])) {
			$str = $this->create_GET_str($key, $value, $excluding);
		} else {
			if(!empty($this->_GET_str)) {
				$str = $this->create_GET_str('','',$excluding) . '&' . $key . '=' . $value;
			} else {
				$str = '?' . $key . '=' . $value;
			}
		}
		return $str;
	}
	
	function get_GET_str() {
		return $this->_GET_str;
	}
	
	function get_hidden_form_elements($excluding=array()) {
		$str = '';
		foreach($_GET AS $key => $val) {
			$excluded = false;
			if(sizeof($excluding) > 0) {
				foreach($excluding AS $d_val) {
					if($key == $d_val) $excluded = true;
				}
			}
			if(!$excluded) {
				$str .= '<input type="hidden" name="'.$key.'" value="'.$val.'">';
			}
		}
		return $str;
	}

}
?>