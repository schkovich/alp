<?php
global $dbc;
require_once 'include/_squery.php';
include_once 'include/gamelauncher/hlsw_supported_games.php';
?>
<font class="sm"><a href="gamerequest.php"><b>open play games</b></a> in the past hour:<?php if (current_security_level() >= 1) { ?> [<a href="chng_gamerequest.php"><b>add and modify your own</b></a>]<?php } ?><?php adminlink('admin_gamerequest.php'); ?><br /></font>
<?php
spacer(1,4,1);
?>
<table border="0" width="100%" cellpadding="2" cellspacing="0" class="smm" align="center">
<tr bgcolor="<?php echo $colors['cell_title']; ?>">
	<td>username</td>
    <td>game (click to view details)</td>
	<td>address (click to connect)</td>
    <?php /* <td>map<br /><?php spacer(80); ?></td>
    <td>players</td> */ ?>
 </tr>
	<?php
	$counter = 0;
	$data = $dbc->database_query("SELECT game_requests.*,games.querystr2, games.name AS game_name,games.thumbs_dir, games.short FROM game_requests LEFT JOIN games USING (gameid) WHERE (UNIX_TIMESTAMP()-UNIX_TIMESTAMP(itemtime))<3600 AND (UNIX_TIMESTAMP()-UNIX_TIMESTAMP(itemtime))>-3600 AND (game_requests.gameid>0 OR game_requests.gamename!='')");
	while($row = $dbc->database_fetch_assoc($data)) {
		$user = $dbc->database_fetch_assoc($dbc->database_query('SELECT username FROM users WHERE userid='.(int)$row['userid']));
		if ($counter%2==0) {
			$loopcolor = $colors['cell_background'];
		} else {
			$loopcolor = $colors['cell_alternate']; 
		}
		if (!empty($row['querystr2'])) {
			//$row['gameid']
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
			<tr <?php echo ($counter%2==1?' bgcolor="'.$colors['cell_alternate'].'"':''); ?>>
				<td><a href="disp_users.php?id=<?php echo $row['userid']; ?>"><?php echo $user['username']; ?></a></td>
				<?php /* <td><a href="gamerequest.php#<?php echo $row['id']; ?>"><?php echo (!empty($row['game_name'])?$row['game_name']:$row['gamename']); ?></a></td>
				*/ ?>
				<td><a href="viewserver.php?ip=<?php echo $ipaddress; ?>&port=<?php echo $queryport; ?>&enginetype=<?php echo $row['querystr2']; ?>""><?php echo $row['game_name']; ?></a></td>
				<td><?php if(!empty($hlsw_supported_games[$row['short']])&&$toggle['hlsw']) { ?><a href="hlsw://<?php echo $row['ipaddress']; ?>">
                                            <img src="img/little_icons/hlsw.gif" width="10" height="9" border="0" alt="connect using hlsw" align="absmiddle" /></a>&nbsp;&nbsp;<?php } ?><?php if(!empty($hlsw_supported_games[$row['short']])&&$toggle['hlsw']) { ?><a href="hlsw://<?php echo $row['ipaddress']; ?>"><?php } ?><?php echo $row['ipaddress']; ?><?php if(!empty($hlsw_supported_games[$row['short']])&&$toggle['hlsw']) { ?></a><?php } ?></td>
				<?php /*
				<td><a href="gamerequest.php#<?php echo $row['id']; ?>"><?php echo (!empty($row['querystr2'])&&!empty($map)?$map:'&nbsp;'); ?></a></td>
				<td><a href="gamerequest.php#<?php echo $row['id']; ?>"><?php echo (!empty($row['querystr2'])?($num_players?$num_players:"0")." / ".($max_players?$max_players:"0"):'&nbsp;'); ?></a></td>
				*/ ?>
			</tr>
			<?php
		$counter++;
	}
	?>
</table>
<?php 
echo "<div align=right><font class=sm>Powered By: <a href='http://www.squery.com' target='_BLANK'>".showVersion()."</a></font></div>"; ?>