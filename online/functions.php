<?php
require_once('_config.php');
require_once 'include/genesis_include.php';

//Database Connection Setup for ALP
$dbc = new genesis(); //Create Genesis Ojbect
	
DEFINE(DATABASE_CONNECT_ERROR, "<strong>ALP Error : 1 : Could not connect to database server
				<br />Please check ALP's database settings or the database server.</strong>");
DEFINE(DATABASE_SELECT_ERROR, "<strong>ALP Error : 2 : Could not select a database on the database server
				<br />Please make sure that there is a database created on the server and the username you are tring to use has full access to that database.  Also, check the database settings in ALP.</strong>");
$db = $dbc->database_connect_select($database['server'],$database['user'],$database['passwd'],$database['database'],DATABASE_CONNECT_ERROR,DATABASE_SELECT_ERROR);




function get_settings() {
	global $dbc;
	$data = $dbc->database_fetch_array($dbc->database_query("SELECT * FROM `settings"));

$return['lan']['name'] = $data['event'];
$return['lan']['group'] = $data['group'];
$return['lan']['max'] = $data['max_attendance'];
$return['settings']['paypal_account']= $data['paypal_account']; 
$return['settings']['door_price']= $data['attendance_price_door']; 
$return['settings']['online_price']= $data['attendance_price_online'];
$return['settings']['admin_email']= $data['admin_email']; 
$return['settings']['prepay_toggle']=$data['prepay_toggle'];
$return['admin_email']= $data['admin_email']; //fix a variable
$return['lan']['timestart'] = $data['start_time'];
$return['lan']['timeend'] = $data['end_time'];
$return['lan']['datestart'] = $data['start_date'];
$return['lan']['dateend'] = $data['end_date'];
$return['settings']['send_mail'] = $data['send_mail'];
$return['language'] = "en";
$return['country'] = "us";
$return['email_subj']= $data['event']." Registration Confirmation";
$return['email_msg']="Congratulations! You have successfully registered for ".$data['event'].". Please remember that to guarantee your seat you must also prepay. To prepay simply visit the attendance list and choose the payment link next to your name.\nIf you have any further questions feel free to reply to this email.\n\nThanks!\nthe staff of ".$data['group']."\n".$admin_email;
$return['email_headers']="From: ".$data['group']." <".$admin_email.">";
$return['colors']['image_text'] = "black";
return $return;
}

function settings_form() {
	$settings = get_settings();
	extract($settings);
	?>
	<form method="POST">
	<br /><br />
	SET ADMIN PASSWORD (Must be done each time you change settings)
	<input type="password" name="data[pw]"><br />	
	<br />Name of your lan party 
	<input type="text" name="data[name]" value="<?php echo $lan['name']; ?>">
	<br />Name of the gaming group hosting the party
	<input type="text" name="data[group]" value="<?php echo $lan['group']; ?>">
	<br /> Maximum registrants your facility can support
	<input type="text" name="data[max]" value="<?php echo $lan['max']; ?>">
	<br />Allow prepaying via paypal? 
	<input type="checkbox" name="data[prepay]" value="1" <?php if($settings['prepay_toggle'] == 1) echo "checked"; ?> >
	<br /> The e-mail adress on your paypal account (if applicable)
	<input type="text" name="data[paypal_account]" value="<?php echo $settings['paypal_account']; ?>"> 
	<br /> at the door price. decimal number only!!! Don't include a dollar sign!
	<input type="text" name="data[door_price]" value="<?php echo $settings['door_price']; ?>">
	<br />same as above except this is the online price
	<input type="text" name="data[online_price]" value="<?php echo $settings['online_price']; ?>">

<br /><br />administrative email. this is the FROM address in email confirmation emails
and is where administrative updates will come from. Users will also be instructed
to send questions to this address, so make sure its a valid email!
you will also get notices from this management system TO this address such as when someone
pays. it is importaint that you are able to get messages sent to this address.
<br /><br />Admin Email:
<input type="text" name="data[admin_email]" value="<?php echo $settings['admin_email']; ?>"> 
<br />Allow sending of confirmation e-mails via php mail() function?
<input type="checkbox" name="data[send_mail]" value="1" <?php if($settings['send_mail'] == 1) echo "checked"; ?>>
<br /><br /> starting and ending of your lan party (time is in 24 hour format, year accepts 2 or 4 digit years).
<br /> you _must_ use similar punctuation, with the : for time seperators and / for date seperators.
<br />Start Time:
<input type="text" name="data[timestart]" value="<?php echo $lan['timestart']; ?>">
<br />End Time:
<input type="text" name="data[timeend]" value="<?php echo $lan['timeend']; ?>">
<br />Start Date: 
<input type="text" name="data[datestart]" value="<?php echo $lan['datestart']; ?>">
<br />End Date:
<input type="text" name="data[dateend]" value="<?php echo $lan['dateend']; ?>">
<br/><br />
Examples:<br />
$lan["timestart"] = "09:00";<br />
$lan["timeend"] = "09:00";<br />
$lan["datestart"] = "05/08/2004";<br />
$lan["dateend"] = "05/09/2004";<br />
<input type="submit" name="step3" value="Submit Data"><br />

<br />// the code for the default language you wish to use. (available languages are: en)
<br />$language = "en";

<br /><br />// country where you are having the event. current available countries: us
<br />$country = "us";

<br /><br />// this is going away
	<br />$email_subj=$lan["name"]." Registration Confirmation";
	<br />$email_msg="Congratulations! You have successfully registered for ".$lan["name"].". Please remember that to guarantee your seat you must also prepay. To prepay simply visit the attendance list and choose the payment link next to your name.\nIf you have any further questions feel free to reply to this email.\n\nThanks!\nthe staff of ".$lan['org']."\n".$admin_email;
	<br />$email_headers="From: ".$lan['org']." <".$admin_email.">";
	<br /><input type="submit" name="step3" value="Submit Data"><br />

<?php
}

function check_auth() {
	global $dbc;
	$pw = $_SESSION['passwd'];
	$result = $dbc->database_query("SELECT * FROM `settings` WHERE `pw` = '$pw'") or die($dbc->database_error());
	if($dbc->database_num_rows($result) < 1)
			die(header("Location:login.php"));
}



function process_settings($data) {
	global $dbc;
	//TODO: Need to add checking for blank fields
	extract($data);
	$pw = md5($pw);
	$dbc->database_query("DELETE FROM `settings`");
	$query = "INSERT INTO `settings` (`pw`,`group`,`event`,`start_date`,`end_date`,`start_time`,`end_time`,`admin_email`,`send_mail`,`max_attendance`,`paypal_account`,`attendance_price_door`,`attendance_price_online`,`prepay_toggle`)
			VALUES ('$pw','$group','$name','$datestart','$dateend','$timestart','$timeend','$admin_email','$send_mail','$max','$paypal_account','$door_price','$online_price','$prepay')";
	if($dbc->database_query($query))
		return true;
	else
		return false;
}

if (!function_exists('file_put_contents')) {
    function file_put_contents($filename, $data, $respect_lock = true)
    {
        // Open the file for writing
        $fh = @fopen($filename, 'w');
        if ($fh === false) {
            return false;
        }

        // Check to see if we want to make sure the file is locked before we write to it
        if ($respect_lock === true && !flock($fh, LOCK_EX)) {
            fclose($fh);
            return false;
        }

        // Convert the data to an acceptable string format
        if (is_array($data)) {
            $data = implode('', $data);
        } else {
            $data = (string) $data;
        }

        // Write the data to the file and close it
        $bytes = fwrite($fh, $data);

        // This will implicitly unlock the file if it's locked
        fclose($fh);

        return $bytes;
    }
}