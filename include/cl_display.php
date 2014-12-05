<?php
require_once 'include/cl_field.php';
class display {
	var $_name, $_singular, $_security, $_count_bool, $_exclude, $_crutch, $_table, $_id, $_order, $_default;
	var $_tables = array();
	var $_groups = array();
	var $_grouplist = array();

	function display($name, $singular, $security, $count_bool, $table, $id, $order, $exclude='', $crutch='')
    {
		$this->_name = $name;
		$this->_singular = $singular;
		$this->_security = $security;
		$this->_count_bool = $count_bool;
		$this->_table = $table;
		$this->_id = $id;
		$this->_order = $order;
		$this->_exclude = $exclude;
		$this->_crutch = $crutch;
	}

	function is_secure()
    {
		if (current_security_level()>=$this->_security) {
			return 1;
		} else {
			return 0;
		}
	}

	function add_default($name,$desc,$link=array(),$interp=array())
    {
		$this->_default[$name] = new field($name,$desc,$this->_security,-1,array(),$link,array(),'',$interp);
	}

	function add_field($name,$desc,$security,$group=0,$crutch=array(),$link=array(),$list=array(),$date='',$interp=array())
    {
		$this->_tables[$group][$name] = new field($name,$desc,$security,$group,$crutch,$link,$list,$date,$interp);
	}

	function groups($groups)
    {
		$this->_grouplist = $groups;
	}

	function display_pagingfields(& $pager, & $URL_handler, $music = 0) {
		$this->display_fields($pager, $URL_handler, 1, $music);
	}

	function display_fields(& $pager, & $URL_handler, $paging = 0, $music = 0)
    {
		global $lang, $master, $toggle, $lan, $colors, $images, $start, $end, $container, $modules, $dims;
		if($this->_security==3) { 
			$title = 'super administrator: '.$this->_name;
		} elseif($this->_security==2) { 
			$title = 'administrator: '.$this->_name;
		} else { $title = $this->_name; }
		include 'include/_top.php';
		start_module(); 
		if (current_security_level()>=$this->_security) { 
			if ($this->_security==3) { ?>
				<strong>super administrator</strong>: <?php echo $this->_name; ?><br />
				<?php
			} elseif ($this->_security==2) { ?>
				<strong>administrator</strong>: <?php echo $this->_name; ?><br />
				<?php
			} else { ?>
				<strong><?php echo $this->_name; ?></strong>:<br />
				<br />
				<?php
			}
			if (empty($_POST)) { 
				if(!$paging) include 'include/_display_nopost.php';
				else $this->display_table($pager, $URL_handler);
			} else {
			}
		} else {
			echo 'you are not authorized to view this page.<br /><br />';
		}
		end_module();
		if(!$music) {
			include 'include/_bot.php';
		} else {
		}
	}

	function display_solo()
    {
		global $colors;
		include 'include/_display_nopost.php';
	}

	function display_table(& $pager, & $URL_handler) {
		global $dbc, $colors;
		
		$name = '';
		foreach($this->_default AS $key => $val) {
			$default_name = $key;
			break;
		}
	
		$title_data = $this->get_title_table_data($URL_handler);
		$table_data = $this->get_table_data($pager, $default_name);

		$this->display_selector($URL_handler);
		
		$number_of_records = $dbc->database_num_rows($dbc->database_query($this->get_sql_query($pager,$default_name,0)));
		?>
        <table border="0" width="100%" cellpadding="3" cellspacing="0">
		<tr><td colspan="<?php echo sizeof($title_data); ?>">
		<?php echo $pager->display_links($URL_handler, $default_name); ?><br />
		<?php echo $pager->display_numeric_links($URL_handler, $number_of_records); ?><br />
		<br /></td></tr>
		<tr bgcolor="<?php echo $colors['cell_title']; ?>"><?php
			foreach($title_data as $val) {
				echo '<td class="'.$val[0].'">'.$val[1].'</td>';
			} ?>
		</tr>
		<?php
        $i = 1;
		foreach($table_data AS $val) {
            $bgc = ($i%2 == 1)?$colors['cell_background']:$colors['cell_alternate'];
			echo '<tr bgcolor="'.$bgc.'">';
			foreach($val AS $d_val) {
				echo '<td class="'.$d_val[0].'">'.$d_val[1].'</td>';
			}
			echo '</tr>';
            $i++;
		} ?>
		</table>
		<?php
	}

