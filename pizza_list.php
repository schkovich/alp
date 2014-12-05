<?php
require_once 'include/_universal.php';
$x = new universal(get_lang('plural'),get_lang('singular'),1);
if($toggle['foodrun']&&$x->is_secure()) {
	$x->display_top();
	?>
	<b><?php echo get_lang('plural'); ?></b>:<br>
	<br>
	<?php
    $x->add_related_link(get_lang('link_admin_pizza'),'admin_pizza.php',2);
    $x->add_related_link(get_lang('link_admin_pizza_list'),'admin_pizza_list.php',2);
    $x->add_related_link(get_lang('link_chng_pizza'),'chng_pizza.php',2);
    $x->add_related_link(get_lang('link_pizza'),'pizza.php',1);
	$x->display_related_links();
	?>
	<table border=0 width="100%" cellpadding=3 cellspacing=0 style="font-size: 11px">
	<tr>
	<TD class="smm" bgcolor="<?php echo $colors['cell_title']; ?>"><?php echo get_lang('tr_pizza'); ?></TD>
	<TD class="smm" bgcolor="<?php echo $colors['cell_title']; ?>"><?php echo get_lang('tr_description'); ?></TD>
	<TD class="smm" bgcolor="<?php echo $colors['cell_title']; ?>"><?php echo get_lang('tr_price'); ?></TD>
	</tr>
	<?php
	$counter = 0;
	$data = $dbc->database_query("SELECT * FROM pizza WHERE enabled='1' ORDER BY pizzaid");
	while($row = $dbc->database_fetch_assoc($data)) {
		if($counter%2==0) {
			$loopcolor = $colors['cell_background'];
		} else {
			$loopcolor = $colors['cell_alternate'];
		}
		?>
			<tr>
				<td <?php echo ($counter%2==1?" bgcolor=\"".$colors['cell_alternate']."\"":""); ?>><?php echo $row['pizza']; ?></td>
				<td <?php echo ($counter%2==1?" bgcolor=\"".$colors['cell_alternate']."\"":""); ?>><?php echo $row['description']; ?></td>
				<td <?php echo ($counter%2==1?" bgcolor=\"".$colors['cell_alternate']."\"":""); ?>>$<?php echo $row['price']; ?></td>
			</tr>
			<?php
		$counter++;
	}
	?>
	</table>
	<?php
	$x->display_bottom();
} else {
	$x->display_slim(get_lang('noauth'));
}
?>
