<?php
// language file for install/install.php

$lang['install'] = 'install ALP';
$lang['success'] = 'success!';
$lang['failure'] = 'failure.';
$lang['on'] = 'on';
$lang['off'] = 'off';
$lang['nolongerrequired'] = 'no longer required, it is ok if on.';
$lang['optional'] = 'optional, but recommended.';
$lang['start'] = 'start';
$lang['end'] = 'end';
$lang['errors'] = 'ERRORS';
$lang['warning'] = 'WARNING!';
$lang['and'] = 'and';
	
$lang['stepone'] = 'step one of five';
$lang['stepone_passed'] = '<strong>Database test: </strong>Passed';
//$lang['stepone_description'] = 'edit the _config.php file located in the / directory of ALP.  edit the variables to describe your lan party.';
$lang['stepone_description'] = 'configure database settings - please enter your SQL server database settings below.';
$lang['stepone_next'] = 'move on to step two - validate _config.php and your php.ini';
$lang['stepone_repeat'] = 'change database settings';
	
$lang['steptwo'] = 'step two of five';
$lang['steptwo_varone'] = 'lan name';
$lang['steptwo_varone_error'] = "the name of your party cannot be empty.";
$lang['steptwo_vartwo'] = "gaming group name";
$lang['steptwo_vartwo_error'] = "the name of your gaming group cannot be empty.";
$lang['steptwo_varthree'] = "max attendees";
$lang['steptwo_varthree_error'] = "the number of maximum gamers must be greater than zero.";
$lang['steptwo_varfour'] = "super admin username";
$lang['steptwo_varfour_error'] = "the name of your super administrator account cannot be empty.";
$lang['steptwo_varfive'] = "mysql connection info";
$lang['steptwo_varfive_error'] = "your mysql connection information is incorrect.";
$lang['steptwo_varsix'] = "php.ini variable (magic_quotes_gpc)";
$lang['steptwo_varsix_error'] = "edit your php.ini to have magic_quotes_gpc to be on.  if you're unsure on how to do this; consult google or look on the support forums.";
$lang['steptwo_varseven'] = "default language";
$lang['steptwo_varseven_error'] = "you're missing a default language or the language you specified is not included in the ALP files.";
$lang['steptwo_vareight'] = "php.ini variable (short_open_tag)";
$lang['steptwo_vareight_error'] = "edit your php.ini to have short_open_tags to be on.  if you're unsure on how to do this; consult google or look on the support forums.";
$lang['steptwo_varnine'] = "php.ini variable (register_globals)";
$lang['satellitenotes'] = "other notes (ALP satellite):";
$lang['satellitenotes_valone'] = "the domain name of your web server must be alp. (ie: http://alp/ is the address set through DNS; not windows WINS).";
$lang['satellitenotes_valtwo'] = "your php must have the ftp and the secure sockets extensions enabled.<br />(Ignore this if you are not planning to use ALP Satellites)";
$lang['steptwo_varten'] = "start/stop dates";
$lang['steptwo_varten_error'] = "the end date of your lan must be after the starting date.";
$lang['steptwo_vareleven'] = "php.ini variable (error reporting)";
$lang['steptwo_vareleven_error'] = "not the default value (E_ALL & ~E_NOTICE) or (2039).<br />&nbsp;&nbsp;&nbsp;if your value is more strict; ALP will give you errors.";
$lang['steptwo_vartwelve'] = "mysql database";
$lang['steptwo_vartwelve_error'] = "the mysql database name does not currently exist.<br />&nbsp;&nbsp;&nbsp;if you continue; and it still doesn't exist in step four; it <br />&nbsp;&nbsp;&nbsp;will be created automatically.";
	
$lang['steptwo_next'] = "move on to step three - setting up the mysql table structure";
$lang['steptwo_redo'] = "make the necessary modifications to the _config.php file and refresh this page.";
	
$lang["stepthree"] = "step three of five";
$lang["stepthree_warning"] = "Continuing will delete all existing tables of ALP data.  If you have a previous install of ALP that you wish to save; please back up your database.  This script will replace those tables with empty ones.  Due to the vast changes made from the previous release; there is no upgrade script.  Sorry.";
$lang["stepthree_doublewarning"] = "YOU HAVE BEEN WARNED!!!";
$lang["stepthree_tournamentmodetitle"] = "tournament only mode";
$lang["stepthree_tournamentmode"] = "tournament mode will automatically configure the ALP database with your intention to use ALP for tournaments only.  it will automatically disable all the extra unnecessary features.  these features can be re-enabled later. alp tournaments is for computer game tournament, alp sports tournament is for any other type of tournaments (football, pool, basketball, etc.)";
$lang["stepthree_next_choice1"] = "move on to step four - creating the mysql table structure";
$lang["stepthree_next_choice2"] = "move on to step four with ALP in tournament mode only - creating the mysql table structure";
$lang["stepthree_next_choice3"] = "move on to step four with ALP in sports tournament mode only - creating the mysql table structure";
	
$lang["stepfour"] = "step four of five";
$lang["stepfour_creatingdatabase"] = "creating the ALP database";
$lang["stepfour_newtable"] = "creating new table";
$lang["stepfour_defaultvalues"] = "inserting default values into";
$lang["stepfour_success"] = "table structure creation successful";
$lang["stepfour_warning"] = "make sure you delete the install.php file before using the script live";
$lang["stepfour_next"] = "move on to step five - Edit the party settings (Deafult password: admin)";
$lang["stepfour_redo"] = "there has been an unexpected error.  refresh this page to try again.";
	
$lang['coffee']    = 'coffee';
$lang['softdrink'] = 'soft drink';
$lang['tea']       = 'tea';
$lang['other']     = 'other';

?>