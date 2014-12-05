<br />
<?php
include 'fnc_promotewinner.php';
$colspan = 7;
$matches = $dbc->database_query("SELECT DISTINCT rnd,map FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' ORDER BY rnd"); ?>
<table border=0 cellpadding=4 cellspacing=0 style="width: 95%; font-size: 11px" align="center" class="centerd">
<?php
$tournament_game = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM games WHERE gameid='".$tournament['gameid']."'"));
$dbc->database_data_seek($matches,0);
while($row = $dbc->database_fetch_assoc($matches)) { ?>
	<tr bgcolor="<?php echo $colors["cell_title"]; ?>">
		<td width="80"><font color="<?php echo $colors["primary"]; ?>"><b>round <?php echo $row["rnd"]; ?></b></font></td>
		<td><div align="right"><b>team name</b></div></td>
		<td width="10">&nbsp;</td>
		<td width="60"><div align="center">score</div></td>
		<td width="80">&nbsp;</td>
		<td width="60"><div align="center">score</div></td>
		<td width="10">&nbsp;</td>
		<td><b>team name</b></td>
		<td width="30">&nbsp;</td>
		<td width="80"><?php if(!empty($servers_arr)) { ?><b><?php echo (!ALP_TOURNAMENT_MODE || ALP_TOURNAMENT_MODE_COMPUTER_GAMES?'server':'location'); ?></b><?php } else { ?>&nbsp;<?php } ?></td>
		<td>&nbsp;</td>
	</tr>
	<?php 
	if((!ALP_TOURNAMENT_MODE || ALP_TOURNAMENT_MODE_COMPUTER_GAMES) && (!empty($row["map"]) || tournament_is_secure($tournament['tourneyid']))) { ?>
		<tr><td colspan="<?php echo $colspan; ?>">map: <?php echo $row["map"];
		if(tournament_is_secure($tournament['tourneyid'])) { 
			?>&nbsp;<a href="javascript:newWindow('admin_update_maplist.php?id=<?php echo $tournament["tourneyid"]; ?>','200','180','maplist')"><b>modify maps</b>.</a><?php 
		} ?></td></tr>
		<?php
	}
	$data = $dbc->database_query("SELECT * FROM tournament_matches WHERE tourneyid='".$tournament["tourneyid"]."' AND rnd='".$row["rnd"]."' ORDER BY mtc");
	while($round = $dbc->database_fetch_assoc($data)) { 
		$top_info = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_matches_teams WHERE matchid='".$round["id"]."' AND top='1'"));
		$bottom_info = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_matches_teams WHERE matchid='".$round["id"]."' AND top='0'"));
		if($tournament["per_team"]>1) {
			$top_team = $dbc->database_fetch_assoc($dbc->database_query("SELECT name FROM tournament_teams WHERE id='".$top_info["team"]."'"));
			$bottom_team = $dbc->database_fetch_assoc($dbc->database_query("SELECT name FROM tournament_teams WHERE id='".$bottom_info["team"]."'"));
		} else {
			$top_team = $dbc->database_fetch_assoc($dbc->database_query("SELECT username AS name FROM users WHERE userid='".$top_info["team"]."'"));
			$bottom_team = $dbc->database_fetch_assoc($dbc->database_query("SELECT username AS name FROM users WHERE userid='".$bottom_info["team"]."'"));
		}
		if(!empty($tournament["teamcolors"])&&!isset($top_info["score"])&&!isset($bottom_info["score"]))  {
			$colortemp = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournament_teams_type WHERE id='".$tournament["teamcolors"]."'"));
			$random = $dbc->database_fetch_assoc($dbc->database_query("SELECT RAND(".$round["id"].") as random"));
			$random = round($random["random"]);
			if($random) {
				$teamc_one = 1;
				$colorone = $colortemp["onecolor"];
				$teamc_two = 2;
				$colortwo = $colortemp["twocolor"];
			} elseif(!$random) {
				$teamc_one = 2;
				$colorone = $colortemp["twocolor"];
				$teamc_two = 1;
				$colortwo = $colortemp["onecolor"];
			}
		}
		?>
		<tr>
			<td><b>match <?php echo $row["rnd"]."-".$round["mtc"]; ?></b></td>
			<td><div align="right"><?php echo (!empty($top_team["name"])?((current_security_level()>=2||(current_security_level()>=1&&$tournament["moderatorid"]==$_COOKIE["userid"]))&&!empty($bottom_team["name"])?"<a href=\"admin_update_tournaments.php?id=".$tournament["tourneyid"]."&matchid=".$round["id"]."&w=".$top_info["team"]."\"><b>":"").$top_team["name"].(current_security_level()>=2||(current_security_level()>=1&&$tournament["moderatorid"]==$_COOKIE["userid"])?"</b></a>":""):"<font color=\"".$colors["blended_text"]."\"><b>BYE</b></font>"); ?><?php if((current_security_level()>=2||(current_security_level()>=1&&$tournament["moderatorid"]==$_COOKIE["userid"]))&&!empty($top_team["name"])&&!empty($bottom_team["name"])) { ?>&nbsp;&nbsp;<a href="admin_update_tournaments.php?id=<?php echo $tournament["tourneyid"]; ?>&act=del&matchid=<?php echo $round["id"]; ?>"><font color="<?php echo $colors["alert"]; ?>" style="font-size:7px"><b>X</b></font></a><?php } ?></div></td>
			<td width="10"<?php echo (!empty($colorone)?" bgcolor=\"".$colorone."\"":""); ?>><?php echo (!empty($teamc_one)?$teamc_one:"&nbsp;"); ?></td>
			<td><div align="left"><b><?php 
			if((current_security_level()>=2||(current_security_level()>=1&&$tournament["moderatorid"]==$_COOKIE["userid"]))&&!empty($top_team["name"])&&!empty($bottom_team["name"])) { 
				?><form action="admin_update_tournaments.php" method="POST"><input type="hidden" name="id" value="<?php echo $tournament["tourneyid"]; ?>"><input type="hidden" name="matchid" value="<?php echo $round["id"]; ?>"><img src="img/leftarrow.gif" width="5" height="8" border="0" alt="left score"><input type="text" name="leftscore" maxlength="20" style="font-size: 9px; width: 14px;" value="<?php echo (isset($top_info["score"])?$top_info["score"]:""); ?>"><?php
			} else {
				if(!empty($top_team["name"])&&!empty($bottom_team["name"])) echo "[ ".$top_info["score"]." ]";
				else echo "&nbsp;";
			}
			?></b></div></td>
			<td><div align="center"><?php 
				if($top_info["team"]==$round["top_x_advance"]&&!empty($top_info["team"])&&$round["top_x_advance"]!=0) { 
					echo "<font color=\"".$colors["primary"]."\">&lt;- <b>win</b></font>&nbsp;&nbsp;&nbsp;<font color=\"".$colors["secondary"]."\"><b>loss</b> -&gt;</font>"; 
				} elseif($bottom_info["team"]==$round["top_x_advance"]&&!empty($bottom_info["team"])&&$round["top_x_advance"]!=0) {
					echo "<font color=\"".$colors["secondary"]."\">&lt;- <b>loss</b></font>&nbsp;&nbsp;&nbsp;<font color=\"".$colors["primary"]."\"><b>win</b> -&gt;</font>"; 
				} elseif($top_info["score"]==$bottom_info["score"]&&isset($top_info["score"])&&isset($bottom_info["score"])) {
					echo "<font color=\"".$colors["blended_text"]."\">&lt;- &nbsp;<b>tie</b>&nbsp; -&gt;</font>"; 
				} else {
					echo "&nbsp;";
				}
			 ?></div></td>
			<td><div align="right"><b><?php 
			if((current_security_level()>=2||(current_security_level()>=1&&$tournament["moderatorid"]==$_COOKIE["userid"]))&&!empty($top_team["name"])&&!empty($bottom_team["name"])) { 
				?><input type="submit" value="+" style="font-size: 7px"> <input type="text" name="rightscore" maxlength="20" style="font-size: 9px; width: 14px" value="<?php echo (isset($bottom_info["score"])?$bottom_info["score"]:""); ?>"><img src="img/rightarrow.gif" width="5" height="8" border="0" alt="right score"></form><?php
			} else {
				if(!empty($top_team["name"])&&!empty($bottom_team["name"])) echo "[ ".$bottom_info["score"]." ]";
				else echo "&nbsp;";
			}
			?></b></div></td>
			<td width="10"<?php echo (!empty($colortwo)?" bgcolor=\"".$colortwo."\"":""); ?>><?php echo (!empty($teamc_two)?$teamc_two:"&nbsp;"); ?></td>
			<td><?php if((current_security_level()>=2||(current_security_level()>=1&&$tournament["moderatorid"]==$_COOKIE["userid"]))&&!empty($top_team["name"])&&!empty($bottom_team["name"])) { ?><a href="admin_update_tournaments.php?id=<?php echo $tournament["tourneyid"]; ?>&act=del&matchid=<?php echo $round["id"]; ?>"><font color="<?php echo $colors["alert"]; ?>" style="font-size:7px"><b>X</b></font></a>&nbsp;&nbsp;<?php } ?><?php echo (!empty($bottom_team["name"])?((current_security_level()>=2||(current_security_level()>=1&&$tournament["moderatorid"]==$_COOKIE["userid"]))&&!empty($top_team["name"])?"<a href=\"admin_update_tournaments.php?id=".$tournament["tourneyid"]."&matchid=".$round["id"]."&w=".$bottom_info["team"]."\"><b>":"").$bottom_team["name"].(current_security_level()>=2||(current_security_level()>=1&&$tournament["moderatorid"]==$_COOKIE["userid"])?"</b></a>":""):"<font color=\"".$colors["blended_text"]."\"><b>BYE</b></font>"); ?></td>
			<td><?php
				if(current_security_level()>=2||(current_security_level()>=1&&$tournament["moderatorid"]==$_COOKIE["userid"])) { 
					$is_scorevote_entered = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_matches_score_votes WHERE tourneyid='".$tournament["tourneyid"]."' AND matchid='".$round["id"]."'")); 
					$is_matchscore_entered = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_matches_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND matchid='".$round["id"]."' AND score IS NOT NULL")); 
					if($is_matchscore_entered) {
						$colorholder = $colors["cell_title"];
					} elseif($is_scorevote_entered) {
						$colorholder = $colors["primary"];
					} 
					if(!ALP_TOURNAMENT_MODE) { 
						?><a href="admin_disp_tournament.php?id=<?php echo $tournament['tourneyid']; ?>&rnd=<?php echo $round['rnd']; ?>"><img src="img/info.gif" width="10" height="8" border="0" alt="administrate"></a><?php spacer(3,1); ?><a href="javascript:newWindow('admin_disp_scores.php?id=<?php echo $tournament["tourneyid"]; ?>&matchid=<?php echo $round["id"]; ?>','300','180','score')"><font style="text-decoration: none;"><?php echo ($is_scorevote_entered?"<font color=\"".$colorholder."\">":""); ?><?php if(is_score_discrep($tournament['tourneyid'],$round['id'])&&all_scores_submitted($tournament['tourneyid'],$round['id'])) { echo "&lt;"; } ?><img src="img/scores.gif" width="10" height="8" border="0" alt="submitted scores"><?php if(is_score_discrep($tournament['tourneyid'],$round['id'])&&all_scores_submitted($tournament['tourneyid'],$round['id'])) { echo "&gt;"; } ?><?php echo ($is_scorevote_entered?"</font>":""); ?></font></a><?php
					}
				} elseif(current_security_level()==1&&$tournament["moderatorid"]!=$_COOKIE["userid"]) {
					if($tournament["per_team"]>1) {
						$currentteaminfo = $dbc->database_fetch_assoc($dbc->database_query("SELECT id FROM tournament_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND captainid='".$_COOKIE["userid"]."'"));
						if($top_info["team"]==$currentteaminfo["id"]||$bottom_info["team"]==$currentteaminfo["id"]) {
							$bool = true;
						} else {
							$bool = false;
						}
					} else {
						if($top_info["team"]==$_COOKIE["userid"]||$bottom_info["team"]==$_COOKIE["userid"]) {
							$bool = true;
						} else {
							$bool = false;
						}
					}
					$no_winner = no_winner($tournament['tourneyid'],$round['id']);
					if($bool && $no_winner) { 
						$is_scorevote_entered = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_matches_score_votes WHERE tourneyid='".$tournament["tourneyid"]."' AND matchid='".$round["id"]."' AND userid='".$_COOKIE["userid"]."'")); ?>
						<a href="javascript:newWindow('chng_update_scores.php?id=<?php echo $tournament["tourneyid"]; ?>&matchid=<?php echo $round["id"]; ?>','200','180','score')"><?php echo ($is_scorevote_entered?"<font color=\"".$colors["blended_text"]."\">":""); ?><b>submit score</b><?php echo ($is_scorevote_entered?"</font>":""); ?></a>
						<?php
					}
				}
				?></td>
			<td><?php 
			if($dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_matches WHERE tourneyid='".$tournament['tourneyid']."' AND server>0"))) {
				echo "<font color=\"".$colors["blended_text"]."\">".$round["server"]."</font>";
			} else {
				echo "&nbsp;";
			} ?></td>
			<td><?php 
			if(!empty($servers_arr[$round["server"]][1])) {
				if(!empty($hlsw_supported_games[$tournament_game['short']])&&$toggle['hlsw']) { 
					?><a href="hlsw://<?php echo $servers_arr[$round["server"]][1]; ?>"><img src="img/little_icons/hlsw.gif" width="10" height="9" border="0" alt="connect using hlsw" align="absmiddle"></a>&nbsp;&nbsp;<a href="hlsw://<?php echo $servers_arr[$round["server"]][1]; ?>"><?php
				}
				echo $servers_arr[$round["server"]][1]; 
				if(!empty($hlsw_supported_games[$tournament_game['short']])&&$toggle['hlsw']) { ?></a><?php }
			} else {
				echo "&nbsp;";
			} ?></td>
		</tr>
		<?php
	} ?>
	<tr><td colspan="<?php echo $colspan; ?>">&nbsp;<br /></td></tr>
	<?php
}
?>
</table>