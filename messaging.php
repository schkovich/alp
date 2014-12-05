<?php
require_once 'include/_universal.php';
$x = new universal('messaging','',0);
if ($x->is_secure() && $toggle['messaging']) { 
	if (empty($_POST)) { 
		// nothing has been posted, so most likely we want the inbox
			$x->display_top();
		if ($_GET['messageid'] && current_security_level() >= 1) {
			// if we have a messageid, lets go get the message and view it

 			$data = $dbc->database_query('SELECT `messages`.`messageid` as messageid,`messages`.`subject` as subject,`messages`.`read` as `read`,`messages`.`time_stamp` as `time_stamp`,`messages`.`from_userid` as `from_userid`,`messages`.`data` as data, `users`.`username` as `username` FROM messages LEFT JOIN users ON (`messages`.`from_userid` = `users`.`userid`) WHERE `to_userid` = "'.$userinfo['userid'].'" and messageid = "'.(int)$_GET['messageid'].'" AND deleted = "n"');
			if ($dbc->database_num_rows($data)) {
				begitem('view message');
 			        $row = $dbc->database_fetch_assoc($data);
?> 
<table border="0">
<tbody>
	<tr><td>Sender</td><td><a href="disp_users.php?id=<?php print $row['from_userid']; ?>"><?php print $row['username']; ?></a></td></tr>
	<tr><td>Subject</td><td><?php print $row['subject']; ?></td></tr>
	<tr><td>Time</td><td><?php print $row['time_stamp']; ?></td></tr>
	<tr><td colspan="2">Message</td></tr>
	<tr><td colspan="2">
<pre>
<?php print $row['data']; ?>
</pre>
	</td></tr>
</tbody>
</table><br />
<?php
				enditem('view message');
				begitem('message controls');
?>	
			<form action="messaging.php" method="POST">
			<input type="hidden" name="message-<?php print $row['messageid']; ?>" value="message-<?php print $row['messageid']; ?>">
			<input type="submit" name="type" value="Delete" style="font-size:10px; width: 50px" class="formcolors">
			</form><br />
<?php
				enditem('message controls');


				$dbc->database_query('update `messages` set `read` = "y", `time_stamp`=`time_stamp` where `to_userid` = "'.$userinfo['userid'].'" and messageid = "'.(int)$_GET['messageid'].'"');
			} else {
i?>the requested message is not available or it may not exist.<?php

			};

		} else {

			// display the inbox page

			?>
			<strong>user message</strong>:<br /><br />
			messaging lets you send a text-based message to a user of this lan party software. you can also read any messages that others have sent you.<?php if (current_security_level() < 1) { ?> to use this feature, you must log in.<?php } ?><br />
			<br />
			<?php
			if (current_security_level() >= 1) {
				begitem('your messages');
 
		?><form method="post" action="messaging.php" ><?php 
 $data = $dbc->database_query('SELECT `messages`.`messageid` as `messageid`,`messages`.`subject` as `subject`,`messages`.`read` as `read`,`messages`.`time_stamp` as `time_stamp`,`messages`.`from_userid` as `from_userid`,`users`.`username` as `username` FROM messages LEFT JOIN users ON (`messages`.`from_userid` = `users`.`userid`) WHERE
`to_userid` = "' . $userinfo['userid'] . '" and `messages`.`deleted` = "n" order by `messages`.`time_stamp`');
				if ($dbc->database_num_rows($data)) {
        				$counter = 0;
?>
<table border="0">
<tbody>
	<tr>
		<th align="left">Tag</th><th align="left">Time</th><th align="left">Subject</th><th align="left">Sender</th>
	</tr>
<?php
 			        while($row = $dbc->database_fetch_assoc($data)) {
?>
	<tr>
		<td><input type="checkbox" name="message-<?php print $row['messageid']; ?>" /></td>
		<td><?php print $row['time_stamp']; ?></td>
		<td><a href="messaging.php?messageid=<?php print $row['messageid']; ?>"><?php print $row['subject']; ?></a></td>
		<td><a href="disp_users.php?id=<?php print $row['from_userid']; ?>"><?php print $row['username']; ?></a></td>
	</tr>
<?                	
                			$counter++;
        			};
?>
</tbody>
</table>
<br />
<?php  
			} else {
?>
<p>No messages for you =(</p>
<?php


			};

			enditem('your messages');
			begitem('message controls');
?>
			<input type="submit" name="type" value="Delete" style="font-size:10px; width: 50px" class="formcolors"> -
			<input type="submit" name="type" value="New" style="font-size:10px; width: 50px" class="formcolors">
			</form><br />
<?php
			enditem('message controls');

			};
 
		};

		$x->display_bottom();
	} else {
// if posted, this means we probably want to do something

		if (current_security_level() >= 1 && ctype_digit($_COOKIE['userid'])) {
			require_once 'include/cl_validation.php';
			$valid = new validate();

                        if ($valid->get_value('type') == 'Send') {

				$valid->set_value('message',substr($valid->get_value('message'),0,255));
				$recipient_id = $valid->get_value('recipient');

				if ($dbc->database_num_rows($dbc->database_query('SELECT userid from users where userid=\''.$recipient_id.'\'')))
				{
					if ($dbc->database_query("INSERT INTO messages (to_userid,from_userid,subject,data) VALUES ('".$recipient_id . "','".$_COOKIE['userid']."','".$valid->get_value('subject')."','".$valid->get_value('message')."')")) {

                                      	  $x->display_slim('post successful.','messaging.php');
                                	} else {
                                      	  $x->display_slim('error! message not posted.','messaging.php');
                                	}
				}
				else
					$x->display_slim('the existence of the message recipient was not verified.','messaging.php');

                        } else if ($valid->get_value('type') == 'Delete') {

	 			$data = $dbc->database_query('SELECT `messageid` FROM messages WHERE `to_userid` = "'.$_COOKIE['userid'].'" AND deleted = "n"');

				$count = 0;

 			        while($row = $dbc->database_fetch_assoc($data)) {

					if ($_POST['message-' . $row['messageid']]) {

	                                        $dbc->query('UPDATE messages SET deleted = "y" WHERE messageid = "'.$row['messageid'].'"');

						$count++;

					};

				};

				if ($count > 0) {
					$x->display_slim('delete successful.','messaging.php');
				} else {
                                        $x->display_slim('error! delete unsuccessful.','messaging.php');

				}

                        } else {

				// if we arent sure what we want, well just go with a new message

				$x->display_top(); 
				begitem('new message');?>

		<form action="messaging.php" method="POST">
		<table border=0 cellpadding=4 cellspacing=0 width="99%">
		  <tr>
                            <td>Recipient: <select name="recipient">
                                <option value="0"> </option>
                                <?php
                                    $users = $dbc->database_query('SELECT * FROM `users` ORDER BY `userid` ');
                                    while($row = $dbc->database_fetch_assoc($users)){
                                        ?><option value="<?php echo $row['userid'];?>"><?php echo$row['username'];?></option>
                                <?php
                                }?>
                                </select>
                            </td>
		  </tr>
		  <tr>
		    <td>Subject: <input type="text" size="100" maxlength="255" name="subject" value="the subject" onblur="if(this.value=='') this.value='the subject';" onfocus="if(this.value=='the subject') this.value='';">
		    </td>
		  </tr>
		  <tr>
		    <td>
		<textarea cols="" rows="20" name="message" style="font-size:10px; width: 99%" onblur="if(this.value=='') this.value='your message here.';" onfocus="if(this.value=='your message here.') this.value='';">your message here.</textarea><br />
		    </td>
		  </tr>
		  <tr>
		    <td width="50" valign="bottom" align="left">
<input type="submit" name="type" value="Send" style="font-size:10px; width: 50px" class="formcolors">
		    </td>
		  </tr>		</table>
		</form>
				<?php
		enditem('new message'); 


			}

		}
		else
			$x->display_slim('you are not authorized to view this page.');

	}
} else {
	$x->display_slim('you are not authorized to view this page.');
}
?>
