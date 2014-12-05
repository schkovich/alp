<?php
class pager {
	var $_separator 	= '&nbsp;&nbsp;';
	var $_GET_var 		= 'u';
	var $_GET_per_var	= 'per';
	var $_per_var		= 50;
	var $_GET_start_var = 'start';
	var $_alphanumeric 	= array(	'A','B','C','D','E','F','G','H',
									'I','J','K','L','M','N','O','P',
									'Q','R','S','T','U','V','W','X',
									'Y','Z','Other','None');

	function pager() {
		
	}

	function get_per_var() {
		return $this->_per_var;
	}
	function get_GET_var() {
		return $this->_GET_var;
	}
	function get_GET_per_var() {
		return $this->_GET_per_var;
	}
	function get_GET_start_var() {
		return $this->_GET_start_var;
	}
	
	function change_GET_var($value) {
		$this->_GET_var = $value;
	}
	function change_per_var($value) {
		$this->_per_var = $value;
	}
	function change_GET_per_var($value) {
		$this->_GET_per_var = $value;
	}
	function change_GET_start_var($value) {
		$this->_GET_start_var = $value;
	}
	
	function change_separator($value) {
		$this->_separator = $value;
	}

	function display_lowercase_links(& $URL_handler, $label = 'view') {
		$this->display_links($URL_handler,$label,1);
	}

	function display_links(& $URL_handler, $label = 'view', $lowercase = 0) {
		$str = '<strong>'.$label.'</strong>: ';
		$counter = 0;
		foreach($this->_alphanumeric AS $val) {
			$str .= ($counter!=0 ? $this->_separator : '');
			$str .= '<a href="'.get_script_name();
			$str .= $URL_handler->add_GET_to_str($this->_GET_var,($val!='None' ? $val : ''),array('start'));
			$str .= '" class="menu">';
			$str .= (!empty($_GET[$this->_GET_var]) && $_GET[$this->_GET_var] === $val ? '<span class="title">' : '');
			$str .= ($lowercase ? strtolower($val) : $val);
			$str .= (!empty($_GET[$this->_GET_var]) && $_GET[$this->_GET_var] === $val ? '</span>' : '');
			$str .= '</a>';
			$counter++;
		}
		return $str;
	}

	function display_numeric_links(& $URL_handler, $num_rows) {
		global $colors;
		$per = $this->get_per();
		$num_pages = ceil($num_rows / $per);
		$str  = '<table border="0" width="100%" cellpadding="0" cellspacing="0"><tr><td valign="bottom" style="color: '.$colors['blended_text'].'"><span class="sm"><nobr>';
		$str .= '<img src="img/pxt.gif" width="1" height="4" border="0" /><br />';
		$str .= $num_rows.' items<br />';
		$str .= $num_pages.' pages&nbsp;&nbsp;&nbsp;<br /></span></td>';
		$str .= '<td valign="bottom" align="left" width="100%">';
		$sta = $this->get_GET_cur_min();
		$ena = $this->get_GET_cur_max($num_pages);
		
		// display link to first
		$str .= '<a href="';
		$str .= get_script_name();
		$str .= $URL_handler->add_GET_to_str($this->_GET_start_var,0);
		$str .= '" class="sm" style="color: '.$colors['primary'].'">&laquo; First</a>&nbsp;';
		
		if($num_pages > 1 && !empty($_GET[$this->_GET_start_var])) {
			$str .= '<a href="';
			$str .= get_script_name();
			$str .= $URL_handler->add_GET_to_str($this->_GET_start_var,$_GET[$this->_GET_start_var]-$per);
			$str .= '" class="sm" style="color: '.$colors['primary'].'">&laquo; Previous</a>&nbsp;';
		}
		if($sta > 1) {
			$str .= ' ... ';
		}
		for($i=$sta;$i<=$ena;$i++) {
			$str .= ($i!=$sta ? $this->_separator : '');
			if((empty($_GET[$this->_GET_start_var]) && $i==1) || (isset($_GET[$this->_GET_start_var]) && ($_GET[$this->_GET_start_var]/$per) === ($i-1))) {
				$str .= '<span class="title">';
			} else {
				$str .= '<a href="'.get_script_name();
				$str .= $URL_handler->add_GET_to_str($this->_GET_start_var,($i-1)*$per);
				$str .= '" class="menu">';
			}
			$str .= $i;
			if((empty($_GET[$this->_GET_start_var]) && $i==1) || (isset($_GET[$this->_GET_start_var]) && ($_GET[$this->_GET_start_var]/$per) === ($i-1))) {
				$str .= '</span>';
			} else {
				$str .= '</a>';
			}
			//$str .= ($i%20==0 ? '<br />' : '');
		}
		if($ena < $num_pages) {
			$str .= ' ... ';
		}
		if($num_pages > 1 && ( empty($_GET[$this->_GET_start_var]) || ($_GET[$this->_GET_start_var]+$per) < ($num_pages * $per) )) {
			$str .= '&nbsp;<a href="';
			$str .= get_script_name();
			$str .= $URL_handler->add_GET_to_str($this->_GET_start_var,(empty($_GET[$this->_GET_start_var])?$per:$_GET[$this->_GET_start_var]+$per));
			$str .= '" class="sm" style="color: '.$colors['primary'].'">Next &raquo;</a>';
		}
		
		// display link to end
		$str .= '&nbsp;<a href="';
		$str .= get_script_name();
		$str .= $URL_handler->add_GET_to_str($this->_GET_start_var,($num_pages>0 ? ($num_pages-1)*$per : 0));
		$str .= '" class="sm" style="color: '.$colors['primary'].'">End &raquo;</a>';

		$str .= '</td></tr></table>';
		return $str;
	}
	function get_GET_cur_max($num_pages) {
		$per = $this->get_per();
		if(empty($_GET[$this->_GET_start_var]) && $num_pages == 1) {
			$v = 1;
		} elseif($num_pages <= ((!empty($_GET[$this->_GET_start_var])?$_GET[$this->_GET_start_var]:0)/$per + 5)) {
			$v = $num_pages;
		} else {
			$v = (!empty($_GET[$this->_GET_start_var])?$_GET[$this->_GET_start_var]:0)/$per + 5;
		}
		return $v;
	}
	function get_GET_cur_min() {
		$per = $this->get_per();
		if(empty($_GET[$this->_GET_start_var]) || ($_GET[$this->_GET_start_var]/$per) <= 5) {
			$v = 1;
		} elseif($_GET[$this->_GET_start_var] > 5) {
			$v = $_GET[$this->_GET_start_var]/$per - 5;
		}
		return $v;
	}
	function get_per() {
		return (!empty($_GET[$this->_GET_per_var]) ? $_GET[$this->_GET_per_var] : $this->_per_var);
	}

}
?>