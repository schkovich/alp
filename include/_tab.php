<?php

function start_module($type = 'main', $str = '', $bgcolor = '', $link='', $width='100%', $align='')
{
	global $lan, $colors, $master, $container;

	$sides = array('tl','tr','bl','br','t','b','lside','rside');
	for ($i = 0; $i < sizeof($sides); $i++) {
		$imgs['mod'.$sides[$i]] = file_exists($master['currentskin'] . 'mod'.$sides[$i].'.gif');
	}
	foreach ($imgs AS $key => $value) {
		if ($value) {
			$dims[$key] = getimagesize($master['currentskin'] . $key . '.gif');
		} else {
			$dims[$key] = array($container['border_width'],$container['border_height']);
		}
	}
	if(!empty($bgcolor)) { 
		$current_color = $bgcolor;
	} else {
		$current_color = $colors['cell_background'];
	}
	if (!empty($str)) {
		$rowspan = 5;
	} else {
		$rowspan = 3;
	}
	?><table border="0" cellpadding="0" cellspacing="0" width="<?php echo $width; ?>"<?php if(!empty($align)) { echo " align=\"".$align."\""; } ?>>
	<tr>
	<?php
	if ($container['border_height'] > 0) { 
        if ($container['border_width'] > 0) { 
            if ($type == 'right' || $type == 'main') { 
?>
                                <td bgcolor="<?php echo ($imgs['modtl']?$colors['cell_background']:$colors['border']); ?>" width="<?php echo $dims['modtl'][0]; ?>" height="<?php echo $dims["modtl"][1]; ?>"><img src="<?php echo ($imgs["modtl"] ? $master["currentskin"] . "modtl.gif" : "img/pxt.gif"); ?>" width="<?php echo $dims["modtl"][0]; ?>" height="<?php echo $dims["modtl"][1]; ?>" border="0" alt="" /></td><?php } ?><?php } ?>
		            <td bgcolor="<?php echo ($imgs['modt']?$colors["cell_background"]:$colors['border']); ?>" colspan="3" width="100%" height="<?php echo $dims["modt"][1]; ?>"<?php echo ($imgs["modt"] ? " background=\"".$master["currentskin"] . "modt.gif\"" : ''); ?>><img src="img/pxt.gif" width="100%" height="<?php echo $dims["modt"][1]; ?>" border="0" alt="" /></td>
<?php 
    if ($container['border_width'] > 0) {
        if($type == 'left' || $type == 'main') { 
?>
                        <td bgcolor="<?php echo ($imgs["modtr"]?$colors["cell_background"]:$colors["border"]); ?>" width="<?php echo $dims["modtr"][0]; ?>" height="<?php echo $dims["modtr"][1]; ?>"><img src="<?php echo ($imgs["modtr"] ? $master["currentskin"] . "modtr.gif" : "img/pxt.gif"); ?>" width="<?php echo $dims["modtr"][0]; ?>" height="<?php echo $dims["modtr"][1]; ?>" border="0" alt="" /></td><?php } ?><?php } ?>
		<?php
	} ?>
	</tr>
	<tr>
<?php 
    if($container["border_width"] > 0) {
        if($type == "right" || $type == "main") { 
?>
                        <td bgcolor="<?php echo ($imgs["modlside"]?$colors["cell_background"]:$colors["border"]); ?>" rowspan="<?php echo $rowspan; ?>" width="<?php echo $dims["modlside"][0]; ?>" height="1"<?php echo ($imgs["modlside"] ? " background=\"".$master["currentskin"] . "modlside.gif\"" : ""); ?>><img src="img/pxt.gif" width="<?php echo $dims["modlside"][0]; ?>" height="1" border="0" alt="" /></td><?php } ?><?php } ?>
		<td colspan="3" width="1" height="<?php echo $container["verticalmodulepadding"]; ?>" bgcolor="<?php echo $current_color; ?>"><img src="img/pxt.gif" width="1" height="<?php echo $container["verticalmodulepadding"]; ?>" border="0" alt=""></td>
<?php 
    if($container["border_width"] > 0) {
    if($type == "left" || $type == "main") { 
?> 
            <td bgcolor="<?php echo ($imgs["modrside"]?$colors["cell_background"]:$colors["border"]); ?>" rowspan="<?php echo $rowspan; ?>" width="<?php echo $dims["modrside"][0]; ?>" height="1"<?php echo ($imgs["modrside"] ? " background=\"".$master["currentskin"] . "modrside.gif\"" : ""); ?>><img src="img/pxt.gif" width="<?php echo $dims["modrside"][0]; ?>" height="1" border="0" alt="" /></td><?php } ?><?php } ?>
	</tr>
	<?php 
	if(!empty($str)) { ?>
		<tr>
			<td colspan="3" bgcolor="<?php echo $colors["cell_title"]; ?>" class="celltitle"><img src="img/pxt.gif" width="5" height="1" border="0" alt="" /><font color="<?php echo $colors["text"]; ?>"><b><?php echo (!empty($link)?"<a href=\"".$link."\" class=\"menu\">":"").$str.(!empty($link)?"&nbsp;&laquo;&laquo;&laquo;</a>":"");?></b></font><br /></td>
		</tr>
		<tr>
			<td colspan="3" width="1" height="<?php echo $container["verticalmodulepadding"]; ?>" bgcolor="<?php echo $current_color; ?>"><img src="img/pxt.gif" width="1" height="<?php echo $container["verticalmodulepadding"]; ?>" border="0" alt="" /></td>
		</tr><?php
	} ?>
	<tr>
		<td width="<?php echo $container["horizontalmodulepadding"]; ?>" height="1" bgcolor="<?php echo $current_color; ?>"><img src="img/pxt.gif" width="<?php echo $container["horizontalmodulepadding"]; ?>" height="1" border="0" alt="" /></td>
		<td width="100%" valign="top" bgcolor="<?php echo $current_color; ?>">
	<?php
}

