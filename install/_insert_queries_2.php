<?php
if(file_exists('DISABLED')) { echo 'install has been disabled because it has already been run.<br />If you wish to run it again, please delete the file /install/DISABLED'; exit();}
	$insert_table_queries_2 = array();
	
	$gtemp = array();
	/*
	$id = $dbc->database_fetch_assoc($dbc->database_query("SELECT gameid FROM games WHERE short='bf1942'"));
	$gtemp[] = "INSERT INTO tournament_teams_type (gameid,onename,onecolor,twoname,twocolor) VALUES (".$id["gameid"].",'Axis','#FF0000','Allies','#0000FF')";

	$id = $dbc->database_fetch_assoc($dbc->database_query("SELECT gameid FROM games WHERE short='bfviet'"));
	$gtemp[] = "INSERT INTO tournament_teams_type (gameid,onename,onecolor,twoname,twocolor) VALUES (".$id["gameid"].",'North Vietnamese Army','#958671','United States','#404040')";
	
	$id = $dbc->database_fetch_assoc($dbc->database_query("SELECT gameid FROM games WHERE short='hl-cs'"));
	$gtemp[] = "INSERT INTO tournament_teams_type (gameid,onename,onecolor,twoname,twocolor) VALUES (".$id["gameid"].",'Terrorist','#958671','Counter-terrorist','#404040')";

	$id = $dbc->database_fetch_assoc($dbc->database_query("SELECT gameid FROM games WHERE short='ut'"));
	$gtemp[] = "INSERT INTO tournament_teams_type (gameid,onename,onecolor,twoname,twocolor) VALUES (".$id["gameid"].",'Red Team','#FF0000','Blue Team','#0000FF')";

	$id = $dbc->database_fetch_assoc($dbc->database_query("SELECT gameid FROM games WHERE short='halo'"));
	$gtemp[] = "INSERT INTO tournament_teams_type (gameid,onename,onecolor,twoname,twocolor) VALUES (".$id["gameid"].",'Red Team','#FF0000','Blue Team','#0000FF')";

	$id = $dbc->database_fetch_assoc($dbc->database_query("SELECT gameid FROM games WHERE short='ut2003'"));
	$gtemp[] = "INSERT INTO tournament_teams_type (gameid,onename,onecolor,twoname,twocolor) VALUES (".$id["gameid"].",'Red Team','#FF0000','Blue Team','#0000FF')";

	$id = $dbc->database_fetch_assoc($dbc->database_query("SELECT gameid FROM games WHERE short='ut2004'"));
	$gtemp[] = "INSERT INTO tournament_teams_type (gameid,onename,onecolor,twoname,twocolor) VALUES (".$id["gameid"].",'Red Team','#FF0000','Blue Team','#0000FF')";

	$id = $dbc->database_fetch_assoc($dbc->database_query("SELECT gameid FROM games WHERE short='cod'"));
	$gtemp[] = "INSERT INTO tournament_teams_type (gameid,onename,onecolor,twoname,twocolor) VALUES (".$id["gameid"].",'Axis','#FF0000','Allies','#0000FF')";
	*/

	$gtemp[] = "INSERT INTO tournament_teams_type (onename,onecolor,twoname,twocolor) VALUES ('Axis','#FF0000','Allies','#0000FF')";
	$gtemp[] = "INSERT INTO tournament_teams_type (onename,onecolor,twoname,twocolor) VALUES ('Terrorist','#FF0000','Counter-terrorist','#0000FF')";
	$gtemp[] = "INSERT INTO tournament_teams_type (onename,onecolor,twoname,twocolor) VALUES ('Red','#FF0000','Blue','#0000FF')";
	$gtemp[] = "INSERT INTO tournament_teams_type (onename,onecolor,twoname,twocolor) VALUES ('Bad guys','#FF0000','Good guys','#0000FF')";
	$gtemp[] = "INSERT INTO tournament_teams_type (onename,onecolor,twoname,twocolor) VALUES ('Enemy','#FF0000','Friendly','#0000FF')";
	$gtemp[] = "INSERT INTO tournament_teams_type (onename,onecolor,twoname,twocolor) VALUES ('Home','#FF0000','Away','#0000FF')";
	$gtemp[] = "INSERT INTO tournament_teams_type (onename,onecolor,twoname,twocolor) VALUES ('Shirts','#FF0000','Skins','#0000FF')";

	
	$insert_table_queries_2["tournament_teams_type"] = $gtemp;
?>