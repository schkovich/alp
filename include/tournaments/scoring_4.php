<?php
// consolation - 1st through 4th place
// is included into disp_tournament.php
// input: $tournament["tourneyid"]
// output: $first_id, $second_id, $third_id, $fourth_id (ids of the respective placings)

$winnersbracket = $dbc->database_query("SELECT id FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' AND bracket='w' ORDER BY rnd DESC LIMIT 2");
$ids = array();
while($row = $dbc->database_fetch_assoc($winnersbracket)) {
	$ids[] = $row["id"];
}
$firstplace = $dbc->database_fetch_assoc($dbc->database_query("SELECT team FROM tournament_matches_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND matchid='".$ids[0]."'"));
$first_id = $firstplace["team"];
$secondplace = $dbc->database_fetch_assoc($dbc->database_query("SELECT team FROM tournament_matches_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND matchid='".$ids[1]."' AND team!='".$first_id."'"));
$second_id = $secondplace["team"];

$loserssbracket = $dbc->database_query("SELECT id FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' AND bracket='l' ORDER BY rnd DESC LIMIT 3");
$ids = array();
while($row = $dbc->database_fetch_assoc($loserssbracket)) {
	$ids[] = $row["id"];
}
$thirdplace = $dbc->database_fetch_assoc($dbc->database_query("SELECT team FROM tournament_matches_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND matchid='".$ids[1]."' AND top='1'"));
$third_id = $thirdplace["team"];
$fourthplace = $dbc->database_fetch_assoc($dbc->database_query("SELECT team FROM tournament_matches_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND matchid='".$ids[2]."' AND team!='".$third_id."'"));
$fourth_id = $fourthplace["team"];
?>