<?php
// bracket creation code for consolation tournament
include_once("include/tournaments/start_1.php");

if($tournament["per_team"]==1) {
	$n = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_players WHERE tourneyid='".$valid->get_value("tourneyid")."'"));
} elseif($tournament["per_team"]>1) {
	$n = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_teams WHERE tourneyid='".$valid->get_value("tourneyid")."'"));
}

$original = $n;
$n = $n-(ceil(pow(2,ceil(log($n)/log(2)))/4));

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

$newlist = find_brackets($n);

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
			if($i%8==1) $temp[$i] = (2*$j)+1 - $temp[$i-1];
			elseif($i%8==2||$i%8==6) $temp[$i] = $temp[$i-2] + (2*$j)/2;
			elseif($i%8==5) $temp[$i] = (2*$j)+1 - $temp[$i-1];
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
			if($top) {
				$query = "INSERT INTO tournament_matches (tourneyid,rnd,mtc,server,bracket) VALUES ('".$tournament["tourneyid"]."','".$i."','".$matchcounter."','".($servers>0?$servercounter%$servers+1:0)."','l')";
				$dbc->database_query($query);
				$matchid = $dbc->database_insert_id();
				$query = "INSERT INTO tournament_matches_teams (tourneyid,matchid,team,top) VALUES ('".$tournament["tourneyid"]."','".$matchid."','0','1')";
				$dbc->database_query($query);
			} else {
				$query = "INSERT INTO tournament_matches_teams (tourneyid,matchid,team,top) VALUES ('".$tournament["tourneyid"]."','".$matchid."','0','0')";
				$dbc->database_query($query);
				$matchcounter++;
				$servercounter++;
			}
			$top = !$top;
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
	$top = true;
	$servercounter = 0;
	$matchcounter = 1;
	for($j=1;$j<=$teamsperround;$j++) {
		$query = "INSERT INTO tournament_matches (tourneyid,rnd,mtc,server,bracket) VALUES ('".$tournament["tourneyid"]."','".$i."','".$matchcounter."','".($servers>0?$servercounter%$servers+1:0)."','l')";
		$dbc->database_query($query);
		$matchid = $dbc->database_insert_id();
		$query = "INSERT INTO tournament_matches_teams (tourneyid,matchid,team,top) VALUES ('".$tournament["tourneyid"]."','".$matchid."','0','1')";
		$dbc->database_query($query);
		$query = "INSERT INTO tournament_matches_teams (tourneyid,matchid,team,top) VALUES ('".$tournament["tourneyid"]."','".$matchid."','0','0')";
		$dbc->database_query($query);

		$servercounter++;
		$matchcounter++;
		$top = !$top;
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
		if($top) {
			$query = "INSERT INTO tournament_matches (tourneyid,rnd,mtc,server,bracket) VALUES ('".$tournament["tourneyid"]."','".($i+$incrementer)."','".$matchcounter."','".($servers>0?$servercounter%$servers+1:0)."','l')";
			$dbc->database_query($query);
			$firstmatchid = $dbc->database_insert_id();
			$query = "INSERT INTO tournament_matches_teams (tourneyid,matchid,team,top) VALUES ('".$tournament["tourneyid"]."','".$firstmatchid."','0','1')";
			$dbc->database_query($query);

			$query = "INSERT INTO tournament_matches (tourneyid,rnd,mtc,server,bracket) VALUES ('".$tournament["tourneyid"]."','".($i+1+$incrementer)."','".$matchcounter."','".($servers>0?$servercounter%$servers+1:0)."','l')";
			$dbc->database_query($query);
			$secondmatchid = $dbc->database_insert_id();
			$query = "INSERT INTO tournament_matches_teams (tourneyid,matchid,team,top) VALUES ('".$tournament["tourneyid"]."','".$secondmatchid."','0','1')";
			$dbc->database_query($query);
		} else {
			$query = "INSERT INTO tournament_matches_teams (tourneyid,matchid,team,top) VALUES ('".$valid->get_value("tourneyid")."','".$firstmatchid."','0','0')";
			$dbc->database_query($query);
			$query = "INSERT INTO tournament_matches_teams (tourneyid,matchid,team,top) VALUES ('".$valid->get_value("tourneyid")."','".$secondmatchid."','0','0')";
			$dbc->database_query($query);
			$matchcounter++;
			$servercounter++;
		}
		$top = !$top;
	}
	$incrementer += 1;
}
$servercounter = 0;
$query = "INSERT INTO tournament_matches (tourneyid,rnd,mtc,server,bracket) VALUES ('".$tournament["tourneyid"]."','".($rounds+$start+$incrementer-3)."','1','".($servers>0?$servercounter%$servers+1:0)."','l')";
$dbc->database_query($query);
$matchid = $dbc->database_insert_id();
$query = "INSERT INTO tournament_matches_teams (tourneyid,matchid,team,top) VALUES ('".$tournament["tourneyid"]."','".$matchid."','0','0')";
$dbc->database_query($query);
?>