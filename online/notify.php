<?php
require_once("functions.php");
$link_id = start_mysql();
$amount = $settings["online_price"];
$data = get_settings();
extract($data);
$paypal_server = 'www.paypal.com';

//if($_POST['receiver_email'] != 'billing@cgservers.com' && $_POST['receiver_email'] != 'test@cgservers.com') { exit(); }

//require_once('../includes/_include.php');

// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';

foreach ($_POST as $key => $value) {
	$value = urlencode(stripslashes($value));
	$req .= "&$key=$value";
}

// post back to PayPal system to validate
$header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
//$fp = fsockopen ('www.paypal.com', 80, $errno, $errstr, 30);
$fp = fsockopen ($paypal_server, 80, $errno, $errstr, 30);

// assign posted variables to local variables
$item_name = $_POST['item_name'];
$item_number = $_POST['item_number'];
$payment_status = $_POST['payment_status'];
$payment_amount = $_POST['mc_gross'];
$payment_currency = $_POST['mc_currency'];
$txn_id = $_POST['txn_id'];
$receiver_email = $_POST['receiver_email'];
$payer_email = $_POST['payer_email'];

if (!$fp) {
	// HTTP ERROR
} else {
	fputs ($fp, $header . $req);
	while (!feof($fp)) {
		$res = fgets ($fp, 1024);
		if (strcmp ($res, "VERIFIED") == 0) {
			$getid = $item_number;
			
		if ($receiver_email == $settings["paypal_account"]) {
				if ((int) $payment_amount == (int) $amount) {
					if ($payment_status == "Completed") {
						$query=$dbc->database_query("SELECT * FROM ".$database["prefix"]."tempusers WHERE userid = '$getid'", $link_id);
						$user=$dbc->database_fetch_array($query);
						$sql3 = "UPDATE ".$database["prefix"]."tempusers SET paid='1' WHERE userid='$getid'";
						$result3 = $dbc->database_query($sql3);
						$email = $user['email'];
						$email_subj = $lan["name"]." Prepay Confirmation";
						$email_msg = "Your prepayment has been received. Thank you!  Please bring your paypal receipt that will be emailed separately to the LAN for confirmation. If you have any questions please email ".$settings["admin_email"];
						$email_headers="From: ".$lan['org']." Payment System <".$settings["admin_email"].">";
						// notify user
						mail($email, $email_subj, $email_msg, $email_headers);
						// notify admin
						mail($settings["admin_email"], "ALP Administrative notice: LAN prepay received", "This is an administrative notice from the ALP User management system. A user has been set as paid automatically via paypal. Please verify that the details of the transaction match. You should receive a notice from paypal as well. If you do not, you should ensure that a payment has actually been received. You should also make sure that the amount matches. This system is programmed to reject payments that are not the set amount for online payments, but they will not refund the money. If a user has submitted a payment that is not accepted you must login to paypal and refund the money manually.", $email_headers);
					}
			}
		}
		}
		else if (strcmp ($res, "INVALID") == 0) {
			/*
			// log for manual investigation
			$from_header = "From: custsoft@cgservers.com";
			$to = "@setting";
			$subject = $res;
			$message = $req;
			mail($to, $subject, $message, $from_header);
			*/
		}
	}
fclose ($fp);
}
?>
