<?php
global $dbc, $images, $colors, $master, $toggle, $userinfo, $start, $end, $container;
if ($toggle['foodrun']) {
	$holder = " UNION (SELECT itemtime,headline,-1,0 FROM foodrun WHERE itemtime>NOW() AND (UNIX_TIMESTAMP(itemtime)-UNIX_TIMESTAMP())<3600 LIMIT 4)";
	$foodruns = $dbc->database_num_rows($dbc->database_query("SELECT itemtime,headline,-1 FROM foodrun WHERE itemtime>NOW() AND (UNIX_TIMESTAMP(itemtime)-UNIX_TIMESTAMP())<3600 LIMIT 4"));
} else {
	$holder = '';
	$foodruns = 0;
}
$tournaments = $dbc->database_num_rows($dbc->database_query("SELECT itemtime,name,tourneyid FROM tournaments WHERE itemtime>NOW() AND (UNIX_TIMESTAMP(itemtime)-UNIX_TIMESTAMP())<3600"));
if ($tournaments == 0) {
	$tournaments = 4;
}
$schedules = $dbc->database_num_rows($dbc->database_query("SELECT itemtime,headline,0 FROM schedule WHERE itemtime>NOW() AND itemtime_priv<=".current_security_level()." AND (UNIX_TIMESTAMP(itemtime)-UNIX_TIMESTAMP())<3600"));
if ($schedules == 0) {
	$schedules = 4;
}
$limit = $tournaments + $schedules + $foodruns;

$query = "(SELECT itemtime,name,tourneyid,0 AS itemtime_priv FROM tournaments WHERE itemtime>NOW() AND (UNIX_TIMESTAMP(itemtime)-UNIX_TIMESTAMP())<3600) UNION (SELECT itemtime,headline,0,itemtime_priv FROM schedule WHERE itemtime>NOW() AND itemtime_priv<=".current_security_level()." AND (UNIX_TIMESTAMP(itemtime)-UNIX_TIMESTAMP())<3600)".$holder." ORDER BY itemtime LIMIT ".$limit.";";
$data = $dbc->database_query($query);
$counter = 0; ?>
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<?php
while ($row = $dbc->database_fetch_assoc($data)) { 
	if ((date('U',strtotime($row['itemtime']))-date('U')) < 3600 && $row['tourneyid'] >= 0) { ?>
		<tr><td colspan="2"><img src="img/pxt.gif" width="1" height="2" border="0" alt="" /></td></tr>
		<tr><td colspan="2" bgcolor="<?php echo $colors["primary"]; ?>"><img src="img/pxt.gif" width="1" height="1" border="0" alt="" /></td></tr>
		<?php
	} ?>
	<tr><td colspan="2">
	<?php
	if ((date('U',strtotime($row['itemtime']))-date('U')) < 3600 && $row['tourneyid'] >= 0) {
		$length = 21; 
		echo ($row['tourneyid']>0?"<a href=\"disp_teams.php?id=".$row['tourneyid']."\">":'')."<font color=\"".$colors['primary']."\"><b>";
	} else { 
		$length = 26; ?>
		<?php get_arrow(); ?>&nbsp;<?php echo ($row["tourneyid"]>0?"<a href=\"disp_teams.php?id=".$row['tourneyid']."\">":''); ?><font class="sm" style="color: <?php echo $colors['blended_text']; ?>"><?php
	}
	echo ($row['tourneyid']==-1?'food: ':'')?><?php echo ($row["tourneyid"]>0?"<u>":''); ?><b><?php echo substr($row['name'],0,$length).(strlen($row["name"])>($length-1)?"...":""); ?></b><?php echo ($row['tourneyid']>0?"</u>":''); ?></font><?php echo ($row['tourneyid']>0?"</a>":''); ?>	<font class="smm"><?php echo ($row['itemtime_priv']==2?' (a)':($row['itemtime_priv']==3?' (sa)':'')); ?></font><br />
	<?php
	if((date('U',strtotime($row['itemtime']))-date('U'))<3600) { ?>
		<font class="sm"><?php echo ($row['tourneyid']<0?"<font color=\"".$colors['blended_text']."\">":""); get_arrow(); ?>&nbsp;<?php echo disp_datetime(strtotime($row['itemtime']), 2); ?> // eta / <?php echo round((date('U',strtotime($row['itemtime']))-date('U'))/60); ?> minutes<br /><?php echo ($row['tourneyid']<0?"</font>":''); ?></font>
		<?php
	} ?>
	<img src="img/pxt.gif" width="1" height="1" border="0" alt="" /><br />
	</td></tr>
	<?php
	if ((date('U',strtotime($row['itemtime']))-date('U')) < 3600 && $row['tourneyid'] >= 0) { ?>
		<tr><td colspan="2" bgcolor="<?php echo $colors['primary']; ?>"><img src="img/pxt.gif" width="1" height="1" border="0" alt="" /></td></tr>
		<tr><td colspan="2"><img src="img/pxt.gif" width="1" height="2" border="0" alt="" /></td></tr>
		<?php
	}
	$counter++;
} ?>
</table>
<img src="img/pxt.gif" width="1" height="4" border="0" alt="" /><br />
<div align="right"><font class="sm">[<a href="disp_schedule.php"><b><?php echo get_lang("view_all"); ?></b></a>]</font></div>
