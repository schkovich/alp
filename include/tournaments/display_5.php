<?php
include_once 'include/tournaments/display_4.php';
// accepts $bracketDraw as current tournament bracket setup
$lb = ($losers_bracket_middle+$winners_bracket_total+1);

$bracketDraw[$winners_bracket_middle][$losers_bracket_round+2] = array("top",2);
$bracketDraw[$lb][$losers_bracket_round+2] = array("bottom",2);

$themiddle = floor(($lb+$winners_bracket_middle)/2);
for($i=($winners_bracket_middle+1);$i<$lb;$i++) {
	if($i!=$themiddle) $bracketDraw[$i][$losers_bracket_round+2] = array("straight",2);
	else $bracketDraw[$themiddle][$losers_bracket_round+2] = array("middle",2);
}
$winners_matchid = $dbc->database_fetch_assoc($dbc->database_query("SELECT id,server FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' AND rnd='0' AND mtc='0' AND bracket='l'"));
$winners_matchteam = $dbc->database_fetch_assoc($dbc->database_query("SELECT team FROM tournament_matches_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND matchid='".$winners_matchid["id"]."' AND top='1'"));
if($tournament["per_team"]>1) {
	$team = $dbc->database_fetch_assoc($dbc->database_query("SELECT name FROM tournament_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND id='".$winners_matchteam["team"]."'"));
} else {
	$team = $dbc->database_fetch_assoc($dbc->database_query("SELECT username AS name FROM users WHERE userid='".$winners_matchteam["team"]."'"));
}
$holder = $team["name"];
$holderid = $winners_matchteam["team"];
for($i=0;$i<sizeof($bracketDraw);$i++) {
	if($i!=$themiddle) {
		$bracketDraw[$i][] = array("",-2);
	} else {
		$bracketDraw[$i][] = array($holder,0,-1,-1,$holderid);
	}
}

$number_of_teams = $dbc->database_num_rows($dbc->database_query("SELECT team FROM tournament_matches_teams WHERE matchid='".$winners_matchid["id"]."' AND team!='0' AND top='1'"));
$colortemp = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_teams_type WHERE id='".$tournament["teamcolors"]."'"));
if($number_of_teams==0&&!$tournament["ffa"]) {
	if(!empty($tournament["teamcolors"]))  {
		$random = $dbc->database_fetch_assoc($dbc->database_query("SELECT RAND(".$winners_matchid["id"].") as random"));
		$random = round($random["random"]);
		if($random) {
			//$teamc = 1;
			//$otherteamc = 2;
			$teamc = "&nbsp;";
			$otherteamc = "&nbsp;";
			$color = $colortemp["onecolor"];
			$othercolor = $colortemp["twocolor"];
		} else {
			//$teamc = 2;
			//$otherteamc = 1;
			$teamc = "&nbsp;";
			$otherteamc = "&nbsp;";
			$color = $colortemp["twocolor"];
			$othercolor = $colortemp["onecolor"];
		}
	} else {
		$teamc = "&nbsp;";
		$otherteamc = "&nbsp;";
		$color = $colortemp["onecolor"];
		$othercolor = $colortemp["twocolor"];
	}

	$bracketDraw[$winners_bracket_middle][$winners_bracket_round-2] = array($teamc,3,$color);
	$bracketDraw[$lb][$losers_bracket_round+1] = array($otherteamc,3,$othercolor);
} else {
	$winners_match = $dbc->database_fetch_assoc($dbc->database_query("SELECT id FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' AND bracket='w' ORDER BY rnd DESC LIMIT 1"));
	$losers_match = $dbc->database_fetch_assoc($dbc->database_query("SELECT id FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' AND bracket='l' ORDER BY rnd DESC LIMIT 1"));

	$winners_currmatchteams = $dbc->database_fetch_assoc($dbc->database_query("SELECT team,score FROM tournament_matches_teams WHERE matchid='".$winners_match["id"]."' AND top='1'"));
	$losers_currmatchteams = $dbc->database_fetch_assoc($dbc->database_query("SELECT team,score FROM tournament_matches_teams WHERE matchid='".$losers_match["id"]."' AND top='0'"));
	$nextmatchteams = $dbc->database_fetch_assoc($dbc->database_query("SELECT team FROM tournament_matches_teams WHERE matchid='".$winners_matchid["id"]."' AND top='1'"));

	if(isset($winners_currmatchteams["score"])) $teamc = $winners_currmatchteams["score"];
	elseif(isset($losers_currmatchteams["score"])) $teamc = "0";
	else $teamc = "";
	if(isset($losers_currmatchteams["score"])) $otherteamc = $losers_currmatchteams["score"];
	elseif(isset($winners_currmatchteams["score"])) $otherteamc = "0";
	else $otherteamc = "";
	if($nextmatchteams["team"]==$winners_currmatchteams["team"]) {
		$color = $colortemp["onecolor"];
		$othercolor =$colortemp["twocolor"];
	} elseif($nextmatchteams["team"]==$losers_currmatchteams["team"]) {
		$color = $colortemp["onecolor"];
		$othercolor = $colortemp["twocolor"];
	}
	$bracketDraw[$winners_bracket_middle][$winners_bracket_round-2] = array($teamc,3,$color);
	$bracketDraw[$lb][$losers_bracket_round+1] = array($otherteamc,3,$othercolor);
}
$bracketDraw[$themiddle][$losers_bracket_round] = array($winners_matchid["server"],1,0,0,0,"l");

if($tournament["ttype"]==5) {
	display_brackets($bracketDraw);
}
?>