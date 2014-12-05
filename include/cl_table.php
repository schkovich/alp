<?php
class formatting_table {
	var $_table_open = false;
	var $_row_open   = false;
	var $_cell_open  = false;
	
	function start_table()
    { 
		?><table cellpadding="0" cellspacing="0" style="border: 0px;"><?php
		$this->_table_open = true;
	}
	function end_table()
    { 
		?></table><?php 
		$this->_table_open = false;
	}
	function start_row()
    { 
		?><tr><?php 
		$this->_row_open = true;
	}
	function end_row()
    { 
		?></tr><?php 
		$this->_row_open = false;
	}
	function start_cell()
    { 
		?><td><?php 
		$this->_cell_open = true;
	}
	function end_cell()
    { 
		?></td><?php 
		$this->_cell_open = false;
	}
	
	function get_table_open()
    {
		if ($this->_table_open) {
            return true;
        }
		return false;
	}
	function get_row_open()
    {
		if ($this->_row_open) {
            return true;
        }
		return false;
	}
	function get_cell_open()
    {
		if ($this->_cell_open) {
            return true;
		}
        return false;
	}
	function get_type()
    {
		return 'format';
	}
}
?>