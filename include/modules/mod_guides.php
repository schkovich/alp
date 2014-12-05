<?php
global $toggle;
?>
<script language="Javascript">
<!--
function toggle(id) {
	if(document.getElementById(id).style.display == '') {
		document.getElementById(id).style.display = 'none';
	} else {
		document.getElementById(id).style.display = '';
	}
}
// -->
</script>
<?php get_arrow(); ?>&nbsp;<a href="javascript:toggle('tournaments_checklist')" class="sm"><b>tournaments</b></a><br />
<?php
$checklist = array();
$checklist[] = 'make sure the tournament game is listed in the <a href="admin_games.php">games table</a>.';
$checklist[] = 'enter the <a href="admin_tournament.php">tournament</a> into the database.';
if(!ALP_TOURNAMENT_MODE) $checklist[] = 'if you want to allow map voting, <a href="admin_mapvoting.php">enter any approved maps into the poll</a>.';
$checklist[] = '<a href="admin_teams.php">input the teams</a> for the tournament'.(!ALP_TOURNAMENT_MODE?', or let teams join and form by themselves':'').'.';
$checklist[] = '<a href="disp_teams.php">view the teams</a> to make sure they are correct.';
$checklist[] = '<a href="admin_teams_delete.php">delete any teams</a> you don\'t want.';
$checklist[] = '<a href="admin_seeding.php">seed any number of teams or erase all seeding</a>';
if(!ALP_TOURNAMENT_MODE) $checklist[] = 'if the tournament is random by rankings <a href="admin_tournament_start.php#tip1">(?)</a>, make sure no one is trying to cheat with their <a href="admin_profic.php">gaming proficiency</a>.';
$checklist[] = 'if you want ALP to randomly assign the side each team starts on in each match, make sure <a href="admin_teams_type.php">team information</a> is available.';
$checklist[] = 'once you are happy with the teams, <a href="admin_tournament_start.php">start the tournament</a>. (this will lock the teams and create the brackets)';
$checklist[] = 'promote winners by clicking on the winning team name or inputting the scores and clicking the [+] button.  to delete a team from a bracket (and/or erase the score), click the small x.';
$checklist[] = 'did you screw up the tournament? (be careful with this) you can erase all the brackets and modify the teams in the tournament by <a href="admin_tournament_unstart.php">un-starting the tournament</a>.  unstarting a tournament doesn\'t erase anything.  however, unstarting a tournament will allow you to start it again from scratch, which <b>will erase all scores, matches, and brackets, but not teams entered into the tournament.</b>';
?>
<table id="tournaments_checklist" style="display: none" class="smm">
<?php
$counter = 1;
foreach($checklist as $val) { ?>
	<tr><td valign="top">[<?php echo $counter; ?>]</td><td><?php echo $val; ?></td></tr>
	<?php
	$counter++;
} ?>
</table>
<?php
if($toggle['prizes']) { ?>
	<?php get_arrow(); ?>&nbsp;<a href="javascript:toggle('prizes_checklist')" class="sm"><b>prizes</b></a><br />
	<?php
	$checklist = array(
		"enter the <a href=\"admin_prizes.php\">prizes</a> into the database.",
		"allow sufficient time for users to register for prizes (ideally you'd wait until the end of the LAN)",
		"<a href=\"admin_prize_control.php\">lock the prizes</a> so that you can begin prize drawings.  this will not allow you to add any more prizes, will not allow users to change registration of prizes, and is not reversable after you've drawn a prize.  be careful with this step.",
		"there are two methods to prize drawing: <a href=\"admin_prizes_print.php\">print off slips</a> for drawing of prizes from a hat, or <a href=\"admin_prize_draw.php\">draw prizes interactively</a>.  if you're using slips of paper, you're done, don't read on.",
		"once you're finished drawing a group of prizes, you must <a href=\"admin_prize_control.php\">display prize winners</a> to everyone.  this is optional if you decide to just read the prizes over a P.A. system.",
		"if someone leaves and isn't there to claim a prize, you can redraw the prize, but this will place them on an absentee blacklist.  you can <a href=\"admin_prize_control.php\">remove them from the absentee blacklist</a>.",
		);
	?>
	<table id="prizes_checklist" style="display: none" class="smm">
	<?php
	$counter = 1;
	foreach($checklist as $val) { ?>
		<tr><td valign="top">[<?php echo $counter; ?>]</td><td><?php echo $val; ?></td></tr>
		<?php
		$counter++;
	} ?>
	</table>
	<?php
} ?>