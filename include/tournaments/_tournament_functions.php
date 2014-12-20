<?php
function get_what_teams_called($tourneyid, $plural = 1)
{
    global $dbc;
	$q = $dbc->database_query('SELECT per_team, random, lockstart FROM tournaments WHERE tourneyid='.(int)$tourneyid);
	if ($dbc->database_num_rows($q)) {
		$tournament = $dbc->database_fetch_assoc($q);
		if ($tournament['per_team'] == 1 || ($tournament['random'] && !$tournament['lockstart'])) {
			return 'competitor'.($plural?'s':'');
		} else {
			return 'team'.($plural?'s':'');
		}
	} else {
		return '';
	}
}

function get_num_teams($tourneyid, $random_as_competitors = 1)
{
    global $dbc;
	$q = $dbc->database_query('SELECT per_team, random, lockstart FROM tournaments WHERE tourneyid='.(int)$tourneyid);
	if ($dbc->database_num_rows($q)) {
		$tournament = $dbc->database_fetch_assoc($q);
		if ($tournament['per_team'] == 1 || ($tournament['random'] && !$tournament['lockstart'])) {
			if ($tournament['per_team'] == 1 || ($tournament['random'] && !$tournament['lockstart'] && $random_as_competitors)) {
				$teams = $dbc->database_num_rows($dbc->database_query('SELECT * FROM tournament_players WHERE tourneyid='.(int)$tourneyid));
			} else {
				$teams = ceil($dbc->database_num_rows($dbc->database_query('SELECT * FROM tournament_players WHERE tourneyid='.(int)$tournament['tourneyid'])) / $tournament['per_team']);
			}
		} else {
			$teams = $dbc->database_num_rows($dbc->database_query('SELECT * FROM tournament_teams WHERE tourneyid='.(int)$tourneyid));
		}
		if ($teams) {
			return $teams;
		} else {
			return 0;
		}
	} else {
		return 0;
	}
}

function is_under_max_teams($tourneyid)
{
    global $dbc;
	$q = $dbc->database_query('SELECT max_teams FROM tournaments WHERE tourneyid='.(int)$tourneyid);
	if ($dbc->database_num_rows($q)) {
		$tournament = $dbc->database_fetch_assoc($q);
		$teams = get_num_teams($tourneyid);
		if ($tournament['max_teams'] > 0) {
			return ($teams < $tournament['max_teams']);
		}
        return true;
	}
    return false;
}

function make_tournament_link($tourneyid)
{
	if (current_security_level() < 2 && file_exists('_tournament_'.$tourneyid.'.html')) {
		return '_tournament_'.$tourneyid.'.html';
	} else {
		return 'disp_tournament.php?id='.$tourneyid;
	}	
}

function tournament_is_secure($tourneyid) {
	global $dbc;
	$result = $dbc->database_fetch_assoc($dbc->database_query("SELECT moderatorid FROM tournaments WHERE tourneyid='".$tourneyid."'"));
	$moderatorid = $result['moderatorid'];
	if(current_security_level() >= 2 || (current_security_level() >= 1 && $moderatorid == $_COOKIE['userid'])) {
		return true;	
	} else {
		return false;	
	}
}

function display_tournament_menu($tourneyid,$double_br=1,$extra_admin=0) {
	global $dbc;
	$txt = get_what_teams_called($tourneyid);
	$link = make_tournament_link($tourneyid);
	$tournament = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM tournaments WHERE tourneyid='".$tourneyid."'"));
	get_arrow();
	?>&nbsp;<a href="tournaments.php?id=<?php echo $tourneyid; ?>" class="menu"><strong>information</strong></a>
	<?php get_arrow(); ?>&nbsp;<a href="disp_teams.php?id=<?php echo $tourneyid; ?>" class="menu"><?php echo $txt; ?></a> <?php
	if($extra_admin && current_security_level()>=2 && !$tournament['lockstart']) {
		?>[ <a href="admin_teams.php?show=<?php echo $tourneyid; ?>" class="menu">add</a> | <a href="admin_teams_delete.php?show=<?php echo $tourneyid; ?>" class="menu">del</a> ]<?php
	} ?>
	<?php get_arrow(); ?>&nbsp;<?php 
	if ($tournament['lockstart']) { 
		?><a href="<?php echo $link; ?>" class="menu"><?php 
	} else { 
		?><font style="color: <?php echo $colors['blended_text']; ?>"><?php 
	} 
	?>standings<?php 
	if($tournament['lockstart']) { 
		?></a><?php 
	} else { 
		?></font><?php 
	} ?>
	<?php 
	$mapvote = $dbc->database_num_rows($dbc->database_query('SELECT * FROM poll_maps WHERE tourneyid='.(int)$tourneyid.' AND selected=1'));
	if ($mapvote) { ?>
		<?php get_arrow(); ?>&nbsp;<a href="maps.php?id=<?php echo $tourneyid; ?>" class="menu">maps</a><?php 
	}
	
	/*Disable admin_disp_tournaments for now
	if(tournament_is_secure($tourneyid) && !ALP_TOURNAMENT_MODE) { ?>
		<?php get_arrow(); ?>&nbsp;<?php 
		if ($tournament['lockstart']) { 
			?><a href="admin_disp_tournament.php?id=<?php echo $tourneyid; ?>" class="menu"><?php 
		} else { 
			?><font style="color: <?php echo $colors['blended_text']; ?>"><?php 
		} 
		?>admin<?php 
		if($tournament['lockstart']) { 
			?></a><?php 
		} else { 
			?></font><?php 
		} 
	}
	*/
	if ($extra_admin && current_security_level()>=2 && !$tournament['lockstart']) { ?>
		<?php get_arrow(); ?>&nbsp;<a href="admin_seeding.php?show=<?php echo $tourneyid; ?>" class="menu">seeding</a>
		<?php 
	}
	?><br /><?php 
	if($double_br) { 
		?><br /><?php 
	}
}

?>