function end_module($type = "main", $bgcolor = "") {
	global $colors, $container, $master;
	// globalize this for multiple formats??
	if(!empty($bgcolor)) { 
		$current_color = $bgcolor;
	} else {
		$current_color = $colors["cell_background"];
	}
	$sides = array("tl","tr","bl","br","t","b","lside","rside");
	for($i=0;$i<sizeof($sides);$i++) {
		$imgs["mod".$sides[$i]] = file_exists($master["currentskin"] . "mod".$sides[$i].".gif");
	}
	foreach($imgs AS $key => $value) {
		if($value) {
			$dims[$key] = getimagesize($master["currentskin"] . $key . ".gif");
		} else {
			$dims[$key] = array($container["border_width"],$container["border_height"]);
		}
	} ?>
		</td>
		<td width="<?php echo $container["horizontalmodulepadding"]; ?>" bgcolor="<?php echo $current_color; ?>"><img src="img/pxt.gif" width="<?php echo $container["horizontalmodulepadding"]; ?>" height="1" border="0" alt="" /></td>
	</tr>
	<tr>
		<td colspan="3" width="1" height="<?php echo $container["verticalmodulepadding"]; ?>" bgcolor="<?php echo $current_color; ?>"><img src="img/pxt.gif" width="1" height="<?php echo $container["verticalmodulepadding"]; ?>" border="0" alt="" /><br /></td>
	</tr>
	<?php
	if($container["border_height"]>0) { ?>
		<tr>
<?php 
        if($container["border_width"]>0) {
            if($type == "right" || $type == "main") { 
?>
                            <td bgcolor="<?php echo ($imgs['modbl']?$colors['cell_background']:$colors['border']); ?>" width="<?php echo $dims['modbl'][0]; ?>" height="<?php echo $dims['modbl'][1]; ?>"><img src="<?php echo ($imgs['modbl'] ? $master['currentskin'] . 'modbl.gif' : 'img/pxt.gif'); ?>" width="<?php echo $dims['modbl'][0]; ?>" height="<?php echo $dims["modbl"][1]; ?>" border="0" alt="" /></td><?php } ?><?php } ?>
		<td bgcolor="<?php echo ($imgs['modb']?$colors['cell_background']:$colors['border']); ?>" colspan="3"<?php echo ($imgs['modb'] ? " background=\"".$master['currentskin'] . "modb.gif\"" : ''); ?>><img src="img/pxt.gif" width="100%" height="<?php echo $dims["modb"][1]; ?>" border="0" alt=""></td>
<?php 
        if ($container['border_width'] > 0) { 
            if ($type == 'left' || $type == 'main') { 
?>
                                <td bgcolor="<?php echo ($imgs['modbr']?$colors['cell_background']:$colors['border']); ?>" width="<?php echo $dims['modbr'][0]; ?>" height="<?php echo $dims['modbr'][1]; ?>"><img src="<?php echo ($imgs['modbr'] ? $master['currentskin'] . 'modbr.gif' : 'img/pxt.gif'); ?>" width="<?php echo $dims['modbr'][0]; ?>" height="<?php echo $dims['modbr'][1]; ?>" border="0" alt="" /></td><?php } ?><?php } ?>
		</tr>
		<?php
	} ?>
	<!--<tr><td colspan="<?php print ($type == 'left' || $type == 'right' ? '5' : '7') ?>" width="1" height="<?php echo $container['horizontalpadding']; ?>"><img src="img/pxt.gif" width="1" height="<?php echo $container['horizontalpadding']; ?>" border="0" alt="" /></td></tr>-->
	</table>
	<img src="img/pxt.gif" border="0" width="1" height="<?php echo $container['verticalpadding']; ?>" alt="" /><br />
	<?php
}
?>