<?php
include_once("include/tournaments/display_1.php");

echo "<br /><br />";

if($tournament["per_team"]==1) {
	$n = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_players WHERE tourneyid='".$tournament["tourneyid"]."' ORDER BY ranking"));
} elseif($tournament["per_team"]>1) {
	$n = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_teams WHERE tourneyid='".$tournament["tourneyid"]."' ORDER BY ranking"));
}

$original = $n;
$n= $n-(ceil(pow(2,ceil(log($n)/log(2)))/4));

$NHPT = pow(2,ceil(log($n)/log(2)));
$NLPT = pow(2,floor(log($n-1)/log(2)));
$originalNHPT = pow(2,ceil(log($original)/log(2)));
$originalNLPT = pow(2,floor(log($original-1)/log(2)));

$rounds = log($NHPT)/log(2)+1;

if($original>($originalNHPT-(($originalNHPT-$originalNLPT)/2))&&floor($originalNHPT/8)>0) {
	$extrarounds = floor($originalNLPT/8)+1;
} elseif(floor($originalNHPT/8)>0) {
	$extrarounds = floor($originalNLPT/8);
} elseif($original>($originalNHPT-(($originalNHPT-$originalNLPT)/2))) {
	$extrarounds = 1;
} else {
	$extrarounds = 0;
}
// save winners bracket
$tempbracketDraw = $bracketDraw;

$bracketDraw = array();

$newlist = find_brackets($n);

if($tournament["ttype"]==4) {
	$temp = 1;
} else {
	$temp = 0;
}

for($i=0;$i<((log($originalNHPT)/log(2)+1+$extrarounds-$temp)*3);$i++) {
	for($j=0;$j<((2*$NHPT));$j++) {
		$bracketDraw[$j][$i] = array("",-1);
	}
}

$seeding = array(1,4,3,2);
for($j=4;$j<$n;$j*=2) {
	$temp = array();
	for($i=0;$i<sizeof($seeding);$i++) {
		if($seeding[$i]%2==0) {
			$temp[2*$i+1] = $seeding[$i];
			$temp[2*$i] = 0;
		} else {
			$temp[2*$i] = $seeding[$i];
			$temp[2*$i+1] = 0;
		}
	}
	for($i=0;$i<sizeof($temp);$i++) {
		if($temp[$i]==0) {
			if($i%8==1) $temp[$i] = (2*$j)+1 - $temp[$i-1]; // ||$i%8==3
			elseif($i%8==2||$i%8==6) $temp[$i] = $temp[$i-2] + (2*$j)/2;
			elseif($i%8==5) $temp[$i] = (2*$j)+1 - $temp[$i-1];	// ||$i%8==7
		}
	}
	$seeding = $temp;
}

$nums = array();
for($i=0;$i<floor(sizeof($seeding)/4);$i++) {
	$nums[$i] = 0;
}
for($i=0;$i<sizeof($seeding);$i++) {
	if($seeding[$i]<=$n) $nums[floor($i/4)]++;
}

$completed = array();
for($i=0;$i<floor(sizeof($seeding)/4);$i++) { 
	$completed[] = array($nums[$i],false);
}

if($original>($originalNHPT-(($originalNHPT-$originalNLPT)/2))) {
	for($i=0;$i<sizeof($completed);$i+=2) {
		$temp = $completed[$i][0];
		$completed[$i][0] = $completed[$i+1][0];
		$completed[$i+1][0] = $temp;
	}
}

