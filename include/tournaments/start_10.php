<?php
// bracket creation code for round robin tournament

$dbc->database_query("DELETE FROM tournament_matches WHERE tourneyid='".$tournament['tourneyid']."'");
$dbc->database_query("DELETE FROM tournament_matches_teams WHERE tourneyid='".$tournament['tourneyid']."'");

$teams = array();
$counter = 1;
if($tournament["per_team"]==1) {
	$data = $dbc->database_query("SELECT userid FROM tournament_players WHERE tourneyid='".$valid->get_value("tourneyid")."' ORDER BY ranking");
	$n = $dbc->database_num_rows($data);
	while($temp = $dbc->database_fetch_assoc($data)) {
		$teams[$counter] = $temp["userid"];
		$counter++;
	}
} elseif($tournament["per_team"]>1) {
	$data = $dbc->database_query("SELECT id FROM tournament_teams WHERE tourneyid='".$valid->get_value("tourneyid")."' ORDER BY ranking");
	$n = $dbc->database_num_rows($data);
	while($temp = $dbc->database_fetch_assoc($data)) {
		$teams[$counter] = $temp["id"];
		$counter++;
	}
}

$servers = $dbc->database_num_rows($dbc->database_query("SELECT * FROM servers WHERE tourneyid='".$valid->get_value("tourneyid")."'"));

