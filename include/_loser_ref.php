<?php
// input: requires # of teams in as $n
// output: creates $losers_bracket[0] and $losers_bracket[1] which hold where the losers go (in order of matches)
//				0 is the first round, 1 is the second.

								   //losers of matches go to what (round,match,top) in losers bracket
								   //round 1
	$loser_ref[4]	       = array(
									array(1=>array(1,1,1),array(1,1,0)),
									array(1=>array(2,1,0)));
	$loser_ref[3][2]	   = array(
									array(1=>array(1,1,1)),
									array(1=>array(2,1,0),array(1,1,0)));
	$loser_ref[3][3]	   = array(
									array(1=>array(1,1,1),array(1,2,1)),
									array(1=>array(1,2,0),array(1,1,0)));
	$loser_ref[3][4]	   = array(
									array(1=>array(2,2,1),array(1,1,1),array(1,1,0)),
									array(1=>array(2,1,0),array(2,2,0)));
	$loser_ref[4][4]	   = array(
									array(1=>array(1,1,1),array(1,1,0),array(1,2,1),array(1,2,0)),
									array(1=>array(2,1,0),array(2,2,0)));
	$loser_ref[2][2][2][2] = array(
									array(),
									array(1=>array(2,2,1),array(2,2,0),array(2,1,1),array(2,1,0)));
	$loser_ref[2][2][2][3] = array(
									array(1=>array(1,1,1)),
									array(1=>array(1,1,0),array(2,2,0),array(2,1,1),array(2,1,0)));
	$loser_ref[3][2][2][2] = array(
									array(1=>array(1,1,1)),
									array(1=>array(2,2,1),array(2,2,0),array(1,1,0),array(2,1,0)));
	$loser_ref[2][3][2][3] = array(	
									array(1=>array(1,1,1),array(1,2,1)),
									array(1=>array(1,2,0),array(2,2,0),array(1,1,0),array(2,1,0)));
	$loser_ref[3][2][2][3] = array(
									array(1=>array(1,1,1),array(1,2,1)),
									array(1=>array(2,2,0),array(1,2,0),array(1,1,0),array(2,1,0)));
	$loser_ref[3][2][3][2] = array(
									array(1=>array(1,1,1),array(1,2,1)),
									array(1=>array(2,2,0),array(1,2,0),array(2,1,0),array(1,1,0)));
	$loser_ref[3][2][3][3] = array(
									array(1=>array(1,1,1),array(1,2,1),array(1,3,1)),
									array(1=>array(1,2,0),array(1,3,0),array(1,1,0),array(2,1,0)));
	$loser_ref[3][3][2][3] = array(
									array(1=>array(1,1,1),array(1,2,1),array(1,3,1)),
									array(1=>array(1,3,0),array(2,2,0),array(1,1,0),array(1,2,0)));
	$loser_ref[3][3][3][3] = array(
									array(1=>array(1,1,1),array(1,2,1),array(1,3,1),array(1,4,1)),
									array(1=>array(1,3,0),array(1,4,0),array(1,1,0),array(1,2,0)));
	$loser_ref["3333bug"] = array(
									array(1=>array(2,1,1),array(2,2,1),array(2,3,1),array(2,4,1)),
									array(1=>array(2,3,0),array(2,4,0),array(2,1,0),array(2,2,0)));
	$loser_ref[3][4][3][3] = array(
									array(1=>array(2,1,1),array(1,1,1),array(1,1,0),array(2,3,1),array(2,4,1)),
									array(1=>array(2,3,0),array(2,4,0),array(2,1,0),array(2,2,0)));
	$loser_ref[3][3][4][3] = array(
									array(1=>array(2,1,1),array(2,2,1),array(1,1,1),array(1,1,0),array(2,4,1)),
									array(1=>array(2,3,0),array(2,4,0),array(2,1,0),array(2,2,0)));
	$loser_ref[3][4][4][3] = array(
									array(1=>array(2,1,1),array(1,1,1),array(1,1,0),array(1,2,1),array(1,2,0),array(2,4,1)),
									array(1=>array(2,3,0),array(2,4,0),array(2,1,0),array(2,2,0)));
	$loser_ref[3][4][3][4] = array(
									array(1=>array(2,1,1),array(1,1,1),array(1,1,0),array(2,3,1),array(1,2,1),array(1,2,0)),
									array(1=>array(2,3,0),array(2,4,0),array(2,1,0),array(2,2,0)));
	$loser_ref[4][3][4][3] = array(
									array(1=>array(1,1,1),array(1,1,0),array(2,2,1),array(1,2,1),array(1,2,0),array(2,4,1)),
									array(1=>array(2,3,0),array(2,4,0),array(2,1,0),array(2,2,0)));
	$loser_ref[3][4][4][4] = array(
									array(1=>array(2,1,1),array(1,1,1),array(1,1,0),array(1,2,1),array(1,2,0),array(1,3,1),array(1,3,0)),
									array(1=>array(2,3,0),array(2,4,0),array(2,1,0),array(2,2,0)));
	$loser_ref[4][4][4][3] = array(
									array(1=>array(1,1,1),array(1,1,0),array(1,2,1),array(1,2,0),array(1,3,1),array(1,3,0),array(2,4,1)),
									array(1=>array(2,3,0),array(2,4,0),array(2,1,0),array(2,2,0)));
	$loser_ref[4][4][4][4] = array(	
									array(1=>array(1,1,1),array(1,1,0),array(1,2,1),array(1,2,0),array(1,3,1),array(1,3,0),array(1,4,1),array(1,4,0)),
									array(1=>array(2,3,0),array(2,4,0),array(2,1,0),array(2,2,0)));

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

