<?php
require_once 'include/_universal.php';
include_once 'include/tournaments/_tournament_functions.php';
$x = new universal('maps','',0);
if ($x->is_secure() && !empty($_GET['id']) && empty($_POST) && $dbc->database_num_rows($dbc->database_query("SELECT * FROM tournaments WHERE tourneyid=".$_GET['id'])) && $dbc->database_num_rows($dbc->database_query("SELECT * FROM poll_maps WHERE tourneyid='".$_GET['id']."' AND selected=1"))) {
	$x->display_top(0,0);
	$t_name = $dbc->database_fetch_assoc($dbc->database_query("SELECT tournaments.*,games.thumbs_dir AS thumbs_dir FROM tournaments LEFT JOIN games USING (gameid) WHERE tournaments.tourneyid='".$_GET['id']."'"));
	$poll = $dbc->database_query("SELECT * FROM poll_maps WHERE tourneyid='".$t_name['tourneyid']."' AND selected=1"); ?>
	<table border="0" cellpadding="8" width="100%"><tr><td>
		<script language="JavaScript">
		<!-- 
		function goTo() {
			if(document.othermenu.othergo.value!="") document.location.href = document.othermenu.othergo.value;
		} 
		// -->
		</script>
		<div align="right"><form name="othermenu"><font class="sm"><strong>display map voting: </strong></font><select name="othergo" style="width: 250px; font: 10px Verdana" onChange="goTo()"><?php
		$data = $dbc->query('SELECT tourneyid, name FROM tournaments ORDER BY name');
		while($row = $data->fetchRow()) { 
			if($dbc->database_num_rows($dbc->database_query("SELECT * FROM poll_maps WHERE tourneyid='".$row['tourneyid']."' AND selected=1"))) {
				?><option value="disp_teams.php?id=<?php echo $row['tourneyid']; ?>"<?php echo (!empty($_GET['id']) && $row['tourneyid'] == $_GET['id']?' selected':''); ?>><?php echo $row['name']; ?></option>
				<?php
			}
		} ?></select>&nbsp;&nbsp;&nbsp;[<a href="tournaments.php?id=<?php echo $t_name['tourneyid']; ?>"><strong>back to tournament information</strong></a>]</form></div>
	<a href="tournaments.php?id=<?php echo $t_name['tourneyid']; ?>"><font class="tourneytitle"><?php echo $t_name['name']; ?></font></a><br />
	<font class="sm"><strong><?php echo ($t_name['random']?'random ':''); ?><?php echo $tournament_types[$t_name['ttype']][0]; ?> tournament</strong><br /></font>
	<br />
	<?php
	display_tournament_menu($t_name['tourneyid']);
	if ($dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_players WHERE tourneyid='".$t_name['tourneyid']."' AND userid='".$userinfo['userid']."'"))) {
		$is_playing = true;
	} else {
		$is_playing = false;
	}
		if (current_security_level() >= 1 && !$t_name['lockstart'] && $is_playing) { ?>
			<font class="sm"><strong>note</strong>: if you withdraw from the tournament, your map votes <strong><u>will</u></strong> be deleted.<br />
			<strong>note</strong>: a score of 0 will be averaged into the vote while a 'no opinion' vote will be ignored.<br /><br /></font>
			<?php
		} ?>
	<?php
		$x->add_related_link('modify this poll.','admin_mapvoting.php?id='.$t_name['tourneyid'],2);
		$x->display_related_links();

		$choices = array(	-2 => array('Very Poor', '#ff0000'),
                            -1 => array('Poor',      '#ffbb00'),
							0  => array('Average',   $colors['cell_background']),
							1  => array('Good',      '#66cc00'),
							2  => array('Very Good', '#009900'));

	?>
		<table border=0 width="400" class="centerd" cellpadding=6 cellspacing=1 bgcolor="<?php echo $colors['border']; ?>"><tr><td bgcolor="<?php echo $colors['cell_background']; ?>">
		<strong>most popular maps</strong>:<br />
		<?php spacer(200,4); ?>
			<table border="0" width="100%" cellpadding="2" cellspacing="2">
			<tr class="sm"><td><strong>score</strong></td><td><strong>map name</strong></td></tr>
			<?php
			$topmaps = $dbc->database_query("SELECT AVG(poll_votes_maps.vote) as thevote, poll_votes_maps.mapid FROM poll_votes_maps LEFT JOIN poll_maps ON poll_votes_maps.mapid=poll_maps.id WHERE poll_maps.tourneyid='".$t_name['tourneyid']."' GROUP BY poll_votes_maps.mapid ORDER BY thevote DESC");
			while ($row = $dbc->database_fetch_assoc($topmaps)) {
				$id = $row['mapid'];
				$score = $row['thevote'];
				$mapname = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM poll_maps WHERE tourneyid='".$t_name['tourneyid']."' AND id='".$id."'")); ?>
				<tr><td bgcolor="<?php echo $choices[round($score)][1]; ?>"><div align="center"><strong><?php 
				if (is_null($score)) { 
					echo "<font class=\"sm\" color=\"".$colors['blended_text']."\"><strong>no score</strong></font>";
				} else { ?>
					<font color="<?php echo ($score!=0?$colors['cell_background']:$colors['text']); ?>">
					<?php
					echo (round($score,2)>0?'+':'').round($score,2); 
					?>
					</font>
					<?php
				} ?></strong></div></td><td><a href="#<?php echo $id; ?>"><strong><?php echo $mapname['filename']; ?></strong></a></td></tr>
				<?php
			} ?>
			</table>
		</td></tr></table>
	<br />
		<table border="0" width="100%" cellpadding="3" cellspacing=0 style="font-size: 11px">
		<?php
		if (current_security_level() >= 1 && !$t_name['lockstart'] && $is_playing) {  ?>
			<tr><td colspan="2"><form action="<?php echo get_script_name(); ?>" method="POST">
			<input type="hidden" name="tourneyid" value="<?php echo $t_name['tourneyid']; ?>" />
			<input type="hidden" name="num_maps" value="<?php echo $dbc->database_num_rows($poll); ?>" /></td></tr>
			<?php
		}
		$i = 0;
		while ($maps = $dbc->database_fetch_assoc($poll)) { 
			if ($i%2 == 0) {
				$loopcolor = $colors['cell_background'];
			} else {
				$loopcolor = $colors['cell_alternate']; 
			}
			$votes = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM poll_votes_maps WHERE tourneyid='".$t_name['tourneyid']."' AND userid='".$userinfo['userid']."' AND mapid='".$maps['id']."'")); ?>
	    	<tr bgcolor="<?php echo $loopcolor; ?>">
			<td width="218"><table border="0" width="100%" cellpadding=0 cellspacing=1 bgcolor="<?php echo $colors['cell_title']; ?>"><tr><td bgcolor="<?php echo $colors['cell_background']; ?>"><?php
			if (file_exists('img/map_thumbnails/'.$t_name['thumbs_dir'].'/'.$maps['filename'].'.png') || file_exists('img/map_thumbnails/'.$t_name['thumbs_dir'].'/'.$maps['filename'].'.jpg')||file_exists('img/map_thumbnails/'.$t_name['thumbs_dir'].'/'.$maps['filename'].'.gif')) { ?>
				<img src="img/map_thumbnails/<?php echo $t_name['thumbs_dir'].'/'.$maps['filename']; ?>.<?php echo (file_exists('img/map_thumbnails/'.$t_name['thumbs_dir'].'/'.$maps['filename'].'.jpg')?'jpg':(file_exists('img/map_thumbnails/'.$t_name['thumbs_dir'].'/'.$maps['filename'].'.gif')?'gif':(file_exists('img/map_thumbnails/'.$t_name['thumbs_dir'].'/'.$maps['filename'].'.png')?'png':''))); ?>" width="218" height="163" border="0" alt="<?php echo $maps['filename']; ?>" /><?php
			} else { ?>
				<div align="center"><img src="img/map_thumbnails/notfound.gif" width="158" height="163" border="0" alt="map image not available."></div>
				<?php
			} ?></td></tr></table></td>
			<td valign="top">
				<a name="<?php echo $maps['id']; ?>"></a><font size="2"><strong><?php echo $maps['filename']; ?></strong></font><br />
				<br />
				<table border="0" cellpadding="2" cellspacing="0" style="font-size: 11px">
					<?php if (!empty($maps['filename'])) { ?><tr><td width="200"><strong>file name</strong>:</td><td width="100%"><?php echo $maps['filename']; ?><br /></td></tr><?php } ?>
					<?php if (!empty($maps['filedesc'])) { ?><tr><td width="200"><strong>description</strong>:</td><td width="100%"><?php echo $maps['filedesc']; ?><br /></td></tr><?php } ?>
					<tr><td colspan="2">
							<table border="0" cellpadding=3 cellspacing=1 style="font-size: 11px" class="centerd">
							<?php
							if (current_security_level() >= 1 && !$t_name['lockstart'] && $dbc->database_num_rows($dbc->database_query('SELECT * FROM tournament_players WHERE tourneyid='.(int)$t_name['tourneyid'].' AND userid='.(int)$userinfo['userid']))) { ?>	
								<tr><td colspan="2"><?php get_arrow(); ?>&nbsp;<strong>your rating</strong>:<input type="hidden" name="map<?php echo $i; ?>id" value="<?php echo $maps['id']; ?>"></td>
									<td colspan="3"><input type="radio" class="radio" name="map<?php echo $i; ?>" value="-3"<?php echo (is_null($votes['vote'])?" checked":""); ?> /> <strong> No opinion</strong><br /></td></tr>
								<tr>
								<?php
								foreach($choices as $key=>$val) { ?>
									<td bgcolor="<?php echo $val[1]; ?>" width="44"><input type="radio" class="radio" name="map<?php echo $i; ?>" value="<?php echo $key; ?>"<?php echo ($votes['vote'] == $key && !is_null($votes['vote'])?' checked':''); ?> /> <strong><?php echo ($key > 0?'+':'').$key; ?></strong></td>
									<?php
								} ?>
								</tr>
								<tr>
									<td <?php echo ($votes['vote'] == -2 && !is_null($votes['vote'])?'bgcolor="'.$colors['primary'].'"':''); ?>><img src="img/pxt.gif" width="1" height="1" border="0" alt="" /><br /></td>
									<td <?php echo ($votes['vote'] == -1 && !is_null($votes['vote'])?'bgcolor="'.$colors['primary'].'"':''); ?>><img src="img/pxt.gif" width="1" height="1" border="0" alt="" /><br /></td>
									<td <?php echo ($votes['vote'] == 0  && !is_null($votes['vote'])?'bgcolor="'.$colors['primary'].'"':''); ?>><img src="img/pxt.gif" width="1" height="1" border="0" alt="" /><br /></td>
									<td <?php echo ($votes['vote'] == 1  && !is_null($votes['vote'])?'bgcolor="'.$colors['primary'].'"':''); ?>><img src="img/pxt.gif" width="1" height="1" border="0" alt="" /><br /></td>
									<td <?php echo ($votes['vote'] == 2  && !is_null($votes['vote'])?'bgcolor="'.$colors['primary'].'"':''); ?>><img src="img/pxt.gif" width="1" height="1" border="0" alt="" /><br /></td>
								</tr>
								<tr><td colspan="5"><img src="img/pxt.gif" width="1" height="4" border="0" alt="" /><br /></td></tr>
								<?php
							} elseif (current_security_level() >= 1 && !$dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_players WHERE tourneyid='".$t_name['tourneyid']."' AND userid='".$userinfo['userid']."'"))) { ?>
								<tr><td colspan="5"><?php get_arrow(); ?>&nbsp;<span class="sm">you must be <strong>playing in the tournament</strong> to vote.<br /></span></td></tr>
								<?php
							} ?>
							<tr><td colspan="5"><?php get_arrow(); ?>&nbsp;<strong>average rating</strong>: <?php 
								$average = $dbc->database_fetch_assoc($dbc->database_query("SELECT AVG(vote) AS average FROM poll_votes_maps WHERE tourneyid='".$t_name['tourneyid']."' AND mapid='".$maps['id']."'"));
								echo (round($average['average'],2)>0?"+":"").round($average['average'],2);
								?></td></tr>
							</table>
						</td></tr>
				</table>
			</td>
			</tr><?php
			$i++;
		}
		?>
		<?php
		if (current_security_level() >= 1 && !$t_name['lockstart'] && $is_playing) { ?>
			<tr><td colspan="2"><div align="right"><input type="submit" value="place your vote!" style="font-size: 13px" class="formcolors" /></div>
			</form></td></tr>
			<?php
		} ?>		
		</table>
	</table>
	<?php
	$x->display_bottom(0,0);
} elseif ($x->is_secure() && current_security_level() >= 1 && !empty($_POST)) {
	require_once 'include/cl_validation.php';
	$valid = new validate();
	$data = $dbc->database_query("SELECT * FROM tournaments WHERE tourneyid='".$valid->get_value('tourneyid')."'");
	if(!$dbc->database_num_rows($data)) {
		$valid->add_error('tournament doesn\'t exist!');
	}
	$tournament = $dbc->database_fetch_assoc($data);
	if (!$dbc->database_num_rows($dbc->database_query("SELECT * FROM poll_maps WHERE tourneyid='".$tournament['tourneyid']."' AND selected=1"))) {
		$valid->add_error('poll doesn\'t exist!');
	}
	if ($tournament['lockstart']) {
		$valid->add_error('the tournament you are trying to vote in has already started.');
	}
	if (!$dbc->database_num_rows($dbc->database_query("SELECT * FROM tournament_players WHERE tourneyid='".$tournament['tourneyid']."' AND userid='".$userinfo['userid']."'"))) {
		$valid->add_error('you are not signed up for this tournament.');
	}
	if (!$valid->is_error()) {
		$bool = true;
		if (!$dbc->database_query("DELETE FROM poll_votes_maps WHERE tourneyid='".$tournament['tourneyid']."' AND userid='".$userinfo['userid']."'")) {
			$bool = false;
		}
		for ($i=0; $i < $valid->get_value('num_maps'); $i++) { 
			$maps = $dbc->database_fetch_assoc($dbc->database_query("SELECT * FROM poll_maps WHERE tourneyid='".$tournament['tourneyid']."' AND selected=1 AND id='".$valid->get_value('map'.$i.'id')."'"));
			if(!$dbc->database_query("INSERT INTO poll_votes_maps (tourneyid,userid,mapid,vote) VALUES ('".$tournament['tourneyid']."','".$userinfo['userid']."','".$valid->get_value('map'.$i.'id')."',".($valid->get_value('map'.$i)>-3?"'":'').($valid->get_value('map'.$i)==-3?"NULL":$valid->get_value('map'.$i)).($valid->get_value('map'.$i)>-3?"'":'').")")) {
				$bool = false;
			}
		}
		if ($bool) {
			$x->display_slim('success.','maps.php?id='.$tournament['tourneyid']);
		} else {
			$x->display_slim('unknown error.  your maps were not updated.','maps.php?id='.$tournament['tourneyid']);
		}
	} else {
		$x->display_top();
		$valid->display_errors();
		$x->display_bottom();
	}
} else {
	if (!$x->is_secure() || (!empty($_POST) && current_security_level() < 1)) {
		$str = 'you are not authorized to view this page.';
		$url = 'index.php';
	} elseif (empty($_GET['id'])) {
		$str = 'you must specifiy a tournament to vote maps for.';
		$url = 'tournaments.php';
	} elseif (!$dbc->database_num_rows($dbc->database_query('SELECT * FROM tournaments WHERE tourneyid='.(int)$_GET['id']))) {
		$str = 'that tournament is not in the database.';
		$url = 'tournaments.php';
	} elseif (!$dbc->database_num_rows($dbc->database_query('SELECT * FROM poll_maps WHERE tourneyid='.(int)$_GET['id'].' AND selected=1'))) {
		$str = 'there are no maps available to vote for in that tournament.';
		$url = 'tournaments.php';
	} else {
		$str = 'unknown error.';
		$url = 'tournaments.php';
	}
	$x->display_slim($str,$url);
}
?>