for($i=1;$i<=2;$i++) { 
	$teamsperround = pow(2,$rounds-$i);

	$multiplier = $NHPT/$teamsperround;
	$top = true;
	$middle = 0;
	$servercounter = 0;
	$matchcounter = 1;

	for($j=1;$j<=$teamsperround;$j++) {
		$matchinfo = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' AND rnd='".$i."' AND mtc='".$matchcounter."' AND bracket='l'"));
		$data = $dbc->database_query("SELECT team,id FROM tournament_matches_teams WHERE matchid='".$matchinfo["id"]."' AND top='".$top."'");
		$number_of_teams = $dbc->database_num_rows($data);
		$temp = $dbc->database_fetch_assoc($data);
		if($tournament["per_team"]>1) {
			$teaminfo = $dbc->database_fetch_assoc($dbc->database_query("SELECT name,id FROM tournament_teams WHERE id='".$temp["team"]."'"));
			$holder = $teaminfo["name"];
			$holderid = $teaminfo["id"];
		} else {
			$playerinfo = $dbc->database_fetch_assoc($dbc->database_query("SELECT username,userid FROM users WHERE userid='".$temp["team"]."'"));
			$holder = $playerinfo["username"];
			$holderid = $playerinfo["userid"];
		}
		if($i==2) {
			$type = $completed[floor(($j-1)/2)][0];
			$up = $completed[floor(($j-1)/2)][1];
		} elseif($i==1) {
			$type = $completed[floor(($j-1)/4)][0];
			$up = $completed[floor(($j-1)/4)][1];
		} else {
			$type = 0;
			$up = false;
		}
		if($i!=1||($i==1&&$type==4)||($i==1&&$type==3&&((($j%4==3||$j%4==0)&&$up)||($j%4==1||$j%4==2)&&!$up))) {
			$round = 3*$i-3;
			$bracketDraw[(2*$j-1)*$multiplier-1][$round] = array($holder,0,$i,$j,$holderid,"l");
			if($i!=$rounds) {
				$query = "SELECT id FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' AND rnd='".($i+1)."' AND mtc='".($i==2&&$original<=($originalNHPT-(($originalNHPT-$originalNLPT)/2))?ceil($j/2):ceil($j/4))."' AND bracket='l'";
				$nextmatch = $dbc->database_fetch_assoc($dbc->database_query($query));
				$query = "SELECT team FROM tournament_matches_teams WHERE matchid='".$nextmatch["id"]."' AND team!='0'";
				if($i==1) $query .= " AND top='".(($j%4==1)||($j%4==2)?1:0)."'";
				$number_of_teams = $dbc->database_num_rows($dbc->database_query($query));
				$colortemp = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_teams_type WHERE id='".$tournament["teamcolors"]."'"));
				if($number_of_teams==0&&!$tournament["ffa"]) {
					if(!empty($tournament["teamcolors"]))  {
						$random = $dbc->database_fetch_assoc($dbc->database_query("SELECT RAND(".$matchinfo["id"].") as random"));
						$random = round($random["random"]);
						if(($random&&$top)||(!$random&&!$top)) {
							//$teamc = 1;
							$teamc = "&nbsp;";
							$color = $colortemp["onecolor"];
						} elseif(($random&&!$top)||(!$random&&$top)) {
							//$teamc = 2;
							$teamc = "&nbsp;";
							$color = $colortemp["twocolor"];
						}
					} else {
						$teamc = "&nbsp;";
						$color = $colors["cell_title"];
					}
					$bracketDraw[(2*$j-1)*$multiplier-1][($i-1)*3+1] = array($teamc,3,$color);
				} else {
					$currmatchteams = $dbc->database_fetch_assoc($dbc->database_query("SELECT team,score FROM tournament_matches_teams WHERE matchid='".$matchinfo["id"]."' AND top='".($top?"1":"0")."'"));
					$nextmatchteams = $dbc->database_fetch_assoc($dbc->database_query("SELECT team FROM tournament_matches_teams WHERE matchid='".$nextmatch["id"]."' AND top='".($i==2&&$original<=($originalNHPT-(($originalNHPT-$originalNLPT)/2))?1:(($j%4==1)||($j%4==2)))."'"));
					$otherteam = $dbc->database_fetch_assoc($dbc->database_query("SELECT score FROM tournament_matches_teams WHERE matchid='".$matchinfo["id"]."' AND top='".(!$top?"1":"0")."'"));
					if(isset($currmatchteams["score"])) $teamc = $currmatchteams["score"];
					elseif(isset($otherteam["score"])) $teamc = "0";
					else $teamc = "";
					if($nextmatchteams["team"]!=0) {
						if($nextmatchteams["team"]==$currmatchteams["team"]) {
							$color = $colortemp["onecolor"];
						} else {
							$color = $colortemp["twocolor"];
						}
					} else {
						$color = $colors["cell_title"];
					}
					$bracketDraw[(2*$j-1)*$multiplier-1][$round+1] = array($teamc,3,$color);
				}
				if($top) {
					$holder = "top";
					$middle = (2*$j-1)*$multiplier;
				} else {
					$holder = "bottom";
					$themiddle = (((2*$j-1)*$multiplier-2)-$middle)/2 + $middle;
					$bracketDraw[$themiddle][$round+2] = array("middle",2);
					$bracketDraw[$themiddle][$round] = array($matchinfo["server"],1,$i,$j,$matchcounter,"l");
					for($k=$middle;$k<=((2*$j-1)*$multiplier-2);$k++) {
						if($k!=$themiddle) {
							$bracketDraw[$k][$round+2] = array("straight",2);
						}
					}
					$matchcounter++;
					$servercounter++;
				}
				$bracketDraw[(2*$j-1)*$multiplier-1][$round+2] = array($holder,2);
				$top = !$top;
			}
		}
	}
}

