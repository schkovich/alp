<?php
global $master;
if ($master['techsupport_index_limit']!=0) {
	$holder = ' LIMIT '.$master['techsupport_index_limit'];
} else {
	$holder = '';
}
$data = $dbc->query('SELECT itemid, severity, itemtime, userid FROM techsupport 
                        WHERE fixed=0 ORDER BY severity DESC'.$holder);
$c = array('009999','3333cc','009900','66cc00','99cc00','ffff00','ffcc00','ff6600','ff0000','990000'); ?>
<a href="techsupport.php"><strong>tech support</strong></a><?php get_go('techsupport.php'); ?><br />
<?php
if($data->numRows()) { 
	spacer(1,4,1); ?>
	<table cellpadding="0" cellspacing="0" width="100%" class="sm">
	<tr><td>&nbsp;</td><td>&nbsp;</td><td>severity</td></tr><?php
	while($row = $data->fetchRow()) {
		$user = $dbc->queryOne('SELECT username FROM users WHERE userid='.(int)$row['userid']);  ?>
		<tr style="<?php echo ($row['itemid']%2==1?'background-color:'.$colors['cell_alternate'].';':''); ?>color: #<?php echo $c[($row['severity'] * 2) - 1]; ?>">
			<td><a href="disp_users.php?id=<?php echo $row['userid']; ?>" style="color: #<?php echo $c[($row['severity'] * 2) - 1]; ?>"><strong><?php echo $user; ?></strong></a><br /><img src="img/pxt.gif" width="1" height="3" border="0" alt="" /><br /></td>
			<?php $time = round((date('U')-date('U', strtotime($row['itemtime'])))/3600,1); ?>
			<td><?php echo ($time != 0 ? $time.' hours ago' : 'now'); ?></td>
			<td><?php echo $row['severity']; ?>/5</td>
		</tr>
		<?php
	} ?>
	</table>
	<?php
} ?>