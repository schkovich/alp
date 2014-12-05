<?php
session_start();
chdir('..');
require_once('header.php');
check_auth();
chdir('admin');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
.style1 {font-size: 24px}
.style2 {font-size: 16px}
body,td,th {
	font-family: Arial, Helvetica, sans-serif;
}
-->
</style>
</head>

<body>
<p class="style1">Autonomous LAN Party Online Administration</p>
<p class="style2">options: user list | <a href="admins.php">add admins</a> | <a href="party.php">party options</a> | <a href="export.php">export users</a> </p>
<p class="style2">Current registered users:<br />
  Currently you cannot change information. This will be added in the next release, along with reusing user signups. Currently, when a LAN is over you have to clear the list and start over. This will change next time around.</p>
<p class="style2">Total Registered Gamers (reserved and unreserved): 
<?php 
$bquery=$dbc->database_query("SELECT count(*) FROM gamers", $link_id);
$barray=$dbc->database_fetch_row($bquery);
$cquery=$dbc->database_query("SELECT count(*) FROM gamers WHERE prepaid = '1'", $link_id);
$carray=$dbc->database_fetch_row($cquery);
 $avail = 100 - $c_array[0];
 $nopay = $b_array[0] - $c_array[0];
 ?>
<?php echo $b_array[0];?><br />
Total Unconfirmed Seats: <?php echo $nopay; ?><br />
Total Prepaid Seats (reserved): <?php echo $c_array[0]; ?> <br />
Seats still available: <?php echo $avail;?>
</p>
<table width="90%" border="1" align="center" cellspacing="0" cellpadding="0">
  <tr>
    <th scope="col"><div align="center">Nickname</div></th>
    <th scope="col"><div align="center">Location</div></th>
    <th scope="col"><div align="center">Seat Status</div></th>
	<th scope="col"><div align="center">&nbsp;Payment&nbsp;</div></th>
  </tr>
  <?php
$a_query=$dbc->database_query("SELECT * FROM gamers WHERE prepaid = '1' ORDER BY `id` ASC", $link_id);
	if(!$a_query) error_message(sql_error());
	while($a_array=$dbc->database_fetch_array($a_query)){
		if(!$a_array) error_message(sql_error());
		 echo "<tr><td><div align=\"center\">".$a_array["nickn"]."</div></td><td><div align=\"center\">".$a_array["location"]."</div></td> <td><div align=\"center\"><font color=\"#00FF00\">RESERVED</font></div></td><td>n/a</td></tr>";
		 }
			$q_query=$dbc->database_query("SELECT * FROM gamers WHERE prepaid = '0' ORDER BY `id` ASC", $link_id);
	if(!$q_query) error_message(sql_error());
	while($q_array=$dbc->database_fetch_array($q_query)){
		if(!$q_array) error_message(sql_error());
echo "<tr><td><div align=\"center\">".$q_array['nickn']."</div></td><td><div align=\"center\">".$q_array['location']."</div></td> <td><div align=\"center\"><font color=\"#FF0000\"><b>UNRESERVED<b></font></div></td><td><div align=\"center\"><a href=\"https://www.paypal.com/xclick/business=payments%40ownijbgc.net&item_name=Ownij+LAN+Party+Payment&item_number=".$q_array['id']."&amount=20.00&page_style=Primary&return=http%3A//www.ownijbgc.net/pages/paypalyes&cancel_return=http%3A//www.ownijbgc.net/pages/paypalno&cn=Seat+Reservation+%28Optional%29&currency_code=USD\"><img src=\"http://www.ownijbgc.net/images/paypal_payments.gif\" border=\"0\" alt=\"Pay now with Paypal\" /></a></div></td></tr>";
		 } ?>
</table>
</body>
</html>
