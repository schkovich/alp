<?php
require_once 'include/_universal.php';
$x = new universal('log out of your account','log out',1);
if($x->is_secure()) { 
	if ($dbc->database_query ("UPDATE users SET sesid='' WHERE userid='".$_COOKIE['userid']."';")) {
		setcookie('username','',time() - 3600);
		setcookie('userid','',time() - 3600);
		setcookie('sesid','',time() - 3600);
		$x->display_slim('you have been successfully logged out.');
	} else {
		$x->display_slim('there\'s been an error and you were not logged out.');
	}
} else {
	$x->display_slim('you are not authorized to view this page.');
}
?>