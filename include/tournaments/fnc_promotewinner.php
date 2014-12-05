<?php
function get_scores_submitted($tourneyid, $matchid)
{
    global $dbc;
	return $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_matches_score_votes WHERE tourneyid='".$tourneyid."' AND matchid='".$matchid."' GROUP BY userid"));
}

function get_scores_needed($tourneyid, $matchid)
{
    global $dbc;
	return $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_matches_teams WHERE tourneyid='".$tourneyid."' AND matchid='".$matchid."'"));
}

function all_scores_submitted($tourneyid, $matchid)
{
	// checks to see if all necessary teams have submitted their scores.
	if(get_scores_needed($tourneyid,$matchid) == get_scores_submitted($tourneyid,$matchid)) {
		return true;
	} else {
		return false;
	}
}

function is_score_discrep($tourneyid,$matchid)
{
    global $dbc;
	// checks for a score discrepency in the player submitted scores.
	$boolean = false;
	if (all_scores_submitted($tourneyid,$matchid)) {
		$scorecheck = array();
		$scoredata = $dbc->database_query("SELECT * FROM tournament_matches_score_votes WHERE tourneyid='".$tourneyid."' AND matchid='".$matchid."'");
		// check to see the score votes match up.
		while($scorerow = $dbc->database_fetch_assoc($scoredata)) {
			if(empty($scorecheck[$scorerow['entry_id']])) {
				$scorecheck[$scorerow['entry_id']] = $scorerow['entry_val'];
			} elseif($scorecheck[$scorerow['entry_id']] != $scorerow['entry_val']) {
				$boolean = true;
			}
		}
	} else {
		$boolean = true;
	}
	return $boolean;
}

function no_winner($tourneyid, $matchid, $i='', $j='',  $bracket='', $L_rnd='', $L_mtc='', $L_top='')
{
    global $dbc;
	// makes sure there isn't already a winner promoted in the next round.
	$boolean = true;
	$tournament = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournaments WHERE tourneyid='".$tourneyid."'"));
	if($tournament['ffa']) {
		$nextmatch = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_matches WHERE tourneyid='".$tourneyid."' AND rnd='".($i+1)."' AND mtc='".ceil($j/4)."' AND bracket='".$bracket."'"));
		if(!empty($nextmatch['id'])) {
			$boolean = !$dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_matches_teams WHERE tourneyid='".$tourneyid."' AND matchid='".$nextmatch["id"]."' AND team > 0 AND top='".($j%4==2?"1":"0")."'"));
		}
	} else {
		if(!empty($i) && !empty($j) && !empty($matchid) && !empty($bracket)) {
			if($tournament["per_team"]==1) {
				$n = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_players WHERE tourneyid='".$tourneyid."'"));
			} elseif($tournament["per_team"]>1) {
				$n = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_teams WHERE tourneyid='".$tourneyid."'"));
			}
			$NHPT = pow(2,ceil(log($n)/log(2)));
			$NLPT = pow(2,floor(log($n-1)/log(2)));

			if($bracket=="w") {
				$nextmatch = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_matches WHERE tourneyid='".$tourneyid."' AND rnd='".($i+1)."' AND mtc='".ceil($j/4)."' AND bracket='w'"));
			} else {
				if($n>($NHPT-(($NHPT-$NLPT)/2))) { 
					if(($i+1)%2==0&&$i>1) $nextmatch = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_matches WHERE tourneyid='".$tourneyid."' AND rnd='".($i+1)."' AND mtc='".ceil($j/2)."' AND bracket='".$bracket."'"));
					else $nextmatch = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_matches WHERE tourneyid='".$tourneyid."' AND rnd='".($i+1)."' AND mtc='".ceil($j/4)."' AND bracket='".$bracket."'"));
				} else {
					if(($i+1)%2==1&&$i>1)	$nextmatch = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_matches WHERE tourneyid='".$tourneyid."' AND rnd='".($i+1)."' AND mtc='".ceil($j/2)."' AND bracket='".$bracket."'"));
					else $nextmatch = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_matches WHERE tourneyid='".$tourneyid."' AND rnd='".($i+1)."' AND mtc='".ceil($j/4)."' AND bracket='".$bracket."'"));
				}
			}
			if(!empty($nextmatch['id'])) $boolean = !$dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_matches_teams WHERE matchid='".$nextmatch['id']."' AND team > 0 AND top='".(($j%4==1)||($j%4==2))."'"));
		} elseif($i==0&&($j==0||$j==1)&&$bracket=="l") {
			$nextmatch = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_matches WHERE tourneyid='".$tourneyid."' AND rnd='0' AND mtc='".$j."' AND bracket='l'"));
			if(!empty($nextmatch['id'])) $boolean = !$dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_matches_teams WHERE matchid='".$nextmatch["id"]."' AND tourneyid='".$tourneyid."' AND team > 0 AND top='1'"));
		} elseif(!empty($matchid)) {
			$boolean = !$dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_matches WHERE top_x_advance > 0 AND id='".$matchid."'"));
		}
	}
	return $boolean;
}

