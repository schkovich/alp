<?php
include("header.php");
echo "NOTE: If you pay via Paypal, please inform the admin and print a recipt in the event the handler fails!";
$query="SELECT * FROM ".$database["prefix"]."tempusers WHERE userid = '".$_GET['user']."'";
$result = $dbc->database_query($query, $link_id);
$user = $dbc->database_fetch_array($result);
$query2 = "SELECT `prepay_toggle` FROM `settings`";
$result = $dbc->database_fetch_array($dbc->database_query($query2, $link_id));
$prepay_toggle = $result['prepay_toggle'];
?>
<table width="420" border="1" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <th width="124" scope="row">username</th>
    <td colspan="2">&nbsp;<?php echo $user["username"]; ?></td>
  </tr>
  <tr>
    <th scope="row">real name </th>
    <td colspan="2">&nbsp;<?php echo $user["last_name"]; ?>, <?php echo $user["first_name"]; ?></td>
  </tr>
  <tr>
    <th scope="row">gaming group </th>
    <td colspan="2">&nbsp;<?php echo $user["gaming_group"]; ?></td>
  </tr>
  <tr>
    <th scope="row">payment options </th>
    <td width="112">&nbsp;Door Price: $20 </td>
    <td width="176">&nbsp;Online Price: $15 <?php if($prepay_toggle == 1) { ?> <a href="https://www.paypal.com/xclick/business=<?php echo $settings['paypal_account'];?>&item_name=<?php echo $lan['name'];?>+Payment&item_number=<?php echo $user['userid'];?>&amount=<?php echo $settings['online_price'];?>&page_style=Primary&return=&cancel_return=&cn=Notes+or+message+%28Optional%29&currency_code=USD" target="_self"><img src="img/paypal.gif" alt="Pay now with Paypal!" width="62" height="31" border="0" align="absmiddle"></a> <?php } ?> </td>
  </tr>
</table>
<?php 
include("footer.php");
?>