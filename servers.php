<?php
require_once 'include/_universal.php';
require_once 'include/_squery.php';
$x = new universal('servers','',0);
if($toggle['servers']&&$x->is_secure()) {
	$x->display_top();
	?>
	<strong>servers</strong>:<br />
	<br />
	<?php
	$x->add_related_link('add/modify servers','admin_serverlist.php',2);
	$x->display_related_links(); 
	?>
	<table border="0" width="100%" cellpadding="3" cellspacing="0" style="font-size: 11px">
	<?php
	$counter = 0;
	$data = $dbc->database_query('SELECT servers.gameid, servers.id, servers.ipaddress, servers.queryport, 
                        games.querystr2, games.name AS game_name, games.thumbs_dir, games.short 
                        FROM servers LEFT JOIN games USING (gameid) WHERE servers.tourneyid=0 ORDER BY games.name');
	while ($row = $dbc->database_fetch_array($data)) {
		if ($counter%2==0) {
			$loopcolor = $colors['cell_background'];
		} else {
			$loopcolor = $colors['cell_alternate']; 
		}
		if (!empty($row['querystr2'])) {
			extract($row);
			if($queryport == "") {
				$ipaddress = explode(":",$ipaddress);
				$queryport = $ipaddress['1'];
				$ipaddress = $ipaddress['0'];
			}
			
			$gameserver = queryServer($ipaddress,$queryport,$querystr2,FALSE,FALSE);
			/* ?>
			<pre><?php print_r($gameserver); ?></pre>
			<?php */
			//$hostname = $gameserver->htmlize($gameserver->servertitle);
			$hostname = $gameserver->servertitle;
			$game_ver = $gameserver->rules["gameversion"];
			$map = strtolower($gameserver->mapname);
			$num_players = $gameserver->numplayers;
			$max_players = $gameserver->maxplayers;
			$passworded = $gameserver->password;
		}
		?>
			<tr>
				<td colspan="5"<?php echo ($counter%2==1?' bgcolor="'.$colors['cell_alternate'].'"':''); ?>><?php spacer(1,10,1); ?><a name="<?php echo $row['id']; ?>"></a></td>
			</tr>

			<tr<?php echo ($counter%2==1?" bgcolor=\"".$colors["cell_alternate"]."\"":''); ?>>
				<td><?php spacer(10); ?></td>
				<td valign="middle" align="center" width="218"><?php
				if (!empty($row['querystr2']) && file_exists('img/map_thumbnails/'.$row['thumbs_dir'].'/'.$map.'.jpg')
						|| file_exists('img/map_thumbnails/'.$row['thumbs_dir'].'/'.$map.'.gif')
						|| file_exists('img/map_thumbnails/'.$row['thumbs_dir'].'/'.$map.'.png')) {
						//$mapdir=domappic($gameserver);
						$mapdir = 'img/map_thumbnails/'.$row['thumbs_dir'].'/'.$map.(file_exists('img/map_thumbnails/'.$row['thumbs_dir'].'/'.$map.'.jpg')?'.jpg':(file_exists('img/map_thumbnails/'.$row['thumbs_dir'].'/'.$map.'.gif')?'.gif':(file_exists('img/map_thumbnails/'.$row['thumbs_dir'].'/'.$map.'.png')?'.png':''))); 
						$mapdir_dimensions = getimagesize($mapdir); 
						?>
					<table border="0" width="100%" cellpadding=0 cellspacing=1 bgcolor="<?php echo $colors['cell_title']; ?>"><tr><td align="center" bgcolor="<?php echo $colors['cell_background']; ?>"><img src="<?php echo $mapdir; ?>" width="130" height="100" border="0" alt="<?php echo $map; ?>" /></td></tr></table>
					<?php
				} elseif (!empty($row['querystr2'])&&!empty($map)) { ?>
					<img src="img/map_thumbnails/unknown.gif" width="130" height="90" border="0" alt="" /><br />
					<font class="smm" color="<?php echo $colors['blended_text']; ?>">map not found.</font>
					<?php
				} elseif (!empty($row['querystr2'])) { ?>
					<img src="img/non.gif" width="13" height="13" border="0" alt="" /><br />
					<br />
					<!--<img src="img/map_thumbnails/<?php echo $colors['image_text']; ?>_notresponding.gif" width="158" height="11" border="0" alt="" /><br />-->
					<font class="smm" color="<?php echo $colors['blended_text']; ?>">server not responding.</font>
					<?php
				} else { ?>
					<font class="smm" color="<?php echo $colors['blended_text']; ?>">not available.</font>
					<?php
				} 
				spacer(218); ?>
				</td>
				<td width="60%" valign="top" cellpadding="4">
					<font class="normal"><strong><?php echo $row['game_name']; ?></strong><br /></font>
					<br />
					<?php if($ipaddress && $queryport && $row['querystr2']) { ?><a href="viewserver.php?ip=<?php echo $ipaddress; ?>&port=<?php echo $queryport; ?>&enginetype=<?php echo $row['querystr2']; ?>">view details</a> <?php } ?>
					<br /><br />
					<table border="0" class="sm">
						<tr><td><strong>ip</strong><br /><?php spacer(70); ?></td>
							<td width="100%"><?php if(!empty($hlsw_supported_games[$row['short']])&&$toggle['hlsw']) { ?><a href="hlsw://<?php echo $row['ipaddress']; ?>"><img src="img/little_icons/hlsw.gif" width="10" height="9" border="0" alt="connect using hlsw" align="absmiddle" /></a>&nbsp;&nbsp;<?php } ?><?php if(!empty($hlsw_supported_games[$row['short']])&&$toggle['hlsw']) { ?><a href="hlsw://<?php echo $row['ipaddress']; ?>"><?php } ?><?php echo $row['ipaddress']; ?><?php if(!empty($hlsw_supported_games[$row['short']])&&$toggle['hlsw']) { ?></a><?php } ?></td></tr>
						<?php
						if (!empty($row['querystr2'])) { ?>
							<tr><td><strong>players</strong></td>
								<td width="100%"><?php echo ($num_players?$num_players:'0')." / ".($max_players?$max_players:'0'); ?></td></tr>
							<tr><td><strong>map</strong></td>
								<td width="100%"><?php echo $map; ?></td></tr>
							<?php
						} ?>
					</table>
				</td>
				<td width="50%" align="right" valign="top">
				<?php 
					if (!empty($row['querystr2'])&&!empty($hostname)) { 
						?><font class="normal"><strong> <?php echo $hostname; ?></strong><?php if($passworded  == 1) { ?>&nbsp;&nbsp;<img src="<?php echo $master['currentskin']; ?>lock.gif" width="8" height="11" border="0" alt="passworded" /><?php } ?></font><br /><?php 
					} else {
						echo '<br />';
					} ?>
				<br />
				<?php
				if (!empty($row['querystr2'])&&!empty($map)) {
					if (sizeof($config_array)>0) {
						foreach ($config_array as $val) {
							if ($info[$val]!=='') { ?><nobr><?php echo $val.": ".$info[$val]; ?><br /><?php }
						}
					}
				} ?>
				</td>
				<td><?php spacer(10); ?></td>
			</tr>
			<tr>
				<td colspan="5"<?php echo ($counter%2==1?" bgcolor=\"".$colors['cell_alternate']."\"":''); ?>><?php spacer(1,10,1); ?></td>
			</tr>
			<?php
		$counter++;
	}
	?>
	</table>
	<?php
	echo "<div align=right><font class=sm> Powered By: ".showCredits(showVersion())."</font></div>";
	$x->display_bottom();
} else {
	$x->display_slim('you are not authorized to view this page.');
}
?>