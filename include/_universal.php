<?php
@error_reporting(E_ALL ^ E_NOTICE); // This will NOT report uninitialized variables

list($usec, $sec) = explode(" ", microtime());  //Code for process time clock
$start_time = ((float)$usec + (float)$sec);		//Code for process time clock

require_once '_config.php';
require_once 'include/_functions.php';
require_once 'include/_includes.php';

$URL_handler = new URL_handler();

class universal extends form {
	var $_name, $_singular;
	var $_table_name, $_id, $_order;
	var $_permissions = array();		// which options to display to user.
	var $_notes = array();				// notes to display to user
	var $_extra = array();				// extra sql statements to run
	var $_security; 					// level 3: super admin only, level 2: admins+, level 1: users+, level 0: guests+
	var $_crutch;						// if the crutch is true, some elements become unmodifiable. (must be the name of a column in the table)
	//var $_print;
	var $_related_links;
	var $_delmod_query;
	
	function universal($name, $singular, $security)
    {
		$this->_name     = $name;
		$this->_singular = $singular;
		$this->_security = $security;

		$this->_table_name = '';
		$this->_id         = '';
		$this->_order      = '';

		$this->_permissions['add']    = 0;
		$this->_permissions['del']    = 0;
		$this->_permissions['mod']    = 0;
		$this->_permissions['update'] = 0;
		
		// initialize optional variables.
		$this->_notes['add']    = '';
		$this->_notes['del']    = '';
		$this->_notes['mod']    = '';
		$this->_notes['update'] = '';
		$this->_print           = array();
		$this->_related_links   = array();
		$this->_crutch          = 0;
		$this->_delmod_query    = '';
	}
    
	function database($table,$id,$order)
    {
		$this->_table_name = $table;
		$this->_id = $id;
		$this->_order = $order;
	}
    
	function permissions($type,$value)
    {
		$this->_permissions[$type] = $value;
	}
    
	function add_extra($type,$array)
    {
		$this->_extra[$type] = $array;
	}
    
	function add_notes($type,$note) 
    {
		$this->_notes[$type] = $note;
	}
    
	function add_crutch($crutch)
    {
		$this->_crutch = $crutch;
	}
    
	/*function add_print($print)
         {
		$this->_print = $print;
	}*/
    
	function add_delmod_query($query)
    {
		$this->_delmod_query = $query;
	}

	function start_elements()
    {
		// call after adding crutch and before adding elements.
		$this->form($this->_crutch);
	}

	function get_name()
    {
		return $this->_name;
	}
    
	function get_singular()
    {
		return $this->_singular;
	}

	function is_secure()
    {
		if(current_security_level()>=$this->_security) return true;
		else return false;
	}
    
	// display functions
	function display()
    {
		global $lang, $master, $toggle, $lan, $colors, $images, $start, $end, $container, $modules, $dims;
		if(empty($_POST)&&$this->is_secure()) {
			$this->display_top();
			$this->display_form();
			$this->display_bottom();
		} elseif(!empty($_POST)&&$this->is_secure()) {
			$this->display_results();
		} else {
			$this->display_slim('you are not authorized to view this page.');
		}
	}

	
	function add_related_link($name, $url, $security)
    {
		$this->_related_links[] = array($name,$url,$security);
	}
    
	function display_related_links()
    {
		global $lang, $images;
		if(sizeof($this->_related_links)>0) {
			$counter = 0;
			foreach($this->_related_links as $val) {
				if(current_security_level()>=$val[2]) $counter++;
			}
			if($counter>0) { ?>
				<font class="sm"><strong>related links</strong> //<br /><?php
				foreach($this->_related_links as $val) { 
					if(current_security_level()>=$val[2]) { ?>
						&nbsp;<?php get_arrow(); ?>&nbsp;<a href="<?php echo $val[1]; ?>"><?php echo ($val[2]>2?'<strong>super </strong>':''); ?><?php echo ($val[2]>1?'<strong>administrator</strong>: ':''); ?><?php echo $val[0]; ?></a><br /><?php
					}
				} ?>
				</font><br /><?php
			}
		}
	}
    
