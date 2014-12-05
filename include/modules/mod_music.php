<table border="0" width="100%" cellpadding="0" cellspacing="0">
<td width="150"><a href="music.php"><strong>music</strong></a><?php get_go('music.php'); ?></td>
<?php
global $dbc;
if ($dbc->queryOne('SELECT playingid FROM music WHERE `nowplaying` = \'1\'') == 0) {
					// random play mode
                    $random = true;
                    $query = "SELECT artist, title, plays, votes FROM music 
                            WHERE `nowplaying` = '1'";
				} else {
                    // queued play mode
                    $random = false;
                    $query = "SELECT m.artist, m.title, m.plays, m.votes, u.userid, u.username
                            FROM music_votes AS v
                            LEFT JOIN music AS m ON v.playingid = m.playingid
                            LEFT JOIN users AS u ON v.userid = u.userid
                            WHERE m.nowplaying = '1'
                            ORDER BY v.subtime ASC 
                            LIMIT 1";
                }
                $nowplaying = $dbc->query($query);
                if ($nowplaying->numRows() >0) {
                    $nowplaying = $nowplaying->fetchRow();
                    if ($random) {
                        // fill random play details to array
                        $nowplaying['userid'] = 0;
                        $nowplaying['username'] = 'random play';
                    }
                    ?>
                
  <table width="100%" cellpadding="3" cellspacing="0">
    <tr class="sm" bgcolor="<?php echo $colors['cell_title']; ?>">
      <td width="22%" align="right" valign="center"><b>Now Playing:</b></td><td width="78%"><?php echo $nowplaying['artist'].' - '.$nowplaying['title']; ?></td>
    </tr>
    <tr class="sm" bgcolor="<?php echo $colors['cell_alternate']; ?>">
      <td align="right">Requester:</td><td><a href="disp_users.php?id=<?php echo $nowplaying['userid'].'">'.$nowplaying['username']; ?></a></td>
    </tr>
    <tr class="smm" bgcolor="<?php echo $colors['cell_alternate']; ?>">
      <td>&nbsp;</td><td>[votes: <?php echo $nowplaying['votes']; ?>] [plays: <?php echo $nowplaying['plays']; ?>]</td>
    </tr>    

 <?php
                }
               $data = $dbc->query("SELECT * FROM music WHERE playingid!='0' AND `nowplaying` != '1' ORDER BY `votes` DESC, `playingid` ASC LIMIT 5");
               if($data->numRows() > 0) {
               	?> <tr class="sm" bgcolor="<?php echo $colors['cell_title']; ?>"><td  colspan="2"><b>Coming Up:</b></tr> <?PHP
               	?> <tr class="sm" bgcolor="<?php echo $colors['cell_title']; ?>"><td width ="30%">Artist</td><td>Title</td></tr> <?PHP
               	$z = 1;
               	$alt = true;
               	while($row = $data->fetchRow()) {
               		if(!$alt) {
               			$color = $colors['cell_title'];
               			$alt = true;
               		} else {
               			$color = $colors['cell_alternate'];
               			$alt = false;
               		}
               		$title = $row['title'];
               		$artist = $z.". ".$row['artist'];
               		echo '<tr nowrap class="sm" bgcolor='.$color.'><td>'.$artist.'</td><td>'.$title.'</td></tr>';
               		$z++;
               	}
               }
               echo '</table>';