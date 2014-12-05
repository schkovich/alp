<?php
require_once 'fnc_promotewinner.php';
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
	if($x<=4) {
		$list[] = $x;
	} else {
		divide(round($x/2));
		divide($x-round($x/2));
	}
}

function display_brackets($brackets)
{ 
	global $colors,$tournament,$toggle,$hlsw_supported_games,$dbc; ?>
	<table border="0" cellpadding="0" cellspacing="0"><tr><td rowspan="<?php echo (sizeof($brackets)+2); ?>"><img src="img/pxt.gif" width="10" height="1" border="0" alt="" /><br /></td>
	<?php
	if($tournament["ttype"]==5) $temp = 2;
	else $temp = 0;
	for($i=1;$i<=((sizeof($brackets[0])+$temp)/3);$i++) { ?>
		<td colspan=3 valign="top"><img src="img/pxt.gif" width="200" height="1" border="0"><br />
			<div align="center">
			<?php 
			if($i==1) {
				if(( !ALP_TOURNAMENT_MODE || ALP_TOURNAMENT_MODE_COMPUTER_GAMES ) && tournament_is_secure($tournament['tourneyid'])) { ?>
					<form action="admin_update_maps.php" method="POST">
					<input type="hidden" name="tourneyid" value="<?php echo $tournament["tourneyid"]; ?>">
					<?php
				}
			}
			/*if($tournament["ttype"]==1) {
				echo "<font class=\"sm\"><b>".($i==(sizeof($brackets[0])/3-2)?"semifinals":"").($i==(sizeof($brackets[0])/3-1)?"finals":"")."</b><br /></font>";
			} elseif($tournament["ttype"]==4) {
			} elseif($tournament["ttype"]==5)	 {
			}*/
			if($i!=((sizeof($brackets[0])+$temp)/3)) { 
				if($tournament["ttype"]==1) {
					$bracket = "w";
				} else {
					$bracket = "l";
				}
				$map = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' AND rnd='".($tournament["ttype"]==5&&$i==((sizeof($brackets[0])+$temp)/3 -1)?"0":$i)."' AND bracket='".$bracket."'")); ?>
				<font class="sm">
				round <?php echo $i; ?><br />
				<?php if(( !ALP_TOURNAMENT_MODE || ALP_TOURNAMENT_MODE_COMPUTER_GAMES ) && tournament_is_secure($tournament['tourneyid'])) { ?>map: <input type="text" name="map_<?php echo ($tournament["ttype"]==5&&$i==((sizeof($brackets[0])+$temp)/3 -1)?"0":$i); ?>" style="width: 100px" value="<?php echo $map["map"]; ?>" style="font-size: 11px"><?php } else { echo (!empty($map["map"])?"map: ".$map["map"]:""); } ?><br />
				</font>
				<?php
			} else { ?>
				<font class="sm"><br /></font>
				<?php
				if(( !ALP_TOURNAMENT_MODE || ALP_TOURNAMENT_MODE_COMPUTER_GAMES ) && tournament_is_secure($tournament['tourneyid'])) { ?>
					<input type="submit" value="update maps" style="font-size: 11px; width: 100px; height: 16px">
					</form>
					<?php
				}
			} ?></div></td>
		<?php
	} ?>
	</tr>
	<tr><td colspan=<?php echo sizeof($brackets[0]); ?>><img src="img/pxt.gif" width="1" height="16" border="0"><br /></td></tr>
	<?php
	$gameinfo = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM games WHERE gameid='".$tournament['gameid']."'"));
	$bracketSwitch = false;
	for($i=0;$i<sizeof($brackets);$i++) {
		echo "<tr>";
		for($j=0;$j<sizeof($brackets[$i]);$j++) { 
			if($brackets[$i][$j][1]==2&&$brackets[$i][$j][0]=="blanket") $bracketSwitch = true;
			if($brackets[$i][$j][1]==0) { 
				if($brackets[$i][$j][2]==-1&&$brackets[$i][$j][3]==-1) {
					$temp_match = $dbc->database_fetch_assoc($dbc->database_query("SELECT id FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' AND bracket='l' AND rnd='0' AND mtc='0'"));
					$del_matchid = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_matches_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND matchid='".$temp_match["id"]."' AND team='".$brackets[$i][$j][4]."'")); 
				} elseif($brackets[$i][$j][2]==0&&$brackets[$i][$j][3]==0) {
					$max_matchid = $dbc->database_fetch_assoc($dbc->database_query("SELECT id FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' AND bracket='w' ORDER BY rnd DESC LIMIT 1"));
					$min_matchid = $dbc->database_fetch_assoc($dbc->database_query("SELECT id FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' AND bracket='l' ORDER BY rnd DESC LIMIT 1"));
					$del_matchid = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_matches_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND (matchid='".$min_matchid["id"]."' OR matchid='".$max_matchid["id"]."') AND team='".$brackets[$i][$j][4]."'")); 
				} else {
					$max_matchid = $dbc->database_fetch_assoc($dbc->database_query("SELECT id FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' AND rnd='".$brackets[$i][$j][2]."' AND bracket='".(!empty($brackets[$i][$j][5])?$brackets[$i][$j][5]:"w")."' ORDER BY id DESC LIMIT 1"));
					$min_matchid = $dbc->database_fetch_assoc($dbc->database_query("SELECT id FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' AND rnd='".$brackets[$i][$j][2]."' AND bracket='".(!empty($brackets[$i][$j][5])?$brackets[$i][$j][5]:"w")."' ORDER BY id LIMIT 1"));
					$del_matchid = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_matches_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND matchid>='".$min_matchid["id"]."' AND matchid<='".$max_matchid["id"]."' AND team='".$brackets[$i][$j][4]."'")); 
				}
				 ?>
				<td width="170" bgcolor="<?php echo $colors["cell_title"]; ?>"><?php
				if(!empty($brackets[$i][$j][0])) { ?><nobr><img src="img/pxt.gif" width="6" height="15" border="0" align="absmiddle" alt=""><font class="sm"><?php echo ((current_security_level()>=2||(current_security_level()>=1&&$tournament["moderatorid"]==$_COOKIE["userid"]))&&(($brackets[$i][$j][2]>2||$brackets[$i][$j][2]==0||$brackets[$i][$j][2]==-1)&&($tournament["ttype"]==1||$tournament["ttype"]==4||$tournament["ttype"]==5))?"<a href=\"admin_update_tournaments.php?id=".$tournament["tourneyid"]."&act=del&matchid=".$del_matchid["id"]."\"><font color=\"".$colors["alert"]."\" style=\"font-size:7px\"><b>X</b></font></a> ":""); ?><b><?php
					if(current_security_level()>=2||(current_security_level()>=1&&$tournament["moderatorid"]==$_COOKIE["userid"])) {
						$max_winnar_round = $dbc->database_fetch_assoc($dbc->database_query("SELECT rnd FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' AND bracket='w' ORDER BY rnd DESC LIMIT 1"));
						$max_losar_round = $dbc->database_fetch_assoc($dbc->database_query("SELECT rnd FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' AND bracket='l' ORDER BY rnd DESC LIMIT 1"));
						if(($tournament["ttype"]==1&&$brackets[$i][$j][2]==0)||($tournament["ttype"]==4&&$brackets[$i][$j][2]==0)||($tournament["ttype"]==4&&($brackets[$i][$j][2]+1)==$max_losar_round["rnd"]&&$bracketSwitch)||($tournament["ttype"]==5&&$brackets[$i][$j][2]==-1&&$brackets[$i][$j][3]==-1)) {
							// do nothing
						} else {
							echo "<a href=\"admin_update_tournaments.php?id=".$tournament["tourneyid"]."&i=".$brackets[$i][$j][2]."&j=".$brackets[$i][$j][3]."&b=".(!empty($brackets[$i][$j][5])?$brackets[$i][$j][5]:"w")."&w=".$brackets[$i][$j][4].(($tournament["ttype"]==4||$tournament["ttype"]==5)&&(!$bracketSwitch||$brackets[$i][$j][2]==0)?"&L_rnd=".$brackets[$i][$j][6]."&L_mtc=".$brackets[$i][$j][7]."&L_top=".$brackets[$i][$j][8]:"")."\">";
						}
					}
					echo (strlen($brackets[$i][$j][0])>24?substr($brackets[$i][$j][0],0,22)."...":$brackets[$i][$j][0]);
					if(current_security_level()>=2||(current_security_level()>=1&&$tournament["moderatorid"]==$_COOKIE["userid"])) {
						echo "</a>";
					}
					?></b></font><img src="img/pxt.gif" width="10" height="1" border="0"><?php 
				} 
				?></td><?php
			} elseif($brackets[$i][$j][1]==-1) { ?>
				<td><img src="img/pxt.gif" width="1" height="1" border="0"></td>
				<?php
			} elseif($brackets[$i][$j][1]==-2) { ?>
				<td><img src="img/pxt.gif" width="160" height="1" border="0"></td>
				<?php
			} elseif($brackets[$i][$j][1]==1) { 
				$servers = $dbc->database_query("SELECT * FROM servers WHERE tourneyid='".$tournament["tourneyid"]."' ORDER BY id"); ?>
				<td width="170"><div align="center"><nobr>
				<?php 
				if(current_security_level()>=2||(current_security_level()>=1&&$tournament["moderatorid"]==$_COOKIE["userid"])) { 
					$minid = $dbc->database_fetch_assoc($dbc->database_query("SELECT MIN(id) as id FROM servers WHERE tourneyid='".$tournament["tourneyid"]."'"));
					if($tournament["ttype"]==5&&$brackets[$i][$j][2]==0&&($brackets[$i][$j][3]==0||$brackets[$i][$j][3]==1)) {
						$matchid = $dbc->database_fetch_assoc($dbc->database_query("SELECT id,rnd FROM tournament_matches WHERE rnd='0' AND mtc='".$brackets[$i][$j][4]."' AND tourneyid='".$tournament["tourneyid"]."' AND bracket='l'")); 
						$winners_id = $dbc->database_fetch_assoc($dbc->database_query("SELECT id FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' AND bracket='w' ORDER BY rnd DESC LIMIT 1"));
						$losers_id = $dbc->database_fetch_assoc($dbc->database_query("SELECT id FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' AND bracket='l' ORDER BY rnd DESC LIMIT 1"));
						$topscore = $dbc->database_fetch_assoc($dbc->database_query("SELECT score FROM tournament_matches_teams WHERE matchid='".$winners_id["id"]."' AND top='1'"));
						$bottomscore = $dbc->database_fetch_assoc($dbc->database_query("SELECT score FROM tournament_matches_teams WHERE matchid='".$losers_id["id"]."' AND top='0'"));
					} else {
						$matchid = $dbc->database_fetch_assoc($dbc->database_query("SELECT id,rnd FROM tournament_matches WHERE rnd='".$brackets[$i][$j][2]."' AND mtc='".$brackets[$i][$j][4]."' AND tourneyid='".$tournament["tourneyid"]."' AND bracket='".$brackets[$i][$j][5]."'")); 
						$topscore = $dbc->database_fetch_assoc($dbc->database_query("SELECT score FROM tournament_matches_teams WHERE matchid='".$matchid["id"]."' AND top='1'"));
						$bottomscore = $dbc->database_fetch_assoc($dbc->database_query("SELECT score FROM tournament_matches_teams WHERE matchid='".$matchid["id"]."' AND top='0'"));
					}					
					if($dbc->database_num_rows($servers)||!$tournament["ffa"]) { ?><form action="admin_update_tournaments.php" method="POST"><?php } 
					$is_scorevote_entered = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_matches_score_votes WHERE tourneyid='".$tournament["tourneyid"]."' AND matchid='".$matchid["id"]."'")); 
					$is_matchscore_entered = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_matches_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND matchid='".$matchid["id"]."' AND score IS NOT NULL")); 
					?><font color="<?php echo $colors["blended_text"]; ?>" style="font-size: 9px"><?php
					if(!ALP_TOURNAMENT_MODE) {
						/*?><a href="admin_disp_tournament.php?id=<?php echo $tournament['tourneyid']; ?>&rnd=<?php echo ($matchid['rnd']==0&&isset($matchid['rnd'])?'0':($brackets[$i][$j][5]=='l'?($matchid['rnd']+1):$matchid['rnd'])); ?>"><img src="img/info.gif" width="10" height="8" border="0" alt="administrate"></a><?php spacer(3,1); */?><a href="javascript:newWindow('admin_disp_scores.php?id=<?php echo $tournament["tourneyid"]; ?>&matchid=<?php echo $matchid["id"]; ?>','300','180','score')"><?php echo ($is_matchscore_entered?"<font color=\"".$colors["cell_title"]."\">":($is_scorevote_entered?"<font color=\"".$colors["primary"]."\">":"")); ?><font style="text-decoration: none;"><?php if(is_score_discrep($tournament['tourneyid'],$matchid['id'])&&all_scores_submitted($tournament['tourneyid'],$matchid['id'])) { echo "&lt;"; } ?><img src="img/scores.gif" width="10" height="8" border="0" alt="submitted scores"><?php if(is_score_discrep($tournament['tourneyid'],$matchid['id'])&&all_scores_submitted($tournament['tourneyid'],$matchid['id'])) { echo "&lt;"; } ?></font><?php echo ($is_matchscore_entered||$is_scorevote_entered?"</font>":""); ?></a>&nbsp;&nbsp;<?php
					}	
					if($tournament["ffa"]) { ?><a href="javascript:newWindow('admin_update_scores.php?id=<?php echo $tournament["tourneyid"]; ?>&i=<?php echo $brackets[$i][$j][2]; ?>&j=<?php echo $brackets[$i][$j][3]; ?>&matchid=<?php echo $matchid["id"]; ?>','200','180','scores')">scores</a>&nbsp;<?php }
					if($dbc->database_num_rows($servers)||!$tournament["ffa"]) { ?>
						<input type="hidden" name="id" value="<?php echo $tournament["tourneyid"]; ?>"><input type="hidden" name="b" value="<?php echo $brackets[$i][$j][5]; ?>"><input type="hidden" name="i" value="<?php echo $brackets[$i][$j][2]; ?>"><input type="hidden" name="j" value="<?php echo $brackets[$i][$j][3]; ?>"><input type="hidden" name="matchid" value="<?php echo $matchid["id"]; ?>"><?php 
						if(($tournament["ttype"]==4||$tournament["ttype"]==5)&&!$bracketSwitch) { ?><input type="hidden" name="L_rnd" value="<?php echo $brackets[$i][$j][6]; ?>"><input type="hidden" name="L_mtc" value="<?php echo $brackets[$i][$j][7]; ?>"><input type="hidden" name="L_top" value="<?php echo $brackets[$i][$j][8]; ?>"><?php }
						if($dbc->database_num_rows($servers)) { 
							if(!ALP_TOURNAMENT_MODE && ALP_TOURNAMENT_MODE_COMPUTER_GAMES) {
								echo 'server';
							} else {
								echo 'location';
							}
							?><select name="server" style="font-size: 7px"><?php 
							while($temp = $dbc->database_fetch_assoc($servers)) {
								echo "<option value=\"".($temp["id"]-$minid["id"]+1)."\"".($brackets[$i][$j][0]==($temp["id"]-$minid["id"]+1)?" selected":"").">".($temp["id"]-$minid["id"]+1)."</option>";
							} ?></select><?php } ?> <?php if(!$tournament["ffa"]) { ?><img src="img/uparrow.gif" width="8" height="5" border="0" alt="up score"><input type="text" name="topscore" maxlength="20" style="font-size: 9px; width: 14px;" value="<?php echo (isset($topscore["score"])?$topscore["score"]:""); ?>"> <img src="img/downarrow.gif" width="8" height="5" border="0" alt="up score"><input type="text" name="bottomscore" maxlength="20" style="font-size: 9px; width: 14px" value="<?php echo (isset($bottomscore["score"])?$bottomscore["score"]:""); ?>"><?php } ?><input type="submit" value="+" style="font-size: 7px"><?php 
					} ?>
					</font>
					<?php if($dbc->database_num_rows($servers)||!$tournament["ffa"]) { ?></form><?php }
				} else { 
					$matchid = $dbc->database_fetch_assoc($dbc->database_query("SELECT id FROM tournament_matches WHERE rnd='".$brackets[$i][$j][2]."' AND mtc='".$brackets[$i][$j][4]."' AND tourneyid='".$tournament["tourneyid"]."' AND bracket='".$brackets[$i][$j][5]."'")); 
					if(current_security_level()>=1) {
						if($tournament["per_team"]==1) {
							$temp = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_matches_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND matchid='".$matchid["id"]."' AND team='".$_COOKIE["userid"]."'"));;
							$is_captain_of_match = $temp;
							$is_playing_in_match = $temp;
						} else {
							$teaminfo = $dbc->database_fetch_assoc($dbc->database_query("SELECT id FROM tournament_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND captainid='".$_COOKIE["userid"]."'"));
							if(!empty($teaminfo["id"])) {
								$is_captain_of_match = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_matches_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND matchid='".$matchid["id"]."' AND team='".$teaminfo["id"]."'"));
							} else {
								$is_captain_of_match = 0;
							}
							$teaminfo = $dbc->database_fetch_assoc($dbc->database_query("SELECT teamid AS id FROM tournament_players WHERE tourneyid='".$tournament["tourneyid"]."' AND userid='".$_COOKIE["userid"]."'"));
							if(!empty($teaminfo["id"])) {
								$is_playing_in_match = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_matches_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND matchid='".$matchid["id"]."' AND team='".$teaminfo["id"]."'"));
							} else {
								$is_playing_in_match = 0;
							}
						}
					} else {
						$is_captain_of_match = 0;
						$is_playing_in_match = 0;
					}
					if(!empty($brackets[$i][$j][6]) && !empty($brackets[$i][$j][7]) && !empty($brackets[$i][$j][8])) {
						$no_winner = no_winner($tournament['tourneyid'],$matchid["id"],$brackets[$i][$j][2],$brackets[$i][$j][3],$brackets[$i][$j][5],$brackets[$i][$j][6],$brackets[$i][$j][7],$brackets[$i][$j][8]);
					} else {
						$no_winner = no_winner($tournament['tourneyid'],$matchid["id"],$brackets[$i][$j][2],$brackets[$i][$j][3],$brackets[$i][$j][5]);
					}
					if($is_playing_in_match&&$brackets[$i][$j][0]!=0&&!empty($hlsw_supported_games[$gameinfo['short']])&&$toggle['hlsw']) {
						$servertemp = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM servers WHERE tourneyid='".$tournament['tourneyid']."' ORDER BY id LIMIT ".($brackets[$i][$j][0]-1).",".$brackets[$i][$j][0]));
						?><a href="hlsw://<?php echo $servertemp['ipaddress']; ?>"><img src="img/little_icons/hlsw.gif" width="10" height="9" border="0" alt="connect using hlsw" align="absmiddle"></a><?php
					}
					if($is_captain_of_match&&$no_winner) { 
						$is_scorevote_entered = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_matches_score_votes WHERE tourneyid='".$tournament["tourneyid"]."' AND matchid='".$matchid["id"]."' AND userid='".$_COOKIE["userid"]."'")); ?>
						<font class="sm"><a href="javascript:newWindow('chng_update_scores.php?id=<?php echo $tournament["tourneyid"]; ?>&matchid=<?php echo $matchid["id"]; ?>&i=<?php echo $brackets[$i][$j][2]; ?>&j=<?php echo $brackets[$i][$j][3]; ?>&b=<?php echo $brackets[$i][$j][5]; ?><?php if(!empty($brackets[$i][$j][6])&&!empty($brackets[$i][$j][7])&&!empty($brackets[$i][$j][8])) { ?>&L_rnd=<?php echo $brackets[$i][$j][6]; ?>&L_mtc=<?php echo $brackets[$i][$j][7]; ?>&L_top=<?php echo $brackets[$i][$j][8]; ?><?php } ?>','200','180','score')"><?php echo ($is_scorevote_entered?"<font color=\"".$colors["blended_text"]."\">":""); ?><b>submit score</b><?php echo ($is_scorevote_entered?"</font>":""); ?></a></font><br />
						<?php
					} else {
						if($bracketSwitch) {
							$winners_bracket_rounds = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' AND bracket='w' AND rnd='".($brackets[$i][$j][2]+1)."'"));
						}
						$losar_rounds = $dbc->database_num_rows($dbc->database_query("SELECT DISTINCT rnd FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' AND bracket='l'")); ?>
						<font color="<?php echo $colors["blended_text"]; ?>" style="font-size: 9px">match <b><?php if($brackets[$i][$j][2]==0&&$brackets[$i][$j][3]==0) { echo $losar_rounds."-1"; } else { echo ($bracketSwitch?($brackets[$i][$j][2]+1):$brackets[$i][$j][2])."-".($bracketSwitch?($brackets[$i][$j][4]+$winners_bracket_rounds):$brackets[$i][$j][4]); } ?><?php echo ($brackets[$i][$j][0]!=0?"</b> | server <b>".$brackets[$i][$j][0]:""); ?></b><br /></font>
						<?php
					}
				} ?></div></td>
				<?php
			} elseif($brackets[$i][$j][1]==2) { 
				if($brackets[$i][$j][0]=="across"||$brackets[$i][$j][0]=="straight") { ?>
					<td width="15" background="img/<?php echo $brackets[$i][$j][0]; ?>.gif"><img src="img/blank.gif" width="15" height="15" border="0"><br /></td>
					<?php
				} else { ?>
					<td width="15"><img src="img/<?php echo $brackets[$i][$j][0]; ?>.gif" width="15" height="15" border="0"><br /></td>
					<?php
				}
			} elseif($brackets[$i][$j][1]==3) { ?>
				<td width="15" height="15" bgcolor="<?php echo $brackets[$i][$j][2]; ?>"><div align="center"><font class="sm"><font color="<?php echo $colors["cell_title"]; ?>" style="font-size: 9px"><nobr>&nbsp;<b><?php echo $brackets[$i][$j][0]; ?></b>&nbsp;</font></font></div></td>
				<?php	
			} else { ?>
				<td><img src="img/pxt.gif" width="1" height="1" border="0"><br /></td>
				<?php
			} 
		}
		echo "</tr>";
	}
	echo "</table>";
}

