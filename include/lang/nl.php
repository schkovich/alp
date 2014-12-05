<?php
//English language array...have fun :)

$lang = array(
	"global" => array(
	//General
	"noauth" => "Je bent niet geregistreerd om deze pagina te bekijken.",
	"home" => "Home",
	"file" => "bestand",
	"files" => "bestanden",
	"map" => "map",
	"music" => "muziek",
	"servers" => "servers",
	"schedule" => "programma",
	"tournaments" => "toernooien",
	"sponsors" => "sponsors",
	"staff" => "staf",
	"shoutbox" => "shoutbox",
	"benchmarks" => "benchmarks",
	"tech_support" => "technische support",
	"restaurants" => "restaurants",
	"policy" => "beleid",
	"users" => "gebruikers",
	"benchmarks" => "benchmarks",
	"profile" => "profiel",
	"logout" => "Log uit",
	"login" => "Log in",
	"register" => "Registreer",
	"logedout_message" => "Je bent niet ingelogd &gt; log in",
	"forgot" => "Je paswoord vergeten?",
	"admin" => "admin",
	"administrator" => "administrator",
	"sadministrator" => "super administrator",
	"change_pw" => "Verander je paswoord",
	//Left hand module stuff
 		//cpanel	
	"cpanel" => "configuratie paneel",
	"cp_cuser" => "client gebruikersnaam",
	"cp_cip" => "client ip adres",
	"food" => "eten",
	"gr" => "gevraagde game",
	"prizes" => "prijzen",
	"going_for" => "doel",
	"reg_for" => "Registreer voor",
	"open_play" => "open spelen",
	"cp_lodge" => "lokaal onderbrengen",
	"cp_register" => "Een account nodig? Registreer hier",
	"cp_security" => "&nbsp;Voor maximale beveiliging, zet javascript aan.<br />&nbsp;cookies vereist.<br />",
	"cp_other" => "andere links",
	
	
	"admin_guides" => "administrator gids",
	"register_account" => "Registreer voor een account",
	"schedule_hour" => "programma voor volgende uur.",
	"view_all" => "bekijjk alles",
	"bench_link" => "benchmark competitie",
	"caffeine" => "cafeine",
	"caffeine_log" => "cafeine log",
	"marathon" => "de marathon",
	"polls" => "polls",
	"announcements" => "aankondigingen",
	//omfg were gonna need a giant error set :)
	),
	
	"admin_modules" => array(
	"title" => "module config",
	"add_notes" => "This allows you to move modules up and down as well as disable/enable them them. Just select a module and click the button to move it around<br>NOTE: This still requires the corresponding toggle (if any) to be on",
	"enable" => "aanzetten",
	"enabled" => "aangezet",
	"up" => "zet omhoog",
	"down" => "zet omlaag",
	"disable" => "uitzetten",
	"disabled" => "uitgezet",
	),
	
	"admin_benchmark_cheaters" => array(
	"plural" => "benchmark cheaters",
	"singular" => "reset cheater",
	"notes_update" => "reset een gebruiker's benchmarks als zij vals spelen. Dit is onomkeerbaar, dus wees voorzichtig.",
	"desc_userid" => "gebruikersnaam",
	"error_userid" => "Je vergat een cheater te selecteren.",
	"noauth" => "Je bent niet geregistreerd om deze pagina te bekijken.",
	),
	
	"admin_benchmarks" => array(
	"plural" => "benchmarks",
	"singular" => "benchmark",
	"notes_add" => "Voeg of wijzig beschikbare benchmarks aan de gebruiker toe. Hou rekening mee dat alle benchmarks die als deel van de samengestelde score worden vermeld voor de algemene globale benchmarkwinnaar zullen worden vereist.",
	"desc_name" => "benchmark naam",
	"error_name" => "Je vergat een benchmarknaam te vermelden!",
	"desc_abbreviation" => "benchmark afkorting",
	"desc_composite" => "Een deel van globale samengestelde score?",
	"desc_deflate" => "percentage om score te laten leeglopen wanneer het toevoegen aan samenstelling.",
	),
	
	"admin_caffeine_cheaters" => array(
	"plural" => "cafeine cheaters",
	"singular" => "reset cheater",
	"notes_update" => "reset de cafeinetelling van een gebruiker als zij van het bedriegen worden verdacht. Dit is onomkeerbaar, dus wees voorzichtig.",
	"desc_userid" => "cheater's gebruikersnaam",
	"error_userid" => "Je vergat de cheater te selecteren.",
	),
	
	"admin_caffeine_types" => array(
	"plural" => "cafeine types",
	"singular" => "cafeine type",
	"notes_add" => "voeg of wijzig beschikbare cafeinetypes aan de gebruiker toe.",
	"desc_name" => "cafeine type naam",
	"error_name" => "JJe vergat een cafe•netype naam te vermelden!",
	),
	
	"admin_caffeine" => array(
	"plural" => "cafeine items",
	"singular" => "cafeine",
	"notes_add" => "voeg of wijzig beschikbare cafeine items aan de gebuiker toe.",
	"desc_name" => "caffeine item naam",
	"error_name" => "Je vergat een cafeine item naam te vermelden!",
	"desc_caffeine_permg" => "aantal cafeine per milligram (tot 10 decimalen)",
	"error_caffeine_permg" => "om cafe•neinhoud te berekenen, moet u de hoeveelheid cafe•ne per milligram in de substantie invoeren",
	"desc_ttype" => "type van substantie",
	"descother_ttype" => "Voeg meer types toe",
	),
	
	//is this legal?
	"admin_deleteuser" => array(
	"plural" => "Verwijder gebruikers",
	"singular" => "Verwijder gebruiker",
	"notes_update" => "verwijdert een gebruiker, bijhorende informatie inbegrepen. Die zal in alle toernooien verwijderd zijn, maar die zal blanco zijn in toernooien. Dit is onomkeerbaar, dus wees voorzichtig. De superadministrators worden niet hier getoond, Je moet een gebruiker eerst degraderen om hem te schrappen.",
	//tournament...how to implement?"notes_update" => "deletes a user.  this is not reversible, so be careful.  super administrators are not shown here, you must first demote a user to administrator or normal user status to delete them.",
	"desc_userid" => "gebruikersnaam",
	"error_userid" => "Je vergat een gebruikersnaam te selecteren.",
	),
	
	"admin_disp_scores" => array(
	"plural" => "user submitted scores",
	"singular" => "",
	"teamname" => "teamnaam",
	"id" => "id",
	),
	
	"admin_foodrun" => array(
	"plural" => "food runs",
	"singular" => "food run",
	"notes_del" => "Deze functie is momenteel afgezet en de gebruikers kunnen geen eten toevoegen. Als je wenst om zijn status te veranderen, kan je <a href=\"admin_toggle.php\">aanzetten</a>.",
	"desc_userid" => "gepost door",
	"desc_datetime_leaving" => "vertrektijd",
	"error_datetime_leaving" => "Je vergat de tijdsvertrek te vermelden!",
	"desc_headline" => "aankomst",
	"error_headline" => "Je zei niet waar je gaat!",
	),
	
	"admin_games" => array(
	"plural" => "games",
	"singular" => "game",
	"notes_add" => "Maak een lijst van alle spellen dat in aanmerking komt voor toernooien of zo. Als je een spel gaat spelen voor toernooien, moet je hier een lijst maken.",
	"desc_name" => "de naam van een game",
	"error_name" => "Je vergat een game te vermelden!",
	"desc_current_version" => "huidige versie",
	"desc_url_update" => "relatieve of absolute url naar game updates",
	"desc_url_maps" => "relatieve of absolute url naar map directory of een map pack",
	),
	
	"admin_gamingrig" => array(
	"plural" => "gaming rig details",
	"singular" => "gaming rig",
	"notes_mod" => "wijzig een gebruikers's gaming rig informatie.",
	"desc_ms_sharename" => "microsoft share naam",
	"desc_ms_workgroup" => "microsoft werkgroep naam",
	"desc_ftp_server" => "Heb je een FTP server?",
	"desc_comp_proc" => "gaming rig cpu (merknaam en processor speed)",
	"desc_comp_mem" => "gaming rig memory (aantal, in mb, en type)",
	"desc_comp_hdstorage" => "gaming rig storage (aantal, in gb, en nummers van drivers)",
	"desc_comp_gfx" => "gaming rig graphics (aantal geheugen en chipset)",
	),
);











