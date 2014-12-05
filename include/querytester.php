<?php
require_once '../_config.php';
require_once '_functions.php';
if (current_security_level() >= 2) {  ?>
	<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
	
	<html>
	<head>
		<title>Untitled</title>
	</head>
	
	<body>
	<?php
	/*$data = $dbc->database_query("SELECT * FROM games WHERE short=''");
	while($row = $dbc->database_fetch_assoc($data)) { 
		echo "\"UPDATE games SET short='' WHERE name='".$row['name']."'\",<br />";
	}*/ ?>
	<div align="center">
	<table width="600" style="font: 9px Verdana" border=1>
	<?php
	if(!empty($_POST)&&!empty($_POST['q'])) {
		$query = $dbc->database_query($_POST['q']);
		$counter = 0;
		while($row = $dbc->database_fetch_assoc($query)) {
			if($counter==0) {
				echo "<tr>";
				foreach($row as $key => $val) {
					echo "<td>".$key."</td>";
				} 
				echo "</tr>";
			}
			echo "<tr>";
			foreach($row as $key => $val) {
				echo "<td>".$val."</td>";
			} 
			echo "</tr>";
			$counter++;
		}
	}
	?></table>
	<form action="<?php echo get_script_name(); ?>" method="post" type="post">
	<textarea name='q' cols="" rows="10" wrap="hard" style="width: 600px"><?php echo (!empty($_POST['q'])?$_POST['q']:''); ?></textarea><br />
	<input type="submit" style="width: 600px" /></div>
	</form>
	</body>
	</html>
	<?php
} ?>
