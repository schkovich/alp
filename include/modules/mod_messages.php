<?php
global $master,$userinfo; 
if(current_security_level() > 0) {
if ($master['shoutbox_inbox_limit'] != 0) {
	$holder = ' LIMIT '.$master['shoutbox_inbox_limit'].' order by `time_stamp` desc';
} else {
	$holder = '';
};

$data = $dbc->database_query('SELECT `messageid`,`subject`,`read` FROM messages WHERE `deleted` = "n" AND `to_userid` = "' . $_COOKIE['userid'] . '" ' . $holder); 

?>
<script language="JavaScript">
<!-- 
function goToMessage() {
	if(document.messages.go.value!="") document.location.href = document.messages.go.value;
} // -->
</script>
<?php
	// (url to go to, name to display, minimum security level)
$menu = array(
	array('','[    Inbox    ]','y'),
	array('','--------------','y'),
);

if ($dbc->database_num_rows($data)) {
	$counter = 0;
	while($row = $dbc->database_fetch_assoc($data)) { 
		$menu[] = array('/messaging.php?messageid=' . $row['messageid'],$row['subject'],$row['read']);
		$counter++;
	};
} else {
	$menu[] = array('','[ -- empty -- ]','y');

};
 
?>
<a href="messaging.php"><strong>messaging</strong></a><?php get_go('messaging.php'); ?><br />
<img src="img/pxt.gif" width="1" height="4" border="0"><br />
<form name="messages">
<select name="go" style="width: <?php //echo $this->get_inner_width(); ?>; font: 10px Verdana" onChange="goToMessage()">
<?php
foreach($menu as $val) { 

   if ($val[2] == 'n') {
		$newmail = 'yes';
?>
	<option value="<?php echo $val[0]; ?>">*<?php echo $val[1]; ?></option>
<?php 
  } else {
?>
	<option value="<?php echo $val[0]; ?>"><?php echo $val[1]; ?></option>
<?php
  };

};
?>
</select>
</form>
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr><td><?php spacer(1,8,1); ?></td></tr>
<td class="sm">
<form name="newmessage" action="messaging.php" method="post"><input type="hidden" name="type" value="new">
<?php get_arrow(); ?>&nbsp;<a href="javascript: document.newmessage.submit()" class="radio">Send a message</a></form><br /></td></tr>
<?php if  ($newmail == 'yes') { print "<tr><td align=\"center\"><big>you have new mail!</big></td></tr>"; }; ?>
</table>
<?php } ?>
