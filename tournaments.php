<?php
require_once 'include/_universal.php';
require_once 'include/cl_bargraph.php';
require_once 'include/tournaments/_tournament_functions.php';
$x = new universal('tournaments','',0);
if ($x->is_secure()) { 
	$x->display_top();
	if (empty($_GET)) { ?>
		<strong>tournaments</strong>:<br />
		<br />
		<?php
		if(!ALP_TOURNAMENT_MODE || ALP_TOURNAMENT_MODE_COMPUTER_GAMES) $x->add_related_link('add/modify games','admin_games.php',2);
		$x->add_related_link('add/modify tournaments','admin_tournament.php',2);
		$x->display_related_links();

	    function allscores($teamid)
        {
	        global $tournament, $dbc;
	        $totalscore = 0;
	        $data = $dbc->query('SELECT score FROM tournament_matches_teams 
                                WHERE tourneyid='.$dbc->quote($tournament['tourneyid']).' 
                                AND team='.$dbc->quote($teamid));
	        while($row = $data->fetchRow()) {
	        	$totalscore += $row['score'];
	        }
	    	return $totalscore;
	    }
        
		function get_first($tourneyid)
        {
            global $dbc;
			$tournament = $dbc->queryRow('SELECT * FROM tournaments WHERE tourneyid='.(int)$tourneyid);
			require 'include/tournaments/scoring_'.$tournament['ttype'].'.php';
			return $first_id;
		}
		?>
		<table border="0" width="100%" cellpadding="3" cellspacing="0" style="font-size: 11px">
		<?php
		if ($toggle['marath']) { ?>
			<tr>
				<td>
					<table border="0" width="100%" height="40" cellpadding="4" cellspacing="1" bgcolor="<?php echo $colors["cell_title"]; ?>">
						<tr>
							<td bgcolor="<?php echo $colors['cell_background']; ?>" valign="middle">
							&nbsp;&nbsp;<strong><a href="themarathon.php" style="color: <?php echo $colors['primary']; ?>">the marathon</a></strong> <font class="sm">&nbsp;<font color="<?php echo $colors['blended_text']; ?>">( indicated by an </font><font color="<?php echo $colors['primary']; ?>"><strong>*</strong></font><font color="<?php echo $colors['blended_text']; ?>"> )</font></font>
							</td>
						</tr>
					</table>
				</td>
				<td valign="top" width="300">
					<table border="0" width="300" height="40" cellpadding="4" cellspacing="1" bgcolor="<?php echo $colors['cell_title']; ?>">
						<tr>
							<td bgcolor="<?php echo $colors['cell_background']; ?>" valign="middle">
								&nbsp;&nbsp;<strong>leader</strong> //&nbsp;&nbsp; <?php 
									$marathon_leader = $dbc->queryRow('SELECT userid,username FROM users WHERE userid='.(int)$master['marathonleader']); 
									if (!empty($marathon_leader['username'])) { echo "<a href=\"disp_users.php?id=".$marathon_leader['userid']."\">".$marathon_leader['username']."</a>"; } else { echo "<font color=\"".$colors['cell_title']."\"><strong>none available.</strong></font>"; } ?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<?php
		}
		if ($toggle['benchmarks']) { ?>
			<tr>
				<td>
					<table border="0" width="100%" height="40" cellpadding="4" cellspacing="1" bgcolor="<?php echo $colors['cell_title']; ?>">
						<tr>
							<td bgcolor="<?php echo $colors['cell_background']; ?>" valign="middle">
							&nbsp;&nbsp;<strong><a href="benchmarks.php" style="color: <?php echo $colors['primary']; ?>">benchmarking competition</a></strong>
							</td>
						</tr>
					</table>
				</td>
				<td valign="top" width="300">
					<table border="0" width="300" height="40" cellpadding="4" cellspacing="1" bgcolor="<?php echo $colors['cell_title']; ?>">
						<tr>
							<td bgcolor="<?php echo $colors['cell_background']; ?>" valign="middle">
								&nbsp;&nbsp;<strong>leader</strong> //&nbsp;&nbsp; <?php 
									$benchmark_leader = $dbc->queryRow('SELECT userid,username FROM users WHERE userid='.(int)$master['benchmarkleader']); 
									if (!empty($benchmark_leader['username'])) { echo "<a href=\"disp_users.php?id=".$benchmark_leader['userid']."\">".$benchmark_leader['username']."</a>"; } else { echo "<font color=\"".$colors['cell_title']."\"><strong>none available.</strong></font>"; } ?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<?php
		}
		if ($toggle['caffeine']) { ?>
			<tr>
				<td>
					<table border="0" width="100%" height="40" cellpadding="4" cellspacing="1" bgcolor="<?php echo $colors['cell_title']; ?>">
						<tr>
							<td bgcolor="<?php echo $colors['cell_background']; ?>" valign="middle">
							&nbsp;&nbsp;<strong><a href="caffeine.php" style="color: <?php echo $colors['primary']; ?>">caffeine log</a></strong>
							</td>
						</tr>
					</table>
				</td>
				<td valign="top" width="300">
					<table border="0" width="300" height="40" cellpadding="4" cellspacing="1" bgcolor="<?php echo $colors['cell_title']; ?>">
						<tr>
							<td bgcolor="<?php echo $colors['cell_background']; ?>" valign="middle">
								&nbsp;&nbsp;<strong>leader</strong> //&nbsp;&nbsp; <?php 
									$caffeine_leader = $dbc->queryRow('SELECT userid,username FROM users WHERE caffeine_mg != 0 ORDER BY caffeine_mg DESC LIMIT 1'); 
									if (!empty($caffeine_leader['username'])) { echo "<a href=\"disp_users.php?id=".$caffeine_leader['userid']."\">".$caffeine_leader['username']."</a>"; } else { echo "<font color=\"".$colors['cell_title']."\"><strong>none available.</strong></font>"; } ?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<?php
		}
		$counter = 0;
		$data = $dbc->query('SELECT tournaments.tourneyid, tournaments.random, tournaments.ttype,
                            tournaments.name, tournaments.marathon, tournaments.lockstart,
                            tournaments.per_team, tournaments.max_teams, tournaments.lockjoin, tournaments.lockteams,
                            tournaments.lockstart,
                            games.name AS game_name, games.current_version, games.url_update, games.url_maps 
                            FROM tournaments LEFT JOIN games USING (gameid) WHERE tentative=0 ORDER BY name');
		while ($tournament = $data->fetchRow()) { 
			$first_id = get_first($tournament['tourneyid']);
			$txt = get_what_teams_called($tournament['tourneyid']);
			$teams = get_num_teams($tournament['tourneyid']);
			?>
			<tr>
				<td width="100%" height="163">
					<table border="0" width="100%" height="163" cellpadding="4" cellspacing="1" bgcolor="<?php echo $colors['cell_title']; ?>">
					<tr>
					<td<?php echo (current_security_level()>=1?" colspan=\"2\"":""); ?> bgcolor="<?php echo $colors['cell_background']; ?>" valign="middle" style="font-size: 11px" height="123">
					<?php
					if ($tournament['lockstart']) {
						$link = make_tournament_link($tournament['tourneyid']);
					} else {
						$link = 'disp_teams.php?id='.$tournament['tourneyid'];
					} ?>
					<div align="center">
					<font size="2"><strong><a href="tournaments.php?id=<?php echo $tournament['tourneyid']; ?>"><?php echo $tournament['name']; ?></a></strong></font> 
                    <?php 
					if ($tournament['marathon']) { ?>
                    	<font class="sm" color="<?php echo $colors['primary']; ?>"><strong>*</strong></font>
                        <?php 
					} 
					if(current_security_level()>=2) { ?>&nbsp;&nbsp;<font color="<?php echo $colors['blended_text']; ?>">[<a href="admin_tournament.php?mod=1&q=<?php echo $tournament['tourneyid']; ?>" style="color: <?php echo $colors['blended_text']; ?>">admin</a>]</font><?php } ?><br />
					<?php 
					echo ($tournament['random']?'random ':'').$tournament_types[$tournament['ttype']][0]; ?> tournament
					</div>
					</td></tr>
					<tr>
					<?php
					if(current_security_level()>=1 && !ALP_TOURNAMENT_MODE) { 
						$participant = $dbc->query("SELECT id FROM tournament_players WHERE tourneyid='".$tournament['tourneyid']."' AND userid='".$_COOKIE['userid']."'"); ?>
						<td width="80" bgcolor="<?php echo $colors['cell_background']; ?>" valign="middle" style="font-size: 11px" height="18" align="left">
						<div align="center"><?php
						if($participant->numRows()) { ?>
							<font color="<?php echo $colors['primary']; ?>"><strong>registered</strong></font>
							<?php
						} else { ?>
							unregistered
							<?php
						} ?></div>
						</td>
						<?php
					} ?>
					<td bgcolor="<?php echo $colors['cell_background']; ?>" valign="middle" style="font-size: 11px" height="18" align="right"><?php
					display_tournament_menu($tournament['tourneyid'],0);
					?></td></tr></table>
				</td>
				<td valign="top" width="300">
					<table border="0" width="300" height="163" cellpadding="4" cellspacing="1" bgcolor="<?php echo $colors['cell_title']; ?>"><tr><td bgcolor="<?php echo $colors['cell_background']; ?>" valign="top" height="126">
						<table border="0" width="100%" cellpadding="2" cellspacing="0" style="font-size: 11px">
							<?php
							if(!ALP_TOURNAMENT_MODE || ALP_TOURNAMENT_MODE_COMPUTER_GAMES) { ?>
								<tr>
									<td colspan="2"><?php echo $tournament['game_name'].' '.(!empty($tournament['current_version'])?$tournament['current_version']:'')."<font color=\"".$colors['blended_text']."\">".(!empty($tournament['url_update'])?" &nbsp;&nbsp;[<a href=\"".$tournament['url_update']."\"><font color=\"".$colors['blended_text']."\">update</font></a>]":"").(!empty($tournament['url_maps'])?" &nbsp;&nbsp;[<a href=\"".$tournament["url_maps"]."\"><font color=\"".$colors["blended_text"]."\">maps</font></a>]":"")."</font>"; ?><br /></td>
								</tr>
								<?php
							} ?>
							<tr>
								<td><img src="img/pxt.gif" width="80" height="1" border="0" alt="" /><br /><strong>status</strong>:</td>
								<td><?php echo ($tournament['lockstart']?(get_first($tournament['tourneyid'])>0?'finished.':'in progress.'):'waiting to start.'); ?></td>
							</tr>
							<tr>
								<td><strong><?php echo $txt; ?></strong>:</td>
								<td><?php echo $teams.($tournament['max_teams']>0?" / ".$tournament['max_teams']:''); ?>&nbsp;&nbsp;&nbsp;[<a href="disp_teams.php?id=<?php echo $tournament["tourneyid"]; ?>">view</a>] <?php 
								if ($tournament['max_teams']<$teams) {
									if ($teams<$tournament_types[$tournament['ttype']][2]) { 
										if (!$tournament['random']||$tournament['lockstart']) {
											?>[need +<?php echo ($tournament_types[$tournament['ttype']][2]-$teams); ?> <?php echo $txt; ?>]<?php 
										} else {
											if($teams%$tournament['per_team']!=0||$teams==0) { ?>
												[need +<?php echo ($tournament['per_team']-$teams%$tournament['per_team']);
												if($teams>$tournament['per_team']) { 
													?>or -<?php echo ($teams%$tournament['per_team']);
												} ?> players]
												<?php
											}
										}
									} elseif ($tournament['random']&&!$tournament['lockstart']) {
										$potential_teams = floor($teams/$tournament['per_team']);
										$players_needed = $tournament_types[$tournament['ttype']][2]*$tournament['per_team'];
										if ($potential_teams<$tournament_types[$tournament['ttype']][2]) { 
											?>[need +<?php echo ($players_needed-$teams); ?> players]<?php 
										}
									}
								} ?></td>
							</tr>
							<?php
							if(!ALP_TOURNAMENT_MODE) { ?>
								<tr>
									<td><strong>team size</strong>:</td>
									<td><?php echo $tournament['per_team']; ?> player teams</td>
								</tr>
								<?php
							}
							if (current_security_level()>=1&&!$tournament['lockstart']&&!ALP_TOURNAMENT_MODE) { ?>
								<tr>
									<td colspan="2">
										<strong>options</strong>:<?php if(!is_under_max_teams($tournament['tourneyid'])) echo "&nbsp;&nbsp;<font color=\"".$colors['blended_text']."\">maximum ".get_what_teams_called($tournament['tourneyid'],0)." limit reached.</font><br />"; ?><br />
										<div align="center">
										<?php
										$locked = "<font color=\"".$colors['blended_text']."\"><strong>locked.</strong></font><br />";
										// assumptions: tournament hasn't already started and user has already logged in.
										$is_playing = $dbc->query("SELECT id FROM tournament_players WHERE tourneyid='".$tournament['tourneyid']."' AND userid='".$_COOKIE['userid']."'");
                                        $is_playing = $is_playing->numRows();
										$is_captain = $dbc->query("SELECT id FROM tournament_teams WHERE tourneyid='".$tournament['tourneyid']."' AND captainid='".$_COOKIE['userid']."'");
                                        $is_captain = $is_captain->numRows();
										$is_teams = $dbc->query("SELECT id FROM tournament_teams WHERE tourneyid='".$tournament['tourneyid']."'");
                                        $is_teams = $is_teams->numRows();
										// display current team info? if($is_playing&&$tournament["per_team"]>1&&!$tournament["random"]) $team_info = $dbc->database_fetch_assoc($dbc->database_query
										
										if ($tournament['lockjoin']) {
											echo $locked;
										} elseif ($tournament['lockteams']&&!$tournament['lockjoin']) {
											if($tournament['per_team']==1||$tournament['random']) {
												echo $locked;
											} else {
												$bool = false;
													if (!$is_playing&&!$is_captain&&$is_teams) {
														echo "<a href=\"disp_teams.php?id=".$tournament['tourneyid']."\"><strong>join a team</strong></a>";
														$bool = true;
													} elseif ($is_playing&&!$is_captain) {
														echo "<a href=\"disp_teams.php?id=".$tournament['tourneyid']."\"><strong>join a new team</strong></a></strong>";
														$bool = true;
													} elseif ($is_playing&&$is_captain) {
														echo "<a href=\"disp_teams.php?id=".$tournament['tourneyid']."\"><strong>modify your team</strong></a>";
														$bool = true;
													}
												if (!$is_captain&&!$is_playing) {
													if($bool) echo "&nbsp;&nbsp;|&nbsp;&nbsp;";
													echo "<a href=\"chng_update_teams.php?id=".$tournament['tourneyid']."&act=add&u=1\"><strong>pug</strong></a>";
													$bool = true;
												} elseif (!$is_captain) {
													if($bool) echo "&nbsp;&nbsp;|&nbsp;&nbsp;";
													echo "<a href=\"chng_update_teams.php?id=".$tournament['tourneyid']."&act=del\"><strong>withdraw</strong></a>";
													$bool = true;
												}
												if (!$bool) echo "<font color=\"".$colors['blended_text']."\">none.</font>";
											}
										} elseif (!$tournament["lockteams"]&&!$tournament['lockjoin']) {
											$under_limit = is_under_max_teams($tournament['tourneyid']);
											if($tournament["per_team"]==1||$tournament['random']) {
												if (!$is_playing) {
													if($under_limit) echo "<a href=\"chng_update_teams.php?id=".$tournament['tourneyid']."&act=add\"><strong>join</strong></a>";
												} else {
													echo "<a href=\"chng_update_teams.php?id=".$tournament['tourneyid']."&act=del\"><strong>withdraw</strong></a>";
												}
											} else {
												$bool = false;
												if (!$is_playing&&!$is_teams) {
													if ($under_limit) {
														echo "<a href=\"chng_teams.php?id=".$tournament['tourneyid']."\"><strong>create team</strong></a>";
														$bool = true;
													}
												} elseif ((!$is_playing&&$is_teams)||($is_playing&&$is_teams&&!$is_captain)) {
													echo "<a href=\"disp_teams.php?id=".$tournament['tourneyid']."\"><strong>join a ".($is_playing&&$is_teams&&!$is_captain?"new ":"")."team</a></strong>";
													if($under_limit) {
														echo "&nbsp;&nbsp;|&nbsp;&nbsp;<strong><a href=\"chng_teams.php?id=".$tournament['tourneyid']."\">create a ".($is_playing&&$is_teams&&!$is_captain?"new ":"")."team</strong></a>";
													}
													$bool = true;
												} elseif ($is_playing&&$is_teams&&$is_captain) {
													echo "<a href=\"disp_teams.php?id=".$tournament['tourneyid']."\"><strong>modify your team</strong></a>";
													$bool = true;
												}
												if (!$is_captain&&!$is_playing) {
													if($bool) echo "&nbsp;&nbsp;|&nbsp;&nbsp;";
													echo "<a href=\"chng_update_teams.php?id=".$tournament['tourneyid']."&act=add&u=1\"><strong>pug</strong></a>";
													$bool = true;
												} elseif (!$is_captain) {
													if($bool) echo "&nbsp;&nbsp;|&nbsp;&nbsp;";
													echo "<a href=\"chng_update_teams.php?id=".$tournament['tourneyid']."&act=del\"><strong>withdraw</strong></a>";
													$bool = true;
												}
												if (!$bool) echo "<font color=\"".$colors['blended_text']."\">none.</font>";
											}
										} ?>
									</div>
									</td>
								</tr>
								<?php
							} elseif ($tournament['per_team']>1&&$tournament['random']&&$tournament['lockstart']&&!ALP_TOURNAMENT_MODE) { ?>
								<tr>
									<td colspan="2">
										<strong>options</strong>:<br />
										<div align="center">
										<?php
										// tournament has already started and is random.
										if ($is_playing&&$is_captain) {
											echo "<a href=\"disp_teams.php?id=".$tournament['tourneyid']."\"><strong>modify your team</strong></a>";
										} ?>
										</div>
									</td>
								</tr>
								<?php
							}
                        $result = $dbc->query("SELECT id FROM poll_maps WHERE tourneyid='".$tournament['tourneyid']."' AND selected=1");
						if (!ALP_TOURNAMENT_MODE && current_security_level()>=1 && $result->numRows()) {
                            $result = $dbc->query("SELECT id FROM poll_votes_maps WHERE tourneyid='".$tournament['tourneyid']."' AND userid='".$userinfo['userid']."' AND vote IS NOT NULL"); ?>
							<tr><td colspan="2"><div align="center">&raquo; <a href="maps.php?id=<?php echo $tournament['tourneyid']; ?>"<?php if(!$result->numRows()) { ?> style="color: <?php echo $colors["primary"]; ?>; font-weight: bold"<?php } ?>>vote for maps</a> &laquo;</div></td></tr>
							<?php
						}
						?>
						</table>
					</td></tr>
					<tr><td bgcolor="<?php echo $colors['cell_background']; ?>" valign="middle" align="center" height="18"><?php
						if ($tournament['lockstart']) {
							if ($tournament['per_team']==1) {
                                $result = $dbc->query("SELECT id FROM tournament_players WHERE tourneyid='".$tournament['tourneyid']."'");
								$n = $result->numRows();
							} else {
								$result = $dbc->query("SELECT id FROM tournament_teams WHERE tourneyid='".$tournament['tourneyid']."'");
                                $n = $result->numRows();
							}
							if ($tournament['ttype']==10) {
                                $result = $dbc->query("SELECT id FROM tournament_matches WHERE tourneyid='".$tournament['tourneyid']."' AND top_x_advance!=0");
								if ($n%2==1) {
									$filled = $result->numRows()+$n;
								} else {
									$filled = $result->numRows();
								}
                                $result = $dbc->query("SELECT id FROM tournament_matches WHERE tourneyid='".$tournament['tourneyid']."'");
								$total = $result->numRows();
							} else {
                                $result = $dbc->query("SELECT * FROM tournament_matches_teams WHERE tourneyid='".$tournament['tourneyid']."' AND team!=0");
								$filled = $result->numRows()-$n;
                                $result = $dbc->query("SELECT * FROM tournament_matches_teams WHERE tourneyid='".$tournament['tourneyid']."'");
								$total = $result->numRows()-$n;
								if($tournament['ttype']==5) {
									$total -= 3;
								} elseif ($tournament['ttype']==4) {
									$total -= 1;
								}
							}
							if ($total!=0) {
                                $percent = $filled/$total;
							} else {
                                $percent = 0;
                            }
						} else {
							$percent = 0;
						}
						$b = new bargraph($percent,100,1);
						$b->set_labels(0);
						$b->set_padding(0,0);
						$b->display(); ?>
					</td></tr></table>
				</td>
			</tr>
			<?php
			$counter++;
		} 
		if(!ALP_TOURNAMENT_MODE) {
			$data = $dbc->query("SELECT tournaments.*,games.name AS game_name,games.current_version,games.url_update,games.url_maps FROM tournaments LEFT JOIN games USING (gameid) WHERE tentative='1' ORDER BY name"); 
			if ($data->numRows()) { ?>
				<tr>
					<td colspan="2">
					<br />
					<strong>tentative tournaments</strong>: these tournaments are listed as tentative because there is no guarantee that they will be held.  tentative tournaments will be held if there is enough extra time to run the tournament given the number of teams listed to play.<br />
					</td>
				</tr>
				<?php
				while ($tournament = $data->fetchRow()) { 
					if ($tournament['lockstart']) {
						if ($master['caching'] && current_security_level()<=1 && file_exists('_tournament_'.$tournament['tourneyid'].'.html')) {
							$link = '_tournament_'.$tournament['tourneyid'].'.html';
						} else {
							$link = 'disp_tournament.php?id='.$tournament['tourneyid'];
						}
					} else {
						$link = 'disp_teams.php?id='.$tournament['tourneyid'];
					} 
					if ($tournament['per_team']==1||($tournament['random']&&!$tournament['lockstart'])) {
						$txt = 'competitors';
					} else {
						$txt = 'teams';
					}
					?>
					<tr>
						<td width="100%">
							<table border="0" height="50" width="100%" cellpadding="4" cellspacing="1" bgcolor="<?php echo $colors["cell_title"]; ?>">
							<tr>
							<td bgcolor="<?php echo $colors['cell_background']; ?>" valign="middle" style="font-size: 11px">
								<div align="center"><font size="2"><a href="<?php echo $link; ?>"><strong><?php echo $tournament['name']; ?></strong></a></font><?php if(current_security_level()>=2) { ?>&nbsp;&nbsp;
	                                                    <font color="<?php echo $colors['blended_text']; ?>">[<a href="admin_tournament.php?mod=1&q=<?php echo $tournament['tourneyid']; ?>" style="color: <?php echo $colors['blended_text']; ?>">admin</a>]</font><?php } ?><br />
								<?php echo ($tournament['random']?'random ':'').$tournament_types[$tournament['ttype']][0]; ?> tournament</div>
							</td>
							</tr>
							</table>
						</td>
						<td>
							<table border="0" height="50" width="100%" cellpadding="4" cellspacing="1" bgcolor="<?php echo $colors["cell_title"]; ?>">
							<tr>
							<td bgcolor="<?php echo $colors['cell_background']; ?>" valign="middle" style="font-size: 11px">
							<div align="center"><a href="disp_teams.php?id=<?php echo $tournament['tourneyid']; ?>"><strong><?php echo $txt; ?></strong></a>
							&nbsp; | &nbsp;<?php 
							if($tournament['lockstart']) { 
								?><a href="<?php echo $link; ?>"><?php 
							} else {
								?><font color="<?php echo $colors['blended_text']; ?>"><?php 
							} 
							?><strong>standings</strong><?php 
							if($tournament['lockstart']) { 
								?></a><?php 
							} else { 
								?></font><?php 
							} ?></div>
							</td>
							</tr>
							</table>
						</td>
					</tr>
					<?php
				}
			}
		} ?>
		</table>
		<?php
        // TODO: add variable checks 
        // - i gather casting as int would drop chars like ';' - is the query then completely safe?
	} elseif (!empty($_GET) && $dbc->database_num_rows($dbc->database_query('SELECT * FROM tournaments WHERE tourneyid='.(int)$_GET['id']))) {
		$tournament = $dbc->queryRow('SELECT tourneyid, ttype, tournaments.* FROM tournaments WHERE tourneyid='.(int)$_GET['id']);
		$game = $dbc->queryRow('SELECT * FROM games WHERE gameid='.(int)$tournament['gameid']);
		$txt = get_what_teams_called($tournament['tourneyid']);
		$team_num = get_num_teams($tournament['tourneyid']);
		if ($tournament['lockstart']) {
			$link = make_tournament_link($tournament['tourneyid']);
		} else {
			$link = 'disp_teams.php?id='.$tournament['tourneyid'];
		} 
		if (!ALP_TOURNAMENT_MODE && $dbc->database_num_rows($dbc->database_query('SELECT * FROM poll_maps WHERE tourneyid='.(int)$tournament['tourneyid'].' AND selected=1'))) {
			$mapvote = true;
		} else {
			$mapvote = false;
		}
		function allscores($teamid) {
	        global $tournament, $dbc;
	        $totalscore = 0;
	        $data = $dbc->query("SELECT score FROM tournament_matches_teams WHERE tourneyid='".$tournament['tourneyid']."' AND team='".$teamid."'");
	        while($row = $data->fetchRow()) {
	                $totalscore += $row['score'];
	        }
 	       return $totalscore;
        }
		require_once 'include/tournaments/scoring_'.$tournament['ttype'].'.php';
		$top_four = array($first_id,$second_id,$third_id,$fourth_id); ?>
		<strong>tournaments</strong>: <?php echo $tournament['name']; ?> <font class="sm">[<a href="tournaments.php">back to all tournaments</a>]</font><br />
		<br />
		<table border="0" cellpadding="4" width="100%" bgcolor="<?php echo $colors['cell_title']; ?>"><tr><td>
		<font class="tourneytitle"><?php echo $tournament['name']; ?></font><br />
		</td></tr></table>
		<br />
		<table border="0" cellpadding="4" width="100%">
		<tr>
			<td width="50%" valign="top">
				<?php start_module(); ?>
				<font class="sm"><?php
				if(!ALP_TOURNAMENT_MODE || ALP_TOURNAMENT_MODE_COMPUTER_GAMES) { ?>
					<strong><?php echo $game['name']; ?></strong> <?php echo (!empty($game['current_version'])?$game['current_version']:'')."<font color=\"".$colors['blended_text']."\">".(!empty($game['url_update'])?" &nbsp;&nbsp;[<a href=\"".$game['url_update']."\"><font color=\"".$colors['blended_text']."\">update</font></a>]":"").(!empty($game['url_maps'])?" &nbsp;&nbsp;[<a href=\"".$game['url_maps']."\"><font color=\"".$colors["blended_text"]."\">maps</font></a>]":"")."</font>"; ?><br />
					<?php
				} ?>
				<strong><?php echo ($tournament['random']?'random ':''); ?><?php echo $tournament_types[$tournament['ttype']][0]; ?></strong> <?php if ($tournament['marathon']) { ?><font class="sm" color="<?php echo $colors['primary']; ?>"><strong>*</strong> <a href="themarathon.php" style="color: <?php echo $colors['primary']; ?>">marathon</a></font><?php } ?> tournament<br />
				<?php spacer(1,8,1); ?>
				<font color="<?php echo $colors['blended_text']; ?>"><strong>tournament status</strong>: <?php echo ($tournament["lockstart"]?(!empty($top_four[0])>0?'finished.':'currently in progress.'):'waiting to start.'); ?></font></font><br />
				<?php 
				end_module();
				spacer(1,4,1);
				start_module();
				get_arrow(); ?>&nbsp;<a href="disp_teams.php?id=<?php echo $tournament['tourneyid']; ?>"><strong><?php echo $txt; ?></strong></a>: <?php echo $team_num.($tournament["max_teams"]>0?" / ".$tournament['max_teams']:''); ?><br />
				<?php spacer(1,4,1); ?>
				<?php get_arrow(); ?>&nbsp;<?php if ($tournament['lockstart']) { ?><a href="<?php echo $link; ?>"><?php } else { ?><font color="<?php echo $colors["blended_text"]; ?>"><?php } ?><strong>standings</strong><?php if($tournament['lockstart']) { ?></a><?php } else { ?></font><?php } ?><br />
				<?php spacer(1,4,1); ?>
				<?php 
					if(!ALP_TOURNAMENT_MODE) {
						get_arrow(); ?>&nbsp;<?php if ($mapvote) { ?><a href="maps.php?id=<?php echo $tournament['tourneyid']; ?>"><?php } else { ?><font color="<?php echo $colors['blended_text']; ?>"><?php } ?><strong>map voting</strong><?php if($mapvote) { ?></a><?php } else { ?></font><?php } adminlink('admin_mapvoting.php?id='.$tournament['tourneyid']);?><br />
						<?php 
						spacer(1,4,1);
						get_arrow(); ?>&nbsp;<?php if (!empty($tournament['url_stats'])) { ?><a href="<?php echo $tournament['url_stats']; ?>"><?php } else { ?><font color="<?php echo $colors['blended_text']; ?>"><?php } ?><strong>statistics</strong><?php if(!empty($tournament['url_stats'])) { ?></a><?php } else { ?></font><?php } ?><br />
						<?php 
					}
				/*Disable admin_disp_tournaments for now
				if(tournament_is_secure($tournament['tourneyid']) && !ALP_TOURNAMENT_MODE) {
					spacer(1,4,1); ?>
					<?php get_arrow(); ?>&nbsp;<?php if ($tournament['lockstart']) { ?><a href="admin_disp_tournament.php?id=<?php echo $tournament['tourneyid']; ?>"><?php } else { ?><font color="<?php echo $colors["blended_text"]; ?>"><?php } ?><strong>admin</strong><?php if($tournament['lockstart']) { ?></a><?php } else { ?></font><?php } ?><br /><?php 
				}
				*/ ?>
				<?php end_module();
				if (current_security_level()>=1 && !ALP_TOURNAMENT_MODE) {
					spacer(1,4,1);
					start_module(); ?>
					<font class="sm"><strong>user control panel</strong><br /></font>
					<?php
					spacer(1,4,1);
					if (!$tournament['lockstart']) {
						$locked = "<font color=\"".$colors['blended_text']."\"><strong>locked.</strong></font><br />";
						// assumptions: tournament hasn't already started and user has already logged in.
                        $is_playing = $dbc->query("SELECT * FROM tournament_players WHERE tourneyid='".$tournament['tourneyid']."' AND userid='".$_COOKIE['userid']."'");
						$is_playing = $is_playing->numRows();
                        $is_captain = $dbc->query("SELECT * FROM tournament_teams WHERE tourneyid='".$tournament['tourneyid']."' AND captainid='".$_COOKIE['userid']."'");
						$is_captain = $is_captain->numRows();
                        $is_teams = $dbc->query("SELECT * FROM tournament_teams WHERE tourneyid='".$tournament['tourneyid']."'");
						$is_teams = $is_teams->numRows();
						// display current team info? if($is_playing&&$tournament["per_team"]>1&&!$tournament["random"]) $team_info = $dbc->database_fetch_assoc($dbc->database_query
						
						if ($tournament['lockjoin']) {
							echo $locked;
						} elseif ($tournament['lockteams']&&!$tournament['lockjoin']) {
							if ($tournament['per_team']==1||$tournament['random']) {
								echo $locked;
							} else {
								$bool = false;
								if (!$is_playing&&!$is_captain&&$is_teams) {
									get_arrow();
									echo "&nbsp;<a href=\"disp_teams.php?id=".$tournament['tourneyid']."\"><strong>join a team</strong></a><br />";
									spacer(1,4,1);
									$bool = true;
								} elseif ($is_playing&&!$is_captain) {
									get_arrow();
									echo "&nbsp;<a href=\"disp_teams.php?id=".$tournament["tourneyid"]."\"><strong>join a new team</strong></a></strong><br />";
									spacer(1,4,1);
									$bool = true;
								} elseif ($is_playing&&$is_captain) {
									get_arrow();
									echo "&nbsp;<a href=\"disp_teams.php?id=".$tournament['tourneyid']."\"><strong>modify your team</strong></a><br />";
									spacer(1,4,1);
									$bool = true;
								}
								if (!$is_captain&&!$is_playing) {
									get_arrow();
									echo "&nbsp;<a href=\"chng_update_teams.php?id=".$tournament['tourneyid']."&act=add&u=1\"><strong>pug</strong></a><br />";
									spacer(1,4,1);
									$bool = true;
								} elseif (!$is_captain) {
									get_arrow();
									echo "&nbsp;<a href=\"chng_update_teams.php?id=".$tournament['tourneyid']."&act=del\"><strong>withdraw from tournament</strong></a><br />";
									spacer(1,4,1);
									$bool = true;
								}
								if (!$bool) echo "<font color=\"".$colors['blended_text']."\">none.</font>";
							}
						} elseif (!$tournament['lockteams']&&!$tournament['lockjoin']) {
							$under_limit = is_under_max_teams($tournament['tourneyid']);
							if (!$under_limit) {
								get_arrow();
								echo "&nbsp;<font color=\"".$colors['blended_text']."\">maximum ".get_what_teams_called($tournament['tourneyid'],0)." limit reached.</font><br />";
							}
							if ($tournament['per_team']==1||$tournament['random']) {
								if (!$is_playing) {
									if ($under_limit) {
										get_arrow();
										echo "&nbsp;<a href=\"chng_update_teams.php?id=".$tournament['tourneyid']."&act=add\"><strong>join this tournament</strong></a><br />";
									}
								} else {
									get_arrow();
									echo "&nbsp;<a href=\"chng_update_teams.php?id=".$tournament['tourneyid']."&act=del\"><strong>withdraw from tournament</strong></a><br />";
								}
								spacer(1,4,1);
							} else {
								$bool = false;
								if (!$is_playing&&!$is_teams) {
									if ($under_limit) {
										get_arrow();
										echo "&nbsp;<a href=\"chng_teams.php?id=".$tournament['tourneyid']."\"><strong>create team</strong></a><br />";
										spacer(1,4,1);
										$bool = true;
									}
								} elseif ((!$is_playing&&$is_teams)||($is_playing&&$is_teams&&!$is_captain)) {
									get_arrow();
									echo "&nbsp;<a href=\"disp_teams.php?id=".$tournament['tourneyid']."\"><strong>join a ".($is_playing&&$is_teams&&!$is_captain?'new ':'').'team</a></strong><br />';
									spacer(1,4,1);
									if($under_limit) {
										get_arrow();
										echo "&nbsp;<strong><a href=\"chng_teams.php?id=".$tournament['tourneyid']."\">create a ".($is_playing&&$is_teams&&!$is_captain?'new ':'').'team</strong></a><br />';
										spacer(1,4,1);
									}
									$bool = true;
								} elseif ($is_playing&&$is_teams&&$is_captain) {
									get_arrow();
									echo "&nbsp;<a href=\"disp_teams.php?id=".$tournament['tourneyid']."\"><strong>modify your team</strong></a><br />";
									spacer(1,4,1);
									$bool = true;
								}
								if (!$is_captain&&!$is_playing) {
									get_arrow();
									echo "&nbsp;<a href=\"chng_update_teams.php?id=".$tournament['tourneyid']."&act=add&u=1\"><strong>pug</strong></a><br />";
									spacer(1,4,1);
									$bool = true;
								} elseif(!$is_captain) {
									get_arrow();
									echo "&nbsp;<a href=\"chng_update_teams.php?id=".$tournament['tourneyid']."&act=del\"><strong>withdraw from tournament</strong></a><br />";
									spacer(1,4,1);
									$bool = true;
								}
								if(!$bool) echo "<font color=\"".$colors['blended_text']."\">none.</font>";
							}
						} ?>
						</div>
						<?php
					} elseif ($tournament['per_team']>1&&$tournament['random']&&$tournament['lockstart']) {
						// tournament has already started and is random.
						if ($is_playing&&$is_captain) {
							get_arrow();
							echo "&nbsp;<a href=\"disp_teams.php?id=".$tournament['tourneyid']."\"><strong>modify your team</strong></a><br />";
							spacer(1,4,1);
						}
					}
					end_module();
				} ?>
				<br />
				<?php 
				if (!empty($top_four[0])) {
					dotted_line(0,4); ?>
					<strong>results</strong>:<br />
					<?php dotted_line(); ?>
					<table border="0" width="100%">
						<tr>
						<td><strong>1st</strong>:
							<?php
							if ($tournament['per_team']==1) { ?>
								<br /><?php
								$player = $dbc->queryOne('SELECT username FROM users WHERE userid='.(int)$top_four[0]);
								 ?>&nbsp;<font color="<?php echo $colors['blended_text']; ?>"><strong><?php echo $player; ?></strong><br /></font><?php
							} else {
								$team_data = $dbc->queryOne('SELECT name FROM tournament_teams WHERE id = '.(int)$top_four[0]);
								if(!ALP_TOURNAMENT_MODE) $player_data = $dbc->query('SELECT tournament_players.userid as userid,users.username AS username 
                                                            FROM tournament_players LEFT JOIN users USING (userid) 
                                                            WHERE tournament_players.tourneyid='.(int)$tournament['tourneyid'].' 
                                                            AND tournament_players.teamid='.(int)$top_four[0].' ORDER BY username'); 
								echo $team_data; ?><br />
								<?php
								if(!ALP_TOURNAMENT_MODE) {
									while($player = $player_data->fetchRow()) { ?>
										<?php spacer(16); ?>&nbsp;<a href="disp_users.php?id=<?php echo $player['userid']; ?>" style="color: <?php echo $colors["blended_text"]; ?>"><?php echo $player["username"]; ?></a><br /><?php
									}
								}
							} spacer(1,6,1);?></td>
						</tr>
						<?php
						if (!empty($top_four[1])) { ?>
							<tr>
							<td><strong>2nd</strong>:
								<?php
								if ($tournament['per_team']==1) { ?>
									<br /><?php
									$player = $dbc->queryOne('SELECT username FROM users WHERE userid='.(int)$top_four[1]);
									?>&nbsp;<font color="<?php echo $colors['blended_text']; ?>"><strong><?php echo $player; ?></strong><br /></font><?php
								} else {
									$team_data = $dbc->queryOne('SELECT name FROM tournament_teams WHERE id='.(int)$top_four[1]);
									if(!ALP_TOURNAMENT_MODE) $player_data = $dbc->query('SELECT tournament_players.userid as userid,users.username AS username 
                                                                FROM tournament_players LEFT JOIN users USING (userid) 
                                                                WHERE tournament_players.tourneyid='.(int)$tournament['tourneyid'].' 
                                                                AND tournament_players.teamid='.(int)$top_four[1].' ORDER BY username'); 
									echo $team_data; ?><br />
									<?php
									if(!ALP_TOURNAMENT_MODE) {
										while($player = $player_data->fetchRow()) { ?>
											<?php spacer(16); ?>&nbsp;<a href="disp_users.php?id=<?php echo $player['userid']; ?>" style="color: <?php echo $colors['blended_text']; ?>"><?php echo $player['username']; ?></a><br /><?php
										}
									}
								} spacer(1,6,1);?></td>
							</tr><?php
						} ?>
						<?php 
						if (!empty($top_four[2])) { ?>
							<tr>
							<td><strong>3rd</strong>:
								<?php
								if ($tournament['per_team']==1) { ?>
									<br /><?php
									$player = $dbc->queryOne('SELECT username FROM users WHERE userid = '.(int)$top_four[2]);
									?>&nbsp;<font color="<?php echo $colors['blended_text']; ?>"><strong><?php echo $player; ?></strong><br /></font><?php
								} else {
									$team_data = $dbc->queryOne('SELECT name FROM tournament_teams WHERE id='.(int)$top_four[2]);
									if(!ALP_TOURNAMENT_MODE) $player_data = $dbc->query('SELECT tournament_players.userid as userid,users.username AS username 
                                                                FROM tournament_players LEFT JOIN users USING (userid) 
                                                                WHERE tournament_players.tourneyid='.(int)$tournament['tourneyid'].' 
                                                                AND tournament_players.teamid='.(int)$top_four[2].' ORDER BY username'); 
									echo $team_data; ?><br />
									<?php
									if(!ALP_TOURNAMENT_MODE) {
										while($player = $player_data->fetchRow()) { ?>
											<?php spacer(16); ?>&nbsp;<a href="disp_users.php?id=<?php echo $player['userid']; ?>" style="color: <?php echo $colors['blended_text']; ?>"><?php echo $player['username']; ?></a><br /><?php
										}
									}
								} spacer(1,6,1);?></td>
							</tr><?php
						} ?>
						<?php 
						if (!empty($top_four[3])) { ?>
							<tr>
							<td><strong>4th</strong>:
								<?php
								if($tournament['per_team']==1) { ?>
									<br /><?php
									$player = $dbc->queryOne('SELECT username FROM users WHERE userid = '.(int)$top_four[3]);
									?>&nbsp;<font color="<?php echo $colors['blended_text']; ?>"><strong><?php echo $player; ?></strong><br /></font><?php
								} else {
									$team_data = $dbc->queryOne('SELECT name FROM tournament_teams WHERE id='.(int)$top_four[3]);
									if(!ALP_TOURNAMENT_MODE) $player_data = $dbc->query('SELECT tournament_players.userid as userid,users.username AS username 
                                                                FROM tournament_players LEFT JOIN users USING (userid) 
                                                                WHERE tournament_players.tourneyid='.(int)$tournament['tourneyid'].' 
                                                                AND tournament_players.teamid='.(int)$top_four[3].' ORDER BY username'); 
									echo $team_data; ?><br />
									<?php
									if(!ALP_TOURNAMENT_MODE) {
										while($player = $player_data->fetchRow()) { ?>
											<?php spacer(16); ?>&nbsp;<a href="disp_users.php?id=<?php echo $player['userid']; ?>" style="color: <?php echo $colors['blended_text']; ?>"><?php echo $player['username']; ?></a><br /><?php
										}
									}
								} spacer(1,6,1);?></td>
							</tr><?php
						} ?>
					</table>
					<br />
					<?php
				}
				if(!ALP_TOURNAMENT_MODE) {
					$placings = array('1st','2nd','3rd','4th');
					$prizes = $dbc->query('SELECT tourneyplace, prizeid, prizename FROM prizes 
	                                    WHERE tourneyid='.(int)$tournament['tourneyid'].' 
	                                    AND tourneyplace>0 ORDER BY tourneyplace');
					$otherprizes = $dbc->query('SELECT prizeid, prizename FROM prizes 
	                                            WHERE tourneyid='.(int)$tournament['tourneyid'].' 
	                                            AND tourneyplace=0 ORDER BY tourneyplace');
					if ($prizes->numRows() || $otherprizes->numRows()) { ?>
						<?php dotted_line(0,4); ?>
						<strong>prizes</strong>:<?php if (current_security_level()>=2) { echo " <font class=\"sm\">[<a href=\"admin_prizes.php\">admin</a>]</font>"; } ?><br />
						<?php dotted_line(); ?>
						<table border="0" width="100%"><tr><td>
						<?php
						if ($prizes->numRows()) {
							$prevplace = 0;
							while ($row = $prizes->fetchRow()) {
								if ($prevplace != $row['tourneyplace']) {
									if ($prevplace>0) spacer(1,8,1);
									echo '<strong>'.$placings[($row['tourneyplace']-1)].'</strong> place wins:<br />';
								}
								spacer(16); ?><font class="sm">&nbsp;<a href="disp_prizes.php#<?php echo $row['prizeid']; ?>" style="color: <?php echo $colors['blended_text']; ?>"><?php echo $row['prizename']; ?></a><br /></font>
								<?php
								$prevplace = $row['tourneyplace'];
							}
						}
						if($otherprizes->numRows()) {
							if($prizes->numRows()) spacer(1,8,1);
							echo '<strong>other</strong>:<br /><font class="sm">';
							while($row = $otherprizes->fetchRow()) {
								spacer(16); ?>&nbsp;<a href="disp_prizes.php#<?php echo $row['prizeid']; ?>" style="color: <?php echo $colors['blended_text']; ?>"><?php echo $row['prizename']; ?></a><br />
								<?php
							}
							echo '</font>';
						} ?>
						</td></tr></table>
						<?php
					}
				} ?>
				<br />
			</td>
			<td width="50%" valign="top">
				<?php
				if(!ALP_TOURNAMENT_MODE || ALP_TOURNAMENT_MODE_COMPUTER_GAMES) { ?>
					<strong>official maps</strong>: <?php if ($mapvote && !$tournament['lockstart']) { ?>[<a href="maps.php?id=<?php echo $tournament['tourneyid']; ?>"><strong>vote for maps</strong></a>]<?php } ?><br />
					<div class="ul"><font class="sm">
					<?php
					$maps = $dbc->query("SELECT DISTINCT map FROM tournament_matches WHERE tourneyid='".$tournament['tourneyid']."' AND map IS NOT NULL ORDER BY rnd");
					if($maps->numRows()) {
						while($map = $maps->fetchRow()) { ?>
							<a href="preview.php?gameid=<?php echo $game['gameid']; ?>&map=<?php echo urlencode($map['map']); ?>" target="MAPS"><?php 
							echo $map['map']; 
							?></a><br />
							<?php
						} ?>
						<table border="0" cellspacing="1" cellpadding="0" width="218" bgcolor="<?php echo $colors['border']; ?>" class="centerd">
						<tr>
						<td bgcolor="<?php echo $colors['cell_background']; ?>">
							<iframe src="preview.php" name="MAPS" width="218" height="163" scrolling="no" frameborder="0" style="
								scrollbar-3dlight-color: <?php echo $colors['cell_title']; ?>;
								scrollbar-arrow-color: <?php echo $colors['cell_title']; ?>;
								scrollbar-base-color: <?php echo $colors['cell_background']; ?>;
								scrollbar-darkshadow-color: <?php echo $colors['cell_background']; ?>;
								scrollbar-face-color: <?php echo $colors['cell_background']; ?>;
								scrollbar-highlight-color: <?php echo $colors['text']; ?>;
								scrollbar-shadow-color: <?php echo $colors['blended_text']; ?>;
								scrollbar-track-color: <?php echo $colors['cell_title']; ?>"></iframe></td>
						</tr>
						</table>
						<?php
					} else { ?>
						none listed.
						<?php
					} ?>
					</font></div>
					<br />
					<?php
				} ?>
				<strong>rules</strong>:<br />
				<?php
				if(!empty($tournament['notes'])) { ?>
					<div class="ul"><font class="sm"><?php
					$article = $tournament['notes'];
					$article = str_replace("&lt;","<",$article);
					$article = str_replace("&gt;",">",$article);
					$article = strip_tags($article,'<a><strong><i><u><font><img />');
					echo nl2br($article); ?></font></div>
					<?php
				} ?>
				<br />
				<?php
				if(!ALP_TOURNAMENT_MODE || ALP_TOURNAMENT_MODE_COMPUTER_GAMES) { ?>
					<strong>server settings</strong>:<br />
					<?php
					if(!empty($tournament['settings'])) { ?>
						<div class="ul"><font class="sm"><?php
						$article = $tournament['settings'];
						$article = str_replace("&lt;","<",$article);
						$article = str_replace("&gt;",">",$article);
						$article = strip_tags($article,'<a><strong><i><u><font><img />');
						echo nl2br($article); ?></font></div>
						<?php
					}
				} ?>
				<br />
				</td>
		</tr>
		</table>
		<div align="right">[<a href="tournaments.php">back to all tournaments</a>]</div>
		<?php
	} else { ?>
		<strong>tournaments</strong>:<br />
		<br />
		the tournament you requested is no longer with us.  pause now for a time of mourning.<br />
		<br />
		<?php
	}
	$x->display_bottom();
} else {
	$x->display_slim('you are not authorized to view this page.');
}
?>