	function display_selector(& $URL_handler, $toggle_show = 1, $toggle_per = 1) { ?>
		<form action="<?php echo get_script_name(); ?>" method="GET">
		<?php
		$excluding = array('show','per');
		echo $URL_handler->get_hidden_form_elements($excluding);
		?>
		<font class="sm">
		<?php
		if($toggle_show) { 
			?>show: <select name="show" style="width: 200px; font: 10px Verdana;"><?php
			foreach($this->_grouplist as $key => $val) { 
					?><option value="<?php echo $key; ?>"<?php 
					echo ((empty($_GET['show']) && $key==0) || (!empty($_GET['show']) && $_GET['show']==$key)?" selected":"");
					?>><?php echo $val; ?></option><?php
			} 
			?></select><?php
		}
		if($toggle_per) {
			?>per page: <select name="per" style="font: 10px Verdana;"><?php
			if(empty($_GET['per'])) $_GET['per'] = 50;
			$per_page = array(	10 	=> 10,
								25 	=> 25,
								50 	=> 50,
								100 => 100);
			foreach($per_page as $key => $val) { 
					?><option value="<?php echo $key; ?>"<?php 
					echo (!empty($_GET['per'])&&$_GET['per']==$key?" selected":"");
					?>><?php echo $val; ?></option><?php
			} ?>
			</select><?php
		} 
		?></font>
		<input type="submit" value="go" style="font: 10px Verdana;" class="formcolors" />
		</form>
		<br />
		<?php
	}

	function get_title_table_data(& $URL_handler) {
		global $dbc, $colors;
		$array = array();
		$c_counter = 0;
		if(!empty($this->_count_bool)) {
			$array[$c_counter] = array('celltitle','#');
		}
		if(!empty($this->_default)) {
			foreach($this->_default as $key => $val) { 
				$bt  = '<a href="'.get_script_name();
				$bt .= $URL_handler->add_GET_to_str('sort',$key);
				$bt .= '" style="color: '.$colors["blended_text"].'"><b><u>';
				$bt .= $val->get_description();
				$bt .= '</u></b></a>';
				$array[$c_counter] = array('celltitle',$bt);
				$c_counter++;
			}
		}
		if(empty($_GET)||empty($_GET["show"])) {
			$temp = 0;
		} else {
			$temp = (int)$_GET["show"];
		}

		if (IsSet($this->_tables[$temp]))
		{
			foreach($this->_tables[$temp] as $key => $val) { 
				if(current_security_level()>=$val->get_security()) {
					$sort_boolean = $dbc->database_num_rows($dbc->database_query("SELECT * FROM ".$this->_table." ORDER BY ".$key));
					if(sizeof($val->get_interp())==0 && $sort_boolean) { // sort, $key
						$bt = '<a href="'.get_script_name().$URL_handler->add_GET_to_str('sort',$key).'" style="color: '.$colors["blended_text"].'"><u>';
					} else { 
						$bt = '<font style="color: '.$colors["blended_text"].'">';
					}
					$bt .= '<b>'.$val->get_description().'</b>';
					$bt .= ( sizeof( $val->get_interp() ) == 0 && $sort_boolean ?'</u></a>':'</font>');
					$array[$c_counter] = array('celltitle',$bt);
					$c_counter++;
				}
			}
		}

		return $array;
	}

	function get_table_data(& $pager, $default_name) {
		global $colors;
		$query = $this->get_sql_query($pager,$default_name);
		include 'include/_display_get_table.php';
		return $array;
	}
	
	function get_sql_query(& $pager, $default_name, $limited = 1) {
		$query = 'SELECT * FROM '.$this->_table;
		if(!empty($_GET[$pager->get_GET_var()])) {
			if($_GET[$pager->get_GET_var()]=='Other') {
				$query .= ' WHERE LEFT('.$default_name.',1) NOT BETWEEN \'A\' AND \'Z\'';
			} else {
				$query .= ' WHERE LEFT('.$default_name.',1)=\''.preg_replace('/[^a-zA-Z]/','',utf8_decode($_GET[$pager->get_GET_var()])).'\'';
			}
		}
		if(!empty($this->_order)) {
		    if(empty($_GET)||empty($_GET["sort"])) {
		        $query .= " ORDER BY ".$this->_order;
		    } else {
		        $query .= " ORDER BY ".($this->can_sort_by_this_field($this->_table, $_GET["sort"]) ? $_GET["sort"] : $this->_order);
		    }
		}
		if($limited) {
			// pager limits
			$query .= ' LIMIT ';
			$query .= (!empty($_GET[$pager->get_GET_start_var()]) ? (int)$_GET[$pager->get_GET_start_var()] : 0);
			$query .= ',';
			$query .= (!empty($_GET[$pager->get_GET_per_var()]) && $_GET[$pager->get_GET_per_var()] <= 100 ? (int)$_GET[$pager->get_GET_per_var()] : 50);
		}
		return $query;
	}

	function can_sort_by_this_field($table_name, $field_name)
	{
		$valid_fields['music'] = array_flip(array('artist','title','genre','plays'));
		$valid_fields['users'] = array('username','first_name','last_name','gaming_group','email','gender','priv_level','sharename','recent_ip','ftp_server','comp_proc','comp_proc_type','comp_proc_spd','comp_mem','comp_mem_type','comp_hdstorage','comp_gfx_gpu','date_of_arrival','date_of_departure');

		if (current_security_level() > 1)
			$valid_fields['users'][] = 'paid';

		$valid_fields['users'] = array_flip($valid_fields['users']);

		if (IsSet($valid_fields[$table_name]) && IsSet($valid_fields[$table_name][$field_name]))
			return TRUE;

		return FALSE;
	}
}

?>