function is_power_of_two($x) {
	if(((log($x)/log(2))-floor(log($x)/log(2)))==0) {
		return true;
	} else {
		return false;
	}
}
function power_of_two($x) {
	if(((log($x)/log(2))-floor(log($x)/log(2)))==0) {
		return log($x)/log(2);
	} else {
		return false;
	}
}

if($n>=25) {
	$temp = (floor($n/25)*25);
	if($n>=50) $othertemp = (2*power_of_two($temp/25)-1);
	else $othertemp = 0;
	if(is_power_of_two($temp/25)&&$n>=$temp&&$n<=($temp+$othertemp)) {
		$buggie = true;
	} else {
		$buggie = false;
	}
} else {
	$buggie = false;
}

//f= first, s= second
//h= half, r= round
$fh_fr = array();
$fh_sr = array();
$sh_fr = array();
$sh_sr = array();
$half = (sizeof($completed)/2);
for($i=0;$i<$half;$i+=2) {
	//$completed[$i][0]
	//$completed[$i+1][0]
	//$completed[$i+$half][0]
	//$completed[$i+$half+1][0]
	if(sizeof($completed)>2) {
		$ref = $loser_ref[$completed[$i][0]][$completed[$i+1][0]][$completed[$i+$half][0]][$completed[$i+$half+1][0]];
		if(!empty($ref)) {
			if($buggie&&$completed[$i][0]==3&&$completed[$i+1][0]==3&&$completed[$i+$half][0]==3&&$completed[$i+$half+1][0]==3) {
				$ref = $loser_ref["3333bug"];
			}
			$fin = $completed[$i][0]-2 + $completed[$i+1][0]-2;
			for($j=1;$j<=$fin;$j++) {
				$fh_fr[] = $ref[0][$j];
			}
			$st = $fin+1;
			$fin = sizeof($ref[0]);
			for($j=$st;$j<=$fin;$j++) {
				$sh_fr[] = $ref[0][$j];
			}
			for($j=1;$j<=2;$j++) {
				$fh_sr[] = $ref[1][$j];
			}
			for($j=3;$j<=4;$j++) {
				$sh_sr[] = $ref[1][$j];
			}
		}
	} elseif(sizeof($completed)>1) {
		$ref = $loser_ref[$completed[$i][0]][$completed[$i+1][0]];
		if(!empty($ref)) {
			$fin = $completed[$i][0]-2;
			for($j=1;$j<=$fin;$j++) {
				$fh_fr[] = $ref[0][$j];
			}
			$st = $fin+1;
			$fin = sizeof($ref[0]);
			for($j=$st;$j<=$fin;$j++) {
				$sh_fr[] = $ref[0][$j];
			}
			$fh_sr[] = $ref[1][1];
			$sh_sr[] = $ref[1][2];
		}
	} elseif(sizeof($completed)==1) {
		$ref = $loser_ref[4];
		if(!empty($ref)) {
			$fh_fr[] = $ref[0][1];
			$sh_fr[] = $ref[0][2];
			$fh_sr[] = $ref[1][1];
			$sh_sr[] = array();
		}
	}
}
$losers_bracket = array();

$completed_temp = array();
$tempcounter = 1;
for($i=0;$i<sizeof($completed);$i++) {
	if($completed[$i][0]==3||$completed[$i][0]==4) {
		if($i%2==0&&$i!=0) $tempcounter++;
		$completed_temp[] = $tempcounter;
		if($completed[$i][0]==4) $completed_temp[] = $tempcounter;
	}
}
$completed_temp_deux = array();
$counter = 1;
foreach($completed as $val) {
	if($val[0]==3) {
		$completed_temp_deux[] = $counter;
		$counter++;
	} elseif($val[0]==4) {
		$counter++;
	}
}
for($i=0;$i<sizeof($fh_fr);$i++) {
	$losers_bracket[0][] = $fh_fr[$i];
}
for($i=0;$i<sizeof($sh_fr);$i++) {
	$losers_bracket[0][] = $sh_fr[$i];
}
$one_counter = 0;
$two_counter = ($NHPT/16)-1;
$other_counter = 1;
for($i=0;$i<sizeof($sh_sr);$i++) {
	if($sh_sr[$i][0]==1) {
		$sh_sr[$i][1] = $other_counter;
		$other_counter++;
	} elseif($sh_sr[$i][0]==2&&$n>16) {
		if($sh_sr[$i][1]%2==1) $sh_sr[$i][1] += $one_counter;
		elseif($sh_sr[$i][1]%2==0) $sh_sr[$i][1] += $two_counter;
	}
	if($i%2==1) {
		$one_counter++;
		$two_counter++;
	}
}
$one_counter = 0;
$two_counter = ($NHPT/16)-1;
for($i=0;$i<sizeof($fh_sr);$i++) {
	if($fh_sr[$i][0]==1) {
		$fh_sr[$i][1] = $other_counter;
		$other_counter++;
	} elseif($fh_sr[$i][0]==2&&$n>16) {
		if($fh_sr[$i][1]%2==1) $fh_sr[$i][1] += $one_counter;
		elseif($fh_sr[$i][1]%2==0) $fh_sr[$i][1] += $two_counter;
	}
	if($i%2==1) {
		$one_counter++;
		$two_counter++;
	}
}

for($i=0;$i<sizeof($fh_sr);$i++) {
	$losers_bracket[1][] = $fh_sr[$i];
}
for($i=0;$i<sizeof($sh_sr);$i++) {
	$losers_bracket[1][] = $sh_sr[$i];
} ?>