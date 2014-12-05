<?php
global $master,$dbc;
$row = $dbc->database_fetch_assoc($dbc->database_query('SELECT userid, username, quote from users ORDER BY rand('.(date('z')*100+date('H')).') limit 1'));
?>
<script language="JavaScript" type="text/javascript">
<!-- 
function newWindow(url,width,height,name) {
	window.open(url,name,"width="+width+", height="+height+",resizable=yes,scrollbars=yes,toolbar=no,location=no,directories=no,status=no,copyhistory=no,screenX=50,screenY=150,left=50,top=150");
} // -->
</script>
<table border="0" width="100%" cellpadding="0" cellspacing="0">
<tr>
	<td width="150"><?php if (!empty($master['gamerhour']) || !empty($row['quote'])) { ?><a href="javascript:newWindow('gamerofthehour.php?id=<?php echo $row['userid']; ?>','200','180','gamerofthehour')"><?php } ?><b>gamer of the hour</b><?php if (!empty($master['gamerhour'])) { ?></a><?php } ?></td>
	<td><a href="disp_users.php?id=<?php echo $row['userid']; ?>" style="color: <?php echo $colors['primary']; ?>"><b><?php echo $row['username']; ?></b></a></td>
</tr>
</table>