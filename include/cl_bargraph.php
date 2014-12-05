<?php
class bargraph {
	var $_percent;						// percent graph is filled.
	var $_width;						// total width of the graph (in pixels, unless widthpercent is 1)
	var $_widthpercent;					// boolean to indicate if the $_width variable is a percent
	var $_border = 1;					// border size (in pixels)
	var $_bordercolor;
	var $_filledcolor;
	var $_emptycolor;
	var $_labelfilledcolor;
	var $_labelemptycolor;
	var $_toppadding = 6;
	var $_bottompadding = 4;
	var $_alignment = 'left';			// left or right - controls which side the filled comes from.
	var $_labels = 1;					// boolean to indicate if display % label on the graph
	var $_height = 12;
	var $_background = true;			// whether to use the 3d background for the empty section
	
	function bargraph($percent,$width,$widthpercent)
    {
		global $colors;
		$this->_percent = $percent;
		$this->_width = $width;
		$this->_widthpercent = $widthpercent;
		$this->_bordercolor = $colors['border_alternate'];
		$this->_filledcolor = $colors['graphs'];
		$this->_emptycolor = $colors['cell_title'];
		$this->_labelfilledcolor = $colors['cell_background'];
		$this->_labelemptycolor = $colors['text'];
	}
	
	function set_height($height)
    {
		$this->_height = $height;
	}
    
	function set_border($bordersize)
    {
		$this->_border = $bordersize;
	}
    
	function set_bordercolor($bordercolor)
    {
		$this->_bordercolor = $bordercolor;
	}
    
	function set_filledcolor($filledcolor)
    {
		$this->_filledcolor = $filledcolor;
	}
    
	function set_emptycolor($emptycolor)
    {
		$this->_emptycolor = $emptycolor;
	}
    
	function set_labelfilledcolor($labelcolor)
    {
		$this->_labelfilledcolor = $labelcolor;
	}
    
	function set_labelemptycolor($labelcolor)
    {
		$this->_labelemptycolor = $labelcolor;
	}
	function set_padding($toppadding,$bottompadding) {
		$this->_toppadding = $toppadding;
		$this->_bottompadding = $bottompadding;
	}
    
	function set_alignment($alignment)
    {
		$this->_alignment = $alignment;
	}
    
	function set_labels($labels)
    {
		$this->_labels = $labels;
	}
    
	function set_bool_background($bool)
    {
		$this->_background = $bool;
	}
    
	function display()
    {
		global $master, $images, $colors;
		if($this->_toppadding>0) spacer(1,$this->_toppadding,1);
		$emptybg = $master['currentskin'].$images['empty_bargraph_background'];
		?>
		<table border="0" cellpadding="0" cellspacing="<?php echo $this->_border; ?>"<?php if($this->_border>0) { ?> bgcolor="<?php echo $this->_bordercolor; ?>"<?php } ?> width="<?php echo $this->_width.($this->_widthpercent?"%":""); ?>"><tr>
			<?php 
			if ($this->_alignment == 'left') {
				if ($this->_percent!=0) { ?>
					<td style="width: <?php echo round($this->_percent*$this->_width).($this->_widthpercent?'%':''); ?>; background-color: <?php echo $this->_filledcolor; ?>; text-align: right;"><?php 
						if ($this->_percent>=.85) { ?><img src="img/pxt.gif" width="1" height="<?php echo $this->_height; ?>" border="0" align="absmiddle" alt="" /><?php
							if ($this->_labels) { ?><font class="sm" style="font-weight: bold" color="<?php echo $this->_labelfilledcolor; ?>"><?php echo round($this->_percent*100,1); ?>%&nbsp;</font><?php }
						} else {
							spacer(1,$this->_height);
						} ?>
					</td><?php 
				}
			}
			if ($this->_percent!=1) { ?>
				<td style="width: <?php echo round($this->_width-$this->_percent*$this->_width).($this->_widthpercent?'%':''); ?>; background-color: <?php echo $this->_emptycolor; ?>; text-align: <?php echo $this->_alignment; ?>; "<?php if($this->_background&&file_exists($emptybg)) { ?> background="<?php echo $emptybg; ?>"<?php } ?>><?php 
					if ($this->_percent<.85) { ?><img src="img/pxt.gif" width="1" height="<?php echo $this->_height; ?>" border="0" align="absmiddle" alt="" /><?php
						if ($this->_labels) { ?><font class="sm" style="font-weight: bold" color="<?php echo $this->_labelemptycolor; ?>"><?php echo ($this->_alignment == 'left'?'&nbsp;':''); ?><?php echo round($this->_percent*100,1); ?>%<?php echo ($this->_alignment == 'right'?'&nbsp;':''); ?></font><?php } 
					} else {
						spacer(1,$this->_height);
					} ?>
				</td><?php 
			} 
			if ($this->_alignment == 'right') {
				if ($this->_percent!=0) { ?>
					<td style="width: <?php echo round($this->_percent*$this->_width).($this->_widthpercent?'%':''); ?>; background-color: <?php echo $this->_filledcolor; ?>; text-align: left;"><?php 
						if ($this->_percent>=.85) { ?><img src="img/pxt.gif" width="1" height="<?php echo $this->_height; ?>" border="0" align="absmiddle" alt="" /><?php
							if($this->_labels) { ?><font class="sm" style="font-weight: bold" color="<?php echo $this->_labelfilledcolor; ?>">&nbsp;<?php echo round($this->_percent*100,1); ?>%</font><?php }
						} else {
							spacer(1,$this->_height);
						} ?>
					</td><?php 
				}
			}
			?>
		</tr></table>
		<?php
		if ($this->_bottompadding>0) spacer(1,$this->_bottompadding,1);
	}
}
?>