function promote_winner($tourneyid, $matchid, $scores, $i='', $j='',  $bracket='', $L_rnd='', $L_mtc='', $L_top='')
{
    global $dbc;
	// if all scores are inputted by the teams playing in the match (or the team captain), make the scores official and promote the winner to the next round.
	// scores is an array
	$tournament = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournaments WHERE tourneyid='".$tourneyid."'"));

	if(current_security_level()>=1) {
		if($tournament["per_team"]==1) {
			$is_playing_in_match = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_matches_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND matchid='".$matchid."' AND team='".$_COOKIE["userid"]."'"));
		} else {
			$teaminfo = $dbc->database_fetch_assoc($dbc->database_query("SELECT id FROM tournament_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND captainid='".$_COOKIE["userid"]."'"));
			if(!empty($teaminfo["id"])) {
				$is_playing_in_match = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_matches_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND matchid='".$matchid."' AND team='".$teaminfo["id"]."'"));
			} else {
				$is_playing_in_match = 0;
			}
		}
	} else {
		$is_playing_in_match = 0;
	}

	if($is_playing_in_match) {
	//if(current_security_level()>=2||(current_security_level()>=1&&$tournament["moderatorid"]==$_COOKIE["userid"])||$is_playing_in_match) {
		$allgood = true;
		if($tournament["ffa"]) { 
			$data = $dbc->database_query("SELECT * FROM tournament_matches_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND matchid='".$matchid."'");
			while($row = $dbc->database_fetch_assoc($data)) {
				if($scores[$row["id"]."_score"]===0) $temp = "'0.0'";
				elseif($scores[$row["id"]."_score"]==='') $temp = "NULL";
				else $temp = "'".$scores[$row["id"]."_score"]."'";
				if(!$dbc->database_query("UPDATE tournament_matches_teams SET score=".$temp." WHERE id='".$row["id"]."'")) {
					$allgood = false;
				}
			}
			$nextmatch = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_matches WHERE tourneyid='".$tourneyid."' AND rnd='".($i+1)."' AND mtc='".ceil($j/4)."' AND bracket='".$bracket."'"));
			$previouswinners = array();
			$data = $dbc->database_query("SELECT * FROM tournament_matches_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND matchid='".$nextmatch["id"]."' AND top='".($j%4==2?"1":"0")."'");
			while($row = $dbc->database_fetch_assoc($data)) {
				if($row["team"]!=0) $previouswinners[] = $row["team"];
			}
			$nextmatch_slots = ceil($dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_matches_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND matchid='".$nextmatch["id"]."'"))/2);
			$data = $dbc->database_query("SELECT * FROM tournament_matches_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND matchid='".$matchid."' ORDER BY score DESC LIMIT ".$nextmatch_slots);
			$baseid = $dbc->database_fetch_assoc($dbc->database_query("SELECT MIN(id) AS id FROM tournament_matches_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND matchid='".$nextmatch["id"]."' AND top='".($j%4==2?"1":"0")."'"));
			$counter = 0;
			$newwinners = array();
			while($row = $dbc->database_fetch_assoc($data)) {
				if($row["team"]!=0) $newwinners[] = $row["team"];
				if(!$dbc->database_query("UPDATE tournament_matches_teams SET team='".$row["team"]."' WHERE tourneyid='".$tournament["tourneyid"]."' AND matchid='".$nextmatch["id"]."' AND id='".($baseid["id"]+$counter)."'")) {
					$allgood = false;
				}
				$counter++;
			}
			$garbage = array_diff($previouswinners,$newwinners);
			if(sizeof($garbage)>0) {
				$query = "UPDATE tournament_matches_teams SET team='0',score=NULL WHERE tourneyid='".$tournament["tourneyid"]."' AND (";
				$counter = 1;
				foreach($garbage as $val) {
					$query .= "team='".$val."'";
					if(sizeof($garbage)!=$counter) $query .= " OR ";
					$counter++;
				}	
				$query .= ") AND matchid<'".$matchid."'";
				if(!$dbc->database_query($query)) {
					$allgood = false;
				}
			}
			if($allgood) {
				return true;
			} else {
				return false;
			}
		} else {
			if(!empty($i)&&!empty($j)&&isset($scores['top'])&&isset($scores['bottom'])&&!empty($matchid)&&!empty($bracket)) {
				if($tournament["per_team"]==1) {
					$n = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_players WHERE tourneyid='".$tournament["tourneyid"]."'"));
				} elseif($tournament["per_team"]>1) {
					$n = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_teams WHERE tourneyid='".$tournament["tourneyid"]."'"));
				}
				$NHPT = pow(2,ceil(log($n)/log(2)));
				$NLPT = pow(2,floor(log($n-1)/log(2)));
	
				if($bracket=="w") {
					$temp = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_matches WHERE tourneyid='".$tourneyid."' AND rnd='".$i."' AND bracket='w' ORDER BY id LIMIT 1"));
					$matchinfo = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_matches WHERE tourneyid='".$tourneyid."' AND rnd='".($i+1)."' AND mtc='".ceil($j/4)."' AND bracket='w'"));
				} else {
					if($n>($NHPT-(($NHPT-$NLPT)/2))) { 
						if(($i+1)%2==0&&$i>1) $matchinfo = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_matches WHERE tourneyid='".$tourneyid."' AND rnd='".($i+1)."' AND mtc='".ceil($j/2)."' AND bracket='".$bracket."'"));
						else $matchinfo = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_matches WHERE tourneyid='".$tourneyid."' AND rnd='".($i+1)."' AND mtc='".ceil($j/4)."' AND bracket='".$bracket."'"));
					} else {
						if(($i+1)%2==1&&$i>1)	$matchinfo = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_matches WHERE tourneyid='".$tourneyid."' AND rnd='".($i+1)."' AND mtc='".ceil($j/2)."' AND bracket='".$bracket."'"));
						else $matchinfo = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_matches WHERE tourneyid='".$tourneyid."' AND rnd='".($i+1)."' AND mtc='".ceil($j/4)."' AND bracket='".$bracket."'"));
					}
				}
				if($scores['top']===0) $temp = "'0.0'";
				elseif($scores['top']==='') $temp = "NULL";
				else $temp = "'".$scores['top']."'";
				if(!$dbc->database_query("UPDATE tournament_matches_teams SET score=".$temp." WHERE matchid='".$matchid."' AND top='1'")) {
					$allgood = false;
				}
				if($scores['bottom']===0) $temp = "'0.0'";
				elseif($scores['bottom']==='') $temp = "NULL";
				else $temp = "'".$scores['bottom']."'";
				if(!$dbc->database_query("UPDATE tournament_matches_teams SET score=".$temp." WHERE matchid='".$matchid."' AND top='0'")) {
					$allgood = false;
				}
				if($scores['bottom']==''&&$scores['top']=='') {
					$currwinner["team"] = 0;
				} else {
					$currwinner = $dbc->database_fetch_assoc($dbc->database_query("SELECT team FROM tournament_matches_teams WHERE matchid='".$matchid."' AND top='".($scores['top']>$scores['bottom']?"1":"0")."'"));
				}
				
				$temp = $dbc->database_fetch_assoc($dbc->database_query("SELECT team FROM tournament_matches_teams WHERE matchid='".$matchinfo["id"]."' AND top='".(($j%4==1)||($j%4==2))."'"));
				$previouswinner = $temp["team"];
				if($bracket=="l") {
					if($tournament["per_team"]==1) {
						$n = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_players WHERE tourneyid='".$tournament["tourneyid"]."'"));
					} elseif($tournament["per_team"]>1) {
						$n = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_teams WHERE tourneyid='".$tournament["tourneyid"]."'"));
					}
					$maxrounds = $dbc->database_fetch_assoc($dbc->database_query("SELECT MAX(rnd) as rnd FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' AND bracket='l'"));
					if(($i+1)==$maxrounds["rnd"]) {
						$top = 0;
					} else {
						$NHPT = pow(2,ceil(log($n)/log(2)));
						$NLPT = pow(2,floor(log($n-1)/log(2)));
						if($n>($NHPT-(($NHPT-$NLPT)/2))) { 
							if(($i+1)%2==0) $top = 1;
							else $top = (($j%4==1)||($j%4==2));
						} else {
							if(($i+1)%2==1) $top = 1;
							else $top = (($j%4==1)||($j%4==2));
						}
					}
				} else {
				 	$top = (($j%4==1)||($j%4==2));
				}
	
				if(($scores['top']!=$scores['bottom']||($scores['bottom']==''&&$scores['top']==''))&&$previouswinner!=$currwinner["team"]) {
					if($previouswinner!=0) {
						$data = $dbc->database_query("SELECT * FROM tournament_matches WHERE tourneyid='".$tourneyid."' AND rnd>'".$i."' AND bracket='".$bracket."' ORDER BY id DESC");
						while($row = $dbc->database_fetch_assoc($data)) {
							if($dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_matches_teams WHERE matchid='".$row["id"]."' AND team='".$previouswinner."'"))) {
								if($row["rnd"]>($i+1)) {
									if(!$dbc->database_query("UPDATE tournament_matches_teams SET team='0' WHERE matchid='".$row["id"]."' AND team='".$previouswinner."'")) {
										$allgood = false;
									}
								}
								if(!$dbc->database_query("UPDATE tournament_matches_teams SET score=NULL WHERE matchid='".$row["id"]."'")) {
									$allgood = false;
								}
							}
						}
					}
					if(!$dbc->database_query("UPDATE tournament_matches_teams SET team='".$currwinner["team"]."' WHERE matchid='".$matchinfo["id"]."' AND top='".$top."'")) {
						$allgood = false;
					}
					if($bracket=="w"&&!empty($L_rnd)&&!empty($L_mtc)&&isset($L_top)&&!empty($matchid)) {
						$loser = $dbc->database_fetch_assoc($dbc->database_query("SELECT team FROM tournament_matches_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND matchid='".$matchid."' AND team!='".$currwinner["team"]."'"));
						$losers_match = $dbc->database_fetch_assoc($dbc->database_query("SELECT id FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' AND rnd='".$L_rnd."' AND mtc='".$L_mtc."' AND bracket='l'"));
						if(!$dbc->database_query("UPDATE tournament_matches_teams SET team='".$loser["team"]."' WHERE matchid='".$losers_match["id"]."' AND top='".$L_top."'")) {
							$allgood = false;
						}
					}
				}
			} elseif($i==0&&($j==0||$j==1)&&$bracket=="l"&&isset($scores['top'])&&isset($scores['bottom'])) {
				$winners_id = $dbc->database_fetch_assoc($dbc->database_query("SELECT id FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' AND bracket='w' ORDER BY rnd DESC LIMIT 1"));
				$losers_id = $dbc->database_fetch_assoc($dbc->database_query("SELECT id FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' AND bracket='l' ORDER BY rnd DESC LIMIT 1"));
				$matchid = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' AND rnd='0' AND mtc='".$j."' AND bracket='l'"));
				if(!$dbc->database_query("UPDATE tournament_matches_teams SET score='".$scores['top']."' WHERE tourneyid='".$tournament["tourneyid"]."' AND top='1' AND matchid='".$winners_id["id"]."'")) {
					$allgood = false;
				}
				if(!$dbc->database_query("UPDATE tournament_matches_teams SET score='".$scores['bottom']."' WHERE tourneyid='".$tournament["tourneyid"]."' AND top='0' AND matchid='".$losers_id["id"]."'")) {
					$allgood = false;
				}
				if($scores['top']!=$scores['bottom']) {
					$winner = $dbc->database_fetch_assoc($dbc->database_query("SELECT team FROM tournament_matches_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND matchid='".($scores['top']>$scores['bottom']?$winners_id["id"]:$losers_id["id"])."'"));
					$query = "UPDATE tournament_matches_teams SET team='".$winner["team"]."' WHERE matchid='".$matchid["id"]."' AND tourneyid='".$tournament["tourneyid"]."' AND top='1'";
					if(!$dbc->database_query($query)) {
						$allgood = false;
					}
				}
				if($allgood) {
					$str = "success.";
				} else {
					$str = "error!";
				}
			} elseif(!empty($matchid)&&isset($scores['left'])&&isset($scores['right'])) {
				if($scores['left']>$scores['right']) {
					$temp = $dbc->database_fetch_assoc($dbc->database_query("SELECT team FROM tournament_matches_teams WHERE matchid='".$matchid."' AND top='1'"));
					$winner = $temp["team"];
				} elseif($scores['left']<$scores['right']) {
					$temp = $dbc->database_fetch_assoc($dbc->database_query("SELECT team FROM tournament_matches_teams WHERE matchid='".$matchid."' AND top='0'"));
					$winner = $temp["team"];
				}
				if(!$dbc->database_query("UPDATE tournament_matches SET top_x_advance='".($scores['left']!=$scores['right']?$winner:"0")."' WHERE id='".$matchid."'")) {
					$allgood = false;
				}
				if(!$dbc->database_query("UPDATE tournament_matches_teams SET score='".$scores['left']."' WHERE matchid='".$matchid."' AND top='1'")||!$dbc->database_query("UPDATE tournament_matches_teams SET score='".$scores['right']."' WHERE matchid='".$matchid."' AND top='0'")) {
					$allgood = false;
				}
			} else { 
				return false;
			}
			if($allgood) {
				return true;
			} else {
				return false;
			}
		}
	} else {
		return false;
	}
}
?>