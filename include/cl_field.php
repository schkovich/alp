<?php
class field {
	var $_name, $_description, $_security, $_group ,$_date,$_interp;
	var $_crutch = array();
	var $_link = array();
	var $_list = array();

	function field($name,$desc,$security,$group,$crutch=array(),$link=array(),$list=array(),$date="",$interp=array())
    {
		$this->_name = $name;
		$this->_description = $desc;
		$this->_security = $security;
		$this->_group = $group;
		$this->_crutch = $crutch;
		$this->_link = $link;
		$this->_list = $list;
		$this->_date = $date;
		$this->_interp = $interp;
	}
    
	function get_name()
    {
		return $this->_name;
	}
    
	function get_description()
    {
		return $this->_description;
	}
    
	function get_security()
    {
		return $this->_security;
	}
    
	function get_group()
    {
		return $this->_group;
	}
    
	function get_crutch()
    {
		return $this->_crutch;
	}
    
	function get_link()
    {
		return $this->_link;
	}
    
	function get_list()
    {
		return $this->_list;
	}
    
	function get_date()
    {
		return $this->_date;
	}
    
	function get_interp()
    {
		return $this->_interp;
	}
}
?>