// $i = round
if($n%2==0) {
	$end_round = $n-1;
	$modifier = 0;
} else {
	$end_round = $n;
	$modifier = 1;
}
$allgood = true;
$max_team = 0;
$servercounter = 0;
for($i=1;$i<=$end_round;$i++) {
	$matchcounter = 1;

	$sum = $n-$i+2;
	if($n%2==0) {
		$evens = array();
		for($j=1;$j<=ceil($sum/2);$j++) {
			if($j==1) $max_team = $sum-$j+$modifier;
			if($i%2==1&&(($sum-$j+$modifier)>$n||$j>=($sum-$j+$modifier))) {
				// do nothing
			} else {
				if(($sum-$j+$modifier)>$n||$j>=($sum-$j+$modifier)) {
					$evens[] = $j;
				} else {
					$query = "INSERT INTO tournament_matches (tourneyid,rnd,mtc,server) VALUES ('".$valid->get_value("tourneyid")."','".$i."','".$matchcounter."','".($servers>0?$servercounter%$servers+1:0)."')";
					if(!$dbc->database_query($query)) $allgood = false;
					$matchid = $dbc->database_insert_id();
					$query = "INSERT INTO tournament_matches_teams (tourneyid,matchid,team,top) VALUES ('".$valid->get_value("tourneyid")."','".$matchid."','".$teams[$j]."','1')";
					if(!$dbc->database_query($query)) $allgood = false;
					$query = "INSERT INTO tournament_matches_teams (tourneyid,matchid,team,top) VALUES ('".$valid->get_value("tourneyid")."','".$matchid."','".(($sum-$j+$modifier)>$n||$j>=($sum-$j+$modifier)?0:$teams[$sum-$j+$modifier])."','0')";
					if(!$dbc->database_query($query)) $allgood = false;
					$matchcounter++;
					$servercounter++;
				}
			}
		}		
		if($max_team<$n) {
			if(($max_team+1)==$n) {
				$evens[] = $max_team+1;
			} else {
				$inner_sum = $max_team+1+$n;
				for($j=$max_team+1;$j<$n;$j++) {
					if($j<=($inner_sum-$j)) {
						if($i%2==0) {
							$evens[] = ($inner_sum-$j);
							$evens[] = $j;
						} else {
							$evens[] = ($inner_sum-$j);
							$evens[] = $j;
						}
					}
				}
			}
		}
		if($i%2==0) {
			$temp = 0;
		} else {
			$temp = 1;
		}
		for($k=0;$k<sizeof($evens)-1;$k+=2) {
			if($k+$temp+1>sizeof($evens)-1) $inner_temp = 0;
			else $inner_temp = $k+$temp+1;
			$query = "INSERT INTO tournament_matches (tourneyid,rnd,mtc,server) VALUES ('".$valid->get_value("tourneyid")."','".$i."','".$matchcounter."','".($servers>0?$servercounter%$servers+1:0)."')";
			if(!$dbc->database_query($query)) $allgood = false;
			$matchid = $dbc->database_insert_id();
			$query = "INSERT INTO tournament_matches_teams (tourneyid,matchid,team,top) VALUES ('".$valid->get_value("tourneyid")."','".$matchid."','".$teams[$evens[$k+$temp]]."','1')";
			if(!$dbc->database_query($query)) $allgood = false;
			$query = "INSERT INTO tournament_matches_teams (tourneyid,matchid,team,top) VALUES ('".$valid->get_value("tourneyid")."','".$matchid."','".$teams[$evens[$inner_temp]]."','0')";
			if(!$dbc->database_query($query)) $allgood = false;
			$matchcounter++;
			$servercounter++;
		}
	} else {
		for($j=1;$j<=ceil($sum/2);$j++) {
			if($j==1) $max_team = $sum-$j+$modifier;
			$query = "INSERT INTO tournament_matches (tourneyid,rnd,mtc,server) VALUES ('".$valid->get_value("tourneyid")."','".$i."','".$matchcounter."','".($servers>0?$servercounter%$servers+1:0)."')";
			if(!$dbc->database_query($query)) $allgood = false;
			$matchid = $dbc->database_insert_id();
			$query = "INSERT INTO tournament_matches_teams (tourneyid,matchid,team,top) VALUES ('".$valid->get_value("tourneyid")."','".$matchid."','".$teams[$j]."','1')";
			if(!$dbc->database_query($query)) $allgood = false;
			$query = "INSERT INTO tournament_matches_teams (tourneyid,matchid,team,top) VALUES ('".$valid->get_value("tourneyid")."','".$matchid."','".(($sum-$j+$modifier)>$n||$j>=($sum-$j+$modifier)?0:$teams[$sum-$j+$modifier])."','0')";
			if(!$dbc->database_query($query)) $allgood = false;
			$matchcounter++;
			$servercounter++;
		}
		if($max_team<$n) {
			if(($max_team+1)==$n) {
				$query = "INSERT INTO tournament_matches (tourneyid,rnd,mtc,server) VALUES ('".$valid->get_value("tourneyid")."','".$i."','".$matchcounter."','".($servers>0?$servercounter%$servers+1:0)."')";
				if(!$dbc->database_query($query)) $allgood = false;
				$matchid = $dbc->database_insert_id();
				$query = "INSERT INTO tournament_matches_teams (tourneyid,matchid,team,top) VALUES ('".$valid->get_value("tourneyid")."','".$matchid."','".$teams[$max_team+1]."','1')";
				if(!$dbc->database_query($query)) $allgood = false;
				$query = "INSERT INTO tournament_matches_teams (tourneyid,matchid,team,top) VALUES ('".$valid->get_value("tourneyid")."','".$matchid."','0','0')";
				if(!$dbc->database_query($query)) $allgood = false;
				$matchcounter++;
				$servercounter++;
			} else {
				$inner_sum = $max_team+1+$n;
				for($j=$max_team+1;$j<$n;$j++) {
					if($j<=($inner_sum-$j)) {
						$query = "INSERT INTO tournament_matches (tourneyid,rnd,mtc,server) VALUES ('".$valid->get_value("tourneyid")."','".$i."','".$matchcounter."','".($servers>0?$servercounter%$servers+1:0)."')";
						if(!$dbc->database_query($query)) $allgood = false;
						$matchid = $dbc->database_insert_id();
						$query = "INSERT INTO tournament_matches_teams (tourneyid,matchid,team,top) VALUES ('".$valid->get_value("tourneyid")."','".$matchid."','".$teams[$j]."','1')";
						if(!$dbc->database_query($query)) $allgood = false;
						$query = "INSERT INTO tournament_matches_teams (tourneyid,matchid,team,top) VALUES ('".$valid->get_value("tourneyid")."','".$matchid."','".($j==($inner_sum-$j)?0:$teams[$inner_sum-$j])."','0')";
						if(!$dbc->database_query($query)) $allgood = false;
						$matchcounter++;
						$servercounter++;
					}
				}
			}
		}
	}
}
?>
