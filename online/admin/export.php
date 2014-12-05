<?php 
session_start();
chdir('..');
require_once('functions.php');
check_auth();
chdir('admin');

/* @header("Status: 404 Not Found"); exit; 
 die;*/
// header("Status: 200 OK");
$output_file="alpusers.txt";
@ini_set('zlib.output_compression', 'Off');
header('Pragma: public');
header('Content-Transfer-Encoding: none');
header('Content-Type: application/octetstream; name="' . $output_file . '"');
header('Content-Disposition: inline; filename="' . $output_file . '"');
$query=$dbc->database_query("SELECT * FROM tempusers");
echo "--Exporting data for users...please see bottom for results<br>";
while($array=$dbc->database_fetch_array($query)) {
	$newpassword=crypt(md5($array['password']));
	$nick=$array['username'];
	$firstn=$array['first_name'];
	$lastn=$array['last_name'];
	$email=$array['email'];
	$paid=$array['paid'];
	echo "INSERT INTO users (username, first_name, last_name, passwd, paid, email, recent_ip, priv_level, date_of_arrival) VALUES('$nick', '$firstn', '$lastn', '$newpassword', '$paid', '$email', '192.168.1.250', '1', NOW()); <br>";
	$text .= "INSERT INTO users (username, first_name, last_name, passwd, paid, email, recent_ip, priv_level, date_of_arrival) VALUES('$nick', '$firstn', '$lastn', '$newpassword', '$paid', '$email', '192.168.1.250', '1', NOW()); \r\n";
}
if(file_put_contents("../settings/export.txt",$text))
	echo "<br><br>Contents exported into online/settings/export.txt!";
else
	echo "<br><br>--Unable to save to file, plase copy and paste";
?>