//Installer stuff...old format
$lang["install"]['install'] = 'install ALP';
$lang["install"]['success'] = 'success!';
$lang["install"]['failure'] = 'failure.';
$lang["install"]['on'] = 'on';
$lang["install"]['off'] = 'off';
$lang["install"]['nolongerrequired'] = 'no longer required, it is ok if on.';
$lang["install"]['optional'] = 'optional, but recommended.';
$lang["install"]['start'] = 'start';
$lang["install"]['end'] = 'end';
$lang["install"]['errors'] = 'ERRORS';
$lang["install"]['warning'] = 'WARNING!';
$lang["install"]['and'] = 'and';
	
$lang["install"]['stepone'] = 'step one of five';
$lang["install"]['stepone_passed'] = '<strong>Database test: </strong>Passed';
//$lang["install"]['stepone_description'] = 'edit the _config.php file located in the / directory of ALP.  edit the variables to describe your lan party.';
$lang["install"]['stepone_description'] = 'configure database settings - please enter your SQL server database settings below.';
$lang["install"]['stepone_next'] = 'move on to step two - validate _config.php and your php.ini';
$lang["install"]['stepone_repeat'] = 'change database settings';
	
$lang["install"]['steptwo'] = 'step two of five';
$lang["install"]['steptwo_varone'] = 'lan name';
$lang["install"]['steptwo_varone_error'] = "the name of your party cannot be empty.";
$lang["install"]['steptwo_vartwo'] = "gaming group name";
$lang["install"]['steptwo_vartwo_error'] = "the name of your gaming group cannot be empty.";
$lang["install"]['steptwo_varthree'] = "max attendees";
$lang["install"]['steptwo_varthree_error'] = "the number of maximum gamers must be greater than zero.";
$lang["install"]['steptwo_varfour'] = "super admin username";
$lang["install"]['steptwo_varfour_error'] = "the name of your super administrator account cannot be empty.";
$lang["install"]['steptwo_varfive'] = "mysql connection info";
$lang["install"]['steptwo_varfive_error'] = "your mysql connection information is incorrect.";
$lang["install"]['steptwo_varsix'] = "php.ini variable (magic_quotes_gpc)";
$lang["install"]['steptwo_varsix_error'] = "edit your php.ini to have magic_quotes_gpc to be on.  if you're unsure on how to do this; consult google or look on the support forums.";
$lang["install"]['steptwo_varseven'] = "default language";
$lang["install"]['steptwo_varseven_error'] = "you're missing a default language or the language you specified is not included in the ALP files.";
$lang["install"]['steptwo_vareight'] = "php.ini variable (short_open_tag)";
$lang["install"]['steptwo_vareight_error'] = "edit your php.ini to have short_open_tags to be on.  if you're unsure on how to do this; consult google or look on the support forums.";
$lang["install"]['steptwo_varnine'] = "php.ini variable (register_globals)";
$lang["install"]['satellitenotes'] = "other notes (ALP satellite):";
$lang["install"]['satellitenotes_valone'] = "the domain name of your web server must be alp. (ie: http://alp/ is the address set through DNS; not windows WINS).";
$lang["install"]['satellitenotes_valtwo'] = "your php must have the ftp and the secure sockets extensions enabled.<br />(Ignore this if you are not planning to use ALP Satellites)";
$lang["install"]['steptwo_varten'] = "start/stop dates";
$lang["install"]['steptwo_varten_error'] = "the end date of your lan must be after the starting date.";
$lang["install"]['steptwo_vareleven'] = "php.ini variable (error reporting)";
$lang["install"]['steptwo_vareleven_error'] = "not the default value (E_ALL & ~E_NOTICE) or (2039).<br />&nbsp;&nbsp;&nbsp;if your value is more strict; ALP will give you errors.";
$lang["install"]['steptwo_vartwelve'] = "mysql database";
$lang["install"]['steptwo_vartwelve_error'] = "the mysql database name does not currently exist.<br />&nbsp;&nbsp;&nbsp;if you continue; and it still doesn't exist in step four; it <br />&nbsp;&nbsp;&nbsp;will be created automatically.";
	
