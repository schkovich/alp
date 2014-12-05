<?php 
// array( tournament name, time variable, minimum teams )
$tournament_types = array(
	12 => array('boiloff','1',2),
	1 => array('single elimination',1,3),
	//2 => array('single elimination switchover to consolation',2,3),
	//3 => array('single elimination switchover to double elimination',2,3),
	4 => array('consolation',3,5),
	5 => array('double elimination',3,5),
	//6 => array('round robin split switchover to single elimination',4,0),
	//7 => array('round robin split switchover to consolation',4,0),
	//8 => array('round robin split switchover to double elimination',4,0),
	//9 => array('round robin split switchover to round robin',4,0),
	10 => array('round robin',5,2),
	//11 => array('ladder',5,2)
	);

/*
ladder progress [files modified]:
	admin_teams.php
	admin_teams_delete.php

	in progress: disp_teams.php
	to do: chng_teams.php, chng_update_teams.php, etc
*/
?>