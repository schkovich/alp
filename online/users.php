<?php
include("header.php");
?>
<div align="center">
<b>Event Info</b><br />
<?php
extract($lan);
echo "Event Name: $name<br /> Hosted By: $group <br /> Start Time: $timestart on $datestart<br />End Time: $timeend on $dateend";
?><br />
-----------------------------------------<br />
<?php echo "<a href='./admin/'>Admin Menu</a> | <a href=\"register.php\"> Register Now!</a><br />";
$gamerscount=$dbc->database_result($dbc->database_query("SELECT count(*) FROM ".$database["prefix"]."tempusers", $link_id), 0);
   $paidcount=$dbc->database_result($dbc->database_query("SELECT count(*) FROM ".$database["prefix"]."tempusers WHERE paid = '1'", $link_id), 0);
   $avail = (int)$lan["max"] - $paidcount[0];
   $nopay = (int)$gamerscount[0] - $paidcount[0];
   echo "Total registered users (reserved and unreserved): ".(int)$gamerscount[0];
   echo "<br />Total Unconfirmed users (not paid): ".(int)$nopay;
   
   echo "<br />Prepaid users (reserved): " .$paidcount . "<br />Seats still available: ".$avail;
echo "<table width=\"70%\" border=\"1\" align=\"center\" cellspacing=\"0\" cellpadding=\"0\">
  <tr>
    <th scope=\"col\"><div align=\"center\">Username</div></th>
    <th scope=\"col\"><div align=\"center\">Gaming Group</div></th>
    <th scope=\"col\"><div align=\"center\">Payment Status</div></th>
	<th scope=\"col\"><div align=\"center\">&nbsp;</div></th>
  </tr>";
$paid_query=$dbc->database_query("SELECT * FROM ".$database["prefix"]."tempusers WHERE paid = '1' ORDER BY `userid` ASC", $link_id);
	if(!$paid_query) error_message(sql_error());
	while($paid_array=$dbc->database_fetch_array($paid_query)){
		if(!$paid_array) error_message(sql_error());
		 echo "<tr><td><div align=\"center\">".$paid_array["username"]."</div></td><td><div align=\"center\">".$paid_array["gaming_group"]."</div></td> <td><div align=\"center\"><font color=\"#00FF00\">RESERVED</font></div></td><td>n/a</td></tr>";
		 }
			$luser_query=$dbc->database_query("SELECT * FROM ".$database["prefix"]."tempusers WHERE paid = '0' ORDER BY `userid` ASC", $link_id);
	if(!$luser_query) error_message(sql_error());
	while($luser_array=$dbc->database_fetch_array($luser_query)){
		if(!$luser_array) error_message(sql_error());
echo "<tr><td><div align=\"center\">".$luser_array['username']."</div></td><td><div align=\"center\">".$luser_array["gaming_group"]."</div></td> <td><div align=\"center\"><font color=\"#FF0000\"><b>UNRESERVED<b></font></div></td><td><div align=\"center\"><a href=\"payments.php?user=".$luser_array["userid"]."\">Pay Now!</a></div></td></tr>";
		 }
 echo "</table>";  ?> </div><br />

 
<?php
include("footer.php");
?>