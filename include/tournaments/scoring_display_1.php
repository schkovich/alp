<?php
if($tournament["per_team"]==1) {
	$temp = $dbc->database_fetch_assoc($dbc->database_query("SELECT username FROM users WHERE userid='".$first_id."'"));
	$first = $temp["username"];
	$temp = $dbc->database_fetch_assoc($dbc->database_query("SELECT username FROM users WHERE userid='".$second_id."'"));
	$second = $temp["username"];
	$temp = $dbc->database_fetch_assoc($dbc->database_query("SELECT username FROM users WHERE userid='".$third_id."'"));
	$third = $temp["username"];
	$temp = $dbc->database_fetch_assoc($dbc->database_query("SELECT username FROM users WHERE userid='".$fourth_id."'"));
	$fourth = $temp["username"];
} else {
	$temp = $dbc->database_fetch_assoc($dbc->database_query("SELECT name FROM tournament_teams WHERE id='".$first_id."'"));
	$first = $temp["name"];
	$temp = $dbc->database_fetch_assoc($dbc->database_query("SELECT name FROM tournament_teams WHERE id='".$second_id."'"));
	$second = $temp["name"];
	$temp = $dbc->database_fetch_assoc($dbc->database_query("SELECT name FROM tournament_teams WHERE id='".$third_id."'"));
	$third = $temp["name"];
	$temp = $dbc->database_fetch_assoc($dbc->database_query("SELECT name FROM tournament_teams WHERE id='".$fourth_id."'"));
	$fourth = $temp["name"];
}
if($first_id!=0&&$second_id!=0) { ?>
	<table border=0 cellpadding=8><tr><td>
	<table border=0 cellpadding=0>
	<tr><td><font color="<?php echo $colors["primary"]; ?>"><b>1st</b></font>&nbsp;</td><td>| <b><?php echo $first; ?></b> <?php if(allscores($first_id)!=0) { ?><font class="sm">(<?php echo allscores($first_id); ?> points)</font><?php } ?><br /></td></tr>
	<tr><td><font color="<?php echo $colors["secondary"]; ?>"><b>2nd</b></font>&nbsp;</td><td>| <b><?php echo $second; ?></b> <?php if(allscores($second_id)!=0) { ?><font class="sm">(<?php echo allscores($second_id); ?> points)</font><?php } ?><br /></td></tr>
	<?php if(isset($third)) {?><tr><td><b>3rd</b>&nbsp;</td><td>| <b><?php echo $third; ?></b> <?php if(allscores($third_id)!=0) { ?><font class="sm">(<?php echo allscores($third_id); ?> points)</font><?php } ?><br /></td><?php } else { ?><td>&nbsp;</td></tr><?php } ?>
	<?php if(isset($fourth)) {?><tr><td><b>4th</b>&nbsp;</td><td>| <b><?php echo $fourth; ?></b> <?php if(allscores($fourth_id)!=0) { ?><font class="sm">(<?php echo allscores($fourth_id); ?> points)</font><?php } ?><br /></td><?php } else { ?><td>&nbsp;</td></tr><?php } ?>
	</table>
	</td></tr></table>
	<?php
}
?>