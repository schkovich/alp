<?php
session_start();
chdir('..');
require_once('header.php');
check_auth();
chdir('admin');
if($_POST['data']) {
	$data = $_POST['data'];
	$result = process_settings($data);
	if($result)
		echo "Settings Updated!!! <a href=\"index.php\">Go Back</a><br /><br /><br />";
	else 
		echo "Unable to update...please fill in all fields";
}
settings_form();
?>
