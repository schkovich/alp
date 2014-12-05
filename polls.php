<?php
/*
 * TODO: make it so optionally guests can vote 
 * store userid as -ve value calculated from IP address
 */
require_once 'include/_universal.php';
include_once 'include/cl_bargraph.php';
$x = new universal('polls','',$master['pollsguest']?0:1);
if($x->is_secure()) { 
	$x->display_top();
	begitem('view all polls'); 

	$x->add_related_link('add/modify polls','admin_poll.php',2);
	$x->add_related_link('add/modify map voting polls','admin_mapvoting.php',2);
	$x->display_related_links(); 

	$maps = $dbc->query('SELECT tourneyid FROM poll_maps WHERE selected=1 GROUP BY tourneyid'); 
	if ($maps->numRows()) { ?>
		<strong>maps voting</strong>: <br />
		<img src="img/pxt.gif" width="1" height="4" border="0" alt="" /><br />
		<?php
		while ($blargy = $maps->fetchRow()) { 
			$t_name = $dbc->queryOne('SELECT name FROM tournaments WHERE tourneyid='.(int)$blargy['tourneyid']); ?>
			<?php get_arrow(); ?>&nbsp;<a href="maps.php?id=<?php echo $blargy['tourneyid']; ?>"><?php echo $t_name; ?></a><br />
			<?php
		} ?>
		<br />
		<strong>other polls</strong>:<br />
		<?php
		spacer(1,6,1);
	}
	?>
	<table border="0" cellpadding="8" cellspacing="0" width="100%">
	<?php
	$counter = 0;
	$data = $dbc->query('SELECT pollid, headline, poll.* from poll');
	while ($row = $data->fetchRow()) {
		if (ceil($counter/2)%2==1) {
			$leftcolor = $colors['cell_alternate'];
			$rightcolor = $colors['cell_background'];
		} else {
			$leftcolor = $colors['cell_background'];
			$rightcolor = $colors['cell_alternate'];
		}
		if ($counter%2 == 0) {echo '<tr>'; } ?>
		<td width="50%" valign="top" bgcolor="<?php echo $leftcolor; ?>"><a name="POLL<?php echo $row['pollid']; ?>"></a>
		<strong><?php echo $row['headline']; ?></strong><br />
		<?php
		$temp = $dbc->query('SELECT pollid FROM poll_votes WHERE userid='.(int)$_COOKIE['userid'].' AND pollid='.(int)$row['pollid']);
		if (!$temp->numRows() && current_security_level() >= 1) { ?>
			<font size="1"><br /></font>
			<form action="chng_vote.php" method="post">
			<input type="hidden" name="pollid" value="<?php echo $row['pollid']; ?>" />
			<?php 
			for ($i=1; $i <= 15; $i++) {
				if (!empty($row['choice'.$i])) { ?>
					<input type="radio" class="radio" name="vote" value="<?php echo $i; ?>" /><?php echo $row['choice'.$i]; ?><br />
					<?php 
				}
			} ?>
			<input type="radio" class="radio" name="vote" value="0" /><font color="<?php echo $colors['secondary']; ?>" />abstain (view results)</font><br />
			<br />
			<div align="right"><input type="submit" value="vote" class="formcolors"></div>
			</form>
			<?php
		} else { 
			$res = $dbc->query('SELECT pollid FROM poll_votes WHERE pollid='.(int)$row['pollid'].' AND choiceid != 0'); 
            $numvotes = $res->numRows();
            ?>
                <font size="1" color="<?php echo $colors['blended_text']; ?>"><?php echo $numvotes; ?> total vote<?php echo ($numvotes!=1?'s':''); ?><br /><br /></font>
			<?php
			if ($numvotes > 0) {
				for ($i=1; $i <= 15; $i++) {
					if (!empty($row['choice'.$i])) {
						$rowtemp = $dbc->query('SELECT pollid FROM poll_votes WHERE pollid='.(int)$row['pollid'].' AND choiceid='.(int)$i);
                        $rowtempnum = $rowtemp->numRows();
						?>
						<span class="sm"><?php echo $row['choice'.$i]; ?> &nbsp;<font color="<?php echo $colors['blended_text']; ?>">[<?php echo $rowtempnum; ?> vote<?php echo ($rowtempnum != 1?'s':''); ?>]</font><br /></span>
						<?php
						$percent = $rowtempnum/$numvotes;
						$b = new bargraph($percent,100,1);
						$b->set_labels(1);
						$b->set_padding(0,4);
						$b->display();
					}
				}
			} else {
				echo '<strong>no votes have been cast.</strong>';
			}
		} ?>
		<br /><br />
		</td>
		<?php
		if ($data->numRows() == ($counter+1)) { ?>
            <td width="50%" bgcolor="<?php if ($data->numRows()%2==0) echo $rightcolor; ?>"><img src="img/pxt.gif" width="100%" height="1" border="0" alt="" /><br /></td>
			<?php
		}
		if ($counter%2==1) { ?>
			</tr>
			<?php
		}
		$counter++;
	} ?>
	</table>
	<?php
	enditem('view all polls');
	$x->display_bottom();
} else {
	$x->display_slim('you are not authorized to view this page.');
}
?>
