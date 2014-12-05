<?php
switch ($database['type']) {
	case 'mysql':
		require_once('genesis_mysql.php');
		break;
	case 'mysqli':
		require_once('genesis_mysqli.php');
		break;
	case '':
		require_once('genesis_mysql.php');
		break;
	default:
		echo 'Unknown database type set: '.$database['type'];
		exit();
}
?>