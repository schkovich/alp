<?php
global $colors, $dbc;
include_once 'include/cl_bargraph.php';
include_once 'include/_gaming_rig_db.php';

function disp_gamingrig($name, $where, $whereNot, $logo, $websiteurl, $textcolor, $width) {
	global $dbc, $colors;
	spacer(1,4,1); ?>
	<table border="0" cellpadding="0" cellspacing="1" width="100%" bgcolor="<?php echo $colors['border_alternate']; ?>">
	<tr><td width="56" bgcolor="<?php echo (file_exists($master['currentskin'].$logo)?$colors['text']:$textcolor); ?>"><div align="center"><a href="<?php echo $websiteurl; ?>" target="TOP"><img src="<?php echo (file_exists($master['currentskin'].$logo)?$master['currentskin'].$logo:'img/rigs/'.$logo); ?>" width="<?php echo $width; ?>" height="16" border="0" alt="<?php echo $name; ?>"></a></div><?php spacer(56); ?></td><td width="100%"><?php
		if($num_others = $dbc->queryOne("SELECT count(userid) FROM users WHERE " . $whereNot)) {
			$percent = $dbc->queryOne("SELECT count(userid) FROM users WHERE " . $where) / $num_others;
		} else {
			$percent = 0;
		}
		$b = new bargraph($percent,100,1);
		$b->set_border(0);
		$b->set_height(17);
		$b->set_padding(0,0);
		$b->set_filledcolor($colors['border_alternate']);
		$b->display();
		?></td></tr></table>
<?php	
}
?>
<a href="chng_userinfo.php"><strong>gaming rigs</strong></a><?php get_go('chng_userinfo.php'); ?><br />

<?php
disp_gamingrig('amd', "comp_proc='AMD'", "comp_proc!=''", 'amd.gif', 'http://www.amd.com', '#319C63', 56);
disp_gamingrig('intel', "comp_proc='INTEL'", "comp_proc!=''", 'intel.gif', 'http://www.intel.com', '#0033FF', 56);
disp_gamingrig('ati', "comp_gfx_gpu='ATI'", "comp_gfx_gpu!=''", 'ati.gif', 'http://www.ati.com', '#ff0000', 23);
disp_gamingrig('nvidia', "comp_gfx_gpu='Nvidia'", "comp_gfx_gpu!=''", 'nvidia.gif', 'http://www.nvidia.com', '#ffffff', 28);
spacer(1,4,1); ?>

<div align="right"><font class="smm" color="<?php echo $colors['blended_text']; ?>">all logos are &copy; their respective owners.</font></div>