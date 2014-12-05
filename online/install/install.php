<?php
if(file_exists('DISABLED')) { echo 'install has been disabled because it has already been run.<br />If you wish to run it again, please delete the file /install/DISABLED'; exit();}

@error_reporting(E_ALL ^ E_NOTICE); // This will NOT report uninitialized variables
chdir('..');
@include_once '_config.php';
require_once 'include/genesis_include.php';

$dbc = new genesis();

if(!empty($language)&&is_dir("../include/lang/".$language)) {
	$temp = $language;
} else {
	$temp = "en";
}
include_once 'include/lang/get_lang.php';
include_once 'include/lang/'.$temp.'/install__install.php'; ?>
<html>
<head><title><?php echo get_lang("install"); ?></title></head>
<body bgcolor="#ffffff" text="#000000">
<font face="verdana" size="2">
<?php
if(empty($_GET['s'])) {
?>
	<font color="#0000ff">&lt;<b><?php echo get_lang("install"); ?></b>: <?php echo get_lang("stepone"); ?>&gt;<br /></font>
	<br />
	<ul style="margin: 15px 15px 15px 15px;">
	<?php
	if($_POST['submit']) {
		$database['type']     = $_POST['type'];	
		$database['user']     = $_POST['user'];			
		$database['passwd']   = $_POST['passwd'];		
		$database['database'] = $_POST['db'];			
		$database['server']	  = $_POST['server'];
	}
	//Check for blank settings.
	if(!$database['type']) $fail = 1;
	if(!$database['user']) $fail = 1;
	if(!$database['database']) $fail = 1;
	if(!$database['server']) $fail = 1;
	if($_GET['fail']) $fail = 1;
	
	if(!$db = $dbc->database_connect($database['server'],$database['user'],$database['passwd']) && !$fail) {
			if($_POST['submit'] && !fail) { $dbc->error('Unable to connect to database'); }
			//Ask for database info.
			echo '<br /><br />' . get_lang("stepone_description"); ?><br /><br />
<form id="database" name="database" method="post" action="install.php">
	<table width="362" border="0" cellspacing="0" cellpadding="3">
		<tr>
			<td align="right"><strong>Database Type: </strong></td>
			<td align="left"><select name="type" id="type">
				<option value="mysql"<?php echo ($database['type']=='mysql'?' selected':'') ?>>MySQL</option>
				<option value="mysqli"<?php echo ($database['type']=='mysqli'?' selected':'') ?>>MySQLi</option>
			</select>			</td>
		</tr>
		<tr>
			<td align="right"><strong>Server Address: </strong></td>
			<td align="left"><input name="server" type="text" id="server" value="<?php if($database['server']) {echo $database['server']; } else {echo 'localhost';} ?>" size="30" maxlength="70" /></td>
		</tr>
		<tr>
			<td align="right"><strong>Username: </strong></td>
			<td align="left"><input name="user" type="text" id="user" size="30" value="<?php echo $database['user']; ?>" maxlength="70" /></td>
		</tr>
		<tr>
			<td align="right"><strong>Password:</strong></td>
			<td align="left"><input name="passwd" type="password" id="passwd" size="30" value="<?php echo $database['passwd']; ?>" maxlength="70" /></td>
		</tr>
		<tr>
			<td align="right"><strong>Database:</strong></td>
			<td align="left"><input name="db" type="text" id="db" size="30" value="<?php echo $database['database']; ?>" maxlength="70" /></td>
		</tr>
		<tr>
			<td align="right">&nbsp;</td>
			<td align="left"><input name="submit" type="submit" id="submit" value="Submit" /></td>
		</tr>
	</table>
</form>
	<?php
	} else {
		echo get_lang('stepone_passed');
		$data = "<?php" .
				"\n//This is the new config file, almost all settings have been moved to the database." .
				"\n//You no longer need to edit this file," .
				"\n//unless the installer is not able to edit this file because of permissions." .
				"\n//Just run the installer at /install/install.php" .
				"\n" .
				"\n//Database server connection settings" .
				"\n\$database['type']     = '" . $database['type'] . "';" .
				"\n\$database['user']     = '" . $database['user'] . "';" .
				"\n\$database['passwd']   = '" . $database['passwd'] . "';" .
				"\n\$database['database'] = '" . $database['database'] . "';" .
				"\n\$database['server']   = '" . $database['server'] . "';" .
				"\n?>";
		@chmod('_config.php', 0660);
		if($handle = @fopen('_config.php', 'w')) {
			fwrite($handle, $data);
			fclose($handle);	
		} else {
			if($db && !$fail) {
				echo '<br /><br />the install script was NOT able to write to _config.php to save the database settings';
				echo '<br />please give write permission to _config.php and rerun this install script.';
				echo '<br />or manually edit _config.php and paste the code from below into _config.php';
				echo '<br />this must be done before you can continue with this install script.';
				echo '<br />once you have done this, click on the link below to continue.';
				echo '<br /><br />copy and paste code for _config.php:';
				echo '<pre>' . htmlentities($data) . '</pre>';
			}
		}
	?>
	<br /><br />	
	<span style="font-size: 11px"><b><a href="install.php?s=2"><?php echo get_lang("stepone_next"); ?></a>.<br /><br /></b></span>
	<span style="font-size: 11px"><b><a href="install.php?fail=1"><?php echo get_lang("stepone_repeat"); ?></a>.<br /></b></span>
	<?php
	} ?>
	</ul>
	<font color="#0000ff">&lt;/<b><?php echo get_lang("install"); ?></b>: <?php echo get_lang("stepone"); ?>&gt;<br /></font>
	<?php
} elseif($_GET["s"]==2) {
	include 'include/cl_validation.php';
	$valid = new validate(); ?>
	<font color="#0000ff">&lt;<b><?php echo get_lang("install"); ?></b>: <?php echo get_lang("steptwo"); ?>&gt;<br /></font>
	<br />
	<ul style="margin: 15px 15px 15px 15px;">
	<table border=0 style="font-size: 11px">
	<?php
	$counter = 1;
	echo "<tr><td valign=top>_config.php variable (<b>".get_lang("steptwo_varfive")."</b>):</td><td valign=top><b>".$database["user"]."<br /></b><i>password hidden</i><br /><b>".$database["server"]."<br /></b></td><td valign=top>&nbsp;&nbsp;&nbsp;<b>";
	if($dbc->database_connect($database['server'],$database['user'],$database['passwd'])) {
		echo "<font color=#00ff00>".get_lang("success")."</font>";
	} else {
		echo "<font color=#ff0000>".get_lang("failure")." (".$counter.")</font>";
		$valid->add_error(get_lang("steptwo_varfive_error")." \$database[\"user\"], \$database[\"passwd\"], \$database[\"server\"]");
		$counter++;
	}
	echo "</b></td></tr>"; 
	echo "<tr><td valign=top>_config.php variable (<b>".get_lang("steptwo_vartwelve")."</b>):</td><td valign=top><b>".$database["database"]."</b></td><td valign=top>&nbsp;&nbsp;&nbsp;<b>";
	if($dbc->database_select_db($database["database"])) {
		echo "<font color=#00ff00>".get_lang("success")."</font>";
	} else {
		echo "<font color=#ff9900>".get_lang("warning")." ".get_lang("steptwo_vartwelve_error")."</font>";
	}
	echo "</b></td></tr>"; 
	
	echo "<tr><td></td></tr>";
	
	echo "<tr><td valign=top>".get_lang("steptwo_vareleven").":</td><td valign=top><b>".(ini_get("error_reporting"))."</b></td><td>&nbsp;&nbsp;&nbsp;<b>";
	if(ini_get("error_reporting")==2039) {
		echo "<font color=#00ff00>".get_lang("success")."</font>";
	} else {
		echo "<font color=#ff9900>".get_lang("warning")." ".get_lang("steptwo_vareleven_error")."</font>";
	}
	echo "</b></td></tr><tr><td colspan=3>&nbsp;</td></tr>";
	
	echo "<tr><td valign=top>".get_lang("steptwo_varsix").":</td><td valign=top><b>".(ini_get("magic_quotes_gpc")?get_lang("on"):get_lang("off"))."</b></td><td>&nbsp;&nbsp;&nbsp;".get_lang("nolongerrequired")."</td></tr>";
	echo "<tr><td colspan=3>&nbsp;</td></tr>";
	
	echo "<tr><td valign=top>".get_lang("steptwo_vareight").":</td><td valign=top><b>".(ini_get("short_open_tag")?get_lang("on"):get_lang("off"))."</b></td><td>&nbsp;&nbsp;&nbsp;".get_lang("nolongerrequired")."</td></tr>";
	echo "<tr><td colspan=3>&nbsp;</td></tr>";

	echo "<tr><td valign=top>".get_lang("steptwo_varnine").":</td><td valign=top><b>".(ini_get("register_globals")?get_lang("on"):get_lang("off"))."</b></td><td>&nbsp;&nbsp;&nbsp;".get_lang("nolongerrequired")."</td></tr>";
	echo "<tr><td colspan=3>&nbsp;</td></tr>";
	?>
	<tr><td colspan=3>&nbsp;</td></tr>
	</table>
	<?php
	if(!$valid->is_error()) { ?>
		<br />
		<span style="font-size: 11px"><b><a href="install.php?s=3"><?php echo get_lang("steptwo_next"); ?></a>.<br /></b></span>
	<?php
	} else {
		echo "<br /><br /><span style=\"font-size: 11px\">";
		echo "<font color=#ff0000><b>".get_lang("errors").": </b></font><br />";
		$err = $valid->get_errors();
		for($i=0;$i<sizeof($err);$i++) {
			echo "&nbsp;&nbsp;".($i+1).")&nbsp; ".$err[$i]."<br />";
		}
		echo "<br />".get_lang("steptwo_redo")."<br /></span>";
	} ?>
	</ul>
	<br /><font color="#0000ff">&lt;/<b><?php echo get_lang("install"); ?></b>: <?php echo get_lang("steptwo"); ?>&gt;<br /></font>
	<?php
} elseif($_GET["s"]==3) { ?>
	<?php
	$allgood = true;
	 ?>
	<font color="#0000ff">&lt;<b><?php echo get_lang("install"); ?></b>: <?php echo get_lang("stepfour"); ?>&gt;<br /></font>
	<br />
	<ul style="margin: 15px 15px 15px 15px;">
	<table border=0 style="font-size: 11px">
	<?php
	$dbc->database_connect($database['server'],$database['user'],$database['passwd']);
	if(!$dbc->database_select_db($database["database"])) { ?>
		<tr>
			<td><?php echo get_lang("stepfour_creatingdatabase"); ?></td>
			<td><?php
             if($dbc->database_query('CREATE DATABASE ' . $database["database"])&&$dbc->database_select_db($database["database"])) {
					echo "<font color=#00ff00>".get_lang("success")."</font>";
				} else { echo "<font color=#ff0000>".get_lang("failure")."</font>"; $allgood = false; } ?></td>
		</tr>
		<?php
	} 
	include "_create_queries.php";
	foreach($create_table_queries as $tkey => $pquery) { ?>
		<tr>
			<td><?php echo get_lang("stepfour_newtable"); ?>: <?php echo $tkey; ?>&nbsp;&nbsp;</td>
			<td><?php if($dbc->database_query("DROP TABLE IF EXISTS ".$tkey.";")&&$dbc->database_query("CREATE TABLE ".$tkey."(".$pquery.") TYPE=MyISAM;")) {
						echo "<font color=#00ff00>".get_lang("success")."</font>";
					} else { echo "<font color=#ff0000>".get_lang("failure")."</font>"; $allgood = false; } ?></td>
		</tr>
		<?php
	}
	echo "<tr>";
	if($dbc->query("INSERT INTO `settings` SET	`pw` = '21232f297a57a5a743894a0e4a801fc3';"))
		echo "Admin password set to deafult \"Admin\"...edit your settings to change it ";
	else
		echo "Unable to insert default admin pass...please manually insert a MD5 encoded password";	
	?>
	</tr>
	<tr>
		<td colspan=2><br />
			<b><?php echo ($allgood?"<font color=#00ff00>".get_lang("stepfour_success")."</font><br /><br /><b>"."</b><br /><br /><a href=\"../admin/login.php\">".get_lang("stepfour_next")."</a>.":"<font color=#ff0000>".get_lang("stepfour_redo")."</font>"); ?></b><br />
		</td>
	</tr>
	</table>
	</ul>
	<?php
	//Disable install from being able to run again.
	@chmod('install', 0770);
	if($handle = @fopen('install/DISABLED', 'w')) {
		fwrite($handle, '1');
		fclose($handle);
	} else {
		echo '<strong>Note: </strong> this script was unable to secure alp by adding file named DISABLED to /install';
		echo '<br />either create a file named DISABLED in the /install directory';
		echo '<br />or delete the install directory';
		echo '<br /><br /><br />';
	}
	?>
	<font color="#0000ff">&lt;/<b><?php echo get_lang("install"); ?></b>: <?php echo get_lang("stepfour"); ?>&gt;<br /></font>
	<?php
}
?>
</font>
</body>
</html>
