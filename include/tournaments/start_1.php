<?php
// bracket creation code for single elimination tournament

function find_brackets($num)
{
	global $list;
	$list = array(); 
	divide($num);
	return $list;
}

function divide($x)
{
	global $list;
	if ($x<=4) {
		$list[] = $x;
	} else {
		divide(round($x/2));
		divide($x-round($x/2));
	}
}

$dbc->database_query("DELETE FROM tournament_matches WHERE tourneyid='".$tournament['tourneyid']."'");
$dbc->database_query("DELETE FROM tournament_matches_teams WHERE tourneyid='".$tournament['tourneyid']."'");


$teams = array();
if ($tournament['per_team'] == 1) {
	$data = $dbc->query('SELECT userid FROM tournament_players 
                        WHERE tourneyid='.(int)$valid->get_value('tourneyid').' ORDER BY ranking');
	$n = $data->numRows();
	while($temp = $data->fetchRow()) {
		$teams[] = $temp['userid'];
	}
} elseif ($tournament['per_team'] > 1) {
	$data = $dbc->query('SELECT id FROM tournament_teams 
                        WHERE tourneyid='.(int)$valid->get_value('tourneyid').' ORDER BY ranking');
	$n = $data->numRows();
	while($temp = $data->fetchRow()) {
		$teams[] = $temp['id'];
	}
}
if ($tournament['ffa'] && $tournament['rrsplit'] > 0) {
	$total = $n;
	$groups = 2*ceil($total/$tournament['rrsplit']);
	$n = $groups;
	
	$advance = array();
	$extra = array();
	// index 0 is rrsplit.
	$advance[0] = $tournament['rrsplit'];
	$extra[0] = $advance[0]-2;
	for($i=1;$i<=$valid->get_value('number_of_rounds');$i++) {
		if($valid->get_value('round_'.$i.'_advance')!="") $advance[$i] = $valid->get_value('round_'.$i.'_advance');
		else $advance[$i] = 1;
		$extra[$i] = 2*$advance[$i]-2;
	}
	$newlist = find_brackets($groups);
	$ffaseeding = array(1,4,3,2);
	for($j=4;$j<$total;$j*=2) {
		$temp = array();
		for($i=0;$i<sizeof($ffaseeding);$i++) {
			if($ffaseeding[$i]%2==0) {
				$temp[2*$i+1] = $ffaseeding[$i];
				$temp[2*$i] = 0;
			} else {
				$temp[2*$i] = $ffaseeding[$i];
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
		$ffaseeding = $temp;
	}
	for($i=0;$i<sizeof($ffaseeding);$i++) {
		if($ffaseeding[$i]>$total) $ffaseeding[$i] = 0;
	}
	$temp = array();
	foreach($ffaseeding as $val) {
		if($val!=0) $temp[] = $val;
	}
	$ffaseeding = $temp;
} else {
	$total = $n;
	$newlist = find_brackets($n);
}
$NHPT = pow(2,ceil(log($n)/log(2)));
$rounds = log($NHPT)/log(2)+1;
$NLPT = pow(2,floor(log($n-1)/log(2)));

$servers = $dbc->database_num_rows($dbc->database_query("SELECT * FROM servers WHERE tourneyid='".$valid->get_value('tourneyid')."'"));

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

			//elseif($i%8==0) $temp[$i] = $temp[$i-8] + (2*$j)/8;
			//elseif($i%8==4) $temp[$i] = $temp[$i-2] - (2*$j)/4;
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
$up = true;
for($i=0;$i<floor(sizeof($seeding)/4);$i++) { 
	$completed[] = array($nums[$i],$up);
	$up = !$up;
}

$matchid = 0;
for($i=$rounds;$i>0;$i--) { 
	$teamsperround = pow(2,$rounds-$i);

	$multiplier = $NHPT/$teamsperround;
	$top = true;
	$servercounter = 0;
	$matchcounter = 1;
	for ($j=1; $j <= $teamsperround; $j++) {
		if ($i == 2) {
			$type = $completed[floor(($j-1)/2)][0];
			$up = $completed[floor(($j-1)/2)][1];
			if ($type == 2 || ($type==3 && (($j%2==1 && $up) || ($j%2==0&&!$up)))) {
				if ($seeding[($j-1)*2] > $seeding[($j-1)*2+1]) {
					$holder = $teams[$seeding[($j-1)*2+1]-1];
				} else {
					$holder = $teams[$seeding[($j-1)*2]-1];
				}
			} else {
				$holder = '';
			}
		} elseif ($i == 1) {
			$type = $completed[floor(($j-1)/4)][0];
			$up = $completed[floor(($j-1)/4)][1];
			if ($tournament['ffa']&&$tournament['rrsplit']>0) $holder = $teams[$ffaseeding[$j-1+$extra[$i-1]*($matchcounter-1)]-1];
			else $holder = $teams[$seeding[$j-1]-1];
			
		} else {
			$type = 0;
			$up = false;
			$holder = '';
		}
		if ($i!=1||($i==1&&$type==4)||($i==1&&$type==3&&((($j%4==3||$j%4==0)&&$up)||($j%4==1||$j%4==2)&&!$up))) {
			if ($top) {
				$query = "INSERT INTO tournament_matches (tourneyid,rnd,mtc,server,bracket) VALUES ('".$tournament["tourneyid"]."','".$i."','".$matchcounter."','".($servers>0?$servercounter%$servers+1:0)."','w')";
				$dbc->database_query($query);
				$matchid = $dbc->database_insert_id();
				$query = "INSERT INTO tournament_matches_teams (tourneyid,matchid,team,top) VALUES ('".$tournament["tourneyid"]."','".$matchid."','".$holder."','1')";
				$dbc->database_query($query);
				if($tournament["ffa"]&&$tournament["rrsplit"]>0&&$i>1&&$advance[$i-1]>1) {
					for($z=1;$z<$advance[$i-1];$z++) {
						$query = "INSERT INTO tournament_matches_teams (tourneyid,matchid,team,top) VALUES ('".$tournament["tourneyid"]."','".$matchid."','0','1')";
						$dbc->database_query($query);
					}
				}
			} else {
				$query = "INSERT INTO tournament_matches_teams (tourneyid,matchid,team,top) VALUES ('".$valid->get_value("tourneyid")."','".$matchid."','".$holder."','0')";
				$dbc->database_query($query);
				if($tournament["ffa"]&&$tournament["rrsplit"]&&$extra[$i-1]>0) {
					if($i==1) {
						for($z=1;$z<=$extra[$i-1];$z++) {
							if($i==1) $holder = $teams[$ffaseeding[$j-1+$extra[$i-1]*($matchcounter-1)+$z]-1];
							$query = "INSERT INTO tournament_matches_teams (tourneyid,matchid,team,top) VALUES ('".$valid->get_value("tourneyid")."','".$matchid."','".$holder."','0')";
							$dbc->database_query($query);
						}
					} else {
						for($z=1;$z<$advance[$i-1];$z++) {
							$query = "INSERT INTO tournament_matches_teams (tourneyid,matchid,team,top) VALUES ('".$valid->get_value("tourneyid")."','".$matchid."','0','0')";
							$dbc->database_query($query);
						}
					}
				}
				$matchcounter++;
				$servercounter++;
			}
			$top = !$top;
		}
	}
}
?>