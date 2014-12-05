<?php
// single elimination - 1st through 4th place
// is included into disp_tournament.php
// input: $tournament["per_team"] and $tournament["tourneyid"]
// output: $first_id, $second_id, $third_id, $fourth_id (ids of the respective placings)

if($tournament["per_team"]==1) {
	$data = $dbc->database_query("SELECT userid AS id FROM tournament_players WHERE tourneyid='".$tournament["tourneyid"]."'");
	$n = $dbc->database_num_rows($data);
} elseif($tournament["per_team"]>1) {
	$data = $dbc->database_query("SELECT id FROM tournament_teams WHERE tourneyid='".$tournament["tourneyid"]."'");
	$n = $dbc->database_num_rows($data);
}

$scores = array();
while($row = $dbc->database_fetch_assoc($data)) { 
	$wins = $dbc->database_fetch_assoc($dbc->database_query("SELECT count(*) AS wins, top_x_advance AS team FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' AND top_x_advance!=0 AND top_x_advance='".$row["id"]."' GROUP BY top_x_advance ORDER BY wins DESC"));
	$losses = $dbc->database_fetch_assoc($dbc->database_query("SELECT count(*) AS losses FROM tournament_matches LEFT JOIN tournament_matches_teams ON tournament_matches.top_x_advance!=tournament_matches_teams.team AND tournament_matches.id=tournament_matches_teams.matchid WHERE tournament_matches.tourneyid='".$tournament["tourneyid"]."' AND tournament_matches.top_x_advance!=0 AND tournament_matches_teams.team=".$row["id"]));
	$tiecounter = 0;
	$ties = $dbc->database_query("SELECT score,matchid FROM tournament_matches_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND team='".$row["id"]."' AND score IS NOT NULL");
	while($temp = $dbc->database_fetch_assoc($ties)) {
		if($dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_matches_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND matchid='".$temp["matchid"]."' AND team!='".$row["id"]."' AND score='".$temp["score"]."'"))) {
			$tiecounter++;
		}
	}
	$total_score = $dbc->database_fetch_assoc($dbc->database_query("SELECT SUM(score) AS total_score FROM tournament_matches_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND team='".$row["id"]."'"));
	$scores[(empty($wins["wins"])?0:$wins["wins"])][(empty($losses["losses"])?0:$losses["losses"])][$tiecounter][$total_score["total_score"]][] = $row["id"];
}
$teamscores = array();
krsort($scores);
foreach($scores as $_3key => $val) {
	ksort($val);
	foreach($val as $_2key => $inner_val) {
		krsort($inner_val);
		foreach($inner_val as $_1key => $innerinner_val) {
			krsort($innerinner_val);
			foreach($innerinner_val as $_4key => $innerinnerinner_val) {
				krsort($innerinnerinner_val);
				foreach($innerinnerinner_val as $teamid) {
					$teamscores[] = array($teamid,$_3key,$_2key,$_1key,$_4key);
				}
			}
		}
	}
}

// check to see if each round robin match has a winner (if so, it is finished)
if($n%2==1) {
	if($dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' AND top_x_advance=0"))==$n) {
		$finished = true;
	} else {
		$finished = false;
	}
} else {
	$finished = !$dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' AND top_x_advance=0"));
}
if($finished) {
	$first_id = (!empty($teamscores[0][0])?$teamscores[0][0]:0);
	$second_id = (!empty($teamscores[1][0])?$teamscores[1][0]:0);
	$third_id = (!empty($teamscores[2][0])?$teamscores[2][0]:0);
	$fourth_id = (!empty($teamscores[3][0])?$teamscores[3][0]:0);
} else {
	$first_id = 0;
	$second_id = 0;
	$third_id = 0;
	$fourth_id = 0;
}
?>