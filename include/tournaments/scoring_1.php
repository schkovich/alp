<?php
// single elimination - 1st through 4th place
// is included into disp_tournament.php
// input: $tournament["ffa"] and $tournament["tourneyid"]
// output: $first_id, $second_id, $third_id, $fourth_id (ids of the respective placings)

// 3rd and 4th place go on scores
$data = $dbc->database_query("SELECT id FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' AND bracket='w' ORDER BY rnd DESC LIMIT 4");
$ids = array();
while($row = $dbc->database_fetch_assoc($data)) {
	$ids[] = $row["id"];
}
$final_match = $dbc->database_query("SELECT * FROM tournament_matches_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND matchid='".$ids[1]."' ORDER BY score DESC LIMIT 4");
$final_teams = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_matches_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND matchid='".$ids[1]."'"));
$firstplace = $dbc->database_fetch_assoc($dbc->database_query("SELECT team FROM tournament_matches_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND matchid='".$ids[0]."'"));
$first_id = $firstplace["team"];
if($tournament["ffa"]&&$final_teams>=4) {
	$scores = array();
	while($row = $dbc->database_fetch_assoc($final_match)) {
		$temp = allscores($row["team"]);
		$scores[$row["score"]][$temp][] = $row["team"];
	}
	krsort($scores);
	$teams = array();
	foreach($scores as $val) {
		krsort($val);
		foreach($val as $insideval) {
			foreach($insideval as $teamid) {
				$teams[] = $teamid;
			}
		}
	}
	$first_id = $teams[0];
	$second_id = $teams[1];
	$third_id = $teams[2];
	$fourth_id = $teams[3];
} else {
	$secondplace = $dbc->database_fetch_assoc($dbc->database_query("SELECT team FROM tournament_matches_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND matchid='".$ids[1]."' AND team!='".$first_id."'"));
	$second_id = $secondplace["team"];

	$query = "SELECT * FROM tournament_matches_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND team!='".$first_id."' AND team!='".$second_id."' AND (matchid='".$ids[2]."' OR matchid='".$ids[3]."') ORDER BY score DESC";
	$semifinal_match = $dbc->database_query($query);
	$scores = array();
	while($row = $dbc->database_fetch_assoc($semifinal_match)) {
		$temp = allscores($row["team"]);
		$scores[$temp][] = $row["team"];
	}
	krsort($scores);
	$teams = array();
	foreach($scores as $val) {
		foreach($val as $teamid) {
			$teams[] = $teamid;
		}
	}
	$third_id = (!empty($teams[0]) ? $teams[0] : '');
	$fourth_id = (!empty($teams[0]) ? $teams[1] : '');
}
?>