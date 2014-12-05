<?php
// single elimination - 1st through 4th place
// is included into disp_tournament.php
// input: $tournament["per_team"] and $tournament["tourneyid"]
// output: $first_id, $second_id, $third_id, $fourth_id (ids of the respective placings)

if($tournament["per_team"]==1) {
	$data = $dbc->database_query("SELECT userid AS t_id FROM tournament_players WHERE tourneyid='".$tournament["tourneyid"]."'");
	$n = $dbc->database_num_rows($data);
} elseif($tournament["per_team"]>1) {
	$data = $dbc->database_query("SELECT id AS t_id FROM tournament_teams WHERE tourneyid='".$tournament["tourneyid"]."'");
	$n = $dbc->database_num_rows($data);
}

$scores = array();
while($row = $dbc->database_fetch_assoc($data)) { 
	$survived = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_matches_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND team='".$row["t_id"]."'")) - 1;
	$total_score = $dbc->database_fetch_assoc($dbc->database_query("SELECT SUM(score) AS total_score FROM tournament_matches_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND team='".$row["t_id"]."'"));
	$scores[(empty($survived)?0:$survived)][$total_score["total_score"]][] = $row["t_id"];
}
$teamscores = array();
krsort($scores);
foreach($scores as $_3key => $val) {
	krsort($val);
	foreach($val as $_2key => $inner_val) {
		krsort($inner_val);
		foreach($inner_val as $teamid) {
			$teamscores[] = array($teamid,$_3key,$_2key);
		}
	}
}

if($tournament['lockfinish']) {
	$first_id = $teamscores[0][0];
	$second_id = $teamscores[1][0];
	$third_id = $teamscores[2][0];
	$fourth_id = $teamscores[3][0];
}
?>