$incrementer = 0;
if($original>($originalNHPT-(($originalNHPT-$originalNLPT)/2))) {
	$start = 3;
	$initial = 0;
} else {
	// start with h
	$i = 3;
	$teamsperround = pow(2,$rounds-$i);
	$multiplier = $NHPT/$teamsperround;
	$servercounter = 0;
	$matchcounter = 1;
	for($j=1;$j<=$teamsperround;$j++) {
		$matchinfo = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' AND rnd='".$i."' AND mtc='".$matchcounter."' AND bracket='l'"));
		$tempone = $dbc->database_fetch_assoc($dbc->database_query("SELECT team,id FROM tournament_matches_teams WHERE matchid='".$matchinfo["id"]."' AND top='1'"));
		$temptwo = $dbc->database_fetch_assoc($dbc->database_query("SELECT team,id FROM tournament_matches_teams WHERE matchid='".$matchinfo["id"]."' AND top='0'"));
		if($tournament["per_team"]>1) {
			$teaminfo = $dbc->database_fetch_assoc($dbc->database_query("SELECT name,id FROM tournament_teams WHERE id='".$tempone["team"]."'"));
			$holderone = $teaminfo["name"];
			$holderidone = $teaminfo["id"];
			$teaminfo = $dbc->database_fetch_assoc($dbc->database_query("SELECT name,id FROM tournament_teams WHERE id='".$temptwo["team"]."'"));
			$holdertwo = $teaminfo["name"];
			$holderidtwo = $teaminfo["id"];
		} else {
			$playerinfo = $dbc->database_fetch_assoc($dbc->database_query("SELECT username,userid FROM users WHERE userid='".$tempone["team"]."'"));
			$holderone = $playerinfo["username"];
			$holderidone = $playerinfo["userid"];
			$playerinfo = $dbc->database_fetch_assoc($dbc->database_query("SELECT username,userid FROM users WHERE userid='".$temptwo["team"]."'"));
			$holdertwo = $playerinfo["username"];
			$holderidtwo = $playerinfo["userid"];
		}
		$j_val = (2*$j-1)*$multiplier-1;
		//+$incrementer;
		$bracketDraw[$j_val][3*$i-3+$incrementer] = array($holderone,0,$i,($j+$matchcounter-1),$holderidone,"l");
		if($tournament["ttype"]!=4||$original!=5) $bracketDraw[$j_val+$multiplier][3*$i-3+$incrementer] = array($holdertwo,0,$i,($j+$matchcounter),$holderidtwo,"l");

		$nextmatch = $dbc->database_fetch_assoc($dbc->database_query("SELECT id FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' AND rnd='".($i+1)."' AND mtc='".ceil(($j+$matchcounter-1)/4)."' AND bracket='l'"));
		if($original==5) {
			$holder = "0";
		} else {
			$holder = (($j+$matchcounter-1)%4==1?"1":"0");
		}
		$query = "SELECT team FROM tournament_matches_teams WHERE matchid='".$nextmatch["id"]."' AND team!='0' AND top='".$holder."'";
		$number_of_teams = $dbc->database_num_rows($dbc->database_query($query));
		if($number_of_teams==0&&!$tournament["ffa"]) {
			if(!empty($tournament["teamcolors"]))  {
				$colortemp = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_teams_type WHERE id='".$tournament["teamcolors"]."'"));
				$random = $dbc->database_fetch_assoc($dbc->database_query("SELECT RAND(".$matchinfo["id"].") as random"));
				$random = round($random["random"]);
				if($random) {
					$teamc = "&nbsp;";
					$otherteamc = "&nbsp;";
					$color = $colortemp["onecolor"];
					$othercolor = $colortemp["twocolor"];
				} else {
					$teamc = "&nbsp;";
					$otherteamc = "&nbsp;";
					$color = $colortemp["twocolor"];
					$othercolor = $colortemp["onecolor"];
				}
			} else {
				$teamc = "&nbsp;";
				$otherteamc = "&nbsp;";
				$color = $colors["cell_title"];
				$othercolor = $colors["cell_title"];
			}
			if($tournament["ttype"]!=4||$original!=5) {
				$bracketDraw[$j_val][3*$i-2+$incrementer] = array($teamc,3,$color);
				$bracketDraw[$j_val+$multiplier][3*$i-2+$incrementer] = array($otherteamc,3,$othercolor);
			}
		} else {
			$currmatch = $dbc->database_fetch_assoc($dbc->database_query("SELECT id FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' AND rnd='".$i."' AND mtc='".$matchcounter."' AND bracket='l'"));
			$currmatchteam = $dbc->database_fetch_assoc($dbc->database_query("SELECT team,score FROM tournament_matches_teams WHERE matchid='".$currmatch["id"]."' AND top='1'"));
			$currmatchotherteam = $dbc->database_fetch_assoc($dbc->database_query("SELECT score FROM tournament_matches_teams WHERE matchid='".$currmatch["id"]."' AND top='0'"));
			
			if($original==5) {
				$holder = "0";
			} else {
				$holder = (($j+$matchcounter-1)%4==1?"1":"0");
			}
			$nextmatchteam = $dbc->database_fetch_assoc($dbc->database_query("SELECT team FROM tournament_matches_teams WHERE matchid='".$nextmatch["id"]."' AND top='".$holder."'"));
			
			if(isset($currmatchteam["score"])) $teamc = $currmatchteam["score"];
			elseif(isset($currmatchotherteam["score"])) $teamc = "0";
			else $teamc = "";
			if(isset($currmatchotherteam["score"])) $otherteamc = $currmatchotherteam["score"];
			elseif(isset($currmatchteams["score"])) $otherteamc = "0";
			else $otherteamc = "";
			if($nextmatchteam["team"]!=0) {
				if($nextmatchteam["team"]==$currmatchteam["team"]) {
					$color = $colortemp["onecolor"];
					$othercolor = $colortemp["twocolor"];
				} else {
					$color = $colortemp["twocolor"];
					$othercolor = $colortemp["onecolor"];
				}
			} else {
				$color = $colors["cell_title"];
				$othercolor = $colors["cell_title"];
			}
			if($tournament["ttype"]!=4||$original!=5) {
				$bracketDraw[$j_val][3*$i-2+$incrementer] = array($teamc,3,$color);
				$bracketDraw[$j_val+$multiplier][3*$i-2+$incrementer] = array($otherteamc,3,$othercolor);
			}
		}
		
		if($tournament["ttype"]!=4||$original!=5) {
			$bracketDraw[$j_val][3*$i-1+$incrementer] = array("top",2);
			$bracketDraw[$j_val+$multiplier][3*$i-1+$incrementer] = array("bottom",2);
			$themiddle = $j_val+($multiplier/2);
			$bracketDraw[$themiddle][3*$i-1+$incrementer] = array("middle",2);
			for($k=($j_val+1);$k<($j_val+$multiplier);$k++) {
				if($k!=$themiddle) $bracketDraw[$k][3*$i-1+$incrementer] = array("straight",2);
			}
			$bracketDraw[$themiddle][3*$i-3+$incrementer] = array($matchinfo["server"],1,$i,($j+$matchcounter),$matchcounter,"l");
		}
		$servercounter++;
		$matchcounter++;
	}

	$start = 4;
	$initial = 2;
}

