<?php
// double elimination - 1st through 4th place
// is included into disp_tournament.php
// input: $tournament["tourneyid"]
// output: $first_id, $second_id, $third_id, $fourth_id (ids of the respective placings)
$interbracket_match = $dbc->database_fetch_assoc($dbc->database_query("SELECT id FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' AND bracket='l' AND rnd='0' and mtc='0'"));
$loserssbracket = $dbc->database_query("SELECT id FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' AND bracket='l' ORDER BY rnd DESC LIMIT 3");
$ids = array();
while($row = $dbc->database_fetch_assoc($loserssbracket)) {
	$ids[] = $row["id"];
}

$winners_match = $dbc->database_fetch_assoc($dbc->database_query("SELECT id FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' AND bracket='w' ORDER BY rnd DESC LIMIT 1"));
$winners_team = $dbc->database_fetch_assoc($dbc->database_query("SELECT team FROM tournament_matches_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND matchid='".$winners_match["id"]."' AND top='1'"));
$losers_team = $dbc->database_fetch_assoc($dbc->database_query("SELECT team FROM tournament_matches_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND matchid='".$ids[0]."' AND top='0'"));

$firstplace = $dbc->database_fetch_assoc($dbc->database_query("SELECT team FROM tournament_matches_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND matchid='".$interbracket_match["id"]."' AND top='1'"));
$first_id = $firstplace["team"];
if($winners_team["team"]==$first_id) {
	$second_id = $losers_team["team"];
} else {
	$second_id = $winners_team["team"];
}
$thirdplace = $dbc->database_fetch_assoc($dbc->database_query("SELECT team FROM tournament_matches_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND matchid='".$ids[1]."' AND team!='".$first_id."' AND team!='".$second_id."'"));
$third_id = $thirdplace["team"];
$fourthplace = $dbc->database_fetch_assoc($dbc->database_query("SELECT team FROM tournament_matches_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND matchid='".$ids[2]."' AND team!='".$first_id."' AND team!='".$second_id."' AND team!='".$third_id."'"));
$fourth_id = $fourthplace["team"];
//echo $tournament["ttype"]." ".$first_id." ".$second_id."<br />";
?>
