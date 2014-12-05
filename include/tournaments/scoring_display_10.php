<br />
<table border=0 cellpadding=4 cellspacing=0 style="width: 95%; font-size: 11px" align="center" class="centerd">
<tr bgcolor="<?php echo $colors["cell_title"]; ?>">
	<td width="40">#</td>
	<td><b>team name</b></td>
	<td width="160"><div align="center">win - loss - tie</div></td>
	<td width="100"><div align="center">total score</div></td>
	<td width="100"><div align="center">games played</div></td>
	<td width="100"><div align="center">games left</div></td>
</tr>
<?php

$total_rounds = $n-1;
$counter = 0;
$previous = array(0,0,0,0,0);
foreach($teamscores as $val) {
	if($val[1]==$previous[1]&&$val[2]==$previous[2]&&$val[3]==$previous[3]&&$val[4]==$previous[4]) {
	
	} else {
		$counter++;
	}
	if($tournament["per_team"]>1) { 
		$temp = $dbc->database_fetch_assoc($dbc->database_query("SELECT name FROM tournament_teams WHERE id='".$val[0]."'")); 
	} else {
		$temp = $dbc->database_fetch_assoc($dbc->database_query("SELECT username AS name FROM users WHERE userid='".$val[0]."'")); 
	} 
	
	?>
	<tr>
		<td width="40"><?php echo $counter; ?></td>
		<td><b><?php echo $temp["name"]; ?></b></td>
		<td width="160"><div align="center"><?php echo $val[1]; ?> - <?php echo $val[2]; ?> - <?php echo $val[3]; ?></div></td>
		<td width="100"><div align="center"><?php echo $val[4]; ?></div></td>
		<td width="100"><div align="center"><?php echo ($val[1]+$val[2]+$val[3]); ?></div></td>
		<td width="100"><div align="center"><?php echo ($total_rounds - ($val[1]+$val[2]+$val[3])); ?></div></td>
	</tr>
	<?php
	$previous = $val;
}

?>
</table>
<br />
