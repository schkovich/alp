<?php
include("header.php"); 
if(empty($_POST)) {
	 ?>
	<center><b>register</b>:<br />
	<div align="center"><p>Please, one person per registration. If you are a parent and will be attending with a minor please fill out two registrations, one for you and one for your child.  Use your name and your child's nickname for the registration. All information will be kept confidential and will only be used for attendance and admittance purposes at the LAN event.  Your email address will not be released to anyone and will only be used for LAN party purposes. Just signing up does NOT guarantee your seat. If you do not prepay, your seat and admission is first come first serve. You can guarantee your seat only by prepaying.</p><p>If you have already registered and wish to prepay, please visit the <a href="users.php">attendance list</a> and click the payment button beside your username.</p>
	<?php
	$gamerscount=$dbc->database_result($dbc->database_query("SELECT count(*) FROM ".$database["prefix"]."tempusers", $link_id), 0);
	$paidcount=$dbc->database_result($dbc->database_query("SELECT count(*) FROM ".$database["prefix"]."tempusers WHERE paid = '1'", $link_id), 0);
	$avail = $lan["max"] - $paidcount;
	$nopay = $gamerscount - $paidcount;
	echo "Total registered users (reserved and unreserved): ".$gamerscount;
	echo "<br />Total Unconfirmed users (not paid): ".$nopay;
	echo "<br />Prepaid users (reserved): " .$paidcount . "<br />Seats still available: ".$avail;
	$dateholder = date("Y-m-d H:i:s");
	?>
	</center>
	<?php if ($avail!=0) { ?>
	<table border=0 cellpadding=4 cellspacing=4 width="400" align="center" class="centerd"><tr><td>
	<form action="<?php echo get_script_name(); ?>" method="POST" name="register">
	<input type="hidden" name="javascript" value="">
	<input type="hidden" name="date_of_arrival" value="<?php echo $dateholder; ?>">
	<input type="hidden" name="recent_ip" value="<?php echo $_SERVER["REMOTE_ADDR"]; ?>">
	Required Information:<br />
	<b>username</b><br /> 
	<input type="text" name="username" size=40 maxlength=40 style="width:99%"><br />
	 <b>password</b><br /> 
	<input type="password" name="passwd" maxlength=34 style="width:99%"><br />
	 <b>confirm password</b><br /> 
	<input type="password" name="passwd_confirm" maxlength=34 style="width:99%"><br />
	 <b>email</b><br /> 
	 <input type="text" name="email" maxlength=60 style="width:99%"><br />
	 <b>allow others to see your email address?</b><br /> 
	<input type="radio" name="display_email" value="0" class="radio" checked> no. <input type="radio" name="display_email" value="1" class="radio"> yes.<br />
	<br />
	
	 <br /><b>first name</b><br /> 
	<input type="text" name="first_name" maxlength=30 style="width:99%"><br />
	 <b>last name</b><br /> 
	<input type="text" name="last_name" maxlength=30 style="width:99%"><br />
	<br />
	optional information<br />
	 <b>how many of the other gamers at the lan party do you think you can kill in a one-on-one in your favorite game?</b> (for random team tournaments -- be honest)<br /> 
	<table border=0 width=100%>
		<tr style="color: #000000">
		<?php
		$c = array("f","d","c","a",9,8,7,5,3,2,0);
		for($i=0;$i<=10;$i++) { ?>
			<td bgcolor="#<?php for($j=0;$j<6;$j++) { echo $c[$i]; } ?>"><?php // spacer(1,5); ?></td>
			<?php
		} ?>
		</tr>
		<tr style="color: #000000">
		<?php
		for($i=0;$i<=10;$i++) {
			$temp = getimagesize("img/percent/".$colors["image_text"]."_".$i.".gif"); ?>
			<td><input type="radio" name="proficiency" class="radio" value="<?php echo $i; ?>"<?php echo ($i==5?" checked":""); ?>><img src="img/percent/<?php echo $colors["image_text"]."_".$i; ?>.gif" border="0" width="<?php echo $temp[0]; ?>" height="<?php echo $temp[1]; ?>"></td>
			<?php
		} ?>
		</tr>
	</table>
	 <br /><b>allow others to see your ip address at the lan?</b> (for windows sharing or ftp server links--this applies to at the LAN only)<br /> 
	<input type="radio" name="display_ip" value="0" class="radio" checked> no. <input type="radio" name="display_ip" value="1" class="radio"> yes.<br />
	<b>gaming group</b> <br /> 
	<input type="text" name="gaming_group" maxlength=20 style="width:99%"><br />
	 <b>gender</b><br /> 
	<input type="radio" name="gender" value="female" class="radio"> female <input type="radio" name="gender" value="male" class="radio"> male <input type="radio" name="gender" value="" class="radio" checked> anonymous<br />
	<br />
	<div align="right"><input type="submit" value="register" style="width: 160px" class="formcolors"></div>
	</form>
	<br />
	</td></tr></table>
	<?php
	}
  if ($avail==0) { ?>
  <center>Sorry, the LAN is full! Please try back later to see if someone canceled! </center>
  <?php }
} 
else 
	{
	include "include/cl_validation.php";
	$valid = new validate();
	if($valid->get_value("javascript")=="") {
		if($valid->get_value("passwd")!="") $valid->set_value("passwd",md5($valid->get_value("passwd")));
		if($valid->get_value("passwd_confirm")!="") $valid->set_value("passwd_confirm",md5($valid->get_value("passwd_confirm")));
	}
	$valid->is_email("email","the email address provided was not valid");
	$valid->is_empty("username","the username field is blank.");
	$valid->is_empty("email","the email field is blank.");
	$valid->is_empty("passwd","the passwd field is blank.");
	$valid->is_empty("passwd_confirm","the passwd_confirm field is blank.");
	$valid->is_empty("first_name","the first_name field is blank.");
	$valid->is_empty("last_name","the last_name field is blank.");
	$data = $dbc->database_query("SELECT * FROM ".$database["prefix"]."tempusers WHERE username = '".$valid->get_value("username")."'");
	if($row = $dbc->database_fetch_array($data)) { 
		$valid->add_error("that username has already been utilized.");
	}
	$valid->is_same($valid->get_value("passwd"),$valid->get_value("passwd_confirm"),"passwords do not match");
	if(!$valid->is_error()) {
		$priv_level = 1;
		$query = "INSERT INTO ".$database["prefix"]."tempusers (proficiency, priv_level, username, passwd, date_of_arrival, recent_ip, display_ip, email, display_email, gaming_group, first_name, last_name, gender) VALUES ('".$valid->get_value("proficiency")."', ".$priv_level.", '".$valid->get_value("username")."', '".crypt($valid->get_value("passwd"))."', '".$valid->get_value("date_of_arrival")."', '".$_SERVER["REMOTE_ADDR"]."', '".$valid->get_value("display_ip")."', '".$valid->get_value("email")."', '".$valid->get_value("display_email")."', '".$valid->get_value("gaming_group")."', '".$valid->get_value("first_name")."', '".$valid->get_value("last_name")."', '".$valid->get_value("gender")."')";
		if($dbc->database_query($query)) {
			if($settings["send_mail"] == 1 && mail($valid->get_value("email"), $email_subj, $email_msg, $email_headers)) echo "Registration Successfull! You should recieve an e-mail shortly";
			else {
				echo "Registration successful. Normally a confirmation email is sent with details about the event. Currently, the email system is unavailable so your confirmation email will not be sent. Here is the message you would have received. Please read it carefully, because it may contain importaint information.<p>".$email_msg."</p>go to the <a href=\"users.php\">attendance</a> page for payment options.";
			}
		}  
		else { 
			include("header.php"); ?>
			there has been an error inputting your registration information into the database.  you have not been registered.  please contact the lan party admin and inform them of the problem.<br />
			<?php
		}
		
	}
	if($valid->is_error()) {
			$valid->display_errors();
		}
}
include("footer.php");
?>