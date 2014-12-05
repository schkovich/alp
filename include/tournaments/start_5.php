<?php
// bracket creation code for double elimination tournament
include_once("include/tournaments/start_4.php");

$servercounter = 0;

$query = "INSERT INTO tournament_matches (tourneyid,rnd,mtc,server,bracket) VALUES ('".$tournament["tourneyid"]."','0','0','".($servers>0?$servercounter%$servers+1:0)."','l')";
$dbc->database_query($query);
$matchid = $dbc->database_insert_id();
$query = "INSERT INTO tournament_matches_teams (tourneyid,matchid,team,top) VALUES ('".$tournament["tourneyid"]."','".$matchid."','0','1')";
$dbc->database_query($query);
$query = "INSERT INTO tournament_matches_teams (tourneyid,matchid,team,top) VALUES ('".$tournament["tourneyid"]."','".$matchid."','0','0')";
$dbc->database_query($query);

$servercounter = 0;

$query = "INSERT INTO tournament_matches (tourneyid,rnd,mtc,server,bracket) VALUES ('".$tournament["tourneyid"]."','0','1','".($servers>0?$servercounter%$servers+1:0)."','l')";
$dbc->database_query($query);
$matchid = $dbc->database_insert_id();
$query = "INSERT INTO tournament_matches_teams (tourneyid,matchid,team,top) VALUES ('".$tournament["tourneyid"]."','".$matchid."','0','1')";
$dbc->database_query($query);
$query = "INSERT INTO tournament_matches_teams (tourneyid,matchid,team,top) VALUES ('".$tournament["tourneyid"]."','".$matchid."','0','0')";
$dbc->database_query($query);

?>