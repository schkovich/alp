<?php
global $container, $toggle, $images, $dbc; ?>
<script language="JavaScript">
<!-- 
function goToSide() {
	if(document.menuSide.go.value!="") document.location.href = document.menuSide.go.value;
} // -->
</script>
<?php
	// (url to go to, name to display, minimum security level)
$menu = array(
	array('','',2),
	array('admin_toggle.php','toggle main features',2),
	array('admin_modules.php','manage index modules',2),
);
if ($toggle['satellite']) $menu[] = array('admin_satellite.php','satellites',2);
$menu[] = array('','',2);
$menu[] = array('',' >>>> miscellaneous',2);
$menu[] = array('admin_deleteuser.php','delete user',3);
$menu[] = array('admin_resetpassword.php','reset user password',3);
if(!ALP_TOURNAMENT_MODE) $menu[] = array('admin_paid.php','paid gamers',2);
$menu[] = array('admin_priv.php','user privileges',3);
$menu[] = array('','',2);
$menu[] = array('',' >>>> tournaments',2);
$menu[] = array('admin_tournament.php','add tournament',2);
$menu[] = array('admin_teams.php','add teams',2);
$menu[] = array('admin_teams_delete.php','delete teams'.(!ALP_TOURNAMENT_MODE?'/players':''),2);
$menu[] = array('admin_seeding.php','seeding',2);
if(!ALP_TOURNAMENT_MODE) $menu[] = array('admin_profic.php','gamer proficiencies',2);
if(!ALP_TOURNAMENT_MODE || ALP_TOURNAMENT_MODE_COMPUTER_GAMES) $menu[] = array('admin_games.php','games',2);
if(!ALP_TOURNAMENT_MODE) $menu[] = array('admin_mapvoting.php','map voting',2);
if(!ALP_TOURNAMENT_MODE || ALP_TOURNAMENT_MODE_COMPUTER_GAMES) $menu[] = array('admin_teams_type.php',' - team types',2);
$menu[] = array('','',2);
if(( ALP_TOURNAMENT_MODE && $toggle['schedule'] ) || !ALP_TOURNAMENT_MODE) $menu[] = array('',' >>>> information',2);
if(!ALP_TOURNAMENT_MODE) {
	if ($toggle['foodrun']) $menu[] = array('admin_foodrun.php','foodruns',2);
    if ($toggle['pizza']) $menu[] = array('pizza.php','pizza orders',2);
    if ($toggle['pizza']) $menu[] = array('admin_pizza_list.php',' - pizza orders summary',2);
    if ($toggle['pizza']) $menu[] = array('admin_pizza.php',' - add/mod pizza orders',2);
    if ($toggle['pizza']) $menu[] = array('chng_pizza.php',' - add/mod pizza types',2);
	if ($toggle['gamerequests']) $menu[] = array('admin_gamerequest.php','game requests',2);
	$menu[] = array('admin_lodging.php','lodging',2);
	$menu[] = array('admin_poll.php','polls',2);
	if ($toggle['sponsors']) $menu[] = array('admin_sponsors.php','sponsors',2);
	if ($toggle['prizes']) $menu[] = array('admin_prizes.php','prize database',2);
	if ($toggle['prizes']) $menu[] = array('admin_prize_control.php',' - prize control panel',2);
	if ($toggle['prizes']) $menu[] = array('admin_prize_draw.php',' - draw prizes interactively',2);
	if ($toggle['prizes']) $menu[] = array('admin_prizes_print.php',' - print prizes for drawing',2);
}
if ($toggle['schedule']) $menu[] = array('admin_schedule.php','schedule',2);
if(!ALP_TOURNAMENT_MODE) {
	if ($toggle['seating']) $menu[] = array('seating.php','seating map',2);
	if ($toggle['foodrun']) $menu[] = array('admin_restaurant.php','restaurants',2);
	$menu[] = array('admin_users.php','user profiles',2);
	$menu[] = array('admin_gamingrig.php','user gaming rigs',2);
    if ($toggle['staff']) $menu[] = array('staff.php','staff',2);
    if ($toggle['staff']) $menu[] = array('admin_staff.php',' - add/mod data',2);
    if ($toggle['staff']) $menu[] = array('chng_staff.php',' - add/mod fields',2);
	
	if ($toggle['benchmarks'] || $toggle['caffeine']) { 
		$menu[] = array('','',2);
		$menu[] = array('',' >>>> competitions',2);
		if ($toggle['caffeine']) $menu[] = array('admin_caffeine_cheaters.php','cheaters /caffeine',2);
		if ($toggle['benchmarks']) $menu[] = array('admin_benchmark_cheaters.php','cheaters /benchmarks',2);
		if ($toggle['benchmarks']) $menu[] = array('admin_benchmarks.php','benchmarks',2);
		if ($toggle['caffeine']) $menu[] = array('admin_caffeine.php','caffeine items',2);
		if ($toggle['caffeine']) $menu[] = array('admin_caffeine_types.php',' - caffeine item types',2);
	}
}
$currentfile = basename(get_script_name()); ?>
<img src="img/pxt.gif" width="1" height="4" border="0"><br />
<form name="menuSide">
<select name="go" style="width: <?php echo $this->get_inner_width(); ?>; font: 10px Verdana" onChange="goToSide()">
<?php
foreach($menu as $val) { 
	if (current_security_level() >= $val[2]) { ?>
		<option value="<?php echo $val[0]; ?>"<?php echo (!empty($val[0])&&$val[0]==$currentfile?' selected':''); ?>><?php echo $val[1]; ?></option>
		<?php
	}
} 

if (current_security_level() >= 2) {
$emails = $dbc->query('SELECT `email` FROM `users` WHERE `email` != ""');
if ($emails->numRows() > 0) {
	$link = " <a href=\"mailto:";
	while($row = $emails->fetchRow()) {
		$email = $row["email"];
		$link .= "$email; ";
	}
	$link .= "\" class=\"menu\">email all users</a>";
} else {
	$link = " email all users";
}
}
?>
</select>
</form>
<img src="img/pxt.gif" width="1" height="4" border="0"><br />
<table border=0 cellpadding=0 cellspacing=0 width="100%" class="sm">
<tr><td width="50%"><nobr><?php get_arrow(); ?>&nbsp;<a href="admin_config.php" class="menu"><b>settings</b></a></td><td width="50%"><nobr><?php get_arrow(); ?>&nbsp;<a href="admin_news.php" class="menu"><b>news</b></a></td></tr>
<tr><td width="50%"><nobr><?php get_arrow(); ?>&nbsp;<a href="admin_index.php" class="menu"><b>more settings</b></a>
<?php 
if(!ALP_TOURNAMENT_MODE) { ?>
		<td><nobr><?php get_arrow(); ?>&nbsp;<a href="admin_serverlist.php" class="menu">servers</a></td>
		<?php
		/* <td><?php if($toggle["satellite"]) { ?><nobr><?php get_arrow(); ?>&nbsp;<a href="admin_satellite.php" class="menu">satellites</a><?php } else { ?>&nbsp;<?php } ?></td>
		 */
} ?>
</tr>
<tr><td colspan="2"><nobr><?php get_arrow(); ?>&nbsp;<a href="admin_tournament.php" class="menu"><b>tournaments</b></a>: <a href="admin_tournament_start.php"><b>start</b></a> <a href="admin_tournament_unstart.php">un-start</a></td></tr>
<tr><td colspan="2"><?php if (current_security_level() >= 2) { get_arrow(); echo $link;} ?></td></tr>
</table>
