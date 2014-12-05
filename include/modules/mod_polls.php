<?php
global $colors, $master, $toggle, $userinfo, $images, $dbc;
include_once 'include/cl_bargraph.php';
if (current_security_level() >= 1 || $master['pollsguest']) {
	$temp = $dbc->database_query('SELECT * from poll WHERE activepoll=1');
	$othertemp = $dbc->database_query('SELECT * from poll');
	if ($dbc->database_num_rows($temp) == 1) {
		$rowtemp = $dbc->database_fetch_array($temp);
		echo $rowtemp['headline'].'<br />';
		$newtemp = $dbc->database_query('SELECT * from poll_votes where userid='.(int)$_COOKIE['userid'].' AND pollid='.(int)$rowtemp['pollid']);
		if ($dbc->database_num_rows($newtemp) == 0 && current_security_level() >= 1) { ?>
			<font size="1"><br /></font>
			<form action="chng_vote.php" method="post">
			<input type="hidden" name="pollid" value="<?php echo $rowtemp['pollid']; ?>" />
			<?php 
			for ($i = 1; $i <= 15; $i++) {
				if (!empty($rowtemp['choice'.$i])) { ?>
					<input type="radio" class="radio" name="vote" value="<?php echo $i; ?>" /><?php echo $rowtemp['choice'.$i]; ?><br /><?php
				}
			} ?>
			<input type="radio" class="radio" name="vote" value="0" /><font color="<?php echo $colors['blended_text']; ?>">abstain (view results)</font><br />
			<br />
			<div align="right"><input type="submit" value="vote" class="formcolors" /></div>
			</form>
			<?php
		} else {
			$votes = $dbc->database_query('SELECT * from poll_votes where pollid='.(int)$rowtemp['pollid'].' AND choiceid!=0');
			$numvotes = $dbc->database_num_rows($votes);
			if($numvotes!=1) {
				$holder = 's';
			} else {
				$holder = '';
			}
			echo '<font size=1 color="'.$colors['blended_text'].'">'.$numvotes.' total vote'.$holder.'<br /><br /></font>';
			if ($numvotes != 0) {
				for ($i = 1; $i <= 15; $i++) {
					if (!empty($rowtemp['choice'.$i])) {
						$blahtemp = $dbc->database_query('SELECT * from poll_votes where pollid='.(int)$rowtemp['pollid'].' AND choiceid='.(int)$i);
						?>
						<font class="sm"><?php echo $rowtemp['choice'.$i]; ?> &nbsp;<font color="<?php echo $colors['blended_text']; ?>">[<?php echo $dbc->database_num_rows($blahtemp); ?> vote<?php echo ($dbc->database_num_rows($blahtemp)!=1?'s':''); ?>]</font><br /></font>
						<?php
						$percent = $dbc->database_num_rows($blahtemp)/$numvotes;
						$b = new bargraph($percent,100,1);
						$b->set_labels(1);
						$b->set_padding(0,4);
						$b->display();
					}
				}
			} else { ?>
				<b>no votes have been cast.</b><br /><?php
			}
		}
		?>
		<br />
		<div align="right"><font size="1">[<a href="polls.php">view all opinion polls</a>]</font></div>
		<?php
	} elseif($dbc->database_num_rows($othertemp)>0) {
		$blarg = $dbc->database_query('SELECT * FROM poll'); 
		$counter = 0;
		while($blargy = $dbc->database_fetch_assoc($blarg)) { 
			if(!$dbc->database_num_rows($dbc->database_query('SELECT * FROM poll_votes WHERE pollid='.(int)$blargy['pollid'].' AND userid='.(int)$_COOKIE['userid']))) { ?>
				<font class="sm"><?php get_arrow(); ?>&nbsp;<a href="polls.php#POLL<?php echo $blargy['pollid']; ?>"><?php echo $blargy['headline']; ?></a><br /></font>
				<?php
				$counter++;
			}
		} 
		if ($counter == 0) { ?>
			<span class="sm"><font color="<?php echo $colors['blended_text']; ?>">you've voted in all the polls!</font></span>
			<?php
		} ?>
		<br />
		<div align="right"><font class="sm">[<a href="polls.php"><b>view all</b></a>]</font></div>
		<?php
	}
} 
?>