for($i=$start;$i<($rounds+$start-3);$i++) {			
	// start with c
	if($original<=($originalNHPT-(($originalNHPT-$originalNLPT)/2))) {
		$temp = 1;
	} else {
		$temp = 0;
	}
	$teamsperround = pow(2,$rounds-$i+$temp);
	$multiplier = $NHPT/$teamsperround;
	$top = true;
	$servercounter = 0;
	$matchcounter = 1;
	$lasttop = 0;
	$lastbottom = 0;
	for($j=1;$j<=$teamsperround;$j++) {
		$matchinfo = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' AND rnd='".($i+($incrementer/3))."' AND mtc='".$matchcounter."' AND bracket='l'"));
		if($tournament["ttype"]!=4||$i!=($rounds+$start-4)||$top) $nextmatchinfo = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' AND rnd='".(($i+($incrementer/3))+1)."' AND mtc='".$matchcounter."' AND bracket='l'"));
		$temp = $dbc->database_fetch_assoc($dbc->database_query("SELECT team,id FROM tournament_matches_teams WHERE matchid='".$matchinfo["id"]."' AND top='".($top?1:0)."'"));
		if($tournament["ttype"]!=4||$i!=($rounds+$start-4)||$top) $nexttemp = $dbc->database_fetch_assoc($dbc->database_query("SELECT team,id FROM tournament_matches_teams WHERE matchid='".$nextmatchinfo["id"]."' AND top='".($top?1:0)."'"));

		if($tournament["per_team"]>1) {
			$teaminfo = $dbc->database_fetch_assoc($dbc->database_query("SELECT name,id FROM tournament_teams WHERE id='".$temp["team"]."'"));
			$holder = $teaminfo["name"];
			$holderid = $teaminfo["id"];
			if($tournament["ttype"]!=4||$i!=($rounds+$start-4)||$top) {
				$teaminfo = $dbc->database_fetch_assoc($dbc->database_query("SELECT name,id FROM tournament_teams WHERE id='".$nexttemp["team"]."'"));
				$nextholder = $teaminfo["name"];
				$nextholderid = $teaminfo["id"];
			}
		} else {
			$playerinfo = $dbc->database_fetch_assoc($dbc->database_query("SELECT username,userid FROM users WHERE userid='".$temp["team"]."'"));
			$holder = $playerinfo["username"];
			$holderid = $playerinfo["userid"];
			if($tournament["ttype"]!=4||$i!=($rounds+$start-4)||$top) {
				$playerinfo = $dbc->database_fetch_assoc($dbc->database_query("SELECT username,userid FROM users WHERE userid='".$nexttemp["team"]."'"));
				$nextholder = $playerinfo["username"];
				$nextholderid = $playerinfo["userid"];
			}
		}
		
		$j_val = (2*$j-1)*$multiplier-1+$incrementer+$initial;
		$bracketDraw[$j_val][3*$i-3+$incrementer] = array($holder,0,($i+($incrementer/3)),$j,$holderid,"l");

		$nextmatch = $dbc->database_fetch_assoc($dbc->database_query("SELECT id FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' AND rnd='".(($i+($incrementer/3))+1)."' AND mtc='".ceil($j/2)."' AND bracket='l'"));
		if($tournament["ttype"]!=4||$i!=($rounds+$start-4)||$top) $nextnextmatch = $dbc->database_fetch_assoc($dbc->database_query("SELECT id FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' AND rnd='".(($i+($incrementer/3))+2)."' AND mtc='".ceil($j/4)."' AND bracket='l'"));
		$number_of_teams = $dbc->database_num_rows($dbc->database_query("SELECT team FROM tournament_matches_teams WHERE matchid='".$nextmatch["id"]."' AND team!='0' AND top='1'"));
		if($i==($rounds+$start-4)) {
			$toptemp = 0;
		} else {
			$toptemp = (($j%4==1)||($j%4==2)?1:0);
		}
		if($tournament["ttype"]!=4||$i!=($rounds+$start-4)||$top) $nextnumber_of_teams = $dbc->database_num_rows($dbc->database_query("SELECT team FROM tournament_matches_teams WHERE matchid='".$nextnextmatch["id"]."' AND team!='0' AND top='".$toptemp."'"));
		if($number_of_teams==0&&!$tournament["ffa"]) {
			if(!empty($tournament["teamcolors"]))  {
				$colortemp = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_teams_type WHERE id='".$tournament["teamcolors"]."'"));
				$random = $dbc->database_fetch_assoc($dbc->database_query("SELECT RAND(".$matchinfo["id"].") as random"));
				$random = round($random["random"]);
				if(($random&&$top)||(!$random&&!$top)) {
					$teamc = 1;
					$color = $colortemp["onecolor"];
				} elseif(($random&&!$top)||(!$random&&$top)) {
					$teamc = 2;
					$color = $colortemp["twocolor"];
				}
			} else {
				$teamc = "&nbsp;";
				$color = $colors["cell_title"];
			}
			$bracketDraw[$j_val][3*$i-2+$incrementer] = array($teamc,3,$color);
		} else {
			$currmatch = $dbc->database_fetch_assoc($dbc->database_query("SELECT id FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' AND rnd='".($i+($incrementer/3))."' AND mtc='".$matchcounter."' AND bracket='l'"));
			$topscore = $dbc->database_fetch_assoc($dbc->database_query("SELECT MAX(score) AS score FROM tournament_matches_teams WHERE matchid='".$currmatch["id"]."'"));
			$currmatchteams = $dbc->database_fetch_assoc($dbc->database_query("SELECT team,score FROM tournament_matches_teams WHERE matchid='".$currmatch["id"]."' AND top='".($top?"1":"0")."'"));
			$nextmatchteams = $dbc->database_fetch_assoc($dbc->database_query("SELECT team FROM tournament_matches_teams WHERE matchid='".$nextmatch["id"]."' AND top='".(($j%4==1)||($j%4==2))."'"));
			$otherteam = $dbc->database_fetch_assoc($dbc->database_query("SELECT score FROM tournament_matches_teams WHERE matchid='".$currmatch["id"]."' AND top='".(!$top?"1":"0")."'"));
			if(isset($currmatchteams["score"])) $teamc = $currmatchteams["score"];
			elseif(isset($otherteam["score"])) $teamc = "0";
			else $teamc = "";
			if($nextmatchteams["team"]!=0) {
				if($nextmatchteams["team"]==$currmatchteams["team"]) {
					$color = $colors["primary"];	
				} else {
					$color = $colors["secondary"];
				}
			} else {
				$color = $colors["cell_title"];
			}
			$bracketDraw[$j_val][3*$i-2+$incrementer] = array($teamc,3,$color);
		}
		
		if($top) {
			$othertop = $j_val;
			$lasttop = (2*$j)*$multiplier-1+$incrementer+$initial;
			$bracketDraw[$j_val][3*$i-1+$incrementer] = array("top",2);
			
			$bracketDraw[$lasttop][3*$i+$incrementer] = array($nextholder,0,(($i+($incrementer/3))+1),$j,$nextholderid,"l");

			if($tournament["ttype"]!=4||$i!=($rounds+$start-4)) {
				if($nextnumber_of_teams==0&&!$tournament["ffa"]) {
					if(!empty($tournament["teamcolors"]))  {
						$colortemp = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_teams_type WHERE id='".$tournament["teamcolors"]."'"));
						$random = $dbc->database_fetch_assoc($dbc->database_query("SELECT RAND(".$nextmatchinfo["id"].") as random"));
						$random = round($random["random"]);
						if(($random&&$top)||(!$random&&!$top)) {
							$teamc = 1;
							$color = $colortemp["onecolor"];
						} elseif(($random&&!$top)||(!$random&&$top)) {
							$teamc = 2;
							$color = $colortemp["twocolor"];
						}
					} else {
						$teamc = "&nbsp;";
						$color = $colors["cell_title"];
					}
					$bracketDraw[$lasttop][3*$i+1+$incrementer] = array($teamc,3,$color);
				} else {
					$currmatch = $dbc->database_fetch_assoc($dbc->database_query("SELECT id FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' AND rnd='".(($i+($incrementer/3))+1)."' AND mtc='".$matchcounter."' AND bracket='l'"));
					$topscore = $dbc->database_fetch_assoc($dbc->database_query("SELECT MAX(score) AS score FROM tournament_matches_teams WHERE matchid='".$currmatch["id"]."'"));
					$currmatchteams = $dbc->database_fetch_assoc($dbc->database_query("SELECT team,score FROM tournament_matches_teams WHERE matchid='".$currmatch["id"]."' AND top='".($top?"1":"0")."'"));
					if($i==($rounds+$start-4)) {
						$toptemp = 0;
					} else {
						$toptemp = (($j%4==1)||($j%4==2)?1:0);
					}
					$nextmatchteams = $dbc->database_fetch_assoc($dbc->database_query("SELECT team FROM tournament_matches_teams WHERE matchid='".$nextnextmatch["id"]."' AND top='".$toptemp."'"));
					$otherteam = $dbc->database_fetch_assoc($dbc->database_query("SELECT score FROM tournament_matches_teams WHERE matchid='".$currmatch["id"]."' AND top='".(!$top?"1":"0")."'"));
					if(isset($currmatchteams["score"])) $teamc = $currmatchteams["score"];
					elseif(isset($otherteam["score"])) $teamc = "0";
					else $teamc = "";
					if($nextmatchteams["team"]!=0) {
						if($nextmatchteams["team"]==$currmatchteams["team"]) {
							$color = $colors["primary"];	
						} else {
							$color = $colors["secondary"];
						}
					} else {
						$color = $colors["cell_title"];
					}
					$bracketDraw[$lasttop][3*$i+1+$incrementer] = array($teamc,3,$color);
				}
				$bracketDraw[$lasttop][3*$i+2+$incrementer] = array("top",2);
			}	
		} else {
			$lastbottom = $j_val+2;
			$bracketDraw[$j_val][3*$i-1+$incrementer] = array("bottom",2);
			$themiddle = floor(($j_val-$othertop)/2) + $othertop;
			for($k=($othertop+1);$k<$j_val;$k++) {
				if($k!=$themiddle) $bracketDraw[$k][3*$i-1+$incrementer] = array("straight",2);
			}
			$bracketDraw[$themiddle][3*$i-1+$incrementer] = array("middle",2);
			$bracketDraw[$themiddle][3*$i-3+$incrementer] = array($matchinfo["server"],1,($i+($incrementer/3)),$j,$matchcounter,"l");
			$themiddle = $lasttop+3;

			if($tournament["ttype"]!=4||$i!=($rounds+$start-4)) {
				$bracketDraw[$lastbottom][3*$i+$incrementer] = array($nextholder,0,($i+($incrementer/3)+1),$j,$nextholderid,"l");
				
				if($nextnumber_of_teams==0&&!$tournament["ffa"]) {
					if(!empty($tournament["teamcolors"]))  {
						$colortemp = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_teams_type WHERE id='".$tournament["teamcolors"]."'"));
						$random = $dbc->database_fetch_assoc($dbc->database_query("SELECT RAND(".$nextmatchinfo["id"].") as random"));
						$random = round($random["random"]);
						if(($random&&$top)||(!$random&&!$top)) {
							$teamc = 1;
							$color = $colortemp["onecolor"];
						} elseif(($random&&!$top)||(!$random&&$top)) {
							$teamc = 2;
							$color = $colortemp["twocolor"];
						}
					} else {
						$teamc = "&nbsp;";
						$color = $colors["cell_title"];
					}
					$bracketDraw[$lastbottom][3*$i+1+$incrementer] = array($teamc,3,$color);
				} else {
					$currmatch = $dbc->database_fetch_assoc($dbc->database_query("SELECT id FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' AND rnd='".(($i+($incrementer/3))+1)."' AND mtc='".$matchcounter."' AND bracket='l'"));
					$currmatchteams = $dbc->database_fetch_assoc($dbc->database_query("SELECT team,score FROM tournament_matches_teams WHERE matchid='".$currmatch["id"]."' AND top='".($top?"1":"0")."'"));
					if($i==($rounds+$start-4)) {
						$toptemp = 0;
					} else {
						$toptemp = (($j%4==1)||($j%4==2)?1:0);
					}
					$nextmatchteams = $dbc->database_fetch_assoc($dbc->database_query("SELECT team FROM tournament_matches_teams WHERE matchid='".$nextnextmatch["id"]."' AND top='".$toptemp."'"));
					$otherteam = $dbc->database_fetch_assoc($dbc->database_query("SELECT score FROM tournament_matches_teams WHERE matchid='".$currmatch["id"]."' AND top='".(!$top?"1":"0")."'"));
					if(isset($currmatchteams["score"])) $teamc = $currmatchteams["score"];
					elseif(isset($otherteam["score"])) $teamc = "0";
					else $teamc = "";
					if($nextmatchteams["team"]==$currmatchteams["team"]) {
						$color = $colors["primary"];
					} else {
						$color = $colors["secondary"];
					}
					$bracketDraw[$lastbottom][3*$i+1+$incrementer] = array($teamc,3,$color);
				}
				for($k=($lasttop+1);$k<$lastbottom;$k++) {
					if($k!=$themiddle) $bracketDraw[$k][3*$i+2+$incrementer] = array("straight",2);
				}
				$bracketDraw[$lastbottom][3*$i+2+$incrementer] = array("bottom",2);
				$bracketDraw[$themiddle][3*$i+2+$incrementer] = array("middle",2);
				$bracketDraw[$themiddle][3*$i+$incrementer] = array($nextmatchinfo["server"],1,($i+($incrementer/3)+1),$j,$matchcounter,"l");
			}
			$matchcounter++;
			$servercounter++;
		}
		$top = !$top;
	}
	$j_holder = $teamsperround;
	$incrementer += 3;
}
if(empty($lasttop)) {
	if((sizeof($bracketDraw[0])/3 -1)==3) {
		$lasttop = 2;
	}
}
if($tournament["ttype"]!=4) {
	$max_round = $dbc->database_fetch_assoc($dbc->database_query("SELECT id,rnd FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' AND mtc='1' AND bracket='l' ORDER by rnd DESC LIMIT 1"));
	$temp = $dbc->database_fetch_assoc($dbc->database_query("SELECT team,id FROM tournament_matches_teams WHERE matchid='".$max_round["id"]."' AND top='0'"));
	if($tournament["per_team"]>1) {
		$teaminfo = $dbc->database_fetch_assoc($dbc->database_query("SELECT name,id FROM tournament_teams WHERE id='".$temp["team"]."'"));
		$holder = $teaminfo["name"];
		$holderid = $teaminfo["id"];
	} else {
		$playerinfo = $dbc->database_fetch_assoc($dbc->database_query("SELECT username,userid FROM users WHERE userid='".$temp["team"]."'"));
		$holder = $playerinfo["username"];
		$holderid = $playerinfo["userid"];
	}
}
if($tournament["ttype"]==5) {
	$losers_bracket_middle = $lasttop+3;
}
if($tournament["ttype"]!=4) $bracketDraw[$lasttop+3][3*$rounds-3+$incrementer+($start-3)*3] = array($holder,0,0,0,$holderid,"l",0,1,1);