if($tournament["per_team"]==1) {
	$n = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_players WHERE tourneyid='".$tournament["tourneyid"]."'"));
} else {
	$n = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_teams WHERE tourneyid='".$tournament["tourneyid"]."'"));
}

if($tournament["ffa"]&&$tournament["rrsplit"]>0) {
	$total = $n;
	$groups = 2*ceil($total/$tournament["rrsplit"]);
	$n = $groups;
	
	$advance = array();
	$extra = array();
	// index 0 is rrsplit.
	$advance[0] = $tournament["rrsplit"];
	$extra[0] = $advance[0]-2;
	
	$data = $dbc->database_query("SELECT DISTINCT rnd,top_x_advance FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' AND bracket='w'");
	while($row = $dbc->database_fetch_assoc($data)) {
		$advance[$row["rnd"]] = $row["top_x_advance"];
		$extra[$row["rnd"]] = 2*$advance[$row["rnd"]]-2;
	}
	$newlist = find_brackets($groups);
} else {
	$total = $n;
	$newlist = find_brackets($n);
}


$servers = $dbc->database_num_rows($dbc->database_query("SELECT * FROM servers WHERE tourneyid='".$tournament["tourneyid"]."'"));

$NHPT = pow(2,ceil(log($n)/log(2)));
$NLPT = pow(2,floor(log($n-1)/log(2)));
$rounds = log($NHPT)/log(2)+1;
$bracketDraw = array();

