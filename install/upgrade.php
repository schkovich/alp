<?php
if(file_exists('DISABLED')) { echo 'install has been disabled because it has already been run.<br />If you wish to run it again, please delete the file /install/DISABLED'; exit();}

chdir('..');
require_once '_config.php';
require_once 'include/_functions.php';
chdir('install');
?>
<html>
<head><title>JANKY upgrade script</title></head>
<body bgcolor="#ffffff" text="#000000">
<font face="verdana" size="2">
<?php
$queries = array(
		// added in 0.98.0.1
		array(
			"INSERT INTO `games` (`name`, `short`, `current_version`, `url_update`, `url_maps`, `thumbs_dir`, `querystr2`) VALUES ('Star Wars Battlefront', 'swbf', '', '', '', '', 'gsqp2');"
			),
		// added in 0.98.1
		array(
			"CREATE TABLE `modules` (
  			`moduleid` int(10) unsigned NOT NULL auto_increment,
 			`file` varchar(45) NOT NULL default '',
 			`ordernum` int(10) unsigned NOT NULL default '0',
 			`description` varchar(45) NOT NULL default '',
 			`required` varchar(45) default NULL,
 			PRIMARY KEY  (`moduleid`));",
 			"INSERT INTO `modules` (`moduleid`,`file`,`ordernum`,`description`,`required`) VALUES   (1,'mod_music.php',4,'music','music');",
			"INSERT INTO `modules` (`moduleid`,`file`,`ordernum`,`description`,`required`) VALUES   (2,'mod_sponsors.php',5,'sponsor','sponsors');",
			"INSERT INTO `modules` (`moduleid`,`file`,`ordernum`,`description`,`required`) VALUES   (3,'mod_gamerofthehour.php',3,'gamer of hour','gamerofthehour');",
			"INSERT INTO `modules` (`moduleid`,`file`,`ordernum`,`description`,`required`) VALUES   (4,'mod_timeremaining.php',2,'time remaining',NULL);",
			"INSERT INTO `modules` (`moduleid`,`file`,`ordernum`,`description`,`required`) VALUES   (5,'mod_importantinfo.php',0,'important info',NULL);",
			"INSERT INTO `modules` (`moduleid`,`file`,`ordernum`,`description`,`required`) VALUES   (6,'mod_techsupport.php',6,'tech support','techsupport');",
			"INSERT INTO `modules` (`moduleid`,`file`,`ordernum`,`description`,`required`) VALUES   (7,'mod_shoutbox.php',7,'shoutbox','shoutbox');",
			"INSERT INTO `modules` (`moduleid`,`file`,`ordernum`,`description`,`required`) VALUES   (8,'mod_gamingrig.php',8,'gaming rigs','gamingrigs');",
			"INSERT INTO `modules` (`moduleid`,`file`,`ordernum`,`description`,`required`) VALUES   (9,'mod_attendance.php',1,'attendace',NULL);",
			"INSERT INTO `modules` (`moduleid`,`file`,`ordernum`,`description`,`required`) VALUES   (10,'mod_files.php',0,'files','files');"
		),
		// added in 0.98.2
		array(
		"INSERT INTO `modules` (`moduleid`,`file`,`ordernum`,`description`,`required`) VALUES   (11,'mod_messages.php',0,'user messaging','messaging');",
		"ALTER TABLE `toggle` ADD COLUMN `messaging` BOOL NOT NULL ;",
		"ALTER TABLE `master` ADD COLUMN `dateformat` varchar(45) NOT NULL;",
		"ALTER TABLE `master` ADD COLUMN `skin_override` BOOL NOT NULL DEFAULT '0';",
		"UPDATE `master` SET `dateformat` = 'h:i a - d M Y';",
        "ALTER TABLE `config` DROP `language`;",
		"ALTER TABLE `users` ADD COLUMN `dateformat` varchar(45) NOT NULL;",
		"ALTER TABLE `users` ADD COLUMN `language` varchar(45) NOT NULL;",
		"ALTER TABLE `users` ADD COLUMN `skin` varchar(45) NOT NULL;",
		"UPDATE `toggle` SET `messaging` = '1';",
		"CREATE TABLE `messages` (
				`messageid` BIGINT(20) UNSIGNED NOT NULL  AUTO_INCREMENT,
			 `to_userid` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
  			 `from_userid` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
  			 `time_stamp` TIMESTAMP NOT NULL,
  			 `subject` VARCHAR(255) NOT NULL DEFAULT '',
  			 `data` BLOB NOT NULL DEFAULT '',
  			 `read` ENUM('y','n') NOT NULL DEFAULT 'n',
  			 `deleted` ENUM('y','n') NOT NULL DEFAULT 'n',
  			 PRIMARY KEY(`messageid`),
  			 INDEX `Index_2`(`to_userid`, `deleted`),
  			 INDEX `Index_3`(`to_userid`, `read`, `deleted`),
  			 INDEX `Index_4`(`from_userid`)
			) TYPE=MyISAM;",
		),
		// added in 0.98.3
		array(
		"ALTER TABLE `tournament_matches_teams` MODIFY COLUMN `score` INT(20) DEFAULT NULL;",
		"ALTER TABLE `master` ADD COLUMN `voice_mode` VARCHAR(4) NOT NULL DEFAULT '' AFTER `music_max_queue`;",
		"ALTER TABLE `master` ADD COLUMN `voice_name` VARCHAR(45) NOT NULL DEFAULT '' AFTER `voice_mode`;",
 		"ALTER TABLE `master` ADD COLUMN `voice_ip` VARCHAR(45) NOT NULL DEFAULT '' AFTER `voice_name`;",
 		"ALTER TABLE `master` ADD COLUMN `voice_pass` VARCHAR(20) NOT NULL DEFAULT '' AFTER `voice_ip`;",
 		"INSERT INTO `modules` (file,ordernum,description,required) VALUES ('mod_voice.php','0','voice server monitor',NULL);",
        "ALTER TABLE `master` ADD `pizza_orders_lock` TINYINT( 1 ) DEFAULT '0' NOT NULL;",
        "ALTER TABLE `toggle` ADD COLUMN `pizza` BOOL NOT NULL ;",
        "CREATE TABLE `pizza` (
            `pizzaid` bigint(20) NOT NULL auto_increment,
            `pizza` varchar(150) NOT NULL default '',
            `description` varchar(255) NOT NULL default '',
            `price` decimal(20, 2) NOT NULL default '0',
            `enabled` tinyint(1) NOT NULL default '0',
            UNIQUE KEY `id` (`pizzaid`),
            KEY `pizza` (`pizza`)
            ) TYPE=MyISAM;",
        "CREATE TABLE `users_pizza` (
            `id` bigint(20) NOT NULL auto_increment,
            `userid` bigint(20) NOT NULL default '0',
            `pizzaid` varchar(150) NOT NULL default '0',
            `quantity` bigint(20) NOT NULL default '0',
            `paid` tinyint(1) NOT NULL default '0',
            `delivered` tinyint(1) NOT NULL default '0',
            PRIMARY KEY  (`id`),
            UNIQUE KEY `id` (`id`)
            ) TYPE=MyISAM;",
 		),
		);
		
	// fill up to include the newest version.
	$versions = array(
		-1 => "0.98.0.0",
		0 => "0.98.0.1",
		1 => "0.98.1",
		2 => "0.98.2",
		3 => "0.98.3"
		);