$difference = sizeof($bracketDraw[0])-sizeof($tempbracketDraw[0])+3;
if($tournament["ttype"]==5) {
	$winners_bracket_total = sizeof($tempbracketDraw);
	$winners_bracket_round = sizeof($tempbracketDraw[0]);
 	$losers_bracket_round = sizeof($bracketDraw[0]);
}
for($i=0;$i<sizeof($tempbracketDraw);$i++) {
	for($j=0;$j<$difference;$j++) {
		if($tournament["ttype"]==5&&$i==$winners_bracket_middle) {
			if($j==0) $tempbracketDraw[$i][sizeof($tempbracketDraw[$i])-1] = array("across",2);
			$tempbracketDraw[$i][] = array("across",2);
		} else {
			$tempbracketDraw[$i][] = array("",-1);
		}
	}
}
for($i=0;$i<sizeof($bracketDraw[0])+3;$i++) {
	$temparray[] = array("blanket",2);
}
$tempbracketDraw[] = $temparray;

for($i=0;$i<sizeof($bracketDraw);$i++) {
	array_unshift($bracketDraw[$i],array("",-1));
	array_unshift($bracketDraw[$i],array("",-1));
	array_unshift($bracketDraw[$i],array("",-1));
	$tempbracketDraw[] = $bracketDraw[$i];
}
$bracketDraw = $tempbracketDraw;

if($tournament["ttype"]==4) {
	display_brackets($bracketDraw);
}
?>