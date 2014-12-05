<?php
$dbc->database_query("DELETE FROM tournament_matches WHERE tourneyid='".$tournament['tourneyid']."'");
$dbc->database_query("DELETE FROM tournament_matches_teams WHERE tourneyid='".$tournament['tourneyid']."'");


$servers = $dbc->database_num_rows($dbc->database_query("SELECT * FROM servers WHERE tourneyid='".$tournament['tourneyid']."'"));

$dbc->database_query("INSERT INTO tournament_matches (tourneyid, rnd, mtc, server) VALUES ('".$tournament['tourneyid']."','1','1','".($servers?"1":"0")."')") OR die ($dbc->database_error());
$saveroundid = $dbc->database_insert_id();

if($tournament['per_team']>1) {
	$query = $dbc->database_query("SELECT id AS tempid FROM tournament_teams WHERE tourneyid='".$tournament['tourneyid']."'");
} else {
	$query = $dbc->database_query("SELECT userid AS tempid FROM tournament_players WHERE tourneyid='".$tournament['tourneyid']."'");
}

while($row = $dbc->database_fetch_assoc($query)) {
	$dbc->database_query("INSERT INTO tournament_matches_teams (tourneyid,matchid,team) VALUES ('".$tournament['tourneyid']."','".$saveroundid."','".$row['tempid']."')") OR die ($dbc->database_error());
}
?>