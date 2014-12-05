<?php
require_once 'include/_universal.php';
$x = new universal('the marathon','',0);
if ($x->is_secure() && $toggle['marath']) { 
	$x->display_top(); ?>
	<b>the marathon</b>:<br />
	<br />
	<?php
	function get_top_four($tourneyid)
    {
        global $dbc;
		$tournament = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournaments WHERE tourneyid='".$tourneyid."'"));
		require 'include/tournaments/scoring_'.$tournament['ttype'].'.php';
		return array($first_id,$second_id,$third_id,$fourth_id);
	}
    
    function allscores($teamid)
    {
        global $tournament,$dbc;
        $totalscore = 0;
        $data = $dbc->database_query("SELECT score FROM tournament_matches_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND team='".$teamid."'");
        while($row = $dbc->database_fetch_assoc($data)) {
                $totalscore += $row["score"];
        }
        return $totalscore;
    }
	function get_marathon_ids() {
        global $dbc;
		$data = $dbc->database_query("SELECT tourneyid FROM tournaments WHERE marathon='1' AND lockstart='1' ORDER BY name"); 
		$tourneyids = array();
		while($temp = $dbc->database_fetch_assoc($data)) { 
			$tourneyids[] = $temp['tourneyid'];
		}
		return $tourneyids;
	}
	?>
	<b>what is the marathon?</b> the marathon is a global lan party tournament that takes the winners from each eligible tournament and rates every gamer based on how well they've done
	throughout the entire lan party.  if you've placed in a tournament, you will see your name entered below.<br />
	<br />
	<table border=0 cellpadding=0 cellspacing=0 style="width: 95%; font-size: 11px" align="center">
	<?php
	$global_scores = array();
	$global_score_counter = array();
	$data = $dbc->database_query("SELECT * FROM tournaments WHERE marathon='1' AND lockstart='1' ORDER BY name"); 
	while($tournament = $dbc->database_fetch_assoc($data)) { 
		$top_four = get_top_four($tournament["tourneyid"]); ?>
		<tr>
			<td height="70">
				<table height="100%" width="100%" cellpadding=4 cellspacing=0 style="border: 1px solid <?php echo $colors["cell_title"]; ?>">
				<tr>
				<td valign="middle" rowspan=2>
					<img src="img/pxt.gif" width="14" height="1" border="0" alt="" /><font class="title"><b><a href="tournaments.php?id=<?php echo $tournament["tourneyid"]; ?>"><?php echo $tournament["name"]; ?></a></b></font><br />
				</td>
				<td width="140">
					<?php 
					if(!empty($top_four[0])) { ?>
						<font color="<?php echo $colors["primary"]; ?>"><b>first place</b></font><br />
						<?php
						if($tournament["per_team"]==1) { 
							$player = $dbc->database_fetch_assoc($dbc->database_query("SELECT username FROM users WHERE userid='".$top_four[0]."'"));
							?><img src="img/pxt.gif" width="14" height="1" border="0" alt="" /><font class="sm">+9 &nbsp;<font color="<?php echo $colors["blended_text"]; ?>"><b><?php echo $player["username"]; ?></b><br /></font><?php
							$global_scores[$top_four[0]] += 9;
							$total_score = $dbc->database_fetch_assoc($dbc->database_query("SELECT SUM(score) AS total_score FROM tournament_matches_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND team='".$top_four[0]."'"));
							$global_score_counter[$top_four[0]] += $total_score["total_score"];
						} else {
							$team_data = $dbc->database_fetch_assoc($dbc->database_query("SELECT name FROM tournament_teams WHERE id='".$top_four[0]."'"));
							$player_data = $dbc->database_query("SELECT tournament_players.userid as userid,users.username AS username FROM tournament_players LEFT JOIN users USING (userid) WHERE tournament_players.tourneyid='".$tournament["tourneyid"]."' AND tournament_players.teamid='".$top_four[0]."' ORDER BY username"); ?>
							<font class="smm">
							<?php
							echo "&nbsp;&nbsp;<b>".$team_data["name"]."</b><br />";
							$total_score = $dbc->database_fetch_assoc($dbc->database_query("SELECT SUM(score) AS total_score FROM tournament_matches_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND team='".$top_four[0]."'"));
							while($player = $dbc->database_fetch_assoc($player_data)) {
								?><img src="img/pxt.gif" width="14" height="1" border="0" alt="" />+9 &nbsp;<font color="<?php echo $colors["blended_text"]; ?>"><b><?php echo $player["username"]; ?></b></font><br /><?php

								$global_scores[$player["userid"]] += 9;
								$global_score_counter[$player["userid"]] += $total_score["total_score"];
							} ?>
							</font>
							<?php
						}
					} ?></td>
				<td width="140">
					<?php 
					if(!empty($top_four[2])) { ?>
						<b>third place</b><br /><?php
						if($tournament["per_team"]==1) { 
							$player = $dbc->database_fetch_assoc($dbc->database_query("SELECT username FROM users WHERE userid='".$top_four[2]."'"));
							?><img src="img/pxt.gif" width="14" height="1" border="0" alt="" /><font class="sm">+3 &nbsp;<font color="<?php echo $colors["blended_text"]; ?>"><b><?php echo $player["username"]; ?></b><br /></font><?php
								
							$global_scores[$top_four[2]] += 3;
							$total_score = $dbc->database_fetch_assoc($dbc->database_query("SELECT SUM(score) AS total_score FROM tournament_matches_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND team='".$top_four[2]."'"));
							$global_score_counter[$top_four[2]] += $total_score["total_score"];
						} else {
							$team_data = $dbc->database_fetch_assoc($dbc->database_query("SELECT name FROM tournament_teams WHERE id='".$top_four[2]."'"));
							$player_data = $dbc->database_query("SELECT tournament_players.userid as userid,users.username AS username FROM tournament_players LEFT JOIN users USING (userid) WHERE tournament_players.tourneyid='".$tournament["tourneyid"]."' AND tournament_players.teamid='".$top_four[2]."' ORDER BY username"); ?>
							<font class="smm">
							<?php
							echo "&nbsp;&nbsp;<b>".$team_data["name"]."</b><br />";
							$total_score = $dbc->database_fetch_assoc($dbc->database_query("SELECT SUM(score) AS total_score FROM tournament_matches_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND team='".$top_four[2]."'"));
							while($player = $dbc->database_fetch_assoc($player_data)) {
								?><img src="img/pxt.gif" width="14" height="1" border="0" alt="" />+3 &nbsp;<font color="<?php echo $colors["blended_text"]; ?>"><b><?php echo $player["username"]; ?></b></font><br /><?php
								$global_scores[$player["userid"]] += 3;
								$global_score_counter[$player["userid"]] += $total_score["total_score"];
							} ?>
							</font>
							<?php
						}
					} ?></td>
				</tr>
				<tr>
				<td width="140">
					<?php 
					if(!empty($top_four[1])) { ?>
						<font color="<?php echo $colors["secondary"]; ?>"><b>second place</b></font><br />
						<?php
						if($tournament["per_team"]==1) { 
							$player = $dbc->database_fetch_assoc($dbc->database_query("SELECT username FROM users WHERE userid='".$top_four[1]."'"));
							?><img src="img/pxt.gif" width="14" height="1" border="0" alt="" /><font class="sm">+6 &nbsp;<font color="<?php echo $colors["blended_text"]; ?>"><b><?php echo $player["username"]; ?></b><br /></font><?php
							$global_scores[$top_four[1]] += 6;
							$total_score = $dbc->database_fetch_assoc($dbc->database_query("SELECT SUM(score) AS total_score FROM tournament_matches_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND team='".$top_four[1]."'"));
							$global_score_counter[$top_four[1]] += $total_score["total_score"];
						} else {
							$team_data = $dbc->database_fetch_assoc($dbc->database_query("SELECT name FROM tournament_teams WHERE id='".$top_four[1]."'"));
							$player_data = $dbc->database_query("SELECT tournament_players.userid as userid,users.username AS username FROM tournament_players LEFT JOIN users USING (userid) WHERE tournament_players.tourneyid='".$tournament["tourneyid"]."' AND tournament_players.teamid='".$top_four[1]."' ORDER BY username"); ?>
							<font class="smm">
							<?php
							echo "&nbsp;&nbsp;<b>".$team_data["name"]."</b><br />";
							$total_score = $dbc->database_fetch_assoc($dbc->database_query("SELECT SUM(score) AS total_score FROM tournament_matches_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND team='".$top_four[1]."'"));
							while($player = $dbc->database_fetch_assoc($player_data)) {
								?><img src="img/pxt.gif" width="14" height="1" border="0" alt="" />+6 &nbsp;<font color="<?php echo $colors["blended_text"]; ?>"><b><?php echo $player["username"]; ?></b></font><br /><?php

								$global_scores[$player["userid"]] += 6;
								$global_score_counter[$player["userid"]] += $total_score["total_score"];
							} ?>
							</font>
							<?php
						}
					} ?></td>
				<td width="140">
					<?php 
					if(!empty($top_four[3])) { ?>
						<b>fourth place</b><br />
						<?php
						if($tournament["per_team"]==1) { 
							$player = $dbc->database_fetch_assoc($dbc->database_query("SELECT username FROM users WHERE userid='".$top_four[3]."'"));
							?><img src="img/pxt.gif" width="14" height="1" border="0" alt=""><font class="sm">+1 &nbsp;<font color="<?php echo $colors["blended_text"]; ?>"><b><?php echo $player["username"]; ?></b><br /></font><?php

							$global_scores[$top_four[3]] += 1;
							$total_score = $dbc->database_fetch_assoc($dbc->database_query("SELECT SUM(score) AS total_score FROM tournament_matches_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND team='".$top_four[3]."'"));
							$global_score_counter[$top_four[3]] += $total_score["total_score"];
						} else {
							$team_data = $dbc->database_fetch_assoc($dbc->database_query("SELECT name FROM tournament_teams WHERE id='".$top_four[3]."'"));
							$player_data = $dbc->database_query("SELECT tournament_players.userid as userid,users.username AS username FROM tournament_players LEFT JOIN users USING (userid) WHERE tournament_players.tourneyid='".$tournament["tourneyid"]."' AND tournament_players.teamid='".$top_four[3]."' ORDER BY username"); ?>
							<font class="smm">
							<?php
							echo "&nbsp;&nbsp;<b>".$team_data["name"]."</b><br />";
							$total_score = $dbc->database_fetch_assoc($dbc->database_query("SELECT SUM(score) AS total_score FROM tournament_matches_teams WHERE tourneyid='".$tournament["tourneyid"]."' AND team='".$top_four[3]."'"));
							while($player = $dbc->database_fetch_assoc($player_data)) {
								?><img src="img/pxt.gif" width="14" height="1" border="0" alt="">+1 &nbsp;<font color="<?php echo $colors["blended_text"]; ?>"><b><?php echo $player["username"];?></b></font><br /><?php
									$global_scores[$player["userid"]] += 1;
								$global_score_counter[$player["userid"]] += $total_score["total_score"];
							} ?>
							</font>
							<?php
						}
					} ?></td>
				</tr>
				</table>
			<br />
			</td>
		</tr>
		<?php
	} ?>
	</table>
	<br />
	<?php
	$temp = array();
	foreach($global_scores as $key => $val) {
		$temp[$val][$global_score_counter[$key]][] = $key;
	}
	$the_marathon = array();
	krsort($temp);
	foreach($temp as $key => $val) {
		krsort($val);
		foreach($val as $inner_key => $inner_val) {
			krsort($inner_val);
			foreach($inner_val as $playerid) {
				$the_marathon[] = array($playerid,$key,$inner_key);
			}
		}
	}
	 ?>
	<table cellpadding="8" cellspacing="0" style="width: 95%; border: 1px solid <?php echo $colors['blended_text']; ?>" align="center">
	<tr><td width="30"><b><u>#</u></b></td><td><b><u>player name</u></b></td><td width="60"><div align="center"><b><u>tournaments</u></b><br /><span style="font-size: 10px">marathon (total)</span></div></td><td width="160"><div align="center"><b><u>total marathon points</u></b></div></td><td width="60"><div align="center"><b><u>average</u></b><br /><span style="font-size: 10px">per participated</span></div></td><td width="160"><div align="center"><b><u>total tournament score</u></b></div></td></tr>
	<?php
	$ids = get_marathon_ids();
	$str = makeCommaDel($ids,'tourneyid','OR');
	$counter = 1;
	foreach($the_marathon as $val) { 
		if($counter==1&&$master["marathonleader"]!=$val[0]) {
			$dbc->database_query("UPDATE master SET marathonleader='".$val[0]."'");
		}
		$player = $dbc->database_fetch_assoc($dbc->database_query("SELECT username FROM users WHERE userid='".$val[0]."'"));
		if($player["marathon_pts"]!=$val[1]||$player["marathon_pts_tourney"]!=$val[2]||$player["marathon_rank"]!=$counter) {
			$dbc->database_query("UPDATE users SET marathon_points='".$val[1]."', marathon_points_tourney='".$val[2]."', marathon_rank='".$counter."' WHERE userid='".$val[0]."'");
		}
		$num_participated = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_players WHERE userid='".$val[0]."' AND (".$str.")"));
		$num_all_participated = $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_players WHERE userid='".$val[0]."'"));
		 ?>
		<tr<?php echo ($counter==1?" bgcolor=\"".$colors["cell_alternate"]."\" style=\"color: ".$colors["primary"].";font-weight: bold\"":($counter==2?" bgcolor=\"".$colors["cell_title"]."\" style=\"color: ".$colors["secondary"].";font-weight: bold\"":"")); ?>><td><?php echo $counter; ?></td><td><a href="disp_users.php?id=<?php echo $val[0]; ?>"><?php echo $player["username"]; ?></a></td><td width="60"><div align="center"><?php echo $num_participated.' ('.$num_all_participated.')'; ?></div></td><td width="160"><div align="center"><?php echo $val[1]; ?></div></td><td width="60"><div align="center"><?php echo ($num_participated>0?round($val[1]/$num_participated,2):''); ?></div></td><td width="160"><div align="center"><?php echo $val[2]; ?></div></td></tr>
		<?php
		$counter++;
	} ?>
	</table>
	<br />
	<br />
	<font class="sm"><b>notes</b>: total tournament score will only compile from the tournaments in which you've placed in the top four.  total tournament score will act as a tiebreaker.  average tournament points compile from marathon tournaments participated in (for example, if you have 20 points, and you were in 4 marathon tournaments, your average would be 5, even if there were 8 marathon tournaments total).<br /></font>
	<?php
	$x->display_bottom();
} else {
	$x->display_slim("you are not authorized to view this page.");
}
?>