	function display_top($modjool=1,$side=1)
    {
		global $lang, $master, $toggle, $lan, $colors, $images, $start, $end, $container, $modules, $dims;
		if($this->_security==3) { 
			$title = get_lang("sadministrator").': '.$this->_name;
		} elseif($this->_security==2) { 
			$title = get_lang("administrator").': '.$this->_name;
		} else { $title = $this->_name; }
		if($side) {
			include 'include/_top.php';
		} else {
			include 'include/_top_noside.php';
		}
		if($modjool) start_module();
	}
    
	function display_form()
    {
		global $lang, $master, $toggle, $lan, $colors, $images, $start, $end, $container, $modules, $dims, $URL_handler;
		// if post is empty, this is displayed.
		if($this->_security==3) {
			echo "<strong>".get_lang("sadministrator")."</strong>:  ".$this->_name;
		} elseif($this->_security==2) {
			echo "<strong>".get_lang("administrator")."</strong>:  ".$this->_name;
		} else { ?>
			<strong><?php echo $this->_name; ?></strong>:<?php
		} 
		if(!empty($_GET['mod'])&&!empty($_GET['q'])) { ?>
			<span class="sm" style="color: <?php echo $colors['blended_text']; ?>;">[<a href="<?php echo get_script_name(); ?>">back to all <?php echo $this->_name; ?></a>]</span>
			<?php
		} ?><br /><br /><?php
		$this->display_related_links();
		if(!empty($this->_permissions['add'])||!empty($this->_permissions['del'])||!empty($this->_permissions['mod'])||!empty($this->_permissions['update'])) {
			if(!empty($this->_table_name)||!empty($this->_id)||!empty($this->_order)) include 'include/_universal_nopost.php';
			else echo "the script has no database table information to work with.  don't forget to call the database function from your script.<br /><br />";
		} else {
			echo "the script has no permissions available.  don't forget to call the permissions function from your script.<br /><br />";
		}
	}
    
	function display_bottom($modjool=1,$side=1)
    {
		global $lang, $master, $toggle, $lan, $colors, $images, $start, $end, $container, $modules, $dims;
		if($modjool) end_module();
		if($side) {
			include 'include/_bot.php';
		} else {
			include 'include/_bot_noside.php';
		}
	}
    
	function display_results($redirect='')
    {
		global $lang, $master, $toggle, $lan, $colors, $images, $start, $end, $container, $modules, $dims, $URL_handler;
		// if post is not empty, this is displayed.  WARNING: does not use display_top and display_bottom.
		if(!empty($this->_table_name)||!empty($this->_id)||!empty($this->_order)) {
			include 'include/_universal_post.php';
		} else {
			$this->display_top();
			echo "the script has no database table information to work with.  don't forget to call the database function.<br /><br />";
			$this->display_bottom();
		}
	}

	function display_slim($string, $redirect='index.php',$redirect_time=2)
    {
		global $lang, $master, $toggle, $lan, $colors, $images, $start, $end;
		if(stristr($string,'success')) {
			header('Location: '.$redirect);
		} elseif(stristr($string,'you are not authorized to view this page.')&&current_security_level()==0) {
			$title = $this->_name;
			$redirect='login.php?ref='.urlencode(basename(get_script_name()));
			include 'include/_top_slim.php'; ?>
			<br /><a href="<?php echo $redirect; ?>" class="radio"><strong><?php echo $string; ?> &nbsp;redirect in <span id="pendule"></span>&nbsp;seconds.</strong></a><br /><br />
			<?php
			include 'include/_bot_slim.php';
		} else {
			$title = $this->_name;
			include 'include/_top_slim.php'; ?>
			<br /><a href="<?php echo $redirect; ?>" class="radio"><strong><?php echo $string; ?> &nbsp;redirect in <span id="pendule"></span>&nbsp;seconds.</strong></a><br /><br />
			<?php
			include 'include/_bot_slim.php';
		}
	}
	
	function display_smallwindow_top($bg_override='',$nomargin=0)
    {
		global $lang, $master, $toggle, $lan, $colors, $images, $start, $end, $container, $modules, $dims;
		include 'include/_top_smallwindow.php';
	}
    
	function display_smallwindow_bottom()
    {
		global $lang, $master, $toggle, $lan, $colors, $images, $start, $end, $container, $modules, $dims;
		include 'include/_bot_smallwindow.php';
	}
}
?>