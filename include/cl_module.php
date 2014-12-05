<?php
class Module {
	// stores data for a single module
	var $_name, $_loc, $_str, $_link, $_sec, $_isSlim, $_isOpen, $_type;
	
	function Module($n = '', $l = 'main', $sec = 1, $st = '', $sl = 0, $link = '', $type='')
    {
		// constructor
		$this->_name = $n;
		$this->_loc = $l;
		$this->_str = $st;
		$this->_link = $link;
		$this->_sec = $sec;
		$this->_isSlim = $sl;
		$this->_isOpen = 1;
		$this->_type = $type;
	}
	
	function display_module($overwrite = '')
    {
		global $container;
		$var = (boolean) (current_security_level()>=$this->get_security());
		if($var===true) {
			if(!empty($overwrite)) {
				$loc = $overwrite;
			} else {
				if($this->get_type()!="") {
					$loc = $this->get_type();
				} else {
					$loc = $this->_loc;
				}
			} ?>
			<tr>
			<?php
			if ($this->get_type()!='left' && $this->get_loc()!='main') { ?>
				<td width="<?php echo $container['horizontalpadding']; ?>"><?php spacer($container['horizontalpadding']); ?></td>
				<?php
			} ?>
			<td width="100%"<?php if($this->get_type()!='main') { echo " colspan=\"2\""; } ?>>
			<?php
			start_module($loc, $this->_str, "", $this->_link);
			require_once 'include/modules/mod_' . $this->_name . '.php';
			end_module($loc); ?>
			</td>
			<?php
			if($this->get_type()!='right' && $this->get_loc()!='main') { ?>
				<td width="<?php echo $container['horizontalpadding']; ?>"><?php spacer($container['horizontalpadding']); ?></td>
				<?php
			} ?>
			</tr>
			<?php
		}
	}
	
	function get_loc() {
		// returns string
		return $this->_loc;
	}
	
	function get_type() {
		// returns string
		return $this->_type;
	}
	function get_inner_width() {
		global $master, $container;
		if($this->get_loc()!='main') {
			$sides = array('tl','tr','bl','br');
			// the dims array in this case only stores widths
			for($i=0;$i<sizeof($sides);$i++) {
				$key = 'mod'.$sides[$i];
				if(file_exists($master['currentskin'] . 'mod'.$sides[$i].'.gif')) {
					$temp = getimagesize($master['currentskin'] . $key . '.gif');
					$dims[$key] = $temp[0];
				} else {
					$dims[$key] = $container['border_width'];
				}
			}
			$extra = array();
			if($dims['modtl']>$dims['modbl'] || $dims['modtl']>$dims['modbl']) {
				$extra[0] = max($dims['modtl'],$dims['modbl']);
			} else {
				$extra[0] = $dims['modtl'];
			}
			if($dims['modtr']>$dims['modbr'] || $dims['modtr']>$dims['modbr']) {
				$extra[1] = max($dims['modtr'],$dims['modbr']);
			} else {
				$extra[1] = $dims['modtr'];
			}
			return ($container[$this->get_loc().'module']-2*$container['horizontalmodulepadding']-$extra[0]-$extra[1]-10).'px';
		} else {
			return '96%';
		}
	}
    
	function get_str()
    {
		// returns string
		return $this->_str;
	}

	function is_slim()
    {
		// returns boolean
		return $this->_isSlim;
	}
	
	function get_textPadding()
    {
		// returns int
		return $this->_textPadding;
	}
	
	function is_open()
    {
		// returns boolean
		return $this->_isOpen;
	}

	function get_security()
    {
		// returns int
		return $this->_sec;
	}
    
	function is_module_secure()
    {
		return (current_security_level()>=$this->get_security());
	}
}

class ModuleManager {
	// manages a collection of modules
	var $_numLeft, $_numRight, $_numMain;
	var $_leftModules, $_rightModules, $_mainModules;
	
	function ModuleManager()
    {
		// constructor
		$this->_numLeft = 0;
		$this->_numRight = 0;
		$this->_numMain = 0;
		$this->_leftModules = array();
		$this->_rightModules = array();
		$this->_mainModules = array();
	}
	
	function add_module($n = '', $l = '', $sec = 0, $st = '', $sl = 0, $link='', $type='')
    {
		// adds a module to the manager
		$mod = new Module($n, $l, $sec, $st, $sl, $link, $type);
		if ($l == 'left') {
			$this->_numLeft++;
			$this->_leftModules[] = $mod;
		}
		if ($l == 'right') {
			$this->_numRight++;
			$this->_rightModules[] = $mod;
		}
		if ($l == 'main') {
			$this->_numMain++;
			$this->_mainModules[] = $mod;
		}
	}
	
	function display_all_modules($side)
    {
		global $container;
		// displays all the modules for a side. 
		if ($side=='right') {
			$num_modules = $this->get_numRight();
			$get_modules = $this->get_rightModules();
			$width_modules = $container['rightmodule']; 
		} elseif($side=='left') {
			$num_modules = $this->get_numLeft();
			$get_modules = $this->get_leftModules();
			$width_modules = $container['leftmodule']; 
		} elseif($side=='main') {
			$num_modules = $this->get_numMain();
			$get_modules = $this->get_mainModules();
		}
		if ($num_modules > 0) { 
			$types = array(
				'left'  => 0,
				'right' => 0,
				'main'  => 0);
			foreach ($get_modules as $mod) {
				$types[$mod->get_type()]++;
			} 
			?><table border="0" cellpadding="0" cellspacing="0" width="100%"><?php
			foreach ($get_modules as $mod) {
				$mod->display_module();
			}
			if ($side!='main') { ?>
				<tr>
					<td><?php spacer($container['horizontalpadding']); ?></td>
					<td width="<?php echo $width_modules; ?>"><?php spacer($width_modules); ?></td>
					<td><?php spacer($container['horizontalpadding']); ?></td>
				</tr><?php
			}
			?></table><?php
		} elseif ($side!='main') {
			spacer($container['horizontalpadding']);
		}
	}
	
	function get_Width($side)
    {
		global $container;
		if($side=='right') {
			if ($this->get_numRight()>0) {
				echo ($container['rightmodule']+2*$container['horizontalpadding']);
			} else {
				echo $container['horizontalpadding'];
			}
		} elseif ($side=='left') {
			if ($this->get_numLeft()>0) {
				echo ($container['leftmodule']+2*$container['horizontalpadding']);
			} else {
				echo $container['horizontalpadding'];
			}
		}
	}
	
	function get_numLeft()
    {
		// returns int
		return $this->_numLeft;
	}
	
	function get_numRight()
    {
		// returns int
		return $this->_numRight;
	}
	
	function get_numMain()
    {
		// returns int
		return $this->_numMain;
	}
	
	function get_leftModules()
    {
		// returns array of Module objects
		return $this->_leftModules;
	}
	
	function get_rightModules()
    {
		// returns array of Module objects
		return $this->_rightModules;
	}
	
	function get_mainModules()
    {
		// returns array of Module objects
		return $this->_mainModules;
	}
}
?>
