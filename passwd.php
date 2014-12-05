<?php
require_once 'include/_universal.php';
$x = new universal('reset your password','',0);
if ($x->is_secure()) { 
	$x->display_top();
	begitem('reset your password');
	if ($master['internetmode']) { ?>
		<table border="0" cellpadding="0" cellspacing="0" width="400" align="center"><tr><td>
		<?php
		if (empty($_GET["s"]) && empty($_POST['s'])) { ?>
			<b>stage one of three</b>: e-mail address.<br />
			<br />
			<form action="<?php echo get_script_name(); ?>" method="POST">
			<input type="hidden" name="s" value="1" />
			<input type="hidden" name="sendemail" value="1" />
			<font class="sm">your e-mail address<br /></font>
			<input type="text" name="email" value="" maxlength="60" style="width: 99%" /><br />
			<br />
			<div align="right"><input type="submit" value="send confirmation code" style="width: 160px" class="formcolors" /></div>
			</form>
			<?php
		} elseif ($_GET['s'] == 1 || $_POST['s'] == 1) { ?>
			<b>stage two of three</b>: confirmation code.<br />
			<br />
			<?php
			if (empty($_GET['ccode'])) $data = $dbc->database_query("SELECT * FROM users WHERE email='".(!empty($_POST['email'])?$_POST['email']:'')."'");
			else $data = $dbc->database_query("SELECT * FROM users WHERE ccode='".$_GET['ccode']."'");
			if ($dbc->database_num_rows($data)) {
				$row = $dbc->database_fetch_assoc($data);
				if (!empty($_POST['sendemail'])&&$_POST['sendemail']==1&&empty($_GET['ccode'])) {
					if (!empty($row['ccode'])) $ccode = $row['ccode'];
					else $ccode = md5(uniqid(rand(), true));
					$query = "UPDATE users SET ccode='".$ccode."' WHERE userid='".$row['userid']."'";
					//echo $query."<br />";
					if ($dbc->database_query($query)) { 
						$to = $row['username']." <".$row['email'].">";
						$subject = $lan['name'].": Password Change Confirmation Code";
						$message = "you are receiving this e-mail because of a request to reset your password on your ".$lan['name']." web site account.  if you do not want your password reset, you may ignore or delete this email.\n\nif you desire to reset your password, go to the following website url and continue the process.\n\nhttp://".$_SERVER['HTTP_HOST']."/passwd.php?s=1&ccode=".$ccode;
						$headers = "From: ".$lan['org']."\r\n";
		
						if(@mail($to,$subject,$message,$headers)) { ?>
							a confirmation code has been mailed to your e-mail address.  enter that code here, or click the link in the e-mail to confirm resetting your password.<br />
							<?php
						} else { ?>
							there was an error sending the confirmation code to your e-mail.  most likely, this server isn't configured properly to send email using PHP, but you're welcome to <a href="<?php echo get_script_name(); ?>?s=0">try to send it again</a>.<br />
							<?php
						}
					} else { ?>
						there was an unknown error with your confirmation code.  you were not e-mailed. try again.<br />
						<?php
					}
					echo '<br />';
				} ?>
				<form action="<?php echo get_script_name(); ?>" method="GET">
				<input type="hidden" name="s" value="2" />
				<input type="hidden" name="uid" value="<?php echo $row['userid']; ?>" />
				<font class="sm">confirmation code<br /></font>
				<input type="text" name="ccode" value="<?php echo (!empty($_GET['ccode'])?$_GET['ccode']:''); ?>" maxlength="32" style="width: 99%" /><br />
				<br />
				<div align="right"><input type="submit" value="reset password" style="width: 160px" class="formcolors" /></div>
				</form>
				<?php
			} else {
				if (!empty($_POST['email'])) echo "<b>error</b>: need valid email address.<br />";
				else echo "<b>error</b>: invalid confirmation code.<br />";
			}
			echo '<br />';
		} elseif ($_GET['s'] == 2) { ?>
			<b>stage three of three</b>: reset password.<br />
			<br />
			<?php
			$data = $dbc->database_query("SELECT * FROM users WHERE userid='".$_GET['uid']."' AND ccode='".$_GET['ccode']."'"); 
			if ($dbc->database_num_rows($data)) {
				function makeRandomPassword() { 
				      $salt = 'abchefghjkmnpqrstuvwxyz0123456789'; 
				      srand((double)microtime()*1000000); 
				      $i = 0; 
				      while ($i <= 7) { 
				            $num = rand() % 33; 
				            $tmp = substr($salt, $num, 1); 
				            $pass = $pass . $tmp; 
				            $i++; 
				      } 
				      return $pass; 
				} 
				$row = $dbc->database_fetch_assoc($data);
				$newpasswd = makeRandomPassword();
				if ($dbc->database_query("UPDATE users SET ccode='', passwd='".crypt(md5($newpasswd))."' WHERE userid='".$row['userid']."'")) { 
					$to = $row['username']." <".$row['email'].">";
					$subject = $lan['name'].": New Password";
					$message = "you are receiving this e-mail because your password on your ".$lan['name']." web site account has been reset.  your new password is:\n\n".$newpasswd."\n\nyou can log into your account with this new password at\n\n".$lan['address']."/login.php";
					$headers = "From: ".$lan['org']." <".$lan['email'].">\r\n";
		
					if(@mail($to,$subject,$message,$headers)) { ?>
						your password was successfully reset and was e-mailed to you at <?php echo $row['email']; ?>.<br />
						<?php
					} else { ?>
						there was an error sending the new password to your e-mail.  i'm sorry, but for security reasons you're going to have to <a href="<?php echo get_script_name(); ?>?s=">start over</a>.<br />
						<?php
					}
				} else { 
					echo 'there was an unknown error and your password was not reset.  try again.<br />';
				}
			} else { 
				echo '<b>error</b>: that confirmation code is invalid!<br />';
			}
			echo '<br />';
		} ?>
		</td></tr></table>
		<?php
	} else { ?>
		you llama!  why did you forget your password?  now you have to get up, go to the lan administrator, and ask him/her 
		politely to reset your password for you.  it would be doubly helpful if you got a scrap of paper to write your new 
		password down onto.  then pass this piece of paper to the lan administrator, preferrably with an abe lincoln attached. 
		maybe next time you won't forget your password, huh?<br />
		<br />
		you llama!  you thought this would be automated?  secret questions and other lame methods of getting your password reset would
		only work effectively and securely with some sort of 3rd party messaging system (ie: email).  so if you want no security for
		your password, complain.  if you like your account being secure, praise.<br />
		<br />
		<?php
	}
	enditem('reset your password');
	$x->display_bottom();
} else {
	$x->display_slim('you are not authorized to view this page.');
}
?>