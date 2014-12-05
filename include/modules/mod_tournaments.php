<?php
global $dbc, $colors, $master, $toggle, $userinfo, $images;
$data = $dbc->database_query('SELECT tourneyid, name, marathon, lockstart FROM tournaments
						WHERE tentative=0 ORDER BY name');
?>
<span class="sm"><?php
while($row = $dbc->database_fetch_assoc($data)) { 
	get_arrow(($row['marathon']?'on':'off')); ?>&nbsp;
	<?php
	echo '<a href="tournaments.php?id='.$row['tourneyid'].'" class="menu">'.($row['lockstart']?'<font color="'.$colors['primary'].'">':'').'<strong>';
	echo (strlen($row['name'])>24?substr($row['name'],0,24).'...':$row['name']);
	echo '</strong>'.($row['lockstart']?'</font>':'').'</a> ';
	//echo ($row['marathon'] && $toggle['marath']?'<font color="'.$colors['primary'].'" size="1"><strong>*</strong></font>':'');
	echo '<br />';
} ?>
<br />
<?php if ($toggle['benchmarks']) { ?><?php get_arrow(); ?>&nbsp;<a href="benchmarks.php" class="menu"><strong><?php echo get_lang("bench_link"); ?></strong></a><br /><?php } ?>
<?php if ($toggle['caffeine']) { ?><?php get_arrow(); ?>&nbsp;<a href="caffeine.php" class="menu"><strong><?php echo get_lang("caffeine_log"); ?></strong></a><br /><?php } ?>
<?php
$result = $dbc->database_fetch_assoc($dbc->database_query('SELECT username FROM users WHERE userid='.(int)$master['marathonleader']));
$username = $result['username'];
if ($toggle['marath']) { ?><?php get_arrow(); ?>&nbsp;<a href="themarathon.php" class="menu"><strong><?php echo get_lang("marathon"); ?></strong></a><?php echo (!empty($username)?': '.$username:'&nbsp;'); ?><br /><?php } ?>
</span>