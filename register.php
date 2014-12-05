<?php
require_once 'include/_universal.php';
$x = new universal('register for an account','register',0);
if ($master['ip_register_lock']) {
	$ip_registered = $dbc->database_num_rows($dbc->database_query("SELECT * FROM users WHERE recent_ip='".$_SERVER['REMOTE_ADDR']."'"));
} else {
	$ip_registered = 0;
}
//Check to make sure /install is safe.
if (file_exists('install/install.php')&&!file_exists('install/DISABLED')) {
	$x->display_top(); ?>
	<font color="#ff0000"><strong>/install/DISABLED file must exist so that the alp install can not be accessed by any user to erase the entire database later on. <br />please create a file called DISABLED (all caps) in the /install directory.<br />OR delete the install directory.</strong></font><br />
	<br />
	<?php
	$x->display_bottom();
} else {
if ($x->is_secure()&&!$ip_registered) {
	$x->display_top();
	$users_exist = $dbc->database_num_rows($dbc->database_query('SELECT * FROM users'));
	if (empty($_POST)) { ?>
		<strong><?php
		if(!ALP_TOURNAMENT_MODE) {
			?>register<?php
			//echo (!$users_exist&&$_GET['s']==md5(date('z'))?' as super administrator':'');
			echo (!$users_exist)?' as super administrator':''; 
		} else {
			if($users_exist) echo 'apply to be a moderator';
			else echo 'register';
		} ?></strong>:<br />
		<br />
		<?php
		if (current_security_level()>=1) { echo "you are already logged with an account!  are you sure you want to register for another one?<br /><br />"; } ?>
		<script language="javascript" src="include/_md5.js"></script>
		<script language="javascript">
		<!-- 
		function doLogin() { 
			if(document.register.passwd.value != "") document.register.passwd.value = calcMD5(document.register.passwd.value);
			if(document.register.passwd_confirm.value != "") document.register.passwd_confirm.value = calcMD5(document.register.passwd_confirm.value);
			document.register.javascript.value = "yes";
		} 
		// --> 
		</script>
		<table border=0 cellpadding=4 cellspacing=4 width="400" align="center" class="centerd"><tr><td>
		<form action="<?php echo get_script_name(); ?>" method="POST" name="register">
		<input type="hidden" name="javascript" value="">
		<?php begitem('required information'); ?>
		<font size=1><strong>username</strong> (can only consist of letters, numbers, and underscores. maximum length is 40 characters)<br /></font>
		<input type="text" name="username" size=40 maxlength=40 style="width:99%"><br />
		<?php
		if ($master['doublecheckpassword']) {
			if ($master['authbyiponly']) { ?>
				<br />the administrator has set this software to use IP address authentication instead of passwords.  however, you are required to input a password here just in case
				there are technical problems with DHCP or other things.<br /><br />
				<?php
			}
		}
		if (($master['doublecheckpassword'] && $master['authbyiponly']) || !$master['authbyiponly']) { ?>
			<font size=1><strong>password</strong><br /></font>
			<input type="password" name="passwd" maxlength=34 style="width:99%"><br />
			<font size=1><strong>confirm password</strong><br /></font>
			<input type="password" name="passwd_confirm" maxlength=34 style="width:99%"><br />
			<?php
		} ?>
		<font size=1><br /><strong>first name</strong><br /></font>
		<input type="text" name="first_name" maxlength=30 style="width:99%"><br />
		<font size=1><strong>last name</strong><br /></font>
		<input type="text" name="last_name" maxlength=30 style="width:99%"><br />
		<br />
		<?php 
		if(!ALP_TOURNAMENT_MODE) {
			enditem('required information');
			begitem('optional information'); ?>
			<font size=1><strong>how many of the other gamers at the lan party do you think you can kill in a one-on-one in your favorite game?</strong> (for random team tournaments -- be honest)<br /></font>
			<table border=0 width=100%>
				<tr style="color: #000000">
				<?php
				$c = array('f','d','c','a',9,8,7,5,3,2,0);
				for ($i=0; $i <= 10; $i++) { ?>
					<td bgcolor="#<?php for($j=0;$j<6;$j++) { echo $c[$i]; } ?>"><?php spacer(1,5); ?></td>
					<?php
				} ?>
				</tr>
				<tr style="color: #000000">
				<?php
				for ($i=0; $i <= 10; $i++) {
					$temp = getimagesize('img/percent/'.$colors['image_text'].'_'.$i.'.gif'); ?>
					<td><input type="radio" name="proficiency" class="radio" value="<?php echo $i; ?>"<?php echo ($i==5?' checked':''); ?>><img src="img/percent/<?php echo $colors['image_text'].'_'.$i; ?>.gif" border="0" width="<?php echo $temp[0]; ?>" height="<?php echo $temp[1]; ?>"></td>
					<?php
				} ?>
				</tr>
			</table>
			<font size=1><br /><strong>allow others to see your ip address?</strong> (for windows sharing or ftp server links)<br /></font>
			<input type="radio" name="display_ip" value="0" class="radio" checked> no. <input type="radio" name="display_ip" value="1" class="radio"> yes.<br />
			<?php
		}
		if( (ALP_TOURNAMENT_MODE && $master['internetmode']) || !ALP_TOURNAMENT_MODE) { ?>
			<font size=1><strong>email</strong><?php 
			if(!ALP_TOURNAMENT_MODE) { 
				?> (please input if you wish to be informed of future events)<?php 
			}
			?><br /></font>
			<input type="text" name="email" maxlength=60 style="width:99%"><br />
			<?php
		}
		if(!ALP_TOURNAMENT_MODE) { ?>
			<font size=1><strong>allow others to see your email address?</strong><br /></font>
			<input type="radio" name="display_email" value="0" class="radio" checked> no. <input type="radio" name="display_email" value="1" class="radio"> yes.<br />
			<br />
			<font size=1><strong>gaming group</strong> <br /></font>
			<input type="text" name="gaming_group" maxlength=20 style="width:99%"><br />
			<font size=1><strong>gender</strong><br /></font>
			<input type="radio" name="gender" value="female" class="radio"> female <input type="radio" name="gender" value="male" class="radio"> male <input type="radio" name="gender" value="" class="radio" checked> anonymous<br />
			<br />
			<?php 
			enditem('optional information'); 
		} else { ?>
			<br />
			<?php
			enditem('required information');
		} ?>
		<div align="right"><input type="submit" value="register"<?php if (($master['doublecheckpassword'] && $master['authbyiponly']) || !$master['authbyiponly']) { ?> onClick="doLogin(); return true;"<?php } ?> style="width: 160px" class="formcolors"></div>
		</form>
		<br />
		<font size=1>for maximum security, enable javascript. &nbsp;do not be alarmed, your password will autoencrypt when you click the register button. &nbsp;this is to prevent sniffing of passwords on the local network.</font><br />
		</td></tr></table>
		<?php
	} else {
		require_once 'include/cl_validation.php';
		$valid = new validate();
		if (($master['doublecheckpassword'] && $master['authbyiponly']) || !$master['authbyiponly']) {
			if ($valid->get_value('javascript') == '') {
				if ($valid->get_value('passwd') != '') $valid->set_value('passwd',md5($valid->get_value('passwd')));
				if ($valid->get_value('passwd_confirm') != '') $valid->set_value('passwd_confirm',md5($valid->get_value('passwd_confirm')));
			}
		}

		$valid->is_empty('username','the username field is blank.');
		if (($master['doublecheckpassword'] && $master['authbyiponly']) || !$master['authbyiponly']) {
			$valid->is_empty('passwd','the passwd field is blank.');
			$valid->is_empty('passwd_confirm','the passwd_confirm field is blank.');
		}
		$valid->is_empty('first_name','the first_name field is blank.');
		$valid->is_empty('last_name','the last_name field is blank.');
		$data = $dbc->database_query("SELECT * FROM users WHERE username = '".$valid->get_value('username')."'");
		if($row = $dbc->database_fetch_array($data)) { 
			$valid->add_error('that username has already been utilized.');
		}
		if (strspn(strtolower($valid->get_value('username')),'abcdefghijklmnopqrstuvwxyz0123456789_') !=
			strlen($valid->get_value('username')))
			$valid->add_error('your username must consist only of letters, numbers, and underscore characters.');

		if (strlen($valid->get_value('username'))>40)
			$valid->add_error('your username cannot be longer than 40 characters in length.');

		if (($master['doublecheckpassword'] && $master['authbyiponly']) || !$master['authbyiponly']) {
			$valid->is_same($valid->get_value('passwd'),$valid->get_value('passwd_confirm'),'your passwd does not match the passwd_confirm field.');
		}
		if(ALP_TOURNAMENT_MODE && $master['internetmode']) {
			$em = $valid->get_value('email');
			if(!is_email($em)) {
				$valid->add_error('your email is not the correct format.');
			}
		}
		if (!$valid->is_error()) {
			if (!$users_exist) {
				$priv_level = 3;
			} else {
				$priv_level = 1;
			}
			if(!ALP_TOURNAMENT_MODE) $query = "INSERT INTO users (proficiency, priv_level, username, passwd, date_of_arrival, recent_ip, display_ip, email, display_email, gaming_group, first_name, last_name, gender) VALUES ('".$valid->get_value("proficiency")."', ".$priv_level.", '".$valid->get_value("username")."', '".crypt($valid->get_value("passwd"))."', '".date('Y-m-d H:i:s')."', '".$_SERVER["REMOTE_ADDR"]."', '".$valid->get_value("display_ip")."', '".$valid->get_value("email")."', '".$valid->get_value("display_email")."', '".$valid->get_value("gaming_group")."', '".$valid->get_value("first_name")."', '".$valid->get_value("last_name")."', '".$valid->get_value("gender")."')";
			else $query = "INSERT INTO users (priv_level, username, passwd, date_of_arrival, recent_ip, email, first_name, last_name) VALUES (".$priv_level.", '".$valid->get_value("username")."', '".crypt($valid->get_value("passwd"))."', '".date('Y-m-d H:i:s')."', '".$_SERVER["REMOTE_ADDR"]."', '".$valid->get_value("email")."', '".$valid->get_value("first_name")."', '".$valid->get_value("last_name")."')";
			if ($dbc->database_query($query)) {
				if (($master['doublecheckpassword'] && $master['authbyiponly']) || !$master['authbyiponly']) {
					if ($valid->get_value('javascript')=='') { ?>
						your password was transmitted in plain text because you did not have javascript enabled.<br /><br />
						<?php
					}
				} ?>
				user registration successful.  you can go ahead and log in to access the features of the site.  <?php if(($master["doublecheckpassword"]&&$master["authbyiponly"])||!$master["authbyiponly"]) { ?><strong>do not forget your password.</strong><?php } ?><br /><br />
				<?php
				if(!ALP_TOURNAMENT_MODE) {
					if ($dbc->database_query("INSERT into prizes_votes (userid, getall) VALUES ('".$dbc->database_insert_id()."','1')")) {
						if ($toggle['prizes'] && $dbc->database_num_rows($dbc->database_query('SELECT * FROM prizes'))) { ?>
							prize registration successful.  by default you are registered to win all eligible prizes.  if you do not wish to be eligible for prizes, you can change this option at the prize registration page.<br /><br />
							<?php
						}
					}
				}
			} else { ?>
				there has been an error inputting your registration information into the database.  you have not been registered.  please contact the lan party admin and inform them of the problem.<br />
				<?php
				if(!$dbc->database_num_rows($dbc->database_query('SELECT * FROM users'))) {
					echo '<br />'.$query.'<br /><br />';
				}
			}
		} else {
			$valid->display_errors();
		}
	}
	$x->display_bottom();
} elseif ($ip_registered) {
	$x->display_top(); ?>
	<font color="#ff0000"><strong>only one registration account per user.</strong></font><br />
	<br />
	<?php
	$x->display_bottom();
} else {
	$x->display_slim('you are not authorized to view this page.');
}
} //#if (file_exists('install/install.php')&&!file_exists('install/DISABLED')) {
?>