if($tournament["ffa"]&&$tournament["rrsplit"]>0) {
	$temp = ((2*$NHPT)-1+$extra[0]*($groups/2));
} else {
	$temp = ((2*$NHPT)-1);
}
for($i=0;$i<($rounds*3);$i++) {
	for($j=0;$j<$temp;$j++) {
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

			//elseif($i%8==0) $temp[$i] = $temp[$i-8] + (2*$j)/8;
			//elseif($i%8==4) $temp[$i] = $temp[$i-2] - (2*$j)/4;
		}
	}
	$seeding = $temp;
}
//echo ($n>($NHPT-(($NHPT-$NLPT)/2))?"1":"0");
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
if($tournament["ttype"]==4||$tournament["ttype"]==5) {
	include "include/_loser_ref.php";
	$losers_bracket_4_counter = 0;
	$first_round_counter = 0;
}

for($i=$rounds;$i>0;$i--) { 
	$teamsperround = pow(2,$rounds-$i);

	$multiplier = $NHPT/$teamsperround;
	$top = true;
	$middle = 0;
	$servercounter = 0;
	$matchcounter = 1;
	for($j=1;$j<=$teamsperround;$j++) {
		$matchinfo = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' AND rnd='".$i."' AND mtc='".$matchcounter."' AND bracket='w'"));
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
			if($tournament["ttype"]==4||$tournament["ttype"]==5) {
				if($NHPT>4) $temp = $NHPT/8;
				else $temp = 0;
				if($n>($NHPT-(($NHPT-$NLPT)/2))&&$losers_bracket[$i-1][$matchcounter-1][0]==2) {
					if($matchcounter<=($NHPT/8)) $temp = $matchcounter + $temp;
					else $temp = $matchcounter - $temp;
				} else {
					$temp = $losers_bracket[$i-1][$matchcounter-1][1];
				}
				if($n>($NHPT-(($NHPT-$NLPT)/2))&&$losers_bracket[$i-1][$matchcounter-1][0]==1) $L_rnd = $losers_bracket[$i-1][$matchcounter-1][0]+1;
				else $L_rnd = $losers_bracket[$i-1][$matchcounter-1][0];
				$L_mtc = $temp;
				$L_top = $losers_bracket[$i-1][$matchcounter-1][2];
			}
		} elseif($i==1) {
			$type = $completed[floor(($j-1)/4)][0];
			$up = $completed[floor(($j-1)/4)][1];
			if($tournament["ttype"]==4||$tournament["ttype"]==5) {
				if($n>($NHPT-(($NHPT-$NLPT)/2))) {
					$ct = (!empty($completed_temp[$matchcounter-1])?$completed_temp[$matchcounter-1]:0);
					if($ct%2==1) {
						if($type==4) {
							$temp = $losers_bracket_4_counter+1;
						} else {
							$temp = $completed_temp_deux[$first_round_counter];
						}
					} else {
						if($type==4) {
							$temp = $losers_bracket_4_counter+1;
						} else {
							$temp = (!empty($completed_temp_deux[$first_round_counter])?$completed_temp_deux[$first_round_counter]:0);
						}
					}
				} else {
					$temp = $matchcounter;
				}
				$L_rnd = (!empty($losers_bracket[$i-1][$matchcounter-1][0])?$losers_bracket[$i-1][$matchcounter-1][0]:'');
				$L_mtc = $temp;
				$L_top = (!empty($losers_bracket[$i-1][$matchcounter-1][2])?$losers_bracket[$i-1][$matchcounter-1][2]:'');
				if($type==4&&$top==0&&$j%4==0) $losers_bracket_4_counter++;
				if($type==3&&$top==0) $first_round_counter++;
			}
		} else {
			$type = 0;
			$up = false;
			if($tournament["ttype"]==4||$tournament["ttype"]==5) {
				if($i!=$rounds) {
					if($n<=($NHPT-(($NHPT-$NLPT)/2))) {
						$starting_round = 3;
					} else {
						$starting_round = 4;
					}
					$L_rnd = $starting_round + 2*($i-3);
					if((log($NHPT)/log(2))%2==1) {
						if($matchcounter%2==1) {
							if(($i+1)!=$rounds) $L_mtc = $matchcounter+1;
							else $L_mtc = $matchcounter;
						} else {
							$L_mtc = $matchcounter-1;
						}
					} else {
						$L_mtc = $matchcounter;
					}
					$L_top = 0;
				} else {
					// TODO: code for insertion of match where loser has to beat the winner twice.
				}
			}
		}
		if($i!=1||($i==1&&$type==4)||($i==1&&$type==3&&((($j%4==3||$j%4==0)&&$up)||($j%4==1||$j%4==2)&&!$up))) {
			if($tournament["ffa"]&&$tournament["rrsplit"]>0) $cumul_extra = $extra[0];
			if(!$top&&$tournament["ffa"]&&$tournament["rrsplit"]>0&&$extra[$i-1]>0) {
				if($i>1) $temp = $cumul_extra*pow(2,($i-2));
				else $temp = 0;
						
				$placement = (2*$j-1)*$multiplier-1+(($matchcounter-1)*$cumul_extra*$multiplier)+$temp;
			
				$currmatch = $dbc->database_fetch_assoc($dbc->database_query("SELECT id FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' AND rnd='".$i."' AND mtc='".$matchcounter."' AND bracket='w'"));
				$currmatchteams = $dbc->database_query("SELECT team FROM tournament_matches_teams WHERE matchid='".$currmatch["id"]."' AND top='0' ORDER BY team DESC");
				$counter = 0;
				while($row = $dbc->database_fetch_assoc($currmatchteams)) {
					if($row["team"]!=0) {
						if($tournament["per_team"]>1) $teamtemp = $dbc->database_fetch_assoc($dbc->database_query("SELECT name FROM tournament_teams WHERE id='".$row["team"]."' AND tourneyid='".$tournament["tourneyid"]."'"));
						else $teamtemp = $dbc->database_fetch_assoc($dbc->database_query("SELECT username AS name FROM users WHERE userid='".$row["team"]."'"));
						$bracketDraw[$placement+$counter][3*$i-3] = array($teamtemp["name"],0,$i,$j,$row["team"],"w");
					} else {
						$bracketDraw[$placement+$counter][3*$i-3] = array("",0,$i,$j,$row["team"],"w");
					}
					$counter++;
				}
			} elseif($top&&$i>1&&$tournament["ffa"]&&$tournament["rrsplit"]>0&&$advance[$i-1]>1) {
				$placement = (2*$j-1)*$multiplier-1+(($matchcounter-1)*$cumul_extra*$multiplier);
				
				$currmatch = $dbc->database_fetch_assoc($dbc->database_query("SELECT id FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' AND rnd='".$i."' AND mtc='".$matchcounter."' AND bracket='w'"));
				$currmatchteams = $dbc->database_query("SELECT team FROM tournament_matches_teams WHERE matchid='".$currmatch["id"]."' AND top='1' ORDER BY team DESC");
				$counter = 0;
				while($row = $dbc->database_fetch_assoc($currmatchteams)) {
					if($row["team"]!=0) {
						if($tournament["per_team"]>1) $teamtemp = $dbc->database_fetch_assoc($dbc->database_query("SELECT name FROM tournament_teams WHERE id='".$row["team"]."' AND tourneyid='".$tournament["tourneyid"]."'"));
						else $teamtemp = $dbc->database_fetch_assoc($dbc->database_query("SELECT username AS name FROM users WHERE userid='".$row["team"]."'"));
						$bracketDraw[$placement+$counter][3*$i-3] = array($teamtemp["name"],0,$i,$j,$row["team"],"w");
					} else {
						$bracketDraw[$placement+$counter][3*$i-3] = array("",0,$i,$j,$row["team"],"w");
					}
					$counter++;
				}
			} elseif($tournament["ffa"]&&$tournament["rrsplit"]>0) {
				if(!$top&&$i>1) $temp = $cumul_extra*pow(2,($i-2));
				else $temp = 0;
				$placement = (2*$j-1)*$multiplier-1+(($matchcounter-1)*$cumul_extra*$multiplier)+$temp;
				$bracketDraw[$placement][($i-1)*3] = array($holder,0,$i,$j,$holderid,"w");
			} else {
				if($i!=$rounds) {
					if($tournament["ttype"]==4||$tournament["ttype"]==5) $bracketDraw[(2*$j-1)*$multiplier-1][($i-1)*3] = array($holder,0,$i,$j,$holderid,"w",$L_rnd,$L_mtc,$L_top);
					else $bracketDraw[(2*$j-1)*$multiplier-1][($i-1)*3] = array($holder,0,$i,$j,$holderid,"w");
				} else {
					$bracketDraw[(2*$j-1)*$multiplier-1][($i-1)*3] = array($holder,0,0,0,$holderid,"l",0,1,1);
				}
				
				if($tournament["ttype"]==5&&$i==$rounds) {
					$winners_bracket_middle = (2*$j-1)*$multiplier-1;
				}
			}
			if($i!=$rounds) {
				$nextmatch = $dbc->database_fetch_assoc($dbc->database_query("SELECT id FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' AND rnd='".($i+1)."' AND mtc='".ceil($j/4)."' AND bracket='w'"));
				$number_of_teams = $dbc->database_num_rows($dbc->database_query("SELECT team FROM tournament_matches_teams WHERE matchid='".$nextmatch["id"]."' AND team!='0' AND top='".(($j%4==1)||($j%4==2))."'"));
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
					$currmatch = $dbc->database_fetch_assoc($dbc->database_query("SELECT id FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' AND rnd='".$i."' AND mtc='".$matchcounter."' AND bracket='w'"));
					$topscore = $dbc->database_fetch_assoc($dbc->database_query("SELECT MAX(score) AS score FROM tournament_matches_teams WHERE matchid='".$currmatch["id"]."'"));
					if(!$top&&$tournament["ffa"]&&$tournament["rrsplit"]>0&&$extra[$i-1]>0) {
						if($i>1) $temp = $cumul_extra*pow(2,($i-2));
						else $temp = 0;
						
						$placement = (2*$j-1)*$multiplier-1+(($matchcounter-1)*$cumul_extra*$multiplier)+$temp;
					
						$currmatchteams = $dbc->database_query("SELECT team,score FROM tournament_matches_teams WHERE matchid='".$currmatch["id"]."' AND top='0' ORDER BY team DESC");
						$counter = 0;
						$totalleftelig = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_matches_teams WHERE matchid='".$nextmatch["id"]."' AND team='0' AND top='".(($j%4==1)||($j%4==2))."'"));
						while($row = $dbc->database_fetch_assoc($currmatchteams)) {
							$winners = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_matches_teams WHERE matchid='".$nextmatch["id"]."' AND team='".$row["team"]."' AND team!=0"));
							if($winners) $color = $colors["primary"];
							elseif(!$winners&&$totalleftelig==0) $color = $colors["secondary"];
							else $color = $colors["cell_title"];
							if(isset($row["score"])) $teamc = $row["score"];
							else $teamc = "&nbsp;";
							$bracketDraw[$placement+$counter][($i-1)*3+1] = array($teamc,3,$color);
							$counter++;
						}
					} elseif($top&&$i>1&&$tournament["ffa"]&&$tournament["rrsplit"]>0&&$advance[$i-1]>1) {
						$placement = (2*$j-1)*$multiplier-1+(($matchcounter-1)*$cumul_extra*$multiplier);
						$currmatchteams = $dbc->database_query("SELECT team,score FROM tournament_matches_teams WHERE matchid='".$currmatch["id"]."' AND top='1' ORDER BY team DESC");
						$counter = 0;
						$totalleftelig = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_matches_teams WHERE matchid='".$nextmatch["id"]."' AND team='0' AND top='".(($j%4==1)||($j%4==2))."'"));
						while($row = $dbc->database_fetch_assoc($currmatchteams)) {
							$winners = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_matches_teams WHERE matchid='".$nextmatch["id"]."' AND team='".$row["team"]."' AND team!=0"));
							if($winners) $color = $colors["primary"];
							elseif(!$winners&&$totalleftelig==0) $color = $colors["secondary"];
							else $color = $colors["cell_title"];
							if(isset($row["score"])) $teamc = $row["score"];
							else $teamc = "&nbsp;";
							$bracketDraw[$placement+$counter][($i-1)*3+1] = array($teamc,3,$color);
							$counter++;
						}
					} elseif($tournament["ffa"]&&$tournament["rrsplit"]>0) {
						if(!$top&&$i>1) $temp = $cumul_extra*pow(2,($i-2));
						else $temp = 0;
						$placement = (2*$j-1)*$multiplier-1+(($matchcounter-1)*$cumul_extra*$multiplier)+$temp;
						$currmatchteams = $dbc->database_fetch_assoc($dbc->database_query("SELECT team,score FROM tournament_matches_teams WHERE matchid='".$currmatch["id"]."' AND top='".($top?"1":"0")."'"));
						$winners = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_matches_teams WHERE matchid='".$nextmatch["id"]."' AND team='".$currmatchteams["team"]."' AND team!=0"));
						$totalleftelig = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_matches_teams WHERE matchid='".$nextmatch["id"]."' AND team=0 AND top='".(($j%4==1)||($j%4==2))."'"));
						if($winners) $color = $colors["primary"];
						elseif(!$winners&&$totalleftelig==0) $color = $colors["secondary"];
						else $color = $colors["cell_title"];
						if(isset($currmatchteams["score"])) $teamc = $currmatchteams["score"];
						else $teamc = "&nbsp;";
						$bracketDraw[$placement][($i-1)*3+1] = array($teamc,3,$color);
					} else {
						$currmatchteams = $dbc->database_fetch_assoc($dbc->database_query("SELECT team,score FROM tournament_matches_teams WHERE matchid='".$currmatch["id"]."' AND top='".($top?"1":"0")."'"));
						$nextmatchteams = $dbc->database_fetch_assoc($dbc->database_query("SELECT team FROM tournament_matches_teams WHERE matchid='".$nextmatch["id"]."' AND top='".(($j%4==1)||($j%4==2))."'"));
						$otherteam = $dbc->database_fetch_assoc($dbc->database_query("SELECT score FROM tournament_matches_teams WHERE matchid='".$currmatch["id"]."' AND top='".(!$top?"1":"0")."'"));
						if(isset($currmatchteams["score"])) $teamc = $currmatchteams["score"];
						elseif(isset($otherteam["score"])) $teamc = "0";
						else $teamc = "";
						if($nextmatchteams["team"]==$currmatchteams["team"]) {
							$color = $colortemp["onecolor"];
						} else {
							$color = $colortemp["twocolor"];
						}
						$bracketDraw[(2*$j-1)*$multiplier-1][($i-1)*3+1] = array($teamc,3,$color);
					}
				}
				if($tournament["ffa"]&&$tournament["rrsplit"]>0) {
					if($top) {
						$middletop = $placement+1;
						$bracketDraw[$placement][3*$i-1] = array("top",2);
						if($i>1&&$advance[$i-1]>1) {
							for($z=1;$z<$advance[$i-1];$z++) {
								if(($placement+$z)!=($middletop+($multiplier-1))) {
									$bracketDraw[$placement+$z][3*$i-1] = array("bottom_c",2);
								} else {
									$bracketDraw[$placement+$z][3*$i-1] = array("middle_c",2);
								}
							}
						}
					} else {
						if($extra[$i-1]>0) {
							if($i==1) $temp = $extra[$i-1]+1;
							else $temp = $advance[$i-1];
							for($z=0;$z<$temp;$z++) {
								if($z<($temp-1)) $bracketDraw[$placement+$z][3*$i-1] = array("bottom_c",2);
							}
						}
						
						$middlebot = $placement-1;
						$themiddle = $middletop+($multiplier-1);
						if($bracketDraw[$themiddle][3*$i-1][0]!="middle_c") $bracketDraw[$themiddle][3*$i-1] = array("middle",2);
						if((floor(($middlebot-$middletop)/2)+$middletop)<=($middletop+floor($extra[$i-1]/2))&&$i!=1) $temp = $middlebot-floor(($middlebot-($middletop+floor($extra[$i-1]/2)))/2);
						else $temp = floor(($middlebot-$middletop)/2)+$middletop;
						$bracketDraw[$temp][3*$i-3] = array($matchinfo["server"],1,$i,$j,$matchcounter,"w");
						for($k=$middletop;$k<=$middlebot;$k++) {
							if($k!=$themiddle&&$bracketDraw[$k][3*$i-1][0]!="bottom_c") {
								$bracketDraw[$k][3*$i-1] = array("straight",2);
							}
						}
						if($extra[$i-1]>0&&$i>1) $bracketDraw[$placement+floor($extra[$i-1]/2)][3*$i-1] = array("bottom",2);
						elseif($extra[$i-1]>0&&$i==1) $bracketDraw[$placement+$extra[$i-1]][3*$i-1] = array("bottom",2);
						else $bracketDraw[$placement][3*$i-1] = array("bottom",2);
						$matchcounter++;
						$servercounter++;
					}
				} else {
					if($top) {
						$holder = "top";
						$middle = (2*$j-1)*$multiplier;
					} else {
						$holder = "bottom";
						$themiddle = (((2*$j-1)*$multiplier-2)-$middle)/2 + $middle;
						$bracketDraw[$themiddle][($i-1)*3+2] = array("middle",2);
						if($tournament["ttype"]==4||$tournament["ttype"]==5) $bracketDraw[$themiddle][($i-1)*3] = array($matchinfo["server"],1,$i,$j,$matchcounter,"w",$L_rnd,$L_mtc,$L_top);
						else $bracketDraw[$themiddle][($i-1)*3] = array($matchinfo["server"],1,$i,$j,$matchcounter,"w");
						$matchcounter++;
						$servercounter++;
						for($k=$middle;$k<=((2*$j-1)*$multiplier-2);$k++) {
							if($k!=$themiddle) {
								$bracketDraw[$k][($i-1)*3+2] = array("straight",2);
							}
						}
					}
					$bracketDraw[(2*$j-1)*$multiplier-1][($i-1)*3+2] = array($holder,2);
				}
				$top = !$top;
			}
		}
	}
}
if($tournament["ttype"]==1) display_brackets($bracketDraw);
?>
