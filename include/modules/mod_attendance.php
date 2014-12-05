<table border="0" width="100%" cellpadding="0" cellspacing="0">
<?php
global $dbc;
?>
<tr>
	<td width="150"><a href="users.php"><strong>attendance</strong></a></td>
	<td><font color="<?php echo $colors['primary']; ?>"><strong><?php echo $dbc->queryOne('SELECT count(userId) from users'); ?></strong></font>/<?php echo $lan['max']; ?></td>
</tr>
</table>