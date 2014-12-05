<?php
session_start();
chdir('..');
require_once("header.php");
chdir('admin');
$pw = $_SESSION['passwd'];
	if($dbc->database_num_rows($dbc->database_query("SELECT * FROM `settings` WHERE `pw` = '$pw' > 0")))
			die(header("Location:index.php"));
			


if($_POST['go']) {
	$pw = $_POST['pw'];
	$pw = md5($pw);
	if($dbc->database_num_rows($dbc->database_query("SELECT * FROM `settings` WHERE `pw` = '$pw'")) > 0) {
		$_SESSION['passwd'] = $pw;
		echo "Logged in! <a href='./index.php'>Go back</a>";
	}
	else {
		echo "Invalid Password";
	}
}

echo " <br /><form method='post'> Password: <input type=password name=pw>
	<input type=hidden name=go value=go>
	<input type=submit name=submit value=Submit>";
	ob_end_flush();
?>