$lang["install"]['steptwo_next'] = "move on to step three - setting up the mysql table structure";
$lang["install"]['steptwo_redo'] = "make the necessary modifications to the _config.php file and refresh this page.";
	
$lang["install"]["stepthree"] = "step three of five";
$lang["install"]["stepthree_warning"] = "Continuing will delete all existing tables of ALP data.  If you have a previous install of ALP that you wish to save; please back up your database.  This script will replace those tables with empty ones.  Due to the vast changes made from the previous release; there is no upgrade script.  Sorry.";
$lang["install"]["stepthree_doublewarning"] = "YOU HAVE BEEN WARNED!!!";
$lang["install"]["stepthree_tournamentmodetitle"] = "tournament only mode";
$lang["install"]["stepthree_tournamentmode"] = "tournament mode will automatically configure the ALP database with your intention to use ALP for tournaments only.  it will automatically disable all the extra unnecessary features.  these features can be re-enabled later. alp tournaments is for computer game tournament, alp sports tournament is for any other type of tournaments (football, pool, basketball, etc.)";
$lang["install"]["stepthree_next_choice1"] = "move on to step four - Full Version - creating the mysql table structure";
$lang["install"]["stepthree_next_choice2"] = "move on to step four - ALP in tournament mode only - creating the mysql table structure";
$lang["install"]["stepthree_next_choice3"] = "move on to step four - ALP in sports tournament mode only - creating the mysql table structure";
	
$lang["install"]["stepfour"] = "step four of five";
$lang["install"]["stepfour_creatingdatabase"] = "creating the ALP database";
$lang["install"]["stepfour_newtable"] = "creating new table";
$lang["install"]["stepfour_defaultvalues"] = "inserting default values into";
$lang["install"]["stepfour_success"] = "table structure creation successful";
$lang["install"]["stepfour_warning"] = "make sure you delete the install.php file before using the script live";
$lang["install"]["stepfour_next"] = "move on to step five - register the super admin account";
$lang["install"]["stepfour_redo"] = "there has been an unexpected error.  refresh this page to try again.";
	
$lang["install"]['coffee']    = 'Koffie';
$lang["install"]['softdrink'] = 'frisdrank';
$lang["install"]['tea']       = 'thee';
$lang["install"]['other']     = 'anders';
?>