if(!empty($_GET["runqueries"])&&$_GET["runqueries"]==1) { ?>
	<table border=0 style="font-size: 11px">
	<tr><td><b>query:</b></td><td>&nbsp;</td></tr>
	<?php
	if(!empty($_GET["from"])) $temp = $_GET["from"];
	else $temp = 0;
	if($temp<sizeof($queries)) {
		$allgood = true;
		if(!$dbc->database_query("UPDATE master SET alpver='".$versions[(sizeof($versions)-2)]."'")) {
			$allgood = false;
		}
		$qcounter = 0;
		for($i=$temp;$i<sizeof($queries);$i++) {
			foreach($queries[$i] as $query) { ?>
				<tr>
					<td><?php echo $query; ?></td>
					<td>&nbsp;&nbsp;&nbsp;<b><?php
					if($dbc->database_query($query)) {
						echo $versions[$i]." <font color=#00ff00>success.</font>";
					} else {
						echo $versions[$i]." <font color=#ff0000>error!</font>";
						$allgood = false;
					} ?>
					</b></td></tr>
				</tr>
				<?php
				$qcounter++;
			}
		} ?>
		<tr><td colspan=2><br /><?php
		if($allgood) {
			echo "update successful, you lucky bastard.";
		} else {
			echo "there was an error with your update.  you may have chosen the wrong previous version to upgrade from.  you may already have the newest version.  if all your ALTER TABLE commands gave errors only on the lower version, you probably already have the newest version.  otherwise if you're knowledgable with mysql you can muck around in the tables, but i'd recommend starting from scratch.  sorry.";
		}
		?></td></tr>
		<?php
	} else {
		echo "<tr><td colspan=2>invalid filter</td></tr>";
	}
	?>
	</table>
	<?php
} else { ?>
	<form action="<?php echo get_script_name(); ?>" method="GET">
	warning: are you using the most recent _config.php or did you copy the one from your old version?<br />
	<br />
	warning: this may cause you errors if you input the wrong version here. be careful.<br />
	<br />
	<b>current version: <?php echo $master['alpver']; ?></b> (according to ALP)<br />
	what do you say your current version is? <select name="from">
		<?php
		for($i=-1;$i<(sizeof($versions)-2);$i++) { ?>
			<option value="<?php echo ($i+1); ?>"<?php echo (!empty($_GET["from"])&&$_GET["from"]==($i+1) || $versions[$i]==$master['alpver']?" selected":""); ?>><?php echo $versions[$i]; ?></option>
			<?php
		} ?>
	</select> <input type="submit" value="filter out unneeded queries">
	</form>
	<br />
	<table border=0 style="font-size: 11px">
	<tr><td><b>queries:</b></td><td><b>added in version</b></td></tr>
	<?php
	if(!empty($_GET["from"])) $temp = $_GET["from"];
	else $temp = 0;
	if($temp<sizeof($queries)) {
		for($i=$temp;$i<sizeof($queries);$i++) {
			foreach($queries[$i] as $query) { ?>
				<tr>
					<td><?php echo $query; ?></td>
					<td><?php echo $versions[$i]; ?></td>
				</tr>
				<?php
			}
		} ?>
		<tr><td><?php echo "UPDATE master SET alpver='".$versions[(sizeof($versions)-2)]."'"; ?></td><td><?php echo $versions[(sizeof($versions)-2)]; ?></td></tr>
		<tr><td colspan=2><br />
			<form action="<?php echo get_script_name(); ?>" method="GET">
			<input type="hidden" name="runqueries" value="1">
			<input type="hidden" name="from" value="<?php echo $temp; ?>">
			make sure you have the right current version!  &gt;&gt; <input type="submit" value="attempt to run these queries">
			</form>
			</td></tr>
		<?php
	} else {
		echo "<tr><td colspan=2>invalid filter</td></tr>";
	}
	?>
	</table>
	<?php
} ?>
</body>
</html>
