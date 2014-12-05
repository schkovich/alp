<br />
<?php
include 'fnc_promotewinner.php';
$colspan = 4;
$matches = $dbc->database_query("SELECT * FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' ORDER BY rnd"); 
 ?>
<div class="centerd" style="padding: 4px 2px 4px 4px; width: 420px; border: 1px dotted <?php echo $colors['primary']; ?>">
<strong>options</strong>: 
<form action="admin_generic.php" method="POST" style="display: inline">
	<input type="hidden" name="ref" value="<?php echo basename(get_script_name()).'?id='.$tournament['tourneyid']; ?>">
	<input type="hidden" name="case" value="boiloff_<?php echo ($tournament['lockfinish'] ? 'un':''); ?>finished">
	<input type="hidden" name="tourneyid" value="<?php echo $tournament['tourneyid']; ?>">
	<input type="submit" name="submit" value="<?php echo ($tournament['lockfinish'] ? 'toggle unfinished?' : 'toggle finished?'); ?>" class="formcolors2">
</form>
</div>
<table border=0 cellpadding=4 cellspacing=0 style="width: 95%; font-size: 11px" align="center" class="centerd">
<?php
if(current_security_level()>=2||(current_security_level()>=1&&$tournament["moderatorid"]==$_COOKIE["userid"])) { ?>
	<tr><td colspan="<?php echo $colspan; ?>"><font class="sm"><br />
	&nbsp;&nbsp;&nbsp;<b>note</b>: because the number of winners of each round is determined dynamically by the administrator, winners are not automatically
	promoted from admin or user score inputs.<br />
	<br /></font></td></tr>
	<?php
}
if(!ALP_TOURNAMENT_MODE || ALP_TOURNAMENT_MODE_COMPUTER_GAMES) $tournament_game = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM games WHERE gameid='".$tournament['gameid']."'"));
while($row = $dbc->database_fetch_assoc($matches)) { ?>
	<tr bgcolor="<?php echo $colors['cell_title']; ?>">
		<td width="100%"><font color="<?php echo $colors["primary"]; ?>"><b>round <?php echo $row["rnd"]; ?></b></font> team name</td>
		<td width="80"><?php spacer(80,1,1);?></td>
		<td width="80"><div align="center">score</div><?php spacer(80,1,1);?></td>
		<td width="140"><nobr><?php
			if($dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_matches WHERE tourneyid='".$tournament['tourneyid']."' AND server>0"))) {
				echo "<font color=\"".$colors["blended_text"]."\">server ".$row["server"]."</font>";
			} else {
				echo "&nbsp;";
			}
			if(!empty($servers_arr[$row["server"]][1])) { 
				echo "&nbsp;&nbsp;";
				if(!empty($hlsw_supported_games[$tournament_game['short']])&&$toggle['hlsw']) { 
					?><a href="hlsw://<?php echo $servers_arr[$row["server"]][1]; ?>"><img src="img/little_icons/hlsw.gif" width="10" height="9" border="0" alt="connect using hlsw" align="absmiddle"></a>&nbsp;&nbsp;<a href="hlsw://<?php echo $servers_arr[$row["server"]][1]; ?>"><?php
				}
				echo $servers_arr[$row["server"]][1]; 
				if(!empty($hlsw_supported_games[$tournament_game['short']])&&$toggle['hlsw']) { ?></a><?php }
			} else {
				echo "&nbsp;";
			} ?></td>
	</tr>
	<?php 
	if((!ALP_TOURNAMENT_MODE || ALP_TOURNAMENT_MODE_COMPUTER_GAMES) && (!empty($row["map"]) || tournament_is_secure($tournament['tourneyid']))) { ?>
		<tr><td colspan="<?php echo $colspan; ?>">map: <?php echo $row["map"];
		if(tournament_is_secure($tournament['tourneyid'])) { 
			?>&nbsp;<a href="javascript:newWindow('admin_update_maplist.php?id=<?php echo $tournament["tourneyid"]; ?>','200','180','maplist')"><b>modify maps</b>.</a><?php 
		} ?></td></tr>
		<?php
	}
	if($tournament['per_team']>1) {
		$q = "SELECT tournament_matches_teams.*,tournament_teams.name FROM tournament_matches_teams LEFT JOIN tournament_teams ON tournament_teams.id=tournament_matches_teams.team WHERE tournament_matches_teams.tourneyid='".$tournament["tourneyid"]."' AND tournament_matches_teams.matchid='".$row["id"]."'";
		$data = $dbc->database_query($q);
	} else {
		$data = $dbc->database_query("SELECT tournament_matches_teams.*,users.username AS name FROM tournament_matches_teams LEFT JOIN users ON users.userid=tournament_matches_teams.team WHERE tournament_matches_teams.tourneyid='".$tournament["tourneyid"]."' AND tournament_matches_teams.matchid='".$row["id"]."'");
	}
	$counter = 0;
	while($teaminfo = $dbc->database_fetch_assoc($data)) { 
		?>
		<tr<?php echo ($counter%2==1?" bgcolor=\"".$colors['cell_alternate']."\"":''); ?>>
			<td valign="top"><?php echo ((current_security_level()>=2||(current_security_level()>=1&&$tournament["moderatorid"]==$_COOKIE["userid"]))?"<a href=\"admin_update_tournaments.php?id=".$tournament["tourneyid"]."&matchid=".$row["id"]."&w=".$teaminfo['team']."\"><b>":"").$teaminfo["name"].(current_security_level()>=2||(current_security_level()>=1&&$tournament["moderatorid"]==$_COOKIE["userid"])?"</b></a>":""); 
					if($row['rnd']!=1&&(current_security_level()>=2||(current_security_level()>=1&&$tournament["moderatorid"]==$_COOKIE["userid"]))) { 
						?>&nbsp;&nbsp;<a href="admin_update_tournaments.php?id=<?php echo $tournament["tourneyid"]; ?>&act=del&matchid=<?php echo $row["id"]; ?>&team=<?php echo $teaminfo['team']; ?>"><font color="<?php echo $colors["alert"]; ?>" style="font-size:7px"><b>X</b></font></a><?php 
					} ?></div></td>
			<td valign="top"><div align="center"><?php 
				$nextmatch = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_matches WHERE tourneyid='".$tournament['tourneyid']."' AND rnd='".($row['rnd']+1)."'"));
				if(!empty($nextmatch['id'])&&$dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_matches_teams WHERE tourneyid='".$tournament['tourneyid']."' AND matchid='".$nextmatch['id']."' AND team='".$teaminfo['team']."'"))) {
					echo "<font color=\"".$colors["primary"]."\">&lt;- <b>win</b></font>"; 
				} else {
					echo "&nbsp;";
				} ?></div></td>
			<td valign="top" class="sm"><div align="center"><b><?php 
			if(current_security_level()>=2||(current_security_level()>=1&&$tournament["moderatorid"]==$_COOKIE["userid"])) { 
				?><form action="admin_update_tournaments.php" method="POST"><input type="hidden" name="id" value="<?php echo $tournament["tourneyid"]; ?>"><input type="hidden" name="matchid" value="<?php echo $teaminfo["id"]; ?>"><img src="img/leftarrow.gif" width="5" height="8" border="0" alt="only score"><input type="text" name="onlyscore" maxlength="20" style="font-size: 9px; width: 14px;" value="<?php echo (isset($teaminfo["score"])?$teaminfo["score"]:""); ?>"><input type="submit" value="+" style="font-size: 7px"><?php
			} else {
				if(!empty($teaminfo['score'])) echo "[ ".$teaminfo["score"]." ]";
				else echo "&nbsp;";
			}
			?></b><?php
				if(tournament_is_secure($tournament['tourneyid'])) { ?></form><?php }
			 ?></div></td>
			<td valign="top"><?php
				if(tournament_is_secure($tournament['tourneyid'])) { 
					$is_scorevote_entered = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_matches_score_votes WHERE tourneyid='".$tournament["tourneyid"]."' AND matchid='".$row["id"]."'")); 
					$is_matchscore_entered = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_matches_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND matchid='".$row["id"]."' AND score IS NOT NULL")); 
					if($is_matchscore_entered) {
						$colorholder = $colors["cell_title"];
					} elseif($is_scorevote_entered) {
						$colorholder = $colors["primary"];
					} 
					if(!ALP_TOURNAMENT_MODE) { 
						?><a href="admin_disp_tournament.php?id=<?php echo $tournament['tourneyid']; ?>&rnd=<?php echo $row['rnd']; ?>"><img src="img/info.gif" width="10" height="8" border="0" alt="administrate"></a><?php spacer(3,1); ?><a href="javascript:newWindow('admin_disp_scores.php?id=<?php echo $tournament["tourneyid"]; ?>&matchid=<?php echo $row["id"]; ?>','300','180','score')"><font style="text-decoration: none;"><?php echo ($is_scorevote_entered?"<font color=\"".$colorholder."\">":""); ?><?php if(is_score_discrep($tournament['tourneyid'],$row['id'])&&all_scores_submitted($tournament['tourneyid'],$row['id'])) { echo "&lt;"; } ?><img src="img/scores.gif" width="10" height="8" border="0" alt="submitted scores"><?php if(is_score_discrep($tournament['tourneyid'],$row['id'])&&all_scores_submitted($tournament['tourneyid'],$row['id'])) { echo "&gt;"; } ?><?php echo ($is_scorevote_entered?"</font>":""); ?></font></a><?php
					}
				} elseif(current_security_level()==1&&$tournament["moderatorid"]!=$_COOKIE["userid"]) {
					if($tournament["per_team"]>1) {
						$currentteaminfo = $dbc->database_fetch_assoc($dbc->database_query("SELECT id FROM tournament_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND captainid='".$_COOKIE["userid"]."'"));
						if($teaminfo["team"]==$currentteaminfo["id"]) {
							$bool = true;
						} else {
							$bool = false;
						}
					} else {
						if($teaminfo["team"]==$_COOKIE["userid"]) {
							$bool = true;
						} else {
							$bool = false;
						}
					}
					$no_winner = no_winner($tournament['tourneyid'],$row['id']);
					if($bool && $no_winner) { 
						$is_scorevote_entered = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_matches_score_votes WHERE tourneyid='".$tournament["tourneyid"]."' AND matchid='".$row["id"]."' AND userid='".$_COOKIE["userid"]."'")); ?>
						<a href="javascript:newWindow('chng_update_scores.php?id=<?php echo $tournament["tourneyid"]; ?>&matchid=<?php echo $row["id"]; ?>','200','180','score')"><?php echo ($is_scorevote_entered?"<font color=\"".$colors["blended_text"]."\">":""); ?><b>submit score</b><?php echo ($is_scorevote_entered?"</font>":""); ?></a>
						<?php
					}
				}
				?></td>
		</tr>
		<?php
		$counter++;
	} ?>
	<tr><td colspan="<?php echo $colspan; ?>">&nbsp;<br /></td></tr>
	<?php
}
?>
</table>