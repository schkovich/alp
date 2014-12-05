<?php
global $dbc;
require_once 'include/_squery.php';
include_once 'include/gamelauncher/hlsw_supported_games.php';
?>
<font class="sm"><a href="servers.php"><strong>servers</strong></a>:<?php if (current_security_level() >= 2) { ?> [<a href="admin_serverlist.php"><strong>admin</strong></a>]<?php } ?><br /></font>
<?php spacer(1,4,1); ?>
<table border="0" width="100%" cellpadding="2" cellspacing="0" class="smm" align="center">
<tr bgcolor="<?php echo $colors['cell_title']; ?>">
	<?php //<td>hostname<br /><?php spacer(80); ?></td>
    <td>game (click to view details)</td>
	<td>address (click to connect)</td>
    <?php /*<td>map<br /><?php spacer(80); ?></td>
    <td>players</td> */ ?>
 </tr>
	<?php
	$counter = 0;
	$data = $dbc->database_query('SELECT servers.*, games.querystr2, games.name AS game_name, games.thumbs_dir, games.short FROM servers 
                        LEFT JOIN games USING (gameid) WHERE servers.tourneyid=0 ORDER BY games.name');
	while ($row = $dbc->database_fetch_assoc($data)) {
		if ($counter%2 == 0) {
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
	/*			
			$gameserver = queryServer($ipaddress,$queryport,$querystr2);
			//$hostname = $gameserver->htmlize($gameserver->servertitle);
			$hostname = $gameserver->servertitle;
			$game_ver = $gameserver->rules["gameversion"];
			$map = $gameserver->mapname;
			$num_players = $gameserver->numplayers;
			$max_players = $gameserver->maxplayers;
			//$server_os = gq_var('server_os',$querytype,$info);
			$passworded = $gameserver->password;
			//$config_array = gq_var('config',$querytype,$info);
			$row['game_name'] = $row['game_name'];
	*/
		}
 ?>
			<tr <?php echo ($counter%2==1?'bgcolor="'.$colors['cell_alternate'].'"':''); ?>>
				<?php /*
				<td><a href="viewserver.php?ip=<?php echo $ipaddress; ?>&port=<?php echo $queryport; ?>&enginetype=<?php echo $row['querystr2']; ?>"><?php 
					if (!empty($row['querystr2']) && !empty($hostname)) { 
						?><?php echo $hostname; ?><?php if ($passworded == 1) { ?>&nbsp;&nbsp;<img src="<?php echo $master['currentskin']; ?>lock.gif" width="8" height="11" border="0" alt="passworded" /><?php } ?><br /><?php 
					} else {
						echo '&nbsp;';
					} ?></a></td>
					<?php */ ?>
				<td><a href="viewserver.php?ip=<?php echo $ipaddress; ?>&port=<?php echo $queryport; ?>&enginetype=<?php echo $row['querystr2']; ?>""><?php echo $row['game_name']; ?></a></td>
				<td><?php if(!empty($hlsw_supported_games[$row['short']])&&$toggle['hlsw']) { ?><a href="hlsw://<?php echo $row['ipaddress']; ?>"><img src="img/little_icons/hlsw.gif" width="10" height="9" border="0" alt="connect using hlsw" align="absmiddle" /></a>&nbsp;&nbsp;<?php } if(!empty($hlsw_supported_games[$row['short']])&&$toggle['hlsw']) { ?><a href="hlsw://<?php echo $row['ipaddress']; ?>"><?php } ?><?php echo $row['ipaddress']; ?><?php if(!empty($hlsw_supported_games[$row['short']])&&$toggle['hlsw']) { ?></a><?php } ?></td>
				<?php /*
				<td><a href="viewserver.php?ip=<?php echo $ipaddress; ?>&port=<?php echo $queryport; ?>&enginetype=<?php echo $row['querystr2']; ?>""><?php echo (!empty($row['querystr2'])&&!empty($map)?$map:'&nbsp;'); ?></a></td>
				<td><a href="viewserver.php?ip=<?php echo $ipaddress; ?>&port=<?php echo $queryport; ?>&enginetype=<?php echo $row['querystr2']; ?>""><?php echo (!empty($row['querystr2'])?($num_players?$num_players:'0')." / ".($max_players?$max_players:"0"):'&nbsp;'); ?></a></td>
				*/ ?>
			</tr>
			<?php
		$counter++;
	}
	?>
</table>
<?php echo "<div align=right><font class=sm>Powered By: <a href='http://www.squery.com' target='_BLANK'> ".showVersion()."</a></font></div>"; ?>