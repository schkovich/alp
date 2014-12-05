<?php
header("Location:users.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>ALP Online Main Menu</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
body,td,th {
	font-family: Arial, Helvetica, sans-serif;
}
.style1 {font-size: 24px}
.style2 {font-size: 16px}
.style3 {font-size: xx-small}
-->
</style></head>

<body>
<p class="style1">Autonomous LAN Party Online Administration</p>
<p class="style2">options: <a href="users.php">user list</a> | <a href="admins.php">add admins</a> | <a href="party.php">party options</a> | <a href="export.php">export users</a> </p>
<p class="style2">This is the online addon for ALP. The options above allow you to change different options about your upcoming LAN. Choose user list to see the list of currently registered users and to do various tasks such as mark them as paid/reserved, delete users, or change their information. Add admins allows you to add administrators to have access to this online system. Party options allows you to setup the upcoming LAN. You can reuse this script for as many LANs as needed. Simply update the options for the next party and choose &quot;clear users&quot;. This will clear the list of gamers and allow a new list for the next event. In the very near future, the list will be reusable and gamers can login using their existing account and register for upcoming events. The &quot;export users&quot; option allows you to export the online user table to a text file to allow importation into ALP. Choosing this option will prompt you to download a file called &quot;alpusers.txt&quot;. Simply login to ALP as an administrator with the Online addon installed and go to the signin page and choose import and upload the alpusers.txt file and your users will be imported into ALP for sign-in. </p>
<p class="style2">Current user stats:</p>
<p class="style2">If &quot;prepay required&quot; is set in the party options then users who have not paid will not be included in &quot;total users registered&quot;. Those users will be included in the waiting list. If &quot;prepay required&quot; is set to no then they will be included in the &quot;Total users registered&quot; but will not count towards the &quot;seats remaining&quot;. </p>
<p class="style2">XX Total users registered<br />
  XX Users Prepaid<br />
  XX Users not paid<br />
  XX Seats remaining</p>
</body>
</html>
