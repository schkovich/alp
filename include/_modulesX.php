<?php
// ------------------------------------------------------------------------------------------------------------------------
// configure the modules displayed in ALP here.
// ------------------------------------------------------------------------------------------------------------------------

// modulelist comes from the skin _config.inc.php file.
// if you want to add your own module, you'd need to add another elseif clause below.  the filenames of the modules are located in
// include/modules/*.  filenames are listed as mod_{MODULE NAME}.php
// for example, 
/* 	} elseif($key == "mod_{MODULE NAME}") {
			$modules->add_module("{MODULE NAME}",$modulelistitem[0], {SECURITY LEVEL}, "{MODULE TITLE}",{SLIM BOOLEAN},{LINK},$modulelistitem[1]);
	} */
/* 	{MODULE NAME}		- mod_{MODULE NAME}.php will be included from include/modules/
	{SECURITY LEVEL}	- minimum security level: 	0 - normal guest or unregistered user
													1 - registered user and logged in
													2 - normal administrator
													3 - super administrator
	{MODULE TITLE}		- displayed at the top of the module as a string
	{SLIM BOOLEAN}		- 0: no, 1: yes - as far as i can tell, this isn't used as of now.
	{LINK}				- changes {MODULE TITLE} text to be a link to this file, may be left as an empty string for no link ''	
	*/
// don't forget to add $modulelist["mod_{MODULE NAME}"] = array("{LOCATION}","{TYPE}"); to the skin _config.inc.php.

global $dbc;
$modules = new ModuleManager();
$temp = get_modulelist();
if(sizeof($temp)>0) {
	foreach($temp as $key => $modulelistitem) {
		if($key == "mod_controlpanel" && !ALP_TOURNAMENT_MODE) {
			// in this module, different code displayed if logged in and if not.
			$modules->add_module("controlpanel", $modulelistitem[0], 0, get_lang("cpanel"),0,"",$modulelistitem[1]);
		} elseif($key == "mod_register" && !ALP_TOURNAMENT_MODE) {
			if(current_security_level()==0) {  
				// only displayed modules if not logged in.
				$modules->add_module("register",$modulelistitem[0],0, "",1,"",$modulelistitem[1]); 
			}
		} elseif($key == "mod_admincontrolpanel") {
			if(current_security_level()>=2) $modules->add_module("admincontrolpanel",$modulelistitem[0], 2, get_lang("administrator")." ".get_lang("cpanel"),0,"",$modulelistitem[1]);
		} elseif($key == "mod_guides") {
			if(current_security_level()>=2) $modules->add_module("guides",$modulelistitem[0], 2, get_lang("admin_guides"),0,"",$modulelistitem[1]);
		} elseif($key == "mod_schedule") {
			if($toggle["schedule"]) $modules->add_module("schedule",$modulelistitem[0],0, get_lang("schedule_hour"),0,"",$modulelistitem[1]);
		} elseif($key == "mod_tournaments" && !ALP_TOURNAMENT_MODE) {
			$modules->add_module("tournaments",$modulelistitem[0], 0, get_lang("tournaments"),0,"tournaments.php",$modulelistitem[1]);
		} elseif($key == "mod_polls" && !ALP_TOURNAMENT_MODE) {
			if($dbc->database_num_rows($dbc->database_query("SELECT * FROM poll"))) {
				// only display module if there are polls in the database.
				$modules->add_module("polls",$modulelistitem[0],$master['pollsguest']?0:1,get_lang("polls"),0,"polls.php",$modulelistitem[1]);
			}
		} elseif($key == 'mod_news' && $dbc->database_num_rows($dbc->database_query("SELECT * FROM news WHERE hide_item=0"))) {
			$modules->add_module("news",$modulelistitem[0], 0, get_lang("announcements"),0,'',$modulelistitem[1]);
		}
	}
}

//$modules->add_module("template", "main");
?>